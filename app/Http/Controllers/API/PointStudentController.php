<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\PointService;

class PointStudentController extends Controller
{
    protected static ?PointService $pointServiceInstance = null;

    protected static function getPointService(): PointService
    {
        if (self::$pointServiceInstance === null) {
            self::$pointServiceInstance = app(PointService::class);
        }

        return self::$pointServiceInstance;
    }
    
    public static function submitPoint($userId): bool
    {
        return self::getPointService()->calculateAndSubmitPoints($userId);
    }
}
