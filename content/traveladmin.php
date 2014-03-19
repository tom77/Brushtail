<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);
$datepicker = "yes";
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
$integers[] = "cat_id";
$integers[] = "location_id";
$integers[] = "status";
$integers[] = "id";




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
	
	if ($access->thispage < 2)
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

		$sql = "select * from modules where m = '$m'";
$DB->query($sql,"travel.php");
$row = $DB->get();
$modname = formattext($row["name"]);

echo "<h1 class=\"red\">$modname</h1>";
		
		

	if ($access->thispage == 3)	
		//start block allowed only to edit users
	{	
		
		
		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "adddistance")
{
 $distance_name = $DB->escape($_REQUEST["distance_name"]);
 $distance_value = $DB->escape($_REQUEST["distance_value"]);
 	
$sql = "insert into travel_distances values(NULL,'$distance_name','$distance_value','$m')";	
//echo $sql;
$DB->query($sql,"traveladmin.php");
	
}		
		


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "introduction")
{
 $introduction = $DB->escape($_REQUEST["introduction"]);

$sql = "delete from travel_options where m = '$m'";	
$DB->query($sql,"traveladmin.php");
 
 
$sql = "insert into travel_options values('$m','$introduction')";	
//echo $sql;
$DB->query($sql,"traveladmin.php");
	
}	

		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "updatedistance")
{
 $distance_name = $DB->escape($_REQUEST["distance_name"]);
 $distance_value = $DB->escape($_REQUEST["distance_value"]);
 	
$sql = "update travel_distances set distance_name = '$distance_name', distance_value = '$distance_value' where distance_id = '$id' and m = '$m'";	
//echo $sql;
$DB->query($sql,"traveladmin.php");
	
}
	


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "deletedistance")
{

 	
$sql = "delete from travel_distance where distance_id = '$id' and m = '$m'";	
//echo $sql;
$DB->query($sql,"traveladmin.php");
	
}
		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "add_location")
{
 $location_name = $DB->escape($_REQUEST["location_name"]);
 	
$sql = "insert into travel_location values(NULL,'$location_name','$m','1')";	
//echo $sql;
$DB->query($sql,"traveladmin.php");
	
}


		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "add_category")
{
 $cat_name = $DB->escape($_REQUEST["cat_name"]);
  $cat_code = $DB->escape($_REQUEST["cat_code"]);
  $colour = $DB->escape($_REQUEST["colour"]);
   $restricted = $DB->escape($_REQUEST["restricted"]);
 	
$sql = "insert into travel_category values(NULL,'$cat_name','$m','1','0','$cat_code','$colour','$restricted')";	
//echo $sql;
$DB->query($sql,"traveladmin.php");
	
}


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "delete_cat")
{
 $cat_id = $DB->escape($_REQUEST["cat_id"]);
 	
$sql = "delete from travel_category where cat_id = '$cat_id'";	
$DB->query($sql,"traveladmin.php");
	
}	
		

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "delete_location")
{
 $location_id = $DB->escape($_REQUEST["location_id"]);
 	
$sql = "delete from travel_location where location_id = '$location_id'";

$DB->query($sql,"traveladmin.php");
	
}		
	
