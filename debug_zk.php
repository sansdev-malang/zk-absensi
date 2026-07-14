<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new \App\Services\ZktecoService('10.10.2.26', 4370);
if($service->connect()) {
    $zkProp = (new \ReflectionClass($service))->getProperty('zk');
    $zkProp->setAccessible(true);
    $zkInstance = $zkProp->getValue($service);

    \Mithun\PhpZkteco\Libs\Services\Ping::run($zkInstance);
    $command = \Mithun\PhpZkteco\Libs\Services\Util::CMD_ATT_LOG_RRQ;
    
    $session = $zkInstance->_command($command, '', \Mithun\PhpZkteco\Libs\Services\Util::COMMAND_TYPE_DATA);
    if ($session === false) {
        echo "Command failed\n";
    } else {
        $attData = \Mithun\PhpZkteco\Libs\Services\Util::recData($zkInstance);
        echo "Length of recData: " . strlen($attData) . "\n";
        echo "Hex dump: " . bin2hex(substr($attData, 0, 100)) . "\n";
    }
}
