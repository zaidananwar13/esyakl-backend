<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OverviewService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    use ApiResponseTrait;

    protected const API_NAME = 'Overview';

    protected OverviewService $overviewService;

    public function __construct(OverviewService $overviewService)
    {
        $this->overviewService = $overviewService;
    }

    public function index(Request $request, $slideNumber)
    {
        $lang = $request->input('lang') ?? 'English';
        $overview = $this->overviewService->getOverviewBySlide((int) $slideNumber, $lang);

        return $this->successResponse(self::API_NAME, $overview, "This is an API for Arabic-Go's Overview Page $slideNumber.");
    }

    public function faq(Request $request)
    {
        $lang = $request->input('lang') ?? 'English';
        $faq = $this->overviewService->getFaq($lang);

        return $this->successResponse('FAQ', $faq, 'Retrieving data successful!');
    }
}
