<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_type',
        'score',
        'result_level',
        'answers',
        'test_date'
    ];

    protected $casts = [
        'test_date' => 'date',
        'answers' => 'array',
        'score' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper
    // Test type constants (6 tests only)
    const TEST_PHQ9 = 'phq9';
    const TEST_GAD7 = 'gad7';
    const TEST_PCL5 = 'pcl5';
    const TEST_ISI = 'isi';
    const TEST_PSS = 'pss';
    const TEST_CIS = 'cis';

    // Get all test types (6 tests only)
    public static function getTestTypes()
    {
        return [
            self::TEST_PHQ9 => 'PHQ-9 (Depression)',
            self::TEST_GAD7 => 'GAD-7 (Anxiety)',
            self::TEST_PCL5 => 'PCL-5 (PTSD)',
            self::TEST_ISI => 'ISI (Insomnia)',
            self::TEST_PSS => 'PSS (Perceived Stress)',
            self::TEST_CIS => 'CIS (Functional Impairment)',
        ];
    }

    // Get test information with scoring ranges (6 tests only)
    public static function getTestInfo($testType)
    {
        $tests = [
            self::TEST_PHQ9 => [
                'name' => 'PHQ-9',
                'full_name' => 'Patient Health Questionnaire-9',
                'full_name_ar' => 'استبيان صحة المريض-9',
                'description' => 'Measures depression severity',
                'description_ar' => 'يقيس شدة الاكتئاب',
                'questions' => 9,
                'time_minutes' => 3,
                'scoring' => [
                    ['min' => 0, 'max' => 4, 'level' => 'minimal', 'level_ar' => 'بسيط'],
                    ['min' => 5, 'max' => 9, 'level' => 'mild', 'level_ar' => 'خفيف'],
                    ['min' => 10, 'max' => 14, 'level' => 'moderate', 'level_ar' => 'متوسط'],
                    ['min' => 15, 'max' => 19, 'level' => 'moderately_severe', 'level_ar' => 'شديد متوسط'],
                    ['min' => 20, 'max' => 27, 'level' => 'severe', 'level_ar' => 'شديد'],
                ]
            ],
            self::TEST_GAD7 => [
                'name' => 'GAD-7',
                'full_name' => 'Generalized Anxiety Disorder-7',
                'full_name_ar' => 'اضطراب القلق العام-7',
                'description' => 'Measures anxiety severity',
                'description_ar' => 'يقيس شدة القلق',
                'questions' => 7,
                'time_minutes' => 2,
                'scoring' => [
                    ['min' => 0, 'max' => 4, 'level' => 'minimal', 'level_ar' => 'بسيط'],
                    ['min' => 5, 'max' => 9, 'level' => 'mild', 'level_ar' => 'خفيف'],
                    ['min' => 10, 'max' => 14, 'level' => 'moderate', 'level_ar' => 'متوسط'],
                    ['min' => 15, 'max' => 21, 'level' => 'severe', 'level_ar' => 'شديد'],
                ]
            ],
            self::TEST_PCL5 => [
                'name' => 'PCL-5',
                'full_name' => 'PTSD Checklist for DSM-5',
                'full_name_ar' => 'قائمة اضطراب ما بعد الصدمة',
                'description' => 'Measures PTSD symptoms',
                'description_ar' => 'يقيس أعراض اضطراب ما بعد الصدمة',
                'questions' => 20,
                'time_minutes' => 8,
                'scoring' => [
                    ['min' => 0, 'max' => 30, 'level' => 'minimal', 'level_ar' => 'بسيط'],
                    ['min' => 31, 'max' => 33, 'level' => 'mild', 'level_ar' => 'خفيف - يحتاج متابعة'],
                    ['min' => 34, 'max' => 80, 'level' => 'moderate', 'level_ar' => 'متوسط - يحتاج تدخل'],
                ]
            ],
            self::TEST_ISI => [
                'name' => 'ISI',
                'full_name' => 'Insomnia Severity Index',
                'full_name_ar' => 'مؤشر شدة الأرق',
                'description' => 'Assesses insomnia and sleep problems',
                'description_ar' => 'يقيم الأرق ومشاكل النوم',
                'questions' => 7,
                'time_minutes' => 3,
                'scoring' => [
                    ['min' => 0, 'max' => 7, 'level' => 'none', 'level_ar' => 'لا يوجد أرق سريري'],
                    ['min' => 8, 'max' => 14, 'level' => 'subthreshold', 'level_ar' => 'أرق تحت العتبة'],
                    ['min' => 15, 'max' => 21, 'level' => 'moderate', 'level_ar' => 'أرق متوسط'],
                    ['min' => 22, 'max' => 28, 'level' => 'severe', 'level_ar' => 'أرق شديد'],
                ]
            ],
            self::TEST_PSS => [
                'name' => 'PSS',
                'full_name' => 'Perceived Stress Scale',
                'full_name_ar' => 'مقياس الإجهاد المحسوس',
                'description' => 'Measures perception of stress',
                'description_ar' => 'يقيس إدراك الإجهاد',
                'questions' => 10,
                'time_minutes' => 4,
                'scoring' => [
                    ['min' => 0, 'max' => 13, 'level' => 'low', 'level_ar' => 'إجهاد منخفض'],
                    ['min' => 14, 'max' => 26, 'level' => 'moderate', 'level_ar' => 'إجهاد متوسط'],
                    ['min' => 27, 'max' => 40, 'level' => 'high', 'level_ar' => 'إجهاد مرتفع'],
                ]
            ],
            self::TEST_CIS => [
                'name' => 'CIS',
                'full_name' => 'Columbia Impairment Scale',
                'full_name_ar' => 'مقياس كولومبيا للضعف الوظيفي',
                'description' => 'Measures functional impairment in daily life',
                'description_ar' => 'يقيس الضعف الوظيفي في الحياة اليومية',
                'questions' => 13,
                'time_minutes' => 5,
                'scoring' => [
                    ['min' => 0, 'max' => 15, 'level' => 'minimal', 'level_ar' => 'ضعف بسيط'],
                    ['min' => 16, 'max' => 52, 'level' => 'moderate', 'level_ar' => 'ضعف ملحوظ - يحتاج تدخل'],
                ]
            ],
        ];

        return $tests[$testType] ?? null;
    }

    // Get result level in Arabic
    public function getResultLevelArAttribute()
    {
        $levels = [
            'minimal' => 'بسيط',
            'mild' => 'خفيف',
            'moderate' => 'متوسط',
            'moderately_severe' => 'شديد متوسط',
            'severe' => 'شديد',
            'none' => 'لا يوجد',
            'subthreshold' => 'تحت العتبة',
            'low' => 'منخفض',
            'high' => 'مرتفع',
        ];

        return $levels[$this->result_level] ?? $this->result_level;
    }
}
