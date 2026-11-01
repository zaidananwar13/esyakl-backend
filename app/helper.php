<?php

namespace App;

use App\Models\ClassStudent;
use App\Models\SilabusChecker;
use App\Models\StudentLogActivity;
use App\Models\UserLog;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Carbon;

class Helper
{
    use ApiResponseTrait;

    public static function setCookie($key, $val)
    {
        setcookie($key, $val, time() + (3600 * 30), "/");
    }

    public static function checkSilabusAccess($id_user, $id_sub)
    {
        return SilabusChecker::select('id_sub_kategori_silabus')->where('id_user', $id_user)->where('id_sub_kategori_silabus', $id_sub)->get()->toArray();
    }

    public static function checkSilabusAccessUser($id_user, $id_kategori_silabus, $id_sub_kategori_silabus = null)
    {
        if ($id_sub_kategori_silabus == null) {
            $result = SilabusChecker::select('id_user', 'id_kategori_silabus', 'id_sub_kategori_silabus')
                ->where([
                    'id_user' => $id_user,
                    'id_kategori_silabus' => $id_kategori_silabus
                ])
                ->orderBy('id_silabus_checker', 'desc')
                ->first();
        } else {
            $result = SilabusChecker::select('id_user', 'id_kategori_silabus', 'id_sub_kategori_silabus')
                ->where([
                    'id_user' => $id_user,
                    'id_kategori_silabus' => $id_kategori_silabus,
                    'id_sub_kategori_silabus' => $id_sub_kategori_silabus
                ])
                ->orderBy('id_silabus_checker', 'desc')
                ->first();
        }

        if ($result != null) $result = $result->toArray();

        return $result;
    }

    public static function checkKelasAccessUser($id_user, $id_kelas)
    {
        $result = ClassStudent::select('id_class_student', 'id_user', 'id_class')
            ->where([
                'id_user' => $id_user,
                'id_class' => $id_kelas,
            ])
            ->orderBy('id_class_student', 'desc')
            ->first();

        if ($result != null) $result = $result->toArray();

        return $result;
    }

    public static function generateQuiz($params)
    {
        $data = [];
        $array = [];

        foreach ($params as $param => $value) {
            $temp = [];
            if ((strpos($param, "pilihan")) !== false) {
                $temp["pilihan"] = $value;
            } else if ((strpos($param, "id_quiz")) !== false) {
                $temp["id_quiz"] = $value;
            }

            if (count($temp) > 0) {
                array_push($data, $temp);
            }
        }

        for ($i = 0; $i < count($data); $i = $i + 2) {
            $temp = [
                "id_quiz" => $data[$i]["id_quiz"],
                "pilihan" => $data[$i + 1]["pilihan"]
            ];
            array_push($array, $temp);
        }

        return $array;
    }

    public static function time_elapsed_string($datetime, $full = false)
    {
        return Carbon::parse($datetime)->diffForHumans();
    }

    public static function unauthorized($api)
    {
        return response([
            'title' => config('app-settings.api.title_prefix', 'E - Syakl') . " | $api",
            'code' => 401,
            'message' => 'Unauthorized!',
            'data' => null
        ], 401);
    }

    public static function updateUserLog($status, $id)
    {
        UserLog::create([
            'id_user' => $id,
            'status' => $status,
            'time' => Carbon::now()->format('U'),
        ]);
    }

    public static function secToHour($sec)
    {
        return round($sec / 3600, 1);
    }

    public static function recordUserActivity($id_user, $activity, $class)
    {
        try {
            StudentLogActivity::create([
                'id_student' => $id_user,
                'last_class_access' => $class,
                'access_type' => 'Unset',
                'information_detail' => $activity,
            ]);
        } catch (\Exception $e) {
            \Log::error('recordUserActivity failed: ' . $e->getMessage());
        }
    }
}
