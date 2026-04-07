<?php

namespace App\Traits;

trait HandlesImageUpload
{
    /**
     * Create a new class instance.
     */
    public function upload(array $images): array
    {
        $files = [];

        foreach ($images as $image) {
            $path = $image->store('products', 'public');
            $files[] = $path;
        }

        return $files;
    }
}
