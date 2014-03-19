<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");


echo "<div id=\"mainbox\"><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>";





			
			
			

if (isset($_REQUEST["view"]) && $_REQUEST["view"] == "day")
	{
	$year = $DB->escape($_REQUEST["year"]);
	$monthnumber = $DB->escape($_REQUEST["monthnumber"]);
	$daynumber = $DB->escape($_REQUEST["daynumber"]);
	$dayname = $_REQUEST["dayname"];
	$monthname = $_REQUEST["monthname"];
	echo "<div class=\"swouter\">
	<form class=\"swinner\" action=\"\"><fieldset><legend>$phrase[187]</legend><br>
	<b>$dayname $daynumber $monthname</b><br><br>
	<table class=\"colourtable\" cellpadding=\"5\" ><tr><td><b>$phrase[185]</b></td><td><b>$phrase[3]</b></td><td><b>$phrase[144]</b></td></tr>";

	
if ($DB->type == "mysql")
		{
	$sql = "select *, UNIX_TIMESTAMP(logtime) as visittime from loginsfailed where year(logtime) = \"$year\" and month(logtime) = \"$monthnumber\" and dayofmonth(logtime) = \"$daynumber\"";	
		}
		
else
		{
	$sql = "select *, strftime('%s',logtime) as visittime from loginsfailed where strftime('%Y',logtime) = \"$year\" and strftime('%m',logtime) = \"$monthnumber\" and strftime('%d',logtime) = \"$daynumber\"";	
		}	
		
		
		
		$DB->query($sql,"failedlogins.php");
		$num= $DB->countrows();
		while ($row = $DB->get()) 
		{
		$visittime = $row["visittime"];
		$displaytime = date("h:i a",$visittime);
		$logip = $row["logip"];
	
		$logname = formattext($row["logname"]);
		
        
		echo "<tr><td>$displaytime</td><td>$logname</td><td>$logip</td></tr>";
		}
		
		echo "</table></fieldset></form></div>";
	}
elseif (isset($_REQUEST["view"]) && $_REQUEST["view"] == "month")
	{
		$year = $DB->escape($_REQUEST["year"]);
	$monthnumber = $DB->escape($_REQUEST["monthnumber"]);
	$monthname = $_REQUEST["monthname"];
	
	echo "
	<div class=\"swouter\">
	<form class=\"swinner\" action=\"\"><fieldset><legend>$phrase[187]</legend><br><b>$monthname $year</b><br><br>
	<table class=\"colourtable\" cellpadding=\"5\" ><tr><td><b>$phrase[182]</b></td><td><b>$phrase[186]</b></td><td><b>$phrase[187]</b></td></tr>";

if ($DB->type == "mysql")
		{
	$sql = "select count(*) as visits, dayofmonth(logtime) as daynumber from loginsfailed where year(logtime) = \"$year\" and month(logtime) = \"$monthnumber\" group by dayofmonth(logtime) desc";
		}

	else
		{
	$sql = "select count(*) as visits, strftime('%d',logtime) as daynumber from loginsfailed where strftime('%Y',logtime) = \"$year\" and strftime('%m',logtime) = \"$monthnumber\" group by daynumber order by daynumber desc";
		}
		
		
		$DB->query($sql,"failedlogins.php");
		$num= $DB->countrows();
		//$count = 0;
		while ($row = $DB->get()) 
		{
		$visits = $row["visits"];
		$daynumber = $row["daynumber"];
		$daynumber = str_pad($daynumber, 2, "0", STR_PAD_LEFT);
		$dayname  = strftime("%A",mktime(0, 0, 0, $monthnumber, $daynumber,  $year));
		$date  = strftime("%x",mktime(0, 0, 0, $monthnumber, $daynumber,  $year));
        
		echo "<tr><td>$dayname</td><td align=\"left\"> <a href=\"failedlogins.php?event=faillogin&amp;monthname=$monthname&amp;monthnumber=$monthnumber&amp;year=$year&amp;daynumber=$daynumber&amp;dayname=$dayname&amp;view=day\">$date</a><td>$visits</td></tr>";
		}
		
		echo "</table></fieldset></form></div>";
		
	
	}

else {

echo "
	<div class=\"swouter\">
<form class=\"swinner\" action=\"\"><fieldset><legend>$phrase[187]</legend><br>
<table class=\"colourtable\" cellpadding=\"5\" ><tr><td><b>$phrase[181] cc</b></td><td><b>$phrase[187]</b></td></tr>";

if ($DB->type == "mysql")
		{
	$sql = "select count(*) as visits, month(logtime) as monthnumber, year(logtime) as year from loginsfailed group by year(logtime) desc, month(logtime) desc ";	
		}
else
		{
	$sql = "select count(*) as visits, strftime('%m',logtime) as monthnumber, strftime('%Y',logtime)as year from loginsfailed group by year, monthnumber order by year ";	
		}	
		
		
		$DB->query($sql,"failedlogins.php");
		$num= $DB->countrows();
		//$count = 0;
		while ($row = $DB->get()) 
		{
		$visits = $row["visits"];
		$monthnumber = $row["monthnumber"];
		$monthnumber = str_pad($monthnumber, 2, "0", STR_PAD_LEFT);
		$year = $row["year"];
        $monthname  = strftime("%B",mktime(0, 0, 0, $monthnumber, 01,  $year));
		echo "<tr><td align=\"left\"> <a href=\"failedlogins.php?event=faillogin&amp;monthnumber=$monthnumber&amp;year=$year&amp;monthname=$monthname&amp;view=month\">$monthname $year</a></td><td>$visits</td></tr>";
		}
		
		echo "</table></fieldset></form></div>";
		
				







	}

echo "</div>";
include ("../includes/footer.php");

?>

