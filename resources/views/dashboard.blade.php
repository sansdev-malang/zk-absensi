<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Tutorial / Panduan -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 shadow-sm flex items-start gap-4">
                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-indigo-900 font-bold mb-1">Selamat Datang di Sistem ZK-Absensi</h4>
                    <p class="text-indigo-800 text-sm mb-2">Ini adalah halaman beranda utama (Dashboard). Alur kerja yang disarankan untuk admin baru:</p>
                    <ol class="list-decimal list-inside text-indigo-800 text-sm space-y-1">
                        <li>Buka <strong>Perangkat ZKTeco</strong> untuk mendaftarkan IP mesin absensi Anda.</li>
                        <li>Buka <strong>Karyawan</strong> untuk mendaftarkan profil dan mencocokkan UID dari mesin.</li>
                        <li>Buka <strong>Jam Kerja / Roster</strong> untuk memastikan setiap karyawan punya aturan jam masuk yang tepat.</li>
                        <li>Klik <strong>Sinkronisasi Sekarang</strong> (di bawah) secara berkala untuk menarik data absensi terbaru!</li>
                    </ol>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Karyawan -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 transform transition-all hover:-translate-y-1 hover:shadow-md">
                    <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Karyawan</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">124</p>
                    </div>
                </div>

                <!-- Hadir Hari Ini -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 transform transition-all hover:-translate-y-1 hover:shadow-md">
                    <div class="w-14 h-14 rounded-full bg-green-50 text-green-500 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Hadir Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">118</p>
                    </div>
                </div>

                <!-- Absen / Terlambat -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 transform transition-all hover:-translate-y-1 hover:shadow-md">
                    <div class="w-14 h-14 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Absen / Terlambat</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">6</p>
                    </div>
                </div>
            </div>

            <!-- Recent Attendances -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Absensi Terkini</h3>
                    <button class="text-sm font-medium text-blue-600 hover:text-blue-800">Lihat Semua &rarr;</button>
                </div>
                <div class="p-6">
                    <div class="text-center py-10">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada data absensi hari ini.</p>
                        <p class="text-sm text-gray-400 mt-1">Lakukan sinkronisasi dengan mesin ZKTeco untuk memperbarui data.</p>
                        
                        @hasanyrole('Admin|HR|Supervisor')
                        <form action="{{ route('zkteco.sync') }}" method="POST" class="mt-6" onsubmit="document.getElementById('sync-overlay').classList.remove('hidden')">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-sm shadow-blue-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Sinkronisasi Sekarang
                            </button>
                        </form>
                        
                        <!-- Overlay Loading -->
                        <div id="sync-overlay" class="hidden fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex flex-col items-center justify-center">
                            <div class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center max-w-sm w-full mx-4">
                                <svg class="animate-spin -ml-1 mr-3 h-12 w-12 text-blue-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 mb-1">Sedang Menarik Data...</h3>
                                <p class="text-sm text-gray-500 text-center">Tunggu sebentar, proses ini bisa memakan waktu 1-2 menit tergantung banyaknya riwayat absensi di mesin ZKTeco.</p>
                                <p class="text-xs text-red-500 text-center mt-3 font-medium">Jangan tutup atau refresh halaman ini!</p>
                            </div>
                        </div>
                        @endhasanyrole
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
