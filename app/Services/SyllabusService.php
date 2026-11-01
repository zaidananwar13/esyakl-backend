<?php

namespace App\Services;

use App\Helper as Help;
use App\Models\FEAuthorizer;
use App\Models\Kategori_Silabus;
use App\Models\KelasHistory;
use App\Models\SilabusChecker;
use App\Models\Sub_Kategori_Silabus;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class SyllabusService
{
    public function getAuthorizerStatus(string $token, int $classId): array
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

        $feAuth = FEAuthorizer::select("id_kelas", "unlocked")
            ->where("id_kelas", $classId)
            ->where("id_user", $user->id_user)
            ->first();

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Retrieving data successful!',
            'data' => $feAuth
        ];
    }

    public function updateFeAuthorizer(string $token, int $classId, int $material): array
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

        $feAuth = FEAuthorizer::select("id_kelas", "unlocked")
            ->where("id_kelas", $classId)
            ->where("id_user", $user->id_user)
            ->first();

        if ($feAuth === null) {
            $feAuth = new FEAuthorizer();
            $feAuth->id_user = $user->id_user;
            $feAuth->id_kelas = $classId;
            $feAuth->unlocked = 1;
            $feAuth->save();
        } else {
            $feAuth->update([
                "unlocked" => $material,
                "updated_at" => Carbon::now(),
            ]);
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => 'Success',
            'data' => null
        ];
    }

    public function getSyllabusByClass(string $token, int $classId): array
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

        $history = KelasHistory::select("id_sub_kategori_silabus")
            ->where("id_user", $user->id_user)
            ->where("id_kategori_silabus", $classId)
            ->first();

        $lastMaterial = ($history !== null) ? $history->id_sub_kategori_silabus : 0;

        $kategoriSilabus = Kategori_Silabus::select('id_kategori_silabus', 'id_kelas', 'judul')
            ->where('id_kelas', $classId)
            ->orderBy('orders', 'asc')
            ->get();

        foreach ($kategoriSilabus as $kSilabus) {
            $kSilabus->sub_kategori_silabus = Sub_Kategori_Silabus::select(
                'id_sub_kategori_silabus',
                'id_kategori_silabus',
                'judul',
                'konten'
            )
                ->where('id_kategori_silabus', $kSilabus->id_kategori_silabus)
                ->get();
        }

        if (count($kategoriSilabus) > 0) {
            return [
                'success' => true,
                'code' => 200,
                'message' => 'Retrieving data successful!',
                'last_material' => $lastMaterial,
                'data' => $kategoriSilabus
            ];
        }

        return [
            'success' => false,
            'code' => 404,
            'message' => 'Not Found'
        ];
    }

    public function getSyllabusMaterial(string $token, int $materialId): array
    {
        $user = User::select('id_user')
            ->where('api_token', $token)
            ->first();

        if ($user === null) {
            return [
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized User'
            ];
        }

        $secureKey = Help::checkSilabusAccess($user->id_user, $materialId);

        if ($secureKey === null) {
            $materi = Sub_Kategori_Silabus::select("id_kategori_silabus", "id_sub_kategori_silabus")
                ->where("id_sub_kategori_silabus", $materialId)
                ->first();

            if ($materi) {
                $authorized = $this->authorizeSubKategoriAccess(
                    $user->id_user,
                    $materi->id_kategori_silabus,
                    $materi->id_sub_kategori_silabus
                );

                if (!$authorized) {
                    return [
                        'success' => false,
                        'code' => 401,
                        'message' => 'Unauthorized User'
                    ];
                }

                $secureKey = [
                    ["id_sub_kategori_silabus" => $materi->id_sub_kategori_silabus],
                ];
            }
        }

        if ($secureKey === null) {
            return [
                'success' => false,
                'code' => 404,
                'message' => 'Not Found'
            ];
        }

        $secureKey = $secureKey[0];

        if ($secureKey['id_sub_kategori_silabus'] == $materialId) {
            $silabus = Sub_Kategori_Silabus::select(
                'id_sub_kategori_silabus',
                'id_kategori_silabus',
                'judul',
                'konten'
            )
                ->where('id_sub_kategori_silabus', $materialId)
                ->first();

            if ($silabus !== null) {
                return [
                    'success' => true,
                    'code' => 200,
                    'message' => 'Retrieving data successful!',
                    'data' => $silabus
                ];
            }
        }

        return [
            'success' => false,
            'code' => 404,
            'message' => 'Not Found'
        ];
    }

    private function authorizeSubKategoriAccess(int $userId, int $kategoriId, int $subKategoriId): bool
    {
        // Check if user already has access
        $existingAccess = Help::checkSilabusAccessUser($userId, $kategoriId, $subKategoriId);
        if ($existingAccess !== null) {
            return true;
        }

        // For first material (id 1), grant access automatically
        if ($subKategoriId == 1) {
            $silabusChecker = new SilabusChecker();
            $silabusChecker->id_user = $userId;
            $silabusChecker->id_kategori_silabus = $kategoriId;
            $silabusChecker->id_sub_kategori_silabus = $subKategoriId;
            $silabusChecker->save();
            return true;
        }

        // Check if user has access to previous material
        $previousMaterial = Sub_Kategori_Silabus::where('id_kategori_silabus', $kategoriId)
            ->where('id_sub_kategori_silabus', '<', $subKategoriId)
            ->orderBy('id_sub_kategori_silabus', 'desc')
            ->first();

        if ($previousMaterial) {
            $hasPreviousAccess = Help::checkSilabusAccessUser($userId, $kategoriId, $previousMaterial->id_sub_kategori_silabus);
            if ($hasPreviousAccess !== null) {
                // Grant access to current material
                $silabusChecker = new SilabusChecker();
                $silabusChecker->id_user = $userId;
                $silabusChecker->id_kategori_silabus = $kategoriId;
                $silabusChecker->id_sub_kategori_silabus = $subKategoriId;
                $silabusChecker->save();
                return true;
            }
        }

        return false;
    }

    public function authKategori(string $token, int $silabusId, int $materialId): array
    {
        try {
            $user = User::select('id_user')
                ->where('api_token', $token)
                ->first();

            if ($user === null) {
                return [
                    'success' => false,
                    'code' => 401,
                    'message' => 'Unauthorized'
                ];
            }

            $userSilabus = Help::checkSilabusAccessUser($user->id_user, $silabusId);

            if ($userSilabus === null) {
                return [
                    'success' => false,
                    'code' => 409,
                    'message' => 'Unauthorized register!'
                ];
            }

            if ($userSilabus['id_sub_kategori_silabus'] != $materialId && $userSilabus['id_sub_kategori_silabus'] < $materialId) {
                $silabus = new SilabusChecker();
                $silabus->id_user = $user->id_user;
                $silabus->id_kategori_silabus = $silabusId;
                $silabus->id_sub_kategori_silabus = $materialId;
                $silabus->id_bahasa = 1;
                $silabus->save();

                return [
                    'success' => true,
                    'code' => 200,
                    'message' => 'Auth register success!',
                    'data' => null
                ];
            }

            return [
                'success' => false,
                'code' => 409,
                'message' => 'Already registered'
            ];
        } catch (Exception $ex) {
            return [
                'success' => false,
                'code' => 500,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function authSubKategori(string $token, int $silabusId, int $materialId): array
    {
        try {
            $user = User::select('id_user')
                ->where('api_token', $token)
                ->first();

            if ($user === null) {
                return [
                    'success' => false,
                    'code' => 401,
                    'message' => 'Unauthorized'
                ];
            }

            $userSilabus = Help::checkSilabusAccessUser($user->id_user, $silabusId);

            if ($userSilabus === null) {
                $silabus = new SilabusChecker();
                $silabus->id_user = $user->id_user;
                $silabus->id_kategori_silabus = $silabusId;
                $silabus->id_sub_kategori_silabus = $materialId;
                $silabus->save();

                return [
                    'success' => true,
                    'code' => 200,
                    'message' => 'Auth register success!',
                    'data' => null
                ];
            }

            if ($userSilabus['id_sub_kategori_silabus'] != $materialId && $userSilabus['id_sub_kategori_silabus'] < $materialId) {
                $silabus = new SilabusChecker();
                $silabus->id_user = $user->id_user;
                $silabus->id_kategori_silabus = $silabusId;
                $silabus->id_sub_kategori_silabus = $materialId;
                $silabus->save();

                return [
                    'success' => true,
                    'code' => 200,
                    'message' => 'Auth register success!',
                    'data' => null
                ];
            }

            return [
                'success' => false,
                'code' => 409,
                'message' => 'Already registered'
            ];
        } catch (Exception $e) {
            $errorHandler = [
                "23503" => "Foreign key violates",
                "23505" => "Duplicate key"
            ];

            $data = json_encode($e);
            $data = json_decode($data);
            $status = $errorHandler[$data->errorInfo[0] ?? ''] ?? 'Unknown error';

            switch ($status) {
                case "Foreign key violates":
                    return [
                        'success' => false,
                        'code' => 409,
                        'message' => 'Illegal access detected'
                    ];
                case "Duplicate key":
                default:
                    return [
                        'success' => false,
                        'code' => 409,
                        'message' => 'Already registered'
                    ];
            }
        }
    }
}
