<?php

namespace App\Http\Controllers;

use App\Models\UserShift;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        $schedules = UserShift::with(['user', 'shift'])
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        return view('schedules.index', compact('schedules', 'month', 'year'));
    }

    public function create()
    {
        $users = User::all();
        $shifts = Shift::all();
        return view('schedules.create', compact('users', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $period = CarbonPeriod::create($request->start_date, $request->end_date);
        
        $count = 0;
        foreach ($period as $date) {
            UserShift::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'tanggal' => $date->format('Y-m-d'),
                ],
                [
                    'shift_id' => $request->shift_id,
                ]
            );
            $count++;
        }

        return redirect()->route('schedules.index')->with('success', "Berhasil menambahkan $count hari jadwal.");
    }

    public function destroy(UserShift $schedule)
    {
        $schedule->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
