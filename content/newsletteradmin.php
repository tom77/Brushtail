<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
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
		$ERROR  =  $phrase[72];
	
		}		
	}
else {
	$ERROR  =  $phrase[72];
	
}	





	$now = time();
$ip = ip("pc");
	

		
		
		include("../includes/leftsidebar.php");
		
			echo "<div id=\"content\"><div>";
		
	if (!isset($ERROR))
	{	


		
		



if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete")
	{
	
$sql = "delete from newsletter_templates where temp_id = '$temp_id' and temp_m = '$m'";

	$DB->query($sql,"newsletteradmin.php");
//deletepage($delpage,$ip,$DB);
	
		}
		
		
		

			


	





	
if (isset($_REQUEST["update"]) &&  $_REQUEST["update"] == "add")
	{

	$temp_name = trim($_REQUEST["temp_name"]);
	if ($temp_name == "")
		{
		$ERROR = "$phrase[82]";
		
		}
	else
		{
		 
		 
		  if (!isset($ERROR)) 
		  {
		 $temp_name = $DB->escape($_REQUEST["temp_name"]);
		 $temp_file = $DB->escape($_REQUEST["temp_file"]);
		 $temp_address = $DB->escape($_REQUEST["temp_address"]);
		 $temp_subject = $DB->escape($_REQUEST["temp_subject"]);
		 $temp_from = $DB->escape($_REQUEST["temp_from"]);
		 
		 $sql = "INSERT INTO newsletter_templates VALUES(NULL,'$m','$temp_name','$temp_file','$temp_subject','$temp_address','$temp_from')"; 

   		 $DB->query($sql,"newsletteradmin.php");
   			
   			
			}	

	}
	}
	
	

	
	
	
	if (isset($_REQUEST["update"]) &&  $_REQUEST["update"] == "update")
	{

	$temp_name = trim($_REQUEST["temp_name"]);
	if ($temp_name == "")
		{
		$ERROR = "$phrase[82]";
		
		}
	else
		{
		 
		 
		  if (!isset($ERROR)) 
		  {
		 $temp_name = $DB->escape($_REQUEST["temp_name"]);
		 $temp_file = $DB->escape($_REQUEST["temp_file"]);
		 $temp_address = $DB->escape($_REQUEST["temp_address"]);
		 $temp_subject = $DB->escape($_REQUEST["temp_subject"]);
		 $temp_from = $DB->escape($_REQUEST["temp_from"]);
		 
		 $sql = "update newsletter_templates set temp_name = '$temp_name', temp_file = '$temp_file',temp_subject = '$temp_subject',temp_address = '$temp_address',
		 temp_from = '$temp_from' where temp_m = '$m' and temp_id = '$temp_id'"; 
		//echo $sql;
   		 $DB->query($sql,"newsletteradmin.php");
   			
   			
			}	

	}
	}
	
	
	
	
	
	
	
	
	}
	

	$sql = "select * from modules where m = \"$m\"";
$DB->query($sql,"page.php");
$row = $DB->get();
$modname = formattext($row["name"]);



echo " <h1 class=\"red\">$modname</h1>  ";


