<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Telegram\Bot\Keyboard\Keyboard;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::group(['middleware' => 'auth'], function(){
    Route::get('/home', 'HomeController@index')->name('home');

    Route::group(['prefix' => 'user','as' => 'user.'],function(){
        Route::get('/','userController@index')->name('index');
        Route::get('/create','userController@create')->name('create');
        Route::get('/edit','userController@create')->name('edit');
        Route::post('/delete','userController@create')->name('delete');
    });

});
Route::post('/telegram/web/hook',function(){
    $update = Telegram::commandsHandler(true);
    $updates = Telegram::getWebhookUpdates();

    $message = $updates['message']['text'];
    $chat_id = $updates['message']['chat']['id'];
    $response = Telegram::getLastResponse();

    if ($message == 'A' || $message == 'a') {
        # code...
        $urlUpdate  = "https://covid19.mathdro.id/api/countries/ID";
        $lastUpdate = http_request($urlUpdate);
        $lastUpdate = json_decode($lastUpdate, TRUE);
        $kasus = "https://indonesia-covid-19.mathdro.id/api";
        $kasus = http_request($kasus);
        $kasus = json_decode($kasus, TRUE); 
        $lastUpdate =  $lastUpdate['lastUpdate'];
        $text  = "Halo Kak, Apa Kabar 😊\n";
        $text .= "<b>Informasi Terbaru Kasus Covid19 di Indonesia </b>\n";
        $text .= "Update Terakhir <i>".date("d-M-Y H:i",strtotime($lastUpdate))."</i> \n";
        $text .= "\n";
        $text .= "Total Kasus: <b>".$kasus['jumlahKasus']."</b> \n";
        $text .= "Pasien Sembuh: <b>".$kasus['sembuh']." 💚</b> \n";
        $text .= "Pasien dalam Perawatan <b>".$kasus['perawatan']."</b> \n";
        $text .= "Pasien Meninggal <b>".$kasus['meninggal']."😢</b> \n";
        $text .= "\n";
        $text .= "Tetap Semangat ya kak, kalau nggak ada keperluan keluar kalau bisa jangan keluar rumah dulu 🙅🏻 \n";
        $text .= "<b>#StayAtHome</b> \n";
        $text .= "\n";
        $text .= "Lihat Informasi Selengkapnya Mengenai Covid-19 di Indonesia https://www.covid19.go.id/situasi-virus-corona \n";
        $response = Telegram::sendMessage([
            'chat_id' => $chat_id, 
            'text' => $text,
            'parse_mode' => 'html'
        ]);
          
    }else if ($message == 'B' || $message == 'b') {
        # code...
        $text = [
            "Jadi gini kak 👩🏻‍⚕️, Covid-19 itu penyakit menular, yang artinya penyakit ini bisa disebarkan secara langsung atau tidak langsung, dari satu orang ke orang lain. \n",
            "Terus Karena ini penyakit menular dan penularannya sangat mudah dan cepat, maka sesuai anjuran pemerintah, ayo kita ikut laksanakan <b>physical distancing.</b> \n",
            "Ayo Kak, kita Gotong Royong Bantu Pemerintah dengan cara <b>dirumahajadulu</b>"
        ];
        foreach ($text as $key => $value) {
            # code...
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id, 
                'text' => $value,
                'parse_mode' => 'html'
            ]);
        }

    }elseif ($message == 'C' || $message == 'c') {
        # code...
        $text = [
            "Orang yang terinfeksi dapat menyebarkan infeksi ke orang yang sehat 👩🏻‍⚕️ \n",
            "Terus cara penyebarannya gimana ? yaitu melalui kontak dekat dengan orang yang terinfeksi atau kontak dengan permukaan benda yang terkontaminasi. \n",
            'Jadi, buat meminimalisir kontak dengan sekitar, <b>dirumahajadulu</b> yuk kak 👩🏻',
        ];
        foreach ($text as $key => $value) {
            # code...
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id, 
                'text' => $value,
                'parse_mode' => 'html'
            ]);
        }
    }elseif ($message == 'D' || $message == 'd' ) {
        # code...
        $text = [
            "Anda dapat menjadi suspect coronavirus, hanya jika Anda memiliki gejala (demam, batuk, sesak napas) + salah satu dari ini:👩🏻‍⚕️ \n",
            "<b>1.</b> Riwayat Perjalanan dari daerah yang terkena coronavirus (seperti Cina, Iran, Italia, Republik Korea, dll.). \n",
            '<b>2.</b> Kontak langung dengan pasien Covid19.',
            '<b>3.</b> Mengunjungi fasilitas/lab kesehatan di mana Pasien Coronavirus sedang dirawat.'
        ];
        foreach ($text as $key => $value) {
            # code...
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id, 
                'text' => $value,
                'parse_mode' => 'html'
            ]);
        }
    }else if($message == 'E' || $message == 'e'){
        $text = [
            "Ayo kak jangan kalah sama Covid19, Lakukan tips-tips berikut buat melindungi diri dari Covid19: 👩🏻 \n",
            "<b>1.</b> Cuci tangan Anda secara teratur dengan sabun dan air. \n",
            '<b>2.</b> Siapkan pembersih berbasis alkohol untuk saat-saat ketika sabun dan air tidak tersedia.',
            '<b>3.</b> Jangan menyentuh mata, mulut, atau hidung Anda dengan tangan yang tidak bersih.',
            '<b>4.</b> Jaga jarak Anda setidaknya 1 meter dari siapa pun yang batuk atau bersin.',
            '<b>5.</b> Ikuti anjuran pemerintah buat <b>dirumahajadulu</b> <b>jagajarakdulu</b>'
            
        ];
        foreach ($text as $key => $value) {
            # code...
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id, 
                'text' => $value,
                'parse_mode' => 'html'
            ]);
        }
    }else if($message == 'F' || $message == 'f'){
        
        $keyboard = Keyboard::make()
        ->inline()
        ->row(
            Keyboard::inlineButton(['text' => 'Bantu Sesama Lawan Covid19', 'url' => 'https://kitabisa.com/campaign/bantuekonomiwarga'])
        );
        $text = [
            "Halo Kak 👩🏻, Penyebaran virus corona di Indonesia terus meluas. Dampak virus ini sangat mengkhawatirkan: Ribuan orang positif dan dalam pengawasan, sementara ratusan lainnya meninggal dunia. Ini jelas kabar buruk. Apalagi, angka orang yang positif corona terus meningkat dari hari ke hari.  \n",
            " Melalui galang dana ini, kami mengajak kamu dan kita semua untuk membantu kebutuhan ekonomi masyarakat kecil dan pekerja informal yang rawan terdampak Corona. \n",
            
        ];
        foreach ($text as $key => $value) {
            # code...
            if ($key == count($text)-1) {
                # code...
                $response = Telegram::sendMessage([
                    'chat_id' => $chat_id, 
                    'text' => $value,
                    'parse_mode' => 'html', 
                    'reply_markup' => $keyboard
                ]);
            }else{
                $response = Telegram::sendMessage([
                    'chat_id' => $chat_id, 
                    'text' => $value,
                    'parse_mode' => 'html'
                ]);
            }

        }
    }else if($message == 'G' || $message == 'g'){
        
        $keyboard = Keyboard::make()
        ->inline()
        ->row(
            Keyboard::inlineButton(['text' => 'Official Website Covid19', 'url' => 'https://www.covid19.go.id'])
        );
        $text = [
            "Halo Kak 👧🏻, Sedang ingin Konsultasi atau ingin mendapat bantuan segera karena ada gejala Covid19 ?  \n",
            "Hubungi 0811 333 99 000 atau Hotline <b>119</b> \n",
            "Mari saling melindungi dari virus corona dengan saling membagikan informasi kontak ini \n",
            "Kunjungi Official Website https://www.covid19.go.id \n",
            
        ];
        foreach ($text as $key => $value) {
            # code...
            if ($key == count($text)-1) {
                # code...
                $response = Telegram::sendMessage([
                    'chat_id' => $chat_id, 
                    'text' => $value,
                    'parse_mode' => 'html', 
                    'reply_markup' => $keyboard
                ]);
            }else{
                $response = Telegram::sendMessage([
                    'chat_id' => $chat_id, 
                    'text' => $value,
                    'parse_mode' => 'html'
                ]);
            }

        }
    }
    else{
        if ($message != '/start' && $message != '/stop' && $message != '/help' ) {
            # code...
            $text = "Maaf Kak, adek nggak paham apa yang kakak maksud 👧🏻 \n";
            $text .= "Kakak bisa ketikkan /help buat lihat menu yang lain kak";
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id, 
                'text' => $text,
                'parse_mode' => 'html'
            ]);
        }

    }

    
    return $chat_id;
});

