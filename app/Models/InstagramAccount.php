<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InstagramAccount
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $password
 * @property string|null $name_account
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|InstagramAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstagramAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstagramAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|InstagramAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstagramAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstagramAccount whereNameAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstagramAccount wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstagramAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstagramAccount whereUsername($value)
 * @mixin \Eloquent
 */
class InstagramAccount extends Model
{
    protected $fillable = [
        'username', 'password', 'name_account'
    ];
}
