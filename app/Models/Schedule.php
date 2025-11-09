<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'doctor_id',
        'date',
        'slots',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'slots' => 'array',
        'date' => 'date',
    ];

    /**
     * Get the user (doctor) that owns the schedule.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}