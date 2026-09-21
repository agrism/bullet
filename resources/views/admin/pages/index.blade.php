@extends('admin.layouts.app')

@section('title', 'Manage Pages & Content')

@section('content')
<div style="margin-bottom: 24px;">
    <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--primary); margin-bottom: 6px;">
        Pages & Content Management
    </h1>
    <p style="color: var(--text-muted); font-size: 0.95rem;">
        Edit titles, descriptions, texts, and HTML content across all 3 languages (Latvian, English, Russian).
    </p>
</div>

<!-- Stats and Filters Bar -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card__body" style="padding: 16px 20px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px;">
        
        <!-- Language Filter Tabs -->
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('admin.pages.index', ['locale' => 'all', 'search' => $search]) }}" 
               class="btn btn-sm {{ $locale === 'all' ? 'btn-primary' : 'btn-outline' }}">
                All ({{ $counts['total'] }})
            </a>
            <a href="{{ route('admin.pages.index', ['locale' => 'lv', 'search' => $search]) }}" 
               class="btn btn-sm {{ $locale === 'lv' ? 'btn-primary' : 'btn-outline' }}">
                <span class="locale-badge locale-badge--lv" style="margin-right: 4px;">LV</span> Latvian ({{ $counts['lv'] }})
            </a>
            <a href="{{ route('admin.pages.index', ['locale' => 'en', 'search' => $search]) }}" 
               class="btn btn-sm {{ $locale === 'en' ? 'btn-primary' : 'btn-outline' }}">
                <span class="locale-badge locale-badge--en" style="margin-right: 4px;">EN</span> English ({{ $counts['en'] }})
            </a>
            <a href="{{ route('admin.pages.index', ['locale' => 'ru', 'search' => $search]) }}" 
               class="btn btn-sm {{ $locale === 'ru' ? 'btn-primary' : 'btn-outline' }}">
                <span class="locale-badge locale-badge--ru" style="margin-right: 4px;">RU</span> Russian ({{ $counts['ru'] }})
            </a>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('admin.pages.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px;">
            <input type="hidden" name="locale" value="{{ $locale }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search title or slug..."
                style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.875rem; width: 220px; outline: none;">
            <button type="submit" class="btn btn-sm btn-primary">Search</button>
            @if(!empty($search))
                <a href="{{ route('admin.pages.index', ['locale' => $locale]) }}" class="btn btn-sm btn-outline">Clear</a>
            @endif
        </form>

    </div>
</div>

<!-- Pages Table -->
<div class="card">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05rem;">
                    <th style="padding: 14px 20px;">Locale</th>
                    <th style="padding: 14px 20px;">Page Title</th>
                    <th style="padding: 14px 20px;">URL Path / Slug</th>
                    <th style="padding: 14px 20px;">Last Modified</th>
                    <th style="padding: 14px 20px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                @php
                    $liveUrl = $page->locale === 'lv'
                        ? ($page->slug === '' ? '/' : "/{$page->slug}/")
                        : ($page->slug === '' ? "/{$page->locale}/" : "/{$page->locale}/{$page->slug}/");
                @endphp
                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 14px 20px;">
                        <span class="locale-badge locale-badge--{{ $page->locale }}">{{ strtoupper($page->locale) }}</span>
                    </td>
                    <td style="padding: 14px 20px; font-weight: 600; color: var(--text-dark);">
                        <a href="{{ route('admin.pages.edit', $page) }}" style="color: inherit; text-decoration: none;">
                            {{ $page->title }}
                        </a>
                    </td>
                    <td style="padding: 14px 20px; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; font-size: 0.825rem;">
                        {{ $liveUrl }}
                    </td>
                    <td style="padding: 14px 20px; color: var(--text-light); font-size: 0.825rem;">
                        {{ $page->updated_at ? $page->updated_at->format('Y-m-d H:i') : '-' }}
                    </td>
                    <td style="padding: 14px 20px; text-align: right;">
                        <div style="display: inline-flex; align-items: center; gap: 8px;">
                            <a href="{{ $liveUrl }}" target="_blank" class="btn btn-sm btn-outline" style="color: var(--text-muted);" title="View live page">
                                Live &UpperRightArrow;
                            </a>
                            <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-primary">
                                Edit Content
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete page \'{{ addslashes($page->title) }}\' ({{ $page->locale }})?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline" style="color: #ef4444; border-color: rgba(239, 68, 68, 0.3); padding: 5px 9px;" title="Delete page">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: var(--text-muted);">
                        No pages found matching the specified criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pages->hasPages())
    <div style="padding: 16px 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
        {{ $pages->links() }}
    </div>
    @endif
</div>
@endsection
