<?php

namespace App\Http\Controllers;

use App\Exceptions\BackupOperationException;
use App\Exceptions\BackupPrerequisiteException;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\RuntimeException as ProcessRuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BackupController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();
        abort_unless($user?->hasPermission('backup.manage'), 403);

        return response()->view('pages.settings.backup');
    }

    public function create(): BinaryFileResponse|RedirectResponse
    {
        $user = request()->user();
        abort_unless($user?->hasPermission('backup.manage'), 403);

        set_time_limit(0);

        $driver = (string) config('database.default');
        $cleanupDirs = [];
        $broughtDown = false;

        $timestamp = now()->format('Y-m-d_H-i-s');
        $zipDir = storage_path('app/backups');
        File::ensureDirectoryExists($zipDir);
        $zipPath = $zipDir.DIRECTORY_SEPARATOR."backup_{$timestamp}.zip";

        try {
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
                throw new BackupOperationException('Unable to create zip archive.');
            }

            $zip->addFromString('manifest.json', json_encode([
                'app' => config('app.name'),
                'created_at' => now()->toISOString(),
                'db_driver' => $driver,
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}');

            try {
                if ($driver === 'mysql') {
                    $this->assertMySqlToolsAvailableForBackup();

                    if (! app()->isDownForMaintenance()) {
                        Artisan::call('down');
                        $broughtDown = true;
                    }

                    $sqlPath = $this->runMySqlDumpToFile();
                    $cleanupDirs[] = dirname($sqlPath);
                    $zip->addFile($sqlPath, 'database.sql');
                } elseif ($driver === 'sqlite') {
                    $this->addSqliteDatabaseToZip($zip);
                } else {
                    throw new BackupPrerequisiteException("Backup is not supported for database driver [{$driver}].");
                }

                $uploadsDir = public_path('uploads');
                if (is_dir($uploadsDir)) {
                    $this->addFolderToZip($zip, $uploadsDir, 'uploads');
                }

                $settingsPath = storage_path('app/myacademy/settings.json');
                if (is_file($settingsPath)) {
                    $zip->addFile($settingsPath, 'settings.json');
                }
            } finally {
                $zip->close();

                if ($broughtDown) {
                    Artisan::call('up');
                }

                foreach ($cleanupDirs as $dir) {
                    if (is_dir($dir)) {
                        File::deleteDirectory($dir);
                    }
                }
            }

            Audit::log('backup.created', null, [
                'db_driver' => $driver,
            ]);

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (BackupPrerequisiteException|BackupOperationException $e) {
            report($e);

            if (is_file($zipPath)) {
                File::delete($zipPath);
            }

            return back()->withErrors(['backup_create' => $e->getMessage()]);
        }
    }

    public function restore(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->hasPermission('backup.manage'), 403);

        set_time_limit(0);

        $driver = (string) config('database.default');
        $broughtDown = false;

        $request->validate([
            'backup' => ['required', 'file', 'mimes:zip'],
        ]);

        $tmpDir = storage_path('app/_restore_tmp/'.Str::random(10));
        File::ensureDirectoryExists($tmpDir);

        $zipPath = $request->file('backup')->storeAs('_restore_tmp/'.basename($tmpDir), 'backup.zip', 'local');
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
            try {
                if (! app()->isDownForMaintenance()) {
                    Artisan::call('down');
                    $broughtDown = true;
                }

                if ($driver === 'mysql') {
                    $this->assertMySqlToolsAvailableForRestore();

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
                    return back()->withErrors(['backup' => "Restore is not supported for database driver [{$driver}]."]);
                }

            $uploadsDest = public_path('uploads');
            if (is_dir($uploadsSrc)) {
                if (is_dir($uploadsDest)) {
                    File::deleteDirectory($uploadsDest);
                }
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
                if ($broughtDown) {
                    Artisan::call('up');
                }

                if (is_dir($tmpDir)) {
                    File::deleteDirectory($tmpDir);
                }
            }
        } catch (BackupPrerequisiteException|BackupOperationException $e) {
            report($e);
            return back()->withErrors(['backup' => $e->getMessage()]);
        }

        Audit::log('backup.restored', null, [
            'db_driver' => $driver,
        ]);

        return redirect()
            ->route('settings.backup')
            ->with('status', 'Backup restored successfully.');
    }

    private function assertMySqlToolsAvailableForBackup(): void
    {
        // No longer needed - using Laravel's database connection
    }

    private function assertMySqlToolsAvailableForRestore(): void
    {
        // No longer needed - using Laravel's database connection
    }

    private function runMySqlDumpToFile(): string
    {
        $db = (string) config('database.connections.mysql.database');
        $charset = (string) config('database.connections.mysql.charset', 'utf8mb4');
        $collation = (string) config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');

        $tmpDir = storage_path('app/backups/_tmp/'.Str::random(10));
        File::ensureDirectoryExists($tmpDir);
        $sqlPath = $tmpDir.DIRECTORY_SEPARATOR.'database.sql';

        $sql = "-- MySQL dump via Laravel\n";
        $sql .= "-- Database: {$db}\n";
        $sql .= "-- Date: ".now()->toDateTimeString()."\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sql .= "SET NAMES {$charset} COLLATE {$collation};\n\n";

        // Backup tables
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createStatement = array_values((array)$createTable[0])[1];
            $sql .= $createStatement . ";\n\n";
            
            $columns = DB::select("SHOW COLUMNS FROM `{$tableName}`");
            $columnNames = array_map(fn($col) => "`{$col->Field}`", $columns);
            $columnList = implode(', ', $columnNames);
            
            $rows = DB::table($tableName)->get();
            if ($rows->isNotEmpty()) {
                foreach ($rows->chunk(100) as $batch) {
                    $values = [];
                    foreach ($batch as $row) {
                        $rowValues = collect((array)$row)->map(function ($value) {
                            if ($value === null) return 'NULL';
                            return "'" . str_replace(["'", "\\"], ["\\'", "\\\\"], (string)$value) . "'";
                        })->implode(', ');
                        $values[] = "({$rowValues})";
                    }
                    
                    $sql .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES \n";
                    $sql .= implode(",\n", $values) . ";\n";
                }
                $sql .= "\n";
            }
        }

        // Backup views
        $views = DB::select("SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ?", [$db]);
        foreach ($views as $view) {
            $viewName = $view->TABLE_NAME;
            $sql .= "DROP VIEW IF EXISTS `{$viewName}`;\n";
            
            $createView = DB::select("SHOW CREATE VIEW `{$viewName}`");
            $createStatement = array_values((array)$createView[0])[1];
            $sql .= $createStatement . ";\n\n";
        }

        // Backup triggers
        $triggers = DB::select("SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = ?", [$db]);
        foreach ($triggers as $trigger) {
            $triggerName = $trigger->TRIGGER_NAME;
            $sql .= "DROP TRIGGER IF EXISTS `{$triggerName}`;\n";
            
            $createTrigger = DB::select("SHOW CREATE TRIGGER `{$triggerName}`");
            $createStatement = array_values((array)$createTrigger[0])[2];
            $sql .= $createStatement . ";\n\n";
        }

        // Backup procedures
        $procedures = DB::select("SELECT ROUTINE_NAME FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = ? AND ROUTINE_TYPE = 'PROCEDURE'", [$db]);
        foreach ($procedures as $procedure) {
            $procName = $procedure->ROUTINE_NAME;
            $sql .= "DROP PROCEDURE IF EXISTS `{$procName}`;\n";
            
            $createProc = DB::select("SHOW CREATE PROCEDURE `{$procName}`");
            $createStatement = array_values((array)$createProc[0])[2];
            $sql .= "DELIMITER ;;\n{$createStatement};;\nDELIMITER ;\n\n";
        }

        // Backup functions
        $functions = DB::select("SELECT ROUTINE_NAME FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = ? AND ROUTINE_TYPE = 'FUNCTION'", [$db]);
        foreach ($functions as $function) {
            $funcName = $function->ROUTINE_NAME;
            $sql .= "DROP FUNCTION IF EXISTS `{$funcName}`;\n";
            
            $createFunc = DB::select("SHOW CREATE FUNCTION `{$funcName}`");
            $createStatement = array_values((array)$createFunc[0])[2];
            $sql .= "DELIMITER ;;\n{$createStatement};;\nDELIMITER ;\n\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        file_put_contents($sqlPath, $sql);

        if (! is_file($sqlPath)) {
            throw new BackupOperationException('Database dump failed to create SQL file.');
        }

        return $sqlPath;
    }

    private function wipeDatabase(): void
    {
        Artisan::call('db:wipe', [
            '--drop-views' => true,
            '--force' => true,
        ]);
    }

    private function importSql(string $sqlPath): void
    {
        // Read SQL file
        $sql = file_get_contents($sqlPath);
        if ($sql === false) {
            throw new BackupOperationException('Unable to read database.sql for import.');
        }

        // Disable foreign key checks
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');

        // Remove comments and split into statements
        $lines = explode("\n", $sql);
        $statement = '';
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip comments and empty lines
            if (empty($line) || str_starts_with($line, '--') || str_starts_with($line, '/*')) {
                continue;
            }
            
            // Accumulate statement
            $statement .= $line . "\n";
            
            // Execute when we hit a semicolon at end of line
            if (str_ends_with($line, ';')) {
                $statement = trim($statement);
                
                if (!empty($statement)) {
                    try {
                        DB::unprepared($statement);
                    } catch (\Exception $e) {
                        // Log but continue for SET statements
                        if (!str_contains($statement, 'SET ')) {
                            report($e);
                        }
                    }
                }
                
                $statement = '';
            }
        }

        // Execute any remaining statement
        if (!empty(trim($statement))) {
            try {
                DB::unprepared(trim($statement));
            } catch (\Exception $e) {
                report($e);
            }
        }

        // Re-enable foreign key checks
        DB::unprepared('SET FOREIGN_KEY_CHECKS=1');
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

    private function mysqlClientEnv(string $password): array
    {
        if ($password === '') {
            return [];
        }

        return [
            'MYSQL_PWD' => $password,
        ];
    }

    private function resolveMySqlBinaryOrThrow(string $envKey, string $fallbackName): string
    {
        $resolved = $this->resolveMySqlBinaryOrNull($envKey, $fallbackName);
        if ($resolved === null) {
            $hint = $this->mysqlBinaryHint($envKey, $fallbackName.'.exe');
            throw new BackupPrerequisiteException("Unable to locate required MySQL tool [{$fallbackName}].{$hint}");
        }

        return $resolved;
    }

    private function resolveMySqlBinaryOrNull(string $envKey, string $fallbackName): ?string
    {
        $configured = trim((string) env($envKey, ''), "\"' \t\r\n");
        if ($configured !== '') {
            if (is_file($configured)) {
                return $configured;
            }

            $found = $this->findBinaryOnPath($configured);
            return $found ?? null;
        }

        $found = $this->findBinaryOnPath($fallbackName);
        if ($found !== null) {
            return $found;
        }

        foreach ($this->defaultWindowsMySqlBinaries($fallbackName) as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    private function mysqlBinaryHint(string $envKey, string $exampleExe): string
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return '';
        }

        return " (Tip: set {$envKey} in .env to full path, e.g. \"C:\\\\Program Files\\\\MySQL\\\\MySQL Server 8.0\\\\bin\\\\{$exampleExe}\")";
    }

    private function findBinaryOnPath(string $name): ?string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $process = new Process(['where', $name]);
            $process->setTimeout(5);
            $process->run();

            if (! $process->isSuccessful()) {
                return null;
            }

            $lines = preg_split('/\r\n|\r|\n/', trim($process->getOutput())) ?: [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line !== '' && is_file($line)) {
                    return $line;
                }
            }

            return null;
        }

        $process = new Process(['sh', '-lc', 'command -v '.escapeshellarg($name)]);
        $process->setTimeout(5);
        $process->run();

        if (! $process->isSuccessful()) {
            return null;
        }

        $path = trim($process->getOutput());
        return $path !== '' ? $path : null;
    }

    /**
     * @return array<int, string>
     */
    private function defaultWindowsMySqlBinaries(string $name): array
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return [];
        }

        $exe = str_ends_with(strtolower($name), '.exe') ? $name : ($name.'.exe');

        $patterns = [
            'C:\\Program Files\\MySQL\\MySQL Server*\\bin\\'.$exe,
            'C:\\Program Files (x86)\\MySQL\\MySQL Server*\\bin\\'.$exe,
            'C:\\Program Files\\MariaDB*\\bin\\'.$exe,
            'C:\\Program Files (x86)\\MariaDB*\\bin\\'.$exe,
            'C:\\xampp\\mysql\\bin\\'.$exe,
            'C:\\laragon\\bin\\mysql\\mysql*\\bin\\'.$exe,
            'C:\\wamp64\\bin\\mysql\\mysql*\\bin\\'.$exe,
            'C:\\wamp\\bin\\mysql\\mysql*\\bin\\'.$exe,
        ];

        $hits = [];
        foreach ($patterns as $pattern) {
            foreach (glob($pattern) ?: [] as $path) {
                $hits[] = $path;
            }
        }

        return $hits;
    }

    private function addSqliteDatabaseToZip(ZipArchive $zip): void
    {
        $dbPath = $this->resolveSqliteDatabasePath();

        if (! is_file($dbPath)) {
            abort(500, 'SQLite database file not found: '.$dbPath);
        }

        DB::disconnect();
        DB::purge();

        clearstatcache(true, $dbPath);
        $zip->addFile($dbPath, 'database.sqlite');

        $walPath = $dbPath.'-wal';
        if (is_file($walPath)) {
            clearstatcache(true, $walPath);
            $zip->addFile($walPath, 'database.sqlite-wal');
        }

        $shmPath = $dbPath.'-shm';
        if (is_file($shmPath)) {
            clearstatcache(true, $shmPath);
            $zip->addFile($shmPath, 'database.sqlite-shm');
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
