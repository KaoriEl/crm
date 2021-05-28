<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class CreatePostAjaxController extends Controller
{
    /**
     * Получение соц сетей для публикации по проекту
     *
     * @param int $id id проекта
     * @return array $arr массив селекторов для постинга
     * */
    public function getSocialNetwork($id)
    {
        $project = Project::find($id);

        $arr[] = ($project->vk) ? 'posting_to_vk' : null;
        $arr[] = ($project->ok) ? 'posting_to_ok' : null;
        $arr[] = ($project->fb) ? 'posting_to_fb' : null;
        $arr[] = ($project->insta) ? 'posting_to_ig' : null;
        $arr[] = ($project->tg) ? 'posting_to_tg' : null;
        $arr[] = ($project->y_street) ? 'posting_to_y_street' : null;
        $arr[] = ($project->y_dzen) ? 'posting_to_y_dzen' : null;
        $arr[] = ($project->yt) ? 'posting_to_yt' : null;
        $arr[] = ($project->tt) ? 'posting_to_tt' : null;
        return  $arr;
    }
}
