<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Temp_idea
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $text
 * @property int|null $user_id
 * @property int|null $read_now
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_idea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_idea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_idea query()
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_idea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_idea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_idea whereReadNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_idea whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_idea whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_idea whereUserId($value)
 * @mixin \Eloquent
 */
class Temp_idea extends Model
{
    protected $fillable = ['text', 'user_id', 'read_now'];
}
