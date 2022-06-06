<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Add a mutator to ensure hashed passwords
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    /**
     * Checks if the user belongs to role.
     */
    public function inRole(string $roleSlug)
    {
        return $this->roles()->where('slug', $roleSlug)->count() == 1;
    }

    /**
     * Checks if User has access to $permissions.
     */
    public function hasAccess(array $permissions): bool
    {
        // check if the permission is available in any role
        foreach ($this->roles as $role) {
            if ($role->hasAccess($permissions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if User has access to $feature.
     * 
     * @param string $feature Feature Slug
     * @param string $ability Ability action
     * @return bool
     */
    public function hasAccessFeature(string $feature, string $ability = ""): bool
    {
        // check if the permission is available in any role
        foreach ($this->roles as $role) {
            $featureApplied = $role->hasAccessFeature($feature);

            if ($featureApplied && $ability) {
                return $role->hasAccessFeature($feature, $ability);

            }

            if ($featureApplied) {
                return true;
            }
        }
        return false;
    }

    /**
     * Association between employee with validity (subscription time)
     * 
     */
    public function subscriptions()
    {
        return $this->belongsToMany(Employee::class, 'employee_subscriptions')
            ->withPivot(['valid_until'])
            ->withTimestamps();
    }

    /**
     * Has one employee
     * 
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}
