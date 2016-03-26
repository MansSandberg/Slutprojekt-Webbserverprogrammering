<?php
if(isset($_GET["id"]))
{
	//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
	$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");
	
	//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	//SQL-fråga
	$sql = "select id, profilbild, bildtyp from anvandare where id=:id";

	$stmt = $conn->prepare($sql);
	
	//Kolla att ID har skickats med
	if (isset($_GET["id"]))
	{
		//Skicka med värdet var parametern ID
		$params=array(':id'=>$_GET["id"]);
	}
	else
	{
		$params=array();
	}
	
	//Kör SQL-frågan
	$stmt->execute($params);	
	
	//Hämta första resultat-raden från Databasservern
	$row=$stmt->fetch();
	
	//Avsluta om det inte kom någon rad
	if (!$row)
	{
		exit();
	}
	
	//Om raden inte är tom
	if ($row != null)
	{
		//Hämta värden
		$bilden = base64_decode($row['profilbild']);
		$bildtyp = $row['bildtyp'];
		header("Content-type: $bildtyp");
		echo $bilden;
		
	}
}
else
	//Inget id har skickats med
	die();
?>