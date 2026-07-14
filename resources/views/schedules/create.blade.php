<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('schedules.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Assign Jadwal Baru') }}
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

                <form action="{{ route('schedules.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User -->
                        <!-- Users (Checkboxes) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Karyawan / Satpam <span class="text-red-500">*</span></label>
                            
                            <!-- Search & Select All Tools -->
                            <div class="flex items-center gap-4 mb-3 pb-3 border-b border-gray-100">
                                <input type="text" id="searchUser" placeholder="Cari nama atau jabatan..." class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <label class="flex items-center gap-2 cursor-pointer whitespace-nowrap text-sm font-medium text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-100">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    Pilih Semua
                                </label>
                            </div>

                            <!-- Scrollable Checkbox List -->
                            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg bg-gray-50 p-2 space-y-1" id="userList">
                                @foreach($users as $user)
                                    <label class="user-item flex items-center p-2 hover:bg-white rounded cursor-pointer transition">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mr-3">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 user-name">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500 user-jabatan">{{ $user->jabatan ?? 'No Job Title' }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Centang karyawan yang akan ditugaskan ke jadwal ini.</p>
                        </div>

                        <!-- Shift -->
                        <div class="md:col-span-2">
                            <label for="shift_id" class="block text-sm font-medium text-gray-700 mb-1">Shift / Jam Kerja <span class="text-red-500">*</span></label>
                            <select name="shift_id" id="shift_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="" disabled selected>Pilih Shift</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $defaultStart) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $defaultEnd) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Jika hanya untuk 1 hari, samakan dengan Tanggal Mulai.</p>
                        </div>
                    </div>

                    <div class="bg-yellow-50 text-yellow-800 p-4 rounded-xl text-sm border border-yellow-200 mt-6 flex items-start gap-3">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p>Sistem akan secara otomatis membuat jadwal (Roster) untuk setiap hari dalam rentang tanggal di atas. Data lama di tanggal yang sama akan ditimpa (overwrite).</p>
                    </div>

                    <div class="flex items-center justify-end mt-8 border-t border-gray-100 pt-6">
                        <a href="{{ route('schedules.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2 font-medium transition mr-4">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium shadow-sm transition">
                            Terapkan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.user-checkbox');
            const searchInput = document.getElementById('searchUser');
            const userItems = document.querySelectorAll('.user-item');

            // Select All functionality
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    // Only check visible items
                    const item = cb.closest('.user-item');
                    if(item.style.display !== 'none') {
                        cb.checked = this.checked;
                    }
                });
            });

            // Update Select All state when individual checkboxes change
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allVisible = Array.from(checkboxes).filter(c => c.closest('.user-item').style.display !== 'none');
                    const allChecked = allVisible.every(c => c.checked);
                    const someChecked = allVisible.some(c => c.checked);
                    
                    selectAll.checked = allChecked && allVisible.length > 0;
                    selectAll.indeterminate = someChecked && !allChecked;
                });
            });

            // Search functionality
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                userItems.forEach(item => {
                    const name = item.querySelector('.user-name').textContent.toLowerCase();
                    const jabatan = item.querySelector('.user-jabatan').textContent.toLowerCase();
                    
                    if(name.includes(term) || jabatan.includes(term)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                        item.querySelector('.user-checkbox').checked = false; // uncheck if hidden
                    }
                });
                // Reset select all
                selectAll.checked = false;
                selectAll.indeterminate = false;
            });
        });
    </script>
</x-app-layout>
