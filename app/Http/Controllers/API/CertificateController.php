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

    public static function create(Request $request)
    {
        $token = $request->input('api_token');
        $class = $request->input('class');

        $user = User::select('id_user', 'name')->where('api_token', $token)->first();
        $kelas = \App\Models\Kelas::where('id_class', $class)->first();
        $classStudent = \App\Models\ClassStudent::select('id_class_student')
            ->where('id_user', $user->id_user)
            ->where('id_class', $class)
            ->first();

        $cert = \App\Models\CertificateStudent::select('token')
            ->where('id_class_student', $classStudent->id_class_student)
            ->first();

        if ($cert === null) {
            $file = $user->name . '-' . date('YmdHis') . '.pdf';
            $cert = new \App\Models\CertificateStudent();
            $cert->id_class_student = $classStudent->id_class_student;
            $cert->token = hash('sha256', \Illuminate\Support\Str::random(60));
            $cert->file = $file;
            $cert->created_at = \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s');
            $cert->updated_at = \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s');

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cert', [
                'name' => $user->name,
                'class' => $kelas->title,
                'valid_until' => \Illuminate\Support\Carbon::now()->addYear(2)->format('d F, Y'),
                'no_cert' => substr(hash('sha256', \Illuminate\Support\Str::random(1)), 0, 12),
            ]);

            $pdf->setPaper('A4', 'landscape');
            $content = $pdf->download()->getOriginalContent();
            \Illuminate\Support\Facades\Storage::put('public/files/user/' . $file, $content);

            return $this->successResponse('Certificate API', null, 'success');
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
