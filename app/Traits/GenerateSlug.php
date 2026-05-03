<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GenerateSlug
{
    public function slug(string $name, $model): string
    {
        do {
            $slug = Str::slug($name).'-'.Str::random(8);
        } while (
            $model::query()->where('slug', $slug)->exists()
        );

        return $slug;
    }
}
