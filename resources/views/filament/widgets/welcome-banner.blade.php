@if($visible)
<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="of-welcome"
>
    <div class="of-welcome__inner">
        <div class="of-welcome__body">
            <div class="of-welcome__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                </svg>
            </div>
            <div>
                <h2 class="of-welcome__title">Welcome to OnFlaude CMS</h2>
                <p class="of-welcome__desc">
                    Get started by creating your first page or post.
                    <a href="#">Read the docs →</a>
                </p>
            </div>
        </div>

        <button
            wire:click="dismiss"
            x-on:click="show = false"
            class="of-welcome__dismiss"
            title="Dismiss"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
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