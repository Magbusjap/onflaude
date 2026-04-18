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
            Actions\DeleteAction::make(),
        ];
    }
    
    public function setFeaturedImage(int $id): void
    {
        $this->data['featured_image_id'] = $id;
    }

    public function setBuilderImageId(string $statePath, int $mediaId): void
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

