<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['user', 'device'])
            ->selectRaw('DATE(waktu) as tanggal, user_id, MIN(waktu) as jam_masuk, MAX(waktu) as jam_pulang, MAX(device_id) as device_id');

        if ($request->filled('start_date')) {
            $query->whereDate('waktu', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('waktu', '<=', $request->end_date);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('uid', 'like', "%{$search}%");
            });
        }

        $attendances = $query->groupBy('tanggal', 'user_id')
            ->orderBy('tanggal', 'desc')
            ->orderBy('user_id', 'asc')
            ->paginate(50)
            ->appends($request->all());
            
        return view('attendances.index', compact('attendances'));
    }

    public function create()
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('attendances.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_pulang' => 'nullable|date_format:H:i',
        ]);

        if (empty($request->jam_masuk) && empty($request->jam_pulang)) {
            return back()->with('error', 'Minimal isi Jam Masuk atau Jam Pulang.')->withInput();
        }

        // Simpan Jam Masuk (Status 0)
        if (!empty($request->jam_masuk)) {
            Attendance::create([
                'user_id' => $request->user_id,
                'device_id' => null,
                'waktu' => $request->tanggal . ' ' . $request->jam_masuk . ':00',
                'status' => '0', 
                'uid_log' => null,
            ]);
        }

        // Simpan Jam Pulang (Status 1)
        if (!empty($request->jam_pulang)) {
            Attendance::create([
                'user_id' => $request->user_id,
                'device_id' => null,
                'waktu' => $request->tanggal . ' ' . $request->jam_pulang . ':00',
                'status' => '1',
                'uid_log' => null,
            ]);
        }

        return redirect()->route('attendances.index')->with('success', 'Data absensi manual berhasil ditambahkan.');
    }
}
