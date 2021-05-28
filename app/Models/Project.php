<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * App\Models\Project
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $site
 * @property string|null $vk
 * @property string|null $ok
 * @property string|null $fb
 * @property string|null $insta
 * @property string|null $tg
 * @property string|null $yt
 * @property string|null $tt
 * @property string|null $y_street
 * @property string|null $y_dzen
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Project archived()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project notArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereArchivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereFb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereInsta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereTg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereVk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereYDzen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereYStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereYt($value)
 * @mixin \Eloquent
 * @property int $publication_rate Норма кол-ва публикаций в день для проекта
 * @method static \Illuminate\Database\Eloquent\Builder|Project wherePublicationRate($value)
 */
class Project extends Model
{
    protected $fillable = ['name', 'description', 'site', 'publication_rate', 'vk', 'ok', 'fb', 'insta', 'y_dzen', 'y_street', 'yt', 'tg','tt'];

    protected $dates = ['archived_at'];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Только архивные проекты.
     * @param $query
     * @return mixed
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Только те проекты, которые не находятся в архиве.
     * @param $query
     * @return mixed
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Добавляет проекты в архив.
     *
     * @return void
     */
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    /**
     * Возвращает проекты из архива.
     *
     * @return void
     */
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }
}
