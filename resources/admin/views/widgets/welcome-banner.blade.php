@if($visible)
<div class="of-welcome">
    <div class="of-welcome__inner">
        <div class="of-welcome__body">

            <div class="of-welcome__icon">
                @if($timeOfDay === 'morning')
                    {{-- Sunrise / Morning --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none"
                         viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3v1m0 16v1M4.22 4.22l.707.707M18.364 18.364l.707.707M1 12h1m20 0h1M4.22 19.778l.707-.707M18.364 5.636l.707-.707M12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 20h18"/>
                    </svg>
                @elseif($timeOfDay === 'afternoon')
                    {{-- Sun / Afternoon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none"
                         viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                        <circle cx="12" cy="12" r="5"/>
                        <path stroke-linecap="round"
                              d="M12 2v2m0 16v2M2 12h2m16 0h2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M4.93 19.07l1.41-1.41m11.32-11.32 1.41-1.41"/>
                    </svg>
                @elseif($timeOfDay === 'evening')
                    {{-- Moon / Evening --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none"
                         viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                @elseif($timeOfDay === 'night')
                    {{-- Stars / Night --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none"
                        viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5z"/>
                    </svg>
                @endif
            </div>

            <div>
                <h2 class="of-welcome__title">
                    {{ $greeting }}, {{ Str::before(auth()->user()->name, ' ') }}
                </h2>
                <p class="of-welcome__subtitle">With care. by OnFlaude</p>
                <p class="of-welcome__desc">
                    Get started by creating your first page or post.
                    <a href="#">Read the docs →</a>
                </p>
            </div>
        </div>

        <button wire:click="dismiss" class="of-welcome__dismiss" title="Dismiss">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="of-welcome__actions">
        <a href="{{ url(option('admin_path','admin') . '/pages/create') }}" class="of-welcome__btn">+ New Page</a>
        <a href="{{ url(option('admin_path','admin') . '/posts/create') }}" class="of-welcome__btn">+ New Post</a>
        <a href="{{ url(option('admin_path','admin') . '/settings') }}" class="of-welcome__btn">⚙ Settings</a>
    </div>
</div>
@endif