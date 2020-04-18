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

        
        $tg_username = $this->getUpdate()->getChat()->getUsername();
        $tg_chat_id = $this->getUpdate()->getChat()->getId();

        $name = $this->getUpdate()->getChat()->getFirstName()." ".$this->getUpdate()->getChat()->getSecondName();
        $user = \App\Model\TelegramUser::where('chat_id',$tg_chat_id)->get();
        if (count($user) == 0) {
            # code...
        }
        $text  = 'Halo Kakak, <b>'.$name."</b> 👩🏻\n";
        $text .= "Selamat datang di chatbot <i>temanquarantine</i>, Edukasi dan update informasi terkini Covid19 di Indonesia. \n";
        $text .= "Dapatkan Informasi apa saja tentang Covid19 di Indonesia \n";
        $text .= "Silahkan Ketik: \n";
        $text .= "\n";
        $text .= "<b>A</b> Update kasus terbaru Covid19 di Indonesia \n";
        $text .= "<b>B</b> Apa itu Covid19 ? \n";
        $text .= "<b>C</b> Jadi, Gimana caranya Covid19 dapat Menyebar ? \n";
        $text .= "<b>D</b> Apakah saya menderita coronavirus jika saya punya penyakit batuk atau bersin? \n";
        $text .= "<b>E</b> Bagaimana cara melindungi diri dari Covid19 ?";
        // $commands = $this->getTelegram()->getCommands();
        // foreach ($commands as $name => $command) {
        //     $text .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        // }
        $this->replyWithMessage(['text' => $text,'parse_mode'=>'html']);

        Log::info($tg_chat_id);
        
    }
    
}
