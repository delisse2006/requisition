<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'role',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ ADD THESE ROLE METHODS
    public function isAdmin() { return $this->role === 'admin'; }
    public function isAccountant() { return $this->role === 'accountant'; }
    public function isEmployee() { return $this->role === 'employee'; }

    // ✅ ADD REQUISITION RELATIONSHIP
    public function requisitions()
    {
        return $this->hasMany(Requisition::class);
    }

    // ✅ ADD AVATAR METHODS
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('images/avatars/' . $this->avatar);
        }
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF&size=128";
    }

  public function getAvatarSmallUrlAttribute()
{
    if ($this->avatar) {
        return asset('images/avatars/' . $this->avatar);
    }
    $name = urlencode($this->name);
    return "https://ui-avatars.com/api/?name={$name}&size=32";
}

}
