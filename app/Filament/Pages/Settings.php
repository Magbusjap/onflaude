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
    protected static string $view = 'filament.pages.settings';

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