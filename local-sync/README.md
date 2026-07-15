# ZKTeco Local Sync Service

Script ini digunakan untuk menghubungkan mesin ZKTeco di jaringan lokal kantor dengan aplikasi absensi yang berada di Hosting Publik.

## Persyaratan
- PC / Komputer Windows/Linux yang satu jaringan LAN/WiFi dengan Mesin ZKTeco.
- PHP minimal versi 8.1 terinstall di PC tersebut.
- Composer terinstall.

## Cara Pemasangan di PC Kantor

1. Buka folder ini (`local-sync`) di terminal/CMD PC Anda.
2. Jalankan perintah `composer install` untuk mengunduh library yang dibutuhkan.
3. Buka file `sync.php` dan sesuaikan variabel konfigurasi berikut:
   - `$deviceIp` = IP Mesin ZKTeco Anda (contoh: `192.168.1.201`)
   - `$apiUrl` = URL Hosting aplikasi Anda (contoh: `https://absensi.perusahaan.com/api/sync`)
   - `$apiToken` = `rahasia123` (pastikan sama dengan `SYNC_SECRET_TOKEN` di `.env` server)

## Cara Menjalankan Otomatis (Windows Task Scheduler)

Agar data absensi terkirim secara otomatis setiap X menit, Anda bisa memasang script ini di Windows Task Scheduler:

1. Buka **Task Scheduler** di Windows.
2. Klik **Create Task...**
3. Di tab **General**:
   - Name: `ZKTeco Sync Service`
   - Centang `Run whether user is logged on or not` (opsional)
4. Di tab **Triggers**:
   - Klik **New...**
   - Pilih `Daily`, lalu centang **Repeat task every:** dan atur ke `5 minutes` atau sesuai kebutuhan.
   - Set duration ke `Indefinitely`.
5. Di tab **Actions**:
   - Klik **New...**
   - Action: `Start a program`
   - Program/script: Isi dengan path lokasi PHP Anda (contoh: `C:\xampp\php\php.exe`)
   - Add arguments: Isi dengan path file sync.php Anda (contoh: `C:\local-sync\sync.php`)
   - Start in: Kosongkan atau isi dengan path folder (contoh: `C:\local-sync`)
6. Simpan (OK).

Sekarang, komputer lokal Anda akan bertugas sebagai "Kurir" yang mengambil data dari ZKTeco dan mengirimkannya ke Hosting Anda secara otomatis!
