<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;





Route::middleware(['apacheAuth'])->group(function() {
    Auth::routes(['reset' => false, 'register' => false]);
    Route::get('/projects', 'ProjectController@index')->name('projects.index');

    /** Отключим сброс паролей и регистрацию */
    Route::get('/', 'PostController@index')->name('posts.index');

    /** Обозреватель логов */
    Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware('auth');

    /** Открывает страницу для просмотра профиля */
    Route::get('/profile', 'ProfileController@index')->name('profile');

    /**
     * Аккаунты инстаграма
     */
    Route::get('/profile/instagram', 'ProfileInstagramController@index')->name('profile.instagram');

    /**
     * Аккаунты вк
     */
    Route::get('/profile/vk', 'ProfileVKController@index')->name('profile.vk');

    /**
     * Аккаунты вк
     */
    Route::get('/profile/ok', 'ProfileOKController@index')->name('profile.ok');

    /**
     * Аккаунты телеграма
     */
    Route::get('/profile/telegram', 'Madeline\MadelineController@index')->name('profile.telegram');
    Route::delete('/profile/telegram/{id}', 'Madeline\MadelineController@destroy')->name('profile.telegram.destroy');

    /** Авторизация профиля с публикациями */
    Route::post('/authUser', 'ProfileInstagramController@authorizeInstagram');
    Route::get('/testVK', 'VK\ParsingVK@parse');
    /** Авторизация профиля vk */
    Route::post('/authUserVK/{token}', 'VK\AuthVK@authorizeVK');
    /** Авторизация профиля ok */
    Route::post('/authUserOK/{token}', 'OK\AuthOKController@authorizeOK');

    Route::post('/authMainUser', 'ProfileController@authorizeInstagram');

    /** Обновляет информацию о пользователе из страницы профиля */
    Route::patch('/profile', 'ProfileController@update')->name('profile.update');

    /** Обновляет пароль пользователя */
    Route::patch('/profile/password', 'ProfileController@password')->name('profile.password');

    Route::get('/madeline/start', 'Madeline\ApiMadeline@get');
    /*
     *
     * Инстаграм
     *
     */

    Route::get('/insta', 'Instagram\Instagram@test');


    Route::get('/test', function () {
        (new \App\Statistics\CronUpdateStatistic())->getSMMLinks();
    });
    /*
     * Excel
     *
     */

    Route::get('/authGoogle', 'Excel\Service\AuthGoogle@getClient');
    Route::get('/authCodeGoogle', 'Excel\Service\AuthGoogle@authCode');
    Route::get('/takeSpreead', 'Excel\WorkWithExcel@takeSpreadSheet');
    Route::get('/excel/updateLinks', 'Excel\Service\CronUpdate@getUpdatesForCron');
    Route::get('/excel/create', 'Excel\WorkWithExcel@exportPostsData')->name('excel.export');
    /**
     * База идей
     */

    /** Открывает страницу со списком идей */
    Route::get('/ideas', 'IdeaController@index')->name('ideas.index');

    /** Открывает страницу со список архивированных идей */
    Route::get('/ideas/archived', 'ArchivedIdeaController@index')->name('ideas.archived.index');

    /** Открывает страницу для создания идеи */
    Route::get('/ideas/create', 'IdeaController@create')->name('ideas.create');
    /** Открывает страницу для редактировании идеи */
    Route::get('/ideas/{idea}/edit', 'IdeaController@edit')->name('ideas.edit');
    /** Добавляет идею в БД */
    Route::post('/ideas', 'IdeaController@store')->name('ideas.store');
    /** Редактируем идею в БД */
    Route::post('/ideas/{idea}', 'IdeaController@editIdea')->name('ideas.editIdea');

    /** Удаляем идею в БД */
    Route::delete('/ideas/delete/{idea}', 'IdeaController@deleteIdea')->name('ideas.deleteIdea');


    /** Открывает детальную страницу для просмотра идеи */
    Route::get('/ideas/{idea}', 'IdeaController@show')->name('ideas.show');

    /** Архивировать идею */
    Route::patch('/ideas/{idea}', 'ArchivedIdeaController@store')->name('ideas.archived.store');

    /** Возвращает идею из архива */
    Route::delete('ideas/{idea}', 'ArchivedIdeaController@destroy')->name('ideas.archived.destroy');

    /**
     * Пользователи
     */

    /** Открывает страницу для просмотра списка пользователей */
    Route::get('/users', 'UserController@index')->name('users.index');

    /** Открывает страницу для создания пользователя */
    Route::get('/users/create', 'UserController@create')->name('users.create');

    /** Открывает страницу для просмотра информации о пользователе */
    Route::get('/users/{user}', 'UserController@edit')->name('users.show');

    /** Обновляет информацию о пользователе */
    Route::patch('/users/{user}', 'UserController@update')->name('users.update');

    /** Добавляет пользователя в БД */
    Route::post('/users', 'UserController@store')->name('users.store');


    /**
     * Посты
     */

    /** Открывает страницу со списком постов */


    /** Открывает страницу создания поста */
    Route::get('/posts/create', 'PostController@create')->name('posts.create');

    /** Добавляет пост в БД */
    Route::post('/posts', 'PostController@store')->name('posts.store');

    /** Открывает страницу со списком архивных постов */
    Route::get('/posts/archived', 'ArchivedPostController@index')->name('posts.archived.index');

    /** Открывает детальную страницу поста */
    Route::get('/posts/{post}', 'PostController@show')->name('posts.show');

    /** Открывает страницу редактирования поста */
    Route::get('/posts/{post}/edit', 'PostController@edit')->name('posts.edit');

    /** Обновляет информацио о посте (страница редактирования поста) */
    Route::put('/posts/{post}/', 'PostController@put')->name('posts.put');

    /** Обновляет информацию о посте (детальная страница поста) */
    Route::patch('/posts/{post}', 'PostController@update')->name('posts.update');

    Route::get('posts/{post}/copy', 'PostController@copy')->name('posts.copy');

    /** Назначает исполнителя на пост */
    Route::patch('/posts/{post}/assignee', 'PostAssigneeController@store')->name('posts.assignee.store');

    /** Обновляет исполнителя на пост */
    Route::put('/posts/{post}/assignee', 'PostAssigneeController@update')->name('posts.assignee.update');

    /** Добавляет ссылку на черновик к поста */
    Route::patch('/posts/{post}/draft', 'PostDraftController@update')->name('posts.draft.store');

    /** Отправляет черновик на доработку */
    Route::delete('/posts/{post}/moderated', 'ModeratedPostController@destroy')->name('posts.moderated.delete');

    /** Отправляет черновик на пост */
    Route::post('/posts/{post}/moderated', 'ModeratedPostController@update')->name('posts.moderated.store');

    /** Добавляет ссылку на пост к статье */
    Route::post('/posts/{post}/published', 'PublishedPostController@store')->name('posts.published.store');

    /** Перемещает пост в архив */
    Route::post('/posts/{post}/archived', 'ArchivedPostController@store')->name('posts.archived.store');

    /** Копирует пост из архива */

    Route::get('posts/{post}/archived/copy', 'PostController@copy')->name('posts.archived.copy');


    /** Отправляет пост в работу */
    Route::post('/posts/{post}/status', 'PostController@updateStatus')->name('posts.update.status');

    /** Возвращает пост из архива */
    Route::delete('/posts/{post}/archived', 'ArchivedPostController@destroy')->name('posts.archived.delete');

    /** Удаляет комментарий от главного редактора */
    Route::delete('/posts/{post}/comment', 'ModeratedPostController@uncomment')->name('posts.moderated.uncomment');

    /**
     * Проекты
     */

    /** Показывает все проекты */
//Route::get('/projects', 'ProjectController@index')->name('projects.index');

    /** Открывает страницу для создания проекта */
    Route::get('/projects/create', 'ProjectController@create')->name('projects.create');

    /** Просмотр проекта с ID */
    Route::get('/projects/{project}', 'ProjectController@edit')->name('projects.edit');

    /** Добавляет проект в БД */
    Route::post('/projects', 'ProjectController@store')->name('projects.store');

    /** Обновляет информацию о проекте (детальная страница проекта) */
    Route::post('/projects/{project}', 'ProjectController@update')->name('projects.update');


    /** Архивировать проект */
    Route::post('/projects/archive/{project}', 'ArchivedProjectController@store')->name('projects.archived.store');

    /** Убрать пользователя из проекта */
    Route::post('/projects/{project}/remove', 'ProjectController@user_remove')->name('projects.user.remove');

    Route::get('/madeline/getPost', 'Madeline\ApiMadeline@getClient');
    Route::get('/telegram/getUpdate', 'Telegram\TelegramBot@getUpdate');
    /** сортировка по проекту и дате */
    Route::get('/statistics/show_table_project/{statistic}', 'StatisticController@ShowPostTableProject')->name('statistics.post_project');
    /** Сортировка только по дате */
    Route::get('/statistics/show_table', 'StatisticController@ShowPostTableAll')->name('statistics.sort_table');

    Route::get('statistics', 'StatisticController@index')->name('statistics.publications');

    /** Сортировка архива по датам */
    Route::get('/posts/archived/sort', 'ArchivedPostController@index')->name('arhived.index');

});

