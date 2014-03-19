<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

	
$ip = ip("pc");
$proxy = ip("proxy");

$integers[] = "no";
$integers[] = "id";
$integers[] = "branch_id";
$integers[] = "category";
$integers[] = "m";



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (isset($_REQUEST["keywords"]))
{ 
	if ($_REQUEST["keywords"] == "")
	{ $ERROR = $phrase[219];} else {
	$keywords = $_REQUEST["keywords"];}
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
	$sql = "select name from modules where m = '$m'";
	$DB->query($sql,"inventory.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);	
		
	page_view($DB,$PREFERENCES,$m,"");	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


echo "<div style=\"text-align:center\">";


		
		
		
		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete" && $access->thispage > 1)
{
	$sql = "delete from hardware where no = '$no'";
	$DB->query($sql,"inventory.php");
}



		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "item"  && $access->thispage > 1)
{
	$id = $DB->escape($_REQUEST["itemid"]);
	$category = $DB->escape($_REQUEST["category"]);
	$notes = $DB->escape($_REQUEST["notes"]);
	$location = $DB->escape($_REQUEST["branch_id"]);
	

	
	$sql = "update hardware set location = '$location', category = '$category', notes = '$notes', id = '$id' where no = '$no'";
	$DB->query($sql,"inventory.php");
	
}



		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletebranch"  && $access->thispage > 1)
{
	$branch_id = $DB->escape($_REQUEST["branch_id"]);

		$sql2 = "select count(*) as number from hardware where location = '$branch_id' and m = '$m'";
	$DB->query($sql2,"inventory.php");
		$row2 = $DB->get(); 
		$number = $row2["number"];
		
 if ($number == "0")
 {
	$sql = "delete from hardware_locations where id = '$branch_id' and m = '$m'";
	$DB->query($sql,"inventory.php");
 }
}



		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletecat"  && $access->thispage > 1)
{
	$category = $DB->escape($_REQUEST["category"]);

		$sql2 = "select count(*) as number from hardware where category = '$category' and m = '$m'";
	$DB->query($sql2,"inventory.php");
		$row2 = $DB->get(); 
		$number = $row2["number"];
		
	
		
 if ($number == "0")
 {
	$sql = "delete from hardware_categories where id = '$category'";
	$DB->query($sql,"inventory.php");

 }
}




		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "editbranch"  && $access->thispage > 1)
{
	$branch_id = $DB->escape($_REQUEST["branch_id"]);
	$name = $DB->escape($_REQUEST["name"]);

	

	
	$sql = "update hardware_locations set name = '$name' where id = '$branch_id'";

	$DB->query($sql,"inventory.php");
	
}



