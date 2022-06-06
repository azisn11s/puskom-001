<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->getCredentials();

        if ($token = Auth::attempt($credentials)) {

            return response()->json([
                'success'=> true,
                'message'=> 'Logged in successfully.',
                'access_token'=> $token,
                'token_type'=> 'bearer'
            ]);
        }

        return response()->json([
            'success'=> false,
            'message'=> 'Log in failed.',
            'access_token'=> null,
            'token_type'=> 'bearer'
        ], 403);
    }
}
