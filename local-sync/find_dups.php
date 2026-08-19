<?php
$json = file_get_contents('machine2_current_state.json');
$users = json_decode($json, true);

$pins = [];
foreach ($users as $uid => $u) {
    $pin = trim((string)$u['user_id']);
    if (!isset($pins[$pin])) {
        $pins[$pin] = [];
    }
    $pins[$pin][] = $uid;
}

foreach ($pins as $pin => $uids) {
    if (count($uids) > 1) {
        echo "PIN: $pin memiliki duplikat UID: " . implode(', ', $uids) . "\n";
    }
}
echo "Selesai mengecek duplikat.\n";
