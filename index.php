<!Doctype html>
<html>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="stilmall.css" />
<title>Minitwitter</title>

</head>
<body>
<style>
form
{
	margin:10px;
}

fieldset
{
	width:950px;
	margin:0px;
	border:0;
	clear:none;
	float:left;
}

#sok
{
	width:auto;
	height:50px;
	padding:0px;
	float:left;
	
}

fieldset#sokfalt
{
	width:auto;
	height:48px;
	vertical-align:middle;
	margin:0px;
	float:left;
}
input[type="search"]
{
	font-size:24px;
	margin:0px;
	vertical-align:middle;
}

input[type="text"}
{
	margin:10px;
}

</style>
<?php
session_start();
header("Content-type:text/html");
if (!isset($_SESSION["namn"]))
{
	header('Location:inlogg.html');
}
header('charset=utf-8');
$anvandarID = $_SESSION["anvandarID"];
if(!isset($_SESSION["sortering"]))
{
	$_SESSION["sortering"]="kronologiskt";
}
echo"
<div id=\"content\"><div id=\"header\">
	<img id=\"logo\" src=\"minitwitter.png\" alt=\"logotype\" />
	</div>
    <div id=\"meny\">
    <a class=\"meny\" href=\"index.php\"><div class=\"menyknapp\"><p>Hem</p></div></a>
    <a class=\"meny\" href=\"anvandare.php?id=$anvandarID\"><div class=\"menyknapp\"><p>Min profil</p></div></a>
	
	<div id=\"sok\">
	<form id=\"sokform\" name=\"sokform\" method=\"get\" action=\"sok.php\">
		<fieldset id=\"sokfalt\">
			<input type=\"search\" id=\"sokruta\" name=\"sokruta\" placeholder=\"Sök efter användare\" required>
			<input type=\"submit\" value=\"Sök\">
		</fieldset>
	</form>
	</div>
	
    <a class=\"meny\" href=\"loggaut.php\"><div id=\"menyknapploggaut\"><p>Logga ut</p></div></a>
    </div>
    
    <div id=\"flode\">	
	
	<!--Skriv ett nytt inlägg-->
	<div class=\"inlagg\">
		<form method=\"POST\" action=\"skapainlagg.php\" id=\"statusform\" enctype=\"multipart/form-data\">
		<fieldset>
		<textarea id=\"statustext\" name=\"statustext\" rows=\"2\" cols=\"70\" form=\"statusform\" placeholder=\"Skriv din status här\" maxlength=\"140\" required></textarea>		
		<br />
		<input class=\"skickastatus\" id=\"skickastatus\" name=\"skickastatus\" type=\"submit\" value=\"Skicka\">
		<br /><br />
		<button type=\"button\" class=\"bilduppladdning\" onClick=\"visaUppladdning()\">Lägg till bild</button>

		<input class=\"bildvaljare\" id=\"bild\" name=\"bild\" type=\"file\" value=\"Ladda upp bild\" hidden=\"hidden\">
		</fieldset>
		</form>
	</div>
";
echo "Inläggen är sorterade ".$_SESSION['sortering'] ."<a href=\"andrasortering.php\">Ändra sortering</a> (kronologiskt/populärast)";
echo "<a name=\"toppen\"></a>";
//Inläggsflödet

//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");

