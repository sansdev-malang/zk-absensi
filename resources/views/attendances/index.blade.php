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
                    <p class="text-indigo-800 text-sm">Halaman ini menampilkan seluruh riwayat log (sidik jari/wajah) dari mesin. Untuk mendapatkan data terbaru, klik tombol <strong>"Tarik Data Mesin"</strong>. Pastikan mesin dalam keadaan <span class="bg-green-100 text-green-700 px-1.5 py-0.5 rounded text-xs font-semibold">Online</span> di menu Perangkat ZKTeco sebelum menarik data.</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Riwayat Kehadiran</h3>
                        <p class="text-sm text-gray-500">Log kehadiran karyawan hasil sinkronisasi dari mesin ZKTeco.</p>
                    </div>
                    
                    <form action="{{ route('zkteco.sync') }}" method="POST" onsubmit="document.getElementById('sync-overlay').classList.remove('hidden')">
                        @csrf
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium shadow-sm transition inline-flex items-center gap-2">
                            <svg class="w-5 h-5 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Tarik Data Mesin
                        </button>
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
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-medium">Waktu Absen</th>
                                <th class="px-6 py-4 font-medium">Karyawan</th>
                                <th class="px-6 py-4 font-medium">Status / Tipe</th>
                                <th class="px-6 py-4 font-medium">Mesin Asal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($attendances as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($log->waktu)->format('d M Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($log->waktu)->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $log->user->name ?? 'Unknown User' }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->user->jabatan ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            // Mapping status sederhana, bisa disesuaikan dengan kode dari ZKTeco
                                            $stateLabel = 'Unknown';
                                            $stateColor = 'bg-gray-100 text-gray-700';
                                            
                                            if ($log->status === '0' || $log->status === '1') {
                                                $stateLabel = 'Check In';
                                                $stateColor = 'bg-green-100 text-green-700';
                                            } elseif ($log->status === '1' || $log->status === '2') {
                                                $stateLabel = 'Check Out';
                                                $stateColor = 'bg-red-100 text-red-700';
                                            } else {
                                                $stateLabel = 'Log (' . $log->status . ')';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $stateColor }}">
                                            {{ $stateLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $log->device->nama_mesin ?? 'Unknown Device' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="font-medium text-gray-900 mb-1">Belum ada riwayat absensi</p>
                                        <p class="text-sm">Klik 'Tarik Data Mesin' untuk memulai sinkronisasi dari ZKTeco.</p>
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
