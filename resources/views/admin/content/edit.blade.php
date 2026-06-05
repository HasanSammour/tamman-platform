{{-- resources/views/admin/content/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('Edit Content') . ' - ' . __('Tamman'))

@section('page-title', __('Edit Content'))

@section('content')
    <div class="content-edit-wrapper">
        <!-- Breadcrumb -->
        <div class="breadcrumb-nav animate-fade-in">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">
                <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
            </a>
            <i class="fas fa-chevron-right breadcrumb-sep"></i>
            <a href="{{ route('admin.content') }}" class="breadcrumb-link">
                <i class="fas fa-newspaper"></i> {{ __('Content Management') }}
            </a>
            <i class="fas fa-chevron-right breadcrumb-sep"></i>
            <a href="{{ route('admin.content.show', $content->id) }}" class="breadcrumb-link">
                <i class="fas fa-eye"></i> {{ Str::limit($content->title, 30) }}
            </a>
            <i class="fas fa-chevron-right breadcrumb-sep"></i>
            <span class="breadcrumb-current">{{ __('Edit Content') }}</span>
        </div>

        <!-- Form Card -->
        <div class="form-card animate-slide-up">
            <div class="form-header">
                <div class="header-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="header-text">
                    <h2>{{ __('Edit Content') }}</h2>
                    <p>{{ __('Update your article, video, tip, or guide') }}</p>
                </div>
                <div class="header-status">
                    @if($content->is_published)
                        <span class="status-badge published"><i class="fas fa-check-circle"></i> {{ __('Published') }}</span>
                    @else
                        <span class="status-badge draft"><i class="fas fa-clock"></i> {{ __('Draft') }}</span>
                    @endif
                </div>
            </div>

            <form id="contentForm" class="content-form" method="POST"
                action="{{ route('admin.content.update', $content->id) }}">
                @csrf
                @method('PUT')

                <!-- Title Field -->
                <div class="form-group">
                    <label for="title">{{ __('Title') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-heading input-icon"></i>
                        <input type="text" name="title" id="title" class="form-control"
                            placeholder="{{ __('Enter content title...') }}" value="{{ old('title', $content->title) }}"
                            required>
                    </div>
                    <div class="error-message" id="title-error"></div>
                    <div class="char-counter" id="titleCounter">{{ strlen($content->title) }}/255</div>
                </div>

                <!-- Type Selection -->
                <div class="form-group">
                    <label for="type">{{ __('Content Type') }} <span class="required">*</span></label>
                    <div class="type-cards">
                        <div class="type-card" data-type="article">
                            <div class="type-icon article"><i class="fas fa-newspaper"></i></div>
                            <div class="type-info">
                                <h4>{{ __('Article') }}</h4>
                                <p>{{ __('Written educational content') }}</p>
                            </div>
                            <div class="type-check"><i class="far fa-circle"></i></div>
                        </div>
                        <div class="type-card" data-type="video">
                            <div class="type-icon video"><i class="fas fa-video"></i></div>
                            <div class="type-info">
                                <h4>{{ __('Video') }}</h4>
                                <p>{{ __('YouTube or Vimeo embed') }}</p>
                            </div>
                            <div class="type-check"><i class="far fa-circle"></i></div>
                        </div>
                        <div class="type-card" data-type="tip">
                            <div class="type-icon tip"><i class="fas fa-lightbulb"></i></div>
                            <div class="type-info">
                                <h4>{{ __('Tip') }}</h4>
                                <p>{{ __('Quick mental health tip') }}</p>
                            </div>
                            <div class="type-check"><i class="far fa-circle"></i></div>
                        </div>
                        <div class="type-card" data-type="guide">
                            <div class="type-icon guide"><i class="fas fa-book"></i></div>
                            <div class="type-info">
                                <h4>{{ __('Guide') }}</h4>
                                <p>{{ __('Step-by-step guide') }}</p>
                            </div>
                            <div class="type-check"><i class="far fa-circle"></i></div>
                        </div>
                    </div>
                    <input type="hidden" name="type" id="type" value="{{ $content->type }}">
                    <div class="error-message" id="type-error"></div>
                </div>

                <!-- Media URL (for video type) -->
                <div class="form-group media-url-group"
                    style="{{ $content->type === 'video' ? 'display: block;' : 'display: none;' }}">
                    <label for="media_url">{{ __('Video URL') }}</label>
                    <div class="input-wrapper">
                        <i class="fab fa-youtube input-icon"></i>
                        <input type="text" name="media_url" id="media_url" class="form-control"
                            placeholder="{{ __('https://www.youtube.com/watch?v=...') }}" value="{{ $content->media_url }}">
                    </div>
                    <div class="video-preview" id="videoPreview"
                        style="{{ $content->media_url ? 'display: block;' : 'display: none;' }}">
                        <div class="preview-header">
                            <i class="fas fa-play-circle"></i> <span>{{ __('Video Preview') }}</span>
                            <button type="button" id="clearVideoBtn" class="btn-clear-preview"><i
                                    class="fas fa-times"></i></button>
                        </div>
                        <div class="video-embed" id="videoEmbed"></div>
                    </div>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Supported: YouTube, Vimeo. The video will be embedded automatically.') }}
                    </div>
                    <div class="error-message" id="media_url-error"></div>
                </div>

                <!-- Body / Rich Text Editor -->
                <div class="form-group">
                    <label for="body">{{ __('Content Body') }} <span class="required">*</span></label>
                    <div id="editorToolbar" class="editor-toolbar">
                        <!-- Headings Dropdown -->
                        <div class="dropdown headings-dropdown">
                            <button type="button" class="dropdown-toggle" id="headingsDropdownBtn"
                                title="{{ __('Headings') }}">
                                <i class="fas fa-heading"></i> <span>{{ __('H') }}</span> <i
                                    class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu" id="headingsDropdownMenu">
                                <button type="button" data-heading="h1">H1 - {{ __('Heading 1') }}</button>
                                <button type="button" data-heading="h2">H2 - {{ __('Heading 2') }}</button>
                                <button type="button" data-heading="h3">H3 - {{ __('Heading 3') }}</button>
                                <button type="button" data-heading="h4">H4 - {{ __('Heading 4') }}</button>
                                <button type="button" data-heading="h5">H5 - {{ __('Heading 5') }}</button>
                                <button type="button" data-heading="h6">H6 - {{ __('Heading 6') }}</button>
                                <button type="button" data-heading="p">{{ __('Normal Text') }}</button>
                            </div>
                        </div>
                        <span class="separator"></span>
                        <button type="button" data-command="bold" title="{{ __('Bold') }}"><i
                                class="fas fa-bold"></i></button>
                        <button type="button" data-command="italic" title="{{ __('Italic') }}"><i
                                class="fas fa-italic"></i></button>
                        <button type="button" data-command="underline" title="{{ __('Underline') }}"><i
                                class="fas fa-underline"></i></button>
                        <span class="separator"></span>
                        <button type="button" data-command="insertUnorderedList" title="{{ __('Bullet List') }}"><i
                                class="fas fa-list-ul"></i></button>
                        <button type="button" data-command="insertOrderedList" title="{{ __('Numbered List') }}"><i
                                class="fas fa-list-ol"></i></button>
                        <span class="separator"></span>
                        <button type="button" data-command="createLink" title="{{ __('Insert Link') }}"><i
                                class="fas fa-link"></i></button>
                        <button type="button" data-command="insertImage" title="{{ __('Insert Image') }}"><i
                                class="fas fa-image"></i></button>
                        <span class="separator"></span>
                        <button type="button" data-command="undo" title="{{ __('Undo') }}"><i
                                class="fas fa-undo"></i></button>
                        <button type="button" data-command="redo" title="{{ __('Redo') }}"><i
                                class="fas fa-redo"></i></button>
                    </div>
                    <textarea name="body" id="body" class="form-control rich-editor" rows="15"
                        placeholder="{{ __('Write your content here...') }}">{{ old('body', $content->body) }}</textarea>
                    <div class="error-message" id="body-error"></div>
                </div>

                <!-- Status Toggle -->
                <div class="form-group">
                    <label>{{ __('Publication Status') }}</label>
                    <div class="status-toggle">
                        <div class="toggle-option draft" data-status="draft">
                            <i class="fas fa-pen-fancy"></i>
                            <span>{{ __('Save as Draft') }}</span>
                            <small>{{ __('Not visible to users') }}</small>
                        </div>
                        <div class="toggle-option publish" data-status="published">
                            <i class="fas fa-globe-asia"></i>
                            <span>{{ __('Publish Now') }}</span>
                            <small>{{ __('Visible to all users') }}</small>
                        </div>
                    </div>
                    <input type="hidden" name="status" id="status"
                        value="{{ $content->is_published ? 'published' : 'draft' }}">
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <a href="{{ route('admin.content.show', $content->id) }}" class="btn-cancel">
                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span class="btn-text"><i class="fas fa-save"></i> {{ __('Save Changes') }}</span>
                        <span class="btn-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Upload Modal -->
    <div id="imageUploadModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-image"></i> {{ __('Insert Image') }}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="upload-tabs">
                    <button class="tab-btn active" data-tab="upload">{{ __('Upload Image') }}</button>
                    <button class="tab-btn" data-tab="url">{{ __('Image URL') }}</button>
                </div>
                <div class="tab-content active" id="tab-upload">
                    <div class="upload-area" id="imageUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>{{ __('Click or drag image here') }}</p>
                        <span class="upload-hint">{{ __('JPG, PNG, GIF (Max 2MB)') }}</span>
                        <input type="file" id="imageFile" accept="image/*" style="display: none;">
                        <button type="button" class="btn-select-file" id="selectImageBtn">
                            <i class="fas fa-folder-open"></i> {{ __('Browse') }}
                        </button>
                    </div>
                    <div class="upload-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <p>{{ __('Uploading...') }}</p>
                    </div>
                </div>
                <div class="tab-content" id="tab-url">
                    <div class="form-group">
                        <label for="imageUrl">{{ __('Image URL') }}</label>
                        <input type="text" id="imageUrl" class="form-control" placeholder="https://example.com/image.jpg">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel-modal">{{ __('Cancel') }}</button>
                <button class="btn-insert-image" id="insertImageBtn">{{ __('Insert Image') }}</button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .content-edit-wrapper {
                max-width: 1000px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Breadcrumb */
            .breadcrumb-nav {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 25px;
                padding: 12px 0;
            }

            .breadcrumb-link {
                color: #6b7280;
                text-decoration: none;
                font-size: 0.8rem;
                transition: color 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .breadcrumb-link:hover {
                color: #7c3aed;
            }

            .breadcrumb-sep {
                font-size: 0.7rem;
                color: #cbd5e1;
            }

            .breadcrumb-current {
                color: #7c3aed;
                font-size: 0.8rem;
                font-weight: 500;
            }

            /* Form Card */
            .form-card {
                background: white;
                border-radius: 28px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            .form-header {
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
                padding: 30px;
                display: flex;
                align-items: center;
                gap: 20px;
                border-bottom: 1px solid #e5e7eb;
                flex-wrap: wrap;
            }

            .header-icon {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .header-icon i {
                font-size: 1.8rem;
                color: white;
            }

            .header-text {
                flex: 1;
            }

            .header-text h2 {
                margin: 0 0 5px;
                font-size: 1.4rem;
                color: #1f2937;
            }

            .header-text p {
                margin: 0;
                color: #6b7280;
                font-size: 0.85rem;
            }

            .header-status .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 30px;
                font-size: 0.75rem;
                font-weight: 500;
            }

            .status-badge.published {
                background: #d1fae5;
                color: #065f46;
            }

            .status-badge.draft {
                background: #fef3c7;
                color: #d97706;
            }

            /* Form */
            .content-form {
                padding: 30px;
            }

            .form-group {
                margin-bottom: 25px;
            }

            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                font-size: 0.85rem;
                color: #374151;
            }

            .required {
                color: #ef4444;
            }

            .input-wrapper {
                position: relative;
            }

            .input-icon {
                position: absolute;
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 1rem;
            }

            body.rtl .input-icon {
                left: auto;
                right: 15px;
            }

            body.rtl .form-control {
                padding-left: 16px;
                padding-right: 45px;
            }

            .form-control {
                width: 100%;
                padding: 12px 16px 12px 45px;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                font-size: 0.85rem;
                transition: all 0.3s ease;
                background: #f9fafb;
            }

            .form-control:focus {
                outline: none;
                border-color: #7c3aed;
                background: white;
                box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
            }

            .form-control.is-invalid {
                border-color: #ef4444;
            }

            .char-counter {
                text-align: right;
                font-size: 0.65rem;
                color: #9ca3af;
                margin-top: 5px;
            }

            /* Type Cards */
            .type-cards {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
            }

            .type-card {
                border: 2px solid #e5e7eb;
                border-radius: 16px;
                padding: 16px;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 10px;
                position: relative;
            }

            .type-card:hover {
                border-color: #c4b5fd;
                transform: translateY(-3px);
            }

            .type-card.selected {
                border-color: #7c3aed;
                background: #f5f3ff;
            }

            .type-icon {
                width: 50px;
                height: 50px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .type-icon.article {
                background: #ede9fe;
                color: #7c3aed;
            }

            .type-icon.video {
                background: #fee2e2;
                color: #dc2626;
            }

            .type-icon.tip {
                background: #fef3c7;
                color: #d97706;
            }

            .type-icon.guide {
                background: #d1fae5;
                color: #059669;
            }

            .type-icon i {
                font-size: 1.3rem;
            }

            .type-info h4 {
                margin: 0;
                font-size: 0.9rem;
                color: #1f2937;
            }

            .type-info p {
                margin: 5px 0 0;
                font-size: 0.7rem;
                color: #6b7280;
            }

            .type-check {
                position: absolute;
                top: 12px;
                right: 12px;
            }

            .type-check i {
                font-size: 1rem;
                color: #9ca3af;
            }

            .type-card.selected .type-check i {
                color: #7c3aed;
                font-family: "Font Awesome 6 Free";
                font-weight: 900;
            }

            .type-card.selected .type-check i::before {
                content: "\f058";
            }

            /* Media URL Group */
            .media-url-group {
                animation: fadeIn 0.3s ease;
            }

            .video-preview {
                margin-top: 15px;
                background: #f9fafb;
                border-radius: 12px;
                overflow: hidden;
            }

            .preview-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px 16px;
                background: #f3f4f6;
                border-bottom: 1px solid #e5e7eb;
                font-size: 0.8rem;
            }

            .preview-header i {
                color: #7c3aed;
            }

            .btn-clear-preview {
                background: none;
                border: none;
                cursor: pointer;
                color: #9ca3af;
                transition: all 0.3s ease;
            }

            .btn-clear-preview:hover {
                color: #ef4444;
            }

            .video-embed {
                padding: 20px;
                text-align: center;
            }

            .video-embed iframe {
                max-width: 100%;
                border-radius: 12px;
            }

            /* Editor Toolbar */
            .editor-toolbar {
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-bottom: none;
                border-radius: 14px 14px 0 0;
                padding: 8px 12px;
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                align-items: center;
            }

            .editor-toolbar button {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 6px 10px;
                cursor: pointer;
                transition: all 0.2s;
                color: #4b5563;
            }

            .editor-toolbar button:hover {
                background: #ede9fe;
                border-color: #c4b5fd;
                color: #7c3aed;
            }

            .editor-toolbar .separator {
                width: 1px;
                background: #e5e7eb;
                margin: 0 5px;
            }

            /* Headings Dropdown */
            .headings-dropdown {
                position: relative;
                display: inline-block;
            }

            .dropdown-toggle {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 6px 10px;
                cursor: pointer;
                transition: all 0.2s;
                color: #4b5563;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .dropdown-toggle:hover {
                background: #ede9fe;
                border-color: #c4b5fd;
                color: #7c3aed;
            }

            .dropdown-toggle i:last-child {
                font-size: 0.7rem;
            }

            .dropdown-menu {
                position: absolute;
                top: 100%;
                left: 0;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                min-width: 150px;
                z-index: 1000;
                display: none;
                margin-top: 5px;
                overflow: hidden;
            }

            body.rtl .dropdown-menu {
                left: auto;
                right: 0;
            }

            .dropdown-menu.show {
                display: block;
            }

            .dropdown-menu button {
                display: block;
                width: 100%;
                padding: 8px 16px;
                border: none;
                background: none;
                text-align: left;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 0.85rem;
                color: #374151;
            }

            body.rtl .dropdown-menu button {
                text-align: right;
            }

            .dropdown-menu button:hover {
                background: #f5f3ff;
                color: #7c3aed;
            }

            .rich-editor {
                border-radius: 0 0 14px 14px;
                resize: vertical;
                font-family: monospace;
            }

            /* Status Toggle */
            .status-toggle {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            .toggle-option {
                flex: 1;
                padding: 16px;
                border: 2px solid #e5e7eb;
                border-radius: 16px;
                cursor: pointer;
                text-align: center;
                transition: all 0.3s ease;
            }

            .toggle-option i {
                font-size: 1.5rem;
                margin-bottom: 8px;
                display: block;
            }

            .toggle-option span {
                display: block;
                font-weight: 600;
                margin-bottom: 4px;
            }

            .toggle-option small {
                font-size: 0.7rem;
                color: #9ca3af;
            }

            .toggle-option.draft.selected {
                border-color: #f59e0b;
                background: #fef3c7;
            }

            .toggle-option.draft.selected i,
            .toggle-option.draft.selected span {
                color: #d97706;
            }

            .toggle-option.publish.selected {
                border-color: #10b981;
                background: #d1fae5;
            }

            .toggle-option.publish.selected i,
            .toggle-option.publish.selected span {
                color: #059669;
            }

            /* Form Actions */
            .form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 15px;
                margin-top: 30px;
                padding-top: 25px;
                border-top: 1px solid #e5e7eb;
            }

            .btn-cancel {
                background: #f3f4f6;
                color: #374151;
                text-decoration: none;
                padding: 12px 28px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 500;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-cancel:hover {
                background: #e5e7eb;
                transform: translateY(-2px);
            }

            .btn-submit {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: white;
                border: none;
                padding: 12px 32px;
                border-radius: 40px;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-submit:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
            }

            .btn-submit:disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }

            /* Modal */
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .modal-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            .modal-container {
                background: white;
                border-radius: 24px;
                max-width: 500px;
                width: 90%;
                transform: scale(0.9);
                transition: transform 0.3s ease;
            }

            .modal-overlay.active .modal-container {
                transform: scale(1);
            }

            .modal-header {
                padding: 20px 24px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modal-header h3 {
                margin: 0;
                font-size: 1.2rem;
            }

            .modal-close {
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
            }

            .modal-body {
                padding: 24px;
            }

            .modal-footer {
                padding: 16px 24px;
                border-top: 1px solid #f0f0f0;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .upload-tabs {
                display: flex;
                gap: 10px;
                margin-bottom: 20px;
                border-bottom: 1px solid #e5e7eb;
            }

            .tab-btn {
                background: none;
                border: none;
                padding: 8px 16px;
                cursor: pointer;
                font-size: 0.85rem;
                color: #6b7280;
                position: relative;
            }

            .tab-btn.active {
                color: #7c3aed;
            }

            .tab-btn.active::after {
                content: '';
                position: absolute;
                bottom: -1px;
                left: 0;
                right: 0;
                height: 2px;
                background: #7c3aed;
            }

            .tab-content {
                display: none;
            }

            .tab-content.active {
                display: block;
            }

            .upload-area {
                border: 2px dashed #e5e7eb;
                border-radius: 16px;
                padding: 30px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .upload-area:hover {
                border-color: #7c3aed;
                background: #f5f3ff;
            }

            .upload-area i {
                font-size: 2rem;
                color: #c4b5fd;
                margin-bottom: 12px;
            }

            .upload-area p {
                margin: 0 0 8px;
                font-size: 0.85rem;
            }

            .upload-hint {
                font-size: 0.7rem;
                color: #9ca3af;
                display: block;
                margin-bottom: 15px;
            }

            .btn-select-file {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 40px;
                cursor: pointer;
                font-size: 0.8rem;
            }

            .upload-progress {
                text-align: center;
                padding: 20px;
            }

            .progress-bar {
                height: 6px;
                background: #e5e7eb;
                border-radius: 3px;
                overflow: hidden;
                margin-bottom: 10px;
            }

            .progress-fill {
                height: 100%;
                width: 0%;
                background: #7c3aed;
                transition: width 0.3s ease;
            }

            .btn-cancel-modal {
                background: #f3f4f6;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn-insert-image {
                background: #7c3aed;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 8px;
                cursor: pointer;
            }

            .form-hint {
                font-size: 0.7rem;
                color: #9ca3af;
                margin-top: 8px;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .error-message {
                color: #ef4444;
                font-size: 0.7rem;
                margin-top: 5px;
                display: none;
            }

            .error-message.show {
                display: block;
            }

            /* SweetAlert z-index fix */
            .swal2-container {
                z-index: 20000 !important;
            }

            .swal2-popup {
                z-index: 20001 !important;
            }

            .swal2-backdrop-show {
                z-index: 19999 !important;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fadeIn 0.5s ease;
            }

            .animate-slide-up {
                animation: slideUp 0.5s ease;
            }

            @media (max-width: 768px) {
                .content-edit-wrapper {
                    padding: 15px;
                }

                .form-header {
                    flex-direction: column;
                    text-align: center;
                }

                .content-form {
                    padding: 20px;
                }

                .type-cards {
                    grid-template-columns: repeat(2, 1fr);
                }

                .status-toggle {
                    flex-direction: column;
                }

                .form-actions {
                    flex-direction: column;
                }

                .btn-cancel,
                .btn-submit {
                    justify-content: center;
                }

                .editor-toolbar {
                    flex-wrap: wrap;
                }
            }

            body.rtl .type-check {
                right: auto;
                left: 12px;
            }

            body.rtl .breadcrumb-sep {
                transform: rotate(180deg);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Set initial selected type
            const currentType = '{{ $content->type }}';

            // Type Card Selection
            const typeCards = document.querySelectorAll('.type-card');
            const typeInput = document.getElementById('type');
            const mediaUrlGroup = document.querySelector('.media-url-group');

            typeCards.forEach(card => {
                if (card.dataset.type === currentType) {
                    card.classList.add('selected');
                }

                card.addEventListener('click', () => {
                    typeCards.forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    const type = card.dataset.type;
                    typeInput.value = type;

                    if (type === 'video') {
                        mediaUrlGroup.style.display = 'block';
                    } else {
                        mediaUrlGroup.style.display = 'none';
                        document.getElementById('videoPreview').style.display = 'none';
                    }
                });
            });

            // Status Toggle
            const currentStatus = '{{ $content->is_published ? "published" : "draft" }}';
            const statusOptions = document.querySelectorAll('.toggle-option');
            const statusInput = document.getElementById('status');

            statusOptions.forEach(option => {
                if (option.dataset.status === currentStatus) {
                    option.classList.add('selected');
                }

                option.addEventListener('click', () => {
                    statusOptions.forEach(o => o.classList.remove('selected'));
                    option.classList.add('selected');
                    statusInput.value = option.dataset.status;
                });
            });

            // Title Character Counter
            const titleInput = document.getElementById('title');
            const titleCounter = document.getElementById('titleCounter');

            function updateTitleCounter() {
                const length = titleInput.value.length;
                titleCounter.textContent = `${length}/255`;
                if (length > 255) {
                    titleCounter.style.color = '#ef4444';
                } else {
                    titleCounter.style.color = '#9ca3af';
                }
            }

            titleInput.addEventListener('input', updateTitleCounter);
            updateTitleCounter();

            // Video URL Preview
            const mediaUrl = document.getElementById('media_url');
            const videoPreview = document.getElementById('videoPreview');
            const videoEmbed = document.getElementById('videoEmbed');
            const clearVideoBtn = document.getElementById('clearVideoBtn');

            function getYouTubeId(url) {
                const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
                const match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
            }

            function getVimeoId(url) {
                const regExp = /vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|)(\d+)(?:|\/\?)/;
                const match = url.match(regExp);
                return match ? match[1] : null;
            }

            function loadVideoPreview() {
                const url = mediaUrl.value.trim();
                if (!url) {
                    videoPreview.style.display = 'none';
                    return;
                }

                const youtubeId = getYouTubeId(url);
                const vimeoId = getVimeoId(url);

                if (youtubeId) {
                    videoEmbed.innerHTML = `<iframe width="100%" height="200" src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allowfullscreen></iframe>`;
                    videoPreview.style.display = 'block';
                } else if (vimeoId) {
                    videoEmbed.innerHTML = `<iframe width="100%" height="200" src="https://player.vimeo.com/video/${vimeoId}" frameborder="0" allowfullscreen></iframe>`;
                    videoPreview.style.display = 'block';
                } else {
                    videoPreview.style.display = 'none';
                }
            }

            mediaUrl.addEventListener('blur', loadVideoPreview);
            if (mediaUrl.value.trim()) {
                loadVideoPreview();
            }

            clearVideoBtn?.addEventListener('click', () => {
                mediaUrl.value = '';
                videoPreview.style.display = 'none';
            });

            // ============ HEADINGS DROPDOWN ============
            const headingsDropdownBtn = document.getElementById('headingsDropdownBtn');
            const headingsDropdownMenu = document.getElementById('headingsDropdownMenu');

            if (headingsDropdownBtn && headingsDropdownMenu) {
                headingsDropdownBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    headingsDropdownMenu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!headingsDropdownBtn.contains(e.target) && !headingsDropdownMenu.contains(e.target)) {
                        headingsDropdownMenu.classList.remove('show');
                    }
                });

                // Handle heading selection
                headingsDropdownMenu.querySelectorAll('button').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const headingTag = btn.dataset.heading;
                        insertHeading(headingTag);
                        headingsDropdownMenu.classList.remove('show');
                    });
                });
            }

            // ============ RICH TEXT EDITOR ============
            const editor = document.getElementById('body');

            // Function to insert heading
            function insertHeading(tag) {
                const start = editor.selectionStart;
                const end = editor.selectionEnd;
                const selectedText = editor.value.substring(start, end);

                let headingHtml;
                if (tag === 'p') {
                    headingHtml = selectedText ? `<p>${selectedText}</p>` : '<p></p>';
                } else {
                    headingHtml = selectedText ? `<${tag}>${selectedText}</${tag}>` : `<${tag}></${tag}>`;
                }

                const before = editor.value.substring(0, start);
                const after = editor.value.substring(end);
                editor.value = before + headingHtml + after;

                // Place cursor inside the heading
                const cursorPos = start + (tag === 'p' ? 3 : tag.length + 2);
                editor.selectionStart = cursorPos;
                editor.selectionEnd = cursorPos;

                editor.focus();
                saveState();
            }

            // Function to wrap selected text with HTML tags
            function wrapText(tag) {
                const start = editor.selectionStart;
                const end = editor.selectionEnd;
                const selectedText = editor.value.substring(start, end);

                if (selectedText) {
                    const before = editor.value.substring(0, start);
                    const after = editor.value.substring(end);
                    const wrappedText = `<${tag}>${selectedText}</${tag}>`;
                    editor.value = before + wrappedText + after;

                    // Move cursor after the inserted tag
                    editor.selectionStart = start + wrappedText.length;
                    editor.selectionEnd = start + wrappedText.length;
                } else {
                    // If no text selected, insert empty tags and place cursor inside
                    const before = editor.value.substring(0, start);
                    const after = editor.value.substring(start);
                    const emptyTag = `<${tag}></${tag}>`;
                    editor.value = before + emptyTag + after;
                    editor.selectionStart = start + tag.length + 2;
                    editor.selectionEnd = start + tag.length + 2;
                }
                editor.focus();
                saveState();
            }

            // Function to wrap with list (ul/ol)
            function wrapWithList(type) {
                const start = editor.selectionStart;
                const end = editor.selectionEnd;
                const selectedText = editor.value.substring(start, end);

                if (selectedText) {
                    const lines = selectedText.split('\n');
                    const listItems = lines.map(line => `<li>${line}</li>`).join('');
                    const listHtml = type === 'ul' ? `<ul>${listItems}</ul>` : `<ol>${listItems}</ol>`;

                    const before = editor.value.substring(0, start);
                    const after = editor.value.substring(end);
                    editor.value = before + listHtml + after;

                    editor.selectionStart = start + listHtml.length;
                    editor.selectionEnd = start + listHtml.length;
                } else {
                    // Insert empty list with one item
                    const emptyList = type === 'ul' ? '<ul><li></li></ul>' : '<ol><li></li></ol>';
                    const before = editor.value.substring(0, start);
                    const after = editor.value.substring(start);
                    editor.value = before + emptyList + after;

                    // Place cursor inside the li
                    const cursorPos = start + (type === 'ul' ? 8 : 9);
                    editor.selectionStart = cursorPos;
                    editor.selectionEnd = cursorPos;
                }
                editor.focus();
                saveState();
            }

            // Function to insert link
            function insertLink() {
                const start = editor.selectionStart;
                const end = editor.selectionEnd;
                const selectedText = editor.value.substring(start, end);

                const url = prompt('{{ __("Enter URL") }}', 'https://');
                if (url) {
                    let linkHtml;
                    if (selectedText) {
                        linkHtml = `<a href="${url}" target="_blank" rel="noopener noreferrer">${selectedText}</a>`;
                    } else {
                        linkHtml = `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`;
                    }

                    const before = editor.value.substring(0, start);
                    const after = editor.value.substring(end);
                    editor.value = before + linkHtml + after;

                    editor.selectionStart = start + linkHtml.length;
                    editor.selectionEnd = start + linkHtml.length;
                    editor.focus();
                    saveState();
                }
            }

            // Function to insert image
            function insertImage(url) {
                const start = editor.selectionStart;
                const imgHtml = `<img src="${url}" alt="Image" style="max-width: 100%; border-radius: 12px; margin: 10px 0;">`;

                const before = editor.value.substring(0, start);
                const after = editor.value.substring(start);
                editor.value = before + imgHtml + after;

                editor.selectionStart = start + imgHtml.length;
                editor.selectionEnd = start + imgHtml.length;
                editor.focus();
                saveState();
            }

            // Undo/Redo functionality
            let undoStack = [editor.value];
            let redoStack = [];
            let isUndoRedo = false;

            function saveState() {
                if (!isUndoRedo && editor.value !== undoStack[undoStack.length - 1]) {
                    undoStack.push(editor.value);
                    redoStack = [];
                    // Keep only last 50 states
                    if (undoStack.length > 50) undoStack.shift();
                }
            }

            function undo() {
                if (undoStack.length > 1) {
                    isUndoRedo = true;
                    redoStack.push(undoStack.pop());
                    editor.value = undoStack[undoStack.length - 1];
                    isUndoRedo = false;
                    editor.focus();
                }
            }

            function redo() {
                if (redoStack.length > 0) {
                    isUndoRedo = true;
                    undoStack.push(redoStack.pop());
                    editor.value = undoStack[undoStack.length - 1];
                    isUndoRedo = false;
                    editor.focus();
                }
            }

            // Auto-save on input (but not during undo/redo)
            editor.addEventListener('input', () => {
                if (!isUndoRedo) {
                    saveState();
                }
            });

            // Toolbar button handlers
            document.querySelectorAll('.editor-toolbar button').forEach(btn => {
                // Skip the headings dropdown button and its children
                if (btn.id === 'headingsDropdownBtn') return;
                if (btn.closest('.headings-dropdown') && btn !== headingsDropdownBtn) return;

                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const command = btn.dataset.command;

                    if (!command) return;

                    switch (command) {
                        case 'bold':
                            wrapText('strong');
                            break;
                        case 'italic':
                            wrapText('em');
                            break;
                        case 'underline':
                            wrapText('u');
                            break;
                        case 'insertUnorderedList':
                            wrapWithList('ul');
                            break;
                        case 'insertOrderedList':
                            wrapWithList('ol');
                            break;
                        case 'createLink':
                            insertLink();
                            break;
                        case 'insertImage':
                            openImageModal();
                            break;
                        case 'undo':
                            undo();
                            break;
                        case 'redo':
                            redo();
                            break;
                    }
                });
            });

            // Image Upload Modal
            const imageModal = document.getElementById('imageUploadModal');

            function openImageModal() {
                imageModal.classList.add('active');
            }

            // Close modal functions
            document.querySelectorAll('.modal-close, .btn-cancel-modal').forEach(btn => {
                btn.addEventListener('click', () => {
                    imageModal.classList.remove('active');
                    // Reset tabs and inputs
                    document.getElementById('imageFile').value = '';
                    document.getElementById('imageUrl').value = '';
                    document.querySelector('.tab-btn[data-tab="upload"]').click();
                });
            });

            // Close modal when clicking outside
            imageModal.addEventListener('click', (e) => {
                if (e.target === imageModal) {
                    imageModal.classList.remove('active');
                }
            });

            // Image Upload Tabs
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const tabId = btn.dataset.tab;
                    tabBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    tabContents.forEach(content => content.classList.remove('active'));
                    document.getElementById(`tab-${tabId}`).classList.add('active');
                });
            });

            // Upload Image
            const uploadArea = document.getElementById('imageUploadArea');
            const imageFile = document.getElementById('imageFile');
            const selectImageBtn = document.getElementById('selectImageBtn');
            const uploadProgress = document.querySelector('.upload-progress');
            const progressFill = document.querySelector('.progress-fill');

            if (selectImageBtn) {
                selectImageBtn.addEventListener('click', () => imageFile.click());
            }

            if (uploadArea) {
                uploadArea.addEventListener('click', (e) => {
                    if (e.target !== selectImageBtn && !selectImageBtn.contains(e.target)) {
                        imageFile.click();
                    }
                });

                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.style.borderColor = '#7c3aed';
                    uploadArea.style.background = '#f5f3ff';
                });

                uploadArea.addEventListener('dragleave', () => {
                    uploadArea.style.borderColor = '#e5e7eb';
                    uploadArea.style.background = 'transparent';
                });

                uploadArea.addEventListener('drop', async (e) => {
                    e.preventDefault();
                    const file = e.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        await uploadAndInsertImage(file);
                    }
                });
            }

            imageFile?.addEventListener('change', async (e) => {
                if (e.target.files && e.target.files[0]) {
                    await uploadAndInsertImage(e.target.files[0]);
                }
            });

            async function uploadAndInsertImage(file) {
                const formData = new FormData();
                formData.append('upload', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                uploadArea.style.display = 'none';
                uploadProgress.style.display = 'block';

                let progress = 0;
                const interval = setInterval(() => {
                    progress += 10;
                    progressFill.style.width = `${progress}%`;
                    if (progress >= 100) clearInterval(interval);
                }, 200);

                try {
                    const response = await fetch('{{ route("admin.content.upload-image") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.uploaded && data.url) {
                        insertImage(data.url);
                        imageModal.classList.remove('active');
                    } else {
                        throw new Error('Upload failed');
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Upload Failed") }}',
                        text: '{{ __("Could not upload image. Please try again.") }}',
                        confirmButtonColor: '#7c3aed'
                    });
                } finally {
                    uploadArea.style.display = 'block';
                    uploadProgress.style.display = 'none';
                    progressFill.style.width = '0%';
                    imageFile.value = '';
                }
            }

            // Insert Image URL
            document.getElementById('insertImageBtn')?.addEventListener('click', () => {
                const urlTab = document.getElementById('tab-url');
                if (urlTab.classList.contains('active')) {
                    const imageUrl = document.getElementById('imageUrl').value.trim();
                    if (imageUrl) {
                        insertImage(imageUrl);
                        imageModal.classList.remove('active');
                        document.getElementById('imageUrl').value = '';
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("No URL") }}',
                            text: '{{ __("Please enter an image URL.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                    }
                }
            });

            // Form Submit
            const form = document.getElementById('contentForm');
            if (form) {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const submitBtn = document.getElementById('submitBtn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnSpinner = submitBtn.querySelector('.btn-spinner');

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    submitBtn.disabled = true;

                    const formData = new FormData(form);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success!") }}',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                background: '#fff',
                                color: '#1f2937'
                            });
                            window.location.href = data.redirect_url;
                        } else {
                            if (data.errors) {
                                for (const [field, messages] of Object.entries(data.errors)) {
                                    const errorDiv = document.getElementById(`${field}-error`);
                                    if (errorDiv) {
                                        errorDiv.textContent = messages[0];
                                        errorDiv.classList.add('show');
                                        const input = document.getElementById(field);
                                        if (input) input.classList.add('is-invalid');
                                    }
                                }
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Validation Error") }}',
                                    text: '{{ __("Please check the form for errors.") }}',
                                    confirmButtonColor: '#7c3aed'
                                });
                            } else {
                                await Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Error!") }}',
                                    text: data.message || '{{ __("Failed to update content") }}',
                                    confirmButtonColor: '#7c3aed'
                                });
                            }
                        }
                    } catch (error) {
                        console.error('Submit error:', error);
                        await Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error!") }}',
                            text: '{{ __("Network error. Please try again.") }}',
                            confirmButtonColor: '#7c3aed'
                        });
                    } finally {
                        btnText.style.display = 'inline-flex';
                        btnSpinner.style.display = 'none';
                        submitBtn.disabled = false;
                    }
                });
            }

            // Clear validation errors on input
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function () {
                    this.classList.remove('is-invalid');
                    const errorId = this.id + '-error';
                    const errorDiv = document.getElementById(errorId);
                    if (errorDiv) {
                        errorDiv.classList.remove('show');
                        errorDiv.textContent = '';
                    }
                });
            });
        </script>
    @endpush
@endsection