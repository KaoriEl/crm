<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Barchart\Laravel\RememberAll\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Symfony\Component\Intl\Timezones;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $api_token
 * @property string $timezone
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $phone
 * @property string|null $telegram_id
 * @property-read string $gravatar
 * @property-read string $timezone_translated
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Plank\Mediable\MediableCollection|\App\Models\Post[] $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Barchart\Laravel\RememberAll\RememberToken[] $rememberTokens
 * @property-read int|null $remember_tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User permission($permissions)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereApiToken($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereTelegramId($value)
 * @method static Builder|User whereTimezone($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'timezone', 'phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'roles', 'projects',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Все статьи, которые были назначены пользователю для написания.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }



    /**
     * Get the user's gravatar.
     *
     * @return string
     */
    public function getGravatarAttribute()
    {
        $hash = md5(strtolower(trim($this->username)));

        return "https://www.gravatar.com/avatar/$hash?d=retro";
    }

    /**
     * Вовращает локализированное название часового пояса.
     *
     * @return string
     */
    public function getTimezoneTranslatedAttribute()
    {
        if (!$this->timezone || !Timezones::exists($this->timezone)) {
            return '';
        }

        return sprintf('%s %s',
            Timezones::getGmtOffset($this->timezone),
            Timezones::getName($this->timezone, config('app.locale'))
        );
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
    /**
     * Проверяет есть ли связанный проект в коллекции
     *
     * @return boolean
     */
    public function hasProject(Project $project)
    {
        $return = false;
        foreach ($project->users as $projectuser){
            if ($this->id === $projectuser->id) $return = true;
        }
        return $return;
    }
}
