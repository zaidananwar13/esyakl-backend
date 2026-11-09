<?php

namespace App\Services;

use App\Helpers\DateHelper;
use App\Helper;
use App\Models\ClassStudent;
use App\Models\QuizAnswer;
use App\Models\QuizProgress;
use App\Models\QuizStudent;
use App\Models\Syllabus;
use App\Models\SyllabusStudent;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QuizService
{
    protected int $passScore;

    public function __construct()
    {
        $this->passScore = config('app-settings.grading.pass_score', 78);
    }

    public function getQuizForSyllabus(int $syllabusId, int $userId): ?array
    {
        $syllabus = Syllabus::select('title as judul', 'id_syllabus')
            ->where('id_syllabus', $syllabusId)
            ->first();

        if (!$syllabus) {
            return null;
        }

        $quiz = DB::table('quiz')
            ->where('id_syllabus', $syllabusId)
            ->first();

        if (!$quiz) {
            return null;
        }

        return [
            'title' => $syllabus->judul,
            'quizzes' => $this->getQuizSections($quiz->id_quiz),
            'history' => $this->getQuizHistory($userId, $syllabusId)
        ];
    }

    public function getQuizSections(int $quizId): array
    {
        $sections = DB::table('quiz_section')
            ->where('id_quiz', $quizId)
            ->select(
                'id_quiz_section',
                'id_quiz',
                'title as question',
                'desc as description',
                'image',
                'video'
            )
            ->orderBy('order_number', 'asc')
            ->get();

        $result = [];
        $orderNumber = 1;

        foreach ($sections as $section) {
            $questions = $this->getQuestions($section->id_quiz_section, $orderNumber);
            $section->quiz = $questions['questions'];
            $orderNumber = $questions['next_order'];
            $result[] = $section;
        }

        return $result;
    }

    public function getQuestions(int $sectionId, int $startOrder = 1): array
    {
        $questions = DB::table('question')
            ->select('id_question', 'question as soal', 'type as tipe_soal', 'desc', 'image')
            ->where('id_quiz_section', $sectionId)
            ->orderBy('order_number', 'ASC')
            ->get();

        $orderNumber = $startOrder;
        $result = [];

        foreach ($questions as $question) {
            $options = $this->getOptions($question->id_question);

            if ($question->tipe_soal === 'match_option') {
                $matchOptions = [];
                foreach ($options as $option) {
                    $option->order_number = $orderNumber++;
                    $optionParts = explode('#', $option->option);
                    $option->match = $optionParts[0] ?? '';
                    $option->pair = $optionParts[1] ?? '';
                    unset($option->option);
                    $matchOptions[] = $option;
                }
                $question->option = $matchOptions;
            } else {
                $question->order_number = $orderNumber++;
                $question->option = $options;
            }

            $result[] = $question;
        }

        return [
            'questions' => $result,
            'next_order' => $orderNumber
        ];
    }

    public function getOptions(int $questionId)
    {
        return DB::table('option')
            ->select('id_option', 'option', 'image')
            ->where('id_question', $questionId)
            ->orderBy('order_number', 'ASC')
            ->get();
    }

    public function getQuizHistory(int $userId, ?int $syllabusId = null)
    {
        $query = DB::table('quiz_student')
            ->select('quiz_student.created_at', 'quiz_student.grade')
            ->orderBy('id_quiz_student', 'desc');

        $history = $query->paginate(5);

        foreach ($history as $item) {
            $item->date = Carbon::parse($item->created_at)->format('d F Y');
            $item->score = number_format($item->grade, 1, '.', '');
            $item->status = $item->grade > $this->passScore ? 'Pass' : 'Not Pass';
            unset($item->created_at);
            unset($item->grade);
        }

        return $history;
    }

    public function submitQuiz(int $userId, int $syllabusId, array $answers): array
    {
        $class = DB::table('syllabus')
            ->select('id_class')
            ->where('id_syllabus', $syllabusId)
            ->first();

        if (!$class) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Syllabus not found'
            ];
        }

        $classStudent = DB::table('class_student')
            ->where('id_user', $userId)
            ->where('id_class', $class->id_class)
            ->orderBy('id_class_student', 'DESC')
            ->first();

        if (!$classStudent) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Error! Students are not registered in the class!'
            ];
        }

        $quizzes = Helper::generateQuiz($answers);

        if (empty($quizzes)) {
            return [
                'success' => false,
                'code' => 500,
                'message' => 'Sorry, something went wrong :('
            ];
        }

        $scoreResult = $this->calculateScore($quizzes);
        $grade = $scoreResult['grade'];
        $rightChoice = $scoreResult['correct'];
        $counter = $scoreResult['total'];

        $quiz = DB::table('quiz')
            ->select('id_quiz')
            ->where('id_syllabus', $syllabusId)
            ->first();

        $this->saveQuizAttempt($quiz->id_quiz, $classStudent->id_class_student, $grade, $quizzes);

        return [
            'success' => true,
            'code' => 200,
            'message' => "Congrats, you answer precisely $rightChoice out of $counter!",
            'correct' => $rightChoice,
            'question' => $counter,
            'grade' => $grade,
            'status' => $grade > $this->passScore ? 'Pass' : 'Not Pass'
        ];
    }

    public function calculateScore(array $quizzes): array
    {
        $counter = count($quizzes);
        $rightChoice = 0;

        foreach ($quizzes as $quiz) {
            $answer = DB::table('option')
                ->select('option')
                ->where('id_option', $quiz['id_quiz'])
                ->first();

            $typeAnswer = explode('#', $quiz['pilihan'])[0];

            if ($typeAnswer === 'short_answer') {
                $ans = DB::table('question')
                    ->select('text_answere')
                    ->where('id_question', $quiz['id_quiz'])
                    ->first();

                if ($ans && strtolower($ans->text_answere) === strtolower(explode('#', $quiz['pilihan'])[1])) {
                    $rightChoice++;
                }
            } elseif ($typeAnswer === 'match') {
                if ($answer && explode('#', $quiz['pilihan'])[1] === explode('#', $answer->option)[1]) {
                    $rightChoice++;
                }
            } else {
                $answerOptions = DB::table('option')
                    ->select('is_true', 'option')
                    ->where('id_question', $quiz['id_quiz'])
                    ->get();

                foreach ($answerOptions as $ans) {
                    if ($ans->option === $quiz['pilihan'] && $ans->is_true == 1) {
                        $rightChoice++;
                    }
                }
            }
        }

        $grade = $counter > 0 ? ($rightChoice / $counter) * 100 : 0;

        return [
            'grade' => $grade,
            'correct' => $rightChoice,
            'total' => $counter
        ];
    }

    public function saveQuizAttempt(int $quizId, int $classStudentId, float $grade, array $quizzes): QuizStudent
    {
        $quizStudent = new QuizStudent();
        $quizStudent->id_quiz = $quizId;
        $quizStudent->id_class_student = $classStudentId;
        $quizStudent->grade = $grade;
        $quizStudent->save();

        foreach ($quizzes as $quiz) {
            $answer = DB::table('option')
                ->select('id_question', 'id_option')
                ->where('id_option', $quiz['id_quiz'])
                ->first();

            if ($answer) {
                $quizAnswer = new QuizAnswer();
                $quizAnswer->id_quiz_student = $quizStudent->id_quiz_student;
                $quizAnswer->id_question = $answer->id_question;
                $quizAnswer->id_option = $answer->id_option;
                $quizAnswer->answer = $quiz['pilihan'];
                $quizAnswer->save();
            }
        }

        return $quizStudent;
    }

        public function getQuizKey(?int $syllabusId = null): array
    {
        if ($syllabusId === null) {
            return [];
        }

        $quizContainer = DB::table('quiz')
            ->select('id_quiz')
            ->where('id_syllabus', $syllabusId)
            ->get();

        $arrayTemp = [];
        foreach ($quizContainer as $container) {
            $sections = DB::table("quiz_section")
                ->select("id_quiz_section", "title as question", "image", "video")
                ->where('id_quiz', $container->id_quiz)
                ->get();

            $arrayTemp = $sections;
        }
        $quizContainer = $arrayTemp;

        $result = [];
        $number = 0;

        foreach ($quizContainer as $container) {
            $questions = DB::table('question')
                ->select('id_question', 'question as soal', 'type as tipe_soal', 'desc', 'image')
                ->where('id_quiz_section', $container->id_quiz_section)
                ->orderBy('order_number', 'ASC')
                ->get();

            foreach ($questions as $question) {
                $number++;
                $question->nomor = $number;
                $question->option = DB::table('option')
                    ->select('id_option', 'option', 'is_true')
                    ->where('id_question', $question->id_question)
                    ->orderBy('order_number', 'ASC')
                    ->get();
            }

            $result[] = $questions;
        }

        return $result;
    }

        public function getSampleQuiz(): array
    {
        $quiz = DB::table('quiz')
            ->where('id_syllabus', 2)
            ->first();

        if (!$quiz) {
            return ['quiz' => []];
        }

        $quizContainer = DB::table('quiz_section')
            ->where('id_quiz', $quiz->id_quiz)
            ->select("quiz_section.id_quiz_section", "quiz_section.id_quiz", "quiz_section.title as question", "quiz_section.image", "quiz_section.video")
            ->orderBy('order_number', 'asc')
            ->get();

        $result = [];
        foreach ($quizContainer as $container) {
            $questions = DB::table('question')
                ->select('id_question', 'question as soal', 'type as tipe_soal', 'desc', 'image')
                ->where('id_quiz_section', $container->id_quiz_section)
                ->orderBy('order_number', 'ASC')
                ->get();

            foreach ($questions as $question) {
                $options = DB::table('option')
                    ->select('id_option', 'option', 'image', 'order_number')
                    ->where('id_question', $question->id_question)
                    ->orderBy('order_number', 'ASC')
                    ->get();

                if ($question->tipe_soal === 'match_option') {
                    $matchTmp = [];
                    $pairTmp = [];

                    foreach ($options as $matchPair) {
                        $optionParts = explode('#', $matchPair->option);
                        $matchPair->match = $optionParts[0] ?? '';
                        $pairTmp[] = $optionParts[1] ?? '';
                        unset($matchPair->option);
                        $matchTmp[] = $matchPair;
                    }

                    $question->option = [
                        'draggable' => $matchTmp,
                        'pairs' => $pairTmp,
                    ];
                } else {
                    $question->option = $options;
                }
            }

            $container->quiz = $questions;
            $result[] = $container;
        }

        return [
            'quiz' => [
                [
                    "id_question" => 1321,
                    "soal" => "<p>Tarikla</p>",
                    "tipe_soal" => "match_option",
                    "desc" => null,
                    "image" => null,
                    "option" => [
                        [
                            'dragable' => [
                                ["id_option" => 3465, "image" => null, "match" => "خَاشِعٌ"],
                                ["id_option" => 3466, "image" => null, "match" => "a"],
                                ["id_option" => 3467, "image" => null, "match" => "b"],
                                ["id_option" => 3468, "image" => null, "match" => "s"],
                                ["id_option" => 3469, "image" => null, "match" => "e"],
                            ],
                            'pairs' => ["خَاشِعٌ", "a", "b", "s", "e"]
                        ],
                    ],
                ]
            ]
        ];
    }

        public function getOptionById(int $optionId)
    {
        // SECURITY: public endpoint - never expose the answer key (is_true).
        return DB::table('option')
            ->select('id_option', 'id_question', 'option', 'image', 'order_number', 'created_at', 'updated_at')
            ->where('id_option', $optionId)
            ->get();
    }

        public function getQuizProgress(int $userId)
    {
        return QuizProgress::select("id_kategori_silabus", "grade", "updated_at")
            ->where("id_user", $userId)
            ->get();
    }

        public function isQuizCompleted(int $userId, int $kategoriSilabusId): bool
    {
        return QuizProgress::where("id_user", $userId)
            ->where("id_kategori_silabus", $kategoriSilabusId)
            ->count() > 0;
    }
}
