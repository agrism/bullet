@extends('admin.layouts.app')

@section('title', 'Edit ' . $page->title)

@section('content')
<form action="{{ route('admin.pages.update', $page) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Top Action Bar -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <a href="{{ route('admin.pages.index', ['locale' => $page->locale]) }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">
                    &larr; Back to Pages
                </a>
                <span style="color: var(--border-color);">|</span>
                <span class="locale-badge locale-badge--{{ $page->locale }}">{{ strtoupper($page->locale) }}</span>
            </div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">
                Edit Page: {{ $page->title }}
            </h1>
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ $publicUrl }}" target="_blank" class="btn btn-outline">
                View on Live Site &UpperRightArrow;
            </a>
            <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 0.95rem;">
                Save Changes
            </button>
        </div>
    </div>

    <!-- Meta & Settings Card -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card__header">
            <h2 class="card__title">General & SEO Information</h2>
        </div>
        <div class="card__body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="title" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Page Title (&lt;title&gt; tag)</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $page->title) }}" required
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; outline: none;">
                </div>

                <div>
                    <label for="slug" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">URL Path / Slug (Read-only)</label>
                    <input type="text" id="slug" value="{{ $page->slug === '' ? '(home)' : $page->slug }}" disabled
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; background: #f1f5f9; color: var(--text-muted); font-family: monospace;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Meta Description (SEO)</label>
                <textarea id="description" name="description" rows="2"
                    style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; outline: none; font-family: inherit;">{{ old('description', $page->description) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div>
                    <label for="body_class" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Body CSS Class</label>
                    <input type="text" id="body_class" name="body_class" value="{{ old('body_class', $page->body_class) }}"
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem; outline: none; font-family: monospace;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Language</label>
                    <input type="text" value="{{ strtoupper($page->locale) }} ({{ $page->locale === 'lv' ? 'Latvian' : ($page->locale === 'en' ? 'English' : 'Russian') }})" disabled
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem; background: #f1f5f9; color: var(--text-muted);">
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content HTML Card -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card__header" style="background: #f8fafc;">
            <div>
                <h2 class="card__title">Page Content HTML</h2>
                <p style="font-size: 0.825rem; color: var(--text-muted); margin-top: 2px;">
                    Edit the main sections, blocks, titles, texts, and elements of this page.
                </p>
            </div>
            <div>
                <span style="font-size: 0.8rem; font-family: monospace; color: var(--text-muted);">
                    {{ strlen($page->content_html) }} characters
                </span>
            </div>
        </div>
        <div class="card__body" style="padding: 0;">
            <textarea id="content_html" name="content_html" rows="24" required
                style="width: 100%; padding: 18px 20px; border: none; font-size: 0.9rem; line-height: 1.6; font-family: 'JetBrains Mono', Consolas, Monaco, monospace; outline: none; background: #1e1e2e; color: #f8f8f2; box-sizing: border-box; resize: vertical;">{{ old('content_html', $page->content_html) }}</textarea>
        </div>
    </div>

    <!-- Advanced Header & Footer Section (Collapsible) -->
    <div class="card" style="margin-bottom: 32px;">
        <details>
            <summary class="card__header" style="cursor: pointer; user-select: none;">
                <h2 class="card__title" style="display: inline-flex; align-items: center; gap: 8px;">
                    <span>Advanced: Header & Footer HTML</span>
                </h2>
                <span style="font-size: 0.85rem; color: var(--text-muted);">&#x25BC; Click to expand</span>
            </summary>
            <div class="card__body" style="background: #f8fafc; border-top: 1px solid var(--border-color);">
                <div style="margin-bottom: 20px;">
                    <label for="header_html" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Header HTML</label>
                    <textarea id="header_html" name="header_html" rows="8"
                        style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.85rem; font-family: 'JetBrains Mono', monospace; outline: none;">{{ old('header_html', $page->header_html) }}</textarea>
                </div>

                <div>
                    <label for="footer_html" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Footer HTML</label>
                    <textarea id="footer_html" name="footer_html" rows="8"
                        style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.85rem; font-family: 'JetBrains Mono', monospace; outline: none;">{{ old('footer_html', $page->footer_html) }}</textarea>
                </div>
            </div>
        </details>
    </div>

    <!-- Bottom Action Floating/Sticky Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 40px;">
        <a href="{{ route('admin.pages.index', ['locale' => $page->locale]) }}" class="btn btn-outline">
            Cancel
        </a>
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-size: 1rem;">
            Save All Changes
        </button>
    </div>

</form>
@endsection
