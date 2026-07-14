<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Laporan Absensi & Bonus') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Navigation Tabs -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex gap-6" aria-label="Tabs">
                    <a href="{{ route('reports.index') }}" class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Rincian Harian
                    </a>
                    <a href="{{ route('reports.summary') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Rekap Karyawan (Payroll)
                    </a>
                </nav>
            </div>

            <!-- Tutorial / Panduan -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 shadow-sm flex items-start gap-4">
                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-indigo-900 font-bold mb-1">Panduan: Rincian Harian</h4>
                    <p class="text-indigo-800 text-sm mb-2">Halaman ini menampilkan <strong>Rincian Absensi per Hari</strong>. Untuk melihat total gaji/bonus per karyawan, silakan klik tab <strong>Rekap Karyawan (Payroll)</strong> di atas.</p>
                </div>
            </div>

            <!-- Summary Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Total Bonus -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-sm p-6 text-white flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-green-100 text-sm font-medium uppercase tracking-wider mb-1">Total Bonus (Periode Ini)</p>
                        <h3 class="text-3xl font-bold">Rp {{ number_format($totalBonus ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <!-- Total Terlambat -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-sm p-6 text-white flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-red-100 text-sm font-medium uppercase tracking-wider mb-1">Total Keterlambatan</p>
                        <h3 class="text-3xl font-bold">{{ number_format($totalTerlambat ?? 0, 0, ',', '.') }} Menit</h3>
                    </div>
                </div>
            </div>

            <!-- Filter Card & Recalculate -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col lg:flex-row justify-between gap-6">
                <!-- Form Filter -->
                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col sm:flex-row items-end gap-4 w-full lg:w-auto flex-wrap">
                    <!-- Dropdown Karyawan -->
                    @hasanyrole('Admin|HR|Supervisor')
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Filter Karyawan</label>
                        <select name="user_id" id="user_id" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm w-full lg:w-48 bg-white">
                            <option value="">Semua Karyawan</option>
                            @foreach($usersList as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endhasanyrole

                    <!-- Dropdown Pintasan Periode -->
                    <div>
                        <label for="periode_pintasan" class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                        <select id="periode_pintasan" onchange="setPeriodeDates(this)" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm w-full lg:w-48 bg-white">
                            @foreach($predefinedPeriods as $period)
                                <option value="{{ $period['start'] }}|{{ $period['end'] }}" {{ $startDate == $period['start'] && $endDate == $period['end'] ? 'selected' : '' }}>
                                    {{ $period['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}">
                    <div class="flex gap-2 w-full sm:w-auto mt-4 sm:mt-0">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl font-medium shadow-sm transition w-full sm:w-auto">
                            Terapkan
                        </button>
                        <a href="{{ route('reports.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2 font-medium transition text-sm flex items-center justify-center border border-transparent hover:border-gray-200 rounded-xl">Reset</a>
                    </div>
                </form>

                @hasanyrole('Admin|HR|Supervisor')
                <!-- Form Kalkulasi Ulang -->
                <div class="border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 flex items-end">
                    <form method="POST" action="{{ route('reports.recalculate') }}" id="recalculateForm" class="w-full">
                        @csrf
                        <input type="hidden" name="start_date" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}">
                        <input type="hidden" name="end_date" value="{{ request('end_date', now()->endOfMonth()->toDateString()) }}">
                        <button type="submit" onclick="showRecalculateOverlay()" class="w-full lg:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl font-medium shadow-sm transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Kalkulasi Ulang
                        </button>
                    </form>
                </div>
                @endhasanyrole
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-medium">Tanggal</th>
                                <th class="px-6 py-4 font-medium">Karyawan</th>
                                <th class="px-6 py-4 font-medium">Shift (Jadwal)</th>
                                <th class="px-6 py-4 font-medium">Jam Masuk</th>
                                <th class="px-6 py-4 font-medium">Jam Pulang</th>
                                <th class="px-6 py-4 font-medium">Status & Telat</th>
                                <th class="px-6 py-4 font-medium text-right">Bonus Didapat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($reports as $report)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($report->tanggal)->isoFormat('D MMMM Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $report->user->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $report->user->jabatan ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($report->shiftDetail && $report->shiftDetail->shift)
                                            <span class="text-gray-900 font-medium">{{ $report->shiftDetail->shift->nama }}</span><br>
                                            <span class="text-xs text-gray-500">{{ substr($report->shiftDetail->jam_masuk, 0, 5) }} - {{ substr($report->shiftDetail->jam_pulang, 0, 5) }}</span>
                                        @else
                                            <span class="text-gray-400 italic">Libur / Tidak ada shift</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($report->jam_masuk)
                                            <span class="font-bold text-gray-900">{{ $report->jam_masuk->format('H:i') }}</span>
                                        @else
                                            <span class="text-red-500 font-medium">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($report->jam_pulang)
                                            <span class="font-bold text-gray-900">{{ $report->jam_pulang->format('H:i') }}</span>
                                        @else
                                            <span class="text-gray-400 font-medium">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($report->status_kehadiran == 'Hadir')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Tepat Waktu
                                            </span>
                                        @elseif($report->status_kehadiran == 'Terlambat')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Telat {{ $report->menit_terlambat }} mnt
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $report->status_kehadiran }}
                                            </span>
                                        @endif
                                        
                                        @if($report->menit_pulang_cepat > 0)
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    Pulang Cepat {{ $report->menit_pulang_cepat }} mnt
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($report->bonus_didapat > 0)
                                            <span class="font-bold text-green-600">Rp {{ number_format($report->bonus_didapat, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <p class="font-medium text-gray-900 mb-1">Belum ada data Rekap Harian</p>
                                        <p class="text-sm">Klik Sinkronisasi pada menu Data Absensi (Mentah) atau Dashboard untuk mulai mengkalkulasi.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($reports->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm hidden z-50 flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center max-w-sm w-full mx-4 transform transition-all">
            <svg class="animate-spin -ml-1 mr-3 h-12 w-12 text-indigo-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Mengkalkulasi Ulang...</h3>
            <p class="text-gray-500 text-center text-sm">Sedang menghitung ulang seluruh jam kerja, keterlambatan, dan bonus karyawan. Mohon jangan tutup halaman ini.</p>
        </div>
    </div>

    <script>
        function showRecalculateOverlay() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
            document.getElementById('loadingOverlay').classList.add('flex');
        }

        function setPeriodeDates(select) {
            if (select.value) {
                const dates = select.value.split('|');
                document.getElementById('start_date').value = dates[0];
                document.getElementById('end_date').value = dates[1];
            }
        }
    </script>
</x-app-layout>
