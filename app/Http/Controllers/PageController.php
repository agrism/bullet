<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PageController extends Controller
{
    public function show(Request $request, string $path = '')
    {
        $path = trim($path, '/');

        // Detect locale from URL prefix
        $locale = 'lv';
        $subpath = $path;
        if ($path === 'en' || str_starts_with($path, 'en/')) {
            $locale = 'en';
            $subpath = trim(preg_replace('/^en\/?/', '', $path), '/');
        } elseif ($path === 'ru' || str_starts_with($path, 'ru/')) {
            $locale = 'ru';
            $subpath = trim(preg_replace('/^ru\/?/', '', $path), '/');
        }

        app()->setLocale($locale);

        $map = config('slug_map', []);

        // Find canonical key in slug_map
        $canonicalKey = null;
        if (array_key_exists($subpath, $map)) {
            $canonicalKey = $subpath;
        } else {
            foreach ($map as $key => $locs) {
                if (($locs['en'] ?? null) === $subpath || ($locs['ru'] ?? null) === $subpath || ($locs['lv'] ?? null) === $subpath) {
                    $canonicalKey = $key;
                    break;
                }
            }
        }

        // Handle 301 Redirects for legacy/mismatched URLs if mapped
        if ($canonicalKey !== null) {
            $expectedSlug = $map[$canonicalKey][$locale] ?? $canonicalKey;
            if ($subpath !== $expectedSlug) {
                $targetUrl = $locale === 'lv'
                    ? ($expectedSlug === '' ? '/' : "/{$expectedSlug}/")
                    : ($expectedSlug === '' ? "/{$locale}/" : "/{$locale}/{$expectedSlug}/");
                return redirect($targetUrl, 301);
            }

            $lvTarget = $map[$canonicalKey]['lv'] ?? $canonicalKey;
            $enTarget = $map[$canonicalKey]['en'] ?? $canonicalKey;
            $ruTarget = $map[$canonicalKey]['ru'] ?? $canonicalKey;

            $alternateUrls = [
                'lv' => url('/' . ($lvTarget ? $lvTarget . '/' : '')),
                'en' => url('/en/' . ($enTarget ? $enTarget . '/' : '')),
                'ru' => url('/ru/' . ($ruTarget ? $ruTarget . '/' : '')),
                'x-default' => url('/' . ($lvTarget ? $lvTarget . '/' : '')),
            ];
        } else {
            $expectedSlug = $subpath;
            $alternateUrls = [
                'lv' => url('/' . ($subpath ? $subpath . '/' : '')),
                'en' => url('/en/' . ($subpath ? $subpath . '/' : '')),
                'ru' => url('/ru/' . ($subpath ? $subpath . '/' : '')),
                'x-default' => url('/' . ($subpath ? $subpath . '/' : '')),
            ];
        }

        // 1. Check if page exists in MySQL Database with localized slug (cached for performance)
        $cacheKey = "page_{$locale}_" . ($expectedSlug === '' ? '__home__' : $expectedSlug);
        $page = Cache::remember($cacheKey, 86400, function () use ($expectedSlug, $locale) {
            return Page::where('slug', $expectedSlug)->where('locale', $locale)->first();
        });

        if ($page) {
            return view('page', [
                'locale' => $page->locale,
                'slug' => $page->slug,
                'title' => $page->title,
                'description' => $page->description,
                'body_class' => $page->body_class,
                'header' => $page->header_html,
                'content' => $page->content_html,
                'footer' => $page->footer_html,
                'path' => $path,
                'alternate_urls' => $alternateUrls,
            ]);
        }

        abort(404);
    }
}

