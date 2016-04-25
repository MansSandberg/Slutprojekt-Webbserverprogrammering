<html>
<head>
<meta charset="utf-8">
<title>Minitwitter - Användare</title>
<link rel="stylesheet" type="text/css" href="style/stilmall.css" />
<link rel="stylesheet" type="text/css" href="style/anvandare.css" />
</head>
<?php
session_start();
header("Content-type:text/html");
if (!isset($_SESSION["namn"]))
{
	header('Location:inlogg.html');
}
header('charset=utf-8');
$anvandarID = $_SESSION["anvandarID"];

if(isset($_GET["id"]))
{
	$profilID = $_GET["id"];
}
else
{
	$profilID = $_SESSION["anvandarID"];
}

//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");

//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Läs in prfilinfo
$sqlprofil = "select id, presentation, bredd, hojd from anvandare where id = $profilID";

//Skicka frågan till databasen
$stmt = $conn->prepare($sqlprofil);
	
//Kör frågan
$stmt->execute();
	
//Hämta resultat från databasen
$row = $stmt->fetch();
	
//Avsluta om det inte kom någon rad
if (!$row)
{
	echo "Den användaren finns inte";
	die();
}

//Upprepa så länge det finns en rad
if ($row != null)
{
	$profilinfo = $row['presentation'];
	$profilbildsbredd = $row['bredd'];
	$profilbildshojd = $row['hojd'];
}

//Profilbild
//Utgå från att bredden ska vara 200px
//Kolla förhållandet mellan bildens bredd och önskad bredd
$bilddiff = $profilbildsbredd/200;

//Utgå från önskad bredd
//Se till att höjden är proportionerlig

if ($bilddiff > 0)
{
	$nybildhojd = $profilbildshojd/$bilddiff;
}
else
{
	$nybildhojd = $profilbildshojd;
}