if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "editcategory"  && $access->thispage > 1)
{
	$category = $DB->escape($_REQUEST["category"]);
	$name = $DB->escape($_REQUEST["name"]);

	

	
	$sql = "update hardware_categories set name = '$name' where id = '$category'";

	$DB->query($sql,"inventory.php");
	
}

		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addlocation"  && $access->thispage > 1)
{

	$name = $DB->escape($_REQUEST["name"]);

	

	
	$sql = "insert into hardware_locations values(NULL,'$name','$m')";

	$DB->query($sql,"inventory.php");
	
}


		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addcategory"  && $access->thispage > 1)
{

	$name = $DB->escape($_REQUEST["name"]);

	

	
	$sql = "insert into hardware_categories values(NULL,'$name','$m')";

	$DB->query($sql,"inventory.php");
	
}

		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "add"  && $access->thispage > 1)
{
	$id = $DB->escape($_REQUEST["itemid"]);
	$branch_id = $DB->escape($_REQUEST["branch_id"]);
	$category = $DB->escape($_REQUEST["category"]);
	$notes = $DB->escape($_REQUEST["notes"]);
	//$location = trim($DB->escape($_REQUEST["location"]));
	
	if ($branch_id == "")
	{
	echo "<h2>Addition failed. No location specified</h2>";
		
	}
	else 
	{
	$sql = "insert into hardware values (NULL, '$id','$notes', '$branch_id', '$m', '$category')";
	$DB->query($sql,"inventory.php");
	}
				
}








	
		
		
		
		
	
		
		
	
			echo "
		<h1>$modname</h1>";
	if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delete")
	{	
		$view = $_REQUEST["view"];
		
		if (isset($_REQUEST["branch_id"]))
		{
		$branch_id = urlencode($_REQUEST["branch_id"]); 
		$link = "branch_id=$branch_id"	;
		}
		
		if (isset($_REQUEST["category"]))
		{
		$category = urlencode($_REQUEST["category"]); 
		$link = "category=$category"	;
		}
		
		
		
	echo "<br><b>$phrase[14]</b><br><br>
	<a href=\"inventory.php?m=$m&amp;no=$no&amp;update=delete&amp;event=$view&amp;$link\">$phrase[12]</a> | <a href=\"inventory.php?m=$m&amp;event=$view&amp;$link\">$phrase[13]</a>";
	
		
	}
	
	
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "totals")
		{
		
				echo "<a href=\"inventory.php?m=$m\">$phrase[404]</a><br>";
			
				
		
				$sql= "select category,name, count(category) as total from hardware, hardware_categories where  and hardware_categories.m = '$m' and hardware_categories.id = hardware.category group by category order by name"; 
		$DB->query($sql,"inventory.php");
	
				
		echo "<br><b>$phrase[405]</b><br>
		<br>
		<Table cellpadding=\"5\" class=\colourtable\"  style=\"margin-left:auto;margin-right:auto;text-align:left\"><tr><td>$phrase[406]</td><td>$phrase[407]</td></tr>";
				while ($row = $DB->get()) {
				$category = formattext($row["category"]);
				$name = formattext($row["name"]);
				$total = formattext($row["total"]);
				
				echo "<tr><td>$name</td><td>$total</td></tr>";
				}
				echo "</table>";
			
			

		
		
		}
	
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "search")
	{
	$keywords = $DB->escape($keywords);	
	$sql = "select hardware.no as no, hardware.id as id ,hardware.notes as notes,hardware_categories.name as category, hardware_locations.name as location from hardware, hardware_categories,hardware_locations 
        where hardware.m = '$m' and hardware_locations.id = hardware.location 
	and hardware_categories.id = hardware.category and MATCH (notes) AGAINST (\"$keywords\" IN BOOLEAN MODE)";
		
		//echo $sql;
		
	$DB->query($sql,"inventory.php");
		
		echo "<a href=\"inventory.php?m=$m\">$phrase[404]</a><br><br>
		
<script type=\"text/javascript\">
 function pop_window(url) {
  var popit = window.open(url,'console','status,resizable,scrollbars,width=500,height=300');
 }
 </script>
$phrase[329]  <b>$keywords</b><br><br>";
		
		echo "<table cellpadding=\"5\" class=\"colourtable\"  style=\"width:80%;text-align:left;margin:0 auto;\"><tr>
		<td></td>
		<td><b>$phrase[340]</b></td>
		<td><b>$phrase[406]</b></td>
		<td><b>$phrase[121]</b></td>
		<td><b>$phrase[409]</b></td>";
		
		if ($access->thispage > 1)
		{
		echo "<td></td><td></td>";
		
		}
		
		echo "</tr>";
		$counter = 0;
		while ($row = $DB->get()) 
			{
				$counter++;
			$no = $row["no"];
			$id = $row["id"];
			$notes = nl2br($row["notes"]);
			$category = $row["category"];
			$location = $row["location"];
			
			echo "<tr><td>$counter</td><td>$id</td><td>$category</td><td>$location</td><td>$notes</td>";
			
			if ($access->thispage > 1)
			{
			echo "
			<td><a href=\"inventory.php?m=$m&amp;event=edit&amp;no=$no\">$phrase[26]</a></td><td><a href=\"inventory.php?m=$m&amp;event=delete&amp;no=$no\">$phrase[24]</a></td>";
			//<a href=\"javascript:pop_window('inventoryview.php?m=$m&amp;no=$no')\">notes</a>
			}
			echo "</tr>";
			}
			
		echo "</table>";

	
	}
	
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "duplicate")
	{
		
		$view = $_REQUEST["view"];
	
	$sql = "SELECT * FROm hardware where no = '$no' and m = '$m'";
	$DB->query($sql,"inventory.php");
	$row = $DB->get();

			
				$no = $row["no"];
				
				$id = formattext($row["id"]);
				
				$category = formattext($row["category"]);
				$notes = $row["notes"];
				$location = $row["location"];
				
			
			
echo "<a href=\"inventory.php?m=$m\">$phrase[404]</a><br<br>
<form action=\"inventory.php\" method=\"post\" style=\"margin-left:auto;margin-right:auto;width:80%\"> <fieldset><legend>$phrase[912]</legend>
	<table border=\"0\"  cellpadding=\"7\">	
	
	
	<tr><td align=\"right\"><b>$phrase[340]</b></td><td align=\"left\"><input type=\"text\" name=\"itemid\" value=\"$id\" size=\"60\"></td></tr>
	<tr><td align=\"right\"><b>$phrase[406]</b></td><td align=\"left\">
		<select name=\"category\">";

$sql = "select * from hardware_categories where  m = '$m' order by name";
	$DB->query($sql,"inventory.php");
	while ($row = $DB->get())
	{
		$name = $row["name"];
		$id = $row["id"];
		
		
	echo "<option value=\"$id\"";
	if ($id == $category) {echo " selected";}
	echo ">$name</option>";	
		
	}



echo "</select>
	
	
	</td></tr>
	<tr><td align=\"right\"><b>$phrase[180]</b></td><td align=\"left\">
	<select name=\"branch_id\">";

$sql = "select * from hardware_locations  where m = '$m' order by name";
	$DB->query($sql,"inventory.php");
	while ($row = $DB->get())
	{
		$name = $row["name"];
		$id = $row["id"];
		
		
	echo "<option value=\"$id\"";
	if ($id == $location) {echo " selected";}
	echo ">$name</option>";	
		
	}



echo "</select>
	
	
	</td></tr>
	 <tr><td valign=\"top\" align=\"right\">$phrase[409]</td><td align=\"left\"><textarea cols=\"70\" rows=\"10\" name=\"notes\">$notes</textarea><br>
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"event\" value=\"$view\">

<input type=\"hidden\" name=\"update\" value=\"add\">
<input type=\"hidden\" name=\"no\" value=\"$no\">
<input type=\"submit\" name=\"edit\" value=\"$phrase[912]\">
			
</td></tr>
	</table></fieldset>	</form> ";	

	}
	
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit")
	{
		
		$view = $_REQUEST["view"];
	
	$sql = "SELECT * FROm hardware where no = \"$no\"  and m = '$m'";
	$DB->query($sql,"inventory.php");
	$row = $DB->get();

			
				$no = $row["no"];
				
				$id = formattext($row["id"]);
				
				$category = formattext($row["category"]);
				$notes = $row["notes"];
				$location = $row["location"];
				
			
			
echo "<a href=\"inventory.php?m=$m\">$phrase[404]</a><br<br>
<form action=\"inventory.php\" method=\"post\" style=\"margin-left:auto;margin-right:auto;width:80%\"> <fieldset><legend>$phrase[26]</legend>
	<table border=\"0\"  cellpadding=\"7\">	
	
	
	<tr><td align=\"right\"><b>$phrase[340]</b></td><td align=\"left\"><input type=\"text\" name=\"itemid\" value=\"$id\" size=\"60\"></td></tr>
	<tr><td align=\"right\"><b>$phrase[406]</b></td><td align=\"left\">
		<select name=\"category\">";

$sql = "select * from hardware_categories where m = '$m' order by name";
	$DB->query($sql,"inventory.php");
	while ($row = $DB->get())
	{
		$name = $row["name"];
		$id = $row["id"];
		
		
	echo "<option value=\"$id\"";
	if ($id == $category) {echo " selected";}
	echo ">$name</option>";	
		
	}



echo "</select>
	
	
	</td></tr>
	<tr><td align=\"right\"><b>$phrase[180]</b></td><td align=\"left\">
	<select name=\"branch_id\">";

$sql = "select * from hardware_locations where m = '$m' order by name";
	$DB->query($sql,"inventory.php");
	while ($row = $DB->get())
	{
		$name = $row["name"];
		$id = $row["id"];
		
		
	echo "<option value=\"$id\"";
	if ($id == $location) {echo " selected";}
	echo ">$name</option>";	
		
	}



echo "</select>
	
	
	</td></tr>
	 <tr><td valign=\"top\" align=\"right\">$phrase[409]</td><td align=\"left\"><textarea cols=\"70\" rows=\"10\" name=\"notes\">$notes</textarea><br>
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"event\" value=\"$view\">

<input type=\"hidden\" name=\"update\" value=\"item\">
<input type=\"hidden\" name=\"no\" value=\"$no\">
<input type=\"submit\" name=\"edit\" value=\"$phrase[28]\">
			
</td></tr>
	</table></fieldset>	</form> ";	

	}
	
	
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "add")
	{
	
	
	//print_r($_REQUEST);	
	if (isset($_REQUEST["branch_id"])) {$branch_id = $DB->escape($_REQUEST["branch_id"]);}	 else {$branch_id = "0";}
	if (isset($_REQUEST["category"])) {$category = $DB->escape($_REQUEST["category"]);}	 else {$category = "0";}		
			
			
echo "<a href=\"inventory.php?m=$m\">$phrase[404]</a><br<br>
<form action=\"inventory.php\" method=\"post\" style=\"margin-left:auto;margin-right:auto;width:80%\">
 <fieldset><legend>$phrase[176]</legend>
	<table border=\"0\"  cellpadding=\"7\">	
	
	
	<tr><td align=\"right\"><b>$phrase[340]</b></td><td align=\"left\"><input type=\"text\" name=\"itemid\"  size=\"60\"></td></tr>
	<tr><td align=\"right\"><b>$phrase[406]</b></td><td align=\"left\">
	<select name=\"category\">";

$sql = "select * from hardware_categories where m = '$m' order by name";
	$DB->query($sql,"inventory.php");
	while ($row = $DB->get())
	{
		$name = $row["name"];
		$id = $row["id"];
		
		
	
	echo "<option value=\"$id\"";
	if ($id == $category) {echo " selected";}
	echo ">$name</option>";	
		
	}



echo "</select>
	</td></tr>
	<tr><td align=\"right\"><b>$phrase[180]</b></td><td align=\"left\"><select name=\"branch_id\">";

$sql = "select * from hardware_locations where m = '$m' order by name";
	$DB->query($sql,"inventory.php");
	while ($row = $DB->get())
	{
		$name = $row["name"];
		$id = $row["id"];
		
		
	
	echo "<option value=\"$id\"";
	if ($id == $branch_id) {echo " selected";}
	echo ">$name</option>";	
		
	}



echo "</select></td></tr>
	 <tr><td valign=\"top\" align=\"right\"><b>$phrase[409]</b></td><td align=\"left\"><textarea cols=\"70\" rows=\"10\" name=\"notes\"></textarea><br>
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"update\" value=\"add\">
<br>";
if (isset($branch_id))
{

	
	echo "
	<input type=\"hidden\" name=\"event\" value=\"branchview\">
	";
}

echo "
<input type=\"submit\" name=\"edit\" value=\"$phrase[176]\">
			
</td></tr>
	</table></fieldset>	</form> ";	

	}
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editbranch")
	
	{
		
		$branch_id = $DB->escape($_REQUEST["branch_id"]);
		
		
		$sql = "select name from hardware_locations where id = '$branch_id'  and m = '$m'";
		
		$DB->query($sql,"inventory.php");
		$row = $DB->get(); 
		$name = $row["name"];
		
		
		$sql = "select name from hardware_locations where id=\"$branch_id\"  and m = '$m'";
		
		$DB->query($sql,"inventory.php");
		
		echo "<a href=\"inventory.php?m=$m\">$phrase[404]</a><br<br><h2>$phrase[488]</h2>
	
		<form action=\"inventory.php\" method=\"post\">
		
		<input name=\"name\" type=\"text\" size=\"60\" value=\"$name\">
		<input name=\"m\" type=\"hidden\" value=\"$m\">
			<input name=\"branch_id\" type=\"hidden\" value=\"$branch_id\">
		<input name=\"update\" type=\"hidden\" value=\"editbranch\">
		<input name=\"submit\" type=\"submit\" value=\"$phrase[28]\">
		
		
		
		</form>";
		
		
	}
	
	
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editcat")
	
	{
		
		$category = $DB->escape($_REQUEST["category"]);
		
		
		$sql = "select name from hardware_categories where id = '$category'  and m = '$m'";
		
		$DB->query($sql,"inventory.php");
		$row = $DB->get(); 
		$name = $row["name"];
		
		
		$sql = "select name from hardware_categories where id=\"$category\"  and m = '$m'";
		
		$DB->query($sql,"inventory.php");
		
		echo "<form action=\"inventory.php\" method=\"post\">
		<a href=\"inventory.php?m=$m\">$phrase[404]</a><br<br>
		<h2>$phrase[850]</h2>
		<input name=\"name\" type=\"text\" size=\"60\" value=\"$name\">
		<input name=\"m\" type=\"hidden\" value=\"$m\">
			<input name=\"category\" type=\"hidden\" value=\"$category\">
		<input name=\"update\" type=\"hidden\" value=\"editcategory\">
		<input name=\"submit\" type=\"submit\" value=\"$phrase[850]\">
		
		
		
		</form>";
		
		
	}
	
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addlocation")
	
	{
		
	
		
	
		
	
		
		echo "<a href=\"inventory.php?m=$m\">$phrase[404]</a><br<br>
		<h2>$phrase[177]</h2>
		<form action=\"inventory.php\" method=\"post\">

		<input name=\"name\" type=\"text\" size=\"60\">
		<input name=\"m\" type=\"hidden\" value=\"$m\">
	
		<input name=\"update\" type=\"hidden\" value=\"addlocation\">
		<input name=\"submit\" type=\"submit\" value=\"$phrase[177]\">
		
		
		
		</form>";
		
		
	}
	
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addcategory")
	
	{
		
	
		
	
		
	
		
		echo "<a href=\"inventory.php?m=$m\">$phrase[404]</a><br<br>
		<h2>$phrase[845]</h2>
		<form action=\"inventory.php\" method=\"post\">

		<input name=\"name\" type=\"text\" size=\"60\">
		<input name=\"m\" type=\"hidden\" value=\"$m\">
	
		<input name=\"update\" type=\"hidden\" value=\"addcategory\">
		<input name=\"submit\" type=\"submit\" value=\"$phrase[845]\">
		
		
		
		</form>";
		
		
	}
	
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "catview")
	
	{
		$category = $DB->escape($_REQUEST["category"]);
		
		
		$sql = "select name from hardware_categories where m = '$m' and id = '$category'";
	$DB->query($sql,"inventory.php");
		$row = $DB->get(); 
		$cat = $row["name"];
		
		
		
	$sql = "select count(*) as number from hardware where m = '$m' and category = '$category'";
	$DB->query($sql,"inventory.php");
		$row = $DB->get(); 
		$number = $row["number"];
		

		$sql = "select * from hardware_locations where m = '$m' ";
	$DB->query($sql,"inventory.php");
		
	 while($row = $DB->get())
	 {
	 $id = $row["id"];
	 $locations[$id] = $row["name"];	
	 	
	}
		
		
		
		
		

		
		echo "<a href=\"inventory.php?m=$m\">$phrase[404]</a>";
		
		if ($access->thispage > 1)
{
	
	echo " | <a href=\"inventory.php?m=$m&amp;event=editcat&amp;category=$category\">$phrase[850]</a>";

	
	if ($number == "0") {echo " | <a href=\"inventory.php?m=$m&amp;update=deletecat&amp;category=$category\">$phrase[851]</a>";}
	
}
		
		echo " | <a href=\"inventorycsv.php?m=$m&category=$category\">CSV</a><br><br>
		
<script type=\"text/javascript\">
 function pop_window(url) {
  var popit = window.open(url,'console','status,resizable,scrollbars,width=500,height=300');
 }
 </script>
 <b>$cat</b><br><br>";
		
		
		
		
		
		
				$sql= "select location,name, count(location) as total from hardware, hardware_locations where hardware_locations.m = '$m' and category = '$category' and hardware_locations.id = hardware.location group by location order by name"; 
				//echo $sql;
		$DB->query($sql,"inventory.php");
	
				
		echo "<br><b>Totals by location</b><br>
		<br>
		<Table cellpadding=\"5\" class=\"colourtable\"  style=\"margin-left:auto;margin-right:auto;text-align:left\"><tr><td><b>$phrase[121]</b></td><td><b>$phrase[407]</b></td></tr>";
				while ($row = $DB->get()) {
				$_location = formattext($row["location"]);
				$_name = formattext($row["name"]);
				$_total = formattext($row["total"]);
				
				echo "<tr><td>$_name</td><td>$_total</td></tr>";
				}
				echo "</table><br><br>";
		
//$branch = urlencode($branch);


		if (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "id")
		{
			$orderby = "order by id";
		}
		elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "location")
			{
			$orderby = "order by location";
		}
		else 
		{
			$orderby = "order by id";
		}
		
		$sql = "select * from hardware where m=\"$m\"  and category = \"$category\" $orderby";

		$DB->query($sql,"inventory.php");


