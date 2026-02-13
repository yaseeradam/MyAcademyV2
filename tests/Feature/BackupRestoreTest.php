<?php

namespace Tests\Feature;

use App\Http\Controllers\BackupController;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use ZipArchive;

class BackupRestoreTest extends TestCase
{
    private function useFileSqliteDatabase(): string
    {
        $dbPath = storage_path('app/_test_backup/database.sqlite');
        File::ensureDirectoryExists(dirname($dbPath));

        if (is_file($dbPath)) {
            File::delete($dbPath);
        }

        File::put($dbPath, '');

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => $dbPath,
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Artisan::call('migrate', ['--force' => true]);

        return $dbPath;
    }

    public function test_backup_create_generates_zip_for_sqlite(): void
    {
        $this->useFileSqliteDatabase();

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('settings.backup.create'));
        $response->assertOk();

        /** @var \Symfony\Component\HttpFoundation\BinaryFileResponse $response */
        $zipPath = $response->getFile()->getPathname();
        $this->assertTrue(is_file($zipPath));

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipPath) === true);

        $this->assertNotFalse($zip->locateName('manifest.json'));
        $this->assertNotFalse($zip->locateName('database.sqlite'));

        $zip->close();
    }

    public function test_restore_accepts_octet_stream_zip_upload(): void
    {
        $dbPath = $this->useFileSqliteDatabase();

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // Create a backup zip with required files
        $tmpZip = storage_path('app/_test_backup/restore.zip');
        if (is_file($tmpZip)) {
            File::delete($tmpZip);
        }

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($tmpZip, ZipArchive::CREATE) === true);
        $zip->addFromString('manifest.json', json_encode(['db_driver' => 'sqlite'], JSON_UNESCAPED_SLASHES) ?: '{}');
        $zip->addFromString('database.sqlite', (string) file_get_contents($dbPath));
        $zip->close();

        $upload = new UploadedFile(
            $tmpZip,
            'backup.zip',
            'application/octet-stream',
            null,
            true
        );

        $response = $this->actingAs($admin)->post(route('settings.restore'), [
            'backup' => $upload,
        ]);

        $response->assertRedirect(route('settings.backup'));
    }
}

