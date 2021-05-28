<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\SocialNetwork
 *
 * @property int $id
 * @property string $name
 * @property string $link
 * @property-read \Plank\Mediable\MediableCollection|\App\Models\Post[] $posts
 * @property-read int|null $posts_count
 * @method static \Illuminate\Database\Eloquent\Builder|SocialNetwork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialNetwork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialNetwork query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialNetwork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialNetwork whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialNetwork whereName($value)
 * @mixin \Eloquent
 * @property string $slug
 * @property string $icon
 * @method static \Illuminate\Database\Eloquent\Builder|SocialNetwork whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialNetwork whereSlug($value)
 */
class SocialNetwork extends Model
{
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_social_network', 'social_network_id', 'post_id');
    }

    public function smmLinks() {
        return $this->belongsToMany(SocialNetwork::class, 'post_smm_links');
    }
}
