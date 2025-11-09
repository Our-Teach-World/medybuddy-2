<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Log facade import karein

class AuthController extends Controller
{
    // Registration
    public function register(Request $request)
    {
         \Log::info('Register request data: ', $request->all());
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users',
            'phone'      => 'required',
            'address'    => 'required',
            'password'   => 'required|min:8|confirmed',
            'role'       => 'required|in:patient,doctor',

            // Doctor
            'specialization' => 'required_if:role,doctor',
            'license'        => 'required_if:role,doctor',
            'experience'     => 'required_if:role,doctor',

            // Patient
            'dob'    => 'required_if:role,patient|date',
            'gender' => 'required_if:role,patient',
        ]);

        try {
            $user = User::create([
                'name'          => $request->first_name . ' ' . $request->last_name,
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'role'          => $request->role,
                'specialization'=> $request->specialization,
                'license'       => $request->license,
                'experience'    => $request->experience,
                'dob'           => $request->dob,
                'gender'        => $request->gender,
                'password'      => Hash::make($request->password),
            ]);



            if ($request->role === 'patient') {
                $year = date('Y');
                $id = $user->id;
                $patientIdentifier = "#MED-{$year}-" . str_pad($id, 3, '0', STR_PAD_LEFT);

                PatientProfile::create([
                    'user_id' => $user->id,
                    'patient_identifier' => $patientIdentifier,
                    'medical_history' => json_encode(['medications' => [], 'allergies' => []]),
                ]);
            }

            if ($request->role === 'doctor') {
                if ($request->role === 'doctor') {

    // Debug line
    Log::info('Doctor register debug:', [
        'specialization' => $request->specialization,
        'license'        => $request->license,
        'experience'     => $request->experience,
    ]);

    DoctorProfile::create([
        'user_id' => $user->id,
        'specialization' => $request->specialization,
        'license'        => $request->license,
        'experience'     => $request->experience,
    ]);
}

            }
            
            return redirect()->route('authentication')->with('success', 'Registration successful!');

        } catch (\Exception $e) {
            // Error ko log karein aur user ko bata dein
            Log::error('Registration failed: ' . $e->getMessage());
            return back()->withErrors(['registration' => 'An error occurred during registration. Please try again.']);
        }
    }

    // Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Role ke hisab se redirect karein
            $user = Auth::user();
            if ($user->role === 'doctor') {
                return redirect()->route('home')->with('success', 'Welcome, Dr. ' . $user->first_name . '!');
            }
            // Patient ke liye default redirection
            return redirect()->route('home')->with('success', 'Welcome, ' . $user->first_name . '!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials provided.',
        ])->onlyInput('email');
    }
}