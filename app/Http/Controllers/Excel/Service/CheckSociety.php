<?php


namespace App\Http\Controllers\Excel\Service;


class CheckSociety
{
    /**
     * Проверяем доабвлена ли ссылка на пост
     * @param $post
     * @return string
     */

    public function checkSMMLinks($post) {
        $links_to_society = '';

        if(!$post->posting) {
           return $links_to_society = 'Нет';
        }

        ($post->vk_post_url) ? $links_to_society .= $post->vk_post_url . "\n" : ' ';
//        ($post->tt_post_url) ? $links_to_society .= $post->tt_post_url . "\n" : ' ';
        ($post->ok_post_url) ? $links_to_society .= $post->ok_post_url . "\n" : ' ';
        ($post->fb_post_url) ? $links_to_society .= $post->fb_post_url . "\n" : ' ';
        ($post->ig_post_url) ? $links_to_society .= $post->ig_post_url . "\n" : ' ';
        ($post->yt_post_url) ? $links_to_society .= $post->yt_post_url . "\n" : ' ';
        ($post->tg_post_url) ? $links_to_society .= $post->tg_post_url . "\n" : ' ';
        ($post->y_street_post_url) ? $links_to_society .= $post->y_street_post_url . "\n" : ' ';
        ($post->y_dzen_post_url) ? $links_to_society .= $post->y_dzen_post_url . "\n" : ' ';

        return $links_to_society;
    }

    /**
     * Проверяем доабвлена ли ссылка на публикацию
     * @param $post
     * @return string
     */

    public function checkPublicationUrl($post) {
        ($post->publication_url) ? $publication_url = $post->publication_url : $publication_url = 'Публикация не добавлена';

        return $publication_url;
    }

    /**
     * Проверяем доабвлена ли ссылка на посевы
     * @param $post
     * @return string
     */

    public function checkSeederLink($post) {
        ($post->seed_list_url) ? $post->seed_list_url : $post->seed_list_url = 'Ссылка на посевы не добавлена';
        return $post->seed_list_url;
    }

    /**
     * Проверяем запущен ли таргет
     * @param $post
     * @return string
     */

    public function checkTargetOn($post) {
        $target_to_society = '';
        if(!$post->targeting) {
            return $target_to_society .= 'Нет';
        }

        ($post->targeting_to_vk) ? $target_to_society .= ' ВК ' . "\n" : ' ';
        ($post->targeting_to_ok) ? $target_to_society .= ' OK ' . "\n" : ' ';
        ($post->targeting_to_fb) ? $target_to_society .= ' FB ' . "\n" : ' ';
        ($post->targeting_to_ig) ? $target_to_society .= ' Insta ' . "\n" : ' ';
        ($post->targeting_to_yt) ?  $target_to_society .= ' YT ' . "\n" : ' ';
        ($post->targeting_to_tg) ?  $target_to_society .= ' TG ' . "\n" : ' ';
        ($post->targeting_to_y_street) ?  $target_to_society .= ' Я.Улица ' . "\n" : ' ';
        ($post->targeting_to_y_dzen) ?  $target_to_society .= ' Я.Дзен ' . "\n" : ' ';

        $target_to_society .= "\n " . 'Запущено: ';

        ($post->targeted_to_vk) ? $target_to_society .= 'ВК ' : ' ';
        ($post->targeted_to_ok) ? $target_to_society .= 'OK ' : ' ';
        ($post->targeted_to_fb) ? $target_to_society .= 'FB ' : ' ';
        ($post->targeted_to_ig) ? $target_to_society .= 'Insta '  : ' ';
        ($post->targeted_to_yt) ?  $target_to_society .= 'YT '  : ' ';
        ($post->targeted_to_tg) ?  $target_to_society .= 'TG ' : ' ';
        ($post->targeted_to_y_street) ?  $target_to_society .= 'Я.Улица '  : ' ';
        ($post->targeted_to_y_dzen) ?  $target_to_society .= 'Я.Дзен '  : ' ';

        return $target_to_society;
    }


}
