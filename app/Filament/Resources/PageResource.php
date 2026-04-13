<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Traits\HasGlobalSearch;

class PageResource extends Resource
{
    use HasGlobalSearch;

    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Pages';
    protected static ?string $navigationLabel = 'All Pages';
    protected static ?int $navigationSort = 4;

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
                                Forms\Components\RichEditor::make('content')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline',
                                        'bulletList', 'orderedList',
                                        'blockquote', 'codeBlock',
                                        'h2', 'h3',
                                        'link', 'undo', 'redo',
                                    ]),
                                Forms\Components\Textarea::make('excerpt')
                                    ->rows(3)
                                    ->maxLength(500),
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
                                        Forms\Components\Select::make('template')
                                            ->options([
                                                'default' => 'Default',
                                                'full'    => 'Full Width',
                                                'landing' => 'Landing Page',
                                            ])
                                            ->default('default'),
                                        Forms\Components\Select::make('parent_id')
                                            ->label('Parent Page')
                                            ->relationship('parent', 'title')
                                            ->placeholder('None'),
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
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL')
                    ->searchable(),
                Tables\Columns\TextColumn::make('template')
                    ->badge(),
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
                Tables\Filters\SelectFilter::make('template')
                    ->options([
                        'default' => 'Default',
                        'full'    => 'Full Width',
                        'landing' => 'Landing Page',
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
            'index'  => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit'   => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'excerpt'];
    }
}
