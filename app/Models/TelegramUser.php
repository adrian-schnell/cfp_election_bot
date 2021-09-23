<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @mixin \Eloquent
 * @property string telegramId
 * @property string firstName
 * @property string username
 * @property string result_qty
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class TelegramUser extends Model
{
    protected $fillable = [
        'telegramId',
        'firstName',
        'username',
        'result_qty',
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];
}
