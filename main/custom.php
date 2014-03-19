<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);





$integers = array();


foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}	
	
	 if (isset($ERROR))
	{
   
	header("Location: $url" . "error.php?error=input");
	exit();
	}
        
        elseif (!isset($m))
		{
		
		
		header("Location: $url" . "error.php?error=module");
		exit();
		}

	
	$ip = ip("pc");  

	
	$access->check($m);
	
	if ($access->thispage < 1)
		{
		
		
		header("Location: $url" . "error.php?error=permissions");
		exit();
		}
	elseif ($access->iprestricted == "yes")
		{
		header("Location: $url" . "error.php?error=ipaccess");
		exit();
		}

	

if (!isset($ERROR)) 
	{

	$sql = "select name, menupath from modules where m = '$m'";

	$DB->query($sql,"custom.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);
	$menupath = $row["menupath"];
		
	page_view($DB,$PREFERENCES,$m,"");	
	
	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

echo "<div style=\"text-align:center\">";

	include('../includes/leftsidebar.php');
		
			

	  	echo "<div id=\"content\"><div>";
	  
	  	
	  	$path = "custom/" . $menupath;
	
include($path);
	
	echo "</div></div>";
	

	}

	echo "</div>";


	include('../includes/rightsidebar.php');

	
include ("../includes/footer.php");

?>


  