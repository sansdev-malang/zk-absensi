<?php
require 'local-sync/vendor/autoload.php';
require 'local-sync/RobustZkteco.php';
$zk = new RobustZkteco('10.10.2.223', 4370, 123456, 'udp');
echo "Connecting to 10.10.2.223...\n";
if ($zk->connect()) {
    echo "Connected successfully!\n";
    $zk->disconnect();
} else {
    echo "Connection failed.\n";
}
