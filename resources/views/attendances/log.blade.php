<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Raw Log Absensi') }}
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
                    <h4 class="text-indigo-900 font-bold mb-1">Panduan: Raw Log Absensi</h4>
                    <p class="text-indigo-800 text-sm">Halaman ini menampilkan seluruh riwayat log mentah (raw log) secara urut setiap kali karyawan melakukan tap (scan) pada mesin absensi. Halaman ini sangat berguna untuk debugging jika ada karyawan yang merasa sudah absen namun tidak terkap.</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Riwayat Tap Absensi</h3>
                        <p class="text-sm text-gray-500">History raw log kehadiran karyawan dari semua mesin ZKTeco.</p>
                    </div>
                </div>
                
                <!-- Filter & Search Form -->
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('attendances.log') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
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
                                <a href="{{ route('attendances.log') }}" class="w-full md:w-auto bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-xl font-medium shadow-sm transition text-sm text-center">
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
                                <th class="px-6 py-4 font-medium">Waktu Tap</th>
                                <th class="px-6 py-4 font-medium">Nama Karyawan</th>
                                <th class="px-6 py-4 font-medium">Mesin</th>
                                <th class="px-6 py-4 font-medium">Status Log</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($log->waktu)->format('d M Y, H:i:s') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $log->user->name ?? 'Unknown User' }}</div>
                                        <div class="text-xs text-gray-500">UID: {{ $log->user->uid ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->device)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700">
                                                {{ $log->device->nama_mesin }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                Input Manual
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $log->status }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="font-medium text-gray-900 mb-1">Belum ada riwayat tap absensi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($logs->hasPages())
                    <div class="p-6 border-t border-gray-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
