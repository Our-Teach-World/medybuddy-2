<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(Request $request)
        {
                $request->validate([
                            'doctor_id' => 'required|exists:users,id',
                                        'date' => 'required|date',
                                                    'time' => 'required',
                                                                'consultation_mode' => 'required|in:In-person,Video,Phone',
                                                                        ]);

                                                                                $appointment = new Appointment();
                                                                                        $appointment->patient_id = auth()->id(); // logged in patient
                                                                                                $appointment->doctor_id = $request->doctor_id;
                                                                                                        $appointment->date_time = $request->date . ' ' . $request->time; // tumhare appointments table me `date_time` hai
                                                                                                                $appointment->consultation_mode = $request->consultation_mode; // ✅ Point 8
                                                                                                                        $appointment->status = 'pending';
                                                                                                                                $appointment->save();

                                                                                                                                        return response()->json([
                                                                                                                                                    'success' => true,
                                                                                                                                                                'message' => 'Appointment booked successfully!',
                                                                                                                                                                            'data' => $appointment
                                                                                                                                                                                    ]);
                                                                                                                                                                                        }
                                                                                                                                                                                        }
                                                                                                                                                                                        