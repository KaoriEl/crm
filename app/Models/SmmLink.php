<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmmLink extends Model
{
    protected $table = 'post_smm_links';

    public function socialNetwork() {
        return $this->belongsTo(SocialNetwork::class);
    }

    public function StatisticSocialNetwork() {
        return $this->hasMany(StatisticSocialNetwork::class);
    }

    public function post() {
        return $this->belongsTo(Post::class);
    }
}
