# temanquarantine
temanquarantine (Quarantine Friend) apps <br />
this is the temanquarantine chatbot application, a simple education about covid19 in Indonesia. people do not need to install the application. just need to open the chatbot and type the command. the application language is made in Bahasa because the target of this apps is Indonesians. <br />
https://t.me/temanquarantine

## Instalation guide
this application is based on php with laravel framework and requires https domain for telegram webhook <br />

1. Clone this project <br />
2. Run Composer Install <br />
3. Copy .env.example and rename to .env file <br />
4. Set the database user and password according to your application.
5. set telegram webhook url, you can use your https domain or you can use 'ngrok' to host your apps with https domain locally. <br />
6. run https://yourdomain.com/telegram/web/hook/set/ to set the wehbook url <br />
7. Run php artisan migrate to migrate the database <br />
8. Run php artisan DB:seed to seed the database <br />
9. Test whether the application runs by opening the chatbot https://t.me/temanquarantine and then type /start <br />
<br />

## Application Using Guide
this application uses the telegram chat application so you must first install telegram. <br />

1. type / start to start and see other commands. <br />
2. ketik /help untuk melihat perintah lainnya. <br />
3. test ketik perintah A,B, C, D, E, F or G untuk melihat hasilnya.
