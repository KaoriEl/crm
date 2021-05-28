<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Models\StatisticSocialNetwork;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\If_;

class GetStatisticsSocialNetwortksAjaxController extends Controller
{
    public function getStatThisPost($id)
    {
        $query = Post::skipArchived()->orderBy('expires_at')->where('editor_id', '!=', 7);
        $posts2 = $query->get();

        $posts =Post::find($id)->getSMMLinks();
        $postsCommercial = Post::find($id)->getSeedLinks();
        $arrSMMLinksStatistic = [];

       foreach ($posts as $post){
           foreach (StatisticSocialNetwork::where('post_smm_links_id', $post->id)->get() as $statistic) {
            if ($post->social_network_id == 1){
                $statistic['commercial'] = "N";
                $statistic['linkstat'] = $post->link;
                $arrSMMLinksStatistic["vk"][] = $statistic;
            }elseif ($post->social_network_id == 2){
                $statistic['commercial'] = "N";
                $statistic['linkstat'] = $post->link;
                $arrSMMLinksStatistic["ok"][] = $statistic;
            }elseif ($post->social_network_id == 3){
                $arrSMMLinksStatistic["fb"][] = $statistic;
            }elseif ($post->social_network_id == 4){
                $statistic['commercial'] = "N";
                $statistic['linkstat'] = $post->link;
                $arrSMMLinksStatistic["ig"][] = $statistic;
            }elseif ($post->social_network_id == 5){
                $arrSMMLinksStatistic["y_dzen"][] = $statistic;
            }elseif ($post->social_network_id == 6){
                $arrSMMLinksStatistic["y_street"][] = $statistic;
            }elseif ($post->social_network_id == 7){
                $arrSMMLinksStatistic["yt"][] = $statistic;
            }elseif ($post->social_network_id == 8){
                $arrSMMLinksStatistic["tg"][] = $statistic;
            }elseif ($post->social_network_id == 17){
                $arrSMMLinksStatistic["tt"][] = $statistic;
            }
           }
       }

        foreach ($postsCommercial as $post){
            foreach (StatisticSocialNetwork::where('post_seeds_links_id', $post->id)->get() as $statistic) {
                if ($post->social_network_id == 1){
                    $statistic['commercial'] = "Y";
                    $statistic['linkstat'] = $post->link;
                    $arrSMMLinksStatistic["vk"][] = $statistic;

                }elseif ($post->social_network_id == 2){
                    $statistic['commercial'] = "Y";
                    $statistic['linkstat'] = $post->link;
                    $arrSMMLinksStatistic["ok"][] = $statistic;
                }elseif ($post->social_network_id == 3){
                    $arrSMMLinksStatistic["fb"][] = $statistic;
                }elseif ($post->social_network_id == 4){
                    $statistic['commercial'] = "Y";
                    $statistic['linkstat'] = $post->link;
                    $arrSMMLinksStatistic["ig"][] = $statistic;
                }elseif ($post->social_network_id == 5){
                    $arrSMMLinksStatistic["y_dzen"][] = $statistic;
                }elseif ($post->social_network_id == 6){
                    $arrSMMLinksStatistic["y_street"][] = $statistic;
                }elseif ($post->social_network_id == 7){
                    $arrSMMLinksStatistic["yt"][] = $statistic;
                }elseif ($post->social_network_id == 8){
                    $arrSMMLinksStatistic["tg"][] = $statistic;
                }elseif ($post->social_network_id == 17){
                    $arrSMMLinksStatistic["tt"][] = $statistic;
                }
            }
        }
        return $arrSMMLinksStatistic;




    }
}
