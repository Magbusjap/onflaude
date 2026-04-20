<x-filament-panels::page>
<div x-data="{ screenOpen: false }" style="position: relative;">

    {{-- Screen Options button -- positioned top right --}}
    <div style="position: absolute; top: -4.5rem; right: 0; z-index: 10;">
        <button
            x-on:click="screenOpen = !screenOpen"
            class="of-screen-options-btn"
        >
            Screen Options <span x-text="screenOpen ? '▴' : '▾'">▾</span>
        </button>
    </div>

    {{-- Screen Options Panel --}}
    <div x-show="screenOpen" x-transition class="of-screen-options">
        <p class="of-screen-options__title">Show on screen:</p>
        <div class="of-screen-options__list"
             x-data="{ activeKeys: {{ json_encode($this->getActiveWidgetKeys()) }} }">
            @foreach(static::getAvailableWidgets() as $key => $widget)
            <label class="of-screen-options__item">
                <input
                    type="checkbox"
                    :checked="activeKeys.includes('{{ $key }}')"
                    x-on:change="
                        activeKeys.includes('{{ $key }}')
                            ? activeKeys = activeKeys.filter(k => k !== '{{ $key }}')
                            : activeKeys.push('{{ $key }}')
                    "
                >
                {{ $widget['label'] }}
            </label>
            @endforeach

            <button
                type="button"
                class="of-screen-options__save"
                x-on:click="$wire.saveWidgetPreferences(activeKeys); screenOpen = false"
            >
                Apply
            </button>
        </div>
    </div>

    {{-- Widgets --}}
    <x-filament-widgets::widgets
        :widgets="$this->getVisibleWidgets()"
        :columns="$this->getColumns()"
    />

</div>
</x-filament-panels::page>