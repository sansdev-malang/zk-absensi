<?php
require 'vendor/autoload.php';
use Mithun\PhpZkteco\Libs\ZKTeco;
use Mithun\PhpZkteco\Libs\Services\User;

$ip = '10.10.2.26';
$port = 4370;
$zk = new ZKTeco($ip, $port, false, 25, 0, 'tcp');

if ($zk->connect()) {
    $users = User::get($zk);
    echo "Jumlah user di Mesin 1 saat ini: " . count($users) . "\n";
    $zk->disconnect();
} else {
    echo "Gagal koneksi ke Mesin 1.\n";
}
