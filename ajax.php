<?php
// Токен телеграм бота
$tg_bot_token = "5360345876:AAEyM_sckiSJyjD3ci4QLtPfstR4Uxn9tDk";
// ID Чата
$chat_id = "-1002134349051";

$text = '';
$text .= 'Заявка со страницы скупки 100nout.by!'. "\n" . "\n";
foreach ($_POST as $key => $val) {
    $text .= $key . ": " . $val . "\n";
}
$text .=  "\n" . 'Фотографии (если есть) ниже ⬇️⬇️⬇️';

$param = [
    "chat_id" => $chat_id,
    "text" => $text
];



$url = "https://api.telegram.org/bot" . $tg_bot_token . "/sendMessage?" . http_build_query($param);


var_dump($text);

file_get_contents($url);

foreach ( $_FILES as $file ) {

    $url = "https://api.telegram.org/bot" . $tg_bot_token . "/sendDocument";

    move_uploaded_file($file['tmp_name'], $file['name']);

    $document = new \CURLFile($file['name']);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ["chat_id" => $chat_id, "document" => $document]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $out = curl_exec($ch);

    curl_close($ch);

    unlink($file['name']);
}

die('1');
