<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);
$datepicker = "yes";
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
$integers[] = "role_id";
$integers[] = "location_id";
$integers[] = "status";
$integers[] = "id";

$weekdays[0] = $phrase[425];
$weekdays[1] = $phrase[419];
$weekdays[2] = $phrase[420];
$weekdays[3] = $phrase[421];
$weekdays[4] = $phrase[422];
$weekdays[5] = $phrase[423];
$weekdays[6] = $phrase[424];


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
	

		
	if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "deletetype")
{
 $type_id = $DB->escape($_REQUEST["type_id"]);	
		
	//check shift type not in use	
	$sql  = "select count(*) as count from roster_shifts where shift_type = '$type_id'";	
	$DB->query($sql,"rosterdmin.php");
	//echo $sql;
	$row = $DB->get();
	$count = $row["count"];
	
	if ($count < 1)
	{
	$sql = "delete from roster_types where type_id = $type_id";	
	$DB->query($sql,"rosterdmin.php");	
	}
	
	
}




if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "addtype")
{

 $type_name = $DB->escape($_REQUEST["type_name"]);
 $type_swap = $DB->escape($_REQUEST["type_swap"]);
 $day_0 = $DB->escape($_REQUEST["day_0"]);
 $day_1 = $DB->escape($_REQUEST["day_1"]);
 $day_2 = $DB->escape($_REQUEST["day_2"]);
 $day_3 = $DB->escape($_REQUEST["day_3"]);
 $day_4 = $DB->escape($_REQUEST["day_4"]);
 $day_5 = $DB->escape($_REQUEST["day_5"]);
 $day_6 = $DB->escape($_REQUEST["day_6"]);

 $sh = $DB->escape($_REQUEST["sh"]);
 $sm = $DB->escape($_REQUEST["sm"]); 
 $eh = $DB->escape($_REQUEST["eh"]); 
 $em = $DB->escape($_REQUEST["em"]); 
 

$sql = "insert into roster_types values(NULL,'$sh','$sm','$eh','$em','$type_swap','$day_0','$day_1','$day_2','$day_3','$day_4','$day_5','$day_6','$type_name','$m')";
//echo $sql;
$DB->query($sql,"rosteradmin.php");

}		
		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "edittype")
{

 $type_name = $DB->escape($_REQUEST["type_name"]);
 $type_swap = $DB->escape($_REQUEST["type_swap"]);
 $type_id = $DB->escape($_REQUEST["type_id"]);
 $day_0 = $DB->escape($_REQUEST["day_0"]);
 $day_1 = $DB->escape($_REQUEST["day_1"]);
 $day_2 = $DB->escape($_REQUEST["day_2"]);
 $day_3 = $DB->escape($_REQUEST["day_3"]);
 $day_4 = $DB->escape($_REQUEST["day_4"]);
 $day_5 = $DB->escape($_REQUEST["day_5"]);
 $day_6 = $DB->escape($_REQUEST["day_6"]);

 $sh = $DB->escape($_REQUEST["sh"]);
 $sm = $DB->escape($_REQUEST["sm"]); 
 $eh = $DB->escape($_REQUEST["eh"]); 
 $em = $DB->escape($_REQUEST["em"]); 
 

$sql = "update roster_types set type_sh = '$sh', type_sm = '$sm', type_eh = '$eh',type_em = '$em', type_swap = '$type_swap', day_0 = '$day_0', day_1 = '$day_1',day_2 = '$day_2', day_3 = '$day_3', day_4 = '$day_4', day_5 = '$day_5',day_6 = '$day_6', type_name = '$type_name' where type_id = '$type_id'";
//echo $sql;
$DB->query($sql,"rosteradmin.php");

}		
		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "add_location")
{
 $location_name = $DB->escape($_REQUEST["location_name"]);
 	
$sql = "insert into roster_locations values(NULL,'$location_name','$m')";
//echo $sql;
$DB->query($sql,"rosteradmin.php");
	
}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "addkey")
{
 $_userid = $DB->escape($_REQUEST["_userid"]);
 $location_id = $DB->escape($_REQUEST["location_id"]);
$sql = "update roster_staff set staff_key  = '1' where staff_location = '$location_id' and staff_userid = '$_userid'";
//echo $sql;
$DB->query($sql,"rosteradmin.php");

}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "removekey")
{
 $_userid = $DB->escape($_REQUEST["_userid"]);
 $location_id = $DB->escape($_REQUEST["location_id"]);
$sql = "update roster_staff set staff_key  = '0' where staff_location = '$location_id' and staff_userid = '$_userid'";
//echo $sql;
$DB->query($sql,"rosteradmin.php");

}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "adduser")
{
 $_userid = $DB->escape($_REQUEST["_userid"]);
 $location_id = $DB->escape($_REQUEST["location_id"]);
$sql = "insert into roster_staff values('$location_id','$_userid','0')";
//echo $sql;
$DB->query($sql,"rosteradmin.php");

}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "addmanager")
{
 $_userid = $DB->escape($_REQUEST["_userid"]);
 $location_id = $DB->escape($_REQUEST["location_id"]);
$sql = "insert into roster_managers values('$location_id','$_userid')";
//echo $sql;
$DB->query($sql,"rosteradmin.php");

}


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "adduserrole")
{
 $_userid = $DB->escape($_REQUEST["_userid"]);
 $role_id = $DB->escape($_REQUEST["role_id"]);
$sql = "insert into roster_rolebridge values('$_userid','$role_id')";
//echo $sql;
$DB->query($sql,"rosteradmin.php");

}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "removestaffrole")
{
 $_userid = $DB->escape($_REQUEST["_userid"]);
 $role_id = $DB->escape($_REQUEST["role_id"]);
$sql = "delete from  roster_rolebridge where role = '$role_id' and userid = '$_userid'";
//echo $sql;
$DB->query($sql,"rosteradmin.php");

}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "removestaff")
{
 $_userid = $DB->escape($_REQUEST["_userid"]);
 $location_id = $DB->escape($_REQUEST["location_id"]);
$sql = "delete from  roster_staff where staff_location = '$location_id' and staff_userid = '$_userid'";
//echo $sql;
$DB->query($sql,"rosteradmin.php");

}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "removemanager")
{
 $_userid = $DB->escape($_REQUEST["_userid"]);
 $location_id = $DB->escape($_REQUEST["location_id"]);
$sql = "delete from  roster_managers where manager_location = '$location_id' and manager_userid = '$_userid'";
//echo $sql;
$DB->query($sql,"rosteradmin.php");

}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "add_role")
{
 $role_name = $DB->escape($_REQUEST["role_name"]);
  $role_code = $DB->escape($_REQUEST["role_code"]);
  $colour = $DB->escape($_REQUEST["colour"]);

 	
$sql = "insert into roster_roles values(NULL,'$role_name','$role_code','$colour')";	
$DB->query($sql,"rosteradmin.php");
//echo $sql;	
}


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "delete_role")
{
 $role_id = $DB->escape($_REQUEST["role_id"]);
 	
$sql = "delete from roster_roles where role_id = '$role_id'";	
$DB->query($sql,"rosteradmin.php");
	
}	
		

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "delete_location")
{
 $location_id = $DB->escape($_REQUEST["location_id"]);
 
 
 
 
 
 
 
 
 
 
 
 
 	$sql = "SELECT count(staff_location) AS count
FROM roster_staff where staff_location = '$location_id'";
$DB->query($sql,"rosteradmin.php");

//echo $sql;
	$DB->query($sql,"rosteradmin.php");
	$row = $DB->get();
	$count = $row["count"];

if ($count == 0)
{
 	
$sql = "delete from roster_locations where location_id = '$location_id' and m = '$m'";
//echo $sql;
$DB->query($sql,"rosteradmin.php");
} else { echo "<span style=\"color:red\">Cannot delete location while staff are assigned.</span>";}
	
}		
	

	


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "update_location")
{
 $location_id = $DB->escape($_REQUEST["location_id"]);
 $location_name = $DB->escape($_REQUEST["location_name"]);
 	
$sql = "update roster_location set  location_name = '$location_name' where location_id = '$location_id'";

$DB->query($sql,"rosteradmin.php");

	
}	

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "update_role")
{

 $role_name = $DB->escape($_REQUEST["role_name"]);
 $role_code = $DB->escape($_REQUEST["role_code"]);
   $role_colour = $DB->escape($_REQUEST["role_colour"]);
 

$sql = "update roster_roles set role_name = '$role_name', role_code = '$role_code', role_colour = '$role_colour' where role_id = '$role_id'";	
$DB->query($sql,"rosteradmin.php");
	
}	


	

		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delcycle"  )
   {
    
    	
    

 
	$id = $DB->escape($_REQUEST["id"]);
	$sql = "delete from roster_periods where id = '$id'";
	//echo $sql;
	$DB->query($sql,"rosteradmin.php");
   }
	
   	

		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addcycle"  )
   {
    
   
   	
    	$name = $DB->escape($_POST["name"]);
	$colour = $DB->escape($_POST["colour"]);
	$pdays = $DB->escape($_POST["pdays"]);
	$pdate = $_POST["pdate"];
	
	$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
			//echo "echo hello $pdate";
		
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
	
	 $sql = "INSERT INTO roster_periods VALUES(NULL,'$m','$name','$colour','$pdate','$pdays')"; 
//echo $sql;
	$DB->query($sql,"rosteradmin.php");
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
	
	 $sql = "update roster_periods set name = '$name',colour = '$colour',pdate = '$pdate',pdays = '$pdays' where id = '$id' and m = '$m'"; 
//echo $sql;
	$DB->query($sql,"rosteradmin.php");
}
	}	
	
	
		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "options"  )
   {
    
    	 $sql = "delete from leave_options where m = '$m'"; 
			$DB->query($sql,"helpdeskedit.php");
    

 
	$mode = $DB->escape($_POST["mode"]);
	$break = $DB->escape($_POST["break"]);
	
	
	 $sql = "INSERT INTO leave_options VALUES('$m','$mode','$break')"; 
//echo $sql;
	$DB->query($sql,"rosteradmin.php");
	}


	
	} //end block allowed only to edit users