echo "<a name=\"toppen\"></a>
<div id=\"content\"><div id=\"header\">
	<img id=\"logo\" src=\"minitwitter.png\" alt=\"logotype\" />
	</div>
    <div id=\"meny\">
    <a class=\"meny\" href=\"index.php\"><div class=\"menyknapp\"><p>Hem</p></div></a>
    <a class=\"meny\" href=\"anvandare.php?id=$anvandarID\"><div class=\"menyknapp\"><p>Min profil</p></div></a>
    <a class=\"meny\" href=\"loggaut.php\"><div id=\"menyknapploggaut\"><p>Logga ut</p></div></a>
	
	<div id=\"sok\">
	<form id=\"sokform\" name=\"sokform\" method=\"get\" action=\"sok.php\">
		<fieldset id=\"sokfalt\">
			<input type=\"search\" id=\"sokruta\" name=\"sokruta\" placeholder=\"Sök efter användare\" required>
			<input type=\"submit\" value=\"Sök\">
		</fieldset>
	</form>
	</div>
    </div>
	
	<!--Användarprofil-->
	<div id=\"profil\">
    <div id=\"profilInfo\">
    <p class=\"status\">$profilinfo</p>";
	
	if ($profilID == $_SESSION["anvandarID"])
	{
		//Besöker sin egen profil
		echo "
			<!--Knapp för att ändra presentation-->
			<button type=\"button\" id=\"redigeraprofil\" onClick=\"visaProfilAndraing()\">Ändra profil</button>
			<button type=\"button\" onclick=\"window.open('foljer.php')\">Vilka följer jag?</button>";
	}
	else
	{
		//Besöker någon annans profil
		//Kolla om användaren följs
		$sqlkollafoljstatus = "select foljarID, foljdID from foljningar where foljarid=$anvandarID and foljdid=$profilID";
		
		//Skicka frågan till databasen
		$stmt2 = $conn->prepare($sqlkollafoljstatus);
			
		//Kör frågan
		$stmt2->execute();
			
		//Hämta resultat från databasen
		$row = $stmt2->fetch();
			
		//Avsluta om det inte kom någon rad
		if (!$row)
		{
			echo "<p>Du följer inte</p>";
			echo"<button type=\"button\" onClick=\"window.location.href='foljning.php?funktion=folj&id=$profilID'\">Följ</button>";
		}
		
		//Upprepa så länge det finns en rad
		if ($row != null)
		{
			echo "<p>Du följer</p>";
			echo"<button type=\"button\" onClick=\"window.location.href='foljning.php?funktion=slutafolja&id=$profilID'\">Sluta följa</button>";
		}
	}
	echo "
	<br/><br/>
	
	<!--Formulär för ändring av presentation-->
	<form id=\"formpresentation\" method=\"POST\" action=\"uppdaterapresentation.php\" hidden>
		<textarea id=\"nypresentation\" name=\"nypresentation\" rows=\"2\" cols=\"60\" form=\"formpresentation\">$profilinfo</textarea>
		<br />
		<input class=\"skickastatus\" type=\"submit\" value=\"Spara\">
	</form>
	
	<!--Formulär för ändring av profilbild-->
	<form id=\"formprofilbild\" method=\"POST\" action=\"uppdaterapresentation.php\" enctype=\"multipart/form-data\" hidden>
		<input class=\"bildvaljare\" id=\"nyprofilbild\" name=\"nyprofilbild\" type=\"file\" value=\"Ladda upp profilbild\">
		<br />
		<input class=\"skickastatus\" type=\"submit\" value=\"Spara\">
	</form>
	
    </div>
    <div id=\"profilBild\">
	<img id=\"profilbilden\" src=\"visaprofilbild.php?id=$profilID\" width=\"200\" height=\"$nybildhojd\"/>";
	if ($profilID == $_SESSION["anvandarID"])
	{
		echo"
		<!--Knapp för profilbildsbyte-->
			<div style=\"width:100%;height:auto;display:block;overflow:auto;text-align:center;\">
				<button type=\"button\" onClick=\"visaProfilbildsandring()\" style=\"position:relative;width:200px;margin:0px;\">Ladda upp profilbild</button>
			</div>";
	}
		
	echo "
	<br />
   	</div>
	</div>
    <div id=\"flode\">	
	";
	if(isset($_GET["id"]))
	{
		if ($_GET["id"] == $_SESSION["anvandarID"])
		{
		echo "
		<!--Skriv ett nytt inlägg-->
		<div class=\"inlagg\">
			<form method=\"POST\" action=\"skapainlagg.php\" id=\"statusform\" enctype=\"multipart/form-data\">
				<fieldset>
					<textarea id=\"statustext\" name=\"statustext\" rows=\"2\" cols=\"70\" form=\"statusform\" placeholder=\"Skriv din status här\" maxlength=\"140\" required></textarea>		
					<br />
					<input class=\"skickastatus\" id=\"skickastatus\" name=\"skickastatus\" type=\"submit\" value=\"Skicka\">
					<br />
					<button type=\"button\" class=\"bilduppladdning\" onClick=\"visaUppladdning()\">Lägg till bild</button>

					<input class=\"bildvaljare\" id=\"bild\" name=\"bild\" type=\"file\" value=\"Ladda upp bild\" hidden=\"hidden\">
			</fieldset>
		</form>
	</div>
";
		}
	}

//Inläggsflödet

$sql = "select id, datumskapat, text, bild, anvandarID, namnAnvandare from inlagg where anvandarid = $profilID order by datumskapat desc";


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
	$profilID = $row['anvandarID'];
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
		<p class=\"status\"><b><a href=\"anvandare.php?id=$profilID\">$namn</a></b> $text</p>";
		
		if ($bild != null)
		{
			//Lägg tll bild
			echo "<img src=\"visabild.php?id=$inlaggsid\" alt=\"Bild\" />";
		}
		echo "<div class=\"interagera\">
        		<a class=\"gillakommentera\" href=\"gilla.php?inlagg=$inlaggsid\">Gilla</a>
        		<a class=\"gillakommentera\" href=\"inlagg.php?id=$inlaggsid\">Kommentera</a>";
			if ($profilID == $_SESSION["anvandarID"])
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
	echo "
    </div>
    
</div>
<div style=\"text-align:center\">
  <a href=\"#toppen\">Gå till toppen</a>
  </div>";
?>
<body>
<script src="visa.js"></script>
</body>
</html>
