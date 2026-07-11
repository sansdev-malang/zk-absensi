<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BonusScheme;
use App\Models\BonusRule;

class BonusSchemeController extends Controller
{
    public function index()
    {
        $schemes = BonusScheme::withCount('rules')->get();
        return view('bonus-schemes.index', compact('schemes'));
    }

    public function create()
    {
        return view('bonus-schemes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        BonusScheme::create($request->only(['nama', 'deskripsi']));

        return redirect()->route('bonus-schemes.index')->with('success', 'Skema Bonus berhasil ditambahkan. Silakan klik "Atur Rentang" untuk memasukkan nominal.');
    }

    public function show(BonusScheme $bonusScheme)
    {
        $bonusScheme->load(['rules' => function ($query) {
            $query->orderBy('min_menit', 'asc');
        }]);
        return view('bonus-schemes.show', compact('bonusScheme'));
    }

    public function update(Request $request, BonusScheme $bonusScheme)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        $bonusScheme->update($request->only(['nama', 'deskripsi']));

        return redirect()->route('bonus-schemes.index')->with('success', 'Skema Bonus berhasil diperbarui.');
    }

    public function destroy(BonusScheme $bonusScheme)
    {
        $bonusScheme->delete();
        return redirect()->route('bonus-schemes.index')->with('success', 'Skema Bonus berhasil dihapus.');
    }

    // Custom method to add rules to a scheme
    public function storeRule(Request $request, BonusScheme $bonusScheme)
    {
        $request->validate([
            'min_menit' => 'required|integer',
            'max_menit' => 'required|integer|gte:min_menit',
            'nominal' => 'required|numeric|min:0'
        ]);

        $bonusScheme->rules()->create($request->only(['min_menit', 'max_menit', 'nominal']));

        return redirect()->route('bonus-schemes.show', $bonusScheme->id)->with('success', 'Kriteria jam absen berhasil ditambahkan.');
    }

    public function destroyRule(BonusRule $bonusRule)
    {
        $schemeId = $bonusRule->bonus_scheme_id;
        $bonusRule->delete();
        return redirect()->route('bonus-schemes.show', $schemeId)->with('success', 'Kriteria jam absen berhasil dihapus.');
    }
}
