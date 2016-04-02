<?php
session_start();
if($_SESSION["sortering"] == "kronologiskt")
{
	$_SESSION["sortering"] = "populart";
}
else if($_SESSION["sortering"] == "populart")
{
	$_SESSION["sortering"] = "kronologiskt";
}
header('Location: ' . $_SERVER['HTTP_REFERER']);
?>