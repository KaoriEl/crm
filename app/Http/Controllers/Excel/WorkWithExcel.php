<?php


namespace App\Http\Controllers\Excel;

use App\Http\Controllers\Excel\Service\AuthGoogle;
use App\Http\Controllers\Excel\Service\CreateExcel;
use App\Http\Controllers\Excel\Service\CheckSociety;
use App\Http\Controllers\Instagram\Instagram;
use App\Http\Controllers\Instagram\ParsingInstagram;
use App\Models\InstagramAccount;
use App\Models\Post;
use App\Models\Setting;
use Carbon\Carbon;
use danog\MadelineProto\Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Madeline\Services\ParseTelegram;

class WorkWithExcel
{
    private $spreadSheetsId;
    private $authGoogle;
    private $client;
    private $service;
    private $list_name;

    public function __construct()
    {
        // https://docs.google.com/spreadsheets/d/1-KijAV4QZ0xwHo_PyH2h_uWOTGRHXXJ6UIS6EoptihE/edit?usp=sharing

        $this->spreadSheetsId = config('app.GOOGLE_SPREADSHEET_ID');
        $this->authGoogle = new AuthGoogle();
        $this->client = $this->authGoogle->getClient();
        $this->service = new \Google_Service_Sheets($this->client);
        $this->list_name = 'Лист1';

    }

    /**
     * Получение листа
     * @return string
     */

    public function getListName()
    {
        return $this->list_name;
    }

    /**
     * Получение листа
     * @return string
     */

    public function getService()
    {
        return $this->service;
    }

    /**
     * Получение листа
     * @return string
     */

    public function getClient()
    {
        return $this->client;
    }

    /**
     * Получение айдишника excel file
     * @return string
     */

    public function getSpreadSheetsId()
    {
        return $this->spreadSheetsId;
    }


    /**
     * Парсинг на соц-сети
     * @param $links
     * @param $job
     * @param bool $cron
     * @return void
     */
    public function parseLinks($links, $job = null, $cron = false)
    {
        try {
        $obj = null;

        $data = [];
        foreach ($links as $social => $accounts) {

            switch ($social) {
                case 'TG':
                    $obj = new ParseTelegram();
                    break;
                case 'IG':
                    $obj = new ParsingInstagram();
                    break;
            }
            $data = $obj->parse($links[$social]);

        }


        foreach ($data as $row => $value_links) {
            if ($cron) {
                $job->row_count = $row;
                $job->save();
            }


            $range = $this->list_name . '!F' . $row . ':N';

            if ($value_links['social'] == 'IG') {
                $values[0][0] = $value_links['caption'];
                $values[0][1] = $value_links['comment_count'];
                $values[0][2] = $value_links['like_count'];
                $values[0][3] = $value_links['followers'];
                $data_publish = Carbon::createFromTimestamp($value_links['time'])->setTimezone('Europe/Moscow')->format('d.m.Y H:i');
                $values[0][4] = $data_publish . ' (MSK)';
                $values[0][5] = $value_links['reach_count'];
                $values[0][6] = $value_links['impression_count'];
                $values[0][7] = $value_links['save_count'];
            }

            if ($value_links['social'] == 'TG') {
                $values[0][0] = $value_links['caption'];
                $values[0][1] = ' ';
                $values[0][2] = ' ';
                $values[0][3] = ' ';
                $data_publish = Carbon::createFromTimestamp($value_links['time'])->setTimezone('Europe/Moscow')->format('d.m.Y H:i');
                $values[0][4] = $data_publish . ' (MSK)';
                $values[0][5] = $value_links['views'];
                $values[0][6] = ' ';
                $values[0][7] = ' ';
            }
            try {
                $this->addingDataToExcel($values, $range, $cron);
            }catch (Exception $e){
                continue;
            }



            $this->addingDataToExcel($values, $range, $cron);
            sleep(5);
            }
            }catch (Exception $e)
            {

            }

    }

    /**
     * Выгрузка по всем задачам
     * @return void
     */

