<?php

namespace App\Services;

use App\Models\User;
use App\Models\Shift;
use App\Models\ShiftDetail;
use App\Models\UserShift;
use App\Models\Attendance;
use App\Models\DailyAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceCalculatorService
{
    /**
     * Kalkulasi semua user pada tanggal tertentu
     */
    public function calculateAllForDate(Carbon $date)
    {
        $users = User::whereDoesntHave('roles', function($q) {
            $q->where('name', 'Admin');
        })->get();
        
        foreach ($users as $user) {
            $this->calculateUserDaily($user, $date);
        }
    }

    /**
     * Kalkulasi untuk 1 user pada 1 tanggal
     */
    public function calculateUserDaily(User $user, Carbon $date)
    {
        // 1. Tentukan Shift yang berlaku
        // Cek Roster (UserShift) dulu
        $roster = UserShift::where('user_id', $user->id)
                           ->where('tanggal', $date->toDateString())
                           ->first();
        
        $shiftId = $roster ? $roster->shift_id : $user->default_shift_id;

        if (!$shiftId) {
            // User tidak punya shift pada hari itu, abaikan
            return;
        }

        $shift = Shift::with('bonusScheme.rules')->find($shiftId);
        if (!$shift) return;

        // 2. Tentukan ShiftDetail (jam kerja pada hari tersebut)
        $dayOfWeek = $date->dayOfWeek; // 0 (Sunday) - 6 (Saturday)
        
        $shiftDetail = ShiftDetail::where('shift_id', $shiftId)
                                  ->where('hari', $dayOfWeek)
                                  ->first();

        if (!$shiftDetail) {
            // Libur
            DailyAttendance::updateOrCreate(
                ['user_id' => $user->id, 'tanggal' => $date->toDateString()],
                [
                    'shift_detail_id' => null,
                    'status_kehadiran' => 'Libur',
                    'bonus_didapat' => 0
                ]
            );
            return;
        }

        // 3. Cari Log Sidik Jari (Raw Attendances)
        $startWindow = $date->copy()->startOfDay();
        $endWindow = $date->copy()->endOfDay();

        if ($shiftDetail->is_cross_day) {
            // Misal Satpam Shift 3 (22:00 - 06:00)
            // Log masuk dimulai dari sore/malam hari H, sampai siang hari H+1
            $startWindow = $date->copy()->setTime(14, 0, 0); // Jam 2 siang
            $endWindow = $date->copy()->addDay()->setTime(12, 0, 0); // Jam 12 siang besoknya
        }

        $logs = Attendance::where('user_id', $user->id)
                          ->whereBetween('waktu', [$startWindow, $endWindow])
                          ->orderBy('waktu', 'asc')
                          ->get();

        $jamMasuk = $logs->first() ? Carbon::parse($logs->first()->waktu) : null;
        $jamPulang = null;
        
        if ($jamMasuk && $logs->count() > 1) {
            $lastLog = Carbon::parse($logs->last()->waktu);
            // Mencegah double tap dalam waktu berdekatan (< 30 menit) dianggap sebagai jam pulang
            if ($jamMasuk->diffInMinutes($lastLog) >= 30) {
                $jamPulang = $lastLog;
            }
        }

        // 4. Kalkulasi Keterlambatan
        $menitTerlambat = 0;
        $menitPulangCepat = 0;
        $status = 'Alfa';
        $bonus = 0;

        if ($jamMasuk) {
            $targetMasuk = $date->copy()->setTimeFromTimeString($shiftDetail->jam_masuk);
            
            // diffInMinutes dg parameter false = hasil positif jika $jamMasuk > $targetMasuk
            $menitTerlambatRaw = $targetMasuk->diffInMinutes($jamMasuk, false);
            
            // Kita hitung menit terlambat hanya jika positif
            $menitTerlambat = $menitTerlambatRaw > 0 ? (int)$menitTerlambatRaw : 0;
            
            $status = $menitTerlambat > 0 ? 'Terlambat' : 'Hadir';

            // 5. Kalkulasi Bonus (berdasarkan aturan menit_terlambatRaw)
            // Jika masuk lebih awal, nilainya negatif. Jika telat, positif.
            if ($shift->bonus_scheme_id && $shift->bonusScheme) {
                $rules = $shift->bonusScheme->rules;
                foreach ($rules as $rule) {
                    if ($menitTerlambatRaw >= $rule->min_menit && $menitTerlambatRaw <= $rule->max_menit) {
                        $bonus = $rule->nominal;
                        break;
                    }
                }
            }

            // Hitung pulang cepat
            if ($jamPulang) {
                $targetPulangDate = $shiftDetail->is_cross_day ? $date->copy()->addDay() : $date->copy();
                $targetPulang = $targetPulangDate->setTimeFromTimeString($shiftDetail->jam_pulang);
                
                // Jika jamPulang < targetPulang, berarti pulang cepat
                $diffPulang = $jamPulang->diffInMinutes($targetPulang, false);
                if ($diffPulang > 0) { // Jam pulang belum waktunya
                    $menitPulangCepat = (int)$diffPulang;
                }
            }
        }

        // 6. Simpan ke DailyAttendance
        DailyAttendance::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $date->toDateString()],
            [
                'shift_detail_id' => $shiftDetail->id,
                'jam_masuk' => $jamMasuk,
                'jam_pulang' => $jamPulang,
                'menit_terlambat' => $menitTerlambat,
                'menit_pulang_cepat' => $menitPulangCepat,
                'status_kehadiran' => $status,
                'bonus_didapat' => $bonus
            ]
        );
    }
}
