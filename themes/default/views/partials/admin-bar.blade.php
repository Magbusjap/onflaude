@auth
<div class="of-admin-bar" id="of-admin-bar">
    <div class="of-admin-bar__inner">

        {{-- OnFlaude Menu --}}
        <div class="of-admin-bar__item of-admin-bar__item--dropdown">
            <button class="of-admin-bar__trigger">
                <img src="{{ option('site_favicon') ? asset('storage/' . option('site_favicon')) : asset('themes/default/assets/onflaude-favicon.svg') }}"
                     class="of-admin-bar__favicon" alt="OnFlaude">
            </button>
            <div class="of-admin-bar__dropdown">
                <a href="https://onflaude.com/about" target="_blank">About OnFlaude</a>
                <a href="https://onflaude.com/docs" target="_blank">Documentation</a>
                <a href="https://onflaude.com/learn" target="_blank">Learn OnFlaude</a>
                <a href="https://onflaude.com/support" target="_blank">Support</a>
            </div>
        </div>

        {{-- Admin Panel --}}
        <div class="of-admin-bar__item of-admin-bar__item--dropdown">
            <button class="of-admin-bar__trigger">Admin Panel</button>
            <div class="of-admin-bar__dropdown">
                <a href="{{ url(option('admin_path', 'admin')) }}">Go to Admin Panel</a>
                <a href="{{ url(option('admin_path', 'admin') . '/plugins') }}">Plugins</a>
                <a href="{{ url(option('admin_path', 'admin') . '/themes') }}">Themes</a>
            </div>
        </div>

        {{-- Edit Site --}}
        <a href="{{ url(option('admin_path', 'admin') . '/settings') }}" class="of-admin-bar__item">
            Edit Site
        </a>

        {{-- Comments --}}
        <a href="{{ url(option('admin_path', 'admin') . '/comments') }}" class="of-admin-bar__item">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
            </svg>
            <span>0</span>
        </a>

        {{-- Add New --}}
        <div class="of-admin-bar__item of-admin-bar__item--dropdown">
            <button class="of-admin-bar__trigger">+ New</button>
            <div class="of-admin-bar__dropdown">
                <a href="{{ url(option('admin_path', 'admin') . '/posts/create') }}">Post</a>
                <a href="{{ url(option('admin_path', 'admin') . '/media') }}">Media</a>
                <a href="{{ url(option('admin_path', 'admin') . '/pages/create') }}">Page</a>
                <a href="{{ url(option('admin_path', 'admin') . '/users/create') }}">User</a>
            </div>
        </div>

        {{-- Edit current page/post --}}
        @isset($post)
            <a href="{{ url(option('admin_path', 'admin') . '/posts/' . $post->id . '/edit') }}" class="of-admin-bar__item of-admin-bar__item--edit">
                Edit Post
            </a>
        @endisset
        @isset($page)
            <a href="{{ url(option('admin_path', 'admin') . '/pages/' . $page->id . '/edit') }}" class="of-admin-bar__item of-admin-bar__item--edit">
                Edit Page
            </a>
        @endisset

    </div>
</div>
@endauth
