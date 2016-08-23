<?php

$botToken="XX3256069:AAFu8GSiSgNFfnW-6mer8ILx-SK6Ga9GlXX";
$website="https://api.telegram.org/bot".$botToken;

$update=file_get_contents('php://input');

$update=json_decode($update,TRUE);
$chatId=$update["message"]["chat"]["id"];
$text=$update["message"]["text"];

$text=strtoupper($text);

include_once "simple_html_dom.php";

switch ($text) {
    case "TOPICS":
		file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=current,warning");
		break;
    case "TELLME CURRENT":
		$html = file_get_html('http://rss.weather.gov.hk/rss/CurrentWeather.xml');
		$e = $html->find('description', 1);
		$fullWeather=$e->plaintext ;
		$pos = strpos($fullWeather, "At");
		$currenttime=substr($fullWeather,$pos+13 ,7);
		file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=HK ".$currenttime);
		$pos = strpos($fullWeather, "temperature :");
		$temp=substr($fullWeather,$pos+13 ,3);
		file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=".$temp." degrees Celsius");
		$pos = strpos($fullWeather, "Relative Humidity :");
		$Humidity=substr($fullWeather,$pos +19,3);
		file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=".$Humidity." RelativeHumidity");
		break;
    case "TELLME WARNING":
		$html = file_get_html('http://rss.weather.gov.hk/rss/WeatherWarningBulletin.xml');
		$e = $html->find('title', 2);
		$warningmsg=$e->plaintext ;
		if(strpos($warningmsg,"no special announcement")==true){
			file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=There is no special announcement");
		}
		else{
			file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=".$warningmsg);

		}
        break;
    default:
		file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=invaild command");
}
?>