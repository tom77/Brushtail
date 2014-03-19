<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
$integers[] = "cat_id";
$integers[] = "location_id";
$integers[] = "status";





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


		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "add_location")
{
 $location_name = $DB->escape($_REQUEST["location_name"]);
 	
$sql = "insert into clicker_location values(NULL,'$location_name','$m','1','0')";	
//echo $sql;
$DB->query($sql,"clickeradmin.php");
	
}


		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "add_category")
{
 $cat_name = $DB->escape($_REQUEST["cat_name"]);
 	
$sql = "insert into clicker_category values(NULL,'$cat_name','$m','1','0')";	
$DB->query($sql,"clickeradmin.php");
	
}


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "delete_cat")
{
 $cat_id = $DB->escape($_REQUEST["cat_id"]);
 	
$sql = "delete from clicker_category where cat_id = '$cat_id'";	
$DB->query($sql,"clickeradmin.php");
	
}	
		

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "delete_location")
{
 $location_id = $DB->escape($_REQUEST["location_id"]);
 	
$sql = "delete from clicker_location where location_id = '$location_id'";	
$DB->query($sql,"clickeradmin.php");
	
}	
	
if (isset($_REQUEST["reordercat"]))
	{
	//reorders pages
	$reorder = explode(",",$_REQUEST["reordercat"]);
	//print_r($reorder);
	foreach ($reorder as $index => $value)
		{
		$index = $DB->escape($index);	
		$value = $DB->escape($value);
		
		$sql = "update clicker_category set position = \"$index\" WHERE cat_id = \"$value\"";	
		//echo $sql;
		$DB->query($sql,"clickeradmin.php");
		}
			
	}

	
if (isset($_REQUEST["reorderloc"]))
	{
	//reorders pages
	$reorder = explode(",",$_REQUEST["reorderloc"]);


	
	foreach ($reorder as $index => $value)
		{
		$index = $DB->escape($index);	
		$value = $DB->escape($value);
		
		$sql = "update clicker_location set position = \"$index\" WHERE location_id = \"$value\"";	
		//echo $sql;
		$DB->query($sql,"clickeradmin.php");
		}
			
	}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "update_location")
{
 $location_id = $DB->escape($_REQUEST["location_id"]);
 $location_name = $DB->escape($_REQUEST["location_name"]);
 	
$sql = "update clicker_location set status = '$status' , location_name = '$location_name' where location_id = '$location_id'";	

$DB->query($sql,"clickeradmin.php");
	
}	

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "update_cat")
{
 $cat_id = $DB->escape($_REQUEST["cat_id"]);
 $cat_name = $DB->escape($_REQUEST["cat_name"]);

$sql = "update clicker_category set status = '$status' , cat_name = '$cat_name' where cat_id = '$cat_id'";	
$DB->query($sql,"clickeradmin.php");
	
}	


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "update_options")
{
 $mode = $DB->escape($_REQUEST["mode"]);


$sql = "delete from  clicker_options  where m = '$m'";	
$DB->query($sql,"clickeradmin.php");

$sql = "insert into  clicker_options  values('$m','$mode')";	
$DB->query($sql,"clickeradmin.php");

	
}	
	
	
	
	
	

	$sql = "select * from modules where m = '$m'";
$DB->query($sql,"clickeradmin.php");
$row = $DB->get();
$modname = formattext($row["name"]);



echo " <h1 class=\"red\">$modname</h1>  ";


