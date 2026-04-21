<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        $page = Page::where('slug', 'home')
            ->where('status', 'published')
            ->first();

        $posts = Post::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();

        return view('theme::pages.home', compact('page', 'posts'));
    }

    public function posts()
    {
        $posts = Post::where('status', 'published')
            ->with(['author', 'categories'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('theme::pages.blog', compact('posts'));
    }

    public function post(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->with(['author', 'categories', 'tags'])
            ->firstOrFail();

        return view('theme::pages.post', compact('post'));
    }

    public function category(string $slug)
    {
        $category = \App\Models\Category::where('slug', $slug)->firstOrFail();

        $posts = Post::where('status', 'published')
            ->whereHas('categories', fn($q) => $q->where('slug', $slug))
            ->with(['author', 'categories'])
            ->orderBy('published_at', 'desc')
            ->paginate(option('posts_per_page', 10));

        return view('theme::pages.category', compact('category', 'posts'));
    }

    public function tag(string $slug)
    {
        $tag = \App\Models\Tag::where('slug', $slug)->firstOrFail();

        $posts = Post::where('status', 'published')
            ->whereHas('tags', fn($q) => $q->where('slug', $slug))
            ->with(['author', 'categories'])
            ->orderBy('published_at', 'desc')
            ->paginate(option('posts_per_page', 10));

        return view('theme::pages.tag', compact('tag', 'posts'));
    }

    // Slug = URL friendly string, e.g. "about-us", "contact", "services"
    public function page(string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('theme::pages.page', compact('page'));
    }
}