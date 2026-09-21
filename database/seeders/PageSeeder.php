<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = __DIR__ . '/pages.json';
        if (File::exists($jsonPath)) {
            $pages = json_decode(File::get($jsonPath), true);
            foreach ($pages as $p) {
                unset($p['id'], $p['created_at'], $p['updated_at']);
                Page::updateOrCreate(
                    ['slug' => $p['slug'], 'locale' => $p['locale']],
                    $p
                );
            }
        }
    }
}
