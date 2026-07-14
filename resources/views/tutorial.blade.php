<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            {{ __('Buku Panduan & SOP (Walkthrough)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-10 text-center max-w-3xl mx-auto px-4">
                <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-4">SOP Sistem Absensi Terintegrasi</h3>
                <p class="text-gray-500 text-lg">Panduan komprehensif bagi HR dan Admin. Ikuti alur dari Modul 1 hingga Modul 5 untuk memastikan mesin absensi dan laporan penggajian Anda sinkron 100%.</p>
            </div>

            <div class="grid grid-cols-1 gap-8">
                
                <!-- MODULE 1: Setup Awal -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-slate-900 px-6 py-4 flex items-center gap-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-700 text-white font-bold">1</span>
                        <h4 class="text-xl font-bold text-white">Setup Master Data (Satu Kali Saja)</h4>
                    </div>
                    <div class="p-6 md:p-8 space-y-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="md:w-1/3 shrink-0">
                                <div class="bg-blue-50 text-blue-800 rounded-xl p-4 text-sm font-medium border border-blue-100 h-full">
                                    Langkah pondasi ini cukup dilakukan saat pertama kali Anda menggunakan aplikasi, atau bila ada perubahan kebijakan perusahaan.
                                </div>
                            </div>
                            <div class="md:w-2/3 space-y-5">
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> 1.1 Tentukan Periode Penggajian</h5>
                                    <p class="text-sm text-gray-600">Masuk ke menu <a href="{{ route('settings.index') }}" class="text-blue-600 font-medium hover:underline">Periode</a>. Setel tanggal potong gaji (Cut-off). Misal: <strong>Awal Periode tanggal 27, Akhir Periode tanggal 26</strong>. Semua kalkulasi bonus akan berpatokan pada kalender ini.</p>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 1.2 Buat Skema Bonus</h5>
                                    <p class="text-sm text-gray-600">Buka <a href="{{ route('bonus-schemes.index') }}" class="text-blue-600 font-medium hover:underline">Skema Bonus</a>. Buat aturan denda keterlambatan (Misal: Hadir 00.00-06.50 = Rp 15.000). Jika kebijakan ini berlaku untuk <strong>semua karyawan</strong>, Anda cukup membuat 1 Skema "Global".</p>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 1.3 Daftarkan Jam Kerja (Shift)</h5>
                                    <p class="text-sm text-gray-600">Buka <a href="{{ route('shifts.index') }}" class="text-blue-600 font-medium hover:underline">Jam Kerja (Shift)</a>. Tambahkan semua shift yang ada di perusahaan Anda (Pagi, Malam, Satpam). <strong>Jangan lupa tautkan Skema Bonus</strong> yang dibuat di Langkah 1.2 pada setiap shift.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODULE 2: Manajemen Karyawan -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-emerald-600 px-6 py-4 flex items-center gap-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-800 text-white font-bold">2</span>
                        <h4 class="text-xl font-bold text-white">Sinkronisasi & Manajemen Karyawan</h4>
                    </div>
                    <div class="p-6 md:p-8 space-y-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="md:w-1/3 shrink-0">
                                <div class="bg-emerald-50 text-emerald-800 rounded-xl p-4 text-sm font-medium border border-emerald-100 h-full">
                                    Lakukan saat ada rekrutmen karyawan baru atau ada yang berhenti (resign).
                                </div>
                            </div>
                            <div class="md:w-2/3 space-y-5">
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> 2.1 Tarik Data Mesin ke Web</h5>
                                    <p class="text-sm text-gray-600">Pertama, daftarkan sidik jari karyawan di mesin ZKTeco. Kemudian, buka menu <a href="{{ route('users.index') }}" class="text-emerald-600 font-medium hover:underline">Karyawan</a> dan klik <strong>"Tarik Data"</strong>. Karyawan baru akan langsung muncul di tabel web secara otomatis.</p>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> 2.2 Atur Default Shift Karyawan</h5>
                                    <p class="text-sm text-gray-600">Di tabel Karyawan, perhatikan kolom <strong>Jadwal Shift (Default)</strong>. Anda dapat mengkliknya dan langsung mengubah jam kerja utamanya. Warna <em>badge</em> akan otomatis berubah sesuai shift untuk memudahkan identifikasi secara visual.</p>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> 2.3 Cara Menghapus Karyawan Resign</h5>
                                    <p class="text-sm text-gray-600">Jangan hapus dari mesin fisik! Cukup <strong>Hapus Karyawan dari aplikasi web ini</strong>. Sistem kita akan secara otomatis mengirim sinyal "hapus" ke mesin ZKTeco sehingga sidik jarinya juga terhapus dengan sendirinya.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODULE 3: Penjadwalan Bulanan -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-600 px-6 py-4 flex items-center gap-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-800 text-white font-bold">3</span>
                        <h4 class="text-xl font-bold text-white">Rutinitas Awal Bulan: Penjadwalan (Roster)</h4>
                    </div>
                    <div class="p-6 md:p-8 space-y-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="md:w-1/3 shrink-0">
                                <div class="bg-indigo-50 text-indigo-800 rounded-xl p-4 text-sm font-medium border border-indigo-100 h-full">
                                    Lakukan pada H-1 sebelum tanggal Cut-Off (misal tanggal 26) agar kalender kerja tertata.
                                </div>
                            </div>
                            <div class="md:w-2/3 space-y-5">
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> 3.1 Generate Jadwal Otomatis (1-Klik)</h5>
                                    <p class="text-sm text-gray-600">Buka menu <a href="{{ route('schedules.index') }}" class="text-indigo-600 font-medium hover:underline">Roster (Jadwal)</a>. Klik tombol biru <strong>"Generate Otomatis dari Shift Default"</strong>. Sistem akan melihat <em>Default Shift</em> semua orang, lalu membuatkan jadwal penuh sampai akhir periode. Proses ini sangat cepat!</p>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> 3.2 Tim Khusus (Tukar Shift / Satpam / Oplos)</h5>
                                    <p class="text-sm text-gray-600">Jika ada karyawan yang shiftnya berubah-ubah minggu ini, klik tombol <strong>"Assign Shift (Khusus)"</strong>. Anda bisa memilih nama karyawan, tanggal, dan shift tujuannya. Jadwal manual ini otomatis menimpa Jadwal Default yang digenerate di langkah 3.1.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODULE 4: Absensi & Tutup Buku -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-purple-600 px-6 py-4 flex items-center gap-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-800 text-white font-bold">4</span>
                        <h4 class="text-xl font-bold text-white">Rutinitas Akhir Bulan: Log & Penggajian (Payroll)</h4>
                    </div>
                    <div class="p-6 md:p-8 space-y-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="md:w-1/3 shrink-0">
                                <div class="bg-purple-50 text-purple-800 rounded-xl p-4 text-sm font-medium border border-purple-100 h-full">
                                    Langkah final di penghujung periode (tanggal 26 malam atau 27 pagi) untuk memproses Gaji & Bonus.
                                </div>
                            </div>
                            <div class="md:w-2/3 space-y-5">
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> 4.1 Tarik Riwayat (Log) Absensi</h5>
                                    <p class="text-sm text-gray-600">Di Menu **Attendance**, masuk ke <a href="{{ route('attendances.index') }}" class="text-purple-600 font-medium hover:underline">Data Absensi</a> lalu klik tombol <strong>"Tarik Log Mesin"</strong>. Tunggu sampai seluruh riwayat sidik jari masuk ke tabel web.</p>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> 4.2 Cek Rincian Harian & Kalkulasi Ulang</h5>
                                    <p class="text-sm text-gray-600">Buka menu <a href="{{ route('reports.index') }}" class="text-purple-600 font-medium hover:underline">Laporan Absensi & Bonus</a>, berada di tab <strong>Rincian Harian</strong>. Di sini Anda bisa memantau jam datang dan pulang <em>day-by-day</em>. Jika datanya dirasa belum terupdate penuh, tekan tombol <strong>"Kalkulasi Ulang"</strong>.</p>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 flex items-center gap-2 mb-2"><svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 4.3 Cetak Rekap Karyawan (Payroll)</h5>
                                    <p class="text-sm text-gray-600">Masih di menu Laporan, pindahlah ke tab <strong>Rekap Karyawan (Payroll)</strong>. Halaman ini merekapitulasi langsung <strong>Total Uang Bonus</strong> per karyawan selama sebulan. Tekan tombol <strong>"Cetak Laporan"</strong> untuk meng-export tabel ini ke PDF / Kertas dan melampirkannya bersama slip gaji.</p>
                                </div>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-2">
                                    <h5 class="font-bold text-yellow-800 flex items-center gap-2 mb-1"><svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> 4.4 Kosongkan Memori Mesin (Bila Diperlukan)</h5>
                                    <p class="text-sm text-yellow-800">Jika penggajian bulan tersebut sudah selesai, amankan ruang memori mesin. Buka <a href="{{ route('devices.index') }}" class="text-yellow-700 font-bold hover:underline">Perangkat ZKTeco</a>, pilih alatnya, dan tekan <strong>"Bersihkan Semua Log"</strong> agar mesin Anda kembali fresh untuk bulan berikutnya.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
