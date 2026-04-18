<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use App\Services\MediaSettings;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class MediaController extends Controller
{
    public function serve(string $path)
    {
        // Ищем по пути в БД
        $media = Media::where('path', $path)->firstOrFail();

        $fullPath = Storage::disk('media')->path($path);
        abort_if(!file_exists($fullPath), 404);

        return response()->file($fullPath, [
            'Content-Type'  => $media->mime_type,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    public function serveThumb(string $path)
    {
        $thumbPath = 'thumbs/' . $path;
        $fullThumb = Storage::disk('media')->path($thumbPath);

        if (file_exists($fullThumb)) {
            return response()->file($fullThumb, [
                'Content-Type'  => mime_content_type($fullThumb),
                'Cache-Control' => 'public, max-age=31536000',
            ]);
        }

        $media = Media::where('path', $path)->firstOrFail();
        $fullPath = Storage::disk('media')->path($path);
        abort_if(!file_exists($fullPath), 404);

        return response()->file($fullPath, [
            'Content-Type'  => $media->mime_type,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    public function uploadQuick(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['file' => 'required|file|max:' . (MediaSettings::maxBytes() / 1024)]);

        $file     = $request->file('file');
        $tmpPath  = $file->getRealPath();
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($tmpPath);

        if (!in_array($realMime, MediaSettings::allowedMimes())) {
            return response()->json(['error' => 'File type not allowed'], 422);
        }

        $manager      = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Imagick\Driver());
        $originalName = $file->getClientOriginalName();
        $ext          = strtolower($file->getClientOriginalExtension());
        $cleanName    = MediaSettings::sanitizeFilename($originalName);
        $folder       = MediaSettings::uploadFolder();
        $newPath      = $folder . '/' . uniqid() . '_' . $cleanName;

        \Illuminate\Support\Facades\Storage::disk('media')->put($newPath, file_get_contents($tmpPath));

        $isImage = str_starts_with($realMime, 'image/') && $ext !== 'svg';
        $width = $height = null;

        if ($isImage) {
            try {
                $img = $manager->decodePath(\Illuminate\Support\Facades\Storage::disk('media')->path($newPath));
                if (MediaSettings::stripExif()) $img->core()->native()->stripImage();
                if (MediaSettings::convertToWebp()) {
                    $webpPath = preg_replace('/\.[^.]+$/', '.webp', $newPath);
                    $img->encodeUsingMediaType('image/webp', quality: MediaSettings::jpegQuality())
                        ->save(\Illuminate\Support\Facades\Storage::disk('media')->path($webpPath));
                    \Illuminate\Support\Facades\Storage::disk('media')->delete($newPath);
                    $newPath = $webpPath; $ext = 'webp'; $realMime = 'image/webp';
                    $originalName = preg_replace('/\.[^.]+$/', '.webp', $originalName);
                } else {
                    $img->encodeUsingFileExtension($ext, MediaSettings::jpegQuality())
                        ->save(\Illuminate\Support\Facades\Storage::disk('media')->path($newPath));
                }
                $thumbDir = \Illuminate\Support\Facades\Storage::disk('media')->path('thumbs/' . dirname($newPath));
                if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);
                $manager->decodePath(\Illuminate\Support\Facades\Storage::disk('media')->path($newPath))
                    ->scaleDown(400, 400)
                    ->encodeUsingMediaType($realMime, quality: 75)
                    ->save(\Illuminate\Support\Facades\Storage::disk('media')->path('thumbs/' . $newPath));
                clearstatcache(true, \Illuminate\Support\Facades\Storage::disk('media')->path($newPath));
                $width  = $img->width();
                $height = $img->height();
            } catch (\Throwable $e) {
                \Log::error('uploadQuick: ' . $e->getMessage());
            }
        }

        $size  = filesize(\Illuminate\Support\Facades\Storage::disk('media')->path($newPath));
        $media = Media::create([
            'filename'      => basename($newPath),
            'original_name' => $originalName,
            'path'          => $newPath,
            'mime_type'     => $realMime,
            'ext'           => $ext,
            'size'          => $size,
            'width'         => $width,
            'height'        => $height,
            'uploaded_by'   => auth()->id(),
        ]);

        return response()->json(['id' => $media->id, 'url' => $media->url]);
    }
}