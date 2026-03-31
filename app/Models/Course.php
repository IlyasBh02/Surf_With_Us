<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'coach_id',
        'title',
        'description',
        'thumbnail',
        'date',
        'duration',
        'available_places',
        'price',
        'level',
        'location',
    ];

    protected $casts = [
        'date'  => 'datetime',
        'price' => 'decimal:2',
    ];

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
