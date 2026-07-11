<?php

namespace App\Services;

use Mithun\PhpZkteco\Libs\ZKTeco;
use Exception;

class ZktecoService
{
    protected $ip;
    protected $port;
    protected $zk;

    public function __construct($ip = '192.168.1.201', $port = 4370)
    {
        $this->ip = $ip;
        $this->port = $port;
        // Menggunakan protokol TCP agar tidak nyangkut (hang)
        try {
            $this->zk = new ZKTeco($ip, $port, protocol: 'tcp');
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
                $attendance = $this->zk->getAttendances();
                $this->disconnect();
                return $attendance;
            }
        } catch (Exception $e) {
            // Log error
        }
        return [];
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
