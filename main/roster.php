<?php




include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$now = time();
$ip = ip("pc");

$integers[] = "content_id";
$integers[] = "location_id";
$integers[] = "page_id";



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
	
		
	
	
	if (!isset($ERROR))
	{
	
		
		
$sql = "select name from modules where m = '$m'";
	$DB->query($sql,"staff.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);	
		
	page_view($DB,$PREFERENCES,$m,"");	
	
	$datepicker = "yes";
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
	//	include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div style=\"padding:2em\">
	  	<h1>$modname</h1>";
	  	
	  	
	  	
	  	
	  	
if (isset($_REQUEST["view"])  && $_REQUEST["view"] == "custom")
		

{

 $location_id = $DB->escape($_REQUEST["location_id"]);	
 
 if (isset($_REQUEST["day"])) {$day = $_REQUEST["day"];	}
 else 
 {
 $day[0] = "on";
 $day[1] = "on";	
 $day[2] = "on";	
 $day[3] = "on";	
 $day[4] = "on";	
 $day[5] = "on";	
 $day[6] = "on";	
		
 }
 
 $sql = "select * from roster_locations where location_id = '$location_id'";
 $DB->query($sql,"rosteradmin.php");
$row = $DB->get();


	$location_name = $row["location_name"];

	//exit();
	//get list of dates
	
	if (isset($_REQUEST["showdays"]))
	{
	
	$showdays = $_REQUEST["showdays"];	
	} else {$showdays = 7;}
	if (isset($_REQUEST["startdate"]))
	{
	$startdate = $_REQUEST["startdate"];	
	
		if ($DATEFORMAT == "%d-%m-%Y")
             {
             $sd = substr($startdate,0,2);
             $sm = substr($startdate,3,2);
             $sy = substr($startdate,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $sd = substr($startdate,3,2);
         $sm = substr($startdate,0,2);
         $sy = substr($startdate,6,4);
             }	
	
      $today = mktime(0,0,0,$sm,$sd,$sy);
	}
	else 
	{
	$today = time();
	}

	//$test = date("%d-%m-%Y");
 $_DATEFORMAT=  str_replace("%","",$DATEFORMAT);
	
	//echo "test is $test";
	
	 $matches = array();
 while (count($matches) < $showdays )
 {
 	$weekday = date("w",$today);
 	
 	if (array_key_exists($weekday,$day) && $day[$weekday] == "on")
 	{
 	$matches[] = date("Ymd",$today);
 		
 	
 	if (count($matches) == 1) {$displaydate = date($_DATEFORMAT,$today);}
 	
 	}
 	//echo "<td>date</td>";
 	
 	$today = $today + 86400;
 	//$counter++;
 	//if ($counter > 100) {break;}
 }
	
//	print_r($matches);
	//exit();
	
	//get list of shifts
	$shifts_id = array();
	if (count($matches) > 0)
	{
	$start = $matches[0];
	$end = end($matches);
	$sql = "select * from roster_shifts,roster_types where shift_location = '$location_id' and type_id = shift_type and shift_date >= '$start' and shift_date <= '$end'";
	$DB->query($sql,"roster.php");
	//echo $sql;
	while ($row = $DB->get())
	{

	$shifts_id[] = $row["shift_id"];
	$shifts_date[] = $row["shift_date"];
	
	$shifts_available[] = $row["shift_available"];
	$shifts_published[] = $row["shift_published"];
	$shifts_userid[] = $row["userid"];
	$shifts_type[] = $row["shift_type"];
	$shifts_displaytime[] = format_time($row["type_sh"],$row["type_sm"]) . " - " . format_time($row["type_eh"],$row["type_em"]);
	}
	
	//print_r($shifts_id);
	
	reset($matches);
	}
	
	//get list of staff 
	$sql = "select * from user ";
	 $DB->query($sql,"roster.php");
	while ($row = $DB->get())
	{

	$_userid = $row["userid"];
	$first_names[$_userid] = $row["first_name"];
	$last_names[$_userid] = $row["last_name"];
	}
	
	
	
	//get list of managers for this location
	$managers = array();
	$sql = "select * from roster_managers where manager_location = '$location_id' ";
	 $DB->query($sql,"roster.php");
	while ($row = $DB->get())
	{

	$managers[] = $row["manager_userid"];
	
	}
	//print_r($managers);
	
	
	//get list of roles
		$sql = "select * from roster_roles ";
	 $DB->query($sql,"roster.php");
	while ($row = $DB->get())
	{

	$_roleid = $row["role_id"];
	$role_names[$_roleid] = $row["role_name"];
	}
	
	
	
	
	
	//print_r($last_names);
	$sql = "select * from roster_staff left join roster_rolebridge on roster_rolebridge.userid = roster_staff.staff_userid 
	where roster_staff.staff_location = '$location_id' order by role ";
	 $DB->query($sql,"roster.php");
	//echo $sql;
	while ($row = $DB->get())
	{

	$_staff[] = $row["staff_userid"];
	$_roles[] = $row["role"];
	$_keys[] = $row["staff_key"];
	}
	
	//print_r($day);
	
	
	include("../classes/Rosters.php");
	
 	$RosterShift = new RosterShift($m,$DB,$matches,$location_id,$phrase);
 	
	
	
	
	
 echo "<h2>$location_name</h2> <div class=\"hide\"> <a href=\"roster.php?m=$m\">$phrase[1051]</a><br><br>
 
 <form style=\"display:inline\" action=\"roster.php\" >
$phrase[267] <input type=\"text\" size=\"10\" id=\"startdate\" name=\"startdate\" readonly value=\"$displaydate\">
$phrase[1056] <select name=\"showdays\">";
 $counter = 1;
 while ($counter < 10 ) {echo "<option value=\"$counter\"";
 if ($counter == $showdays) { echo " selected";}
 echo ">$counter</option>"; $counter++;}
 echo "</select> &nbsp;
 Sun <input type=\"checkbox\" name=\"day[0]\" "; if (array_key_exists(0,$day) && $day[0] == "on") {echo "checked";} echo ">
 Mon <input type=\"checkbox\" name=\"day[1]\" "; if (array_key_exists(1,$day) && $day[1] == "on") {echo "checked";} echo ">
 Tue <input type=\"checkbox\" name=\"day[2]\" "; if (array_key_exists(2,$day) && $day[2] == "on") {echo "checked";} echo ">
 Wed <input type=\"checkbox\" name=\"day[3]\" "; if (array_key_exists(3,$day) && $day[3] == "on") {echo "checked";} echo ">
 Thu <input type=\"checkbox\" name=\"day[4]\" "; if (array_key_exists(4,$day) && $day[4] == "on") {echo "checked";} echo ">
 Fri <input type=\"checkbox\" name=\"day[5]\" "; if (array_key_exists(5,$day) && $day[5] == "on") {echo "checked";} echo ">
 Sat <input type=\"checkbox\" name=\"day[6]\" "; if (array_key_exists(6,$day) && $day[6] == "on") {echo "checked";} echo "> &nbsp; &nbsp;

 <input type=\"hidden\" name=\"m\" value=\"$m\">
 <input type=\"hidden\" name=\"view\" value=\"custom\">
  <input type=\"hidden\" name=\"location_id\" value=\"$location_id\">
 <input type=\"submit\" name=\"submit\" value=\"$phrase[1050]\">
 
 </form></div><br>
 
 
 <table class=\"colourtable\" style=\"background:white\"><tr><td>Staff</td>";
 
 foreach ($matches as $i => $date)
		{
		$dayname = strftime("%a %x", strtotime($date));
		echo "<td>$dayname</td>";
			
		}
		echo "</tr>";
		
	

		 foreach ($_staff as $index => $staff)
		{
			$rid = "";
		echo "
<tr><td style=\"white-space:nowrap;\"><b>$last_names[$staff], $first_names[$staff]</b>";
		if ($_roles[$index] != "")
			{
			$rid = $_roles[$index];
			echo "<br>$role_names[$rid]";
			}
		
		if ($_keys[$index] == 1) {echo " <img src=\"../images/key2.png\">";}
		echo "</td>
";	
			 foreach ($matches as $i => $date)
					{
					$dayname = strftime("%a %x", strtotime($date));
				$id = "t_" . $date . "_" . $staff;
					echo "<td ><div id=\"$id\">";
					
					$RosterShift->displayUser($staff,$date,$rid);
			
					echo "</div></td>
";
					}
		echo "</tr>";	
		}
		
		
			 foreach ($role_names as $rid => $rname)
		{
			$staff = "0";
		echo "<tr><td style=\"white-space:nowrap;\"><b>Available <br>
	$rname</b></td>";
					 foreach ($matches as $i => $date)
					{
					$dayname = strftime("%a %x", strtotime($date));
				$id = "t_" . $date . "_" . $staff . "_" . $rid;
					echo "<td ><div id=\"$id\">";
					
					$RosterShift->displayUser($staff,$date,$rid);
			
					echo "</div></td>
";
					}
		
		echo "</tr>";	
		}
				$staff = "0";
		echo "<tr><td style=\"white-space:nowrap;\"><b>Available <br>
	Any</b></td>";
					 foreach ($matches as $i => $date)
					{
					$dayname = strftime("%a %x", strtotime($date));
				$id = "t_" . $date . "_" . $staff . "_" ;
					echo "<td ><div id=\"$id\">";
					
					$RosterShift->displayUser($staff,$date,"");
			
					echo "</div></td>
";
					}
		
		echo "</tr>";
		
		
		
 echo "</table>
 
 
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
	
		datepicker('startdate');
		
		
		
	function showMenu (shiftid)
	{
	var id = 'menu_' + shiftid
	
	var url = 'ajax_roster.php?m=$m&shiftid=' + shiftid + '&event=showmenu&view=custom';
	//alert(url)
	updatePage(url,id);
	
	
	
	var element = document.getElementById(id);
	element.style.display= 'block';
	
	}	
	
	
	
	function showShift (date,staff,rid,location)
	{
	//alert('hello menu')
	var id = 'm_' + date + '_' + staff
	
	var url = 'ajax_roster.php?m=$m&date=' + date + '&userid=' + staff + '&rid=' + rid + '&location_id=' + location + '&event=showshift&view=custom';
	//alert(url)
	updatePage(url,id);
	
	
	
	var element = document.getElementById(id);
	element.style.display= 'block';
	
	}	
	
	
	
	
	function deleteShift (id,date,user,rid)
	{
	var menu = 'menu_' + id;
	hideMenu(menu);
	
	
	var url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=deleteshift' + '&view=custom';
	if (user == '0') 
	{var id = 't_' + date + '_' + user + '_' + rid;}
	else 
	{var id = 't_' + date + '_' + user;}
	updatePage(url,id);
	
	
	}
	
	
	function takeShift (id,date,user,rid)
	{
	var menu = 'menu_' + id;
	hideMenu(menu);
	
	var url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=takeshift' + '&view=custom';
	if (user == '0') 
	{var element = 't_' + date + '_' + user + '_' + rid;}
	else 
	{var element = 't_' + date + '_' + user;}
	//alert(url)
	//alert(id)
	updatePage(url,element);
	
	var me = $_SESSION[userid];
	element = 't_' + date + '_' + me;
	var url = 'ajax_roster.php?m=$m&shiftid=' + id + '&date=' + date + '&userid=' + me + '&event=displayuser' + '&view=custom&rid=' + rid;
	//alert(url);
	updatePage(url,element);
	
	}
	
	
	function makeAvailable (id,date,user)
	{
	var menu = 'menu_' + id;
	hideMenu(menu);
	
	var url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=makeavailable' + '&view=custom';
	//alert(url);
	//var id = 't_' + date + '_' + user;
	//alert(id)
	
	if (user == '0') 
	{var id = 't_' + date + '_' + user + '_' + rid;}
	else 
	{var id = 't_' + date + '_' + user;}
	
	
	updatePage(url,id);
	}
	
	
	
	
	function makeNotAvailable (id,date,user)
	{
	var menu = 'menu_' + id;
	hideMenu(menu);
	
	var url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=makenotavailable' + '&view=custom';
	//alert(url)
	//var id = 't_' + date + '_' + user;
	//alert(id)
	
		if (user == '0') 
	{var id = 't_' + date + '_' + user + '_' + rid;}
	else 
	{var id = 't_' + date + '_' + user;}
	
	
	updatePage(url,id);
	}
	
	
	
	
	function addShift (user,type_id,date,weekday,role,location)
	{
	
	
	var url = 'ajax_roster.php?m=$m&event=addshift&date=' + date + '&uid=' + user + '&type_id=' + type_id + '&role=' + role + '&location_id=' + location + '&view=custom';
	//alert(url)
	//var id = 't_' + date + '_' + user + '_' + role;
	
	if (user == '0') 
	{var id = 't_' + date + '_' + user + '_' + role;}
	else 
	{var id = 't_' + date + '_' + user;}
	
	//alert(id)
	updatePage(url,id);
	var id = 'm_' + date + '_' + user;
	hideMenu(id);
	
	}
	
		function hideMenu (id)
	{
	//alert(id)
	var element = document.getElementById(id);
	element.style.display= 'none';
	
	}
	
	</script>
 
 ";
 
 
 
}
	

	elseif (isset($_REQUEST["view"])  && $_REQUEST["view"] == "location")
		

{
	if (isset($_REQUEST["showdays"]))
	{
	
	$showdays = $_REQUEST["showdays"];	
	} else {$showdays = 7;}
	
	
	 if (isset($_REQUEST["day"])) {$day = $_REQUEST["day"];	}
 else 
 {
 $day[0] = "on";
 $day[1] = "on";	
 $day[2] = "on";	
 $day[3] = "on";	
 $day[4] = "on";	
 $day[5] = "on";	
 $day[6] = "on";	
		
 }
 
	
	
	
	if (isset($_REQUEST["startdate"]))
	{
	$startdate = $_REQUEST["startdate"];	
	
		if ($DATEFORMAT == "%d-%m-%Y")
             {
             $sd = substr($startdate,0,2);
             $sm = substr($startdate,3,2);
             $sy = substr($startdate,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $sd = substr($startdate,3,2);
         $sm = substr($startdate,0,2);
         $sy = substr($startdate,6,4);
             }	
	
      $today = mktime(0,0,0,$sm,$sd,$sy);
	}
	else 
	{
	$today = time();
	}
	
	if (isset($_REQUEST["week"]) && $_REQUEST["week"] == "previous")
	{
		$today = $today - (86400 * 7);
	}
	if (isset($_REQUEST["week"]) && $_REQUEST["week"] == "next")
	{
		$today = $today + (86400 * 7);
	}
	
 $_DATEFORMAT=  str_replace("%","",$DATEFORMAT);
	
	
	$weekday = date("w",$today);
	
	//echo "weekday is $weekday";
	
	$today = $today - (86400 * $weekday);
	
	if (isset($CALENDAR_START) && $CALENDAR_START =="MON") { $today = $today + 86400;}
	
	//echo date("D",$today);
	
	
	// $matches = array();
	// $counter = 0;
 	//while ($counter < 7)
 	//{ 
 //$matches[] = date("Ymd",$today);
 	//$matches_w[] = date("w",$today);
 	
 //	if (count($matches) == 1) {$displaydate = date($_DATEFORMAT,$today);}
 	
 	//$today = $today + 86400;
 	//$counter++;
 	//}
 	
 	
 	$matches = array();
 while (count($matches) < $showdays )
 {
 	$weekday = date("w",$today);
 	
 	if (array_key_exists($weekday,$day) && $day[$weekday] == "on")
 	{
 	$matches[] = date("Ymd",$today);
 	$matches_w[] = date("w",$today);	
 	
 	if (count($matches) == 1) {$displaydate = date($_DATEFORMAT,$today);}
 	
 	}
 	//echo "<td>date</td>";
 	
 	$today = $today + 86400;
 	//$counter++;
 	//if ($counter > 100) {break;}
 }
	
 	//print_r($matches);
 	include("../classes/Rosters.php");
	
 	$RosterShift = new RosterShift($m,$DB,$matches,$location_id,$phrase);
 	
 	 $sql = "select * from roster_locations where location_id = '$location_id'";
	// echo $sql;
 $DB->query($sql,"roster.php");
$row = $DB->get();
$location_name = $row["location_name"];
	
	
	echo "<h2>$location_name</h2>
	<div class=\"hide\"> <a href=\"roster.php?m=$m\" style=\"padding-right:1em\">$phrase[1051]</a> 
<br><br><form style=\"display:inline\" action=\"roster.php\" >$phrase[267]
<input type=\"text\" size=\"10\" id=\"startdate\" name=\"startdate\" readonly value=\"$displaydate\">
$phrase[1056] <select name=\"showdays\">";
 $counter = 1;
 while ($counter < 10 ) {echo "<option value=\"$counter\"";
 if ($counter == $showdays) { echo " selected";}
 echo ">$counter</option>"; $counter++;}
 echo "</select> &nbsp;

 Sun <input type=\"checkbox\" name=\"day[0]\" "; if (array_key_exists(0,$day) && $day[0] == "on") {echo "checked";} echo ">
 Mon <input type=\"checkbox\" name=\"day[1]\" "; if (array_key_exists(1,$day) && $day[1] == "on") {echo "checked";} echo ">
 Tue <input type=\"checkbox\" name=\"day[2]\" "; if (array_key_exists(2,$day) && $day[2] == "on") {echo "checked";} echo ">
 Wed <input type=\"checkbox\" name=\"day[3]\" "; if (array_key_exists(3,$day) && $day[3] == "on") {echo "checked";} echo ">
 Thu <input type=\"checkbox\" name=\"day[4]\" "; if (array_key_exists(4,$day) && $day[4] == "on") {echo "checked";} echo ">
 Fri <input type=\"checkbox\" name=\"day[5]\" "; if (array_key_exists(5,$day) && $day[5] == "on") {echo "checked";} echo ">
 Sat <input type=\"checkbox\" name=\"day[6]\" "; if (array_key_exists(6,$day) && $day[6] == "on") {echo "checked";} echo "> &nbsp; &nbsp;



 <input type=\"hidden\" name=\"m\" value=\"$m\">
 <input type=\"hidden\" name=\"view\" value=\"location\">
  <input type=\"hidden\" name=\"location_id\" value=\"$location_id\">
 <input type=\"submit\" name=\"submit\" value=\"$phrase[1050]\">

 
 </form>

	  </div><br>
	<table class=\"colourtable\" style=\"width:100%\"><tr>";
	
	 foreach ($matches as $i => $date)
		{
		
		
		
		$dayname = strftime("%a %x", strtotime($date));
				
					echo "<td>$dayname</td>";
			
		}
		echo "</tr><tr>";
		foreach ($matches as $i => $date)
		{
		$weekday = $matches_w[$i];
		echo "<td ><div id=\"cell_$date\">";
	    $RosterShift->displayShift($date);
		
		echo "</div></td>";
		
		}
		
		
	echo "</tr></table>
	

	<script>
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
	
		datepicker('startdate');
	
	function showMenu (shiftid)
	{
	var id = 'menu_' + shiftid
	
	var url = 'ajax_roster.php?m=$m&shiftid=' + shiftid + '&event=showmenu&view=location';
	updatePage(url,id);
	
	
	
	var element = document.getElementById(id);
	element.style.display= 'block';
	
	}
	
	
	function showStaff (type_id,date,location_id)
	{
	var id = 'addmenu_' + type_id
	
	var url = 'ajax_roster.php?m=$m&type_id=' + type_id + '&event=showstaff&location_id=' + location_id + '&date=' + date;
	//alert(url)
	updatePage(url,id);
	
	
	
	var element = document.getElementById(id);
	element.style.display= 'block';
	
	}
	
	
	
	function deleteShift (id,date)
	{
	var url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=deleteshift' + '&view=location';
	//alert(url)
	var id = 'cell_' + date;
	updatePage(url,id);
	}
	
	
	function takeShift (id,date)
	{
	var url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=takeshift' + '&view=location';
	var id = 'cell_' + date;
	updatePage(url,id);
	}
	
	
	function makeAvailable (id,date)
	{
	var url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=makeavailable' + '&view=location';
	//alert(url);
	var id = 'cell_' + date;
	updatePage(url,id);
	}
	
	function makeNotAvailable (id,date)
	{
	var url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=makenotavailable' + '&view=location';
	var id = 'cell_' + date;
	updatePage(url,id);
	}
	
	function addShift (userid,type_id,date,weekday,role,location)
	{
	
	
	var url = 'ajax_roster.php?m=$m&event=addshift&date=' + date + '&uid=' + userid + '&type_id=' + type_id + '&role=' + role + '&location_id=' + location + '&view=location';
	//alert(url)
	var id = 'cell_' + date;
	//alert(id)
	updatePage(url,id);
	
	
	
	}
	
	
	
	function hideMenu (id)
	{
	
	var element = document.getElementById(id);
	element.style.display= 'none';
	
	}
	</script>
	
	";
	
}



else
{
	
	//get list of roles
		$sql = "select * from roster_roles ";
	 $DB->query($sql,"roster.php");
	while ($row = $DB->get())
	{

	$_roleid = $row["role_id"];
	$role_names[$_roleid] = $row["role_name"];
	}
	
	
	
	
	  	//my shifts
	 echo " <div style=\"width:20%;float:left;padding:1em;margin:1em;border:solid 1px black\" class=\"accent\"><p style=\"font-size:1.6em;color:black\">$phrase[1049]</p><br><div id=\"myshifts\">";
	  	$today = date("Ymd");
	  	$sql = "select * from roster_shifts,roster_types, roster_locations where roster_locations.location_id = roster_shifts.shift_location
	  	and 	roster_types.type_id = roster_shifts.shift_type and
	  	 userid = '$_SESSION[userid]' and shift_date >= '$today' order by shift_date";
	  	$DB->query($sql,"roster.php");
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
	  	
		$role = "";
	  	$sql = "select role from roster_rolebridge where userid = '$_SESSION[userid]'";
	  	$DB->query($sql,"roster.php");
		$row = $DB->get();
		$role = $row["role"];
		
		
		
		if ($role == "") {$insert = "";}
		else 
		{
		$insert = " and (shift_role = '$role' or shift_role = '') ";	
		}
	  	
	  	//available shifts
	  	echo "</div></div><div style=\"width:20%;float:left;padding:1em;margin:1em;border:solid 1px black\" class=\"accent\"><p style=\"font-size:1.6em;color:black\">$phrase[1046]</p><br><div id=\"available\">
	  	";
	  	
	  	$sql = "select * from roster_shifts,roster_types, roster_locations where roster_locations.location_id = roster_shifts.shift_location
	  	and 	roster_types.type_id = roster_shifts.shift_type and
	  	 shift_available = '1'  $insert and userid != $_SESSION[userid] and shift_date >= '$today' order by shift_date";
	  	$DB->query($sql,"roster.php");
	//echo "$sql <br><br>";
	while ($row = $DB->get())
	{
	$location_name = $row["location_name"];
	$location_id = $row["location_id"];
	$shift_id = $row["shift_id"];
	$userid = $row["userid"];
	$shift_role = $row["shift_role"];
	$displaytime = format_time($row["type_sh"],$row["type_sm"]) . " - " . format_time($row["type_eh"],$row["type_em"]);
	$shiftdate = strftime("%a %d %b %Y",strtotime($row["shift_date"]));
	$startdate = date("d-m-Y",strtotime($row["shift_date"]));
	
	
	
	echo "<span onclick=\"takeShift('$shift_id')\">$shiftdate $displaytime<br>
	$location_name";
	if ($shift_role != "") {echo "<br>" . $role_names[$shift_role];}
	echo"</span> <br><br>";		
	

	}
	  	
	  	
	  	echo "</div></div><div style=\"width:30%;float:left;padding:1em;margin:1em;border:solid 1px black\" class=\"accent\"><p style=\"font-size:1.6em;color:black\">$phrase[179]</p><br>
	  	<table class=\"colourtable\">";
	  	$sql = "select * from roster_locations where m= '$m'";
	  	$DB->query($sql,"roster.php");
	//echo $sql;
	while ($row = $DB->get())
	{
	$location_id = $row["location_id"];
	$location_name = $row["location_name"];
	echo "<tr><td><b>$location_name</b></td></td><td><a href=\"roster.php?m=$m&amp;view=location&location_id=$location_id\">$phrase[1047]</a></td><td>
	<a href=\"roster.php?m=$m&amp;view=custom&location_id=$location_id\">$phrase[1048]</a></td></tr>
	
	";
	}
	  	
	  	echo "</table></div>
	  	
	  	<script>
	  	
	  	function makeAvailable(id)
	  	{
	  	url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=showMyShifts' + '&update=avail';
	  	updatePage(url,'myshifts');
	  		
	  	}
	  	
	  	function takeShift(id)
	  	{
	  	
	  	if (confirm('$phrase[1054]')) {

	  	url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=takeshift' + '&view=home';
	  	updatePage(url,'available');
	  	
	  	url = 'ajax_roster.php?m=$m&event=showMyShifts';
	  	updatePage(url,'myshifts');
} 
	  	
	  	
	  	
	  	}
	  	
	   	function makeNotAvailable(id)
	  	{
	  	url = 'ajax_roster.php?m=$m&shiftid=' + id + '&event=showMyShifts' + '&update=notavail';
	  	updatePage(url,'myshifts');
	  		
	  	}
	  	
	  	</script>
	  	
	  	
	  	
	  	";
	  	
}	  	
	  	
	  	
	  echo "</div>	";


include ("../includes/footer.php");
	
	}
	
	
	
	
	

?>