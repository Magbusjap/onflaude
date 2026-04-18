<div
    x-data="{
        previewUrl: '',
        mediaModalOpen: false,
        modalKey: 1,
        uploading: false,
        openModal() {
            this.mediaModalOpen = true;
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => {
                const all = Livewire.all();
                const picker = all.find(c => c.name === 'media-picker');
                if (picker) picker.call('resetState');
            });
        },
        closeModal() {
            this.mediaModalOpen = false;
            document.body.style.overflow = '';
        },
        handleDrop(e) {
            const file = e.dataTransfer.files[0];
            if (file) this.uploadDirect(file);
        },
        handleClick() {
            if (this.previewUrl) return;
            this.$refs.directInput.click();
        },
        handleFileInput(e) {
            const file = e.target.files[0];
            if (file) this.uploadDirect(file);
        },
        uploadDirect(file) {
            this.uploading = true;
            const fd = new FormData();
            fd.append('file', file);
            fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
            fetch('/media/upload-quick', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.id && data.url) {
                        this.previewUrl = data.url;
                        $dispatch('media-quick-uploaded', { id: data.id, url: data.url });
                    }
                })
                .finally(() => { this.uploading = false; });
        }
    }"
    x-on:media-picked.window="previewUrl = $event.detail.url; closeModal();"
    x-on:media-quick-uploaded.window="
        $wire.$parent.set('data.featured_image_id', $event.detail.id);
    "
    x-on:featured-image-removed.window="previewUrl = ''"
    x-on:keydown.escape.window="closeModal()"
    x-init="
        @if($getRecord()?->featured_image_id)
            previewUrl = {{ json_encode(\App\Models\Media::find($getRecord()->featured_image_id)?->url ?? '') }}
        @endif
    ">

    {{-- Hidden input download --}}
    <input type="file" x-ref="directInput" class="hidden" accept="image/*"
        x-on:change="handleFileInput($event)" />

    {{-- Preview or Drop-area --}}
    <template x-if="previewUrl">
        <div class="rounded-md overflow-hidden border border-gray-200 mb-2 relative group">
            <img :src="previewUrl" class="w-full object-cover max-h-48" />
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
        </div>
    </template>
    <template x-if="!previewUrl">
        <div
            class="mb-2 rounded-md border-2 border-dashed border-gray-200 bg-gray-50
                flex flex-col items-center justify-center text-gray-400 text-sm cursor-pointer
                hover:border-primary-300 hover:bg-primary-50 transition-colors"
            style="height: 120px;"
            x-on:click="handleClick()"
            x-on:dragover.prevent="$el.classList.add('border-primary-400', 'bg-primary-50')"
            x-on:dragleave.prevent="$el.classList.remove('border-primary-400', 'bg-primary-50')"
            x-on:drop.prevent="$el.classList.remove('border-primary-400', 'bg-primary-50'); handleDrop($event)">
            <template x-if="!uploading">
                <div class="flex flex-col items-center gap-1 pointer-events-none">
                    <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Drop image or click to upload</span>
                </div>
            </template>
            <template x-if="uploading">
                <div class="flex flex-col items-center gap-1 pointer-events-none">
                    <svg class="w-5 h-5 animate-spin text-primary-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <span>Uploading...</span>
                </div>
            </template>
        </div>
    </template>

    {{-- Кнопки --}}
    <div class="flex items-center gap-2 flex-wrap">
        <button type="button" x-on:click="openModal()"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg
                   border border-gray-300 bg-white text-gray-700 shadow-sm
                   hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Media Library
        </button>

        <template x-if="previewUrl">
            <button type="button"
                x-on:click="previewUrl = ''; $dispatch('featured-image-removed')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg
                       border border-red-200 bg-white text-red-600 shadow-sm
                       hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Remove
            </button>
        </template>
    </div>

    {{-- Alpine Modal --}}
    <template x-teleport="body">
        <div
            x-show="mediaModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 flex items-center justify-center p-4"
            style="display:none;">

            <div class="absolute inset-0 bg-black/50" x-on:click="closeModal()"></div>

            <div class="relative bg-white rounded-xl shadow-2xl w-full flex flex-col"
                style="max-width: 72rem; height: 80vh; max-height: 800px; margin-left: 280px;"
                x-on:click.stop>

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
                    <h2 class="text-lg font-semibold text-gray-900">Select Image</h2>
                    <button type="button" x-on:click="closeModal()"
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 min-h-0 overflow-hidden p-4">
                    <div x-key="modalKey">
                        @livewire('media-picker', [], key('featured-image-picker'))
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>