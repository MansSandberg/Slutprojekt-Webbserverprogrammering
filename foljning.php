<?php
session_start();
header("Content-type:text/html");
if (!isset($_SESSION["namn"]))
{
	header('Location:inlogg.html');
}
header('charset=utf-8');
$anvandarID = $_SESSION["anvandarID"];

if(isset($_GET["id"]) && isset($_GET["funktion"]))
{
	
	//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
	$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");
	
	//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
	//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//Parametrarna har skickats med
	if($_GET["funktion"] == "folj")
	{
		//Användaren börjar följa personen
		$sql = "insert into foljningar(foljarid, foljdID) values(:foljarid, :foljdid)";
	}
	else if($_GET["funktion"] == "slutafolja")
	{
		//Användaren slutar följa personen
		$sql = "delete from foljningar where foljarid=:foljarid and foljdID=:foljdid";
	}
	else
	{
		//Något har blivit fel
		echo "Någonting blev fel. <ahref=\"index.php\">Gå tillbaka till startsidan";
	}
	
	//Utför åtgärd
	$params = array(':foljarid'=>$_SESSION["anvandarID"], ':foljdid'=>$_GET["id"]);
	
	//skicka fråga till databasservern
	$stmt=$conn->prepare($sql);
	
	//Kör frågan på databasen
	$stmt->execute($params);
	header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else
{
	//Det har blivit fel. Vet inte vem som ska hanteras eller hur
	header('Location:index.php');
}
?>