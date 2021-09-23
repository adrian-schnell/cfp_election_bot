<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @mixin \Eloquent
 * @property string telegramId
 * @property string firstName
 * @property string lastName
 * @property string username
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class TelegramUser extends Model
{
    protected $fillable = [
        'telegramId',
        'firstName',
        'lastName',
        'username',
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];
}
