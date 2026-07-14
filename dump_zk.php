<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test with UDP
echo "Testing with UDP...\n";
$zkUdp = new \Mithun\PhpZkteco\Libs\ZKTeco('10.10.2.26', 4370, false, 15, 0, 'udp');
if($zkUdp->connect()) {
    $data = $zkUdp->getAttendances();
    echo "Fetched " . count($data) . " via UDP\n";
    $zkUdp->disconnect();
} else {
    echo "UDP failed connect\n";
}

// Test with TCP
echo "Testing with TCP...\n";
$zkTcp = new \Mithun\PhpZkteco\Libs\ZKTeco('10.10.2.26', 4370, false, 15, 0, 'tcp');
if($zkTcp->connect()) {
    $data = $zkTcp->getAttendances();
    echo "Fetched " . count($data) . " via TCP\n";
    $zkTcp->disconnect();
} else {
    echo "TCP failed connect\n";
}
