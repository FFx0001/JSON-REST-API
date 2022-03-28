<?php


namespace App\Services;


use App\Models\Author;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthorService
{
    /**
     * get all authors
     * @return Author[]|\Illuminate\Database\Eloquent\Collection
     */
    public function listAll()
    {
        return Author::All();
    }

    /**
     * Crete new author
     * @param array $jsonBody {}
     * @return Author
     */
    public function create(array $jsonBody)
    {
        $oAuthor = new Author;
        $oAuthor->surname = $jsonBody['surname'];
        $oAuthor->first_name = $jsonBody['first_name'];
        $oAuthor->last_name = $jsonBody['last_name'];
        if (!$oAuthor->save()) {
            throw new HttpException(409, "error on write db");
        }
        return $oAuthor;
    }

    /**
     * update author from json request
     * @param array $jsonBody
     * @return Author|null
     */
    public function update(array $jsonBody)
    {
        $oAuthor = Author::getByID($jsonBody['id']);

        if (!($oAuthor instanceof Author)) {
            throw new NotFoundHttpException( "author not fount in db");
        }

        if (key_exists('surname',$jsonBody)) {
            $oAuthor->surname = $jsonBody['surname'];
        }
        if (key_exists('first_name',$jsonBody)) {
            $oAuthor->first_name = $jsonBody['first_name'];
        }
        if (key_exists('last_name',$jsonBody)) {
            $oAuthor->last_name = $jsonBody['last_name'];
        }
        if (!$oAuthor->save()) {
            throw new HttpException(409, "error on write db");
        }
        return $oAuthor;
    }

    /** delete author from json request
     * @param array $jsonBody
     * @return Author|null
     */
    public function delete(array $jsonBody)
    {
        $oAuthor = Author::getByID($jsonBody['id']);

        if (!($oAuthor instanceof Author)) {
            throw new NotFoundHttpException( "author not fount in db");
        }

        if (!$oAuthor->delete()) {
            throw new HttpException(409, "error on delete from db");
        }
        return $oAuthor;
    }
}
