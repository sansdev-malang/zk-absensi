<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyAttendance;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyAttendance::with(['user', 'shiftDetail.shift']);

        // Filter by Date Range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } else {
            // Default to current month
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
            $query->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        }

        $reports = $query->orderBy('tanggal', 'desc')
                         ->orderBy('user_id')
                         ->paginate(50)
                         ->withQueryString();

        return view('reports.index', compact('reports'));
    }

    public function recalculate(Request $request, \App\Services\AttendanceCalculatorService $calculator)
    {
        // Hindari timeout jika data banyak
        set_time_limit(0);
        ignore_user_abort(true);

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($currentDate->lte($end)) {
            $calculator->calculateAllForDate($currentDate);
            $currentDate->addDay();
        }

        return redirect()->back()->with('success', "Berhasil mengkalkulasi ulang seluruh data absensi dan bonus dari $startDate sampai $endDate.");
    }
}
