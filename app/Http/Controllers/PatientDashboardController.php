<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $upcomingAppointments = Appointment::where('patient_id', $user->id)
            ->where('date_time', '>', now())
            ->with('doctor')
            ->orderBy('date_time', 'asc')
            ->get();

        $pastAppointments = Appointment::where('patient_id', $user->id)
            ->where('date_time', '<=', now())
            ->with('doctor')
            ->orderBy('date_time', 'desc')
            ->get();

        return view('patient.dashboard', compact('upcomingAppointments', 'pastAppointments'));
    }

    public function updatePersonal(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date|before:today', // ✅ DOB future me na ho
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'address' => 'nullable|string|max:500', // ✅ length limit add
        ]);

        $user = Auth::user();
        // 👇 Prepare data to update
    $data = $request->only([
        'first_name', 'last_name', 'dob', 'gender', 'phone', 'email', 'address'
    ]);


    // 👇 Auto-update 'name' field by combining first_name + last_name
    $data['name'] = trim($data['first_name'] . ' ' . $data['last_name']);

    // 👇 Now update user with 'name' included
    $user->update($data);


        return response()->json(['success' => true, 'message' => 'Personal info updated']);
    }

    public function updateHealth(Request $request)
    {
        $request->validate([
    
            'blood_group' => 'nullable|string|max:10',
            'height' => 'nullable|numeric|min:30|max:250',   // ✅ realistic limits
            'weight' => 'nullable|numeric|min:2|max:500',
            'emergency_contact' => 'nullable|string|max:15',
            'medical_history' => 'nullable|string',
        ]);

        $user = Auth::user();
        $data = $request->only(['blood_group', 'height', 'weight', 'emergency_contact']);
        $data['medical_history'] = $request->medical_history
            ? json_encode(json_decode($request->medical_history, true))
            : null;

        // Upsert patient profile
        $user->patientProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json(['success' => true, 'message' => 'Health info updated']);
    }

    public function updateImage(Request $request)
{
    $request->validate([
        'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user = auth()->user();
    $profile = $user->patientProfile;

    if (!$profile) {
        return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
    }

    try {
        // ✅ delete old image if exists
        if ($profile->profile_image && \Storage::disk('public')->exists($profile->profile_image)) {
            \Storage::disk('public')->delete($profile->profile_image);
        }

        // ✅ store with unique filename
        $path = $request->file('profile_image')->store('patient_images', 'public');

        $profile->update([
            'profile_image' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile image updated.',
            'path' => asset('storage/' . $path),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Image upload failed. Try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}