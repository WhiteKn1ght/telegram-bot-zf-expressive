<?php
declare(strict_types = 1);
namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Bot\Telegram\Api;

class BotHandler implements RequestHandlerInterface
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $telegram = new Api('YOUR KEY');

        $result = $telegram->getWebhookUpdate();
        # file_put_contents( __DIR__ .'/../../../../logs/bot.log', var_export( [$text, $chat_id, $keyboard], true ) );
        $text = $result["message"]["text"]; //Текст сообщения
        $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
        $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
        $keyboard = [["Последние статьи"]]; //Клавиатура

        if($text){
            if ($text == "/start") {
                $reply = "Не всегда всё зависит от нашего мастерства и умений, порой чистое везение спасает нам жизнь!";
                $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
            }elseif ($text == "/help") {
                $reply = "Информация с помощью.";
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            }elseif ($text == "Последние статьи") {
                $html = simplexml_load_file('https://shiyanbin.ru/feed/');
                foreach ($html->channel->item as $item) {
                    $reply .= "\xE2\x9E\xA1 ".$item->title." (<a href='".$item->link."'>читать</a>)\n";
                }
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply ]);
            } else {
                $reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
            }
        } else {
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
        }

        return new JsonResponse([
            'ack' => time(),
            'result' => 'true'
        ]);
    }
}
