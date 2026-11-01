<?php

namespace App\Services;

use App\Models\CertificateStudent;
use App\Models\ClassLecture;
use App\Models\ClassReview;
use App\Models\ClassStudent;
use App\Models\Comment;
use App\Models\Kelas;
use App\Models\LearningPathClass;
use App\Models\LectureRating;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Syllabus;
use App\Models\SyllabusStudent;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClassroomService
{
        public function getClassroomData(User $user, ClassStudent $classStudent, int $classId, string $token): array
    {
        $projectUser = ProjectUser::where('id_class_student', $classStudent->id_class_student)
            ->select('expired', 'id_project')
            ->first();

        $present = Carbon::now()->format('U');
        $projectPassPoin = config('app-settings.grading.project_pass_score', 80);

        // Check if project access is expired
        if ($projectUser) {
            if ($projectUser->expired < $present && $classStudent->completed_at === null) {
                return [
                    'expired' => true,
                    'status' => 'expired',
                    'id_class' => $classStudent->id_class
                ];
            }

            $project = Project::select('pass_poin')
                ->where('id_project', $projectUser->id_project)
                ->first();
            $projectPassPoin = $project ? $project->pass_poin : $projectPassPoin;
        }

        $class = Kelas::select('id_class', 'title', 'id_class_level', 'duration')
            ->where('id_class', $classId)
            ->first();

        if (!$class) {
            return ['error' => 'class_not_found'];
        }

        $teachers = $this->getClassTeachers($class->id_class);
        $quizHistories = $this->getQuizHistories($classStudent, $classId, $projectPassPoin);
        $progressData = $this->getClassProgress($classStudent);
        $certificate = $this->getCertificateStatus($classStudent->id_class_student);
        $materials = $this->getMaterialsWithQuizzes($classStudent, $projectPassPoin);
        $project = $this->getProjectData($class, $classStudent, $token);

        $learningPath = LearningPathClass::select('id_learning_path')
            ->where('id_class', $class->id_class)
            ->first();

        $statusClass = 'on-progress';
        $classProgress = $progressData['progress'];

        if ($classProgress === 'completed') {
            if (!is_array($certificate)) {
                $statusClass = 'completed';
            } else {
                $classProgress = $progressData['progress_temp'];
            }
        }

        $review = ClassReview::select('star', 'content')
            ->where('id_class_student', $classStudent->id_class_student)
            ->first();

        return [
            'status' => $statusClass,
            'class' => $class->title,
            'id_class' => $class->id_class,
            'learning_path' => $learningPath ? $learningPath->id_learning_path : '-',
            'teachers' => $teachers,
            'class_progress' => $classProgress,
            'certificate' => $certificate,
            'quiz_history' => $quizHistories,
            'project' => $project,
            'last_material' => $progressData['status'] === 'completed' ? 'completed' : $progressData['last_material'],
            'material' => $materials,
            'review' => $review,
        ];
    }

        public function getClassTeachers(int $classId): array
    {
        $teachers = ClassLecture::where('id_class', $classId)->get();
        $result = [];

        foreach ($teachers as $teacher) {
            $userData = User::select('name as nama', 'avatar_original as foto', 'email', 'phone_number', 'social_media')
                ->where('id_user', $teacher->id_user)
                ->first();

            if (!$userData) {
                continue;
            }

            $rating = round(LectureRating::where('id_lecture', $teacher->id_class_lecture)->avg('rating'), 1);

            $result[] = [
                'id_teacher' => $teacher->id_user,
                'nama' => $userData->nama,
                'foto' => $userData->foto,
                'rating' => $rating ?: 0,
                'email' => $userData->email,
                'phone' => $userData->phone_number,
                'social_media' => json_decode($userData->social_media),
            ];
        }

        return $result;
    }

        public function getQuizHistories(ClassStudent $classStudent, int $classId, int $projectPassPoin)
    {
        $quizHistories = DB::table('quiz_student')
            ->join('quiz', 'quiz_student.id_quiz', 'quiz.id_quiz')
            ->join('class_student', 'class_student.id_class_student', 'quiz_student.id_class_student')
            ->select(
                'quiz.id_quiz',
                'class_student.id_class',
                'quiz.id_syllabus',
                'quiz_student.id_quiz_student',
                'quiz_student.created_at',
                'quiz_student.grade'
            )
            ->where('quiz_student.id_class_student', $classStudent->id_class_student)
            ->where('class_student.id_class', $classId)
            ->get();

        foreach ($quizHistories as $history) {
            $syllabus = Syllabus::select('id_syllabus', 'title as judul')
                ->where('id_syllabus', $history->id_syllabus)
                ->first();

            $history->quiz_title = $syllabus ? $syllabus->judul : '';
            $history->date = Carbon::parse($history->created_at)->format('d F Y');
            $history->score = number_format($history->grade, 1, '.', '');
            $history->status = $history->score >= $projectPassPoin ? 'Pass' : 'Not Pass';

            unset($history->created_at);
            unset($history->grade);
            unset($history->id_quiz);
            unset($history->id_quiz_student);
        }

        return $quizHistories;
    }

        public function getClassProgress(ClassStudent $classStudent): array
    {
        $lastSyllabus = Syllabus::select('id_syllabus', 'order_number')
            ->where('id_class', $classStudent->id_class)
            ->orderBy('order_number', 'DESC')
            ->first();

        $mySyllabusStudent = SyllabusStudent::select('id_syllabus')
            ->where('id_class_student', $classStudent->id_class_student)
            ->first();

        if (!$mySyllabusStudent || !$lastSyllabus) {
            return [
                'progress' => [0, 0],
                'progress_temp' => [0, 0],
                'last_material' => 0,
                'status' => 'on-progress'
            ];
        }

        $mySyllabus = Syllabus::select('id_syllabus', 'order_number')
            ->where('id_syllabus', $mySyllabusStudent->id_syllabus)
            ->first();

        $progressArray = [$mySyllabus->order_number, $lastSyllabus->order_number];
        $status = 'on-progress';

        $progress = $progressArray;
        if ($mySyllabus->order_number / $lastSyllabus->order_number == 1) {
            $lastQuiz = DB::table('quiz')
                ->select('id_quiz')
                ->where('id_syllabus', $lastSyllabus->id_syllabus)
                ->first();

            $grade = DB::table('quiz_student')
                ->select('grade')
                ->where('id_class_student', $classStudent->id_class_student)
                ->where('id_quiz', $lastQuiz ? $lastQuiz->id_quiz : 0)
                ->first();

            if ($grade && $grade->grade > 79) {
                $status = 'completed';
                $progress = 'completed';
            }
        }

        return [
            'progress' => $progress,
            'progress_temp' => $progressArray,
            'last_material' => $mySyllabus->order_number,
            'status' => $status
        ];
    }

        public function getCertificateStatus(int $classStudentId): array
    {
        $certificate = CertificateStudent::select('id_class_certificate', 'created_at')
            ->where('id_class_student', $classStudentId)
            ->first();

        if (!$certificate) {
            return [
                'status' => 'on-progress',
                'message' => "You haven't finish this class yet"
            ];
        }

        $validityYears = config('app-settings.certificate.validity_years', 2);
        $expired = Carbon::createFromFormat('Y-m-d H:i:s', $certificate->created_at)
            ->addYears($validityYears);
        $expiredFormatted = $expired->format('d F Y');
        $expiredTimestamp = $expired->format('U');

        $isExpired = $expiredTimestamp < Carbon::now()->format('U');

        return [
            'id_class_certificate' => $certificate->id_class_certificate,
            'status' => $isExpired ? 'expired' : 'available',
            'message' => $isExpired
                ? "Expired at $expiredFormatted"
                : "Available until $expiredFormatted",
        ];
    }

        public function getMaterialsWithQuizzes(ClassStudent $classStudent, int $projectPassPoin): array
    {
        $syllabi = Syllabus::select('id_syllabus', 'title', 'content')
            ->where('id_class', $classStudent->id_class)
            ->orderBy('order_number', 'ASC')
            ->get();

        $result = [];

        foreach ($syllabi as $syllabus) {
            $quizContainer = DB::table('quiz')
                ->where('id_syllabus', $syllabus->id_syllabus)
                ->get();

            $quizHistories = $this->buildQuizHistoryForMaterial(
                $classStudent->id_class_student,
                $syllabus->id_syllabus,
                $projectPassPoin
            );

            $quizData = [
                'title' => $syllabus->title,
                'description' => '',
                'quizzes' => $quizContainer,
                'history' => $quizHistories,
            ];

            $result[] = [$syllabus, $quizData];
        }

        return $result;
    }

        private function buildQuizHistoryForMaterial(int $classStudentId, int $syllabusId, int $projectPassPoin): array
    {
        $quizHistories = DB::table('quiz_student')
            ->where('id_class_student', $classStudentId)
            ->orderBy('created_at', 'ASC')
            ->get();

        $result = [];

        foreach ($quizHistories as $history) {
            $quizInfo = DB::table('quiz')
                ->where('id_quiz', $history->id_quiz)
                ->where('id_syllabus', $syllabusId)
                ->first();

            if (!$quizInfo) {
                continue;
            }

            $historyData = (object)[
                'id_quiz' => $quizInfo->id_quiz,
                'id_syllabus' => $quizInfo->id_syllabus,
                'id_quiz_student' => $history->id_quiz_student,
                'date' => Carbon::parse($history->created_at)->format('d F Y'),
                'score' => number_format($history->grade, 1, '.', ''),
                'status' => $history->grade >= $projectPassPoin ? 'Pass' : 'Not Pass',
            ];

            $historyData->detail = $this->buildQuizDetail($history->id_quiz, $history->id_quiz_student);
            $historyData->information = $this->calculateQuizInformation($history->id_quiz, $history->id_quiz_student);

            unset($historyData->id_quiz);

            $result[] = $historyData;
        }

        return $result;
    }

        private function buildQuizDetail(int $quizId, int $quizStudentId): array
    {
        $sections = DB::table('quiz_section')
            ->select('id_quiz_section', 'title', 'desc', 'image', 'video')
            ->where('id_quiz', $quizId)
            ->orderBy('id_quiz_section', 'ASC')
            ->distinct()
            ->get();

        $result = [];

        foreach ($sections as $section) {
            $questions = DB::table('question')
                ->select('id_question', 'question', 'type', 'text_answere')
                ->where('id_quiz_section', $section->id_quiz_section)
                ->orderBy('id_question', 'ASC')
                ->get();

            $questionDetails = [];
            $nomor = 1;

            foreach ($questions as $question) {
                $answerData = $this->buildQuestionAnswer($question, $quizStudentId);
                $questionDetails[] = [
                    'nomor_soal' => $nomor++,
                    'soal' => $question->question,
                    'answer' => $answerData,
                ];
            }

            $result[] = [
                'hint' => $section->title,
                'questions' => $questionDetails,
            ];
        }

        return $result;
    }

        private function buildQuestionAnswer(object $question, int $quizStudentId): array
    {
        if ($question->type === 'multiple_choice') {
            return $this->buildMultipleChoiceAnswer($question, $quizStudentId);
        } elseif ($question->type === 'short_answer') {
            return $this->buildShortAnswer($question, $quizStudentId);
        } else {
            return $this->buildMatchAnswer($question, $quizStudentId);
        }
    }

        private function buildMultipleChoiceAnswer(object $question, int $quizStudentId): array
    {
        $options = DB::table('option')
            ->select('option', 'is_true')
            ->where('id_question', $question->id_question)
            ->get();

        $userAnswer = DB::table('quiz_answer')
            ->select('answer')
            ->where('id_quiz_student', $quizStudentId)
            ->where('id_option', $question->id_question)
            ->first();

        $result = [];
        foreach ($options as $option) {
            $status = 'not-answer';

            if ($userAnswer) {
                if (strtolower($option->option) === strtolower($userAnswer->answer)) {
                    $status = $option->is_true ? 'correct' : 'incorrect';
                }
            }

            $result[] = [$option->option, $status];
        }

        return $result;
    }

        private function buildShortAnswer(object $question, int $quizStudentId): array
    {
        $userAnswer = DB::table('quiz_answer')
            ->select('answer')
            ->where('id_quiz_student', $quizStudentId)
            ->where('id_option', $question->id_question)
            ->first();

        $correctAnswer = strtolower($question->text_answere);
        $givenAnswer = $userAnswer ? strtolower(explode('#', $userAnswer->answer)[1] ?? '') : '';
        $status = $correctAnswer === $givenAnswer ? 'correct' : 'incorrect';

        return [
            [$question->question . ' => ' . ($userAnswer ? explode('#', $userAnswer->answer)[1] : ''), $status]
        ];
    }

        private function buildMatchAnswer(object $question, int $quizStudentId): array
    {
        $options = DB::table('option')
            ->select('id_option', 'option', 'is_true')
            ->where('id_question', $question->id_question)
            ->get();

        $result = [];
        foreach ($options as $option) {
            $userAnswer = DB::table('quiz_answer')
                ->select('answer')
                ->where('id_quiz_student', $quizStudentId)
                ->where('id_option', $option->id_option)
                ->first();

            $correctPair = explode('#', $option->option)[1] ?? '';
            $givenPair = $userAnswer ? (explode('#', $userAnswer->answer)[1] ?? '') : '';
            $status = $correctPair === $givenPair ? 'correct' : 'incorrect';

            $result[] = [explode('#', $option->option)[0] . ' => ' . $correctPair, $status];
        }

        return $result;
    }

        private function calculateQuizInformation(int $quizId, int $quizStudentId): array
    {
        $sections = DB::table('quiz_section')
            ->where('id_quiz', $quizId)
            ->get();

        $totalQuestions = 0;
        $correctAnswers = 0;

        foreach ($sections as $section) {
            $questions = DB::table('question')
                ->where('id_quiz_section', $section->id_quiz_section)
                ->get();

            $totalQuestions += $questions->count();

            foreach ($questions as $question) {
                if ($question->type === 'multiple_choice') {
                    $options = DB::table('option')
                        ->where('id_question', $question->id_question)
                        ->where('is_true', 1)
                        ->get();

                    foreach ($options as $option) {
                        $userAnswer = DB::table('quiz_answer')
                            ->where('id_quiz_student', $quizStudentId)
                            ->where('id_option', $question->id_question)
                            ->first();

                        if ($userAnswer && strtolower($option->option) === strtolower($userAnswer->answer)) {
                            $correctAnswers++;
                        }
                    }
                } elseif ($question->type === 'short_answer') {
                    $userAnswer = DB::table('quiz_answer')
                        ->where('id_quiz_student', $quizStudentId)
                        ->where('id_option', $question->id_question)
                        ->first();

                    if ($userAnswer) {
                        $givenAnswer = strtolower(explode('#', $userAnswer->answer)[1] ?? '');
                        if ($givenAnswer === strtolower($question->text_answere)) {
                            $correctAnswers++;
                        }
                    }
                }
            }
        }

        return [
            'correct' => $correctAnswers,
            'incorrect' => $totalQuestions - $correctAnswers,
            'total_question' => $totalQuestions,
        ];
    }

        public function getProjectData(Kelas $class, ClassStudent $classStudent, string $token)
    {
        $project = Project::where('id_class', $class->id_class)->first();

        if (!$project) {
            return ['data' => 'No Project Yet.'];
        }

        unset($project->is_submition);

        $project->expired = new Carbon($classStudent->created_at);
        $project->created_at = new Carbon($classStudent->created_at);
        $project->updated_at = new Carbon($classStudent->updated_at);

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return ['data' => 'No Project Yet.'];
        }

        $projectUser = ProjectUser::where('id_class_student', $classStudent->id_class_student)
            ->where('id_project', $project->id_project)
            ->first();

        $duration = $class->duration ?? config('app-settings.class.default_expiry_days', 60);
        $project->expired = $project->expired->addDays($duration);

        $idProjectResult = null;

        if ($projectUser) {
            $presult = DB::table('project_result')
                ->where('id_project_student', $projectUser->id_project_student)
                ->first();

            $idProjectResult = $projectUser->id_project_student;

            if ($presult) {
                $project->status = 'Turned in';

                if ($projectUser->poin !== null) {
                    $passPoin = $project->pass_poin ?: config('app-settings.grading.project_pass_score', 80);

                    $projectComment = Comment::where('id_commented', $projectUser->id_project_student)
                        ->where('commented', 'project_student')
                        ->orderBy('id_comment', 'desc')
                        ->first();

                    $project->lectureNote = [
                        'grade' => (int)$projectUser->poin,
                        'status' => (int)$projectUser->poin >= (int)$passPoin ? 'Approved' : 'Disapproved',
                        'note' => $projectComment ? $projectComment->content : 'No comment was given.',
                        'noted_at' => $projectUser->updated_at,
                    ];
                } else {
                    $project->lectureNote = ['status' => 'Waiting to be graded.'];
                }
            } else {
                $project->status = 'No File Attached';
                $project->lectureNote = ['status' => 'Empty, No File Submitted Yet.'];
            }
        } else {
            $project->status = 'No File Attached';
            $project->lectureNote = ['status' => 'Empty, No File Submitted Yet.'];
        }

        $project->expired = $project->expired->format('d M Y');
        unset($project->updated_at);

        $now = Carbon::now()->format('d M Y');
        $isActive = $project->expired > $now;

        $project->project_title = $project->title;
        $project->date = $project->expired;

        $project->history = 'No project upload history found.';

        if ($idProjectResult) {
            $projectResults = DB::table('project_result')
                ->where('id_project_student', $idProjectResult)
                ->orderBy('created_at', 'asc')
                ->get();

            $history = [];
            foreach ($projectResults as $result) {
                $status = 'Waiting to be Graded';
                if ($result->status !== null) {
                    $status = $result->status == 2 ? 'Approved' : 'Disapproved';
                }

                $history[] = [
                    'title' => $project->title,
                    'date' => Carbon::parse($result->created_at)->format('d F Y'),
                    'status' => $status,
                ];
            }

            $project->history = $history;
        }

        return $project;
    }
}
