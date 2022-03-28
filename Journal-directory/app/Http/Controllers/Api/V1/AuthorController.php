<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Services\AuthorService;
use App\Services\JsonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthorController extends Controller
{
    /**
     *  Get paginated list of authors with additional field web_route
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $oAuthorService = new AuthorService();
        $allAuthors = $oAuthorService->listAll();

        return $this->jsonResponse($allAuthors,true);
    }

    /**
     * create author from json request
     * @param Request $oRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postCreate(Request $oRequest)
    {
        $oJsonService = new JsonService();
        $jsonBody = $oJsonService->getJsonFromRequest($oRequest);

        $validation = Validator::make($jsonBody, [
            'surname'       => 'required|min:3|max:191',
            'first_name'    => 'required|max:191',
            'last_name'     => 'max:191',
        ]);

        if ($validation->fails()) {
            throw new HttpException(422, $validation->errors());
        }

        $oAuthorService = new AuthorService();
        $author = $oAuthorService->create($validation->validated());

        return $oJsonService->sendJsonResponse($author);
    }

    /**
     * update author from json request
     * @param Request $oRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postUpdate(Request $oRequest)
    {
        $oJsonService = new JsonService();
        $jsonBody = $oJsonService->getJsonFromRequest($oRequest);

        $validation = Validator::make($jsonBody, [
            'id'            => 'required|integer',
            'surname'       => 'min:3|max:191',
            'first_name'    => 'max:191',
            'last_name'     => 'max:191',
        ]);

        if ($validation->fails()) {
            throw new HttpException(422, $validation->errors());
        }

        $oAuthorService = new AuthorService();
        $oAuthor = $oAuthorService->update($validation->validated());

        return $oJsonService->sendJsonResponse($oAuthor);
    }

    /**
     * delete author from json request
     * @param Request $oRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postDelete(Request $oRequest)
    {
        $oJsonService = new JsonService();
        $jsonBody = $oJsonService->getJsonFromRequest($oRequest);

        $validation = Validator::make($jsonBody, [
            'id'            => 'required|integer',
        ]);

        if ($validation->fails()) {
            throw new HttpException(422, $validation->errors());
        }

        $oAuthorService = new AuthorService();
        $oAuthor = $oAuthorService->delete($validation->validated());

        return $oJsonService->sendJsonResponse($oAuthor);
    }
}
