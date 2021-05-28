<?php


namespace App\Http\Controllers\Excel;


interface Parsing
{
    public function parse($links);

    public function parseSMMLinks($links, $job_name);

}
