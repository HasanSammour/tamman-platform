<?php

namespace App\Helpers;

class TestHelper
{
    // Test types
    const TEST_PHQ9 = 'phq9';
    const TEST_GAD7 = 'gad7';
    const TEST_PCL5 = 'pcl5';
    const TEST_ISI = 'isi';
    const TEST_PSS = 'pss';
    const TEST_CIS = 'cis';

    /**
     * Get all test types
     */
    public static function getAllTests()
    {
        return [
            self::TEST_PHQ9 => [
                'name' => 'PHQ-9',
                'full_name' => 'Patient Health Questionnaire-9',
                'full_name_ar' => 'استبيان صحة المريض-9',
                'description' => 'Measures depression severity over the last 2 weeks',
                'description_ar' => 'يقيس شدة الاكتئاب خلال الأسبوعين الماضيين',
                'questions_count' => 9,
                'time_minutes' => 3,
                'icon' => 'fas fa-heartbeat',
                'color' => '#7c3aed',
                'bg' => '#ede9fe'
            ],
            self::TEST_GAD7 => [
                'name' => 'GAD-7',
                'full_name' => 'Generalized Anxiety Disorder-7',
                'full_name_ar' => 'اضطراب القلق العام-7',
                'description' => 'Measures anxiety severity over the last 2 weeks',
                'description_ar' => 'يقيس شدة القلق خلال الأسبوعين الماضيين',
                'questions_count' => 7,
                'time_minutes' => 2,
                'icon' => 'fas fa-brain',
                'color' => '#10b981',
                'bg' => '#d1fae5'
            ],
            self::TEST_PCL5 => [
                'name' => 'PCL-5',
                'full_name' => 'PTSD Checklist for DSM-5',
                'full_name_ar' => 'قائمة اضطراب ما بعد الصدمة',
                'description' => 'Measures PTSD symptoms over the last month',
                'description_ar' => 'يقيس أعراض اضطراب ما بعد الصدمة خلال الشهر الماضي',
                'questions_count' => 20,
                'time_minutes' => 8,
                'icon' => 'fas fa-shield-alt',
                'color' => '#f59e0b',
                'bg' => '#fef3c7'
            ],
            self::TEST_ISI => [
                'name' => 'ISI',
                'full_name' => 'Insomnia Severity Index',
                'full_name_ar' => 'مؤشر شدة الأرق',
                'description' => 'Assesses insomnia and sleep problems',
                'description_ar' => 'يقيم الأرق ومشاكل النوم',
                'questions_count' => 7,
                'time_minutes' => 3,
                'icon' => 'fas fa-moon',
                'color' => '#ef4444',
                'bg' => '#fee2e2'
            ],
            self::TEST_PSS => [
                'name' => 'PSS',
                'full_name' => 'Perceived Stress Scale',
                'full_name_ar' => 'مقياس الإجهاد المحسوس',
                'description' => 'Measures perception of stress over the last month',
                'description_ar' => 'يقيس إدراك الإجهاد خلال الشهر الماضي',
                'questions_count' => 10,
                'time_minutes' => 4,
                'icon' => 'fas fa-tachometer-alt',
                'color' => '#ec4899',
                'bg' => '#fce7f3'
            ],
            self::TEST_CIS => [
                'name' => 'CIS',
                'full_name' => 'Columbia Impairment Scale',
                'full_name_ar' => 'مقياس كولومبيا للضعف الوظيفي',
                'description' => 'Measures functional impairment in daily life',
                'description_ar' => 'يقيس الضعف الوظيفي في الحياة اليومية',
                'questions_count' => 13,
                'time_minutes' => 5,
                'icon' => 'fas fa-chart-bar',
                'color' => '#06b6d4',
                'bg' => '#cffafe'
            ],
        ];
    }

    /**
     * Get single test info
     */
    public static function getTestInfo($testType)
    {
        $tests = self::getAllTests();
        return $tests[$testType] ?? null;
    }

