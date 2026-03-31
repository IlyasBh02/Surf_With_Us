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
        'role',
        'status',
        'coach_approved',
        'bio',
        'description',
        'years_experience',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'coach_approved'    => 'boolean',
        ];
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'coach_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'surfeur_id');
    }

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isCoach(): bool   { return $this->role === 'coach'; }
    public function isSurfeur(): bool { return $this->role === 'surfeur'; }
}
