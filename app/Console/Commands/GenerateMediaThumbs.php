<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class GenerateMediaThumbs extends Command
{
    protected $signature = 'media:generate-thumbs {--force : Regenerate existing thumbs}';
    protected $description = 'Generate thumbnails for existing media files';

    public function handle(): void
    {
        $manager = new ImageManager(new ImagickDriver());
        $images  = Media::whereIn('ext', ['jpg','jpeg','png','gif','webp','avif'])->get();

        $this->info("Found {$images->count()} images");
        $bar = $this->output->createProgressBar($images->count());

        foreach ($images as $media) {
            $thumbPath = Storage::disk('media')->path('thumbs/' . $media->path);
            $origPath  = Storage::disk('media')->path($media->path);

            if (!file_exists($origPath)) {
                $this->newLine();
                $this->warn("Missing: {$media->path}");
                $bar->advance();
                continue;
            }

            if (file_exists($thumbPath) && !$this->option('force')) {
                $bar->advance();
                continue;
            }

            $thumbDir = dirname($thumbPath);
            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }

            try {
                $manager->decodePath($origPath)
                    ->scaleDown(400, 400)
                    ->encodeUsingMediaType($media->mime_type, quality: 75)
                    ->save($thumbPath);
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("Failed {$media->path}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done');
    }
}