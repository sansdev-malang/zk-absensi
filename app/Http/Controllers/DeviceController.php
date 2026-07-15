<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::latest()->get();
        
        $devices->map(function ($device) {
            $isOnline = false;
            if ($device->status) {
                // Mode 1: Cek dari last_sync_at (Cloud-friendly)
                if ($device->last_sync_at && $device->last_sync_at->diffInMinutes(now()) <= 15) {
                    $isOnline = true;
                } else {
                    // Mode 2: Fallback ke Ping langsung (Local-friendly)
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        exec("ping -n 1 -w 1000 " . escapeshellarg($device->ip_address), $output, $status);
                    } else {
                        exec("ping -c 1 -W 1 " . escapeshellarg($device->ip_address), $output, $status);
                    }
                    if ($status === 0) {
                        $isOnline = true;
                    }
                }
            }
            $device->is_online = $isOnline;
            return $device;
        });

        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mesin' => 'required|string|max:255',
            'nomor_mesin' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'required|integer',
            'comm_key' => 'nullable|string',
            'status' => 'boolean',
        ]);

        Device::create($validated);

        return redirect()->route('devices.index')->with('success', 'Perangkat ZKTeco berhasil ditambahkan.');
    }

    public function edit(Device $device)
    {
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'nama_mesin' => 'required|string|max:255',
            'nomor_mesin' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'required|integer',
            'comm_key' => 'nullable|string',
            'status' => 'boolean',
        ]);

        // checkbox might not be sent if false
        if (!$request->has('status')) {
            $validated['status'] = 0;
        }

        $device->update($validated);

        return redirect()->route('devices.index')->with('success', 'Perangkat ZKTeco berhasil diperbarui.');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Perangkat ZKTeco berhasil dihapus.');
    }

    public function clearLogs(Device $device)
    {
        $zkteco = new \App\Services\ZktecoService($device->ip_address, $device->port);
        $result = $zkteco->clearAttendance();

        if ($result) {
            return redirect()->route('devices.index')->with('success', "Seluruh log absensi di mesin {$device->nama_mesin} berhasil dibersihkan (Kosong).");
        } else {
            return redirect()->route('devices.index')->with('error', "Gagal membersihkan log di mesin {$device->nama_mesin}. Pastikan mesin dalam keadaan aktif dan terhubung.");
        }
    }
}
