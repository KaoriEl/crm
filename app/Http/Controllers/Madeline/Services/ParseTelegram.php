<?php


namespace App\Http\Controllers\Madeline\Services;


use App\Http\Controllers\Excel\Parsing;
use App\Http\Controllers\Madeline\ApiMadeline;

class ParseTelegram implements Parsing
{
    private $client;
    public function __construct()
    {
        $this->client = (new ApiMadeline())->getClient();

    }

    public function parse($links)
    {
        $data = [];

        foreach($links as $link) {
            if($this->client == false) {
                $data[$link['row_link']]['social'] = 'Нет рабочих аккаунтов';
                $data[$link['row_link']]['caption'] = 'Нет рабочих аккаунтов';
                $data[$link['row_link']]['views'] = 'Нет рабочих аккаунтов';
                $data[$link['row_link']]['time'] = 'Нет рабочих аккаунтов';
                continue;
            }



            $linkPost = str_replace('https://t.me/', '', $link['link']);
            $linkArr = explode('/', $linkPost);
            try {
                $postInfo = $this->client->channels->getMessages(['channel' => $linkArr[0], 'id' => [$linkArr[1]]]);
            } catch (\Exception $e) {
                $data[$link['row_link']]['social'] = 'TG';
                $data[$link['row_link']]['caption'] = 'Закрытый канал';
                $data[$link['row_link']]['views'] = 'Закрытый канал';
                $data[$link['row_link']]['time'] = 'Закрытый канал';
                continue;
            }
            $data[$link['row_link']]['social'] = 'TG';
            $data[$link['row_link']]['caption'] = $postInfo['messages'][0]['message'];
            $data[$link['row_link']]['views'] = $postInfo['messages'][0]['views'];
            $data[$link['row_link']]['time'] = $postInfo['messages'][0]['date'];
        }

        return $data;
    }

    public function parseSMMLinks($links, $job_name)
    {
        // TODO: Implement parseSMMLinks() method.
    }
}
