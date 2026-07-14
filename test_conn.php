<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new \App\Services\ZktecoService('10.10.2.26', 4370);
if($service->connect()) {
    echo "Connected successfully! Fetching...\n";
    $data = $service->getAttendance();
    echo "Fetched " . count($data) . " records.\n";
    if (count($data) > 0) {
        print_r(array_slice($data, 0, 3));
    }
} else {
    echo "Failed to connect!\n";
}
