<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getUser()
    {
        return $this->success(auth('api')->user());
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        $credentials = $request->only('email', 'password');
        $token = auth('api')->attempt($credentials);

        $expires_in = Carbon::now()->addMinutes(auth('api')->factory()->getTTL())->timestamp;
        
        if (!$token) {
            return $this->fail('Unauthorized', 401);
        }

        $user = auth('api')->user();
        return $this->success([
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'expires_in' => $expires_in,
                    'type' => 'bearer',
                ]
            ]);

    }

    public function register(Request $request){
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]
        );

        DB::beginTransaction();
        try{ 

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ];

            $user = User::create($data);
            DB::commit();

            $expires_in = Carbon::now()->addMinutes(auth('api')->factory()->getTTL())->timestamp;
            return $this->success([
                'user' => $user,
                'authorisation' => [
                    'token' => auth('api')->login($user),
                    'expires_in' => $expires_in,
                    'type' => 'bearer',
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error('User registration failed', 500);
        }
    }

    public function logout()
    {
        auth('api')->logout();
        return $this->success('Successfully logged out');
    }

    public function refresh()
    {
        $user = auth('api')->user();
        $token = auth('api')->refresh();
        $expires_in = Carbon::now()->addMinutes(auth('api')->factory()->getTTL())->timestamp;
        return $this->success([
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'expires_in' => $expires_in,
                'type' => 'bearer',
            ]
        ]);
    }
}