    public function exportPostsData()
    {
        // очищаем файл
        $this->spreadSheetsId = config('app.GOOGLE_EXPORT_POSTS_SPREEDSHEET_ID');

        $row_count = $this->getRowsCount() + 1;
        // формируем список данных и отправляем его в файл
        $range = $this->list_name . '!A' . $row_count . ':Q';
        $posts = Post::get()->where('id', '>=', $row_count - 1);

        $i = 0;
        $values = [];
        foreach ($posts as $post) {

            $journalist = User::find($post->journalist_id);
            if ($journalist) {
                $journalist = $journalist->name;
            } else {
                $journalist = 'Нет журналиста';
            }

            $editor = User::find($post->editor_id);

            $archived_at_post = '';
            if ($post->archived_at != null) {
                $archived_at_post = $post->archived_at->setTimezone('Europe/Moscow')->format('d.m.Y H:i');
            } else {
                $archived_at_post = 'Пост не в архиве';
            }
            $create_at_post = $post->created_at->setTimezone('Europe/Moscow')->format('d.m.Y H:i');
            $expires_at_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d.m.Y H:i');

            $checker = new CheckSociety();

            if ($editor == null) {
                $editor = '';
            } else {
                $editor = $editor->name;
            }
            $name_project = '';
            if ($post->project != null) {
                $name_project = $post->project->name;
            }

            $smm_society = $checker->checkSMMLinks($post);
            $publication_url = $checker->checkPublicationUrl($post);
            $seed_link = $checker->checkSeederLink($post);
            $target_society = $checker->checkTargetOn($post);
            ($post->commenting_text) ? $post->commenting_text : $post->commenting_text = ' ';

            $values[$i] = array($post->id, $create_at_post, $expires_at_post, $archived_at_post, $post->title, $this->formatPostText($post->text), $name_project, $post->status->title, $editor, $journalist,
                $publication_url, $smm_society, $seed_link, $target_society, $post->commenting_text
            );
            $i++;
        }

        $this->addingDataToExcel($values, $range, true);
        $url = 'https://docs.google.com/spreadsheets/d/' . $this->getSpreadSheetsId() . '/edit?usp=sharing';
        return redirect()->to($url);

    }


    /**
     * Добавляем информацию в Excel
     * @param $values
     * @param $range
     * @param bool $update
     */

    public function addingDataToExcel($values, $range, $update = false)
    {
        $params = [
            'valueInputOption' => "RAW"
        ];

        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);

        if ($update) {
            $this->service->spreadsheets_values->update($this->spreadSheetsId, $range, $body, $params);
        } else {
            $this->service->spreadsheets_values->append($this->spreadSheetsId, $range, $body, $params);
        }

    }

    /**
     * Добавление ссылки с публикации в Excel
     * @param $post
     * @param $link
     */
    public function addingLinkInExcel($post, $link)
    {
        $count_rows = $this->getRowsCount();

        $range = $this->list_name . '!A' . $count_rows . ':E';
        $values[0][0] = $link;
        (strpos($link, 'vk') !== false) ? $values[0][1] = 'ВК' : null;
        (strpos($link, 'instagram') !== false) ? $values[0][1] = 'IG' : null;
        (strpos($link, 't.me') !== false) ? $values[0][1] = 'TG' : null;
        (!isset($values[0][1])) ? $values[0][1] = 'Не удалось распарсить соц-сеть' : null;
        $values[0][2] = $post->project->name;
        $values[0][3] = $post->title;
        $values[0][4] = $this->formatPostText($post->text);


        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => "RAW"
        ];

        $this->service->spreadsheets_values->append($this->spreadSheetsId, $range, $body, $params);

        $count_rows = $this->getRowsCount();

        $data_link = [];

        $api_insta = (new Instagram())->loginAccount(null);
        $instance_instagram = Instagram::getInstance();
        if ($values[0][1] == 'IG') {

            $shortcode = $instance_instagram->getShortCodeInstagram($link);
            $media_info = $api_insta->media->getMediaByGraphQl($shortcode);
            if (!is_bool($media_info)) {
                $author_post = $media_info->owner->username;

                $username = InstagramAccount::get()->where('username', $author_post)->pluck('username')->first();

                if (is_null($username)) {
                    $username = Setting::get()->where('setting_name', 'inst_login')->pluck('setting_value')->first();
                }

                $data_link[$values[0][1]][$username][] = [
                    'media_id' => $media_info->id,
                    'row_link' => $count_rows,
                    'errorException' => false
                ];
            }

        }

        if ($values[0][1] == 'TG') {
            $data_link[$values[0][1]][] = [
                'row_link' => $count_rows,
                'link' => $link
            ];
        }


        $this->parseLinks($data_link, null, false);

    }

    /**
     * @return int
     * получаем кол-во строк в файле
     */

    public function getRowsCount()
    {
        $range = $this->list_name . '!A1:BL';
        $count_rows = count($this->service->spreadsheets_values->get($this->spreadSheetsId, $range));
        return $count_rows;
    }

    /**
     * @param $text
     * Форматируем текст
     * @return string|string[]
     */

    private function formatPostText($text)
    {
        $formattText = strip_tags($text, '<b></b><strong></strong><i></i><em></em><a></a><code></code><pre></pre>');
        $formattText = str_replace('</p>', '\n', $formattText);
        $formattText = str_replace('<p>', '', $formattText);
        return $formattText;
    }

    /**
     * Очистка всего файла
     * @param $spreadsheet_id
     */
    private function clearValuesInFileExcel($spreadsheet_id)
    {
        $requestBody = new \Google_Service_Sheets_ClearValuesRequest();
        $range = $this->list_name . '!A2:P';

        $this->service->spreadsheets_values->clear($spreadsheet_id, $range, $requestBody);
    }


}
