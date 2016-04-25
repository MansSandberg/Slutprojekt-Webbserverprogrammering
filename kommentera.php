<?php
session_start();
if (!isset($_SESSION["namn"]))
{
	header('Location:inlogg.html');
}

if(isset($_POST["kommentar"]) && isset($_POST["id"]))
{
	//Kommentar och ID har skickats med
		
	//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
	$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");

	//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
	//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "insert into kommentarer(text, inlaggsid, anvandarid) values(:text, :inlaggsid, :anvandarid)";
	
	$params = array(':text'=>$_POST["kommentar"], ':inlaggsid'=>$_POST["id"], ':anvandarid'=>$_SESSION["anvandarID"]);
	
	//skicka fråga till databasservern
	$stmt=$conn->prepare($sql);

	//Kör frågan på databasen
	$stmt->execute($params);
	header('Location: ' . $_SERVER['HTTP_REFERER']."#".$inlagg);
	
}
else
{
	//Kommentar eller ID kom inte med
	header('Location: ' . $_SERVER['HTTP_REFERER']."#".$inlagg);
}
?>