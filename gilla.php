<?php
session_start();
if (!isset($_SESSION["namn"]))
{
	header('Location:inlogg.html');
}


if(isset($_GET["inlagg"]))
{
	$anvandarID = $_SESSION["anvandarID"];
	//Inlägg har skickats med
	
	//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
	$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");

	//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
	//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "select anvandarid, inlaggsid from gillningar where anvandarid=:anvandare and inlaggsid=:inlagg";
	
	if($_GET["inlagg"] != "")
	{
		$inlagg = $_GET["inlagg"];
		//Ett inläggsID har skickats med
		$params = array(':anvandare'=>$anvandarID, 'inlagg'=>$_GET["inlagg"]);
		
		//Skicka frågan till databasen
		$stmt = $conn->prepare($sql);
	
		//Kör frågan
		$stmt->execute($params);
	
		//Hämta resultat från databasen
		$row = $stmt->fetch();
	
		//Avsluta om det inte kom någon rad
		if (!$row)
		{
			//Personen har inte gillat inlägget förut
			
			//Skapa gillningen
			
			$sqlgilla="insert into gillningar(anvandarID, inlaggsID) values(:anvandare, :inlagg)";
			
			
			//skicka fråga till databasservern
			$stmt=$conn->prepare($sqlgilla);
	
			//Kör frågan på databasen
			$stmt->execute($params);
			header('Location: ' . $_SERVER['HTTP_REFERER']."#".$inlagg);
		}

		//Upprepa så länge det finns en rad
		if ($row != null)
		{
			//Personen har redan gillat inlägget
			header('Location: ' . $_SERVER['HTTP_REFERER']."#".$inlagg);
		}
	}
	else
	{
		//Inget inläggsID har skickats med
		header('Location: ' . $_SERVER['HTTP_REFERER']."#".$inlagg);
	}

}
else
{
	//Inlägg har inte skickats med
	header('Location: ' . $_SERVER['HTTP_REFERER']);
}
?>