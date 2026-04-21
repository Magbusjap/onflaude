{{--
    Default theme — blog index

    Rendered for /blog. Vars: $posts (paginated).
--}}
@extends('theme::layout')

@section('title', 'Blog — ' . option('site_name', 'OnFlaude'))

@section('content')
    <h1 class="text-4xl font-bold text-[#003893] mb-8">Blog</h1>

    @forelse($posts as $post)
        <article class="border-b border-gray-200 py-8">
            <h2 class="text-2xl font-semibold mb-2">
                <a href="{{ route('post', $post->slug) }}"
                    class="text-[#003893] hover:text-[#0097D7]">>
                    {{ $post->title }}
                </a>
            </h2>
            <div class="text-sm text-gray-400 mb-3">
                {{ $post->published_at?->format('d M Y') }}
                @if($post->author)
                    · {{ $post->author->name }}
                @endif
                @if($post->categories->count())
                    <span>
                        @foreach($post->categories as $category)
                            <a href="{{ route('category', $category->slug) }}" class="text-[#0097D7] hover:underline">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </span>
                @endif
            </div>
            @if($post->excerpt)
                <p class="text-gray-600">{{ $post->excerpt }}</p>
            @endif
            <a href="{{ route('post', $post->slug) }}" 
                class="inline-block mt-4 text-sm text-[#0097D7] hover:underline">
                Read more →
            </a>
        </article>
    @empty
        <p class="text-gray-500">No posts yet.</p>
    @endforelse

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
@endsection
