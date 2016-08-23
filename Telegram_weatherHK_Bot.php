<?php

$botToken="XXXX:XXXX";
$website="https://api.telegram.org/bot".$botToken;

$update=file_get_contents('php://input');

$update=json_decode($update,TRUE);
$chatId=$update["message"]["chat"]["id"];
$text=$update["message"]["text"];

$servername = "localhost";
$username = "XXX";
$password = "XXX";
$dbname = "XXX";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$checkSql="select * from userProfile where chat_id='".$chatId."'";
$result = $conn->query($checkSql);
$row = $result->fetch_assoc();
if($row==0){
	$sql = "INSERT INTO userProfile (chat_id) VALUES ('".$chatId."')";
	if ($conn->query($sql) === TRUE) {
		//echo "New record created successfully";
	} else {
		//echo "Error: " . $sql . "<br>" . $conn->error;
	}
}

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
	case  "SUBSCRIBE WARNING":
		$subscribeSql="update userProfile set subscribe='Y' where chat_id='".$chatId."'";
		$conn->query($subscribeSql);
		file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=OK");
		break;
	case  "UNSUBSCRIBE WARNING":
		$unsubscribeSql="update userProfile set subscribe='' where chat_id='".$chatId."'";
		$conn->query($unsubscribeSql);
		file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=OK");
		break;
    default:
		file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=invaild command");
	}
$conn->close();
?>