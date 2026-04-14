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
    public function upload(array $files): array
    {
        $urls = [];

        foreach ($files as $file) {
            try {
                $path = $file->store('images', 'supabase');
                $urls[] = Storage::disk('supabase')->url($path);
            } catch (UnableToWriteFile $e) {
                Log::error('Supabase upload failed: '.$e->getPrevious()->getMessage());
                throw $e;
            }
        }

        return $urls;
    }
}
