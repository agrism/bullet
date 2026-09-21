<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $locale = $request->query('locale', 'all');
        $search = $request->query('search', '');

        $query = Page::query();

        if ($locale !== 'all' && in_array($locale, ['lv', 'en', 'ru'])) {
            $query->where('locale', $locale);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $pages = $query->orderBy('slug', 'asc')->orderBy('locale', 'asc')->paginate(30)->withQueryString();

        $counts = [
            'total' => Page::count(),
            'lv' => Page::where('locale', 'lv')->count(),
            'en' => Page::where('locale', 'en')->count(),
            'ru' => Page::where('locale', 'ru')->count(),
        ];

        return view('admin.pages.index', compact('pages', 'locale', 'search', 'counts'));
    }

    public function edit(Page $page)
    {
        $slugMap = config('slug_map', []);
        
        // Find public URL for this page
        $publicUrl = $page->locale === 'lv'
            ? ($page->slug === '' ? '/' : "/{$page->slug}/")
            : ($page->slug === '' ? "/{$page->locale}/" : "/{$page->locale}/{$page->slug}/");

        return view('admin.pages.edit', compact('page', 'publicUrl'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'body_class' => ['nullable', 'string', 'max:255'],
            'content_html' => ['required', 'string'],
            'header_html' => ['nullable', 'string'],
            'footer_html' => ['nullable', 'string'],
        ]);

        $oldSlug = $page->slug;
        $oldLocale = $page->locale;

        $page->update($validated);

        // Clear cache for this page
        Cache::forget("page_{$oldLocale}_{$oldSlug}");
        Cache::forget("page_{$page->locale}_{$page->slug}");
        Cache::flush();

        // Sync changes to pages.json
        $this->syncToJson();

        return redirect()->route('admin.pages.edit', $page)
            ->with('success', "Page '{$page->title}' ({$page->locale}) was successfully updated and cache cleared!");
    }

    public function destroy(Page $page)
    {
        $title = $page->title;
        $locale = $page->locale;
        $slug = $page->slug;

        $page->delete();

        // Clear cache
        Cache::forget("page_{$locale}_{$slug}");
        Cache::flush();

        // Sync changes to pages.json
        $this->syncToJson();

        return redirect()->route('admin.pages.index')
            ->with('success', "Page '{$title}' ({$locale}) was successfully deleted.");
    }

    public function clearCache()
    {
        Cache::flush();

        return back()->with('success', 'All website and page caches have been successfully purged!');
    }

    public function showPassword()
    {
        return view('admin.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password successfully updated!');
    }

    protected function syncToJson(): void
    {
        $jsonPath = database_path('seeders/pages.json');
        $allPages = Page::all()->toArray();
        file_put_contents($jsonPath, json_encode($allPages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
