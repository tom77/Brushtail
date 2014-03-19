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
$DB->query($sql,"leave.php");
$row = $DB->get();
$modname = formattext($row["name"]);

echo "<h1 class=\"red\">$modname</h1>";
		
		

	if ($access->thispage == 3)	
		//start block allowed only to edit users
	{	
	
		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "add_location")
{
 $location_name = $DB->escape($_REQUEST["location_name"]);
 	
$sql = "insert into leave_location values(NULL,'$location_name','$m','1')";	
//echo $sql;
$DB->query($sql,"leaveadmin.php");
	
}


		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "add_category")
{
 $cat_name = $DB->escape($_REQUEST["cat_name"]);
  $cat_code = $DB->escape($_REQUEST["cat_code"]);
  $colour = $DB->escape($_REQUEST["colour"]);
   $restricted = $DB->escape($_REQUEST["restricted"]);
 	
$sql = "insert into leave_category values(NULL,'$cat_name','$m','1','0','$cat_code','$colour','$restricted')";	
$DB->query($sql,"leaveadmin.php");
	
}


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "delete_cat")
{
 $cat_id = $DB->escape($_REQUEST["cat_id"]);
 	
$sql = "delete from leave_category where cat_id = '$cat_id'";	
$DB->query($sql,"leaveadmin.php");
	
}	
		

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "delete_location")
{
 $location_id = $DB->escape($_REQUEST["location_id"]);
 	
$sql = "delete from leave_location where location_id = '$location_id'";

$DB->query($sql,"clickeradmin.php");
	
}		
	
