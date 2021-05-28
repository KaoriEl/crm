<?php


namespace App\UseCases\Posts;


use App\Models\Post;
use App\Models\User;

class CrudPostUseCase
{
    /**
     * @param Post $post
     * @param array $socialNetworks
     * @return array
     */
    public static function syncSocialNetworks(Post $post, array $socialNetworks): array
    {
        $syncSocialNetworks = collect($socialNetworks)->filter(function ($value, $key) {
            return $value['price'] !== null && (int)$value['price'] !== 0;
        });



        return $post->socialNetworks()->sync($syncSocialNetworks);
    }

    public static function syncCommercialSeedNetworks(Post $post, array $socialNetworksSeed): array
    {
//        $syncSocialNetworks = collect($socialNetworks);

        return $post->seedLinks()->sync($socialNetworksSeed);
    }
}
