<?php
session_start();
header('charset=utf-8');
if(isset($_POST["formular"]))
{
	//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
	$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");
	
	//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if($_POST["formular"] == "login")
	{
		//Logga in användare
		if(isset($_POST["mail"]) && isset($_POST["losenord"]))
		{
	
			//SQL-frågan
			$sql = "select epost, losenord, fornamn, efternamn, id from anvandare";
		
			$hashlosen = MD5($_POST["losenord"]);
			$mail = $_POST["mail"];
			
			//Skicka frågan till databasen
			$stmt = $conn->prepare($sql);
			
			$params = array();
	
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
			while ($row != null)
			{
				if($row['epost'] == $mail && $row['losenord'] == $hashlosen)
				{
					$fornamn = $row['fornamn'];
					$efternamn = $row['efternamn']; 
					$namn = $fornamn ." " .$efternamn;
					$_SESSION["namn"]=$namn;
					$_SESSION["anvandarID"]=$row['id'];
					header('Location:index.php');
				}
				//Hämta nästa rad
				$row = $stmt->fetch();
			}
			
		}
		else
			//En eller flera variabler är tom
			echo "Någonting var tomt. <a href=\"inlogg.html\">Gå tillbaka till inloggningen</a>";
	}
	else if ($_POST["formular"] == "registrera")
	{
		//Registrera användare
		if(isset($_POST["fornamn"]) && isset($_POST["efternamn"]) && isset($_POST["fodelsedatum"]) && isset($_POST["mail"]) && isset($_POST["losenord"]) && isset($_POST["bekraftalosenord"]))
		{
			//SQL-frågan
			$sql = "insert into anvandare(fornamn, efternamn, losenord, epost, fodelseDatum) values(:fornamn, :efternamn, :losenord, :mail, :fodelsedatum)";
		
			$hashlosen = MD5($_POST["losenord"]);
			
			$params = array(':fornamn'=>$_POST["fornamn"], ':efternamn'=>$_POST["efternamn"], ':losenord'=>$hashlosen, ':mail'=>$_POST["mail"], ':fodelsedatum'=>$_POST["fodelsedatum"]);
			
			//skicka fråga till databasservern
			$stmt=$conn->prepare($sql);
	
			//Kör frågan på databasen
			$stmt->execute($params);
			header("location:index.php");
		}
		else
			//En eller flera variabler är tom
			echo "Någonting var tomt. <a href=\"inlogg.html\">Gå tillbaka till inloggningen</a>";
	}
	else
		//Om varken login eller registrera är medskickat
			echo "Någonting var tomt. <a href=\"inlogg.html\">Gå tillbaka till inloggningen</a>";
}
else
{
	//Något märkligt fel har inträffat
			echo "Någonting var tomt. <a href=\"inlogg.html\">Gå tillbaka till inloggningen</a>";
}
?>