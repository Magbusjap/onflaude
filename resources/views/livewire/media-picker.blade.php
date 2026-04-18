<div class="flex flex-col h-full" style="min-height: 500px;">

    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 mb-4">
        <button wire:click="$set('activeTab', 1)"
            class="px-4 py-2 text-sm font-medium border-b-2 -mb-px cursor-pointer
                {{ $activeTab === 1 ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Media Library
        </button>
        <button wire:click="$set('activeTab', 2)"
            class="px-4 py-2 text-sm font-medium border-b-2 -mb-px cursor-pointer
                {{ $activeTab === 2 ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Upload
        </button>
    </div>

    {{-- Tab: Library --}}
    @if($activeTab === 1)
    <div class="flex flex-col flex-1 min-h-0">
        <div class="flex gap-2 mb-3 flex-wrap">
            <input wire:model.live.debounce.300ms="search" type="search"
                placeholder="Search files..."
                class="flex-1 min-w-32 px-3 py-1.5 text-sm border border-gray-200 rounded-md" />
            <select wire:model.live="typeFilter"
                class="px-3 py-1.5 text-sm border border-gray-200 rounded-md">
                <option value="all">All types</option>
                <option value="images">Images</option>
                <option value="documents">Documents</option>
                <option value="video">Video</option>
                <option value="audio">Audio</option>
            </select>
        </div>

        <div class="flex gap-0 flex-1 min-h-0 border border-gray-200 rounded-lg overflow-hidden">
            {{-- Folders --}}
            <div class="w-36 flex-shrink-0 bg-white border-r border-gray-200 overflow-y-auto py-1">
                <button wire:click="selectFolder(null)"
                    class="w-full text-left px-3 py-1.5 text-sm flex items-center gap-2 cursor-pointer
                        {{ $currentFolderId === null ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                    </svg>
                    <span class="truncate">All files</span>
                </button>
                <button wire:click="selectFolder(0)"
                    class="w-full text-left px-3 py-1.5 text-sm flex items-center gap-2 cursor-pointer
                        {{ $currentFolderId === 0 ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                    </svg>
                    <span class="truncate">Uncategorized</span>
                </button>
                @foreach($this->folders as $folder)
                <button wire:click="selectFolder({{ $folder->id }})"
                    class="w-full text-left px-3 py-1.5 text-sm flex items-center gap-2 cursor-pointer
                        {{ $currentFolderId === $folder->id ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4 flex-shrink-0 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                    </svg>
                    <span class="truncate">{{ $folder->name }}</span>
                </button>
                @endforeach
            </div>

            {{-- Grid --}}
            <div class="flex-1 bg-gray-100 overflow-y-auto p-2" wire:loading.class="opacity-50">
                <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(90px,1fr)); gap:2px; align-content:start;">
                    @forelse($this->media->items() as $file)
                    <div wire:key="mp-{{ $file->id }}"
                         wire:click="selectFile({{ $file->id }})"
                         class="bg-white rounded-md cursor-pointer overflow-hidden border-2
                            {{ $selectedId === $file->id ? 'border-primary-500' : 'border-transparent' }}">
                        <div class="aspect-square flex items-center justify-center bg-gray-50 overflow-hidden">
                            @if($file->isImage())
                                <img src="{{ $file->thumb_url }}" alt=""
                                    class="w-full h-full object-cover" loading="lazy" />
                            @else
                                <span class="text-2xl">{{ match($file->type ?? '') {
                                    'video' => '🎬', 'audio' => '🎵',
                                    'document' => '📄', default => '📎',
                                } }}</span>
                            @endif
                        </div>
                        <div class="text-[9px] text-gray-400 px-1 py-0.5 truncate border-t border-gray-100">
                            {{ $file->title ?: $file->original_name }}
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-8 text-gray-400 text-sm">
                        No files found
                    </div>
                    @endforelse
                </div>

                @if($this->totalPages > 1)
                <div class="flex items-center justify-center gap-1 pt-2 mt-2 border-t border-gray-200">
                    <button wire:click="goToPage({{ $currentPage - 1 }})"
                        @disabled($currentPage === 1)
                        class="px-2 py-1 text-sm border border-gray-200 rounded bg-white
                            {{ $currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50 cursor-pointer' }}">‹</button>
                    @for($i = max(1,$currentPage-2); $i <= min($this->totalPages,$currentPage+2); $i++)
                    <button wire:click="goToPage({{ $i }})"
                        class="px-2 py-1 text-sm border rounded cursor-pointer
                            {{ $i === $currentPage ? 'bg-primary-600 border-primary-600 text-white' : 'border-gray-200 bg-white' }}">
                        {{ $i }}
                    </button>
                    @endfor
                    <button wire:click="goToPage({{ $currentPage + 1 }})"
                        @disabled($currentPage === $this->totalPages)
                        class="px-2 py-1 text-sm border border-gray-200 rounded bg-white
                            {{ $currentPage === $this->totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50 cursor-pointer' }}">›</button>
                </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200">
            <span class="text-sm text-gray-500">
                @if($selectedId)
                    @php $sel = \App\Models\Media::find($selectedId) @endphp
                    {{ $sel?->title ?: $sel?->original_name }}
                @else
                    No file selected
                @endif
            </span>
            <x-filament::button
                wire:click="confirmSelection"
                :disabled="!$selectedId">
                Select
            </x-filament::button>
        </div>
    </div>
    @endif

    {{-- Tab: Upload --}}
    @if($activeTab === 2)
    <div class="flex flex-col items-center justify-center flex-1 py-4 overflow-y-auto">
        <div class="w-full max-w-lg text-center" x-data="{ previews: [] }">

            <div
                x-on:dragover.prevent="$el.classList.add('border-primary-400', 'bg-primary-50')"
                x-on:dragleave.prevent="$el.classList.remove('border-primary-400', 'bg-primary-50')"
                x-on:drop.prevent="
                    $el.classList.remove('border-primary-400', 'bg-primary-50');
                    $refs.fileInput.files = $event.dataTransfer.files;
                    $refs.fileInput.dispatchEvent(new Event('change'));
                "
                class="border-2 border-dashed border-gray-300 bg-gray-50 rounded-xl p-8 transition-colors cursor-pointer"
                x-on:click="$refs.fileInput.click()">

                <svg class="mx-auto mb-3 w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <p class="text-sm text-gray-500 mb-1">Drag & drop your files or <span class="text-primary-600 underline">browse</span></p>
                <p class="text-xs text-gray-400">Max {{ round(\App\Services\MediaSettings::maxBytes() / 1024 / 1024) }}MB per file</p>

                <input type="file"
                    wire:model="uploadFiles"
                    x-ref="fileInput"
                    multiple
                    class="hidden"
                    x-on:change="
                        previews = [];
                        Array.from($event.target.files).forEach(f => {
                            if (!f.type.startsWith('image/')) {
                                previews.push({ name: f.name, url: null });
                                return;
                            }
                            const reader = new FileReader();
                            reader.onload = e => previews.push({ name: f.name, url: e.target.result });
                            reader.readAsDataURL(f);
                        });
                    " />
            </div>

            {{-- Preview--}}
            <template x-if="previews.length > 0">
                <div class="mt-3 grid grid-cols-4 gap-2" style="max-height: 160px; overflow-y: auto;">
                    <template x-for="(file, i) in previews" :key="i">
                        <div class="relative rounded-lg overflow-hidden border border-gray-200 bg-gray-50 aspect-square flex items-center justify-center" style="max-height: 80px;">
                            <template x-if="file.url">
                                <img :src="file.url" class="w-full h-full object-cover" />
                            </template>
                            <template x-if="!file.url">
                                <span class="text-2xl">📄</span>
                            </template>
                            <div class="absolute bottom-0 left-0 right-0 bg-black/40 px-1 py-0.5">
                                <span class="text-white text-[9px] truncate block" x-text="file.name"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div class="mt-4">
                <x-filament::button
                    wire:click="saveFiles"
                    wire:loading.attr="disabled"
                    wire:target="saveFiles,uploadFiles">
                    <span wire:loading wire:target="saveFiles,uploadFiles">Processing...</span>
                    <span wire:loading.remove wire:target="saveFiles,uploadFiles">Upload & Select</span>
                </x-filament::button>
            </div>
        </div>
    </div>
    @endif

</div>