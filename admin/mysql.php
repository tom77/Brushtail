<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");


echo "<div id=\"mainbox\"><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>";


	
	
	if (isset($_REQUEST["repair"]))
		{
		$repair= $DB->escape($_REQUEST["repair"]);
		$sql = "repair table $repair";
		echo "$sql";
		$DB->query($sql,"mysql.php");
		$row = $DB->get();
		
	
	$table = $row["Table"];
	$type = $row["Msg_type"];
	$text = $row["Msg_text"];
		
		if ($text == "OK")
			{	echo "<h2>Table repair succeeded</h2>$table: $type $text";}
		else
			{	echo "<h2 style=\"color:#ff3366;\">Table repair failed</h2>$table: $type $text";}
			
		
		}
	
	
	
	
	
$msg = "";

	
$tables = mysql_query("SHOW TABLES");


if (!$tables || mysql_num_rows($tables) <= 0) {
echo "Could not iterate database tables\n";}


echo "
<div class=\"swouter\">
<form class=\"swinner\" action=\"\"><fieldset><legend>Mysql database check</legend><br>

This is intended as a simple utility for checking database integrity. <br>
For more information about database repair consult the MySQL manual.<br><br><table border=\"1\" cellpadding=\"4\" >
<tr><td><b>Table name</b></td><td><b>Export</b></td> <td><b>Status</b></td><td><b>Repair</b></td></tr>";
while (list($tname) = mysql_fetch_row($tables)) 
	{
	
	//$status = mysql_query("CHECK TABLE `$tname`");
	$status = $DB->query("CHECK TABLE `$tname`","mysql.php");
	if (!$status || $DB->countrows <= 0) 
		{
		$msg .= "Could not get status for table $tname\n";
		}
	
	//mysql_data_seek($status, mysql_num_rows($status)-1);
	//$row_status = mysql_fetch_assoc($status);
	//$row = mysql_fetch_array($status);
	$row = $DB->get();
	$table = $row["Table"];
	$urltable = urlencode($table);
	$type = $row["Msg_type"];
	$text = $row["Msg_text"];
	echo  "<tr><td>$table</td><td><a href=\"export.php?table=$urltable\">export</a></td><td>$text</td><td>";
	if (($text <> "OK") || ($type == "error"))
		{echo "<a href=\"mysql.php?repair=$table\">Repair table</a>";} else { echo "&nbsp;";}
	
	echo "</td></tr>";
			
			
	}
	echo "</table></fieldset></form>";

		

echo "</div>";
include ("../includes/footer.php");

?>

