<?php
require 'vendor/autoload.php';
require 'RobustZkteco.php';

$ip = '10.10.2.26';
$port = 4370;
$comKey = 0;

echo "Mencoba koneksi ke Mesin 1 ($ip) dengan COM Key $comKey...\n";

// Test UDP first, it's safer
echo "1. Menguji protokol UDP...\n";
$zkUdp = new RobustZkteco($ip, $port, $comKey, 'udp');
if ($zkUdp->connect()) {
    echo "   [SUKSES] Koneksi UDP berhasil!\n";
    $zkUdp->disconnect();
} else {
    echo "   [GAGAL] Koneksi UDP ditolak atau timeout.\n";
}

// Test TCP
echo "\n2. Menguji protokol TCP...\n";
$zkTcp = new RobustZkteco($ip, $port, $comKey, 'tcp');
if ($zkTcp->connect()) {
    echo "   [SUKSES] Koneksi TCP berhasil!\n";
    $zkTcp->disconnect();
} else {
    echo "   [GAGAL] Koneksi TCP ditolak atau timeout.\n";
}
