<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Idea;
use App\Models\Project;
use App\Models\User;
use App\Http\Controllers\Instagram\Instagram;

class AjaxController extends Controller
{
    /**
     * Получаем журналистов из проекта
     * @param Requests\Ajax\GetJournalistInProjectRequest $request
     * @return JsonResponse
     */
    public function getJournalistsInProject(Requests\Ajax\GetJournalistInProjectRequest $request)
    {
        $validated = $request->validated();

        //$currentProject = Project::find($validated['project_id']);
        $id = $validated['project_id'];
        //$journalists = User::role('journalist')->get();
        $journalists = User::role('journalist')->whereHas(
            'projects', function ($q) use ($id) {
            $q->where('id', $id);
        }
        )->get();

        //return response()->json(array('msg'=> $request), 200);
        return response()->json($journalists);
    }
}
