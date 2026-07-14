<?php
require __DIR__.'/vendor/autoload.php';
use Mithun\PhpZkteco\Libs\Services\Util;

$t = hexdec(Util::reverseHex('41dd322e'));
echo "Timestamp integer: $t\n";
echo "Date: " . Util::decodeTime($t) . "\n";
