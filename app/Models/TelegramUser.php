<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @mixin \Eloquent
 * @property string telegramId
 * @property string firstName
 * @property string username
 * @property string result_qty
 * @property array  cfp_selection
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class TelegramUser extends Model
{
    use HasFactory;

    public $casts = [
        'cfp_selection' => 'array',
    ];
    protected $fillable = [
        'telegramId',
        'firstName',
        'username',
        'result_qty',
        'cfp_selection',
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];
}
