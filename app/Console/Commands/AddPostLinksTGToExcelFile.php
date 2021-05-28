<?php

namespace App\Console\Commands;

use App\Http\Controllers\Excel\WorkWithExcel;
use App\Http\Controllers\Madeline\ApiMadeline;
use App\Http\Controllers\Madeline\Services\ParseTelegram;
use Illuminate\Console\Command;
use App\Models\Post;
use Carbon\Carbon;
class AddPostLinksTGToExcelFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:links:tg';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One time task for adding t.me links to excel file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $excel = new WorkWithExcel();

        $posts = Post::all();
        $count_row = $excel->getRowsCount();

        $data = [];
        $data_link = [];
        $values = [];

        foreach ($posts as $post) {

            $range = $excel->getListName().'!A'.$count_row.':E';

            if(!is_null($post->publication_url) && stripos($post->publication_url, 'https://t.me') !== false) {

                $link = $post->publication_url;

                $this->addingDataExcel($post, $link, $excel, $range);
                $count_row++;

                $data_link['TG'][] = [
                    'row_link' => $count_row,
                    'link' => $post->publication_url
                ];
            }

            if(!is_null($post->tg_post_url) && stripos($post->tg_post_url, 'https://t.me') !== false && $post->publication_url != $post->tg_post_url) {

                $link = $post->tg_post_url;

                $this->addingDataExcel($post, $link, $excel, $range);
                $count_row++;
                $data_link['TG'][] = [
                    'row_link' => $count_row,
                    'link' => $post->tg_post_url
                ];
            }
            sleep(10);

        }

        $telegram = new ParseTelegram();
        $data = $telegram->parse($data_link['TG']);


        foreach ($data as $row => $value_links) {
            $range = $excel->getListName().'!F'.$row.':N';

            $values[0][0] = $value_links['caption'];
            $values[0][1] = ' ';
            $values[0][2] = ' ';
            $values[0][3] = ' ';
            $data_publish = Carbon::createFromTimestamp($value_links['time'])->setTimezone('Europe/Moscow')->format('d.m.Y H:i');
            $values[0][4] = $data_publish . ' (MSK)';
            $values[0][5] = $value_links['views'];
            $values[0][6] = ' ';
            $values[0][7] = ' ';

            $excel->addingDataToExcel($values, $range, true);
            sleep(5);
        }

    }

    private function addingDataExcel($post, $link, $excel, $range) {

        $values[0][0] = $link;
        $values[0][1] = 'TG';
        $values[0][2] = $post->project->name;
        $values[0][3] = $post->title;
        $values[0][4] = $this->formatPostText($post->text);


        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => "RAW"
        ];

        $excel->getService()->spreadsheets_values->append($excel->getSpreadSheetsId(), $range, $body, $params);
    }

    private function formatPostText($text)
    {
        $formattText = strip_tags($text, '<b></b><strong></strong><i></i><em></em><a></a><code></code><pre></pre>');
        $formattText = str_replace('</p>', '\n', $formattText);
        $formattText = str_replace('<p>', '', $formattText);
        return $formattText;
    }
}
