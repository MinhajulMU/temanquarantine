<?php

namespace App\Model\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Uuids;

class TelegramUser extends BaseModel
{
    //
    use SoftDeletes,Uuids;
    protected $table = 'telegram_user';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'chat_id','first_name','username'
    ];
    public $incrementing = false;
}
