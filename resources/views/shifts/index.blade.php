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
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Master Shift</h3>
                        <p class="text-sm text-gray-500">Konfigurasi hari dan jam kerja untuk masing-masing grup (Guru, Salehmart, Satpam).</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($shifts as $shift)
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                <h4 class="font-bold text-gray-800">{{ $shift->nama }}</h4>
                                <span class="text-xs font-medium bg-blue-100 text-blue-700 px-2 py-1 rounded-full">{{ $shift->kategori }}</span>
                            </div>
                            <div class="p-4 bg-white">
                                <table class="w-full text-sm text-left">
                                    <tbody class="divide-y divide-gray-100">
                                        @php
                                            $hari_names = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                            // Group continuous days with same time
                                            $details = $shift->details->sortBy('hari');
                                        @endphp
                                        
                                        @foreach($details as $detail)
                                            <tr>
                                                <td class="py-2 text-gray-600 font-medium">{{ $hari_names[$detail->hari] }}</td>
                                                <td class="py-2 text-gray-900 text-right">
                                                    {{ \Carbon\Carbon::parse($detail->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($detail->jam_pulang)->format('H:i') }}
                                                    @if($detail->is_cross_day)
                                                        <span class="text-xs text-red-500 ml-1" title="Melewati tengah malam">(+1)</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 p-4 bg-blue-50 text-blue-800 rounded-xl text-sm border border-blue-100">
                    <p class="font-bold mb-1">Informasi:</p>
                    <p>Saat ini data shift ditambahkan melalui Database Seeder sesuai konfigurasi awal Anda (Guru, Salehmart, Satpam). Untuk menambah / mengedit jam kerja di kemudian hari, sistem form UI penuh sedang dalam tahap antrean pengembangan.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
