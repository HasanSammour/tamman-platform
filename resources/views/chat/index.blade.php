{{-- resources/views/chat/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Chat System') . ' - ' . __('Tamman'))

@section('page-title', __('Chat System'))

@section('content')
    <div class="chat-container">
        <!-- Sidebar - Desktop always visible, Mobile hidden initially -->
        <div class="chat-sidebar desktop-sidebar" id="chatSidebar">
            <div class="chat-sidebar-header">
                <h3><i class="fas fa-comments"></i> {{ __('Conversations') }}</h3>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchConversations" placeholder="{{ __('Search conversations...') }}">
                </div>
            </div>
            <div class="conversations-list" id="conversationsList">
                <div class="loading-conversations">
                    <i class="fas fa-spinner fa-spin"></i> {{ __('Loading conversations...') }}
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main" id="chatMain">
            <div class="chat-placeholder" id="chatPlaceholder">
                <i class="fas fa-comments"></i>
                <h3>{{ __('Select a conversation to start messaging') }}</h3>
                <p>{{ __('Choose a conversation from the left sidebar') }}</p>
            </div>

            <div id="chatWindow" style="display: none; flex-direction: column; height: 100%;">
                <!-- Mobile Back Button -->
                <div class="mobile-back-btn" id="mobileBackBtn">
                    <button onclick="closeMobileChat()">
                        <i class="fas fa-arrow-left"></i>
                        <span>{{ __('Back') }}</span>
                    </button>
                </div>

                <div class="chat-header" id="chatHeader"></div>
                <div class="chat-messages" id="chatMessages"></div>
                <div class="chat-input-area" id="chatInputArea"></div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Button (FAB) -->
    <button class="mobile-fab" id="mobileFab">
        <i class="fas fa-comment-dots"></i>
    </button>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Edit Message Modal -->
    <div id="editModal" class="custom-modal">
        <div class="custom-modal-content edit-modal-content">
            <div class="custom-modal-header edit-modal-header">
                <div class="edit-modal-icon">
                    <i class="fas fa-pen"></i>
                </div>
                <h3>{{ __('Edit Message') }}</h3>
                <button class="custom-modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="custom-modal-body edit-modal-body">
                <textarea id="editMessageContent" class="edit-textarea-modern" rows="4"
                    placeholder="{{ __('Edit your message...') }}"></textarea>
                <div class="edit-hint">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ __('Press Ctrl + Enter to save') }}</span>
                </div>
            </div>
            <div class="custom-modal-footer edit-modal-footer">
                <button class="edit-cancel-btn" onclick="closeEditModal()">{{ __('Cancel') }}</button>
                <button class="edit-save-btn" id="saveEditBtn">
                    <i class="fas fa-save"></i>
                    <span>{{ __('Save Changes') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Options Modal -->
    <div id="deleteModal" class="custom-modal">
        <div class="custom-modal-content delete-modal-content">
            <div class="custom-modal-header delete-modal-header">
                <div class="delete-modal-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <h3>{{ __('Delete Message') }}</h3>
                <button class="custom-modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="custom-modal-body delete-modal-body">
                <p class="delete-warning-text">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ __('This action cannot be undone.') }}
                </p>
                <div class="delete-options-container">
                    <button class="delete-option-card delete-for-me" id="deleteForMeBtn">
                        <div class="delete-option-icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <div class="delete-option-info">
                            <div class="delete-option-title">{{ __('Delete for me') }}</div>
                            <div class="delete-option-desc">{{ __('Remove this message only from your view') }}</div>
                        </div>
                        <div class="delete-option-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </button>
                    <button class="delete-option-card delete-for-everyone" id="deleteForEveryoneBtn">
                        <div class="delete-option-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="delete-option-info">
                            <div class="delete-option-title">{{ __('Delete for everyone') }}</div>
                            <div class="delete-option-desc">{{ __('Remove this message for both you and the recipient') }}
                            </div>
                        </div>
                        <div class="delete-option-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </button>
                </div>
                <div id="deleteErrorMsg" class="delete-error-message" style="display: none;">
                    <i class="fas fa-clock"></i>
                    <span>{{ __('Messages can only be deleted for everyone within 1 hour of sending.') }}</span>
                </div>
            </div>
            <div class="custom-modal-footer delete-modal-footer">
                <button class="delete-cancel-btn" onclick="closeDeleteModal()">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>

    <!-- Lock/Unlock Confirmation Modal -->
    <div id="lockModal" class="custom-modal">
        <div class="custom-modal-content lock-modal-content">
            <div class="custom-modal-header">
                <h3 id="lockModalTitle"><i class="fas fa-lock"></i> {{ __('End Session') }}</h3>
                <button class="custom-modal-close" onclick="closeLockModal()">&times;</button>
            </div>
            <div class="custom-modal-body">
                <p id="lockModalMessage">
                    {{ __('Are you sure you want to end this session? The patient will not be able to send messages.') }}
                </p>
            </div>
            <div class="custom-modal-footer">
                <button class="btn-cancel" id="lockModalCancelBtn">{{ __('Cancel') }}</button>
                <button class="btn-save" id="lockModalConfirmBtn">{{ __('Yes, end session') }}</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toastNotification" class="toast-notification">
        <span id="toastMessage"></span>
    </div>
@endsection