    /**
     * Get questions for a test
     */
    public static function getQuestions($testType, $locale = 'en')
    {
        $questions = self::getAllQuestions();
        return $questions[$testType] ?? [];
    }

    /**
     * Get questions for result page (with answers)
     */
    public static function getQuestionsWithAnswers($testType, $answers)
    {
        $questions = self::getQuestions($testType);
        $options = self::getOptions($testType);

        foreach ($questions as &$question) {
            $question['user_answer'] = $answers[$question['id']] ?? null;
            $question['answer_text'] = $options[$question['user_answer']] ?? null;
        }

        return $questions;
    }

    /**
     * Get all questions for each test
     */
    private static function getAllQuestions()
    {
        return [
            self::TEST_PHQ9 => [
                ['id' => 1, 'text_en' => 'Little interest or pleasure in doing things', 'text_ar' => 'قلة الاهتمام أو المتعة في القيام بالأشياء'],
                ['id' => 2, 'text_en' => 'Feeling down, depressed, or hopeless', 'text_ar' => 'الشعور بالحزن أو الاكتئاب أو اليأس'],
                ['id' => 3, 'text_en' => 'Trouble falling or staying asleep, or sleeping too much', 'text_ar' => 'صعوبة في النوم أو الاستمرار فيه، أو النوم كثيراً'],
                ['id' => 4, 'text_en' => 'Feeling tired or having little energy', 'text_ar' => 'الشعور بالتعب أو نقص الطاقة'],
                ['id' => 5, 'text_en' => 'Poor appetite or overeating', 'text_ar' => 'ضعف الشهية أو الإفراط في تناول الطعام'],
                ['id' => 6, 'text_en' => 'Feeling bad about yourself - or that you are a failure or have let yourself or your family down', 'text_ar' => 'الشعور بالسوء تجاه نفسك - أو أنك فاشل أو خذلت نفسك أو عائلتك'],
                ['id' => 7, 'text_en' => 'Trouble concentrating on things, such as reading the newspaper or watching television', 'text_ar' => 'صعوبة في التركيز على الأشياء، مثل قراءة الجريدة أو مشاهدة التلفزيون'],
                ['id' => 8, 'text_en' => 'Moving or speaking so slowly that other people could have noticed? Or the opposite - being so fidgety or restless that you have been moving around a lot more than usual', 'text_ar' => 'الحركة أو التحدث ببطء شديد لدرجة أن الآخرين لاحظوا ذلك؟ أو العكس - التململ أو الأرق لدرجة التحرك أكثر من المعتاد'],
                ['id' => 9, 'text_en' => 'Thoughts that you would be better off dead or of hurting yourself in some way', 'text_ar' => 'أفكار بأنك أفضل حالاً لو متّ أو بإيذاء نفسك بطريقة ما'],
            ],
            self::TEST_GAD7 => [
                ['id' => 1, 'text_en' => 'Feeling nervous, anxious, or on edge', 'text_ar' => 'الشعور بالعصبية أو القلق أو التوتر'],
                ['id' => 2, 'text_en' => 'Not being able to stop or control worrying', 'text_ar' => 'عدم القدرة على التوقف عن القلق أو التحكم به'],
                ['id' => 3, 'text_en' => 'Worrying too much about different things', 'text_ar' => 'القلق المفرط بشأن أشياء مختلفة'],
                ['id' => 4, 'text_en' => 'Trouble relaxing', 'text_ar' => 'صعوبة في الاسترخاء'],
                ['id' => 5, 'text_en' => 'Being so restless that it is hard to sit still', 'text_ar' => 'التململ لدرجة صعوبة الجلوس بهدوء'],
                ['id' => 6, 'text_en' => 'Becoming easily annoyed or irritable', 'text_ar' => 'سرعة الانزعاج أو التهيج'],
                ['id' => 7, 'text_en' => 'Feeling afraid as if something awful might happen', 'text_ar' => 'الشعور بالخوف وكأن شيئاً فظيعاً سيحدث'],
            ],
            self::TEST_PCL5 => [
                ['id' => 1, 'text_en' => 'Repeated, disturbing, and unwanted memories of the stressful experience', 'text_ar' => 'ذكريات متكررة ومزعجة وغير مرغوب فيها عن التجربة المجهدة'],
                ['id' => 2, 'text_en' => 'Repeated, disturbing dreams of the stressful experience', 'text_ar' => 'أحلام متكررة ومزعجة عن التجربة المجهدة'],
                ['id' => 3, 'text_en' => 'Suddenly feeling or acting as if the stressful experience were actually happening again', 'text_ar' => 'الشعور أو التصرف فجأة وكأن التجربة المجهدة تحدث مرة أخرى'],
                ['id' => 4, 'text_en' => 'Feeling very upset when something reminded you of the stressful experience', 'text_ar' => 'الشعور بالانزعاج الشديد عندما يذكرك شيء بالتجربة المجهدة'],
                ['id' => 5, 'text_en' => 'Having strong physical reactions when something reminded you of the stressful experience', 'text_ar' => 'ردود فعل جسدية قوية عندما يذكرك شيء بالتجربة المجهدة'],
                ['id' => 6, 'text_en' => 'Avoiding memories, thoughts, or feelings related to the stressful experience', 'text_ar' => 'تجنب الذكريات أو الأفكار أو المشاعر المتعلقة بالتجربة المجهدة'],
                ['id' => 7, 'text_en' => 'Avoiding external reminders of the stressful experience', 'text_ar' => 'تجنب المثيرات الخارجية للتجربة المجهدة'],
                ['id' => 8, 'text_en' => 'Trouble remembering important parts of the stressful experience', 'text_ar' => 'صعوبة في تذكر أجزاء مهمة من التجربة المجهدة'],
                ['id' => 9, 'text_en' => 'Having strong negative beliefs about yourself, other people, or the world', 'text_ar' => 'معتقدات سلبية قوية عن نفسك أو الآخرين أو العالم'],
                ['id' => 10, 'text_en' => 'Blaming yourself or someone else for the stressful experience', 'text_ar' => 'لوم نفسك أو شخص آخر على التجربة المجهدة'],
                ['id' => 11, 'text_en' => 'Having strong negative feelings', 'text_ar' => 'مشاعر سلبية قوية'],
                ['id' => 12, 'text_en' => 'Loss of interest in activities you used to enjoy', 'text_ar' => 'فقدان الاهتمام بالأنشطة التي كنت تستمتع بها'],
                ['id' => 13, 'text_en' => 'Feeling distant or cut off from other people', 'text_ar' => 'الشعور بالبعد أو الانعزال عن الآخرين'],
                ['id' => 14, 'text_en' => 'Trouble experiencing positive feelings', 'text_ar' => 'صعوبة في تجربة المشاعر الإيجابية'],
                ['id' => 15, 'text_en' => 'Irritable behavior, angry outbursts, or acting aggressively', 'text_ar' => 'سلوك سريع الانفعال، نوبات غضب، أو تصرف بعدوانية'],
                ['id' => 16, 'text_en' => 'Taking too many risks or doing things that could cause you harm', 'text_ar' => 'المخاطرة المفرطة أو فعل أشياء قد تسبب لك الضرر'],
                ['id' => 17, 'text_en' => 'Being super alert or watchful on guard', 'text_ar' => 'اليقظة المفرطة أو الحذر الشديد'],
                ['id' => 18, 'text_en' => 'Feeling jumpy or easily startled', 'text_ar' => 'الشعور بالارتباك أو الفزع بسهولة'],
                ['id' => 19, 'text_en' => 'Having difficulty concentrating', 'text_ar' => 'صعوبة في التركيز'],
                ['id' => 20, 'text_en' => 'Trouble falling or staying asleep', 'text_ar' => 'صعوبة في النوم أو الاستمرار فيه'],
            ],
            self::TEST_ISI => [
                ['id' => 1, 'text_en' => 'Difficulty falling asleep', 'text_ar' => 'صعوبة في النوم'],
                ['id' => 2, 'text_en' => 'Difficulty staying asleep', 'text_ar' => 'صعوبة في الاستمرار في النوم'],
                ['id' => 3, 'text_en' => 'Problems waking up too early', 'text_ar' => 'مشاكل الاستيقاظ المبكر'],
                ['id' => 4, 'text_en' => 'How satisfied/dissatisfied are you with your current sleep pattern?', 'text_ar' => 'ما مدى رضاك عن نمط نومك الحالي؟'],
                ['id' => 5, 'text_en' => 'How noticeable to others do you think your sleep problem is?', 'text_ar' => 'ما مدى وضوح مشكلة نومك للآخرين؟'],
                ['id' => 6, 'text_en' => 'How worried/distressed are you about your current sleep problem?', 'text_ar' => 'ما مدى قلقك أو انزعاجك من مشكلة نومك الحالية؟'],
                ['id' => 7, 'text_en' => 'To what extent do you consider your sleep problem interferes with your daily functioning?', 'text_ar' => 'إلى أي مدى تعتقد أن مشكلة نومك تتداخل مع أدائك اليومي؟'],
            ],
            self::TEST_PSS => [
                ['id' => 1, 'text_en' => 'In the last month, how often have you been upset because of something that happened unexpectedly?', 'text_ar' => 'خلال الشهر الماضي، كم مرة شعرت بالانزعاج بسبب شيء حدث بشكل غير متوقع؟'],
                ['id' => 2, 'text_en' => 'In the last month, how often have you felt that you were unable to control the important things in your life?', 'text_ar' => 'خلال الشهر الماضي، كم مرة شعرت أنك غير قادر على التحكم في الأشياء المهمة في حياتك؟'],
                ['id' => 3, 'text_en' => 'In the last month, how often have you felt nervous and stressed?', 'text_ar' => 'خلال الشهر الماضي، كم مرة شعرت بالتوتر والقلق؟'],
                ['id' => 4, 'text_en' => 'In the last month, how often have you felt confident about your ability to handle your personal problems?', 'text_ar' => 'خلال الشهر الماضي، كم مرة شعرت بالثقة في قدرتك على التعامل مع مشاكلك الشخصية؟'],
                ['id' => 5, 'text_en' => 'In the last month, how often have you felt that things were going your way?', 'text_ar' => 'خلال الشهر الماضي، كم مرة شعرت أن الأمور تسير كما تريد؟'],
                ['id' => 6, 'text_en' => 'In the last month, how often have you found that you could not cope with all the things that you had to do?', 'text_ar' => 'خلال الشهر الماضي، كم مرة وجدت أنك لا تستطيع التعامل مع كل الأشياء التي كان عليك القيام بها؟'],
                ['id' => 7, 'text_en' => 'In the last month, how often have you been able to control irritations in your life?', 'text_ar' => 'خلال الشهر الماضي، كم مرة تمكنت من التحكم في حالات الانزعاج في حياتك؟'],
                ['id' => 8, 'text_en' => 'In the last month, how often have you felt that you were on top of things?', 'text_ar' => 'خلال الشهر الماضي، كم مرة شعرت أنك مسيطر على الأمور؟'],
                ['id' => 9, 'text_en' => 'In the last month, how often have you been angered because of things that happened that were outside of your control?', 'text_ar' => 'خلال الشهر الماضي، كم مرة شعرت بالغضب بسبب أشياء حدثت خارجة عن إرادتك؟'],
                ['id' => 10, 'text_en' => 'In the last month, how often have you felt difficulties were piling up so high that you could not overcome them?', 'text_ar' => 'خلال الشهر الماضي، كم مرة شعرت أن الصعوبات تتراكم لدرجة أنك لا تستطيع التغلب عليها؟'],
            ],
            self::TEST_CIS => [
                ['id' => 1, 'text_en' => 'How much trouble do you have getting along with your friends?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في التعامل مع أصدقائك؟'],
                ['id' => 2, 'text_en' => 'How much trouble do you have getting along with your parents?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في التعامل مع والديك؟'],
                ['id' => 3, 'text_en' => 'How much trouble do you have getting along with other adults?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في التعامل مع البالغين الآخرين؟'],
                ['id' => 4, 'text_en' => 'How much trouble do you have getting along with your siblings?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في التعامل مع إخوتك؟'],
                ['id' => 5, 'text_en' => 'How much trouble do you have getting along with your teachers?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في التعامل مع معلميك؟'],
                ['id' => 6, 'text_en' => 'How much trouble do you have with school work?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في أداء واجباتك المدرسية؟'],
                ['id' => 7, 'text_en' => 'How much trouble do you have with your job or chores at home?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في عملك أو مهامك المنزلية؟'],
                ['id' => 8, 'text_en' => 'How much trouble do you have with participating in activities like sports or clubs?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في المشاركة في أنشطة مثل الرياضة أو الأندية؟'],
                ['id' => 9, 'text_en' => 'How much trouble do you have with spending time with friends?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في قضاء الوقت مع الأصدقاء؟'],
                ['id' => 10, 'text_en' => 'How much trouble do you have with spending time with family?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في قضاء الوقت مع العائلة؟'],
                ['id' => 11, 'text_en' => 'How much trouble do you have with feeling happy or having fun?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في الشعور بالسعادة أو المرح؟'],
                ['id' => 12, 'text_en' => 'How much trouble do you have with feeling good about yourself?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في الشعور بالرضا عن نفسك؟'],
                ['id' => 13, 'text_en' => 'How much trouble do you have with sleeping?', 'text_ar' => 'ما مدى الصعوبة التي تواجهها في النوم؟'],
            ],
        ];
    }

