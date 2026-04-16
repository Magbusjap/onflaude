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

class MediaLibrary extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Media';
    protected static ?string $title = 'Media Library';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.media-library';

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
                    ->maxSize(65536) // 64MB
                    ->acceptedFileTypes([
                        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
                        'application/pdf', 'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'video/mp4', 'video/webm', 'audio/mpeg', 'audio/wav',
                    ])
                    ->disk('public')
                    ->directory('media')
                    ->storeFileNamesIn('original_names')
                    ->image()
                    ->imageResizeMode('contain')
                    ->columnSpanFull(),
            ])
            ->statePath('uploadData');
    }

    public function getMediaProperty()
    {
        $query = Media::query()
            ->when($this->search, fn($q) => $q->where('original_name', 'ilike', '%' . $this->search . '%'))
            ->when($this->typeFilter !== 'all', function ($q) {
                return match ($this->typeFilter) {
                    'images'    => $q->where('mime_type', 'like', 'image/%'),
                    'documents' => $q->where('mime_type', 'like', 'application/%'),
                    'video'     => $q->where('mime_type', 'like', 'video/%'),
                    'audio'     => $q->where('mime_type', 'like', 'audio/%'),
                    default     => $q,
                };
            });

        return match ($this->sortBy) {
            'oldest'   => $query->orderBy('created_at')->get(),
            'name_asc' => $query->orderBy('original_name')->get(),
            default    => $query->orderByDesc('created_at')->get(), 
        };
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['administrator', 'editor']);
    }

    public function getSelectedMedia(): ?Media
    {
        return $this->selectedId ? Media::find($this->selectedId) : null;
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
        $this->validate();

        $files = $this->uploadData['files'] ?? [];
        $manager = new ImageManager(new Driver());
        $uploaded = 0;

        if (!$this->selectedId) return;

        Media::where('id', $this->selectedId)->update([
            'alt_text'    => $this->altText,
            'title'       => $this->mediaTitle,
            'caption'     => $this->mediaCaption,
            'description' => $this->mediaDescription,
        ]);

        Notification::make()->title('Saved')->success()->send();

        foreach ($files as $file) {
            $tmpPath = $file->getRealPath();

            if (!$tmpPath || !file_exists($tmpPath)) {
                continue;
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $realMime = $finfo->file($tmpPath);

            $allowedMimes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/pdf', 'video/mp4', 'video/webm',
                'audio/mpeg', 'audio/wav',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];

            if (!in_array($realMime, $allowedMimes)) {
                Notification::make()->title("Rejected: {$realMime}")->danger()->send();
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();

            $newPath = 'uploads/' . uniqid() . '_' . $originalName;
            Storage::disk('media')->put($newPath, file_get_contents($tmpPath));

            $width = null;
            $height = null;

            if (str_starts_with($realMime, 'image/') && $ext !== 'svg') {
                try {
                    $img = $manager->decodePath(Storage::disk('media')->path($newPath));
                    $width  = $img->width();
                    $height = $img->height();
                } catch (\Throwable) {}
            }

            $newPath = 'uploads/' . uniqid() . '_' . $originalName;
            Storage::disk('media')->put($newPath, file_get_contents($tmpPath));

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

        $this->uploadData = ['files' => []];
        $this->showUploader = false;

        if ($uploaded > 0) {
            Notification::make()->title("{$uploaded} file(s) uploaded")->success()->send();
        }
    }
}