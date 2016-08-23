<?php

$botToken="XXXX:XXXXX";
$website="https://api.telegram.org/bot".$botToken;

$servername = "localhost";
$username = "XXX";
$password = "XXX";
$dbname = "XXX";

// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM userProfile where subscribe='Y'";

echo "Warning:".$_GET['msg'];
echo "<br>";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
	$chat_id=$row["chat_id"];
	echo "Send to chat id:".$chat_id;
	echo "<br>";
	file_get_contents($website."/sendmessage?chat_id=".$chat_id."&text=".urlencode($_GET['msg']));
}    
$conn->close();
?>