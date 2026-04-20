<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?int $navigationSort = 8;
    protected static string $view = 'admin::pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name'        => option('site_name', 'OnFlaude CMS'),
            'site_description' => option('site_description', ''),
            'site_url'         => option('site_url', ''),
            'admin_email'      => option('admin_email', ''),
            'posts_per_page'   => option('posts_per_page', '10'),
            'admin_path'       => option('admin_path', 'admin'),
            'active_theme'     => option('active_theme', 'default'),
            'site_favicon'     => option('site_favicon', ''),
            'media_max_upload_size'    => (int) option('media_max_upload_size', 10),
            'media_allowed_types'      => array_filter(explode(',', option('media_allowed_types', 'images'))),
            'media_convert_to_webp'    => (bool) option('media_convert_to_webp', false),
            'media_jpeg_quality'       => (int) option('media_jpeg_quality', 85),
            'media_strip_exif'         => (bool) option('media_strip_exif', true),
            'media_sanitize_filenames' => (bool) option('media_sanitize_filenames', true),
            'media_organize_by_date'   => (bool) option('media_organize_by_date', false),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\TextInput::make('site_name')
                                    ->label('Site Name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('site_description')
                                    ->label('Site Description / Tagline')
                                    ->rows(3)
                                    ->maxLength(500),

                                Forms\Components\FileUpload::make('site_favicon')
                                    ->label('Site Icon (Favicon)')
                                    ->helperText('Square image, minimum 512×512px. Used in browser tabs, bookmarks and admin panel.')
                                    ->image()
                                    ->imagePreviewHeight('80')
                                    ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/x-icon', 'image/webp'])
                                    ->disk('public')
                                    ->directory('favicon')
                                    ->visibility('public')
                                    ->maxSize(2048),

                                Forms\Components\TextInput::make('site_url')
                                    ->label('Site URL')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('https://example.com'),

                                Forms\Components\TextInput::make('admin_email')
                                    ->label('Admin Email')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('posts_per_page')
                                    ->label('Posts Per Page')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->default(10),
                            ]),

                        Forms\Components\Tabs\Tab::make('Appearance')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\Select::make('active_theme')
                                    ->label('Active Theme')
                                    ->options(fn () => $this->getAvailableThemes())
                                    ->required(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Security')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\TextInput::make('admin_path')
                                    ->label('Admin Panel URL')
                                    ->required()
                                    ->maxLength(100)
                                    ->prefix('/')
                                    ->helperText('Changing this will redirect you to the new URL. Use only letters, numbers, hyphens.')
                                    ->regex('/^[a-z0-9\-]+$/'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make('Upload')
                                    ->schema([
                                        Forms\Components\TextInput::make('media_max_upload_size')
                                            ->label('Max Upload Size')
                                            ->numeric()
                                            ->suffix('MB')
                                            ->minValue(1)
                                            ->maxValue(512)
                                            ->helperText('Server limit: 64 MB (set in php.ini and nginx).'),

                                        Forms\Components\CheckboxList::make('media_allowed_types')
                                            ->label('Allowed File Types')
                                            ->options([
                                                'images'    => 'Images (jpg, png, gif, webp, svg)',
                                                'documents' => 'Documents (pdf, doc, docx, xls, xlsx, txt)',
                                                'audio'     => 'Audio (mp3, wav, ogg)',
                                                'video'     => 'Video (mp4, webm, ogv)',
                                                'archives'  => 'Archives (zip, gz, 7z)',
                                            ])
                                            ->columns(2),
                                    ]),

                                Forms\Components\Section::make('Image Processing')
                                    ->schema([
                                        Forms\Components\Toggle::make('media_convert_to_webp')
                                            ->label('Convert images to WebP on upload')
                                            ->helperText('Reduces file size. Original format is discarded.'),

                                        Forms\Components\TextInput::make('media_jpeg_quality')
                                            ->label('JPEG / WebP Quality')
                                            ->numeric()
                                            ->suffix('%')
                                            ->minValue(10)
                                            ->maxValue(100)
                                            ->helperText('85 is a good balance. Lower = smaller file, less quality.'),

                                        Forms\Components\Toggle::make('media_strip_exif')
                                            ->label('Strip EXIF metadata')
                                            ->helperText('Removes GPS, camera model and other metadata from uploaded images.'),
                                    ]),

                                Forms\Components\Section::make('Organization')
                                    ->schema([
                                        Forms\Components\Toggle::make('media_sanitize_filenames')
                                            ->label('Sanitize filenames on upload')
                                            ->helperText('Cyrillic → transliteration, spaces → hyphens, lowercase.'),

                                        Forms\Components\Toggle::make('media_organize_by_date')
                                            ->label('Organize uploads by date')
                                            ->helperText('Files go to uploads/2026/04/ instead of uploads/'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

   public function save(): void
    {
        $data = $this->form->getState();

        $oldAdminPath = option('admin_path', 'admin');
        $newAdminPath = $data['admin_path'];

        set_option('site_name',        $data['site_name'],        'general');
        set_option('site_description', $data['site_description'], 'general');
        set_option('site_url',         $data['site_url'],         'general');
        set_option('admin_email',      $data['admin_email'],      'general');
        set_option('posts_per_page',   $data['posts_per_page'],   'general');
        set_option('active_theme',     $data['active_theme'],     'appearance');
        set_option('admin_path',       $newAdminPath,             'system');
        set_option('site_favicon',     $data['site_favicon'],     'general');
        set_option('media_max_upload_size',    $data['media_max_upload_size'],                'media');
        set_option('media_allowed_types',      implode(',', $data['media_allowed_types'] ?? ['images']), 'media');
        set_option('media_convert_to_webp',    $data['media_convert_to_webp'] ? '1' : '0',   'media');
        set_option('media_jpeg_quality',       $data['media_jpeg_quality'],                   'media');
        set_option('media_strip_exif',         $data['media_strip_exif'] ? '1' : '0',         'media');
        set_option('media_sanitize_filenames', $data['media_sanitize_filenames'] ? '1' : '0', 'media');
        set_option('media_organize_by_date',   $data['media_organize_by_date'] ? '1' : '0',   'media');

        //If the admin_path has changed, redirect to the new URL
        if ($oldAdminPath !== $newAdminPath) {
            $this->redirect('/' . $newAdminPath . '/settings', navigate: false);
            return;
        }

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('save')
                ->color('primary'),
        ];
    }

    protected function getAvailableThemes(): array
    {
        $themesPath = base_path('themes');
        $themes = ['default' => 'Default Theme'];

        if (is_dir($themesPath)) {
            foreach (scandir($themesPath) as $dir) {
                if ($dir === '.' || $dir === '..') continue;
                $jsonPath = $themesPath . '/' . $dir . '/theme.json';
                if (file_exists($jsonPath)) {
                    $meta = json_decode(file_get_contents($jsonPath), true);
                    $themes[$dir] = $meta['name'] ?? $dir;
                }
            }
        }

        return $themes;
    }
}