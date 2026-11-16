<?php

namespace Tests\Unit;

use App\Services\ExportTemplateStorage;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExportTemplateStorageTest extends TestCase
{
    public function test_local_template_disk_returns_the_template_path(): void
    {
        Storage::fake('template-local-test');
        config([
            'filesystems.export_templates_disk' => 'template-local-test',
            'filesystems.export_templates_prefix' => 'templates',
            'filesystems.disks.template-local-test.driver' => 'local',
        ]);
        Storage::disk('template-local-test')->put('templates/PBT_Template.xlsx', 'template-bytes');

        $path = app(ExportTemplateStorage::class)->localPath('PBT_Template.xlsx');

        $this->assertIsString($path);
        $this->assertSame('template-bytes', file_get_contents($path));
    }

    public function test_remote_template_disk_is_copied_to_a_temporary_local_file(): void
    {
        Storage::fake('template-remote-test');
        config([
            'filesystems.export_templates_disk' => 'template-remote-test',
            'filesystems.export_templates_prefix' => 'exports',
            'filesystems.disks.template-remote-test.driver' => 's3',
        ]);
        Storage::disk('template-remote-test')->put('exports/MIS_Template.docx', 'remote-template-bytes');

        $path = app(ExportTemplateStorage::class)->localPath('MIS_Template.docx');

        $this->assertIsString($path);
        $this->assertSame('remote-template-bytes', file_get_contents($path));
        @unlink($path);
    }

    public function test_missing_template_returns_null(): void
    {
        Storage::fake('template-missing-test');
        config([
            'filesystems.export_templates_disk' => 'template-missing-test',
            'filesystems.export_templates_prefix' => '',
            'filesystems.disks.template-missing-test.driver' => 'local',
        ]);

        $this->assertNull(app(ExportTemplateStorage::class)->localPath('not-present.xlsx'));
    }
}
