<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any articles.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    public function viewArchived(User $user)
    {
        return $user->hasRole('editor');
    }

    /**
     * Определяет, может ли пользователь просматривать пост.
     *
     * @param User $user
     * @param Post $post
     * @return mixed
     */
    public function view(User $user, Post $post)
    {
        return $user->hasRole('editor') || $user->hasRole('journalist') || $post->published();
    }

    /**
     * Определяет, может ли пользователь создавать посты.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole('editor');
    }

    /**
     * Determine whether the user can update the article.
     *
     * @param User $user
     * @param Post $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        return $user->hasRole('editor');
    }

    /**
     * Определяет, может ли пользователь редактировать пост (страница редактирования поста).
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function put(User $user, Post $post)
    {
        return $user->hasRole('editor');
    }

    /**
     * Determine whether the user can delete the article.
     *
     * @param User $user
     * @param Post $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        //
    }

    /**
     * Determine whether the user can restore the article.
     *
     * @param User $user
     * @param Post $post
     * @return mixed
     */
    public function restore(User $user, Post $post)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the article.
     *
     * @param User $user
     * @param Post $post
     * @return mixed
     */
    public function forceDelete(User $user, Post $post)
    {
        //
    }

    /**
     * Определяет, может ли пользователь взять пост в работу или назначать исполнителя.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function assign(User $user, Post $post)
    {
        return $user->hasRole('editor') || $user->hasRole('journalist');
    }

    /**
     * Определяет, может ли пользователь отправить публикацию на доработку.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function rejectDraft(User $user, Post $post)
    {
        return $user->hasRole('editor');
    }

    /**
     * Определяет, может ли пользователь назначить себя или другого пользователя в качестве испольнителя.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function setAssignee(User $user, Post $post)
    {
        return $user->hasRole('editor') || $user->hasRole('journalist');
    }

    /**
     * Определяет, может ли пользователь добавить ссылку на черновик к публикации.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function setDraft(User $user, Post $post)
    {
        return $post->journalist_id !== null && ($user->hasRole('editor') || $user->id === $post->journalist_id);
    }

    /**
     * Определяет, может ли пользователь проверить публикаци.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function moderate(User $user, Post $post)
    {
        return $user->hasRole('editor');
    }

    /**
     * Определяет, может ли пользователь добавить ссылку на публикацию.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function setPublication(User $user, Post $post)
    {
        return $user->hasRole('editor') || ($user->hasRole('journalist') && $user->id === $post->journalist_id);
    }

    /**
     * Определяет, может ли пользователь переместить публикацию в архив.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function archive(User $user, Post $post)
    {
        return $user->hasRole('editor');
    }

    /**
     * Удаляет комментарий от главного редактора.
     *
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function uncomment(User $user, Post $post)
    {
        return $user->hasRole('editor');
    }
}