if ($access->thispage == 3)
{
	
	//<li style=\"display:inline\"><a href=\"rosteradmin.php?m=$m&event=options\">Options</a></li>
	echo "<ul style=\"list-style:none;margin:1em 0\">
	
	<li style=\"display:inline;padding-right:1em\"><a href=\"rosteradmin.php?m=$m&event=loc\">Locations</a></li>
	<li style=\"display:inline;padding-right:1em\"><a href=\"rosteradmin.php?m=$m&event=role\">Roles</a></li>
	<li style=\"display:inline;padding-right:1em\"><a href=\"rosteradmin.php?m=$m&event=types\">Shift types</a></li>
	<li style=\"display:inline\"><a href=\"rosteradmin.php?m=$m&event=cycles\">cycles</a></li>
	
	</ul>";
}

if (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edit_location" && $access->thispage == 3)
{
	
	$sql = "select location_name from roster_locations where location_id = '$location_id'";
//	echo $sql;
	$DB->query($sql,"rosteradmin.php");
	$row = $DB->get();
	$location_name = formattext($row["location_name"]);

	echo "<h2>$location_name</h2><form action=\"rosteradmin.php\" method=\"post\">
	<h4>$phrase[180]</h4><br>
	<input type=\"text\" name=\"location_name\" value=\"$location_name\"> 

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"location_id\" value=\"$location_id\">
	<input type=\"hidden\" name=\"update\" value=\"update_location\">
	<input type=\"hidden\" name=\"event\" value=\"loc\">
<input type=\"submit\" name=\"submit\" value=\"Update \">


	</form>
	<br><br>
	<h4>Staff</h4>
	<table class=\"colourtable\" >
        <tr style=\"font-weight:bold\"><td>Name</td><td>Key</td><td>Remove</td></tr>";
	
	$users = array();
	$sql = "select userid,first_name,staff_key, last_name from roster_staff,user where user.userid = roster_staff.staff_userid and staff_location = '$location_id'";
	$DB->query($sql,"rosteradmin.php");
	while ($row = $DB->get())
		{
		$id = $row["userid"];
		$users[] = $id;
                $first_name = $row["first_name"];
                $last_name = $row["last_name"];
                 $key = $row["staff_key"];
                echo "<tr><td>$last_name, $first_name</td><td >";
                if ($key == 1) {echo "<img src=\"../images/key2.png\"> <a href=\"rosteradmin.php?m=$m&update=removekey&event=edit_location&_userid=$id&location_id=$location_id\">Remove key</a>";} else {
                {echo " <a href=\"rosteradmin.php?m=$m&update=addkey&event=edit_location&_userid=$id&location_id=$location_id\">Add key</a>";}
            }
                
          
                echo "</td><td><a href=\"rosteradmin.php?m=$m&update=removestaff&event=edit_location&_userid=$id&location_id=$location_id\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></a></td></tr>";
		}

                echo "</table>

<form action=\"rosteradmin.php\" method=\"post\">

	  <br><br><select name=\"_userid\">
	 ";
	 $sql = "select first_name, last_name, userid from user where username != 'guest' and userid != '1'";
     $sql = "select first_name, last_name, userid from user where username != 'guest'";
	  $DB->query($sql,"staff.php");

	while ($row = $DB->get())
		{
		$userid = $row["userid"];
		$first_name = $row["first_name"];
		$last_name = $row["last_name"];

		if (!in_array($userid,$users))
		{ echo "<option value=\"$userid\">$last_name, $first_name</option>";
		}

	 }

	 echo "</select>
 	<input type=\"hidden\" name=\"m\" value=\"$m\">
         <input type=\"hidden\" name=\"event\" value=\"edit_location\">
	<input type=\"hidden\" name=\"update\" value=\"adduser\">
   
	 <input type=\"hidden\" name=\"location_id\" value=\"$location_id\">
	 <input type=\"submit\" value=\"Add user\"></form>

<br><br>
	<h4>Managers</h4>
	<table class=\"colourtable\" >
        <tr style=\"font-weight:bold\"><td>Name</td><td>Remove</td></tr>";
	
	$users = array();
	$sql = "select userid,first_name, last_name from roster_managers,user where user.userid = roster_managers.manager_userid and manager_location = '$location_id'";
	$DB->query($sql,"rosteradmin.php");
	while ($row = $DB->get())
		{
		$id = $row["userid"];
		$managers[] = $id;
                $first_name = $row["first_name"];
                $last_name = $row["last_name"];
          
                echo "<tr><td>$last_name, $first_name</td><td><a href=\"rosteradmin.php?m=$m&update=removemanager&event=edit_location&_userid=$id&location_id=$location_id\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></a></td></tr>";
		}

                echo "</table>

<form action=\"rosteradmin.php\" method=\"post\">

	  <br><br><select name=\"_userid\">
	 ";
     $sql = "select first_name, last_name, userid from user where username != 'guest' and userid != '1'";
	 $sql = "select first_name, last_name, userid from user where username != 'guest'";
     
	  $DB->query($sql,"staff.php");

	while ($row = $DB->get())
		{
		$userid = $row["userid"];
		$first_name = $row["first_name"];
		$last_name = $row["last_name"];

		if (!in_array($userid,$managers))
		{ echo "<option value=\"$userid\">$last_name, $first_name</option>";
		}

	 }

	 echo "</select>
 	<input type=\"hidden\" name=\"m\" value=\"$m\">
         <input type=\"hidden\" name=\"event\" value=\"edit_location\">

         <input type=\"hidden\" name=\"update\" value=\"addmanager\">
	 <input type=\"hidden\" name=\"location_id\" value=\"$location_id\">
	 <input type=\"submit\" value=\"Add manager\"></form>
";
	
}

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edit_role" && $access->thispage == 3)
{
	
	$sql = "select * from roster_roles where role_id = '$role_id'";

	$DB->query($sql,"clickeradmin.php");
	$row = $DB->get();
	$role_name = formattext($row["role_name"]);
	$role_code = $row["role_code"];
	$role_colour = $row["role_colour"];

	
	if ($role_colour == "" || $role_colour == "NULL") {$role_colour = "000000";}
	
	echo "<form action=\"rosteradmin.php\" method=\"post\">
	<b>Group name</b><br>
	<input type=\"text\" name=\"role_name\" value=\"$role_name\"><br><br>
	<b>Category code</b><br>
	<input type=\"text\" name=\"role_code\" size=\"3\" value=\"$role_code\"><br><br>
	
	
	
	<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
<b>Colour</b><br><input type=\"text\" name=\"role_colour\" class=\"color\" value=\"$role_colour\"><br><br>
	
	
	
	
	
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"role_id\" value=\"$role_id\">
	<input type=\"hidden\" name=\"update\" value=\"update_role\">
	<input type=\"hidden\" name=\"event\" value=\"role\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
	</form>";
	
}

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "editcycle" && $access->thispage == 3)
		

