<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(RegisterRequest $request)
    {
        return $this->register($request);
    }

    private function register(RegisterRequest $request)
    {

        DB::beginTransaction();
        try {

            $user = User::create($request->validated());

            $roleGuest = Role::whereSlug('guest')->first();
            
            $user->roles()->sync($roleGuest);

            DB::commit();

            return response()->json([
                'success'=> true,
                'message'=> 'User register successfully.',
                'data'=> $user
            ]);
            

        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
}
