<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

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
}