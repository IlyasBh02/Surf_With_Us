<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'surfeur_id',
        'status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function surfeur()
    {
        return $this->belongsTo(User::class, 'surfeur_id');
    }
}
