<?php


namespace App\Http\Controllers\Excel\Service;


use App\Http\Controllers\Excel\WorkWithExcel;
use App\Http\Controllers\Instagram\Instagram;
use App\Models\InstagramAccount;
use App\Models\Post;
use App\Models\Setting;

class CronUpdate
{
    private $excel;

    public function __construct()
    {
        $this->excel = new WorkWithExcel();
    }

    /**
     * Получение сслыки на новую соц-сеть
     *
     * @param $job
     * @return void
     */

    public function getUpdatesForCron($job)
    {
        $count_rows = $this->excel->getRowsCount();
        $range = $this->excel->getListName() . '!A' . $job->row_count . ":N" . $count_rows;
        $response = $this->excel->getService()->spreadsheets_values->get($this->excel->getSpreadSheetsId(), $range);
        $value_links = $response->getValues();
        $cron_rows = $job->row_count;

        $api_insta = (new Instagram())->loginAccount(null);
        $instance_instagram = Instagram::getInstance();

        $links = [];


        foreach ($value_links as $link) {
            if (isset($link[9])) {
                $time = strtotime($link[9]) + (168 * 60 * 60);
            } else {
                $time = strtotime('now');
            }
            $post = Post::where('vk_post_url', $link[0])->orWhere('ig_post_url', $link[0])->orWhere('tg_post_url', $link[0])->get();
            if ($post->count() > 0) {
                if ($time < strtotime('now')) {
                    if ($post[0]->cron_has_update) {
                        $cron_rows++;
                        continue;
                    }
                }
                $post[0]->cron_has_update = 1;
                $post[0]->save();
            }


            $errorException = false;

            /**
             * Инстаграм - формирование массива с ссылками по именам
             */
            if (stripos($link[0], 'instagram.com') !== false) {
                $shortcode = $instance_instagram->getShortCodeInstagram($link[0]);
                $media_info = $api_insta->media->getMediaByGraphQl($shortcode);
                if (!is_bool($media_info)) {
                    $author_post = $media_info->owner->username;

                    $username = InstagramAccount::get()->where('username', $author_post)->pluck('username')->first();

                    if (isset($link[9]) && (stripos($link[5], 'Аккаунт заблокирован!') === false || stripos($link[5], 'Не удалось распарсить пост') === false || !empty($link[5]))) {
                        $errorException = true;
                    }

                    if (is_null($username)) {
                        $username_main = Setting::get()->where('setting_name', 'inst_login')->pluck('setting_value')->first();
                        $links[$link[1]][$username_main][] = [
                            'media_id' => $media_info->id,
                            'row_link' => $cron_rows,
                            'errorException' => $errorException
                        ];
                    } else {
                        $links[$link[1]][$username][] = [
                            'media_id' => $media_info->id,
                            'row_link' => $cron_rows,
                            'errorException' => $errorException
                        ];
                    }
                }
            }




            /**
             * Telegram - формирует данные ссылок, которые нужно будет распарсить
             */
            if (stripos($link[0], 't.me') !== false) {
                $links[$link[1]][] = [
                    'link' => $link[0],
                    'row_link' => $cron_rows
                ];
            }

//            sleep(300);
            $cron_rows++;
            sleep(2);
        }

        $this->excel->parseLinks($links, $job, true);

        $job->delete();

        exit;
    }
}
