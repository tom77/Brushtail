<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);



$integers[] = "t";
$integers[] = "probnum";
$integers[] = "time";
$integers[] = "locationid";
$integers[] = "id";
$integers[] = "process";

$ip = ip("pc");



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
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
		

if (isset($ERROR))
	{
	echo "$ERROR";
	}
else 
	{
		

if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteshift")
	{

	$shift_id = $DB->escape($_REQUEST["shiftid"]);
	$view = $_REQUEST["view"];
		


	include("../classes/Rosters.php");
		
	$sql = "select * from roster_shifts where  shift_id = '$shift_id'";	
	//echo "$sql";
	$DB->query($sql,"rosterdmin.php");
	$row = $DB->get();
	$weekday = date("w",strtotime($row["shift_date"]));
	$date = str_replace("-","",$row["shift_date"])	;
	$matches[0] = str_replace("-","",$row["shift_date"])	;
	$location_id = $row["shift_location"];
	$userid = $row["userid"];
	$rid = $row["shift_role"];
	
	$sql = "delete from roster_shifts where shift_id = '$shift_id'";	
	$DB->query($sql,"rosterdmin.php");
	
	//echo "<br>weekday is $weekday<br>";
	
 	$RosterShift = new RosterShift($m,$DB,$matches,$location_id,$phrase);
	if ($view == "location")
 	{
 		//echo "hello";
	$RosterShift->displayShift($date);
 	}
	
 	if ($view == "custom")
 	{
	$RosterShift->displayUser($userid,$date,$rid);
 	}
	
	}	

	
	
	
if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "takeshift")
	{

	$shift_id = $DB->escape($_REQUEST["shiftid"]);
	$view = $_REQUEST["view"];
		


		include("../classes/Rosters.php");
		
$sql = "select * from roster_shifts,roster_types, roster_locations where roster_types.type_id = roster_shifts.shift_type 
and roster_shifts.shift_location = roster_locations.location_id and shift_id = '$shift_id'";	
	//echo "$sql";
	$DB->query($sql,"rosterdmin.php");
	$row = $DB->get();
	$shift_date = $row["shift_date"];
	$weekday = date("w",strtotime($row["shift_date"]));
	$date = str_replace("-","",$row["shift_date"]);
	$matches[0] = str_replace("-","",$row["shift_date"])	;
	$location_id = $row["shift_location"];
	$location_name = $row["location_name"];
	$displaytime = format_time($row["type_sh"],$row["type_sm"]) . " - " . format_time($row["type_eh"],$row["type_em"]);
	$displaydate = strftime("%a %x",strtotime($row["shift_date"]));
	$shift_available = $row["shift_available"];
	$olduserid = $row["userid"];
	$shift_type = $row["shift_type"];
	$newuserid = $_SESSION["userid"];
	$rid = $row["shift_role"];
	
	
	//check if user already has a shift at that time
	
	$sql = "select * from roster_shifts where shift_type = '$shift_type' and shift_date = '$shift_date' and userid = '$newuserid'";
	$DB->query($sql,"rosterdmin.php");
	//echo "$sql "  . $DB->countrows() . "   vvv" ;
	if ($DB->countrows() == 0)
	{

	
	$sql = "update roster_shifts set userid = '$newuserid', shift_available = '0' where shift_available = '1' and shift_id = '$shift_id'";	
	//echo $sql;
	$DB->query($sql,"rosterdmin.php");
	
	
//	echo "<br>old $olduserid new $newuserid<br>";
	
//	echo "MMM$olduserid,$date,$rid MMMM";
	
	$sql = "select * from user"; 
	$DB->query($sql,"rosterdmin.php");
	while ($row = $DB->get())
	{
	$id = $row["userid"];	
	if ($id == $newuserid)
		{
		$new_name =  $row["first_name"] . "," .  $row["last_name"];
		$new_email = $row["email"] ;
		}
	if ($id == $olduserid)
		{
		$old_name = $row["first_name"] . "," . $row["last_name"];
		$old_email = $row["email"] ;
		}
		
	}
	
if ($olduserid == 0)
{
$old_name = "Available";
$old_email = "" ;	
}
	
	
$message = "
Roster reallocation

$displaydate
$displaytime
$location_name

from
$old_name
to
$new_name

";	

if ($shift_available == 1)
{
send_email($DB,"$new_email, $old_email", "Roster reallocation", $message,'');
}
	


	}


 	$RosterShift = new RosterShift($m,$DB,$matches,$location_id,$phrase);
	
 		if ($view == "location")
 	{
	$RosterShift->displayShift($date);
 	}
	
 	if ($view == "custom")
 	{
	$RosterShift->displayUser($olduserid,$date,$rid);
 	}
 	
 	if ($view == "home")
 	{
 			$today = date("Ymd");
 		$sql = "select * from roster_shifts,roster_types, roster_locations where roster_locations.location_id = roster_shifts.shift_location
	  	and 	roster_types.type_id = roster_shifts.shift_type and
	  	 shift_available = '1' and userid != $_SESSION[userid] and shift_date >= '$today' order by shift_date";
	  	$DB->query($sql,"rosterdmin.php");
	//echo $sql;
	while ($row = $DB->get())
	{
	$location_name = $row["location_name"];
	$location_id = $row["location_id"];
	$shift_id = $row["shift_id"];
	$displaytime = format_time($row["type_sh"],$row["type_sm"]) . " - " . format_time($row["type_eh"],$row["type_em"]);
	$shiftdate = strftime("%a %d %b %Y",strtotime($row["shift_date"]));
	$startdate = date("d-m-Y",strtotime($row["shift_date"]));
	
	echo "<span onclick=\"takeShift('$shift_id')\">$shiftdate $displaytime<br>
	$location_name</span><br><br>";	
	}
 		
 	}








	}	



	
	
	