if (isset($_REQUEST["reordercat"]))
	{
	//reorders pages
	$reorder = explode(",",$_REQUEST["reordercat"]);
	
	foreach ($reorder as $index => $value)
		{
		$index = $DB->escape($index);	
		$value = $DB->escape($value);
		
		$sql = "update travel_category set position = \"$index\" WHERE cat_id = \"$value\"";	
		//echo $sql;
		$DB->query($sql,"traveladmin.php");
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
		
		$sql = "update travel_location set position = \"$index\" WHERE location_id = \"$value\"";	
		//echo $sql;
		$DB->query($sql,"traveladmin.php");
		}
			
	}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "update_location")
{
 $location_id = $DB->escape($_REQUEST["location_id"]);
 $location_name = $DB->escape($_REQUEST["location_name"]);
 	
$sql = "update travel_location set  location_name = '$location_name' where location_id = '$location_id'";	

$DB->query($sql,"traveladmin.php");

$sql = "delete from travel_members where location_id = '$location_id'";
$DB->query($sql,"traveladmin.php");


$member = array();

if (isset($_REQUEST["member"]))  {$member = $_REQUEST["member"];}
if (isset($member))
{
foreach ($member as $index => $value)
		{
			
		if ($value == "on")
		{

		$_userid = $DB->escape($index);
		$sql = "insert into travel_members values('$location_id','$_userid')";

		$DB->query($sql,"traveladmin.php");	
		}	
		}
}



$sql = "delete from travel_alertees where location_id = '$location_id'";
$DB->query($sql,"traveladmin.php");

//print_r($_REQUEST);

$alertees = array();

if (isset($_REQUEST["alertees"]))  { $alertees = $_REQUEST["alertees"]; }
if (isset($alertees))
{
foreach ($alertees as $index => $value)
		{
			
		if ($value == "on")
		{

		$_userid = $DB->escape($index);
		$sql = "insert into travel_alertees values('$location_id','$_userid')";
		//echo $sql;
		$DB->query($sql,"traveladmin.php");	
		}	
		}
}

$sql = "delete from travel_managers where location_id = '$location_id'";
$DB->query($sql,"traveladmin.php");

$manager = $_REQUEST["manager"];
if (isset($manager))
{


foreach ($manager as $index => $value)
		{
		
		if ($value > 0 )
		{
	
		$_userid = $DB->escape($index);
		$_authority = $DB->escape($value);
		$sql = "insert into travel_managers values('$location_id','$_userid','$_authority')";
		//echo $sql;
		$DB->query($sql,"traveladmin.php");	
		}	
		}
}

	
}	

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "update_cat")
{
 $cat_id = $DB->escape($_REQUEST["cat_id"]);
 $cat_name = $DB->escape($_REQUEST["cat_name"]);
 $cat_code = $DB->escape($_REQUEST["cat_code"]);
   $colour = $DB->escape($_REQUEST["colour"]);
   $restricted = $DB->escape($_REQUEST["restricted"]);

$sql = "update travel_category set status = '$status' , cat_name = '$cat_name', cat_code = '$cat_code', colour = '$colour', restricted = '$restricted' where cat_id = '$cat_id'";	
$DB->query($sql,"traveladmin.php");
	
}	


	



	
	


if(isset($_REQUEST["update"]) && $_REQUEST["update"] == "import")
{
	
	//$lines = @file($_FILES['upload']['tmp_name']);
	
 $sql = "SELECT userid, username from user";
		$DB->query($sql,"pdedit.php");
		while ($row = $DB->get()) 
					{
					$username = $row["username"];
					$userid = $row["userid"];
					$users[$username] = $userid;
					}

//print_r($users);
	
	$rows = 0;
	
	//$data = file($_FILES['upload']['tmp_name'], "r");
	$file = $_FILES['upload']['tmp_name'];
	
	//$row = 1;
$handle = fopen($file, "r");


echo "<div style=\"text-align:center\">";


while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    //$num = count($data);
   
    
        $title = $DB->escape($data[0]);
 		$description = $DB->escape($data[1]);
 		$date = $DB->escape($data[2]);
		 if (strtolower($data[3]) == "yes") {$external = 1;} else {$external = 0;}
		$hours = $DB->escape($data[4]);
		$cost = $DB->escape($data[5]);
		 if (strtolower($data[6]) == "yes") {$approved = 1;} else {$approved = 0;}
		if (strtolower($data[7]) == "yes") {$attended = 1;} else {$attended = 0;}
		$csvuser = $data[8];
       $user = $users[$csvuser];
       $replacment = $DB->escape($data[9]);
       
     
       $pattern = '/^[0-9][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]$/';
		preg_match($pattern, $date, $result);

if ($result) {
	   	$sql = "insert into pd_sessions values
		(NULL,'$title','$description','$date','$external','$hours','$cost','$approved','$attended','$user','','$replacement','1')";
	

		} else {echo "Failed row insert. Invalid date format. Should be yyy-mm-dd<br>";}
        
    echo "</div>";
       //	echo $sql;
		
		$DB->query($sql,"traveladmin.php");
		$rows++;
}
fclose($handle);

