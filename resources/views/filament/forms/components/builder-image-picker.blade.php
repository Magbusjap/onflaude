<div
    x-data="{
        previewUrl: '',
        mediaModalOpen: false,
        instanceId: '',
        openModal() {
            this.mediaModalOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.mediaModalOpen = false;
            document.body.style.overflow = '';
        }
    }"
    x-on:keydown.escape.window="closeModal()"
    x-init="
        instanceId = 'block-' + Math.random().toString(36).substr(2, 9);
        $nextTick(() => {
            const block = $el.closest('[wire\\:key*=\'.item\']');
            if (block) {
                const hidden = block.querySelector('input[type=hidden][name*=media_id], input[type=hidden][wire\\:model*=media_id]');
                if (hidden && hidden.value) {
                    fetch('/media/url/' + hidden.value)
                        .then(r => r.json())
                        .then(data => { if (data.url) previewUrl = data.url; })
                        .catch(() => {});
                }
            }
        });
        window.addEventListener('media-picked.' + instanceId, (e) => {
            previewUrl = e.detail.url;
            closeModal();
            let blockKey = null;
            let el = $el;
            while (el) {
                const key = el.getAttribute && el.getAttribute('wire:key');
                if (key && key.includes('.item')) { blockKey = key; break; }
                el = el.parentElement;
            }
            if (!blockKey) return;
            const parts = blockKey.split('.');
            const uuidIndex = parts.findIndex(p => p.match(/^[0-9a-f-]{36}$/));
            if (uuidIndex === -1) return;
            const uuid = parts[uuidIndex];
            const statePath = 'content.' + uuid + '.data.media_id';
            const editPost = Livewire.all().find(c => c.name === 'app.filament.resources.post-resource.pages.edit-post');
            if (editPost) editPost.$wire.setBuilderImageId(statePath, e.detail.id);
        });
        $watch('mediaModalOpen', value => {
            if (value) {
                setTimeout(() => {
                    const modals = document.querySelectorAll('.of-builder-picker-modal');
                    let modal = null;
                    modals.forEach(m => { if (m.offsetParent !== null) modal = m; });
                    if (!modal) return;
                    const wireEl = modal.querySelector('[wire\\:id]');
                    if (!wireEl) return;
                    const picker = Livewire.find(wireEl.getAttribute('wire:id'));
                    if (picker) { picker.resetState(); picker.setInstanceId(instanceId); }
                }, 100);
            }
        });
    ">

    {{-- Preview --}}
    <template x-if="previewUrl">
        <div class="rounded-md overflow-hidden border border-gray-200 mb-2 relative">
            <img :src="previewUrl" class="w-full object-cover max-h-40" />
        </div>
    </template>
    <template x-if="!previewUrl">
        <div class="mb-2 rounded-md border-2 border-dashed border-gray-200 bg-gray-50
            flex items-center justify-center text-gray-400 text-sm"
            style="height: 100px;">
            No image selected
        </div>
    </template>

    {{-- Button --}}
    <button type="button" x-on:click="openModal()"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg
               border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Choose from Media Library
    </button>

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
            class="of-media-modal fixed inset-0 flex items-center justify-center p-4"
            style="display:none;">

            <div class="absolute inset-0 bg-black/50" x-on:click="closeModal()"></div>

            <div class="of-builder-picker-modal relative bg-white rounded-xl shadow-2xl w-full flex flex-col"
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
                    <div x-show="mediaModalOpen">
                        @livewire('media-picker', ['instanceId' => 'default'], key('builder-image-picker-' . uniqid()))
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>