<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
         $validator = Validator::make($request->all(),[
            'username' => 'required|unique:users,username|min:6|max:20',
            'password' => 'required',
            'confirm' => 'required|same:password'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'messages' => $validator->errors()
            ]);
        }

        $credentials = [
            'username'=>$request->username,
            'password'=>$request->password
        ];
        
        $user = User::create($credentials);

        Auth::attempt($credentials);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required|min:6|max:20',
            'password' => 'required',
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
            'status' => 200
        ],200);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out','status'=>200]);
    }
}
