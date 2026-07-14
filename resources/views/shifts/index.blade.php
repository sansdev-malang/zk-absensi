<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Data Jam Kerja (Shifts)') }}
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
                    <h4 class="text-indigo-900 font-bold mb-1">Panduan: Master Shift</h4>
                    <p class="text-indigo-800 text-sm mb-2">Halaman ini adalah pusat referensi untuk semua pola jam kerja. Alur kerjanya:</p>
                    <ul class="list-disc list-inside text-indigo-800 text-sm space-y-1">
                        <li>Sistem mendefinisikan jadwal bukan sekadar 1 jam masuk/pulang, melainkan <strong>berbeda-beda per harinya</strong>.</li>
                        <li>Sebagai contoh: <em>Guru</em> mungkin libur di hari Minggu, dan pulangnya lebih cepat di hari Sabtu.</li>
                        <li>Shift-shift ini nantinya akan digunakan di pengaturan Karyawan (sebagai Default) atau di halaman Roster (untuk jadwal yang berganti-ganti).</li>
                    </ul>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Master Shift</h3>
                        <p class="text-sm text-gray-500">Konfigurasi hari dan jam kerja untuk masing-masing grup (Guru, Salehmart, Satpam).</p>
                    </div>
                    <div>
                        <a href="{{ route('shifts.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-medium shadow-sm transition inline-flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Shift
                        </a>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($shifts as $shift)
                        <div class="border border-gray-100 bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition duration-200 flex flex-col">
                            <div class="bg-gradient-to-r from-gray-50 to-white px-5 py-4 border-b border-gray-100 flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $shift->nama }}
                                    </h4>
                                    @if($shift->bonus_scheme_id)
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $shift->bonusScheme->nama ?? 'Bonus Aktif' }}
                                        </p>
                                    @endif
                                </div>
                                <span class="text-xs font-semibold bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full border border-indigo-100">{{ $shift->kategori }}</span>
                            </div>
                            <div class="p-5 flex-1">
                                <table class="w-full text-sm text-left">
                                    <tbody class="divide-y divide-gray-50">
                                        @php
                                            $hari_names = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                            $details = $shift->details->sortBy('hari');
                                        @endphp
                                        
                                        @foreach($details as $detail)
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="py-2.5 text-gray-600 font-medium">{{ $hari_names[$detail->hari] }}</td>
                                                <td class="py-2.5 text-gray-900 text-right">
                                                    <span class="bg-gray-100 px-2 py-1 rounded-md text-gray-700 font-mono text-xs">
                                                        {{ \Carbon\Carbon::parse($detail->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($detail->jam_pulang)->format('H:i') }}
                                                    </span>
                                                    @if($detail->is_cross_day)
                                                        <span class="text-xs font-bold text-red-500 ml-1" title="Melewati tengah malam">+1</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 flex justify-end gap-4 mt-auto">
                                <a href="{{ route('shifts.edit', $shift->id) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold transition-colors">Edit</a>
                                <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus shift ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold transition-colors">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>


            </div>
        </div>
    </div>
</x-app-layout>
