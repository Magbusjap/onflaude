<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\HttpFoundation\Response;

/**
 * Injects the OnFlaude admin bar into HTML responses on the public site
 * for authenticated users.
 *
 * Platform-level component from resources/admin-bar/ — not part of any theme.
 * Injected as inline <style>+markup+<script> before </body>.
 *
 * Scope: text/html responses, authenticated, GET. Filament admin paths are
 * skipped to avoid double toolbars.
 */
class InjectAdminBar
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldInject($request, $response)) {
            return $response;
        }

        $html = $response->getContent();
        $injection = $this->buildInjection($request);

        if (str_contains($html, '</body>')) {
            $html = str_replace('</body>', $injection . '</body>', $html);
        } else {
            $html .= $injection;
        }

        $response->setContent($html);

        return $response;
    }

    protected function shouldInject(Request $request, Response $response): bool
    {
        if (! auth()->check()) return false;
        if (! $request->isMethod('GET')) return false;
        if ($response->getStatusCode() !== 200) return false;

        $contentType = $response->headers->get('Content-Type', '');
        if (! str_contains($contentType, 'text/html')) return false;

        $adminPath = trim(option('admin_path', 'admin'), '/');
        if ($adminPath && $request->is($adminPath, $adminPath . '/*')) return false;

        return true;
    }

    protected function buildInjection(Request $request): string
    {
        $base = base_path('resources/admin-bar');
        $css = @file_get_contents($base . '/admin-bar.css') ?: '';
        $js  = @file_get_contents($base . '/admin-bar.js')  ?: '';

        $data = [
            'post' => $request->route('post'),
            'page' => $request->route('page'),
        ];
        $markup = Blade::render(
            file_get_contents($base . '/admin-bar.blade.php'),
            $data
        );

        return "<style id=\"of-admin-bar-css\">{$css}</style>"
             . $markup
             . "<script id=\"of-admin-bar-js\">{$js}</script>";
    }
}
