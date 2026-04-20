<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
    </x-filament-panels::form>
</x-filament-panels::page>

@push('scripts')
<script>
    // If the admin_path has changed, we need to update the browser's URL 
    // without reloading the page
    // This prevents the "Back" button from returning to the old admin_path → 404
    if (window.history.replaceState) {
        window.history.replaceState(null, '', window.location.href);
    }
</script>
@endpush