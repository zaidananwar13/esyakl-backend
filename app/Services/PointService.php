<?php

namespace App\Services;

use App\Models\ClassStudent;
use App\Models\Kelas;
use App\Models\PointStudent;

class PointService
{
        private const POINT_BRACKETS = [
        [0, 20, 20],
        [21, 40, 40],
        [41, 60, 60],
        [61, 80, 80],
        [81, 100, 100],
    ];

        public function calculateAndSubmitPoints(int $userId): bool
    {
        $pendingPoints = PointStudent::where('id_user', $userId)
            ->where('status', 0)
            ->get();

        if ($pendingPoints->isEmpty()) {
            return false;
        }

        $success = false;

        foreach ($pendingPoints as $pointRecord) {
            $classStudent = ClassStudent::select('id_class')
                ->where('id_class_student', $pointRecord->id_class_student)
                ->first();

            if (!$classStudent) {
                continue;
            }

            $class = Kelas::select('point_reward')
                ->where('id_class', $classStudent->id_class)
                ->first();

            if (!$class) {
                continue;
            }

            $bracket = $this->getBracket($pointRecord->points);
            $finalPoints = ($bracket / 100) * $class->point_reward;

            $updated = PointStudent::where('id_point_student', $pointRecord->id_point_student)
                ->update([
                    'points' => $finalPoints,
                    'status' => 1,
                ]);

            if ($updated) {
                $success = true;
            }
        }

        return $success;
    }

        private function getBracket(int $points): int
    {
        foreach (self::POINT_BRACKETS as [$min, $max, $value]) {
            if ($points >= $min && $points <= $max) {
                return $value;
            }
        }

        // Default to 100 if above all brackets
        return 100;
    }

        public function getTotalPoints(int $userId): int
    {
        return (int) PointStudent::where('id_user', $userId)
            ->where('status', 1)
            ->sum('points');
    }

        public function getPendingPoints(int $userId)
    {
        return PointStudent::where('id_user', $userId)
            ->where('status', 0)
            ->get();
    }
}
