<x-filament-panels::page>
<div>
    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500">{{ $this->media->total() }} files</span>

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false"
                    class="flex items-center gap-1.5 text-sm border border-gray-200 rounded-md px-2.5 py-1.5 bg-white text-gray-700 hover:bg-gray-50 cursor-pointer">
                    <span>{{ $perPage }}</span>
                    <svg class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"/>
                    </svg>
                </button>
                <div x-show="open" x-transition
                    class="absolute left-0 mt-1 w-20 bg-white border border-gray-200 rounded-md shadow-lg z-10 py-1">
                    @foreach([25, 50, 100, 250] as $option)
                    <button
                        wire:click="$set('perPage', {{ $option }})"
                        @click="open = false"
                        class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-50
                            {{ $perPage === $option ? 'text-primary-600 font-medium' : 'text-gray-700' }}">
                        {{ $option }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <x-filament::button wire:click="openUploader">
            + Add new
        </x-filament::button>
    </div>

    {{-- Uploader --}}
    @if($showUploader)
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 mb-4 bg-gray-50"
        x-data="{ uploading: false }"
        x-on:livewire-upload-start.window="uploading = true"
        x-on:livewire-upload-finish.window="uploading = false"
        x-on:livewire-upload-error.window="uploading = false">

        {{ $this->form }}

        <div class="flex gap-2 mt-3">
            <x-filament::button
                wire:click="saveFiles"
                x-bind:disabled="uploading"
                wire:loading.attr="disabled"
                wire:target="saveFiles">
                <span x-show="uploading">Uploading...</span>
                <span x-show="!uploading">
                    <span wire:loading.remove wire:target="saveFiles">Upload files</span>
                    <span wire:loading wire:target="saveFiles">Saving...</span>
                </span>
            </x-filament::button>

            <x-filament::button
                type="button"
                color="gray"
                x-bind:disabled="uploading"
                wire:click="closeUploader">
                Cancel
            </x-filament::button>
        </div>
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
    <div x-data="{
        sidebarWidth: 280,
        minWidth: 220,
        maxWidth: 1100,
        dragging: false,
        startX: 0,
        startWidth: 0,
        startDrag(e) {
            this.dragging = true;
            this.startX = e.clientX;
            this.startWidth = this.sidebarWidth;
            document.body.style.cursor = 'col-resize';
            document.body.style.userSelect = 'none';
        },
        onDrag(e) {
            if (!this.dragging) return;
            const diff = this.startX - e.clientX;
            this.sidebarWidth = Math.min(this.maxWidth, Math.max(this.minWidth, this.startWidth + diff));
        },
        stopDrag() {
            this.dragging = false;
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
        }
    }"
    @mousemove.window="onDrag($event)"
    @mouseup.window="stopDrag()"
    class="flex border border-gray-200 rounded-lg overflow-hidden min-h-96">

        {{-- Grid --}}
        <div class="flex-1 bg-gray-100 overflow-y-auto flex flex-col" wire:loading.class="opacity-50">
            <div class="flex-1 p-2.5"
                style="display:grid; grid-template-columns:repeat(auto-fill,minmax(110px,1fr)); gap:2px; align-content:start;">
                @forelse($this->media->items() as $file)
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

            {{-- Pagination --}}
            @if($this->totalPages > 1)
            <div class="flex items-center justify-center gap-1 py-2.5 border-t border-gray-200 bg-gray-100">

                <button wire:click="goToPage({{ $currentPage - 1 }})"
                    @disabled($currentPage === 1)
                    class="px-2.5 py-1.5 text-sm border border-gray-200 rounded-md bg-white
                        {{ $currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50 cursor-pointer' }}">
                    ‹
                </button>

                @php
                    $start = max(1, $currentPage - 2);
                    $end   = min($this->totalPages, $currentPage + 2);
                @endphp

                @if($start > 1)
                    <button wire:click="goToPage(1)"
                        class="px-2.5 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 cursor-pointer">
                        1
                    </button>
                    @if($start > 2)
                        <span class="px-1 text-gray-400">…</span>
                    @endif
                @endif

                @for($i = $start; $i <= $end; $i++)
                    <button wire:click="goToPage({{ $i }})"
                        class="px-2.5 py-1.5 text-sm border rounded-md cursor-pointer
                            {{ $i === $currentPage
                                ? 'bg-primary-600 border-primary-600 text-white'
                                : 'border-gray-200 bg-white hover:bg-gray-50 text-gray-700' }}">
                        {{ $i }}
                    </button>
                @endfor

                @if($end < $this->totalPages)
                    @if($end < $this->totalPages - 1)
                        <span class="px-1 text-gray-400">…</span>
                    @endif
                    <button wire:click="goToPage({{ $this->totalPages }})"
                        class="px-2.5 py-1.5 text-sm border border-gray-200 rounded-md bg-white hover:bg-gray-50 cursor-pointer">
                        {{ $this->totalPages }}
                    </button>
                @endif

                <button wire:click="goToPage({{ $currentPage + 1 }})"
                    @disabled($currentPage === $this->totalPages)
                    class="px-2.5 py-1.5 text-sm border border-gray-200 rounded-md bg-white
                        {{ $currentPage === $this->totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50 cursor-pointer' }}">
                    ›
                </button>

            </div>
            @endif
        </div>

        {{-- Resize Handle --}}
        <div @mousedown="startDrag($event)"
            class="of-ml-resize-handle"
            :class="dragging ? 'of-ml-resize-handle--active' : ''">
            <div class="of-ml-resize-handle__icon">‹›</div>
        </div>

        {{-- Sidebar --}}
        <div class="bg-white overflow-y-auto flex-shrink-0"
            style="container-type: inline-size; container-name: of-sidebar;"
            :style="'width:' + sidebarWidth + 'px'">
            @php $sel = $this->getSelectedMedia() @endphp
            @if($sel)
            <div class="p-3.5">
                <div>
                    <div class="of-ml-sidebar-preview-wrap">
                        <div class="w-full rounded-md overflow-hidden border border-gray-200
                            flex items-center justify-center bg-gray-50 mb-3">
                            @if($sel->isImage())
                                <div class="w-full aspect-square">
                                    <img src="{{ $sel->url }}" alt="{{ $sel->alt_text }}"
                                        class="w-full h-full object-contain" />
                                </div>
                            @elseif($sel->type === 'video')
                                <video controls class="w-full max-h-64" preload="metadata">
                                    <source src="{{ $sel->url }}" type="{{ $sel->mime_type }}">
                                </video>
                            @elseif($sel->type === 'audio')
                                <div class="w-full p-4">
                                    <div class="flex justify-center mb-3">
                                        <span class="text-5xl">🎵</span>
                                    </div>
                                    <p class="text-xs text-gray-500 text-center truncate mb-3">{{ $sel->original_name }}</p>
                                    <audio controls class="w-full" preload="metadata">
                                        <source src="{{ $sel->url }}" type="{{ $sel->mime_type }}">
                                    </audio>
                                </div>
                            @else
                                <div class="aspect-square flex items-center justify-center">
                                    <span class="text-5xl">{{ match($sel->type) {
                                        'document' => '📄', default => '📎'
                                    } }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="of-ml-sidebar-meta" :class="sidebarWidth >= 450 ? 'of-ml-sidebar-meta--wide' : ''">
                        <div>
                            <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Filename</p>
                            <p class="text-sm mb-2.5 break-all">{{ $sel->original_name }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Size</p>
                            <p class="text-sm mb-2.5">
                                {{ $sel->human_size }}
                                @if($sel->width && $sel->height)
                                    · {{ $sel->width }}×{{ $sel->height }}px
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Uploaded by</p>
                            <p class="text-sm mb-2.5">{{ $sel->uploader?->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Uploaded</p>
                            <p class="text-sm mb-2.5">
                                {{ $sel->created_at
                                    ->setTimezone(auth()->user()->timezone ?? 'UTC')
                                    ->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="of-ml-sidebar-fields mt-3" :class="sidebarWidth >= 450 ? 'of-ml-sidebar-fields--wide' : ''">
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Title</p>
                        <input type="text" wire:model="mediaTitle"
                            placeholder="File title..."
                            class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-md" />
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Alt text</p>
                        <input type="text" wire:model="altText"
                            placeholder="Describe the image..."
                            class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-md" />
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Caption</p>
                        <input type="text" wire:model="mediaCaption"
                            placeholder="Short caption..."
                            class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-md" />
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Description</p>
                        <textarea wire:model="mediaDescription"
                            placeholder="Full description..."
                            rows="3"
                            class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-md"></textarea>
                    </div>
                </div>

                <x-filament::button wire:click="saveMediaMeta" class="w-full mt-3 mb-2" size="sm">
                    Save
                </x-filament::button>

                <div class="flex flex-col gap-1.5 pt-2.5 border-t border-gray-100">
                    <button onclick="navigator.clipboard.writeText('{{ $sel->url }}')"
                        class="w-full text-xs py-1.5 border border-gray-200 rounded-md bg-white cursor-pointer">
                        Copy URL
                    </button>
                    
                    <a href="{{ $sel->url }}" target="_blank"
                            class="w-full text-xs py-1.5 border border-gray-200 rounded-md bg-white text-center no-underline text-gray-700 block">
                            Open file
                        </a>

                        {{-- Replace --}}
                        <button wire:click="openReplacer"
                            class="w-full text-xs py-1.5 border border-gray-200 rounded-md bg-white cursor-pointer text-gray-700">
                            Replace file
                        </button>

                        @if($showReplacer)
                        <div class="mt-2 border border-gray-200 rounded-md p-2 bg-gray-50"
                            x-data="{ uploading: false }"
                            x-on:livewire-upload-start.window="uploading = true"
                            x-on:livewire-upload-finish.window="uploading = false"
                            x-on:livewire-upload-error.window="uploading = false">

                            {{ $this->replaceForm }}

                            <div class="flex gap-1.5 mt-2">
                                <x-filament::button
                                    wire:click="replaceFile"
                                    size="sm"
                                    x-bind:disabled="uploading"
                                    wire:loading.attr="disabled"
                                    wire:target="replaceFile"
                                    class="flex-1">
                                    <span x-show="uploading">Uploading...</span>
                                    <span x-show="!uploading">
                                        <span wire:loading.remove wire:target="replaceFile">Save</span>
                                        <span wire:loading wire:target="replaceFile">Saving...</span>
                                    </span>
                                </x-filament::button>

                                <x-filament::button
                                    wire:click="closeReplacer"
                                    size="sm"
                                    color="gray"
                                    class="flex-1">
                                    Cancel
                                </x-filament::button>
                            </div>
                        </div>
                        @endif

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