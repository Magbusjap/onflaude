<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [FrontendController::class, 'home'])->name('home');

// Blog
Route::get('/blog', [FrontendController::class, 'posts'])->name('posts');
Route::get('/blog/{slug}', [FrontendController::class, 'post'])->name('post');

// fixed URL, never changes
Route::get('/onflaude-recovery', function () {
    return view('recovery');
})->name('recovery');

Route::post('/onflaude-recovery', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'secret' => 'required|string',
        'new_path' => 'required|string|regex:/^[a-z0-9\-]+$/',
    ]);

    // The secret key is the first 16 characters of the APP_KEY after "base64:"
    $appKey = substr(config('app.key'), 7, 16); // base64:XXXX... → XXXX...
    
    if ($request->secret !== $appKey) {
        return back()->withErrors(['secret' => 'Invalid secret key']);
    }

    set_option('admin_path', $request->new_path, 'system');

    return redirect('/' . $request->new_path)->with('success', 
        'Admin path reset to: /' . $request->new_path
    );
})->name('recovery.post');

// Pages — уuniversal catch-all, last
Route::get('/{slug}', [FrontendController::class, 'page'])->name('page');