{
	
	
		 $sql  = "select id, name,colour,pdays, pdate from roster_periods where id = '$id' and m = '$m'";	
	$DB->query($sql,"rosteradmin.php");
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
			<form action=\"rosteradmin.php\" method=\"post\">
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

	 $sql  = "select id, name,colour,pdays, pdate from roster_periods where m = '$m'";	
	$DB->query($sql,"rosteradmin.php");
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
	<td><a href=\"rosteradmin.php?m=$m&event=editcycle&id=$id\">Edit</a></td>
	<td><a href=\"rosteradmin.php?m=$m&event=cycles&update=delcycle&id=$id\">Delete</a></td></tr>";
	
	}
	
	
	echo "</table>
	
	
		
	
	<h3>Add cycle</h3>
			<form action=\"rosteradmin.php\" method=\"post\">
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

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "types" && $access->thispage == 3)
		

{
	
	echo "<h2>Shift types</h2>
	
	<a href=\"rosteradmin.php?m=$m&amp;event=addtype\">Add shift type</a><br><br>
	<table class=\"colourtable\">
	
	
	";

	$type_totals = array();
	
	 $sql  = "select count(*) as count,shift_type from roster_shifts group by shift_type";	
	$DB->query($sql,"rosterdmin.php");
	//echo $sql;
	while ($row = $DB->get())
	{
	$count = $row["count"];
	$shift_type = $row["shift_type"];
	$type_totals[$shift_type] = $count;
	}
	
	 $sql  = "select * from roster_types where  m = '$m'";	
	$DB->query($sql,"rosterdmin.php");
	//echo $sql;
	while ($row = $DB->get())
	{
	$type_id = $row["type_id"];
	$sh = $row["type_sh"];
	$sm= $row["type_sm"];
	$eh = $row["type_eh"];
	$em= $row["type_em"];
	$type_swap = $row["type_swap"];
	$type_name = $row["type_name"];
        
        if ($em == "0") {$em = "00";}
        if ($sm == "0") {$sm = "00";}
        if ($sh < 10) {$sh = "&nbsp;&nbsp;" . $sh;}
        if ($eh < 10) {$eh = "&nbsp;&nbsp;" . $eh;}

	$day[0] = $row["day_0"];
	$day[1] = $row["day_1"];
	$day[2] = $row["day_2"];
	$day[3] = $row["day_3"];
	$day[4] = $row["day_4"];
	$day[5] = $row["day_5"];
	$day[6] = $row["day_6"];
	
	echo "<tr><td><b>$type_name</b></td><td>$sh:$sm-$eh:$em</td><td><a href=\"rosteradmin.php?m=$m&amp;event=edittype&amp;type_id=$type_id\">$phrase[26]</a></td>
	<td>";
	if (!in_array($type_id,$type_totals))
	{ echo "<a href=\"rosteradmin.php?m=$m&amp;event=types&amp;update=deletetype&amp;type_id=$type_id\">$phrase[24]</a>";}
	
	echo "</td></tr>";
	
	}
	echo "</table>
		
	";
	 
}



elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edittype" && $access->thispage == 3)

{	
	echo "<h2>Edit shift type</h2>";
	
	$location_count = 0;
	$sql = "select location_id, location_name  from roster_locations where m = '$m'";
	$DB->query($sql,"rosterdmin.php");
	while ($row = $DB->get())
	{
	$location_id = $row["location_id"];	
	$location_names[$location_id] = $row["location_name"];	
	$location_count++;
	}
	
	
	 $type_id = $DB->escape($_REQUEST["type_id"]);
	 $sql  = "select * from roster_types where  type_id = '$type_id' and m = '$m'";	
	$DB->query($sql,"rosterdmin.php");
//	echo $sql;
	$row = $DB->get();
	

	$sh = $row["type_sh"];
	$sm= $row["type_sm"];
	$eh = $row["type_eh"];
	$em= $row["type_em"];
	$type_swap = $row["type_swap"];
	$type_name = $row["type_name"];

	$day[0] = $row["day_0"];
	$day[1] = $row["day_1"];
	$day[2] = $row["day_2"];
	$day[3] = $row["day_3"];
	$day[4] = $row["day_4"];
	$day[5] = $row["day_5"];
	$day[6] = $row["day_6"];
	
	echo "
<form action=\"rosteradmin.php\" method=\"post\">
<table class=\"colourtable\">
<tr><td>Name </td><td><input type=\"text\" name=\"type_name\" value=\"$type_name\"></td></tr>
<tr><td>Start </td><td><select name=\"sh\">";
		$temp = 6;
			while ($temp <23) 
			{
				if ($temp < 12) {$displayhour = $temp . " am";}
				elseif ($temp ==  12) {$displayhour = $temp . " pm";}
				elseif ($temp > 12 && $temp < 23) {$displayhour = $temp - 12 . " pm";}
				
				echo "<option value=\"$temp\"";
				if ($temp == $sh) {echo " selected";}
				echo ">$displayhour</option>";
			$temp++;
			}
	echo "</select> <select name=\"sm\">";
	$temp = 0;
			while ($temp <60) 
			{
				if ($temp < 10) {$display = "0" . $temp ;} else {$display = $temp;}
				
				echo "<option value=\"$temp\"";
				if ($temp == $sm) {echo " selected";}
				echo ">$display</option>";
			$temp = $temp + 15;
			}
	echo "</select></td></tr>
	<tr><td>End </td><td><select name=\"eh\">";
		$temp = 6;
			while ($temp <23) 
			{
				if ($temp < 12) {$displayhour = $temp . " am";}
				elseif ($temp ==  12) {$displayhour = $temp . " pm";}
				elseif ($temp > 12 && $temp < 23) {$displayhour = $temp - 12 . " pm";}
				
				echo "<option value=\"$temp\"";
				if ($temp == $eh) {echo " selected";}
				echo ">$displayhour</option>";
			$temp++;
			}
	echo "</select> <select name=\"em\">";
	$temp = 0;
			while ($temp <60) 
			{
				if ($temp < 10) {$display = "0" . $temp ;} else {$display = $temp;}
				
				echo "<option value=\"$temp\"";
				if ($temp == $em) {echo " selected";}
				echo ">$display</option>";
			$temp = $temp + 15;
			}
	echo "</select></td></tr>
	

	<tr><td>Swappable </td><td><select name=\"type_swap\"><option value=\"0\">$phrase[13]</option><option value=\"1\"";
	if ($type_swap == 1) {echo " selected=\"selected\"";}
	echo ">$phrase[12]</option></select>
</td></tr>";
	$counter = 0;
	while ($counter < 7)
	{
	echo "<tr><td>$weekdays[$counter]</td><td><select name=\"day_$counter\"><option value=\"0\">$phrase[13]</option><option value=\"1\"";
	if ($day[$counter] == 1) {echo " selected=\"selected\"";}
	echo ">$phrase[12]</option></select></td></tr>";	
		
		
	$counter++;	
	}
	
	
	echo "
	
	
	<tr><td></td><td>
	<input type=\"submit\" name=\"submit\" value=\"$phrase[16]\">  
	<input type=\"hidden\" name=\"event\" value=\"types\">  
	<input type=\"hidden\" name=\"type_id\" value=\"$type_id\">  
	<input type=\"hidden\" name=\"update\" value=\"edittype\">  
	<input type=\"hidden\" name=\"m\" value=\"$m\">  </td></tr></table>

	</form>	";

}




elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "addtype" && $access->thispage == 3)

{	
	echo "<h2>Add shift type</h2>";
	
	$location_count = 0;
	$sql = "select location_id, location_name  from roster_locations where m = '$m'";
	$DB->query($sql,"rosterdmin.php");
	while ($row = $DB->get())
	{
	$location_id = $row["location_id"];	
	$location_names[$location_id] = $row["location_name"];	
	$location_count++;
	}
	
	
	if ($location_count == 0)
	{
		
	echo "Roster locations need to be created before creating roster shit types.";	
		
	}
	else 
	{
	
	echo "
<form action=\"rosteradmin.php\" method=\"post\">
<table class=\"colourtable\">
<tr><td>Name </td><td><input type=\"text\" name=\"type_name\"></td></tr>
<tr><td>Start </td><td><select name=\"sh\">";
		$temp = 6;
			while ($temp <23) 
			{
				if ($temp < 12) {$displayhour = $temp . " am";}
				elseif ($temp ==  12) {$displayhour = $temp . " pm";}
				elseif ($temp > 12 && $temp < 23) {$displayhour = $temp - 12 . " pm";}
				
				echo " 
				+ '<option value=\"$temp\">$displayhour</option>'";
			$temp++;
			}
	echo "</select> <select name=\"sm\">";
	$temp = 0;
			while ($temp <60) 
			{
				if ($temp < 10) {$display = "0" . $temp ;} else {$display = $temp;}
				
				echo " 
				+ '<option value=\"$temp\">$display</option>'";
			$temp = $temp + 15;
			}
	echo "</select></td></tr>
<tr><td>	End </td><td><select name=\"eh\">";
		$temp = 6;
			while ($temp <23) 
			{
				if ($temp < 12) {$displayhour = $temp . " am";}
				elseif ($temp ==  12) {$displayhour = $temp . " pm";}
				elseif ($temp > 12 && $temp < 23) {$displayhour = $temp - 12 . " pm";}
				
				echo " 
				+ '<option value=\"$temp\">$displayhour</option>'";
			$temp++;
			}
	echo "</select> <select name=\"em\">";
	$temp = 0;
			while ($temp <60) 
			{
				if ($temp < 10) {$display = "0" . $temp ;} else {$display = $temp;}
				
				echo " 
				+ '<option value=\"$temp\">$display</option>'";
			$temp = $temp + 15;
			}
	echo "</select></td></tr>
	

<tr><td>	Swappable </td><td><select name=\"type_swap\"><option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option></select>";
	
	$counter = 0;
	while ($counter < 7)
	{
	echo "<tr><td>$weekdays[$counter]</td><td><select name=\"day_$counter\"><option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option></select></td></tr>";	
		
		
	$counter++;	
	}
	
	
	echo "<tr><td></td><td>
	<input type=\"submit\" name=\"submit\" value=\"Add type\">  
	<input type=\"hidden\" name=\"event\" value=\"types\">  
	<input type=\"hidden\" name=\"update\" value=\"addtype\">  
	<input type=\"hidden\" name=\"m\" value=\"$m\">  </td></tr></table>

	</form>	";
}
}

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "loc" && $access->thispage == 3)

