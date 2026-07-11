<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('devices.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Tambah Perangkat') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 p-8">
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('devices.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Mesin -->
                        <div>
                            <label for="nama_mesin" class="block text-sm font-medium text-gray-700 mb-1">Nama Mesin <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_mesin" id="nama_mesin" value="{{ old('nama_mesin') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Misal: Mesin Pintu Depan">
                        </div>

                        <!-- Nomor Mesin -->
                        <div>
                            <label for="nomor_mesin" class="block text-sm font-medium text-gray-700 mb-1">Nomor Mesin / Device ID <span class="text-red-500">*</span></label>
                            <input type="text" name="nomor_mesin" id="nomor_mesin" value="{{ old('nomor_mesin', '1') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Biasanya 1">
                        </div>

                        <!-- IP Address -->
                        <div>
                            <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-1">IP Address <span class="text-red-500">*</span></label>
                            <input type="text" name="ip_address" id="ip_address" value="{{ old('ip_address') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="192.168.1.201">
                        </div>

                        <!-- Port -->
                        <div>
                            <label for="port" class="block text-sm font-medium text-gray-700 mb-1">Port <span class="text-red-500">*</span></label>
                            <input type="number" name="port" id="port" value="{{ old('port', 4370) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Comm Key -->
                        <div>
                            <label for="comm_key" class="block text-sm font-medium text-gray-700 mb-1">Comm Key (Opsional)</label>
                            <input type="text" name="comm_key" id="comm_key" value="{{ old('comm_key', '0') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Biarkan 0 jika tidak disetting">
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="mt-6 flex items-center">
                        <input type="checkbox" name="status" id="status" value="1" {{ old('status', true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <label for="status" class="ml-2 block text-sm font-medium text-gray-700">Mesin Aktif</label>
                    </div>

                    <div class="flex items-center justify-end mt-8 border-t border-gray-100 pt-6">
                        <a href="{{ route('devices.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2 font-medium transition mr-4">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium shadow-sm transition">
                            Simpan Perangkat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
