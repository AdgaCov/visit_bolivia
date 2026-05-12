<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    public function getAll(): Collection
    {
        return User::with(['role', 'plan'])->get();
    }

    public function getById(int $id): User
    {
        return User::with(['role', 'plan'])->findOrFail($id);
    }

    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Si se envía role como string, buscar el role_id
        if (isset($data['role']) && is_string($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $data['role_id'] = $role->id;
            } else {
                // Por defecto, buscar el rol 'user'
                $defaultRole = Role::where('name', 'user')->first();
                $data['role_id'] = $defaultRole ? $defaultRole->id : 1;
            }
            unset($data['role']);
        } elseif (!isset($data['role_id'])) {
            // Si no se especifica role ni role_id, usar 'user' por defecto
            $defaultRole = Role::where('name', 'user')->first();
            $data['role_id'] = $defaultRole ? $defaultRole->id : 1;
        }
        
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Si se envía role como string, buscar el role_id
        if (isset($data['role']) && is_string($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $data['role_id'] = $role->id;
            }
            unset($data['role']);
        }
        
        $user->update($data);
        return $user->fresh(['role', 'plan']);
    }

    public function delete(int $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    public function toggleStatus(int $id): User
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();
        return $user->fresh(['role', 'plan']);
    }

    public function getByRole(string $roleName): Collection
    {
        return User::whereHas('role', function($query) use ($roleName) {
            $query->where('name', $roleName);
        })->with(['role', 'plan'])->get();
    }

    public function getActiveUsers(): Collection
    {
        return User::where('status', 1)->get();
    }
}
