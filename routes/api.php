<?php

use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\CertificateController;
use App\Http\Controllers\API\DiscussionController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\FEAuthController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\KelasController;
use App\Http\Controllers\API\LearningPathController;
use App\Http\Controllers\API\SilabusController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\MyClassController;
use App\Http\Controllers\API\ClassroomController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\MigrasiController;
use App\Http\Controllers\API\OverviewController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\PromoController;
use App\Http\Controllers\API\QuizController;
use App\Http\Controllers\API\QuizProgressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::middleware('auth:api')->get('/user', [UserController::class, 'index']);
// Route::get('/user', [UserController::class, 'index']);

Route::get('overview-{id}', [OverviewController::class, 'index']);

Route::get('kategori', [KategoriController::class, 'index']);
Route::get('kelas', [KelasController::class, 'kelas']);
Route::get('kelas/{id}', [KelasController::class, 'index']);
Route::get('getusertoken', [UserController::class, 'index']);

Route::get('migrasi', [MigrasiController::class, 'index']);
Route::get('migrasi/{mode}', [MigrasiController::class, 'index']);

Route::get('register', [UserController::class, 'register']);
Route::post('registerPost', [UserController::class, 'registerPost']);

Route::get('user-register', [UserController::class, 'register']);
Route::post('user-registerPost', [UserController::class, 'registerPost']);

Route::post('kelas/filter', [KelasController::class, 'filter']);
Route::post('kelas/filter/{keywords}', [KelasController::class, 'filter']);

Route::post('kelas/search/{keywords}', [KelasController::class, 'search']);

Route::get('learningpath', [LearningPathController::class, 'index']);
Route::get('learningpath/{id}', [LearningPathController::class, 'index']);

Route::get('blog-category', [BlogController::class, 'category']);
Route::get('blog', [BlogController::class, 'index']);
Route::get('blog/react', [BlogController::class, 'react']);
Route::get('blog/{id}', [BlogController::class, 'index']);

Route::get('event', [EventController::class, 'index']);
Route::get('event/today', [EventController::class, 'eventToday']);
Route::get('event/{id}', [EventController::class, 'index']);

Route::post('register', [LoginController::class, 'registerEmail'])->middleware('throttle:registration');
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:login');

Route::get('/mentor', [UserController::class, 'getMentor']);
Route::get('/faq', [OverviewController::class, 'faq']);

Route::get('/certificate/{token}', [CertificateController::class, 'shared']);

// Route::middleware('checkapitoken')->get('/materi/{id}', [SilabusController::class, 'silabus']);

