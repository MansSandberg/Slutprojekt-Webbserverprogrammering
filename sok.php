<!Doctype html>
<html>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="stilmall.css" />
<title>Minitwitter - Sök</title>

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
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Användare
$sqlanvandare="select id, fornamn, efternamn, presentation from anvandare where fornamn like :sokterm or efternamn like :sokterm";

if(isset($_GET["sokruta"]))
{
	$params = array(':sokterm'=>$_GET["sokruta"].'%');
}
else
{
	header('Location:index.php');
}
	//Skicka frågan till databasen
	$stmt = $conn->prepare($sqlanvandare);
	
	//Kör frågan
	$stmt->execute($params);
	
	//Hämta resultat från databasen
	$row = $stmt->fetch();
	
	//Avsluta om det inte kom någon rad
	if (!$row)
	{
		exit();
	}

	echo"<div class=\"inlagg\"><table><tr><th>Användare</th></tr>";
	//Upprepa så länge det finns en rad
	while ($row != null)
	{
		$fornamn = $row['fornamn'];
		$efternamn = $row['efternamn'];
		$presentation = $row['presentation'];
		$anvandarID = $row['id'];
	
		//Skriv ut resultatet
		echo"<tr><td><a href=\"anvandare.php?id=$anvandarID\">$fornamn $efternamn</a></td><td>$presentation</td></tr>";
		
		//Hämta nästa rad
		$row = $stmt->fetch();
	}

echo "</table>
		</div>
	</div>
    
</div>
<div style=\"text-align:center\">
  <a href=\"#toppen\">Gå till toppen</a>
  </div>";
?>
<script src="visa.js"></script>
</body>
</html>
