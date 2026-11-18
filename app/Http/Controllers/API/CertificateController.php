<?php

namespace App\Http\Controllers\API;

use App\Helper as Help;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CertificateService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Certificate';

    private CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function index(Request $request)
    {
        $token = $request->input('api_token');
        $classId = $request->input('class');

        if (!$token) {
            return Help::unauthorized('Certificate');
        }

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return Help::unauthorized('Certificate');
        }

        if ($classId) {
            $certificate = $this->certificateService->getCertificateByClass($user->id_user, (int) $classId);

            if (!$certificate) {
                return $this->notFoundResponse(self::API_NAME, 'No certificate found');
            }

            return $this->successResponse(self::API_NAME, $certificate, 'Retrieving data successful!');
        }

        $certificates = $this->certificateService->getUserCertificates($user->id_user);

        return $this->successResponse(self::API_NAME, $certificates, 'Retrieving data successful!');
    }

    public function create(Request $request)
    {
        $token = $request->input('api_token');
        $class = $request->input('class');

        if (!$token || !$class) {
            return $this->errorResponse('Certificate API', 400, 'api_token and class are required');
        }

        $user = User::select('id_user', 'name')->where('api_token', $token)->first();
        if (!$user) {
            return Help::unauthorized('Certificate');
        }

        $kelas = \App\Models\Kelas::where('id_class', $class)->first();
        if (!$kelas) {
            return $this->notFoundResponse(self::API_NAME, 'Class not found');
        }

        $classStudent = \App\Models\ClassStudent::select('id_class_student')
            ->where('id_user', $user->id_user)
            ->where('id_class', $class)
            ->first();

        if (!$classStudent) {
            return $this->notFoundResponse(self::API_NAME, 'Student enrollment not found');
        }

        $cert = \App\Models\CertificateStudent::select('token')
            ->where('id_class_student', $classStudent->id_class_student)
            ->first();

        if ($cert === null) {
            $template = \App\Models\Certificate::select('template')
                ->where('status', 'active')
                ->first();

            $noCert = substr(hash('sha256', \Illuminate\Support\Str::random(60)), 0, 24);
            $file = $user->name . '-' . date('YmdHis') . '.pdf';
            $fileUrl = config('app.url', 'https://api.arabic-go.com') . '/storage/files/user/' . $file;

            $cert = new \App\Models\CertificateStudent();
            $cert->id_class_student = $classStudent->id_class_student;
            $cert->token = $noCert;
            $cert->file = $fileUrl;
            $cert->created_at = \Illuminate\Support\Carbon::now()->format('U');
            $cert->updated_at = \Illuminate\Support\Carbon::now()->format('U');
            $cert->save();

            $qrUrl = config('app-settings.frontend.base_url', 'https://arabic-go.com/') . 'certificate/' . $noCert;
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cert', [
                'template' => $template->template ?? null,
                'name' => $user->name,
                'class' => $kelas->title,
                'valid_until' => \Illuminate\Support\Carbon::now()->addYear(2)->format('d F, Y'),
                'no_cert' => $noCert,
                'img' => base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->errorCorrection('H')->generate($qrUrl)),
            ]);

            $pdf->setPaper('A4', 'landscape');
            $content = $pdf->download()->getOriginalContent();
            \Illuminate\Support\Facades\Storage::put('public/files/user/' . $file, $content);

            return $this->successResponse('Certificate API', ['token' => $noCert, 'file' => $fileUrl], 'success');
        }

        return $this->errorResponse('Certificate API', 400, 'failed');
    }

    public static function createLocal($classStudent)
    {
        $service = app(CertificateService::class);
        return $service->createCertificate($classStudent);
    }

    public function shared($token)
    {
        $certificate = $this->certificateService->getCertificateByToken($token);

        if (!$certificate) {
            return Help::unauthorized('Certificate');
        }

        return $this->successResponse(self::API_NAME, $certificate, 'Retrieving data successful!');
    }
}
