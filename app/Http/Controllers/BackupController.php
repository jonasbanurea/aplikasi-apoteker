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

        // Note: Tidak perlu cek koneksi dulu karena mysqldump akan memberikan error yang jelas
        // Cek koneksi bisa false positive jika Laravel connection pool bermasalah

        $mysqldump = $this->resolveMysqlDump();
        
        // Build command with proper escaping and protocol handling
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';
        $username = $config['username'];
        $password = $config['password'] ?? '';
        $database = $config['database'];
        
        // CRITICAL FIX: Force 127.0.0.1 BEFORE building command
        if (strtolower($host) === 'localhost') {
            $host = '127.0.0.1';
        }
        
        // Log actual connection params
        \Log::info("Backup params - Host: {$host}, Port: {$port}, User: {$username}, DB: {$database}");
        
        // WINDOWS FIX: Jangan gunakan --protocol=tcp karena menyebabkan error 10106
        // Gunakan --result-file lebih reliable daripada output redirect >
        
        if (empty($password)) {
            // No password
            $command = sprintf(
                '"%s" -h %s -P %s -u %s --routines --single-transaction --quick --no-tablespaces --result-file="%s" %s',
                $mysqldump,
                $host,
                $port,
                $username,
                $dumpFile,
                $database
            );
        } else {
            // With password
            $command = sprintf(
                '"%s" -h %s -P %s -u %s --password="%s" --routines --single-transaction --quick --no-tablespaces --result-file="%s" %s',
                $mysqldump,
                $host,
                $port,
                $username,
                $password,
                $dumpFile,
                $database
            );
        }
        
        // Log command untuk debugging (hide password)
        $debugCommand = !empty($password) ? str_replace("--password=\"{$password}\"", "--password=\"***\"", $command) : $command;
        \Log::info("Executing mysqldump command: {$debugCommand}");

        $process = Process::fromShellCommandline($command, base_path());
        $process->setTimeout(180);
        $process->run();

        // If failed, try with minimal flags
        if (!$process->isSuccessful() || !File::exists($dumpFile) || File::size($dumpFile) < 1024) {
            $errorOutput = $process->getErrorOutput();
            \Log::error("Mysqldump attempt 1 failed or file too small: {$errorOutput}");
            
            // Delete failed file if exists
            if (File::exists($dumpFile)) {
                File::delete($dumpFile);
            }
            
            // FALLBACK: Minimal command - no host/port (use default connection)
            if (empty($password)) {
                $command = sprintf(
                    '"%s" -u %s --routines --single-transaction --quick --result-file="%s" %s',
                    $mysqldump,
                    $username,
                    $dumpFile,
                    $database
                );
            } else {
                $command = sprintf(
                    '"%s" -u %s --password="%s" --routines --single-transaction --quick --result-file="%s" %s',
                    $mysqldump,
                    $username,
                    $password,
                    $dumpFile,
                    $database
                );
            }
            
            \Log::warning("Trying minimal command without host/port flags");
            
            $process = Process::fromShellCommandline($command, base_path());
            $process->setTimeout(180);
            $process->run();
            
            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();
                \Log::error("Mysqldump attempt 2 also failed: {$errorOutput}");
            }
        }

        // CRITICAL CHECK: Verify backup file was created and has content
        if (!File::exists($dumpFile)) {
            $errorOutput = $process->getErrorOutput();
            $stdOutput = $process->getOutput();
            
            \Log::error("Backup file not created! Error: {$errorOutput}, Output: {$stdOutput}");
            
            throw new RuntimeException(
                "❌ File backup tidak terbuat!\n\n" .
                "Mysqldump dijalankan tapi file backup tidak dibuat.\n\n" .
                "Error Output: " . ($errorOutput ?: 'Tidak ada error') . "\n" .
                "Std Output: " . ($stdOutput ?: 'Tidak ada output') . "\n\n" .
                "Kemungkinan penyebab:\n" .
                "1. Permission write ke folder storage\n" .
                "2. Disk space penuh\n" .
                "3. Path mysqldump salah\n\n" .
                "Path file: {$dumpFile}\n" .
                "Mysqldump: {$mysqldump}\n\n" .
                "Solusi:\n" .
                "1. Cek folder storage/app/backups/ bisa ditulis\n" .
                "2. Coba backup manual via phpMyAdmin\n"
            );
        }
        
        // Check if file has content (> 1KB)
        $fileSize = File::size($dumpFile);
        if ($fileSize < 1024) {
            $errorOutput = $process->getErrorOutput();
            $fileContent = File::get($dumpFile);
            
            \Log::error("Backup file too small ({$fileSize} bytes). Content: {$fileContent}");
            
            throw new RuntimeException(
                "❌ File backup terlalu kecil (kemungkinan kosong atau error)!\n\n" .
                "File size: {$fileSize} bytes\n" .
                "Error: " . ($errorOutput ?: 'Tidak ada error') . "\n\n" .
                "Content preview:\n{$fileContent}\n\n" .
                "Kemungkinan penyebab:\n" .
                "1. Database kosong (tidak ada tabel)\n" .
                "2. Mysqldump error tapi tidak tertangkap\n" .
                "3. Permission error saat read database\n\n" .
                "Solusi:\n" .
                "1. Cek apakah database {$database} ada isi datanya\n" .
                "2. Coba backup manual via phpMyAdmin\n"
            );
        }
        
        \Log::info("Backup file created successfully: {$dumpFile} ({$fileSize} bytes)");

        // Previous error handling for process failures
        if (!$process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput();
            
            // Deteksi DNS resolution error
            if (strpos($errorOutput, 'Unknown MySQL server host') !== false ||
                strpos($errorOutput, '11003') !== false ||
                strpos($errorOutput, '2005') !== false) {
                throw new RuntimeException(
                    "❌ Backup Database GAGAL - Koneksi MySQL Error!\n\n" .
                    "Error: {$errorOutput}\n\n" .
                    "DEBUG INFO:\n" .
                    "- DB_HOST di config: {$host}\n" .
                    "- DB_PORT: {$port}\n" .
                    "- Database: {$database}\n\n" .
                    "SOLUSI:\n" .
                    "1. Cek file .env pastikan DB_HOST=127.0.0.1 (bukan localhost)\n" .
                    "2. Jalankan di terminal: php artisan config:clear\n" .
                    "3. Restart aplikasi (stop dan start lagi)\n" .
                    "4. Coba backup lagi\n\n" .
                    "ALTERNATIF - Backup Manual:\n" .
                    "1. Buka: http://localhost/phpmyadmin\n" .
                    "2. Pilih database → Export → Quick → SQL → Go\n" .
                    "3. Save file yang didownload\n\n" .
                    "Error detail: " . trim($errorOutput)
                );
            }
            
            // Berikan pesan error yang lebih spesifik berdasarkan tipe error
            if (strpos($errorOutput, "Can't create TCP/IP socket") !== false || 
                strpos($errorOutput, '10106') !== false ||
                strpos($errorOutput, 'Connection refused') !== false ||
                strpos($errorOutput, 'SQLSTATE[HY000] [2002]') !== false) {
                throw new RuntimeException(
                    "❌ Mysqldump tidak bisa terkoneksi ke MySQL!\n\n" .
                    "Masalah ini bisa terjadi karena:\n" .
                    "1. MySQL baru saja start dan belum fully ready\n" .
                    "2. MySQL tidak listen di TCP/IP port (hanya named pipe)\n" .
                    "3. Firewall/antivirus memblock koneksi\n\n" .
                    "Solusi:\n" .
                    "1. Tunggu 30-60 detik setelah MySQL start\n" .
                    "2. Test buka http://localhost/phpmyadmin\n" .
                    "   - Jika phpMyAdmin BISA dibuka = gunakan backup manual\n" .
                    "   - Jika phpMyAdmin TIDAK bisa = MySQL belum ready\n" .
                    "3. Restart MySQL di XAMPP/Laragon\n" .
                    "4. Coba backup manual via phpMyAdmin (lebih reliable):\n" .
                    "   • Buka phpMyAdmin\n" .
                    "   • Pilih database\n" .
                    "   • Tab Export → Quick → SQL → Go\n\n" .
                    "💡 WORKAROUND: Untuk sementara, gunakan backup manual via phpMyAdmin.\n" .
                    "Ini lebih reliable untuk environment dengan masalah TCP/IP.\n\n" .
                    "Error detail: " . trim($errorOutput)
                );
            }
            
            if (strpos($errorOutput, 'Access denied') !== false ||
                strpos($errorOutput, '1045') !== false) {
                throw new RuntimeException(
                    "❌ Password MySQL salah!\n\n" .
                    "Solusi:\n" .
                    "1. Cek file .env → DB_USERNAME dan DB_PASSWORD\n" .
                    "2. Pastikan sesuai dengan password MySQL Anda\n" .
                    "3. Setelah ubah .env, jalankan: php artisan config:clear\n" .
                    "4. Coba backup lagi\n\n" .
                    "Error detail: " . trim($errorOutput)
                );
            }
            
            if (strpos($errorOutput, 'Unknown database') !== false ||
                strpos($errorOutput, '1049') !== false) {
                throw new RuntimeException(
                    "❌ Database tidak ditemukan!\n\n" .
                    "Database: " . ($config['database'] ?? 'N/A') . "\n\n" .
                    "Solusi:\n" .
                    "1. Buka phpMyAdmin: http://localhost/phpmyadmin\n" .
                    "2. Cek apakah database '" . ($config['database'] ?? '') . "' ada\n" .
                    "3. Jika tidak ada, buat database baru dengan nama tersebut\n" .
                    "4. Atau ubah DB_DATABASE di file .env sesuai nama database yang ada\n\n" .
                    "Error detail: " . trim($errorOutput)
                );
            }
            
            // Generic error
            throw new RuntimeException(
                "Gagal backup database.\n\n" .
                "Error: " . trim($errorOutput) . "\n\n" .
                "Troubleshooting:\n" .
                "1. Pastikan mysqldump tersedia (cek di XAMPP/Laragon)\n" .
                "2. Pastikan MySQL service running\n" .
                "3. Cek koneksi database di .env\n" .
                "4. Lihat panduan lengkap: TROUBLESHOOTING_BACKUP.md"
            );
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

        // Common paths on Windows - Extended list
        $commonPaths = [
            // XAMPP - berbagai drive dan lokasi
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'D:\\xampp\\mysql\\bin\\mysqldump.exe',
            'E:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\XAMPP\\mysql\\bin\\mysqldump.exe',
            
            // Laragon - berbagai versi MySQL
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.27\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-5.7.33\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-5.7.24\\bin\\mysqldump.exe',
            
            // WAMP
            'C:\\wamp64\\bin\\mysql\\mysql8.0.27\\bin\\mysqldump.exe',
            'C:\\wamp\\bin\\mysql\\mysql8.0.27\\bin\\mysqldump.exe',
        ];

        if (PHP_OS_FAMILY === 'Windows') {
            foreach ($commonPaths as $path) {
                if (File::exists($path)) {
                    \Log::info("Found mysqldump at: $path");
                    return $path;
                }
            }
            
            // Try to find Laragon MySQL path dynamically
            $laragonBase = 'C:\\laragon\\bin\\mysql';
            if (File::exists($laragonBase)) {
                $mysqlDirs = File::directories($laragonBase);
                foreach ($mysqlDirs as $dir) {
                    $mysqldumpPath = $dir . '\\bin\\mysqldump.exe';
                    if (File::exists($mysqldumpPath)) {
                        \Log::info("Found mysqldump at (Laragon dynamic): $mysqldumpPath");
                        return $mysqldumpPath;
                    }
                }
            }
            
            // Try XAMPP di berbagai drive
            foreach (['C', 'D', 'E', 'F'] as $drive) {
                $xamppPath = "{$drive}:\\xampp\\mysql\\bin\\mysqldump.exe";
                if (File::exists($xamppPath)) {
                    \Log::info("Found mysqldump at (XAMPP scan): $xamppPath");
                    return $xamppPath;
                }
            }
        }

        // Fallback to system PATH (kemungkinan tidak akan berhasil kalau sampai sini)
        \Log::warning("Mysqldump not found in common paths, falling back to system PATH");
        return 'mysqldump';
    }
}
