<?php
session_start();
$anvandarID = $_SESSION["anvandarID"];
//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");
	
//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
//$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

if(isset($_POST["nypresentation"]))
{
	//Formuläret hängde med
	
	$sql = "update anvandare set presentation = :nypresentation where id = $anvandarID";
	
	if($_POST["nypresentation"] !="")
	{
		$nypress = $_POST["nypresentation"];
		$params = array(':nypresentation'=>$nypress);
	}
	else
	{
		header('Location:anvandare.php');
	}
	
	//skicka fråga till databasservern
	$stmt=$conn->prepare($sql);
	
	//Kör frågan på databasen
	$stmt->execute($params);
	header("location:anvandare.php");
}
else if(isset($_FILES["nyprofilbild"]))
{
	//Byt profilbild
	
	$sql = "update anvandare set profilbild = :bild, bildtyp = :bildtyp, bredd = :bredd, hojd = :hojd where id=:anvandarid";
	
	//Kontrollera bilden
	if($_FILES["nyprofilbild"]["size"]>0)
	{
		$check = getimagesize($_FILES["nyprofilbild"]["tmp_name"]);
    	if($check == true) 
    	{	
			if($_FILES["nyprofilbild"]["size"]>65535)
			{
				echo "Oj, den filen är för stor för att ladda upp. <a href=\"javascript:history.back()\">Gå Tillbaka</a>";
				die();
			}
    		//Det är en bild
			$bildtyp = $_FILES['nyprofilbild']['type'];
        	$bild = addslashes($_FILES['nyprofilbild']['tmp_name']);
        	$bild = file_get_contents($bild);
        	$bild = base64_encode($bild);
			$bredd = $check[0];
			$hojd = $check[1];
        	
    	}
    	else 
    	{
    		//Det är ingen bild
    		echo "Det där är ingen bild";
    		die();
    	}
	}
	else
	{
		//Gå tillbaka till förra sidan
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}
	
	//Ladda upp bilden till databasen
	$params = array(':bild'=>$bild, ':bildtyp'=>$bildtyp, ':anvandarid'=>$anvandarID, ':bredd'=>$bredd, 'hojd'=>$hojd);
	
	//skicka fråga till databasservern
	$stmt=$conn->prepare($sql);
	
	//Kör frågan på databasen
	$stmt->execute($params);
	header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else
{
	header('Location:anvandare.php');
}
?>