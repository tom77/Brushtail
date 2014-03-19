<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

echo "<div style=\"text-align:center\">";






if (isset($_REQUEST["m"]))
	{	
	
	if (isinteger($_REQUEST["m"]))
		{	
		$m = $_REQUEST["m"];	
		$access->check($m);
	
	if ($access->thispage < 3)
			{
		
			$ERROR  =  "<div style=\"text-align:center;font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
			}
	elseif ($access->iprestricted == "yes")
			{
			$ERROR  =  $phrase[0];
			}
	
		}
	else 
		{
		$ERROR  =  "$phrase[72]";
	
		}		
	}
else {
	$ERROR  =  "no module chosen";
}	

if (!isset($ERROR))
{	
$sql = "select name,adminpath from modules where m = \"$m\"";
$DB->query($sql,"customedit.php");
			$row = $DB->get();
$modname = formattext($row["name"]);
$adminpath = $row["adminpath"];

echo "<h1>$modname</h1>";

	  	$path = "custom/" . $adminpath;
	
include($path);


	
}