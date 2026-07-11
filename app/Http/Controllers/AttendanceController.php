<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with(['user', 'device'])
            ->orderBy('waktu', 'desc')
            ->paginate(50);
            
        return view('attendances.index', compact('attendances'));
    }
}
