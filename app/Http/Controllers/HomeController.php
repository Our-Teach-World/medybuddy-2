<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // unique specialization ke saath doctors count
        $categories = User::where('role', 'doctor')
            ->select('specialization')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('specialization')
            ->get();

        // meet our doctors (latest 6 doctors)
        $doctors = User::with('doctorProfile')
            ->where('role', 'doctor')
            ->latest()
            ->take(6)
            ->get();

        return view('index', compact('categories', 'doctors'));
    }
}
