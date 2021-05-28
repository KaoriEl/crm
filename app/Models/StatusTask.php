<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StatusTask
 *
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Plank\Mediable\MediableCollection|Post[] $posts
 * @property-read int|null $posts_count
 * @method static \Illuminate\Database\Eloquent\Builder|StatusTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusTask whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusTask whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StatusTask extends Model
{
    /**
     * Задача для статуса
     * @return mixed
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
