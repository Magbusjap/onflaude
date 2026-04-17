<?php

namespace App\Services;

class MediaSettings
{
    public static function maxBytes(): int
    {
        return (int) option('media_max_upload_size', 10) * 1024 * 1024;
    }

    public static function allowedMimes(): array
    {
        $raw    = option('media_allowed_types', 'images');
        $active = array_filter(array_map('trim', explode(',', $raw)));

        $map = [
            'images'    => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            'documents' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
            ],
            'audio'    => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4'],
            'video'    => ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'],
            'archives' => ['application/zip', 'application/gzip', 'application/x-7z-compressed'],
        ];

        $mimes = [];
        foreach ($active as $preset) {
            if (isset($map[$preset])) {
                $mimes = array_merge($mimes, $map[$preset]);
            }
        }

        return $mimes ?: $map['images'];
    }

    public static function convertToWebp(): bool
    {
        return (bool) option('media_convert_to_webp', false);
    }

    public static function jpegQuality(): int
    {
        return max(10, min(100, (int) option('media_jpeg_quality', 85)));
    }

    public static function stripExif(): bool
    {
        return (bool) option('media_strip_exif', true);
    }

    public static function uploadFolder(): string
    {
        if ((bool) option('media_organize_by_date', false)) {
            return 'uploads/' . now()->format('Y/m');
        }
        return 'uploads';
    }

    public static function sanitizeFilename(string $filename): string
    {
        if (!(bool) option('media_sanitize_filenames', true)) {
            return $filename;
        }

        $ext  = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);

        $cyr = [
            'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo',
            'ж'=>'zh','з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m',
            'н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u',
            'ф'=>'f','х'=>'kh','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch',
            'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
            'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'Yo',
            'Ж'=>'Zh','З'=>'Z','И'=>'I','Й'=>'J','К'=>'K','Л'=>'L','М'=>'M',
            'Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U',
            'Ф'=>'F','Х'=>'Kh','Ц'=>'Ts','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Sch',
            'Ъ'=>'','Ы'=>'Y','Ь'=>'','Э'=>'E','Ю'=>'Yu','Я'=>'Ya',
        ];

        $name = strtr($name, $cyr);
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9\-_]/', '-', $name);
        $name = preg_replace('/-+/', '-', $name);
        $name = trim($name, '-');

        return $name . ($ext ? '.' . strtolower($ext) : '');
    }
}