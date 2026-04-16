<x-filament-panels::page>
<div>
    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-4">
        <span class="text-sm text-gray-500">{{ $this->media->count() }} files</span>
        <x-filament::button wire:click="openUploader">
            + Add new
        </x-filament::button>
    </div>

    {{-- Uploader --}}
    @if($showUploader)
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 mb-4 bg-gray-50">
        <form wire:submit="saveFiles">
            {{ $this->form }}
            <div class="flex gap-2 mt-3">
                <x-filament::button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Upload files</span>
                    <span wire:loading>Uploading...</span>
                </x-filament::button>
                <x-filament::button wire:click="closeUploader" color="gray" type="button">
                    Cancel
                </x-filament::button>
            </div>
        </form>
    </div>
    @endif

    {{-- Filters --}}
    <div class="flex gap-2 mb-4 flex-wrap">
        <input wire:model.live.debounce.300ms="search" type="search"
            placeholder="Search files..."
            class="flex-1 min-w-40 px-3 py-1.5 text-sm border border-gray-200 rounded-md" />
        <select wire:model.live="typeFilter"
            class="px-3 py-1.5 text-sm border border-gray-200 rounded-md">
            <option value="all">All types</option>
            <option value="images">Images</option>
            <option value="documents">Documents</option>
            <option value="video">Video</option>
            <option value="audio">Audio</option>
        </select>
        <select wire:model.live="sortBy"
            class="px-3 py-1.5 text-sm border border-gray-200 rounded-md">
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
            <option value="name_asc">Name A–Z</option>
        </select>
    </div>

    {{-- Body --}}
    <div class="grid border border-gray-200 rounded-lg overflow-hidden min-h-96"
        style="grid-template-columns: 1fr 220px;">

        {{-- Grid --}}
        <div class="p-2.5 bg-gray-100 overflow-y-auto"
            style="display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:2px; align-content:start;">
            @forelse($this->media as $file)
            <div wire:key="media-{{ $file->id }}"
                 wire:click="selectFile({{ $file->id }})"
                 class="bg-white rounded-md cursor-pointer overflow-hidden border-2 of-ml-item
                    {{ $selectedId === $file->id ? 'of-ml-item--selected' : 'border-transparent' }}">
                 <div class="aspect-square flex items-center justify-center bg-gray-50 overflow-hidden">
                    @if($file->isImage())
                        <img src="{{ $file->url }}" alt="{{ $file->alt_text }}"
                            class="w-full h-full object-cover" loading="lazy" />
                    @else
                        <span class="text-3xl">{{ match($file->type) {
                            'video' => '🎬', 'audio' => '🎵',
                            'document' => '📄', default => '📎',
                        } }}</span>
                    @endif
                </div>
                <div class="text-[10px] text-gray-400 px-1.5 py-1 truncate border-t border-gray-100">
                    {{ $file->original_name }}
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-10 text-gray-400 text-sm">
                No files found
            </div>
            @endforelse
        </div>

        {{-- Sidebar --}}
        <div class="border-l border-gray-200 bg-white overflow-y-auto">
            @if($this->getSelectedMedia())
            @php $sel = $this->getSelectedMedia() @endphp
            <div class="p-3.5">
                <div class="w-full aspect-square rounded-md overflow-hidden border border-gray-200
                    flex items-center justify-center bg-gray-50 mb-3">
                    @if($sel->isImage())
                        <img src="{{ $sel->url }}" alt="{{ $sel->alt_text }}"
                            class="w-full h-full object-contain" />
                    @else
                        <span class="text-5xl">{{ match($sel->type) {
                            'video' => '🎬', 'audio' => '🎵',
                            'document' => '📄', default => '📎'
                        } }}</span>
                    @endif
                </div>

                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Filename</p>
                <p class="text-sm mb-2.5 break-all">{{ $sel->original_name }}</p>

                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Size</p>
                <p class="text-sm mb-2.5">
                    {{ $sel->human_size }}
                    @if($sel->width && $sel->height)
                        · {{ $sel->width }}×{{ $sel->height }}px
                    @endif
                </p>

                {{-- Uploaded by --}}
                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Uploaded by</p>
                <p class="text-sm mb-2.5">{{ $sel->uploader?->name ?? 'Unknown' }}</p>

                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Uploaded</p>
                <p class="text-sm mb-2.5">
                    {{ $sel->created_at
                        ->setTimezone(auth()->user()->timezone ?? 'UTC')
                        ->format('d M Y, H:i') }}
                </p>

                {{-- Title --}}
                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Title</p>
                <input type="text" wire:model="mediaTitle"
                    placeholder="File title..."
                    class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-md mb-2" />

                {{-- Alt text --}}
                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Alt text</p>
                <input type="text" wire:model="altText"
                    placeholder="Describe the image..."
                    class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-md mb-2" />

                {{-- Caption --}}
                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Caption</p>
                <input type="text" wire:model="mediaCaption"
                    placeholder="Short caption..."
                    class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-md mb-2" />

                {{-- Description --}}
                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Description</p>
                <textarea wire:model="mediaDescription"
                    placeholder="Full description..."
                    rows="3"
                    class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-md mb-2"></textarea>

                {{-- Save button --}}
                <x-filament::button wire:click="saveMediaMeta" class="w-full mb-2" size="sm">
                    Save
                </x-filament::button>

                @if($sel->isImage())
                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">Alt text</p>
                <input type="text" wire:model.live.debounce.500ms="altText"
                    placeholder="Describe the image..."
                    class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-md mb-3" />
                @endif

                <div class="flex flex-col gap-1.5 pt-2.5 border-t border-gray-100">
                    <button onclick="navigator.clipboard.writeText('{{ $sel->url }}')"
                        class="w-full text-xs py-1.5 border border-gray-200 rounded-md bg-white cursor-pointer">
                        Copy URL
                    </button>
                    <a href="{{ $sel->url }}" target="_blank"
                        class="w-full text-xs py-1.5 border border-gray-200 rounded-md bg-white text-center no-underline text-gray-700 block">
                        Open file
                    </a>
                    <button wire:click="deleteSelected"
                        wire:confirm="Delete '{{ $sel->original_name }}'?"
                        class="w-full text-xs py-1.5 border border-red-200 rounded-md bg-white text-red-600 cursor-pointer">
                        Delete
                    </button>
                </div>
            </div>
            @else
            <div class="flex flex-col items-center justify-center h-full gap-2.5 text-gray-400 text-sm">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M3 9l4-4 4 4 4-5 4 4"/>
                    <circle cx="8.5" cy="7.5" r="1.5"/>
                </svg>
                Select a file
            </div>
            @endif
        </div>
    </div>
</div>
</x-filament-panels::page>