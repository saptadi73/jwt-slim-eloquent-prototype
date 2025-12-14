<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Firebase\JWT\JWT;

class AuthService
{
    // Fungsi login
    // Fungsi login
    public static function login($email, $password)
    {
        $user = User::where('email', $email)->with('roles')->first();

        if ($user && password_verify($password, $user->password)) {
            $key = $_ENV['JWT_SECRET'] ?? null;
            if (!$key) {
                throw new \Exception('JWT_SECRET not set in environment');
            }

            $payload = [
                'sub' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'iat' => time(),
                'exp' => time() + (12 * 3600)
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            return [
                'success' => true,
                'token' => $jwt,
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'name'  => $role->name,
                            'label' => $role->label,
                        ];
                    })
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid credentials'
        ];
    }


    public static function register($name, $email, $password, $role_id = null)
    {
        // Ambil role default jika role_id tidak diberikan
        if (!$role_id) {
            $role = Role::where('name', 'user')->first();
            if (!$role) {
                return ['success' => false, 'message' => 'Default role not found'];
            }
            $role_id = $role->id;
        }

        // Membuat user baru dengan UUID manual
        $user = User::create([
            'id'       => (string) \Illuminate\Support\Str::uuid(),
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // Attach role ke pivot
        $user->roles()->attach($role_id);

        return [
            'success' => true,
            'user'    => $user->load('roles'),
        ];
    }
}
