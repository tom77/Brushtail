<?php



include ("../includes/initiliaze_page.php");

$m = array();
$locations = array();



$sql = "select m from modules where type = 'c'";

$DB->query($sql,"editcalendar.php");
				while ($row = $DB->get()) 
				{
				$m[] = $row["m"];
				}
				

$sql = "select branchno from cal_branches";

$DB->query($sql,"editcalendar.php");
				while ($row = $DB->get()) 
				{
				$locations[] = $row["branchno"];
				}


foreach ($m as $module)
	{
	foreach ($locations as $location)
	{
	$sql = "insert into cal_branch_bridge values('$module','$location')";
	$DB->query($sql,"editcalendar.php");
	
	}
	}




?>