if ($access->thispage > 1)
{
echo "<a href=\"inventory.php?m=$m&amp;event=add&amp;category=$category\">$phrase[176]</a><br><br>";}


	echo "<table cellpadding=\"5\" class=\"colourtable\"  style=\"width:80%;margin:0 auto;text-align:left\"><tr><td></td><td><b><a href=\"inventory.php?m=$m&event=catview&category=$category&orderby=id\">$phrase[340]</a></b></td><td><b><a href=\"inventory.php?m=$m&event=catview&category=$category&orderby=location\">$phrase[121]</a></b></td><td><b>$phrase[409]</b></td>";
		if ($access->thispage > 1)
		{
			echo "<td></td><td></td>";
		}
			echo "</tr>";
			
		$counter = 0;
		while ($row = $DB->get()) 
			{
				$counter++;
			$no = $row["no"];
			$id = $row["id"];
			$notes = nl2br($row["notes"]);
			$location = $row["location"];
			
			echo "<tr><td>$counter</td><td>$id</td><td>$locations[$location]</td><td>$notes</td>";
			//<a href=\"javascript:pop_window('inventoryview.php?m=$m&amp;no=$no')\">$phrase[236]</a>
			if ($access->thispage > 1)
		{
			echo "
			<td><a href=\"inventory.php?m=$m&amp;event=duplicate&amp;no=$no&amp;category=$category&amp;view=catview\">$phrase[912]</a></td>
			<td><a href=\"inventory.php?m=$m&amp;event=edit&amp;no=$no&amp;category=$category&amp;view=catview\">$phrase[26]</a></td><td><a href=\"inventory.php?m=$m&amp;event=delete&amp;no=$no&amp;category=$category&view=catview\">$phrase[24]</a></td>";
			
			//
		}
		echo "</tr>";
			}
			
		echo "</table><br><br>";
}
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "branchview")
	
	{
		$branch_id = $DB->escape($_REQUEST["branch_id"]);
		
		
		$sql = "select name from hardware_locations where m = '$m' and id = '$branch_id'";
	$DB->query($sql,"inventory.php");
		$row = $DB->get(); 
		$branch = $row["name"];
		
		
		
	$sql = "select count(*) as number from hardware where m = '$m' and location = '$branch_id'";
	$DB->query($sql,"inventory.php");
		$row = $DB->get(); 
		$number = $row["number"];
		

		$sql = "select * from hardware_categories where m = '$m' ";
	$DB->query($sql,"inventory.php");
		
	 while($row = $DB->get())
	 {
	 $id = $row["id"];
	 $categories[$id] = $row["name"];	
	 	
	}
		
		
		
		
		
	
		
		echo "<a href=\"inventory.php?m=$m\">$phrase[404]</a>";
		
		if ($access->thispage > 1)
{
	
	echo " | <a href=\"inventory.php?m=$m&amp;event=editbranch&amp;branch_id=$branch_id\">$phrase[488]</a>";

	
	if ($number == "0") {echo " | <a href=\"inventory.php?m=$m&amp;update=deletebranch&amp;branch_id=$branch_id\">$phrase[911]</a>";}
	
}
		
		echo " | <a href=\"inventorycsv.php?m=$m&branch_id=$branch_id\">CSV</a><br><br>
		
<script type=\"text/javascript\">
 function pop_window(url) {
  var tagspop = window.open(url,'','status,resizable,scrollbars,width=500,height=300');
if (window.focus) {tagspop.focus()}
 }
 </script>
 <h2>$branch</h2>";
		
		
		
		
				$sql= "select category,name, count(category) as total from hardware, hardware_categories where hardware_categories.m = '$m' and location = '$branch_id' and hardware_categories.id = hardware.category group by category order by name"; 
				//echo $sql;
		$DB->query($sql,"inventory.php");
	
				
		echo "<br><b>$phrase[405]</b><br>
		<br>
		<Table cellpadding=\"5\" class=\"colourtable\"  style=\"margin-left:auto;margin-right:auto;text-align:left\"><tr><td><b>$phrase[406]</b></td><td><b>$phrase[407]</b></td></tr>";
				while ($row = $DB->get()) {
				$_category = formattext($row["category"]);
				$_name = formattext($row["name"]);
				$_total = formattext($row["total"]);
				
				echo "<tr><td>$_name</td><td>$_total</td></tr>";
				}
				echo "</table><br><br>";
					
		
		
			if (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "id")
		{
			$orderby = "order by id";
		}
		elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "category")
			{
			$orderby = "order by category";
		}
		else 
		{
			$orderby = "order by id";
		}
		
		$sql = "select * from hardware where m=\"$m\"  and location = \"$branch_id\" $orderby";

		$DB->query($sql,"inventory.php");
		
		
		
		
