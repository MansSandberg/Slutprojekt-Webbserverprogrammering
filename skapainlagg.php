<?php
session_start();
if(isset($_POST["statustext"]))
{
	$status = $_POST["statustext"];
	//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
	$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");
	
	//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
	//$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	
	$sql = "insert into inlagg(anvandarID, namnAnvandare, text, bild, bildtyp) values(:anvandarid, :namnanvandare, :text, :bild, :bildtyp)";
	
	if(isset($_FILES["bild"]) && $_FILES["bild"]["size"]>0)
	{
		$check = getimagesize($_FILES["bild"]["tmp_name"]);
    	if($check == true) 
    	{
			if($check[0]>980)
			{
				echo "Du får inte ladda upp bilder som är mer än 980 pixlar bred.  <a href=\"javascript:history.back()\">Gå Tillbaka</a>";
				die();
			}
			
			if($_FILES["bild"]["size"]>65535)
			{
				echo "Oj, den filen är för stor för att ladda upp. <a href=\"javascript:history.back()\">Gå Tillbaka</a>";
				die();
			}
    		//Det är en bild
			$bildtyp = $_FILES['bild']['type'];
        	$bild = addslashes($_FILES['bild']['tmp_name']);
        	$bild = file_get_contents($bild);
        	$bild = base64_encode($bild);
        	
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
		$bild = null;
		$bildtyp = null;
	}
		
	//echo "Du laddade upp" .$_FILES["bild"]["name"];
	
	$params = array(':anvandarid'=>$_SESSION["anvandarID"], ':namnanvandare'=>$_SESSION["namn"], ':text'=>$status, ':bild'=>$bild, ':bildtyp'=>$bildtyp);
	
	//skicka fråga till databasservern
	$stmt=$conn->prepare($sql);
	
	//Kör frågan på databasen
	$stmt->execute($params);
	header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else
	header('Location: ' . $_SERVER['HTTP_REFERER']);
?>