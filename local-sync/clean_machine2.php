<?php
require 'vendor/autoload.php';
use Mithun\PhpZkteco\Libs\ZKTeco;
use Mithun\PhpZkteco\Libs\Services\User;

$ip = '10.10.2.223';
$port = 4370;
$zk = new ZKTeco($ip, $port, false, 25, 123456, 'tcp');

echo "Menyambungkan ke mesin...\n";
if ($zk->connect()) {
    echo "Tersambung! Mengambil data pengguna...\n";
    $users = User::get($zk);
    echo "Ditemukan " . count($users) . " total record.\n";

    $backupJson = file_get_contents('backup_mesin_2_users.json');
    $backupData = json_decode($backupJson, true);

    $validUids = [];
    foreach ($backupData as $pin => $u) {
        $validUids[$u['uid']] = true;
    }

    $deleted = 0;
    foreach ($users as $uid => $mu) {
        if (!isset($validUids[$uid])) {
            echo "Menghapus duplikat PIN {$mu['user_id']} dengan UID {$uid}...\n";
            $res = $zk->removeUser($uid);
            if ($res) {
                $deleted++;
            }
        }
    }
    
    $zk->disconnect();
    echo "Selesai! Menghapus $deleted duplikat.\n";
} else {
    echo "Gagal connect.\n";
}
