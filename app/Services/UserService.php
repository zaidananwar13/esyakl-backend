<?php

namespace App\Services;

use App\Helper;
use App\Models\Bahasa;
use App\Models\CertificateStudent;
use App\Models\ClassLecture;
use App\Models\ClassReview;
use App\Models\ClassStudent;
use App\Models\Comment;
use App\Models\Country;
use App\Models\ForgotPassword;
use App\Models\Kelas;
use App\Models\LearningPathClass;
use App\Models\LectureRating;
use App\Models\PointStudent;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Syllabus;
use App\Models\SyllabusStudent;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Get all users
     */
    public function getAllUsers(): array
    {
        return User::all()->toArray();
    }

    /**
     * Register new user
     */
    public function registerUser(string $email, string $password): array
    {
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            return [
                'success' => false,
                'code' => 409,
                'message' => 'Email sudah terdaftar'
            ];
        }

        $name = explode('@', $email)[0];

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->google_id = "default";
        $user->password = bcrypt($password);
        $user->api_token = hash('sha256', Str::random(60));
        $user->save();

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Kamu berhasil Register'
        ];
    }

    /**
     * Get user profile with classes, projects, and progress
     */
    public function getProfile(string $token): array
    {
        if ($token === null) {
            return [
                'success' => false,
                'code' => 403,
                'message' => 'Access Forbidden! What are you looking for?'
            ];
        }

        $user = User::select("id_user", "name", "email", "created_at")
            ->where("api_token", $token)
            ->first();

        if ($user === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized'
            ];
        }

        $user = $user->toArray();

        $classProgress = ClassStudent::select("id_class_student", "id_class as id_kelas", "status_class")
            ->where('expired_at', '>=', Carbon::now()->format('U'))
            ->where("id_user", $user["id_user"])
            ->where("completed_at", null)->get();

        $classCompleted = ClassStudent::select("id_class as id_kelas")
            ->where("id_user", $user["id_user"])
            ->where("completed_at", "!=", null)->get();

        $projects = [];
        $temp = [];

        if (count($classProgress) > 0) {
            foreach ($classProgress as $class) {
                $classTemp = Kelas::select("id_class as id_kelas", "image as gambar", "title as judul")
                    ->where("id_class", $class["id_kelas"])
                    ->first();

                if ($classTemp === null) continue;

                $classTemp = $classTemp->toArray();
                $classTemp['status_class'] = $class->status_class ?? 'On';

                $history = DB::table("syllabus_student")
                    ->join('class_student', 'class_student.id_class_student', '=', 'syllabus_student.id_class_student')
                    ->where("syllabus_student.id_class_student", $class["id_class_student"])
                    ->select('class_student.id_class', 'class_student.id_class_student', 'id_syllabus', 'syllabus_student.created_at')
                    ->orderBy("class_student.id_class_student", 'DESC')
                    ->first();

                $classTemp["last_material"] = "You haven't taken a syllabus yet";

                if ($history !== null) {
                    $syllabus = Syllabus::select("order_number")->where('id_syllabus', $history->id_syllabus)->first();
                    if ($syllabus) {
                        $classTemp["last_material"] = $syllabus->order_number;
                    }
                }

                $project = Project::select("id_project")->where("id_class", $class["id_kelas"])->first();
                $classProject = null;

                if ($project !== null) {
                    $classProject = ProjectUser::select("id_project", "expired", "poin")
                        ->where("id_project", $project->id_project)
                        ->where("id_class_student", $class["id_class_student"])->first();

                    if ($classProject !== null) {
                        $classProject->expired = Carbon::createFromTimestamp($classProject->expired);
                    }
                }

                $classTemp["project_class"] = $classProject;

                if ($classProject !== null) {
                    $projectPoint = DB::table('project')->select('pass_poin')
                        ->where('id_project', $classProject["id_project"])
                        ->first();
                    $projectPoint = $projectPoint->pass_poin ?? 80;

                    $projectID = Project::select("title")->where("id_project", $classProject["id_project"])->first();
                    $classTemp["project_class"]["id_class"] = $classTemp["id_kelas"];
                    $classTemp["project_class"]["title"] = $projectID->title;
                    $classTemp["project_class"]["status"] = "";
                    $classTemp["project_class"]["due"] = "";

                    $present = Carbon::now();
                    $due = $classProject->expired;

                    if ($due->toDateString() > $present->toDateString()) {
                        $diff = (int) $due->diffInDays($present, true);
                        $classTemp["project_class"]["status"] = "on-progress";
                        $classTemp["project_class"]["due"] = "$diff days";

                        if ($classProject->poin !== null) {
                            $classTemp["project_class"]["status"] = ($classProject->poin >= $projectPoint) ? "Complete" : "failed";
                        }

                        if ($diff <= 7) {
                            $classTemp["project_class"]["status"] = "alert";
                        } elseif ($diff == 1) {
                            $classTemp["project_class"]["status"] = "today";
                            $classTemp["project_class"]["due"] = "Today";
                        }
                    } else {
                        $classTemp["project_class"]["status"] = "expired";
                        $classTemp["project_class"]["due"] = "expired";
                    }

                    $classProject->expired = $classProject->expired->format('d F Y');
                    $projects[] = $classTemp["project_class"];
                } else {
                    $classTemp["project_class"] = "No Project Yet";
                }

                $histories = DB::table("syllabus_student")
                    ->join('class_student', 'class_student.id_class_student', '=', 'syllabus_student.id_class_student')
                    ->where("syllabus_student.id_class_student", $class["id_class_student"])
                    ->select('class_student.id_class', 'class_student.expired_at', 'class_student.id_class_student', 'id_syllabus', 'syllabus_student.created_at')
                    ->orderBy("class_student.id_class_student", 'DESC')
                    ->first();

                if ($histories !== null) {
                    $syls = Syllabus::select("order_number")->where('id_class', $histories->id_class)->orderBy('order_number', 'DESC')->first();
                    $mySyls = Syllabus::select("order_number")->where('id_syllabus', $histories->id_syllabus)->first();

                    if ($syls && $mySyls) {
                        $classTemp["progress_class"] = [$mySyls->order_number, $syls->order_number];
                    }

                    $classTemp["expired"] = Carbon::createFromTimestamp($histories->expired_at)->format('d M Y');
                }

                $temp[] = $classTemp;
            }
        }

        $temp2 = [];
        if (count($classCompleted) > 0) {
            foreach ($classCompleted as $class) {
                $compt = ClassStudent::select("completed_at")
                    ->where("id_class", $class["id_kelas"])
                    ->where("id_user", $user["id_user"])
                    ->whereNotNull("completed_at")
                    ->first();

                $classTemp = Kelas::select("id_class", "image as gambar", "title as judul")
                    ->where("id_class", $class["id_kelas"])
                    ->first();

                if ($classTemp && $compt) {
                    $classTemp = $classTemp->toArray();
                    $classTemp["date"] = Helper::time_elapsed_string($compt->completed_at);
                    $temp2[] = $classTemp;
                }
            }
        }

        $temp3 = [];
        $expiredClass = ClassStudent::select('id_class_student', 'expired_at')
            ->where('expired_at', '<=', Carbon::now()->format('U'))
            ->where('id_user', $user['id_user'])->get();

        foreach ($expiredClass as $class) {
            $expt = ClassStudent::select("id_class")
                ->where("id_class_student", $class->id_class_student)
                ->where("id_user", $user["id_user"])
                ->whereNull("completed_at")
                ->first();

            if ($expt !== null) {
                $classTemp = Kelas::select("id_class", "image as gambar", "title as judul")
                    ->where("id_class", $expt->id_class)
                    ->first();

                if ($classTemp) {
                    $classTemp = $classTemp->toArray();
                    $classTemp["date"] = Carbon::parse($expt->expired ?? now())->format('Y-m-d');
                    $temp3[] = $classTemp;
                }
            }
        }

        $projects = (count($projects) > 0) ? $projects : "You haven't taken a project yet";

        $profile = [
            "name" => $user["name"],
            "email" => $user["email"],
            "joined" => "Joined since " . Helper::time_elapsed_string($user["created_at"]),
            "class_on_progress" => count($classProgress),
            "completed_classes" => count($classCompleted),
            "class_progress" => $temp,
            "class_completed" => $temp2,
            "class_expired" => $temp3,
            "project" => $projects,
            "event_today" => [],
        ];

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Ok.',
            'data' => $profile
        ];
    }

    /**
     * Get all mentors
     */
    public function getMentors(): array
    {
        $mentors = User::select('name', 'current_job', 'avatar_original as foto', 'type as jabatan', 'social_media')
            ->where('type', 'teacher')
            ->get();

        foreach ($mentors as $mentor) {
            $mentor->social_media = json_decode($mentor->social_media);
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Ok.',
            'data' => $mentors
        ];
    }

    /**
     * Get user profile details for settings
     */
    public function getUserProfile(string $token): array
    {
        $user = User::select('id_user', 'name', 'username', 'about', 'email', 'phone_number', 'avatar_original as profile', 'region as country', 'id_bahasa as language', 'password', 'created_at as joined_at')
            ->where('api_token', $token)
            ->first();

        if ($user === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized'
            ];
        }

        // 'password' is in User::$hidden, so read it before toArray() drops it.
        $password = $user->password;
        $user = $user->toArray();
        $idUser = $user['id_user'];
        $user['joined_at'] = Carbon::createFromTimestamp($user['joined_at'])->format('M d, Y, g:i:s A');
        unset($user['id_user']);

        $language = Bahasa::select('id_bahasa as id', 'bahasa as language')->get();
        foreach ($language as $lang) {
            $lang->status = ($lang->id == $user['language']);
        }
        $user['language'] = $language->toArray();
        $user['is_password'] = ($password !== null);
        unset($user['password']);

        $countries = Country::select('id_country as id', 'country')->get();
        foreach ($countries as $country) {
            $country->status = ($country->id == $user['country']);
        }
        $user['country'] = $countries->toArray();

        $temps = ClassStudent::select("id_class as class", "completed_at", "status_class")
            ->where("id_user", $idUser)
            ->where("expired_at", ">=", Carbon::now()->format('U'))
            ->get();

        $courses = [];
        foreach ($temps as $temp) {
            $class = Kelas::select("id_class as class", "title")->where('id_class', $temp->class)->first();
            if ($class) {
                $class = $class->toArray();
                $class['status'] = ($temp->completed_at !== null) ? 'Completed' : 'Active';
                $class['status_class'] = $temp->status_class;
                $courses[] = $class;
            }
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Ok. User Data Retrieved',
            'data' => [
                "my-profile" => $user,
                "courses" => $courses,
            ]
        ];
    }

    /**
     * Update user profile
     * SECURITY FIX: Added proper file validation for uploads
     */
    public function updateUserProfile(string $token, array $data, $image = null): array
    {
        $user = User::where('api_token', $token)->first();

        if ($user === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized'
            ];
        }

        // Check email uniqueness if changed
        if ($user->email !== $data['email']) {
            if (User::where('email', $data['email'])->exists()) {
                return [
                    'success' => false,
                    'code' => 409,
                    'message' => 'Email is already taken'
                ];
            }
        }

        $updateData = [
            'name' => $data['name'],
            'username' => $data['username'] ?? null,
            'about' => $data['about'] ?? null,
            'email' => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'region' => $data['country'] ?? null,
            'id_bahasa' => $data['language'],
        ];

        // SECURITY FIX: Validate uploaded image
        if ($image) {
            $validationResult = $this->validateProfileImage($image);
            if (!$validationResult['valid']) {
                return [
                    'success' => false,
                    'code' => 422,
                    'message' => $validationResult['message']
                ];
            }

            // Use random filename to prevent enumeration
            $extension = strtolower($image->getClientOriginalExtension());
            $filePath = Str::uuid() . '.' . $extension;
            $destinationPath = 'user-profile/';

            $image->move($destinationPath, $filePath);
            $updateData['avatar_original'] = URL::to('/' . $destinationPath . $filePath);
        }

        if (isset($data['profile']) && $data['profile'] === 'default') {
            $updateData['avatar_original'] = URL::to('/user-profile/user.png');
        }

        if (!User::where('api_token', $token)->update($updateData)) {
            return [
                'success' => false,
                'code' => 500,
                'message' => 'Failed to update profile'
            ];
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Profile updated successfully',
            'data' => null
        ];
    }

    /**
     * Validate profile image upload
     * SECURITY: Validates MIME type, extension, and file size
     */
    private function validateProfileImage($image): array
    {
        // Allowed MIME types for images
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ];

        // Allowed extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Max file size (2MB)
        $maxSize = 2 * 1024 * 1024;

        // Check file size
        if ($image->getSize() > $maxSize) {
            return [
                'valid' => false,
                'message' => 'Image file size must not exceed 2MB'
            ];
        }

        // Check MIME type
        $mimeType = $image->getMimeType();
        if (!in_array($mimeType, $allowedMimes, true)) {
            return [
                'valid' => false,
                'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed'
            ];
        }

        // Check extension
        $extension = strtolower($image->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions, true)) {
            return [
                'valid' => false,
                'message' => 'Invalid file extension'
            ];
        }

        // Verify it's actually an image by trying to get dimensions
        $imageInfo = @getimagesize($image->getPathname());
        if ($imageInfo === false) {
            return [
                'valid' => false,
                'message' => 'File does not appear to be a valid image'
            ];
        }

        return ['valid' => true, 'message' => 'Valid image'];
    }

    /**
     * Update user password
     * SECURITY FIX: Added password confirmation validation and stronger requirements
     */
    public function updatePassword(string $token, string $password, string $passwordConfirm, ?string $oldPassword = null): array
    {
        // SECURITY FIX: Validate password confirmation matches
        if ($password !== $passwordConfirm) {
            return [
                'success' => false,
                'code' => 422,
                'message' => 'Passwords do not match'
            ];
        }

        // SECURITY FIX: Stronger password requirements (minimum 8 characters)
        if (strlen($password) < 8) {
            return [
                'success' => false,
                'code' => 422,
                'message' => 'Password must be at least 8 characters long'
            ];
        }

        // Check for at least one uppercase, one lowercase, and one number
        if (!preg_match('/[a-z]/', $password) ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[0-9]/', $password)) {
            return [
                'success' => false,
                'code' => 422,
                'message' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number'
            ];
        }

        $user = User::where('api_token', $token)->first();

        if ($user === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized'
            ];
        }

        // If user has existing password, verify old password
        if ($user->password !== null) {
            if ($oldPassword === null) {
                return [
                    'success' => false,
                    'code' => 422,
                    'message' => 'Current password is required'
                ];
            }

            if (!Hash::check($oldPassword, $user->password)) {
                return [
                    'success' => false,
                    'code' => 422,
                    'message' => 'Current password is incorrect'
                ];
            }
        }

        User::where('api_token', $token)->update(['password' => bcrypt($password)]);

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Password updated successfully',
            'data' => null
        ];
    }

    /**
     * Update user course status
     */
    public function updateCourseStatus(string $token, int $classId, string $status): array
    {
        $user = User::where('api_token', $token)->first();

        if ($user === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized'
            ];
        }

        $updated = ClassStudent::where('id_user', $user->id_user)
            ->where('id_class', $classId)
            ->update(['status_class' => $status]);

        if (!$updated) {
            return [
                'success' => false,
                'code' => 500,
                'message' => 'Something went wrong :(.'
            ];
        }

        $helperMessage = ($status != 'Off') ? 'Activate Class' : 'Deactivate Class';
        Helper::recordUserActivity($user->id_user, $helperMessage, $classId);

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Ok. Update course successfull',
            'data' => null
        ];
    }

    /**
     * Generate forgot password email
     * SECURITY FIX: Added token expiration
     */
    public function generateForgotPasswordMail(string $email): array
    {
        $user = User::where('email', $email)->first();

        if ($user === null) {
            // SECURITY: Return generic message to prevent email enumeration
            return [
                'success' => true,
                'code' => 200,
                'message' => 'If this email exists, a password reset link will be sent.',
                'data' => null
            ];
        }

        // SECURITY FIX: Invalidate any existing reset tokens for this user
        ForgotPassword::where('id_user', $user->id_user)->delete();

        $token = hash('sha256', Str::random(60));
        $expiryMinutes = config('app-settings.password_reset.expiry_minutes', 30);

        $forgotPassword = new ForgotPassword();
        $forgotPassword->token = $token;
        $forgotPassword->id_user = $user->id_user;
        $forgotPassword->expired_at = Carbon::now()->addMinutes($expiryMinutes)->format('U');
        $forgotPassword->save();

        $frontendUrl = config('app-settings.frontend_url', 'https://arabic-go.com');
        $link = $frontendUrl . '/new-password?token=' . $token;

        Mail::to($user->email)->send(new \App\Mail\ForgotPassword([
            'mailed' => $user->name,
            'content' => "Please click the link below within $expiryMinutes minutes to reset your password",
            'link' => $link,
        ]));

        return [
            'success' => true,
            'code' => 200,
            'message' => 'If this email exists, a password reset link will be sent.',
            'data' => null
        ];
    }

    /**
     * Set new password from forgot password token
     * SECURITY FIX: Added token expiration validation
     */
    public function setNewPassword(string $token, string $password): array
    {
        // Validate token format
        if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Invalid token format'
            ];
        }

        // SECURITY FIX: Check token with expiration
        $verifier = ForgotPassword::where('token', $token)
            ->where('expired_at', '>', Carbon::now()->format('U'))
            ->first();

        if ($verifier === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Invalid or expired token'
            ];
        }

        // Validate new password strength
        if (strlen($password) < 8) {
            return [
                'success' => false,
                'code' => 422,
                'message' => 'Password must be at least 8 characters long'
            ];
        }

        $updated = User::where('id_user', $verifier->id_user)->update([
            'password' => bcrypt($password),
        ]);

        // SECURITY FIX: Delete token after successful use
        $verifier->delete();

        if ($updated) {
            return [
                'success' => true,
                'code' => 200,
                'message' => 'Password updated successfully',
                'data' => null
            ];
        }

        return [
            'success' => false,
            'code' => 500,
            'message' => 'Failed to update password'
        ];
    }

    /**
     * Get user learning progress statistics
     */
    public function getProgress(string $token): array
    {
        $user = User::select("id_user")
            ->where("api_token", $token)
            ->first();

        if ($user === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized'
            ];
        }

        // Submit point calculation
        \App\Http\Controllers\API\PointStudentController::submitPoint($user->id_user);

        $userLogin = UserLog::where('id_user', $user->id_user)
            ->where('status', true)
            ->orderBy('id_user_log', 'DESC')
            ->get();

        $today = Carbon::now()->format('d-m-Y');
        $loginContainer = [];

        foreach ($userLogin as $login) {
            $loginTemp = Carbon::parse($login->time)->format('d-m-Y');
            if ($loginTemp == $today) {
                $loginContainer[] = $login->time;
            }
        }

        $userLoginTime = count($loginContainer) > 0
            ? Carbon::parse($loginContainer[count($loginContainer) - 1])
            : Carbon::now();

        $userLog = UserLog::where('id_user', $user->id_user)
            ->where('status', false)
            ->orderBy('id_user_log', 'DESC')
            ->first();

        $userLogTime = $userLog ? Carbon::parse($userLog->time) : Carbon::now();
        $userLifeTime = Helper::secToHour((int) $userLoginTime->diffInSeconds($userLogTime, true));

        $login = UserLog::where('id_user', $user->id_user)
            ->where('status', true)
            ->orderBy('id_user_log', 'asc')
            ->get();

        $months = [];
        $times = [];
        $temp = [];

        foreach ($login as $log) {
            $time = Carbon::parse($log->time)->format('M');
            if (!in_array($time, $months)) {
                $times[] = $temp;
                $temp = [];
                $months[] = $time;
                $temp[] = $log->time;
            } else {
                $temp[] = $log->time;
            }
        }
        $times[] = $temp;
        array_shift($times);

        $temp = [];
        foreach ($times as $waktu) {
            if (count($waktu) < 1) {
                $waktu = 0;
            } else {
                $first = Carbon::parse($waktu[0]);
                $last = Carbon::parse($waktu[count($waktu) - 1]);
                $waktu = Helper::secToHour((int) $first->diffInSeconds($last, true));
            }
            $temp[] = $waktu;
        }

        $perBulan = [];
        for ($i = 0; $i < count($months); $i++) {
            $perBulan[$months[$i]] = $temp[$i] / 10;
        }

        $perBulan["Okt"] = 0;
        $perBulan["Nov"] = 0;
        $perBulan["Des"] = 0;

        $completedClass = ClassStudent::where('id_user', $user->id_user)
            ->whereNotNull('completed_at')
            ->count();

        $progressClass = ClassStudent::where('id_user', $user->id_user)
            ->whereNull('completed_at')
            ->count();

        $score = PointStudent::select("points")
            ->where('status', 1)
            ->where('id_user', $user->id_user)
            ->sum('points');

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Progress retrieved successfully',
            'data' => [
                'today_spent' => $userLifeTime,
                'certificates' => $completedClass,
                'score' => $score,
                'total_courses' => $progressClass + $completedClass,
                'course' => [
                    'in_progress' => $progressClass,
                    'completed' => $completedClass,
                    'yet_to_start' => 0,
                ],
                'monthly_spent' => $perBulan
            ]
        ];
    }
}
