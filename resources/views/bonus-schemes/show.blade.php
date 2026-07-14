<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('bonus-schemes.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ $bonusScheme->nama }}
                </h2>
                <p class="text-sm text-gray-500">{{ $bonusScheme->deskripsi ?? 'Tidak ada deskripsi' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tabel Rentang Waktu -->
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Kriteria Jam Absen & Nominal Bonus</h3>
                        <p class="text-sm text-gray-500">Daftar rentang menit (relatif terhadap jadwal masuk).</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 uppercase tracking-wider border-b border-gray-100">
                                    <th class="px-6 py-4 font-medium">Rentang (Menit)</th>
                                    <th class="px-6 py-4 font-medium">Keterangan</th>
                                    <th class="px-6 py-4 font-medium">Nominal (Rp)</th>
                                    <th class="px-6 py-4 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($bonusScheme->rules as $rule)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap font-mono font-medium text-gray-800">
                                            {{ $rule->min_menit }} s/d {{ $rule->max_menit }} 
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($rule->max_menit <= 0)
                                                <span class="text-green-600 font-medium">Awal / Tepat Waktu</span>
                                            @elseif($rule->min_menit > 0)
                                                <span class="text-red-600 font-medium">Terlambat</span>
                                            @else
                                                <span class="text-yellow-600 font-medium">Campuran</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-bold text-gray-900">
                                            Rp {{ number_format($rule->nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('bonus-rules.destroy', $rule->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus rentang kriteria ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            Belum ada kriteria rentang jam absen. Tambahkan pada form di samping.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Petunjuk & Contoh -->
                <div class="md:col-span-2 bg-blue-50 overflow-hidden shadow-sm rounded-2xl border border-blue-100 p-6">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-xl shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Panduan Pengisian Rentang Menit</h3>
                            <p class="text-sm text-blue-800 mb-4">
                                Nilai <strong>Minus (-)</strong> berarti datang LEBIH AWAL dari jam masuk.<br>
                                Nilai <strong>Positif</strong> berarti datang TERLAMBAT.
                            </p>
                            
                            <h4 class="font-semibold text-blue-900 text-sm mb-2">Contoh Kasus (Jam Masuk 07:00):</h4>
                            <div class="overflow-x-auto bg-white rounded-xl border border-blue-100">
                                <table class="w-full text-left text-xs sm:text-sm">
                                    <thead>
                                        <tr class="bg-blue-100/50 text-blue-900 border-b border-blue-100">
                                            <th class="px-4 py-2">Kondisi (Jam)</th>
                                            <th class="px-4 py-2">Min Menit</th>
                                            <th class="px-4 py-2">Max Menit</th>
                                            <th class="px-4 py-2">Bonus (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-blue-50 text-blue-800 font-mono">
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-4 py-2 font-sans">05.00 - 06.50 <span class="text-xs text-blue-600 block">(Datang awal 2 jam - 10 mnt)</span></td>
                                            <td class="px-4 py-2 font-bold text-green-600">-120</td>
                                            <td class="px-4 py-2 font-bold text-green-600">-10</td>
                                            <td class="px-4 py-2">10.000</td>
                                        </tr>
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-4 py-2 font-sans">06.51 - 07.00 <span class="text-xs text-blue-600 block">(Datang awal 9 mnt - pas)</span></td>
                                            <td class="px-4 py-2 font-bold text-green-600">-9</td>
                                            <td class="px-4 py-2 font-bold text-green-600">0</td>
                                            <td class="px-4 py-2">8.000</td>
                                        </tr>
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-4 py-2 font-sans">07.01 - 07.10 <span class="text-xs text-blue-600 block">(Telat 1 - 10 mnt)</span></td>
                                            <td class="px-4 py-2 font-bold text-red-500">1</td>
                                            <td class="px-4 py-2 font-bold text-red-500">10</td>
                                            <td class="px-4 py-2">6.000</td>
                                        </tr>
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-4 py-2 font-sans">07.11 - 07.20 <span class="text-xs text-blue-600 block">(Telat 11 - 20 mnt)</span></td>
                                            <td class="px-4 py-2 font-bold text-red-500">11</td>
                                            <td class="px-4 py-2 font-bold text-red-500">20</td>
                                            <td class="px-4 py-2">4.000</td>
                                        </tr>
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-4 py-2 font-sans">07.21 - 07.30 <span class="text-xs text-blue-600 block">(Telat 21 - 30 mnt)</span></td>
                                            <td class="px-4 py-2 font-bold text-red-500">21</td>
                                            <td class="px-4 py-2 font-bold text-red-500">30</td>
                                            <td class="px-4 py-2">2.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-xs text-blue-700 mt-3 italic">* Karyawan yang telat di atas 30 menit otomatis mendapatkan Rp 0, sehingga tidak perlu dibuat aturannya.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Tambah Rentang -->
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6 h-fit sticky top-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Kriteria Baru</h3>
                    <form action="{{ route('bonus-schemes.rules.store', $bonusScheme->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-3 bg-blue-50 text-blue-800 text-xs rounded-lg mb-4">
                            <strong>Cara Isi Menit:</strong><br>
                            Angka Minus (-) berarti "Lebih Awal/Kepagian". <br>
                            Angka Positif berarti "Terlambat". <br>
                            <em>Contoh: -999 s/d 0 = Tepat Waktu. 1 s/d 10 = Telat 1-10 Menit.</em>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Min Menit</label>
                                <input type="number" name="min_menit" required placeholder="-999" class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Max Menit</label>
                                <input type="number" name="max_menit" required placeholder="0" class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nominal Bonus (Rp)</label>
                            <input type="number" name="nominal" required min="0" placeholder="10000" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl font-medium shadow-sm transition">
                            Tambah Rentang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