echo "<h4 style=\"text-align:center\">Import processed $rows rows!</h4>";
	 
	


}

	} //end block allowed only to edit users

if ($access->thispage == 3)
{
	
	//<li style=\"display:inline\"><a href=\"traveladmin.php?m=$m&event=options\">Options</a></li>
	echo "<ul style=\"list-style:none;margin:1em 0\">
	
	<li style=\"display:inline;padding-right:1em\"><a href=\"traveladmin.php?m=$m&event=loc\">Groups</a></li>
	<li style=\"display:inline;padding-right:1em\"><a href=\"traveladmin.php?m=$m&event=cat\">Categories</a></li> 
	<li style=\"display:inline;padding-right:1em\"><a href=\"traveladmin.php?m=$m&event=distances\">Distances</a></li> 
	<li style=\"display:inline;padding-right:1em\"><a href=\"traveladmin.php?m=$m&event=introduction\">Introduction</a></li> 

	
	</ul>";
}


if (isset($_REQUEST["event"])  && $_REQUEST["event"] == "distances" && $access->thispage == 3)
{
	
	
	 $sql  = "select * from travel_distances where m = '$m' order by distance_name";	
	$DB->query($sql,"traveladmin.php");
	
	echo "<h2>Distances</h2><a href=\"traveladmin.php?m=$m&event=addjourney\"><img src=\"../images/add.png\" title=\"$phrase[176]\" alt=\"$phrase[176]\"></a><br><br><table class=\"colourtable\">
	<tr><td><b>$phrase[1075]</b></td><td><b>$phrase[1074]</b> </td><td></td><td></td></tr>";
	while ($row = $DB->get())
	{
	$distance_id = $row["distance_id"];
	$distance_name = $row["distance_name"];
	$distance_value = $row["distance_value"];

	echo "<tr><td>$distance_name</td><td>$distance_value</td><td><a href=\"traveladmin.php?m=$m&event=editjourney&id=$distance_id\">$phrase[26]</a></td>
	<td><a href=\"traveladmin.php?m=$m&event=deletejourney&id=$distance_id\">$phrase[24]</a></td></tr>";


	}	

	echo "</table>";
	
}

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "deletejourney" && $access->thispage == 3)
{
	

	 $sql  = "select * from travel_distances where m = '$m' and distance_id = '$id'";	
	$DB->query($sql,"traveladmin.php");
	
	
$row = $DB->get();
$distance_name = $row["distance_name"];
echo "<h2>Distances</h2>
	<h3>$phrase[14]</h3>
	
	<a href=\"traveladmin.php?m=$m&id=$id&update=deletedistance&event=distances\">$phrase[12]</a> | <a href=\"traveladmin.php?m=$m&event=distances\">$phrase[13]</a>";

}

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "editjourney" && $access->thispage == 3)
{
	

	 $sql  = "select * from travel_distances where m = '$m' and distance_id = '$id'";	
	$DB->query($sql,"traveladmin.php");
	
	echo "<h2>Distances</h2>
	<form action=\"traveladmin.php\" method=\"post\">";
$row = $DB->get();
	

	$distance_name = $row["distance_name"];
	$distance_value = $row["distance_value"];
	
	echo "<b>$phrase[1075]</b><br>
	<input type=\"text\" name=\"distance_name\" value=\"$distance_name\" size=\"80\"><br><br>
	<b>$phrase[1074]</b><br>
	<input type=\"text\" name=\"distance_value\" value=\"$distance_value\"><br><br>
	<input type=\"submit\" name=\"submit\" value=\"$phrase[16]\">
	<input type=\"hidden\" name=\"id\" value=\"$id\">
	<input type=\"hidden\" name=\"update\" value=\"updatedistance\">
	<input type=\"hidden\" name=\"event\" value=\"distance\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	</form>";
	
	
}
elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "addjourney" && $access->thispage == 3)
{
	


	
	echo "<h2>Distances</h2>
	<form action=\"traveladmin.php\" method=\"post\">";
$row = $DB->get();
	

	$distance_name = $row["distance_name"];
	$distance_value = $row["distance_value"];
	
	echo "<b>$phrase[1075]</b><br>
	<input type=\"text\" name=\"distance_name\"  size=\"80\"><br><br>
	<b>$phrase[1074]</b><br>
	<input type=\"text\" name=\"distance_value\" ><br><br>
	<input type=\"submit\" name=\"submit\" value=\"$phrase[176]\">

	<input type=\"hidden\" name=\"update\" value=\"adddistance\">
	<input type=\"hidden\" name=\"event\" value=\"distances\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	</form>";
	
	
}	
elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edit_location" && $access->thispage == 3)
{
	
	$sql = "select location_name from travel_location where location_id = '$location_id'";
	
	$DB->query($sql,"traveladmin.php");
	$row = $DB->get();
	$location_name = formattext($row["location_name"]);

	echo "<form action=\"traveladmin.php\" method=\"post\">
	
	<b>Group name</b><br>
	<input type=\"text\" name=\"location_name\" value=\"$location_name\"><br><br>

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"location_id\" value=\"$location_id\">
	<input type=\"hidden\" name=\"update\" value=\"update_location\">
	<input type=\"hidden\" name=\"event\" value=\"loc\">

	
	
	<table class=\"colourtable\" >";
	
	$members = array();
	$sql = "select userid from travel_members where location_id = '$location_id'";
	$DB->query($sql,"traveladmin.php");
	while ($row = $DB->get())
		{
		$members[] = $row["userid"];
		}
		
		
	$users = array();
	$sql = "select first_name, last_name, userid from user order by last_name
";
	//$sql = "select first_name, last_name, userid from user where username != 'guest' and userid != '1' ";
	$DB->query($sql,"traveladmin.php");
	while ($row = $DB->get())
		{
		$users[] = $row["userid"];
		$users_fname[] = $row["first_name"];
		$users_lname[] = $row["last_name"];
		}	

		
	$managers = array();
	$sql = "select userid,authority from travel_managers where location_id = '$location_id'";

	$DB->query($sql,"traveladmin.php");
	while ($row = $DB->get())
		{
		$uid = $row["userid"];
		$managers[$uid] = $row["authority"];
		}
		
	$alertees = array();
	$sql = "select userid from travel_alertees where location_id = '$location_id'";

	$DB->query($sql,"traveladmin.php");
	while ($row = $DB->get())
		{
		$alertees[] = $row["userid"];
		//$manager_alert[] = $row["alert"];
		}
		
	echo "<tr style=\"font-weight:bold\"><td>Name</td><td>Member</td><td>Manager</td><td>Alertee</td></tr>";
		
		
	foreach ($users as $index => $userid)
		{
		
		echo "<tr><td>$users_lname[$index], $users_fname[$index]</td><td";
		if (in_array($userid,$members)) {echo " class=\"accent\"";}	
		echo "><input type=\"checkbox\" name=\"member[$userid]\"";
		if (in_array($userid,$members)) {echo " checked";}	
		
		echo "></td><td";
		
		if (array_key_exists ($userid,$managers) && $managers[$userid] == 1) {echo " class=\"yesoption\"";} 
		if (array_key_exists ($userid,$managers) && $managers[$userid] == 2) {echo " class=\"nooption\"";} 
		if (array_key_exists ($userid,$managers) && $managers[$userid] == 3) {echo " style=\"background:#FF593F\"";} 
		
		echo "><select name=\"manager[$userid]\"><option value=\"0\" >No</option>";
		echo "<option value=\"1\" "; if (array_key_exists ($userid,$managers) && $managers[$userid] == 1) {echo " selected";} echo ">Read only</option>";
		echo "<option value=\"2\" "; if (array_key_exists ($userid,$managers) && $managers[$userid] == 2) {echo " selected";} echo ">General</option>";
		echo "<option value=\"3\" "; if (array_key_exists ($userid,$managers) && $managers[$userid] == 3) {echo " selected";} echo ">Unrestricted</option>";
		
		echo "</select></td><td";
		if (in_array($userid,$alertees)) {echo " class=\"accent\"";}	
		echo "><input type=\"checkbox\" name=\"alertees[$userid]\"";
		if (isset($alertees)){
		if (in_array($userid,$alertees)) {echo " checked";}
		}
		echo "></td></tr>";
			
		}
		
		echo "</table>	<br><input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
	
	
	</form>";
	
}


elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "introduction" && $access->thispage == 3)
{
	
	$sql = "select * from travel_options where m = '$m'";

	$DB->query($sql,"traveladmin.php");
	$row = $DB->get();
	$introduction = $row["introduction"];
	
	 echo "<h2>$phrase[556]</h2>
	<form action=\"traveladmin.php\">
	<textarea name=\"introduction\" cols=\"60\" rows=\"10\">$introduction</textarea>
	<input type=\"hidden\" name=\"m\" value=\"$m\"><br><br>
	<input type=\"hidden\" name=\"update\" value=\"introduction\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
	</form>
	";
	
	
}


elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edit_cat" && $access->thispage == 3)
{
	
	$sql = "select cat_name,cat_code,colour,restricted, status from travel_category where cat_id = '$cat_id'";

	$DB->query($sql,"traveladmin.php");
	$row = $DB->get();
	$cat_name = formattext($row["cat_name"]);
	$status = $row["status"]; 
	$cat_code = $row["cat_code"];
	$colour = $row["colour"];
	$restricted = $row["restricted"];
	
	if ($colour == "" || $colour == "NULL") {$colour = "000000";}
	
	echo "<form action=\"traveladmin.php\" method=\"post\">
	<b>Group name</b><br>
	<input type=\"text\" name=\"cat_name\" value=\"$cat_name\"><br><br>
	<b>Category code</b><br>
	<input type=\"text\" name=\"cat_code\" size=\"3\" value=\"$cat_code\"><br><br>
	<b>$phrase[254]</b><br>
	<select name=\"status\">
	<option value=\"1\">$phrase[13]</option>
	<option value=\"0\"";
	if ($status == "0") {echo " selected";}
	echo ">$phrase[12]</option>
	</select><br><br>
	
	
	<b>Restricted</b><br>
	<select name=\"restricted\">";
	

	if ($restricted == "1") {echo "<option value=\"1\">$phrase[12]</option><option value=\"0\">$phrase[13]</option>";}
	else {echo "<option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option>";}
	echo ">$phrase[12]</option>
	</select><br><br>
	<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
<b>Colour</b><br><input type=\"text\" name=\"colour\" class=\"color\" value=\"$colour\"><br><br>
	
	
	
	
	
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">
	<input type=\"hidden\" name=\"update\" value=\"update_cat\">
	<input type=\"hidden\" name=\"event\" value=\"cat\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
	</form>";
	
}






elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "cat" && $access->thispage == 3)

{
		
		
		
		
		echo "<h2>$phrase[884]</h2> <table  class=\"colourtable\">";
		
			$sql = "select count(travel_type) as count,status, position, cat_name,cat_code,colour,restricted, cat_id from travel_category left join travel_requests on travel_requests.travel_type = travel_category.cat_id where travel_category.m = '$m' group by cat_id order by position";
	//echo $sql;
		$DB->query($sql,"traveladmin.php");
		$num = $DB->countrows();
		$counter = 0;
		while ($row = $DB->get())
		{
		$counter++;
		$cname[$counter] = $row["cat_name"];
		$ccode[$counter] = $row["cat_code"];
		$ccolour[$counter] = $row["colour"];
		$crestricted[$counter] = $row["restricted"];
		$_cat_id[$counter] = $row["cat_id"];	
		$count[$counter] = $row["count"];
		$position[$counter] = $row["cat_id"];		
		$status[$counter] = $row["status"];
		}
		
		if (isset($_cat_id))
		{
		foreach ($_cat_id as $counter => $catid)
		{
			echo "<tr";
			if ($status[$counter] == "0") {echo " style=\"background:#e6e8ea\"";}
			echo "><td ";
			echo "style=\"color:#$ccolour[$counter]\"";
			echo ">$cname[$counter]</td><td>$ccode[$counter]</td><td>";
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
							
				echo "<a href=\"traveladmin.php?event=cat&reordercat=$up&m=$m\"><img src=\"../images/up.png\" title=\"$phrase[77]\"></a>";
				
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
							echo "<a href=\"traveladmin.php?event=cat&reordercat=$down&m=$m\"><img src=\"../images/down.png\" title=\"$phrase[78]\"></a>";
			}
			
			
			echo "	</td>	<td>";  
			
			if ($crestricted[$counter] == 1) {echo "Restricted";} else {echo "General";} 
			echo "</td>	<td><a href=\"traveladmin.php?m=$m&amp;cat_id=$catid&amp;event=edit_cat\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
			<td style=\"width:1.6em\">";
			if ($count[$counter] == 0) {
			echo "<a href=\"traveladmin.php?m=$m&amp;cat_id=$catid&amp;update=delete_cat&event=cat\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>";
			}
			echo "</td></tr>";
		}
}
		
		
		echo "</table>
				<form action=\"traveladmin.php\" method=\"post\">
				<h2>Add travel category</h2>