if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "displayuser")
	{
	$userid = $DB->escape($_REQUEST["userid"]);
	$date = $DB->escape($_REQUEST["date"]);
	$rid = $DB->escape($_REQUEST["rid"]);
	$shift_id = $DB->escape($_REQUEST["shiftid"]);
	
	$sql = "select shift_date,userid, shift_location from roster_shifts where  shift_id = '$shift_id'";	
	//echo "$sql";
	$DB->query($sql,"rosterdmin.php");
	$row = $DB->get();
	$weekday = date("w",strtotime($row["shift_date"]));
	$matches[0] = str_replace("-","",$row["shift_date"]);
	$location_id = $row["shift_location"];
	
	include("../classes/Rosters.php");	
	
	$RosterShift = new RosterShift($m,$DB,$matches,$location_id,$phrase);
	$RosterShift->displayUser($userid,$date,$rid);	
	}
	
	
if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "makeavailable")
	{

	$shift_id = $DB->escape($_REQUEST["shiftid"]);
	
	
	
	
	$view = $_REQUEST["view"];
		


		include("../classes/Rosters.php");
		
	$sql = "select * from roster_shifts where  shift_id = '$shift_id'";	
	//echo "$sql";
	$DB->query($sql,"rosterdmin.php");
	$row = $DB->get();
	$weekday = date("w",strtotime($row["shift_date"]));
	$matches[0] = str_replace("-","",$row["shift_date"]);
	$date = str_replace("-","",$row["shift_date"])	;
	$location_id = $row["shift_location"];
	$userid = $row["userid"];
	$rid = $row["shift_role"];
	$sql = "update roster_shifts set  shift_available = '1' where shift_id = '$shift_id'";	
	$DB->query($sql,"rosterdmin.php");
	
	//echo "<br>weekday is $weekday<br>";
	$RosterShift = new RosterShift($m,$DB,$matches,$location_id,$phrase);
	
 	if ($view == "location")
 	{
	$RosterShift->displayShift($date);
 	}
	
 	if ($view == "custom")
 	{
 		
	$RosterShift->displayUser($userid,$date,$rid);
 	}
	
	}	
	
	
	
	
	
if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "makenotavailable")
	{

	$shift_id = $DB->escape($_REQUEST["shiftid"]);
		$view = $_REQUEST["view"];
		


		include("../classes/Rosters.php");
		
	$sql = "select * from roster_shifts where  shift_id = '$shift_id'";	
	//echo "$sql";
	$DB->query($sql,"rosterdmin.php");
	$row = $DB->get();
	$weekday = date("w",strtotime($row["shift_date"]));
	$date = str_replace("-","",$row["shift_date"]);
	$matches[0] = str_replace("-","",$row["shift_date"])	;
	$location_id = $row["shift_location"];
	$userid = $row["userid"];
	$rid = $row["shift_role"];
	$sql = "update roster_shifts set  shift_available = '0' where shift_id = '$shift_id'";	
	$DB->query($sql,"rosterdmin.php");
	
	//echo "<br>weekday is $weekday<br>";
	
 	$RosterShift = new RosterShift($m,$DB,$matches,$location_id,$phrase);
	
 		if ($view == "location")
 	{
	$RosterShift->displayShift($date);
 	}
	
 	if ($view == "custom")
 	{
 	//	echo "hell $userid " . $date;
	$RosterShift->displayUser($userid,$date,$rid);
 	}

	
	}
	
	
	
	

	

