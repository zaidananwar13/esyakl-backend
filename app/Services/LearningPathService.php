<?php

namespace App\Services;

use App\Helpers\MediaHelper;
use App\Models\Kelas;
use App\Models\LearningPath;
use App\Models\LearningPathClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LearningPathService
{
    public function getAllPaths(string $lang = 'English'): array
    {
        $langId = $lang === 'English' ? 2 : 1;

        $paths = LearningPath::select('id_learning_path', 'name', 'desc')
            ->orderBy('id_learning_path', 'asc')
            ->get();

        $result = [];
        foreach ($paths as $path) {
            $classIds = LearningPathClass::select('id_class')
                ->where('id_learning_path', $path->id_learning_path)
                ->pluck('id_class')
                ->toArray();

            $classCount = DB::table('class')
                ->where('id_bahasa', $langId)
                ->where('status', 'publish')
                ->whereIn('id_class', $classIds)
                ->count();

            $result[] = [
                'title' => $path->name,
                'desc' => $path->desc,
                'classes' => $classCount,
                'link' => '/learning-path?id=' . $path->id_learning_path,
            ];
        }

        return $result;
    }

    public function getPathById(int $pathId, ?int $userId = null): ?array
    {
        $path = LearningPath::select('id_learning_path', 'name', 'desc', 'id_bahasa')
            ->where('id_learning_path', $pathId)
            ->first();

        if (!$path) {
            return null;
        }

        $learnedClasses = [];
        $completedClasses = [];

        if ($userId) {
            $learnedClasses = DB::table('class')
                ->join('class_student', 'class_student.id_class', '=', 'class.id_class')
                ->select('class.id_class')
                ->where('id_user', $userId)
                ->pluck('class.id_class')
                ->toArray();

            $completedClasses = DB::table('class')
                ->join('class_student', 'class_student.id_class', '=', 'class.id_class')
                ->select('class.id_class')
                ->where('id_user', $userId)
                ->whereNotNull('completed_at')
                ->pluck('class.id_class')
                ->toArray();
        }

        $pathClasses = DB::table('learning_path_class')
            ->join('learning_path', 'learning_path.id_learning_path', 'learning_path_class.id_learning_path')
            ->select('learning_path_class.id_class')
            ->where('learning_path.id_learning_path', $pathId)
            ->get();

        $classes = [];
        foreach ($pathClasses as $pc) {
            $kelas = Kelas::select('id_class as id_kelas', 'title as judul', 'desc as deskripsi_singkat', 'thumb as gambar')
                ->where('id_class', $pc->id_class)
                ->first();

            if (!$kelas) {
                continue;
            }

            $status = false;
            if ($userId && in_array($kelas->id_kelas, $learnedClasses)) {
                $status = in_array($kelas->id_kelas, $completedClasses) ? 'completed' : 'on-going';
            }

            $classes[] = [
                'judul' => $kelas->judul,
                'deskripsi_singkat' => $kelas->deskripsi_singkat,
                'link' => '/class?id=' . $kelas->id_kelas,
                'img' => MediaHelper::getMediaUrl($kelas->gambar),
                'status' => $status,
            ];
        }

        return [
            'path' => $path->name,
            'desc' => $path->desc,
            'classes' => $classes,
        ];
    }
}
