<?php

namespace App\Services;

use App\Helper as Help;
use App\Helpers\DateHelper;
use App\Helpers\MediaHelper;
use App\Models\ClassLecture;
use App\Models\ClassReview;
use App\Models\ClassStudent;
use App\Models\Kategori;
use App\Models\Kategori_Silabus;
use App\Models\Kelas;
use App\Models\KelasHistory;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Sub_Kategori_Silabus;
use App\Models\Syllabus;
use App\Models\SyllabusStudent;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClassService
{
    public function getClassDetails(int $classId, ?int $userId = null): ?array
    {
        $kelas = Kelas::select(
            'id_class as id_kelas',
            'owner as id_reviewer',
            'title as judul',
            'thumb as gambar',
            'image as thumb',
            'id_class_level as level',
            'desc as deskripsi_singkat',
            'desc as deskripsi_kelas',
            'duration',
            'type',
            'price'
        )
            ->where('id_class', $classId)
            ->first();

        if (!$kelas) {
            return null;
        }

        $data = $kelas->toArray();
        $data['langkah'] = Kategori_Silabus::where('id_class', $classId)->count();
        
        if ($userId) {
            $enrollment = ClassStudent::where('id_class', $classId)
                ->where('id_user', $userId)
                ->first();
            $data['status'] = $enrollment ? 'registered' : 'unregistered';
            
            if ($data['status'] === 'registered') {
                unset($data['price']);
            }
        }

        $data['gambar'] = MediaHelper::getMediaUrl($data['gambar']);
        $data['thumb'] = MediaHelper::getMediaUrl($data['thumb']);
        $data['jumlah_user'] = ClassStudent::where('id_class', $classId)->count();

        $reviewData = $this->getClassReviewData($classId);
        $data['rating'] = $reviewData['rating'];
        $data['komentar'] = $reviewData['reviews'];
        $data['type'] = $data['type'] ?? 'free';
        $data['silabus'] = $this->getClassSyllabus($classId);
        $data['durasi'] = $data['duration'];
        unset($data['duration']);

        return $data;
    }

    public function getClassList(): array
    {
        $classes = Kelas::select(
            'id_class as id_kelas',
            'owner as id_reviewer',
            'title as judul',
            'thumb as gambar',
            'image as thumb',
            'id_class_level as level',
            'desc as deskripsi_singkat',
            'desc as deskripsi_kelas',
            'type as classType'
        )->get();

        $result = [];

        foreach ($classes as $kelas) {
            $data = $kelas->toArray();
            $data['langkah'] = Syllabus::where('id_class', $data['id_kelas'])->count();

            $kategori = Kategori::select('level as judul')
                ->where('id_class_level', $data['level'])
                ->first();

            if (!$kategori) {
                continue;
            }

            $data['kategori'] = $kategori->judul;
            unset($data['id_kategori']);
            $data['classType'] = $data['classType'] ?? 'free';
            $data['gambar'] = MediaHelper::getMediaUrl($data['gambar']);
            $data['thumb'] = MediaHelper::getMediaUrl($data['thumb']);
            $data['jumlah_user'] = ClassStudent::where('id_class', $data['id_kelas'])->count();

            $reviewData = $this->getClassReviewData($data['id_kelas']);
            $data['rating'] = $reviewData['rating'];
            $data['komentar'] = $reviewData['reviews'];

            $learningPath = DB::table('learning_path_class')
                ->join('learning_path', 'learning_path.id_learning_path', '=', 'learning_path_class.id_learning_path')
                ->select('learning_path.name')
                ->where('id_class', $data['id_kelas'])
                ->first();

            $data['learningPath'] = $learningPath ? $learningPath->name : 'Unsorted';
            $data['silabus'] = $this->getClassSyllabus($data['id_kelas']);

            $result[] = $data;
        }

        return $result;
    }

    public function enrollUserToClass(int $classId, int $userId, bool $isPremium = false): array
    {
        $kelas = Kelas::where('id_class', $classId)->first();
        if (!$kelas) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Illegal Access'
            ];
        }

        $existingEnrollment = $this->getActiveEnrollment($classId, $userId);
        if ($existingEnrollment) {
            return [
                'success' => false,
                'code' => 409,
                'message' => 'Already Registered!'
            ];
        }

        if (!$isPremium && $kelas->type === 'premium') {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Illegal Access'
            ];
        }

        $now = DateHelper::nowTimestamp();
        $expiryDays = $kelas->duration ?? config('app-settings.class.default_expiry_days', 60);
        $expired = DateHelper::addDaysTimestamp($expiryDays);

        $classStudent = ClassStudent::create([
            'id_user' => $userId,
            'id_class' => $classId,
            'completed_at' => null,
            'status_class' => 'On',
            'expired_at' => $expired,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if (!$classStudent) {
            return [
                'success' => false,
                'code' => 500,
                'message' => 'Failed to enroll'
            ];
        }

        $firstSyllabus = Syllabus::select('id_syllabus')
            ->where('id_class', $classId)
            ->orderBy('order_number', 'asc')
            ->first();

        if ($firstSyllabus) {
            SyllabusStudent::create([
                'id_class_student' => $classStudent->id_class_student,
                'id_syllabus' => $firstSyllabus->id_syllabus,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Class register success!',
            'class_student' => $classStudent
        ];
    }

    public function getActiveEnrollment(int $classId, int $userId): ?ClassStudent
    {
        $now = DateHelper::nowTimestamp();
        
        return ClassStudent::where('id_class', $classId)
            ->where('id_user', $userId)
            ->where('expired_at', '>=', $now)
            ->first();
    }

    public function checkEnrollment(int $classId, int $userId): array
    {
        $enrollment = ClassStudent::where('id_class', $classId)
            ->where('id_user', $userId)
            ->orderBy('id_class_student', 'desc')
            ->first();

        if (!$enrollment) {
            return [
                'enrolled' => false,
                'code' => 1919,
                'message' => "Haven't enlisted yet"
            ];
        }

        $now = DateHelper::nowTimestamp();
        if ($enrollment->expired_at < $now) {
            return [
                'enrolled' => true,
                'expired' => true,
                'code' => 403,
                'message' => 'expired'
            ];
        }

        return [
            'enrolled' => true,
            'expired' => false,
            'code' => 409,
            'message' => 'Already registered'
        ];
    }

    public function calculateClassProgress(int $classStudentId): array
    {
        $classStudent = ClassStudent::with(['syllabusStudents', 'class.syllabuses'])
            ->find($classStudentId);

        if (!$classStudent) {
            return [
                'progress' => 0,
                'completed' => 0,
                'total' => 0
            ];
        }

        $totalSyllabi = $classStudent->class->syllabuses->count();
        $completedSyllabi = $classStudent->syllabusStudents->count();

        $progress = $totalSyllabi > 0 
            ? round(($completedSyllabi / $totalSyllabi) * 100, 2) 
            : 0;

        return [
            'progress' => $progress,
            'completed' => $completedSyllabi,
            'total' => $totalSyllabi
        ];
    }

    private function getClassSyllabus(int $classId)
    {
        return DB::table('syllabus')
            ->select('id_syllabus', 'title as judul')
            ->where('id_class', $classId)
            ->orderBy('order_number', 'asc')
            ->get();
    }

    private function getClassReviewData(int $classId): array
    {
        $reviews = DB::table('class_review')
            ->join('class_student', 'class_student.id_class_student', '=', 'class_review.id_class_student')
            ->select(
                'class_review.id_class_review',
                'class_student.id_user',
                'class_student.id_class',
                'class_review.star',
                'class_review.content',
                'class_review.created_at as time',
                'class_review.created_at as date'
            )
            ->where('id_class', $classId)
            ->get();

        $totalRating = 0;
        $formattedReviews = [];

        foreach ($reviews as $review) {
            $user = DB::table('user')
                ->select('name', 'avatar_original')
                ->where('id_user', $review->id_user)
                ->first();

            $totalRating += $review->star;

            $formattedReviews[] = [
                'id_class_review' => $review->id_class_review,
                'id_user' => $review->id_user,
                'id_class' => $review->id_class,
                'star' => $review->star,
                'nama' => $user->name ?? 'Unknown',
                'img' => $user->avatar_original ?? 'user.png',
                'content' => $review->content, // legacy field name, still read by the frontend
                'comment' => $review->content,
                'rating' => $review->star,
                'time' => DateHelper::formatTime($review->time),
                'date' => \Illuminate\Support\Carbon::parse($review->date)->format('M d, Y'),
            ];
        }

        $reviewCount = count($reviews);
        $averageRating = $reviewCount > 0 
            ? (float) number_format($totalRating / $reviewCount, 2) 
            : 0;

        return [
            'rating' => $averageRating,
            'reviews' => $formattedReviews
        ];
    }

    public function completeClass(int $classId, int $userId): array
    {
        $now = DateHelper::nowTimestamp();

        $classStudent = DB::table('class_student')
            ->join('class', 'class.id_class', '=', 'class_student.id_class')
            ->join('user', 'user.id_user', '=', 'class_student.id_user')
            ->select(
                'class_student.id_class_student',
                'class_student.completed_at',
                'class.id_class',
                'class.title',
                'user.id_user',
                'user.name'
            )
            ->where('class.id_class', $classId)
            ->where('user.id_user', $userId)
            ->where('expired_at', '>=', $now)
            ->orderBy('id_class_student')
            ->first();

        if (!$classStudent) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Class enrollment not found'
            ];
        }

        if ($classStudent->completed_at !== null) {
            return [
                'success' => false,
                'code' => 200,
                'message' => 'Class Already Completed!'
            ];
        }

        DB::table('class_student')
            ->where('id_class_student', $classStudent->id_class_student)
            ->update(['completed_at' => $now]);

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Class Completed!',
            'class_student' => $classStudent
        ];
    }

    /**
     * Search classes by keyword
     * SECURITY FIX: Using parameterized queries to prevent SQL injection
     */
    public function searchClasses(string $keyword): array
    {
        // Sanitize and prepare keyword
        $keyword = trim(strtolower($keyword));
        if (empty($keyword)) {
            return [];
        }

        // Split into words and filter empty values
        $keywords = array_filter(explode(' ', $keyword), fn($k) => strlen(trim($k)) > 0);
        if (empty($keywords)) {
            return [];
        }

        $query = Kelas::query();

        foreach ($keywords as $key) {
            // Escape special LIKE characters and use parameterized query
            $searchTerm = '%' . addcslashes(trim($key), '%_\\') . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER("desc") LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(CAST(id_class_level AS TEXT)) LIKE ?', [$searchTerm]);
            });
        }

        return $query->get()->toArray();
    }

    /**
     * Filter classes by given rules
     * SECURITY FIX: Using parameterized queries and input validation
     */
    public function filterClasses(array $filters, ?string $keywords = null): array
    {
        $query = Kelas::query();

        // Apply keyword search with parameterized queries
        if ($keywords !== null && !empty(trim($keywords))) {
            $keywords = trim(strtolower($keywords));
            $searchTerm = '%' . addcslashes($keywords, '%_\\') . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER("desc") LIKE ?', [$searchTerm]);
            });
        }

        // Apply filters safely with whitelist validation
        $allowedFilterFields = ['type', 'id_class_level', 'owner'];

        if (isset($filters['rules']) && is_array($filters['rules'])) {
            foreach ($filters['rules'] as $field => $value) {
                // Only allow whitelisted fields
                if (in_array($field, $allowedFilterFields, true)) {
                    $query->where($field, '=', $value);
                }
            }
        }

        return $query->get()->toArray();
    }

    /**
     * Submit a review for a class
     */
    public function submitReview(int $userId, int $classId, int $star, string $comment): array
    {
        $classStudent = ClassStudent::select('id_class_student')
            ->where('id_user', $userId)
            ->where('id_class', $classId)
            ->first();

        if ($classStudent === null) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Access Prohibited'
            ];
        }

        $review = new ClassReview();
        $review->id_class_student = $classStudent->id_class_student;
        $review->star = $star;
        $review->content = $comment;

        if ($review->save()) {
            return [
                'success' => true,
                'code' => 200,
                'message' => 'Review Submitted'
            ];
        }

        return [
            'success' => false,
            'code' => 500,
            'message' => 'Failed to submit review'
        ];
    }

    /**
     * Update class history/progress
     */
    public function updateClassHistory(int $userId, int $syllabusId): array
    {
        $silabus = Kategori_Silabus::select("id_kategori_silabus", "id_kelas")
            ->where("id_kategori_silabus", $syllabusId)
            ->first();

        if ($silabus === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized'
            ];
        }

        // Check if this is the last syllabus, create project if needed
        $temp = Kategori_Silabus::select("id_kategori_silabus", "id_kelas")
            ->orderBy('id_kategori_silabus', 'desc')
            ->first();

        if ($syllabusId >= $temp->id_kategori_silabus) {
            $projectCheck = Project::where("id_kelas", $temp->id_kelas)->first();
            if ($projectCheck !== null) {
                $existingProject = ProjectUser::where('id_user', $userId)
                    ->where('id_project', $projectCheck->id_project)
                    ->first();

                if ($existingProject === null) {
                    $project = new ProjectUser();
                    $project->id_user = $userId;
                    $project->id_project = $projectCheck->id_project;
                    $project->note = "";
                    $project->file = "";
                    $project->point = 0;
                    $project->expired = strtotime(Carbon::now()->addDays(60));
                    $project->save();
                }
            }
        }

        $history = KelasHistory::where("id_user", $userId)
            ->where("id_kelas", $silabus->id_kelas)
            ->first();

        $materi = Sub_Kategori_Silabus::select("id_sub_kategori_silabus")
            ->where("id_kategori_silabus", $syllabusId)
            ->first();

        if ($history === null) {
            $history = new KelasHistory();
            $history->id_user = $userId;
            $history->id_kelas = $silabus->id_kelas;
            $history->id_kategori_silabus = $silabus->id_kategori_silabus;
            $history->id_sub_kategori_silabus = $materi->id_sub_kategori_silabus;
            $history->save();
        } else {
            $history->update([
                "id_kategori_silabus" => $silabus->id_kategori_silabus,
                "id_sub_kategori_silabus" => $materi->id_sub_kategori_silabus,
                "id_kelas" => $silabus->id_kelas,
            ]);
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Update class success!'
        ];
    }

    /**
     * Rate a lecture
     */
    public function rateLecture(int $userId, int $classId, string $postType, int $postId, string $status): array
    {
        $result = ClassLecture::updateOrCreate(
            [
                'id_class' => $classId,
                'id_user' => $userId,
            ],
            [
                'postType' => $postType,
                'id_post' => $postId,
                'id_user' => $userId,
                'status' => $status,
            ]
        );

        if ($result) {
            Help::recordUserActivity($userId, 'Unlock new chapter', $classId);
            return [
                'success' => true,
                'code' => 200,
                'message' => 'Review Submitted'
            ];
        }

        return [
            'success' => false,
            'code' => 500,
            'message' => 'Oops something went wrong!'
        ];
    }

    /**
     * Get checkout info for a class
     */
    public function getCheckoutInfo(int $userId, int $classId): array
    {
        $classStudent = ClassStudent::select('id_class_student')
            ->where('id_user', $userId)
            ->where('id_class', $classId)
            ->where('expired_at', '>', Carbon::now()->format('U'))
            ->first();

        if ($classStudent !== null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'You already have access to this class!'
            ];
        }

        $class = Kelas::select('id_class', 'title', 'price')
            ->where('id_class', $classId)
            ->first();

        if ($class === null) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Class not found'
            ];
        }

        $class->langkah = Kategori_Silabus::where('id_class', $class->id_class)->count();

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Checkout info retrieved',
            'data' => $class
        ];
    }
}
