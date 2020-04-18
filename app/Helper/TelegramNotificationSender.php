<?php

namespace App\Helper;
use App\Model\Notifikasi;
use Illuminate\Support\Facades\Log;

class TelegramNotificationSender{
    
    private $id_notifikasi;
    private $receiver;
    private $message;
    private $document = null;
    private $caption = "📎 Attachment";
    public function __construct($id_type_notifikasi=null)
    {
        $this->setNotifikasi($id_type_notifikasi);
    }

    public function setNotifikasi($id_notifikasi){
        $this->id_notifikasi = $id_notifikasi;
        if($this->id_notifikasi != null){
            $this->receiver = Notifikasi::where('id',$id_notifikasi)->first()->telegramChats()->where('chat_id','<>','')->get();
        }
        return $this;
    }

    public function setMessage($message){
        $this->message = is_array($message) ? $message : [$message];
        return $this;
    }

    public function setDocument($path=null,$caption=null){
        $this->document = $path;
        $this->caption = $caption ? $caption : $this->caption;
        return $this;
    }

    private function inlineKeyboard(){
        $this->document ? (
            json_encode(['inline_keyboard'=>[[['text'=>$this->caption,'url'=>$this->document]]]])
        ) : null;
    }

    public function send(){
        $rep = $this->document ? (
            json_encode(['inline_keyboard'=>[[['text'=>$this->caption,'url'=>$this->document]]]])
        ) : null;
        foreach($this->receiver as $receiver){
            foreach($this->message as $message){
                \Telegram::setAsyncRequest(true)->sendMessage([
                    'chat_id'=>$receiver->chat_id,
                    'text'=>$message,
                    'parse_mode'=>'html',
                    'reply_markup'=> $rep
                ]);
            }
            
        }
        return $this;
    }


    
}

?>