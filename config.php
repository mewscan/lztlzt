<?php
define('BotFatherToken', '7118226465:AAHpo0QCHTxemMZRYNw6SXwTCJfj_cxvxIA');




$data = file_get_contents('php://input');
$data = json_decode($data, true);
$name = $data["message"]["from"]["username"];
$nameinline = $data["callback_query"]["from"]["username"];
$chat_id = $data["message"]["chat"]["id"];
$text = $data['message']['text'];
$message_id = $data["message"]["message_id"];
function sendTelegram($method, $response)
{
    $ch = curl_init('https://api.telegram.org/bot' . BotFatherToken . '/' . $method);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
$callback_query = $data['callback_query'];
$callbackInline = $callback_query['data'];
$explodeinline = explode("_", $callbackInline);
$chat_id_in = $callback_query['message']['chat']['id'];
$message_id_in = $callback_query['message']['message_id'];
?>
