<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Data Absensi') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('warning'))
                <div class="bg-orange-50 border border-orange-200 text-orange-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('warning') }}</span>
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
                    <h4 class="text-indigo-900 font-bold mb-1">Panduan: Tarik Data Absensi</h4>
                    <p class="text-indigo-800 text-sm">Halaman ini menampilkan seluruh riwayat log (sidik jari/wajah) dari mesin. Untuk mendapatkan data terbaru, klik tombol <strong>"Tarik Data Absensi"</strong>. Anda juga bisa memilih rentang tanggal agar proses penarikan lebih cepat dan fokus pada tanggal tertentu saja. Pastikan mesin dalam keadaan <span class="bg-green-100 text-green-700 px-1.5 py-0.5 rounded text-xs font-semibold">Online</span> di menu Perangkat ZKTeco sebelum menarik data.</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Riwayat Kehadiran</h3>
                        <p class="text-sm text-gray-500">Log kehadiran karyawan hasil sinkronisasi dari mesin ZKTeco.</p>
                    </div>
                    
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" onclick="document.getElementById('sync-modal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium shadow-sm transition inline-flex items-center gap-2 w-full sm:w-auto justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Tarik Data Absensi
                        </button>
                        <a href="{{ route('attendances.create') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-xl font-medium shadow-sm transition inline-flex items-center gap-2 w-full sm:w-auto justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Manual
                        </a>
                    </div>
                </div>
                
                <!-- Filter & Search Form -->
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('attendances.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
                        <div class="w-full md:w-1/3 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama karyawan atau UID..." class="w-full pl-10 border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        
                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full md:w-auto border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" title="Tanggal Awal Filter">
                            <span class="text-gray-500 text-sm">s/d</span>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full md:w-auto border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" title="Tanggal Akhir Filter">
                        </div>
                        
                        <div class="flex gap-2 w-full md:w-auto">
                            <button type="submit" class="w-full md:w-auto bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-xl font-medium shadow-sm transition text-sm">
                                Filter
                            </button>
                            @if(request()->hasAny(['search', 'start_date', 'end_date']))
                                <a href="{{ route('attendances.index') }}" class="w-full md:w-auto bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-xl font-medium shadow-sm transition text-sm text-center">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <!-- Overlay Loading -->
                <div id="sync-overlay" class="hidden fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex flex-col items-center justify-center">
                    <div class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center max-w-sm w-full mx-4">
                        <svg class="animate-spin -ml-1 mr-3 h-12 w-12 text-indigo-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">Sedang Menarik Data...</h3>
                        <p class="text-sm text-gray-500 text-center">Proses kalkulasi absensi ke database sedang berjalan, mohon tunggu sebentar.</p>
                    </div>
                </div>
                
                <!-- Sync Modal -->
                <div id="sync-modal" class="hidden fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex flex-col items-center justify-center">
                    <div class="bg-white p-6 rounded-2xl shadow-2xl max-w-md w-full mx-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Tarik Data Absensi</h3>
                            <button type="button" onclick="document.getElementById('sync-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Pilih rentang tanggal untuk mempercepat proses penarikan data dari mesin. Kosongkan jika ingin menarik seluruh data.</p>
                        
                        <form action="{{ route('zkteco.sync') }}" method="POST" onsubmit="document.getElementById('sync-modal').classList.add('hidden'); document.getElementById('sync-overlay').classList.remove('hidden');">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal</label>
                                    <input type="date" name="start_date" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                                    <input type="date" name="end_date" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" onclick="document.getElementById('sync-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-xl font-medium hover:bg-gray-50 transition">
                                    Batal
                                </button>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-medium shadow-sm transition inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Mulai Tarik Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-medium">Tanggal</th>
                                <th class="px-6 py-4 font-medium">Nama Karyawan</th>
                                <th class="px-6 py-4 font-medium">UID</th>
                                <th class="px-6 py-4 font-medium">Jam Check In</th>
                                <th class="px-6 py-4 font-medium">Jam Check Out</th>
                                <th class="px-6 py-4 font-medium">Nama Mesin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($attendances as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $log->user->name ?? 'Unknown User' }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->user->jabatan ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $log->user->uid ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                        {{ \Carbon\Carbon::parse($log->jam_masuk)->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                        @if($log->jam_masuk != $log->jam_pulang)
                                            {{ \Carbon\Carbon::parse($log->jam_pulang)->format('H:i') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $log->device ? 'bg-gray-100 text-gray-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $log->device->nama_mesin ?? 'Input Manual' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="font-medium text-gray-900 mb-1">Belum ada riwayat absensi</p>
                                        <p class="text-sm">Klik 'Tarik Data Absensi' untuk memulai sinkronisasi dari ZKTeco.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($attendances->hasPages())
                    <div class="p-6 border-t border-gray-100">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