if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "addshift")
	{

	$type_id = $DB->escape($_REQUEST["type_id"]);
	$date = $DB->escape($_REQUEST["date"]);
	$uid = $DB->escape($_REQUEST["uid"]);
	$role = $DB->escape($_REQUEST["role"]);
	$location_id = $DB->escape($_REQUEST["location_id"]);
	
	$view = $_REQUEST["view"];


		


		include("../classes/Rosters.php");
		
		
	//$sql = "select role from roster_rolebridge where userid = '$uid'";	
	//$DB->query($sql,"rosterdmin.php");
	//$row = $DB->get();
	//$role = $row["role"];	
	if ($uid == 0) {$shift_available = 1;} else {$shift_available = 0;}
		
	//$sql = "select  type_location from roster_types where  type_id = '$type_id'";	
//	echo "$sql";
	//$DB->query($sql,"rosterdmin.php");
//	$row = $DB->get();
	//$weekday = date("w",strtotime($row["shift_date"]));
	$matches[0] = $date;	;
	//$location_id = $row["shift_location"];
	
	$sql = "insert into roster_shifts values(NULL,'$date','$type_id','$shift_available','1','$uid','$role','$location_id')";	
	
	$DB->query($sql,"rosterdmin.php");
	
	
 	$RosterShift = new RosterShift($m,$DB,$matches,$location_id,$phrase);
 	
 	if ($view == "location")
 	{
	$RosterShift->displayShift($date);
 	}
	
 	if ($view == "custom")
 	{
	$RosterShift->displayUser($uid,$date,$role);
 	}
 	
	}			

	
	
if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "showshift")
	{	
	
		$location_id = $DB->escape($_REQUEST["location_id"]);
		$userid = $DB->escape($_REQUEST["userid"]);
		$rid = $DB->escape($_REQUEST["rid"]);
		$date = $DB->escape($_REQUEST["date"]);
		$weekday = date("w",strtotime($date));
		
		$menuid = "m_" . $date . "_" . $userid;
		
	echo "<img src=\"../images/closewindow.png\" onclick=\"hideMenu('$menuid')\" style=\"float:right\" alt=\"Close\"  title=\"Close\"><div style=\"font-weight:bold;clear:both\">$phrase[1055]</div><ul class=\"rostermenu\">";
	$sql = "SELECT type_name, type_id
FROM roster_types
LEFT JOIN roster_shifts ON type_id = shift_type
where day_$weekday = '1' 

GROUP BY type_id
";
	$DB->query($sql,"rosterdmin.php");
	//echo $sql;
	while ($row = $DB->get())
	{
	$type_id = $row["type_id"];
	$type_name = $row["type_name"];
		
	echo "<li onclick=\"addShift('$userid','$type_id','$date','$weekday','$rid','$location_id')\">$type_name</li> ";
	}
	
	echo "</ul>";
		
		
		
	}	
	
	
	
	
