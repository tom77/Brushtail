<?php



include ("../includes/initiliaze_page.php");

//$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

echo " ";

if ($_REQUEST["mode"] == "full")
{
$params[0] = "";
	if (isset($_REQUEST["max"]))
	{
	$params[1] = $_REQUEST["max"];	
	}
	else {$params[1] = "";}
	$params[2] = $_REQUEST["link"];
	
	rss_full($params);
}


	
elseif ($_REQUEST["mode"] == "brief")


{
	$params[0] = "";
	if (isset($_REQUEST["max"]))
	{
	$params[1] = $_REQUEST["max"];	
	}
	else {$params[1] = "";}
	$params[2] = $_REQUEST["link"];
	

	rss_brief($params);
	
	
	
}

else {echo "no data";}







	
		

	



?>

