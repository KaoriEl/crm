<?php

namespace App\Models;

use App\Models\Post;
use App\Models\SocialNetwork;
use App\Models\StatisticSocialNetwork;
use Illuminate\Database\Eloquent\Model;

class ModelsSeedLinks extends Model
{
    protected $table = 'commercial_seed_links';

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