if (isset($ERROR))
{
	echo "$ERROR";
}

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edit_location")
{
	
	$sql = "select location_name, status from clicker_location where location_id = '$location_id'";
	
	$DB->query($sql,"clickeradmin.php");
	$row = $DB->get();
	$location_name = formattext($row["location_name"]);
	$status = $row["status"];
	echo "<form action=\"clickeradmin.php\" method=\"post\">
	<b>$phrase[180]</b><br>
	<input type=\"text\" name=\"location_name\" value=\"$location_name\"><br><br>
	<b>$phrase[254]</b><br>
	<select name=\"status\">
	<option value=\"1\">$phrase[13]</option>
	<option value=\"0\"";
	if ($status == "0") {echo " selected";}
	echo ">$phrase[12]</option>
	</select><br><br>
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"location_id\" value=\"$location_id\">
	<input type=\"hidden\" name=\"update\" value=\"update_location\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
	</form>";
	
}

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edit_cat")
{
	
	$sql = "select cat_name, status from clicker_category where cat_id = '$cat_id'";
	$DB->query($sql,"clickeradmin.php");
	$row = $DB->get();
	$cat_name = formattext($row["cat_name"]);
	$status = $row["status"];
	echo "<form action=\"clickeradmin.php\" method=\"post\">
	<b>$phrase[180]</b><br>
	<input type=\"text\" name=\"cat_name\" value=\"$cat_name\"><br><br>
	<b>$phrase[254]</b><br>
	<select name=\"status\">
	<option value=\"1\">$phrase[13]</option>
	<option value=\"0\"";
	if ($status == "0") {echo " selected";}
	echo ">$phrase[12]</option>
	</select><br><br>
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">
	<input type=\"hidden\" name=\"update\" value=\"update_cat\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
	</form>";
	
}
else

