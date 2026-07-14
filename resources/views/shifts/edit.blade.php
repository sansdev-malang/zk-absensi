<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Edit Shift') }}
            </h2>
            <a href="{{ route('shifts.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('shifts.update', $shift->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- General Info -->
                <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Umum</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Shift <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" value="{{ old('nama', $shift->nama) }}" required class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <input type="text" name="kategori" value="{{ old('kategori', $shift->kategori) }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Opsional (mis. Guru, Satpam)">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Skema Bonus (Opsional)</label>
                            <select name="bonus_scheme_id" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($bonusSchemes as $scheme)
                                    <option value="{{ $scheme->id }}" {{ old('bonus_scheme_id', $shift->bonus_scheme_id) == $scheme->id ? 'selected' : '' }}>{{ $scheme->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Schedule Table -->
                <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Jadwal Per Hari</h3>
                    <p class="text-sm text-gray-500 mb-6">Kosongkan jam masuk dan jam pulang untuk menandakan hari libur (Off).</p>
                    
                    <div class="space-y-4">
                        @php
                            $hari_names = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            $urutan_hari = [1, 2, 3, 4, 5, 6, 0];
                        @endphp
                        
                        @foreach($urutan_hari as $h)
                            <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <div class="w-24 font-medium text-gray-700">
                                    {{ $hari_names[$h] }}
                                </div>
                                <div class="flex-1 flex gap-4 items-center">
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-500 mb-1">Jam Masuk</label>
                                        <input type="time" name="details[{{ $h }}][jam_masuk]" value="{{ old('details.'.$h.'.jam_masuk', isset($detailsByDay[$h]) ? $detailsByDay[$h]->jam_masuk : '') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <span class="text-gray-400 mt-5">-</span>
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-500 mb-1">Jam Pulang</label>
                                        <input type="time" name="details[{{ $h }}][jam_pulang]" value="{{ old('details.'.$h.'.jam_pulang', isset($detailsByDay[$h]) ? $detailsByDay[$h]->jam_pulang : '') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('shifts.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-indigo-700">Perbarui Shift</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
