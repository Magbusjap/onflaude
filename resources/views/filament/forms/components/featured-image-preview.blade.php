<div
    x-data="{ previewUrl: '' }"
    x-on:media-picked.window="previewUrl = $event.detail.url"
    x-on:featured-image-removed.window="previewUrl = ''"
    x-init="
        @if($getRecord()?->featured_image_id)
            previewUrl = {{ json_encode(\App\Models\Media::find($getRecord()->featured_image_id)?->url ?? '') }}
        @endif
    ">
    <template x-if="previewUrl">
        <div class="rounded-md overflow-hidden border border-gray-200 mb-2">
            <img :src="previewUrl" class="w-full object-cover max-h-48" />
        </div>
    </template>
    <template x-if="!previewUrl">
        <div class="mb-2 rounded-md border-2 border-dashed border-gray-200 bg-gray-50
            flex items-center justify-center text-gray-400 text-sm"
            style="height: 120px;">
            No image selected
        </div>
    </template>
</div>