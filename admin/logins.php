<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");


include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>";
			
			
			

if (isset($_REQUEST["view"]) && $_REQUEST["view"] == "day")
	{
	$year = $DB->escape($_REQUEST["year"]);
	$monthnumber = $DB->escape($_REQUEST["monthnumber"]);
	$daynumber = $DB->escape($_REQUEST["daynumber"]);
	$dayname = $_REQUEST["dayname"];
	$monthname = $_REQUEST["monthname"];
	echo "	<div >
	<form  action=\"\"><fieldset><legend>$phrase[188]</legend><br><br>
	<b>$dayname $daynumber $monthname</b><br><br>
	<table class=\"colourtable\" cellpadding=\"5\" ><tr><td><b>Time</b></td><td><b>User</b></td><td><b>IP</b></td></tr>";

	
	if ($DB->type == "mysql")
		{	
	$sql = "select *, hour(logtime) as hour, minute(logtime) as minute, second(logtime) as second from logins where year(logtime) = \"$year\" and month(logtime) = \"$monthnumber\" and dayofmonth(logtime) = \"$daynumber\"  order by logtime desc";	
		}
		
	else
		{	
	$sql = "select *, strftime('%H',logtime) as hour, strftime('%M',logtime) as minute, strftime('%S',logtime) as second from logins 
	where strftime('%Y',logtime) = \"$year\" and strftime('%m',logtime) = \"$monthnumber\" and strftime('%d',logtime) = \"$daynumber\" order by logtime desc";	
		}
	
		
		$DB->query($sql,"logins.php");
		$num= $DB->countrows();
		while ($row = $DB->get()) 
		{
		$second = $row["second"];
		$minute = $row["minute"];
		$hour = $row["hour"];
		$timestamp = mktime($hour,$minute,$second,$monthnumber,$daynumber,$year);
		
		$visittime = date("h:i a",$timestamp);
		$logip = $row["logip"];
		
		$logname = formattext($row["logname"]);
		
        
		echo "<tr><td>$visittime</td><td>$logname</td><td>$logip</td></tr>";
		}
		
		echo "</table></fieldset></form></div>";
	}
elseif (isset($_REQUEST["view"]) && $_REQUEST["view"] == "month")
	{
		$year = $DB->escape($_REQUEST["year"]);
	$monthnumber = $DB->escape($_REQUEST["monthnumber"]);
	$monthname = $_REQUEST["monthname"];
	
	echo "	<div >
	<form  action=\"\"><fieldset><legend>$phrase[188]</legend><br>
	
	<b>$monthname $year</b><br><br>
	
	<table class=\"colourtable\" cellpadding=\"5\" ><tr><td><b>$phrase[182]</b></td><td><b>$phrase[186]</b></td><td><b>$phrase[188]</b></td></tr>";
	
	if ($DB->type == "mysql")
		{
	$sql = "select count(*) as visits, dayofmonth(logtime) as daynumber from logins where year(logtime) = \"$year\" and month(logtime) = \"$monthnumber\" group by dayofmonth(logtime) desc";
		}
		
		else
		{
	$sql = "select count(*) as visits, strftime('%d',logtime) as daynumber from logins where strftime('%Y',logtime) = \"$year\" and strftime('%m',logtime) = \"$monthnumber\" group by daynumber order by daynumber desc";
		}	

		//echo $sql;
			
		$DB->query($sql,"logins.php");
		$num= $DB->countrows();
		//$count = 0;
		while ($row = $DB->get()) 
		{
		$visits = $row["visits"];
		$daynumber = $row["daynumber"];
		$daynumber = str_pad($daynumber, 2, "0", STR_PAD_LEFT);
		$dayname  = strftime("%A",mktime(0, 0, 0, $monthnumber, $daynumber,  $year));
		$date  = strftime("%x",mktime(0, 0, 0, $monthnumber, $daynumber,  $year));
        
		echo "<tr><td>$dayname</td><td align=\"left\"> <a href=\"logins.php?monthname=$monthname&amp;monthnumber=$monthnumber&amp;year=$year&amp;daynumber=$daynumber&amp;dayname=$dayname&amp;view=day\">$date</a><td>$visits</td></tr>";
		}
		
		echo "</table></fieldset></form></div>";
		
	
	}

else {

echo "	
	<div >
<form  action=\"\"><fieldset><legend>$phrase[188]</legend><br>
<table class=\"colourtable\" cellpadding=\"5\" ><tr><td><b>$phrase[181]</b></td><td><b>$phrase[188]</b></td></tr>";
	if ($DB->type == "mysql")
		{
			
	$sql = "select count(*) as visits, month(logtime) as monthnumber, year(logtime) as year from logins group by year(logtime) desc, month(logtime) desc ";	
		}
		else
		{
			$sql = "select count(*) as visits, strftime('%Y',logtime) as year, strftime('%m',logtime)  as monthnumber from logins group by year, monthnumber order by year desc ,monthnumber desc";		
			
		}
		
		//echo $sql;
		
		$DB->query($sql,"logins.php");
		$num= $DB->countrows();
		//$count = 0;
		while ($row = $DB->get()) 
		{
		$visits = $row["visits"];
		$monthnumber = $row["monthnumber"];
		$monthnumber = str_pad($monthnumber, 2, "0", STR_PAD_LEFT);
		$year = $row["year"];
        $monthname  = strftime("%B",mktime(0, 0, 0, $monthnumber, 01,  $year));
		echo "<tr><td align=\"left\"> <a href=\"logins.php?monthnumber=$monthnumber&amp;year=$year&amp;monthname=$monthname&amp;view=month\">$monthname $year</a></td><td>$visits</td></tr>";
		}
		
		echo "</table></fieldset></form></div>";
		
				







	}


echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

