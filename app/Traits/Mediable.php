<?php

namespace App\Traits;

use Plank\Mediable\Mediable as BaseMediable;

trait Mediable
{
    use BaseMediable;

    public function getAttachmentsAttribute()
    {
        $files = [];

        foreach ($this->getMedia('attachment') as $media) {
            $item = collect($media->toArray())->only(['id', 'size', 'filename']);
            $item['full_url'] = $media->getUrl();

            $files[] = $item;
        }

        return $files;
    }
}
