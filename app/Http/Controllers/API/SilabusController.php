<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\SyllabusService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SilabusController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Silabus';
    protected const API_NAME_AUTH = 'Silabus Auth';

    protected SyllabusService $syllabusService;

    public function __construct(SyllabusService $syllabusService)
    {
        $this->syllabusService = $syllabusService;
    }

    public function index(Request $request, $id)
    {
        $result = $this->syllabusService->getSyllabusByClass(
            $request->input("api_token"),
            $id
        );

        if (!$result['success']) {
            if ($result['code'] === 401) {
                return $this->unauthorizedResponse(self::API_NAME, $result['message']);
            }
            return $this->notFoundResponse(self::API_NAME, $result['message']);
        }

        $response = [
            'title' => config('app-settings.api.title_prefix', 'E - Syakl') . " | Silabus API",
            'code' => 200,
            'message' => $result['message'],
            'last_material' => $result['last_material'],
            'data' => $result['data']
        ];

        return response($response, 200);
    }

    public function silabus(Request $request, $id)
    {
        $result = $this->syllabusService->getSyllabusMaterial(
            $request->api_token,
            $id
        );

        if (!$result['success']) {
            if ($result['code'] === 401) {
                return $this->unauthorizedResponse(self::API_NAME, $result['message']);
            }
            return $this->notFoundResponse(self::API_NAME, $result['message']);
        }

        return $this->successResponse(self::API_NAME, $result['data'], $result['message']);
    }

    public function authKategori(Request $request)
    {
        $result = $this->syllabusService->authKategori(
            $request->input('api_token'),
            $request->input('silabus'),
            $request->input('material')
        );

        if (!$result['success']) {
            if ($result['code'] === 401) {
                return $this->unauthorizedResponse(self::API_NAME_AUTH, $result['message']);
            }
            return $this->conflictResponse(self::API_NAME_AUTH, $result['message']);
        }

        return $this->successResponse(self::API_NAME_AUTH, null, $result['message']);
    }

    public function authSubKategori(Request $request)
    {
        $result = $this->syllabusService->authSubKategori(
            $request->input('api_token'),
            $request->input('silabus'),
            $request->input('material')
        );

        if (!$result['success']) {
            if ($result['code'] === 401) {
                return $this->unauthorizedResponse(self::API_NAME_AUTH, $result['message']);
            }
            return $this->conflictResponse(self::API_NAME_AUTH, $result['message']);
        }

        return $this->successResponse(self::API_NAME_AUTH, null, $result['message']);
    }
}
