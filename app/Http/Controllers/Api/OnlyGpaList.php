<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\TestAssignment\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnlyGpaList extends ApiController
{

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, ApiResponseService $apiResponseService): \Illuminate\Http\JsonResponse
    {
        $gpas = DB::table('results')->select('gpa')->distinct()->get();
        return $apiResponseService->efflux($gpas);
    }
}
