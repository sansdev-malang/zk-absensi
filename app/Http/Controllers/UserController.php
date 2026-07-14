<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Device;
use App\Services\ZktecoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'defaultShift'])->whereDoesntHave('roles', function($q) {
            $q->where('name', 'Admin');
        })->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('uid', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->jabatan);
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->get();
        $roles = Role::all();
        $jabatans = User::whereNotNull('jabatan')->where('jabatan', '!=', '')->distinct()->pluck('jabatan');
        $shifts = \App\Models\Shift::all();

        return view('users.index', compact('users', 'roles', 'jabatans', 'shifts'));
    }

    public function updateShift(Request $request, User $user)
    {
        $request->validate([
            'default_shift_id' => ['nullable', 'exists:shifts,id'],
        ]);

        $user->update([
            'default_shift_id' => $request->default_shift_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Default Shift berhasil diperbarui'
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        $shifts = \App\Models\Shift::all();
        return view('users.create', compact('roles', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'uid' => ['nullable', 'string', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', 'exists:roles,name'],
            'default_shift_id' => ['nullable', 'exists:shifts,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'uid' => $request->uid,
            'default_shift_id' => $request->default_shift_id,
        ]);

        $user->assignRole($request->role);

        // Sinkronisasi ke Mesin ZKTeco
        if ($user->uid) {
            $this->syncUserToDevices($user->uid, $user->name, $request->role);
        }

        return redirect()->route('users.index')->with('success', 'Data Karyawan berhasil ditambahkan dan disinkronisasikan ke mesin.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $shifts = \App\Models\Shift::all();
        return view('users.edit', compact('user', 'roles', 'shifts'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'uid' => ['nullable', 'string', 'max:255', 'unique:'.User::class.',uid,'.$user->id],
            'role' => ['required', 'string', 'exists:roles,name'],
            'default_shift_id' => ['nullable', 'exists:shifts,id'],
        ]);

        $oldUid = $user->uid;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'uid' => $request->uid,
            'default_shift_id' => $request->default_shift_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        // Sinkronisasi Perubahan ke Mesin
        if ($oldUid && $oldUid !== $user->uid) {
            // Hapus UID lama
            $this->removeUserFromDevices($oldUid);
        }
        if ($user->uid) {
            $this->syncUserToDevices($user->uid, $user->name, $request->role);
        }

        return redirect()->route('users.index')->with('success', 'Data Karyawan berhasil diperbarui dan disinkronisasikan ke mesin.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->hasRole('Admin') && User::role('Admin')->count() === 1) {
            return back()->with('error', 'Tidak dapat menghapus satu-satunya Admin di sistem.');
        }

        $uid = $user->uid;
        $user->delete();

        // Hapus dari mesin
        if ($uid) {
            $this->removeUserFromDevices($uid);
        }

        return redirect()->route('users.index')->with('success', 'Data Karyawan berhasil dihapus, termasuk dari mesin absensi.');
    }

    /**
     * Helper: Kirim data user ke semua mesin aktif
     */
    private function syncUserToDevices($pin, $name, $role)
    {
        $devices = Device::where('status', true)->get();
        foreach ($devices as $device) {
            $zk = new ZktecoService($device->ip_address, $device->port);
            $zk->pushUser($pin, $name, $role);
        }
    }

    /**
     * Helper: Hapus data user dari semua mesin aktif
     */
    private function removeUserFromDevices($pin)
    {
        $devices = Device::where('status', true)->get();
        foreach ($devices as $device) {
            $zk = new ZktecoService($device->ip_address, $device->port);
            $zk->removeUser($pin);
        }
    }
}
