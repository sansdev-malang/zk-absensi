<?php
require 'vendor/autoload.php';
use Mithun\PhpZkteco\Libs\ZKTeco;
use Mithun\PhpZkteco\Libs\Services\User;
use GuzzleHttp\Client;

$ip = '10.10.2.26';
$port = 4370;
$zk = new ZKTeco($ip, $port, false, 25, 0, 'tcp');

if ($zk->connect()) {
    echo "Terhubung ke Mesin 1. Menyedot data user...\n";
    $users = User::get($zk);
    echo "Berhasil mengambil " . count($users) . " data user dari Mesin 1.\n";
    $zk->disconnect();

    $apiUrl = 'https://zkabsensi.gheptech.com/api/sync';
    $apiToken = 'rahasia123';
    $client = new Client([
        'base_uri' => $apiUrl . '/',
        'headers' => [
            'Authorization' => 'Bearer ' . $apiToken,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ],
        'verify' => false,
    ]);

    $userChunks = array_chunk($users, 100);
    foreach ($userChunks as $i => $chunk) {
        echo "-> Mengirim data user ke server (Part " . ($i + 1) . " dari " . count($userChunks) . ")...\n";
        $response = $client->post('users', [
            'json' => [
                'device_ip' => $ip,
                'users' => $chunk
            ]
        ]);
        echo "   Response User: " . $response->getBody() . "\n";
    }
    
    echo "Operasi Penyelamatan Selesai!\n";
} else {
    echo "Gagal koneksi ke Mesin 1.\n";
}