if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "showstaff")
	{	
	
		$location_id = $DB->escape($_REQUEST["location_id"]);
		$type_id = $DB->escape($_REQUEST["type_id"]);
		$date = $DB->escape($_REQUEST["date"]);
		$weekday = date("w",strtotime($date));
		
		echo "<img src=\"../images/closewindow.png\" onclick=\"hideMenu('addmenu_$type_id')\" style=\"float:right\" alt=\"Close\"  title=\"Close\"><div style=\"font-weight:bold;clear:both\">Select staff member</div><ul class=\"rostermenu\">";
		
	
		//get list of staff already booked in for this shift	
			$bookings = array();
	$sql = "select userid from roster_shifts where shift_type = '$type_id' and shift_date = '$date'";
	$DB->query($sql,"rosterdmin.php");
//	echo $sql;
	while ($row = $DB->get())
	{

	$bookings[] = $row["userid"];
	}
	
	
	

		$roles = array();
	
		//get list of role names
		$sql = "select * from roster_roles ";
	 $DB->query($sql,"rosterdmin.php");
	while ($row = $DB->get())
	{

	$_roleid = $row["role_id"];
	$role_names[$_roleid] = $row["role_name"];
	}
	

	
		//get list of assigned roles 
		$assigned_roles = array();
		$sql = "select * from roster_rolebridge ";
	 $DB->query($sql,"rosterdmin.php");
	while ($row = $DB->get())
	{

	$_userid = $row["userid"];
	$assigned_roles[$_userid] = $row["role"];
	}
	
	
		//get list of staff 
	$sql = "select first_name, last_name, user.userid as userid from user, roster_staff where roster_staff.staff_location  = '$location_id' and roster_staff.staff_userid = user.userid";
	//echo $sql;
	 $DB->query($sql,"rosterdmin.php");
	while ($row = $DB->get())
	{

	$userid = $row["userid"];
	$first_name = $row["first_name"];
	$last_name = $row["last_name"];
	if (!in_array($userid,$bookings))
	{
		
	if (array_key_exists($userid,$assigned_roles)) { $role = $assigned_roles[$userid];} else {$role = "";}	
		
	echo "<li onclick=\"addShift('$userid','$type_id','$date','$weekday','$role','$location_id')\">$last_name, $first_name</li>";
	}
	}
	
	 foreach ($role_names as $rid => $rname)
					{

	echo "<li onclick=\"addShift('0','$type_id','$date','$weekday','$rid','$location_id')\">$phrase[418] - $rname</li>";
	}
	
	
	echo "
	<li onclick=\"addShift('0','$type_id','$date','$weekday','','$location_id')\">$phrase[418]</li>
	</ul>";	
		
	
	}
	
	
	if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "showAvailable")
	{
		$today = date("Ymd");
		
		$sql = "select * from roster_shifts,roster_types, roster_locations where roster_locations.location_id = roster_shifts.shift_location
	  	and 	roster_types.type_id = roster_shifts.shift_type and
	  	 shift_available = '1' and shift_date >= '$today' order by shift_date";
	  	$DB->query($sql,"rosterdmin.php");
	//echo $sql;
	while ($row = $DB->get())
	{
	$location_name = $row["location_name"];
	$location_id = $row["location_id"];
	$displaytime = format_time($row["type_sh"],$row["type_sm"]) . " - " . format_time($row["type_eh"],$row["type_em"]);
	$shiftdate = strftime("%a %d %b %Y",strtotime($row["shift_date"]));
	$startdate = date("d-m-Y",strtotime($row["shift_date"]));
	
	echo "$shiftdate $displaytime<br>
	<a href=\"roster.php?m=99&view=location&location_id=$location_id&startdate=$startdate\">$location_name</a><br><br>";
	}
	
	
	
	}

if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "showMyShifts")
	{	
		
	if (isset($_REQUEST["update"]) )
	{
		
	if ($_REQUEST["update"]== "avail")	 {$available = "1";} else {$available = "0";}
	
	$shift_id = $DB->escape($_REQUEST["shiftid"]);
	$sql = "update roster_shifts set  shift_available = '$available' where shift_id = '$shift_id'";	
	$DB->query($sql,"rosterdmin.php");	
	}
		
		
	
	$today = date("Ymd");
	  	$sql = "select * from roster_shifts,roster_types, roster_locations where roster_locations.location_id = roster_shifts.shift_location
	  	and 	roster_types.type_id = roster_shifts.shift_type and
	  	 userid = '$_SESSION[userid]' and shift_date >= '$today' order by shift_date";
	  	$DB->query($sql,"rosterdmin.php");
	//echo $sql;
	while ($row = $DB->get())
	{
	$location_name = $row["location_name"];
	$location_id = $row["location_id"];
	$shift_id = $row["shift_id"];
	$shift_available = $row["shift_available"];
	$displaytime = format_time($row["type_sh"],$row["type_sm"]) . " - " . format_time($row["type_eh"],$row["type_em"]);
	$shiftdate = strftime("%a %d %b %Y",strtotime($row["shift_date"]));
	$startdate = date("d-m-Y",strtotime($row["shift_date"]));
	
	if ($shift_available == 0)
	{
	echo "<span onclick=\"makeAvailable('$shift_id')\">$shiftdate $displaytime<br>
	$location_name</span><br><br>";
	}
	else 
	{
	echo "<span onclick=\"makeNotAvailable('$shift_id')\" style=\"color:red\">$shiftdate $displaytime<br>$phrase[418]<br>
	$location_name</span><br><br>";	
	}
	//<a href=\"roster.php?m=99&view=location&location_id=$location_id&startdate=$startdate\">$location_name</a><br><br>";
	}
	}
	
	
