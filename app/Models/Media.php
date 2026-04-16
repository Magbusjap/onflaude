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
        return route('media.serve', ['path' => $this->path]);
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

}