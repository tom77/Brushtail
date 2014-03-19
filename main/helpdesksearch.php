<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


if (isset($_REQUEST["m"])) 
	{
	if (!(isinteger($_REQUEST["m"]))) {$ERROR  = "not an integer";}
	else {$m = $_REQUEST["m"];}
	}
	

if (isset($m) && isinteger($m))
{
	
	$access->check($m);
	
	if ($access->thispage < 1)
		{
		
		$ERROR  =  "<div style=\"text-align:center;font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
		}
	elseif ($access->iprestricted == "yes")
		{
	$ERROR  =  $phrase[0];
		}
	
	


	
if (isset($WARNING))
	{
	echo "$WARNING";
	}
	

elseif (isset($ERROR))
	{
	echo "$ERROR";
	}
else 
	{
		$include = "yes";
	include("helpdesksearchinclude.php");	
	}

}	
?>