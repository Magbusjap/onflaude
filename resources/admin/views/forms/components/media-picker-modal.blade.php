<div x-on:media-picked.window="$dispatch('close-modal', { id: 'choose_imageAction-modal' })">
    @livewire('media-picker', [], key('media-picker-modal'))
</div>