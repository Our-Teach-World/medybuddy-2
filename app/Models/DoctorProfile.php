<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'license',
        'experience',
        'bio',
        'profile_image',
        'in_person_fee',
        'video_fee',
        'rating',
        'reviews_count',
        'hospital_name',
        'hospital_address',
        'languages',
        'qualifications',
        'treatments',
        'expertise',
        'awards',
        'publications',
        'consultation_modes',
        'linkedin',
        'facebook',
        'twitter',
        'instagram',
        'consultation_hours',
        'default_mode',
    ];

    protected $casts = [
        'consultation_modes' => 'array',
        'time_slots' => 'array', // if stored in doctor_profiles
    ];

    // =========================
    // Accessors — always return array
    // =========================
    public function getLanguagesAttribute($value)
    {
        return $this->convertToArray($value);
    }

    public function getTreatmentsAttribute($value)
    {
        return $this->convertToArray($value);
    }

    public function getExpertiseAttribute($value)
    {
        return $this->convertToArray($value);
    }

    public function getConsultationModesAttribute($value)
{
    return $this->convertToArray($value);
}

public function getTimeSlotsAttribute($value)
{
    return $this->convertToArray($value);
}

    // =========================
    // Mutators — always store as JSON array
    // =========================
    public function setLanguagesAttribute($value)
    {
        $this->attributes['languages'] = $this->convertToJsonArray($value);
    }

    public function setTreatmentsAttribute($value)
    {
        $this->attributes['treatments'] = $this->convertToJsonArray($value);
    }

    public function setExpertiseAttribute($value)
    {
        $this->attributes['expertise'] = $this->convertToJsonArray($value);
    }

    public function setConsultationModesAttribute($value)
{
    $this->attributes['consultation_modes'] = $this->convertToJsonArray($value);
}

public function setTimeSlotsAttribute($value)
{
    $this->attributes['time_slots'] = $this->convertToJsonArray($value);
}

    // =========================
    // Helper functions
    // =========================
    private function convertToArray($value)
    {
        if (is_null($value)) return [];

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return is_string($value) ? [$value] : [];
    }

    private function convertToJsonArray($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        } elseif (is_string($value)) {
            return json_encode([$value]);
        } else {
            return json_encode([]);
        }
    }

    // Relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
