<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;

trait FileManager
{
    /**
     * Upload files to the specified disk.
     */
    public function upload(array $files, string $path): array
    {
        $urls = [];
        foreach ($files as $file) {
            try {
                $path = $file->store($path, 'supabase');
                $urls[] = Storage::disk('supabase')->url($path);
            } catch (UnableToWriteFile $exception) {
                Log::error('Supabase upload failed: '.$exception->getMessage());
                throw $exception;
            }
        }

        return $urls;
    }
}
