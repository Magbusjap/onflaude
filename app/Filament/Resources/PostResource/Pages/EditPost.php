<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')
                ->label('')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->tooltip('View post')
                ->url(fn () => '/blog/' . $this->record->slug)
                ->openUrlInNewTab()
                ->color('gray'),

            Actions\Action::make('save_draft')
                ->label('Save Draft')
                ->icon('heroicon-o-document')
                ->color('gray')
                ->action(function () {
                    $this->data['status'] = 'draft';
                    $this->save();
                }),

            Actions\Action::make('publish')
                ->label(fn () => $this->record->status === 'published' ? 'Update' : 'Publish')
                ->icon('heroicon-o-globe-alt')
                ->color('primary')
                ->action(function () {
                    $this->data['status'] = 'published';
                    $this->save();
                }),

            Actions\Action::make('toggle_sidebar')
                ->label('')
                ->icon('heroicon-o-squares-2x2')
                ->tooltip('Toggle sidebar')
                ->color('gray')
                ->livewireClickHandlerEnabled(false)
                ->extraAttributes([
                    'x-on:click.stop.prevent' => 'ofToggleSidebar()',
                ]),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
    
    public function setFeaturedImage(int $id): void
    {
        $this->data['setBuilderImageId'] = $id;
    }

    public function setBuilderImageId(string $statePath, ?int $mediaId): void
    {
        $keys = explode('.', $statePath);
        $data = &$this->data;
        foreach ($keys as $i => $key) {
            if ($i === count($keys) - 1) {
                $data[$key] = $mediaId;
            } else {
                if (!isset($data[$key]) || !is_array($data[$key])) {
                    $data[$key] = [];
                }
                $data = &$data[$key];
            }
        }
    }
}

