<?php

namespace App\Http\Controllers;

use App\Http\Requests\Statistic\ShowPublicationStatisticRequest;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use App\UseCases\Statistics\DateUseCase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ArchivedPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Показывает страницу с архивными постами.
     *
     * @return Factory|View
     */
    public function index(ShowPublicationStatisticRequest $request, DateUseCase $dateService)
    {
        // Проверка на авторизацию для архива
        // $this->authorize('view-archived', Post::class);
        $projects_user = Auth::user()->projects()->pluck('id');
        if(!Auth::user()->hasRole('admin')) {
            $posts = Post::onlyArchived()->where('project_id', '!=',10)->whereIn('project_id', $projects_user)->orderBy('archived_at','desc')->paginate(200);
        } else {
            $posts = Post::onlyArchived()->where('project_id', '!=',10)->orderBy('archived_at','desc')->paginate(200);
        }



        //Фильтрация, да простит меня Никита за такую фильтрацию.
        $dateStart = $request->input('date_start', date('Y-m-d', time() - 604800));
        $dateEnd = $request->input('date_end', date('Y-m-d', time() + 86400));

        if ($dateStart == $dateEnd){
            $dateEnd = date("Y-m-d", strtotime($dateEnd.'+ 1 days'));
        }

        if ($dateStart > $dateEnd){
            $dates = 0;
            return view('posts.archived.index', compact('posts','dateStart', 'dateEnd'));

        }else{
            $dates = $dateService->getRangeDates($dateStart, $dateEnd);
            $posts_each = $posts;
            $posts1 = [];
            $posts2 = [];
            foreach ($posts_each as $post){
             if ($post["archived_at"] >= $dateStart){
                 if ($post["archived_at"] <= $dateEnd){
                         $posts1[] = $post;
                 }
             }else{
                 $posts3[] = $post;
             }
            }
            $posts = array_merge($posts2, $posts1);
            return view('posts.archived.index', compact('posts','dateStart', 'dateEnd'));
        }

    }

    /**
     * Перемещает публикацию в архив.
     *
     * @param Post $post
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Post $post)
    {
        $this->authorize('archive', $post);

        $post->archive();

        return back();
    }

    /**
     * Возвращает публикацию из архива.
     *
     * @param Post $post
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Post $post)
    {
        $this->authorize('archive', $post);

        $post->unarchive();

        return back();
    }
}
