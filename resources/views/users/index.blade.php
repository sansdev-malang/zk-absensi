<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Data Karyawan & Mapping Mesin') }}
            </h2>
        </div>
    </x-slot>

    @php
        $shiftColors = [
            'bg-blue-50 text-blue-700 border-blue-200 focus:border-blue-500 focus:ring-blue-500',
            'bg-emerald-50 text-emerald-700 border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500',
            'bg-purple-50 text-purple-700 border-purple-200 focus:border-purple-500 focus:ring-purple-500',
            'bg-amber-50 text-amber-700 border-amber-200 focus:border-amber-500 focus:ring-amber-500',
            'bg-rose-50 text-rose-700 border-rose-200 focus:border-rose-500 focus:ring-rose-500',
            'bg-cyan-50 text-cyan-700 border-cyan-200 focus:border-cyan-500 focus:ring-cyan-500',
        ];
        $shiftColorMap = [];
        foreach($shifts as $index => $shift) {
            $shiftColorMap[$shift->id] = $shiftColors[$index % count($shiftColors)];
        }
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Tutorial / Panduan -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 shadow-sm flex items-start gap-4">
                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-indigo-900 font-bold mb-1">Panduan: Manajemen Karyawan</h4>
                    <p class="text-indigo-800 text-sm mb-2">Halaman ini digunakan untuk mengelola data akun karyawan, guru, dan staff. Alur kerja penting:</p>
                    <ul class="list-disc list-inside text-indigo-800 text-sm space-y-1">
                        <li><strong>UID ZKTeco:</strong> Pastikan UID di sini sama persis dengan User ID yang ada di dalam mesin fisik sidik jari agar data absensinya terhubung dengan orang yang tepat.</li>
                        <li><strong>Role:</strong> Hanya akun dengan Role Admin/HR yang bisa login dan melihat halaman Dashboard & Pengaturan ini.</li>
                        <li><strong>Jadwal:</strong> Anda bisa klik "Edit" pada karyawan untuk menentukan <em>Shift Default</em> mereka (misal shift Guru).</li>
                    </ul>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Daftar Karyawan</h3>
                        <p class="text-sm text-gray-500">Kelola semua pengguna sistem dan karyawan yang terdaftar di mesin.</p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('zkteco.sync-users') }}" method="POST" id="syncUsersForm">
                            @csrf
                            <button type="submit" onclick="showLoadingOverlay()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium shadow-sm transition inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Tarik Karyawan
                            </button>
                        </form>
                        <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium shadow-sm transition inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Tambah Manual
                        </a>
                    </div>
                </div>

                <!-- Filter & Search Section -->
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label for="search" class="block text-xs font-medium text-gray-500 mb-1">Cari Karyawan</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full pl-10 sm:text-sm border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500" placeholder="Nama, Email, atau NIK...">
                            </div>
                        </div>
                        
                        <div class="w-full md:w-48">
                            <label for="jabatan" class="block text-xs font-medium text-gray-500 mb-1">Filter Jabatan</label>
                            <select name="jabatan" id="jabatan" class="block w-full sm:text-sm border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Jabatan</option>
                                @foreach($jabatans as $jabatan)
                                    <option value="{{ $jabatan }}" {{ request('jabatan') == $jabatan ? 'selected' : '' }}>{{ $jabatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 w-full md:w-auto">
                            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm w-full md:w-auto">
                                Filter
                            </button>
                            @if(request()->anyFilled(['search', 'jabatan']))
                                <a href="{{ route('users.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm w-full md:w-auto text-center">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-medium text-center w-16">No</th>
                                <th class="px-6 py-4 font-medium">Nama / Email</th>
                                <th class="px-6 py-4 font-medium">Jabatan</th>
                                <th class="px-6 py-4 font-medium">UID ZKTeco</th>
                                <th class="px-6 py-4 font-medium">Default Shift</th>
                                <th class="px-6 py-4 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-center text-gray-500 font-medium">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->jabatan)
                                            <span class="text-gray-600">{{ $user->jabatan }}</span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                Belum Diatur
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->uid)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                UID: {{ $user->uid }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum di-mapping</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $currentBg = $user->default_shift_id && isset($shiftColorMap[$user->default_shift_id]) ? $shiftColorMap[$user->default_shift_id] : 'bg-gray-50 text-gray-500 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500';
                                        @endphp
                                        <select class="shift-select block w-full rounded-md border shadow-sm sm:text-sm font-medium transition-colors cursor-pointer {{ $currentBg }}" data-user-id="{{ $user->id }}">
                                            <option value="" class="bg-white text-gray-700 font-normal">(Via Roster)</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" class="bg-white text-gray-700 font-normal" {{ $user->default_shift_id == $shift->id ? 'selected' : '' }}>
                                                    {{ $shift->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-3">
                                        <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm transition">Edit</a>
                                        
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini beserta data sidik jarinya di mesin?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm transition">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada data Karyawan</h3>
                                        <p class="text-gray-500 text-sm mb-4">Klik tombol Tarik Karyawan di atas untuk sinkronisasi data dari mesin.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl p-6 flex items-center gap-4 shadow-xl">
            <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-medium">Memproses data dari mesin...</span>
        </div>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('flex');
        }
    </script>
    <!-- Toast Notification for AJAX -->
    <div id="toast" class="fixed bottom-5 right-5 transform translate-y-full opacity-0 transition-all duration-300 bg-gray-900 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 z-50">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="toast-message" class="text-sm font-medium">Tersimpan</span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('.shift-select');
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toast-message');
            const shiftColorMap = @json($shiftColorMap);

            function showToast(message, isError = false) {
                toastMsg.textContent = message;
                if(isError) {
                    toast.classList.replace('bg-gray-900', 'bg-red-600');
                    toast.querySelector('svg').outerHTML = '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                } else {
                    toast.classList.replace('bg-red-600', 'bg-gray-900');
                    toast.querySelector('svg').outerHTML = '<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                }

                toast.classList.remove('translate-y-full', 'opacity-0');
                
                setTimeout(() => {
                    toast.classList.add('translate-y-full', 'opacity-0');
                }, 3000);
            }

            selects.forEach(select => {
                select.addEventListener('change', function() {
                    const userId = this.getAttribute('data-user-id');
                    const shiftId = this.value;
                    
                    this.style.opacity = '0.5';
                    this.disabled = true;

                    fetch(`/users/${userId}/update-shift`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            default_shift_id: shiftId === '' ? null : shiftId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.style.opacity = '1';
                        this.disabled = false;
                        
                        if(data.success) {
                            showToast(data.message);
                            
                            // Update Color dynamically
                            this.className = "shift-select block w-full rounded-md border shadow-sm sm:text-sm font-medium transition-colors cursor-pointer";
                            const newColorClass = shiftId ? (shiftColorMap[shiftId] || 'bg-gray-50 text-gray-700 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500') : 'bg-gray-50 text-gray-500 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500';
                            
                            // Using split to add multiple classes properly
                            newColorClass.split(' ').forEach(cls => {
                                if(cls) this.classList.add(cls);
                            });

                            // Add blink effect
                            this.classList.add('ring-2', 'ring-green-400');
                            setTimeout(() => {
                                this.classList.remove('ring-2', 'ring-green-400');
                            }, 1000);
                        } else {
                            showToast('Gagal menyimpan perubahan.', true);
                        }
                    })
                    .catch(error => {
                        this.style.opacity = '1';
                        this.disabled = false;
                        showToast('Terjadi kesalahan jaringan.', true);
                    });
                });
            });
        });
    </script>
</x-app-layout>
