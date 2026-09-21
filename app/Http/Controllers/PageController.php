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

        // Helper: link transformation function
        $transformLinks = function (string $html) use ($locale, $map) {
            $targetUrlFor = function(string $canonical) use ($map, $locale) {
                $locSlug = $map[$canonical][$locale] ?? $canonical;
                if ($locale === 'lv') {
                    return $locSlug === '' ? '/' : "/{$locSlug}/";
                } else {
                    return $locSlug === '' ? "/{$locale}/" : "/{$locale}/{$locSlug}/";
                }
            };

            // Replace empty href in breadcrumbs
            $homeUrl = $locale === 'lv' ? '/' : "/{$locale}/";
            $html = preg_replace('/href=([\'"])\s*([\'"])/i', 'href=' . '$1' . $homeUrl . '$2', $html);

            $canonicalKeys = array_keys($map);
            usort($canonicalKeys, fn($a, $b) => strlen($b) <=> strlen($a));

            // Legacy alias map
            $aliases = [
                'services' => 'juridiskie-pakalpojumi',
                'practice-areas' => 'jurista-darbibas-nozare',
                'lawyers' => 'zverinati-advokati',
            ];

            return preg_replace_callback('/href=([\'"])(.*?)([\'"])/i', function($m) use ($canonicalKeys, $map, $locale, $targetUrlFor, $aliases) {
                $quote = $m[1];
                $href = $m[2];

                if (str_starts_with($href, '#') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:') || str_starts_with($href, 'javascript:')) {
                    return $m[0];
                }

                if (preg_match('/\.(css|js|svg|jpg|jpeg|png|webp|gif|pdf|ico)(\?.*)?$/i', $href)) {
                    return $m[0];
                }

                $path = $href;
                if (preg_match('/^https?:\/\/bullet\.legal(\/.*)?$/i', $href, $urlMatches)) {
                    $path = $urlMatches[1] ?? '/';
                } elseif (!str_starts_with($href, '/')) {
                    return $m[0];
                }

                $cleanPath = parse_url($path, PHP_URL_PATH) ?? '/';
                $query = parse_url($path, PHP_URL_QUERY);
                $fragment = parse_url($path, PHP_URL_FRAGMENT);

                $trimmed = trim($cleanPath, '/');
                if ($trimmed === 'en' || str_starts_with($trimmed, 'en/')) {
                    $trimmed = trim(substr($trimmed, 2), '/');
                } elseif ($trimmed === 'ru' || str_starts_with($trimmed, 'ru/')) {
                    $trimmed = trim(substr($trimmed, 2), '/');
                }

                if (isset($aliases[$trimmed])) {
                    $trimmed = $aliases[$trimmed];
                }

                foreach ($canonicalKeys as $canKey) {
                    if ($trimmed === $canKey ||
                        $trimmed === ($map[$canKey]['en'] ?? null) ||
                        $trimmed === ($map[$canKey]['ru'] ?? null)) {
                        
                        $newTarget = $targetUrlFor($canKey);
                        if ($query) $newTarget .= '?' . $query;
                        if ($fragment) $newTarget .= '#' . $fragment;
                        return 'href=' . $quote . $newTarget . $quote;
                    }
                }

                return $m[0];
            }, $html);
        };

        // Clean up sizes="auto, ...", replace external upload URLs with local /uploads/, and add referrerpolicy
        $cleanImages = function ($markup) {
            $markup = preg_replace('/https?:\/\/bullet\.legal\/wp-content\/uploads\//i', '/uploads/', $markup);
            $markup = preg_replace('/sizes=[\'"]auto,[^\'"]*[\'"]/i', 'sizes="(max-width: 768px) 100vw, 400px"', $markup);
            $markup = preg_replace('/<img(?![^>]*referrerpolicy)/i', '<img referrerpolicy="no-referrer"', $markup);
            return $markup;
        };

        // 3-Language switcher construction
        $buildLangSwitcher = function ($headerMarkup) use ($locale, $canonicalKey, $map, $subpath) {
            $currLabel = strtoupper($locale);
            $can = $canonicalKey ?? $subpath;

            $lvSlug = $map[$can]['lv'] ?? $can;
            $enSlug = $map[$can]['en'] ?? $can;
            $ruSlug = $map[$can]['ru'] ?? $can;

            $langs = [
                'LV' => $lvSlug === '' ? '/' : "/{$lvSlug}/",
                'EN' => $enSlug === '' ? '/en/' : "/en/{$enSlug}/",
                'RU' => $ruSlug === '' ? '/ru/' : "/ru/{$ruSlug}/",
            ];

            $optionsHtml = '';
            foreach ($langs as $l => $url) {
                $isActive = (strtoupper($locale) === $l);
                $optionsHtml .= '<a href="' . $url . '" class="header__lang header__lang--' . strtolower($l) . ($isActive ? ' active current-lang' : '') . '">' . $l . '</a>';
            }

            $pllHtml = '<div class="header__pll">'
                . '<div class="header__current-wrap">'
                . '<svg class="header__icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">'
                . '<path d="M10 18.4792C8.83333 18.4792 7.72917 18.2569 6.6875 17.8125C5.65972 17.3681 4.76389 16.7639 4 16C3.23611 15.2361 2.63194 14.3403 2.1875 13.3125C1.74306 12.2708 1.52083 11.1667 1.52083 10C1.52083 8.81944 1.74306 7.71528 2.1875 6.6875C2.63194 5.65972 3.23611 4.76389 4 4C4.76389 3.23611 5.65972 2.63194 6.6875 2.1875C7.72917 1.74305 8.83333 1.52083 10 1.52083C11.1806 1.52083 12.2847 1.74305 13.3125 2.1875C14.3403 2.63194 15.2361 3.23611 16 4C16.7639 4.76389 17.3681 5.66667 17.8125 6.70833C18.2569 7.73611 18.4792 8.83333 18.4792 10C18.4792 11.1806 18.2569 12.2847 17.8125 13.3125C17.3681 14.3403 16.7639 15.2361 16 16C15.2361 16.7639 14.3403 17.3681 13.3125 17.8125C12.2847 18.2569 11.1806 18.4792 10 18.4792ZM9.14583 16.7083V15.0625C8.6875 15.0625 8.29167 14.8958 7.95833 14.5625C7.63889 14.2292 7.47917 13.8333 7.47917 13.375V12.5208L3.4375 8.45833C3.38194 8.72222 3.33333 8.97917 3.29167 9.22917C3.26389 9.47917 3.25 9.73611 3.25 10C3.25 11.7083 3.80556 13.2083 4.91667 14.5C6.04167 15.7778 7.45139 16.5139 9.14583 16.7083ZM14.9792 14.5625C15.2708 14.2569 15.5278 13.9236 15.75 13.5625C15.9722 13.2014 16.1528 12.8264 16.2917 12.4375C16.4444 12.0486 16.5556 11.6528 16.625 11.25C16.7083 10.8333 16.75 10.4167 16.75 10C16.75 8.61111 16.3681 7.34722 15.6042 6.20833C14.8403 5.06944 13.8194 4.24305 12.5417 3.72917V4.0625C12.5417 4.52083 12.375 4.92361 12.0417 5.27083C11.7083 5.60417 11.3125 5.77083 10.8542 5.77083H9.14583V7.45833C9.14583 7.69444 9.0625 7.89583 8.89583 8.0625C8.72917 8.21528 8.52778 8.29167 8.29167 8.29167H6.625V10H11.7083C11.9444 10 12.1389 10.0833 12.2917 10.25C12.4583 10.4167 12.5417 10.6111 12.5417 10.8333H13.375C13.75 13.375 14.0833 13.4861 14.375 13.7083C14.6667 13.9306 14.8681 14.2153 14.9792 14.5625Z" fill="currentColor"></path>'
                . '</svg>'
                . '<span class="header__current">'
                . $currLabel
                . '<svg class="header__arrow" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">'
                . '<path d="M9.54167 12.1875L6.33333 8.97917C6.29167 8.9375 6.25694 8.89583 6.22917 8.85417C6.20139 8.79861 6.1875 8.73611 6.1875 8.66667C6.1875 8.55555 6.22917 8.45833 6.3125 8.375C6.39583 8.27778 6.5 8.22917 6.625 8.22917H13.375C13.5 8.22917 13.6042 8.27778 13.6875 8.375C13.7708 8.45833 13.8125 8.55555 13.8125 8.66667C13.8125 8.70833 13.7639 8.8125 13.6667 8.97917L10.4583 12.1875C10.3889 12.2569 10.3125 12.3056 10.2292 12.3333C10.1597 12.3611 10.0833 12.375 10 12.375C9.91667 12.375 9.83333 12.3611 9.75 12.3333C9.68056 12.3056 9.61111 12.2569 9.54167 12.1875Z" fill="currentColor"></path>'
                . '</svg>'
                . '</span>'
                . '</div>'
                . '<div class="header__select">'
                . $optionsHtml
                . '</div>'
                . '</div>';

            return preg_replace('/<div class=[\'"]header__pll[\'"].*?<\/div>\s*<\/div>/is', $pllHtml, $headerMarkup);
        };

        // Translations helper
        $translate = function ($markup) use ($locale) {
            static $enReplacements = null;
            static $ruReplacements = null;

            $buildReplacements = function ($jsonFile) {
                if (!file_exists($jsonFile)) return [];
                $dict = json_decode(file_get_contents($jsonFile), true) ?: [];
                $replacements = [];
                foreach ($dict as $lv => $trans) {
                    $replacements[$lv] = $trans;
                    $encodedLv = htmlspecialchars($lv, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $encodedTrans = htmlspecialchars($trans, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    if ($encodedLv !== $lv) {
                        $replacements[$encodedLv] = $encodedTrans;
                    }
                    $dashLv = str_replace('–', '&#8211;', $lv);
                    if ($dashLv !== $lv) {
                        $replacements[$dashLv] = str_replace('–', '&#8211;', $trans);
                    }
                }
                uksort($replacements, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));
                return $replacements;
            };

            if ($locale === 'en') {
                if ($enReplacements === null) {
                    $enReplacements = $buildReplacements(base_path('translations_en.json'));
                }
                return strtr($markup, $enReplacements);
            } elseif ($locale === 'ru') {
                if ($ruReplacements === null) {
                    $ruReplacements = $buildReplacements(base_path('translations_ru.json'));
                }
                return strtr($markup, $ruReplacements);
            }
            return $markup;
        };

        // 2. Fallback: If EN/RU requested and LV page exists in DB, generate from LV page
        $html = '';
        $bodyClass = 'page-default';
        $title = '';
        $description = '';

        $lookupKey = $canonicalKey ?? $subpath;
        $lvPage = Page::where('slug', $lookupKey)->where('locale', 'lv')->first();
        if ($lvPage) {
            $html = $lvPage->header_html . $lvPage->content_html . $lvPage->footer_html;
            $bodyClass = $lvPage->body_class;
            $title = $lvPage->title;
            $description = $lvPage->description;
        }

        if (empty($html)) {
            $targetUrl = $lookupKey === '' ? 'https://bullet.legal/' : "https://bullet.legal/{$lookupKey}/";

            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ])->withoutRedirecting()->timeout(15)->get($targetUrl);

                if ($response->status() === 301 || $response->status() === 302) {
                    $targetUrl = rtrim($targetUrl, '/');
                    $response = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                    ])->withoutRedirecting()->timeout(15)->get($targetUrl);
                }

                if (!$response->successful()) {
                    abort(404);
                }

                $html = $response->body();
            } catch (\Exception $e) {
                abort(404, $e->getMessage());
            }
        }

        // Extract title
        preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $titleMatch);
        $title = $title ?: ($titleMatch[1] ?? ($locale === 'en' ? 'Law Office BULLET | Legal Assistance and Defense' : ($locale === 'ru' ? 'Адвокатское бюро BULLET | Юридическая помощь и защита' : 'Advokātu birojs BULLET | Juridiskā palīdzība un aizstāvība')));

        // Extract description
        preg_match('/<meta[^>]+name=[\'"]description[\'"][^>]+content=[\'"]([^\'"]*)[\'"]/is', $html, $descMatch);
        $description = $description ?: ($descMatch[1] ?? ($locale === 'en' ? 'Law Office BULLET provides legal assistance in Latvia and internationally' : ($locale === 'ru' ? 'Адвокатское бюро BULLET оказывает юридическую помощь предприятиям и частным лицам в Латвии и на международном уровне' : 'Advokātu birojs BULLET sniedz juridisko palīdzību uzņēmumiem un privātpersonām Latvijā un starptautiski')));

        // Extract body class
        preg_match('/<body[^>]+class=[\'"]([^\'"]*)[\'"]/is', $html, $bodyClassMatch);
        $bodyClass = $bodyClassMatch[1] ?? $bodyClass;

        // Extract header
        preg_match('/<header[^>]*class=[\'"][^\'"]*header[^\'"]*[\'"][^>]*>.*?<\/header>/is', $html, $headerMatch);
        $headerHtml = $headerMatch[0] ?? '';

        // Extract main content
        $contentHtml = '';
        if (preg_match('/<main[^>]*class=[\'"][^\'"]*main[^\'"]*[\'"][^>]*>(.*?)<\/main>/is', $html, $mainMatch)) {
            $contentHtml = '<main class="main" role="main">' . $mainMatch[1] . '</main>';
        } elseif (preg_match('/<main[^>]*>(.*?)<\/main>/is', $html, $mainMatch)) {
            $contentHtml = '<main class="main" role="main">' . $mainMatch[1] . '</main>';
        } elseif (preg_match('/<div[^>]+class=[\'"][^\'"]*page-content[^\'"]*[\'"][^>]*>(.*?)<\/div>\s*<div[^>]+class=[\'"][^\'"]*footer/is', $html, $pageMatch)) {
            $contentHtml = '<main class="main" role="main">' . $pageMatch[1] . '</main>';
        } else {
            $contentHtml = $html;
        }

        // Extract footer
        preg_match('/<div[^>]*class=[\'"][^\'"]*footer[^\'"]*[\'"][^>]*>.*?<\/div>\s*(?=<script|<div[^>]+id=[\'"]cmplz|<div[^>]+class=[\'"]cmplz|$)/is', $html, $footerMatch);
        $footerHtml = $footerMatch[0] ?? '';

        // Clean up footer agency credits
        $cleanFooter = function ($markup) {
            return preg_replace('/<div class=[\'"]footer__copy-right[\'"].*?<\/div>/is', '', $markup);
        };

        $headerHtml = $translate($buildLangSwitcher($cleanImages($transformLinks($headerHtml))));
        $contentHtml = $translate($cleanImages($transformLinks($contentHtml)));
        $footerHtml = $cleanFooter($translate($cleanImages($transformLinks($footerHtml))));

        $title = $translate($title);
        $description = $translate($description);
        $decodedTitle = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Save to MySQL Database with localized slug
        $page = Page::updateOrCreate(
            ['slug' => $expectedSlug, 'locale' => $locale],
            [
                'title' => $decodedTitle,
                'description' => $description,
                'body_class' => $bodyClass,
                'header_html' => $headerHtml,
                'content_html' => $contentHtml,
                'footer_html' => $footerHtml,
            ]
        );

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
}