if (isset($_REQUEST["reordercat"]))
	{
	//reorders pages
	$reorder = explode(",",$_REQUEST["reordercat"]);
	
	foreach ($reorder as $index => $value)
		{
		$index = $DB->escape($index);	
		$value = $DB->escape($value);
		
		$sql = "update leave_category set position = \"$index\" WHERE cat_id = \"$value\"";	
		//echo $sql;
		$DB->query($sql,"leaveadmin.php");
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
		
		$sql = "update leave_location set position = \"$index\" WHERE location_id = \"$value\"";	
		//echo $sql;
		$DB->query($sql,"leaveadmin.php");
		}
			
	}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "update_location")
{
 $location_id = $DB->escape($_REQUEST["location_id"]);
 $location_name = $DB->escape($_REQUEST["location_name"]);
 	
$sql = "update leave_location set  location_name = '$location_name' where location_id = '$location_id'";	

$DB->query($sql,"leaveadmin.php");

$sql = "delete from leave_members where location_id = '$location_id'";
$DB->query($sql,"leaveadmin.php");



if (isset($_REQUEST["member"]))
{
$member = $_REQUEST["member"];
if (isset($member))
{
foreach ($member as $index => $value)
		{
			
		if ($value == "on")
		{

		$_userid = $DB->escape($index);
		$sql = "insert into leave_members values('$location_id','$_userid')";

		$DB->query($sql,"leaveadmin.php");	
		}	
		}
}
}


$sql = "delete from leave_alertees where location_id = '$location_id'";
$DB->query($sql,"leaveadmin.php");

//print_r($_REQUEST);

if (isset($_REQUEST["alertees"]))
{
$alertees = $_REQUEST["alertees"];
if (isset($alertees))
{
foreach ($alertees as $index => $value)
		{
			
		if ($value == "on")
		{

		$_userid = $DB->escape($index);
		$sql = "insert into leave_alertees values('$location_id','$_userid')";
		//echo $sql;
		$DB->query($sql,"leaveadmin.php");	
		}	
		}
}
}

$sql = "delete from leave_managers where location_id = '$location_id'";
$DB->query($sql,"leaveadmin.php");

$manager = $_REQUEST["manager"];
if (isset($manager))
{


foreach ($manager as $index => $value)
		{
		
		if ($value > 0 )
		{
	
		$_userid = $DB->escape($index);
		$_authority = $DB->escape($value);
		$sql = "insert into leave_managers values('$location_id','$_userid','$_authority')";
		//echo $sql;
		$DB->query($sql,"leaveadmin.php");	
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

$sql = "update leave_category set status = '$status' , cat_name = '$cat_name', cat_code = '$cat_code', colour = '$colour', restricted = '$restricted' where cat_id = '$cat_id'";	
$DB->query($sql,"leaveadmin.php");
	
}	


	

		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delcycle"  )
   {
    
    	
    

 
	$id = $DB->escape($_REQUEST["id"]);
	$sql = "delete from leave_periods where id = '$id'";
	//echo $sql;
	$DB->query($sql,"leaveadmin.php");
   }
	
   	

		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addcycle"  )
   {
    
    	$name = $DB->escape($_POST["name"]);
	$colour = $DB->escape($_POST["colour"]);
	$pdays = $DB->escape($_POST["pdays"]);
	$pdate = $_POST["pdate"];
	
	$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
		
		
		if (preg_match ($pattern ,$pdate) )
{

	if ($DATEFORMAT == "%d-%m-%Y")
             {
             $_d = substr($pdate,0,2);
             $_m = substr($pdate,3,2);
             $_y = substr($pdate,6,4);
             }
     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $_d = substr($pdate,3,2);
         $_m = substr($pdate,0,2);
         $_y = substr($pdate,6,4);
             }
	$pdate = "$_y-$_m-$_d";
	
	 $sql = "INSERT INTO leave_periods VALUES(NULL,'$m','$name','$colour','$pdate','$pdays')"; 
//echo $sql;
	$DB->query($sql,"leaveadmin.php");	
}
	}	
	
	
			if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updatecycle"  )
   {
    
    $name = $DB->escape($_POST["name"]);
	$colour = $DB->escape($_POST["colour"]);
	$pdays = $DB->escape($_POST["pdays"]);
	$pdate = $_POST["pdate"];
	
	$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
		
		
		if (preg_match ($pattern ,$pdate) )
{

	if ($DATEFORMAT == "%d-%m-%Y")
             {
             $_d = substr($pdate,0,2);
             $_m = substr($pdate,3,2);
             $_y = substr($pdate,6,4);
             }
     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $_d = substr($pdate,3,2);
         $_m = substr($pdate,0,2);
         $_y = substr($pdate,6,4);
             }
	$pdate = "$_y-$_m-$_d";
	
	 $sql = "update leave_periods set name = '$name',colour = '$colour',pdate = '$pdate',pdays = '$pdays' where id = '$id' and m = '$m'"; 
//echo $sql;
	$DB->query($sql,"leaveadmin.php");	
}
	}	
	
	
		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "options"  )
   {
    
    	 $sql = "delete from leave_options where m = '$m'"; 
			$DB->query($sql,"helpdeskedit.php");
    

 
	$mode = $DB->escape($_POST["mode"]);
	$break = $DB->escape($_POST["break"]);
	$introduction = $DB->escape($_POST["introduction"]);
	$comment_label = $DB->escape($_POST["comment_label"]);
        $summary = $DB->escape($_POST["summary"]);
	//$label = $DB->escape($_POST["label"]);
	//$label_cancelled = $DB->escape($_POST["label_cancelled"]);
	//$label_submitted = $DB->escape($_POST["label_submitted"]);
	//$label_updated = $DB->escape($_POST["label_updated"]);
	//$label_myleave = $DB->escape($_POST["label_myleave"]);
	
	
	 $sql = "INSERT INTO leave_options VALUES('$m','$mode','$break','$introduction','$comment_label','$summary')"; 
//echo $sql;
	$DB->query($sql,"leaveadmin.php");	
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
		
		$DB->query($sql,"leaveadmin.php");
		$rows++;
}
fclose($handle);

echo "<h4 style=\"text-align:center\">Import processed $rows rows!</h4>";
	 
	


}

	} //end block allowed only to edit users

if ($access->thispage == 3)
{
	
	//<li style=\"display:inline\"><a href=\"leaveadmin.php?m=$m&event=options\">Options</a></li>
	echo "<ul style=\"list-style:none;margin:1em 0\">
	
	<li style=\"display:inline;padding-right:1em\"><a href=\"leaveadmin.php?m=$m&event=loc\">Groups</a></li>
	<li style=\"display:inline;padding-right:1em\"><a href=\"leaveadmin.php?m=$m&event=cat\">Categories</a></li> 
	<li style=\"display:inline;padding-right:1em\"><a href=\"leaveadmin.php?m=$m&event=options\">Options</a></li>
	<li style=\"display:inline\"><a href=\"leaveadmin.php?m=$m&event=cycles\">cycles</a></li>
	
	</ul>";
}

