<?php


namespace App\Services;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonService
{
    function __construct()
    {
    }

    /**
     * Parse json from request body
     * @param $oRequest
     * @return mixed
     */
    public function getJsonFromRequest(Request $oRequest)
    {
        $json = $oRequest->json()->all();
        if (count($json) > 0) {
            return $json;
        } else {
            throw new BadRequestHttpException('bad request, json body not found');
        }
    }

    /**
     * Method prepares and returns json output with pagination optionally
     * @param array $mResponse
     * @return JsonResponse
     */
    public function sendJsonResponse($mResponse = [], $isPagination = false)
    {
        if ($isPagination) {
            $oPaginationService = new PaginationService($mResponse);
            return response()->json($oPaginationService->getCurrentPage());
        }

        return response()->json($mResponse);
    }

}
