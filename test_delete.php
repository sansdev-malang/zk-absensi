<?php
require 'vendor/autoload.php';
require_once 'local-sync/RobustZkteco.php';

$zk = new RobustZkteco('10.10.2.223', 4370);
$users = $zk->getUsers();
echo "Total users: " . count($users) . "\n";
if (count($users) > 0) {
    // Pick the last user to avoid deleting important ones, or just list them
    $firstUser = $users[count($users) - 1];
    echo "Trying to delete UID: " . $firstUser['uid'] . " and USERID: " . $firstUser['userid'] . "\n";
    
    // Test 1: removeUser with UID (integer)
    $res1 = $zk->deleteUsers([$firstUser['uid']]);
    echo "Delete by UID result: $res1\n";
    
    // Test 2: removeUser with USERID (string)
    $res2 = $zk->deleteUsers([$firstUser['userid']]);
    echo "Delete by USERID result: $res2\n";
}
