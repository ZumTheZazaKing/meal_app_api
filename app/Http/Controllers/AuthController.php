<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required|min:6|max:20',
            'password' => 'required',
            'confirm' => 'required|same:password'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'messages' => $validator->errors()
            ]);
        }

        $username = $request->username;
        $password = $request->password;

        if(!Auth::attempt(['username'=>$username,'password'=>$password])){
            return response()->json([
                'status' => 400,
                'messages' => ['Invalid credentials']
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
