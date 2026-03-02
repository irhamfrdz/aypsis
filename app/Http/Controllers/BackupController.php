<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    /**
     * Download a mysqldump of the database.
     */
    public function download()
    {
        // Pastikan hanya user dengan username 'kiky' yang bisa akses
        if (auth()->user()->username !== 'kiky') {
            abort(403, 'Unauthorized action.');
        }

        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');

        $fileName = 'backup-' . $database . '-' . date('Y-m-d-H-i-s') . '.sql';
        $storagePath = storage_path('app/public/' . $fileName);

        // Path to mysqldump might differ depending on environment
        $mysqldumpPath = file_exists('C:\xampp\mysql\bin\mysqldump.exe') ? '"C:\xampp\mysql\bin\mysqldump.exe"' : 'mysqldump';
        
        if ($password) {
            putenv("MYSQL_PWD={$password}");
        }
        
        $command = "{$mysqldumpPath} --user=\"{$username}\" --host=\"{$host}\" --port=\"{$port}\" {$database} > \"{$storagePath}\"";

        exec($command, $output, $returnVar);

        // Reset environment variable for safety
        if ($password) {
            putenv("MYSQL_PWD=");
        }

        if ($returnVar === 0 && file_exists($storagePath)) {
            return Response::download($storagePath)->deleteFileAfterSend(true);
        }

        $errorMessage = 'Gagal membackup database.';
        if (!empty($output)) {
            $errorMessage .= ' ' . implode("\n", $output);
        }

        return back()->with('error', $errorMessage);
    }
}
