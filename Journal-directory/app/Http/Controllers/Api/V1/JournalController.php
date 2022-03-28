<?php


namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Services\JournalService;
use App\Services\JsonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JournalController extends Controller
{
    /**
     * Get paginated list of journals with additional field web_route
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $oJournalService = new JournalService();
        $allJournals = $oJournalService->listAll();
        return $this->jsonResponse($allJournals,true);
    }

    /**
     * Create new journal from json request
     * @param Request $oRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postCreate(Request $oRequest)
    {
        $oJsonService = new JsonService();
        $jsonBody = $oJsonService->getJsonFromRequest($oRequest);

        // Загрузка изображения через строку based64 вместо поля post формы file была выбрана из за условия в задании
        // что всех входные даные должны быть в офрмате json 1 строкой
        $validation = Validator::make($jsonBody, [
            'name'              => 'required|max:255',
            'description'       => 'max:2000',
            'image_based64_string'  => 'min:10',
            'image_extension'   => 'required_with:image_based64_string|max:5',
            'authors'           => 'required|max:255',
            'release_date'      => 'required|date_format:Y-m-d H:i:s',

        ]);

        if ($validation->fails()) {
            throw new HttpException(422, $validation->errors());
        }

        $oJournalService = new JournalService();
        $oJournal = $oJournalService->create($validation->validated());

        return $oJsonService->sendJsonResponse($oJournal);
    }

    /**
     * update journal from json request
     * auto deleting old image from storage on insert new
     * @param Request $oRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postUpdate(Request $oRequest)
    {
        $oJsonService = new JsonService();
        $jsonBody = $oJsonService->getJsonFromRequest($oRequest);

        // Загрузка изображения через строку based64 вместо поля post формы file была выбрана из за условия в задании что всех входные даные должны быть в офрмате json 1 строкой
        $validation = Validator::make($jsonBody, [
            'id'              => 'required|integer',
            'name'              => 'max:255',
            'description'       => 'max:2000',
            'image_based64_string'  => 'min:10',
            'image_extension'   => 'required_with:image_based64_string|max:5',
            'authors'           => 'max:255',
            'release_date'      => 'date_format:Y-m-d H:i:s',

        ]);

        if ($validation->fails()) {
            throw new HttpException(422, $validation->errors());
        }

        $oJournalService = new JournalService();
        $oJournal = $oJournalService->update($validation->validated());

        return $oJsonService->sendJsonResponse($oJournal);
    }

    /**
     * delete journal from json request
     * auto deleting image from storage
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

        $oJournalService = new JournalService();
        $oJournal = $oJournalService->delete($validation->validated());

        return $oJsonService->sendJsonResponse($oJournal);
    }
}
