<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TherapySession;
use App\Models\Content;
use Illuminate\Http\Request;
use App\Models\PointTransaction;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Show the home page (welcome page)
     */
    public function index()
    {
        // Get featured specialists - ONLY verified AND approved, ordered by rating (top 6)
        // Fix: Use join with specialist_profiles to order by rating_avg
        $featuredSpecialists = User::whereHas('roles', function ($query) {
            $query->where('name', 'specialist');
        })
            ->whereHas('specialistProfile', function ($query) {
                $query->where('is_verified', true)
                    ->where('application_status', 'approved');
            })
            ->with('specialistProfile')
            ->join('specialist_profiles', 'users.id', '=', 'specialist_profiles.user_id')
            ->orderBy('specialist_profiles.rating_avg', 'desc')
            ->select('users.*')  // Select only users columns to avoid ambiguity
            ->take(6)
            ->get();

        // Get statistics for the home page
        $stats = [
            'total_users' => User::whereHas('roles', function ($query) {
                $query->where('name', 'patient');
            })->count(),
            'total_specialists' => User::whereHas('roles', function ($query) {
                $query->where('name', 'specialist');
            })->whereHas('specialistProfile', function ($query) {
                $query->where('is_verified', true)
                    ->where('application_status', 'approved');
            })->count(),
            'total_sessions' => TherapySession::where('status', 'completed')->count(),
            'total_points_awarded' => PointTransaction::where('type', 'earned')->sum('points'),
        ];

        // Get recent published content for resources section
        $recentResources = Content::where('is_published', true)
            ->where('type', 'article')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // Get recent testimonials (from completed sessions with reviews)
        $testimonials = TherapySession::where('status', 'completed')
            ->whereHas('review')
            ->with(['patient', 'review'])
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        // Get authenticated user info for conditional rendering
        $isAuthenticated = auth()->check();
        $userRole = null;
        $userPoints = null;

        if ($isAuthenticated) {
            $user = auth()->user();
            if ($user->hasRole('admin')) {
                $userRole = 'admin';
            } elseif ($user->hasRole('specialist')) {
                $userRole = 'specialist';
            } else {
                $userRole = 'patient';
                $userPoints = $user->total_points;
            }
        }

        return view('welcome', compact('featuredSpecialists', 'stats', 'recentResources', 'testimonials', 'isAuthenticated', 'userRole', 'userPoints'));
    }

    /**
     * Show how it works page
     */
    public function howItWorks()
    {
        // Get platform statistics
        $stats = [
            'total_users' => User::whereHas('roles', function ($query) {
                $query->where('name', 'patient');
            })->count(),
            'total_specialists' => User::whereHas('roles', function ($query) {
                $query->where('name', 'specialist');
            })->whereHas('specialistProfile', function ($query) {
                $query->where('is_verified', true)
                    ->where('application_status', 'approved');
            })->count(),
            'total_sessions' => TherapySession::where('status', 'completed')->count(),
            'satisfaction_rate' => 98,
            'avg_response_time' => '< 24',
        ];

        // Get step statistics (optional - for dynamic content)
        $stepStats = [
            'step1' => User::whereHas('roles', function ($query) {
                $query->where('name', 'patient');
            })->count(),
            'step2' => User::whereHas('roles', function ($query) {
                $query->where('name', 'specialist');
            })->whereHas('specialistProfile', function ($query) {
                $query->where('is_verified', true)
                    ->where('application_status', 'approved');
            })->count(),
            'step3' => TherapySession::where('status', 'scheduled')->count(),
            'step4' => TherapySession::where('status', 'completed')->count(),
        ];

        // Get FAQ items
        $faqs = [
            [
                'question' => 'Is Tamman free to use?',
                'answer' => 'Tamman offers both free and paid options. Basic mood tracking and resources are free. Therapy sessions have affordable rates, and financial aid is available for those who need it through our donor support system.'
            ],
            [
                'question' => 'Are my sessions private and confidential?',
                'answer' => 'Yes! All sessions are completely private and confidential. We use end-to-end encryption for all communications, and your data is never shared with third parties.'
            ],
            [
                'question' => 'How do I choose the right specialist?',
                'answer' => 'You can filter specialists by specialization, language, gender, price, and availability. Each specialist has a detailed profile with their qualifications, experience, and patient reviews.'
            ],
            [
                'question' => 'What if I can\'t afford therapy?',
                'answer' => 'We have a donor support system that provides financial assistance to those in need. You can apply for support, and if approved, you\'ll receive credits for subsidized sessions.'
            ],
            [
                'question' => 'Can I switch specialists?',
                'answer' => 'Absolutely! You can switch specialists at any time. Your session history and treatment plans can be transferred with your consent.'
            ],
            [
                'question' => 'What is the Tamman Points system?',
                'answer' => 'Tamman Points is our reward system. You earn points by tracking your mood, attending sessions, and completing activities. Points can be redeemed for session discounts and other benefits.'
            ]
        ];

        $isAuthenticated = auth()->check();
        $userRole = null;

        if ($isAuthenticated) {
            $user = auth()->user();
            if ($user->hasRole('admin')) {
                $userRole = 'admin';
            } elseif ($user->hasRole('specialist')) {
                $userRole = 'specialist';
            } else {
                $userRole = 'patient';
            }
        }

        return view('how-it-works', compact('stats', 'stepStats', 'faqs', 'isAuthenticated', 'userRole'));
    }

    /**
     * Show about us page
     */
    public function about()
    {
        // Get team members (admins and verified specialists)
        $teamMembers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'specialist']);
        })
            ->whereHas('specialistProfile', function ($query) {
                $query->where('is_verified', true)
                    ->where('application_status', 'approved');
            })
            ->with('specialistProfile')
            ->take(6)
            ->get();

        // Get platform statistics
        $stats = [
            'total_users' => User::whereHas('roles', function ($query) {
                $query->where('name', 'patient');
            })->count(),
            'total_specialists' => User::whereHas('roles', function ($query) {
                $query->where('name', 'specialist');
            })->whereHas('specialistProfile', function ($query) {
                $query->where('is_verified', true)
                    ->where('application_status', 'approved');
            })->count(),
            'total_sessions' => TherapySession::where('status', 'completed')->count(),
            'satisfaction_rate' => 98,
            'countries_served' => 1,
            'years_experience' => 2,
        ];

        // Get platform milestones
        $milestones = [
            [
                'year' => 2025,
                'title' => 'Platform Launch',
                'description' => 'Tamman was founded to provide accessible mental health support to the Gaza community.',
                'icon' => 'fas fa-rocket'
            ],
            [
                'year' => 2025,
                'title' => 'First 1000 Users',
                'description' => 'Reached 1000 registered users within the first 3 months.',
                'icon' => 'fas fa-users'
            ],
            [
                'year' => 2026,
                'title' => 'Specialist Network Grows',
                'description' => 'Expanded to 200+ verified mental health specialists.',
                'icon' => 'fas fa-user-md'
            ],
            [
                'year' => 2026,
                'title' => '10,000 Sessions',
                'description' => 'Completed over 10,000 successful therapy sessions.',
                'icon' => 'fas fa-calendar-check'
            ]
        ];

        // Get core values
        $coreValues = [
            [
                'title' => 'Privacy First',
                'description' => 'Your data is encrypted and never shared. Complete confidentiality guaranteed.',
                'icon' => 'fas fa-shield-alt'
            ],
            [
                'title' => 'Stigma-Free',
                'description' => 'A safe space where everyone can seek help without fear of judgment.',
                'icon' => 'fas fa-heart'
            ],
            [
                'title' => 'Accessible Care',
                'description' => 'Affordable therapy with financial support for those in need.',
                'icon' => 'fas fa-hand-holding-heart'
            ],
            [
                'title' => 'Quality Guaranteed',
                'description' => 'All specialists are licensed, verified, and experienced professionals.',
                'icon' => 'fas fa-certificate'
            ]
        ];

        $isAuthenticated = auth()->check();
        $userRole = null;

        if ($isAuthenticated) {
            $user = auth()->user();
            if ($user->hasRole('admin')) {
                $userRole = 'admin';
            } elseif ($user->hasRole('specialist')) {
                $userRole = 'specialist';
            } else {
                $userRole = 'patient';
            }
        }

        return view('about', compact('teamMembers', 'stats', 'milestones', 'coreValues', 'isAuthenticated', 'userRole'));
    }

    /**
     * Show contact form
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Send contact message via AJAX
     */
    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        try {
            Mail::to('hasansammour01@gmail.com')->send(new ContactMail($data));

            return response()->json([
                'success' => true,
                'message' => __('Your message has been sent successfully! We will reply within 24 hours.')
            ]);
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('An error occurred while sending the message. Please try again.')
            ], 500);
        }
    }

    /**
     * Show Help Center page
     */
    public function helpCenter()
    {
        // FAQ Categories and Questions
        $faqs = [
            'general' => [
                'title' => __('General Questions'),
                'icon' => 'fas fa-question-circle',
                'questions' => [
                    [
                        'question' => __('What is Tamman?'),
                        'answer' => __('Tamman is a secure digital mental health platform that connects individuals with licensed mental health professionals through online video, audio, and text sessions. We provide a private, stigma-free environment for mental health support.')
                    ],
                    [
                        'question' => __('Is Tamman free to use?'),
                        'answer' => __('Tamman offers both free and paid options. Basic features like mood tracking and educational resources are free. Therapy sessions have affordable rates, and financial aid is available through our donor support system for those who need it.')
                    ],
                    [
                        'question' => __('Is my information private and secure?'),
                        'answer' => __('Yes! We take your privacy very seriously. All communications are end-to-end encrypted, and your personal information is never shared with third parties. Our platform is HIPAA-compliant and follows strict data protection standards.')
                    ],
                    [
                        'question' => __('How do I get started?'),
                        'answer' => __('Simply create a free account, complete your profile, browse our network of licensed specialists, and book your first session. You can also start with our free mood tracking and self-assessment tools.')
                    ],
                ]
            ],
            'account' => [
                'title' => __('Account & Profile'),
                'icon' => 'fas fa-user-circle',
                'questions' => [
                    [
                        'question' => __('How do I create an account?'),
                        'answer' => __('Click the "Sign Up" button on the homepage, fill in your basic information (name, email, password), and verify your email address. The process takes less than 2 minutes.')
                    ],
                    [
                        'question' => __('How do I reset my password?'),
                        'answer' => __('Click "Forgot Password" on the login page, enter your email address, and we\'ll send you a password reset link. Follow the instructions to create a new password.')
                    ],
                    [
                        'question' => __('Can I delete my account?'),
                        'answer' => __('Yes, you can delete your account from your profile settings. Go to Profile → Danger Zone → Delete Account. Please note that this action is permanent and cannot be undone.')
                    ],
                    [
                        'question' => __('How do I update my profile information?'),
                        'answer' => __('Log in to your account, go to Profile from the sidebar menu, and click the "Edit" button next to each section. You can update your personal information, upload a profile picture, and manage notification preferences.')
                    ],
                ]
            ],
            'sessions' => [
                'title' => __('Sessions & Booking'),
                'icon' => 'fas fa-calendar-check',
                'questions' => [
                    [
                        'question' => __('How do I book a session?'),
                        'answer' => __('Browse our specialists list, choose a specialist that fits your needs, select a date and time from their available slots, choose your session type (video/audio/text), and confirm your booking.')
                    ],
                    [
                        'question' => __('What types of sessions are available?'),
                        'answer' => __('We offer three types of sessions: Video (face-to-face video call), Audio (voice-only call), and Text Chat (real-time messaging). Each session is 60 minutes long.')
                    ],
                    [
                        'question' => __('How do I join my session?'),
                        'answer' => __('For video and audio sessions, you\'ll receive a meeting link in your booking confirmation email and in your dashboard. Click the link at the scheduled time. For text sessions, go to the Messages section.')
                    ],
                    [
                        'question' => __('Can I reschedule or cancel a session?'),
                        'answer' => __('Yes, you can cancel or reschedule your session from the "My Sessions" page. Please note that cancellations must be made at least 24 hours in advance to avoid charges.')
                    ],
                ]
            ],
            'payments' => [
                'title' => __('Payments & Points'),
                'icon' => 'fas fa-credit-card',
                'questions' => [
                    [
                        'question' => __('How do I pay for sessions?'),
                        'answer' => __('You can pay using credit balance (top up your account), Tamman Points (earned through activities), or cash/bank transfer. Payment methods are available at checkout.')
                    ],
                    [
                        'question' => __('What are Tamman Points?'),
                        'answer' => __('Tamman Points are our reward system. You earn points by tracking your mood daily, attending sessions, completing assessments, and finishing treatment tasks. Points can be redeemed for session discounts.')
                    ],
                    [
                        'question' => __('How do I earn more points?'),
                        'answer' => __('Track your mood daily (+5 points), attend sessions (+50 points), complete psychological assessments (+25 points), finish treatment tasks (+15 points), rate specialists (+3 points), and refer friends (+100 points).')
                    ],
                    [
                        'question' => __('What is the donor support system?'),
                        'answer' => __('Our donor support system allows generous individuals and organizations to donate funds that help subsidize sessions for users who cannot afford therapy. If you need financial assistance, you can apply for donor credits.')
                    ],
                ]
            ],
            'technical' => [
                'title' => __('Technical Support'),
                'icon' => 'fas fa-laptop-code',
                'questions' => [
                    [
                        'question' => __('What devices can I use?'),
                        'answer' => __('Tamman works on any device with a modern browser: desktop computers, laptops, tablets, and smartphones. We recommend using Chrome, Firefox, or Safari for the best experience.')
                    ],
                    [
                        'question' => __('Do I need to install any software?'),
                        'answer' => __('No software installation is required. Tamman works entirely in your web browser. For video sessions, make sure your browser has permission to access your camera and microphone.')
                    ],
                    [
                        'question' => __('What should I do if I have connection issues?'),
                        'answer' => __('Check your internet connection, close other tabs and applications, refresh your browser, or try switching to audio-only mode if video is unstable. You can also contact our support team for assistance.')
                    ],
                    [
                        'question' => __('How do I contact support?'),
                        'answer' => __('You can reach our support team via the Contact Us page, email us at support@tamman.ps, or call our helpline at +970 8 123 4567. We\'re available Saturday to Thursday, 9 AM to 5 PM.')
                    ],
                ]
            ],
        ];

        // Quick guides
        $quickGuides = [
            [
                'title' => __('Getting Started Guide'),
                'description' => __('Learn how to create your account and start your mental health journey.'),
                'icon' => 'fas fa-rocket',
                'color' => '#7c3aed',
                'link' => '#'
            ],
            [
                'title' => __('How to Book a Session'),
                'description' => __('Step-by-step guide to finding and booking the right specialist for you.'),
                'icon' => 'fas fa-calendar-plus',
                'color' => '#10b981',
                'link' => '#'
            ],
            [
                'title' => __('Using Tamman Points'),
                'description' => __('Everything you need to know about earning and redeeming points.'),
                'icon' => 'fas fa-star',
                'color' => '#f59e0b',
                'link' => '#'
            ],
            [
                'title' => __('Privacy & Security'),
                'description' => __('Learn how we protect your data and ensure your privacy.'),
                'icon' => 'fas fa-shield-alt',
                'color' => '#ef4444',
                'link' => '#'
            ],
        ];

        // Video tutorials
        $videoTutorials = [
            [
                'title' => __('How to Create an Account'),
                'duration' => '2:15',
                'thumbnail' => asset('images/video-thumb-1.jpg'),
                'link' => '#'
            ],
            [
                'title' => __('Booking Your First Session'),
                'duration' => '3:30',
                'thumbnail' => asset('images/video-thumb-2.jpg'),
                'link' => '#'
            ],
            [
                'title' => __('Navigating Your Dashboard'),
                'duration' => '4:00',
                'thumbnail' => asset('images/video-thumb-3.jpg'),
                'link' => '#'
            ],
        ];

        return view('help-center', compact('faqs', 'quickGuides', 'videoTutorials'));
    }
}