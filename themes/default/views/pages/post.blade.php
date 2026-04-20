@extends('theme::layout')

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
            @foreach($post->content ?? [] as $block)
                @switch($block['type'])
                    @case('heading')
                        <{{ $block['data']['level'] }}>{{ $block['data']['text'] }}</{{ $block['data']['level'] }}>
                        @break
                    @case('text')
                        <div>{!! $block['data']['content'] !!}</div>
                        @break
                    @case('markdown')
                        <div>{!! \Illuminate\Support\Str::markdown($block['data']['content']) !!}</div>
                        @break
                    @case('quote')
                        <blockquote>
                            <p>{{ $block['data']['text'] }}</p>
                            @if($block['data']['author'] ?? null)
                                <cite>— {{ $block['data']['author'] }}</cite>
                            @endif
                        </blockquote>
                        @break
                    @case('image')
                        @php $media = \App\Models\Media::find($block['data']['media_id']) @endphp
                        @if($media)
                            <figure class="text-center">
                                <img src="{{ $media->url }}"
                                    class="mx-auto block"
                                    alt="{{ $block['data']['caption'] ?? '' }}"
                                    @if($block['data']['proportional'] ?? true)
                                        @if($block['data']['width'] ?? null)
                                            style="width: {{ $block['data']['width'] }}px; height: auto;"
                                        @endif
                                    @else
                                        style="
                                            {{ isset($block['data']['width']) ? 'width: ' . $block['data']['width'] . 'px;' : '' }}
                                            {{ isset($block['data']['height']) ? 'height: ' . $block['data']['height'] . 'px;' : '' }}
                                            object-fit: cover;
                                        "
                                    @endif
                                />
                                @if($block['data']['caption'] ?? null)
                                    <figcaption class="text-sm text-gray-500 mt-2">{{ $block['data']['caption'] }}</figcaption>
                                @endif
                            </figure>
                        @endif
                        @break
                    @case('image_text')
                        @php $media = \App\Models\Media::find($block['data']['media_id']) @endphp
                        <div class="flex gap-6 items-start {{ ($block['data']['position'] ?? 'left') === 'right' ? 'flex-row-reverse' : '' }}">
                            @if($media)
                                <img src="{{ $media->url }}"
                                    alt=""
                                    style="width: {{ $block['data']['width'] ?? 300 }}px; flex-shrink: 0;"
                                />
                            @endif
                            <div>{!! $block['data']['text'] !!}</div>
                        </div>
                        @break
                @endswitch
            @endforeach
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
