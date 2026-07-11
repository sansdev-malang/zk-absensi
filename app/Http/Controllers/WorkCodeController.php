<?php

namespace App\Http\Controllers;

use App\Models\WorkCode;
use Illuminate\Http\Request;

class WorkCodeController extends Controller
{
    public function index()
    {
        $workCodes = WorkCode::latest()->get();
        return view('work-codes.index', compact('workCodes'));
    }

    public function create()
    {
        return view('work-codes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:255|unique:work_codes,kode',
            'deskripsi' => 'nullable|string',
        ]);

        WorkCode::create($validated);

        return redirect()->route('work-codes.index')->with('success', 'Work Code berhasil ditambahkan.');
    }

    public function edit(WorkCode $workCode)
    {
        return view('work-codes.edit', compact('workCode'));
    }

    public function update(Request $request, WorkCode $workCode)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:255|unique:work_codes,kode,' . $workCode->id,
            'deskripsi' => 'nullable|string',
        ]);

        $workCode->update($validated);

        return redirect()->route('work-codes.index')->with('success', 'Work Code berhasil diperbarui.');
    }

    public function destroy(WorkCode $workCode)
    {
        $workCode->delete();

        return redirect()->route('work-codes.index')->with('success', 'Work Code berhasil dihapus.');
    }
}
