<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyAttendance;
use Carbon\Carbon;

use App\Services\PayrollPeriodService;

class ReportController extends Controller
{
    public function index(Request $request, PayrollPeriodService $periodService)
    {
        $query = DailyAttendance::with(['user', 'shiftDetail.shift'])
            ->whereHas('user', function($q) {
                $q->whereDoesntHave('roles', function($r) {
                    $r->where('name', 'Admin');
                });
            });

        // Limit to own data if not Admin/HR/Supervisor
        if (!auth()->user()->hasAnyRole(['Admin', 'HR', 'Supervisor'])) {
            $query->where('user_id', auth()->id());
        }

        [$defaultStart, $defaultEnd] = $periodService->calculatePeriod(Carbon::now());
        $predefinedPeriods = $periodService->getPredefinedPeriods(3);

        // Filter by Date Range if provided
        $startDate = $request->has('start_date') ? $request->start_date : $defaultStart;
        $endDate = $request->has('end_date') ? $request->end_date : $defaultEnd;

        $query->whereBetween('tanggal', [$startDate, $endDate]);

        // Filter by User
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Calculate Totals before pagination
        $totalBonus = (clone $query)->sum('bonus_didapat');
        $totalTerlambat = (clone $query)->sum('menit_terlambat');

        $reports = $query->orderBy('tanggal', 'desc')
                         ->orderBy('user_id')
                         ->paginate(50)
                         ->withQueryString();

        $usersList = \App\Models\User::whereDoesntHave('roles', function($r) {
            $r->where('name', 'Admin');
        })->orderBy('name')->get();

        return view('reports.index', compact('reports', 'startDate', 'endDate', 'totalBonus', 'totalTerlambat', 'predefinedPeriods', 'usersList'));
    }

    public function summary(Request $request, PayrollPeriodService $periodService)
    {
        [$defaultStart, $defaultEnd] = $periodService->calculatePeriod(Carbon::now());
        $predefinedPeriods = $periodService->getPredefinedPeriods(3);

        $startDate = $request->has('start_date') ? $request->start_date : $defaultStart;
        $endDate = $request->has('end_date') ? $request->end_date : $defaultEnd;

        $query = \App\Models\User::whereDoesntHave('roles', function($r) {
            $r->where('name', 'Admin');
        });

        // Limit to own data if not Admin/HR/Supervisor
        if (!auth()->user()->hasAnyRole(['Admin', 'HR', 'Supervisor'])) {
            $query->where('id', auth()->id());
        }

        $users = $query->get();
        $summaries = [];
        $totalBonus = 0;
        $totalTerlambat = 0;

        foreach ($users as $user) {
            $attendances = DailyAttendance::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();
            
            if ($attendances->isEmpty()) {
                continue;
            }

            $summary = [
                'user' => $user,
                'total_hadir_tepat' => $attendances->where('status_kehadiran', 'Hadir')->count(),
                'total_terlambat' => $attendances->where('status_kehadiran', 'Terlambat')->count(),
                'total_menit_terlambat' => $attendances->sum('menit_terlambat'),
                'total_bonus' => $attendances->sum('bonus_didapat'),
                'total_hari_kerja' => $attendances->count(),
            ];

            $summaries[] = $summary;
            $totalBonus += $summary['total_bonus'];
            $totalTerlambat += $summary['total_menit_terlambat'];
        }

        // Sort by total bonus descending, or by user name
        usort($summaries, function($a, $b) {
            return strcmp($a['user']->name, $b['user']->name);
        });

        return view('reports.summary', compact('summaries', 'startDate', 'endDate', 'totalBonus', 'totalTerlambat', 'predefinedPeriods'));
    }

    public function printSummary(Request $request, PayrollPeriodService $periodService)
    {
        [$defaultStart, $defaultEnd] = $periodService->calculatePeriod(Carbon::now());
        
        $startDate = $request->has('start_date') ? $request->start_date : $defaultStart;
        $endDate = $request->has('end_date') ? $request->end_date : $defaultEnd;

        $query = \App\Models\User::whereDoesntHave('roles', function($r) {
            $r->where('name', 'Admin');
        });

        if (!auth()->user()->hasAnyRole(['Admin', 'HR', 'Supervisor'])) {
            $query->where('id', auth()->id());
        }

        $users = $query->get();
        $summaries = [];
        $totalBonus = 0;
        $totalTerlambat = 0;

        foreach ($users as $user) {
            $attendances = DailyAttendance::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();
            
            if ($attendances->isEmpty()) {
                continue;
            }

            $summary = [
                'user' => $user,
                'total_hadir_tepat' => $attendances->where('status_kehadiran', 'Hadir')->count(),
                'total_terlambat' => $attendances->where('status_kehadiran', 'Terlambat')->count(),
                'total_menit_terlambat' => $attendances->sum('menit_terlambat'),
                'total_bonus' => $attendances->sum('bonus_didapat'),
                'total_hari_kerja' => $attendances->count(),
            ];

            $summaries[] = $summary;
            $totalBonus += $summary['total_bonus'];
            $totalTerlambat += $summary['total_menit_terlambat'];
        }

        usort($summaries, function($a, $b) {
            return strcmp($a['user']->name, $b['user']->name);
        });

        return view('reports.print-summary', compact('summaries', 'startDate', 'endDate', 'totalBonus', 'totalTerlambat'));
    }

    public function recalculate(Request $request, \App\Services\AttendanceCalculatorService $calculator, PayrollPeriodService $periodService)
    {
        // Hindari timeout jika data banyak
        set_time_limit(0);
        ignore_user_abort(true);

        [$defaultStart, $defaultEnd] = $periodService->calculatePeriod(Carbon::now());
        $startDate = $request->input('start_date', $defaultStart);
        $endDate = $request->input('end_date', $defaultEnd);

        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($currentDate->lte($end)) {
            $calculator->calculateAllForDate($currentDate);
            $currentDate->addDay();
        }

        return redirect()->back()->with('success', "Berhasil mengkalkulasi ulang seluruh data absensi dan bonus dari $startDate sampai $endDate.");
    }
}