//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Kolla vilken ordning inläggen ska visas i
if($_SESSION["sortering"] == "kronologiskt")
{
	//Kronologiskt flöde
	$sql = "select id, datumskapat, text, bild, anvandarID, namnAnvandare from inlagg order by datumskapat desc";

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
		$text = $row['text'];
		$bild = $row['bild'];
		$anvandarID = $row['anvandarID'];
		$inlaggsid = $row['id'];
		$namn = $row['namnAnvandare'];
	
		//Räkna antal gilla-markeringar
		$sqlgillar = "select count(*) as antalgillar from gillningar where inlaggsid = :inlaggsid";
	
		$params2 = array(':inlaggsid'=>$row['id']);
	
		//Skicka frågan till databasen
		$stmt2 = $conn->prepare($sqlgillar);
	
		//Kör frågan
		$stmt2->execute($params2);
	
		//Hämta resultat från databasen
		$row = $stmt2->fetch();
	
		if($row == null)
		{
			$antalgillar = 0;
		}
		if($row != null)
		{
			$antalgillar = $row['antalgillar'];
		}
	
		//Skriv ut inlägget
    		echo "
			<a id=\"$inlaggsid\"></a>
			<div class=\"inlagg\">
			<p class=\"status\"><b><a href=\"anvandare.php?id=$anvandarID\">$namn</a></b> $text</p>";
		
			if ($bild != null)
			{
				//Lägg tll bild
				echo "<img src=\"visabild.php?id=$inlaggsid\" alt=\"Bild\" />";
			}
			echo "<div class=\"interagera\">
        		<a class=\"gillakommentera\" href=\"gilla.php?inlagg=$inlaggsid\">Gilla</a>
        		<a class=\"gillakommentera\">Kommentera</a>";
				if ($anvandarID == $_SESSION["anvandarID"])
				{
					echo "<a class=\"gillakommentera\" href=\"radera.php?id=$inlaggsid\">Radera</a>";
				}
				echo"
        		<p class=\"interaktioner\">$antalgillar personer gillar detta</p>
        		<p class=\"interaktioner\">2 kommentarer</p>
            	</div>
        	</div>";
		
		//Hämta nästa rad
		$row = $stmt->fetch();
	}
}
else if ($_SESSION["sortering"] == "populart")
{
	//Populärt flöde
	
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
		$ID = $row['inlaggsid'];	
		$antalgillningar = $row['antalGillningar'];
		//Räkna antal gilla-markeringar
		$sqlinlagg = "select id, datumskapat, text, bild, anvandarID, namnAnvandare from inlagg where id=:id";
	
		$params2 = array(':id'=>$ID);
	
		//Skicka frågan till databasen
		$stmt2 = $conn->prepare($sqlinlagg);
	
		//Kör frågan
		$stmt2->execute($params2);
	
		//Hämta resultat från databasen
		$row = $stmt2->fetch();
	
		$text = $row['text'];
		$bild = $row['bild'];
		$inlaggsid = $row['id'];
		$namn = $row['namnAnvandare'];
		
		if($row == null)
		{
			$antalgillar = 0;
		}
		if($row != null)
		{
			$antalgillar = $antalgillningar;
		}
	
		//Skriv ut inlägget
    		echo "
			<a id=\"$inlaggsid\"></a>
			<div class=\"inlagg\">
			<p class=\"status\"><b><a href=\"anvandare.php?id=$anvandarID\">$namn</a></b> $text</p>";
		
			if ($bild != null)
			{
				//Lägg tll bild
				echo "<img src=\"visabild.php?id=$inlaggsid\" alt=\"Bild\" />";
			}
			echo "<div class=\"interagera\">
        		<a class=\"gillakommentera\" href=\"gilla.php?inlagg=$inlaggsid\">Gilla</a>
        		<a class=\"gillakommentera\">Kommentera</a>";
				if ($anvandarID == $_SESSION["anvandarID"])
				{
					echo "<a class=\"gillakommentera\" href=\"radera.php?id=$inlaggsid\">Radera</a>";
				}
				echo"
        		<p class=\"interaktioner\">$antalgillar personer gillar detta</p>
        		<p class=\"interaktioner\">2 kommentarer</p>
            	</div>
        	</div>";
		
		//Hämta nästa rad
		$row = $stmt->fetch();
	
	}

}
else
{
	echo "Någonting har blivit fel. ";
}
	echo "
    </div>
    
</div>
<div style=\"text-align:center\">
  <a href=\"#toppen\">Gå till toppen</a>
  </div>";
?>
<script src="visa.js"></script>
</body>
</html>