    /**
     * Get options for answers
     */
    public static function getOptions($testType = null)
    {
        $options = [
            '0' => ['en' => 'Not at all', 'ar' => 'ليس على الإطلاق'],
            '1' => ['en' => 'Several days', 'ar' => 'عدة أيام'],
            '2' => ['en' => 'More than half the days', 'ar' => 'أكثر من نصف الأيام'],
            '3' => ['en' => 'Nearly every day', 'ar' => 'يومياً تقريباً'],
        ];

        if ($testType === self::TEST_PCL5) {
            $options = [
                '0' => ['en' => 'Not at all', 'ar' => 'أبداً'],
                '1' => ['en' => 'A little bit', 'ar' => 'قليلاً'],
                '2' => ['en' => 'Moderately', 'ar' => 'بشكل معتدل'],
                '3' => ['en' => 'Quite a bit', 'ar' => 'كثيراً'],
                '4' => ['en' => 'Extremely', 'ar' => 'بشدة'],
            ];
        }

        if ($testType === self::TEST_ISI) {
            $options = [
                '0' => ['en' => 'None / Very satisfied', 'ar' => 'لا شيء / راضٍ جداً'],
                '1' => ['en' => 'Mild / Satisfied', 'ar' => 'خفيف / راضٍ'],
                '2' => ['en' => 'Moderate / Neutral', 'ar' => 'متوسط / محايد'],
                '3' => ['en' => 'Severe / Dissatisfied', 'ar' => 'شديد / غير راضٍ'],
                '4' => ['en' => 'Very severe / Very dissatisfied', 'ar' => 'شديد جداً / غير راضٍ جداً'],
            ];
        }

        if ($testType === self::TEST_PSS) {
            $options = [
                '0' => ['en' => 'Never', 'ar' => 'أبداً'],
                '1' => ['en' => 'Almost never', 'ar' => 'تقريباً أبداً'],
                '2' => ['en' => 'Sometimes', 'ar' => 'أحياناً'],
                '3' => ['en' => 'Fairly often', 'ar' => 'في كثير من الأحيان'],
                '4' => ['en' => 'Very often', 'ar' => 'في كثير من الأحيان جداً'],
            ];
        }

        if ($testType === self::TEST_CIS) {
            $options = [
                '0' => ['en' => 'No problem', 'ar' => 'لا توجد مشكلة'],
                '1' => ['en' => 'A small problem', 'ar' => 'مشكلة بسيطة'],
                '2' => ['en' => 'A moderate problem', 'ar' => 'مشكلة متوسطة'],
                '3' => ['en' => 'A big problem', 'ar' => 'مشكلة كبيرة'],
                '4' => ['en' => 'A very big problem', 'ar' => 'مشكلة كبيرة جداً'],
            ];
        }

        return $options;
    }

