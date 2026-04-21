{{--
    Default theme — category archive

    Rendered for /category/{slug}. Vars: $category, $posts.
--}}
@extends('theme::layout')

@section('title', $category->name . ' — ' . option('site_name', 'OnFlaude'))

@section('content')
    <h1 class="text-4xl font-bold text-[#003893] mb-2">{{ $category->name }}</h1>
    @if($category->description)
        <p class="text-gray-500 mb-8">{{ $category->description }}</p>
    @endif

    @forelse($posts as $post)
        <article class="border-b border-gray-200 py-8">
            <h2 class="text-2xl font-semibold mb-2">
                <a href="{{ route('post', $post->slug) }}" class="text-[#003893] hover:text-[#0097D7]">
                    {{ $post->title }}
                </a>
            </h2>
            <div class="text-sm text-gray-400 mb-3">
                {{ $post->published_at?->format('d M Y') }}
                @if($post->author)· {{ $post->author->name }}@endif
            </div>
            @if($post->excerpt)
                <p class="text-gray-600">{{ $post->excerpt }}</p>
            @endif
            <a href="{{ route('post', $post->slug) }}" class="inline-block mt-4 text-sm text-[#0097D7] hover:underline">
                Read more →
            </a>
        </article>
    @empty
        <p class="text-gray-500">No posts in this category.</p>
    @endforelse

    <div class="mt-8">{{ $posts->links() }}</div>
@endsection