if (isset($ERROR))
{
	echo "$ERROR";
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delete")
	{
	
	echo "<br><b>$phrase[14]</b><br><br><br><br>
	<a href=\"newsletteradmin.php?m=$m&amp;update=delete&amp;temp_id=$temp_id\">$phrase[12]</a> | <a href=\"newsletteradmin.php?m=$m\">$phrase[13]</a>";
	}


	
	

	
	
	





	
		
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit")
		{
	$sql = "SELECT temp_name, temp_file, temp_from, temp_address, temp_subject FROM newsletter_templates where temp_id = \"$temp_id\" and temp_m = '$m'";
	$DB->query($sql,"page.php");
	$row = $DB->get();
	$temp_name = $row["temp_name"];
	$temp_file = $row["temp_file"];
	$temp_subject = $row["temp_subject"];
	$temp_address = $row["temp_address"];
	$temp_from = $row["temp_from"];
	
	
	echo "
	
	
	<form method=\"post\" action=\"newsletteradmin.php\" >
	<fieldset>
	<legend>$phrase[28]</legend>
	<table >
	<tr><td class=\"formlabels\">$phrase[141]</td><td style=\"text-align:left\"><input type=\"text\" name=\"temp_name\" value=\"$temp_name\" size=\"50\"></td></tr>
	<tr><td class=\"formlabels\">$phrase[945]</td><td style=\"text-align:left\"><input type=\"text\" name=\"temp_file\" value=\"$temp_file\" size=\"50\"></td></tr>
	<tr><td class=\"formlabels\">$phrase[101]</td><td style=\"text-align:left\"><input type=\"text\" name=\"temp_address\" value=\"$temp_address\" size=\"50\"></td></tr>
	<tr><td class=\"formlabels\">$phrase[103]</td><td style=\"text-align:left\"><input type=\"text\" name=\"temp_from\" value=\"$temp_from\" size=\"50\"></td></tr>
	<tr><td class=\"formlabels\">$phrase[102]</td><td style=\"text-align:left\"><input type=\"text\" name=\"temp_subject\" value=\"$temp_subject\" size=\"50\"></td></tr>
	<tr><td></td><td style=\"text-align:left\"><input type=\"submit\" value=\"$phrase[28]\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"temp_id\" value=\"$temp_id\">
	<input type=\"hidden\" name=\"update\" value=\"update\">
	</td></tr>
	

	</td></tr>
	</table>
	</fieldset>
	
	
	
	</form>";
	
		}
	
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "add")
		{

	
	echo "
	
	
	<form method=\"post\" action=\"newsletteradmin.php\" >
	<fieldset>
	<legend>$phrase[176]</legend>
	<table >
	<tr><td class=\"formlabels\">$phrase[141]</td><td style=\"text-align:left\"><input type=\"text\" name=\"temp_name\" size=\"50\"></td></tr>
	<tr><td class=\"formlabels\">$phrase[945]</td><td style=\"text-align:left\"><input type=\"text\" name=\"temp_file\" size=\"50\"></td></tr>
	<tr><td class=\"formlabels\">$phrase[101]</td><td style=\"text-align:left\"><input type=\"text\" name=\"temp_address\" size=\"50\"></td></tr>
	<tr><td class=\"formlabels\">$phrase[103]</td><td style=\"text-align:left\"><input type=\"text\" name=\"temp_from\" size=\"50\"></td></tr>
	<tr><td class=\"formlabels\">$phrase[102]</td><td style=\"text-align:left\"><input type=\"text\" name=\"temp_subject\" size=\"50\"></td></tr>
	<tr><td></td><td style=\"text-align:left\"><input type=\"submit\" value=\"$phrase[176]\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"add\">
	</td></tr>
	

	</td></tr>
	</table>
	</fieldset>
	
	
	
	</form>";
	
		}
	else	{
			
			echo 	"<a href=\"newsletteradmin.php?event=add&amp;m=$m\"><img src=\"../images/add.png\" title=\"$phrase[176]\"></a>";
		
			$sql = "select temp_id, temp_name, temp_file, temp_subject, temp_address, temp_from from newsletter_templates where temp_m = '$m'";
			$DB->query($sql,"page.php");
			$num = $DB->countrows();
			if ($num > 0) {
				
				echo "<form action=\"newsletteradmin.php\" method=\"post\"><br><table class=\"colourtable\">
				<tr style=\"font-weight:bold\"><td>$phrase[141]</td><td>$phrase[945]</td><td></td><td>$phrase[26]</td><td>$phrase[24]</td></tr>
				";
			}
			while ($row = $DB->get())
			{
			$temp_id = $row["temp_id"];	
			$temp_name = $row["temp_name"];	
			$temp_file = $row["temp_file"];	
			$temp_subject = $row["temp_subject"];	
			$temp_address = $row["temp_address"];	
			$temp_from = $row["temp_from"];	
			
			echo "<tr><td>$temp_name</td><td>$temp_file</td>
			<td>
			$phrase[101] $temp_address<br>
			$phrase[103] $temp_from<br>
			$phrase[102] $temp_subject
			</td>
			<td><a href=\"newsletteradmin.php?m=$m&amp;event=edit&amp;temp_id=$temp_id\"><img src=\"../images/page_white_edit.png\" title=\"$phrase[26]\"></a></td>
			<td><a href=\"newsletteradmin.php?m=$m&amp;event=delete&amp;temp_id=$temp_id\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"></a></td></tr>";
				
			}
			
				if ($num > 0) {
				
				echo "</table>
				</form>
				";
			}
			
			
			
		}

		
	
	
	
	
	
		//end contentbox
		echo "</div></div>";
		
		
	include("../includes/rightsidebar.php");

include ("../includes/footer.php");

?>

