@if($visible)
<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fi-wi-welcome rounded-xl p-6 mb-2"
    style="background: linear-gradient(135deg, #003893 0%, #0097D7 100%);"
>
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <div style="width:48px; height:48px; background:rgba(255,255,255,0.15); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                </svg>
            </div>
            <div>
                <h2 style="color:white; font-size:1.125rem; font-weight:700; margin:0 0 4px;">
                    Welcome to OnFlaude CMS
                </h2>
                <p style="color:rgba(255,255,255,0.8); font-size:0.875rem; margin:0;">
                    Get started by creating your first page or post. Need help?
                    <a href="#" style="color:white; text-decoration:underline;">Read the docs →</a>
                </p>
            </div>
        </div>

        {{-- Крестик --}}
        <button
            wire:click="dismiss"
            x-on:click="show = false"
            style="color:rgba(255,255,255,0.7); background:none; border:none; cursor:pointer; padding:4px; line-height:1;"
            title="Dismiss"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Быстрые действия --}}
    <div style="display:flex; gap:12px; margin-top:20px; flex-wrap:wrap;">
        <a href="{{ url(option('admin_path','admin') . '/pages/create') }}"
           style="background:rgba(255,255,255,0.2); color:white; padding:8px 16px; border-radius:8px; font-size:0.875rem; font-weight:500; text-decoration:none; backdrop-filter:blur(4px);">
            + New Page
        </a>
        <a href="{{ url(option('admin_path','admin') . '/posts/create') }}"
           style="background:rgba(255,255,255,0.2); color:white; padding:8px 16px; border-radius:8px; font-size:0.875rem; font-weight:500; text-decoration:none; backdrop-filter:blur(4px);">
            + New Post
        </a>
        <a href="{{ url(option('admin_path','admin') . '/settings') }}"
           style="background:rgba(255,255,255,0.2); color:white; padding:8px 16px; border-radius:8px; font-size:0.875rem; font-weight:500; text-decoration:none; backdrop-filter:blur(4px);">
            ⚙ Settings
        </a>
    </div>
</div>
@endif
