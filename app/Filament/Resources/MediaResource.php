<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Media';
    protected static ?int $navigationSort = 5;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Group::make()
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\FileUpload::make('path')
                                    ->label('File')
                                    ->required()
                                    ->disk('public')
                                    ->directory('media')
                                    ->maxSize(65536) 
                                    ->acceptedFileTypes([
                                        'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml',
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'video/mp4', 'video/webm',
                                        'audio/mpeg', 'audio/wav', 'audio/ogg',
                                    ])
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('alt')
                                    ->label('Alt Text')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('title')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('caption')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Group::make()
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Section::make('File Info')
                                    ->schema([
                                        Forms\Components\TextInput::make('file_name')
                                            ->label('Filename')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('mime_type')
                                            ->label('Type')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('size')
                                            ->label('Size')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('folder')
                                            ->label('Folder')
                                            ->default('/'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Preview')
                    ->disk('public')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => $state >= 1048576
                        ? round($state / 1048576, 2) . ' MB'
                        : round($state / 1024, 2) . ' KB'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('folder')
                    ->label('Folder'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mime_type')
                    ->label('Type')
                    ->options([
                        'image/jpeg' => 'JPEG',
                        'image/png'  => 'PNG',
                        'image/gif'  => 'GIF',
                        'image/webp' => 'WebP',
                        'application/pdf' => 'PDF',
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
            'index'  => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit'   => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}