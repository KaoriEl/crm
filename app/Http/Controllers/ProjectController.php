<?php

namespace App\Http\Controllers;


use App\Http\Requests\Project\PutProjectRequest;
use App\Http\Requests\Project\StoreProject;
use App\Models\Project;
use App\Models\User;
use App\Models\Idea;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $timezones = cache()->rememberForever('timezones', function () {
            return timezones();
        });
        $projects = Project::with('users')->notArchived()->paginate(20);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|Response|View
     * @throws AuthorizationException
     */
    public function create()
    {
        /*$project = new Project();
        $project->name = $name;
        $project->description = 'Тестовый';

        $project->save();

        $user = User::find([2]);
        $project->users()->attach($user);

        $projects = Project::with('users')->paginate();
        return view('projects.index', compact('projects'));*/

        $this->authorize('create', Project::class);

        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProject $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(StoreProject $request): RedirectResponse
    {
        $user = auth()->user();

        $this->authorize('create', Project::class);
        $project = new Project();
        $project->fill($request->validated());
        $project->save();

        return redirect()->route('projects.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Project $project
     * @return Application|Factory|Response|View
     * @throws AuthorizationException
     */
    public function edit(Project $project)
    {
        $this->authorize('create', Project::class);

        $users = $project->load('users');

        $timezones = cache()->rememberForever('timezones', function () {
            return timezones();
        });

        return view('projects.edit', compact('project', 'users', 'timezones'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Project $project
     * @param PutProjectRequest $request
     * @return RedirectResponse
     */
    public function put(Project $project, PutProjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $project->fill($validated);
        $project->save();

        return redirect()->route('projects.index');
    }

    public function update(Request $request, $id)
    {
        $project = Project::find($id);
        $project->fill($request->all());
        $project->save();
        return redirect()->route('projects.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Удалить пользователя
     *
     * @param  Project
     * @return Response
     */
    public function user_remove(Project $project)
    {
        $this->authorize('create', Project::class);
        $validated = request()->validate([
            'user' => 'required|min:1'
        ]);

        $user = User::find([$validated]);
        $project->users()->detach($user);

        return back()->with('message', 'Информация успешно обновлена');
    }
}
