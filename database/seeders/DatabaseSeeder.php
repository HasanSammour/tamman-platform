<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SpecialistProfile;
use App\Models\DonorProfile;
use App\Models\TherapySession;
use App\Models\Availability;
use App\Models\MoodLog;
use App\Models\TestResult;
use App\Models\PointTransaction;
use App\Models\TreatmentPlan;
use App\Models\TreatmentTask;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Review;
use App\Models\CreditTransaction;
use App\Models\Content;
use App\Models\SystemLog;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\SpecialistPayment;
use App\Models\Conversation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{

    protected $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create('ar_SA');
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('Starting Tamman Database Seeding...');
        $this->command->info('========================================');

        // Disable foreign key checks for faster inserts
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ==================== STEP 1: Run RolePermissionSeeder first ====================
        $this->command->info('');
        $this->command->info('Step 1: Creating Roles and Permissions...');
        $this->call(RolePermissionSeeder::class);

        // ==================== STEP 2: Create Users ====================
        $this->command->info('');
        $this->command->info('Step 2: Creating Users...');

        // Create 1 Super Admin
        $admin = User::create([
            'name' => 'مدير النظام - حسن سمور',
            'email' => 'admin@tamman.ps',
            'password' => Hash::make('admin123'),
            'phone' => '+970591020304',
            'gender' => 'male',
            'date_of_birth' => '2003-01-24',
            'is_active' => true,
            'total_points' => 0,
            'credit_balance' => 0,
            'referral_code' => null,
            'email_verified_at' => now(),
            'profile_image' => "images/profile_seed/admin.jpeg",
            'notification_settings' => json_encode([
                'session_reminders' => true,
                'points_earned' => true,
                'new_messages' => true,
                'treatment_tasks' => true,
                'promotional_emails' => false,
            ]),
            'privacy_settings' => json_encode([
                'profile_visibility' => 'public',
                'show_email' => false,
                'show_phone' => false,
                'show_activity_status' => true,
                'allow_messages_from' => 'everyone',
            ]),
            'preferred_locale' => 'ar',
        ]);
        $admin->assignRole('admin');
        $this->command->info('  - Admin created: admin@tamman.ps');

        // Create 200 Specialists
        $this->command->info('  - Creating 200 Specialists...');
        $specialists = [];
        $specialistImages = [1, 3, 5, 7, 9];

        for ($i = 0; $i < 200; $i++) {
            $gender = rand(0, 1) ? 'male' : 'female';
            $imageNumber = $gender === 'male'
                ? $specialistImages[array_rand($specialistImages)]
                : [2, 4, 6, 8, 10][array_rand([2, 4, 6, 8, 10])];

            $user = User::create([
                'name' => $this->getRandomArabicName('specialist'),
                'email' => "specialist_{$i}@tamman.ps",
                'password' => Hash::make('password123'),
                'phone' => '+9705' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'gender' => $gender,
                'date_of_birth' => $this->getRandomDate('-65 years', '-25 years'),
                'is_active' => true,
                'total_points' => rand(0, 500),
                'credit_balance' => rand(0, 500) * 10,
                'referral_code' => null,
                'email_verified_at' => now(),
                'profile_image' => "images/profile_seed/profile_{$imageNumber}.jpg",
                'notification_settings' => json_encode([
                    'session_reminders' => true,
                    'points_earned' => true,
                    'new_messages' => true,
                    'treatment_tasks' => true,
                    'promotional_emails' => false,
                ]),
                'privacy_settings' => json_encode([
                    'profile_visibility' => 'public',
                    'show_email' => false,
                    'show_phone' => false,
                    'show_activity_status' => true,
                    'allow_messages_from' => 'everyone',
                ]),
                'preferred_locale' => 'ar',
            ]);
            $user->assignRole('specialist');
            $specialists[] = $user;
        }


        // Create 750 Patients
        $this->command->info('  - Creating 750 Patients...');
        $patients = [];
        $patientImages = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        for ($i = 0; $i < 750; $i++) {
            $gender = rand(0, 1) ? 'male' : 'female';
            $imageNumber = $patientImages[array_rand($patientImages)];

            $user = User::create([
                'name' => $this->getRandomArabicName('patient'),
                'email' => "patient_{$i}@tamman.ps",
                'password' => Hash::make('password123'),
                'phone' => '+9705' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'gender' => $gender,
                'date_of_birth' => $this->getRandomDate('-65 years', '-18 years'),
                'is_active' => true,
                'total_points' => rand(0, 1000),
                'credit_balance' => rand(0, 200) * 5,
                'referral_code' => 'TAM' . Str::upper(Str::random(6)),
                'email_verified_at' => now(),
                'profile_image' => "images/profile_seed/profile_{$imageNumber}.jpg",
                'notification_settings' => json_encode([
                    'session_reminders' => true,
                    'points_earned' => true,
                    'new_messages' => true,
                    'treatment_tasks' => true,
                    'promotional_emails' => false,
                ]),
                'privacy_settings' => json_encode([
                    'profile_visibility' => 'public',
                    'show_email' => false,
                    'show_phone' => false,
                    'show_activity_status' => true,
                    'allow_messages_from' => 'everyone',
                ]),
                'preferred_locale' => 'ar',
            ]);
            $user->assignRole('patient');
            $patients[] = $user;
        }

        // Create 50 Donors (from existing patients and specialists)
        $this->command->info('  - Creating 50 Donor Profiles...');

        $donorCandidates = array_merge(array_slice($patients, 0, 40), array_slice($specialists, 0, 10));
        $donorCandidates = array_slice($donorCandidates, 0, 50);

        foreach ($donorCandidates as $index => $donorUser) {
            if (!$donorUser->hasRole('donor')) {
                $donorUser->assignRole('donor');
            }

            DonorProfile::updateOrCreate(
                ['user_id' => $donorUser->id],
                [
                    'organization_name' => $index < 10 ? $this->getRandomOrganizationName() : null,
                    'tax_id' => $index < 10 ? 'TAX-' . rand(10000000, 99999999) : null,
                    'total_donated' => rand(100, 50000),
                ]
            );
        }

        $this->command->info('  - Donor Profiles Completed: 50');
        $this->command->info('  - Total Users Created: ' . (1 + 200 + 750));

        // ==================== STEP 3: Create Specialist Profiles ====================
        $this->command->info('');
        $this->command->info('Step 3: Creating Specialist Profiles...');

        $specializations = [
            'علم النفس السريري',
            'العلاج النفسي',
            'العلاج السلوكي المعرفي',
            'علاج الصدمات النفسية',
            'علم نفس الطفل',
            'العلاج الأسري',
            'علاج الإدمان',
            'الطب النفسي',
            'الخدمة الاجتماعية',
            'العلاج بالفن'
        ];

        $licenseFiles = ['license_01.jpg', 'license_02.jpg', 'license_03.jpg', 'license_04.jpg', 'license_05.jpg'];
        $certificateFiles = ['certificate_01.jpg', 'certificate_02.jpg', 'certificate_03.jpg', 'certificate_04.jpg', 'certificate_05.jpg'];

        foreach ($specialists as $specialist) {
            $experienceYears = rand(1, 30);
            $totalSessions = rand(0, 1000);
            $ratingAvg = $totalSessions > 0 ? rand(30, 50) / 10 : 0;

            SpecialistProfile::create([
                'user_id' => $specialist->id,
                'license_number' => 'TRKH-' . strtoupper(Str::random(3) . '-' . rand(100, 999)),
                'specialization' => $specializations[array_rand($specializations)],
                'qualifications' => $this->getRandomQualifications(),
                'bio' => $this->getRandomBio(),
                'consultation_fee' => rand(80, 350),
                'languages' => rand(0, 1) ? 'العربية' : 'العربية, English',
                'experience_years' => $experienceYears,
                'certificate_file' => 'certificate_file_seed/' . $certificateFiles[array_rand($certificateFiles)],
                'license_file' => 'licence_file_seed/' . $licenseFiles[array_rand($licenseFiles)],
                'rating_avg' => $ratingAvg,
                'total_sessions' => $totalSessions,
                'is_verified' => true,
                'application_status' => 'approved',
                'applied_at' => $this->getRandomDate('-90 days', '-30 days'),
                'verified_at' => now(),
                'application_notes' => 'تمت الموافقة على الطلب بعد التحقق من جميع المستندات والمؤهلات.',
            ]);
        }
        $this->command->info('  - Specialist Profiles Completed: 200');

        // ==================== STEP 4: Create Availability Slots ====================
        $this->command->info('Step 4: Creating Availability Slots...');

        $availabilityCount = 0;
        $timeSlotsConfig = [
            ['start' => '09:00:00', 'end' => '12:00:00'],
            ['start' => '12:00:00', 'end' => '13:00:00'],
            ['start' => '13:00:00', 'end' => '16:00:00'],
            ['start' => '16:00:00', 'end' => '19:00:00'],
        ];

        foreach ($specialists as $specialist) {
            $daysToAdd = rand(3, 6);
            $availableDays = [];

            for ($i = 0; $i < $daysToAdd; $i++) {
                $day = rand(0, 6);
                if (!in_array($day, $availableDays)) {
                    $availableDays[] = $day;
                } else {
                    for ($j = 0; $j < 7 && count($availableDays) < $daysToAdd; $j++) {
                        if (!in_array($j, $availableDays)) {
                            $availableDays[] = $j;
                            break;
                        }
                    }
                }
            }

            foreach ($availableDays as $day) {
                $numSlots = rand(1, 4);
                $selectedSlots = (array) array_rand($timeSlotsConfig, min($numSlots, count($timeSlotsConfig)));

                foreach ($selectedSlots as $slotIndex) {
                    $slot = $timeSlotsConfig[$slotIndex];
                    Availability::create([
                        'specialist_id' => $specialist->id,
                        'day_of_week' => $day,
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end'],
                        'is_recurring' => true,
                        'specific_date' => null,
                        'is_available' => true,
                    ]);
                    $availabilityCount++;
                }
            }

            $numOneTime = rand(0, 2);
            for ($i = 0; $i < $numOneTime; $i++) {
                $futureDate = date('Y-m-d', strtotime('+' . rand(7, 60) . ' days'));
                $slot = $timeSlotsConfig[array_rand($timeSlotsConfig)];
                Availability::create([
                    'specialist_id' => $specialist->id,
                    'day_of_week' => null,
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                    'is_recurring' => false,
                    'specific_date' => $futureDate,
                    'is_available' => true,
                ]);
                $availabilityCount++;
            }
        }
        $this->command->info("  - Availability Slots Completed: {$availabilityCount}");

        // ==================== STEP 5: Create Therapy Sessions ====================
        $this->command->info('Step 5: Creating 3000 Therapy Sessions...');

        $sessions = [];
        $statuses = ['scheduled', 'completed', 'cancelled', 'no_show'];
        $types = ['video', 'audio', 'text'];

        for ($i = 0; $i < 3000; $i++) {
            $patient = $patients[array_rand($patients)];
            $specialist = $specialists[array_rand($specialists)];
            $status = $statuses[array_rand($statuses)];
            $type = $types[array_rand($types)];
            $sessionDate = $this->getRandomSessionDate($status);

            // Generate secure, unique room name (only for non-cancelled sessions)
            $secureRoomName = null;
            $meetingLink = null;

            if ($status !== 'cancelled') {
                // Use session ID placeholder (will be actual ID after creation)
                // For seeder, we generate a unique name without session ID
                $timestamp = $sessionDate instanceof \DateTime ? $sessionDate->getTimestamp() : time();
                $randomBytes = bin2hex(random_bytes(32)); // 64 characters
                $secureRoomName = 'tamman-seed-' . $i . '-' . $timestamp . '-' . $randomBytes;
                $meetingLink = 'https://meet.jit.si/' . $secureRoomName;
            }

            $session = TherapySession::create([
                'patient_id' => $patient->id,
                'specialist_id' => $specialist->id,
                'session_datetime' => $sessionDate,
                'duration_minutes' => rand(30, 90),
                'status' => $status,
                'session_type' => $type,
                'meeting_link' => $meetingLink,
                'secure_room_name' => $secureRoomName,
                'specialist_joined' => false,
                'patient_joined' => false,
                'specialist_joined_at' => null,
                'patient_joined_at' => null,
                'notes' => $status === 'completed' ? $this->getRandomSessionNote() : null,
                'points_awarded' => $status === 'completed' ? rand(5, 15) : 0,
                'is_paid_by_credit' => rand(0, 100) < 20,
                'is_free' => false,
                'reward_redemption_id' => null,
            ]);

            $sessions[] = $session;

            if (($i + 1) % 500 == 0) {
                $this->command->info("      Created " . ($i + 1) . " sessions...");
            }
        }
        $this->command->info("  - Therapy Sessions Completed: 3000");

        // ==================== STEP 6: Create Mood Logs ====================
        $this->command->info('Step 6: Creating 20,000 Mood Logs...');

        $moodLabels = [
            1 => 'سيء جداً',
            2 => 'سيء',
            3 => 'حزين',
            4 => 'منزعج',
            5 => 'محايد',
            6 => 'مرتاح',
            7 => 'جيد',
            8 => 'سعيد',
            9 => 'سعيد جداً',
            10 => 'ممتاز'
        ];

        $usedCombinations = [];
        $moodLogsInserted = 0;
        $maxAttempts = 50000;
        $attempts = 0;

        while ($moodLogsInserted < 20000 && $attempts < $maxAttempts) {
            $attempts++;
            $patient = $patients[array_rand($patients)];
            $moodValue = rand(1, 10);
            $logDate = date('Y-m-d', strtotime($this->getRandomDate('-60 days', 'now')));

            $key = $patient->id . '|' . $logDate;

            if (!isset($usedCombinations[$key])) {
                $usedCombinations[$key] = true;

                MoodLog::updateOrCreate(
                    [
                        'user_id' => $patient->id,
                        'log_date' => $logDate,
                    ],
                    [
                        'mood_value' => $moodValue,
                        'mood_label' => $moodLabels[$moodValue],
                        'notes' => rand(0, 100) < 30 ? $this->getRandomMoodNote() : null,
                    ]
                );
                $moodLogsInserted++;

                if ($moodLogsInserted % 2000 == 0) {
                    $this->command->info("      Created {$moodLogsInserted} mood logs...");
                }
            }
        }
        $this->command->info("  - Mood Logs Completed: {$moodLogsInserted}");

        // ==================== STEP 7: Create Test Results ====================
        $this->command->info('Step 7: Creating 10,000 Test Results (6 test types)...');

        $testTypes = ['phq9', 'gad7', 'pcl5', 'isi', 'pss', 'cis'];

        $resultLevels = [
            'phq9' => ['minimal', 'mild', 'moderate', 'moderately_severe', 'severe'],
            'gad7' => ['minimal', 'mild', 'moderate', 'severe'],
            'pcl5' => ['minimal', 'mild', 'moderate'],
            'isi' => ['none', 'subthreshold', 'moderate', 'severe'],
            'pss' => ['low', 'moderate', 'high'],
            'cis' => ['minimal', 'moderate'],
        ];

        $scoreRanges = [
            'phq9' => 27,
            'gad7' => 21,
            'pcl5' => 80,
            'isi' => 28,
            'pss' => 40,
            'cis' => 52,
        ];

        for ($i = 0; $i < 10000; $i++) {
            $patient = $patients[array_rand($patients)];
            $testType = $testTypes[array_rand($testTypes)];
            $maxScore = $scoreRanges[$testType];
            $score = rand(0, $maxScore);

            $levels = $resultLevels[$testType];
            $levelCount = count($levels);
            $levelIndex = floor($score / ($maxScore / $levelCount));
            $levelIndex = min($levelIndex, $levelCount - 1);

            TestResult::create([
                'user_id' => $patient->id,
                'test_type' => $testType,
                'score' => $score,
                'result_level' => $levels[$levelIndex],
                'answers' => json_encode($this->getRandomAnswers($testType)),
                'test_date' => $this->getRandomDate('-90 days', 'now'),
            ]);

            if (($i + 1) % 2000 == 0) {
                $this->command->info("      Created " . ($i + 1) . " test results...");
            }
        }
        $this->command->info("  - Test Results Completed: 10000");

        // ==================== STEP 8: Create Point Transactions ====================
        $this->command->info('Step 8: Creating 15,000 Point Transactions...');

        $sources = ['mood_tracking', 'session_attendance', 'test_completed', 'task_completed', 'referral', 'specialist_rating', 'streak_bonus', 'booking'];
        $sourceDescriptions = [
            'mood_tracking' => 'نقاط لتتبع المزاج اليومي',
            'session_attendance' => 'نقاط لحضور الجلسة العلاجية',
            'test_completed' => 'نقاط لإكمال الاختبار النفسي',
            'task_completed' => 'نقاط لإكمال المهمة العلاجية',
            'referral' => 'نقاط مكافأة لدعوة صديق للمنصة',
            'specialist_rating' => 'نقاط لتقييم المختص',
            'streak_bonus' => 'مكافأة الاستمرارية',
            'booking' => 'نقاط لحجز الجلسة',
        ];

        for ($i = 0; $i < 15000; $i++) {
            $user = $patients[array_rand($patients)];
            $source = $sources[array_rand($sources)];

            $pointsMap = [
                'mood_tracking' => rand(1, 5),
                'session_attendance' => rand(10, 20),
                'test_completed' => rand(5, 15),
                'task_completed' => rand(3, 10),
                'referral' => rand(50, 100),
                'specialist_rating' => rand(3, 8),
                'streak_bonus' => rand(10, 30),
                'booking' => 5,
            ];
            $points = $pointsMap[$source];

            PointTransaction::create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => 'earned',
                'source' => $source,
                'description' => $sourceDescriptions[$source],
                'created_at' => $this->getRandomDate('-60 days', 'now'),
            ]);

            if (($i + 1) % 3000 == 0) {
                $this->command->info("      Created " . ($i + 1) . " point transactions...");
            }
        }
        $this->command->info("  - Point Transactions Completed: 15000");

        // ==================== STEP 9: Create Treatment Plans and Tasks ====================
        $this->command->info('Step 9: Creating 1500 Treatment Plans and Tasks...');

        $planTitles = [
            'خطة علاج القلق',
            'خطة علاج الاكتئاب',
            'خطة تحسين المزاج',
            'خطة إدارة التوتر',
            'خطة التعافي من الصدمة',
            'خطة تحسين النوم'
        ];

        $taskCount = 0;
        for ($i = 0; $i < 1500; $i++) {
            $patient = $patients[array_rand($patients)];
            $specialist = $specialists[array_rand($specialists)];
            $status = rand(0, 100) < 70 ? 'active' : (rand(0, 1) ? 'completed' : 'cancelled');

            $plan = TreatmentPlan::create([
                'specialist_id' => $specialist->id,
                'patient_id' => $patient->id,
                'title' => $planTitles[array_rand($planTitles)],
                'description' => $this->getRandomPlanDescription(),
                'start_date' => $this->getRandomDate('-60 days', 'now'),
                'end_date' => $status === 'completed' ? $this->getRandomDate('-30 days', 'now') : ($status === 'active' ? $this->getRandomDate('+1 week', '+2 months') : null),
                'status' => $status,
            ]);

            $numTasks = rand(2, 5);
            for ($j = 0; $j < $numTasks; $j++) {
                $isCompleted = $status === 'completed' ? true : (rand(0, 100) < 60);
                TreatmentTask::create([
                    'plan_id' => $plan->id,
                    'title' => $this->getRandomTaskTitle(),
                    'description' => $this->getRandomTaskDescription(),
                    'is_completed' => $isCompleted,
                    'due_date' => $isCompleted ? $this->getRandomDate('-30 days', 'now') : $this->getRandomDate('now', '+1 month'),
                    'points_reward' => rand(3, 15),
                    'completed_at' => $isCompleted ? $this->getRandomDate('-30 days', 'now') : null,
                ]);
                $taskCount++;
            }

            if (($i + 1) % 300 == 0) {
                $this->command->info("      Created " . ($i + 1) . " treatment plans with {$taskCount} tasks...");
            }
        }
        $this->command->info("  - Treatment Plans: 1500, Tasks: {$taskCount}");

        // ==================== STEP 10: Create Conversations & Messages (NEW SCHEMA) ====================
        $this->command->info('Step 10: Creating Conversations and Messages...');

        // 10.1 GET USERS
        $this->command->info('  - Loading users...');

        $patients = User::whereHas('roles', fn($q) => $q->where('name', 'patient'))
            ->take(100)
            ->get();

        $specialists = User::whereHas('roles', fn($q) => $q->where('name', 'specialist'))
            ->whereHas('specialistProfile', fn($q) => $q->where('is_verified', true))
            ->take(50)
            ->get();

        $this->command->info("      Using " . $patients->count() . " patients and " . $specialists->count() . " specialists");

        // 10.2 GET COMPLETED TEXT SESSIONS
        $this->command->info('  - Loading completed text sessions...');

        $textSessions = TherapySession::where('session_type', 'text')
            ->where('status', 'completed')
            ->take(500)
            ->get();

        $this->command->info("      Found " . $textSessions->count() . " completed text sessions");

        // 10.3 CREATE CONVERSATIONS
        $this->command->info('  - Creating conversations...');

        $conversations = [];
        $conversationCount = 0;
        $maxConversations = 1500;

        foreach ($patients as $patient) {
            if ($conversationCount >= $maxConversations) break;

            foreach ($specialists as $specialist) {
                if ($conversationCount >= $maxConversations) break;

                $participantOne = min($patient->id, $specialist->id);
                $participantTwo = max($patient->id, $specialist->id);

                // Find a session between this pair (if exists)
                $pairSession = $textSessions->filter(function ($session) use ($patient, $specialist) {
                    return ($session->patient_id == $patient->id && $session->specialist_id == $specialist->id);
                })->first();

                $isLocked = !$pairSession; // Locked if no session yet

                $conversation = Conversation::firstOrCreate(
                    ['participant_one' => $participantOne, 'participant_two' => $participantTwo],
                    [
                        'is_locked' => $isLocked,
                        'locked_at' => $isLocked ? now() : null,
                        'therapy_session_id' => $pairSession?->id,
                        'is_text_session' => $pairSession ? true : false,
                    ]
                );

                $conversations[] = $conversation;
                $conversationCount++;
            }
        }

        $this->command->info("      Created " . count($conversations) . " conversations");

        // 10.4 MESSAGE TEMPLATES
        $messageTemplates = [
            'مرحباً، كيف تشعر اليوم؟',
            'أنا بحاجة للتحدث معك عن شيء مهم',
            'شكراً لك على مساعدتك، أشعر بتحسن كبير',
            'هل يمكننا تغيير موعد الجلسة القادمة؟',
            'لدي بعض الأسئلة حول التمارين',
            'كيف حالك؟ تذكر أن تمارس تمارين التنفس',
            'أنا هنا لدعمك دائماً',
            'تقدمك رائع، استمر في العمل الجيد',
            'تذكر موعد جلسة الغد الساعة 3 مساءً',
            'هل قمت بتسجيل حالتك المزاجية اليوم؟',
        ];

        // 10.5 CREATE MESSAGES
        $this->command->info('  - Creating messages...');

        $allMessages = [];
        $batchSize = 100;
        $totalMessages = 0;

        foreach ($conversations as $idx => $conversation) {
            $conversationMessages = [];
            $lastDate = now()->subDays(rand(1, 45));

            // Determine who is patient and who is specialist
            $patientId = null;
            $specialistId = null;

            $userOneIsPatient = $patients->contains('id', $conversation->participant_one);
            $userTwoIsPatient = $patients->contains('id', $conversation->participant_two);

            if ($userOneIsPatient) {
                $patientId = $conversation->participant_one;
                $specialistId = $conversation->participant_two;
            } else {
                $patientId = $conversation->participant_two;
                $specialistId = $conversation->participant_one;
            }

            // ========== A. General chat messages (5-15 messages) ==========
            $generalMsgCount = rand(5, 15);

            for ($i = 0; $i < $generalMsgCount; $i++) {
                $isPatientSending = rand(0, 1);
                $senderId = $isPatientSending ? $patientId : $specialistId;
                $receiverId = $isPatientSending ? $specialistId : $patientId;

                $lastDate = $lastDate->addMinutes(rand(30, 360));

                $conversationMessages[] = [
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'conversation_id' => $conversation->id,
                    'content' => $messageTemplates[array_rand($messageTemplates)],
                    'is_read' => rand(0, 100) < 85 ? 1 : 0,
                    'is_system_message' => 0,
                    'is_deleted_by_sender' => 0,
                    'is_deleted_by_receiver' => 0,
                    'is_deleted_for_everyone' => 0,
                    'sent_at' => $lastDate,
                    'created_at' => $lastDate,
                    'updated_at' => $lastDate,
                ];
                $totalMessages++;
            }

            // ========== B. Session messages (if conversation has a session) ==========
            if ($conversation->therapy_session_id) {
                $session = TherapySession::find($conversation->therapy_session_id);
                if ($session && $session->session_datetime < now()) {
                    $sessionStart = Carbon::parse($session->session_datetime);
                    $sessionEnd = $sessionStart->copy()->addMinutes($session->duration_minutes ?? 60);

                    // Session started system message
                    $conversationMessages[] = [
                        'sender_id' => $specialistId,
                        'receiver_id' => $patientId,
                        'conversation_id' => $conversation->id,
                        'content' => '🌟 ' . __('Text therapy session has started'),
                        'is_read' => 1,
                        'is_system_message' => 1,
                        'is_deleted_by_sender' => 0,
                        'is_deleted_by_receiver' => 0,
                        'is_deleted_for_everyone' => 0,
                        'sent_at' => $sessionStart,
                        'created_at' => $sessionStart,
                        'updated_at' => $sessionStart,
                    ];
                    $totalMessages++;

                    $currentTime = $sessionStart->copy()->addMinutes(2);
                    $sessionMsgCount = rand(4, 12);

                    for ($i = 0; $i < $sessionMsgCount; $i++) {
                        $isPatientSending = rand(0, 1);
                        $senderId = $isPatientSending ? $patientId : $specialistId;
                        $receiverId = $isPatientSending ? $specialistId : $patientId;

                        $currentTime = $currentTime->addMinutes(rand(1, 4));
                        if ($currentTime >= $sessionEnd) break;

                        $conversationMessages[] = [
                            'sender_id' => $senderId,
                            'receiver_id' => $receiverId,
                            'conversation_id' => $conversation->id,
                            'content' => $messageTemplates[array_rand($messageTemplates)],
                            'is_read' => 1,
                            'is_system_message' => 0,
                            'is_deleted_by_sender' => 0,
                            'is_deleted_by_receiver' => 0,
                            'is_deleted_for_everyone' => 0,
                            'sent_at' => $currentTime,
                            'created_at' => $currentTime,
                            'updated_at' => $currentTime,
                        ];
                        $totalMessages++;
                    }

                    // Session ended system message
                    $conversationMessages[] = [
                        'sender_id' => $specialistId,
                        'receiver_id' => $patientId,
                        'conversation_id' => $conversation->id,
                        'content' => '🔒 ' . __('Text therapy session has ended'),
                        'is_read' => 1,
                        'is_system_message' => 1,
                        'is_deleted_by_sender' => 0,
                        'is_deleted_by_receiver' => 0,
                        'is_deleted_for_everyone' => 0,
                        'sent_at' => $sessionEnd,
                        'created_at' => $sessionEnd,
                        'updated_at' => $sessionEnd,
                    ];
                    $totalMessages++;
                }
            }

            // Sort messages chronologically
            usort($conversationMessages, fn($a, $b) => strtotime($a['sent_at']) - strtotime($b['sent_at']));

            // Batch insert
            foreach ($conversationMessages as $msg) {
                $allMessages[] = $msg;
                if (count($allMessages) >= $batchSize) {
                    Message::insert($allMessages);
                    $allMessages = [];
                }
            }

            // Update conversation last message
            if (!empty($conversationMessages)) {
                $lastMsg = end($conversationMessages);
                Conversation::where('id', $conversation->id)->update([
                    'last_message' => substr($lastMsg['content'], 0, 100),
                    'last_message_at' => $lastMsg['sent_at'],
                    'last_message_by' => $lastMsg['sender_id'],
                ]);
            }

            if (($idx + 1) % 100 == 0) {
                $this->command->info("      Processed " . ($idx + 1) . " conversations");
            }
        }

        // Insert remaining messages
        if (count($allMessages) > 0) {
            Message::insert($allMessages);
        }

        $this->command->info("  - TOTAL messages created: " . $totalMessages);

        // ==================== STEP 11: Create Reviews ====================
        $this->command->info('Step 11: Creating 1500 Reviews...');

        $completedSessions = array_filter($sessions, fn($s) => $s->status === 'completed');
        $completedSessions = array_values($completedSessions);
        $reviewCount = min(1500, count($completedSessions));

        for ($i = 0; $i < $reviewCount; $i++) {
            $session = $completedSessions[$i];
            $rating = rand(1, 5);

            Review::create([
                'session_id' => $session->id,
                'reviewer_id' => $session->patient_id,
                'specialist_id' => $session->specialist_id,
                'rating' => $rating,
                'comment' => $rating >= 4 ? $this->getPositiveReview() : ($rating <= 2 ? $this->getNegativeReview() : $this->getNeutralReview()),
            ]);
        }
        $this->command->info("  - Reviews Completed: {$reviewCount}");

        // ==================== STEP 12: Create Credit Transactions (with type and parent relations) ====================
        $this->command->info('Step 12: Creating 1500 Credit Transactions with proper relationships...');

        $donorUsersList = User::role('donor')->get();
        $statuses = ['pending', 'allocated', 'used', 'expired'];

        if ($donorUsersList->count() > 0) {

            // ========== 1. Create Credit Requests (patients requesting to add credits) ==========
            $this->command->info('  - Creating Credit Requests...');
            for ($i = 0; $i < 300; $i++) {
                $patient = $patients->random();
                CreditTransaction::create([
                    'donor_id' => null,
                    'recipient_id' => $patient->id,
                    'amount' => rand(50, 500),
                    'status' => $this->faker->randomElement($statuses),
                    'type' => 'credit_request',
                    'parent_transaction_id' => null,
                    'description' => 'طلب شحن رصيد من المستخدم: ' . $patient->name,
                ]);
            }

            // ========== 2. Create Donations (donors giving money) ==========
            $this->command->info('  - Creating Donations...');
            $donations = [];
            for ($i = 0; $i < 400; $i++) {
                $donor = $donorUsersList->random();
                $amount = rand(100, 5000);
                $status = $this->faker->randomElement(['pending', 'allocated']);

                $donation = CreditTransaction::create([
                    'donor_id' => $donor->id,
                    'recipient_id' => null,
                    'amount' => $amount,
                    'status' => $status,
                    'type' => 'donation',
                    'parent_transaction_id' => null,
                    'description' => 'تبرع من المستخدم: ' . $donor->name . ' بمبلغ $' . $amount,
                ]);
                $donations[] = $donation;

                // Update donor profile total donated
                $donorProfile = DonorProfile::firstOrCreate(['user_id' => $donor->id]);
                $donorProfile->total_donated = ($donorProfile->total_donated ?? 0) + $amount;
                $donorProfile->save();
            }

            // ========== 3. Create Donation Allocations (admin allocating to patients) ==========
            $this->command->info('  - Creating Donation Allocations...');

            $allocationCount = 0;
            foreach ($donations as $donation) {
                // Only allocate from donations that are allocated
                if ($donation->status !== 'allocated') {
                    continue;
                }

                $remainingAmount = $donation->amount;
                $allocatedTotal = 0;

                // Get all allocations for this donation to calculate remaining
                $existingAllocations = CreditTransaction::where('parent_transaction_id', $donation->id)->sum('amount');
                $remainingAmount = $donation->amount - $existingAllocations;

                if ($remainingAmount <= 0) {
                    continue;
                }

                // Create 1-3 allocations per donation
                $numAllocations = rand(1, min(3, floor($remainingAmount / 50)));

                for ($j = 0; $j < $numAllocations; $j++) {
                    if ($remainingAmount <= 0) {
                        break;
                    }

                    // Allocate random amount, but leave at least 0 for next allocations
                    $maxAllocation = $remainingAmount - (($numAllocations - $j - 1) * 50);
                    $allocationAmount = rand(50, min(500, $maxAllocation));
                    $allocationAmount = min($allocationAmount, $remainingAmount);

                    $patient = $patients->random();

                    CreditTransaction::create([
                        'donor_id' => $donation->donor_id,
                        'recipient_id' => $patient->id,
                        'amount' => $allocationAmount,
                        'status' => 'allocated',
                        'type' => 'donation_allocation',
                        'parent_transaction_id' => $donation->id,  // LINK TO PARENT DONATION
                        'description' => 'تم تخصيص تبرع من ' . ($donation->donor?->name ?? 'متبرع') . ' للمريض ' . $patient->name,
                    ]);

                    $remainingAmount -= $allocationAmount;
                    $allocatedTotal += $allocationAmount;
                    $allocationCount++;

                    // Add to patient's credit balance
                    $patient->credit_balance += $allocationAmount;
                    $patient->save();
                }

                // If donation is fully allocated, mark it as used
                if ($remainingAmount <= 0) {
                    $donation->status = 'used';
                    $donation->save();
                }
            }

            // ========== 4. Create some standalone allocations (orphaned - for testing) ==========
            $this->command->info('  - Creating additional allocations...');
            for ($i = 0; $i < 200; $i++) {
                $donor = $donorUsersList->random();
                $patient = $patients->random();
                $amount = rand(50, 500);

                CreditTransaction::create([
                    'donor_id' => $donor->id,
                    'recipient_id' => $patient->id,
                    'amount' => $amount,
                    'status' => 'allocated',
                    'type' => 'donation_allocation',
                    'parent_transaction_id' => null,  // orphaned allocation (no parent donation)
                    'description' => 'تخصيص مباشر من المتبرع ' . $donor->name . ' للمريض ' . $patient->name,
                ]);

                $patient->credit_balance += $amount;
                $patient->save();

                // Update donor profile
                $donorProfile = DonorProfile::firstOrCreate(['user_id' => $donor->id]);
                $donorProfile->total_donated = ($donorProfile->total_donated ?? 0) + $amount;
                $donorProfile->save();
            }

            $this->command->info("  - Credit Transactions Completed: 300 Credit Requests + 400 Donations + {$allocationCount} Allocations");
        }

        // ==================== STEP 13: Create Content ====================
        $this->command->info('Step 13: Creating 200 Content Items...');

        $contentTypes = ['article', 'video', 'tip', 'guide'];
        $youtubeUrls = [
            'https://www.youtube.com/watch?v=YFSc7CkZqVY',
            'https://www.youtube.com/watch?v=inpok4MKVLM',
            'https://www.youtube.com/watch?v=WWloiaQqxMc',
            'https://www.youtube.com/watch?v=6p_yaNFSYao',
            'https://www.youtube.com/watch?v=5MuIMqhT8DM',
            'https://www.youtube.com/watch?v=zLq8gFROsXc',
        ];

        for ($i = 0; $i < 200; $i++) {
            $type = $contentTypes[array_rand($contentTypes)];
            $isPublished = rand(0, 100) < 85;
            $contentData = $this->getContentByType($type, $youtubeUrls);

            Content::create([
                'created_by' => $admin->id,
                'title' => $contentData['title'],
                'body' => $contentData['body'],
                'type' => $type,
                'media_url' => $contentData['media_url'],
                'is_published' => $isPublished,
                'published_at' => $isPublished ? $this->getRandomDate('-60 days', 'now') : null,
            ]);
        }
        $this->command->info("  - Content Items Completed: 200");

        // ==================== STEP 14: Create System Logs with Proper JSON Details ====================
        $this->command->info('Step 14: Creating System Logs with proper JSON details...');

        // Get admin user
        $admin = User::where('email', 'admin@tamman.ps')->first();
        if (!$admin) {
            $admin = User::role('admin')->first();
        }

        // Get specialists and patients for reference
        $specialistsList = User::role('specialist')->get();
        $patientsList = User::role('patient')->get();

        $logCount = 0;
        $now = Carbon::now();

        // ========== 1. Approval Logs (approve_specialist, reject_specialist, request_info) ==========
        $this->command->info('  - Creating Approval Logs...');

        // Approved Specialists (50 logs)
        for ($i = 0; $i < 50; $i++) {
            $specialist = $specialistsList->random();
            $approvedAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'approve_specialist',
                'details' => [
                    'specialist_id' => $specialist->id,
                    'specialist_name' => $specialist->name,
                    'specialist_email' => $specialist->email,
                    'license_number' => $specialist->specialistProfile->license_number,
                    'specialization' => $specialist->specialistProfile->specialization,
                    'experience_years' => $specialist->specialistProfile->experience_years,
                    'approved_at' => $approvedAt,
                ],
                'created_at' => $approvedAt,
                'updated_at' => $approvedAt,
            ]);
            $logCount++;
        }

        // Rejected Specialists (20 logs)
        for ($i = 0; $i < 20; $i++) {
            $specialist = $specialistsList->random();
            $rejectedAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'reject_specialist',
                'details' => [
                    'specialist_id' => $specialist->id,
                    'specialist_name' => $specialist->name,
                    'specialist_email' => $specialist->email,
                    'reason' => 'نقص المستندات المطلوبة للتوثيق',
                    'rejected_at' => $rejectedAt,
                ],
                'created_at' => $rejectedAt,
                'updated_at' => $rejectedAt,
            ]);
            $logCount++;
        }

        // Request Info (15 logs)
        for ($i = 0; $i < 15; $i++) {
            $specialist = $specialistsList->random();
            $requestedAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'request_info_to_specialist',
                'details' => [
                    'specialist_id' => $specialist->id,
                    'specialist_name' => $specialist->name,
                    'request_message' => 'يرجى تقديم شهادة خبرة إضافية تثبت سنوات العمل في مجال العلاج النفسي.',
                    'requested_at' => $requestedAt,
                ],
                'created_at' => $requestedAt,
                'updated_at' => $requestedAt,
            ]);
            $logCount++;
        }

        // ========== 2. Content Management Logs ==========
        $this->command->info('  - Creating Content Management Logs...');

        // Content items for reference
        $contentItems = Content::all();
        if ($contentItems->count() == 0) {
            // Create dummy content if none exists
            $contentItems = collect();
            for ($i = 0; $i < 30; $i++) {
                $contentItems->push((object) [
                    'id' => $i + 1000,
                    'title' => 'محتوى رقم ' . ($i + 1),
                    'type' => ['article', 'video', 'tip', 'guide'][array_rand(['article', 'video', 'tip', 'guide'])],
                ]);
            }
        }

        // Create Content (40 logs)
        for ($i = 0; $i < 40; $i++) {
            $content = $contentItems->random();
            $createdAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'create_content',
                'details' => [
                    'content_id' => is_object($content) ? $content->id : $content['id'],
                    'content_title' => is_object($content) ? $content->title : $content['title'],
                    'content_type' => is_object($content) ? $content->type : $content['type'],
                    'is_published' => rand(0, 100) < 70,
                    'created_at' => $createdAt,
                ],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            $logCount++;
        }

        // Update Content (30 logs)
        for ($i = 0; $i < 30; $i++) {
            $content = $contentItems->random();
            $updatedAt = $this->getRandomDate('-60 days', 'now');
            $updatedFields = ['title', 'body', 'media_url'];
            $numFields = rand(1, 2);

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'update_content',
                'details' => [
                    'content_id' => is_object($content) ? $content->id : $content['id'],
                    'content_title' => is_object($content) ? $content->title : $content['title'],
                    'updated_fields' => array_rand(array_flip($updatedFields), $numFields),
                    'updated_at' => $updatedAt,
                ],
                'created_at' => $updatedAt,
                'updated_at' => $updatedAt,
            ]);
            $logCount++;
        }

        // Delete Content (15 logs)
        for ($i = 0; $i < 15; $i++) {
            $content = $contentItems->random();
            $deletedAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'delete_content',
                'details' => [
                    'content_id' => is_object($content) ? $content->id : $content['id'],
                    'content_title' => is_object($content) ? $content->title : $content['title'],
                    'content_type' => is_object($content) ? $content->type : $content['type'],
                    'deleted_at' => $deletedAt,
                ],
                'created_at' => $deletedAt,
                'updated_at' => $deletedAt,
            ]);
            $logCount++;
        }

        // Publish/Unpublish Content (25 logs)
        for ($i = 0; $i < 25; $i++) {
            $content = $contentItems->random();
            $isPublish = rand(0, 100) < 70;
            $action = $isPublish ? 'publish_content' : 'unpublish_content';
            $actionAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => $action,
                'details' => [
                    'content_id' => is_object($content) ? $content->id : $content['id'],
                    'content_title' => is_object($content) ? $content->title : $content['title'],
                    ($isPublish ? 'published_at' : 'unpublished_at') => $actionAt,
                ],
                'created_at' => $actionAt,
                'updated_at' => $actionAt,
            ]);
            $logCount++;
        }

        // ========== 3. Payment Logs ==========
        $this->command->info('  - Creating Payment Logs...');

        // Credit Requests (Approve/Reject)
        $creditTransactions = CreditTransaction::where('type', 'credit_request')->get();
        foreach ($creditTransactions as $transaction) {
            if (rand(0, 100) < 30) {
                $isApprove = rand(0, 100) < 70;
                $action = $isApprove ? 'approve_credit_request' : 'reject_credit_request';
                $actionAt = $this->getRandomDate('-60 days', 'now');

                $details = [
                    'user_id' => $transaction->recipient_id,
                    'user_name' => $transaction->recipient->name,
                    'user_email' => $transaction->recipient->email,
                    'amount' => $transaction->amount,
                    'request_id' => $transaction->id,
                    ($isApprove ? 'approved_at' : 'rejected_at') => $actionAt,
                ];

                if (!$isApprove) {
                    $details['reason'] = 'بيانات الدفعة غير صحيحة';
                }

                SystemLog::create([
                    'admin_id' => $admin->id,
                    'action' => $action,
                    'details' => $details,
                    'created_at' => $actionAt,
                    'updated_at' => $actionAt,
                ]);
                $logCount++;
            }
        }

        // Donations (Approve/Reject/Allocate)
        $donations = CreditTransaction::where('type', 'donation')->get();
        foreach ($donations as $donation) {
            if (rand(0, 100) < 40) {
                $action = rand(0, 100) < 70 ? 'approve_donation' : 'reject_donation';
                $actionAt = $this->getRandomDate('-60 days', 'now');

                $details = [
                    'donor_id' => $donation->donor_id,
                    'donor_name' => $donation->donor->name,
                    'donor_email' => $donation->donor->email,
                    'amount' => $donation->amount,
                    'donation_id' => $donation->id,
                    ($action === 'approve_donation' ? 'approved_at' : 'rejected_at') => $actionAt,
                ];

                if ($action === 'reject_donation') {
                    $details['reason'] = 'طريقة الدفع غير مدعومة';
                }

                SystemLog::create([
                    'admin_id' => $admin->id,
                    'action' => $action,
                    'details' => $details,
                    'created_at' => $actionAt,
                    'updated_at' => $actionAt,
                ]);
                $logCount++;
            }
        }

        // Donation Allocations
        $allocations = CreditTransaction::where('type', 'donation_allocation')->take(50)->get();
        foreach ($allocations as $allocation) {
            $allocatedAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'allocate_donation',
                'details' => [
                    'donor_id' => $allocation->donor_id,
                    'donor_name' => $allocation->donor->name ?? 'متبرع غير معروف',
                    'recipient_id' => $allocation->recipient_id,
                    'recipient_name' => $allocation->recipient->name,
                    'amount' => $allocation->amount,
                    'donation_id' => $allocation->parent_transaction_id,
                    'allocated_at' => $allocatedAt,
                ],
                'created_at' => $allocatedAt,
                'updated_at' => $allocatedAt,
            ]);
            $logCount++;
        }

        // Specialist Payouts
        $specialistPayments = SpecialistPayment::take(40)->get();
        foreach ($specialistPayments as $payment) {
            $processedAt = $payment->paid_at ?? $this->getRandomDate('-30 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'process_payout',
                'details' => [
                    'specialist_id' => $payment->specialist_id,
                    'specialist_name' => $payment->specialist->name,
                    'specialist_email' => $payment->specialist->email,
                    'amount' => $payment->amount,
                    'platform_fee' => $payment->platform_fee,
                    'final_amount' => $payment->final_amount,
                    'month' => (int) substr($payment->month_year, 0, 2),
                    'year' => (int) substr($payment->month_year, 3, 4),
                    'processed_at' => $processedAt,
                ],
                'created_at' => $processedAt,
                'updated_at' => $processedAt,
            ]);
            $logCount++;
        }

        // ========== 4. Report Exports ==========
        $this->command->info('  - Creating Report Export Logs...');

        $reportActions = [
            'export_users_report',
            'export_sessions_report',
            'export_financial_report',
            'export_specialists_report',
            'export_points_report',
            'export_tests_report'
        ];

        for ($i = 0; $i < 60; $i++) {
            $action = $reportActions[array_rand($reportActions)];
            $exportedAt = $this->getRandomDate('-60 days', 'now');

            $details = [
                'exported_at' => $exportedAt,
                'exported_by' => $admin->name,
            ];

            if ($action === 'export_users_report') {
                $details['filters'] = ['status' => 'all', 'donor' => 'all'];
                $details['total_records'] = rand(50, 750);
            } elseif ($action === 'export_sessions_report') {
                $details['filters'] = ['date_from' => '2026-01-01', 'date_to' => '2026-03-31'];
                $details['total_records'] = rand(100, 3000);
            } elseif ($action === 'export_financial_report') {
                $details['date_from'] = $this->faker->date('Y-m-d');
                $details['date_to'] = $this->faker->date('Y-m-d');
                $details['total_revenue'] = rand(5000, 50000);
            } elseif ($action === 'export_specialists_report') {
                $details['filters'] = ['is_verified' => 'all'];
                $details['total_records'] = rand(50, 200);
            } elseif ($action === 'export_points_report') {
                $details['total_users'] = rand(100, 750);
                $details['total_points'] = rand(10000, 500000);
            } elseif ($action === 'export_tests_report') {
                $details['test_type'] = ['phq9', 'gad7', 'pcl5'][array_rand(['phq9', 'gad7', 'pcl5'])];
                $details['date_from'] = $this->faker->date('Y-m-d');
                $details['date_to'] = $this->faker->date('Y-m-d');
                $details['total_submissions'] = rand(50, 1000);
            }

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => $action,
                'details' => $details,
                'created_at' => $exportedAt,
                'updated_at' => $exportedAt,
            ]);
            $logCount++;
        }

        // ========== 5. Specialist Management Logs ==========
        $this->command->info('  - Creating Specialist Management Logs...');

        // Update Specialist (40 logs)
        for ($i = 0; $i < 40; $i++) {
            $specialist = $specialistsList->random();
            $updatedAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'update_specialist',
                'details' => [
                    'specialist_id' => $specialist->id,
                    'specialist_name' => $specialist->name,
                    'updated_fields' => ['consultation_fee', 'specialization'],
                    'updated_at' => $updatedAt,
                ],
                'created_at' => $updatedAt,
                'updated_at' => $updatedAt,
            ]);
            $logCount++;
        }

        // Delete Specialist (10 logs)
        for ($i = 0; $i < 10; $i++) {
            $specialist = $specialistsList->random();
            $deletedAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'delete_specialist',
                'details' => [
                    'specialist_id' => $specialist->id,
                    'specialist_name' => $specialist->name,
                    'specialist_email' => $specialist->email,
                    'deleted_at' => $deletedAt,
                ],
                'created_at' => $deletedAt,
                'updated_at' => $deletedAt,
            ]);
            $logCount++;
        }

        // Activate/Suspend Specialist (30 logs)
        for ($i = 0; $i < 30; $i++) {
            $specialist = $specialistsList->random();
            $isActivate = rand(0, 100) < 50;
            $action = $isActivate ? 'activate_specialist' : 'suspend_specialist';
            $actionAt = $this->getRandomDate('-60 days', 'now');

            $details = [
                'specialist_id' => $specialist->id,
                'specialist_name' => $specialist->name,
                ($isActivate ? 'activated_at' : 'suspended_at') => $actionAt,
            ];

            if (!$isActivate) {
                $details['reason'] = 'مخالفة شروط الاستخدام';
            }

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => $action,
                'details' => $details,
                'created_at' => $actionAt,
                'updated_at' => $actionAt,
            ]);
            $logCount++;
        }

        // Send Email to Specialist (25 logs)
        for ($i = 0; $i < 25; $i++) {
            $specialist = $specialistsList->random();
            $sentAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'send_email_to_specialist',
                'details' => [
                    'specialist_id' => $specialist->id,
                    'specialist_name' => $specialist->name,
                    'subject' => 'تحديث مهم بخصوص حسابك',
                    'sent_at' => $sentAt,
                ],
                'created_at' => $sentAt,
                'updated_at' => $sentAt,
            ]);
            $logCount++;
        }

        // ========== 6. User Management Logs ==========
        $this->command->info('  - Creating User Management Logs...');

        // Update User (50 logs)
        for ($i = 0; $i < 50; $i++) {
            $patient = $patientsList->random();
            $updatedAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'update_user',
                'details' => [
                    'user_id' => $patient->id,
                    'user_name' => $patient->name,
                    'updated_fields' => ['phone', 'is_active'],
                    'updated_at' => $updatedAt,
                ],
                'created_at' => $updatedAt,
                'updated_at' => $updatedAt,
            ]);
            $logCount++;
        }

        // Delete User (15 logs)
        for ($i = 0; $i < 15; $i++) {
            $patient = $patientsList->random();
            $deletedAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'delete_user',
                'details' => [
                    'user_id' => $patient->id,
                    'user_name' => $patient->name,
                    'user_email' => $patient->email,
                    'deleted_at' => $deletedAt,
                ],
                'created_at' => $deletedAt,
                'updated_at' => $deletedAt,
            ]);
            $logCount++;
        }

        // Activate/Suspend User (30 logs)
        for ($i = 0; $i < 30; $i++) {
            $patient = $patientsList->random();
            $isActivate = rand(0, 100) < 50;
            $action = $isActivate ? 'activate_user' : 'suspend_user';
            $actionAt = $this->getRandomDate('-60 days', 'now');

            $details = [
                'user_id' => $patient->id,
                'user_name' => $patient->name,
                ($isActivate ? 'activated_at' : 'suspended_at') => $actionAt,
            ];

            if (!$isActivate) {
                $details['reason'] = 'شكاوى من مختصين';
            }

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => $action,
                'details' => $details,
                'created_at' => $actionAt,
                'updated_at' => $actionAt,
            ]);
            $logCount++;
        }

        // Impersonate/Stop Impersonate (20 logs)
        for ($i = 0; $i < 20; $i++) {
            $patient = $patientsList->random();
            $impersonatedAt = $this->getRandomDate('-60 days', 'now');
            $stoppedAt = Carbon::parse($impersonatedAt)->addMinutes(rand(5, 60));

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'impersonate_user',
                'details' => [
                    'user_id' => $patient->id,
                    'user_name' => $patient->name,
                    'admin_id' => $admin->id,
                    'admin_name' => $admin->name,
                    'impersonated_at' => $impersonatedAt,
                ],
                'created_at' => $impersonatedAt,
                'updated_at' => $impersonatedAt,
            ]);
            $logCount++;

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'stop_impersonate',
                'details' => [
                    'admin_id' => $admin->id,
                    'admin_name' => $admin->name,
                    'user_id' => $patient->id,
                    'user_name' => $patient->name,
                    'stopped_at' => $stoppedAt,
                ],
                'created_at' => $stoppedAt,
                'updated_at' => $stoppedAt,
            ]);
            $logCount++;
        }

        // ========== 7. System Logs (Delete, Bulk Delete, Clear by Date) ==========
        $this->command->info('  - Creating System Maintenance Logs...');

        // Delete single log (10 logs)
        for ($i = 0; $i < 10; $i++) {
            $deletedAt = $this->getRandomDate('-60 days', 'now');

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'delete_log',
                'details' => [
                    'deleted_log_id' => rand(100, 500),
                    'deleted_log_action' => 'old_log_action',
                    'deleted_at' => $deletedAt,
                ],
                'created_at' => $deletedAt,
                'updated_at' => $deletedAt,
            ]);
            $logCount++;
        }

        // Bulk delete logs (5 logs)
        for ($i = 0; $i < 5; $i++) {
            $deletedAt = $this->getRandomDate('-60 days', 'now');
            $deletedCount = rand(10, 100);

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'bulk_delete_logs',
                'details' => [
                    'deleted_count' => $deletedCount,
                    'deleted_log_ids' => range(rand(1, 100), rand(101, 200)),
                    'deleted_at' => $deletedAt,
                ],
                'created_at' => $deletedAt,
                'updated_at' => $deletedAt,
            ]);
            $logCount++;
        }

        // Clear logs by date (3 logs)
        for ($i = 0; $i < 3; $i++) {
            $clearedAt = $this->getRandomDate('-60 days', 'now');
            $dateFrom = Carbon::parse($clearedAt)->subDays(30)->format('Y-m-d');
            $dateTo = Carbon::parse($clearedAt)->format('Y-m-d');
            $deletedCount = rand(50, 300);

            SystemLog::create([
                'admin_id' => $admin->id,
                'action' => 'clear_logs_by_date',
                'details' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'deleted_count' => $deletedCount,
                    'cleared_at' => $clearedAt,
                ],
                'created_at' => $clearedAt,
                'updated_at' => $clearedAt,
            ]);
            $logCount++;
        }

        $this->command->info("  - System Logs Completed: {$logCount} logs");

        // ==================== STEP 15: Create Notifications ====================
        $this->command->info('Step 15: Creating 5000 Notifications...');

        $notificationTypes = ['session_reminder', 'points_earned', 'new_message', 'mood_reminder', 'treatment_task', 'payment', 'donation'];

        for ($i = 0; $i < 5000; $i++) {
            $user = $patients->random();
            $type = $notificationTypes[array_rand($notificationTypes)];

            Notification::create([
                'user_id' => $user->id,
                'title' => $this->getNotificationTitle($type),
                'message' => $this->getNotificationMessage($type),
                'type' => $type,
                'is_read' => rand(0, 100) < 50,
                'sent_at' => $this->getRandomDate('-30 days', 'now'),
            ]);

            if (($i + 1) % 1000 == 0) {
                $this->command->info("      Created " . ($i + 1) . " notifications...");
            }
        }
        $this->command->info("  - Notifications Completed: 5000");

        // ==================== STEP 16: Create Rewards ====================
        $this->command->info('');
        $this->command->info('Step 16: Creating Reward System Data...');

        $rewards = [
            [
                'name' => json_encode(['en' => '$2 Credit', 'ar' => 'رصيد 2 دولار']),
                'points_needed' => 100,
                'type' => 'credit',
                'session_type' => null,
                'credit_amount' => 2.00,
                'description' => json_encode(['en' => 'Get $2 credit added to your account balance', 'ar' => 'احصل على رصيد 2 دولار مضاف إلى حسابك']),
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => json_encode(['en' => '$4 Credit', 'ar' => 'رصيد 4 دولار']),
                'points_needed' => 200,
                'type' => 'credit',
                'session_type' => null,
                'credit_amount' => 4.00,
                'description' => json_encode(['en' => 'Get $4 credit added to your account balance', 'ar' => 'احصل على رصيد 4 دولار مضاف إلى حسابك']),
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => json_encode(['en' => '$10 Credit', 'ar' => 'رصيد 10 دولار']),
                'points_needed' => 500,
                'type' => 'credit',
                'session_type' => null,
                'credit_amount' => 10.00,
                'description' => json_encode(['en' => 'Get $10 credit added to your account balance', 'ar' => 'احصل على رصيد 10 دولار مضاف إلى حسابك']),
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => json_encode(['en' => '$20 Credit', 'ar' => 'رصيد 20 دولار']),
                'points_needed' => 1000,
                'type' => 'credit',
                'session_type' => null,
                'credit_amount' => 20.00,
                'description' => json_encode(['en' => 'Get $20 credit added to your account balance', 'ar' => 'احصل على رصيد 20 دولار مضاف إلى حسابك']),
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => json_encode(['en' => 'Donate Points', 'ar' => 'تبرع بالنقاط']),
                'points_needed' => 10,
                'type' => 'donate',
                'session_type' => null,
                'credit_amount' => null,
                'description' => json_encode(['en' => 'Donate your points to help patients in need', 'ar' => 'تبرع بنقاطك لمساعدة المرضى المحتاجين']),
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => json_encode(['en' => 'Free Text Session', 'ar' => 'جلسة نصية مجانية']),
                'points_needed' => 2000,
                'type' => 'free_session',
                'session_type' => 'text',
                'credit_amount' => null,
                'description' => json_encode(['en' => 'Get one free text chat session', 'ar' => 'احصل على جلسة دردشة نصية مجانية']),
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => json_encode(['en' => 'Free Audio Session', 'ar' => 'جلسة صوتية مجانية']),
                'points_needed' => 3000,
                'type' => 'free_session',
                'session_type' => 'audio',
                'credit_amount' => null,
                'description' => json_encode(['en' => 'Get one free audio call session', 'ar' => 'احصل على جلسة صوتية مجانية']),
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => json_encode(['en' => 'Free Video Session', 'ar' => 'جلسة فيديو مجانية']),
                'points_needed' => 4000,
                'type' => 'free_session',
                'session_type' => 'video',
                'credit_amount' => null,
                'description' => json_encode(['en' => 'Get one free video call session', 'ar' => 'احصل على جلسة فيديو مجانية']),
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($rewards as $rewardData) {
            Reward::create($rewardData);
        }
        $this->command->info('  - Rewards Created: 8');

        // ==================== STEP 17: Create Reward Redemptions ====================
        $this->command->info('  - Creating sample reward redemptions...');

        $redemptionCount = 0;
        $redemptionStatuses = ['pending', 'completed', 'cancelled', 'failed'];

        foreach ($patients as $index => $patient) {
            // Only create redemptions for some patients (30% chance)
            if ($index % 3 !== 0) {
                continue;
            }

            $reward = Reward::inRandomOrder()->first();
            if (!$reward)
                continue;

            $status = $redemptionStatuses[array_rand($redemptionStatuses)];
            $pointsSpent = $reward->points_needed;

            // Ensure user has enough points
            if ($patient->total_points < $pointsSpent && $status === 'completed') {
                $status = 'pending';
            }

            $redeemedAt = $this->getRandomDate('-30 days', 'now');
            $completedAt = $status === 'completed' ? $this->getRandomDate('-29 days', 'now') : null;

            $redemption = RewardRedemption::create([
                'user_id' => $patient->id,
                'reward_id' => $reward->id,
                'points_spent' => $pointsSpent,
                'status' => $status,
                'notes' => $status === 'completed' ? 'تم استلام المكافأة بنجاح' : ($status === 'cancelled' ? 'تم إلغاء الطلب' : null),
                'metadata' => $reward->type === 'donate' ? json_encode(['donated_at' => $redeemedAt]) : null,
                'redeemed_at' => $redeemedAt,
                'completed_at' => $completedAt,
            ]);
            $redemptionCount++;

            // If completed, deduct points from user
            if ($status === 'completed') {
                $patient->total_points -= $pointsSpent;
                $patient->save();

                // If credit reward, add to credit balance
                if ($reward->type === 'credit' && $reward->credit_amount) {
                    $patient->credit_balance += $reward->credit_amount;
                    $patient->save();

                    // Create point transaction for redemption
                    PointTransaction::create([
                        'user_id' => $patient->id,
                        'points' => -$pointsSpent,
                        'type' => 'redeemed',
                        'source' => 'reward_' . $reward->type,
                        'description' => 'استبدال نقاط بـ ' . json_decode($reward->name, true)['ar'],
                        'reference_id' => $redemption->id,
                        'reference_type' => RewardRedemption::class,
                        'created_at' => $redeemedAt,
                    ]);
                }

                // If free session, mark a future session as free
                if ($reward->type === 'free_session') {
                    $freeSession = TherapySession::where('patient_id', $patient->id)
                        ->where('status', 'scheduled')
                        ->where('session_type', $reward->session_type)
                        ->inRandomOrder()
                        ->first();

                    if ($freeSession) {
                        $freeSession->is_free = true;
                        $freeSession->reward_redemption_id = $redemption->id;
                        $freeSession->save();
                    }
                }
            }
        }

        $this->command->info("  - Reward Redemptions Created: {$redemptionCount}");

        // ==================== STEP 18: Create Specialist Payments ====================
        $this->command->info('');
        $this->command->info('Step 18: Creating Specialist Payments...');

        $specialistPaymentsCount = 0;
        $paymentStatuses = ['pending', 'paid', 'failed'];
        $months = [1, 2, 3, 4, 5, 6];
        $currentYear = date('Y');

        foreach ($specialists as $specialist) {
            // Each specialist gets 0-3 payout records
            $numPayments = rand(0, 3);

            for ($i = 0; $i < $numPayments; $i++) {
                // Calculate actual earnings from completed sessions for that month
                $monthIndex = array_rand($months);
                $month = $months[$monthIndex];
                $monthYear = sprintf('%02d/%d', $month, $currentYear);

                $startDate = Carbon::create($currentYear, $month, 1)->startOfMonth();
                $endDate = Carbon::create($currentYear, $month, 1)->endOfMonth();

                $fee = $specialist->specialistProfile->consultation_fee;

                $videoSessions = TherapySession::where('specialist_id', $specialist->id)
                    ->where('status', 'completed')
                    ->where('session_type', 'video')
                    ->whereBetween('session_datetime', [$startDate, $endDate])
                    ->count();

                $audioSessions = TherapySession::where('specialist_id', $specialist->id)
                    ->where('status', 'completed')
                    ->where('session_type', 'audio')
                    ->whereBetween('session_datetime', [$startDate, $endDate])
                    ->count();

                $textSessions = TherapySession::where('specialist_id', $specialist->id)
                    ->where('status', 'completed')
                    ->where('session_type', 'text')
                    ->whereBetween('session_datetime', [$startDate, $endDate])
                    ->count();

                $earnings = ($videoSessions * $fee) + ($audioSessions * $fee * 0.9) + ($textSessions * $fee * 0.8);

                if ($earnings == 0) {
                    continue;
                }

                $platformFee = $earnings * 0.1; // 10% platform fee
                $finalAmount = $earnings - $platformFee;
                $status = $paymentStatuses[array_rand($paymentStatuses)];
                $paidAt = $status === 'paid' ? $this->getRandomDate('-15 days', 'now') : null;

                SpecialistPayment::create([
                    'specialist_id' => $specialist->id,
                    'amount' => $earnings,
                    'month_year' => $monthYear,
                    'platform_fee' => $platformFee,
                    'final_amount' => $finalAmount,
                    'status' => $status,
                    'notes' => $status === 'paid' ? 'تم تحويل المبلغ بنجاح' : ($status === 'failed' ? 'فشل عملية التحويل، يرجى المحاولة مرة أخرى' : 'قيد المراجعة'),
                    'paid_at' => $paidAt,
                ]);

                $specialistPaymentsCount++;

                // If paid, create a notification for the specialist
                if ($status === 'paid') {
                    Notification::create([
                        'user_id' => $specialist->id,
                        'title' => __('تم تحويل مستحقاتك'),
                        'message' => __('تم تحويل مبلغ $:amount إلى حسابك عن شهر :month', [
                            'amount' => number_format($finalAmount, 2),
                            'month' => Carbon::create($currentYear, $month, 1)->translatedFormat('F Y')
                        ]),
                        'type' => 'payment',
                        'is_read' => false,
                        'sent_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info("  - Specialist Payments Created: {$specialistPaymentsCount}");

        // ==================== STEP 19: Create Notifications ====================
        $this->command->info('');
        $this->command->info('Step 19: Creating 5000 Notifications...');

        $notificationTypes = [
            'session_reminder' => [
                'title' => 'تذكير بجلسة',
                'message' => 'لديك جلسة بعد ساعة مع المختص. يرجى الاستعداد.'
            ],
            'points_earned' => [
                'title' => 'نقاط مكتسبة',
                'message' => 'لقد حصلت على {points} نقاط جديدة! استمر في رحلة صحتك النفسية.'
            ],
            'new_message' => [
                'title' => 'رسالة جديدة',
                'message' => 'لديك رسالة جديدة من {sender}. يرجى الاطلاع عليها.'
            ],
            'mood_reminder' => [
                'title' => 'تذكر تتبع مزاجك',
                'message' => 'لم تسجل حالتك المزاجية اليوم. خصص دقيقة لتسجيلها.'
            ],
            'treatment_task' => [
                'title' => 'مهمة علاجية جديدة',
                'message' => 'تم إضافة مهمة جديدة "{task}" لخطتك العلاجية. يرجى إكمالها.'
            ],
            'payment' => [
                'title' => 'إشعار دفع',
                'message' => 'تم معالجة دفعتك بقيمة ${amount} بنجاح.'
            ],
            'donation' => [
                'title' => 'إشعار تبرع',
                'message' => 'شكراً لتبرعك بقيمة ${amount}! مساهمتك تحدث فرقاً.'
            ],
        ];

        $notificationCount = 0;

        // Get all users (patients and specialists)
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['patient', 'specialist']);
        })->get();

        $this->command->info('  - Target users: ' . $users->count());

        for ($i = 0; $i < 5000; $i++) {
            $user = $users->random();
            $type = array_rand($notificationTypes);
            $template = $notificationTypes[$type];

            $title = $template['title'];
            $message = $template['message'];

            // Customize message based on type
            if ($type === 'points_earned') {
                $points = rand(5, 50);
                $message = str_replace('{points}', (string) $points, $message);
            } elseif ($type === 'new_message') {
                $randomUser = $users->where('id', '!=', $user->id)->random();
                $message = str_replace('{sender}', $randomUser->name, $message);
            } elseif ($type === 'treatment_task') {
                $tasks = ['تمارين التنفس', 'كتابة اليوميات', 'التأمل', 'ممارسة الرياضة'];
                $message = str_replace('{task}', $tasks[array_rand($tasks)], $message);
            } elseif ($type === 'payment' || $type === 'donation') {
                $amount = rand(10, 500);
                $message = str_replace('{amount}', (string) $amount, $message);
            }

            // Random date within last 60 days
            $sentAt = $this->getRandomDate('-60 days', 'now');

            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => rand(0, 100) < 40, // 40% chance of being unread
                'sent_at' => $sentAt,
                'created_at' => $sentAt,
                'updated_at' => $sentAt,
            ]);

            $notificationCount++;

            if (($i + 1) % 1000 == 0) {
                $this->command->info("      Created " . ($i + 1) . " notifications...");
            }
        }

        $this->command->info("  - Notifications Completed: {$notificationCount}");

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ==================== FINAL SUMMARY ====================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Database Seeding Completed Successfully!');
        $this->command->info('========================================');
        $this->command->info('Summary:');
        $this->command->info('  - Users: 1,001 (Admin: 1, Specialists: 200, Patients: 750, Donors: 50)');
        $this->command->info('  - Specialist Profiles: 200');
        $this->command->info('  - Donor Profiles: 50');
        $this->command->info('  - Availability Slots: ~' . $availabilityCount);
        $this->command->info('  - Therapy Sessions: 3,000');
        $this->command->info('  - Mood Logs: 20,000');
        $this->command->info('  - Test Results: 10,000');
        $this->command->info('  - Point Transactions: 15,000');
        $this->command->info('  - Treatment Plans: 1,500');
        $this->command->info('  - Treatment Tasks: ~' . $taskCount);
        $this->command->info('  - Messages: 8,000');
        $this->command->info('  - Reviews: ' . $reviewCount);
        $this->command->info('  - Credit Transactions: 1,500 (with types)');
        $this->command->info('  - Content Items: 200');
        $this->command->info('  - System Logs: 500');
        $this->command->info('  - Notifications: 5,000');
        $this->command->info('  - Rewards: 8');
        $this->command->info('  - Reward Redemptions: ' . $redemptionCount);
        $this->command->info('  - Specialist Payments: ' . $specialistPaymentsCount);
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('  Admin: admin@tamman.ps / admin123');
        $this->command->info('  Specialist: specialist_X@tamman.ps / password123');
        $this->command->info('  Patient: patient_X@tamman.ps / password123');
        $this->command->info('========================================');
    }

    // ==================== HELPER METHODS ====================

    private function getRandomArabicName($role): string
    {
        $firstNamesMale = ['أحمد', 'محمد', 'علي', 'حسن', 'حسين', 'عمر', 'عثمان', 'خالد', 'ياسر', 'سامر', 'محمود', 'إبراهيم', 'يوسف', 'مصطفى', 'كريم'];
        $firstNamesFemale = ['فاطمة', 'عائشة', 'نور', 'ليلى', 'سارة', 'ريم', 'هدى', 'أمل', 'منى', 'دينا', 'شهد', 'مريم', 'آية', 'جنى', 'تالا'];
        $lastNames = ['البربري', 'أبو زينة', 'المصري', 'الحلو', 'العمري', 'السقا', 'الدحدوح', 'أبو ندى', 'الرشيد', 'حمدان'];

        $firstName = $role === 'patient'
            ? (rand(0, 1) ? $firstNamesMale[array_rand($firstNamesMale)] : $firstNamesFemale[array_rand($firstNamesFemale)])
            : $firstNamesMale[array_rand($firstNamesMale)];

        return $firstName . ' ' . $lastNames[array_rand($lastNames)];
    }

    private function getRandomOrganizationName(): string
    {
        $orgs = [
            'مؤسسة الخير للتنمية',
            'الهلال الأحمر الفلسطيني',
            'الأونروا',
            'بنك الطعام الفلسطيني',
            'مؤسسة التعاون',
            'قطر الخيرية'
        ];
        return $orgs[array_rand($orgs)];
    }

    private function getRandomQualifications(): string
    {
        $quals = [
            'دكتوراه في علم النفس السريري - الجامعة الإسلامية بغزة (2015)',
            'ماجستير في العلاج النفسي - جامعة الأزهر (2018)',
            'بكالوريوس في علم النفس - الجامعة الإسلامية (2010)',
            'دبلوم عالي في العلاج السلوكي المعرفي - المركز العربي للتدريب النفسي (2019)',
            'دكتوراه في العلاج النفسي - جامعة بيرزيت (2017)',
            'ماجستير في علم نفس الطفل - جامعة الأزهر بغزة (2016)',
        ];
        return $quals[array_rand($quals)];
    }

    private function getRandomBio(): string
    {
        $bios = [
            'أخصائي نفسي سريري بخبرة تزيد عن 10 سنوات في علاج اضطرابات القلق والاكتئاب والصدمات النفسية. أتبنى نهجاً تكاملياً يجمع بين العلاج السلوكي المعرفي والعلاج المرتكز على الحلول.',
            'معالج نفسي متخصص في علاج الصدمات النفسية لدى البالغين والأطفال. عملت مع منظمات دولية في غزة لسنوات عديدة، وأجيد التعامل مع حالات ما بعد الحرب والنزوح.',
            'أخصائي في علم نفس الطفل والمراهق. أساعد الأطفال والشباب على التعامل مع التحديات العاطفية والسلوكية، بما في ذلك القلق المدرسي، التنمر، واضطرابات التعلق.',
            'معالج أسري متخصص في حل النزاعات الأسرية وتحسين التواصل بين أفراد الأسرة. أعمل مع الأسر التي تعاني من صعوبات في التكيف مع الضغوط الحياتية والنزوح.',
            'طبيب نفسي متخصص في العلاج الدوائي والتدخلات النفسية الاجتماعية. لدي خبرة في علاج الاضطرابات النفسية الحادة والمزمنة.',
            'اخصائية نفسية إكلينيكية بخبرة 8 سنوات في علاج النساء المعنفات وضحايا العنف الأسري. أقدم الدعم النفسي والتمكين للنساء والفتيات.',
        ];
        return $bios[array_rand($bios)];
    }

    private function getRandomSessionDate(string $status): string
    {
        if ($status === 'completed') {
            return $this->getRandomDate('-60 days', '-1 day');
        } elseif ($status === 'scheduled') {
            return $this->getRandomDate('+1 day', '+30 days');
        } else {
            return $this->getRandomDate('-30 days', '+30 days');
        }
    }

    private function getRandomDate($start, $end): string
    {
        $timestamp = rand(strtotime($start), strtotime($end));
        return date('Y-m-d H:i:s', $timestamp);
    }

    private function getRandomSessionNote(): string
    {
        $notes = [
            'تقدم ملحوظ في التعامل مع القلق. استمر في تمارين التنفس.',
            'تم مناقشة استراتيجيات التأقلم مع التوتر. وافق المريض على تطبيقها.',
            'تحسن في المزاج مقارنة بالجلسة السابقة. استمرارية العلاج إيجابية.',
            'تم تعيين مهام منزلية جديدة للمريض. سيقوم بتطبيقها قبل الجلسة القادمة.',
            'تحدثنا عن الصدمة السابقة. المريض متعاون ومستعد للمعالجة.',
        ];
        return $notes[array_rand($notes)];
    }

    private function getRandomMoodNote(): string
    {
        $notes = [
            'شعرت بالتوتر بسبب الامتحانات اليوم',
            'كان يوماً جميلاً مع العائلة، تحسن المزاج',
            'واجهت صعوبة في النوم الليلة الماضية',
            'مارست الرياضة وشعرت بتحسن كبير',
            'تذكرت ذكريات جميلة مع الأصدقاء',
        ];
        return $notes[array_rand($notes)];
    }

    private function getRandomAnswers(string $testType): array
    {
        $questionCounts = [
            'phq9' => 9,
            'gad7' => 7,
            'pcl5' => 20,
            'isi' => 7,
            'pss' => 10,
            'cis' => 13,
        ];

        $count = $questionCounts[$testType] ?? 10;
        $answers = [];
        for ($i = 1; $i <= $count; $i++) {
            $answers["q{$i}"] = rand(0, 3);
        }
        return $answers;
    }

    private function getRandomPlanDescription(): string
    {
        $descs = [
            'خطة شاملة لعلاج الاكتئاب تشمل جلسات أسبوعية وتمارين يومية لمدة 8 أسابيع',
            'برنامج مدته 6 أسابيع لتحسين الصحة النفسية العامة باستخدام تقنيات اليقظة الذهنية',
            'خطة متخصصة لإدارة التوتر تتضمن تمارين التنفس وتقنيات الاسترخاء',
            'برنامج علاجي للتعافي من الصدمة باستخدام تقنيات العلاج السلوكي المعرفي',
            'خطة تحسين النوم تتضمن تعديل العادات اليومية وتمارين الاسترخاء قبل النوم',
        ];
        return $descs[array_rand($descs)];
    }

    private function getRandomTaskTitle(): string
    {
        $titles = ['تمارين التنفس', 'كتابة اليوميات', 'ممارسة الرياضة', 'التأمل', 'تحديد الأفكار السلبية', 'تمارين الاسترخاء'];
        return $titles[array_rand($titles)];
    }

    private function getRandomTaskDescription(): string
    {
        $descs = [
            'قم بتمارين التنفس العميق لمدة 5 دقائق، كررها 3 مرات يومياً',
            'اكتب 3 أشياء تشعر بالامتنان لها اليوم',
            'مارس أي نوع من الرياضة لمدة 30 دقيقة (مشي، جري، تمارين منزلية)',
            'جرب تمارين التأمل لمدة 10 دقائق في مكان هادئ',
            'لاحظ الأفكار السلبية وتحداها بأفكار إيجابية بديلة',
        ];
        return $descs[array_rand($descs)];
    }

    private function getPositiveReview(): string
    {
        $reviews = [
            'مختص رائع ومتفهم، ساعدني كثيراً في تخطي صعوباتي النفسية. أنصح به بشدة',
            'أفضل مختص تعاملت معه، جلسات ممتازة ومفيدة جداً. شكراً لك',
            'تجربة رائعة، أشعر بتحسن كبير بعد كل جلسة. مختص محترف ومتعاون',
            'متمكن جداً في مجاله، يشرح بطريقة مبسطة وواضحة. أشكر منصة طمأن',
        ];
        return $reviews[array_rand($reviews)];
    }

    private function getNegativeReview(): string
    {
        $reviews = [
            'تجربة سيئة، لم أستفد من الجلسات كما توقعت',
            'مختص غير مناسب لحالتي، لم أشعر بالارتياح',
            'لم تكن التجربة كما توقعت، أتمنى تحسين التواصل',
        ];
        return $reviews[array_rand($reviews)];
    }

    private function getNeutralReview(): string
    {
        $reviews = [
            'مختص جيد لكن ليس ممتازاً، تجربة عادية',
            'تجربة متوسطة، قد أجرب مختصاً آخر في المستقبل',
            'جيد في بعض النقاط وضعيف في أخرى، تجربة مقبولة',
        ];
        return $reviews[array_rand($reviews)];
    }

    private function getContentByType(string $type, array $youtubeUrls): array
    {
        $articles = [
            ['title' => 'كيف تتعامل مع القلق في أوقات الضغط', 'body' => '<h2>ما هو القلق؟</h2><p>القلق هو استجابة طبيعية للتوتر...</p><h2>طرق التعامل مع القلق</h2><ul><li>تمارين التنفس العميق</li><li>ممارسة الرياضة بانتظام</li><li>الحصول على قسط كاف من النوم</li></ul>', 'media_url' => null],
            ['title' => 'فهم الاكتئاب وطرق علاجه', 'body' => '<h2>أعراض الاكتئاب</h2><p>الحزن المستمر، فقدان الاهتمام، تغيرات في النوم والشهية...</p><h2>خيارات العلاج</h2><p>العلاج النفسي، الدعم الاجتماعي، وفي بعض الحالات الأدوية...</p>', 'media_url' => null],
            ['title' => 'التعامل مع الصدمات النفسية بعد الحرب', 'body' => '<h2>ما هي الصدمة النفسية؟</h2><p>الصدمة النفسية هي رد فعل عاطفي لحدث مؤلم...</p><h2>كيف تتعامل مع الصدمة؟</h2><ul><li>تحدث مع شخص تثق به</li><li>مارس تمارين الاسترخاء</li><li>حافظ على روتين يومي</li></ul>', 'media_url' => null],
        ];

        $videos = [
            ['title' => 'تمارين التنفس العميق للتخلص من التوتر', 'body' => 'تعلم كيفية ممارسة تمارين التنفس العميق للتخلص من التوتر والقلق. هذا التمرين البسيط يمكنك القيام به في أي مكان.', 'media_url' => $youtubeUrls[array_rand($youtubeUrls)]],
            ['title' => 'جلسة تأمل موجهة للاسترخاء - 10 دقائق', 'body' => 'جلسة تأمل موجهة باللغة العربية للمبتدئين. خصص 10 دقائق للاسترخاء وتهدئة العقل.', 'media_url' => $youtubeUrls[array_rand($youtubeUrls)]],
        ];

        $tips = [
            ['title' => 'نصيحة اليوم للصحة النفسية', 'body' => 'خذ 5 دقائق اليوم للتنفس العميق والاسترخاء. تنفس لمدة 4 ثوانٍ، احبس النفس لـ 7 ثوانٍ، ثم أخرج الزفير ببطء لـ 8 ثوانٍ.', 'media_url' => null],
            ['title' => 'نصيحة لتحسين المزاج', 'body' => 'مارس الامتنان يومياً. اكتب 3 أشياء تشعر بالامتنان لها، مهما كانت صغيرة.', 'media_url' => null],
        ];

        $guides = [
            ['title' => 'دليل التعامل مع نوبات الهلع', 'body' => '<h2>ما هي نوبة الهلع؟</h2><p>نوبة الهلع هي شعور مفاجئ بخوف شديد...</p><h2>ماذا تفعل أثناء نوبة الهلع؟</h2><ul><li>ذكر نفسك أن النوبة ستمر</li><li>ركز على تنفسك</li><li>استخدم حواسك الخمس</li></ul>', 'media_url' => null],
            ['title' => 'دليل الرعاية الذاتية للصحة النفسية', 'body' => '<h2>أركان الرعاية الذاتية</h2><ul><li>الرعاية الجسدية: النوم، التغذية، الرياضة</li><li>الرعاية العاطفية: التعبير عن المشاعر، طلب الدعم</li><li>الرعاية الاجتماعية: قضاء وقت مع الأحبة</li></ul>', 'media_url' => null],
        ];

        switch ($type) {
            case 'article':
                return $articles[array_rand($articles)];
            case 'video':
                return $videos[array_rand($videos)];
            case 'tip':
                return $tips[array_rand($tips)];
            default:
                return $guides[array_rand($guides)];
        }
    }

    private function getNotificationTitle(string $type): string
    {
        $titles = [
            'session_reminder' => 'تذكير بجلسة',
            'points_earned' => 'نقاط مكتسبة',
            'new_message' => 'رسالة جديدة',
            'mood_reminder' => 'تذكر تتبع مزاجك',
            'treatment_task' => 'مهمة علاجية جديدة',
            'payment' => 'إشعار دفع',
            'donation' => 'إشعار تبرع',
        ];
        return $titles[$type] ?? 'إشعار جديد';
    }

    private function getNotificationMessage(string $type): string
    {
        $messages = [
            'session_reminder' => 'لديك جلسة بعد ساعة مع المختص. يرجى الاستعداد.',
            'points_earned' => 'لقد حصلت على نقاط جديدة! استمر في رحلة صحتك النفسية.',
            'new_message' => 'لديك رسالة جديدة من المختص. يرجى الاطلاع عليها.',
            'mood_reminder' => 'لم تسجل حالتك المزاجية اليوم. خصص دقيقة لتسجيلها.',
            'treatment_task' => 'تم إضافة مهمة جديدة لخطتك العلاجية. يرجى إكمالها.',
            'payment' => 'تم معالجة دفعتك بنجاح.',
            'donation' => 'شكراً لتبرعك! مساهمتك تحدث فرقاً.',
        ];
        return $messages[$type] ?? 'لديك إشعار جديد على منصة طمأن';
    }
}
