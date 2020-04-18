<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    //
    protected $table = 'telegram_user';
    protected $fillable = [
        'chat_id','first_name','username'
    ];
}
