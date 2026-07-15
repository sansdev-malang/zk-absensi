<?php

namespace App\Services;

use Mithun\PhpZkteco\Libs\ZKTeco;
use Exception;

class ZktecoService
{
    protected $ip;
    protected $port;
    protected $zk;

    public function __construct($ip = '192.168.1.201', $port = 4370, $commKey = 0)
    {
        $this->ip = $ip;
        $this->port = $port;
        // Menggunakan protokol TCP.
        try {
            $this->zk = new ZKTeco($ip, $port, false, 25, $commKey, 'tcp');
        } catch (\Throwable $e) {
            $this->zk = null;
        }
    }

    public function connect()
    {
        if (!$this->zk) {
            return false;
        }

        // Ping socket terlebih dahulu dengan timeout 2 detik untuk menghindari 504 Gateway Timeout
        $fp = @fsockopen($this->ip, $this->port, $errno, $errstr, 2);
        if (!$fp) {
            return false;
        }
        fclose($fp);

        try {
            return $this->zk->connect();
        } catch (\Throwable $e) {
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
                // Gunakan reflection untuk mendapatkan inner ZKTeco client
                $zkProp = (new \ReflectionClass($this->zk))->getProperty('_zkclient');
                
                \Mithun\PhpZkteco\Libs\Services\Ping::run($this->zk);
                $command = \Mithun\PhpZkteco\Libs\Services\Util::CMD_ATT_LOG_RRQ;
                
                $session = $this->zk->_command($command, '', \Mithun\PhpZkteco\Libs\Services\Util::COMMAND_TYPE_DATA);
                if ($session === false) {
                    $this->disconnect();
                    return [];
                }
                
                $attData = \Mithun\PhpZkteco\Libs\Services\Util::recData($this->zk);
                $attendance = [];
                
                if (!empty($attData)) {
                    // Mesin ini menggunakan format TFT 40-byte records.
                    // Payload dari CMD_DATA diawali dengan 8 byte ZK header + 4 byte ukuran (size).
                    // Kita akan buang 12 byte pertama.
                    $attData = substr($attData, 12);
                    
                    while (strlen($attData) >= 40) {
                        $record = substr($attData, 0, 40);
                        
                        $userIdStr = substr($record, 2, 24);
                        $userId = trim(str_replace(chr(0), '', $userIdStr));
                        
                        $timestampBytes = substr($record, 27, 4);
                        $t = unpack('V', $timestampBytes)[1];
                        
                        $stateByte = substr($record, 31, 1);
                        $state = ord($stateByte);
                        
                        // ZKTeco epoch starts at 2000-01-01 (t=0)
                        if ($t > 0 && !empty($userId)) {
                            $timestamp = \Mithun\PhpZkteco\Libs\Services\Util::decodeTime($t);
                            $attendance[] = [
                                'uid' => $userId, // UID auto increment mesin (tidak dipakai library default, kita isi sama dgn user_id)
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
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ZKTeco parse error: ' . $e->getMessage());
        }
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
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ZKTeco clearAttendance error: ' . $e->getMessage());
        }
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
        } catch (Exception $e) {
            // Log error
        }
        return [];
    }

    public function syncTime()
    {
        return true;
    }

    public function restartDevice()
    {
        try {
            if ($this->connect()) {
                $this->zk->restart();
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    public function getDeviceInfo()
    {
        try {
            if ($this->connect()) {
                $version = $this->zk->version();
                $osVersion = $this->zk->osVersion();
                $platform = $this->zk->platform();
                
                $this->disconnect();
                
                return [
                    'firmware' => $version,
                    'os' => $osVersion,
                    'platform' => $platform,
                ];
            }
        } catch (\Throwable $e) {
            // error
        }
        
        return [
            'firmware' => 'Unknown',
            'os' => 'Unknown',
            'platform' => 'Unknown',
        ];
    }

    public function pushUser($pin, $name, $role = 0, $password = '')
    {
        try {
            if ($this->connect()) {
                $users = $this->zk->getUsers();
                
                // Cari apakah user (PIN) sudah ada di mesin
                $existingUid = null;
                $maxUid = 0;
                
                foreach ($users as $userId => $userData) {
                    if ((int)$userData['uid'] > $maxUid) {
                        $maxUid = (int)$userData['uid'];
                    }
                    if ((string)$userId === (string)$pin) {
                        $existingUid = (int)$userData['uid'];
                    }
                }
                
                $targetUid = $existingUid ? $existingUid : ($maxUid + 1);
                
                // ZKTeco role: 0 = User biasa, 14 = Super Admin
                $zkRole = strtolower($role) === 'admin' ? 14 : 0;
                
                $result = $this->zk->setUser($targetUid, $pin, $name, $password, $zkRole);
                $this->disconnect();
                return $result;
            }
        } catch (\Throwable $e) {
            // log error
        }
        return false;
    }

    public function removeUser($pin)
    {
        try {
            if ($this->connect()) {
                $users = $this->zk->getUsers();
                
                // Cari internal UID mesin berdasarkan PIN
                $targetUid = null;
                foreach ($users as $userId => $userData) {
                    if ((string)$userId === (string)$pin) {
                        $targetUid = (int)$userData['uid'];
                        break;
                    }
                }
                
                if ($targetUid) {
                    $result = $this->zk->removeUser($targetUid);
                    $this->disconnect();
                    return $result;
                }
            }
        } catch (\Throwable $e) {
            // log error
        }
        return false;
    }
}
