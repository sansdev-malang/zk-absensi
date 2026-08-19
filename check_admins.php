<?php
require 'vendor/autoload.php';
$client = new GuzzleHttp\Client(['verify'=>false]);
$res = $client->get('https://zkabsensi.gheptech.com/api/sync/users-to-push', [
    'headers' => [
        'Authorization' => 'Bearer rahasia123',
        'Accept' => 'application/json'
    ]
]);
$data = json_decode($res->getBody(), true);
$admins = array_filter($data['users'], function($u) { return $u['role'] == 14; });
echo "Admins from web API:\n";
print_r($admins);
