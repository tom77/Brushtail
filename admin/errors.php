<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");

if (isset($_POST["delete"]))
	{
	$sql = "delete from errors";
	$DB->query($sql,"errors.php");
	}
	
	
if (isset($_REQUEST["logging"]))
	{
	$logging = $DB->escape($_REQUEST["logging"]);
	$sql = "update preferences set pref_value = '$logging' where pref_name = 'errorlogging'";
	$DB->query($sql,"errors.php");
	}
	
	
	

include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>
";

$sql = "select * from preferences where pref_name = 'errorlogging'";	
$DB->query($sql,"errors.php");
$row = $DB->get();
$logging = $row["pref_value"];


echo "<br><br><form method=\"post\" action=\"errors.php\" ><fieldset><legend>$phrase[35]</legend>
<br>$phrase[36]<br><br>";

if ($logging == "on")
{
	echo "<b>$phrase[618]&nbsp;&nbsp;<a href=\"errors.php?logging=off\">$phrase[621]</a></b>";
}
else {
		echo "<b>$phrase[619] <a href=\"errors.php?logging=on\">$phrase[620]</a></b>";
}

$sql = "select * from errors order by errorid desc";	
$DB->query($sql,"errors.php");
$num = $DB->countrows();

echo "<br><br>";

if ($num > 0) {
echo "

<table cellpadding=\"10\" style=\"margin:0 auto\">";
}

while ($row = $DB->get())
	{
		
		$date = $month = strftime("%x %X", $row["errordate"]);
		$message = nl2br($row["message"]);
		echo "<tr><td>$date</td><td>$row[script]</td><td>$message</td></tr>";
		
	}
	
if ($num > 0) { echo "</table>";}	
echo "<input type=\"submit\" value=\"$phrase[623]\" name=\"delete\"></fieldset></form><br>";



echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");
?>