{	
		
	
	
	
		
	
	
	echo "<h2>Locations</h2>
	<table class=\"colourtable\">";
		
	/*
		
		$sql = "SELECT location_id, location_name, roster_locations.location_id as location_id, count(staff_userid) AS count
FROM roster_locations
LEFT JOIN roster_staff ON roster_locations.location_id = roster_staff.staff_location
where roster_locations.m = '$m'
";
*/	
                
                
                		$sql = "SELECT location_id, location_name
FROM roster_locations

where roster_locations.m = '$m'
";
                
	//echo $sql;

		$DB->query($sql,"rosteradmin.php");
		$num = $DB->countrows();
		$counter = 0;
		
		while ($row = $DB->get())
		{
			
		$locname = htmlentities($row["location_name"]);
		
		$locationid = $row["location_id"];
		//$count = $row["count"];
		if ($locationid != "")
                {
			echo "<tr><td><b>$locname</b></td>";
		
			echo "<td><a href=\"rosteradmin.php?m=$m&amp;location_id=$locationid&amp;event=edit_location\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
			<td style=\"width:1.6em\">";
		
			echo "<a href=\"rosteradmin.php?m=$m&amp;location_id=$locationid&amp;update=delete_location&event=loc\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>";
			
			
			echo "</td></tr>";
                }
		}
		
		

		
		echo "</table>
		
		<form action=\"rosteradmin.php\" method=\"post\">
<br><input type=\"text\" name=\"location_name\"> 
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"event\" value=\"loc\">
<input type=\"hidden\" name=\"update\" value=\"add_location\">

<input type=\"submit\" value=\"$phrase[176]\">
</form>";
		
}

elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "addrole" && $access->thispage == 3)
{
	
echo "<form action=\"rosteradmin.php\" method=\"post\">
				<h2>Add Role</h2>
<br><b>Role name</b><br>
<input type=\"text\" name=\"role_name\" size=\"60\"> <br>
<b>Role code</b> <br>
<input type=\"text\" name=\"role_code\" size=\"3\" maxlength=\"3\"> <br>

	<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
<b>Colour</b><br><input type=\"text\" name=\"colour\" class=\"color\" value=\"#000000\"><br><br>
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"update\" value=\"add_role\">
<input type=\"hidden\" name=\"event\" value=\"role\">
<input type=\"submit\" value=\"$phrase[176]\">
</form>";
}



elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "role" && $access->thispage == 3)

{
		
		
		
		
		echo "<h2>Roles</h2> <a href=\"rosteradmin.php?m=$m&event=addrole\">Add role</a><br><br>";
		
		
	//get list of all users //user.userid != '1' and
	$sql = "select userid,first_name, last_name from user where  username != 'guest' order by last_name";
	
	
	
	$DB->query($sql,"rosteradmin.php");
	while ($row = $DB->get())
		{
		$id = $row["userid"];
	
        $first_name[$id] = $row["first_name"];
        $last_name[$id] = $row["last_name"];
		}
		
		//print_r($last_name);	
			
		//get list of roles		
		$sql = "SELECT role_id, role_code, role_name, role_colour, count(userid) as count
FROM roster_roles
LEFT JOIN roster_rolebridge ON roster_rolebridge.role = roster_roles.role_id";

                
                
		$sql = "SELECT role_id, role_code, role_name, role_colour
FROM roster_roles
";                
                
                
			
	//echo $sql;
		$DB->query($sql,"rosteradmin.php");
		$num = $DB->countrows();
		$counter = 0;

		
		$assigned_users = array();
		
		while ($row = $DB->get())
		{
		$role_id = $row["role_id"];
		if ($role_id != "")
		{
	

		//$_role_count[$role_id] = $row["count"];
		$_role_name[$role_id] = $row["role_name"];
		$_role_code[$role_id] = $row["role_code"];
		$_role_colour[$role_id] = $row["role_colour"];
		
		}
		}
	

		foreach ($_role_name as $key => $name)
		{
			
                    $assigned_users = array();
                    
                    
			$role_group[$key]  = array();
		
		$sql = "select userid from roster_rolebridge where role = '$key'";

			$DB->query($sql,"rosteradmin.php");
			while ($row = $DB->get())
			{
		$assigned_users[] = $row["userid"];
	//	echo "row is $row[userid]  xxx ";
		$role_group[$key][] = $row["userid"];
			}
		
		
		
			
	
			echo "<div class=\"accent\" style=\"padding:1em;margin-bottom:4em\"><span style=\"font-size:1.8em;color:#$_role_colour[$key]\"";
			echo ">$name</span><br>Code: $_role_code[$key] <br><a href=\"rosteradmin.php?m=$m&amp;role_id=$key&amp;event=edit_role\">Edit role</a>";
			if (count($assigned_users) == 0) {
			echo " <a href=\"rosteradmin.php?m=$m&amp;role_id=$key&amp;update=delete_role&event=role\" style=\"padding-left:2em\">Delete role</a>";
			}
		
			echo "<br><br><b>Members</b><br>";
			
			
			foreach ($role_group[$key] as $userid)
			{
	
			 echo "<span style=\"width:10em\">$last_name[$userid], $first_name[$userid]</span>
			 <a href=\"rosteradmin.php?m=$m&update=removestaffrole&event=role&_userid=$userid&role_id=$key\" style=\"vertical-align:bottom\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></a><br>";
			}
               
                        
                        $showAddForm = "no";
                        	foreach ($last_name as $id => $lastname)
		{
	if (!in_array($id,$assigned_users)) {$showAddForm = "yes";	}
		
	}
                        
			if ($showAddForm == "yes")
                        {
			echo "<form action=\"rosteradmin.php\" method=\"post\">

	  <br><br><select name=\"_userid\">
	 ";
		foreach ($last_name as $id => $lastname)
		{
	if (!in_array($id,$assigned_users)) {echo "<option value=\"$id\">$lastname, $first_name[$id]</option>";	}
		
	}
	 echo "</select>
 	<input type=\"hidden\" name=\"m\" value=\"$m\">
         <input type=\"hidden\" name=\"event\" value=\"role\">
	
         <input type=\"hidden\" name=\"update\" value=\"adduserrole\">
	 <input type=\"hidden\" name=\"role_id\" value=\"$key\">
	 <input type=\"submit\" value=\"Add user\"></form>";
		}
                echo "</div>";
                }

		
		}
		
		
	
}

	

	

	
	
	
	



	
	
	
	
	
		

		
	



echo "</div></div>";
		
		
	include("../includes/rightsidebar.php");

include ("../includes/footer.php");

?>

