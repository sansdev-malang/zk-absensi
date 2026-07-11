<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Data Work Codes') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tutorial / Panduan -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 shadow-sm flex items-start gap-4">
                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-indigo-900 font-bold mb-1">Panduan: Kode Kerja (Work Codes)</h4>
                    <p class="text-indigo-800 text-sm mb-2">Work Codes digunakan untuk memetakan status absensi khusus dari mesin fisik (misalnya: Lembur, Cuti, Dinas Luar).</p>
                    <ul class="list-disc list-inside text-indigo-800 text-sm space-y-1">
                        <li>Jika mesin Anda mendukung input angka status/kode sebelum absen, Anda bisa meregistrasikan kodenya di sini.</li>
                        <li>Contoh: Kode <strong>0</strong> = Check In, <strong>1</strong> = Check Out, <strong>4</strong> = Lembur.</li>
                    </ul>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Daftar Work Code</h3>
                        <p class="text-sm text-gray-500">Kelola kode kerja / status absen dari mesin ZKTeco.</p>
                    </div>
                    <a href="{{ route('work-codes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium shadow-sm transition inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Kode
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-medium">Kode</th>
                                <th class="px-6 py-4 font-medium">Deskripsi</th>
                                <th class="px-6 py-4 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($workCodes as $wc)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-mono font-bold bg-gray-100 text-gray-800 border border-gray-200">
                                            {{ $wc->kode }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $wc->deskripsi ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right space-x-3">
                                        <a href="{{ route('work-codes.edit', $wc->id) }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm transition">Edit</a>
                                        
                                        <form action="{{ route('work-codes.destroy', $wc->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kode ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm transition">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        </div>
                                        <p class="font-medium text-gray-900 mb-1">Belum ada Work Code</p>
                                        <p class="text-sm">Klik 'Tambah Kode' untuk membuat work code pertama Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
