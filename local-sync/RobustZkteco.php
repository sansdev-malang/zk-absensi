<?php
use Mithun\PhpZkteco\Libs\ZKTeco;
use Mithun\PhpZkteco\Libs\Services\Ping;
use Mithun\PhpZkteco\Libs\Services\Util;

class RobustZkteco
{
    protected $ip;
    protected $port;
    protected $zk;

    public function __construct($ip = '192.168.1.201', $port = 4370, $password = 0, $protocol = 'udp')
    {
        $this->ip = $ip;
        $this->port = $port;
        try {
            if (empty($password) || $password == 0) {
                // Parameter default ZKTeco: host, port, ping, timeout, password, protocol
                $this->zk = new ZKTeco($ip, $port, false, 25, 0, $protocol);
            } else {
                $this->zk = new ZKTeco($ip, $port, false, 25, $password, $protocol);
            }
        } catch (\Throwable $e) {
            echo "   [Debug] Exception saat inisialisasi ZKTeco: " . $e->getMessage() . "\n";
            $this->zk = null;
        }
    }

    public function connect()
    {
        if (!$this->zk) {
            echo "   [Debug] ZK object is null\n";
            return false;
        }

        // [PENTING] Ping rahasia untuk "membangunkan" mesin lama (seperti Mesin 1)
        // Kita tidak akan membatalkan proses jika ini gagal, agar Mesin 2 tetap bisa jalan
        $fp = @fsockopen($this->ip, $this->port, $errno, $errstr, 2);
        if ($fp) {
            fclose($fp);
        }

        try {
            $conn = $this->zk->connect();
            if (!$conn) {
                echo "   [Debug] ZKTeco->connect() mengembalikan false\n";
            }
            return $conn;
        } catch (\Throwable $e) {
            echo "   [Debug] ZKTeco->connect() Exception: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function disconnect()
    {
        return $this->zk->disconnect();
    }

    public function getAttendance()
    {
        try {
            if ($this->connect()) {
                Ping::run($this->zk);
                $command = Util::CMD_ATT_LOG_RRQ;
                
                $session = $this->zk->_command($command, '', Util::COMMAND_TYPE_DATA);
                if ($session === false) {
                    $this->disconnect();
                    return [];
                }
                
                $attData = Util::recData($this->zk);
                $attendance = [];
                
                if (!empty($attData)) {
                    $attData = substr($attData, 12);
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
                                'uid' => $userId,
                                'user_id' => $userId,
                                'state' => $state,
                                'record_time' => $timestamp,
                                'type' => 0,
                            ];
                        }
                        $attData = substr($attData, 40);
                    }
                }
                
                $this->disconnect();
                return $attendance;
            }
        } catch (\Exception $e) {}
        return [];
    }

    public function clearAttendance()
    {
        try {
            if ($this->connect()) {
                $result = $this->zk->clearAttendance();
                $this->disconnect();
                return $result;
            }
        } catch (\Exception $e) {}
        return false;
    }

    public function getUsers()
    {
        try {
            if ($this->connect()) {
                $users = $this->zk->getUsers();
                $this->disconnect();
                return $users;
            }
        } catch (\Exception $e) {}
        return [];
    }
}
