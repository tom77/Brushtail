<?php



if (!isset($DATEFORMAT)) {$DATEFORMAT = "%d-%m-%Y";}


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$now = time();
$ip = ip("pc");

$integers[] = "content_id";
$integers[] = "travel_id";
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
	
		$sql = "select * from modules where m = '$m'";
$DB->query($sql,"travel.php");
$row = $DB->get();
$modname = formattext($row["name"]);
$input = $row["input"];


	 $sql  = "select * from travel_distances where m = '$m' order by distance_name";	
	$DB->query($sql,"editform.php");
	$distances = array();
	while ($row = $DB->get())
	{
	$distance_name = $row["distance_name"];
	$distance_value = $row["distance_value"];

	$distances[$distance_name] = $distance_value;


	}	
	//print_r($distances);
	
$sql  = "select * from travel_options where m = '$m'";	

	$DB->query($sql,"editform.php");
	
	$row = $DB->get();

	//$mode = $row["mode"];
	//$break = $row["break"];
	$introduction = nl2br($row["introduction"]);
		

		
		$leaveuser = $_SESSION["userid"];
	
		//echo "leave user is $leaveuser";

		
		

		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addleave" )
		{		
		

		//print_r($_REQUEST);
		
		if (isset($_REQUEST["draft"])) {$status = '3';} else {$status = '0';}
	
		$startdate = $DB->escape($_POST["startdate"]);
		$enddate = $DB->escape($_POST["enddate"]);
		$leavetype = $DB->escape($_POST["leavetype"]);
		$comment = $DB->escape($_POST["comment"]);
		$registration = $DB->escape($_POST["registration"]);
		$trip = $_POST["trip"];
		$now = time();
		$nowdate = strftime("%x",$now);
		
		$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
		
		//print_r($_REQUEST);
		
		if ($_POST["total_kms"] == 0 || $_POST["total_kms"] == "")
		
		{
			$ERROR =  "Leave submission failed. No kms submitted";
		}
		
		

		
		elseif (preg_match ($pattern ,$startdate) &&  preg_match ($pattern ,$enddate))
{

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


if ($DATEFORMAT == "%d-%m-%Y")
             {
             $ed = substr($enddate,0,2);
             $em = substr($enddate,3,2);
             $ey = substr($enddate,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $ed = substr($enddate,3,2);
         $em = substr($enddate,0,2);
         $ey = substr($enddate,6,4);
             }

$startdate = "$sy-$sm-$sd";
$enddate = "$ey-$em-$ed";
		

		if (trim($comment) != "") {$_comment = $comment . "
<i>$_SESSION[username] $nowdate</i>
";} else {$_comment = "";}

$_comment = $DB->escape($_comment);		

		$total = 0;	
		
				if (isset($_REQUEST["kms"]))
		{
		$kms =  $_REQUEST["kms"];
		$date =  $_REQUEST["date"];
		
		
			$total_kms = 0;	
		
			foreach ($kms as $key => $value)
			{
				
			
			$total_kms = $total_kms + $value;
			}
		}
		
			if ($total > 0 ) {$total_kms = $total;} else {$total_kms = $DB->escape($_POST["total_kms"]);}
	
		$sql= "insert into travel_requests values(NULL,'$leaveuser','$leavetype','$registration','$status','$_SESSION[username]','$now','$m','$total_kms','$startdate','$enddate','$_comment','0')";
		//echo $sql;
		$DB->query($sql,"travel.php");
		//$row = $DB->get();
		
		$travel_id = $DB->last_insert();
		
		//echo "leaveid is $leaveid";
		
		
		
		//print_r($startminute);
		
		$dateslist = "";
		if (isset($kms))
		{
		foreach ($kms as $key => $value)
			{
			if ($value != "" || $date[$key] != "") //insert row
				{
		
				//$_hour = $DB->escape($value);	
				$_trip = $DB->escape($trip[$key]);	
				$_date = $DB->escape($date[$key]);	
				
				if ($DATEFORMAT == "%d-%m-%Y")
             {
				
				$_d = substr($_date,0,2); 
				$_m = substr($_date,3,2);
				$_y = substr($_date,6,4);
             }
             
              if ($DATEFORMAT == "%m-%d-%Y")
             {
             	$_d = substr($_date,3,2); 
				$_m = substr($_date,0,2);
				$_y = substr($_date,6,4);
             	
             }
				$_date = $DB->escape("$_y-$_m-$_d");
				
			
				
				//$_date = strrev($_date);
				$dayname = strftime("%a",mktime(0,0,0,$_m,$_d,$_y));
				
				$sql = "insert into travel_dates values('$value','$travel_id','$_date','$trip[$key]')";
				
			
				
				
				$dateslist .= "$dayname $date[$key] $trip[$key] $value $phrase[1074]  
";
				$DB->query($sql,"travel.php");
				
				}
			}
		}

		
		if ($status == 0)
		
		{
		
				$sql = "select first_name, last_name, cat_name from user, travel_requests, leave_category
			where travel_requests.travel_id = '$travel_id' and user.userid = travel_requests.user_id
			and leave_category.cat_id = travel_requests.travel_type";
			$DB->query($sql,"travel.php");
			$row = $DB->get();
			$first_name = $row["first_name"];
			$cat_name = $row["cat_name"];
			$last_name = $row["last_name"];
			
				if ($DATEFORMAT == "%d-%m-%Y")
             {
            $start = "$sd/$sm/$sy";
            $end = "$ed/$em/$ey";
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
        	$start = "$sm/$sd/$sy";
            $end = "$em/$ed/$ey";
             }	
			
			$message = "
$phrase[141]: $first_name $last_name
$phrase[1000] : $cat_name
$phrase[267]:  $start	
$phrase[268]: $end
$phrase[1076]: $total_kms								
";
			
if (trim($comment) != "") {$message .= "$phrase[135]: $_POST[comment]

";}
if (trim($dateslist) != "") {$message .= "$phrase[1001]: 
$dateslist
";} 
			
			
			
			
			
			
			
			
			
			
		
			
			
			//alert submission
			$sql = "select distinct user.userid,email from user, travel_members, travel_alertees
			where travel_members.userid = '$leaveuser' and travel_members.location_id = travel_alertees.location_id
			and travel_alertees.userid = user.userid";
			
			$DB->query($sql,"travel.php");
			while ($row = $DB->get())
			{
			$email = $row["email"];
			if (filter_var($email, FILTER_VALIDATE_EMAIL))
				{
				$emails[] = $email;	
				}
			}
			
			if (isset($emails))
			{
				$emailaddress = "";
				$counter= 0;
	
			foreach ($emails as $key => $value) {
			if ($counter != 0) {$emailaddress .= ",";}
			$emailaddress .= "$value";
			$counter++;
			}

			$sql = "select email from user
			where userid = '$_SESSION[userid]'";
			
			$DB->query($sql,"travel.php");
			$row = $DB->get();
			$fromemail = $row["email"];
			
			$headers = "From: $fromemail";
			
		
			
			//echo $sql;
			//submitted
			$subject = "$modname - $phrase[1063] #$travel_id";
			
		//echo $emailaddress;
				send_email($DB,$emailaddress, $subject, $message,$headers);				
			
			}
			
			
				//log submission
			$message = $DB->escape($message);	
			$ip = ip("pc");
			$sql = "insert into travel_log values(NULL,'$now','$_SESSION[username]','$ip','$travel_id','$message')";
			//echo $sql;
			$DB->query($sql,"travel.php");
			
			
		}
			
			
			
			} else { $ERROR =  "Travel submission failed. Invalid dates provided";}
		
			
		}
		
		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "cancel" )
		{
			
			$travel_id = $DB->escape($_REQUEST["travel_id"]);	
			
			
			$sql = "select approval_status from travel_requests where travel_id = '$travel_id'";
			
			$DB->query($sql,"travel.php");
			$row = $DB->get();
		
			$status = $row["approval_status"];
			
			
			
			
			
			
			$sql = "delete from travel_dates where travel_id = '$travel_id'";
			$DB->query($sql,"travel.php");
			
			$sql = "delete from travel_requests where travel_requests.m = '$m' and travel_id = '$travel_id'";
			$DB->query($sql,"travel.php");
			
			$sql = "delete from travel_log where travel_id = '$travel_id'";
			$DB->query($sql,"travel.php");
			
			
			
			if ($status == 0)
			{
			$message = "Application #$travel_id cancelled by $_SESSION[username]";
			
			
			$sql = "select distinct user.userid,email from user, travel_members, travel_alertees
			where travel_members.userid = '$leaveuser' and travel_members.location_id = travel_alertees.location_id
			and travel_alertees.userid = user.userid";
			
			$DB->query($sql,"travel.php");
			while ($row = $DB->get())
			{
			$email = $row["email"];
			if (filter_var($email, FILTER_VALIDATE_EMAIL))
				{
				$emails[] = $email;	
				}
			}
			
			if (isset($emails))
			{
				$emailaddress = "";
				$counter= 0;
	
			foreach ($emails as $key => $value) {
			if ($counter != 0) {$emailaddress .= ",";}
			$emailaddress .= "$value";
			$counter++;
			}

			$sql = "select email from user
			where userid = '$_SESSION[userid]'";
			
			$DB->query($sql,"travel.php");
			$row = $DB->get();
			$fromemail = $row["email"];
			
			$headers = "From: $fromemail";
			
		
			
			//echo $sql;
			//cancelled
			$subject = "$modname - $phrase[1065]  #$travel_id";
			
		//echo $emailaddress;
				send_email($DB,$emailaddress, $subject, $message,$headers);		
			}
			}
		}
			

		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updateleave" )
		{		
		
		//print_r($_REQUEST);	
			
		if (isset($_REQUEST["submitdraft"])) {$approvalstatus = '0';}
		elseif (isset($_REQUEST["updatedraft"])) {$approvalstatus = '3';}
		else {$approvalstatus = '0';}
		
		$travel_id = $DB->escape($_POST["travel_id"]);
	
		$startdate = $DB->escape($_POST["startdate"]);
		$enddate = $DB->escape($_POST["enddate"]);
		$leavetype = $DB->escape($_POST["leavetype"]);
		$comment = $DB->escape($_POST["comment"]);
		$registration = $DB->escape($_POST["registration"]);
		$trip = $_POST["trip"];
		
		$sql = "select m , user_id from travel_requests where travel_id = '$travel_id'";
		$DB->query($sql,"travelmanage.php");
		$row = $DB->get();
		$_m = $row["m"];
		$_user_id = $row["user_id"];
			
		if ($m != $_m || $_user_id != $_SESSION["userid"]) {
			//trying to update record from another module
			exit();
		}
	
	
		$now = time();
		$nowdate = strftime("%x",$now);
		
		
		$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
		
	



	if ($_POST["total_kms"] == 0 || $_POST["total_kms"] == "")
		
		{
			$ERROR =  "<br>Submission failed. No kms submitted";
		}


elseif (preg_match ($pattern ,$startdate) &&  preg_match ($pattern ,$enddate))
{
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


if ($DATEFORMAT == "%d-%m-%Y")
             {
             $ed = substr($enddate,0,2);
             $em = substr($enddate,3,2);
             $ey = substr($enddate,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $ed = substr($enddate,3,2);
         $em = substr($enddate,0,2);
         $ey = substr($enddate,6,4);
             }


		
		
		$startdate = "$sy-$sm-$sd";
		$enddate = "$ey-$em-$ed";
	
		$insert = "";
		
			if (trim($comment) != "") {$_comment =  "
$comment
<i>$_SESSION[username] $nowdate</i>
";} else {$_comment = "";}
			
			
			//echo "comment is $comment _comment is $_comment";
			
			$total = 0;		
			
			
				if (isset($_REQUEST["kms"]))
		{
		
		$kms =  $_REQUEST["kms"];
		$date =  $_REQUEST["date"];
		
			
		$total_kms =  0;
		
			foreach ($kms as $key => $value)
			{
				
		$total_kms = $total_kms + $value;
			}
			}
		
			
			
			
				if ($DB->type == "mysql")
			{
		$sql= "update travel_requests set travel_type = '$leavetype',registration = '$registration',approval_status = '$approvalstatus', totalkms = '$total_kms',startdate = '$startdate',enddate = '$enddate',comment = CONCAT(comment,'$_comment') where travel_id = $travel_id and user_id = '$leaveuser'";
			}
			
				else
                                {
		$sql= "update travel_requests set travel_type = '$leavetype',registration = '$registration',approval_status = '$approvalstatus',totalkms = '$total_kms',startdate = '$startdate',enddate = '$enddate',comment = comment || '$_comment' where travel_id = $travel_id and user_id = '$leaveuser'";
		
			}	
	
		
	
		
	//echo $sql;
		$DB->query($sql,"travel.php");
		$row = $DB->get();
		
		$dateslist = "";
		
		//echo "leaveid is $leaveid";
		
	
		
		$sql = "delete from travel_dates where travel_id = $travel_id";
		$DB->query($sql,"travel.php");
		$row = $DB->get();
		//print_r($startminute);
		
		if (isset($kms))
		{
		foreach ($kms as $key => $value)
			{
			if ($value != "" || $date[$key] != "") //insert row
				{
				
					if ($DATEFORMAT == "%d-%m-%Y")
             {
				
				$_d = substr($date[$key],0,2); 
				$_m = substr($date[$key],3,2);
				$_y = substr($date[$key],6,4);
             }
             
              if ($DATEFORMAT == "%m-%d-%Y")
             {
             	$_d = substr($date[$key],3,2); 
				$_m = substr($$date[$key],0,2);
				$_y = substr($date[$key],6,4);
             	
             }
             
				$_date =  $DB->escape("$_y-$_m-$_d");
			
				//$_hour = $DB->escape($value);	
			
				$dayname = strftime("%a",mktime(0,0,0,$_m,$_d,$_y));
				
	$sql = "insert into travel_dates values('$value','$travel_id','$_date','$trip[$key]')";
				
			
				
				
				$dateslist .= "$dayname $date[$key] $trip[$key] $value $phrase[1074]  
";
				//echo $sql;
				$DB->query($sql,"travel.php");
				
				}
			}
		}

		
			$status[0] = "$phrase[1006]";
			$status[1] = "$phrase[1007]";
			$status[2] = "$phrase[1008]";
			
		//	echo "approvalstatus is $approvalstatus";
			if ($approvalstatus == 0)
			{
			
				$sql = "select first_name, last_name, cat_name from user, travel_requests, travel_category
			where travel_requests.travel_id = '$travel_id' and user.userid = travel_requests.user_id
			and travel_category.cat_id = travel_requests.travel_type";
			$DB->query($sql,"travel.php");
			$row = $DB->get();
			$first_name = $row["first_name"];
			$cat_name = $row["cat_name"];
			$last_name = $row["last_name"];
			
	if ($DATEFORMAT == "%d-%m-%Y")
             {
            $start = "$sd/$sm/$sy";
            $end = "$ed/$em/$ey";
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
        	$start = "$sm/$sd/$sy";
            $end = "$em/$ed/$ey";
             }	
             
			$message = "
$phrase[141]: $first_name $last_name
$phrase[1000] : $cat_name
$phrase[267]:  $start	
$phrase[268]: $end
$phrase[1076]: $total_kms						
";
			
if (trim($comment) != "") {$message .= "$phrase[135]: $_POST[comment]

";}
if (trim($dateslist) != "") {$message .= "$phrase[1001]: 
$dateslist
";} 
			
	
			
			
			//alert submission
				//alert submission
			$sql = "select distinct user.userid,email from user, travel_members, travel_alertees
			where travel_members.userid = '$leaveuser' and travel_members.location_id = travel_alertees.location_id
			and travel_alertees.userid = user.userid";
			//echo $sql;
			
		//	$sql = "select email from user where userid = '2235'";
			
			
			$DB->query($sql,"travel.php");
			while ($row = $DB->get())
			{
			$email = $row["email"];
			if (filter_var($email, FILTER_VALIDATE_EMAIL))
				{
				$emails[] = $email;	
				}
			}
			
			if (isset($emails))
			{
				$emailaddress = "";
				$counter= 0;
	
			foreach ($emails as $key => $value) {
			if ($counter != 0) {$emailaddress .= ",";}
			$emailaddress .= "$value";
			$counter++;
			}

			
			
			
			$sql = "select email from user
			where userid = '$_SESSION[userid]'";
			
			$DB->query($sql,"travel.php");
			$row = $DB->get();
			$fromemail = $row["email"];
			
			$headers = "From: $fromemail";
			//updated
			if (isset($_REQUEST["submitdraft"])) {
				$subject = "$modname - $phrase[1063]  #$travel_id";
			}
			
			else 
			{
			$subject = "$modname - $phrase[1064]  #$travel_id";
			}
	
		//echo $message;
	send_email($DB,$emailaddress, $subject, $message,$headers);				
			
			
			
			
			
			
			
				//log submission
			
			$ip = ip("pc");
			$message = $DB->escape($message);
			$sql = "insert into travel_log values(NULL,'$now','$_SESSION[username]','$ip','$travel_id','$message')";
			
			$DB->query($sql,"travel.php");
			}
			}
			
			
			
} else {$ERROR =  "Travel update failed. Invalid dates provided";}
			
		}
		
		
		
		
		
		
		
		
		
		
		
		
		


$datepicker = "yes";

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
			page_view($DB,$PREFERENCES,$m,"");


		
			 
	  	include("../includes/leftsidebar.php");   
		
		
	 
		
			

	  	echo "<div  style=\"float:left;	width:78%;	padding: 1em 0;	margin:0;\" class=\"leave1\"><div>";
	





echo " <h1>$modname</h1>";  


	if ($introduction != "") {echo "<p>$introduction</p><br>";}	
	
		


			if (isset($_REQUEST["filter"])) {$filter = $_REQUEST["filter"];}
	
			if (isset($_REQUEST["year"])) {$year = $DB->escape($_REQUEST["year"]);} else {$year = date("Y");}
			
			//list leave
			
			
			//check if manager
				
		$managers = array();
	
		$sql = "select userid, authority from travel_managers where userid = '$_SESSION[userid]'";

		$authority = 0;
		$DB->query($sql,"travel.php");
		while ($row = $DB->get())
		{
			$managers[] = $row["userid"];
			
			if ($row["authority"] > $authority) {$authority = $row["authority"];}
		}
	
		
		
	
		echo "<span class=\"leave1\">";
		
			
			
			if (isset($_REQUEST["event"]) && $_REQUEST["event"]== "apply")
			{
				echo "<a href=\"travel.php?m=$m&leaveuser=$leaveuser\">$phrase[998]</a> | <a href=\"travel.php?m=$m&view=cal&leaveuser=$leaveuser\">$phrase[999]</a>";
			}
			elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "view")
			{
				echo "<a href=\"travel.php?m=$m&event=apply\">$phrase[994]</a> | <a href=\"travel.php?m=$m&leaveuser=$leaveuser\">$phrase[998]</a> | <a href=\"travel.php?m=$m&view=cal&leaveuser=$leaveuser\">$phrase[999]</a>";
			}
			elseif (isset($_REQUEST["view"]) && $_REQUEST["view"] == "cal")
			{
				echo "<a href=\"travel.php?m=$m&event=apply\">$phrase[994]</a> |  <a href=\"travel.php?m=$m&leaveuser=$leaveuser\">$phrase[998]</a>";
			}
			else
			{
				echo "<a href=\"travel.php?m=$m&event=apply\">$phrase[994]</a> | <a href=\"travel.php?m=$m&view=cal&leaveuser=$leaveuser\">$phrase[999]</a>";
			}
			echo "</span><br><br>";
			
			
			if (in_array($_SESSION["userid"],$managers))
			
		{
		
				echo "<span class=\"leave2\">";
			//if ($authority > 1) {echo "<a href=\"travelmanage.php?m=$m&event=apply&leaveuser=$leaveuser\">$phrase[1011]</a> | ";}
		
					
			echo "<a href=\"travelmanage.php?m=$m&filter=people";
			if (isset($year)) { echo "&year=$year";}
			echo "\">People</a> | <a href=\"travelmanage.php?m=$m&status=0";
			if (isset($year)) { echo "&year=$year";}
			echo "\">$phrase[1006]</a> | <a href=\"travelmanage.php?m=$m&status=1";
			if (isset($year)) { echo "&year=$year";}
			echo "\">$phrase[1007]</a> | <a href=\"travelmanage.php?m=$m&status=2";
			if (isset($year)) { echo "&year=$year";}
			echo "\">$phrase[1008]</a>  | <a href=\"travelmanage.php?m=$m&filter=cat";
			if (isset($year)) { echo "&year=$year";}
			echo "\">Categories</a>  | <a href=\"travelmanage.php?m=$m&filter=location";
			if (isset($year)) { echo "&year=$year";}
			echo "\">Groups</a> </span>";
		}



		if (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "apply"))
		{
			

			
			//$sql = "select mode from leave_options where m = '$m' ";
			
			//$DB->query($sql,"travel.php");
			///$row = $DB->get();
			
			//$mode = $row["mode"];
		
		
			
		
			
			
			
		//	print_r($_SESSION);
			
			$sql = "select * from user where userid = $leaveuser";
			//echo $sql;
			$DB->query($sql,"travel.php");
			$row = $DB->get();
		
			$first_name = $row["first_name"];
			$last_name = $row["last_name"];
			
			
			echo "
		<h2>$phrase[1011]</h2>
			<form action=\"travel.php\" method=\"post\"><br>
		<b >$phrase[141]</b><br> ";
			

			
				
				$sql = "select * from user where userid = '$_SESSION[userid]'";
				$DB->query($sql,"travel.php");
					$row = $DB->get();
					
					$first_name= $row["first_name"];
					$last_name= $row["last_name"];
					
					$userid= $row["userid"];
					
					
					echo "<span >$first_name $last_name</span <input type=\"hidden\" name=\"leaveuser\" value=\"$userid\">";
			
			
			echo "
		<br><br>
			<b>$phrase[1000] </b><br>
			<select name=\"leavetype\">";
			$sql = "select * from travel_category where m = '$m' and status = '1' order by position";
			//echo $sql;
			$DB->query($sql,"travel.php");
			while ($row = $DB->get())
			{
			$cat_name = $row["cat_name"];
			$cat_id = $row["cat_id"];
			echo "<option value=\"$cat_id\"";
			if (isset($travel_type) && $travel_type == $cat_id) {echo " selected";}
			echo ">$cat_name</option>";
			}
			
			
			$days[1] = "Monday";
			$days[2] = "Tuesday";
			$days[3] = "Wednesday";
			$days[4] = "Thursday";
			$days[5] = "Friday";
			$days[6] = "Saturday";
			
			
			
			
			
			echo "</select><br><br>
		<b>	$phrase[1077]</b><br><input type=\"text\" name=\"registration\" id=\"registration\" onkeyup=\"disableCheck()\">
			<br><br>
			
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
	
	</script>
			
			";
			
		
			
			
			//if ($mode == 1)
			//{
			

					
			//}
				//mode1 = simple view, mode2 detailed view
			
			//if ($mode == 1)
			//{
			
			$rowcounter = 1;
			$weekcounter = 1;
			
			echo "<br>
			
		
			<span onclick=\"addday();return false;\"><img src=\"../images/add.png\" alt=\"$phrase[176]\" title=\"$phrase[176]\">$phrase[1095] </span><br><br>
		
			<table class=\"colourtable\" id=\"table\">
			<tr ><td style=\"width:10em\">$phrase[182]</td><td >$phrase[217]</td><td>$phrase[1075]</td><td>$phrase[1074]</td><td></td></tr>
			</table>
			<input type=\"hidden\" id=\"rowcount\" name=\"rowcount\" value=\"0\">
			";
		
			
			echo "
			
			<script type=\"text/javascript\">
			
			var distances = Array(); 
			";
	
			foreach ($distances as $key => $value)
			{
			echo "distances['$key'] = $value
			";
			}
			
			echo "
			
			function trim(stringToTrim) 
				{
				return stringToTrim.replace(/^\s+|\s+$/g,'');
				}
			
			function disableCheck()
			{
		//	alert('hello check');
			var comments = trim(document.getElementById('comments').value);
			var registration = trim(document.getElementById('registration').value);
			var total_kms = document.getElementById('total_kms').value;
			
			if (comments == '' || registration == '' || (total_kms == 0 || total_kms == ''))
			{
			document.getElementById('submit1').disabled = true;
			document.getElementById('submit2').disabled = true;
			}
			else
			{
			document.getElementById('submit1').disabled = false;
			document.getElementById('submit2').disabled = false;
			}
			}
			
			
			function addday()
			{
			var rowcount = Number(document.getElementById('rowcount').value) + 1;
			
			var table = document.getElementById('table');
			var newRow = table.insertRow(table.rows.length);
			
			var cellDay = newRow.insertCell(0);
			cellDay.innerHTML = '<span id=\"day_' + rowcount + '\"></span>';
			
			
			var cellDate = newRow.insertCell(1);
			
			
			var rid = 'r' + rowcount;
			newRow.id= rid; //updates row id
			var dateid = 'date_' + rowcount;
			cellDate.innerHTML = '<input type=\"text\"  id=\"' + dateid + '\" name=\"date[' + rowcount + ']\" size=\"10\" readonly>'
			cellDate.onclick=check_leave
		
			
				datepicker(dateid);
			
			var cellStart = newRow.insertCell(2);
			cellStart.innerHTML = '<select id=\"trip_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\" name=\"trip[' + rowcount + ']\"><option value=\"\">$phrase[1078] </option>";
			
			foreach ($distances as $key => $value)
			{
			echo "<option value=\"$key\">$key</option>";
			}
	
			
			echo "</select>'     ;
			
			var cellkms = newRow.insertCell(3);
			cellkms.innerHTML = '<input type=\"text\"  class=\"kms\" onkeyup=\"check_leave()\" id=\"kms_' + rowcount + '\" name=\"kms[' + rowcount + ']\" size=\"5\"> ';
			
			var cellDelete = newRow.insertCell(4);
			cellDelete.innerHTML = '<span onclick=\"deleterow(\'' + rid + '\'); return false;\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></span>';
			
			document.getElementById('rowcount').value = rowcount;
			}
			
		
			
			
			
			
			function calc(id)
			{
			var element;
			var sel;
			
			
			element = 'trip_' + id;
			sel = document.getElementById(element);
			var trip = sel.options[sel.selectedIndex].value;
		
			var tripname;
			
			if( trip in distances) 
			{
			var milage = distances[trip]
			
			var kms = document.getElementById('kms_' + id);
			kms.value = milage;
			
			check_leave()
			}
			}
			
			function deleterow(id)
			{
//alert(id);
			var table = document.getElementById('table');
			var rows = document.getElementsByTagName('tr');	
			//alert('length is ' + rows.length)
			var counter = 0;
			for (var i = 0; i < rows.length; i++) 
			{
			//alert('row id is ' + rows[i].id )   
 			if (rows[i].id ==  id)
 			{table.deleteRow(counter); }
	
			counter++;	
			}
			check_leave();
			}
			
			
			function checkkms (value)
			{
			if (value.length == 1)
			{
			testRegExp = /[0-9]/i;
			}
			if (value.length == 2)
			{
			testRegExp = /[0-9][0-9]/i;
			}
			if (value.length == 3)
			{
			testRegExp = /^[0-9][0-9][0-9]$/i;
			}
			if (value.length == 4)
			{
			testRegExp = /^[0-9][0-9][0-9][0-9]$/i;
			}
			if (testRegExp.test(value)) {return true;} else {return false;}
			
			
			
			}
			
			function checkdate (value)
			{
		
			var year = value.substring(6,10);
			var month= value.substring(3,5);
			var day= value.substring(0,2);
			var string = 'value is ' + value + 'day is ' + day + ' month is ' + month + ' year is ' + year;
			//alert(string);
			
			
			var d = new Date(year, month, day);
			//alert(d.getUTCDay());
			}
			
			
			function check_leave()
			{
		
			
			var total_kms = 0 ;
		
			
			var startdate = '';
			var enddate = '';
			
		
			
			var rows = document.getElementsByTagName('tr');	
		
		
			for (var i = 0; i < rows.length; i++) 
			{
			
			var rowid = rows[i].id;
	
			if (rowid == '') {continue;}
		
			var id1 = 'kms_' + rowid.substr(1);
		
			var km = document.getElementById(id1);
			
			var id2 = 'date_' + rowid.substr(1);
			
			var date = document.getElementById(id2);
			
			if (km.value != '') 
				{	
				
				
				if (checkkms (km.value)) 
					{
				
				km.style.backgroundColor = 'white';
			
				
				total_kms = Number(total_kms) + Number(km.value)		
				
			
				
				
					} 
				else 
					{
				km.style.backgroundColor = '#FF593F';
			
					}
				
				}
			else //km value blank
				{
			if (date.value != '') {	km.style.backgroundColor = '#FF593F';} else {km.style.backgroundColor = 'white';}
		
				}
		
		
			var dayid = 'day_' + rowid.substr(1);
			var datespan = document.getElementById(dayid)	
				
				
				
			if (date.value == '' && km.value != '')  
				{
				date.style.backgroundColor = '#FF593F'; 
				} else 
				{
				date.style.backgroundColor = 'white'
				
					
				}
			
			
				if (date.value.length == 10) 
					{
					
					
					var year = date.value.substring(6,10);
					var month= date.value.substring(3,5);
					var day= date.value.substring(0,2);
					var today=new Date(year,month - 1,day)
					var thisDay=today.getDay()
					var myDays=[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\"]
				
					datespan.innerHTML = myDays[thisDay]
					
					} else {datespan.innerHTML = '';}
		
	
			
			if (date.value != '') {enddate = date.value;}
			if (date.value != '' && startdate == '') {startdate = date.value;}
			
			} //LOOPING THROUGH TABLE ROWS
			
			var firstday_display = document.getElementById('startdate');
			firstday_display.value =  startdate ;
			firstday_display.readOnly = true;
			
			var lastday_display = document.getElementById('enddate');
			lastday_display.value =  enddate ;
			lastday_display.readOnly = true;
		
			

			var totalkms_display = document.getElementById('total_kms');
			totalkms_display.value =  total_kms;
			totalkms_display.readOnly = true;
			
		
			disableCheck()
			
			//end check_leave
			}
			
			
		//	var inputs = document.getElementsByTagName('input');
			// for (var i = 0; i < inputs.length; i++) {
		//var theInput = inputs[i];
		///if (theInput.className == 'kms')
			// {
		//	theInput.onkeyup=check_leave;
		//	}
			// }
			
		
			addday()
			</script>";
			
			//} //end detailed mode2
			
			
						echo "
			
			<br><br>

			
			<div class=\"leavepicker\"><span style=\"width:10em;display: inline-block;\">$phrase[267]</span><input type=\"text\" size=\"10\" id=\"startdate\" name=\"startdate\" readonly";
			if (isset($startdate)) {echo " value=\"$startdate\"";}
			//if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo "></div>
			<span style=\"width:10em;display: inline-block;\">$phrase[268]</span><input type=\"text\" size=\"10\" name=\"enddate\" id=\"enddate\" readonly";
			if (isset($enddate)) {echo " value=\"$enddate\"";}
			//if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo "><br>
			<span style=\"width:10em;display: inline-block;\">$phrase[1076]</span><input type=\"text\" name=\"total_kms\" id=\"total_kms\"";
			if (isset($total_kms)) {echo " value=\"$total_kms\"";}
			//if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo " readonly> 
			
			
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
	
	
	
	
	</script>
				
			";
			
			
			echo "	<br><br>
		<b>	$phrase[135]</b><br>
			<textarea name=\"comment\" cols=\"60\" rows=\"6\" id=\"comments\" onkeyup=\"disableCheck()\">";
			if (isset($comment) && $comment != '') {echo $comment;}
			echo "</textarea>
					
			
		<br><br>
			<input type=\"hidden\" name=\"leaveuser\" value=\"$leaveuser\">
			<input type=\"hidden\" name=\"m\" value=\"$m\">";
			
			if ($_REQUEST["event"] == "apply")
			{
			echo "<input type=\"hidden\" name=\"update\" value=\"addleave\">
			<input type=\"submit\" value=\"Submit\" disabled=\"disabled\" id=\"submit1\"> <input type=\"submit\" name=\"draft\" value=\"Save as draft\" disabled=\"disabled\" id=\"submit2\">";	
			
			}
		
			echo "</form>
			
			
			
			
			
			";
			
		}
				elseif (isset($ERROR))
		{
		
		echo "<h1 style=\"color:#FF593F\">$ERROR</h1>";	
			
		}
	elseif (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "view"))
		{
			
			
			
			//$sql = "select mode from leave_options where m = '$m' ";
			
			//$DB->query($sql,"travel.php");
			//$row = $DB->get();
			
			//$mode = $row["mode"];
		
			
			$travel_id = $DB->escape($_REQUEST["travel_id"]);		
		
		
			
			$sql = "select * from travel_requests where user_id = '$leaveuser' and travel_id = '$travel_id' ";
			//echo $sql;
			$DB->query($sql,"travel.php");
			$row = $DB->get();
			
			$startdate = $row["startdate"];
			$sy = substr($startdate,0,4);
			$sm = substr($startdate,5,2);
			$sd = substr($startdate,8,4);
		//	$startdate = $sd . "-"  . $sm . "-" . $sy;
			$enddate = $row["enddate"];
			$ey = substr($enddate,0,4);
			$em = substr($enddate,5,2);
			$ed = substr($enddate,8,4);
			//$enddate = $ed . "-"  . $em . "-" . $ey;
			
			
	if ($DATEFORMAT == "%d-%m-%Y")
             {
            $startdate = $sd . "-"  . $sm . "-" . $sy;
            $enddate = $ed . "-"  . $em . "-" . $ey;
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
        	$startdate = $sm . "-"  . $sd . "-" . $sy;
            $enddate = $em . "-"  . $ed . "-" . $ey;
             }	
			
			
			
			$total_kms = $row["totalkms"];
			$approval_status = $row["approval_status"];
			$travel_type = $row["travel_type"];
		
			$comment = $row["comment"];	
			$processed = $row["processed"];	
			$registration = $row["registration"];	
			
			
				$sql = "select * from user where userid = $leaveuser";
			$DB->query($sql,"travel.php");
			$row = $DB->get();
		
			$first_name = $row["first_name"];
			$last_name = $row["last_name"];
			$payroll_number = trim($row["payroll_number"]);
		
				
				echo "<br><br>
		<b>$phrase[1066] #</b> $travel_id<br><br>
				<b>$phrase[401]</b><br>	<form action=\"travel.php\" method=\"post\">";
				
				
				
				
			if ($approval_status == 3) {echo "<span  class=\"partoption\">$phrase[1071]</span>";} //draft
			if ($approval_status == 0) {echo "<span  class=\"partoption\">$phrase[1006]</span>";} //pending
			if ($approval_status == 1) {echo "<span class=\"yesoption\">$phrase[1007]</span>";} //approved
			if ($approval_status == 2) {echo "<span class=\"nooption\">$phrase[1008]</span>";} //rejected
			
				
			echo "<br><br>";
				
			
		
			
		//	print_r($_SESSION);
			
		
			
			echo "
		
		
			<b>$phrase[141]</b><br>
			$first_name $last_name<br><br>
			<b>$phrase[1004]</b><br>
			$payroll_number
			<br><br>
			<b>$phrase[1000] </b> <br>
			<select name=\"leavetype\">";
			$sql = "select * from travel_category where m = '$m' and status = '1' order by position";
			//echo $sql;
			$DB->query($sql,"travel.php");
			while ($row = $DB->get())
			{
			$cat_name = $row["cat_name"];
			$cat_id = $row["cat_id"];
			echo "<option value=\"$cat_id\"";
			if (isset($travel_type) && $travel_type == $cat_id) {echo " selected";}
			echo ">$cat_name</option>";
			}
			
			
			$days[1] = "Monday";
			$days[2] = "Tuesday";
			$days[3] = "Wednesday";
			$days[4] = "Thursday";
			$days[5] = "Friday";
			$days[6] = "Saturday";
			
			
			
			
			
			echo "</select>
			<br><br>  
			<b>$phrase[1077]</b><br>
			<input type=\"text\" name=\"registration\" value=\"$registration\" id=\"registration\" onkeyup=\"disableCheck()\"><br><br>
					
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
	
	</script>
			
			
			
			";
			
			//mode1 = simple view, mode2 detailed view
			
			
			
			
			//echo "<b>First day </b><span id=\"firstday\"><input type=\"hidden\" name=\"startdate\" value=\"\"></span><br>
			//<b>Last day</b> <span id=\"lastday\"><input type=\"hidden\" name=\"enddate\" value=\"\"></span><br>
			//<b>$phrase[1076]</b> <span id=\"totalkms\"><input type=\"hidden\" name=\"total_kms\" value=\"\"></span>
			
			//";
			$rowcounter = 1;
			$weekcounter = 1;
			
			echo "<br>
			
			<br><br>
			<span onclick=\"addday();return false;\"><img src=\"../images/add.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\">$phrase[1095] </span><br><br>
		
			<table class=\"colourtable\" id=\"table\">
			<tr ><td style=\"width:10em\">$phrase[182]</td><td >$phrase[217]</td><td>$phrase[1075]</td><td>$phrase[1074]</td><td></td></tr>";
			
			
			$sql = "select * from travel_dates where travel_id = '$travel_id' order by travel_date";
			//echo $sql;
			$DB->query($sql,"travel.php");
			$counter = 0;
			while ($row = $DB->get())
			{
			
			$date = $row["travel_date"];
			$_date = $row["travel_date"];
			$_journey = $row["journey"];
			$_kms = $row["kms"];
		
			echo "<tr id=\"r$counter\"><td style=\"width:10em\">";
			//$_d= substr ($date, 0,2 );
			//$_m= substr ($date, 3,2 );
			//$_y= substr ($date, 6,4 );
			
			
			$_y = substr($date,0,4);
			$_m = substr($date,5,2);
			$_d = substr($date,8,4);
			
			if ($DATEFORMAT == "%d-%m-%Y")
             {
			$date = $_d . "-"  . $_m . "-" . $_y;
             }
             
              if ($DATEFORMAT == "%m-%d-%Y")
             {
             $date = $_m . "-"  . $_d . "-" . $_y;
             }
			
			
			$timestamp = mktime(1,0,0,$_m,$_d,$_y);
			$dayname = strftime("%A",$timestamp);
		

			
			echo "<span id=\"day_$counter\">$dayname</span></td><td onclick=\"check_leave()\"><input type=\"text\" size=\"10\" id=\"date_$counter\" name=\"date[$counter]\" size=\"10\" value=\"$date\"";
			//if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo "  readonly></td>
			<td><select name=\"trip[$counter]\" id=\"trip_$counter\" onclick=\"calc('$counter')\"><option value=\"\"> </option>";
			
			foreach ($distances as $key => $value)
			{
			echo "<option value=\"$key\"";
			if ($_journey == $key) {echo "selected";}
			echo ">$key</option>";
			}
	
			
			echo "
			
		</select></td><td> <input type=\"text\" name=\"kms[$counter]\"  id=\"kms_$counter\" onclick=\"calc('$counter')\" value=\"$_kms\" size=\"3\">
			
			</td>
			
			<td>";
			if (isset($approval_status) && ($approval_status == 1 ||$approval_status == 2)) {} else {
			echo "<span onclick=\"deleterow('r$counter'); return false;\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></span>";}
			
			echo "</td></tr>";
			$counter++;
			}
			
			
			
			
			echo "</table>
			<input type=\"hidden\" id=\"rowcount\" name=\"rowcount\" value=\"$counter\">
			";
		
			$c = 0;
			
			
			echo "
			
			<script type=\"text/javascript\">
			
			var distances = Array(); 
			";
	
			foreach ($distances as $key => $value)
			{
			echo "distances['$key'] = $value
			";
			}
			
			echo "

	";
	
	while ($c < $counter)
			{
			
				echo "datepicker('date_$c')
				";
			$c++;
			}
	
	echo "
	
	
	
	function trim(stringToTrim) 
				{
				return stringToTrim.replace(/^\s+|\s+$/g,'');
				}
			
			function disableCheck()
			{
		//	alert('hello check');
			var comments = trim(document.getElementById('comments').value);
			var registration = trim(document.getElementById('registration').value);
			var total_kms = document.getElementById('total_kms').value;
			
			if (comments == '' || registration == '' || (total_kms == 0 || total_kms == ''))
			{
			if ( document.getElementById('submit1')) {document.getElementById('submit1').disabled = true;}
			if (document.getElementById('submit2')) { document.getElementById('submit2').disabled = true;}
			}
			else
			{
			if (document.getElementById('submit1')) {document.getElementById('submit1').disabled = false;}
			if (document.getElementById('submit2')) {document.getElementById('submit2').disabled = false;}
			}
			}
	
	
	
	
					function addday()
			{
			var rowcount = Number(document.getElementById('rowcount').value) + 1;
			
			var table = document.getElementById('table');
			var newRow = table.insertRow(table.rows.length);
			
			var cellDay = newRow.insertCell(0);
			cellDay.innerHTML = '<span id=\"day_' + rowcount + '\"></span>';
			
			
			var cellDate = newRow.insertCell(1);
			
			
			var rid = 'r' + rowcount;
			newRow.id= rid; //updates row id
			var dateid = 'date_' + rowcount;
			cellDate.innerHTML = '<input type=\"text\"  id=\"' + dateid + '\" name=\"date[' + rowcount + ']\" size=\"10\" readonly>'
			cellDate.onclick=check_leave
		
			
				datepicker(dateid);
			
			var cellStart = newRow.insertCell(2);
			cellStart.innerHTML = '<select id=\"trip_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\" name=\"trip[' + rowcount + ']\"><option value=\"\"> </option>";
			
			foreach ($distances as $key => $value)
			{
			echo "<option value=\"$key\">$key</option>";
			}
	
			
			echo "</select>'     ;
			
			var cellkms = newRow.insertCell(3);
			cellkms.innerHTML = '<input type=\"text\"  class=\"kms\" onkeyup=\"check_leave()\" id=\"kms_' + rowcount + '\" name=\"kms[' + rowcount + ']\" size=\"5\"> ';
			
			var cellDelete = newRow.insertCell(4);
			cellDelete.innerHTML = '<span onclick=\"deleterow(\'' + rid + '\'); return false;\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></span>';
			
			document.getElementById('rowcount').value = rowcount;
			}
			
			
			
		
			
			
			function calc(id)
			{
			
			var element;
			var sel;
			
			
			element = 'trip_' + id;
			sel = document.getElementById(element);
			var trip = sel.options[sel.selectedIndex].value;
		//alert (trip);
		
			
			if( trip in distances) 
			{
			var milage = distances[trip]
			
			var kms = document.getElementById('kms_' + id);
			kms.value = milage;
			
			check_leave()
			}
			}
			
			
			
			
			
			function deleterow(id)
			{
//alert(id);
			var table = document.getElementById('table');
			var rows = document.getElementsByTagName('tr');	
			//alert('length is ' + rows.length)
			var counter = 0;
			for (var i = 0; i < rows.length; i++) 
			{
			//alert('row id is ' + rows[i].id )   
 			if (rows[i].id ==  id)
 			{table.deleteRow(counter); }
	
			counter++;	
			}
			check_leave();
			}
			
			
			
			
			function checkkms (value)
			{
			if (value.length == 1)
			{
			testRegExp = /[0-9]/i;
			}
			if (value.length == 2)
			{
			testRegExp = /[0-9][0-9]/i;
			}
			if (value.length == 3)
			{
			testRegExp = /^[0-9][0-9][0-9]$/i;
			}
			if (value.length == 4)
			{
			testRegExp = /^[0-9][0-9][0-9][0-9]$/i;
			}
			if (testRegExp.test(value)) {return true;} else {return false;}
			
			
			
			}
			
			
			
			
			function checkdate (value)
			{
		
			var year = value.substring(6,10);
			var month= value.substring(3,5);
			var day= value.substring(0,2);
			var string = 'value is ' + value + 'day is ' + day + ' month is ' + month + ' year is ' + year;
			alert(string);
			
			
			var d = new Date(year, month, day);
			alert(d.getUTCDay());
			}
			
			
		function check_leave()
			{
		
			
			var total_kms = 0 ;
		
			
			var startdate = '';
			var enddate = '';
			
		
			
			var rows = document.getElementsByTagName('tr');	
		
		
			for (var i = 0; i < rows.length; i++) 
			{
			
			var rowid = rows[i].id;
	
			if (rowid == '') {continue;}
		
			var id1 = 'kms_' + rowid.substr(1);
		
			var km = document.getElementById(id1);
			
			var id2 = 'date_' + rowid.substr(1);
			
			var date = document.getElementById(id2);
			
			if (km.value != '') 
				{	
				
				
				if (checkkms (km.value)) 
					{
				
				km.style.backgroundColor = 'white';
			
				
				total_kms = Number(total_kms) + Number(km.value)		
				
			
				
				
					} 
				else 
					{
				km.style.backgroundColor = '#FF593F';
			
					}
				
				}
			else //km value blank
				{
			if (date.value != '') {	km.style.backgroundColor = '#FF593F';} else {km.style.backgroundColor = 'white';}
		
				}
		
		
			var dayid = 'day_' + rowid.substr(1);
			var datespan = document.getElementById(dayid)	
				
				
				
			if (date.value == '' && km.value != '')  
				{
				date.style.backgroundColor = '#FF593F'; 
				} else 
				{
				date.style.backgroundColor = 'white'
				
					
				}
			
			
				if (date.value.length == 10) 
					{
					
					
					var year = date.value.substring(6,10);
					var month= date.value.substring(3,5);
					var day= date.value.substring(0,2);
					var today=new Date(year,month - 1,day)
					var thisDay=today.getDay()
					var myDays=[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\"]
				
					datespan.innerHTML = myDays[thisDay]
					
					} else {datespan.innerHTML = '';}
		
	
			
			if (date.value != '') {enddate = date.value;}
			if (date.value != '' && startdate == '') {startdate = date.value;}
			
			} //LOOPING THROUGH TABLE ROWS
			
			var firstday_display = document.getElementById('startdate');
			firstday_display.value =  startdate ;
			firstday_display.readOnly = true;
			
			var lastday_display = document.getElementById('enddate');
			lastday_display.value =  enddate ;
			lastday_display.readOnly = true;
		
			

			var totalkms_display = document.getElementById('total_kms');
			totalkms_display.value =  total_kms;
			totalkms_display.readOnly = true;
			
		
			disableCheck();
			//end check_leave
			}	
			
		
		
			</script>";
			
		//	} //end detailed mode2
			
			
				echo "
			
			
<br><br>
			
			<span style=\"width:10em;display: inline-block;\">$phrase[267]</span><input type=\"text\" size=\"10\" id=\"startdate\" name=\"startdate\"  readonly";
			if (isset($startdate)) {echo " value=\"$startdate\"";}
			//if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo "><br>
			<span style=\"width:10em;display: inline-block;\">$phrase[268]</span><input type=\"text\" size=\"10\" name=\"enddate\" id=\"enddate\" readonly";
			if (isset($enddate)) {echo " value=\"$enddate\"";}
			//if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo "><br>
			<span style=\"width:10em;display: inline-block;\">$phrase[1076]</span><input type=\"text\" size=\"10\" name=\"total_kms\" id=\"total_kms\"";
			if (isset($total_kms)) {echo " value=\"$total_kms\"";}
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo " > 
			
			
			
			<script type=\"text/javascript\">
		
			
	datepicker('startdate');
	datepicker('enddate');

			
		</script>
					
		
		<br><br>
			<input type=\"hidden\" name=\"m\" value=\"$m\">
		
		";
			
			
			
	
			if ($approval_status == 0 || $approval_status == 3)
			{
				
			echo "
		<b>	$phrase[135]</b><br>";
			if (isset($comment) && $comment != '') 
			{
			$_comment = nl2br($comment);
			echo $_comment;
			}
			echo "<textarea name=\"comment\" cols=\"60\" rows=\"6\" id=\"comments\"  onkeyup=\"disableCheck()\">";
			
			echo "</textarea><br><br>	
				
				
			<input type=\"hidden\" name=\"year\" value=\"$sy\">	
			<input type=\"hidden\" name=\"update\" value=\"updateleave\">
			<input type=\"hidden\" name=\"travel_id\" value=\"$travel_id\">";
		if ($approval_status == 0) { echo "<input type=\"submit\" value=\"$phrase[16]\" id=\"submit1\">";} //update
		if ($approval_status == 3) { echo "<input type=\"submit\" name=\"submitdraft\" value=\"$phrase[192]\" id=\"submit1\">  <input type=\"submit\" name=\"updatedraft\" value=\"$phrase[1072]\" id=\"submit2\">  ";}	
			
			}
			
		
			echo "</form>
			
			
			";
			
			$sql = "select * from travel_log where travel_id = '$travel_id'";
			//echo $sql;
			echo "<div id=\"folder\"><h2>$phrase[728]</h2>";
			$DB->query($sql,"travel.php");
			while ($row = $DB->get())
			{
			$date_stamp = strftime("%d/%m/%y  %H:%M%p",$row["date_stamp"]);
			$user_name = $row["user_name"];	
			$ip = $row["ip"];
			$log_no = $row["log_no"];
			$message = nl2br($row["message"]);	
			//$message = $row["message"];	
			echo "<div ><span onclick=\"toggle('l_$log_no')\">$date_stamp </span>
			<div id=\"l_$log_no\">$message
			Updated $user_name $ip
			</div>
			
			</div>";	
			}
			
		}

		
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "cancel")
		{
			$travel_id = $_REQUEST["travel_id"];
			
		echo "<h2>Are you sure you want to cancel application # $travel_id?</h2>
		
		<a href=\"travel.php?m=$m&update=cancel&travel_id=$travel_id\">$phrase[12]</a> | <a href=\"travel.php?m=$m\">$phrase[13]</a>";
		
		
	
			
			
		}
			
		
		elseif (isset($_REQUEST["view"]) && $_REQUEST["view"] == "cal")
		{
			
			
	echo "<br><br>";

	
	
	
	
	
	
	
	
	
	
	
			
			
		if (!isset($_REQUEST["t"]))
	{

	if (isset($_REQUEST["month"]))
	{
		$t = mktime(0, 0, 0, $_REQUEST["month"], 1, $_REQUEST["year"]);

	}
	else {
		$t = time();
	}
	} else {$t = $_REQUEST["t"];}
	
		
			
			
	$display = strftime("%B %Y", $t);
$day = date("d",$t);
$month = date("m",$t);
$monthname = date("F",$t);
$year = date("Y",$t);  
$daysinmonth = date("t",$t);  
 //$weekday = date("w");   
 
 $thisday = date("d");
$thismonth = date("m");
$thisyear = date("Y"); 

   
//$t = mktime(0, 0, 0, 01, 31,  $year);

$lastmonth = mktime(0, 0, 0, $month -1, 01,  $year);
$nextmonth = mktime(0, 0, 0, $month +1, 01,  $year);
  

   
   $fd  = mktime(0, 0, 0, $month  , 01, $year);
   $fd = date("w",$fd);
   $_fd = $year . $month . "01";
   $_fd = "$year-$month-01";
   //echo "fd is $fd<br>";
   
   $ld  = mktime(0, 0, 0, $month  , $daysinmonth, $year);
   $ld = date("w",$ld);
   $_ld = $year . $month . $daysinmonth;
    $_ld = "$year-$month-$daysinmonth";
   //echo "ld is $ld";		
			

   
   
   //get public holidays and put in array
		if ($DB->type == "mysql")
			{		
			$sql = "select name,date_format(holiday, '%Y%m%d') as holiday from holidays ";	
			}
		else
			{
			$sql = "select name,strftime('%Y%m%d',holiday) as holiday from holidays ";		
			}
		//$sql = "select date_format(holiday, '%D %b %Y') as fholiday, date_format(holiday, '%Y%m%d') as holiday, name from holidays ";	
		
		$DB->query($sql,"travel.php");
		$num = $DB->countrows();
		
		while ($row = $DB->get()) 
		{
			
		$a_holiday[] = $row["holiday"];
	
		$a_name[] = formattext($row["name"]);
		}
		
		
   
   
  
   

if ($DB->type == "mysql")
				{
   $sql = "select cat_name,cat_code,registration,colour, approval_status, travel_requests.travel_id as travel_id,travel_type, kms, dayofmonth(travel_date) as day from travel_requests, travel_dates , travel_category
   where travel_requests.m = '$m' and user_id = '$leaveuser' 
   and travel_requests.travel_id = travel_dates.travel_id
   and travel_date >= '$_fd' and travel_date <= '$_ld'
   and travel_category.cat_id = travel_requests.travel_type
   ";
   
				}

else
				{
				$sql = "select cat_name,cat_code,registration,colour, approval_status, travel_requests.travel_id as travel_id,travel_type, kms, strftime('%d',travel_date) as day from travel_requests, travel_dates , travel_category
   where travel_requests.m = '$m' and user_id = '$leaveuser' 
   and travel_requests.travel_id = travel_dates.travel_id
   and travel_date >= '$_fd' and travel_date <= '$_ld'
   and travel_category.cat_id = travel_requests.travel_type
   ";	
				}
				
				//echo $sql;
   
  $DB->query($sql,"travel.php");
	while ($row = $DB->get())
			{
		
		
			$_registration[] = $row["registration"];	
			$_status[] = $row["approval_status"];	
			
			$_colour[] = $row["colour"];	
			$_id[] = $row["travel_id"];   
			$_day[] = $row["day"];  
			$_kms[] = $row["kms"]; 
		
			
			}
			
		
	//print_r($_leave_colour);			
  echo "<table  style=\"margin-left:2px\" class=\"colourtable\" id=\"calendar\" cellpadding=\"3\">
 <tr class=\"accent\"><td style=\"text-align:left\" valign=\"bottom\"> 
 <a href=\"travel.php?m=$m&amp;view=cal&amp;leaveuser=$leaveuser&amp;t=$lastmonth\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" >
   
   

   <form style=\"display:inline;\" action=\"travel.php\" method=\"get\">
   <select name=\"month\" style=\"font-size:1.5em\">";
$counter = 1;
while ($counter < 13)
{
	$monthname = strftime("%b",mktime(0,0,0,$counter,01,$year));
	echo "<option value=\"$counter\"";
	if ($counter == $month) { echo " selected";}
	echo ">$monthname</option>";
	$counter++;
	
}

$displayyear = $year -2;
$endyear = $year +2;

echo "</select><select name=\"year\" style=\"font-size:1.5em\">";
while ($displayyear <= $endyear)
{
	echo "<option value=\"$displayyear\"";
	if ($displayyear == $year) { echo " selected";}
	echo ">$displayyear</option>";
	$displayyear++;
	
}


echo "</select>
   
       <input type=\"hidden\" name=\"m\" value=\"$m\">
       <input type=\"hidden\" name=\"view\" value=\"cal\">
        <input type=\"hidden\" name=\"leaveuser\" value=\"$leaveuser\">
     <input type=\"submit\" value=\"Go\" style=\"font-size:1.5em\">
   
   </form>";

	$sql = "select first_name, last_name from user where userid = '$leaveuser'";
		$DB->query($sql,"travel.php");
		$row = $DB->get();
		$last_name = $row["last_name"];
		$first_name = $row["first_name"];
		
		echo "<br><br><span style=\"font-size:1.6em\">$first_name $last_name</span>";

echo "</td>
   <td style=\"text-align:right\" valign=\"middle\">
   <a href=\"travel.php?m=$m&amp;view=cal&amp;leaveuser=$leaveuser&amp;t=$nextmonth\" class=\"hide\">$phrase[155]</a> </td></tr>
   
   "; 
   

if (isset($CALENDAR_START) && $CALENDAR_START =="MON") {$cal_offset = 1;} else {$cal_offset = 0;}

   //display blank cells at start of month
   $counter = 0 + $cal_offset;
    if ($cal_offset == 1 && $fd == 0) $fd=7; 
   if ($fd <> 0 + $cal_offset)
   	{
	echo "<tr >";
	}
   while ($counter < $fd)
   	{
	echo "<td>";
	
	
	
	echo "</td>";
	if ($counter == 6 + $cal_offset)
		{
		echo "</tr>\n";
		}
	$counter++;
	}
   
   if (isset($CALENDAR_START) && $CALENDAR_START =="MON") {$cal_offset = 1;} else {$cal_offset = 0;}
   
   
   //display month as table cells
   $daycount = 1;
   $displayholiday = 0;
   while ($daycount <= $daysinmonth)
   	{
	$endline = (($counter + $daycount - $cal_offset) % 7);
	$day = str_pad($daycount, 2, "0", STR_PAD_LEFT);
	$dayname  = strftime("%a",mktime(0, 0, 0, $month, $daycount,  $year));
	$t = mktime(0, 0, 0, $month, $day,  $year);
	if ($endline == 1) { echo "<tr>";}
	echo "<td valign=\"top\"";
	if ($thisyear.$thismonth.$thisday == $year.$month.$day)
				{
				echo " id=\"scrollpoint\" class=\"accent\"";
				$displayholiday = 1;
				}
				
				
	
	
	echo "><span style=\"font-size:large\"><b>$daycount</b></span>&nbsp;&nbsp; $dayname<br><br>";
	
	if(isset($a_holiday))
		{
		foreach ($a_holiday as $index => $holiday)
			{
			
			if ($holiday == $year.$month.$day)
				{
				echo "<span style=\"background:#FFCF4F;padding:0.2em\">$a_name[$index]</span><br><br>";
				}
			}
		}
	
		
		
	if (isset($pname))
	{	
	foreach ($pname as $id => $name)
		{
			
		$today = mktime(0, 0, 0, $month, $daycount,  $year);
		$gapindays = ($today - $pstart[$id]) /86400;
		//echo "gap is $gapindays";	
		if ($gapindays % $pdays[$id] == 0)
		{ echo "<span style=\"background:#$pcolour[$id];padding:0.2em\">$name</span><br><br>";}
		
		}
	}	
		
		
		
	$date =  ($year.$month.$day) * 1;
		
	//display events for today
	if (isset($_kms))
	{	

	
		
	foreach ($_kms as $key => $id) 
		{
			//echo "date is $date start is $_leave_startdate[$key]	end is $_leave_enddate[$key]  mode is $_leave_mode[$key]";
			
			//echo "cat is $_leave_cat[$key] kms is $_leave_kms[$key] kms BBB<br>";
						echo "";
			
		if ( $_day[$key] == $daycount)
			{
			echo "<a href=\"travel.php?m=$m&event=view&travel_id=$_id[$key]\" title=\"$_kms[$key] kms\"  style=\"color:#$_colour[$key]\" >$_registration[$key] $_kms[$key] $phrase[1074]</a>";
			if ($_status[$key] == 1) {echo "<img src=\"../images/tick16.png\" alt=\"$phrase[1007]\" style=\"vertical-align: text-bottom; \">";}
			if ($_status[$key] == 2) {echo "<img src=\"../images/cross16.png\" alt=\"$phrase[1008]\" style=\"vertical-align: text-bottom; \">";}
			echo "<br>";	
			}
		
		}
	}

	
	
	
	
	echo "</td>";
	
   				
				
				
			   if ( $endline == 0)
			   	{
				echo "</tr>\n";
				}
				$daycount++;
   }
   
   //displays blank cells at end of month
   
   if ($endline <> 0)
   	{
	while ($endline < (7 - $cal_offset))
		{
		echo "<td></td>";
		
		if ($endline == (7 - $cal_offset))
		{
		echo "</tr>";
		}
	$endline++;
		
		
		
		}
	
	
	}
   echo "
    <tr class=\"accent\"><td style=\"text-align:left\" valign=\"bottom\">  <a href=\"travel.php?m=$m&amp;view=cal&amp;leaveuser=$leaveuser&amp;t=$lastmonth\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" ><span style=\"font-size:150%\"> $display</span></td>
   <td style=\"text-align:right\" valign=\"middle\">   <a href=\"travel.php?m=$m&amp;view=cal&amp;leaveuser=$leaveuser&amp;t=$nextmonth\" class=\"hide\">$phrase[155]</a></td></tr>
   </table>";
			
		}
		
		else 
		{
			
		
		if (isset($_REQUEST["category"])) {$category = $DB->escape($_REQUEST["category"]);}	
		if (isset($_REQUEST["status"])) {$status = $DB->escape($_REQUEST["status"]);}	
		if (isset($_REQUEST["filter"])) {$filter = $DB->escape($_REQUEST["filter"]);}	
		if (isset($_REQUEST["location"])) {$location = $DB->escape($_REQUEST["location"]);}		
			

		if (isset($leaveuser))
		{
		$sql = "select first_name, last_name from user where userid = '$leaveuser'";
		$DB->query($sql,"travel.php");
		$row = $DB->get();
		$last_name = $row["last_name"];
		$first_name = $row["first_name"];
		
		echo "<br><br><span style=\"font-size:1.6em\">$phrase[141] $first_name $last_name</span><br><br>
	";	}
			
		
			
		
		/*
		if (!isset($leaveuser) && !isset($filter) && !isset($status) && !isset($category) && !isset($location)) {$status = 0;}
		if (isset($status))
		{
			if ($status == 0) {echo "<span style=\"font-size:1.6em\">$phrase[1006] $phrase[1005]</span><br><br>";}
			if ($status == 1) {echo "<span style=\"font-size:1.6em\">$phrase[1007] $phrase[1005]</span><br><br>";}
			if ($status == 2) {echo "<span style=\"font-size:1.6em\">$phrase[1008] $phrase[1005]</span><br><br>";}
		}
		
		if ((isset($filter) && $filter == "cat") || isset($category))
		{
			echo "<span style=\"font-size:1.6em\">Leave by category</span><br><br>";
		}	
		
		if ((isset($filter) && $filter == "location") || isset($location))
		{
			echo "<span style=\"font-size:1.6em\">Leave by group</span><br><br>";
		}		
	
		
		*/
			echo "
		<form action=\"travel.php\" style=\"display:inline\">
			 <select name=\"year\">";
			$temp = date("Y") - 10 ;
			$counter = 0;
			while ($counter < 21)
			{
			$value = $temp   + $counter;	
			echo "<option value=\"$value\"";
			if ($value == $year) {echo " selected";}
			echo ">$value</option>";
			$counter++;
			}
			
			echo "</select> <input type=\"submit\" value=\"Go\">";
			
			echo "<input type=\"hidden\" name=\"m\" value=\"$m\"></form><br><br>
			<table class=\"colourtable\">";
			
		
			
		
			
			echo "<tr style=\"font-weight:bold\"><td>$phrase[340]</td>";
			if (!isset($leaveuser)) {echo "<td><a href=\"travel.php?m=$m&orderby=last_name";
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[131] </a></td>";}
			echo "<td><a href=\"travel.php?m=$m&orderby=startdate";
			if (isset($leaveuser)) {echo "&leaveuser=$leaveuser";}
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[267] </a></td><td>$phrase[268] </td><td>$phrase[1076]</td>
			<td>$phrase[1077]</td><td><a href=\"travel.php?m=$m&orderby=approval_status";
			if (isset($leaveuser)) {echo "&leaveuser=$leaveuser";}
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[401]</a></td>";
			
			
			echo "<td></td><td></td></tr>
			";
			
			
			
			
			
			$sql = "select * from travel_category where m = '$m'";
			$DB->query($sql,"travel.php");
			while ($row = $DB->get())
			{
			$cat_id= $row["cat_id"];
			$cat_name= $row["cat_name"];
			$types[$cat_id] = $cat_name;
			$type_totals[$cat_id] = 0;
			}
			
			$_status[0] = "$phrase[1006]";
			$_status[1] = "$phrase[1007]";
			$_status[2] = "$phrase[1008]";
			$_status[3] = "$phrase[1071]";
			
			
			if (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "startdate") { $orderby  = "startdate" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "approval_status") { $orderby  = "approval_status" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "travel_type") { $orderby  = "travel_type" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "last_name") { $orderby  = "last_name" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "processed") { $orderby  = "processed" ;}
			else {$orderby  = "startdate" ;}
			
			
		
				if ($DB->type == "mysql")
			{
		$sql = "select * from travel_requests where m = '$m' and user_id = '$leaveuser' and year(startdate) = '$year' order by $orderby";
			}
			
			else
			{
		$sql = "select * from travel_requests where m = '$m' and user_id = '$leaveuser' and strftime('%Y',startdate) = '$year' order by $orderby";
		
			}
			
		//echo $sql;
			
			$reg_totals = array();
			
		//	echo $sql;
			$DB->query($sql,"travel.php");
			while ($row = $DB->get())
			{
			  
				
			 
			
			$startdate = $row["startdate"];
			$sy = substr($startdate,0,4);
			$sm = substr($startdate,5,2);
			$sd = substr($startdate,8,4);
			
			
			$enddate = $row["enddate"];
			
			$ey = substr($enddate,0,4);
			$em = substr($enddate,5,2);
			$ed = substr($enddate,8,4);
		
			
			
			if ($DATEFORMAT == "%d-%m-%Y")
             {
            $startdate = $sd . "-"  . $sm . "-" . $sy;
            $enddate = $ed . "-"  . $em . "-" . $ey;
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
        	$startdate = $sm . "-"  . $sd . "-" . $sy;
        	$enddate = $em . "-"  . $ed . "-" . $ey;
             }
			
			
			
			$processed = $row["processed"];
			$total_kms = $row["totalkms"];
			$registration = $row["registration"];
			$approval_status = $row["approval_status"];
			$travel_type = $row["travel_type"];
			$travel_id = $row["travel_id"];
		
			echo "<tr><td>$travel_id</td>";
			//if (isset($status) || isset($category) || isset($location))
			if (!isset($leaveuser))
			{
				$first_name = $row["first_name"];
				$last_name = $row["last_name"];	
				echo "<td>$last_name, $first_name</td>";
			} 
			echo "<td>$startdate</td><td>$enddate </td><td>$total_kms</td><td>$registration</td><td";
			
			if ($approval_status == 3) {echo " class=\"partoption\"";}
			if ($approval_status == 0) {echo " class=\"partoption\"";}
			if ($approval_status == 1) {echo " class=\"yesoption\"";}
			if ($approval_status == 2) {echo " class=\"nooption\"";}
			
			echo ">$_status[$approval_status] </td>";
			//
			
			echo "<td><a href=\"travel.php?m=$m&event=view&travel_id=$travel_id\">$phrase[284]</a></td>";
			
			if ($approval_status == 1) {
				
				if (array_key_exists($registration,$reg_totals))
				{
				$reg_totals[$registration] = $reg_totals[$registration] + $total_kms;} else {$reg_totals[$registration] = $total_kms;}
			}
			
			
			
		
			
			echo "<td>";
			if ($approval_status== 0 || $approval_status== 3) {echo "<a href=\"travel.php?m=$m&event=cancel&travel_id=$travel_id\">$phrase[146]</a>";}
			echo "</td></tr>";
			}
			
			echo "</table>";
			
			if (isset($reg_totals))
			{
			echo "<br><br><b>$phrase[1007] Summary</b><table><tr><td>$phrase[1000] </td><td>$phrase[1076]</td></tr>";
			
			
			foreach ($reg_totals   as $key => $value)
				{
				if ($value != 0) {echo "<tr><td>$key </td><td>$value</td></tr>";}
				}
			echo "</table>";
			}
		}
		
		
		
		
			//end contentbox
		echo "</div></div>";
		
	 
	     

	     	 
	  //	include("../includes/rightsidebar.php");   
		
		
	


include ("../includes/footer.php");
	
	}
	
	
	
	
	

?>