if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "showmenu")
	{
		
	
		$view = $_REQUEST["view"];
		
		$shift_id = $DB->escape($_REQUEST["shiftid"]);

	 	echo "<img src=\"../images/closewindow.png\" onclick=\"hideMenu('menu_$shift_id')\" style=\"float:right\" alt=\"Close\"  title=\"Close\"><ul class=\"rostermenu\" style=\"clear:both\">";
		
		//get details of shift
		$sql = "select * from roster_shifts, roster_types where roster_shifts.shift_type = roster_types.type_id and shift_id = '$shift_id' ";
	$DB->query($sql,"rosterdmin.php");
//	echo $sql;
	$row = $DB->get();
	

	$shift_date = str_replace("-","",$row["shift_date"]);
	$shift_available = $row["shift_available"];
	
	$weekday = date("w",strtotime($shift_date));
	$shift_published = $row["shift_published"];
	$shift_userid = $row["userid"];
	$shift_swap = $row["type_swap"];
	$shift_location = $row["shift_location"];
	$shift_role = $row["shift_role"];
		 
		//get details fo shift type
		
		//get list of managers
		
	$managers = array();
	$sql = "select * from roster_managers where manager_location = '$shift_location' ";
	 $DB->query($sql,"rosterdmin.php");
	while ($row = $DB->get())
	{

	$managers[] = $row["manager_userid"];
	
	}
		//echo "view is $view";
		//print_r($_REQUEST);
		
		if ($shift_swap == 1)
		{
		if (($shift_userid == $_SESSION["userid"] or in_array($_SESSION["userid"],$managers)) && $shift_userid != 0)
			{
			if ($shift_available == 1)
				{
					
				if ($view == "location")
				{echo "<li onclick=\"makeNotAvailable('$shift_id','$shift_date')\">$phrase[1053]</li>";	}
				elseif  ($view == "custom") 
				{echo "<li onclick=\"makeNotAvailable('$shift_id','$shift_date','$shift_userid')\">$phrase[1053]</li>";	}
				}
				else 
				{
					if ($view == "location")
					{echo "<li onclick=\"makeAvailable('$shift_id','$shift_date')\">$phrase[1052]</li>";	}	
					elseif  ($view == "custom") 
					{echo "<li onclick=\"makeAvailable('$shift_id','$shift_date','$shift_userid')\">$phrase[1052]</li>";	}	
				}
			}
			
			
			
		if ($shift_userid != $_SESSION["userid"])
			{
			if ($shift_available == 1)
				{
			
				//check  role of current user
			//	$existing_role = "";
				$applicant_role = "";
				$sql = "select * from roster_rolebridge "; 	
				
				 $DB->query($sql,"rosterdmin.php");
				while ($row = $DB->get())
				{
				$id = $row["userid"];
				$role = $row["role"];
				if ($id == $_SESSION["userid"]) {$applicant_role = $role;}
			//	if ($id == $shift_userid) {$existing_role = $role;}
			
	
				}
					
				if ($shift_role == "" || $shift_role == $applicant_role)	
				{	
				if ($view == "location")
					{echo "<li onclick=\"takeShift('$shift_id','$shift_date')\">$phrase[1054]</li>";	}	
					elseif  ($view == "custom") 
					{echo "<li onclick=\"takeShift('$shift_id','$shift_date','$shift_userid','$shift_role')\">$phrase[1054]</li>";	}	
				}
				}
			}
			
		}
		
		if (in_array($_SESSION["userid"],$managers))
		{
			if ($view == "location")
					{echo "<li onclick=\"deleteShift('$shift_id','$shift_date')\">$phrase[146]</li>";}
					elseif  ($view == "custom") 
					{echo "<li onclick=\"deleteShift('$shift_id','$shift_date','$shift_userid','$shift_role')\">$phrase[146]</li>";}
		}
		
		echo "</ul><br>";
		
		
		
		
		
	}
	
	
	
	
	
	}
	
?>