<?php

namespace App\Services;

use App\Helper;
use App\Models\ClassStudent;
use App\Models\FEAuthorizer;
use App\Models\Syllabus;
use App\Models\SyllabusStudent;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class AuthorizerService
{
        public function getUnlockedMaterial(int $userId, int $classId): array
    {
        $classStudent = ClassStudent::select('id_class_student')
            ->where('id_user', $userId)
            ->where('expired_at', '>=', Carbon::now()->format('U'))
            ->where('id_class', $classId)
            ->first();

        if (!$classStudent) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'You have no access to this class'
            ];
        }

        $history = DB::table("syllabus_student")
            ->join('class_student', 'class_student.id_class_student', '=', 'syllabus_student.id_class_student')
            ->where("syllabus_student.id_class_student", $classStudent->id_class_student)
            ->select('class_student.id_class', 'class_student.id_class_student', 'id_syllabus', 'syllabus_student.created_at')
            ->first();

        if (!$history) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'No syllabus history found'
            ];
        }

        $syllabus = Syllabus::select("order_number")
            ->where('id_syllabus', $history->id_syllabus)
            ->first();

        if (!$syllabus) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Syllabus not found'
            ];
        }

        return [
            'success' => true,
            'data' => [
                'id_class' => $classId,
                'unlocked' => $syllabus->order_number
            ]
        ];
    }

        public function unlockMaterial(int $userId, int $classId, int $material): array
    {
        $classStudent = ClassStudent::where('id_class', $classId)
            ->where('id_user', $userId)
            ->where('expired_at', '>=', Carbon::now()->format('U'))
            ->first();

        if (!$classStudent) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Unauthorized.'
            ];
        }

        $syllabusStudent = SyllabusStudent::where('id_class_student', $classStudent->id_class_student)->first();
        $syllabus = Syllabus::where('order_number', $material)
            ->where('id_class', $classId)
            ->first();

        if (!$syllabusStudent || !$syllabus) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Out of access'
            ];
        }

        try {
            $data = DB::table("syllabus_student")
                ->join('class_student', 'class_student.id_class_student', '=', 'syllabus_student.id_class_student')
                ->join('class', 'class.id_class', '=', 'class.id_class')
                ->select("class.id_class", "syllabus_student.id_syllabus")
                ->where('class.id_class', $classId)
                ->where('syllabus_student.id_syllabus_student', $syllabusStudent->id_syllabus_student)
                ->first();

            if (!$data) {
                return [
                    'success' => false,
                    'code' => 401,
                    'message' => 'Out of access'
                ];
            }

            if ($data->id_syllabus < $syllabus->id_syllabus) {
                $updated = DB::table("syllabus_student")
                    ->join('class_student', 'class_student.id_class_student', '=', 'syllabus_student.id_class_student')
                    ->join('class', 'class.id_class', '=', 'class.id_class')
                    ->select("class.id_class")
                    ->where('class.id_class', $classId)
                    ->where('syllabus_student.id_syllabus_student', $syllabusStudent->id_syllabus_student)
                    ->update([
                        'updated_at' => Carbon::now()->format('U'),
                        'syllabus_student.id_syllabus' => $syllabus->id_syllabus,
                    ]);

                if ($updated) {
                    Helper::recordUserActivity($userId, 'Unlock new chapter', $classId);
                    return [
                        'success' => true,
                        'message' => 'Success'
                    ];
                }
            }

            return [
                'success' => true,
                'message' => 'Cant update less than before'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Out of access'
            ];
        }
    }

        public function staticAuthorize(int $userId, int $classId, int $material): array
    {
        $feAuth = FEAuthorizer::select("id_kelas", "unlocked")
            ->where("id_kelas", $classId)
            ->where("id_user", $userId)
            ->first();

        if (!$feAuth) {
            $feAuth = new FEAuthorizer();
            $feAuth->id_user = $userId;
            $feAuth->id_kelas = $classId;
            $feAuth->unlocked = 1;
            $feAuth->save();
        } else {
            if ($material > $feAuth->unlocked) {
                FEAuthorizer::where("id_kelas", $classId)
                    ->where("id_user", $userId)
                    ->update([
                        "unlocked" => $material,
                        "updated_at" => Carbon::now(),
                    ]);
            }
        }

        return [
            'success' => true,
            'message' => 'Success'
        ];
    }
}
