<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        if(!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user'    => auth()->guard('api')->user(),
            'token'   => $token
        ], 200);
    }

    public function register(Request $request){
        $credentials = $request->only('username', 'name', 'email', 'password');
        $user = User::create([
            'username' => $credentials['username'],
            'name'     => $credentials['name'],
            'email'    => $credentials['email'],
            'password' => bcrypt($credentials['password'])
        ]);

        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftar',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendaftar',
            'user'    => $user,
        ], 200);
    }

    public function logout(){
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        if($removeToken) {
            //return response JSON
            return response()->json([
                'success' => true,
                'message' => 'Logout Berhasil!',
            ]);
        }
    }
}
