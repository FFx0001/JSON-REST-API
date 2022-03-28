<?php


namespace App\Services;


use App\Models\Journal;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JournalService
{
    /**
     * get all journals
     * @return array
     */
    public function listAll()
    {
        $journals = Journal::All();
        $arResult = [];
        $oFileService = new FileService();
        foreach ($journals as $oJournal) {
            $arResult[] = $this->prepareValues($oJournal, $oFileService);
        }
        return $arResult;
    }

    /**
     * create journal
     * custom field for upload image {
     *  'image_based64_string'  => 'min:10',
     *  'image_extension'   => 'required_with:image_based64_string|max:5',
     * }
     * @param array $jsonBody
     * @return array
     */
    public function create(array $jsonBody)
    {
        $imageFullName = null;
        if (key_exists('image_based64_string',$jsonBody)) {
            $oFileService = new FileService();
            $rawImage = base64_decode($jsonBody['image_based64_string']);
            $imageFullName = $oFileService->saveFile($rawImage, $jsonBody['image_extension']);
        }

        $oJournal = new Journal;

        $oJournal->name = $jsonBody['name'];

        if (key_exists('description',$jsonBody)) {
            $oJournal->description = $jsonBody['description'];
        }

        if (key_exists('image_based64_string',$jsonBody) && !empty($imageFullName)) {
            $oJournal->image_file_name = $imageFullName;
        }

        $oJournal->authors = implode(',',$jsonBody['authors']);
        $oJournal->release_date = $jsonBody['release_date'];

        if (!$oJournal->save()) {
            throw new HttpException(409, "error on write db");
        }
        return $this->prepareValues($oJournal, $oFileService);
    }

    /**
     * update journal and image
     * custom field for update image {
     *  'image_based64_string'  => 'min:10',
     *  'image_extension'   => 'required_with:image_based64_string|max:5',
     * }
     * @param array $jsonBody
     * @return array
     */
    public function update(array $jsonBody)
    {
        $imageFullName = null;
        $oFileService = new FileService();
        if (key_exists('image_based64_string',$jsonBody)) {
            $rawImage = base64_decode($jsonBody['image_based64_string']);
            $imageFullName = $oFileService->saveFile($rawImage, $jsonBody['image_extension']);
        }

        $oJournal = Journal::getByID($jsonBody['id']);
        if (!($oJournal instanceof Journal)) {
            throw new NotFoundHttpException( "journal not fount in db");
        }

        if (key_exists('name',$jsonBody)) {
            $oJournal->name = $jsonBody['name'];
        }

        if (key_exists('description',$jsonBody)) {
            $oJournal->description = $jsonBody['description'];
        }

        if (key_exists('image_based64_string',$jsonBody) && !empty($imageFullName)) {
            if ($oJournal->image_file_name != null) {
                // delete old image from disk if exist
                $oFileService->deleteFile($oJournal->image_file_name);
            }
            $oJournal->image_file_name = $imageFullName;
        }

        if (key_exists('authors',$jsonBody)) {
            $oJournal->authors = implode(',',$jsonBody['authors']);
        }

        if (key_exists('release_date',$jsonBody)) {
            $oJournal->release_date = $jsonBody['release_date'];
        }

        if (!$oJournal->save()) {
            throw new HttpException(409, "error on write db");
        }

        return $this->prepareValues($oJournal, $oFileService);
    }

    /**
     * delete journal and image file
     * @param array $jsonBody
     * @return array
     */
    public function delete(array $jsonBody)
    {
        $oFileService = new FileService();
        $oJournal = Journal::getByID($jsonBody['id']);

        if (!($oJournal instanceof Journal)) {
            throw new NotFoundHttpException( "journal not fount in db");
        }

        if ($oJournal->image_file_name != null) {
            // delete image from disk if exist
            (new FileService())->deleteFile($oJournal->image_file_name);
        }

        if (!$oJournal->delete()) {
            throw new HttpException(409, "error on delete from db");
        }

        return $this->prepareValues($oJournal, $oFileService);
    }

    /**
     * pre calculate formatted response
     * @param Journal $oJournal
     * @param FileService $oFileService
     * @return array
     */
    protected function prepareValues(Journal $oJournal, FileService $oFileService)
    {
        $webRoute = null;
        $authors = null;
        if ($oJournal->image_file_name != null) {
            $webRoute = $oFileService->getWebRoute($oJournal->image_file_name);
        }

        if ($oJournal->authors != null) {
            $authors = explode(',',$oJournal->authors);
        }

        return [
            'id'=>$oJournal->id,
            'name'=>$oJournal->name,
            'description'=>$oJournal->description,
            'image_file_name'=>$oJournal->image_file_name,
            'web_route'=>$webRoute,
            'authors'=>$authors,
            'release_date'=>$oJournal->release_date,
        ];
    }
}
