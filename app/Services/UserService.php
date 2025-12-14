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
}
