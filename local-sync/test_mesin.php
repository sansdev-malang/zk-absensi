<?php
$ip = '10.10.2.223';
$port = 4370;

echo "=== DIAGNOSTIK KONEKSI MESIN ZKTECO ===\n";
echo "Target IP   : $ip\n";
echo "Target Port : $port\n\n";

// 1. PING TEST
echo "[1] Menguji Koneksi Dasar (Ping)...\n";
exec("ping -n 2 -w 1000 $ip", $output, $status);
if ($status === 0) {
    echo "    -> SUKSES: Mesin hidup dan terhubung ke jaringan.\n";
} else {
    echo "    -> GAGAL: Mesin tidak merespons ping (Mati atau beda jaringan).\n";
}

// 2. TCP PORT TEST
echo "\n[2] Menguji Koneksi Port TCP $port...\n";
$fp = @fsockopen($ip, $port, $errno, $errstr, 2);
if ($fp) {
    echo "    -> SUKSES: Port TCP Terbuka! Mesin siap menerima koneksi TCP.\n";
    fclose($fp);
} else {
    echo "    -> GAGAL: Port TCP Tertutup atau Menolak Koneksi. ($errstr)\n";
}

// 3. UDP PORT TEST
echo "\n[3] Menguji Soket UDP $port...\n";
$sock = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($sock) {
    socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, ["sec" => 2, "usec" => 0]);
    // Mengirim paket sapaan ZKTeco (CMD_CONNECT = 1000)
    $msg = pack("vvvV", 1000, 0, 0, 0); 
    @socket_sendto($sock, $msg, strlen($msg), 0, $ip, $port);
    
    $buf = '';
    $from = '';
    $port_reply = 0;
    @socket_recvfrom($sock, $buf, 1024, 0, $from, $port_reply);
    
    if (strlen($buf) > 0) {
        echo "    -> SUKSES: Mesin merespons perintah UDP!\n";
    } else {
        echo "    -> GAGAL: Mesin tidak merespons jalur UDP (Timeout).\n";
    }
    socket_close($sock);
} else {
    echo "    -> GAGAL: Tidak bisa membuat soket UDP lokal.\n";
}

echo "\n==========================================\n";
echo "KESIMPULAN:\n";
echo "- Jika TCP dan UDP GAGAL tapi Ping SUKSES: Berarti mesin nge-hang, atau portnya BUKAN 4370.\n";
echo "- Jika TCP SUKSES: Gunakan 'protocol' => 'tcp' di sync.php.\n";
echo "- Jika UDP SUKSES: Gunakan 'protocol' => 'udp' di sync.php.\n";
echo "==========================================\n";
