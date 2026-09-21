@extends('admin.layouts.app')

@section('title', 'Edit ' . $page->title)

@push('styles')
<style>
    .mode-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: var(--text-dark);
        transition: all 0.2s;
    }
    .mode-tab-btn.is-active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }
    .viewport-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.775rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: var(--text-muted);
        transition: all 0.2s;
    }
    .viewport-btn.is-active {
        background: #e2e8f0;
        color: var(--text-dark);
        border-color: #cbd5e1;
    }
    .preview-frame-container {
        width: 100%;
        background: #64748b;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 16px;
        min-height: 600px;
        border-radius: 0 0 12px 12px;
        transition: all 0.3s ease;
    }
    .preview-iframe {
        width: 100%;
        height: 700px;
        border: none;
        background: #ffffff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        border-radius: 8px;
        transition: width 0.3s ease;
    }
    .preview-iframe.viewport-mobile {
        width: 390px !important;
        height: 740px !important;
        border-radius: 28px;
        border: 8px solid #1e293b;
    }
    .editor-split-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        border-top: 1px solid var(--border-color);
    }
    @media (max-width: 1024px) {
        .editor-split-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<form action="{{ route('admin.pages.update', $page) }}" method="POST" id="editPageForm">
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
            <a href="{{ $publicUrl }}" target="_blank" class="btn btn-outline" title="Open live public URL">
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

    <!-- Main Content HTML Card with PREVIEW SWITCHER -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card__header" style="background: #f8fafc; flex-wrap: wrap; gap: 14px;">
            <div>
                <h2 class="card__title">Page Content & Live Preview</h2>
                <p style="font-size: 0.825rem; color: var(--text-muted); margin-top: 2px;">
                    Switch between HTML code editor, full rendered preview, or side-by-side split view.
                </p>
            </div>

            <!-- Preview Mode Switcher Tabs -->
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                
                <!-- View Mode Controls -->
                <div style="display: flex; align-items: center; gap: 6px; background: #f1f5f9; padding: 4px; border-radius: 8px;">
                    <button type="button" class="mode-tab-btn is-active" id="btnModeCode" onclick="switchViewMode('code')">
                        💻 Code Editor
                    </button>
                    <button type="button" class="mode-tab-btn" id="btnModePreview" onclick="switchViewMode('preview')">
                        👁️ Live Preview
                    </button>
                    <button type="button" class="mode-tab-btn" id="btnModeSplit" onclick="switchViewMode('split')">
                        ◫ Split View
                    </button>
                </div>

                <!-- Viewport Size Controls (Visible in Preview / Split modes) -->
                <div id="viewportControls" style="display: none; align-items: center; gap: 6px; background: #f1f5f9; padding: 4px 6px; border-radius: 8px;">
                    <button type="button" class="viewport-btn is-active" id="btnViewportDesktop" onclick="setViewportSize('desktop')">
                        🖥️ Desktop
                    </button>
                    <button type="button" class="viewport-btn" id="btnViewportMobile" onclick="setViewportSize('mobile')">
                        📱 Mobile
                    </button>
                    <button type="button" class="viewport-btn" onclick="updateLivePreview()" title="Refresh live preview">
                        🔄 Refresh
                    </button>
                </div>

                <span id="charCount" style="font-size: 0.8rem; font-family: monospace; color: var(--text-muted); margin-left: 4px;">
                    {{ strlen($page->content_html) }} chars
                </span>
            </div>
        </div>

        <!-- Editor & Preview Workspace -->
        <div id="workspaceContainer" style="position: relative;">
            
            <!-- Code Editor Box -->
            <div id="codeEditorBox" style="display: block;">
                <textarea id="content_html" name="content_html" rows="24" required
                    style="width: 100%; min-height: 600px; padding: 18px 20px; border: none; font-size: 0.9rem; line-height: 1.6; font-family: 'JetBrains Mono', Consolas, Monaco, monospace; outline: none; background: #1e1e2e; color: #f8f8f2; box-sizing: border-box; resize: vertical;"
                    oninput="onCodeInput()">{{ old('content_html', $page->content_html) }}</textarea>
            </div>

            <!-- Live Preview Frame Box -->
            <div id="previewFrameBox" class="preview-frame-container" style="display: none;">
                <iframe id="livePreviewIframe" class="preview-iframe" title="Live Preview"></iframe>
            </div>

            <!-- Split View Box -->
            <div id="splitViewBox" class="editor-split-grid" style="display: none;">
                <div style="border-right: 1px solid #334155;">
                    <textarea id="splitContentTextarea" rows="28"
                        style="width: 100%; height: 720px; padding: 16px; border: none; font-size: 0.875rem; line-height: 1.5; font-family: 'JetBrains Mono', monospace; outline: none; background: #1e1e2e; color: #f8f8f2; box-sizing: border-box; resize: none;"
                        oninput="onSplitInput(this.value)">{{ old('content_html', $page->content_html) }}</textarea>
                </div>
                <div class="preview-frame-container" style="padding: 12px; background: #475569;">
                    <iframe id="splitPreviewIframe" class="preview-iframe" style="height: 696px;" title="Split Preview"></iframe>
                </div>
            </div>

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
                        style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.85rem; font-family: 'JetBrains Mono', monospace; outline: none;"
                        oninput="updateLivePreview()">{{ old('header_html', $page->header_html) }}</textarea>
                </div>

                <div>
                    <label for="footer_html" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 6px;">Footer HTML</label>
                    <textarea id="footer_html" name="footer_html" rows="8"
                        style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.85rem; font-family: 'JetBrains Mono', monospace; outline: none;"
                        oninput="updateLivePreview()">{{ old('footer_html', $page->footer_html) }}</textarea>
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

@push('scripts')
<script>
    var currentMode = 'code'; // 'code', 'preview', 'split'
    var currentViewport = 'desktop'; // 'desktop', 'mobile'
    var previewDebounceTimer = null;

    function switchViewMode(mode) {
        currentMode = mode;
        
        // Update tab buttons active state
        document.getElementById('btnModeCode').classList.toggle('is-active', mode === 'code');
        document.getElementById('btnModePreview').classList.toggle('is-active', mode === 'preview');
        document.getElementById('btnModeSplit').classList.toggle('is-active', mode === 'split');

        // Containers
        var codeBox = document.getElementById('codeEditorBox');
        var previewBox = document.getElementById('previewFrameBox');
        var splitBox = document.getElementById('splitViewBox');
        var vpControls = document.getElementById('viewportControls');

        if (mode === 'code') {
            codeBox.style.display = 'block';
            previewBox.style.display = 'none';
            splitBox.style.display = 'none';
            vpControls.style.display = 'none';
        } else if (mode === 'preview') {
            codeBox.style.display = 'none';
            previewBox.style.display = 'flex';
            splitBox.style.display = 'none';
            vpControls.style.display = 'flex';
            updateLivePreview();
        } else if (mode === 'split') {
            codeBox.style.display = 'none';
            previewBox.style.display = 'none';
            splitBox.style.display = 'grid';
            vpControls.style.display = 'flex';
            
            // Sync text from main textarea to split textarea
            document.getElementById('splitContentTextarea').value = document.getElementById('content_html').value;
            updateLivePreview();
        }
    }

    function setViewportSize(size) {
        currentViewport = size;
        document.getElementById('btnViewportDesktop').classList.toggle('is-active', size === 'desktop');
        document.getElementById('btnViewportMobile').classList.toggle('is-active', size === 'mobile');

        var iframes = document.querySelectorAll('.preview-iframe');
        iframes.forEach(function(iframe) {
            if (size === 'mobile') {
                iframe.classList.add('viewport-mobile');
            } else {
                iframe.classList.remove('viewport-mobile');
            }
        });
    }

    function onCodeInput() {
        var val = document.getElementById('content_html').value;
        document.getElementById('charCount').textContent = val.length + ' chars';
        
        if (currentMode !== 'code') {
            clearTimeout(previewDebounceTimer);
            previewDebounceTimer = setTimeout(updateLivePreview, 250);
        }
    }

    function onSplitInput(val) {
        document.getElementById('content_html').value = val;
        document.getElementById('charCount').textContent = val.length + ' chars';
        
        clearTimeout(previewDebounceTimer);
        previewDebounceTimer = setTimeout(updateLivePreview, 250);
    }

    function updateLivePreview() {
        var contentHtml = document.getElementById('content_html').value;
        var headerHtml = document.getElementById('header_html').value;
        var footerHtml = document.getElementById('footer_html').value;
        var title = document.getElementById('title').value;
        var bodyClass = document.getElementById('body_class').value || 'page-default';
        var locale = '{{ $page->locale }}';

        var fullDocument = '<!doctype html>'
            + '<html lang="' + (locale === 'en' ? 'en-US' : (locale === 'ru' ? 'ru-RU' : 'lv-LV')) + '">'
            + '<head>'
            + '<meta charset="utf-8">'
            + '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
            + '<title>' + title + '</title>'
            + '<link rel="preconnect" href="https://fonts.googleapis.com">'
            + '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
            + '<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">'
            + '<link rel="stylesheet" href="/css/swiper-bundle.min.css">'
            + '<link rel="stylesheet" href="/css/style.min.css">'
            + '<link rel="stylesheet" href="/css/mobile-dropdown.css">'
            + '<style>'
            + 'body { font-family: "Inter", sans-serif; -webkit-font-smoothing: antialiased; }'
            + '.header__logo img { max-height: 40px; width: auto; }'
            + '.hero, .hero--type-1, .hero--type-2 { height: auto !important; min-height: auto !important; }'
            + '@media screen and (min-width: 64em) { .hero, .hero--type-2 { padding: 160px 0 80px 0 !important; min-height: 600px !important; } }'
            + '</style>'
            + '</head>'
            + '<body class="' + bodyClass + '">'
            + '<div class="page-wrapper">'
            + headerHtml
            + '<div class="page-content">' + contentHtml + '</div>'
            + footerHtml
            + '</div>'
            + '<script src="/js/jquery.min.js"><\/script>'
            + '<script src="/js/swiper-bundle.min.js"><\/script>'
            + '<script src="/js/app.min.js"><\/script>'
            + '<script src="/js/mobile-dropdown.js"><\/script>'
            + '</body></html>';

        if (currentMode === 'preview') {
            var iframe = document.getElementById('livePreviewIframe');
            if (iframe) iframe.srcdoc = fullDocument;
        } else if (currentMode === 'split') {
            var splitIframe = document.getElementById('splitPreviewIframe');
            if (splitIframe) splitIframe.srcdoc = fullDocument;
        }
    }

    // Keyboard shortcut (Ctrl+S / Cmd+S) to quickly save
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            document.getElementById('editPageForm').submit();
        }
    });
</script>
@endpush
