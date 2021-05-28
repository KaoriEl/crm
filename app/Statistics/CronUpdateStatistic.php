<?php


namespace App\Statistics;


use App\Http\Controllers\Instagram\ParsingInstagram;
use App\Http\Controllers\OK\ParsingOK;
use App\Http\Controllers\VK\ParsingVK;
use App\Models\ModelsSeedLinks;
use App\Models\SmmLink;
use Carbon\Carbon;

class CronUpdateStatistic
{
    public function __construct()
    {

    }


    public function getSMMLinks($job_name)
    {
        $arrSMMLinks = [];
//        $arrSMMLinks['Insta'] = SmmLink::whereHas('socialNetwork', function ($q) {
//            $q->where('slug', 'ig');
//        })->get();
        $arrSMMLinks['vk'] = SmmLink::whereHas('socialNetwork', function ($q) {
            $q->where('slug', 'vk');
        })->get();
//        $arrSMMLinks['ok'] = SmmLink::whereHas('socialNetwork', function ($q) {
//            $q->where('slug', 'ok');
//        })->get();


        foreach($arrSMMLinks as $social => $links) {
            foreach($links as $key => $link) {
                $time_post = $link->post()->pluck('created_at')->first();
                $time_post = Carbon::parse($time_post)->timestamp;
                if(($time_post + (168 * 60 * 60)) < strtotime('now')) {
                    unset($arrSMMLinks[$social][$key]);

                }
            }
        }

        foreach($arrSMMLinks as $social => $links) {
            switch($social) {
                case 'vk':
                    $obj = new ParsingVK();
                    break;
//                case 'Insta':
//                    $obj = new ParsingInstagram();
//                    break;
//                case 'ok':
//                    $obj = new ParsingOK();
//                    break;
            }
            $obj->parseSMMLinks($links, $job_name);
        }
    }


    public function getComercialSeederLinks($job_name)
    {
        $arrComercialSeedLinks = [];
        $arrComercialSeedLinks['Insta'] = ModelsSeedLinks::whereHas('socialNetwork', function ($q) {
            $q->where('slug', 'ig');
        })->get();
        $arrComercialSeedLinks['vk'] = ModelsSeedLinks::whereHas('socialNetwork', function ($q) {
            $q->where('slug', 'vk');
        })->get();
        $arrComercialSeedLinks['ok'] = ModelsSeedLinks::whereHas('socialNetwork', function ($q) {
            $q->where('slug', 'ok');
        })->get();

        foreach($arrComercialSeedLinks as $social => $links) {
            foreach($links as $key => $link) {
                $time_post = $link->post()->pluck('created_at')->first();
                $time_post = Carbon::parse($time_post)->timestamp;
                if(($time_post + (168 * 60 * 60)) < strtotime('now')) {
                    unset($arrComercialSeedLinks[$social][$key]);

                }
            }
        }
        foreach($arrComercialSeedLinks as $social => $links) {
            switch($social) {
                case 'vk':
                    $obj = new ParsingVK();
                    break;
                case 'Insta':
                    $obj = new ParsingInstagram();
                    break;
                case 'ok':
                    $obj = new ParsingOK();
                    break;
            }

            $obj->parseSMMLinks($links, $job_name);
        }
    }



}
