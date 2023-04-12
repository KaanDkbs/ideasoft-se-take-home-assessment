<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $accessToken = $user->createToken('token-name')->accessToken;
            return response()->json(['status' => 'success', 'accessToken' => $accessToken], 200);
        }

        return response()->json(['status' => 'fail', 'error' => 'Unauthorised'], 401);
    }
}
