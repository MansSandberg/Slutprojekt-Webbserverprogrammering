function registrera()
{
	document.getElementById("loggaIn").style.display = "none";
	document.getElementById("registrera").style.display = "block";
}

function bekraftaLosenord()
{
	var losenord=document.getElementById("nyttlosenord");
	var bekraftaLosenord=document.getElementById("bekraftalosenord");
	
	if(losenord.value==bekraftaLosenord.value)
	{
		//document.getElementById("registreraKnapp").disabled=false;
		bekraftaLosenord.style.backgroundColor="#FFFFFF";
		return true;
	}
	else
	{
		//document.getElementById("registreraKnapp").disabled=true;
		bekraftaLosenord.style.backgroundColor="#FF0000";
		return false;
	}
}
 
function alderKontroll()
{
	var idag = new Date();
	var fodelsedatum = document.getElementById("fodelsedatum").value;
	var fodelse = new Date(fodelsedatum);
	var alder = new Date(idag-fodelse).getFullYear()-1970;
	if (alder > 13)
	{
		//window.alert(alder);
		return true;
	}
	else
	{
		document.getElementById("fodelsedatum").style.backgroundColor="#FF0000";
		return false;
	}
}

function formularKontroll()
{
	try
	{
	if (bekraftaLosenord() && alderKontroll())
	{
		return true;
	}
	else
		return false;
	}
	catch(error)
	{
		window.alert(error);
	}
}