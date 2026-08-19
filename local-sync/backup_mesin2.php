<?php
require 'vendor/autoload.php';
require 'RobustZkteco.php';

$ip = '10.10.2.223';
$port = 4370;
$comKey = 123456;
$protocol = 'tcp';

echo "Memulai backup data pengguna dari Mesin 2 ($ip)...\n";

$zk = new RobustZkteco($ip, $port, $comKey, $protocol);

$users = $zk->getUsers();

if (!empty($users)) {
    echo "Berhasil menemukan " . count($users) . " data user di memori mesin.\n";
    
    // Simpan ke file JSON
    $json = json_encode($users, JSON_PRETTY_PRINT);
    file_put_contents('backup_mesin_2_users.json', $json);
    
    echo "Data berhasil dibackup ke file: backup_mesin_2_users.json\n";
} else {
    echo "Tidak ada data user yang ditemukan di mesin, atau koneksi gagal.\n";
}
