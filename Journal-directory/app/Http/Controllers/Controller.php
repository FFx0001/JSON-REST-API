<?php

namespace App\Http\Controllers;

use App\Services\JsonService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function jsonResponse($response,$isPagination=false)
    {
        return (new JsonService())->sendJsonResponse($response,$isPagination);
    }
}
