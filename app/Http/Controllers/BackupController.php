<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
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
        abort_unless(config('database.default') === 'mysql', 500, 'Backup requires MySQL.');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $zipDir = storage_path('app/backups');
        File::ensureDirectoryExists($zipDir);
        $zipPath = $zipDir.DIRECTORY_SEPARATOR."backup_{$timestamp}.zip";
        $sql = $this->runMySqlDump();

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            abort(500, 'Unable to create zip archive.');
        }

        $zip->addFromString('database.sql', $sql);

        $uploadsDir = public_path('uploads');
        if (is_dir($uploadsDir)) {
            $this->addFolderToZip($zip, $uploadsDir, 'uploads');
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function restore(Request $request): RedirectResponse
    {
        abort_unless(config('database.default') === 'mysql', 500, 'Restore requires MySQL.');

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

        $zip->extractTo($tmpDir);
        $zip->close();

        $sqlPath = $tmpDir.DIRECTORY_SEPARATOR.'database.sql';
        if (! is_file($sqlPath)) {
            return back()->withErrors(['backup' => 'database.sql not found in zip.']);
        }

        $uploadsSrc = $tmpDir.DIRECTORY_SEPARATOR.'uploads';

        try {
            Artisan::call('down');

            $this->wipeDatabase();
            $this->importSql($sqlPath);

            $uploadsDest = public_path('uploads');
            if (is_dir($uploadsDest)) {
                File::deleteDirectory($uploadsDest);
            }
            if (is_dir($uploadsSrc)) {
                File::ensureDirectoryExists($uploadsDest);
                File::copyDirectory($uploadsSrc, $uploadsDest);
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
}
