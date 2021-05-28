<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Excel\Parsing;
use App\Models\Jobs_cron;
use App\Models\StatisticSocialNetwork;
use App\Models\VKAccounts;
use Illuminate\Http\Request;
use VK\Client\VKApiClient;
use VK\Exceptions\Api\VKApiAccessException;
use Session;

class ParsingVK implements Parsing
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
     * Получаем vk ссылки и парсим в зависимости от логина (статистика для дашборда)
     * @param $links
     * @param $job_name
     */
    public function parseSMMLinks($links,$job_name)
    {

        $accounts = VKAccounts::where('status', 1)->get();

        $vk = new VKApiClient();
        foreach($links  as $link) {
            $matches = $this->RegexPostInfo($link);
            $group_id_matches = $this->RegexGroupInfo($matches);
            $pars_info = $this->ParseVk($accounts,$matches,$group_id_matches,$vk,$link);
            if (!is_null($pars_info)){
                $this->SaveParsInfo($pars_info,$link, $job_name);
            }else{
                dump("Пустой парс инфо" . $link->link);
            }

        }

    }

    /**
     * Регуляркой разбираю ссылку и получаю полное id поста и группы.
     * @param $link
     * @return mixed
     */
    public function RegexPostInfo($link){
        //Смайлик, хы :D
        $slice = '/-(\d+_\d+)/m';
        $parsed = $link["link"];
        preg_match_all($slice, $parsed, $matches, PREG_SET_ORDER, 0);
        return $matches;
    }

    /**
     * Регуляркой разбираю id поста и группы и получаю ток id группы.
     * @param $matches
     * @return mixed
     */
    public function RegexGroupInfo($matches){
        $group = '/(\d+)/m';
        $group_id_find = $matches[0][0];
        preg_match_all($group, $group_id_find, $group_id_matches, PREG_SET_ORDER, 0);
        return $group_id_matches;
    }

    /**
     * Делаю запрос по АПИ и получаю необходимые данные для сбора удобного массива.
     * @param $accounts
     * @param $matches
     * @param $group_id_matches
     * @param $vk
     * @return array[]
     */
    public function ParseVk($accounts, $matches,$group_id_matches,$vk,$link){
        foreach ($accounts as $account){
            $post_id = $matches[0][0];
            $group_id = $group_id_matches[0][0];
            //Делаю запрос к АПИ через execute для экономии кол-ва запросов, аккаунт так дольше живет.
            $executeData = $vk->getRequest()->post('execute', $account->token, [
                'v' => "5.130",
                'code' => "var posts = API.wall.getById({\"posts\": \"$post_id\"});
                    var group = API.groups.getMembers({\"group_id\": \"$group_id\"});
                    var group_ids = API.groups.getById({\"group_id\": \"$group_id\"});
                    var info = [];
                    info.push(posts);
                    info.push(group);
                    info.push(group_ids);
                    return info ;",
            ]);
            sleep(2);
            //Собираю себе чудесный массив для вставки в бд.
            if (empty($executeData[0])){
                dump("пост удален");
            }else{
                $pars_info = array(
                    'text' => array(),
                    'likes' => array(),
                    'comments' => array(),
                    'reposts' => array(),
                    'views' => array(),
                    'count_members' => array(),
                    'chanel_name' => array(),
                );

                foreach ($executeData[0] as $post){
                    array_push($pars_info['likes'], $post['likes']['count']);
                    array_push($pars_info['reposts'], $post['reposts']['count']);
                    if (isset($post['views'])){
                        array_push($pars_info['views'], $post['views']['count']);
                    }else{
                        dump( "Апи не отдало просмотры поста - " . $link->link);
                        array_push($pars_info['views'], 0);
                    }
                    array_push($pars_info['comments'], $post['comments']['count']);
                    array_push($pars_info['text'], $post['text']);
                }
                if ($executeData[1] != false){
                    array_push($pars_info['count_members'], $executeData[1]["count"]);
                }else{
                    dump("закрытая группа - " . $link->link);
                    array_push($pars_info['count_members'], 0);
                }
                foreach ($executeData[2] as $group_ids){
                    array_push($pars_info['chanel_name'], $group_ids["name"]);
                }
                return $pars_info;
            }


        }

    }

    /**
     * Сохраняю инфу в постах в бд и проверяю какая команда сейчас работает на комм выходы или на смм линки
     * @param $pars_info
     * @param $link
     * @param $job_name
     */

    public function SaveParsInfo($pars_info,$link, $job_name){
        $pars_info['text'][0] = mb_strimwidth($pars_info['text'][0], 0, 50, "...");
        if ($job_name == "update_social_statistic"){
            if(StatisticSocialNetwork::where('post_smm_links_id', $link->id)->count() > 0) {
                $statisticSMMLink = StatisticSocialNetwork::where('post_smm_links_id', $link->id)->first();
                $statisticSMMLink->update([
                    'views' => $pars_info['views'][0],
                    'like' => $pars_info['likes'][0],
                    'reposts' => $pars_info['reposts'][0],
                    'count_comments' => $pars_info['comments'][0],
                    'followers' => $pars_info['count_members'][0],
                ]);
            }else{
                $statisticSMMLink = StatisticSocialNetwork::create([
                    'post_smm_links_id' => $link->id,
                    'post_snippet' => $pars_info['text'][0],
                    'views' => $pars_info['views'][0],
                    'like' => $pars_info['likes'][0],
                    'reposts' => $pars_info['reposts'][0],
                    'count_comments' => $pars_info['comments'][0],
                    'followers' => $pars_info['count_members'][0],
                    'acc_name' => $pars_info['chanel_name'][0]
                ]);
                $statisticSMMLink->save();
            }


        }
        if ($job_name == "update_comercial_seeder_statistic"){

            if(StatisticSocialNetwork::where('post_seeds_links_id', $link->id)->count() > 0) {
                $statisticSMMLink = StatisticSocialNetwork::where('post_seeds_links_id', $link->id)->first();
                $statisticSMMLink->update([
                    'views' => $pars_info['views'][0],
                    'like' => $pars_info['likes'][0],
                    'reposts' => $pars_info['reposts'][0],
                    'count_comments' => $pars_info['comments'][0],
                    'followers' => $pars_info['count_members'][0],
                ]);
            }else{
                $statisticSMMLink = StatisticSocialNetwork::create([
                    'post_seeds_links_id' => $link->id,
                    'post_snippet' => $pars_info['text'][0],
                    'views' => $pars_info['views'][0],
                    'like' => $pars_info['likes'][0],
                    'reposts' => $pars_info['reposts'][0],
                    'count_comments' => $pars_info['comments'][0],
                    'followers' => $pars_info['count_members'][0],
                    'acc_name' => $pars_info['chanel_name'][0]
                ]);
                $statisticSMMLink->save();
            }
        }
    }
}
