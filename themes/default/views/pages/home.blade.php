{{--
    Default theme — home page

    Rendered for /. Vars: $page, $posts.
--}}
@extends('theme::layout')

@section('title', option('site_name', 'OnFlaude'))

@section('content')
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-[#003893] mb-4">
            {{ option('site_name', 'OnFlaude') }}
        </h1>
        <p class="text-gray-600 text-lg">
            {{ option('site_description', 'A fast and secure CMS built on Laravel.') }}
        </p>
    </div>

    @if($posts->count())
        <h2 class="text-2xl font-bold mb-6">Latest Posts</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
                <article class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <h3 class="text-lg font-semibold mb-2">
                        <a href="/blog/{{ $post->slug }}" class="text-[#003893] hover:text-[#0097D7]">
                            {{ $post->title }}
                        </a>
                    </h3>
                    @if($post->excerpt)
                        <p class="text-gray-600 text-sm mb-4">{{ $post->excerpt }}</p>
                    @endif
                    <div class="text-xs text-gray-400">
                        {{ $post->published_at?->format('d M Y') }}
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
