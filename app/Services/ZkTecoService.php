<?php

namespace App\Services;

class ZkTecoService
{
    private $ip;

    private $port;

    private $socket;

    private $session_id = 0;

    private $reply_id = 0;

    const CMD_CONNECT = 1000;

    const CMD_EXIT = 1002;

    const CMD_ATTLOG_RRQ = 1503;

    const USHRT_MAX = 65535;

    public function __construct($ip, $port = 4370)
    {
        $this->ip = $ip;
        $this->port = $port;
    }

    /**
     * Test connection or open socket.
     */
    public function connect(): bool
    {
        if (empty($this->ip)) {
            return false;
        }

        // Demo / Mock mode for local testing
        if ($this->ip === '127.0.0.1' || $this->ip === 'demo' || $this->ip === 'localhost') {
            return true;
        }

        try {
            $this->socket = @fsockopen('udp://'.$this->ip, $this->port, $errno, $errstr, 3);
            if (! $this->socket) {
                return false;
            }
            stream_set_timeout($this->socket, 3);

            $command = self::CMD_CONNECT;
            $command_string = '';
            $chksum = 0;
            $session_id = 0;
            $reply_id = -1 + self::USHRT_MAX;

            $buf = pack('ssss', $command, $chksum, $session_id, $reply_id).$command_string;
            $len = strlen($buf);
            $chksum = $this->calculateChecksum($buf, $len);
            $buf = pack('ssss', $command, $chksum, $session_id, $reply_id).$command_string;

            $this->writeBuffer($buf);
            $response = $this->readBuffer();

            if (strlen($response) > 0) {
                $u = unpack('s*', substr($response, 0, 8));
                $this->session_id = $u[3] ?? 0;

                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Close connection.
     */
    public function disconnect(): void
    {
        if ($this->socket) {
            try {
                $command = self::CMD_EXIT;
                $command_string = '';
                $chksum = 0;
                $session_id = $this->session_id;
                $reply_id = $this->reply_id;

                $buf = pack('ssss', $command, $chksum, $session_id, $reply_id).$command_string;
                $len = strlen($buf);
                $chksum = $this->calculateChecksum($buf, $len);
                $buf = pack('ssss', $command, $chksum, $session_id, $reply_id).$command_string;

                $this->writeBuffer($buf);
                fclose($this->socket);
            } catch (\Exception $e) {
                // Ignore disconnect errors
            }
            $this->socket = null;
        }
    }

    /**
     * Retrieve attendance logs.
     */
    public function getAttendance(): array
    {
        // Mock mode for local testing
        if ($this->ip === '127.0.0.1' || $this->ip === 'demo' || $this->ip === 'localhost') {
            // Generate some mock logs for active employees
            $logs = [];
            $karyawans = \App\Models\Karyawan::limit(5)->get();
            $baseTime = now()->subDays(2);

            foreach ($karyawans as $idx => $k) {
                $nik = $k->nik ?: ('151'.$idx);
                // Clock-in
                $logs[] = [
                    'pin' => $nik,
                    'timestamp' => $baseTime->copy()->addHours(8)->addMinutes(rand(1, 30))->format('Y-m-d H:i:s'),
                    'state' => 0,
                    'type' => 'Masuk',
                ];
                // Clock-out
                $logs[] = [
                    'pin' => $nik,
                    'timestamp' => $baseTime->copy()->addHours(17)->addMinutes(rand(1, 15))->format('Y-m-d H:i:s'),
                    'state' => 1,
                    'type' => 'Pulang',
                ];
            }

            return $logs;
        }

        if (! $this->socket) {
            return [];
        }

        try {
            $command = self::CMD_ATTLOG_RRQ;
            $command_string = '';
            $chksum = 0;
            $session_id = $this->session_id;
            $reply_id = $this->reply_id;

            $buf = pack('ssss', $command, $chksum, $session_id, $reply_id).$command_string;
            $len = strlen($buf);
            $chksum = $this->calculateChecksum($buf, $len);
            $buf = pack('ssss', $command, $chksum, $session_id, $reply_id).$command_string;

            $this->writeBuffer($buf);

            $attendance = [];
            $data = '';

            // Loop to read all packets
            while (true) {
                $response = $this->readBuffer();
                if (empty($response)) {
                    break;
                }
                $data .= $response;
                if (strlen($response) < 1024) {
                    break;
                }
            }

            if (strlen($data) > 8) {
                $bytes = substr($data, 8);
                $record_len = 40;
                if (strlen($bytes) % 14 === 0 && strlen($bytes) % 40 !== 0) {
                    $record_len = 14;
                }

                $count = floor(strlen($bytes) / $record_len);

                for ($i = 0; $i < $count; $i++) {
                    $record = substr($bytes, $i * $record_len, $record_len);
                    if ($record_len == 14) {
                        $u = unpack('s2id/C5time/Cstate/C2trash', $record);
                        $pin = $u['id1'] + ($u['id2'] << 16);
                        $year = $u['time1'] + 2000;
                        $month = str_pad($u['time2'], 2, '0', STR_PAD_LEFT);
                        $day = str_pad($u['time3'], 2, '0', STR_PAD_LEFT);
                        $hour = str_pad($u['time4'], 2, '0', STR_PAD_LEFT);
                        $minute = str_pad($u['time5'], 2, '0', STR_PAD_LEFT);
                        $second = '00';
                        $timestamp = "$year-$month-$day $hour:$minute:$second";
                        $state = $u['state'];
                    } else {
                        $pin = trim(substr($record, 0, 24));
                        $pin = preg_replace('/[^0-9a-zA-Z]/', '', $pin);

                        $u = unpack('C*', substr($record, 26, 4));
                        if (count($u) >= 4) {
                            $time_val = $u[1] + ($u[2] << 8) + ($u[3] << 16) + ($u[4] << 24);

                            $second = $time_val % 60;
                            $time_val = floor($time_val / 60);
                            $minute = $time_val % 60;
                            $time_val = floor($time_val / 60);
                            $hour = $time_val % 24;
                            $time_val = floor($time_val / 24);
                            $day = ($time_val % 31) + 1;
                            $time_val = floor($time_val / 31);
                            $month = ($time_val % 12) + 1;
                            $time_val = floor($time_val / 12);
                            $year = $time_val + 2000;

                            $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                            $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                            $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
                            $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);
                            $second = str_pad($second, 2, '0', STR_PAD_LEFT);

                            $timestamp = "$year-$month-$day $hour:$minute:$second";
                        } else {
                            continue;
                        }
                        $state = ord(substr($record, 24, 1));
                    }

                    // Map state to corresponding types
                    if ($state == 0) {
                        $type = 'Masuk';
                    } elseif ($state == 4) {
                        $type = 'lembur_masuk';
                    } elseif ($state == 5) {
                        $type = 'lembur_pulang';
                    } else {
                        $type = 'Pulang';
                    }

                    $attendance[] = [
                        'pin' => $pin,
                        'timestamp' => $timestamp,
                        'state' => $state,
                        'type' => $type,
                    ];
                }
            }
        } catch (\Exception $e) {
            return [];
        }

        return $attendance;
    }

    private function calculateChecksum($buffer, $size)
    {
        $chksum = 0;
        $u = unpack('C*', $buffer);
        $i = 1;
        while ($size > 1) {
            $chksum += ($u[$i] | ($u[$i + 1] << 8));
            $i += 2;
            $size -= 2;
        }
        if ($size) {
            $chksum += $u[$i];
        }
        $chksum = ($chksum >> 16) + ($chksum & 0xFFFF);
        $chksum += ($chksum >> 16);

        return ~$chksum & 0xFFFF;
    }

    private function writeBuffer($buf)
    {
        @fwrite($this->socket, $buf);
    }

    private function readBuffer()
    {
        return @fread($this->socket, 10240);
    }
}
