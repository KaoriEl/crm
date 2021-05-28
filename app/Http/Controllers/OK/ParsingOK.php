<?php

namespace App\Http\Controllers\OK;

use App\Models\OKAccounts;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Excel\Parsing;
use Illuminate\Http\Request;
use App\Models\StatisticSocialNetwork;
use App\Models\Jobs_cron;
use function Amp\File\exists;

class ParsingOK implements Parsing
{
    /**
     * Общий интерфейс для парсинга, принимающий ссылки на посты.
     * @param $links
     */
    public function parse($links)
    {

        // TODO: Implement parse() method.
    }

    /**
     * Точка входа для парсинга
     * @param $links
     * @param $job_name
     */
    public function parseSMMLinks($links,$job_name)
    {
        $pars_info = [];
        $client = new \GuzzleHttp\Client();
        $accounts = OKAccounts::where('status', 1)->get();
        foreach ($links as $link){
            $linkId = $link["id"];
            $link = $link["link"];

            $postId = $this->RegexPostID($link);
            $pars_info = $this->GetInfoPost($postId,$client,$pars_info,$accounts);
            $groupId = $this->RegexGroupID($link,$pars_info);
            $pars_info = $this->GetInfoAboutGroup($accounts,$client,$groupId, $pars_info);
            $this->SaveParsInfo($pars_info, $linkId, $job_name);
            dump("Успешно распарсил пост");
        }

    }

    /**
     * Получаю регуляркой id группы
     * @param $link
     * @return mixed
     */
    public function RegexGroupID($link,$pars_info){
        $re = '#group:(\d+)#';
        $str = $pars_info["post"]["group_id"];
        preg_match_all($re, $str, $groupId, PREG_SET_ORDER, 0);
        return $groupId[0][1];
    }

    /**
     * Получаю регуляркой id поста
     * @param $link
     * @return mixed
     */
    public function RegexPostID($link){
        $re = '#topic/(\d+)#m';
        $str = $link;
        preg_match_all($re, $str, $postId, PREG_SET_ORDER, 0);
        return $postId[0][1];
    }

    /**
     * Отправляю запрос по апи и получаю информацию о группе
     * @param $accounts
     * @param $client
     * @param $groupId
     * @param $pars_info
     * @return mixed
     */
    public function GetInfoAboutGroup($accounts,$client,$groupId,$pars_info){
        foreach ($accounts as $account){

            //По апи надо шифровать запрос
            $secret_key =  md5($account["token"] . "ECBC3062E925E0126232B120");
            $sig  = md5("application_key=CCGCCFKGDIHBABABAfields=NAME,MEMBERS_COUNTformat=jsonmethod=group.getInfouids=" . $groupId . $secret_key ."");
            $url = "https://api.ok.ru/fb.do?application_key=CCGCCFKGDIHBABABA&fields=NAME%2CMEMBERS_COUNT&format=json&method=group.getInfo&uids=" . $groupId . "&sig=" . $sig . "&access_token=". $account["token"] . "";

            $request = new \GuzzleHttp\Psr7\Request('POST', $url);
            $response = $client->send($request);
            $jsonResponse = $response->getBody()->getContents();
            $obj = json_decode($jsonResponse);

            $pars_info["group"]["name"] = $obj[0]->{'name'};
            $pars_info["group"]["members_count"] = $obj[0]->{'members_count'};
            sleep(5);
            return $pars_info;
        }

    }

