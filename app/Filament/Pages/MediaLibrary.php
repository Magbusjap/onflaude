<?php

namespace App\Filament\Pages;

use App\Models\Media;
use Filament\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Services\MediaSettings;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Livewire\Attributes\Computed;
use App\Models\MediaFolder;


class MediaLibrary extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Media';
    protected static ?string $title = 'Media Library';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'admin::pages.media-library';

    public ?int $selectedId = null;
    public string $search = '';
    public string $typeFilter = 'all';
    public string $sortBy = 'newest';
    public string $altText = '';
    public string $mediaTitle = '';
    public string $mediaCaption = '';
    public string $mediaDescription = '';

    public array $uploadData = ['files' => []];
    public bool $showUploader = false;

    public int $perPage = 25;
    public int $currentPage = 1;
    public ?int $currentFolderId = null;
    public string $newFolderName = '';
    public bool $showFolderForm = false;

    public function updatedSelectedId(): void
    {
        $media = $this->getSelectedMedia();
        \Log::info('updatedSelectedId', [
            'selectedId'  => $this->selectedId,
            'title'       => $media?->title,
            'alt_text'    => $media?->alt_text,
        ]);
        $this->altText          = $media?->alt_text ?? '';
        $this->mediaTitle       = $media?->title ?? '';
        $this->mediaCaption     = $media?->caption ?? '';
        $this->mediaDescription = $media?->description ?? '';
    }

    public function updatedAltText(string $value): void
    {
        if ($this->selectedId) {
            Media::where('id', $this->selectedId)->update(['alt_text' => $value]);
        }
    }

    public function mount(): void
    {
        $this->form->fill(['files' => []]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('files')
                    ->label('')
                    ->multiple()
                    ->maxSize(MediaSettings::maxBytes() / 1024)
                    ->acceptedFileTypes(MediaSettings::allowedMimes())
                    ->disk('public')
                    ->directory('media')
                    ->storeFileNamesIn('original_names')
                    ->columnSpanFull(),
            ])
            ->statePath('uploadData');
    }

    #[Computed(persist: false, cache: true)]
    public function getMediaProperty()
    {
        $query = Media::query()
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('original_name', 'ilike', '%' . $this->search . '%')
                ->orWhere('title', 'ilike', '%' . $this->search . '%')
                ->orWhere('alt_text', 'ilike', '%' . $this->search . '%');
            }))
            ->when($this->currentFolderId !== null, function($q) {
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
            });

        $query = match ($this->sortBy) {
            'oldest'   => $query->orderBy('created_at'),
            'name_asc' => $query->orderBy('original_name'),
            default    => $query->orderByDesc('created_at'),
        };

        return $query->paginate($this->perPage, ['*'], 'page', $this->currentPage);
    }

    public function getTotalPagesProperty(): int
    {
        return (int) ceil($this->media->total() / $this->perPage);
    }

    public function goToPage(int $page): void
    {
        $this->currentPage = max(1, min($page, $this->totalPages));
        unset($this->media);
    }

    public function updatedPerPage(): void
    {
        $this->currentPage = 1;
        unset($this->media);
    }

    public function updatedSearch(): void
    {
        $this->currentPage = 1;
        unset($this->media);
    }

    public function updatedTypeFilter(): void
    {
        $this->currentPage = 1;
        unset($this->media);
    }

    public function updatedSortBy(): void
    {
        $this->currentPage = 1;
        unset($this->media);
    }

    public function getFoldersProperty()
    {
        return \App\Models\MediaFolder::orderBy('name')->get();
    }

    public function selectFolder(?int $id): void
    {
        $this->currentFolderId = $id;
        $this->currentPage = 1;
        $this->selectedId = null;
        unset($this->media);
    }

    public function createFolder(): void
    {
        $name = trim($this->newFolderName);
        if (!$name) return;

        \App\Models\MediaFolder::create([
            'name' => $name,
            'slug' => \App\Models\MediaFolder::generateSlug($name),
        ]);

        $this->newFolderName = '';
        $this->showFolderForm = false;
    }

    public function deleteFolder(int $id): void
    {
        abort_unless(auth()->user()?->role === 'administrator', 403);
        $folder = \App\Models\MediaFolder::find($id);
        if (!$folder) return;
        // Файлы остаются, folder_id → null (nullOnDelete в FK)
        $folder->delete();
        if ($this->currentFolderId === $id) {
            $this->currentFolderId = null;
        }
    }

    public function moveToFolder(?int $folderId): void
    {
        if (!$this->selectedId) return;
        Media::where('id', $this->selectedId)->update(['folder_id' => $folderId]);
        unset($this->media);
        Notification::make()->title('Moved')->success()->send();
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['administrator', 'editor']);
    }

    public function getSelectedMedia(): ?Media
    {
        if (!$this->selectedId) return null;
        return Media::find($this->selectedId);
    }

    public function selectFile(int $id): void
    {
        $this->selectedId = ($this->selectedId === $id) ? null : $id;
        $this->updatedSelectedId();
    }

    public function saveAltText(int $id, string $alt): void
    {
        Media::where('id', $id)->update(['alt_text' => $alt]);
        Notification::make()->title('Alt text saved')->success()->send();
    }

    public function saveMediaMeta(): void
    {
        if (!$this->selectedId) return;

        \Log::info('saveMediaMeta', [
            'id'          => $this->selectedId,
            'title'       => $this->mediaTitle,
            'alt_text'    => $this->altText,
            'caption'     => $this->mediaCaption,
            'description' => $this->mediaDescription,
        ]);

        Media::where('id', $this->selectedId)->update([
            'alt_text'    => $this->altText,
            'title'       => $this->mediaTitle,
            'caption'     => $this->mediaCaption,
            'description' => $this->mediaDescription,
        ]);

        Notification::make()->title('Saved')->success()->send();
    }

    public function deleteSelected(): void
    {
        abort_unless(auth()->user()?->role === 'administrator', 403);

        $media = Media::find($this->selectedId);
        if (!$media) return;

        Storage::disk('media')->delete($media->path);
        $media->delete();
        $this->selectedId = null;

        Notification::make()->title('File deleted')->success()->send();
    }

    public function openUploader(): void
    {
        $this->uploadData = ['files' => []];
        $this->form->fill(['files' => []]);
        $this->showUploader = true;
    }

    public function closeUploader(): void
    {
        $this->uploadData = ['files' => []];
        $this->showUploader = false;
    }

    public function saveFiles(): void
    {
        $files = $this->uploadData['files'] ?? [];

        if (empty($files)) {
            Notification::make()->title('Please wait for files to finish uploading')->warning()->send();
            return;
        }

        $this->validate();
        $manager = new ImageManager(new ImagickDriver());
        $uploaded = 0;

        foreach ($files as $file) {
            $tmpPath = $file->getRealPath();

            if (!$tmpPath || !file_exists($tmpPath)) {
                continue;
            }

            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $realMime = $finfo->file($tmpPath);

            if (!in_array($realMime, MediaSettings::allowedMimes())) {
                Notification::make()->title("Rejected: {$realMime}")->danger()->send();
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $ext          = strtolower($file->getClientOriginalExtension());
            $cleanName    = MediaSettings::sanitizeFilename($originalName);
            $folder       = MediaSettings::uploadFolder();
            $newPath      = $folder . '/' . uniqid() . '_' . $cleanName;

            Storage::disk('media')->put($newPath, file_get_contents($tmpPath));
            $size = Storage::disk('media')->size($newPath);

            $isImage = str_starts_with($realMime, 'image/') && $ext !== 'svg';

            if ($isImage) {
                try {
                    $img = $manager->decodePath(Storage::disk('media')->path($newPath));

                    if (MediaSettings::stripExif()) {
                        $img->core()->native()->stripImage();
                    }

                    if (MediaSettings::convertToWebp()) {
                        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $newPath);
                        $img->encodeUsingMediaType('image/webp', quality: MediaSettings::jpegQuality())
                            ->save(Storage::disk('media')->path($webpPath));
                        Storage::disk('media')->delete($newPath);
                        $newPath      = $webpPath;
                        $ext          = 'webp';
                        $realMime     = 'image/webp';
                        $originalName = preg_replace('/\.[^.]+$/', '.webp', $originalName);
                    } else {
                        $img->encodeUsingFileExtension($ext, MediaSettings::jpegQuality())
                            ->save(Storage::disk('media')->path($newPath));
                    }

                    // Генерация thumbnail
                    $thumbDir = Storage::disk('media')->path('thumbs/' . dirname($newPath));
                    if (!is_dir($thumbDir)) {
                        mkdir($thumbDir, 0755, true);
                    }
                    $thumbFullPath = Storage::disk('media')->path('thumbs/' . $newPath);
                    $manager->decodePath(Storage::disk('media')->path($newPath))
                        ->scaleDown(400, 400)
                        ->encodeUsingMediaType($realMime, quality: 75)
                        ->save($thumbFullPath);

                    clearstatcache(true, Storage::disk('media')->path($newPath));
                    $size   = filesize(Storage::disk('media')->path($newPath));
                    $width  = $img->width();
                    $height = $img->height();

                } catch (\Throwable $e) {
                    \Log::error('Media processing failed: ' . $e->getMessage());
                    $width  = null;
                    $height = null;
                }
            } else {
                $width  = null;
                $height = null;
            }

            Media::create([
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

            $uploaded++;
        }

        $this->uploadData  = ['files' => []];
        $this->showUploader = false;

        if ($uploaded > 0) {
            Notification::make()->title("{$uploaded} file(s) uploaded")->success()->send();
        }
    }

    public function replaceForm(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file')
                    ->label('')
                    ->maxSize(MediaSettings::maxBytes() / 1024)
                    ->acceptedFileTypes(MediaSettings::allowedMimes())
                    ->disk('public')
                    ->directory('media')
                    ->columnSpanFull(),
            ])
            ->statePath('replaceData');
    }    

    protected function getForms(): array
    {
        return ['form', 'replaceForm'];
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

        $fileValue = $this->replaceData['file'] ?? null;

        \Log::info('replaceFile start', [
            'replaceData' => $this->replaceData,
            'fileValue'   => $fileValue,
        ]);

        if (empty($fileValue) || !is_array($fileValue)) {
            Notification::make()->title('Please wait for file to finish uploading')->warning()->send();
            return;
        }

        $uuid     = array_key_first($fileValue);
        $tmpData  = $fileValue[$uuid];

        $livewireTmpDir = storage_path('app/private/livewire-tmp/');
        $files = glob($livewireTmpDir . '*');

        if (empty($files)) {
            Notification::make()->title('File not found, please try again')->danger()->send();
            return;
        }

        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
        $tmpPath = $files[0];

        \Log::info('tmpPath found', [
            'tmpPath'  => $tmpPath,
            'exists'   => file_exists($tmpPath),
        ]);

        if (!$tmpPath || !file_exists($tmpPath)) {
            Notification::make()->title('File not found')->danger()->send();
            return;
        }

        
        try {
            $originalName = basename($tmpPath);
            $ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $realMime = $finfo->file($tmpPath);

            if (str_contains($ext, '-')) {
                $ext = substr($ext, strrpos($ext, '-') + 1);
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            }

            $manager = new ImageManager(new ImagickDriver());
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
            unset($this->media);

            Notification::make()->title('File replaced')->success()->send();
        } catch (\Throwable $e) {
            \Log::error('replaceFile exception: ' . $e->getMessage() . ' on line ' . $e->getLine());
            Notification::make()->title('Error: ' . $e->getMessage())->danger()->send();
        }
    }
}