<!Doctype html>
<html>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="stilmall.css" />
<title>Minitwitter - Inlägg</title>

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
	$_SESSION["sortering"]="kronologisk";
}

if(isset($_GET["id"]))
{
}
else
{
	header('Location:index.php');
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
	<a name=\"toppen\"></a>";

//Visa sökresultaten

//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");

//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Hämta det aktuella inlägget
$sqlinlagg="select id, datumskapat, text, bild, anvandarID, namnAnvandare from inlagg where id =:id";

$params = array(':id'=>$_GET["id"]);

//Skicka frågan till databasen
$stmt = $conn->prepare($sqlinlagg);
	
//Kör frågan
$stmt->execute($params);
	
//Hämta resultat från databasen
$row = $stmt->fetch();
	
//Avsluta om det inte kom någon rad
if (!$row)
{
	exit();
}

echo"<div class=\"inlagg\">";
//Upprepa så länge det finns en rad
if ($row != null)
{
	$inlaggsID = $row['id'];
	$text = $row['text'];
	$anvandarID = $row['anvandarID'];
	$namn = $row['namnAnvandare'];
	$bild = $row['bild'];
	
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
		
		//Räkna antal kommentarer
		$sqlkommentarer ="select count(*) as antalkommentarer from kommentarer where inlaggsid = :inlaggsid";
		
		//Skicka frågan till databasen
		$stmt2=$conn->prepare($sqlkommentarer);
		
		//Kör frågan
		$stmt2->execute($params2);
		
		//Hämta resultat från databasen
		$row = $stmt2->fetch();
		
		if($row == null)
		{
			$antalkommentarer = 0;
		}
		
		if($row != null)
		{
			$antalkommentarer = $row['antalkommentarer'];
		}
		
	//Skriv ut inlägget
	echo "<p class=\"status\"><b><a href=\"anvandare.php?id=$anvandarID\">$namn</a></b> $text</p>";
				
	if ($bild != null)
	{
		//Lägg tll bild
		echo "<img src=\"visabild.php?id=$inlaggsID\" alt=\"Bild\" />";
	}
	echo "<div class=\"interagera\">
				<a class=\"gillakommentera\" href=\"gilla.php?inlagg=$inlaggsID\">Gilla</a>
				<a class=\"gillakommentera\">Kommentera</a>";
		if ($anvandarID == $_SESSION["anvandarID"])
		{
			echo "<a class=\"gillakommentera\" href=\"radera.php?id=$inlaggsID\">Radera</a>";
		}
		echo"
				<p class=\"interaktioner\">$antalgillar personer gillar detta</p>
				<p class=\"interaktioner\">$antalkommentarer kommentarer</p>
		</div>
	</div>";
	echo"<div class=\"inlagg\">
	<h2>Kommentera</h2>";
	
	//Kommentarsfält
	echo"
	<form method=\"POST\" action=\"kommentera.php\" id=\"kommentar\" enctype=\"multipart/form-data\">
		<fieldset>
		<textarea id=\"kommentar\" name=\"kommentar\" rows=\"2\" cols=\"70\" form=\"kommentar\" placeholder=\"Skriv din kommentar här\" maxlength=\"140\" required></textarea>		
		<input type=\"hidden\" id=\"id\" name=\"id\" value=\"$inlaggsID\">
		<br />
		<input class=\"skickastatus\" id=\"skickastatus\" name=\"skickakommentar\" type=\"submit\" value=\"Skicka\">
		</fieldset>
		</form>
		</div>";
		
	//Kommentarerna
		
	//Hämta kommentarer
		
	$sqlkommentarer = "select id, text, inlaggsid, anvandarid from kommentarer where inlaggsid = :id order by id desc";
		
	//Skicka frågan till databasen
	$stmt = $conn->prepare($sqlkommentarer);
	
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
		$id = $row['id'];
		$text = $row['text'];
		$anvandarID = $row['anvandarid'];
		
		//Ta reda på vem som kommenterade
		$sqlkommentator = "select id, fornamn, efternamn from anvandare where id = :anvandarid";
		
		$paramsvem = array(':anvandarid'=>$anvandarID);
		
		
		//Skicka frågan till databasen
		$stmt2 = $conn->prepare($sqlkommentator);
		
		//Kör frågan
		$stmt2->execute($paramsvem);
		
		//Hämta resultat från databasen
		$row2 = $stmt2->fetch();
		
		//Avsluta om det inte kom någon rad
		if (!$row2)
		{
			exit();
		}
	
		//Upprepa så länge det finns en rad
		if ($row2 != null)
		{
			$kommentatorNamn = $row2['fornamn'] . ' ' .$row2['efternamn'];
		}
			
			//Skriv ut kommentaren
			echo"<a id\"$id\"></a>
			<div class=\"inlagg\">
			<p class=\"status\"><b><a href=\"anvandare.php?id=$anvandarID\">$kommentatorNamn</a></b> $text</p></div>
			";
			
			$row = $stmt->fetch();
	}
}
else
{
	echo "Någonting har blivit fel";
}

echo "
    
</div>
<div style=\"text-align:center\">
  <a href=\"#toppen\">Gå till toppen</a>
  </div>";
?>
</body>
</html>
