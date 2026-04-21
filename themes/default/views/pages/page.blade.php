{{--
    Default theme — generic CMS page

    Rendered for /{slug} when a Page matches. Vars: $page.
--}}
@extends('theme::layout')

@section('title', $page->seo_title ?? $page->title . ' — ' . option('site_name', 'OnFlaude'))
@section('description', $page->seo_description ?? $page->excerpt)

@section('content')
    <article class="max-w-3xl mx-auto">
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-[#003893] mb-4">
                {{ $page->title }}
            </h1>
        </header>

        <div class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </div>
    </article>
@endsection
