<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OKAccounts
 * @package App\Models
 */
class OKAccounts extends Model
{
    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'accounts_ok';

    protected $fillable = [
        'name', 'token', 'status'
    ];
}
