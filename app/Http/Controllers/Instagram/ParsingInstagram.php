<?php


namespace App\Http\Controllers\Instagram;


use App\Http\Controllers\Excel\Parsing;
use App\Models\InstagramAccount;
use App\Models\Jobs_cron;
use App\Models\Setting;
use App\Models\SmmLink;
use App\Models\StatisticSocialNetwork;

class ParsingInstagram implements Parsing
{

    /**
     * @param $links
     * @return array
     */
    public function parse($links) {
        $instagram = (Instagram::getInstance());

        $data = [];

        foreach($links as $login => $links_login) {

            $instaClient = $instagram->loginAccount($login);

            foreach($links_login as $link) {
                $data[$link['row_link']]['social'] = 'IG';
                if(!$instaClient) {

                    if($link['errorException']) {
                        continue;
                    }

                    $data[$link['row_link']]['comment_count']
                        = $data[$link['row_link']]['like_count']
                        = $data[$link['row_link']]['caption']
                        = $data[$link['row_link']]['time']
                        = $data[$link['row_link']]['followers']
                        = $data[$link['row_link']]['save_count']
                        = $data[$link['row_link']]['impression_count']
                        = $data[$link['row_link']]['reach_count']
                        = $data[$link['row_link']]['engagement_count']
                        = $data[$link['row_link']]['avg_engagement_count']
                        = 'Аккаунт заблокирован или находится в теневом бане!';
                }

                try {
                    $info_by_post = $instaClient->media->getInfo($link['media_id']);
                    $username = $info_by_post->getItems()[0]->getUser()->getUsername();

                    if(count($links_login) > 1) {
                        sleep(120);
                    }

                    $user = $instaClient->people->getInfoByName($username);

                }catch (\InstagramAPI\Exception\InstagramException $e) {
                    if($link['errorException']) {
                        unset($data[$link['row_link']]);
                        continue;
                    }

                    $data[$link['row_link']]['comment_count']
                        = $data[$link['row_link']]['like_count']
                        = $data[$link['row_link']]['caption']
                        = $data[$link['row_link']]['time']
                        = $data[$link['row_link']]['followers']
                        = $data[$link['row_link']]['save_count']
                        = $data[$link['row_link']]['impression_count']
                        = $data[$link['row_link']]['reach_count']
                        = $data[$link['row_link']]['engagement_count']
                        = $data[$link['row_link']]['avg_engagement_count']
                        = 'Не удалось распарсить пост';
                }

                $data[$link['row_link']]['comment_count'] = $info_by_post->getItems()[0]->getCommentCount();
                $data[$link['row_link']]['like_count'] = $info_by_post->getItems()[0]->getLikeCount();
                ($info_by_post->getItems()[0]->getCaption() != null) ? $data[$link['row_link']]['caption'] = $info_by_post->getItems()[0]->getCaption()->getText() : $data[$link['row_link']]['caption'] = ' ';
                $data[$link['row_link']]['time'] = $info_by_post->getItems()[0]->getTakenAt();
                $data[$link['row_link']]['followers'] = $user->getUser()->getFollowerCount();

                if(count($links_login) > 1) {
                    sleep(120);
                }


                    $id_post = $instaClient->media->getInfo($link['media_id'])->getItems()[0]->getPk();

                    try {
                        $info_by_post = $instaClient->business->getMediaInsights($id_post);
                } catch(\Exception $e) {
                    if(stripos($e->getMessage(), 'Insights are not avaialble') !== false) {
                        if($link['errorException']) {
                            unset($data[$link['row_link']]);
                            continue;
                        }
                            $data[$link['row_link']]['error'] = true;
                    }

                    if(stripos($e->getMessage(), 'You can only view insights') !== false) {
                        if($link['errorException']) {
                            unset($data[$link['row_link']]);
                            continue;
                        }
                        $data[$link['row_link']]['error'] = true;
                    }


                }


                if(isset($data[$link['row_link']]['error']) &&  $data[$link['row_link']]['error'] == true) {
                    if($link['errorException']) {
                        unset($data[$link['row_link']]);
                        continue;
                    }

                    $data[$link['row_link']]['save_count'] = 'Не могу получить дополнительные данные';
                    $data[$link['row_link']]['impression_count'] = 'Не могу получить дополнительные данные';
                    $data[$link['row_link']]['engagement_count'] = 'Не могу получить дополнительные данные';
                    $data[$link['row_link']]['reach_count'] = 'Не могу получить дополнительные данные';
                } else {
                    $data[$link['row_link']]['save_count'] = $info_by_post->getMediaOrganicInsights()->getSaveCount();
                    $data[$link['row_link']]['impression_count'] = $info_by_post->getMediaOrganicInsights()->getImpressionCount();
                    $data[$link['row_link']]['engagement_count'] = $info_by_post->getMediaOrganicInsights()->getEngagementCount();
                    $data[$link['row_link']]['reach_count'] = $info_by_post->getMediaOrganicInsights()->getReachCount();
                }




            }

            if(count($links_login) > 1) {
                sleep(60);
            }


        }

//        sleep(300);
        return $data;
    }