{

	$sql = "select mode from clicker_options where m = '$m'";
	$DB->query($sql,"clickeradmin.php");
	$row = $DB->get();
	$mode = $row["mode"];
	
	
	
	echo "<h2>$phrase[696]</h2>
		<form action=\"clickeradmin.php\" method=\"post\">
	<b>$phrase[955]</b><br>
	
	<select name=\"mode\">
	<option value=\"1\">$phrase[956]</option>
	<option value=\"0\"";
	if ($mode == "0") {echo " selected";}
	echo ">$phrase[957]</option>
	</select><br><br>
	<input type=\"hidden\" name=\"m\" value=\"$m\">

	<input type=\"hidden\" name=\"update\" value=\"update_options\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
	</form>
	
	<table><tr><td  style=\"width:20em\">
	
	";
		
		
	
	echo "<h2>$phrase[410]</h2>
	<table class=\"colourtable\">";
		$sql = "select count(timestamp) as count,status, location_name, location_id from clicker_location left join clicker_clicks on clicker_clicks.location = clicker_location.location_id  where m = '$m' group by location_id order by position";
	//echo $sql;
		$DB->query($sql,"clickeradmin.php");
		$num = $DB->countrows();
		$counter = 0;
		
		while ($row = $DB->get())
		{
			$counter++;
		$loc_name[$counter] = $row["location_name"];
		
		$loc_id[$counter] = $row["location_id"];
		$position[$counter] = $row["location_id"];	
		$count[$counter] = $row["count"];		
		$status[$counter] = $row["status"];	
		
	
		}
	
		if (isset($loc_id))
		{
		foreach ($loc_id as $counter => $locationid)
		{
		
			echo "<tr";
			if ($status[$counter] == "0") {echo " style=\"background:#e6e8ea\"";}
			echo "><td>$loc_name[$counter]</td><td>";
			
			if ($counter > 1) {
					
				
				//print_r($position);
							foreach ($position as $i => $value)
									{
									//echo "<br>index is $i $value count is $counter <br>";
									if ($i == ($counter - 1))
										{
										
										$up = $position;
										$up[$i] = $position[$counter];
										$up[$counter] = $value;
										
										}
									}
							
							
							
							//print_r($up);
							$up = implode(",",$up);
							
				echo "<a href=\"clickeradmin.php?reorderloc=$up&m=$m\"><img src=\"../images/up.png\" title=\"$phrase[77]\"></a>";
				
			}
			echo "</td><td>";
			//print_r($position);
			if ($counter < $num) {
				
					foreach ($position as $i => $value)
									{
									//echo "$array_order[$index] <br>";
									if ($i == ($counter))
										{
										$down = $position;
										$temp = $down[$i];
										$down[$i] = $down[$i + 1];
										$down[$i + 1] = $temp;
										}
									}
								
								
							
							$down = implode(",",$down);
							echo "<a href=\"clickeradmin.php?reorderloc=$down&m=$m\"><img src=\"../images/down.png\" title=\"$phrase[78]\"></a>";
			}
			
			echo "<td><a href=\"clickeradmin.php?m=$m&amp;location_id=$locationid&amp;event=edit_location\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
			<td style=\"width:1.6em\">";
			if ($count[$counter] == 0) {
			echo "<a href=\"clickeradmin.php?m=$m&amp;location_id=$locationid&amp;update=delete_location\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>";
			}
			
			echo "</td></tr>";
		}
		}
		
		echo "</table>
		
		<form action=\"clickeradmin.php\" method=\"post\">
<br><input type=\"text\" name=\"location_name\"> <br>
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"update\" value=\"add_location\">

<input type=\"submit\" value=\"$phrase[176]\">
</form>
		
		
		
		
		</td><td><h2>$phrase[884]</h2> <table  class=\"colourtable\">";
		
unset($position);

		$sql = "select count(timestamp) as count,status, position, cat_name, cat_id from clicker_category left join clicker_clicks on clicker_clicks.category = clicker_category.cat_id where m = '$m' group by cat_id order by position";
		//echo $sql;
		$DB->query($sql,"clickeradmin.php");
		$num = $DB->countrows();
		$counter = 0;
		while ($row = $DB->get())
		{
		$counter++;
		$cname[$counter] = $row["cat_name"];
		$_cat_id[$counter] = $row["cat_id"];	
		$count[$counter] = $row["count"];
		$position[$counter] = $row["cat_id"];		
		$status[$counter] = $row["status"];
		}
		
				if (isset($_cat_id))
		{
		foreach ($_cat_id as $counter => $cid)
		{
			echo "<tr";
			if ($status[$counter] == "0") {echo " style=\"background:#e6e8ea\"";}
			echo "><td>$cname[$counter]</td><td>";
			if ($counter > 1) {
					
				
				//print_r($position);
							foreach ($position as $i => $value)
									{
									//echo "<br>index is $i $value count is $counter <br>";
									if ($i == ($counter - 1))
										{
										
										$up = $position;
										$up[$i] = $position[$counter];
										$up[$counter] = $value;
										
										}
									}
							
							
							
							//print_r($up);
							$up = implode(",",$up);
							
				echo "<a href=\"clickeradmin.php?reordercat=$up&m=$m\"><img src=\"../images/up.png\" title=\"$phrase[77]\"></a>";
				
			}
			echo "</td><td>";
			//print_r($position);
			if ($counter < $num) {
				
					foreach ($position as $i => $value)
									{
									//echo "$array_order[$index] <br>";
									if ($i == ($counter))
										{
										$down = $position;
										$temp = $down[$i];
										$down[$i] = $down[$i + 1];
										$down[$i + 1] = $temp;
										}
									}
								
								
							
							$down = implode(",",$down);
							echo "<a href=\"clickeradmin.php?reordercat=$down&m=$m\"><img src=\"../images/down.png\" title=\"$phrase[78]\"></a>";
			}
			
			
			echo "	</td>		<td><a href=\"clickeradmin.php?m=$m&amp;cat_id=$catid&amp;event=edit_cat\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
			<td style=\"width:1.6em\">";
			if ($count[$counter] == 0) {
			echo "<a href=\"clickeradmin.php?m=$m&amp;cat_id=$catid&amp;update=delete_cat\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>";
			}
			echo "</td></tr>";
		}
}
		
		
		echo "</table>
				<form action=\"clickeradmin.php\" method=\"post\">
<br><input type=\"text\" name=\"cat_name\"> <br>
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"update\" value=\"add_category\">

<input type=\"submit\" value=\"$phrase[176]\">
</form>
		
		</td></tr></table>";

	
		
		
		
		
	
}
	
	}		//end contentbox
		echo "</div></div>";
		
		
	include("../includes/rightsidebar.php");

include ("../includes/footer.php");

?>

