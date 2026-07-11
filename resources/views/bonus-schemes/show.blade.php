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
