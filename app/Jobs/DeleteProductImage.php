<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteProductImage implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $images) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->images as $image) {
            try {
                $path = str_replace(
                    'https://qxtagomijcgmgxfzpnmg.storage.supabase.co/storage/v1/s3/ProductsAssets/',
                    '',
                    $image
                );
                if (Storage::disk('supabase')->exists($path)) {
                    Storage::disk('supabase')->delete($path);
                }
            } catch (\Exception $exception) {
                Log::error("Failed to delete image from Supabase: {$path}. Error: ".$exception->getMessage());
            }
        }
    }
}
