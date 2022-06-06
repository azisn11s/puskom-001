<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::query()->where('id', '!=', $request->user()->id);

        // Filter
        if ($request->has('search')) {
            $users->where('email', 'ilike', "%{$request->search}%")
                ->orWhere('name', 'ilike', "%{$request->search}%");
        }

        // Order
        if ($request->has('sort')) {
            $sortArray = json_decode($request->get('sort'), true);
            foreach ($sortArray as $column => $direction) {

                if (!$direction) {
                    continue;
                }

                $users->orderBy($column, $direction);
            }
        }
        
        
        $result = $users->paginate($request->get('per_page', 15));
        // $result = $users->simplePaginate($request->get('per_page', 15));

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'username'=> ['required', 'unique:users,username', 'min:3'],
            'email'=> ['required', 'email', 'unique:users,email'],
            'password'=> ['required', 'confirmed'],
            'role'=> ['required', 'exists:roles,slug']
        ]);

        DB::beginTransaction();
        try {
            
            $user = User::create([
                'username'=> $request->username, // rtrim("{$request->firstname} {$request->lastname}"),
                'email'=> $request->email,
                'password'=> $request->password
            ]);

            // Register as guest | default
            $guestRole = Role::whereSlug($request->role)->first();
            $user->roles()->attach($guestRole);
            
            DB::commit();

            return response()->json([
                'success'=> true,
                'message'=> 'User created successfully!',
                'data'=> $user->load('roles'),
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = User::query()->with([
            'roles', 
        ])
        ->find($user->id);

        return response()->json([
            'success'=> true,
            'message'=> 'User detail has been loaded!',
            'data'=> $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'fullname'=> ['required', 'min:3'],
            'email'=> [
                'required', 
                'email', 
                Rule::unique('users')->ignore($user->getKey())
            ],
            'password'=> ['nullable', 'confirmed'],
            'role'=> [
                Rule::requiredIf($request->get('employee_id')), 
                'exists:roles,slug'
            ],
        ]);

        DB::beginTransaction();
        try {
            
            $user->update([
                'name'=> $request->fullname, // rtrim("{$request->firstname} {$request->lastname}"),
                'email'=> $request->email,
            ]);

            if ($request->get('password')) {
                $user->update([
                    'password'=> $request->password
                ]);
            }

            // Register as guest | default
            if ($request->has('role')) {
                $guestRole = Role::whereSlug($request->role)->first();
                $user->roles()->sync($guestRole);
            }
            
            DB::commit();

            return response()->json([
                'success'=> true,
                'message'=> 'User updated successfully!',
                'data'=> $user->load('roles'),
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->subscriptions) {
            $user->subscriptions()->detach();
        }

        if (!$user->delete()) {
            return response()->json([
                'success'=> false,
                'message'=> 'Failed delete user!',
                'data'=> $user
            ], 403);
        }

        return response()->json([
            'success'=> true,
            'message'=> 'User deleted successfully.',
            'data'=> null
        ]);
    }

    /**
     * Replace current user password to default password.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function defaultPassword(User $user)
    {
        $defaultPasswordApplied = $user->fill([
            'password'=> config('custom-config.default_password')
        ])->save();

        if (!$defaultPasswordApplied) {
            return response()->json([
                'success'=> false,
                'message'=> 'Failed delete user!',
                'data'=> $user
            ], 403);
        }

        return response()->json([
            'success'=> true,
            'message'=> 'Password change successfully.',
            'data'=> null
        ]);
    }

    /**
     * Export excel for user collection
     * 
     * @param Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        $timestamp = time();
        return (new UserExport([]))->download("user-export-{$timestamp}.xlsx");
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request, User $user)
    {
        $this->validate($request, [
            'current_password'=> ['required', new MatchOldPassword],
            'new_password'=> [
                'required', 
                'min:5', 
                'same:new_password',
                // Rule::notIn($this->bannedPasswords())]
            ],
            'new_password_confirmation'=> ['required', 'same:new_password']
        ]);

        DB::beginTransaction();
        try {
            
            $user->update([
                'password'=> $request->new_password
            ]);
            
            DB::commit();

            return response()->json([
                'success'=> true,
                'message'=> 'User password updated successfully!',
                'data'=> $user->load('roles'),
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Get the authenticated User
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $roles = $user->roles->load('features');

        return response()->json([
            'user'=> $user,
            'roles'=> $roles
        ]);
    }
}
