<?php
require 'vendor/autoload.php';
require 'RobustZkteco.php';

$ip = '10.10.2.26';
$port = 4370;
$comKey = 0;

$zk = new RobustZkteco($ip, $port, $comKey, 'tcp');
echo "Mengambil attendance...\n";
$att = $zk->getAttendance();
echo "Attendance count: " . count($att) . "\n";

echo "Mengambil users...\n";
$users = $zk->getUsers();
echo "Users count: " . count($users) . "\n";
