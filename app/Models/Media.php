<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'name',
        'filename',
        'original_name',
        'mime_type',
        'disk',
        'path',
        'size',
        'alt',
        'title',
        'caption',
        'description',
        'folder',
        'uploaded_by',
        'ext',
        'width',
        'height',
        'alt_text',
    ];

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getUrlAttribute(): string
    {
        return route('media.serve', ['path' => $this->path]) . '?v=' . $this->updated_at->timestamp;
    }

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getTypeAttribute(): string
    {
        if ($this->isImage()) return 'image';
        if (str_starts_with($this->mime_type, 'video/')) return 'video';
        if (str_starts_with($this->mime_type, 'audio/')) return 'audio';
        return 'document';
    }

    public bool $showReplacer = false;
    public array $replaceData = ['file' => null];

    public function openReplacer(): void
    {
        $this->replaceData = ['file' => null];
        $this->showReplacer = true;
    }

    public function closeReplacer(): void
    {
        $this->replaceData = ['file' => null];
        $this->showReplacer = false;
    }

    public function replaceFile(): void
    {
        $media = Media::find($this->selectedId);
        if (!$media) return;

        $files = $this->replaceData['file'] ?? [];
        if (empty($files)) {
            Notification::make()->title('Please wait for file to finish uploading')->warning()->send();
            return;
        }

        $file    = is_array($files) ? $files[0] : $files;
        $tmpPath = $file->getRealPath();

        if (!$tmpPath || !file_exists($tmpPath)) return;

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($tmpPath);

        if (!in_array($realMime, MediaSettings::allowedMimes())) {
            Notification::make()->title("Rejected: {$realMime}")->danger()->send();
            return;
        }

        $manager = new ImageManager(new ImagickDriver());
        $ext     = strtolower($file->getClientOriginalExtension());
        $isImage = str_starts_with($realMime, 'image/') && $ext !== 'svg';

        $oldPath = $media->path;

        if ($isImage && MediaSettings::convertToWebp()) {
            $newPath  = preg_replace('/\.[^.]+$/', '.webp', $oldPath);
            $ext      = 'webp';
            $realMime = 'image/webp';
        } else {
            $newPath = $oldPath;
        }

        $tmpStoragePath = $newPath . '.tmp';
        Storage::disk('media')->put($tmpStoragePath, file_get_contents($tmpPath));

        $size   = Storage::disk('media')->size($tmpStoragePath);
        $width  = null;
        $height = null;

        if ($isImage) {
            try {
                $img = $manager->decodePath(Storage::disk('media')->path($tmpStoragePath));

                if (MediaSettings::stripExif()) {
                    $img->core()->native()->stripImage();
                }

                if (MediaSettings::convertToWebp()) {
                    $img->encodeUsingMediaType('image/webp', quality: MediaSettings::jpegQuality())
                        ->save(Storage::disk('media')->path($tmpStoragePath));
                } else {
                    $img->encodeUsingFileExtension($ext, MediaSettings::jpegQuality())
                        ->save(Storage::disk('media')->path($tmpStoragePath));
                }

                clearstatcache(true, Storage::disk('media')->path($tmpStoragePath));
                $size   = filesize(Storage::disk('media')->path($tmpStoragePath));
                $width  = $img->width();
                $height = $img->height();

            } catch (\Throwable $e) {
                \Log::error('Replace processing failed: ' . $e->getMessage());
            }
        }

        Storage::disk('media')->delete($oldPath);
        Storage::disk('media')->move($tmpStoragePath, $newPath);

        $media->update([
            'path'      => $newPath,
            'filename'  => basename($newPath),
            'mime_type' => $realMime,
            'ext'       => $ext,
            'size'      => $size,
            'width'     => $width,
            'height'    => $height,
        ]);

        $this->replaceData  = ['file' => null];
        $this->showReplacer = false;

        Notification::make()->title('File replaced')->success()->send();
    }

}