<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ClassRepositoryInterface
{
    /**
     * Find class by ID
     * 
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * Get all classes
     * 
     * @return Collection
     */
    public function findAll(): Collection;

    /**
     * Get student count for a class
     * 
     * @param int $classId
     * @return int
     */
    public function getStudentCount(int $classId): int;

    /**
     * Get reviews for a class
     * 
     * @param int $classId
     * @return Collection
     */
    public function getReviews(int $classId): Collection;

    /**
     * Get syllabus for a class
     * 
     * @param int $classId
     * @return Collection
     */
    public function getSyllabus(int $classId): Collection;

    /**
     * Calculate average rating for a class
     * 
     * @param int $classId
     * @return float
     */
    public function calculateRating(int $classId): float;

    /**
     * Check if user is enrolled in a class
     * 
     * @param int $classId
     * @param int $userId
     * @return bool
     */
    public function isUserEnrolled(int $classId, int $userId): bool;
}
