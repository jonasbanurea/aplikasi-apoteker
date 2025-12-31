<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use ZipArchive;
use RuntimeException;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner']);
    }

    public function index()
    {
        return view('backup.index', [
            'defaultPath' => $this->defaultBackupPath(),
        ]);
    }

    public function store(Request $request)
    {
        $timestamp = now()->format('Ymd_His');
        $baseTmp = storage_path('app/backup_tmp');
        $tmpDir = $baseTmp . DIRECTORY_SEPARATOR . $timestamp;
        $this->resetTempDir($baseTmp);
        File::makeDirectory($tmpDir, 0755, true);

        $dumpFile = $tmpDir . DIRECTORY_SEPARATOR . 'database.sql';
        $backupDir = $request->input('target_dir') ?: $this->defaultBackupPath();
        $backupDir = rtrim($backupDir, "\\/");
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        $zipPath = $backupDir . DIRECTORY_SEPARATOR . 'ro-tua-backup-' . $timestamp . '.zip';

        try {
            $this->dumpDatabase($dumpFile);
            $this->createZip($zipPath, $dumpFile);
        } catch (RuntimeException $e) {
            File::deleteDirectory($baseTmp);
            return back()->with('error', 'Backup gagal: ' . $e->getMessage());
        }

        File::deleteDirectory($baseTmp);

        return back()->with('success', 'Backup tersimpan di: ' . $zipPath);
    }

    protected function defaultBackupPath(): string
    {
        // Windows: gunakan folder Documents user. Fallback ke storage/app/backups.
        $userProfile = env('USERPROFILE');
        if ($userProfile) {
            $documents = $userProfile . DIRECTORY_SEPARATOR . 'Documents';
            return $documents . DIRECTORY_SEPARATOR . 'toko-obat-ro-tua-backup';
        }

        return storage_path('app/backups');
    }

    protected function resetTempDir(string $baseTmp): void
    {
        if (File::exists($baseTmp)) {
            File::deleteDirectory($baseTmp);
        }
    }

    protected function dumpDatabase(string $dumpFile): void
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");
        if (!$config || ($config['driver'] ?? '') !== 'mysql') {
            throw new RuntimeException('Konfigurasi database MySQL tidak ditemukan.');
        }

        $mysqldump = $this->resolveMysqlDump();
        $command = sprintf(
            '"%s" --user=%s --password=%s --host=%s --port=%s --routines --single-transaction --quick %s > "%s"',
            $mysqldump,
            escapeshellarg($config['username']),
            escapeshellarg($config['password'] ?? ''),
            escapeshellarg($config['host'] ?? '127.0.0.1'),
            escapeshellarg($config['port'] ?? '3306'),
            escapeshellarg($config['database']),
            $dumpFile
        );

        $process = Process::fromShellCommandline($command, base_path());
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Gagal dump database. Pastikan mysqldump tersedia di PATH atau set MYSQLDUMP_PATH. Pesan: ' . $process->getErrorOutput());
        }

        if (!File::exists($dumpFile)) {
            throw new RuntimeException('File dump tidak ditemukan.');
        }
    }

    protected function createZip(string $zipPath, string $dumpFile): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Tidak dapat membuat file zip.');
        }

        $zip->addFile($dumpFile, 'database.sql');

        // Lampirkan asset: storage/app/public dan public (frontend build/upload)
        $this->addPathToZip($zip, storage_path('app/public'), 'storage_public');
        $this->addPathToZip($zip, public_path(), 'public');

        $zip->close();
    }

    protected function addPathToZip(ZipArchive $zip, string $path, string $prefix): void
    {
        if (!File::exists($path)) {
            return;
        }

        $path = rtrim($path, DIRECTORY_SEPARATOR);
        $files = File::allFiles($path);
        foreach ($files as $file) {
            $relative = ltrim(str_replace($path, '', $file->getPathname()), DIRECTORY_SEPARATOR);
            $zip->addFile($file->getPathname(), $prefix . '/' . $relative);
        }
    }

    protected function resolveMysqlDump(): string
    {
        $envPath = env('MYSQLDUMP_PATH');
        if ($envPath && File::exists($envPath)) {
            return $envPath;
        }

        // Common XAMPP path on Windows
        $common = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        if (PHP_OS_FAMILY === 'Windows' && File::exists($common)) {
            return $common;
        }

        // Fallback to system PATH
        return 'mysqldump';
    }
}
