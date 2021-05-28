<?php

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IdeaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the idea.
     *
     * @param User $user
     * @param Idea $idea
     * @return mixed
     */
    public function view(User $user, Idea $idea)
    {
        return true;
    }

    /**
     * Определяет, может ли пользователь просматривать архивные идеи.
     *
     * @param User $user
     * @return bool
     */
    public function viewArchived(User $user)
    {
        return $user->hasRole('editor');
    }

    /**
     * Determine whether the user can create ideas.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }


    /**
     * Determine whether the user can edit ideas.
     *
     * @param User $user
     * @return mixed
     */
    public function edit(User $user)
    {
        return true;
    }



    /**
     * Determine whether the user can delete the idea.
     *
     * @param User $user
     * @param Idea $idea
     * @return mixed
     */
    public function delete(User $user, Idea $idea)
    {
        return $user->hasRole('editor');
    }

    /**
     * Определяет, может ли пользователь обновить идею.
     *
     * @param User $user
     * @param Idea $idea
     * @return bool
     */
    public function update(User $user, Idea $idea)
    {
        return $user->hasRole('editor');
    }
}
