<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Page;

$slugMap = config('slug_map');

function cleanImages(string $markup): string
{
    $markup = preg_replace('/https?:\/\/bullet\.legal\/wp-content\/uploads\//i', '/uploads/', $markup);
    $markup = preg_replace('/sizes=[\'"]auto,[^\'"]*[\'"]/i', 'sizes="(max-width: 768px) 100vw, 400px"', $markup);
    $markup = preg_replace('/<img(?![^>]*referrerpolicy)/i', '<img referrerpolicy="no-referrer"', $markup);
    return $markup;
}

function rewriteInternalLinks(string $html, string $targetLocale): string
{
    $map = config('slug_map');
    
    $targetUrlFor = function(string $canonical) use ($map, $targetLocale) {
        $locSlug = $map[$canonical][$targetLocale] ?? $canonical;
        if ($targetLocale === 'lv') {
            return $locSlug === '' ? '/' : "/{$locSlug}/";
        } else {
            return $locSlug === '' ? "/{$targetLocale}/" : "/{$targetLocale}/{$locSlug}/";
        }
    };

    // Replace empty href in breadcrumbs
    $homeUrl = $targetLocale === 'lv' ? '/' : "/{$targetLocale}/";
    $html = preg_replace('/href=([\'"])\s*([\'"])/i', 'href=' . '$1' . $homeUrl . '$2', $html);

    $canonicalKeys = array_keys($map);
    usort($canonicalKeys, fn($a, $b) => strlen($b) <=> strlen($a));

    $aliases = [
        'services' => 'juridiskie-pakalpojumi',
        'practice-areas' => 'jurista-darbibas-nozare',
        'lawyers' => 'zverinati-advokati',
    ];

    return preg_replace_callback('/href=([\'"])(.*?)([\'"])/i', function($m) use ($canonicalKeys, $map, $targetLocale, $targetUrlFor, $aliases) {
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
}

function buildLangSwitcher(string $headerMarkup, string $canonicalKey, string $locale): string
{
    $map = config('slug_map');
    $currLabel = strtoupper($locale);

    $lvSlug = $map[$canonicalKey]['lv'] ?? $canonicalKey;
    $enSlug = $map[$canonicalKey]['en'] ?? $canonicalKey;
    $ruSlug = $map[$canonicalKey]['ru'] ?? $canonicalKey;

    $langs = [
        'LV' => $lvSlug === '' ? '/' : "/{$lvSlug}/",
        'EN' => $enSlug === '' ? '/en/' : "/en/{$enSlug}/",
        'RU' => $ruSlug === '' ? '/ru/' : "/ru/{$ruSlug}/",
    ];

    $optionsHtml = '';
    foreach ($langs as $l => $url) {
        if (strtoupper($locale) === $l) continue;
        $optionsHtml .= '<a href="' . $url . '" class="header__lang">' . $l . '</a>';
    }

    $pllHtml = '<div class="header__pll">'
        . '<svg class="header__icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">'
        . '<path d="M10 18.4792C8.83333 18.4792 7.72917 18.2569 6.6875 17.8125C5.65972 17.3681 4.76389 16.7639 4 16C3.23611 15.2361 2.63194 14.3403 2.1875 13.3125C1.74306 12.2708 1.52083 11.1667 1.52083 10C1.52083 8.81944 1.74306 7.71528 2.1875 6.6875C2.63194 5.65972 3.23611 4.76389 4 4C4.76389 3.23611 5.65972 2.63194 6.6875 2.1875C7.72917 1.74305 8.83333 1.52083 10 1.52083C11.1806 1.52083 12.2847 1.74305 13.3125 2.1875C14.3403 2.63194 15.2361 3.23611 16 4C16.7639 4.76389 17.3681 5.66667 17.8125 6.70833C18.2569 7.73611 18.4792 8.83333 18.4792 10C18.4792 11.1806 18.2569 12.2847 17.8125 13.3125C17.3681 14.3403 16.7639 15.2361 16 16C15.2361 16.7639 14.3403 17.3681 13.3125 17.8125C12.2847 18.2569 11.1806 18.4792 10 18.4792ZM9.14583 16.7083V15.0625C8.6875 15.0625 8.29167 14.8958 7.95833 14.5625C7.63889 14.2292 7.47917 13.8333 7.47917 13.375V12.5208L3.4375 8.45833C3.38194 8.72222 3.33333 8.97917 3.29167 9.22917C3.26389 9.47917 3.25 9.73611 3.25 10C3.25 11.7083 3.80556 13.2083 4.91667 14.5C6.04167 15.7778 7.45139 16.5139 9.14583 16.7083ZM14.9792 14.5625C15.2708 14.2569 15.5278 13.9236 15.75 13.5625C15.9722 13.2014 16.1528 12.8264 16.2917 12.4375C16.4444 12.0486 16.5556 11.6528 16.625 11.25C16.7083 10.8333 16.75 10.4167 16.75 10C16.75 8.61111 16.3681 7.34722 15.6042 6.20833C14.8403 5.06944 13.8194 4.24305 12.5417 3.72917V4.0625C12.5417 4.52083 12.375 4.92361 12.0417 5.27083C11.7083 5.60417 11.3125 5.77083 10.8542 5.77083H9.14583V7.45833C9.14583 7.69444 9.0625 7.89583 8.89583 8.0625C8.72917 8.21528 8.52778 8.29167 8.29167 8.29167H6.625V10H11.7083C11.9444 10 12.1389 10.0833 12.2917 10.25C12.4583 10.4167 12.5417 10.6111 12.5417 10.8333V13.375H13.375C13.75 13.375 14.0833 13.4861 14.375 13.7083C14.6667 13.9306 14.8681 14.2153 14.9792 14.5625Z" fill="currentColor"></path>'
        . '</svg>'
        . '<span class="header__current" style="cursor:pointer;display:inline-flex;align-items:center;gap:4px;">'
        . $currLabel
        . '<svg class="header__arrow" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">'
        . '<path d="M9.54167 12.1875L6.33333 8.97917C6.29167 8.9375 6.25694 8.89583 6.22917 8.85417C6.20139 8.79861 6.1875 8.73611 6.1875 8.66667C6.1875 8.55555 6.22917 8.45833 6.3125 8.375C6.39583 8.27778 6.5 8.22917 6.625 8.22917H13.375C13.5 8.22917 13.6042 8.27778 13.6875 8.375C13.7708 8.45833 13.8125 8.55555 13.8125 8.66667C13.8125 8.70833 13.7639 8.8125 13.6667 8.97917L10.4583 12.1875C10.3889 12.2569 10.3125 12.3056 10.2292 12.3333C10.1597 12.3611 10.0833 12.375 10 12.375C9.91667 12.375 9.83333 12.3611 9.75 12.3333C9.68056 12.3056 9.61111 12.2569 9.54167 12.1875Z" fill="currentColor"></path>'
        . '</svg>'
        . '</span>'
        . '<div class="header__select" style="background:#16134a;border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:6px 14px;box-shadow:0 8px 24px rgba(0,0,0,0.3);">'
        . $optionsHtml
        . '</div>'
        . '</div>';

    return preg_replace('/<div class=[\'"]header__pll[\'"].*?<\/div>\s*<\/div>/is', $pllHtml, $headerMarkup);
}

function buildTranslationDictionary(string $jsonFile): array
{
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
}

function cleanFooter(string $markup): string
{
    return preg_replace('/<div class=[\'"]footer__copy-right[\'"].*?<\/div>/is', '', $markup);
}

$enDict = buildTranslationDictionary(base_path('translations_en.json'));
$ruDict = buildTranslationDictionary(base_path('translations_ru.json'));

echo "Starting full database localization sync with local assets...\n";

Page::whereIn('locale', ['en', 'ru'])->delete();

$lvPages = Page::where('locale', 'lv')->get();

foreach ($lvPages as $lvPage) {
    $canonicalKey = $lvPage->slug;
    echo "Processing canonical: [{$canonicalKey}]...\n";

    // Clean up <br /> &nbsp;<br /> in content and replace external image links
    $cleanedLvContent = preg_replace('/<br\s*\/?>\s*&nbsp;\s*<br\s*\/?>/i', '<br />', $lvPage->content_html);

    // 1. Update LV page header switcher, links & images
    $lvHeader = buildLangSwitcher(cleanImages(rewriteInternalLinks($lvPage->header_html, 'lv')), $canonicalKey, 'lv');
    $lvContent = cleanImages(rewriteInternalLinks($cleanedLvContent, 'lv'));
    $lvFooter = cleanFooter(cleanImages(rewriteInternalLinks($lvPage->footer_html, 'lv')));
    $lvPage->header_html = $lvHeader;
    $lvPage->content_html = $lvContent;
    $lvPage->footer_html = $lvFooter;
    $lvPage->save();

    // 2. Build EN page
    $enSlug = $slugMap[$canonicalKey]['en'] ?? $canonicalKey;
    $enHeader = strtr(buildLangSwitcher(cleanImages(rewriteInternalLinks($lvPage->header_html, 'en')), $canonicalKey, 'en'), $enDict);
    $enContent = strtr(cleanImages(rewriteInternalLinks($cleanedLvContent, 'en')), $enDict);
    $enFooter = cleanFooter(strtr(cleanImages(rewriteInternalLinks($lvPage->footer_html, 'en')), $enDict));
    $enTitle = strtr($lvPage->title, $enDict);
    if (!str_contains($enTitle, 'Law Office BULLET')) {
        $enTitle = preg_replace('/Advokātu birojs BULLET/u', 'Law Office BULLET', $enTitle);
    }
    $enDesc = strtr($lvPage->description, $enDict);
    if (!str_contains($enDesc, 'Law Office BULLET')) {
        $enDesc = preg_replace('/Advokātu birojs BULLET/u', 'Law Office BULLET', $enDesc);
    }

    Page::create([
        'slug' => $enSlug,
        'locale' => 'en',
        'title' => $enTitle,
        'description' => $enDesc,
        'body_class' => $lvPage->body_class,
        'header_html' => $enHeader,
        'content_html' => $enContent,
        'footer_html' => $enFooter,
    ]);

    // 3. Build RU page
    $ruSlug = $slugMap[$canonicalKey]['ru'] ?? $canonicalKey;
    $ruHeader = strtr(buildLangSwitcher(cleanImages(rewriteInternalLinks($lvPage->header_html, 'ru')), $canonicalKey, 'ru'), $ruDict);
    $ruContent = strtr(cleanImages(rewriteInternalLinks($cleanedLvContent, 'ru')), $ruDict);
    $ruFooter = cleanFooter(strtr(cleanImages(rewriteInternalLinks($lvPage->footer_html, 'ru')), $ruDict));
    $ruTitle = strtr($lvPage->title, $ruDict);
    if (!str_contains($ruTitle, 'Адвокатское бюро BULLET')) {
        $ruTitle = preg_replace('/Advokātu birojs BULLET/u', 'Адвокатское бюро BULLET', $ruTitle);
    }
    $ruDesc = strtr($lvPage->description, $ruDict);
    if (!str_contains($ruDesc, 'Адвокатское бюро BULLET')) {
        $ruDesc = preg_replace('/Advokātu birojs BULLET/u', 'Адвокатское бюро BULLET', $ruDesc);
    }

    Page::create([
        'slug' => $ruSlug,
        'locale' => 'ru',
        'title' => $ruTitle,
        'description' => $ruDesc,
        'body_class' => $lvPage->body_class,
        'header_html' => $ruHeader,
        'content_html' => $ruContent,
        'footer_html' => $ruFooter,
    ]);
}

echo "Database sync complete! Total pages: " . Page::count() . "\n";