$branch = urlencode($branch);


if ($access->thispage > 1)
{
echo "<b><a href=\"inventory.php?m=$m&amp;event=add&amp;branch_id=$branch_id&amp;branch=$branch\">$phrase[176]</a></b><br><br>";}


	echo "<table cellpadding=\"5\" class=\"colourtable\"  style=\"width:80%;margin:0 auto;text-align:left\"><tr><td></td><td><b><a href=\"inventory.php?m=$m&event=branchview&branch_id=$branch_id&orderby=id\">$phrase[340]</a></b></td><td><b><a href=\"inventory.php?m=$m&event=branchview&branch_id=$branch_id&orderby=category\">$phrase[406]</a></b></td><td><b>$phrase[409]</b></td>";
		if ($access->thispage > 1)
		{
			echo "<td></td><td></td><td></td>";
		}
			echo "</tr>";
			
		$counter = 0;
		while ($row = $DB->get()) 
			{
				$counter++;
			$no = $row["no"];
			$id = $row["id"];
			$notes = nl2br($row["notes"]);
			$category = $row["category"];
			
			echo "<tr><td>$counter</td><td>$id</td><td>$categories[$category]</td><td>$notes</td>";
			//<a href=\"javascript:pop_window('inventoryview.php?m=$m&amp;no=$no')\">$phrase[236]</a>
			if ($access->thispage > 1)
		{
			echo "<td><a href=\"inventory.php?m=$m&amp;event=duplicate&amp;no=$no&amp;branch_id=$branch_id&view=branchview\">$phrase[912]</a></td>
			<td><a href=\"inventory.php?m=$m&amp;event=edit&amp;no=$no&amp;branch_id=$branch_id&view=branchview\">$phrase[26]</a></td>
			<td><a href=\"inventory.php?m=$m&amp;event=delete&amp;no=$no&amp;branch_id=$branch_id&view=branchview\">$phrase[24]</a></td>";
			
			//
		}
		echo "</tr>";
			}
			
		echo "</table><br><br>";
}
		else 
		{	
			
				echo "<table   style=\"margin-left:auto;margin-right:auto;margin-bottom:3em;text-align:left\">
				<tr><td valign=\"top\"  style=\"padding-right:3em\">";
		
				$sql = "select * from hardware_locations where m = '$m' order by name ";
				$DB->query($sql,"inventory.php");
		$numrows = $DB->countrows();
	echo "<b>$phrase[410]</b><br>";
		while ($row = $DB->get()) 
			{
			$id = $row["id"];
			$name = trim($row["name"]);
			//$length = strlen($name);
			if ($name == "") {$name = "______";}
			//$linkname = urlencode($name);
			
			echo "<a href=\"inventory.php?m=$m&amp;event=branchview&amp;branch_id=$id\">$name</a><br>";
			}
			
			
			echo "</td><td  style=\"padding-right:3em\"><b>$phrase[884]</b>";
			
			
				$sql = "select * from hardware_categories where m = '$m' order by name ";
				$DB->query($sql,"inventory.php");
		$numrows = $DB->countrows();
	echo "<br>";
		while ($row = $DB->get()) 
			{
			$id = $row["id"];
			$name = $row["name"];
			if ($name == "") {$name = "______";}
			
			echo "<a href=\"inventory.php?m=$m&amp;event=catview&amp;category=$id\">$name</a><br>";
			}
			
			
			
			echo "</td>";

print <<<EOF
<td>

<b>$phrase[282] </b>
<form action="inventory.php" method="post" ><p>
<input type="text" name="keywords" size="40"><input type="hidden" name="m" value="$m"><input type="hidden" name="event" value="search"><br><input type="submit" name="search" value="Search"> 
</p></form>
<br><br>
<a href="inventory.php?m=$m&amp;event=totals">$phrase[411]</a>
EOF;

if ( $access->thispage > 1)
			{ echo "
			<br><br>
			<a href=\"inventory.php?m=$m&amp;event=addlocation\">$phrase[177]</a>
			<br><br>
			<a href=\"inventory.php?m=$m&amp;event=addcategory\">$phrase[845]</a>
			
			<br><br>
			";}

echo "<a href=\"inventorycsv.php?m=$m\">CSV</a></td></tr></table>";

		
		
		}
			
			
			
			
			
			
			
			
			
			
	
		
		
		
	}
		
	
		
		
	echo "</div>";

include ("../includes/footer.php");

?>

