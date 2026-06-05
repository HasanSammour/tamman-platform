<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SpecialistApplicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SimpleChat;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::post('/pusher/auth', function (Illuminate\Http\Request $request) {
    try {
        \Log::info('Pusher auth called', $request->all());

        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');

        \Log::info('Socket ID: ' . $socketId . ', Channel: ' . $channelName);

        if (!Auth::check()) {
            \Log::error('User not authenticated');
            return response('Forbidden', 403);
        }

        \Log::info('User authenticated: ' . Auth::id());

        $channelUserId = (int) str_replace('private-chat.', '', $channelName);

        \Log::info('Channel user ID: ' . $channelUserId);

        if (Auth::id() !== $channelUserId) {
            \Log::error('User ID mismatch: ' . Auth::id() . ' vs ' . $channelUserId);
            return response('Forbidden', 403);
        }

        \Log::info('Authorization passed, generating auth signature');

        $pusher = new \Pusher\Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            ['cluster' => config('broadcasting.connections.pusher.options.cluster')]
        );

        $auth = $pusher->socket_auth($channelName, $socketId);

        \Log::info('Auth signature generated: ' . $auth);

        return response($auth);
    } catch (\Exception $e) {
        \Log::error('Pusher auth error: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        return response('Internal Server Error: ' . $e->getMessage(), 500);
    }
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->middleware('auth');

// ==================== GUEST ROUTES ====================
Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['ar', 'en'])) {
        $locale = 'ar';
    }

    // Save to session (for guests)
    session(['locale' => $locale]);

    // If user is logged in, save to database too
    if (Auth::check()) {
        $user = Auth::user();
        $user->preferred_locale = $locale;
        $user->save();
    }

    app()->setLocale($locale);

    return redirect()->back();
})->name('lang.switch');

Route::middleware(['set.locale'])->group(function () {
    // Home and static pages
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/how-it-works', [HomeController::class, 'howItWorks'])->name('how-it-works');
    Route::get('/about', [HomeController::class, 'about'])->name('about');
    Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
    Route::post('/contact', [HomeController::class, 'sendContact'])->name('contact.send');
    Route::get('/help-center', [App\Http\Controllers\HomeController::class, 'helpCenter'])->name('help-center');

    // AJAX Routes (for dynamic content)
    Route::get('/api/stats', [HomeController::class, 'getStats'])->name('api.stats');
    Route::post('/api/mood-resources', [HomeController::class, 'getMoodResources'])->name('api.mood-resources');

    // Specialists Listing (Guest accessible)
    Route::get('/specialists', [SpecialistController::class, 'index'])->name('specialists.index');
    Route::get('/specialists/{specialist}', [SpecialistController::class, 'show'])->name('specialists.show');

    // Resources (Educational Content)
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/category/{category}', [ResourceController::class, 'category'])->name('resources.category');
    Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');
    Route::post('/resources/mood', [ResourceController::class, 'getMoodResources'])->name('resources.mood');

    // Specialist Application (Guest accessible)
    Route::get('/specialist/apply', [SpecialistApplicationController::class, 'create'])->name('specialist.apply');
    Route::post('/specialist/apply', [SpecialistApplicationController::class, 'store'])->name('specialist.apply.store');
    Route::post('/specialist/check-email', [SpecialistApplicationController::class, 'checkEmail'])->name('specialist.check-email');
    Route::get('/specialist/apply/success', [SpecialistApplicationController::class, 'success'])->name('specialist.application.success');

    // Legal Pages
    Route::get('/terms', function () {
        return view('terms');
    })->name('terms');

    Route::get('/privacy', function () {
        return view('privacy');
    })->name('privacy');

    Route::get('/contact', function () {
        return view('contact');
    })->name('contact');
});

