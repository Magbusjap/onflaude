<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Traits\HasGlobalSearch;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use FilamentTiptapEditor\TiptapEditor;

class PostResource extends Resource
{
    use HasGlobalSearch;

    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Posts';
    protected static ?string $navigationLabel = 'All Posts';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Group::make()
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $state, Forms\Set $set) =>
                                        $set('slug', Str::slug($state))
                                    ),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('URL')
                                    ->prefix('/')
                                    ->unique(ignoreRecord: true),


                                Forms\Components\Textarea::make('excerpt')
                                    ->rows(3)
                                    ->maxLength(500),
                                
                                Forms\Components\Builder::make('content')
                                    ->label('Content')
                                    ->columnSpanFull()
                                    ->blocks([
                                        Forms\Components\Builder\Block::make('heading')
                                            ->label('Heading')
                                            ->schema([
                                                Forms\Components\Select::make('level')
                                                    ->options(['h2' => 'H2', 'h3' => 'H3'])
                                                    ->default('h2')
                                                    ->required(),
                                                Forms\Components\TextInput::make('text')
                                                    ->label('Text')
                                                    ->required(),
                                            ]),
                                        Forms\Components\Builder\Block::make('text')
                                            ->label('Text')
                                            ->schema([
                                                \FilamentTiptapEditor\TiptapEditor::make('content')
                                                    ->label('Content')
                                                    ->required(),
                                            ]),
                                        Forms\Components\Builder\Block::make('image')
                                            ->label('Image')
                                            ->schema([
                                                Forms\Components\Hidden::make('media_id'),
                                                Forms\Components\ViewField::make('media_preview')
                                                    ->view('filament.forms.components.builder-image-picker')
                                                    ->dehydrated(false)
                                                    ->live(),
                                                Forms\Components\TextInput::make('caption')
                                                    ->label('Caption'),
                                            ]),
                                        Forms\Components\Builder\Block::make('image_text')
                                            ->label('Image + Text')
                                            ->schema([
                                                Forms\Components\Hidden::make('media_id'),
                                                Forms\Components\ViewField::make('media_preview')
                                                    ->view('filament.forms.components.builder-image-picker')
                                                    ->dehydrated(false)
                                                    ->live(),
                                                Forms\Components\Select::make('position')
                                                    ->options(['left' => 'Left', 'right' => 'Right'])
                                                    ->default('left')
                                                    ->required(),
                                                Forms\Components\TextInput::make('width')
                                                    ->label('Image width (px)')
                                                    ->numeric()
                                                    ->default(300),
                                                \FilamentTiptapEditor\TiptapEditor::make('text')
                                                    ->label('Text')
                                                    ->required(),
                                            ]),
                                        Forms\Components\Builder\Block::make('quote')
                                            ->label('Quote')
                                            ->schema([
                                                Forms\Components\Textarea::make('text')
                                                    ->label('Quote text')
                                                    ->required(),
                                                Forms\Components\TextInput::make('author')
                                                    ->label('Author'),
                                            ]),
                                        Forms\Components\Builder\Block::make('markdown')
                                            ->label('Markdown')
                                            ->schema([
                                                Forms\Components\Textarea::make('content')
                                                    ->label('Markdown text')
                                                    ->required()
                                                    ->rows(10),
                                            ]),
                                    ]),

                            ]),

                        Forms\Components\Group::make()
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Section::make('Publishing')
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'draft'     => 'Draft',
                                                'published' => 'Published',
                                            ])
                                            ->default('draft')
                                            ->required(),
                                        Forms\Components\DateTimePicker::make('published_at')
                                            ->label('Publish Date'),
                                        Forms\Components\Select::make('author_id')
                                            ->relationship('author', 'name')
                                            ->label('Author'),
                                    ]),

                                Forms\Components\Section::make('Featured Image')
                                    ->schema([
                                        Forms\Components\Hidden::make('featured_image_id'),
                                        Forms\Components\ViewField::make('featured_image_preview')
                                            ->view('filament.forms.components.featured-image-preview')
                                            ->dehydrated(false)
                                            ->live(),
                                    ]),

                                Forms\Components\Section::make('Taxonomy')
                                    ->schema([
                                        Forms\Components\Select::make('categories')
                                            ->relationship('categories', 'name')
                                            ->multiple()
                                            ->preload(),
                                        Forms\Components\Select::make('tags')
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->preload(),
                                    ]),

                                Forms\Components\Section::make('SEO')
                                    ->schema([
                                        Forms\Components\TextInput::make('seo_title')
                                            ->maxLength(60)
                                            ->label('SEO Title'),
                                        Forms\Components\Textarea::make('seo_description')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->label('SEO Description'),
                                    ])
                                    ->collapsed(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ]),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'excerpt'];
    }
}