    /**
     * Get scoring ranges for each test
     */
    public static function getScoringRanges($testType)
    {
        $ranges = [
            self::TEST_PHQ9 => [
                ['min' => 0, 'max' => 4, 'level' => 'minimal', 'label_en' => 'Minimal depression', 'label_ar' => 'اكتئاب بسيط'],
                ['min' => 5, 'max' => 9, 'level' => 'mild', 'label_en' => 'Mild depression', 'label_ar' => 'اكتئاب خفيف'],
                ['min' => 10, 'max' => 14, 'level' => 'moderate', 'label_en' => 'Moderate depression', 'label_ar' => 'اكتئاب متوسط'],
                ['min' => 15, 'max' => 19, 'level' => 'moderately_severe', 'label_en' => 'Moderately severe depression', 'label_ar' => 'اكتئاب شديد متوسط'],
                ['min' => 20, 'max' => 27, 'level' => 'severe', 'label_en' => 'Severe depression', 'label_ar' => 'اكتئاب شديد'],
            ],
            self::TEST_GAD7 => [
                ['min' => 0, 'max' => 4, 'level' => 'minimal', 'label_en' => 'Minimal anxiety', 'label_ar' => 'قلق بسيط'],
                ['min' => 5, 'max' => 9, 'level' => 'mild', 'label_en' => 'Mild anxiety', 'label_ar' => 'قلق خفيف'],
                ['min' => 10, 'max' => 14, 'level' => 'moderate', 'label_en' => 'Moderate anxiety', 'label_ar' => 'قلق متوسط'],
                ['min' => 15, 'max' => 21, 'level' => 'severe', 'label_en' => 'Severe anxiety', 'label_ar' => 'قلق شديد'],
            ],
            self::TEST_PCL5 => [
                ['min' => 0, 'max' => 30, 'level' => 'minimal', 'label_en' => 'Minimal PTSD symptoms', 'label_ar' => 'أعراض PTSD بسيطة'],
                ['min' => 31, 'max' => 33, 'level' => 'mild', 'label_en' => 'Mild PTSD symptoms - monitor', 'label_ar' => 'أعراض PTSD خفيفة - يحتاج متابعة'],
                ['min' => 34, 'max' => 80, 'level' => 'moderate', 'label_en' => 'Moderate to severe PTSD symptoms', 'label_ar' => 'أعراض PTSD متوسطة إلى شديدة'],
            ],
            self::TEST_ISI => [
                ['min' => 0, 'max' => 7, 'level' => 'none', 'label_en' => 'No clinically significant insomnia', 'label_ar' => 'لا يوجد أرق سريري ملحوظ'],
                ['min' => 8, 'max' => 14, 'level' => 'subthreshold', 'label_en' => 'Subthreshold insomnia', 'label_ar' => 'أرق تحت العتبة'],
                ['min' => 15, 'max' => 21, 'level' => 'moderate', 'label_en' => 'Moderate insomnia', 'label_ar' => 'أرق متوسط'],
                ['min' => 22, 'max' => 28, 'level' => 'severe', 'label_en' => 'Severe insomnia', 'label_ar' => 'أرق شديد'],
            ],
            self::TEST_PSS => [
                ['min' => 0, 'max' => 13, 'level' => 'low', 'label_en' => 'Low perceived stress', 'label_ar' => 'إجهاد محسوس منخفض'],
                ['min' => 14, 'max' => 26, 'level' => 'moderate', 'label_en' => 'Moderate perceived stress', 'label_ar' => 'إجهاد محسوس متوسط'],
                ['min' => 27, 'max' => 40, 'level' => 'high', 'label_en' => 'High perceived stress', 'label_ar' => 'إجهاد محسوس مرتفع'],
            ],
            self::TEST_CIS => [
                ['min' => 0, 'max' => 15, 'level' => 'minimal', 'label_en' => 'Minimal functional impairment', 'label_ar' => 'ضعف وظيفي بسيط'],
                ['min' => 16, 'max' => 52, 'level' => 'moderate', 'label_en' => 'Significant functional impairment', 'label_ar' => 'ضعف وظيفي ملحوظ - يحتاج تدخل'],
            ],
        ];

        return $ranges[$testType] ?? [];
    }

