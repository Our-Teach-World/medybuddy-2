<?php

namespace App\Models;
use App\Models\PatientProfile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','patient_identifier','blood_group','height','weight','emergency_contact','medical_history','allergies','profile_image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

