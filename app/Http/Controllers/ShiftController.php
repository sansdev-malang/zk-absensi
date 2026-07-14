<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with('details', 'bonusScheme')->get();
        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        $bonusSchemes = \App\Models\BonusScheme::all();
        return view('shifts.create', compact('bonusSchemes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'bonus_scheme_id' => 'nullable|exists:bonus_schemes,id',
            'details' => 'array',
        ]);

        $shift = Shift::create([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'bonus_scheme_id' => $request->bonus_scheme_id,
        ]);

        if ($request->has('details')) {
            foreach ($request->details as $hari => $detail) {
                if (!empty($detail['jam_masuk']) && !empty($detail['jam_pulang'])) {
                    $jamMasuk = $detail['jam_masuk'];
                    $jamPulang = $detail['jam_pulang'];
                    
                    // Deteksi cross day otomatis
                    $isCrossDay = (strtotime($jamPulang) < strtotime($jamMasuk));
                    
                    \App\Models\ShiftDetail::create([
                        'shift_id' => $shift->id,
                        'hari' => $hari,
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'is_cross_day' => $isCrossDay,
                    ]);
                }
            }
        }

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil ditambahkan.');
    }

    public function edit(Shift $shift)
    {
        $shift->load('details');
        $bonusSchemes = \App\Models\BonusScheme::all();
        
        $detailsByDay = [];
        foreach ($shift->details as $detail) {
            $detailsByDay[$detail->hari] = $detail;
        }

        return view('shifts.edit', compact('shift', 'bonusSchemes', 'detailsByDay'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'bonus_scheme_id' => 'nullable|exists:bonus_schemes,id',
            'details' => 'array',
        ]);

        $shift->update([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'bonus_scheme_id' => $request->bonus_scheme_id,
        ]);

        $shift->details()->delete();

        if ($request->has('details')) {
            foreach ($request->details as $hari => $detail) {
                if (!empty($detail['jam_masuk']) && !empty($detail['jam_pulang'])) {
                    $jamMasuk = $detail['jam_masuk'];
                    $jamPulang = $detail['jam_pulang'];
                    
                    $isCrossDay = (strtotime($jamPulang) < strtotime($jamMasuk));
                    
                    \App\Models\ShiftDetail::create([
                        'shift_id' => $shift->id,
                        'hari' => $hari,
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'is_cross_day' => $isCrossDay,
                    ]);
                }
            }
        }

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        if ($shift->usersWithDefault()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus Shift karena masih digunakan sebagai default oleh karyawan.');
        }

        $shift->details()->delete();
        $shift->delete();

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil dihapus.');
    }
}
