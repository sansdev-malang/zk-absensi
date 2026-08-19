<?php
require 'vendor/autoload.php';
require_once 'local-sync/RobustZkteco.php';

$zk = new RobustZkteco('10.10.2.223', 4370);
$users = $zk->getUsers();

$admins = [];
foreach ($users as $u) {
    if ($u['role'] > 0) {
        $admins[] = $u;
    }
}
echo "Admins on machine 10.10.2.223:\n";
print_r($admins);

$zk2 = new RobustZkteco('10.10.2.26', 4370);
$users2 = $zk2->getUsers();
$admins2 = [];
foreach ($users2 as $u) {
    if ($u['role'] > 0) {
        $admins2[] = $u;
    }
}
echo "Admins on machine 10.10.2.26:\n";
print_r($admins2);
