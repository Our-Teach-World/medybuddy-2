<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name','first_name','last_name','email','phone','address','password',
        'role','specialization','license','experience','dob','gender'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



   public function patientProfile()
{
    return $this->hasOne(PatientProfile::class, 'user_id');
}

public function appointments()
{
    return $this->hasMany(Appointment::class, 'patient_id');
}
   public function schedules()
    {
        return $this->hasMany(Schedule::class, 'doctor_id');
    }

public function doctorProfile()
{
    return $this->hasOne(DoctorProfile::class, 'user_id');
}

public function reviews()
{
    return $this->hasMany(Review::class, 'doctor_id');
}

}

