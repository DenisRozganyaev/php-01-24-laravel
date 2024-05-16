<?php

namespace Tests\Unit\Services;

use App\Services\Contract\FileServiceContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    protected FileServiceContract $service;

    protected string $file = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(FileServiceContract::class);
    }

    protected function tearDown(): void
    {
        if ($this->file !== '' && Storage::has($this->file)) {
            Storage::delete($this->file);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (Storage::has('test')) {
            Storage::deleteDirectory('test');
        }
    }

    public function test_file_upload()
    {
        $this->file = $this->uploadedFile();

        $this->assertTrue(Storage::has($this->file));
        $this->assertEquals(Storage::getVisibility($this->file), 'public');
    }

    public function test_file_upload_with_additional_path()
    {
        $this->file = $this->uploadedFile(additionalPath: 'test');

        $this->assertTrue(Storage::has($this->file));
        $this->assertStringContainsString('test', $this->file);
        $this->assertEquals(Storage::getVisibility($this->file), 'public');
    }

    public function test_remove_file()
    {
        $filePath = $this->uploadedFile(additionalPath: 'test');

        $this->assertTrue(Storage::has($filePath));

        $this->service->remove($filePath);

        $this->assertFalse(Storage::has($filePath));
    }

    protected function uploadedFile($fileName = 'image.png', $additionalPath = ''): string
    {
        $file = UploadedFile::fake()->image($fileName);

        return $this->service->upload($file, $additionalPath);
    }
}
