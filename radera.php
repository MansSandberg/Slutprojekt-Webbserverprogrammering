<?php
session_start();
if(isset($_GET["id"]) && isset($_SESSION["anvandarID"]))
{
	//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
	$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");
	
	//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
	//$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	
	$sql = "select id, anvandarid from inlagg where id = :id";
	
	//Skicka frågan till databasen
	$stmt = $conn->prepare($sql);
			
	$params = array(':id'=>$_GET["id"]);
	
	//Kör frågan
	$stmt->execute($params);
	
	//Hämta resultat från databasen
	$row = $stmt->fetch();
	
	//Avsluta om det inte kom någon rad
	if (!$row)
	{
		exit();
	}
	
		//Upprepa så länge det finns en rad
		if ($row != null)
		{
			//Kontrollera att den aktuella användaren har skapat inlägget
			if($row['anvandarid'] == $_SESSION["anvandarID"])
			{
				$params2 = array('id'=>$_GET["id"]);
				
				//Radera alla kommentarer till inlägget
				$sqlkommentarer="delete from kommentarer where inlaggsid=:id";
				$stmt=$conn->prepare($sqlkommentarer);
				$stmt->execute($params2);
				
				//Radera alla gillningar
				$sqlgillningar="delete from gillningar where inlaggsid=:id";
				$stmt=$conn->prepare($sqlgillningar);
				$stmt->execute($params2);
				
				//radera inlägget
				$sqlradera = "delete from inlagg where id = :id";
				
				//skicka fråga till databasservern
				$stmt=$conn->prepare($sqlradera);
	
				//Kör frågan på databasen
				$stmt->execute($params2);
				header("location:index.php");
			}
		}
}
?>