if (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edit_location" && $access->thispage == 3)
{
	
	$sql = "select location_name from leave_location where location_id = '$location_id'";
	
	$DB->query($sql,"leaveadmin.php");
	$row = $DB->get();
	$location_name = formattext($row["location_name"]);

	echo "<form action=\"leaveadmin.php\" method=\"post\">
	
	<b>Group name</b><br>
	<input type=\"text\" name=\"location_name\" value=\"$location_name\"><br><br>

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"location_id\" value=\"$location_id\">
	<input type=\"hidden\" name=\"update\" value=\"update_location\">
	<input type=\"hidden\" name=\"event\" value=\"loc\">

	
	
	<table class=\"colourtable\" >";
	
	$members = array();
	$sql = "select userid from leave_members where location_id = '$location_id'";
	$DB->query($sql,"leaveadmin.php");
	while ($row = $DB->get())
		{
		$members[] = $row["userid"];
		}
		
		
	$users = array();
	//$sql = "select first_name, last_name, userid from user where disabled = 0 order by last_name";
	$sql = "select first_name, last_name, userid from user where username != 'guest' and userid != '1' order by last_name";
	$DB->query($sql,"leaveadmin.php");
	while ($row = $DB->get())
		{
		$users[] = $row["userid"];
		$users_fname[] = $row["first_name"];
		$users_lname[] = $row["last_name"];
		}	

		
	$managers = array();
	$sql = "select userid,authority from leave_managers where location_id = '$location_id'";

	$DB->query($sql,"leaveadmin.php");
	while ($row = $DB->get())
		{
		$uid = $row["userid"];
		$managers[$uid] = $row["authority"];
		}
		
	$alertees = array();
	$sql = "select userid from leave_alertees where location_id = '$location_id'";

	$DB->query($sql,"leaveadmin.php");
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
                if (array_key_exists ($userid,$managers) && $managers[$userid] == 4) {echo " style=\"background:#FF593F\"";} 
		
		echo "><select name=\"manager[$userid]\"><option value=\"0\" >No</option>";
		echo "<option value=\"1\" "; if (array_key_exists ($userid,$managers) && $managers[$userid] == 1) {echo " selected";} echo ">Read only</option>";
		echo "<option value=\"2\" "; if (array_key_exists ($userid,$managers) && $managers[$userid] == 2) {echo " selected";} echo ">General</option>";
		echo "<option value=\"3\" "; if (array_key_exists ($userid,$managers) && $managers[$userid] == 3) {echo " selected";} echo ">Approve any leave</option>";
                echo "<option value=\"4\" "; if (array_key_exists ($userid,$managers) && $managers[$userid] == 4) {echo " selected";} echo ">Approve or add any leave</option>";
		
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

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edit_cat" && $access->thispage == 3)
{
	
	$sql = "select cat_name,cat_code,colour,restricted, status from leave_category where cat_id = '$cat_id'";

	$DB->query($sql,"clickeradmin.php");
	$row = $DB->get();
	$cat_name = formattext($row["cat_name"]);
	$status = $row["status"]; 
	$cat_code = $row["cat_code"];
	$colour = $row["colour"];
	$restricted = $row["restricted"];
	
	if ($colour == "" || $colour == "NULL") {$colour = "000000";}
	
	echo "<form action=\"leaveadmin.php\" method=\"post\">
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

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "editcycle" && $access->thispage == 3)
		

{
	
	
		 $sql  = "select id, name,colour,pdays, pdate from leave_periods where id = '$id' and m = '$m'";	
	$DB->query($sql,"leaveadmin.php");
//echo $sql;
	$row = $DB->get();
	
	$id = $row["id"];
	$name = $row["name"];
	$colour = $row["colour"];
	$pdays = $row["pdays"];
	$pdate = $row["pdate"];
	
	$_y = substr($pdate,0,4);
	$_m = substr($pdate,5,2);
	$_d = substr($pdate,8,4);
		
	if ($DATEFORMAT == "%d-%m-%Y")
             {
		
			 $pdate = $_d . "-"  . $_m . "-" . $_y;
             }
    if ($DATEFORMAT == "%m-%d-%Y")
             {
			
			 $pdate = $_m . "-"  . $_d . "-" . $_y;
             }
	
	
	
	echo "<h3>Edit cycle</h3>
		<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
			<form action=\"leaveadmin.php\" method=\"post\">
<br><b>Name</b><br>
<input type=\"text\" name=\"name\" value=\"$name\"> 
<br><b>Colour</b><br>
<input type=\"text\" name=\"colour\" class=\"color\" value=\"#$colour\"> 
<br><b>Commencement date</b><br>
<input type=\"text\" id=\"pdate\" name=\"pdate\" size=\"10\" value=\"$pdate\"> 
<br><b>Duration in days</b><br>
<input type=\"text\" id=\"pdays\" name=\"pdays\" value=\"$pdays\"> 
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"id\" value=\"$id\">
<input type=\"hidden\" name=\"event\" value=\"cycles\">
<input type=\"hidden\" name=\"update\" value=\"updatecycle\">
<br>
<input type=\"submit\" value=\"$phrase[16]\">
</form>

<script type=\"text/javascript\">
			
			
			function datepicker(id){
			

		
		new JsDatePick({
			useMode:2,
			target:id,
			dateFormat:\"$DATEFORMAT\",
			yearsRange:[2010,2020],
			limitToToday:false,
			isStripped:false,

			imgPath:\"img/\",
			weekStartDay:1
		});
	};
	

	datepicker('pdate')

	</script>

	";
	
}


elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "cycles" && $access->thispage == 3)
		

{

	 $sql  = "select id, name,colour,pdays, pdate from leave_periods where m = '$m'";	
	$DB->query($sql,"leaveadmin.php");
	//echo $sql;
	
	echo "<br><br>
	<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>

	<h2>cycles</h2>
	<table style=\"text-align:left\" cellpadding=\"5\" class=\"colourtable\">
	<tr><td>Name</td><td >Colour</td><td>Commencement of cycle</td><td>Duration of cycle in days</td><td></td><td></td></tr>";
	while ($row = $DB->get())
	{
		$id = $row["id"];
	$name = $row["name"];
	$colour = $row["colour"];
	$pdays = $row["pdays"];
	$pdate = $row["pdate"];
	
	echo "<tr><td>$name</td><td style=\"background:#$colour\">$colour</td><td>$pdate</td><td>$pdays</td>
	<td><a href=\"leaveadmin.php?m=$m&event=editcycle&id=$id\">Edit</a></td>
	<td><a href=\"leaveadmin.php?m=$m&event=cycles&update=delcycle&id=$id\">Delete</a></td></tr>";
	
	}
	
	
	echo "</table>
	
	
		
	
	<h3>Add cycle</h3>
			<form action=\"leaveadmin.php\" method=\"post\">
<br><b>Name</b><br>
<input type=\"text\" name=\"name\"> 
<br><b>Colour</b><br>
<input type=\"text\" name=\"colour\" class=\"color\" value=\"#FFF1AF\"> 
<br><b>Commencement date</b><br>
<input type=\"text\" id=\"pdate\" name=\"pdate\" size=\"10\"> 
<br><b>Duration in days</b><br>
<input type=\"text\" id=\"pdays\" name=\"pdays\"> 
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"event\" value=\"cycles\">
<input type=\"hidden\" name=\"update\" value=\"addcycle\">
<br>
<input type=\"submit\" value=\"$phrase[176]\">
</form>
<script type=\"text/javascript\">
			
			
			function datepicker(id){
			

		
		new JsDatePick({
			useMode:2,
			target:id,
			dateFormat:\"$DATEFORMAT\",
			yearsRange:[2010,2020],
			limitToToday:false,
			isStripped:false,

			imgPath:\"img/\",
			weekStartDay:1
		});
	};
	

	datepicker('pdate')

	</script>

		



";
	

	 
	
	
}

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "options" && $access->thispage == 3)
		

