<?php

namespace App\Services;

use App\Helper;
use App\Models\ClassStudent;
use App\Models\Kelas;
use App\Models\PointStudent;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Syllabus;
use App\Models\UserLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MyClassService
{
        public function getUserDashboard(int $userId, string $userName, string $userEmail, string $createdAt): array
    {
        $classProgress = ClassStudent::select('id_class_student', 'id_class as id_kelas', 'status_class')
            ->where('expired_at', '>=', Carbon::now()->format('U'))
            ->where('id_user', $userId)
            ->whereNull('completed_at')
            ->get();

        $classCompleted = ClassStudent::select('id_class as id_kelas')
            ->where('id_user', $userId)
            ->whereNotNull('completed_at')
            ->get();

        $progressClasses = $this->processProgressClasses($classProgress, $userId);
        $completedClasses = $this->processCompletedClasses($classCompleted, $userId);
        $expiredClasses = $this->processExpiredClasses($userId);
        $projects = $this->extractProjects($progressClasses['projects']);

        return [
            'name' => $userName,
            'email' => $userEmail,
            'joined' => 'Joined since ' . Helper::time_elapsed_string($createdAt),
            'class_on_progress' => count($classProgress),
            'completed_classes' => count($classCompleted),
            'class_progress' => $progressClasses['classes'],
            'class_completed' => $completedClasses,
            'class_expired' => $expiredClasses,
            'project' => $projects,
            'event_today' => [],
        ];
    }

        public function getUserProgress(int $userId): array
    {
        $todaySpent = $this->calculateTodaySpent($userId);
        $monthlySpent = $this->calculateMonthlySpent($userId);

        $completedClass = ClassStudent::where('id_user', $userId)
            ->whereNotNull('completed_at')
            ->count();

        $progressClass = ClassStudent::where('id_user', $userId)
            ->whereNull('completed_at')
            ->count();

        $score = PointStudent::where('status', 1)
            ->where('id_user', $userId)
            ->sum('points');

        return [
            'today_spent' => $todaySpent,
            'certificates' => $completedClass,
            'score' => $score,
            'total_courses' => $progressClass + $completedClass,
            'course' => [
                'in_progress' => $progressClass,
                'completed' => $completedClass,
                'yet_to_start' => 0,
            ],
            'monthly_spent' => $monthlySpent
        ];
    }

        public function processProgressClasses($classProgress, int $userId): array
    {
        $classes = [];
        $projects = [];

        foreach ($classProgress as $class) {
            $classData = Kelas::select('id_class as id_kelas', 'image as gambar', 'title as judul')
                ->where('id_class', $class['id_kelas'])
                ->first();

            if (!$classData) {
                continue;
            }

            $classTemp = $classData->toArray();
            $classTemp['status_class'] = $class->status_class ?? 'On';

            $history = DB::table('syllabus_student')
                ->join('class_student', 'class_student.id_class_student', '=', 'syllabus_student.id_class_student')
                ->where('syllabus_student.id_class_student', $class['id_class_student'])
                ->select('class_student.id_class', 'class_student.id_class_student', 'id_syllabus', 'syllabus_student.created_at')
                ->orderBy('class_student.id_class_student', 'DESC')
                ->first();

            $classTemp['last_material'] = "You haven't taken a syllabus yet";

            if ($history) {
                $syllabus = Syllabus::select('order_number')
                    ->where('id_syllabus', $history->id_syllabus)
                    ->first();
                $classTemp['last_material'] = $syllabus ? $syllabus->order_number : 0;
            }

            $projectData = $this->getProjectStatus($class['id_kelas'], $class['id_class_student']);
            $classTemp['project_class'] = $projectData['project_class'];
            if ($projectData['project']) {
                $projects[] = $projectData['project'];
            }

            if ($history) {
                $lastSyllabus = Syllabus::select('order_number')
                    ->where('id_class', $history->id_class)
                    ->orderBy('order_number', 'DESC')
                    ->first();

                $mySyllabus = Syllabus::select('order_number')
                    ->where('id_syllabus', $history->id_syllabus)
                    ->first();

                $classTemp['progress_class'] = [
                    $mySyllabus ? $mySyllabus->order_number : 0,
                    $lastSyllabus ? $lastSyllabus->order_number : 0
                ];

                $histories = DB::table('syllabus_student')
                    ->join('class_student', 'class_student.id_class_student', '=', 'syllabus_student.id_class_student')
                    ->where('syllabus_student.id_class_student', $class['id_class_student'])
                    ->select('class_student.expired_at')
                    ->first();

                if ($histories) {
                    $classTemp['expired'] = Carbon::createFromTimestamp($histories->expired_at, config('app.timezone'))->format('d M Y');
                }
            } else {
                $classTemp['progress_class'] = [0, 0];
                $classTemp['expired'] = 'N/A';
            }

            $classes[] = $classTemp;
        }

        return ['classes' => $classes, 'projects' => $projects];
    }

        public function processCompletedClasses($classCompleted, int $userId): array
    {
        $classes = [];

        foreach ($classCompleted as $class) {
            $completion = ClassStudent::select('completed_at')
                ->where('id_class', $class['id_kelas'])
                ->where('id_user', $userId)
                ->whereNotNull('completed_at')
                ->first();

            $classData = Kelas::select('id_class', 'image as gambar', 'title as judul')
                ->where('id_class', $class['id_kelas'])
                ->first();

            if ($classData && $completion) {
                $classTemp = $classData->toArray();
                $classTemp['date'] = Helper::time_elapsed_string($completion->completed_at);
                $classes[] = $classTemp;
            }
        }

        return $classes;
    }

        public function processExpiredClasses(int $userId): array
    {
        $classes = [];

        $expiredEnrollments = ClassStudent::select('id_class_student', 'expired_at')
            ->where('expired_at', '<=', Carbon::now()->format('U'))
            ->where('id_user', $userId)
            ->get();

        foreach ($expiredEnrollments as $enrollment) {
            $expiredClass = ClassStudent::select('id_class')
                ->where('id_class_student', $enrollment->id_class_student)
                ->where('id_user', $userId)
                ->whereNull('completed_at')
                ->first();

            if ($expiredClass) {
                $classData = Kelas::select('id_class', 'image as gambar', 'title as judul')
                    ->where('id_class', $expiredClass->id_class)
                    ->first();

                if ($classData) {
                    $classTemp = $classData->toArray();
                    // Legacy data has placeholder expiries (e.g. expired_at = 1). The Laravel 8 API
                    // showed today's date for those, never 1970-01-01.
                    $classTemp['date'] = ((int) $enrollment->expired_at) > 31536000
                        ? Carbon::createFromTimestamp($enrollment->expired_at, config('app.timezone'))->format('Y-m-d')
                        : Carbon::now()->format('Y-m-d');
                    $classes[] = $classTemp;
                }
            }
        }

        return $classes;
    }

        public function getProjectStatus(int $classId, int $classStudentId): array
    {
        $project = Project::select('id_project')
            ->where('id_class', $classId)
            ->first();

        if (!$project) {
            return ['project_class' => 'No Project Yet', 'project' => null];
        }

        $projectUser = ProjectUser::select('id_project', 'expired', 'poin')
            ->where('id_project', $project->id_project)
            ->where('id_class_student', $classStudentId)
            ->first();

        // Same as the Laravel 8 API: a class whose project the student has not started is
        // "No Project Yet" (a string the dashboard renders), not null.
        if (!$projectUser) {
            return ['project_class' => 'No Project Yet', 'project' => null];
        }

        $projectPass = DB::table('project')
            ->select('pass_poin')
            ->where('id_project', $projectUser->id_project)
            ->first();

        $passPoin = $projectPass ? $projectPass->pass_poin : config('app-settings.grading.project_pass_score', 80);

        $projectInfo = Project::select('title')
            ->where('id_project', $projectUser->id_project)
            ->first();

        $projectExpired = Carbon::createFromTimestamp($projectUser->expired, config('app.timezone'));
        $present = Carbon::now();

        $projectClass = [
            'id_class' => $classId,
            'id_project' => $projectUser->id_project,
            'title' => $projectInfo ? $projectInfo->title : 'Project',
            'status' => '',
            'due' => '',
        ];

        if ($projectExpired->toDateString() > $present->toDateString()) {
            $diff = (int) $projectExpired->diffInDays($present, true);

            $projectClass['status'] = 'on-progress';
            $projectClass['due'] = "$diff days";

            if ($projectUser->poin !== null) {
                $projectClass['status'] = $projectUser->poin >= $passPoin ? 'Complete' : 'failed';
            }

            if ($diff <= 7) {
                $projectClass['status'] = 'alert';
            } elseif ($diff == 1) {
                $projectClass['status'] = 'today';
                $projectClass['due'] = 'Today';
            }
        } else {
            $projectClass['status'] = 'expired';
            $projectClass['due'] = 'expired';
        }

        $projectClass['expired'] = $projectExpired->format('d F Y');

        return ['project_class' => $projectClass, 'project' => $projectClass];
    }

        public function extractProjects(array $projects): mixed
    {
        return count($projects) > 0 ? $projects : "You haven't taken a project yet";
    }

        // float, not string: the Laravel 8 API returned a JSON number (e.g. 0.9) and the
        // dashboard does arithmetic/formatting on it.
        public function calculateTodaySpent(int $userId): float
    {
        $userLogins = UserLog::where('id_user', $userId)
            ->where('status', true)
            ->orderBy('id_user_log', 'DESC')
            ->get();

        $today = Carbon::now()->format('d-m-Y');
        $loginContainer = [];

        foreach ($userLogins as $login) {
            $loginDate = Carbon::parse($login->time)->format('d-m-Y');
            if ($loginDate === $today) {
                $loginContainer[] = $login->time;
            }
        }

        $userLogin = count($loginContainer) > 0
            ? Carbon::parse($loginContainer[count($loginContainer) - 1])
            : Carbon::now();

        $userLog = UserLog::where('id_user', $userId)
            ->where('status', false)
            ->orderBy('id_user_log', 'DESC')
            ->first();

        $userLog = $userLog ? Carbon::parse($userLog->time) : Carbon::now();

        return Helper::secToHour((int) $userLogin->diffInSeconds($userLog, true));
    }

        public function calculateMonthlySpent(int $userId): array
    {
        $logins = UserLog::where('id_user', $userId)
            ->where('status', true)
            ->orderBy('id_user_log', 'asc')
            ->get();

        $months = [];
        $times = [];
        $temp = [];

        foreach ($logins as $log) {
            $month = Carbon::parse($log->time)->format('M');
            if (!in_array($month, $months)) {
                if (!empty($temp)) {
                    $times[] = $temp;
                }
                $temp = [];
                $months[] = $month;
            }
            $temp[] = $log->time;
        }
        if (!empty($temp)) {
            $times[] = $temp;
        }

        $timeValues = [];
        foreach ($times as $waktu) {
            if (count($waktu) < 1) {
                $timeValues[] = 0;
            } else {
                $first = Carbon::parse($waktu[0]);
                $last = Carbon::parse($waktu[count($waktu) - 1]);
                $timeValues[] = Helper::secToHour((int) $first->diffInSeconds($last, true)) / 10;
            }
        }

        $perBulan = [];
        for ($i = 0; $i < count($months); $i++) {
            $perBulan[$months[$i]] = $timeValues[$i] ?? 0;
        }

        $defaultMonths = ['Okt', 'Nov', 'Des'];
        foreach ($defaultMonths as $month) {
            if (!isset($perBulan[$month])) {
                $perBulan[$month] = 0;
            }
        }

        return $perBulan;
    }
}