Route::middleware(['checkapitoken'])->group(function () {
    Route::get('/silabus/{id_kelas}', [SilabusController::class, 'index']);
    Route::get('/silabus/materi/{id}', [SilabusController::class, 'silabus']);

    Route::post('/kelas/auth', [KelasController::class, 'authKelas']);
    Route::post('/kelas/check', [KelasController::class, 'checkKelas']);
    Route::post('/class/checkout-info', [KelasController::class, 'checkoutInfoClass']);
    Route::post('/class/checkout', [KelasController::class, 'checkoutClass']);
    Route::post('/class/complete', [KelasController::class, 'completeClass']);
    Route::post('/kelas/user/update/', [FEAuthController::class, 'feAuthorizer']);
    Route::post('/auth/silabus', [SilabusController::class, 'authKategori']);
    Route::post('/auth/materi', [SilabusController::class, 'authSubKategori']);

    Route::post('/quiz', [QuizController::class, 'index']);
    Route::post('/quiz/submit', [QuizController::class, 'submit']);
    Route::post('/quiz/clear', [QuizProgressController::class, 'index']);
    Route::post('/quiz/is-clear', [QuizProgressController::class, 'isClear']);

    Route::post('/certificate', [CertificateController::class, 'index']);
    Route::post('/certificate/create', [CertificateController::class, 'create']);

    Route::post('/profile', [ProfileController::class, 'userProfile']);
    Route::post('/profile/update', [ProfileController::class, 'userProfileUpdate']);
    Route::post('/profile/update/password', [ProfileController::class, 'userPasswordUpdate']);
    Route::post('/profile/update/course', [ProfileController::class, 'userCourseUpdate']);
    Route::post('/my-class', [MyClassController::class, 'index']);
    Route::post('/my-class/classroom', [ClassroomController::class, 'classroom']);
    Route::post('/project/submit', [ProjectController::class, 'submit']);

    Route::post('/auth/syllabus', [FEAuthController::class, 'index']);
    Route::post('/blog/{id}/like', [BlogController::class, 'like']);

    Route::post('event-attend', [EventController::class, 'attend']);
    Route::post('/event-history', [EventController::class, 'history']);

    Route::post('/email-verify', [LoginController::class, 'createEmailVerifier']);

    Route::post('kelas/{id}/review', [KelasController::class, 'review']);

    Route::post('progress', [MyClassController::class, 'progress']);

    Route::post('class/{id}/report', [KelasController::class, 'report']);

    Route::get('class/discussion', [DiscussionController::class, 'classes']);
    Route::get('class/{id}/discussion', [DiscussionController::class, 'discuss']);
    Route::post('discussion/create', [DiscussionController::class, 'createDiscussion']);
    Route::post('like', [DiscussionController::class, 'likePost']);
    Route::post('discussion/view', [DiscussionController::class, 'viewDiscuss']);

    Route::post('lecture/rate', [KelasController::class, 'rateLecture']);

    Route::post('comment', [DiscussionController::class, 'commentDiscussion']);
    Route::post('comment/delete', [DiscussionController::class, 'deleteCommentDiscussion']);

    Route::post('/promo-code', [PromoController::class, 'index']);
});

Route::post('/forgot-password', [ProfileController::class, 'generateForgotMail'])->middleware('throttle:password-reset');
Route::post('/forgot-password/{token}', [ProfileController::class, 'newPassword'])->middleware('throttle:password-reset');

Route::get('/project', [ProjectController::class, 'index']);
Route::get('/project/{class}', [ProjectController::class, 'projectClass']);

Route::group(['middleware' => ['web']], function () {
    Route::get('/auth/redirect', [LoginController::class, 'redirectToProvider']);
    Route::get('/auth/callback', [LoginController::class, 'handleProviderCallback']);
});

// OAuth code exchange endpoint (for secure token retrieval)
Route::post('/auth/exchange', [LoginController::class, 'exchangeOAuthCode']);

Route::post('notification/handle', function(Request $request) {
    DB::table('notification_log')->insert([
        'id_notification_log' => Str::uuid(),
        'data' => $request->all(),
    ]);

    return response($request->all());
});

