<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;

class UserService
{
    // Mencari pengguna berdasarkan email
    public static function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    // Mencari pengguna berdasarkan ID
    public static function findById($id)
    {
        return User::find($id);
    }

    public static function create($name, $email, $password, $role_id = null)
{
    // Ambil role default jika role_id tidak diberikan
    if (!$role_id) {
        $role = Role::where('name', 'User')->first();
        if (!$role) {
            return ['success' => false, 'message' => 'Default role not found'];
        }
        $role_id = $role->id;
    }

    // Create user
    $user = User::create([
        'name'     => $name,
        'email'    => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    $user->refresh();

    // ✅ Validasi sebelum attach
    if (empty($user->id)) {
        throw new \Exception('User ID tidak terisi setelah create');
    }

    $user->roles()->attach($role_id);

    return $user->load('roles');
}


    // Mengupdate pengguna berdasarkan ID dan mengupdate relasi di pivot table
    public static function update($id, $data)
    {
        $user = self::findById($id);
        if ($user) {
            // Jika ada data role_id, pastikan role yang dimaksud valid
            if (isset($data['role_id'])) {
                $role = Role::find($data['role_id']);
                if (!$role) {
                    return ['success' => false, 'message' => 'Role not found'];
                }

                // Memperbarui relasi di tabel pivot jika role_id diubah
                $user->roles()->sync([$data['role_id']]);  // Mengupdate role pengguna di pivot table
            }

            $user->update($data); // Update data pengguna lainnya
            return $user;
        }
        return null;
    }

    // Menghapus pengguna berdasarkan ID
    public static function delete($id)
    {
        $user = self::findById($id);
        if ($user) {
            // Menghapus pengguna dan semua relasi di tabel pivot role_user
            $user->roles()->detach();  // Menghapus semua relasi role untuk pengguna
            return $user->delete();
        }
        return false;
    }

    public function updateRole(array $data) {
        $id     = $data['id']      ?? null;
        $role_id = $data['role_id'] ?? null;

        $user = self::findById($id);
        if ($user) {
            // Memastikan role yang dimaksud valid
            $role = Role::find($role_id);
            if (!$role) {
                return ['success' => false, 'message' => 'Role not found'];
            }

            // Memperbarui relasi di tabel pivot
            $user->roles()->sync([$role_id]);
            return $user;
        }
        return null;
    }

    // Get semua user dengan roles
    public static function getAllWithRoles()
    {
        return User::with('roles')
            ->get()
            ->map(function ($user) {
                return [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'id'    => $role->id,
                            'name'  => $role->name,
                            'label' => $role->label,
                        ];
                    })->toArray()
                ];
            });
    }

    // Get semua roles
    public static function getAllRoles()
    {
        return Role::all()->map(function ($role) {
            return [
                'id'    => $role->id,
                'name'  => $role->name,
                'label' => $role->label,
            ];
        });
    }

    // Assign multiple roles ke user (replace existing)
    public static function assignRoles($userId, $roleIds)
    {
        $user = self::findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Validasi bahwa semua role_id valid
        if (!is_array($roleIds)) {
            $roleIds = [$roleIds];
        }

        $validRoles = Role::whereIn('id', $roleIds)->pluck('id')->toArray();
        if (count($validRoles) !== count($roleIds)) {
            return ['success' => false, 'message' => 'Some roles not found'];
        }

        // Sync roles (replace existing dengan yang baru)
        $user->roles()->sync($roleIds);

        return [
            'success' => true,
            'message' => 'Roles assigned successfully',
            'user'    => $user->load('roles')
        ];
    }

    // Add single role ke user (without replacing)
    public static function addRole($userId, $roleId)
    {
        $user = self::findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $role = Role::find($roleId);
        if (!$role) {
            return ['success' => false, 'message' => 'Role not found'];
        }

        // Attach role (jika belum ada)
        $user->roles()->syncWithoutDetaching([$roleId]);

        return [
            'success' => true,
            'message' => 'Role added successfully',
            'user'    => $user->load('roles')
        ];
    }

    // Remove single role dari user
    public static function removeRole($userId, $roleId)
    {
        $user = self::findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $role = Role::find($roleId);
        if (!$role) {
            return ['success' => false, 'message' => 'Role not found'];
        }

        // Detach specific role
        $user->roles()->detach($roleId);

        return [
            'success' => true,
            'message' => 'Role removed successfully',
            'user'    => $user->load('roles')
        ];
    }
}
