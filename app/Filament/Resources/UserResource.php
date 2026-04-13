<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{

    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('role')
                    ->options([
                        'administrator' => 'Administrator',
                        'editor'        => 'Editor',
                        'author'        => 'Author',
                    ])
                    ->default('author')
                    ->required(),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->autocomplete('new-password')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->maxLength(255)
                    ->label(fn (string $operation) => $operation === 'create' ? 'Password' : 'New Password (leave blank to keep)'),
                Forms\Components\Section::make('Time Preferences')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('timezone')
                            ->label('Timezone')
                            ->options(function () {
                                $zones = [];
                                foreach (timezone_identifiers_list() as $tz) {
                                    $region = explode('/', $tz)[0];
                                    $zones[$region][$tz] = $tz;
                                }
                                return $zones;
                            })
                            ->searchable()
                            ->default('Asia/Yekaterinburg'),

                        Forms\Components\Select::make('morning_start')
                            ->label('Morning starts at')
                            ->options(array_combine(range(3, 10), array_map(fn($h) => "{$h}:00", range(3, 10))))
                            ->default(5),

                        Forms\Components\Select::make('afternoon_start')
                            ->label('Afternoon starts at')
                            ->options(array_combine(range(10, 15), array_map(fn($h) => "{$h}:00", range(10, 15))))
                            ->default(12),

                        Forms\Components\Select::make('evening_start')
                            ->label('Evening starts at')
                            ->options(array_combine(range(15, 22), array_map(fn($h) => "{$h}:00", range(15, 22))))
                            ->default(18),
                        Forms\Components\Select::make('night_start')
                            ->label('Night starts at')
                            ->options(array_combine(range(20, 23), array_map(fn($h) => "{$h}:00", range(20, 23))))
                            ->default(22),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'administrator' => 'danger',
                        'editor'        => 'warning',
                        'author'        => 'success',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    protected static bool $globallySearchable = true;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Role'  => $record->role,
            'Email' => $record->email,
        ];
    }
}