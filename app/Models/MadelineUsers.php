<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MadelineUsers
 *
 * @property int $id
 * @property string $session_name
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MadelineUsers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MadelineUsers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MadelineUsers query()
 * @method static \Illuminate\Database\Eloquent\Builder|MadelineUsers whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MadelineUsers whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MadelineUsers whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MadelineUsers whereSessionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MadelineUsers whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MadelineUsers extends Model
{
    protected $fillable = ['session_name', 'active'];

    /**
     * Обрезаем название сессии и получаем чистый телефон
     */
    public function getPhone() {
        $phone = str_replace('redaktor.', '+', $this->session_name);
        return $phone;
    }
}
