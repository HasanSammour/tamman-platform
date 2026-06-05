<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;

class SpecialistController extends Controller
{
    /**
     * Display a listing of specialists
     */
    public function index(Request $request)
    {
        $query = User::whereHas('roles', function($q) {
                $q->where('name', 'specialist');
            })
            ->whereHas('specialistProfile', function($q) {
                $q->where('is_verified', true)
                  ->where('application_status', 'approved'); // Only approved specialists
            })
            ->with('specialistProfile');

        // Apply filters - Search in both Arabic and English
        if ($request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('specialistProfile', function($sq) use ($searchTerm) {
                      $sq->where('specialization', 'like', '%' . $searchTerm . '%')
                        ->orWhere('bio', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Specialization filter
        if ($request->specialization) {
            $selectedSpec = $request->specialization;
            
            $specializationMap = [
                'Clinical Psychology' => ['علم النفس السريري', 'Clinical Psychology'],
                'علم النفس السريري' => ['علم النفس السريري', 'Clinical Psychology'],
                'Counseling Psychology' => ['علم النفس الإرشادي', 'Counseling Psychology'],
                'علم النفس الإرشادي' => ['علم النفس الإرشادي', 'Counseling Psychology'],
                'Psychiatry' => ['الطب النفسي', 'Psychiatry'],
                'الطب النفسي' => ['الطب النفسي', 'Psychiatry'],
                'CBT Therapy' => ['العلاج السلوكي المعرفي', 'CBT Therapy'],
                'العلاج السلوكي المعرفي' => ['العلاج السلوكي المعرفي', 'CBT Therapy'],
                'Trauma Therapy' => ['علاج الصدمات', 'Trauma Therapy'],
                'علاج الصدمات' => ['علاج الصدمات', 'Trauma Therapy'],
                'Family Therapy' => ['العلاج الأسري', 'Family Therapy'],
                'العلاج الأسري' => ['العلاج الأسري', 'Family Therapy'],
                'Child Psychology' => ['علم نفس الطفل', 'Child Psychology'],
                'علم نفس الطفل' => ['علم نفس الطفل', 'Child Psychology'],
            ];
            
            $searchValues = $specializationMap[$selectedSpec] ?? [$selectedSpec];
            
            $query->whereHas('specialistProfile', function($q) use ($searchValues) {
                $q->where(function($sq) use ($searchValues) {
                    foreach ($searchValues as $value) {
                        $sq->orWhere('specialization', 'like', '%' . $value . '%');
                    }
                });
            });
        }

        // Language filter
        if ($request->language) {
            $selectedLang = $request->language;
            
            $languageMap = [
                'Arabic' => ['العربية', 'Arabic'],
                'العربية' => ['العربية', 'Arabic'],
                'English' => ['الإنجليزية', 'English'],
                'الإنجليزية' => ['الإنجليزية', 'English'],
                'French' => ['الفرنسية', 'French'],
                'الفرنسية' => ['الفرنسية', 'French'],
                'German' => ['الألمانية', 'German'],
                'الألمانية' => ['الألمانية', 'German'],
                'Spanish' => ['الإسبانية', 'Spanish'],
                'الإسبانية' => ['الإسبانية', 'Spanish'],
                'Hebrew' => ['العبرية', 'Hebrew'],
                'العبرية' => ['العبرية', 'Hebrew'],
            ];
            
            $searchValues = $languageMap[$selectedLang] ?? [$selectedLang];
            
            $query->whereHas('specialistProfile', function($q) use ($searchValues) {
                $q->where(function($sq) use ($searchValues) {
                    foreach ($searchValues as $value) {
                        $sq->orWhere('languages', 'like', '%' . $value . '%');
                    }
                });
            });
        }

        if ($request->min_price) {
            $query->whereHas('specialistProfile', function($q) use ($request) {
                $q->where('consultation_fee', '>=', $request->min_price);
            });
        }

        if ($request->max_price) {
            $query->whereHas('specialistProfile', function($q) use ($request) {
                $q->where('consultation_fee', '<=', $request->max_price);
            });
        }

        if ($request->rating) {
            $query->whereHas('specialistProfile', function($q) use ($request) {
                $q->where('rating_avg', '>=', $request->rating);
            });
        }

        $specialists = $query->orderBy('created_at', 'desc')->paginate(9)->withQueryString();;

        $specializations = [
            'Clinical Psychology',
            'Counseling Psychology',
            'Psychiatry',
            'CBT Therapy',
            'Trauma Therapy',
            'Family Therapy',
            'Child Psychology'
        ];

        $languages = [
            'Arabic',
            'English',
            'French',
            'German',
            'Spanish',
            'Hebrew'
        ];

        if ($request->ajax()) {
            $html = view('specialist.partials.specialists_grid', compact('specialists'))->render();
            $pagination = $specialists->links()->render();
            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
                'total' => $specialists->total()
            ]);
        }

        return view('specialist.index', compact('specialists', 'specializations', 'languages'));
    }

    /**
     * Display a specific specialist profile
     */
    public function show($id)
    {
        $specialist = User::whereHas('roles', function($q) {
                $q->where('name', 'specialist');
            })
            ->whereHas('specialistProfile', function($q) {
                $q->where('is_verified', true)
                  ->where('application_status', 'approved'); // Only approved specialists
            })
            ->with(['specialistProfile'])
            ->findOrFail($id);

        $profile = $specialist->specialistProfile;

        $reviews = Review::where('specialist_id', $specialist->id)
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->get();

        $ratingDistribution = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];

        $totalReviews = $reviews->count();

        return view('specialist.show', compact('specialist', 'profile', 'reviews', 'ratingDistribution', 'totalReviews'));
    }
}