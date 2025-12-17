<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Ensure role is fillable
    ];

    // ... (keep your existing hidden and casts methods)

    /**
     * Check if the user has a specific role.
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has any of the provided roles.
     */
    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }
}