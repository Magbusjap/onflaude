@extends('frontend.layout')

@section('title', $post->seo_title ?? $post->title . ' — ' . option('site_name', 'OnFlaude'))
@section('description', $post->seo_description ?? $post->excerpt)

@section('content')
    <article class="max-w-3xl mx-auto">
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-[#003893] mb-4">
                {{ $post->title }}
            </h1>
            <div class="text-sm text-gray-400 flex flex-wrap gap-4">
                @if($post->published_at)
                    <span>{{ $post->published_at->format('d M Y') }}</span>
                @endif
                @if($post->author)
                    <span>By {{ $post->author->name }}</span>
                @endif
                @if($post->categories->count())
                    <span>
                        @foreach($post->categories as $category)
                            <span class="text-[#0097D7]">{{ $category->name }}</span>
                        @endforeach
                    </span>
                @endif
            </div>
        </header>

        <div class="prose prose-lg max-w-none">
            {!! $post->content !!}
        </div>

        @if($post->tags->count())
            <footer class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </footer>
        @endif
    </article>
@endsection
