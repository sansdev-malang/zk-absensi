<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Mithun\PhpZkteco\Libs\Services\Ping;
use Mithun\PhpZkteco\Libs\Services\Util;

echo "Dumping raw attendance data...\n";
$service = new \App\Services\ZktecoService('10.10.2.26', 4370);

if($service->connect()) {
    $zk = (new \ReflectionClass($service))->getProperty('zk');
    $zk->setAccessible(true);
    $zkInstance = $zk->getValue($service);

    $command = Util::CMD_ATT_LOG_RRQ;
    $command_string = '';
    
    $session = $zkInstance->_command($command, $command_string, Util::COMMAND_TYPE_DATA);
    if ($session === false) {
        echo "Command failed\n";
    } else {
        $attData = Util::recData($zkInstance);
        echo "Raw data length: " . strlen($attData) . "\n";
        echo "Hex dump (first 200 bytes): " . bin2hex(substr($attData, 0, 200)) . "\n";
    }
    
    $service->disconnect();
} else {
    echo "TCP failed connect\n";
}
