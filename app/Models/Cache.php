<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Cache
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
 * @method static \Illuminate\Database\Eloquent\Builder|Cache newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cache newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cache query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereCurrentStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereForwardStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereNextStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cache whereUsername($value)
 * @mixin \Eloquent
 */
class Cache extends Model
{
    protected $table = 'telegram_users';
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