    /**
     * Получаю информацию о посте
     * @param $postId
     * @param $client
     * @param $pars_info
     * @param $accounts
     * @return mixed
     */
    public function GetInfoPost($postId,$client,$pars_info,$accounts){
//        $ok_data=[
//            'access_token' => 'tkn1AxR4S2PgKGQrYYVgjqxxf89vFieuischei5S7lmRYcT0E3qWvjZ7zhi9cL1TtX6ij',
//            'secret_session_key' => '5d880f5e0678ffb91bc6d59f534c1c50',
//            'application_id'=>'1279363328',
//            'application_key'=>'CBAKICENEBABABABA',
//            'application_secret_key'=>'88F820D9ED89E53C89C5EFB2',
//            'app_href'=>'https://ok.ru/game/1279363328',
//        ];
        foreach ($accounts as $account){
        $secret_key =  md5($account["token"] . "ECBC3062E925E0126232B120");
        $sig  = md5("application_key=CCGCCFKGDIHBABABAfields=media_topic.*format=jsonmethod=mediatopic.getByIdstopic_ids=" . $postId . $secret_key ."");
        $url = "https://api.ok.ru/fb.do?application_key=CCGCCFKGDIHBABABA&fields=media_topic.*&format=json&method=mediatopic.getByIds&topic_ids=". $postId ."&sig=".$sig."&access_token=". $account["token"] ."";
        $request = new \GuzzleHttp\Psr7\Request('POST', $url);

        $response = $client->send($request);
        $jsonResponse = $response->getBody()->getContents();
        $obj = json_decode($jsonResponse);
        $pars_info["post"]["group_id"] = $obj->{"media_topics"}[0]->{"owner_ref"};
        $pars_info["post"]["text"] = $obj->{"media_topics"}[0]->{"media"}[0]->{"text"};
        $pars_info["post"]["comments_count"] = $obj->{"media_topics"}[0]->{"discussion_summary"}->{"comments_count"};
        $pars_info["post"]["comments_count"] = $obj->{"media_topics"}[0]->{"discussion_summary"}->{"comments_count"};
        $pars_info["post"]["like"] = $obj->{"media_topics"}[0]->{"like_summary"}->{"count"};
        $pars_info["post"]["reshare"] = $obj->{"media_topics"}[0]->{"reshare_summary"}->{"count"};
        if (isset($obj->{"media_topics"}[0]->{"views_count"})){
            $pars_info["post"]["views"] = $obj->{"media_topics"}[0]->{"views_count"};
        }else{
            $pars_info["post"]["views"] = 0;
        }
        sleep(5);
        return $pars_info;
        }
    }

    /**
     * Сохранение в бд инфы о посте
     * @param $pars_info
     * @param $linkId
     * @param $job_name
     */
    public function SaveParsInfo($pars_info,$linkId,$job_name)
    {

        $pars_info["post"]['text'] = mb_strimwidth($pars_info["post"]['text'], 0, 50, "...");

        if ($job_name == "update_social_statistic"){
            if(StatisticSocialNetwork::where('post_smm_links_id', $linkId)->count() > 0) {
                $statisticSMMLink = StatisticSocialNetwork::where('post_smm_links_id', $linkId)->first();
                $statisticSMMLink->update([
                    'views' => $pars_info["post"]['views'],
                    'like' => $pars_info["post"]['like'],
                    'reposts' => $pars_info["post"]['reshare'],
                    'count_comments' => $pars_info["post"]['comments_count'],
                    'followers' => $pars_info["group"]['members_count'],
                ]);
            }else{
                $statisticSMMLink = StatisticSocialNetwork::create([
                    'post_smm_links_id' => $linkId,
                    'post_snippet' => $pars_info["post"]['text'],
                    'views' => $pars_info["post"]['views'],
                    'like' => $pars_info["post"]['like'],
                    'reposts' => $pars_info["post"]['reshare'],
                    'count_comments' => $pars_info["post"]['comments_count'],
                    'followers' => $pars_info["group"]['members_count'],
                    'acc_name' => $pars_info["group"]['name']
                ]);
                $statisticSMMLink->save();
            }


        }
        if ($job_name == "update_comercial_seeder_statistic"){

            if(StatisticSocialNetwork::where('post_seeds_links_id', $linkId)->count() > 0) {
                $statisticSMMLink = StatisticSocialNetwork::where('post_seeds_links_id', $linkId->id)->first();
                $statisticSMMLink->update([
                    'views' => $pars_info["post"]['views'],
                    'like' => $pars_info["post"]['like'],
                    'reposts' => $pars_info["post"]['reshare'],
                    'count_comments' => $pars_info["post"]['comments_count'],
                    'followers' => $pars_info["group"]['members_count'],
                ]);
            }else{
                $statisticSMMLink = StatisticSocialNetwork::create([
                    'post_seeds_links_id' => $linkId,
                    'post_snippet' => $pars_info["post"]['text'],
                    'views' => $pars_info["post"]['views'],
                    'like' => $pars_info["post"]['like'],
                    'reposts' => $pars_info["post"]['reshare'],
                    'count_comments' => $pars_info["post"]['comments_count'],
                    'followers' => $pars_info["group"]['members_count'],
                    'acc_name' => $pars_info["group"]['name']
                ]);
                $statisticSMMLink->save();
            }
        }
    }
}
