<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        // Validate GET inputs to limit abuse
        $validator = Validator::make($request->all(), [
            'q' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            // For AJAX requests respond gracefully
            if ($request->ajax()) {
                return response()->json(['error' => 'Invalid parameters'], 422);
            }
            $request->merge(['q' => '', 'specialization' => 'All']);
        }

        $q = trim($request->query('q', ''));
        $specialization = $request->query('specialization', 'All');

        $query = User::where('role', 'doctor')->with('doctorProfile');

        if ($specialization && $specialization !== 'All') {
            $query->whereHas('doctorProfile', function ($q2) use ($specialization) {
                $q2->where('specialization', $specialization);
            });
        }

        if (!empty($q)) {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('first_name', 'like', "%{$q}%")
                   ->orWhere('last_name', 'like', "%{$q}%")
                   ->orWhereHas('doctorProfile', function ($q2) use ($q) {
                       $q2->where('specialization', 'like', "%{$q}%")
                          ->orWhere('hospital_name', 'like', "%{$q}%");
                   });
            });
        }

        $perPage = min($request->query('per_page', 12), 50);
        $doctors = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        $specializations = DoctorProfile::whereNotNull('specialization')
            ->distinct()
            ->orderBy('specialization')
            ->pluck('specialization')
            ->toArray();

        // If AJAX: return rendered partial (HTML) to inject on client
        if ($request->ajax()) {
    return response()->json([
        'html' => view('doctor._results', compact('doctors'))->render()
    ]);
        }

        return view('doctor.index', compact('doctors', 'specializations', 'q', 'specialization'));
    }

    public function show($id)
    {
        $doctor = User::where('id', $id)
            ->where('role', 'doctor')
            ->with(['doctorProfile', 'reviews.patient'])
            ->firstOrFail();

        $start = Carbon::today();
        $end = Carbon::today()->addDays(14);

        $schedules = Schedule::where('doctor_id', $id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get()
            ->keyBy(fn($s) => $s->date->toDateString());

        return view('doctor.show', compact('doctor', 'schedules'));
    }
}
