<?php
require 'vendor/autoload.php';
use Mithun\PhpZkteco\Libs\ZKTeco;
use Mithun\PhpZkteco\Libs\Services\User;

$ip = '10.10.2.223';
$port = 4370;
$zk = new ZKTeco($ip, $port, false, 25, 123456, 'tcp');

if ($zk->connect()) {
    $users = User::get($zk);
    file_put_contents('machine2_current_state.json', json_encode($users, JSON_PRETTY_PRINT));
    echo "Saved to machine2_current_state.json. Total records: " . count($users) . "\n";
    $zk->disconnect();
} else {
    echo "Failed to connect.\n";
}
