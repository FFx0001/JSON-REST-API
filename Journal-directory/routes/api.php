<?php

use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\JournalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1'],function(){
    Route::group(['prefix'=>'author'],function(){

        /**
         * /api/v1/author/list?page=10&per_page=10
         * optional query: page per_page
         */
        Route::get('/list',     [AuthorController::class, 'getList']);

        /**
         * /api/v1/author/add
         * Json {
         *  'surname'       => 'required|min:3|max:191',
         *  'first_name'    => 'required|max:191',
         *  'last_name'     => 'max:191',
         * }
         */
        Route::post('/add',     [AuthorController::class, 'postCreate']);

        /**
         * /api/v1/author/update
         *  Json {
         *  'id'            => 'required|integer',
         *  'surname'       => 'min:3|max:191',
         *  'first_name'    => 'max:191',
         *  'last_name'     => 'max:191',
         * }
         */
        Route::post('/update',  [AuthorController::class, 'postUpdate']);

        /**
         * /api/v1/author/delete
         * Json {
         *  'id'            => 'required|integer',
         * }
         */
        Route::post('/delete',  [AuthorController::class, 'postDelete']);
    });

    Route::group(['prefix'=>'magazine'],function(){

        /**
         * /api/v1/magazine/list?page=10&per_page=10
         * optional query: page per_page
         */
        Route::get('/list',     [JournalController::class, 'getList']);

        /**
         * /api/v1/magazine/add
         * Загрузка изображения через строку based64 вместо поля post формы file
         * была выбрана из за условия в задании что всех входные даные должны быть в офрмате json 1 строкой
         * "image_extension" ("png" | "jpg")
         * "image_based64_string" (сырой конент изображения без медиа тегов)
         * "authors": [ "3", "7", "21",... ]
         *
         * json {
         *   'name'              => 'required|max:255',
         *   'description'       => 'max:2000',
         *   'image_based64_string'  => 'min:10',
         *   'image_extension'   => 'required_with:image_based64_string|max:5',
         *   'authors'           => 'required|max:255',
         *   'release_date'      => 'required|date_format:Y-m-d H:i:s',
         * }
         */
        Route::post('/add',     [JournalController::class, 'postCreate']);

        /**
         * /api/v1/magazine/update
         * Старый файл удаляется при обновлении
         * "image_extension" ("png" | "jpg")
         * "image_based64_string" (сырой конент изображения без медиа тегов)
         * "authors": [ "3", "7", "21",... ]
         *
         * json {
         *   'id'              => 'required|integer',
         *   'name'              => 'max:255',
         *   'description'       => 'max:2000',
         *   'image_based64_string'  => 'min:10',
         *   'image_extension'   => 'required_with:image_based64_string|max:5',
         *   'authors'           => 'max:255',
         *   'release_date'      => 'date_format:Y-m-d H:i:s',
         * }
         */
        Route::post('/update',  [JournalController::class, 'postUpdate']);

        /**
         * /api/v1/magazine/delete
         * json {
         *   'id'              => 'required|integer',
         * }
         */
        Route::post('/delete',  [JournalController::class, 'postDelete']);
    });
});