    /**
     * Получаем инстаграм ссылки и парсим в зависимости от логина (статистика для дашборда)
     * @param $links
     * @param $job_name
     * @return int
     */
    public function parseSMMLinks($links, $job_name) {
        $job = Jobs_cron::get();
        $instagram = (Instagram::getInstance());
        $instaClient = $instagram->loginAccount(null);

        if(is_int($instaClient)) {
            return 0;
        }

        $arrLinksToUserName = [];
        foreach ($links as $link) {
                $shortcode = $instagram->getShortCodeInstagram($link->link);
                $media_info = $instaClient->media->getMediaByGraphQl($shortcode);
                if (!is_bool($media_info)) {
                    $author_post = $media_info->owner->username;
                    $username = InstagramAccount::where('username', $author_post)->pluck('username')->first();
                    if (is_null($username)) {
                        $username_main = Setting::where('setting_name', 'inst_login')->pluck('setting_value')->first();
                       $arrLinksToUserName[$username_main][] = [
                           'link' => $link,
                           'media_id' => $media_info->id,
                       ];
                    } else {
                        $arrLinksToUserName[$username][] = [
                            'link' => $link,
                            'media_id' => $media_info->id,
                        ];
                    }
                }
                sleep(60);
        }

        foreach($arrLinksToUserName as $username => $links) {
            $instagram = Instagram::getInstance();
            $usernameInstaClient = $instagram->loginAccount($username);

            $accountGlobal = Setting::where('setting_name', 'inst_login')->first();
            $isGlobalAccount = false;
            if($accountGlobal->setting_value == $username) {
                $isGlobalAccount = true;
            }
            if(!is_int($usernameInstaClient)) {
                foreach($links as $link) {
                    $returnData = $this->getInfoByLink($usernameInstaClient, $link['media_id'], $isGlobalAccount);

                    if(is_int($returnData)) {
                        continue;
                    }
                    $returnData['post_snippet'] = mb_strimwidth($returnData['post_snippet'], 0, 50, "...");
                    if ($job_name == "update_social_statistic"){

                        if(StatisticSocialNetwork::where('post_smm_links_id', $link['link']->id)->count() > 0) {

                            $statisticSMMLink = StatisticSocialNetwork::where('post_smm_links_id', $link['link']->id)->first();
                            if(isset($returnData['reach_count'])) {
                                $statisticSMMLink->update([
                                    'views' => (isset($returnData['reach_count'])) ? $returnData['reach_count'] : 0,
                                    'like' => $returnData['like_count'],
                                    'count_comments' => $returnData['comment_count'],
                                    'followers' => $returnData['followers'],
                                    'acc_name' => $returnData['full_name']
                                ]);


                            } else {
                                $statisticSMMLink->update([
                                    'like' => $returnData['like_count'],
                                    'count_comments' => $returnData['comment_count'],
                                    'followers' => $returnData['followers'],
                                    'acc_name' => $returnData['full_name']

                                ]);
                            }
                        } else {
                            $statisticSMMLink = StatisticSocialNetwork::create([
                                'post_smm_links_id' => $link['link']->id,
                                'post_snippet' => $returnData['post_snippet'],
                                'views' => (isset($returnData['reach_count'])) ? $returnData['reach_count'] : 0,
                                'like' => $returnData['like_count'],
                                'count_comments' => $returnData['comment_count'],
                                'followers' => $returnData['followers'],
                                'acc_name' => $returnData['full_name']
                            ]);
                        }
                        $statisticSMMLink->save();

                    }

                    if ($job_name == "update_comercial_seeder_statistic"){
                        $returnData['post_snippet'] = mb_strimwidth($returnData['post_snippet'], 0, 50, "...");
                        if(StatisticSocialNetwork::where('post_seeds_links_id', $link['link']->id)->count() > 0) {

                            $statisticSMMLink = StatisticSocialNetwork::where('post_seeds_links_id', $link['link']->id)->first();
                            if(isset($returnData['reach_count'])) {
                                $statisticSMMLink->update([
                                    'views' => (isset($returnData['reach_count'])) ? $returnData['reach_count'] : 0,
                                    'like' => $returnData['like_count'],
                                    'count_comments' => $returnData['comment_count'],
                                    'followers' => $returnData['followers'],
                                    'acc_name' => $returnData['full_name']
                                ]);


                            } else {
                                $statisticSMMLink->update([
                                    'like' => $returnData['like_count'],
                                    'count_comments' => $returnData['comment_count'],
                                    'followers' => $returnData['followers'],
                                    'acc_name' => $returnData['full_name']

                                ]);
                            }
                        } else {
                            $statisticSMMLink = StatisticSocialNetwork::create([
                                'post_seeds_links_id' => $link['link']->id,
                                'post_snippet' => $returnData['post_snippet'],
                                'views' => (isset($returnData['reach_count'])) ? $returnData['reach_count'] : 0,
                                'like' => $returnData['like_count'],
                                'count_comments' => $returnData['comment_count'],
                                'followers' => $returnData['followers'],
                                'acc_name' => $returnData['full_name']
                            ]);
                        }
                        $statisticSMMLink->save();

                    }
                    }
                    sleep(60);
                }
            }




    }


