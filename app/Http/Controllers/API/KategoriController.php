<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\KategoriService;
use App\Traits\ApiResponseTrait;

class KategoriController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Kategori';

    protected KategoriService $kategoriService;

    public function __construct(KategoriService $kategoriService)
    {
        $this->kategoriService = $kategoriService;
    }

    public function index()
    {
        $kategori = $this->kategoriService->getAllKategori();

        if ($kategori->count() > 0) {
            return $this->successResponse(self::API_NAME, $kategori, 'Retrieving data successful!');
        }

        return $this->notFoundResponse(self::API_NAME, 'Not Found');
    }
}