Route::get('/telegram/web/hook',function(){

    return Telegram::getWebhookInfo();
});
Route::get('/telegram/web/hook/set',function(){
    $response = Telegram::setWebhook(['url' => env('TELEGRAM_WEBHOOK_URL')]);
    $updates = Telegram::getWebhookUpdates();
    
    return dd($updates);
});

Route::get('/tes','NotificationController@index');

Route::get('/cek',function(){
    $response = Telegram::getLastResponse();
    return json_encode($response);
});
function http_request($url){
    // persiapkan curl
    $ch = curl_init(); 
    // set url 
    curl_setopt($ch, CURLOPT_URL, $url);
    // set user agent    
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    // return the transfer as a string 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    // $output contains the output string 
    $output = curl_exec($ch); 
    // tutup curl 
    curl_close($ch);      
    // mengembalikan hasil curl
    return $output;
}
Route::get('/cek2',function(){
    $kasus = "https://indonesia-covid-19.mathdro.id/api";
    $kasus = http_request($kasus);
    $kasus = json_decode($kasus, TRUE); 
    dd($kasus['meninggal']);
});
Route::get('/cek3',function(){
    $users = new \App\Model\Common\TelegramUser();
    $users->chat_id = "ded";
    $users->username = "ded";
    $users->firstname = "ede";
    $users->lastname = "sw";
    $users->save();
});