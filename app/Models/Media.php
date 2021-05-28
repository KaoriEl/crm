<?php

namespace App\Models;

use Plank\Mediable\Media as BaseMedia;

/**
 * App\Models\Media
 *
 * @property int $id
 * @property string $disk
 * @property string $directory
 * @property string $filename
 * @property string $extension
 * @property string $mime_type
 * @property string $aggregate_type
 * @property int $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $basename
 * @method static Builder|Media forPathOnDisk($disk, $path)
 * @method static Builder|Media inDirectory($disk, $directory, $recursive = false)
 * @method static Builder|Media inOrUnderDirectory($disk, $directory)
 * @method static \Illuminate\Database\Eloquent\Builder|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media query()
 * @method static Builder|Media unordered()
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereAggregateType($value)
 * @method static Builder|Media whereBasename($basename)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereDirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Media extends BaseMedia
{
    /**
     * Свойства файла, которые будут возвращены пользователю.
     *
     * @var array
     */
    private $display = ['id', 'filename', 'size'];

    public function prepareForResponse()
    {
        $preparedArray = collect($this->toArray())->only($this->display);
        $preparedArray['full_url'] = $this->getUrl();

        return $preparedArray;
    }
}
