<?php

namespace App\Repositories\Eloquent;

use App\Models\Kelas;
use App\Models\ClassStudent;
use App\Models\Syllabus;
use App\Repositories\Contracts\ClassRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClassRepository implements ClassRepositoryInterface
{
    /**
     * @var Kelas
     */
    protected Kelas $model;

    /**
     * ClassRepository constructor.
     * 
     * @param Kelas $model
     */
    public function __construct(Kelas $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id)
    {
        return $this->model->where('id_class', $id)->first();
    }

    /**
     * @inheritDoc
     */
    public function findAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * @inheritDoc
     */
    public function getStudentCount(int $classId): int
    {
        return ClassStudent::where('id_class', $classId)->count();
    }

    /**
     * @inheritDoc
     */
    public function getReviews(int $classId): Collection
    {
        return DB::table('class_review')
            ->join('class_student', 'class_student.id_class_student', '=', 'class_review.id_class_student')
            ->select(
                'class_review.id_class_review',
                'class_student.id_user',
                'class_student.id_class',
                'class_review.star',
                'class_review.content',
                'class_review.created_at'
            )
            ->where('class_student.id_class', $classId)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getSyllabus(int $classId): Collection
    {
        return Syllabus::where('id_class', $classId)
            ->orderBy('order_number', 'asc')
            ->get(['id_syllabus', 'title as judul']);
    }

    /**
     * @inheritDoc
     */
    public function calculateRating(int $classId): float
    {
        $reviews = $this->getReviews($classId);
        
        if ($reviews->isEmpty()) {
            return 0;
        }

        $total = $reviews->sum('star');
        return (float) number_format($total / $reviews->count(), 2);
    }

    /**
     * @inheritDoc
     */
    public function isUserEnrolled(int $classId, int $userId): bool
    {
        return ClassStudent::where('id_class', $classId)
            ->where('id_user', $userId)
            ->exists();
    }

    /**
     * Find class with specific columns
     * 
     * @param int $id
     * @param array $columns
     * @return mixed
     */
    public function findWithColumns(int $id, array $columns = ['*'])
    {
        return $this->model->select($columns)->where('id_class', $id)->first();
    }

    /**
     * Get learning path for a class
     * 
     * @param int $classId
     * @return mixed
     */
    public function getLearningPath(int $classId)
    {
        return DB::table('learning_path_class')
            ->join('learning_path', 'learning_path.id_learning_path', '=', 'learning_path_class.id_learning_path')
            ->select('learning_path.name')
            ->where('id_class', $classId)
            ->first();
    }
}
