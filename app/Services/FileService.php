<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService implements Contract\FileServiceContract
{

    public function upload(string|UploadedFile $file, string $additionalPath = ''): string
    {
        $additionalPath = !empty($additionalPath) ? $additionalPath . '/' : '';

        $filePath = $additionalPath . Str::random() . '_' . time() . '.' . $file->getClientOriginalExtension();
        Storage::put($filePath, File::get($file));
        Storage::setVisibility($filePath, 'public');

        return $filePath;
    }

    public function remove(string $filePath): void
    {
        // TODO: Implement remove() method.
    }
}
