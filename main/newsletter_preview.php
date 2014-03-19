<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);




	

$integers[] = "content_id";
$integers[] = "issue_id";
$integers[] = "temp_id";






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
	$sql = "select input from modules where m = '$m'";
	
$DB->query($sql,"noticeboard.php");
$row = $DB->get();

$input = $row["input"];


$issue_id = $DB->escape($_REQUEST["issue_id"]);	

$newsletter = newsletter($input,$issue_id,$DB);
echo $newsletter;
}
?>