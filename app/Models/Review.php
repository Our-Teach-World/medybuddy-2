<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'rating',
        'comment',
    ];

    // Ek review sirf ek patient ka hota hai
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Ek review sirf ek doctor ke liye hota hai
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}