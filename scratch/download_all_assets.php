<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

echo "=== 1. Creating directories ===\n";
File::ensureDirectoryExists(public_path('css'));
File::ensureDirectoryExists(public_path('js'));
File::ensureDirectoryExists(public_path('uploads'));

$headers = [
    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Referer' => 'https://bullet.legal/',
];

function downloadFile(string $url, string $destPath, array $headers): bool
{
    if (file_exists($destPath) && filesize($destPath) > 0) {
        return true;
    }
    File::ensureDirectoryExists(dirname($destPath));
    try {
        $resp = Http::withHeaders($headers)->timeout(30)->get($url);
        if ($resp->successful() && strlen($resp->body()) > 0) {
            file_put_contents($destPath, $resp->body());
            echo "Downloaded: " . basename($destPath) . " (" . strlen($resp->body()) . " bytes)\n";
            return true;
        } else {
            echo "FAILED (" . $resp->status() . "): $url\n";
            return false;
        }
    } catch (\Exception $e) {
        echo "ERROR downloading $url: " . $e->getMessage() . "\n";
        return false;
    }
}

echo "\n=== 2. Downloading CSS files ===\n";
$cssMap = [
    'https://bullet.legal/wp-content/cache/min/1/npm/swiper@11/swiper-bundle.min.css' => public_path('css/swiper-bundle.min.css'),
    'https://bullet.legal/wp-content/themes/ecomstrive-new/dist/css/style.min.css' => public_path('css/style.min.css'),
    'https://bullet.legal/wp-content/cache/min/1/wp-content/themes/ecomstrive-new/assets/manual/mobile-dropdown.css' => public_path('css/mobile-dropdown.css'),
];
foreach ($cssMap as $url => $path) {
    downloadFile($url, $path, $headers);
}

echo "\n=== 3. Downloading JS files ===\n";
$jsMap = [
    'https://bullet.legal/wp-includes/js/jquery/jquery.min.js' => public_path('js/jquery.min.js'),
    'https://bullet.legal/wp-includes/js/jquery/jquery-migrate.min.js' => public_path('js/jquery-migrate.min.js'),
    'https://bullet.legal/wp-content/cache/min/1/cf73f18a58dc/npm/swiper@11/swiper-bundle.min.js' => public_path('js/swiper-bundle.min.js'),
    'https://bullet.legal/wp-content/themes/ecomstrive-new/dist/js/app.min.js' => public_path('js/app.min.js'),
    'https://bullet.legal/wp-content/cache/min/1/6a1f532d9432/wp-content/themes/ecomstrive-new/assets/manual/mobile-dropdown.js' => public_path('js/mobile-dropdown.js'),
];
foreach ($jsMap as $url => $path) {
    downloadFile($url, $path, $headers);
}

echo "\n=== 4. Collecting all image & media URLs from DB and layout ===\n";
$mediaUrls = [
    'https://bullet.legal/wp-content/uploads/2025/02/cropped-bullet_symbol_blue-32x32.png',
    'https://bullet.legal/wp-content/uploads/2025/02/cropped-bullet_symbol_blue-192x192.png',
    'https://bullet.legal/wp-content/uploads/2025/02/cropped-bullet_symbol_blue-180x180.png',
    'https://bullet.legal/wp-content/uploads/2025/01/Logo-Container.svg',
    'https://bullet.legal/wp-content/uploads/2026/01/01_DSC_5365-scaled.jpg',
    'https://bullet.legal/wp-content/uploads/2025/01/Layer_1.svg',
];

$pages = Page::all();
foreach ($pages as $p) {
    $html = $p->header_html . ' ' . $p->content_html . ' ' . $p->footer_html;
    preg_match_all('/https?:\/\/bullet\.legal\/wp-content\/uploads\/[^\s\x27\"<>)]+/i', $html, $m);
    foreach ($m[0] as $u) {
        $clean = preg_replace('/\?.*$/', '', $u);
        $mediaUrls[] = $clean;
    }
}

$mediaUrls = array_values(array_unique($mediaUrls));
echo "Total unique media files to download: " . count($mediaUrls) . "\n";

$downloadedCount = 0;
$failedCount = 0;

foreach ($mediaUrls as $idx => $url) {
    // Determine relative path under public/uploads/
    if (preg_match('/\/wp-content\/uploads\/(.*)$/i', $url, $m)) {
        $relPath = $m[1];
        $destPath = public_path('uploads/' . $relPath);
        if (downloadFile($url, $destPath, $headers)) {
            $downloadedCount++;
        } else {
            $failedCount++;
        }
    }
}

echo "\n=== Asset download complete! ===\n";
echo "Successfully downloaded/verified: $downloadedCount\n";
echo "Failed: $failedCount\n";
