<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Process\Process;
use Throwable;

class BackupController extends Controller
{
    public function download()
    {
        if (auth()->user()->username !== 'kiky') {
            abort(403, 'Unauthorized action.');
        }

        $connection = DB::connection();
        if (! in_array($connection->getDriverName(), ['mysql', 'mariadb'], true)) {
            return back()->with('error', 'Backup SQL hanya tersedia untuk database MySQL/MariaDB.');
        }

        $database = $connection->getConfig('database');
        $directory = storage_path('app/private/backups');

        if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
            return back()->with('error', 'Direktori backup tidak dapat dibuat.');
        }

        $path = tempnam($directory, 'backup-');
        if ($path === false) {
            return back()->with('error', 'File backup sementara tidak dapat dibuat.');
        }

        $complete = false;

        try {
            $executable = file_exists('C:\xampp\mysql\bin\mysqldump.exe')
                ? 'C:\xampp\mysql\bin\mysqldump.exe'
                : 'mysqldump';

            $command = [
                $executable,
                '--single-transaction',
                '--quick',
                '--hex-blob',
                '--no-tablespaces',
                '--default-character-set=utf8mb4',
                '--dump-date',
                '--result-file='.$path,
                '--user='.$connection->getConfig('username'),
            ];

            if ($socket = $connection->getConfig('unix_socket')) {
                $command[] = '--socket='.$socket;
            } else {
                $command[] = '--host='.$connection->getConfig('host');
                $command[] = '--port='.$connection->getConfig('port');
            }

            $command[] = $database;

            $process = new Process($command, null, [
                'MYSQL_PWD' => (string) $connection->getConfig('password'),
            ]);
            $process->setTimeout(null);
            $process->run();

            clearstatcache(true, $path);
            $complete = $process->isSuccessful() && $this->isCompleteDump($path) && $this->hasValidInsertValues($path);
            if (! $complete) {
                Log::error('Database backup failed', [
                    'exit_code' => $process->getExitCode(),
                    'error' => $process->getErrorOutput(),
                ]);

                return back()->with('error', 'Backup gagal atau file SQL tidak valid. Coba lagi dan periksa log server.');
            }

            $fileName = 'backup-'.preg_replace('/[^A-Za-z0-9_-]/', '_', $database).'-'.date('Y-m-d-H-i-s').'.sql';

            return Response::download($path, $fileName)->deleteFileAfterSend(true);
        } catch (Throwable $exception) {
            Log::error('Database backup failed', ['exception' => $exception]);

            return back()->with('error', 'Backup gagal. Periksa log server.');
        } finally {
            if (! $complete) {
                @unlink($path);
            }
        }
    }

    private function isCompleteDump(string $path): bool
    {
        if (! is_file($path) || filesize($path) === 0) {
            return false;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }

        try {
            fseek($handle, -min(256, filesize($path)), SEEK_END);

            return str_contains(stream_get_contents($handle), '-- Dump completed on ');
        } finally {
            fclose($handle);
        }
    }

    private function hasValidInsertValues(string $path): bool
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }

        try {
            while (($line = fgets($handle)) !== false) {
                if (! str_starts_with($line, 'INSERT INTO ')) {
                    continue;
                }

                // Ignore quoted data so text and JSON cannot be mistaken for SQL values.
                $outsideStrings = $this->sqlOutsideStrings($line);
                if ($outsideStrings === null) {
                    return false;
                }

                $matchCount = preg_match_all('/(?:^|[,\\s(])(\\d+[A-Za-z_][A-Za-z0-9_]*)(?=[,\\s)])/', $outsideStrings, $matches);
                if ($matchCount === false) {
                    return false;
                }

                if ($matchCount > 0) {
                    foreach ($matches[1] as $token) {
                        if (! is_numeric($token) && ! preg_match('/^0x[0-9a-f]+$/i', $token)) {
                            return false;
                        }
                    }
                }
            }

            return feof($handle);
        } finally {
            fclose($handle);
        }
    }

    private function sqlOutsideStrings(string $line): ?string
    {
        $length = strlen($line);
        $offset = 0;
        $outside = '';

        while (($start = strpos($line, "'", $offset)) !== false) {
            $outside .= substr($line, $offset, $start - $offset)."''";
            $offset = $start + 1;
            $closed = false;

            while ($offset < $length) {
                $offset += strcspn($line, "\\'", $offset);
                if ($offset >= $length) {
                    return null;
                }

                if ($line[$offset] === '\\') {
                    $offset += 2;

                    continue;
                }

                $offset++;
                $closed = true;
                break;
            }

            if (! $closed) {
                return null;
            }
        }

        return $outside.substr($line, $offset);
    }
}
