<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class NotificationController extends Controller
{
    //
    public function index()
    {
        # code...
        $updates = Telegram::getUpdates();
        dd($updates);
    }
}
