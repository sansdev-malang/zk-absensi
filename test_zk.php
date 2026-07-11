<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Mithun\PhpZkteco\Libs\ZKTeco;

echo "Connecting to 10.10.2.26 via TCP...\n";
$zk = new ZKTeco('10.10.2.26', 4370, protocol: 'tcp');

if ($zk->connect()) {
    echo "Connected successfully via TCP!\n";
    echo "Fetching users...\n";
    $time_start = microtime(true);
    
    try {
        $attendances = $zk->getAttendances();
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        
        echo "Fetched " . count($attendances) . " records in {$execution_time} seconds.\n";
        
        if (count($attendances) > 0) {
            echo "Sample Attendance Data (first 3):\n";
            print_r(array_slice($attendances, 0, 3));
        }
        
    } catch (\Exception $e) {
        echo "Exception during getUsers: " . $e->getMessage() . "\n";
    }
    
    $zk->disconnect();
} else {
    echo "Failed to connect via TCP.\n";
}
