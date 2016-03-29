<link rel="stylesheet" type="text/css" href="stilmall.css" />
<style>
#profil
{
	background-color:#FFFFFF;
	width:1000px;
	height:auto;
	display:block;
	overflow:auto;
	margin-bottom:5px;
	float:left;
}
#profilInfo
{
	background-color:#FFFFFF;
	width:800px;
	height:auto;
	display:block;
	overflow:auto;
	margin-top:150px;
	margin-bottom:10px;
	z-index:2;
	clear:none;
	float:left;
}

#profilBild
{
	background-color:#FFFFFF;
	width:200px;
	height:auto;
	display:block;
	overflow:auto;
	margin-top:150px;
	margin-bottom:10px;
	display:block;
	z-index:2;
	float:left;
	text-align:center;}

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

if(isset($_GET["id"]))
{
	$anvandarID = $_GET["id"];
}
else
{
	$anvandarID = $_SESSION["anvandarID"];
}

//skapa koppling till databasen, ange server, databas, teckenuppsättning, användarnamn och lösenord
$conn=new PDO("mysql:host=127.0.0.1;dbname=minitwitter;charset=UTF8","root","");

//tala om att fel skall visas som fel (bra vid utveckling, mindre bra vid skarp drift)
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Läs in prfilinfo
$sqlprofil = "select id, presentation, bredd, hojd from anvandare where id = $anvandarID";

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
    </div>
	
	<!--Användarprofil-->
	<div id=\"profil\">
    <div id=\"profilInfo\">
    <p class=\"status\">$profilinfo</p>";
	
	if ($anvandarID == $_SESSION["anvandarID"])
	{
		echo "
			<!--Knapp för att ändra presentation-->
			<button type=\"button\" id=\"redigeraprofil\" onClick=\"visaProfilAndraing()\">Ändra profil</button>";
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
	<img id=\"profilbilden\" src=\"visaprofilbild.php?id=$anvandarID\" width=\"200\" height=\"$nybildhojd\"/>";
	if ($anvandarID == $_SESSION["anvandarID"])
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

$sql = "select id, datumskapat, text, bild, anvandarID, namnAnvandare from inlagg where anvandarid = $anvandarID order by datumskapat desc";


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
	echo "
    </div>
    
</div>
<div style=\"text-align:center\">
  <a href=\"#toppen\">Gå till toppen</a>
  </div>";
?>
<html>
<head>
<meta charset="utf-8">
<title>Minitwitter - Användare</title>

</head>

<body>
<script src="visa.js"></script>
</body>
</html>
