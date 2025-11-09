<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AppointmentController;

// ---------------------------
// Home Page
// ---------------------------
Route::get('/', function () {
    return view('index');
})->name('home');

// ---------------------------
// Authentication Routes
// ---------------------------
Route::get('/authentication', function () {
    return view('authentication');
})->name('authentication');

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('home')->with('success', 'Logged out successfully!');
})->name('logout');

// ---------------------------
// Patient Routes
// ---------------------------
Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/patient/dashboard', [PatientDashboardController::class, 'index'])->name('patient.dashboard');
    Route::post('/patient/update/personal', [PatientDashboardController::class, 'updatePersonal'])->name('patient.update.personal');
    Route::post('/patient/update/health', [PatientDashboardController::class, 'updateHealth'])->name('patient.update.health');
});

// ---------------------------
// Doctor Routes — FIXED & MATCHED WITH JS
// ---------------------------
Route::middleware(['auth', 'role:doctor'])->group(function () {

    // Dashboard
    Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');

    // Profile Update — matches JS: /doctor/profile/{id}/update
    Route::post('/doctor/profile/{id}/update', [DoctorDashboardController::class, 'updateProfile'])->name('doctor.profile.update');

    // Professional Details — matches JS: /doctor/professional/{id}/update
    Route::post('/doctor/professional/{id}/update', [DoctorDashboardController::class, 'updateProfessional'])->name('doctor.professional.update');

    // Contact Info — matches JS: /doctor/contact/{id}/update
    Route::post('/doctor/contact/{id}/update', [DoctorDashboardController::class, 'updateContact'])->name('doctor.contact.update');

    // Appointment/Schedule — matches JS: /doctor/appointment/{id}/update
    Route::post('/doctor/appointment/{id}/update', [DoctorDashboardController::class, 'updateSchedule'])->name('doctor.appointment.update');

});

// Save per-date schedule
Route::post('/doctor/schedule/save', [DoctorDashboardController::class, 'saveSchedule'])->name('doctor.schedule.save');

// Get schedule for a date (AJAX for patient to fetch)
Route::get('/doctor/schedule', [DoctorDashboardController::class, 'getSchedule'])->name('doctor.schedule.get');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');



Route::get('/doctor', [DoctorController::class, 'index'])->name('doctor.index');
Route::get('/doctor/{id}', [DoctorController::class, 'show'])->name('doctor.show');



Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/patient/update-image', [PatientDashboardController::class, 'updateImage'])->name('patient.update.image');
