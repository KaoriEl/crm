<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VKAccounts
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $token
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|VKAccounts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VKAccounts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VKAccounts query()
 * @method static \Illuminate\Database\Eloquent\Builder|VKAccounts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VKAccounts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VKAccounts whereNameAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VKAccounts wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VKAccounts whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VKAccounts whereUsername($value)
 * @mixin \Eloquent
 */

class VKAccounts extends Model
{
    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'accounts_vk';

    protected $fillable = [
        'name', 'token', 'status'
    ];
}
