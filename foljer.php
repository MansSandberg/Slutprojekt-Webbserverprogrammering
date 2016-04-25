<?php
session_start();
header("Content-type:text/html");
if (!isset($_SESSION["namn"]))
{
	header('Location:inlogg.html');
}

if(isset($_SESSION["anvandarID"]))
{
	$anvandarID = $_SESSION["anvandarID"];
}
else
{
	header('Location:index.php');
}

//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");

//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
//Kolla vilka jag följer
$sqlfoljer="select foljarid, foljdid from foljningar where foljarid =$anvandarID";

//Skicka frågan till databasen
$stmt = $conn->prepare($sqlfoljer);
	
//Kör frågan
$stmt->execute();
	
//Hämta resultat från databasen
$row = $stmt->fetch();
	
//Avsluta om det inte kom någon rad
if (!$row)
{
	echo "Du följer inte någon än.";
	die();
}
else
{
	echo"Du följer:<br />";
}
	
//array som innehåller IDn på alla som jag följer
$foljer = array();

//Upprepa så länge det finns en rad
while ($row != null)
{
	//Lägg till användaren i arrayen
	array_push($foljer, $row['foljdid']);
	
	//Hämta nästa rad
	$row = $stmt->fetch();
}
	
//Skriv ut alla användare i arrayen
$in  = str_repeat('?,', count($foljer) - 1) . '?';
$sqlanvandare = "select fornamn, efternamn, id from anvandare where id IN ($in)";	

//Skicka frågan till databasen
$stmt = $conn->prepare($sqlanvandare);
	
//Kör frågan
$stmt->execute($foljer);
	
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
	$fornamn = $row['fornamn'];
	$efternamn = $row['efternamn'];
	$id = $row['id'];
		
	//Skriv ut användaren
	echo"<a href=\"anvandare.php?id=$id\" target=\"blank\">$fornamn $efternamn</a><br />";
	
	//Hämta nästa rad
	$row = $stmt->fetch();
}

?>