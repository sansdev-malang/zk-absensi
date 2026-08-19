<?php
require 'vendor/autoload.php';
require 'RobustZkteco.php';

$ip = '10.10.2.223';
$port = 4370;
$comKey = 123456;
$protocol = 'tcp';

echo "Memulai pembersihan duplikat di Mesin 2 ($ip)...\n";

$zk = new RobustZkteco($ip, $port, $comKey, $protocol);

// Ambil semua user dari mesin (sekarang key-nya adalah UID berkat perbaikan di User.php)
$machineUsers = $zk->getUsers();
echo "Ditemukan " . count($machineUsers) . " total record di mesin 2 (termasuk duplikat).\n";

// Baca backup yang valid (sebelum ada duplikat atau yang asli)
$backupJson = file_get_contents('backup_mesin_2_users.json');
$backupData = json_decode($backupJson, true);

// Buat peta UID valid (yang ada di backup)
$validUids = [];
foreach ($backupData as $pin => $u) {
    $validUids[$u['uid']] = true;
}

$uidsToDelete = [];
foreach ($machineUsers as $uid => $mu) {
    if (!isset($validUids[$uid])) {
        echo "Menemukan duplikat PIN {$mu['user_id']} dengan UID {$uid}...\n";
        $uidsToDelete[] = $uid;
    }
}

if (!empty($uidsToDelete)) {
    echo "Menghapus " . count($uidsToDelete) . " duplikat...\n";
    $deletedCount = $zk->deleteUsers($uidsToDelete);
    echo "Selesai! Menghapus $deletedCount duplikat.\n";
} else {
    echo "Tidak ada duplikat yang perlu dihapus.\n";
}