// ==================== AUTH ROUTES (Laravel Breeze) ====================
require __DIR__ . '/auth.php';

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware(['auth', 'verified', 'set.locale', 'track.activity'])->group(function () {
    // Dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('specialist')) {
            return redirect()->route('specialist.dashboard');
        } else {
            return redirect()->route('patient.dashboard');
        }
    })->name('dashboard');

    // Profile Routes (Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Additional Profile Routes for Image
    Route::post('/profile/image', [ProfileController::class, 'updateProfileImage'])->name('profile.image.update');
    Route::delete('/profile/image', [ProfileController::class, 'removeProfileImage'])->name('profile.image.remove');

    // Password Update Route
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('user-password.update');

    // Shared Settings Page
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/notifications', [App\Http\Controllers\SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::post('/settings/privacy', [App\Http\Controllers\SettingsController::class, 'updatePrivacy'])->name('settings.privacy');
    Route::post('/settings/language', [App\Http\Controllers\SettingsController::class, 'updateLanguage'])->name('settings.language');

    Route::get('/donate', [App\Http\Controllers\DonationController::class, 'index'])->name('donate');
    Route::post('/donate', [App\Http\Controllers\DonationController::class, 'store'])->name('donate.store');

    // Add Credits (Patient only)
    Route::get('/patient/add-credits', [App\Http\Controllers\Patient\CreditController::class, 'index'])->name('patient.add-credits');
    Route::post('/patient/add-credits', [App\Http\Controllers\Patient\CreditController::class, 'request'])->name('patient.add-credits.request');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/get', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('get');
        Route::get('/fetch', [App\Http\Controllers\NotificationController::class, 'fetch'])->name('fetch');
        Route::get('/count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('count');
        Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/', [App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('destroy-all');
        Route::post('/bulk-action', [App\Http\Controllers\NotificationController::class, 'bulkAction'])->name('bulk-action');
    });

    // ==================== CHAT ROUTES (Shared between Patient & Specialist) ====================
    // Chat views
    Route::get('/chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('/chat/conversations/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/conversations/{conversation}/session-info', [ChatController::class, 'getConversationSessionInfo'])->name('chat.session-info');

    // Message operations
    Route::post('/chat/messages', [ChatController::class, 'send'])->name('chat.send');
    Route::put('/chat/messages/{message}', [ChatController::class, 'edit'])->name('chat.edit');
    Route::delete('/chat/messages/{message}/delete-for-me', [ChatController::class, 'deleteForMe'])->name('chat.deleteForMe');
    Route::delete('/chat/messages/{message}/delete-for-everyone', [ChatController::class, 'deleteForEveryone'])->name('chat.deleteForEveryone');
    Route::get('/chat/messages/{message}/delete-options', [ChatController::class, 'getDeleteOptions'])->name('chat.deleteOptions');

    // Conversation management
    Route::post('/chat/conversations/{conversation}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
    Route::post('/chat/conversations/{conversation}/lock', [ChatController::class, 'lockConversation'])->name('chat.lock');
    Route::post('/chat/conversations/{conversation}/unlock', [ChatController::class, 'unlockConversation'])->name('chat.unlock');

    // Typing indicator
    Route::post('/chat/typing', [ChatController::class, 'typing'])->name('chat.typing');

    // Unread count
    Route::get('/chat/unread/count', [ChatController::class, 'unreadCount'])->name('chat.unread');

    // Session chat management (Specialist only)
    Route::post('/chat/sessions/{conversation}/end', [ChatController::class, 'endSession'])->name('chat.endSession');
    Route::post('/chat/sessions/{conversation}/reopen', [ChatController::class, 'reopenSession'])->name('chat.reopenSession');

    // WILDCARD route LAST (with parameter) so it's not make any route conflict with Others
    Route::get('/chat/{user?}', [ChatController::class, 'index'])->name('chat.index');

    // Simple test
    Route::get('/chat/simple', [SimpleChat::class, 'simple'])->name('chat.simple');
    Route::post('/chat/simple/send', [SimpleChat::class, 'send'])->name('chat.simple.send');

    // ==================== PATIENT ROUTES ====================
    Route::prefix('patient')->name('patient.')->middleware('role:patient')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Patient\DashboardController::class, 'index'])->name('dashboard');

        // Mood Tracker
        Route::get('/mood-tracker', [App\Http\Controllers\Patient\MoodTrackerController::class, 'index'])->name('mood-tracker');
        Route::post('/mood-tracker', [App\Http\Controllers\Patient\MoodTrackerController::class, 'store'])->name('mood-tracker.store');
        Route::put('/mood-tracker/{id}', [App\Http\Controllers\Patient\MoodTrackerController::class, 'update'])->name('mood-tracker.update');
        Route::delete('/mood-tracker/{id}', [App\Http\Controllers\Patient\MoodTrackerController::class, 'destroy'])->name('mood-tracker.destroy');
        Route::get('/mood-tracker/history', [App\Http\Controllers\Patient\MoodTrackerController::class, 'history'])->name('mood-tracker.history');

        // Tests
        Route::get('/tests', [App\Http\Controllers\Patient\TestController::class, 'index'])->name('tests');
        Route::get('/tests/{test}/take', [App\Http\Controllers\Patient\TestController::class, 'take'])->name('tests.take');
        Route::post('/tests/{test}/submit', [App\Http\Controllers\Patient\TestController::class, 'submit'])->name('tests.submit');
        Route::get('/tests/history', [App\Http\Controllers\Patient\TestController::class, 'history'])->name('tests.history');
        Route::get('/tests/results/{result}', [App\Http\Controllers\Patient\TestController::class, 'results'])->name('tests.results');

        // Treatment Plan - CORRECT ORDER (specific before parameterized)
        Route::get('/treatment-plans', [App\Http\Controllers\Patient\TreatmentPlanController::class, 'index'])->name('treatment-plan');
        Route::get('/treatment-plans/history', [App\Http\Controllers\Patient\TreatmentPlanController::class, 'history'])->name('treatment-plan.history');
        Route::get('/treatment-plans/{plan}', [App\Http\Controllers\Patient\TreatmentPlanController::class, 'show'])->name('treatment-plan.show');
        Route::post('/treatment-plans/task/{task}/complete', [App\Http\Controllers\Patient\TreatmentPlanController::class, 'completeTask'])->name('treatment-plan.complete-task');
        Route::get('/treatment-plans/{plan}/progress', [App\Http\Controllers\Patient\TreatmentPlanController::class, 'getProgress'])->name('treatment-plan.progress');

        // Booking System
        Route::get('/specialists/{specialist}/book', [App\Http\Controllers\Patient\BookingController::class, 'book'])->name('book');
        Route::post('/bookings', [App\Http\Controllers\Patient\BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{session}/confirmation', [App\Http\Controllers\Patient\BookingController::class, 'confirmation'])->name('bookings.confirmation');
        Route::get('/bookings/{session}/cancel', [App\Http\Controllers\Patient\BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/booking/slots', [App\Http\Controllers\Patient\BookingController::class, 'getAvailableSlotsAjax'])->name('booking.slots');

        // Sessions Management
        Route::get('/sessions', [App\Http\Controllers\Patient\SessionController::class, 'index'])->name('sessions');
        Route::get('/sessions/{session}', [App\Http\Controllers\Patient\SessionController::class, 'show'])->name('sessions.show');
        Route::get('/sessions/{session}/join', [App\Http\Controllers\Patient\SessionController::class, 'join'])->name('sessions.join');
        Route::post('/sessions/{session}/cancel', [App\Http\Controllers\Patient\SessionController::class, 'cancel'])->name('sessions.cancel');
        Route::post('/sessions/{session}/rate', [App\Http\Controllers\Patient\SessionController::class, 'rate'])->name('sessions.rate');
        Route::get('/sessions/upcoming/count', [App\Http\Controllers\Patient\SessionController::class, 'getUpcomingCount'])->name('sessions.upcoming.count');
        Route::get('/sessions/{session}/check-join', [App\Http\Controllers\Patient\SessionController::class, 'checkJoinStatus'])->name('sessions.check-join');
        Route::post('/sessions/{session}/mark-ongoing', [App\Http\Controllers\Patient\SessionController::class, 'markOngoing'])->name('sessions.mark-ongoing');
        Route::post('/sessions/{session}/check-expiry', [App\Http\Controllers\Patient\SessionController::class, 'checkExpiry'])->name('sessions.check-expiry');
        Route::post('/sessions/{sessionId}/can-join', [App\Http\Controllers\Patient\SessionController::class, 'canJoinSession']);
        Route::post('/sessions/{sessionId}/register-join', [App\Http\Controllers\Patient\SessionController::class, 'registerJoin']);

        // Rewards System
        Route::get('/rewards', [App\Http\Controllers\Patient\RewardsController::class, 'index'])->name('rewards');
        Route::post('/rewards/redeem', [App\Http\Controllers\Patient\RewardsController::class, 'redeem'])->name('rewards.redeem');
        Route::get('/rewards/history', [App\Http\Controllers\Patient\RewardsController::class, 'history'])->name('rewards.history');
        Route::get('/rewards/points-history', [App\Http\Controllers\Patient\RewardsController::class, 'pointsHistory'])->name('rewards.points-history');
        Route::post('/rewards/cancel', [App\Http\Controllers\Patient\RewardsController::class, 'cancel'])->name('rewards.cancel');
        Route::get('/rewards/reward/{rewardId}', [App\Http\Controllers\Patient\RewardsController::class, 'getRewardDetails'])->name('rewards.details');
        Route::get('/rewards/stats', [App\Http\Controllers\Patient\RewardsController::class, 'getStats'])->name('rewards.stats');
    });

    // ==================== SPECIALIST ROUTES ====================
    Route::prefix('specialist')->name('specialist.')->middleware('role:specialist')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Specialist\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/specialist/chart-data', [App\Http\Controllers\Specialist\DashboardController::class, 'getChartData'])->name('chart-data');

        // Schedule Management
        Route::get('/schedule', [App\Http\Controllers\Specialist\ScheduleController::class, 'index'])->name('schedule');
        Route::get('/schedule/events', [App\Http\Controllers\Specialist\ScheduleController::class, 'getEvents'])->name('schedule.events');
        Route::post('/schedule/availability', [App\Http\Controllers\Specialist\ScheduleController::class, 'storeAvailability'])->name('schedule.availability.store');
        Route::post('/schedule/one-time', [App\Http\Controllers\Specialist\ScheduleController::class, 'storeOneTime'])->name('schedule.one-time');
        Route::put('/schedule/availability/{id}', [App\Http\Controllers\Specialist\ScheduleController::class, 'updateAvailability'])->name('schedule.availability.update');
        Route::delete('/schedule/availability/{id}', [App\Http\Controllers\Specialist\ScheduleController::class, 'destroyAvailability'])->name('schedule.availability.destroy');
        Route::post('/schedule/block', [App\Http\Controllers\Specialist\ScheduleController::class, 'blockTime'])->name('schedule.block');
        Route::post('/schedule/copy-week', [App\Http\Controllers\Specialist\ScheduleController::class, 'copyWeek'])->name('schedule.copy-week');

        // ==================== CLIENTS MANAGEMENT ====================
        Route::prefix('clients')->name('clients.')->group(function () {
            Route::get('/', [App\Http\Controllers\Specialist\ClientController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Specialist\ClientController::class, 'getClientsData'])->name('data');
            Route::get('/{client}', [App\Http\Controllers\Specialist\ClientController::class, 'show'])->name('show');
            Route::get('/{client}/sessions', [App\Http\Controllers\Specialist\ClientController::class, 'getSessions'])->name('sessions');
            Route::get('/{client}/mood', [App\Http\Controllers\Specialist\ClientController::class, 'getMoodData'])->name('mood');
            Route::get('/{client}/tests', [App\Http\Controllers\Specialist\ClientController::class, 'getTests'])->name('tests');
            Route::get('/{client}/treatment', [App\Http\Controllers\Specialist\ClientController::class, 'getTreatmentPlans'])->name('treatment');
            Route::get('/{client}/activity', [App\Http\Controllers\Specialist\ClientController::class, 'getRecentActivity'])->name('activity');
        });

        // ==================== SESSIONS MANAGEMENT ====================
        Route::prefix('sessions')->name('sessions.')->group(function () {
            Route::get('/{session}', [App\Http\Controllers\Specialist\SessionController::class, 'show'])->name('show');
            Route::get('/{session}/join', [App\Http\Controllers\Specialist\SessionController::class, 'join'])->name('join');
            Route::post('/{session}/cancel', [App\Http\Controllers\Specialist\SessionController::class, 'cancel'])->name('cancel');
            Route::post('/{session}/complete', [App\Http\Controllers\Specialist\SessionController::class, 'complete'])->name('complete');
            Route::post('/{session}/ongoing', [App\Http\Controllers\Specialist\SessionController::class, 'markOngoing'])->name('ongoing');
            Route::post('/{session}/no-show', [App\Http\Controllers\Specialist\SessionController::class, 'markNoShow'])->name('no-show');
            Route::post('/{sessionId}/can-join', [App\Http\Controllers\Specialist\SessionController::class, 'canJoinSession']);
            Route::post('/{sessionId}/register-join', [App\Http\Controllers\Specialist\SessionController::class, 'registerJoin']);
        });

        // ==================== TREATMENT PLANS ====================
        Route::prefix('treatment-plans')->name('treatment-plans.')->group(function () {
            Route::get('/', [App\Http\Controllers\Specialist\TreatmentPlanController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Specialist\TreatmentPlanController::class, 'getPlansData'])->name('data');
            Route::get('/patients', [App\Http\Controllers\Specialist\TreatmentPlanController::class, 'getPatients'])->name('patients');
            Route::get('/create', [App\Http\Controllers\Specialist\TreatmentPlanController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Specialist\TreatmentPlanController::class, 'store'])->name('store');
            Route::get('/{plan}', [App\Http\Controllers\Specialist\TreatmentPlanController::class, 'show'])->name('show');
            Route::get('/{plan}/edit', [App\Http\Controllers\Specialist\TreatmentPlanController::class, 'edit'])->name('edit');
            Route::put('/{plan}', [App\Http\Controllers\Specialist\TreatmentPlanController::class, 'update'])->name('update');
            Route::delete('/{plan}', [App\Http\Controllers\Specialist\TreatmentPlanController::class, 'destroy'])->name('destroy');
        });

        // ==================== SESSION NOTES ====================
        Route::prefix('session-notes')->name('session-notes.')->group(function () {
            Route::get('/', [App\Http\Controllers\Specialist\SessionNoteController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Specialist\SessionNoteController::class, 'getNotesData'])->name('data');
            Route::get('/patients', [App\Http\Controllers\Specialist\SessionNoteController::class, 'getPatients'])->name('patients');
            Route::get('/{session}', [App\Http\Controllers\Specialist\SessionNoteController::class, 'edit'])->name('edit');
            Route::put('/{session}', [App\Http\Controllers\Specialist\SessionNoteController::class, 'update'])->name('update');
        });

        // Earnings
        Route::get('/earnings', [App\Http\Controllers\Specialist\EarningsController::class, 'index'])->name('earnings');
        Route::get('/earnings/data', [App\Http\Controllers\Specialist\EarningsController::class, 'getEarningsData'])->name('earnings.data');
        Route::get('/earnings/payments', [App\Http\Controllers\Specialist\EarningsController::class, 'getPaymentHistory'])->name('earnings.payments');
        Route::post('/earnings/request-payout', [App\Http\Controllers\Specialist\EarningsController::class, 'requestPayout'])->name('earnings.request-payout');
        Route::get('/earnings/session-breakdown', [App\Http\Controllers\Specialist\EarningsController::class, 'getSessionBreakdown'])->name('earnings.session-breakdown');
        Route::get('/earnings/invoice/{payment}', [App\Http\Controllers\Specialist\EarningsController::class, 'invoice'])->name('earnings.invoice');
    });

    // ==================== ADMIN ROUTES ====================
    // From User Management :: act like a patient
    // to stop That no user middleware applied on That
    Route::post('/admin/users/stop-impersonate', [App\Http\Controllers\Admin\UserController::class, 'stopImpersonate'])->name('admin.users.stop-impersonate');
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Users Management (Patients)
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
        Route::get('/users/data', [App\Http\Controllers\Admin\UserController::class, 'getUsersData'])->name('users.data');
        Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        // Dedicated route for profile image upload
        Route::post('/users/{user}/upload-image', [App\Http\Controllers\Admin\UserController::class, 'uploadImage'])->name('users.upload-image');
        Route::delete('/users/{user}/remove-image', [App\Http\Controllers\Admin\UserController::class, 'removeImage'])->name('users.remove-image');
        Route::post('/users/{user}/suspend', [App\Http\Controllers\Admin\UserController::class, 'toggleSuspend'])->name('users.toggle-suspend');
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/export/pdf', [App\Http\Controllers\Admin\UserController::class, 'exportPdf'])->name('users.export-pdf');
        Route::get('/users/{user}/impersonate', [App\Http\Controllers\Admin\UserController::class, 'impersonate'])->name('users.impersonate');

        // Specialists Management
        Route::get('/specialists', [App\Http\Controllers\Admin\SpecialistController::class, 'index'])->name('specialists');
        Route::get('/specialists/data', [App\Http\Controllers\Admin\SpecialistController::class, 'getSpecialistsData'])->name('specialists.data');
        Route::get('/specialists/{specialist}', [App\Http\Controllers\Admin\SpecialistController::class, 'show'])->name('specialists.show');
        Route::get('/specialists/{specialist}/edit', [App\Http\Controllers\Admin\SpecialistController::class, 'edit'])->name('specialists.edit');
        Route::post('/specialists/{specialist}/upload-image', [App\Http\Controllers\Admin\SpecialistController::class, 'uploadImage'])->name('specialists.upload-image');
        Route::delete('/specialists/{specialist}/remove-image', [App\Http\Controllers\Admin\SpecialistController::class, 'removeImage'])->name('specialists.remove-image');
        Route::post('/specialists/{specialist}/documents', [App\Http\Controllers\Admin\SpecialistController::class, 'updateDocuments'])->name('specialists.update-documents');
        Route::put('/specialists/{specialist}', [App\Http\Controllers\Admin\SpecialistController::class, 'update'])->name('specialists.update');
        Route::post('/specialists/{specialist}/suspend', [App\Http\Controllers\Admin\SpecialistController::class, 'toggleSuspend'])->name('specialists.toggle-suspend');
        Route::delete('/specialists/{specialist}', [App\Http\Controllers\Admin\SpecialistController::class, 'destroy'])->name('specialists.destroy');
        Route::get('/specialists/export/pdf', [App\Http\Controllers\Admin\SpecialistController::class, 'exportPdf'])->name('specialists.export-pdf');
        Route::post('/specialists/{specialist}/send-email', [App\Http\Controllers\Admin\SpecialistController::class, 'sendEmail'])->name('specialists.send-email');

        // Approvals (Specialist Applications) - Pending Applications
        Route::get('/approvals/{status?}', [App\Http\Controllers\Admin\ApprovalController::class, 'index'])
            ->name('approvals')
            ->where('status', 'pending|approved|rejected|all');
        Route::get('/approvals/data', [App\Http\Controllers\Admin\ApprovalController::class, 'getApprovalsData'])->name('approvals.data');
        Route::get('/approvals/{application}', [App\Http\Controllers\Admin\ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('/approvals/{application}/approve', [App\Http\Controllers\Admin\ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{application}/reject', [App\Http\Controllers\Admin\ApprovalController::class, 'reject'])->name('approvals.reject');
        Route::post('/approvals/{application}/request-info', [App\Http\Controllers\Admin\ApprovalController::class, 'requestInfo'])->name('approvals.request-info');

        // ==================== PAYMENTS MANAGEMENT ====================
        Route::prefix('payments')->name('payments.')->group(function () {
            // Main Dashboard
            Route::get('/', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Admin\PaymentController::class, 'getPaymentsData'])->name('data');

            // Credit Requests (Patient adding credits)
            Route::get('/credit-requests', [App\Http\Controllers\Admin\PaymentController::class, 'creditRequests'])->name('credit-requests');
            Route::get('/credit-requests/data', [App\Http\Controllers\Admin\PaymentController::class, 'getCreditRequestsData'])->name('credit-requests.data');
            Route::post('/credit-requests/{id}/approve', [App\Http\Controllers\Admin\PaymentController::class, 'approveCreditRequest'])->name('credit-requests.approve');
            Route::post('/credit-requests/{id}/reject', [App\Http\Controllers\Admin\PaymentController::class, 'rejectCreditRequest'])->name('credit-requests.reject');

            // Donations (Users becoming donors)
            Route::get('/donations', [App\Http\Controllers\Admin\PaymentController::class, 'donations'])->name('donations');
            Route::get('/donations/data', [App\Http\Controllers\Admin\PaymentController::class, 'getDonationsData'])->name('donations.data');
            Route::post('/donations/{id}/approve', [App\Http\Controllers\Admin\PaymentController::class, 'approveDonation'])->name('donations.approve');
            Route::post('/donations/{id}/reject', [App\Http\Controllers\Admin\PaymentController::class, 'rejectDonation'])->name('donations.reject');

            // Allocate Donation to Patient
            Route::post('/donations/allocate', [App\Http\Controllers\Admin\PaymentController::class, 'allocateDonation'])->name('donations.allocate');

            // Points Redemption (Rewards)
            Route::get('/redemptions', [App\Http\Controllers\Admin\PaymentController::class, 'redemptions'])->name('redemptions');
            Route::get('/redemptions/data', [App\Http\Controllers\Admin\PaymentController::class, 'getRedemptionsData'])->name('redemptions.data');

            // Specialists Payouts
            Route::get('/specialists', [App\Http\Controllers\Admin\PaymentController::class, 'specialists'])->name('specialists');
            Route::get('/specialists/data', [App\Http\Controllers\Admin\PaymentController::class, 'getSpecialistsData'])->name('specialists.data');
            Route::get('/specialist-payouts', [App\Http\Controllers\Admin\PaymentController::class, 'specialistPayouts'])->name('specialist-payouts');
            Route::get('/payouts/data', [App\Http\Controllers\Admin\PaymentController::class, 'getPayoutsData'])->name('payments.payouts-data');
            Route::post('/specialist-payouts/generate', [App\Http\Controllers\Admin\PaymentController::class, 'generatePayoutReport'])->name('generate-payout');
            Route::post('/specialist-payouts/pay', [App\Http\Controllers\Admin\PaymentController::class, 'paySpecialist'])->name('pay-specialist');
            Route::post('/specialist-payouts/pay-all', [App\Http\Controllers\Admin\PaymentController::class, 'payAllSpecialists'])->name('pay-all');
            Route::post('/specialist-payouts/pay-selected', [App\Http\Controllers\Admin\PaymentController::class, 'paySelectedSpecialists'])->name('pay-selected');

            // Export Reports
            Route::get('/export/specialists-pdf', [App\Http\Controllers\Admin\PaymentController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/payouts/export-history-pdf', [App\Http\Controllers\Admin\PaymentController::class, 'exportPayoutsHistoryPdf'])->name('export-payouts-history');
            Route::get('/export/payouts-pdf', [App\Http\Controllers\Admin\PaymentController::class, 'exportPayoutPdf'])->name('export-payout-pdf');
            Route::get('/export/credit-requests-pdf', [App\Http\Controllers\Admin\PaymentController::class, 'exportCreditRequestsPdf'])->name('export-credit-requests-pdf');
            Route::get('/export/donations-pdf', [App\Http\Controllers\Admin\PaymentController::class, 'exportDonationsPdf'])->name('export-donations-pdf');
            Route::get('/export/redemptions-pdf', [App\Http\Controllers\Admin\PaymentController::class, 'exportRedemptionsPdf'])->name('export-redemptions-pdf');
        });

        // Content Management
        Route::get('/content', [App\Http\Controllers\Admin\ContentController::class, 'index'])->name('content');
        Route::get('/content/data', [App\Http\Controllers\Admin\ContentController::class, 'getContentData'])->name('content.data');
        Route::get('/content/create', [App\Http\Controllers\Admin\ContentController::class, 'create'])->name('content.create');
        Route::post('/content', [App\Http\Controllers\Admin\ContentController::class, 'store'])->name('content.store');
        Route::post('/content/upload-image', [App\Http\Controllers\Admin\ContentController::class, 'uploadImage'])->name('content.upload-image');
        Route::get('/content/{content}', [App\Http\Controllers\Admin\ContentController::class, 'show'])->name('content.show');
        Route::get('/content/{content}/edit', [App\Http\Controllers\Admin\ContentController::class, 'edit'])->name('content.edit');
        Route::put('/content/{content}', [App\Http\Controllers\Admin\ContentController::class, 'update'])->name('content.update');
        Route::delete('/content/{content}', [App\Http\Controllers\Admin\ContentController::class, 'destroy'])->name('content.destroy');
        Route::post('/content/{content}/publish', [App\Http\Controllers\Admin\ContentController::class, 'publish'])->name('content.publish');
        Route::post('/content/{content}/unpublish', [App\Http\Controllers\Admin\ContentController::class, 'unpublish'])->name('content.unpublish');

        // ==================== REPORTS ====================
        Route::prefix('reports')->name('reports.')->group(function () {
            // Main Reports Dashboard
            Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');

            // Users Reports
            Route::get('/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('users');
            Route::get('/users/data', [App\Http\Controllers\Admin\ReportController::class, 'getUsersReportData'])->name('users.data');
            Route::get('/users/export', [App\Http\Controllers\Admin\ReportController::class, 'exportUsersReport'])->name('users.export');

            // Sessions Reports
            Route::get('/sessions', [App\Http\Controllers\Admin\ReportController::class, 'sessions'])->name('sessions');
            Route::get('/sessions/data', [App\Http\Controllers\Admin\ReportController::class, 'getSessionsReportData'])->name('sessions.data');
            Route::get('/sessions/export', [App\Http\Controllers\Admin\ReportController::class, 'exportSessionsReport'])->name('sessions.export');

            // Financial Reports
            Route::get('/financial', [App\Http\Controllers\Admin\ReportController::class, 'financial'])->name('financial');
            Route::get('/financial/data', [App\Http\Controllers\Admin\ReportController::class, 'getFinancialReportData'])->name('financial.data');
            Route::get('/financial/export', [App\Http\Controllers\Admin\ReportController::class, 'exportFinancialReport'])->name('financial.export');

            // Specialists Reports
            Route::get('/specialists', [App\Http\Controllers\Admin\ReportController::class, 'specialists'])->name('specialists');
            Route::get('/specialists/data', [App\Http\Controllers\Admin\ReportController::class, 'getSpecialistsReportData'])->name('specialists.data');
            Route::get('/specialists/export', [App\Http\Controllers\Admin\ReportController::class, 'exportSpecialistsReport'])->name('specialists.export');

            // Points Reports
            Route::get('/points', [App\Http\Controllers\Admin\ReportController::class, 'points'])->name('points');
            Route::get('/points/data', [App\Http\Controllers\Admin\ReportController::class, 'getPointsReportData'])->name('points.data');
            Route::get('/points/export', [App\Http\Controllers\Admin\ReportController::class, 'exportPointsReport'])->name('points.export');

            // Tests Reports
            Route::get('/tests', [App\Http\Controllers\Admin\ReportController::class, 'tests'])->name('tests');
            Route::get('/tests/data', [App\Http\Controllers\Admin\ReportController::class, 'getTestsReportData'])->name('tests.data');
            Route::get('/tests/export', [App\Http\Controllers\Admin\ReportController::class, 'exportTestsReport'])->name('tests.export');

            // Export helper (AJAX for filters)
            Route::get('/filters', [App\Http\Controllers\Admin\ReportController::class, 'getFilters'])->name('filters');
        });

        // ==================== PLATFORM ANALYTICS ====================
        Route::prefix('analytics')->name('analytics.')->group(function () {
            // Main Analytics Dashboard
            Route::get('/', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('index');

            // Overview Stats (AJAX)
            Route::get('/overview', [App\Http\Controllers\Admin\AnalyticsController::class, 'getOverviewStats'])->name('overview');

            // User Analytics
            Route::get('/users', [App\Http\Controllers\Admin\AnalyticsController::class, 'getUserAnalytics'])->name('users');
            Route::get('/users/growth', [App\Http\Controllers\Admin\AnalyticsController::class, 'getUserGrowthData'])->name('users.growth');
            Route::get('/users/retention', [App\Http\Controllers\Admin\AnalyticsController::class, 'getUserRetentionData'])->name('users.retention');

            // Session Analytics
            Route::get('/sessions', [App\Http\Controllers\Admin\AnalyticsController::class, 'getSessionAnalytics'])->name('sessions');
            Route::get('/sessions/trend', [App\Http\Controllers\Admin\AnalyticsController::class, 'getSessionTrendData'])->name('sessions.trend');
            Route::get('/sessions/types', [App\Http\Controllers\Admin\AnalyticsController::class, 'getSessionTypeData'])->name('sessions.types');

            // Financial Analytics
            Route::get('/financial', [App\Http\Controllers\Admin\AnalyticsController::class, 'getFinancialAnalytics'])->name('financial');
            Route::get('/financial/revenue', [App\Http\Controllers\Admin\AnalyticsController::class, 'getRevenueData'])->name('financial.revenue');
            Route::get('/financial/donations', [App\Http\Controllers\Admin\AnalyticsController::class, 'getDonationData'])->name('financial.donations');

            // Points & Rewards Analytics
            Route::get('/points', [App\Http\Controllers\Admin\AnalyticsController::class, 'getPointsAnalytics'])->name('points');
            Route::get('/points/distribution', [App\Http\Controllers\Admin\AnalyticsController::class, 'getPointsDistributionData'])->name('points.distribution');
            Route::get('/rewards/popular', [App\Http\Controllers\Admin\AnalyticsController::class, 'getPopularRewardsData'])->name('rewards.popular');

            // Tests Analytics
            Route::get('/tests', [App\Http\Controllers\Admin\AnalyticsController::class, 'getTestAnalytics'])->name('tests');
            Route::get('/tests/distribution', [App\Http\Controllers\Admin\AnalyticsController::class, 'getTestDistributionData'])->name('tests.distribution');
            Route::get('/tests/trends', [App\Http\Controllers\Admin\AnalyticsController::class, 'getTestTrendsData'])->name('tests.trends');

            // Specialists Analytics
            Route::get('/specialists', [App\Http\Controllers\Admin\AnalyticsController::class, 'getSpecialistAnalytics'])->name('specialists');
            Route::get('/specialists/top', [App\Http\Controllers\Admin\AnalyticsController::class, 'getTopSpecialistsData'])->name('specialists.top');
        });

        // System Logs
        Route::get('/logs', [App\Http\Controllers\Admin\SystemLogController::class, 'index'])->name('logs');
        Route::get('/logs/data', [App\Http\Controllers\Admin\SystemLogController::class, 'getLogsData'])->name('logs.data');
        Route::delete('/logs/{log}', [App\Http\Controllers\Admin\SystemLogController::class, 'destroy'])->name('logs.destroy');
        Route::delete('/logs/bulk/delete', [App\Http\Controllers\Admin\SystemLogController::class, 'bulkDelete'])->name('logs.bulk-delete');
        Route::post('/logs/clear-by-date', [App\Http\Controllers\Admin\SystemLogController::class, 'clearByDate'])->name('logs.clear-by-date');
        Route::get('/logs/export/csv', [App\Http\Controllers\Admin\SystemLogController::class, 'exportCsv'])->name('logs.export-csv');
    });
});

// TEST ROUTES - Remove after testing
Route::get('/test-403', fn() => abort(403));
Route::get('/test-404', fn() => abort(404));
Route::get('/test-419', fn() => abort(419));
Route::get('/test-429', fn() => abort(429));
Route::get('/test-500', fn() => abort(500));
Route::get('/test-503', fn() => abort(503));

// In web.php (temporary, remove later)
Route::get('/test-purifier', function() {
    $dirty = '<script>alert("XSS")</script><h1>Hello</h1><p onclick="alert(1)">World</p>';
    $clean = App\Helpers\HtmlPurifierHelper::clean($dirty);
    
    return [
        'original' => $dirty,
        'cleaned' => $clean,
        'safe' => App\Helpers\HtmlPurifierHelper::isSafe($clean)
    ];
});