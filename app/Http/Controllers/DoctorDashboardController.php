<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\DoctorProfile;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\Schedule;

class DoctorDashboardController extends Controller
{
    // Dashboard index
    public function index()
    {
        $user = Auth::user();
        $doctorProfile = $user->doctorProfile;

        $doctorSchedule = Schedule::where('doctor_id', $user->id)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        $reviews = Review::where('doctor_id', $user->id)
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.dashboard', compact('user', 'doctorProfile', 'doctorSchedule', 'reviews'));
    }

    // Update basic profile
    public function updateProfile(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 🔐 Security: Only allow self-edit (or admin)
        if (Auth::id() !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $doctorProfile = $user->doctorProfile;

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'experience' => 'nullable|string|max:50',
            'license' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'hospital_name' => 'nullable|string',
            'hospital_address' => 'nullable|string',
            'languages' => 'nullable|string', // will be JSON
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qualifications' => 'nullable|string',
        ]);

        // Update User table
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name'       => $request->first_name . ' ' . $request->last_name,
            'experience' => $request->experience,
            'license' => $request->license,
            'specialization' => $request->specialization,
        ]);

        // Prepare DoctorProfile data
        $profileData = [
              'experience' => $request->experience,
                'license' => $request->license,
                'specialization' => $request->specialization,
                'bio' => $request->bio,
                'hospital_name' => $request->hospital_name,
                'hospital_address' => $request->hospital_address,
                'languages' => $request->languages,
                'qualifications' => $request->qualifications,
        ];

        // Handle profile image
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($doctorProfile && $doctorProfile->profile_image) {
                Storage::disk('public')->delete($doctorProfile->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $profileData['profile_image'] = $path;
        }

        // Update or create DoctorProfile
        $user->doctorProfile()->updateOrCreate(['user_id' => $user->id], $profileData);

        return response()->json(['message' => 'Profile updated successfully']);
    }

    // Update professional details
    public function updateProfessional(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'bio' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'treatments' => 'nullable|string', // JSON array
            'expertise' => 'nullable|string',  // JSON array
            'awards' => 'nullable|string',
            'publications' => 'nullable|string',
        ]);

        $profileData = [
            'bio' => $request->bio,
            'qualifications' => $request->qualifications,
            'treatments' => $request->treatments, // JSON from JS
            'expertise' => $request->expertise,   // JSON from JS
            'awards' => $request->awards,
            'publications' => $request->publications,
        ];

        $user->doctorProfile()->updateOrCreate(['user_id' => $user->id], $profileData);

        return response()->json(['message' => 'Professional details updated successfully']);
    }

    // Update contact info
    public function updateContact(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'hospital_name' => 'nullable|string',
            'hospital_address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'consultation_hours' => 'nullable|string',
        ]);

        $profileData = [
            'hospital_name' => $request->hospital_name,
            'hospital_address' => $request->hospital_address,
            'phone' => $request->phone,
            'email' => $request->email,
            'consultation_hours' => $request->consultation_hours,
        ];

        $user->doctorProfile()->updateOrCreate(['user_id' => $user->id], $profileData);

        return response()->json(['message' => 'Contact information updated successfully']);
    }

    // Update schedule & fees
    public function updateSchedule(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
        'in_person_fee' => 'nullable|numeric|min:0',
        'video_fee' => 'nullable|numeric|min:0',
        'consultation_modes' => 'nullable',
        'time_slots' => 'nullable',
    ]);

    // normalize arrays
    $consultationModes = $this->normalizeArray($request->input('consultation_modes'));
    $timeSlots = $this->normalizeArray($request->input('time_slots'));

        $profileData = [
            'in_person_fee' => $request->in_person_fee,
            'video_fee' => $request->video_fee,
           'consultation_modes' => $consultationModes, // mutator accepts array
           'time_slots' => $timeSlots,               // JSON from JS — Note: This is stored in DoctorProfile, but you might want to move to Schedule
        ];

        $user->doctorProfile()->updateOrCreate(['user_id' => $user->id], $profileData);

        // ⚠️ Optional: Also update Schedule table if date is provided
        // But your JS doesn't send date — so we skip for now.
        // If you want daily schedule update — add date picker + logic.

        return response()->json(['message' => 'Schedule updated successfully']);
    }

    private function normalizeArray($value)
{
    if (is_null($value)) return [];
    if (is_array($value)) return $value;
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) return $decoded;
        // single string -> return as single-element array
        return [$value];
    }
    return [];
}

public function saveSchedule(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'slots' => 'required', // can be array or json-string
    ]);

    $user = Auth::user();

    // normalize slots
    $slots = $this->normalizeArray($request->input('slots'));

    // ✅ Point 9: Prevent removing already booked slots
    $bookedAppointments = Appointment::where('doctor_id', $user->id)
        ->whereDate('date_time', $request->date)
        ->pluck('date_time')
        ->map(fn($dt) => \Carbon\Carbon::parse($dt)->format('H:i'))
        ->toArray();

    foreach ($bookedAppointments as $bookedSlot) {
        if (!in_array($bookedSlot, $slots)) {
            return response()->json([
                'success' => false,
                'message' => "Cannot remove slot {$bookedSlot}, already booked by a patient."
            ], 422);
        }
    }

    // Save or update schedule if no conflict
    Schedule::updateOrCreate(
        ['doctor_id' => $user->id, 'date' => $request->date],
        ['slots' => $slots]
    );

    return response()->json(['message' => 'Schedule saved successfully', 'slots' => $slots]);
}


public function getSchedule(Request $request)
{
    $doctorId = $request->query('doctor_id');
    $date = $request->query('date'); // yyyy-mm-dd

    if (!$doctorId || !$date) {
        return response()->json(['error' => 'doctor_id and date are required'], 422);
    }

    $schedule = Schedule::where('doctor_id', $doctorId)->where('date', $date)->first();
    if ($schedule) {
        return response()->json(['slots' => $schedule->slots, 'source' => 'schedule']);
    }

    // fallback: return doctor's default time_slots if set
    $doctor = User::find($doctorId);
    $default = $doctor && $doctor->doctorProfile ? $doctor->doctorProfile->time_slots ?? [] : [];
    return response()->json(['slots' => $default, 'source' => 'default']);
}


}