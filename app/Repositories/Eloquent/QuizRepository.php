<?php

namespace App\Repositories\Eloquent;

use App\Models\QuizStudent;
use App\Models\QuizAnswer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuizRepository
{
    /**
     * Get quiz by syllabus ID
     * 
     * @param int $syllabusId
     * @return object|null
     */
    public function getQuizBySyllabus(int $syllabusId)
    {
        return DB::table('quiz')->where('id_syllabus', $syllabusId)->first();
    }

    /**
     * Get quiz sections
     * 
     * @param int $quizId
     * @return Collection
     */
    public function getQuizSections(int $quizId): Collection
    {
        return DB::table('quiz_section')
            ->where('id_quiz', $quizId)
            ->select('id_quiz_section', 'id_quiz', 'title as question', 'desc as description', 'image', 'video')
            ->orderBy('order_number', 'asc')
            ->get();
    }

    /**
     * Get questions for a section
     * 
     * @param int $sectionId
     * @return Collection
     */
    public function getQuestions(int $sectionId): Collection
    {
        return DB::table('question')
            ->select('id_question', 'question as soal', 'type as tipe_soal', 'desc', 'image', 'text_answere')
            ->where('id_quiz_section', $sectionId)
            ->orderBy('order_number', 'ASC')
            ->get();
    }

    /**
     * Get options for a question
     * 
     * @param int $questionId
     * @return Collection
     */
    public function getOptions(int $questionId): Collection
    {
        return DB::table('option')
            ->select('id_option', 'option', 'image', 'is_true')
            ->where('id_question', $questionId)
            ->orderBy('order_number', 'ASC')
            ->get();
    }

    /**
     * Get quiz history for a class student
     * 
     * @param int $classStudentId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getQuizHistory(int $classStudentId, int $perPage = 5)
    {
        return DB::table('quiz_student')
            ->select('created_at', 'grade')
            ->where('id_class_student', $classStudentId)
            ->orderBy('id_quiz_student', 'desc')
            ->paginate($perPage);
    }

    /**
     * Save quiz attempt
     * 
     * @param array $data
     * @return QuizStudent
     */
    public function saveQuizAttempt(array $data): QuizStudent
    {
        $quizStudent = new QuizStudent();
        $quizStudent->id_quiz = $data['id_quiz'];
        $quizStudent->id_class_student = $data['id_class_student'];
        $quizStudent->grade = $data['grade'];
        $quizStudent->save();

        return $quizStudent;
    }

    /**
     * Save quiz answer
     * 
     * @param array $data
     * @return QuizAnswer
     */
    public function saveQuizAnswer(array $data): QuizAnswer
    {
        $quizAnswer = new QuizAnswer();
        $quizAnswer->id_quiz_student = $data['id_quiz_student'];
        $quizAnswer->id_question = $data['id_question'];
        $quizAnswer->id_option = $data['id_option'];
        $quizAnswer->answer = $data['answer'];
        $quizAnswer->save();

        return $quizAnswer;
    }

    /**
     * Get correct answer for a question (multiple choice)
     * 
     * @param int $questionId
     * @return object|null
     */
    public function getCorrectAnswer(int $questionId)
    {
        return DB::table('option')
            ->where('id_question', $questionId)
            ->where('is_true', 1)
            ->first();
    }

    /**
     * Get text answer for a question (short answer)
     * 
     * @param int $questionId
     * @return string|null
     */
    public function getTextAnswer(int $questionId): ?string
    {
        $question = DB::table('question')
            ->select('text_answere')
            ->where('id_question', $questionId)
            ->first();

        return $question->text_answere ?? null;
    }
}
