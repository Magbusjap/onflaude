<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $path = $data['path'];
        $fullPath = Storage::disk('public')->path($path);

        $data['file_name'] = basename($path);
        $data['ext']       = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $data['mime_type'] = mime_content_type($fullPath);
        $data['size']      = filesize($fullPath);
        $data['uploaded_by'] = auth()->id();

        // Для изображений — получаем размеры
        if (str_starts_with($data['mime_type'], 'image/') && $data['ext'] !== 'svg') {
            try {
                $image = Image::read($fullPath);
                $data['width']  = $image->width();
                $data['height'] = $image->height();
            } catch (\Throwable $e) {
                // не критично если не удалось прочитать
            }
        }

        return $data;
    }
}