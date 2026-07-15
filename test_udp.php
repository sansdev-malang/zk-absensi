<?php
require 'vendor/autoload.php';
$zk = new Mithun\PhpZkteco\Libs\ZKTeco('10.10.2.223', 4370, false, 25, 123456, 'udp');
echo "Connecting UDP...\n";
var_dump($zk->connect());
