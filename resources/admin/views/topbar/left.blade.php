<div class="of-topbar-left">
    <a href="{{ option('site_url') ?: url('/') }}" target="_blank" class="of-topbar-site-link" title="Visit site">
        <img 
            src="{{ option('site_favicon') ? asset('storage/' . option('site_favicon')) : asset('themes/default/assets/onflaude-favicon.svg') }}" 
            class="of-topbar-favicon" 
            alt="Site icon"
        >
        <span class="of-topbar-site-name">{{ option('site_name', 'OnFlaude') }}</span>
    </a>

    <div class="of-topbar-divider"></div>

    <a href="#" class="of-topbar-comments" title="Comments (coming soon)">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
        </svg>
        <span class="of-topbar-comments-count">0</span>
    </a>

    <div class="of-topbar-divider"></div>

    <div class="of-topbar-add-new" x-data="{ open: false }">
        <button x-on:click="open = !open" class="of-topbar-add-btn">
            <p class="of-topbar-add-btn-text">+ Add New</p>
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>
        <div x-show="open" x-on:click.outside="open = false" x-transition class="of-topbar-add-dropdown">
            <a href="{{ url(option('admin_path', 'admin') . '/posts/create') }}">Post</a>
            <a href="{{ url(option('admin_path', 'admin') . '/pages/create') }}">Page</a>
            <a href="{{ url(option('admin_path', 'admin') . '/media') }}">Media</a>
            <a href="{{ url(option('admin_path', 'admin') . '/users/create') }}">User</a>
        </div>
    </div>
</div>