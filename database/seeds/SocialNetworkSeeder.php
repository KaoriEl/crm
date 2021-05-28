<?php

use App\Models\SocialNetwork;
use Illuminate\Database\Seeder;

class SocialNetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $networks = [
            [
                'name' => 'ВК',
                'link' => 'https://vk.com/',
                'icon' => 'img/vk-medium.png',
                'slug' => 'vk',
            ],
            [
                'name' => 'ОК',
                'link' => 'https://ok.ru/',
                'icon' => 'img/ok-medium.png',
                'slug' => 'ok',
            ],
            [
                'name' => 'FB',
                'link' => 'https://http://www.facebook.com//',
                'icon' => 'img/fb-medium.png',
                'slug' => 'fb',
            ],
            [
                'name' => 'Insta',
                'link' => 'https://www.instagram.com//',
                'icon' => 'img/ig-medium.png',
                'slug' => 'ig',
            ],
            [
                'name' => 'Я.Д',
                'link' => 'https://zen.yandex.ru/',
                'icon' => 'img/dzen-medium.png',
                'slug' => 'y_dzen',
            ],
            [
                'name' => 'Я.Р',
                'link' => 'https://vk.com/',
                'icon' => 'img/ya-medium.png',
                'slug' => 'y_street',
            ],
            [
                'name' => 'YT',
                'link' => 'https://www.youtube.com/',
                'icon' => 'img/yt-medium.png',
                'slug' => 'yt',
            ],
            [
                'name' => 'TG',
                'link' => 'https://telegram.org/',
                'icon' => 'img/tg-medium.png',
                'slug' => 'tg',
            ],
            [
                'name' => 'TT',
                'link' => 'https://www.tiktok.com/',
                'icon' => 'img/tt-medium.png',
                'slug' => 'tt',
            ],
        ];
        SocialNetwork::insert($networks);
    }
}
