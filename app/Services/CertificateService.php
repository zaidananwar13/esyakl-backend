<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateStudent;
use App\Models\ClassCertificate;
use App\Models\ClassStudent;
use App\Models\Kelas;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateService
{
    public function getUserCertificates(int $userId): array
    {
        $user = User::select('id_user', 'name', 'avatar_original as img')
            ->where('id_user', $userId)
            ->first();

        $classStudents = ClassStudent::select('id_class_student', 'id_class')
            ->where('id_user', $userId)
            ->get();

        $classIds = [];
        $classMap = [];
        foreach ($classStudents as $cs) {
            $classIds[] = $cs->id_class_student;
            $classMap[$cs->id_class_student] = $cs->id_class;
        }

        $certificates = ClassCertificate::select('id_class_student', 'file', 'token', 'created_at')
            ->whereIn('id_class_student', $classIds)
            ->get();

        $result = [];
        foreach ($certificates as $cert) {
            $result[] = $this->formatCertificate($cert, $user, $classMap[$cert->id_class_student]);
        }

        return $result;
    }

    public function getCertificateByClass(int $userId, int $classId): ?array
    {
        $user = User::select('id_user', 'name', 'avatar_original as img')
            ->where('id_user', $userId)
            ->first();

        $classStudent = ClassStudent::select('id_class_student', 'id_class')
            ->where('id_class', $classId)
            ->where('id_user', $userId)
            ->first();

        if (!$classStudent) {
            return null;
        }

        $certificate = ClassCertificate::select('id_class_student', 'file', 'token', 'created_at')
            ->where('id_class_student', $classStudent->id_class_student)
            ->first();

        if (!$certificate) {
            return null;
        }

        return $this->formatCertificate($certificate, $user, $classId);
    }

    public function getCertificateByToken(string $token): ?array
    {
        $certificate = ClassCertificate::select('id_class_student', 'file', 'token', 'created_at')
            ->where('token', $token)
            ->first();

        if (!$certificate) {
            return null;
        }

        $classStudent = ClassStudent::select('id_user', 'id_class')
            ->where('id_class_student', $certificate->id_class_student)
            ->first();

        $user = User::select('id_user', 'name', 'avatar_original as img')
            ->where('id_user', $classStudent->id_user)
            ->first();

        return $this->formatCertificate($certificate, $user, $classStudent->id_class);
    }

    public function createCertificate($classStudent): bool
    {
        $existing = ClassCertificate::where('id_class_student', $classStudent->id_user)->first();

        if ($existing) {
            return false;
        }

        $template = Certificate::select('template')
            ->where('status', 'active')
            ->first();

        $noCert = substr(hash('sha256', Str::random(60)), 0, 24);
        $file = $classStudent->name . '-' . date('YmdHis') . '.pdf';

        $cert = new ClassCertificate();
        $cert->id_certificate = null;
        $cert->id_class_student = $classStudent->id_class_student;
        $cert->token = $noCert;
        $cert->file = config('app.url', 'https://api.arabic-go.com') . '/storage/files/user/' . $file;
        $cert->created_at = Carbon::now()->format('U');
        $cert->updated_at = Carbon::now()->format('U');
        $cert->save();

        $pdf = Pdf::loadView('pdf.cert', [
            'template' => $template->template ?? null,
            'name' => $classStudent->name,
            'class' => $classStudent->title,
            'valid_until' => Carbon::now()->addYear(2)->format('d F, Y'),
            'no_cert' => $noCert,
            'img' => base64_encode(QrCode::format('svg')->errorCorrection('H')->generate("https://arabic-go.com/certificate/" . $noCert))
        ]);

        $pdf->setPaper('A4', 'landscape');
        $content = $pdf->download()->getOriginalContent();
        Storage::put('public/files/user/' . $file, $content);

        return true;
    }

    private function formatCertificate($certificate, $user, int $classId): array
    {
        $kelas = Kelas::select('id_class', 'title')
            ->where('id_class', $classId)
            ->first();

        $createdAt = $certificate->created_at;
        $date = Carbon::parse($createdAt)->format('d F Y');
        $expired = Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->addYears(2)->format('d F Y');
        $isExpired = Carbon::parse($expired)->format('U') < Carbon::now()->format('U');

        return [
            'id_class_student' => $certificate->id_class_student,
            'id_class' => $kelas->id_class,
            'user' => $user->name,
            'img' => $user->img,
            'title' => $kelas->title,
            'file' => $certificate->file,
            'token' => $certificate->token,
            'date' => $date,
            'expired' => $expired,
            'status' => $isExpired ? 'expired' : 'available',
        ];
    }
}
