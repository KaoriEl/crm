<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use App\Models\Temp_task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use App\Models\Post;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function index()
    {

        $this->authorize('viewAny', User::class);

        $users = User::with('roles')->paginate();
//

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $roles = Role::all();

        $projects = Project::all()->filter(function ($item) {
            return $item['archived_at'] == null;
        });
        $timezones = cache()->rememberForever('timezones', function () {
            return timezones();
        });
        $user = auth()->user();
        return view('users.create', compact('projects', 'user', 'roles', 'timezones'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store()
    {
        $this->authorize('create', User::class);

        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|min:5|unique:users',
            'email' => 'nullable|string|max:255|email|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'projects' => 'required|array|min:1',
            'projects.*' => 'nullable|exists:projects,id',
            'timezone' => 'required|timezone',
            'phone' => 'nullable|string',
        ]);

        $user = User::make($validated);

        $user->password = Hash::make($validated['password']);
        $user->save();

        $user->roles()->sync($validated['roles']);
        $user->projects()->sync($validated['projects']);

        return redirect()->route('users.index')->with('message', 'Новый пользователь успешно создан!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $user->load('roles');

        $roles = Role::all();

        $projects = Project::all()->filter(function ($item) {
            return $item['archived_at'] == null;
        });

        $timezones = cache()->rememberForever('timezones', function () {
            return timezones();
        });

        return view('users.edit', compact('projects', 'user', 'roles', 'timezones'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param User $user
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(User $user)
    {
        $this->authorize('update', $user);

        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
            'roles' => 'required|array|min:1',
            'roles.*' => 'nullable|exists:roles,id',
            'projects' => 'required|array|min:1',
            'projects.*' => 'nullable|exists:projects,id',
            'timezone' => 'required|timezone',
            'phone' => 'nullable|string',
        ]);
        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->timezone = $validated['timezone'];

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->roles()->sync($validated['roles']);

        $user->projects()->sync($validated['projects']);

        $user->save();

        return redirect()->route('users.index')->with('message', 'Информация о пользователе успешно обновлена!');
    }
}
