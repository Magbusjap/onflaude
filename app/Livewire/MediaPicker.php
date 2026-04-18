<?php

namespace App\Livewire;

use App\Models\Media;
use App\Models\MediaFolder;
use App\Services\MediaSettings;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

class MediaPicker extends Component
{
    use WithFileUploads;

    public ?int $selectedId = null;
    public string $search = '';
    public string $typeFilter = 'all';
    public ?int $currentFolderId = null;
    public int $perPage = 25;
    public int $currentPage = 1;
    public int $activeTab = 1;

    public $uploadFiles = [];

    #[Computed(persist: false, cache: false)]
    public function getMediaProperty()
    {
        $query = Media::query()
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('original_name', 'ilike', '%' . $this->search . '%')
                    ->orWhere('title', 'ilike', '%' . $this->search . '%');
            }))
            ->when($this->currentFolderId !== null, function ($q) {
                if ($this->currentFolderId === 0) {
                    return $q->whereNull('folder_id');
                }
                return $q->where('folder_id', $this->currentFolderId);
            })
            ->when($this->typeFilter !== 'all', function ($q) {
                return match ($this->typeFilter) {
                    'images'    => $q->where('mime_type', 'like', 'image/%'),
                    'documents' => $q->where('mime_type', 'like', 'application/%'),
                    'video'     => $q->where('mime_type', 'like', 'video/%'),
                    'audio'     => $q->where('mime_type', 'like', 'audio/%'),
                    default     => $q,
                };
            })
            ->orderByDesc('created_at');

        return $query->paginate($this->perPage, ['*'], 'page', $this->currentPage);
    }

    public function getFoldersProperty()
    {
        return MediaFolder::orderBy('name')->get();
    }

    public function getTotalPagesProperty(): int
    {
        return (int) ceil($this->media->total() / $this->perPage);
    }

    public function selectFile(int $id): void
    {
        $this->selectedId = ($this->selectedId === $id) ? null : $id;
    }

    public function confirmSelection(): void
    {
        if (!$this->selectedId) return;
        $media = Media::find($this->selectedId);
        if (!$media) return;
        $this->dispatch('media-picked', id: $this->selectedId, url: $media->url);
    }

    public function selectFolder(?int $id): void
    {
        $this->currentFolderId = $id;
        $this->currentPage = 1;
        unset($this->media);
    }

    public function goToPage(int $page): void
    {
        $this->currentPage = max(1, min($page, $this->totalPages));
        unset($this->media);
    }

    public function updatedSearch(): void { $this->currentPage = 1; unset($this->media); }
    public function updatedTypeFilter(): void { $this->currentPage = 1; unset($this->media); }

    public function saveFiles(): void
    {
        if (empty($this->uploadFiles)) return;

        $manager  = new ImageManager(new ImagickDriver());
        $lastId   = null;

        foreach ($this->uploadFiles as $file) {
            $tmpPath  = $file->getRealPath();
            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $realMime = $finfo->file($tmpPath);

            if (!in_array($realMime, MediaSettings::allowedMimes())) continue;

            $originalName = $file->getClientOriginalName();
            $ext          = strtolower($file->getClientOriginalExtension());
            $cleanName    = MediaSettings::sanitizeFilename($originalName);
            $folder       = MediaSettings::uploadFolder();
            $newPath      = $folder . '/' . uniqid() . '_' . $cleanName;

            Storage::disk('media')->put($newPath, file_get_contents($tmpPath));

            $isImage = str_starts_with($realMime, 'image/') && $ext !== 'svg';
            $width = $height = null;

            if ($isImage) {
                try {
                    $img = $manager->decodePath(Storage::disk('media')->path($newPath));
                    if (MediaSettings::stripExif()) $img->core()->native()->stripImage();

                    if (MediaSettings::convertToWebp()) {
                        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $newPath);
                        $img->encodeUsingMediaType('image/webp', quality: MediaSettings::jpegQuality())
                            ->save(Storage::disk('media')->path($webpPath));
                        Storage::disk('media')->delete($newPath);
                        $newPath = $webpPath; $ext = 'webp'; $realMime = 'image/webp';
                        $originalName = preg_replace('/\.[^.]+$/', '.webp', $originalName);
                    } else {
                        $img->encodeUsingFileExtension($ext, MediaSettings::jpegQuality())
                            ->save(Storage::disk('media')->path($newPath));
                    }

                    $thumbDir = Storage::disk('media')->path('thumbs/' . dirname($newPath));
                    if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);
                    $manager->decodePath(Storage::disk('media')->path($newPath))
                        ->scaleDown(400, 400)
                        ->encodeUsingMediaType($realMime, quality: 75)
                        ->save(Storage::disk('media')->path('thumbs/' . $newPath));

                    clearstatcache(true, Storage::disk('media')->path($newPath));
                    $width  = $img->width();
                    $height = $img->height();
                } catch (\Throwable $e) {
                    \Log::error('MediaPicker upload: ' . $e->getMessage());
                }
            }

            $size = filesize(Storage::disk('media')->path($newPath));

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

            $lastId = $media->id;
        }

        $this->uploadFiles = [];
        $this->activeTab   = 1;
        unset($this->media);

        if ($lastId) {
            $this->selectedId = $lastId;
        }
    }

    public function resetState(): void
    {
        $this->selectedId    = null;
        $this->search        = '';
        $this->typeFilter    = 'all';
        $this->currentFolderId = null;
        $this->currentPage   = 1;
        $this->activeTab     = 1;
        $this->uploadFiles   = [];
        unset($this->media);
    }

    public function render()
    {
        return view('livewire.media-picker');
    }
}