<?php
session_start();
header("Content-type:text/html");
if (!isset($_SESSION["namn"]))
{
	header('Location:inlogg.html');
}

//Kolla om sökningen kom med
if(isset($_GET["sok"]))
{
	//Sök kom med
	
	//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
	$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");

	//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	
	//Kolla vilka inlägg som har flest gillningar, lägg dem först i listan
	$sql = "select inlaggsid,count(*) as antalGillningar from gillningar GROUP by inlaggsid order by antalGillningar DESC";
	
	
	//Skicka frågan till databasen
	$stmt = $conn->prepare($sql);
	
	//Kör frågan
	$stmt->execute();
	
	//Hämta resultat från databasen
	$row = $stmt->fetch();
	
	//Avsluta om det inte kom någon rad
	if (!$row)
	{
		exit();
	}

	//Upprepa så länge det finns en rad
	while ($row != null)
	{
		//Hämta det inlägget med det aktuella ID
		$ID = $row['inlaggsID'];
	
		//Räkna antal gilla-markeringar
		$sqlinlagg = "select id, datumskapat, text, bild, anvandarID, namnAnvandare from inlagg where id=:id";
	
		$params2 = array(':id'=>$row['id']);
	
		//Skicka frågan till databasen
		$stmt2 = $conn->prepare($sqlinlagg);
	
		//Kör frågan
		$stmt2->execute($params2);
	
		//Hämta resultat från databasen
		$row = $stmt2->fetch();
	
	}

}
else
{
	//Sök kom inte med
	header('Location:index.php');
}
?>