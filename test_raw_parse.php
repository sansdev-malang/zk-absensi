<?php
require __DIR__.'/vendor/autoload.php';
use Mithun\PhpZkteco\Libs\Services\Util;

$hex = "dd052ef300000100b8b4470001004153534131303434360000000000000000000000000000000f41dd322e00000000000000000002004153534131303434360000000000000000000000000000000feddd322e01000000000000000003004153534131303434360000000000000000000000000000000f90de322e01000000000000000004004153534131303434360000000000000000000000000000000f52ee322e0000000000000000000500415353413934353500";
$attData = hex2bin($hex);

// Wait, the ZKTeco library strips 8 bytes TCP header first.
// The `dd052ef3...` is already stripped of TCP header?
// Yes, `Util::recData` returns payload.
// Actually, ZKTeco packets always have an 8-byte ZKTeco header:
// command (2), chksum (2), session_id (2), reply_id (2).
// In my hex: `dd05 2ef3 0000 0100` -> 8 bytes ZKTeco header!
// Wait! `dd05` = 1501 (CMD_DATA)!
// `2ef3` = checksum
// `0000` = session ID
// `0100` = reply ID
// After the 8-byte ZKTeco header, for CMD_DATA, there is a 4-byte size!
// `b8b44700` = 0x0047b4b8 = 4699320 bytes!
// After the 4-byte size, the payload starts!
// So the payload starts at 8 + 4 = 12 bytes!
// This makes perfect sense!

$attData = substr($attData, 12);

$attendance = [];
while (strlen($attData) >= 40) {
    $record = substr($attData, 0, 40);
    
    $userIdStr = substr($record, 2, 24);
    $userId = trim(str_replace(chr(0), '', $userIdStr));
    
    $timestampBytes = substr($record, 27, 4);
    $t = unpack('V', $timestampBytes)[1];
    
    $stateByte = substr($record, 31, 1);
    $state = ord($stateByte);
    
    if ($t > 0 && !empty($userId)) {
        $timestamp = Util::decodeTime($t);
        $attendance[] = [
            'user_id' => $userId,
            'state' => $state,
            'record_time' => $timestamp,
        ];
    }
    
    $attData = substr($attData, 40);
}

print_r($attendance);