{

	 $sql  = "select * from leave_options where m = '$m'";	
	$DB->query($sql,"editform.php");
	//echo $sql;
	$row = $DB->get();

	$mode = $row["mode"];
	$break = $row["break"];
	$introduction = $row["introduction"];
	$comment_label = $row["comment_label"];
        $summary = $row["summary"];

	
	
	echo "<br><br>
		
	<form action=\"leaveadmin.php\" method=\"post\">
	<h2>Options</h2><table style=\"text-align:left\" cellpadding=\"5\">

	 
	 	 <tr><td align=\"right\">
	<b>$phrase[1061] </b></td><td><select name=\"mode\">";
	if (isset($mode) && $mode == 1) { echo "<option value=\"1\"  selected>$phrase[12] </option><option value=\"0\">$phrase[13]</option>";}
	else {echo "<option value=\"0\"  selected>$phrase[13]</option><option value=\"1\">$phrase[12]</option>";}
	 
	 echo "</select> </td></tr>
	 <tr><td>Allowed break values (separate multiple values with 2 || characters)</td><td><input type=\"text\" name=\"break\" value=\"$break\"></td></tr>
	 
	 <tr><td><b>$phrase[556]</b></td><td><textarea name=\"introduction\" cols=\"50\" rows=\"10\">$introduction</textarea></td></tr>
	  <tr><td><b>$phrase[1068]</b></td><td><input type=\"text\" name=\"comment_label\" value=\"$comment_label\"></td></tr> 
              <tr><td><b>$phrase[1122]</b></td><td><select name=\"summary\">
<option value=\"1\">$phrase[12]</option>     
<option value=\"o\"";
         if ($summary == 0) {echo " selected";}
         echo ">$phrase[13]</option>   
</select></td></tr> 
	 
	 <tr><td align=\"right\">
	</td><td><input type=\"submit\" name=\"submit\" value=\"submit\">
	<input type=\"hidden\" name=\"update\" value=\"options\">  
	<input type=\"hidden\" name=\"m\" value=\"$m\">  </td></tr></table>

	</form>";
	 
}


elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "loc" && $access->thispage == 3)

{	
		
	
	
	
		
	
	
	echo "<h2>Groups</h2>
	<table class=\"colourtable\">";
		
	
		
		$sql = "SELECT location_name,
position, location_name, leave_location.location_id as location_id, count( userid ) AS count
FROM leave_location
LEFT JOIN leave_members ON leave_location.location_id = leave_members.location_id
where leave_location.m = '$m'
GROUP BY leave_location.location_id order by location_name";
				
	//echo $sql;			

		$DB->query($sql,"leaveadmin.php");
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
							
				echo "<a href=\"leaveadmin.php?reorderloc=$up&m=$m\"><img src=\"../images/up.png\" title=\"$phrase[77]\"></a>";
				 
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
							echo "<a href=\"leaveadmin.php?reorderloc=$down&m=$m\"><img src=\"../images/down.png\" title=\"$phrase[78]\"></a>";
			}
			*/
			echo "<td><a href=\"leaveadmin.php?m=$m&amp;location_id=$locationid&amp;event=edit_location\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
			<td style=\"width:1.6em\">";
			if ($count[$counter] == 0) {
			echo "<a href=\"leaveadmin.php?m=$m&amp;location_id=$locationid&amp;update=delete_location&event=loc\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>";
			}
			
			echo "</td></tr>";
		}
		}
		

		
		echo "</table>
		
		<form action=\"leaveadmin.php\" method=\"post\">
<br><input type=\"text\" name=\"location_name\"> 
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"event\" value=\"loc\">
<input type=\"hidden\" name=\"update\" value=\"add_location\">

<input type=\"submit\" value=\"$phrase[176]\">
</form>";
		
}

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "cat" && $access->thispage == 3)

{
		
		
		
		
		echo "<h2>$phrase[884]</h2> <table  class=\"colourtable\">";
		
			$sql = "select count(leave_type) as count,status, position, cat_name,cat_code,colour,restricted, cat_id from leave_category left join leave_requests on leave_requests.leave_type = leave_category.cat_id where leave_category.m = '$m' group by cat_id order by position";
	//echo $sql;
		$DB->query($sql,"leaveadmin.php");
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
							
				echo "<a href=\"leaveadmin.php?event=cat&reordercat=$up&m=$m\"><img src=\"../images/up.png\" title=\"$phrase[77]\"></a>";
				
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
							echo "<a href=\"leaveadmin.php?event=cat&reordercat=$down&m=$m\"><img src=\"../images/down.png\" title=\"$phrase[78]\"></a>";
			}
			
			
			echo "	</td>	<td>";  
			
			if ($crestricted[$counter] == 1) {echo "Restricted";} else {echo "General";} 
			echo "</td>	<td><a href=\"leaveadmin.php?m=$m&amp;cat_id=$catid&amp;event=edit_cat\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
			<td style=\"width:1.6em\">";
			if ($count[$counter] == 0) {
			echo "<a href=\"leaveadmin.php?m=$m&amp;cat_id=$catid&amp;update=delete_cat&event=cat\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>";
			}
			echo "</td></tr>";
		}
}
		
		
		echo "</table>
				<form action=\"leaveadmin.php\" method=\"post\">
				<h2>Add leave category</h2>
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
}
	

	

	
	
	
	



	
	
	
	
	
		

		
	



echo "</div></div>";
		
		
	include("../includes/rightsidebar.php");

include ("../includes/footer.php");

?>

