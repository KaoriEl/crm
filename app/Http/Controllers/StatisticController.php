<?php

namespace App\Http\Controllers;

use App\Http\Requests\Statistic\ShowPublicationStatisticRequest;
use App\Models\Post;
use App\Models\Project;
use App\UseCases\Statistics\DateUseCase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StatisticController extends Controller
{
    public function index(ShowPublicationStatisticRequest $request, DateUseCase $dateService)
    {
$posts = [];
        $dateStart = $request->input('date_start', date('Y-m-d', time() - 604800));
        $dateEnd = $request->input('date_end', date('Y-m-d', time() + 86400));

        if ($dateStart == $dateEnd){
            $dateEnd = date("Y-m-d", strtotime($dateEnd.'+ 1 days'));
        }

        if ($dateStart > $dateEnd){
            $dates = 0;
            $statistics = 0;
            return view('statistics.publications', compact('dateStart', 'dateEnd', 'dates', 'posts','statistics','datesEndSort', 'projects'));
        }else{
            $dates = $dateService->getRangeDates($dateStart, $dateEnd);
            $datesEndSort = $dates;


            $projects = Project::with('posts')->whereHas('posts', function ($q) use ($dateStart, $dateEnd) {
                $q->whereBetween('publication_url_updated_at', [$dateStart, $dateEnd]);
            })->get();

            $statistics = [];

            foreach ($projects as $project) {

                $db = \DB::table('posts')->select([\DB::raw('DATE(posts.publication_url_updated_at) as date'), \DB::raw('count(*) as count')])
                    ->whereBetween('publication_url_updated_at', [$dateStart, $dateEnd])
                    ->where('project_id', '=', $project->id)
                    ->groupByRaw('DATE(posts.publication_url_updated_at)')
                    ->orderByRaw('date')
                    ->get()->pluck('count', 'date')->map(function ($value) use ($project) {
                        $resultArray['count'] = $value;
                        $resultArray['count'] >= $project->publication_rate ? $resultArray['class'] = 'text-success' : $resultArray['class'] = 'text-danger';
                        return $resultArray;
                    })->toArray();

                $statistics[$project->id] = [
                    'id'=> $project->id,
                    'name' => $project->name,
                    'dates' => $db
                ];
            }

            return view('statistics.publications', compact('dateStart', 'dateEnd', 'dates', 'posts','statistics','datesEndSort', 'projects'));
        }
    }

    /**
     * Сортировка публикаций по дате
     *
     * @param Post $post
     * @param $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function ShowPostTableAll(ShowPublicationStatisticRequest $request, DateUseCase $dateService) {
        $projects_user = Auth::user()->projects()->pluck('id');
        if(!Auth::user()->hasRole('admin')) {
            $posts = Post::PublishedStat()->where('project_id', '!=',10)->whereIn('project_id', $projects_user)->orderBy('publication_url_updated_at','desc')->paginate(200);
        } else {
            $posts = Post::PublishedStat()->where('project_id', '!=',10)->orderBy('publication_url_updated_at','desc')->paginate(200);
        }
        $dateStart = $request->input('date_start', date('Y-m-d', time() - 604800));
        $dateEnd = $request->input('date_end', date('Y-m-d', time() + 86400));

        if ($dateStart == $dateEnd){
            $dateEnd = date("Y-m-d", strtotime($dateEnd.'+ 1 days'));
        }

        if ($dateStart > $dateEnd){
            $dates = 0;
            $statistics = 0;
            return view('statistics.publications', compact('dateStart', 'dateEnd', 'dates', 'posts','statistics','datesEndSort', 'projects'));
        }else{
            $dates = $dateService->getRangeDates($dateStart, $dateEnd);
            $datesEndSort = $dates;
            $posts_each = $posts;
            $posts1 = [];
            $posts2 = [];
            foreach ($posts_each as $post){
                if ($post["publication_url_updated_at"] >= $dateStart){
                    if ($post["publication_url_updated_at"] <= $dateEnd){
                        $posts1[] = $post;
                    }
                }else{
                    $posts3[] = $post;
                }
            }
            $posts = array_merge($posts2, $posts1);
            $projects = Project::with('posts')->whereHas('posts', function ($q) use ($dateStart, $dateEnd) {
                $q->whereBetween('publication_url_updated_at', [$dateStart, $dateEnd]);
            })->get();

            $statistics = [];

            foreach ($projects as $project) {

                $db = \DB::table('posts')->select([\DB::raw('DATE(posts.publication_url_updated_at) as date'), \DB::raw('count(*) as count')])
                    ->whereBetween('publication_url_updated_at', [$dateStart, $dateEnd])
                    ->where('project_id', '=', $project->id)
                    ->groupByRaw('DATE(posts.publication_url_updated_at)')
                    ->orderByRaw('date')
                    ->get()->pluck('count', 'date')->map(function ($value) use ($project) {
                        $resultArray['count'] = $value;
                        $resultArray['count'] >= $project->publication_rate ? $resultArray['class'] = 'text-success' : $resultArray['class'] = 'text-danger';
                        return $resultArray;
                    })->toArray();

                $statistics[$project->id] = [
                    'id'=> $project->id,
                    'name' => $project->name,
                    'dates' => $db
                ];
            }

            return view('statistics.publications', compact('dateStart', 'dateEnd', 'dates', 'posts','statistics','datesEndSort', 'projects'));
        }
    }
    /**
     * Сортировка публикаций по проектам
     *
     * @param Post $post
     * @param $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function ShowPostTableProject(ShowPublicationStatisticRequest $request, DateUseCase $dateService) {

        $projects_user = Auth::user()->projects()->pluck('id');
        if(!Auth::user()->hasRole('admin')) {
            $posts = Post::PublishedStat()->where('project_id', '!=',10)->whereIn('project_id', $projects_user)->orderBy('publication_url_updated_at','desc')->paginate(200);
        } else {
            $posts = Post::PublishedStat()->where('project_id', '!=',10)->orderBy('publication_url_updated_at','desc')->paginate(200);
        }

        $dateStart = $request->input('date_start', date('Y-m-d', time() - 604800));
        $dateEnd = $request->input('date_end', date('Y-m-d', time() + 86400));

        if ($dateStart == $dateEnd){
            $dateEnd = date("Y-m-d", strtotime($dateEnd.'+ 1 days'));
        }

        if ($dateStart > $dateEnd){
            $dates = 0;
            $statistics = 0;
            return view('statistics.publications', compact('dateStart', 'dateEnd', 'dates', 'posts','statistics','datesEndSort', 'projects'));
        }else{
            $dates = $dateService->getRangeDates($dateStart, $dateEnd);
            $datesEndSort = $dates;

            $projects = Project::with('posts')->whereHas('posts', function ($q) use ($dateStart, $dateEnd) {
                $q->whereBetween('publication_url_updated_at', [$dateStart, $dateEnd]);
            })->get();

            $statistics = [];

            foreach ($projects as $project) {

                $db = \DB::table('posts')->select([\DB::raw('DATE(posts.publication_url_updated_at) as date'), \DB::raw('count(*) as count')])
                    ->whereBetween('publication_url_updated_at', [$dateStart, $dateEnd])
                    ->where('project_id', '=', $project->id)
                    ->groupByRaw('DATE(posts.publication_url_updated_at)')
                    ->orderByRaw('date')
                    ->get()->pluck('count', 'date')->map(function ($value) use ($project) {
                        $resultArray['count'] = $value;
                        $resultArray['count'] >= $project->publication_rate ? $resultArray['class'] = 'text-success' : $resultArray['class'] = 'text-danger';
                        return $resultArray;
                    })->toArray();

                $statistics[$project->id] = [
                    'id'=> $project->id,
                    'name' => $project->name,
                    'dates' => $db
                ];
            }
            $posts_each = $posts;
            $posts1 = [];
            $posts2 = [];
            $project_id = $request->route('statistic');
                foreach ($posts_each as $post){
                    if ($post["publication_url_updated_at"] >= $dateStart){
                        if ($post["publication_url_updated_at"] <= $dateEnd){
                            if ($post["project_id"] == $project_id ){
                                $posts1[] = $post;
                            }
                        }
                    }else{
                        $posts3[] = $post;

                    }
                }

            $posts = array_merge($posts2, $posts1);

            return view('statistics.publications', compact('dateStart', 'dateEnd', 'dates', 'posts','statistics','datesEndSort', 'projects'));
        }
    }

}