@push('styles')
    <style>
        /* ==================== BASE STYLES (DESKTOP FIRST) ==================== */
        .chat-container {
            display: flex;
            height: calc(100vh - 200px);
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* Desktop Sidebar - Always visible */
        .chat-sidebar {
            width: 380px;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            background: #f9fafb;
        }

        .chat-sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            background: white;
        }

        .chat-sidebar-header h3 {
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
        }

        .chat-sidebar-header h3 i {
            color: #7c3aed;
            margin-right: 8px;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .search-box input {
            width: 100%;
            padding: 10px 12px 10px 38px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.85rem;
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #7c3aed;
            background: white;
        }

        .conversations-list {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
            display: flex;
            flex-direction: column;
        }

        .loading-conversations {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
        }

        .empty-conversations {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .empty-conversations i {
            font-size: 3rem;
            color: #c4b5fd;
            margin-bottom: 15px;
        }

        .empty-conversations p {
            margin-bottom: 15px;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 16px;
            margin-bottom: 4px;
        }

        .conversation-item:hover {
            background: #f3f4f6;
        }

        .conversation-item.active {
            background: #ede9fe;
        }

        .conversation-item.disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .conversation-avatar {
            position: relative;
            flex-shrink: 0;
        }

        .conversation-avatar img {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .online-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            background: #10b981;
            border: 2px solid white;
            border-radius: 50%;
            display: block;
        }

        .lock-icon {
            position: absolute;
            bottom: -2px;
            right: -2px;
            background: #ef4444;
            color: white;
            font-size: 0.6rem;
            padding: 2px;
            border-radius: 50%;
            border: 2px solid white;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .conversation-info {
            flex: 1;
            min-width: 0;
        }

        .conversation-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .conversation-last-message {
            font-size: 0.75rem;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-meta {
            text-align: right;
            flex-shrink: 0;
        }

        .conversation-time {
            font-size: 0.65rem;
            color: #9ca3af;
        }

        .unread-badge {
            background: #7c3aed;
            color: white;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            margin-top: 5px;
            display: inline-block;
        }

        .locked-badge {
            background: #ef4444;
            color: white;
            font-size: 0.6rem;
            padding: 2px 6px;
            border-radius: 10px;
            margin-top: 5px;
            display: inline-block;
        }

        /* Upcoming session badge in conversation list */
        .upcoming-session-badge {
            display: inline-block;
            background: #fef3c7;
            color: #d97706;
            font-size: 0.6rem;
            padding: 2px 8px;
            border-radius: 20px;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Chat Header Badge */
        .chat-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 500;
            margin-left: 10px;
        }

        .chat-header-badge i {
            font-size: 0.65rem;
        }

        .chat-header-badge.active {
            background: #d1fae5;
            color: #065f46;
        }

        .chat-header-badge.upcoming {
            background: #fef3c7;
            color: #92400e;
        }

        .chat-header-badge.general {
            background: #f3f4f6;
            color: #6b7280;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .chat-placeholder {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #9ca3af;
        }

        .chat-placeholder i {
            font-size: 4rem;
            margin-bottom: 16px;
            color: #c4b5fd;
        }

        .chat-placeholder h3 {
            font-size: 1.2rem;
            color: #374151;
            margin-bottom: 8px;
        }

        #chatWindow {
            height: 100%;
        }

        .chat-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
        }

        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .chat-avatar {
            position: relative;
        }

        .chat-avatar img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .chat-user-details {
            display: flex;
            flex-direction: column;
        }

        .chat-user-details h4 {
            margin: 0;
            font-size: 1rem;
        }

        .user-status {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .chat-actions {
            display: flex;
            gap: 10px;
        }

        .lock-chat-btn,
        .unlock-chat-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .lock-chat-btn {
            color: #ef4444;
        }

        .lock-chat-btn:hover {
            background: #fee2e2;
        }

        .unlock-chat-btn {
            color: #10b981;
        }

        .unlock-chat-btn:hover {
            background: #d1fae5;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f9fafb;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .message {
            display: flex;
            margin-bottom: 8px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message.received {
            justify-content: flex-start;
        }

        .message-content {
            max-width: 70%;
            padding: 10px 16px;
            border-radius: 20px;
            position: relative;
        }

        .message.sent .message-content {
            background: #7c3aed;
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.received .message-content {
            background: white;
            color: #1f2937;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .message.deleted .message-content {
            background: #4b5563 !important;
            color: #e5e7eb !important;
        }

        .deleted-message-text {
            display: flex;
            align-items: center;
            gap: 8px;
            font-style: italic;
        }

        .message-text {
            word-wrap: break-word;
            line-height: 1.4;
        }

        .message-time {
            font-size: 0.6rem;
            margin-top: 4px;
            opacity: 0.7;
        }

        .message.sent .message-time {
            text-align: right;
        }

        .message-actions {
            position: absolute;
            top: -20px;
            right: 0;
            display: none;
            gap: 5px;
            background: white;
            padding: 4px 8px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .message:hover .message-actions {
            display: flex;
        }

        .message-action-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.7rem;
            color: #6b7280;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .message-action-btn:hover {
            color: #7c3aed;
            background: #f3f4f6;
        }

        .system-message {
            text-align: center;
            margin: 16px 0;
        }

        .system-message span {
            background: #f3f4f6;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.7rem;
            color: #6b7280;
            display: inline-block;
        }

        .chat-input-area {
            padding: 16px 20px;
            border-top: 1px solid #e5e7eb;
            background: white;
        }

        .input-wrapper {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        #messageInput {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            resize: none;
            font-size: 0.9rem;
            max-height: 100px;
            font-family: inherit;
        }

        #messageInput:focus {
            outline: none;
            border-color: #7c3aed;
        }

        .send-btn {
            background: #7c3aed;
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .send-btn:hover {
            background: #6d28d9;
            transform: scale(1.05);
        }

        .typing-indicator {
            font-size: 0.7rem;
            color: #9ca3af;
            margin-bottom: 8px;
            padding-left: 15px;
        }

        .chat-disabled {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }

        /* Custom Modal Base Styles */
        .custom-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }

        .custom-modal.show {
            display: flex;
            animation: fadeIn 0.2s ease;
        }

        .custom-modal-content {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 450px;
            animation: slideUp 0.3s ease;
            overflow: hidden;
        }

        .lock-modal-content {
            max-width: 400px;
        }

        .custom-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
        }

        .custom-modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #1f2937;
        }

        .custom-modal-header h3 i {
            color: #7c3aed;
            margin-right: 8px;
        }

        .custom-modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #9ca3af;
            transition: all 0.2s ease;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-modal-close:hover {
            background: #f3f4f6;
            color: #ef4444;
        }

        .custom-modal-body {
            padding: 24px;
            max-height: 400px;
            overflow-y: auto;
        }

        .custom-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: #f9fafb;
        }

        .btn-cancel {
            padding: 8px 20px;
            background: #f3f4f6;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .btn-save {
            padding: 8px 24px;
            background: #7c3aed;
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-save:hover {
            background: #6d28d9;
        }

        .btn-save:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* ==================== EDIT MODAL STYLES ==================== */
        .edit-modal-content {
            max-width: 500px;
            border-radius: 28px;
            overflow: hidden;
            animation: modalSlideIn 0.3s ease;
        }

        .edit-modal-header {
            padding: 24px 24px 16px 24px;
            border-bottom: none;
            background: linear-gradient(135deg, #fff 0%, #eff6ff 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .edit-modal-header .edit-modal-icon {
            width: 56px;
            height: 56px;
            background: #dbeafe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .edit-modal-header .edit-modal-icon i {
            font-size: 28px;
            color: #3b82f6;
        }

        .edit-modal-header h3 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .edit-modal-header .custom-modal-close {
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .edit-modal-body {
            padding: 0 24px;
        }

        .edit-textarea-modern {
            width: 100%;
            padding: 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 20px;
            font-size: 0.9rem;
            font-family: inherit;
            resize: vertical;
            transition: all 0.2s ease;
            background: #f9fafb;
            line-height: 1.5;
        }

        .edit-textarea-modern:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .edit-hint {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding: 8px 12px;
            background: #f3f4f6;
            border-radius: 12px;
            font-size: 0.7rem;
            color: #6b7280;
        }

        .edit-hint i {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .edit-modal-footer {
            padding: 16px 24px 24px 24px;
            border-top: none;
            background: white;
            gap: 12px;
        }

        .edit-cancel-btn {
            padding: 12px 24px;
            background: #f3f4f6;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            color: #4b5563;
            transition: all 0.2s ease;
            flex: 1;
        }

        .edit-cancel-btn:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .edit-save-btn {
            padding: 12px 28px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex: 1;
        }

        .edit-save-btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .edit-save-btn:active {
            transform: translateY(0);
        }

        .edit-save-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .edit-save-btn.loading {
            background: #93c5fd;
            cursor: wait;
        }

        .edit-save-btn.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* ==================== DELETE MODAL STYLES ==================== */
        .delete-modal-content {
            max-width: 480px;
            border-radius: 28px;
            overflow: hidden;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .delete-modal-header {
            padding: 24px 24px 16px 24px;
            border-bottom: none;
            background: linear-gradient(135deg, #fff 0%, #fef2f2 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .delete-modal-header .delete-modal-icon {
            width: 56px;
            height: 56px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .delete-modal-header .delete-modal-icon i {
            font-size: 28px;
            color: #ef4444;
        }

        .delete-modal-header h3 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .delete-modal-header .custom-modal-close {
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .delete-modal-body {
            padding: 0 24px;
        }

        .delete-warning-text {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            font-size: 0.8rem;
            color: #92400e;
            margin-bottom: 24px;
        }

        .delete-warning-text i {
            font-size: 1rem;
            color: #f59e0b;
        }

        .delete-options-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .delete-option-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.25s ease;
            width: 100%;
            text-align: left;
        }

        .delete-option-card:hover {
            border-color: #ef4444;
            background: #fef2f2;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);
        }

        .delete-option-icon {
            width: 48px;
            height: 48px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.25s ease;
        }

        .delete-for-me .delete-option-icon {
            background: #f3f4f6;
            color: #6b7280;
        }

        .delete-for-me:hover .delete-option-icon {
            background: #e5e7eb;
            color: #4b5563;
        }

        .delete-for-everyone .delete-option-icon {
            background: #fee2e2;
            color: #ef4444;
        }

        .delete-for-everyone:hover .delete-option-icon {
            background: #fecaca;
            color: #dc2626;
        }

        .delete-option-icon i {
            font-size: 1.4rem;
        }

        .delete-option-info {
            flex: 1;
        }

        .delete-option-title {
            font-weight: 700;
            font-size: 1rem;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .delete-option-desc {
            font-size: 0.7rem;
            color: #6b7280;
            line-height: 1.3;
        }

        .delete-option-arrow {
            width: 28px;
            height: 28px;
            border-radius: 14px;
            background: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
            flex-shrink: 0;
        }

        .delete-option-arrow i {
            font-size: 0.7rem;
            color: #9ca3af;
            transition: all 0.25s ease;
        }

        .delete-option-card:hover .delete-option-arrow {
            background: #ef4444;
        }

        .delete-option-card:hover .delete-option-arrow i {
            color: white;
            transform: translateX(2px);
        }

        .delete-error-message {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: #fef2f2;
            border-radius: 16px;
            font-size: 0.75rem;
            color: #991b1b;
            margin-top: 8px;
        }

        .delete-error-message i {
            font-size: 1rem;
            flex-shrink: 0;
        }

        .delete-modal-footer {
            padding: 16px 24px 24px 24px;
            border-top: none;
            background: white;
            justify-content: center;
        }

        .delete-cancel-btn {
            width: 100%;
            padding: 14px 20px;
            background: #f3f4f6;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            color: #4b5563;
            transition: all 0.2s ease;
        }

        .delete-cancel-btn:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #1f2937;
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 0.85rem;
            z-index: 10001;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toast-notification.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast-notification.success {
            background: #10b981;
        }

        .toast-notification.error {
            background: #ef4444;
        }

        /* Mobile Elements - Hidden on Desktop */
        .mobile-fab,
        .mobile-back-btn,
        .mobile-overlay {
            display: none;
        }

        /* RTL Support */
        body.rtl .search-box i {
            left: auto;
            right: 12px;
        }

        body.rtl .search-box input {
            padding: 10px 38px 10px 12px;
        }

        body.rtl .conversation-meta {
            text-align: left;
        }

        body.rtl .message.sent .message-content {
            border-bottom-right-radius: 20px;
            border-bottom-left-radius: 4px;
        }

        body.rtl .message.received .message-content {
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 4px;
        }

        /* RTL for Edit Modal */
        body.rtl .edit-modal-header .custom-modal-close {
            right: auto;
            left: 20px;
        }

        body.rtl .edit-hint i {
            margin-left: 8px;
            margin-right: 0;
        }

        body.rtl .edit-save-btn i {
            margin-left: 8px;
            margin-right: 0;
        }

        /* RTL for Delete Modal */
        body.rtl .delete-modal-header .custom-modal-close {
            right: auto;
            left: 20px;
        }

        body.rtl .delete-option-card {
            text-align: right;
        }

        body.rtl .delete-option-arrow i {
            transform: rotate(180deg);
        }

        body.rtl .delete-option-card:hover .delete-option-arrow i {
            transform: rotate(180deg) translateX(-2px);
        }

        body.rtl .delete-warning-text {
            border-left: none;
            border-right: 4px solid #f59e0b;
        }

        body.rtl .delete-warning-text i {
            margin-left: 10px;
            margin-right: 0;
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
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .btn-retry {
            background: #7c3aed;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 40px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-retry:hover {
            background: #6d28d9;
            transform: translateY(-2px);
        }

        /* ==================== MOBILE RESPONSIVE STYLES ==================== */
        @media (max-width: 768px) {
            .chat-container {
                border-radius: 0;
                height: calc(100vh - 150px);
                position: relative;
            }

            .chat-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 85%;
                max-width: 320px;
                height: 100vh;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                border-radius: 0;
                box-shadow: none;
            }

            .chat-sidebar.mobile-open {
                transform: translateX(0);
                box-shadow: 2px 0 20px rgba(0, 0, 0, 0.15);
            }

            .mobile-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .mobile-overlay.active {
                display: block;
            }

            .mobile-fab {
                display: flex;
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #7c3aed;
                color: white;
                border: none;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 100;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .mobile-fab:active {
                transform: scale(0.95);
            }

            .mobile-fab i {
                font-size: 1.5rem;
            }

            .mobile-back-btn {
                display: block;
                padding: 12px 16px;
                background: white;
                border-bottom: 1px solid #e5e7eb;
            }

            .mobile-back-btn button {
                background: none;
                border: none;
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                color: #7c3aed;
                font-weight: 500;
                padding: 8px 0;
            }

            .mobile-back-btn button i {
                font-size: 1.1rem;
            }

            .chat-main {
                width: 100%;
            }

            .chat-placeholder {
                padding: 20px;
            }

            .chat-placeholder i {
                font-size: 3rem;
            }

            .chat-placeholder h3 {
                font-size: 1rem;
            }

            .chat-placeholder p {
                font-size: 0.8rem;
            }

            .message-content {
                max-width: 85%;
            }

            .chat-header {
                padding: 12px 15px;
            }

            .chat-avatar img {
                width: 40px;
                height: 40px;
            }

            .chat-user-details h4 {
                font-size: 0.9rem;
            }

            .user-status {
                font-size: 0.65rem;
            }

            .chat-header-badge {
                padding: 2px 8px;
                font-size: 0.6rem;
                margin-left: 8px;
            }

            .chat-input-area {
                padding: 12px 15px;
            }

            #messageInput {
                padding: 10px 14px;
                font-size: 0.85rem;
            }

            .send-btn {
                width: 38px;
                height: 38px;
            }

            .send-btn i {
                font-size: 0.9rem;
            }

            .message-text {
                font-size: 0.85rem;
            }

            .message-time {
                font-size: 0.55rem;
            }

            /* RTL Mobile Support */
            body.rtl .chat-sidebar {
                left: auto;
                right: 0;
                transform: translateX(100%);
            }

            body.rtl .chat-sidebar.mobile-open {
                transform: translateX(0);
            }

            body.rtl .mobile-fab {
                right: auto;
                left: 20px;
            }

            body.rtl .mobile-back-btn button i {
                transform: rotate(180deg);
            }
        }

        @media (max-width: 480px) {

            .conversation-avatar img,
            .avatar-placeholder {
                width: 45px;
                height: 45px;
                font-size: 1rem;
            }

            .conversation-name {
                font-size: 0.85rem;
            }

            .conversation-last-message {
                font-size: 0.7rem;
            }

            .message-content {
                max-width: 90%;
                padding: 8px 12px;
            }

            .custom-modal-content {
                width: 95%;
                margin: 20px;
            }

            .toast-notification {
                bottom: 80px;
                right: 15px;
                left: 15px;
                width: auto;
                text-align: center;
                justify-content: center;
            }

            body.rtl .toast-notification {
                right: 15px;
                left: 15px;
            }

            .mobile-fab {
                bottom: 15px;
                right: 15px;
                width: 50px;
                height: 50px;
            }

            .mobile-fab i {
                font-size: 1.3rem;
            }

            body.rtl .mobile-fab {
                right: auto;
                left: 15px;
            }

            .edit-modal-content,
            .delete-modal-content {
                max-width: calc(100% - 32px);
                margin: 0 16px;
                border-radius: 24px;
            }

            .edit-modal-header,
            .delete-modal-header {
                padding: 20px 20px 12px 20px;
            }

            .edit-modal-header .edit-modal-icon,
            .delete-modal-header .delete-modal-icon {
                width: 48px;
                height: 48px;
            }

            .edit-modal-header .edit-modal-icon i,
            .delete-modal-header .delete-modal-icon i {
                font-size: 24px;
            }

            .edit-modal-header h3,
            .delete-modal-header h3 {
                font-size: 1.2rem;
            }

            .edit-modal-body,
            .delete-modal-body {
                padding: 0 20px;
            }

            .edit-textarea-modern {
                padding: 14px;
                font-size: 0.85rem;
            }

            .edit-hint {
                font-size: 0.65rem;
            }

            .edit-modal-footer,
            .delete-modal-footer {
                padding: 12px 20px 20px 20px;
            }

            .edit-cancel-btn,
            .edit-save-btn,
            .delete-cancel-btn {
                padding: 10px 20px;
                font-size: 0.8rem;
            }

            .delete-option-card {
                padding: 14px 16px;
            }

            .delete-option-icon {
                width: 40px;
                height: 40px;
            }

            .delete-option-icon i {
                font-size: 1.2rem;
            }

            .delete-option-title {
                font-size: 0.9rem;
            }

            .delete-option-desc {
                font-size: 0.65rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

    <script>
        // Show error if invalid user ID was provided
        @if(isset($errorMessage) && $errorMessage)
            Swal.fire({
                icon: 'error',
                title: '{{ __("Invalid Conversation") }}',
                text: '{{ $errorMessage }}',
                confirmButtonColor: '#7c3aed'
            });
        @endif
    </script>

    <script>
                        // Configuration
                        const currentUserId = {{ auth()->id() }};
        const currentUserRole = '{{ auth()->user()->hasRole("specialist") ? "specialist" : "patient" }}';
        const pusherKey = '{{ env("PUSHER_APP_KEY") }}';
        const pusherCluster = '{{ env("PUSHER_APP_CLUSTER", "mt1") }}';
        const currentLocale = '{{ app()->getLocale() }}';

        // Global variables
        let currentConversationId = null;
        let currentReceiverId = null;
        let currentPage = 1;
        let lastPage = 1;
        let isLoading = false;
        let typingTimeout = null;
        let pusher = null;
        let channel = null;
        let refreshInterval = null;
        let currentEditingMessageId = null;
        let currentDeletingMessageId = null;
        let canDeleteForEveryone = false;
        let currentIsLocked = false;
        let pendingLockAction = null;

        // Helper function
        function getElement(id) {
            return document.getElementById(id);
        }

        // Check if mobile
        function isMobile() {
            return window.innerWidth <= 768;
        }

        // Mobile functions
        function openMobileSidebar() {
            if (!isMobile()) return;
            const sidebar = getElement('chatSidebar');
            const overlay = getElement('mobileOverlay');
            if (sidebar) {
                sidebar.classList.add('mobile-open');
                if (overlay) overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileSidebar() {
            if (!isMobile()) return;
            const sidebar = getElement('chatSidebar');
            const overlay = getElement('mobileOverlay');
            if (sidebar) {
                sidebar.classList.remove('mobile-open');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        window.closeMobileChat = function () {
            if (!isMobile()) return;
            const fab = getElement('mobileFab');
            const backBtn = getElement('mobileBackBtn');
            const placeholder = getElement('chatPlaceholder');
            const chatWindow = getElement('chatWindow');

            if (fab) fab.style.display = 'flex';
            if (backBtn) backBtn.style.display = 'none';
            if (placeholder) placeholder.style.display = 'flex';
            if (chatWindow) chatWindow.style.display = 'none';

            currentConversationId = null;
        }

        // Toast function
        function showToast(message, type = 'success') {
            const toast = getElement('toastNotification');
            const toastMessage = getElement('toastMessage');
            if (toast && toastMessage) {
                toastMessage.innerHTML = '';
                let iconHtml = '';
                if (type === 'success') {
                    iconHtml = '<i class="fas fa-check-circle"></i> ';
                } else if (type === 'error') {
                    iconHtml = '<i class="fas fa-exclamation-circle"></i> ';
                } else if (type === 'info') {
                    iconHtml = '<i class="fas fa-info-circle"></i> ';
                }
                toastMessage.innerHTML = iconHtml + message;
                toast.className = 'toast-notification ' + type;
                toast.classList.add('show');
                if (currentLocale === 'ar') {
                    toast.style.right = 'auto';
                    toast.style.left = '30px';
                } else {
                    toast.style.right = '30px';
                    toast.style.left = 'auto';
                }
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            }
        }

        // Connection status
        let isOnline = navigator.onLine;
        let pusherReconnectAttempts = 0;
        let connectionCheckInterval = null;

        // Check internet connection
        function checkInternetConnection() {
            if (!navigator.onLine) {
                showToast('{{ __("No internet connection. Please check your network.") }}', 'error');
                return false;
            }
            return true;
        }

        // Show connection lost message
        function showConnectionLostMessage() {
            const placeholder = document.getElementById('chatPlaceholder');
            if (placeholder && placeholder.style.display !== 'none') {
                placeholder.innerHTML = `
                                                                    <i class="fas fa-wifi-slash" style="font-size: 4rem; color: #ef4444;"></i>
                                                                    <h3>{{ __("Connection Lost") }}</h3>
                                                                    <p>{{ __("Unable to connect to chat server. Please check your internet connection.") }}</p>
                                                                    <button onclick="retryConnection()" class="btn-retry" style="margin-top: 15px; background: #7c3aed; color: white; border: none; padding: 8px 20px; border-radius: 40px; cursor: pointer;">
                                                                        <i class="fas fa-sync-alt"></i> {{ __("Retry Connection") }}
                                                                    </button>
                                                                `;
            }

            const chatWindow = document.getElementById('chatWindow');
            if (chatWindow && chatWindow.style.display === 'flex') {
                const messagesContainer = document.getElementById('chatMessages');
                if (messagesContainer) {
                    messagesContainer.innerHTML = `
                                                                        <div class="connection-error-message" style="text-align: center; padding: 40px;">
                                                                            <i class="fas fa-wifi-slash" style="font-size: 3rem; color: #ef4444; margin-bottom: 15px;"></i>
                                                                            <p style="color: #6b7280;">{{ __("Connection lost. Reconnecting...") }}</p>
                                                                        </div>
                                                                    `;
                }
            }
        }

        // Retry connection
        window.retryConnection = function () {
            showToast('{{ __("Attempting to reconnect...") }}', 'info');
            setTimeout(() => {
                location.reload();
            }, 1500);
        }

        // Monitor online/offline status
        window.addEventListener('online', function () {
            showToast('{{ __("Internet connection restored. Reconnecting to chat...") }}', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        });

        window.addEventListener('offline', function () {
            showConnectionLostMessage();
            showToast('{{ __("Internet connection lost. Please check your network.") }}', 'error');
        });

        // Lock Modal functions
        let lockConfirmCallback = null;

        window.openLockModal = function (action, conversationId, onConfirm) {
            pendingLockAction = action;
            lockConfirmCallback = onConfirm;
            const lockModal = getElement('lockModal');
            const modalTitle = getElement('lockModalTitle');
            const modalMessage = getElement('lockModalMessage');
            const confirmBtn = getElement('lockModalConfirmBtn');

            if (action === 'lock') {
                if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-lock"></i> ' + '{{ __("End Session") }}';
                if (modalMessage) modalMessage.innerHTML = '{{ __("Are you sure you want to end this session? The patient will not be able to send messages.") }}';
                if (confirmBtn) confirmBtn.innerHTML = '{{ __("Yes, end session") }}';
            } else {
                if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-unlock-alt"></i> ' + '{{ __("Reopen Session") }}';
                if (modalMessage) modalMessage.innerHTML = '{{ __("Are you sure you want to reopen this session? The patient will be able to send messages again.") }}';
                if (confirmBtn) confirmBtn.innerHTML = '{{ __("Yes, reopen session") }}';
            }
            if (lockModal) lockModal.classList.add('show');
        }

        window.closeLockModal = function () {
            const lockModal = getElement('lockModal');
            if (lockModal) lockModal.classList.remove('show');
            pendingLockAction = null;
            lockConfirmCallback = null;
        }

        window.lockConversation = async function () {
            const confirmBtn = document.getElementById('lockModalConfirmBtn');
            const originalText = confirmBtn ? confirmBtn.innerHTML : '';

            openLockModal('lock', currentConversationId, async () => {
                if (confirmBtn) {
                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}';
                }

                try {
                    const response = await fetch(`/chat/conversations/${currentConversationId}/lock`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = originalText;
                    }

                    if (data.success) {
                        currentIsLocked = true;

                        if (currentUserRole === 'patient') {
                            const inputArea = getElement('chatInputArea');
                            if (inputArea) {
                                inputArea.innerHTML = `<div class="chat-disabled"><i class="fas fa-lock"></i><p>{{ __("This session has ended. You cannot send messages.") }}</p></div>`;
                            }
                        }

                        const chatUserDetails = document.querySelector('.chat-user-details');
                        if (chatUserDetails) {
                            const existingBadge = chatUserDetails.querySelector('.locked-badge');
                            if (!existingBadge) {
                                chatUserDetails.insertAdjacentHTML('beforeend', `<span class="locked-badge"><i class="fas fa-lock"></i> {{ __("Session Ended") }}</span>`);
                            }
                        }

                        const conversationItem = document.querySelector(`.conversation-item[data-id="${currentConversationId}"]`);
                        if (conversationItem) {
                            const avatarDiv = conversationItem.querySelector('.conversation-avatar');
                            if (avatarDiv && !avatarDiv.querySelector('.lock-icon')) {
                                avatarDiv.insertAdjacentHTML('beforeend', `<span class="lock-icon"><i class="fas fa-lock"></i></span>`);
                            }
                            const infoDiv = conversationItem.querySelector('.conversation-info');
                            if (infoDiv && !infoDiv.querySelector('.locked-badge')) {
                                infoDiv.insertAdjacentHTML('beforeend', `<div class="locked-badge"><i class="fas fa-lock"></i> {{ __("Session Ended") }}</div>`);
                            }
                            conversationItem.setAttribute('data-is-locked', 'true');
                        }

                        if (currentUserRole === 'specialist') {
                            const chatActions = document.querySelector('.chat-actions');
                            if (chatActions) {
                                chatActions.innerHTML = `<button class="unlock-chat-btn" onclick="unlockConversation()" title="{{ __("Reopen Session") }}"><i class="fas fa-unlock-alt"></i></button>`;
                            }
                        }

                        if (data.system_message) {
                            addSystemMessageToChat(data.system_message.content, data.system_message.id);
                        }

                        showToast('{{ __("Session ended successfully") }}', 'success');
                        closeLockModal();
                    } else {
                        showToast(data.message || '{{ __("Error ending session") }}', 'error');
                        closeLockModal();
                    }
                } catch (error) {
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = originalText;
                    }
                    console.error('Error:', error);
                    showToast('{{ __("Network error") }}', 'error');
                    closeLockModal();
                }
            });
        }

        window.unlockConversation = async function () {
            const confirmBtn = document.getElementById('lockModalConfirmBtn');
            const originalText = confirmBtn ? confirmBtn.innerHTML : '';

            openLockModal('unlock', currentConversationId, async () => {
                if (confirmBtn) {
                    confirmBtn.disabled = true;
                    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}';
                }

                try {
                    const response = await fetch(`/chat/conversations/${currentConversationId}/unlock`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = originalText;
                    }

                    if (data.success) {
                        currentIsLocked = false;

                        if (currentUserRole === 'patient') {
                            const inputArea = getElement('chatInputArea');
                            if (inputArea) {
                                inputArea.innerHTML = `
                                                                                                    <div class="typing-indicator" id="typingIndicator" style="display: none;">
                                                                                                        <i class="fas fa-ellipsis-h"></i> <span id="typingText">{{ __("Someone is typing...") }}</span>
                                                                                                    </div>
                                                                                                    <div class="input-wrapper">
                                                                                                        <textarea id="messageInput" rows="1" placeholder="{{ __("Type your message...") }}"></textarea>
                                                                                                        <button id="sendBtn" class="send-btn">
                                                                                                            <i class="fas fa-paper-plane"></i>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                `;
                                setupMessageInput();
                            }
                        }

                        const chatUserDetails = document.querySelector('.chat-user-details');
                        if (chatUserDetails) {
                            const badge = chatUserDetails.querySelector('.locked-badge');
                            if (badge) badge.remove();
                        }

                        const conversationItem = document.querySelector(`.conversation-item[data-id="${currentConversationId}"]`);
                        if (conversationItem) {
                            const lockIcon = conversationItem.querySelector('.lock-icon');
                            if (lockIcon) lockIcon.remove();
                            const lockedBadge = conversationItem.querySelector('.locked-badge');
                            if (lockedBadge) lockedBadge.remove();
                            conversationItem.setAttribute('data-is-locked', 'false');
                        }

                        if (currentUserRole === 'specialist') {
                            const chatActions = document.querySelector('.chat-actions');
                            if (chatActions) {
                                chatActions.innerHTML = `<button class="lock-chat-btn" onclick="lockConversation()" title="{{ __("End Session") }}"><i class="fas fa-lock"></i></button>`;
                            }
                        }

                        if (data.system_message) {
                            addSystemMessageToChat(data.system_message.content, data.system_message.id);
                        }

                        showToast('{{ __("Session reopened successfully") }}', 'success');
                        closeLockModal();
                    } else {
                        showToast(data.message || '{{ __("Error reopening session") }}', 'error');
                        closeLockModal();
                    }
                } catch (error) {
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = originalText;
                    }
                    console.error('Error:', error);
                    showToast('{{ __("Network error") }}', 'error');
                    closeLockModal();
                }
            });
        }

        // Fetch session info for chat header badge
        async function fetchAndUpdateSessionBadge(conversationId) {
            if (!conversationId) return;
            try {
                const response = await fetch(`/chat/conversations/${conversationId}/session-info`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    const badgeContainer = document.querySelector('.chat-badge-container');
                    if (badgeContainer) {
                        let badgeHtml = '';
                        if (data.type === 'active') {
                            badgeHtml = `<span class="chat-header-badge active"><i class="fas fa-video"></i> ${data.text}</span>`;
                        } else if (data.type === 'upcoming') {
                            badgeHtml = `<span class="chat-header-badge upcoming"><i class="fas fa-calendar-alt"></i> ${data.text}: ${data.session_time}</span>`;
                        } else {
                            badgeHtml = `<span class="chat-header-badge general"><i class="fas fa-comments"></i> ${data.text}</span>`;
                        }
                        badgeContainer.innerHTML = badgeHtml;
                    }
                }
            } catch (error) {
                console.error('Error fetching session badge:', error);
            }
        }

        // Edit Modal functions
        window.openEditModal = function (messageId, currentContent) {
            console.log('openEditModal called with ID:', messageId);
            if (!messageId || messageId.toString().startsWith('temp_')) {
                console.error('Invalid message ID for edit:', messageId);
                showToast('{{ __("Cannot edit this message right now. Please refresh and try again.") }}', 'error');
                return;
            }
            currentEditingMessageId = messageId;

            const messageElement = document.querySelector(`.message[data-id="${messageId}"]`);
            let actualContent = currentContent;

            if (messageElement) {
                const textElement = messageElement.querySelector('.message-text');
                if (textElement) {
                    actualContent = textElement.innerText || textElement.textContent;
                    console.log('Content from DOM:', actualContent);
                }
            }

            const editMessageContent = getElement('editMessageContent');
            const editModal = getElement('editModal');

            const saveBtn = getElement('saveEditBtn');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save"></i> <span>{{ __("Save Changes") }}</span>';
            }

            if (editMessageContent) editMessageContent.value = actualContent;
            if (editModal) editModal.classList.add('show');
            if (editMessageContent) {
                editMessageContent.focus();
                editMessageContent.setSelectionRange(editMessageContent.value.length, editMessageContent.value.length);
            }
        }

        window.closeEditModal = function () {
            const editModal = getElement('editModal');
            if (editModal) editModal.classList.remove('show');

            const saveBtn = getElement('saveEditBtn');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save"></i> <span>{{ __("Save Changes") }}</span>';
            }

            currentEditingMessageId = null;
        }

        // Delete Modal functions
        window.openDeleteModal = function (messageId, canDeleteEveryone) {
            if (!messageId || messageId.toString().startsWith('temp_')) {
                showToast('{{ __("Cannot delete this message right now. Please refresh and try again.") }}', 'error');
                return;
            }
            currentDeletingMessageId = messageId;
            canDeleteForEveryone = canDeleteEveryone;
            const deleteForEveryoneBtn = getElement('deleteForEveryoneBtn');
            const deleteErrorMsg = getElement('deleteErrorMsg');
            if (deleteForEveryoneBtn && deleteErrorMsg) {
                if (canDeleteForEveryone) {
                    deleteForEveryoneBtn.style.display = 'flex';
                    deleteErrorMsg.style.display = 'none';
                } else {
                    deleteForEveryoneBtn.style.display = 'none';
                    deleteErrorMsg.style.display = 'flex';
                }
            }
            const deleteModal = getElement('deleteModal');
            if (deleteModal) deleteModal.classList.add('show');
        }

        window.closeDeleteModal = function () {
            const deleteModal = getElement('deleteModal');
            if (deleteModal) deleteModal.classList.remove('show');

            const deleteForMeBtn = getElement('deleteForMeBtn');
            const deleteForEveryoneBtn = getElement('deleteForEveryoneBtn');

            if (deleteForMeBtn) {
                deleteForMeBtn.disabled = false;
                deleteForMeBtn.style.opacity = '1';
                deleteForMeBtn.innerHTML = `
                                                                                    <div class="delete-option-icon">
                                                                                        <i class="fas fa-user-slash"></i>
                                                                                    </div>
                                                                                    <div class="delete-option-info">
                                                                                        <div class="delete-option-title">{{ __("Delete for me") }}</div>
                                                                                        <div class="delete-option-desc">{{ __("Remove this message only from your view") }}</div>
                                                                                    </div>
                                                                                    <div class="delete-option-arrow">
                                                                                        <i class="fas fa-chevron-right"></i>
                                                                                    </div>
                                                                                `;
            }

            if (deleteForEveryoneBtn) {
                deleteForEveryoneBtn.disabled = false;
                deleteForEveryoneBtn.style.opacity = '1';
                deleteForEveryoneBtn.innerHTML = `
                                                                                    <div class="delete-option-icon">
                                                                                        <i class="fas fa-ban"></i>
                                                                                    </div>
                                                                                    <div class="delete-option-info">
                                                                                        <div class="delete-option-title">{{ __("Delete for everyone") }}</div>
                                                                                        <div class="delete-option-desc">{{ __("Remove this message for both you and the recipient") }}</div>
                                                                                    </div>
                                                                                    <div class="delete-option-arrow">
                                                                                        <i class="fas fa-chevron-right"></i>
                                                                                    </div>
                                                                                `;
            }

            currentDeletingMessageId = null;
        }

        // ==================== PUSHER INITIALIZATION ====================
        function initPusher() {
            // Check if Pusher library is loaded
            if (typeof Pusher === 'undefined') {
                console.warn('Pusher library not loaded yet');
                showToast('{{ __("Loading chat service...") }}', 'info');
                // Try again in 2 seconds
                setTimeout(initPusher, 2000);
                return;
            }

            // Check internet connection
            if (!navigator.onLine) {
                showConnectionLostMessage();
                return;
            }
            try {
                pusher = new Pusher(pusherKey, {
                    cluster: pusherCluster,
                    forceTLS: true,
                    enabledTransports: ['ws', 'wss', 'xhr_streaming', 'xhr_polling']
                });

                // Connection success handler
                pusher.connection.bind('connected', function () {
                    console.log('✅ Pusher connected');
                    pusherReconnectAttempts = 0;
                    // Clear any error messages
                    const errorContainer = document.querySelector('.connection-error-message');
                    if (errorContainer) errorContainer.remove();
                });

                // Connection error handler
                pusher.connection.bind('error', function (err) {
                    console.error('Pusher connection error:', err);
                    pusherReconnectAttempts++;

                    if (pusherReconnectAttempts <= 3) {
                        showToast('{{ __("Connecting to chat service...") }}', 'info');
                    } else {
                        showToast('{{ __("Chat service is temporarily unavailable. Messages will be delivered when connection restores.") }}', 'error');
                    }
                });

                // Connection failed handler (no internet)
                pusher.connection.bind('failed', function () {
                    console.warn('Pusher connection failed');
                    if (!navigator.onLine) {
                        showConnectionLostMessage();
                    } else {
                        showToast('{{ __("Unable to connect to chat server. Please refresh the page.") }}', 'error');
                    }
                });

                // Connection disconnected handler
                pusher.connection.bind('disconnected', function () {
                    console.warn('Pusher disconnected');
                    if (!navigator.onLine) {
                        showConnectionLostMessage();
                    }
                });

                const channelName = 'private-chat.' + currentUserId;
                channel = pusher.subscribe(channelName);

                // Channel subscription error handler
                channel.bind('pusher:subscription_error', function (status) {
                    console.error('Pusher subscription error:', status);
                    showToast('{{ __("Chat connection error. Please refresh the page.") }}', 'error');
                });

                channel.bind('MessageSent', function (data) {
                    console.log('🔔 MESSAGE SENT EVENT RECEIVED:', JSON.stringify(data, null, 2));
                    refreshConversationsList();
                    updateChatUnreadCount();

                    const message = data.message || data;
                    const conversationId = data.conversation_id || message.conversation_id;
                    const senderId = message.sender_id || data.sender_id;

                    const isSystemMessage = message.is_system_message === true || message.is_system_message === 1;
                    // Check for Lock messages (English OR Arabic)
                    const isLockMessage = isSystemMessage && (
                        message.content.includes('Session ended') ||
                        message.content.includes('انتهت الجلسة') ||
                        message.content.includes('View only') ||
                        message.content.includes('قراءة فقط')
                    );

                    // Check for Unlock messages (English OR Arabic)
                    const isUnlockMessage = isSystemMessage && (
                        message.content.includes('Session reopened') ||
                        message.content.includes('تم فتح الجلسة') ||
                        message.content.includes('فتح الجلسة') ||
                        message.content.includes('can send messages')
                    );

                    if (isLockMessage) {
                        currentIsLocked = true;
                        const inputArea = document.getElementById('chatInputArea');
                        if (inputArea && currentUserRole === 'patient') {
                            inputArea.innerHTML = `<div class="chat-disabled"><i class="fas fa-lock"></i><p>{{ __("This session has ended. You cannot send messages.") }}</p></div>`;
                        }
                        const chatUserDetails = document.querySelector('.chat-user-details');
                        if (chatUserDetails && !chatUserDetails.querySelector('.locked-badge')) {
                            chatUserDetails.insertAdjacentHTML('beforeend', `<span class="locked-badge"><i class="fas fa-lock"></i> {{ __("Session Ended") }}</span>`);
                        }
                        if (currentUserRole === 'specialist') {
                            const chatActions = document.querySelector('.chat-actions');
                            if (chatActions) {
                                chatActions.innerHTML = `<button class="unlock-chat-btn" onclick="unlockConversation()" title="{{ __("Reopen Session") }}"><i class="fas fa-unlock-alt"></i></button>`;
                            }
                        }
                        addSystemMessageToChat(message.content, message.id);
                        scrollToBottom();
                        return;
                    }

                    if (isUnlockMessage) {
                        console.log('🔓 UNLOCK MESSAGE RECEIVED - Updating UI');
                        currentIsLocked = false;

                        // Only update if this conversation is currently open
                        if (currentConversationId == conversationId) {
                            // 1. RESTORE INPUT AREA (for patient)
                            if (currentUserRole === 'patient') {
                                const inputArea = document.getElementById('chatInputArea');
                                if (inputArea) {
                                    inputArea.innerHTML = `
                                                                            <div class="typing-indicator" id="typingIndicator" style="display: none;">
                                                                                <i class="fas fa-ellipsis-h"></i> <span id="typingText">{{ __("Someone is typing...") }}</span>
                                                                            </div>
                                                                            <div class="input-wrapper">
                                                                                <textarea id="messageInput" rows="1" placeholder="{{ __("Type your message...") }}"></textarea>
                                                                                <button id="sendBtn" class="send-btn">
                                                                                    <i class="fas fa-paper-plane"></i>
                                                                                </button>
                                                                            </div>
                                                                        `;
                                    setupMessageInput();
                                }
                            }

                            // 2. REMOVE LOCK BADGE FROM CHAT HEADER
                            const chatUserDetails = document.querySelector('.chat-user-details');
                            if (chatUserDetails) {
                                const badge = chatUserDetails.querySelector('.locked-badge');
                                if (badge) badge.remove();
                            }

                            // 3. UPDATE LOCK/UNLOCK BUTTONS FOR SPECIALIST
                            if (currentUserRole === 'specialist') {
                                const chatActions = document.querySelector('.chat-actions');
                                if (chatActions) {
                                    chatActions.innerHTML = `<button class="lock-chat-btn" onclick="lockConversation()" title="{{ __("End Session") }}"><i class="fas fa-lock"></i></button>`;
                                }
                            }
                        }

                        // 4. UPDATE SIDEBAR CONVERSATION ITEM (remove lock icon and badge)
                        const conversationItem = document.querySelector(`.conversation-item[data-id="${conversationId}"]`);
                        if (conversationItem) {
                            const lockIcon = conversationItem.querySelector('.lock-icon');
                            if (lockIcon) lockIcon.remove();
                            const lockedBadge = conversationItem.querySelector('.locked-badge');
                            if (lockedBadge) lockedBadge.remove();
                            conversationItem.setAttribute('data-is-locked', 'false');
                        }

                        // 5. ADD SYSTEM MESSAGE TO CHAT (only if this conversation is open)
                        if (currentConversationId == conversationId) {
                            addSystemMessageToChat(message.content, message.id);
                            scrollToBottom();
                        }
                        return;
                    }

                    if (isSystemMessage) {
                        if (currentConversationId == conversationId) {
                            addSystemMessageToChat(message.content, message.id);
                            scrollToBottom();
                        }
                        return;
                    }

                    if (currentConversationId == conversationId && senderId != currentUserId) {
                        if (!document.querySelector(`.message[data-id="${message.id}"]`)) {
                            const newMessage = {
                                id: message.id,
                                content: message.content,
                                sent_at: 'Just now',
                                is_mine: false,
                                is_system_message: false,
                                is_deleted_for_everyone: false,
                                therapy_session_id: message.therapy_session_id,
                                session_date: message.session_date,
                                edited_at: null
                            };
                            appendMessage(newMessage, false);
                            scrollToBottom();
                            markMessagesAsRead(currentConversationId);
                        }
                    }
                    refreshConversationsList();
                });

                channel.bind('UserTyping', function (data) {
                    if (currentConversationId === data.conversation_id && data.user_id != currentUserId) {
                        showTypingIndicator(data.user_name);
                    }
                });

                channel.bind('MessageEdited', function (data) {
                    console.log('MessageEdited event received:', data);
                    if (currentConversationId == data.conversation_id) {
                        const messageElement = document.querySelector(`.message[data-id="${data.id}"]`);
                        if (messageElement) {
                            const textElement = messageElement.querySelector('.message-text');
                            if (textElement) textElement.innerHTML = escapeHtml(data.content);
                            const timeElement = messageElement.querySelector('.message-time');
                            if (timeElement && !timeElement.innerHTML.includes('edited')) {
                                const currentTime = timeElement.innerHTML.split('<i')[0];
                                timeElement.innerHTML = `${currentTime} <i class="fas fa-edit"></i> {{ __('edited') }}`;
                            }
                        }
                    }
                    refreshConversationsList();
                });

                channel.bind('MessageDeleted', function (data) {
                    if (currentConversationId == data.conversation_id) {
                        const messageElement = document.querySelector(`.message[data-id="${data.id}"]`);
                        if (messageElement) {
                            messageElement.classList.add('deleted');
                            const textElement = messageElement.querySelector('.message-text');
                            if (textElement) {
                                textElement.innerHTML = '<div class="deleted-message-text"><i class="fas fa-ban"></i> <span>{{ __("This message was deleted") }}</span></div>';
                            }
                            const actions = messageElement.querySelector('.message-actions');
                            if (actions) actions.remove();
                        }
                        setTimeout(() => refreshConversationsList(), 500);
                    } else {
                        refreshConversationsList();
                    }
                });
            } catch (error) {
                console.error('Pusher initialization error:', error);
                showToast('{{ __("Chat service unavailable. Please refresh the page.") }}', 'error');
            }
        }

        // ==================== LOAD CONVERSATION ====================
        window.loadConversation = async function (conversationId, userId, userName, userAvatar, isLocked, canSend, canLock, canUnlock) {
            // Validate that the conversation exists
            if (!conversationId || !userId) {
                showToast('{{ __("Invalid conversation. Please select a valid chat.") }}', 'error');
                return;
            }

            currentConversationId = conversationId;
            currentReceiverId = userId;
            currentIsLocked = isLocked;

            const chatPlaceholder = getElement('chatPlaceholder');
            const chatWindow = getElement('chatWindow');
            if (chatPlaceholder) chatPlaceholder.style.display = 'none';
            if (chatWindow) chatWindow.style.display = 'flex';

            updateChatHeader(userName, userAvatar, isLocked, canLock, canUnlock);
            await fetchAndUpdateSessionBadge(conversationId);
            await fetchMessages(conversationId);
            await markMessagesAsRead(conversationId);
            updateInputArea(canSend, isLocked);

            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.id == conversationId) {
                    item.classList.add('active');
                }
            });

            if (isMobile()) {
                const fab = getElement('mobileFab');
                const backBtn = getElement('mobileBackBtn');
                if (fab) fab.style.display = 'none';
                if (backBtn) backBtn.style.display = 'block';
                closeMobileSidebar();
            }

            if (typeof updateChatUnreadCount === 'function') {
                updateChatUnreadCount();
            }
        }

        function updateChatHeader(userName, userAvatar, isLocked, canLock, canUnlock) {
            let lockHtml = '';
            if (currentUserRole === 'specialist') {
                if (canLock && !isLocked) {
                    lockHtml = `<button class="lock-chat-btn" onclick="lockConversation()" title="End Session"><i class="fas fa-lock"></i></button>`;
                }
                if (canUnlock && isLocked) {
                    lockHtml = `<button class="unlock-chat-btn" onclick="unlockConversation()" title="Reopen Session"><i class="fas fa-unlock-alt"></i></button>`;
                }
            }
            const lockStatusHtml = isLocked ? `<span class="locked-badge"><i class="fas fa-lock"></i> {{ __('Session Ended') }}</span>` : '';
            const onlineStatusHtml = `<span class="user-status" id="userOnlineStatus">Loading...</span>`;
            const chatHeader = getElement('chatHeader');
            if (chatHeader) {
                chatHeader.innerHTML = `<div class="chat-user-info"><div class="chat-avatar">${userAvatar ? `<img src="${userAvatar}" alt="${userName}">` : `<div class="avatar-placeholder" style="width: 45px; height: 45px; font-size: 1rem;">${userName ? userName.charAt(0) : 'U'}</div>`}</div><div class="chat-user-details"><div style="display: flex; align-items: center; flex-wrap: wrap; gap: 8px;"><h4>${escapeHtml(userName || 'User')}</h4><div class="chat-badge-container"></div></div>${onlineStatusHtml}${lockStatusHtml}</div></div><div class="chat-actions">${lockHtml}</div>`;
            }
        }

        function updateInputArea(canSend, isLocked) {
            const inputArea = getElement('chatInputArea');
            if (!inputArea) return;
            if (canSend) {
                inputArea.innerHTML = `<div class="typing-indicator" id="typingIndicator" style="display: none;"><i class="fas fa-ellipsis-h"></i> <span id="typingText">{{ __("Someone is typing...") }}</span></div><div class="input-wrapper"><textarea id="messageInput" rows="1" placeholder="{{ __("Type your message...") }}"></textarea><button id="sendBtn" class="send-btn"><i class="fas fa-paper-plane"></i></button></div>`;
                setupMessageInput();
            } else {
                inputArea.innerHTML = `<div class="chat-disabled"><i class="fas fa-lock"></i><p>${isLocked ? '{{ __("This session has ended. You cannot send messages.") }}' : '{{ __("You cannot send messages in this conversation.") }}'}</p></div>`;
            }
        }

        function setupMessageInput() {
            const messageInput = getElement('messageInput');
            const sendBtn = getElement('sendBtn');
            if (!messageInput) return;
            let typingTimeoutLocal = null;
            let isTypingCurrently = false;
            let lastTypingTime = 0;

            function sendTyping(isTyping) {
                if (!currentConversationId) return;
                const now = Date.now();
                if (isTyping && (now - lastTypingTime) < 2000) return;
                lastTypingTime = now;
                fetch('{{ route("chat.typing") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ conversation_id: currentConversationId, is_typing: isTyping })
                }).catch(error => console.error('Typing error:', error));
            }

            messageInput.addEventListener('input', function () {
                if (!isTypingCurrently) {
                    isTypingCurrently = true;
                    sendTyping(true);
                }
                if (typingTimeoutLocal) clearTimeout(typingTimeoutLocal);
                typingTimeoutLocal = setTimeout(() => {
                    if (isTypingCurrently) {
                        isTypingCurrently = false;
                        sendTyping(false);
                    }
                }, 1500);
            });

            messageInput.addEventListener('blur', function () {
                if (typingTimeoutLocal) clearTimeout(typingTimeoutLocal);
                if (isTypingCurrently) {
                    isTypingCurrently = false;
                    sendTyping(false);
                }
            });

            messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            if (sendBtn) {
                sendBtn.addEventListener('click', sendMessage);
            }
        }

        // ==================== SEND MESSAGE ====================
        async function sendMessage() {
            // Check internet connection first
            if (!navigator.onLine) {
                showToast('{{ __("No internet connection. Please check your network and try again.") }}', 'error');
                return;
            }

            const messageInput = getElement('messageInput');
            const content = messageInput?.value.trim();
            if (!content || !currentConversationId) return;
            if (currentUserRole === 'patient' && currentIsLocked) {
                showToast('{{ __("This session has ended. You cannot send messages.") }}', 'error');
                return;
            }
            const tempId = 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
            const tempMessage = {
                id: tempId,
                content: content,
                sent_at: 'Sending...',
                is_mine: true,
                is_system_message: false,
                is_deleted_for_everyone: false,
                edited_at: null
            };
            appendMessage(tempMessage, true);
            scrollToBottom();
            if (messageInput) {
                messageInput.value = '';
                messageInput.style.height = 'auto';
            }
            const sendBtn = getElement('sendBtn');
            if (sendBtn) {
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
            try {
                const response = await fetch('{{ route("chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ conversation_id: currentConversationId, content: content })
                });
                const data = await response.json();
                if (data.success) {
                    const tempElement = document.querySelector(`.message[data-id="${tempId}"]`);
                    if (tempElement) {
                        tempElement.setAttribute('data-id', data.message.id);
                        const timeElement = tempElement.querySelector('.message-time');
                        if (timeElement) timeElement.innerHTML = 'Just now';
                        const actionsContainer = tempElement.querySelector('.message-actions');
                        if (actionsContainer) {
                            actionsContainer.innerHTML = `<button class="message-action-btn" onclick="openEditModal(${data.message.id}, '${escapeHtml(content)}')"><i class="fas fa-edit"></i></button><button class="message-action-btn" onclick="showDeleteOptions(${data.message.id})"><i class="fas fa-trash"></i></button>`;
                        } else {
                            const messageContent = tempElement.querySelector('.message-content');
                            if (messageContent) {
                                const actionsHtml = `<div class="message-actions"><button class="message-action-btn" onclick="openEditModal(${data.message.id}, '${escapeHtml(content)}')"><i class="fas fa-edit"></i></button><button class="message-action-btn" onclick="showDeleteOptions(${data.message.id})"><i class="fas fa-trash"></i></button></div>`;
                                messageContent.insertAdjacentHTML('beforeend', actionsHtml);
                            }
                        }
                        refreshConversationsList();
                    }
                } else {
                    const tempElement = document.querySelector(`.message[data-id="${tempId}"]`);
                    if (tempElement) tempElement.remove();
                    showToast(data.message || '{{ __("Error sending message") }}', 'error');
                }
            } catch (error) {
                const tempElement = document.querySelector(`.message[data-id="${tempId}"]`);
                if (tempElement) {
                    const textElement = tempElement.querySelector('.message-text');
                    if (textElement) {
                        textElement.innerHTML = '<span style="color: #ef4444;">{{ __("Failed to send. Click to retry.") }}</span>';
                        textElement.style.cursor = 'pointer';
                        textElement.onclick = () => sendMessage();
                    }
                    const timeElement = tempElement.querySelector('.message-time');
                    if (timeElement) timeElement.innerHTML = 'Failed';
                } else {
                    showToast('{{ __("Network error") }}', 'error');
                }
            } finally {
                if (sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                }
            }
        }

        // ==================== FETCH MESSAGES ====================
        async function fetchMessages(conversationId, page = 1, append = false) {
            if (isLoading) return;
            isLoading = true;
            const messagesContainer = getElement('chatMessages');
            try {
                let url = `/chat/conversations/${conversationId}?page=${page}`;
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    currentPage = data.pagination.current_page;
                    lastPage = data.pagination.last_page;
                    if (!append && messagesContainer) messagesContainer.innerHTML = '';
                    displayMessages(data.messages, append);
                    if (!append) scrollToBottom();
                }
            } catch (error) {
                console.error('Error fetching messages:', error);
                if (!append && messagesContainer) {
                    messagesContainer.innerHTML = '<div class="messages-loading" style="text-align: center; padding: 20px; color: #ef4444;">{{ __("Error loading messages. Please refresh.") }}</div>';
                }
            } finally {
                isLoading = false;
            }
        }

        // ==================== DISPLAY MESSAGES ====================
        function displayMessages(messages, append = false) {
            const container = getElement('chatMessages');
            if (!container) return;
            let html = '';
            messages.forEach(msg => {
                if (msg.is_system_message) {
                    html += `<div class="system-message"><span><i class="fas fa-info-circle"></i> ${escapeHtml(msg.content)}</span></div>`;
                    return;
                }
                let timeHtml = msg.sent_at;
                let contentHtml = msg.content;
                let messageClass = '';
                let showActions = false;
                if (msg.is_deleted_for_everyone === true || msg.is_deleted_for_everyone === 1) {
                    contentHtml = '<div class="deleted-message-text"><i class="fas fa-ban"></i> <span>{{ __("This message was deleted") }}</span></div>';
                    messageClass = 'deleted';
                    showActions = false;
                } else if (msg.edited_at) {
                    timeHtml += ` <i class="fas fa-edit"></i> {{ __('edited') }}`;
                    showActions = msg.is_mine;
                } else {
                    showActions = msg.is_mine;
                }
                if (msg.is_mine) {
                    html += `<div class="message sent ${messageClass}" data-id="${msg.id}"><div class="message-content"><div class="message-text">${contentHtml}</div><div class="message-time">${timeHtml}</div>${showActions ? `<div class="message-actions"><button class="message-action-btn" onclick="openEditModal(${msg.id}, '${escapeHtml(msg.content)}')"><i class="fas fa-edit"></i></button><button class="message-action-btn" onclick="showDeleteOptions(${msg.id})"><i class="fas fa-trash"></i></button></div>` : ''}</div></div>`;
                } else {
                    html += `<div class="message received ${messageClass}" data-id="${msg.id}"><div class="message-content"><div class="message-text">${contentHtml}</div><div class="message-time">${timeHtml}</div></div></div>`;
                }
            });
            if (append) {
                container.insertAdjacentHTML('afterbegin', html);
            } else {
                container.innerHTML = html;
            }
        }

        function appendMessage(message, isMine) {
            const container = document.getElementById('chatMessages');
            if (!container) return;
            if (message.is_system_message === true || message.is_system_message === 1) return;
            if (document.querySelector(`.message[data-id="${message.id}"]`)) return;
            let timeHtml = message.sent_at || 'Just now';
            let contentHtml = message.content;
            let messageClass = '';
            let showActions = false;
            if (message.is_deleted_for_everyone === true || message.is_deleted_for_everyone === 1) {
                contentHtml = '<div class="deleted-message-text"><i class="fas fa-ban"></i> <span>{{ __("This message was deleted") }}</span></div>';
                messageClass = 'deleted';
                showActions = false;
            } else {
                if (isMine && !message.id.toString().startsWith('temp_')) {
                    showActions = true;
                }
            }
            if (message.edited_at && !message.is_deleted_for_everyone) {
                timeHtml += ` <i class="fas fa-edit"></i> {{ __('edited') }}`;
            }
            const actionsHtml = showActions ? `<div class="message-actions"><button class="message-action-btn" onclick="openEditModal(${message.id}, '${escapeHtml(message.content)}')"><i class="fas fa-edit"></i></button><button class="message-action-btn" onclick="showDeleteOptions(${message.id})"><i class="fas fa-trash"></i></button></div>` : '';
            const html = `<div class="message ${isMine ? 'sent' : 'received'} ${messageClass}" data-id="${message.id}"><div class="message-content"><div class="message-text">${contentHtml}</div><div class="message-time">${timeHtml}</div>${actionsHtml}</div></div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

        function addSystemMessageToChat(content, messageId = null) {
            const container = document.getElementById('chatMessages');
            if (!container) return;
            if (messageId && document.querySelector(`.system-message[data-id="${messageId}"]`)) return;
            const html = `<div class="system-message" data-id="${messageId || 'temp_' + Date.now()}"><span><i class="fas fa-info-circle"></i> ${escapeHtml(content)}</span></div>`;
            container.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }

        // ==================== DELETE OPTIONS ====================
        window.showDeleteOptions = async function (messageId) {
            if (!messageId || messageId.toString().startsWith('temp_')) {
                showToast('{{ __("Cannot delete this message right now. Please refresh and try again.") }}', 'error');
                return;
            }
            try {
                const response = await fetch(`/chat/messages/${messageId}/delete-options`, {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const data = await response.json();
                openDeleteModal(messageId, data.can_delete_for_everyone);
            } catch (error) {
                openDeleteModal(messageId, false);
            }
        }

        // ==================== EDIT MESSAGE ====================
        const saveEditBtn = getElement('saveEditBtn');
        if (saveEditBtn) {
            const newSaveBtn = saveEditBtn.cloneNode(true);
            saveEditBtn.parentNode.replaceChild(newSaveBtn, saveEditBtn);

            newSaveBtn.addEventListener('click', async function (e) {
                e.preventDefault();
                e.stopPropagation();

                const editMessageContentElem = getElement('editMessageContent');
                const newContent = editMessageContentElem?.value.trim();

                if (!newContent) {
                    showToast('{{ __("Please enter a message") }}', 'error');
                    return;
                }

                if (!currentEditingMessageId) {
                    showToast('{{ __("No message selected for editing") }}', 'error');
                    return;
                }

                const messageId = currentEditingMessageId;
                const messageElement = document.querySelector(`.message[data-id="${messageId}"]`);
                const oldContent = messageElement?.querySelector('.message-text')?.innerHTML;

                const originalButtonHtml = this.innerHTML;

                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>{{ __("Saving...") }}</span>';

                if (messageElement) {
                    const textElement = messageElement.querySelector('.message-text');
                    if (textElement) textElement.innerHTML = escapeHtml(newContent);
                }

                try {
                    const response = await fetch(`/chat/messages/${messageId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ content: newContent })
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (messageElement) {
                            const timeElement = messageElement.querySelector('.message-time');
                            if (timeElement && !timeElement.innerHTML.includes('edited')) {
                                const currentTime = timeElement.innerHTML.split('<i')[0];
                                timeElement.innerHTML = `${currentTime} <i class="fas fa-edit"></i> {{ __('edited') }}`;
                            }
                        }
                        refreshConversationsList();
                        closeEditModal();
                        showToast('{{ __("Message updated successfully") }}', 'success');
                    } else {
                        if (messageElement && oldContent) {
                            const textElement = messageElement.querySelector('.message-text');
                            if (textElement) textElement.innerHTML = oldContent;
                        }
                        showToast(data.message || '{{ __("Error editing message") }}', 'error');
                        this.disabled = false;
                        this.innerHTML = originalButtonHtml;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    if (messageElement && oldContent) {
                        const textElement = messageElement.querySelector('.message-text');
                        if (textElement) textElement.innerHTML = oldContent;
                    }
                    showToast('{{ __("Network error. Please try again.") }}', 'error');
                    this.disabled = false;
                    this.innerHTML = originalButtonHtml;
                }
            });
        }

        const editMessageContentElem = getElement('editMessageContent');
        if (editMessageContentElem) {
            const newEditMessageContent = editMessageContentElem.cloneNode(true);
            editMessageContentElem.parentNode.replaceChild(newEditMessageContent, editMessageContentElem);

            newEditMessageContent.addEventListener('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    const saveBtn = getElement('saveEditBtn');
                    if (saveBtn && !saveBtn.disabled) {
                        saveBtn.click();
                    }
                }
            });
        }

        // ==================== DELETE FOR ME ====================
        const deleteForMeBtn = getElement('deleteForMeBtn');
        if (deleteForMeBtn) {
            deleteForMeBtn.addEventListener('click', async function () {
                if (!currentDeletingMessageId) return;

                const originalHtml = this.innerHTML;

                this.disabled = true;
                this.style.opacity = '0.7';
                this.innerHTML = `
                                                                                    <div class="delete-option-icon">
                                                                                        <i class="fas fa-spinner fa-spin"></i>
                                                                                    </div>
                                                                                    <div class="delete-option-info">
                                                                                        <div class="delete-option-title">{{ __("Deleting...") }}</div>
                                                                                        <div class="delete-option-desc">{{ __("Please wait") }}</div>
                                                                                    </div>
                                                                                `;

                try {
                    const response = await fetch(`/chat/messages/${currentDeletingMessageId}/delete-for-me`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        const messageElement = document.querySelector(`.message[data-id="${currentDeletingMessageId}"]`);
                        if (messageElement) messageElement.remove();
                        refreshConversationsList();
                        closeDeleteModal();
                        showToast('{{ __("Message deleted from your view") }}', 'success');
                    } else {
                        showToast(data.message || '{{ __("Could not delete message") }}', 'error');
                        this.disabled = false;
                        this.style.opacity = '1';
                        this.innerHTML = originalHtml;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('{{ __("Network error") }}', 'error');
                    this.disabled = false;
                    this.style.opacity = '1';
                    this.innerHTML = originalHtml;
                }
            });
        }

        // ==================== DELETE FOR EVERYONE ====================
        const deleteForEveryoneBtn = getElement('deleteForEveryoneBtn');
        if (deleteForEveryoneBtn) {
            deleteForEveryoneBtn.addEventListener('click', async function () {
                if (!currentDeletingMessageId || !canDeleteForEveryone) return;

                const originalHtml = this.innerHTML;

                this.disabled = true;
                this.style.opacity = '0.7';
                this.innerHTML = `
                                                                                    <div class="delete-option-icon">
                                                                                        <i class="fas fa-spinner fa-spin"></i>
                                                                                    </div>
                                                                                    <div class="delete-option-info">
                                                                                        <div class="delete-option-title">{{ __("Deleting...") }}</div>
                                                                                        <div class="delete-option-desc">{{ __("Please wait") }}</div>
                                                                                    </div>
                                                                                `;

                try {
                    const response = await fetch(`/chat/messages/${currentDeletingMessageId}/delete-for-everyone`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        const messageElement = document.querySelector(`.message[data-id="${currentDeletingMessageId}"]`);
                        if (messageElement) {
                            messageElement.classList.add('deleted');
                            const textElement = messageElement.querySelector('.message-text');
                            if (textElement) {
                                textElement.innerHTML = '<div class="deleted-message-text"><i class="fas fa-ban"></i> <span>{{ __("This message was deleted") }}</span></div>';
                            }
                            const actions = messageElement.querySelector('.message-actions');
                            if (actions) actions.remove();
                            const timeElement = messageElement.querySelector('.message-time');
                            if (timeElement) {
                                timeElement.innerHTML = timeElement.innerHTML.replace(/<i class="fas fa-edit"><\/i> edited/, '');
                            }
                        }
                        refreshConversationsList();
                        closeDeleteModal();
                        showToast('{{ __("Message deleted for everyone") }}', 'success');
                    } else {
                        showToast(data.message || '{{ __("Could not delete message") }}', 'error');
                        this.disabled = false;
                        this.style.opacity = '1';
                        this.innerHTML = originalHtml;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('{{ __("Network error") }}', 'error');
                    this.disabled = false;
                    this.style.opacity = '1';
                    this.innerHTML = originalHtml;
                }
            });
        }

        // ==================== MARK AS READ ====================
        async function markMessagesAsRead(conversationId) {
            try {
                await fetch(`/chat/conversations/${conversationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                refreshConversationsList();
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        }

        // ==================== REFRESH CONVERSATIONS LIST ====================
        async function refreshConversationsList() {
            try {
                const response = await fetch('{{ route("chat.conversations") }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    updateConversationsList(data.conversations);
                    updateUnreadBadge(data.total_unread);
                }
            } catch (error) {
                console.error('Error refreshing conversations:', error);
            }
        }

        function updateConversationsList(conversations) {
            const container = getElement('conversationsList');
            if (!container) return;
            if (!conversations || conversations.length === 0) {
                container.innerHTML = `<div class="empty-conversations"><i class="fas fa-comment-slash"></i><p>{{ __('No conversations yet') }}</p></div>`;
                return;
            }
            let html = '';
            conversations.forEach(conv => {
                const isActive = currentConversationId == conv.id ? 'active' : '';
                const isDisabled = !conv.can_send && currentUserRole === 'patient' ? 'disabled' : '';
                const lockBadge = conv.is_locked ? `<div class="locked-badge"><i class="fas fa-lock"></i> {{ __("Session Ended") }}</div>` : '';
                const upcomingBadge = conv.badge_text ? `<div class="${conv.badge_class}">${escapeHtml(conv.badge_text)}</div>` : '';
                html += `<div class="conversation-item ${isActive} ${isDisabled}" data-id="${conv.id}" data-is-locked="${conv.is_locked}" onclick="loadConversation(${conv.id}, ${conv.other_user.id}, '${escapeHtml(conv.other_user.name)}', '${conv.other_user.avatar || ''}', ${conv.is_locked}, ${conv.can_send}, ${conv.can_lock || false}, ${conv.can_unlock || false})"><div class="conversation-avatar">${conv.other_user.avatar ? `<img src="${conv.other_user.avatar}" alt="${conv.other_user.name}">` : `<div class="avatar-placeholder">${conv.other_user.name.charAt(0)}</div>`}${conv.other_user.is_online ? '<span class="online-indicator"></span>' : ''}${conv.is_locked ? '<span class="lock-icon"><i class="fas fa-lock"></i></span>' : ''}</div><div class="conversation-info"><div class="conversation-name">${escapeHtml(conv.other_user.name)}</div><div class="conversation-last-message">${escapeHtml(conv.last_message || '{{ __("No messages yet") }}')}</div>${lockBadge}${upcomingBadge}</div><div class="conversation-meta"><div class="conversation-time">${conv.last_message_at || ''}</div>${conv.unread_count > 0 ? `<div class="unread-badge">${conv.unread_count}</div>` : ''}</div></div>`;
            });
            container.innerHTML = html;
        }

        function updateUnreadBadge(count) {
            const badge = document.querySelector('.notifications-badge');
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }

        function showTypingIndicator(userName) {
            const indicator = getElement('typingIndicator');
            const typingText = getElement('typingText');
            if (indicator && typingText) {
                typingText.innerHTML = `${userName} {{ __("is typing...") }}`;
                indicator.style.display = 'block';
                if (typingTimeout) clearTimeout(typingTimeout);
                typingTimeout = setTimeout(() => {
                    if (indicator) indicator.style.display = 'none';
                }, 1000);
            }
        }

        function scrollToBottom() {
            const container = getElement('chatMessages');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }

        function handleScroll() {
            const container = getElement('chatMessages');
            if (container && container.scrollTop === 0 && !isLoading && currentPage < lastPage) {
                fetchMessages(currentConversationId, currentPage + 1, true);
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ==================== EVENT LISTENERS ====================
        document.querySelectorAll('.custom-modal').forEach(modal => {
            modal.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeEditModal();
                    closeDeleteModal();
                    closeLockModal();
                }
            });
        });

        const lockModalConfirmBtn = getElement('lockModalConfirmBtn');
        if (lockModalConfirmBtn) {
            lockModalConfirmBtn.addEventListener('click', function () {
                if (lockConfirmCallback) {
                    lockConfirmCallback();
                }
            });
        }

        const lockModalCancelBtn = getElement('lockModalCancelBtn');
        if (lockModalCancelBtn) {
            lockModalCancelBtn.addEventListener('click', closeLockModal);
        }

        const searchInput = getElement('searchConversations');
        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.conversation-item').forEach(item => {
                    const name = item.querySelector('.conversation-name')?.innerText.toLowerCase();
                    item.style.display = name?.includes(searchTerm) ? 'flex' : 'none';
                });
            });
        }

        const messagesContainer = getElement('chatMessages');
        if (messagesContainer) messagesContainer.addEventListener('scroll', handleScroll);

        const mobileFab = getElement('mobileFab');
        if (mobileFab) {
            mobileFab.addEventListener('click', openMobileSidebar);
        }

        const mobileOverlay = getElement('mobileOverlay');
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', closeMobileSidebar);
        }

        // ==================== INITIALIZE ====================
        async function loadInitialConversations() {
            try {
                const response = await fetch('{{ route("chat.conversations") }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success && data.conversations) {
                    updateConversationsList(data.conversations);
                    updateUnreadBadge(data.total_unread);
                }
            } catch (error) {
                console.error('Error loading conversations:', error);
            }
        }

        async function updateChatHeaderOnlineStatus() {
            if (!currentConversationId) return;
            try {
                const response = await fetch(`/chat/conversations`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success && data.conversations) {
                    const currentConv = data.conversations.find(c => c.id == currentConversationId);
                    if (currentConv && currentConv.other_user) {
                        const onlineStatusSpan = document.getElementById('userOnlineStatus');
                        if (onlineStatusSpan) {
                            if (currentConv.other_user.is_online) {
                                onlineStatusSpan.innerHTML = '<i class="fas fa-circle" style="color: #10b981; font-size: 0.6rem;"></i> Online';
                                onlineStatusSpan.style.color = '#10b981';
                            } else {
                                onlineStatusSpan.innerHTML = '<i class="fas fa-circle" style="color: #9ca3af; font-size: 0.6rem;"></i> Offline';
                                onlineStatusSpan.style.color = '#9ca3af';
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Error updating online status:', error);
            }
        }

        initPusher();
        loadInitialConversations();
        refreshInterval = setInterval(refreshConversationsList, 30000);
        setInterval(updateChatHeaderOnlineStatus, 30000);

        // Auto-open conversation if target is specified
        @if(isset($targetConversation) && $targetConversation)
            setTimeout(function () {
                const otherUser = @json($targetConversation->otherUser());
                loadConversation(
                                                            {{ $targetConversation->id }},
                                                            {{ $targetUserId }},
                    '{{ addslashes($targetConversation->otherUser()->name) }}',
                    '{{ $targetConversation->otherUser()->getProfileImageUrl() }}',
                                                            {{ $targetConversation->is_locked ? 'true' : 'false' }},
                                                            {{ $targetConversation->canSendMessage(Auth::id()) ? 'true' : 'false' }},
                    false,
                    false
                );
            }, 500);
        @endif
    </script>
@endpush