    /**
     * Парсим ссылку и вытягиваем о ней данные
     * @param $usernameInstaClient
     * @param $media_id
     * @param $isGlobalAccount
     * @return array
     */
    private function getInfoByLink($usernameInstaClient, $media_id, $isGlobalAccount) : array {
        $returnData = [];

        try {
            $info_by_post = $usernameInstaClient->media->getInfo($media_id);

            $username = $info_by_post->getItems()[0]->getUser()->getUsername();
            $user = $usernameInstaClient->people->getInfoByName($username);

        }catch (\InstagramAPI\Exception\InstagramException $e) {
            $job = Jobs_cron::where('job_name', 'update_social_statistic')->first();
            $job->delete();
            return 0;
        }


        if(is_object($info_by_post)) {
            ($info_by_post->getItems()[0]->getCaption() != null) ? $returnData['post_snippet'] = $info_by_post->getItems()[0]->getCaption()->getText() : $returnData['post_snippet'] = ' ';
            $returnData['like_count'] = $info_by_post->getItems()[0]->getLikeCount();
            $returnData['comment_count'] = $info_by_post->getItems()[0]->getCommentCount();
            $returnData['followers'] = $user->getUser()->getFollowerCount();
            $returnData['full_name'] = $info_by_post->getItems()[0]->getUser()->getUsername();


        }
        sleep(60);
        if(!$isGlobalAccount) {
            $id_post = $info_by_post->getItems()[0]->getPk();
            try {
                $info_by_post = $usernameInstaClient->business->getMediaInsights($id_post);
                $returnData['reach_count'] = $info_by_post->getMediaOrganicInsights()->getImpressionCount();

            } catch (\Exception $e) {
                $job = Jobs_cron::where('job_name', 'update_social_statistic')->first();
                $job->delete();
                return 0;
            }
        }

        return $returnData;

    }


}
