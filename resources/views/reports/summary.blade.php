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
                    <a href="{{ route('reports.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Rincian Harian
                    </a>
                    <a href="{{ route('reports.summary') }}" class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Rekap Karyawan (Payroll)
                    </a>
                </nav>
            </div>

            <!-- Tutorial / Panduan -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 shadow-sm flex items-start gap-4">
                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-indigo-900 font-bold mb-1">Panduan: Rekap Karyawan (Payroll)</h4>
                    <p class="text-indigo-800 text-sm mb-2">Halaman ini adalah <strong>Rekapitulasi Akhir</strong> yang digunakan untuk penggajian. Di sini Anda bisa melihat <strong>Total Uang Bonus</strong> yang dikumpulkan setiap karyawan selama satu periode terpilih secara langsung.</p>
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
                        <p class="text-green-100 text-sm font-medium uppercase tracking-wider mb-1">Total Pencairan Bonus</p>
                        <h3 class="text-3xl font-bold">Rp {{ number_format($totalBonus ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <!-- Total Terlambat -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-sm p-6 text-white flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-red-100 text-sm font-medium uppercase tracking-wider mb-1">Total Menit Terlambat</p>
                        <h3 class="text-3xl font-bold">{{ number_format($totalTerlambat ?? 0, 0, ',', '.') }} Menit</h3>
                    </div>
                </div>
            </div>

            <!-- Filter Card & Recalculate -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col lg:flex-row justify-between gap-6">
                <!-- Form Filter -->
                <form method="GET" action="{{ route('reports.summary') }}" class="flex flex-col sm:flex-row items-end gap-4 w-full lg:w-auto">
                    <!-- Dropdown Pintasan Periode -->
                    <div>
                        <label for="periode_pintasan" class="block text-sm font-medium text-gray-700 mb-1">Periode Penggajian</label>
                        <select id="periode_pintasan" onchange="setPeriodeDates(this)" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm w-full lg:w-64 bg-white">
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
                        <a href="{{ route('reports.summary') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2 font-medium transition text-sm flex items-center justify-center border border-transparent hover:border-gray-200 rounded-xl">Reset</a>
                    </div>
                </form>

                <div class="flex flex-col sm:flex-row items-end gap-4 border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 w-full lg:w-auto">
                    <!-- Tombol Cetak -->
                    <a href="{{ route('reports.summary.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2 rounded-xl font-medium shadow-sm transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Laporan
                    </a>

                    @hasanyrole('Admin|HR|Supervisor')
                    <!-- Form Kalkulasi Ulang -->
                    <form method="POST" action="{{ route('reports.recalculate') }}" id="recalculateForm" class="w-full sm:w-auto">
                        @csrf
                        <input type="hidden" name="start_date" value="{{ request('start_date', $startDate) }}">
                        <input type="hidden" name="end_date" value="{{ request('end_date', $endDate) }}">
                        <button type="submit" onclick="showRecalculateOverlay()" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl font-medium shadow-sm transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Kalkulasi Ulang
                        </button>
                    </form>
                    @endhasanyrole
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-medium text-center w-16">No</th>
                                <th class="px-6 py-4 font-medium">Karyawan</th>
                                <th class="px-6 py-4 font-medium text-center">Total Hari Masuk</th>
                                <th class="px-6 py-4 font-medium text-center">Tepat Waktu</th>
                                <th class="px-6 py-4 font-medium text-center">Terlambat (Hari / Menit)</th>
                                <th class="px-6 py-4 font-medium text-right text-lg">Total Bonus</th>
                                <th class="px-6 py-4 font-medium text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($summaries as $index => $summary)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-center text-gray-500 font-medium">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">
                                                {{ substr($summary['user']->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 text-base">{{ $summary['user']->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $summary['user']->jabatan ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-800 font-bold">
                                            {{ $summary['total_hari_kerja'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $summary['total_hadir_tepat'] }} Hari
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($summary['total_terlambat'] > 0)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mb-1">
                                                {{ $summary['total_terlambat'] }} Hari
                                            </span><br>
                                            <span class="text-xs text-red-500 font-medium">({{ $summary['total_menit_terlambat'] }} menit)</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($summary['total_bonus'] > 0)
                                            <span class="font-bold text-green-600 text-lg">Rp {{ number_format($summary['total_bonus'], 0, ',', '.') }}</span>
                                        @else
                                            <span class="font-medium text-gray-400">Rp 0</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('reports.index', ['user_id' => $summary['user']->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border border-blue-200">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada data Rekap</h3>
                                        <p class="text-gray-500 text-sm">Tidak ada absensi pada periode yang dipilih.</p>
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
            <svg class="animate-spin h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-medium">Mengkalkulasi Ulang Data...</span>
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
