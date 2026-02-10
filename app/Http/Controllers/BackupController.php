<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use ZipArchive;

class BackupController extends Controller
{
    public function index(): Response
    {
        return response()->view('pages.settings.backup');
    }

    public function create(): Response
    {
        $driver = (string) config('database.default');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $zipDir = storage_path('app/backups');
        File::ensureDirectoryExists($zipDir);
        $zipPath = $zipDir.DIRECTORY_SEPARATOR."backup_{$timestamp}.zip";

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            abort(500, 'Unable to create zip archive.');
        }

        $zip->addFromString('manifest.json', json_encode([
            'app' => config('app.name'),
            'created_at' => now()->toISOString(),
            'db_driver' => $driver,
            'php' => PHP_VERSION,
            'laravel' => app()->version(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}');

        if ($driver === 'mysql') {
            $zip->addFromString('database.sql', $this->runMySqlDump());
        } elseif ($driver === 'sqlite') {
            $this->addSqliteDatabaseToZip($zip);
        } else {
            $zip->close();
            abort(500, "Backup is not supported for database driver [{$driver}].");
        }

        $uploadsDir = public_path('uploads');
        if (is_dir($uploadsDir)) {
            $this->addFolderToZip($zip, $uploadsDir, 'uploads');
        }

        $settingsPath = storage_path('app/myacademy/settings.json');
        if (is_file($settingsPath)) {
            $zip->addFile($settingsPath, 'settings.json');
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function restore(Request $request): RedirectResponse
    {
        $driver = (string) config('database.default');

        $request->validate([
            'backup' => ['required', 'file', 'mimetypes:application/zip,application/x-zip-compressed'],
        ]);

        $tmpDir = storage_path('app/_restore_tmp/'.Str::random(10));
        File::ensureDirectoryExists($tmpDir);

        $zipPath = $request->file('backup')->storeAs('_restore_tmp/'.basename($tmpDir), 'backup.zip');
        $zipPath = storage_path('app/'.$zipPath);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return back()->withErrors(['backup' => 'Invalid zip file.']);
        }

        $this->safeExtractZip($zip, $tmpDir);
        $zip->close();

        $backupDriver = $this->detectBackupDriver($tmpDir);
        if ($backupDriver === null) {
            return back()->withErrors(['backup' => 'Unable to detect backup database type.']);
        }
        if ($backupDriver !== $driver) {
            return back()->withErrors(['backup' => "Backup is for [{$backupDriver}] but this app is configured for [{$driver}]."]);
        }

        $uploadsSrc = $tmpDir.DIRECTORY_SEPARATOR.'uploads';

        try {
            Artisan::call('down');

            if ($driver === 'mysql') {
                $sqlPath = $tmpDir.DIRECTORY_SEPARATOR.'database.sql';
                if (! is_file($sqlPath)) {
                    return back()->withErrors(['backup' => 'database.sql not found in zip.']);
                }

                $this->wipeDatabase();
                $this->importSql($sqlPath);
            } elseif ($driver === 'sqlite') {
                $sqliteSrc = $tmpDir.DIRECTORY_SEPARATOR.'database.sqlite';
                if (! is_file($sqliteSrc)) {
                    return back()->withErrors(['backup' => 'database.sqlite not found in zip.']);
                }

                $this->restoreSqliteDatabase($sqliteSrc);
            } else {
                abort(500, "Restore is not supported for database driver [{$driver}].");
            }

            $uploadsDest = public_path('uploads');
            if (is_dir($uploadsDest)) {
                File::deleteDirectory($uploadsDest);
            }
            if (is_dir($uploadsSrc)) {
                File::ensureDirectoryExists($uploadsDest);
                File::copyDirectory($uploadsSrc, $uploadsDest);
            }

            $settingsSrc = $tmpDir.DIRECTORY_SEPARATOR.'settings.json';
            if (! is_file($settingsSrc)) {
                $settingsSrc = $tmpDir.DIRECTORY_SEPARATOR.'myacademy'.DIRECTORY_SEPARATOR.'settings.json';
            }

            if (is_file($settingsSrc)) {
                $settingsDest = storage_path('app/myacademy/settings.json');
                File::ensureDirectoryExists(dirname($settingsDest));
                File::copy($settingsSrc, $settingsDest);
            }
        } finally {
            Artisan::call('up');

            if (is_dir($tmpDir)) {
                File::deleteDirectory($tmpDir);
            }
        }

        return redirect()
            ->route('settings.backup')
            ->with('status', 'Backup restored successfully.');
    }

    private function runMySqlDump(): string
    {
        $dumpBinary = env('MYACADEMY_MYSQLDUMP', 'mysqldump');
        $db = config('database.connections.mysql.database');

        $process = new Process([
            $dumpBinary,
            '--host='.config('database.connections.mysql.host'),
            '--port='.(string) config('database.connections.mysql.port'),
            '--user='.config('database.connections.mysql.username'),
            '--single-transaction',
            '--routines',
            '--events',
            '--add-drop-table',
            '--no-tablespaces',
            $db,
        ]);

        $password = (string) config('database.connections.mysql.password');
        if ($password !== '') {
            $process->setEnv(array_merge($this->processEnv(), ['MYSQL_PWD' => $password]));
        }

        $process->run();

        if (! $process->isSuccessful()) {
            abort(500, 'mysqldump failed: '.$process->getErrorOutput());
        }

        return $process->getOutput();
    }

    private function wipeDatabase(): void
    {
        $mysqlBinary = env('MYACADEMY_MYSQL', 'mysql');
        $db = config('database.connections.mysql.database');

        $process = new Process([
            $mysqlBinary,
            '--host='.config('database.connections.mysql.host'),
            '--port='.(string) config('database.connections.mysql.port'),
            '--user='.config('database.connections.mysql.username'),
            '--protocol=tcp',
            '--execute=DROP DATABASE IF EXISTS `'.$db.'`; CREATE DATABASE `'.$db.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;',
        ]);

        $password = (string) config('database.connections.mysql.password');
        if ($password !== '') {
            $process->setEnv(array_merge($this->processEnv(), ['MYSQL_PWD' => $password]));
        }

        $process->run();

        if (! $process->isSuccessful()) {
            abort(500, 'Database wipe failed: '.$process->getErrorOutput());
        }
    }

    private function importSql(string $sqlPath): void
    {
        $mysqlBinary = env('MYACADEMY_MYSQL', 'mysql');
        $db = config('database.connections.mysql.database');

        $process = new Process([
            $mysqlBinary,
            '--host='.config('database.connections.mysql.host'),
            '--port='.(string) config('database.connections.mysql.port'),
            '--user='.config('database.connections.mysql.username'),
            '--protocol=tcp',
            $db,
        ]);

        $password = (string) config('database.connections.mysql.password');
        if ($password !== '') {
            $process->setEnv(array_merge($this->processEnv(), ['MYSQL_PWD' => $password]));
        }

        $process->setInput(file_get_contents($sqlPath));
        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            abort(500, 'SQL import failed: '.$process->getErrorOutput());
        }
    }

    private function addFolderToZip(ZipArchive $zip, string $folder, string $zipPrefix): void
    {
        $folder = rtrim($folder, DIRECTORY_SEPARATOR);
        $files = File::allFiles($folder);

        foreach ($files as $file) {
            $relative = ltrim(str_replace($folder, '', $file->getPathname()), DIRECTORY_SEPARATOR);
            $zip->addFile($file->getPathname(), $zipPrefix.'/'.str_replace(DIRECTORY_SEPARATOR, '/', $relative));
        }
    }

    private function processEnv(): array
    {
        return array_merge($_SERVER, $_ENV);
    }

    private function addSqliteDatabaseToZip(ZipArchive $zip): void
    {
        $dbPath = $this->resolveSqliteDatabasePath();

        if (! is_file($dbPath)) {
            abort(500, 'SQLite database file not found: '.$dbPath);
        }

        DB::disconnect();
        DB::purge();

        $zip->addFromString('database.sqlite', (string) file_get_contents($dbPath));

        $walPath = $dbPath.'-wal';
        if (is_file($walPath)) {
            $zip->addFromString('database.sqlite-wal', (string) file_get_contents($walPath));
        }

        $shmPath = $dbPath.'-shm';
        if (is_file($shmPath)) {
            $zip->addFromString('database.sqlite-shm', (string) file_get_contents($shmPath));
        }

        DB::reconnect();
    }

    private function resolveSqliteDatabasePath(): string
    {
        $database = (string) config('database.connections.sqlite.database');

        if ($database === '' || $database === ':memory:') {
            abort(500, 'SQLite database must be a file (not :memory:) to backup/restore.');
        }

        if ($this->isAbsolutePath($database)) {
            return $database;
        }

        return base_path($database);
    }

    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        if (Str::startsWith($path, ['/', '\\'])) {
            return true;
        }

        return (bool) preg_match('/^[a-zA-Z]:[\/\\\\]/', $path);
    }

    private function restoreSqliteDatabase(string $sqliteSrc): void
    {
        $dest = $this->resolveSqliteDatabasePath();

        DB::disconnect();
        DB::purge();

        File::ensureDirectoryExists(dirname($dest));

        if (is_file($dest)) {
            File::delete($dest);
        }

        File::copy($sqliteSrc, $dest);
        clearstatcache(true, $dest);

        if (is_file($dest.'-wal')) {
            File::delete($dest.'-wal');
        }
        if (is_file($dest.'-shm')) {
            File::delete($dest.'-shm');
        }

        $walSrc = dirname($sqliteSrc).DIRECTORY_SEPARATOR.'database.sqlite-wal';
        if (is_file($walSrc)) {
            File::copy($walSrc, $dest.'-wal');
        }

        $shmSrc = dirname($sqliteSrc).DIRECTORY_SEPARATOR.'database.sqlite-shm';
        if (is_file($shmSrc)) {
            File::copy($shmSrc, $dest.'-shm');
        }

        DB::reconnect();
    }

    private function detectBackupDriver(string $tmpDir): ?string
    {
        $manifestPath = $tmpDir.DIRECTORY_SEPARATOR.'manifest.json';
        if (is_file($manifestPath)) {
            $manifest = json_decode((string) file_get_contents($manifestPath), true);
            if (is_array($manifest) && isset($manifest['db_driver']) && is_string($manifest['db_driver'])) {
                return $manifest['db_driver'];
            }
        }

        if (is_file($tmpDir.DIRECTORY_SEPARATOR.'database.sqlite')) {
            return 'sqlite';
        }

        if (is_file($tmpDir.DIRECTORY_SEPARATOR.'database.sql')) {
            return 'mysql';
        }

        return null;
    }

    private function safeExtractZip(ZipArchive $zip, string $tmpDir): void
    {
        $allowedPrefixes = [
            'uploads/',
            'uploads\\',
            'myacademy/',
            'myacademy\\',
        ];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            if ($name === '') {
                continue;
            }

            $normalized = str_replace('\\', '/', $name);
            if (str_contains($normalized, '../') || Str::startsWith($normalized, ['/'])) {
                continue;
            }

            if (
                $normalized === 'database.sql'
                || $normalized === 'database.sqlite'
                || $normalized === 'database.sqlite-wal'
                || $normalized === 'database.sqlite-shm'
                || $normalized === 'manifest.json'
                || $normalized === 'settings.json'
            ) {
                $zip->extractTo($tmpDir, [$name]);
                continue;
            }

            foreach ($allowedPrefixes as $prefix) {
                if (Str::startsWith($name, $prefix)) {
                    $zip->extractTo($tmpDir, [$name]);
                    break;
                }
            }
        }
    }
}