    /**
     * Calculate score and result level
     */
    public static function calculateResult($testType, $answers)
    {
        $score = array_sum($answers);

        $ranges = self::getScoringRanges($testType);

        $level = 'minimal';
        foreach ($ranges as $range) {
            if ($score >= $range['min'] && $score <= $range['max']) {
                $level = $range['level'];
                break;
            }
        }

        return [
            'score' => $score,
            'result_level' => $level,
            'interpretation' => self::getInterpretation($testType, $level),
        ];
    }

    /**
     * Get interpretation text
     */
    public static function getInterpretation($testType, $level)
    {
        $interpretations = [
            self::TEST_PHQ9 => [
                'minimal' => [
                    'en' => 'Your score suggests minimal depression. Continue practicing self-care and maintain healthy habits.',
                    'ar' => 'تشير نتيجتك إلى وجود اكتئاب بسيط. استمر في ممارسة الرعاية الذاتية والحفاظ على العادات الصحية.'
                ],
                'mild' => [
                    'en' => 'Your score suggests mild depression. Consider talking to a mental health professional and practicing self-care techniques.',
                    'ar' => 'تشير نتيجتك إلى وجود اكتئاب خفيف. فكر في التحدث مع أخصائي الصحة النفسية وممارسة تقنيات الرعاية الذاتية.'
                ],
                'moderate' => [
                    'en' => 'Your score suggests moderate depression. We recommend speaking with a mental health professional for support.',
                    'ar' => 'تشير نتيجتك إلى وجود اكتئاب متوسط. نوصي بالتحدث مع أخصائي الصحة النفسية للحصول على الدعم.'
                ],
                'moderately_severe' => [
                    'en' => 'Your score suggests moderately severe depression. Please reach out to a mental health professional as soon as possible.',
                    'ar' => 'تشير نتيجتك إلى وجود اكتئاب شديد متوسط. يرجى التواصل مع أخصائي الصحة النفسية في أقرب وقت ممكن.'
                ],
                'severe' => [
                    'en' => 'Your score suggests severe depression. Please contact a mental health professional immediately.',
                    'ar' => 'تشير نتيجتك إلى وجود اكتئاب شديد. يرجى الاتصال بأخصائي الصحة النفسية فوراً.'
                ],
            ],
            self::TEST_GAD7 => [
                'minimal' => [
                    'en' => 'Your score suggests minimal anxiety. Continue your daily wellness practices.',
                    'ar' => 'تشير نتيجتك إلى وجود قلق بسيط. استمر في ممارسات العافية اليومية.'
                ],
                'mild' => [
                    'en' => 'Your score suggests mild anxiety. Consider stress management techniques and self-care.',
                    'ar' => 'تشير نتيجتك إلى وجود قلق خفيف. فكر في تقنيات إدارة التوتر والرعاية الذاتية.'
                ],
                'moderate' => [
                    'en' => 'Your score suggests moderate anxiety. We recommend speaking with a mental health professional.',
                    'ar' => 'تشير نتيجتك إلى وجود قلق متوسط. نوصي بالتحدث مع أخصائي الصحة النفسية.'
                ],
                'severe' => [
                    'en' => 'Your score suggests severe anxiety. Please reach out to a mental health professional for support.',
                    'ar' => 'تشير نتيجتك إلى وجود قلق شديد. يرجى التواصل مع أخصائي الصحة النفسية للحصول على الدعم.'
                ],
            ],
            self::TEST_PCL5 => [
                'minimal' => [
                    'en' => 'Your score suggests minimal PTSD symptoms. Continue with your regular activities.',
                    'ar' => 'تشير نتيجتك إلى وجود أعراض PTSD بسيطة. استمر في أنشطتك المعتادة.'
                ],
                'mild' => [
                    'en' => 'Your score suggests mild PTSD symptoms. Consider monitoring your symptoms and practicing self-care.',
                    'ar' => 'تشير نتيجتك إلى وجود أعراض PTSD خفيفة. فكر في مراقبة أعراضك وممارسة الرعاية الذاتية.'
                ],
                'moderate' => [
                    'en' => 'Your score suggests moderate to severe PTSD symptoms. We strongly recommend speaking with a mental health professional.',
                    'ar' => 'تشير نتيجتك إلى وجود أعراض PTSD متوسطة إلى شديدة. نوصي بشدة بالتحدث مع أخصائي الصحة النفسية.'
                ],
            ],
            self::TEST_ISI => [
                'none' => [
                    'en' => 'Your score suggests no clinically significant sleep problems. Maintain good sleep hygiene.',
                    'ar' => 'تشير نتيجتك إلى عدم وجود مشاكل نوم سريرية ملحوظة. حافظ على نظافة النوم الجيدة.'
                ],
                'subthreshold' => [
                    'en' => 'Your score suggests subthreshold insomnia. Try improving your sleep habits.',
                    'ar' => 'تشير نتيجتك إلى وجود أرق تحت العتبة. حاول تحسين عادات نومك.'
                ],
                'moderate' => [
                    'en' => 'Your score suggests moderate insomnia. Consider speaking with a healthcare provider.',
                    'ar' => 'تشير نتيجتك إلى وجود أرق متوسط. فكر في التحدث مع مقدم الرعاية الصحية.'
                ],
                'severe' => [
                    'en' => 'Your score suggests severe insomnia. Please consult a healthcare professional.',
                    'ar' => 'تشير نتيجتك إلى وجود أرق شديد. يرجى استشارة أخصائي الرعاية الصحية.'
                ],
            ],
            self::TEST_PSS => [
                'low' => [
                    'en' => 'Your score suggests low perceived stress. Continue your healthy coping strategies.',
                    'ar' => 'تشير نتيجتك إلى وجود إجهاد محسوس منخفض. استمر في استراتيجيات التأقلم الصحية.'
                ],
                'moderate' => [
                    'en' => 'Your score suggests moderate perceived stress. Consider stress reduction techniques.',
                    'ar' => 'تشير نتيجتك إلى وجود إجهاد محسوس متوسط. فكر في تقنيات تقليل التوتر.'
                ],
                'high' => [
                    'en' => 'Your score suggests high perceived stress. We recommend speaking with a mental health professional.',
                    'ar' => 'تشير نتيجتك إلى وجود إجهاد محسوس مرتفع. نوصي بالتحدث مع أخصائي الصحة النفسية.'
                ],
            ],
            self::TEST_CIS => [
                'minimal' => [
                    'en' => 'Your score suggests minimal functional impairment. You are managing daily activities well.',
                    'ar' => 'تشير نتيجتك إلى وجود ضعف وظيفي بسيط. أنت تدير الأنشطة اليومية بشكل جيد.'
                ],
                'moderate' => [
                    'en' => 'Your score suggests significant functional impairment. We recommend speaking with a mental health professional.',
                    'ar' => 'تشير نتيجتك إلى وجود ضعف وظيفي ملحوظ. نوصي بالتحدث مع أخصائي الصحة النفسية.'
                ],
            ],
        ];

        return $interpretations[$testType][$level] ?? $interpretations[$testType]['minimal'];
    }
}
