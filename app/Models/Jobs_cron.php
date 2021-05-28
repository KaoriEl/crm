<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Jobs_cron
 *
 * @property int $id
 * @property int|null $row_count
 * @property string|null $job_name
 * @property string|null $list_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Jobs_cron newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Jobs_cron newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Jobs_cron query()
 * @method static \Illuminate\Database\Eloquent\Builder|Jobs_cron whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Jobs_cron whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Jobs_cron whereJobName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Jobs_cron whereListName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Jobs_cron whereRowCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Jobs_cron whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Jobs_cron extends Model
{
    protected $fillable = ['row_count', 'job_name', 'list_name'];
}