<br><b>Category Name</b><br>
<input type=\"text\" name=\"cat_name\" size=\"60\"> <br>
<b>Category Code</b> <br>
<input type=\"text\" name=\"cat_code\" size=\"3\"> <br>
<b>Restricted</b><br>
	<select name=\"restricted\">
	<option value=\"0\">$phrase[13]</option>
	<option value=\"1\">$phrase[12]</option>
	
	</select><br>
	<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
<b>Colour</b><br><input type=\"text\" name=\"colour\" class=\"color\" value=\"#000000\"><br><br>
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"update\" value=\"add_category\">
<input type=\"hidden\" name=\"event\" value=\"cat\">
<input type=\"submit\" value=\"$phrase[176]\">
</form>
		
	";

	}
	
	else

{	
		
	
	
	
		
	
	
	echo "<h2>Groups</h2>
	<table class=\"colourtable\">";
		
	
		
		$sql = "SELECT location_name,
position, location_name, travel_location.location_id as location_id, count( userid ) AS count
FROM travel_location
LEFT JOIN travel_members ON travel_location.location_id = travel_members.location_id
where travel_location.m = '$m'
GROUP BY travel_location.location_id order by location_name";
				
	//echo $sql;			

		$DB->query($sql,"traveladmin.php");
		$num = $DB->countrows();
		$counter = 0;
		
		while ($row = $DB->get())
		{
			$counter++;
		$loc_name[$counter] = $row["location_name"];
		
		$loc_id[$counter] = $row["location_id"];
		$position[$counter] = $row["location_id"];	
		$count[$counter] = $row["count"];		
		
		
	
		}
	
		if (isset($loc_id))
		{
		foreach ($loc_id as $counter => $locationid)
		{
		
			echo "<tr><td>$loc_name[$counter]</td>";
		/*	
		echo "<td>";
		
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
							
				echo "<a href=\"traveladmin.php?reorderloc=$up&m=$m\"><img src=\"../images/up.png\" title=\"$phrase[77]\"></a>";
				 
			}
			echo "</td><td> ";
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
							echo "<a href=\"traveladmin.php?reorderloc=$down&m=$m\"><img src=\"../images/down.png\" title=\"$phrase[78]\"></a>";
			}
			*/
			echo "<td><a href=\"traveladmin.php?m=$m&amp;location_id=$locationid&amp;event=edit_location\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
			<td style=\"width:1.6em\">";
			if ($count[$counter] == 0) {
			echo "<a href=\"traveladmin.php?m=$m&amp;location_id=$locationid&amp;update=delete_location&event=loc\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>";
			}
			
			echo "</td></tr>";
		}
		}
		

		
		echo "</table>
		
		<form action=\"traveladmin.php\" method=\"post\">
<br><input type=\"text\" name=\"location_name\"> 
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"event\" value=\"loc\">
<input type=\"hidden\" name=\"update\" value=\"add_location\">

<input type=\"submit\" value=\"$phrase[176]\">
</form>";
		
}
	
	
	
}
	

	


	
	
	
	
	
		

		
	



echo "</div></div>";
		
		
	include("../includes/rightsidebar.php");

include ("../includes/footer.php");

?>

