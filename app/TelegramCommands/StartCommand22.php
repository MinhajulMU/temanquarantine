<?php

namespace App\TelegramCommands;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Illuminate\Support\Facades\Log;
use Telegram;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";
    /**
     * @inheritdoc
     */
    public function handle()
    {

        $keyboard = [
            [[
                'text' => 'My Location',
                'request_location' => true
            ]],[
                'Cancel'
            ]
        ];
        $replyMarkup['keyboard'] = $keyboard;
        $replyMarkup['resize_keyboard'] = true;
        $replyMarkup['one_time_markup'] = true;
        $encodedMarkup = json_encode($replyMarkup);

        $tg_username = $this->getUpdate()->getChat()->getUsername();
        $tg_chat_id = $this->getUpdate()->getChat()->getId();
        // $telegramChat = TelegramChat::where('username',$tg_username)->first();
        $name = $this->getUpdate()->getChat()->getFirstName()." ".$this->getUpdate()->getChat()->getSecondName();
        // if($telegramChat){
        $text = 'halo, <b>'.$name."</b>\n";
        $text.= "Kamu berhasil didaftarkan sebagai penerima Notifikasi Web IKITAS,kamu akan menerima notifikasi berikut:\n";
            // foreach ($telegramChat->notifikasi as $key => $value) {
            //    $text.= "-".$value->name."\n";
            // }
        $commands = $this->getTelegram()->getCommands();
        foreach ($commands as $name => $command) {
            $text .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }
        $this->replyWithMessage(['text' => $text,'parse_mode'=>'html','reply_markup' => $encodedMarkup]);
            // $telegramChat->name = $name;
            // $telegramChat->chat_id = $tg_chat_id;
            // $telegramChat->save();
//        }

        Log::info($tg_chat_id);
        
    }
    
}
