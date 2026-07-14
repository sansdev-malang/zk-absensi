<?php

namespace App\Http\Controllers;

use App\Models\UserShift;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Services\PayrollPeriodService;

class ScheduleController extends Controller
{
    public function index(Request $request, PayrollPeriodService $periodService)
    {
        [$defaultStart, $defaultEnd] = $periodService->calculatePeriod(Carbon::now());
        
        $startDate = $request->input('start_date', $defaultStart);
        $endDate = $request->input('end_date', $defaultEnd);

        // Prepare predefined periods for dropdown (last 3 periods)
        $predefinedPeriods = $periodService->getPredefinedPeriods(3);
        
        $schedules = UserShift::with(['user', 'shift'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();
            
        // Group by Shift ID, then by User ID
        $groupedSchedules = $schedules->groupBy('shift_id')->map(function ($shifts) {
            return $shifts->groupBy('user_id');
        });

        return view('schedules.index', compact('groupedSchedules', 'startDate', 'endDate', 'predefinedPeriods'));
    }

    public function create(PayrollPeriodService $periodService)
    {
        $users = User::whereDoesntHave('roles', function($q) {
            $q->where('name', 'Admin');
        })->get();
        $shifts = Shift::all();
        [$defaultStart, $defaultEnd] = $periodService->calculatePeriod(Carbon::now());
        
        return view('schedules.create', compact('users', 'shifts', 'defaultStart', 'defaultEnd'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $period = CarbonPeriod::create($request->start_date, $request->end_date);
        
        $countDays = 0;
        $countUsers = count($request->user_ids);

        foreach ($request->user_ids as $userId) {
            $days = 0;
            foreach ($period as $date) {
                UserShift::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'tanggal' => $date->format('Y-m-d'),
                    ],
                    [
                        'shift_id' => $request->shift_id,
                    ]
                );
                $days++;
            }
            $countDays = $days; // same for all users
        }

        return redirect()->route('schedules.index')->with('success', "Berhasil menugaskan $countUsers karyawan untuk $countDays hari jadwal.");
    }

    public function autoGenerate(PayrollPeriodService $periodService)
    {
        // Get current period range
        [$startDate, $endDate] = $periodService->calculatePeriod(Carbon::now());
        
        // Find users who are NOT admin and have a default_shift_id
        $users = User::whereDoesntHave('roles', function($q) {
            $q->where('name', 'Admin');
        })->whereNotNull('default_shift_id')->get();

        if ($users->isEmpty()) {
            return redirect()->route('schedules.index')->with('error', 'Tidak ada karyawan yang memiliki Default Shift untuk di-generate secara otomatis.');
        }

        $period = CarbonPeriod::create($startDate, $endDate);
        $countUsers = 0;

        foreach ($users as $user) {
            foreach ($period as $date) {
                UserShift::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'tanggal' => $date->format('Y-m-d'),
                    ],
                    [
                        'shift_id' => $user->default_shift_id,
                    ]
                );
            }
            $countUsers++;
        }

        $formattedStart = Carbon::parse($startDate)->format('d M Y');
        $formattedEnd = Carbon::parse($endDate)->format('d M Y');

        return redirect()->route('schedules.index')->with('success', "Generate Otomatis Berhasil! Jadwal untuk $countUsers karyawan telah dibuat dari tanggal $formattedStart hingga $formattedEnd.");
    }

    public function destroy(UserShift $schedule)
    {
        $schedule->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
