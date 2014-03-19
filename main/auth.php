<?php


error_reporting(E_ALL);
ini_set('display_errors','On');

include ("../includes/initiliaze_page.php");




//print_r($_REQUEST);



//$ipaddress = $_SERVER['REMOTE_ADDR'];

//if ($ipaddress != "192.168.10.233" && $ipaddress != "192.168.10.11")
//{
	//echo "Remote access disallowed";
	//exit();
	
//}



if (!isset($_REQUEST["password"]) || !isset($_REQUEST["barcode"]))
{
	echo "Patron barcode and password must be supplied";
	exit();
	
}






//include("../includes/functions.php");
include("../classes/PcBookings.php");


$password = $DB->escape(substr($_REQUEST["password"],0,100));
$barcode = $DB->escape(substr($_REQUEST["barcode"],0,100));


//check if patron banned

$BAN= new PcBookings($DB);

if ($BAN->checkPatronBan($barcode) == "yes")
{
echo "USERERR";	
exit();	
	
}



//check password
	include("../classes/AuthenticatePatron.php");

	$CHECK = new AuthenticatePatron($password,$barcode,$PREFERENCES,$DB,"pc");
	
	if ($CHECK->auth == "yes")
	{ 
		echo "USEROK";
	}
	else 
	{
		
	echo "USERERR";	
	}



?>