Route::post('/madeline/auth-madeline', 'Madeline\MadelineController@authPhone');

/** AJAX */

Route::namespace('Ajax')->prefix('get')->group(function() {
    Route::post('social-network/{id}', 'CreatePostAjaxController@getSocialNetwork');
    Route::post('getStatisticSocialNetwork/{id}', 'GetStatisticsSocialNetwortksAjaxController@getStatThisPost')->name('statistics.getStatThisPost');
});


Route::get('ajax',function(){
    return view('posts.create');
});
Route::post('/getmsg', 'AjaxController@getJournalistsInProject');



/**
 * Telegram
 */
Route::get('/telegram/updated-activity', 'TelegramBotController@updatedActivity');
Route::get('/telegram/take-message', 'TelegramBotController@TakeMessageUser');
Route::get('/telegram', 'TelegramBotController@sendMessage');
Route::post('/telegram/send-message', 'TelegramBotController@storeMessage');
Route::get('/telegram/send-photo', 'TelegramBotController@sendPhoto');
Route::post('/telegram/store-photo', 'TelegramBotController@storePhoto');
Route::get('/telegram/bot/set-webhook', 'TelegramBotController@setWebhook')->name('bot-set-webhook');
Route::get('/telegram/bot/remove-webhook', 'TelegramBotController@removeWebhook')->name('bot-remove-webhook');
Route::post('/telegram/bot/webhook', 'Telegram\TelegramBot@webhookHandler')->name('bot-webhook');



//Route::get('/telegram/updated-activity', 'TelegramBotController@updatedActivity');
