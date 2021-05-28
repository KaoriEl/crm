<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Telegram_user
 *
 * @property int $id
 * @property string|null $telegram_id
 * @property string $username
 * @property string|null $next_step
 * @property string|null $forward_step
 * @property string|null $current_step
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $post_id
 * @property int|null $role_id
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user query()
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user whereCurrentStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user whereForwardStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user whereNextStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Telegram_user whereUsername($value)
 * @mixin \Eloquent
 */
class Telegram_user extends Model
{
    protected $fillable = ['username','telegram_id','next_step','forward_step','current_step', 'post_id'];


    /**
     * Смена ролей пользователя
     * @param string $current_step
     * @param string $forward_step
     */
    public function changeStepsUser($current_step, $forward_step) {
        $this->current_step = $current_step;
        $this->forward_step = $forward_step;
        $this->save();
    }
}
