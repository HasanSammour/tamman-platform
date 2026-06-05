<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\TestResult;
use App\Models\PointTransaction;
use App\Models\Notification;
use App\Helpers\TestHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TestController extends Controller
{
    /**
     * Display all available tests
     */
    public function index()
    {
        $user = Auth::user();
        $tests = TestHelper::getAllTests();
        $testsData = [];

        foreach ($tests as $key => $test) {
            $lastResult = TestResult::where('user_id', $user->id)
                ->where('test_type', $key)
                ->latest('test_date')
                ->first();

            $canTake = $user->canTakeTest($key);
            $nextAvailable = $user->getNextTestAvailableDate($key);

            $testsData[$key] = [
                'info' => $test,
                'last_result' => $lastResult,
                'can_take' => $canTake,
                'next_available' => $nextAvailable,
                'has_taken' => !is_null($lastResult),
            ];
        }

        // Fix: Count unique test types taken this month (max 6)
        $testsThisMonth = TestResult::where('user_id', $user->id)
            ->whereYear('test_date', Carbon::now()->year)
            ->whereMonth('test_date', Carbon::now()->month)
            ->distinct('test_type')
            ->count('test_type');

        $stats = [
            'total_tests' => TestResult::where('user_id', $user->id)->count(),
            'total_points_earned' => PointTransaction::where('user_id', $user->id)
                ->where('source', 'test_completed')
                ->sum('points'),
            'tests_this_month' => $testsThisMonth, // Now max is 6
            'completed_tests' => TestResult::where('user_id', $user->id)
                ->select('test_type')
                ->distinct()
                ->count('test_type'),
        ];

        return view('patient.tests.index', compact('testsData', 'stats'));
    }

    /**
     * Take a specific test
     */
    public function take($testType)
    {
        $user = Auth::user();

        if (!$user->canTakeTest($testType)) {
            return redirect()->route('patient.tests')
                ->with('error', __('You have already taken this test this month. Please come back next month.'));
        }

        $testInfo = TestHelper::getTestInfo($testType);
        if (!$testInfo) {
            return redirect()->route('patient.tests')->with('error', __('Test not found.'));
        }

        $questions = TestHelper::getQuestions($testType);
        $options = TestHelper::getOptions($testType);

        return view('patient.tests.take', compact('testType', 'testInfo', 'questions', 'options'));
    }

    /**
     * Submit test answers via AJAX
     */
    public function submit(Request $request, $testType)
    {
        try {
            Log::info('Test submission started', ['test_type' => $testType, 'user_id' => Auth::id()]);

            $user = Auth::user();

            if (!$user->canTakeTest($testType)) {
                return response()->json([
                    'success' => false,
                    'message' => __('You have already taken this test this month. Please come back next month.')
                ], 422);
            }

            $testInfo = TestHelper::getTestInfo($testType);
            if (!$testInfo) {
                return response()->json([
                    'success' => false,
                    'message' => __('Test not found.')
                ], 404);
            }

            $questions = TestHelper::getQuestions($testType);
            $answers = [];

            foreach ($questions as $question) {
                $answerKey = 'q_' . $question['id'];
                if (!$request->has($answerKey)) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Please answer all questions.')
                    ], 422);
                }
                $answers[$question['id']] = (int) $request->input($answerKey);
            }

            $result = TestHelper::calculateResult($testType, $answers);

            $testResult = TestResult::create([
                'user_id' => $user->id,
                'test_type' => $testType,
                'score' => $result['score'],
                'result_level' => $result['result_level'],
                'answers' => json_encode($answers),
                'test_date' => Carbon::today(),
            ]);

            $points = 10;
            $user->addPoints(
                $points,
                'test_completed',
                __('Completed :test assessment', ['test' => $testInfo['name']]),
                $testResult->id,
                TestResult::class
            );

            // Only create notification if user wants points earned notifications
            if ($user->wantsNotification('points_earned')) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => __('Test Completed! 📋'),
                    'message' => __('You completed the :test assessment and earned :points points!', [
                        'test' => $testInfo['name'],
                        'points' => $points
                    ]),
                    'type' => 'points_earned',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }

            Log::info('Test submitted successfully', ['test_result_id' => $testResult->id]);

            return response()->json([
                'success' => true,
                'message' => __('Test submitted successfully! You earned :points points.', ['points' => $points]),
                'result_id' => $testResult->id,
                'score' => $result['score'],
                'level' => $result['result_level'],
                'points_earned' => $points,
            ]);
        } catch (\Exception $e) {
            Log::error('Test submission error: ' . $e->getMessage(), [
                'test_type' => $testType,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Something went wrong. Please try again.')
            ], 500);
        }
    }

    /**
     * Show test results
     */
    public function results($id)
    {
        $user = Auth::user();
        $testResult = TestResult::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $testInfo = TestHelper::getTestInfo($testResult->test_type);
        $questions = TestHelper::getQuestions($testResult->test_type);
        $options = TestHelper::getOptions($testResult->test_type);
        $ranges = TestHelper::getScoringRanges($testResult->test_type);
        $interpretation = TestHelper::getInterpretation($testResult->test_type, $testResult->result_level);

        $previousResults = TestResult::where('user_id', $user->id)
            ->where('test_type', $testResult->test_type)
            ->where('id', '!=', $id)
            ->orderBy('test_date', 'desc')
            ->take(5)
            ->get();

        return view('patient.tests.results', compact(
            'testResult',
            'testInfo',
            'questions',
            'options',
            'ranges',
            'interpretation',
            'previousResults'
        ));
    }

    /**
     * Show test history with AJAX support
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = TestResult::where('user_id', $user->id);

        if ($request->has('test_type') && $request->test_type != 'all') {
            $query->where('test_type', $request->test_type);
        }

        $testResults = $query->orderBy('test_date', 'desc')
            ->paginate(15);

        $testTypes = TestHelper::getAllTests();

        // If AJAX request, return only the table and pagination
        if ($request->ajax()) {
            $html = view('patient.tests.partials.history_table', compact('testResults'))->render();
            $pagination = $testResults->links()->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination,
            ]);
        }

        return view('patient.tests.history', compact('testResults', 'testTypes'));
    }

    /**
     * Get test details via AJAX
     */
    public function getTestDetails($testType)
    {
        $user = Auth::user();
        $testInfo = TestHelper::getTestInfo($testType);

        if (!$testInfo) {
            return response()->json(['success' => false], 404);
        }

        $lastResult = TestResult::where('user_id', $user->id)
            ->where('test_type', $testType)
            ->latest('test_date')
            ->first();

        $canTake = $user->canTakeTest($testType);
        $nextAvailable = $user->getNextTestAvailableDate($testType);

        return response()->json([
            'success' => true,
            'test' => $testInfo,
            'last_result' => $lastResult,
            'can_take' => $canTake,
            'next_available' => $nextAvailable->translatedFormat('M d, Y'),
        ]);
    }
}