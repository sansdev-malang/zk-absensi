<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Roster (Jadwal Dinamis)') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tutorial / Panduan -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 shadow-sm flex items-start gap-4">
                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-indigo-900 font-bold mb-1">Panduan: Roster Penjadwalan Dinamis</h4>
                    <p class="text-indigo-800 text-sm mb-2">Halaman ini digunakan untuk mengatur jadwal khusus (Roster) yang berganti-ganti, seperti jadwal Satpam atau Karyawan Toko.</p>
                    <ul class="list-disc list-inside text-indigo-800 text-sm space-y-1">
                        <li><strong>Cara Kerja:</strong> Jadwal Roster yang Anda buat di sini <strong>akan mengabaikan (override)</strong> jadwal default karyawan pada tanggal tersebut.</li>
                        <li><strong>Kemudahan:</strong> Klik tombol <span class="bg-blue-600 text-white px-1.5 py-0.5 rounded text-xs">Assign Jadwal Baru</span> untuk mengatur shift selama seminggu atau sebulan ke depan sekaligus (Mass Assignment).</li>
                    </ul>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Daftar Jadwal Khusus</h3>
                        <p class="text-sm text-gray-500">Jadwal ini akan menimpa Shift Default dari masing-masing Karyawan.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                        <form action="{{ route('schedules.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <select id="periode_pintasan" onchange="setPeriodeDates(this)" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm w-full sm:w-64 bg-white">
                                @foreach($predefinedPeriods as $period)
                                    <option value="{{ $period['start'] }}|{{ $period['end'] }}" {{ $startDate == $period['start'] && $endDate == $period['end'] ? 'selected' : '' }}>
                                        {{ $period['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}">
                            <button type="submit" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg font-medium shadow-sm transition border border-indigo-200">Terapkan</button>
                        </form>
                        <form action="{{ route('schedules.auto-generate') }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Tindakan ini akan menggenerate jadwal otomatis 1 periode untuk seluruh karyawan yang memiliki Default Shift. Lanjutkan?');">
                            @csrf
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-medium shadow-sm transition flex items-center gap-2 w-full sm:w-auto justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Generate Otomatis
                            </button>
                        </form>
                        <a href="{{ route('schedules.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium shadow-sm transition flex items-center gap-2 w-full sm:w-auto justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Assign Baru (Manual)
                        </a>
                    </div>
                </div>

                <div class="p-6 bg-gray-50/50">
                    @if($groupedSchedules->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-base font-medium text-gray-600">Belum ada jadwal Roster di periode ini</p>
                            <p class="text-sm mt-1">Silakan Assign Jadwal Baru untuk Satpam atau Karyawan lainnya.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                            @foreach($groupedSchedules as $shiftId => $userGroups)
                                @php
                                    // Get the shift object from the first item
                                    $shift = $userGroups->first()->first()->shift;
                                @endphp
                                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                                    <div class="bg-indigo-50 border-b border-indigo-100 px-5 py-4 flex items-center gap-3">
                                        <div class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-indigo-900 text-lg">{{ $shift->nama }}</h4>
                                            <p class="text-xs text-indigo-600 font-medium">{{ $shift->kategori }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="p-5 divide-y divide-gray-100 flex-1">
                                        @foreach($userGroups as $userId => $userSchedules)
                                            @php
                                                $user = $userSchedules->first()->user;
                                            @endphp
                                            <div class="py-4 first:pt-0 last:pb-0">
                                                <div class="flex items-center gap-3 mb-3">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900 leading-tight">{{ $user->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ $user->jabatan ?? 'No Job Title' }}</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex flex-wrap gap-2 pl-11">
                                                    @foreach($userSchedules as $schedule)
                                                        <div class="inline-flex items-center bg-gray-50 border border-gray-200 rounded-md shadow-sm pl-2 pr-1 py-1 text-xs text-gray-700">
                                                            <span class="font-medium mr-2">{{ \Carbon\Carbon::parse($schedule->tanggal)->format('d M') }}</span>
                                                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus jadwal tanggal ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-gray-400 hover:text-red-500 hover:bg-red-50 rounded p-0.5 transition" title="Hapus">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function setPeriodeDates(select) {
            if (select.value) {
                const dates = select.value.split('|');
                document.getElementById('start_date').value = dates[0];
                document.getElementById('end_date').value = dates[1];
            }
        }
    </script>

</x-app-layout>
