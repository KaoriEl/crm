<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/** Загружает файл временно (без прикрепления к какой-либо модели) */
Route::post('/files', 'Api\FileController@store');

/** Удаляет файл */
Route::delete('/files', 'Api\FileController@destroy');



/**
 * Посты
 */

/** Прикрепить файл к посту */
Route::post('/posts/{post}/files', 'Api\PostFileController@store');

/** Открепить файл от поста и удалить файл */
Route::delete('/posts/{post}/files', 'Api\PostFileController@destroy');

/** Удаляет прикрепленный скриншот комментария от поста */
Route::delete('/posts/{post}/comment/screenshot', 'Api\PostCommentScreenshotController@destroy');
/** Удаляет прикрепленный скриншот комментария VK от поста */
Route::delete('/posts/{post}/comment/screenshot/vk', 'Api\PostCommentScreenshotController@vkdestroy');
/** Удаляет прикрепленный скриншот комментария OK от поста */
Route::delete('/posts/{post}/comment/screenshot/ok', 'Api\PostCommentScreenshotController@okdestroy');
/** Удаляет прикрепленный скриншот комментария FB от поста */
Route::delete('/posts/{post}/comment/screenshot/fb', 'Api\PostCommentScreenshotController@fbdestroy');
/** Удаляет прикрепленный скриншот комментария INSTA от поста */
Route::delete('/posts/{post}/comment/screenshot/insta', 'Api\PostCommentScreenshotController@instadestroy');



/**
 * База идей
 */

/** Прикрепляет файл к идее */
Route::post('/ideas/{idea}/files', 'Api\IdeaFileController@store');

/** Открепляет файл от идеи */
Route::delete('/ideas/{idea}/files', 'Api\IdeaFileController@destroy');