Route::prefix("v2")->group(function () {
    Route::get('overview-{id}', [OverviewController::class, 'index']);
    
    Route::get('sample-quiz', [QuizController::class, 'sample']);

    Route::get('quiz-key', [QuizController::class, 'freekey']);
    Route::get('quiz-key/{syll}', [QuizController::class, 'freekey']);

    Route::get('kategori', [KategoriController::class, 'index']);
    Route::get('kelas', [KelasController::class, 'kelas']);
    Route::get('kelas/{id}', [KelasController::class, 'index']);
    Route::get('getusertoken', [UserController::class, 'index']);

    Route::get('migrasi', [MigrasiController::class, 'index']);
    Route::get('migrasi/{mode}', [MigrasiController::class, 'index']);

    Route::get('register', [UserController::class, 'register']);
    Route::post('registerPost', [UserController::class, 'registerPost']);

    Route::get('user-register', [UserController::class, 'register']);
    Route::post('user-registerPost', [UserController::class, 'registerPost']);

    Route::get('learningpath', [LearningPathController::class, 'index']);
    Route::get('learningpath/{id}', [LearningPathController::class, 'index']);

    Route::get('blog-category', [BlogController::class, 'category']);
    Route::get('blog', [BlogController::class, 'index']);
    Route::get('blog/{id}', [BlogController::class, 'index']);

    Route::get('event', [EventController::class, 'index']);
    Route::get('event/today', [EventController::class, 'eventToday']);
    Route::get('event/{id}', [EventController::class, 'index']);

    Route::post('register', [LoginController::class, 'registerEmail'])->middleware('throttle:registration');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:login');

    Route::get('/mentor', [UserController::class, 'getMentor']);
    Route::get('/faq', [OverviewController::class, 'faq']);
    /*  ^^^ ----------- Done Before Login ----------- ^^^ */

    Route::get('option/{id}', [QuizController::class, 'getOption']);

    Route::get('/certificate/{token}', [CertificateController::class, 'shared']);
    Route::middleware(['checkapitoken'])->group(function () {
        Route::get('/silabus/{id_kelas}', [SilabusController::class, 'index']);
        Route::get('/silabus/materi/{id}', [SilabusController::class, 'silabus']);

        Route::post('/class/checkout-info', [KelasController::class, 'checkoutInfoClass']);
        Route::post('/class/checkout', [KelasController::class, 'checkoutClass']);
        Route::post('/kelas/auth', [KelasController::class, 'authKelas']);
        Route::post('/kelas/check', [KelasController::class, 'checkKelas']);
        Route::post('/class/complete', [KelasController::class, 'completeClass']);

        Route::post('/kelas/user/update/', [FEAuthController::class, 'feAuthorizer']);

        Route::post('/quiz', [QuizController::class, 'index']);
        Route::post('/quiz/submit', [QuizController::class, 'submit']);
        Route::post('/quiz/clear', [QuizProgressController::class, 'index']);
        Route::post('/quiz/is-clear', [QuizProgressController::class, 'isClear']);

        Route::post('/certificate', [CertificateController::class, 'index']);

        Route::post('/profile', [ProfileController::class, 'userProfile']);
        Route::post('/profile/update', [ProfileController::class, 'userProfileUpdate']);
        Route::post('/profile/update/password', [ProfileController::class, 'userPasswordUpdate']);
        Route::post('/profile/update/course', [ProfileController::class, 'userCourseUpdate']);
        Route::post('/my-class', [MyClassController::class, 'index']);
        Route::post('/my-class/classroom', [ClassroomController::class, 'classroom']);
        Route::post('/project/submit', [ProjectController::class, 'submit']);

        Route::post('/auth/syllabus', [FEAuthController::class, 'index']);

        Route::post('event-attend', [EventController::class, 'attend']);
        Route::post('/event-history', [EventController::class, 'history']);

        Route::post('/email-verify', [LoginController::class, 'createEmailVerifier']);

        Route::post('kelas/{id}/review', [KelasController::class, 'review']);

        Route::post('class/{id}/report', [KelasController::class, 'report']);

        Route::get('class/discussion', [DiscussionController::class, 'classes']);
        Route::get('class/{id}/discussion', [DiscussionController::class, 'discuss']);
        Route::post('discussion/create', [DiscussionController::class, 'createDiscussion']);
        Route::post('like', [DiscussionController::class, 'likePost']);
        Route::post('discussion/view', [DiscussionController::class, 'viewDiscuss']);

        Route::post('lecture/rate', [KelasController::class, 'rateLecture']);

        Route::post('/blog/{id}/like', [BlogController::class, 'like']);

        Route::post('comment', [DiscussionController::class, 'commentDiscussion']);

        Route::post('progress', [MyClassController::class, 'progress']);

        Route::post('finish-payment', [PaymentController::class, 'handlePayment']);
    });

    Route::get('/project', [ProjectController::class, 'index']);
    Route::get('/project/{class}', [ProjectController::class, 'projectClass']);

    Route::post('/forgot-password', [ProfileController::class, 'generateForgotMail'])->middleware('throttle:password-reset');
    Route::post('/forgot-password/{token}', [ProfileController::class, 'newPassword'])->middleware('throttle:password-reset');

    Route::group(['middleware' => ['web']], function () {
        Route::get('/auth/redirect', [LoginController::class, 'redirectToProvider']);
        Route::get('/auth/callback', [LoginController::class, 'handleProviderCallback']);
    });

    // OAuth code exchange endpoint (for secure token retrieval)
    Route::post('/auth/exchange', [LoginController::class, 'exchangeOAuthCode']);
});

Route::get('/payment-test', [PaymentController::class, 'snapPay']);

