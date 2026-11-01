<?php

namespace App\Services;

use App\Helpers\MediaHelper;
use App\Models\Bahasa;
use App\Models\ClassReview;
use App\Models\Faq;
use App\Models\Kategori_Silabus;
use App\Models\Kelas;
use App\Models\Overview;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OverviewService
{
        public function getOverviewBySlide(int $slideNumber, string $lang = 'English'): mixed
    {
        switch ($slideNumber) {
            case 1:
                return $this->getOverviewOne($lang);
            case 2:
                return $this->getOverviewTwo($lang);
            case 3:
                return $this->getOverviewThree($lang);
            case 4:
                return $this->getOverviewFour($lang);
            case 5:
                return $this->getOverviewFive($lang);
            default:
                return "unknown request";
        }
    }

        public function getFaq(string $lang = 'English'): Collection
    {
        $langModel = Bahasa::select('id_bahasa')
            ->where('bahasa', $lang)
            ->first();

        if (!$langModel) {
            return collect([]);
        }

        return Faq::select('question as title', 'answere as content')
            ->where('id_bahasa', $langModel->id_bahasa)
            ->get();
    }

        private function getOverviewOne(string $lang): object
    {
        $langId = ($lang === 'English') ? 1 : 2;

        $overview = Overview::select("title", "desc")
            ->where("id_bahasa", $langId)
            ->where("id_overview", 1)
            ->first();

        if (!$overview) {
            return (object)['error' => 'Overview not found'];
        }

        $ratings = ClassReview::select("star")->get()->toArray();

        $totalRating = 0;
        $reviewCount = count($ratings);

        foreach ($ratings as $review) {
            $totalRating += $review["star"];
        }

        $overview->rating = ($reviewCount > 0)
            ? number_format(($totalRating / $reviewCount), 1, ".", "") . "/5"
            : 0;
        $overview->votes = $reviewCount;
        $overview->students = User::where("type", "student")->count();

        return $overview;
    }

        private function getOverviewTwo(string $lang): object
    {
        $langId = ($lang === 'English') ? 1 : 2;

        $overview = Overview::select("title", "desc")
            ->where("id_bahasa", $langId)
            ->where("id_overview", 2)
            ->first();

        if (!$overview) {
            return (object)['error' => 'Overview not found'];
        }

        $publishedClassCount = Kelas::where('status', 'publish')->count();
        $alumniCount = DB::table('class_student')->whereNotNull('completed_at')->count();

        if ($lang === 'English') {
            $overview->items = [
                [
                    "title" => "100%",
                    "subTitle" => "COMPLETION RATE",
                    "desc" => "For context, typical online courses have a 12.6% completion rate. We're proud to have a 100% completion rate.",
                    "image" => "Sports medal.svg",
                ],
                [
                    "title" => $publishedClassCount,
                    "subTitle" => "CERTIFIED ARABIC CLASSES",
                    "desc" => "Each learning path consists of various levels of classes starting from beginner, intermediate, and advanced.",
                    "image" => "Money with wings.svg",
                ],
                [
                    "title" => $alumniCount,
                    "subTitle" => "OUR ALUMNI",
                    "desc" => "We have taught over 500 students online and our awesome alumni have gone on to work Google and more.",
                    "image" => "student.svg",
                ],
            ];
        } else {
            $overview->items = [
                [
                    "title" => "100%",
                    "subTitle" => "Rating Penyelesaian",
                    "desc" => "Rata-rata tingkat penyelesaian kursus online sebesar 12,6%. Kami bangga memiliki tingkat penyelesaian 100%.",
                    "image" => "Sports medal.svg",
                ],
                [
                    "title" => $publishedClassCount,
                    "subTitle" => "Kelas Arabic Tersertifikasi",
                    "desc" => "Setiap learning path terdiri dari berbagai tingkatan kelas dimulai dari beginner, intermediate, dan advanced.",
                    "image" => "Money with wings.svg",
                ],
                [
                    "title" => $alumniCount,
                    "subTitle" => "Alumni Kami",
                    "desc" => "Kami telah mengajar lebih dari 500 siswa secara online dan akan terus bertambah seiring waktu.",
                    "image" => "student.svg",
                ],
            ];
        }

        return $overview;
    }

        private function getOverviewThree(string $lang): object
    {
        $langId = ($lang === 'English') ? 1 : 2;

        $overview = Overview::select("title", "desc")
            ->where("id_bahasa", $langId)
            ->where("id_overview", 3)
            ->first();

        if (!$overview) {
            return (object)['error' => 'Overview not found'];
        }

        $kelas = Kelas::select('id_class as id_kelas', 'title as judul', 'thumb as gambar', 'id_class_level as level')
            ->where('status', 'publish')
            ->get();

        foreach ($kelas as $kel) {
            $kel->gambar = MediaHelper::getMediaUrl($kel->gambar);
            $kel->link = "/class?id=" . $kel->id_kelas;
            $kel->langkah = Kategori_Silabus::where('id_class', $kel->id_kelas)->count();

            $reviews = DB::table('class_review')
                ->join('class_student', 'class_student.id_class_student', '=', 'class_review.id_class_student')
                ->join('class', 'class.id_class', '=', 'class_student.id_class')
                ->select('id_class_review', 'id_user', 'star', 'content')
                ->where('class.id_class', $kel->id_kelas)
                ->get();

            foreach ($reviews as $review) {
                $userName = DB::table('user')
                    ->select('name')
                    ->where('id_user', '=', $review->id_user)
                    ->first();

                $review->nama = $userName ? $userName->name : '';
            }

            $ratings = $reviews->pluck('star')->toArray();
            $totalRating = array_sum($ratings);
            $ratingCount = count($ratings);

            $kel->rating = ($ratingCount > 0)
                ? (float) number_format($totalRating / $ratingCount, 2)
                : 0;

            unset($kel->id_kelas);
        }

        $overview->items = $kelas;

        return $overview;
    }

        private function getOverviewFour(string $lang): object
    {
        $langId = ($lang === 'English') ? 1 : 2;

        $overview = Overview::select("title", "desc")
            ->where("id_bahasa", $langId)
            ->where("id_overview", 4)
            ->first();

        if (!$overview) {
            return (object)['error' => 'Overview not found'];
        }

        $review = DB::table('class_review')
            ->join('class_student', 'class_student.id_class_student', '=', 'class_review.id_class_student')
            ->join('class', 'class.id_class', '=', 'class_student.id_class')
            ->select('id_class_review', 'id_user', 'star', 'content')
            ->first();

        $user = $review ? User::where('id_user', '=', $review->id_user)->first() : null;

        $commentData = [
            "name" => $user ? $user->name : '',
            "comment" => $review ? $review->content : '',
            "img" => $user ? $user->avatar_original : '',
        ];

        if ($lang === 'English') {
            $overview->items = [
                "title" => "Learning is easier with the giving harakat featured",
                "desc" => "Confused in reading arabic without harakat? No worries, we'll do it for you! Introducing, our giving harakat feature.",
                "link" => "https://e-syakl.org/",
                "comments" => $commentData,
            ];
        } else {
            $overview->items = [
                "title" => "Belajar jadi lebih mudah dengan fitur pemberian harakat",
                "desc" => "Bingung membaca bahasa arab tanpa harakat? Jangan khawatir, kami akan melakukannya untuk Anda! Perkenalkan, fitur pemberian harakat kami.",
                "link" => "https://e-syakl.org/",
                "comments" => $commentData,
            ];
        }

        return $overview;
    }

        private function getOverviewFive(string $lang): object
    {
        $langId = ($lang === 'English') ? 1 : 2;

        $overview = Overview::select("title", "desc")
            ->where("id_bahasa", $langId)
            ->where("id_overview", 5)
            ->first();

        if (!$overview) {
            return (object)['error' => 'Overview not found'];
        }

        $reviews = DB::table('class_review')
            ->join('class_student', 'class_student.id_class_student', '=', 'class_review.id_class_student')
            ->join('class', 'class.id_class', '=', 'class_student.id_class')
            ->select('id_class_review', 'id_user', 'star', 'content', 'class_review.created_at')
            ->get();

        foreach ($reviews as $review) {
            $date = Carbon::parse($review->created_at);

            $review->time = $date->format('g:i A');
            $review->date = $date->format('M d, Y');

            $review->comment = $review->content;
            unset($review->content);
            unset($review->created_at);

            $review->rating = $review->star;
            unset($review->star);

            $user = User::where('id_user', '=', $review->id_user)->first();
            $review->name = $user ? $user->name : '';
            $review->img = $user ? $user->avatar_original : '';
        }

        $overview->items = $reviews;

        return $overview;
    }
}
