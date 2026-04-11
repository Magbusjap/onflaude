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

        return view('frontend.home', compact('page', 'posts'));
    }

    public function posts()
    {
        $posts = Post::where('status', 'published')
            ->with(['author', 'categories'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('frontend.posts', compact('posts'));
    }

    public function post(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->with(['author', 'categories', 'tags'])
            ->firstOrFail();

        return view('frontend.post', compact('post'));
    }

    // Slug = URL friendly string, e.g. "about-us", "contact", "services"
    public function page(string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('frontend.page', compact('page'));
    }
}