<?php

namespace App\Models;

use   Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatisticSocialNetwork extends Model
{
    protected $table = 'social_media_statistics_in_dashboard';

    protected $fillable = ['post_seeds_links_id','post_smm_links_id', 'post_snippet', 'views', 'like', 'count_comments', 'reposts', 'followers', 'acc_name'];


    /**
     * Связь с постами
     *
     * @return HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function smmLinks() {
        return $this->belongsToMany(SmmLink::class);
    }

}
