<?php

if (!isset($DATEFORMAT)) {$DATEFORMAT = "%d-%m-%Y";}


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$now = time();
$ip = ip("pc");

$integers[] = "content_id";
$integers[] = "image_id";
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
$DB->query($sql,"leave.php");
$row = $DB->get();
$modname = formattext($row["name"]);
$input = $row["input"];



		
		 $sql  = "select * from leave_options where m = '$m'";	
	$DB->query($sql,"editform.php");
	
	$row = $DB->get();

	$mode = $row["mode"];
	$break = $row["break"];
	$introduction = $row["introduction"];
	$comment_label = $row["comment_label"];
        $summary = $row["summary"];
	//$label = $row["label"];
	//$label_updated = $row["label_updated"];
	//$label_cancelled = $row["label_cancelled"];
	//$label_submitted = $row["label_submitted"];
	//$label_myleave = $row["label_myleave"];
		
	
		
		//$leaveuser = $_SESSION["userid"];//check if manager
				
		$managers = array();
	
		$sql = "select userid, authority from leave_managers where userid = '$_SESSION[userid]'";

		$authority = 0;
		$DB->query($sql,"leaveadmin.php");
		while ($row = $DB->get())
		{
			$managers[] = $row["userid"];
			
			if ($row["authority"] > $authority) {$authority = $row["authority"];}
		}
	
                
        
        if ($authority == 4 && isset($_REQUEST["leaveuser"])) { 
                    $leaveuser = $DB->escape($_REQUEST["leaveuser"]);
                    $_SESSION["alias"] = $DB->escape($_REQUEST["leaveuser"]);
                }
                elseif (isset($_SESSION["alias"]) && $_SESSION["alias"] != "") { $leaveuser = $_SESSION["alias"];}
                else { $leaveuser = $DB->escape($_SESSION["userid"]);}
	
		//echo "leave user is $leaveuser";
                
                $sql = "select * from user where userid = '$leaveuser'";
				$DB->query($sql,"leave.php");
					$row = $DB->get();
					
					$first_name= $row["first_name"];
					$last_name= $row["last_name"];
                                        $payroll_number = trim($row["payroll_number"]);


                
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "dm" )
		{	
    
    $leave_id = $DB->escape($_REQUEST["leave_id"]);	
   
    $sql = "update leave_requests set comment = '' where approval_status = '3' and leave_id = '$leave_id' and user_id = '$leaveuser'";
    $DB->query($sql,"leave.php");
    
    
}

		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addleave" )
		{		
		

		//print_r($_REQUEST);
		
		if (isset($_REQUEST["draft"])) {$status = '3';} else {$status = '0';}
	
		$startdate = $DB->escape($_POST["startdate"]);
		$enddate = $DB->escape($_POST["enddate"]);
		$leavetype = $DB->escape($_POST["leavetype"]);
		$comment = $DB->escape($_POST["comment"]);
		$now = time();
		$nowdate = strftime("%x",$now);
		
		$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
		
		//print_r($_REQUEST);
		
		if ($_POST["total_hours"] == 0 || $_POST["total_hours"] == "")
		
		{
			$ERROR =  "Leave submission failed. No hours submitted";
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

		
		$total = 0;	
		
				if (isset($_REQUEST["hours"]))
		{
		$hours =  $_REQUEST["hours"];
		$date =  $_REQUEST["date"];
		$starthour =  $_REQUEST["starthour"];
		$startminute =  $_REQUEST["startminute"];
		$endhour =  $_REQUEST["endhour"];
		$endminute =  $_REQUEST["endminute"];
		$break =  $_REQUEST["break"];
		
				
		
			foreach ($hours as $key => $value)
			{
				
			$_duration = (( (($endhour[$key] * 60) + ($endminute[$key] * 1) )  - (($starthour[$key] * 60) + ($startminute[$key] * 1) ) )  - ($break[$key] * 60) ) / 60;
			$_duration = round($_duration,2);
			
			$durations[$key] = $_duration; 
				
			$total = $total + $_duration;
			}
		}
		
			if ($total > 0 ) {$total_hours = $total;} else {$total_hours = $DB->escape($_POST["total_hours"]);}
	
		$sql= "insert into leave_requests values(NULL,'$leaveuser','$leavetype','$status','$_SESSION[username]','$now','$m','$total_hours','$startdate','$enddate','$_comment','0')";
		//echo $sql;
		$DB->query($sql,"leave.php");
		$row = $DB->get();
		
		$leave_id = $DB->last_insert();
		
		//echo "leaveid is $leaveid";
		
		
		
		//print_r($startminute);
		
		$dateslist = "";
		if (isset($hours))
		{
		foreach ($hours as $key => $value)
			{
			if ($value != "" || $date[$key] != "") //insert row
				{
				$_hour = $DB->escape($durations[$key]);
				//$_hour = $DB->escape($value);	
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
				
				$_startminute = $DB->escape($startminute[$key]);	
				$_starthour = $DB->escape($starthour[$key]);	
				$_endhour = $DB->escape($endhour[$key]);	
				$_endminute = $DB->escape($endminute[$key]);	
				$_break = $DB->escape($break[$key]);	
				
				//$_date = strrev($_date);
				$dayname = strftime("%a",mktime(0,0,0,$_m,$_d,$_y));
				
				$sql = "insert into leave_dates values('$_hour','$_startminute','$_starthour','$_endminute','$_endhour','$_break','$leave_id','$_date')";
				
				$am1 = "am";
				$am2 = "am";
				
				if ($_starthour > 12) {$_starthour = $_starthour - 12 ; $am1 = "pm";}
				if ($_endhour > 12) {$_endhour = $_endhour - 12 ; $am2 = "pm";}
				if ($_startminute < 10) {$_startminute = "0" . $_startminute;}
				if ($_endminute < 10) {$_endminute = "0" . $_endminute;}
				
				
				$dateslist .= "$dayname $date[$key] $_hour $phrase[474] $_starthour:$_startminute$am1 - $_endhour:$_endminute$am2 $phrase[1003] $_break
";
				$DB->query($sql,"leave.php");
				
				}
			}
		}

		
		if ($status == 0)
		
		{
		
				$sql = "select first_name, last_name, cat_name from user, leave_requests, leave_category
			where leave_requests.leave_id = '$leave_id' and user.userid = leave_requests.user_id
			and leave_category.cat_id = leave_requests.leave_type";
			$DB->query($sql,"leave.php");
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
$phrase[1002]: $total_hours								
";
			
if (trim($comment) != "") {$message .= "$comment_label: $_POST[comment]

";}
if (trim($dateslist) != "") {$message .= "$phrase[1001]: 
$dateslist
";} 
			
			
			
			
			
			
			
			
			
			
		
			
			
			//alert submission
			$sql = "select distinct user.userid,email from user, leave_members, leave_alertees
			where leave_members.userid = '$leaveuser' and leave_members.location_id = leave_alertees.location_id
			and leave_alertees.userid = user.userid";
			
			$DB->query($sql,"leave.php");
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
			
			$DB->query($sql,"leave.php");
			$row = $DB->get();
			$fromemail = $row["email"];
			
			$headers = "From: $fromemail";
			
		
			
			//echo $sql;
			//submitted
			$subject = "$modname - $phrase[1063] #$leave_id";
			
		//echo $emailaddress;
				send_email($DB,$emailaddress, $subject, $message,$headers);				
			
			}
			
			
				//log submission
			$message = $DB->escape($message);	
			$ip = ip("pc");
			$sql = "insert into leave_log values(NULL,'$now','$_SESSION[username]','$ip','$leave_id','$message')";
		//	echo $sql;
			$DB->query($sql,"leave.php");
			
			
		}
			
			
			
			} else { $ERROR =  "Leave submission failed. Invalid dates provided";}
		
			
		}
		
		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "cancel" )
		{
			
			$leave_id = $DB->escape($_REQUEST["leave_id"]);	
			
			
			$sql = "select approval_status from leave_requests where leave_id = '$leave_id'";
			
			$DB->query($sql,"leave.php");
			$row = $DB->get();
		
			$status = $row["approval_status"];
			
			
			
			
			
			
			$sql = "delete from leave_dates where leave_id = '$leave_id'";
			$DB->query($sql,"leave.php");
			
			$sql = "delete from leave_requests where leave_requests.m = '$m' and leave_id = '$leave_id'";
			$DB->query($sql,"leave.php");
			
			$sql = "delete from leave_log where leave_id = '$leave_id'";
			$DB->query($sql,"leave.php");
			
			
			
			if ($status == 0)
			{
			$message = "Leave application #$leave_id cancelled by $_SESSION[username]";
			
			
			$sql = "select distinct user.userid,email from user, leave_members, leave_alertees
			where leave_members.userid = '$leaveuser' and leave_members.location_id = leave_alertees.location_id
			and leave_alertees.userid = user.userid";
			
			$DB->query($sql,"leave.php");
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
			
			$DB->query($sql,"leave.php");
			$row = $DB->get();
			$fromemail = $row["email"];
			
			$headers = "From: $fromemail";
			
		
			
			//echo $sql;
			//cancelled
			$subject = "$modname - $phrase[1065]  #$leave_id";
			
		//echo $emailaddress;
				send_email($DB,$emailaddress, $subject, $message,$headers);		
			}
			}
		}
			

		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updateleave" )
		{		
		
		if (isset($_REQUEST["submitdraft"])) {$approvalstatus = '0';}
		elseif (isset($_REQUEST["updatedraft"])) {$approvalstatus = '3';}
		else {$approvalstatus = '0';}
		
		$leave_id = $DB->escape($_POST["leave_id"]);
	
		$startdate = $DB->escape($_POST["startdate"]);
		$enddate = $DB->escape($_POST["enddate"]);
		$leavetype = $DB->escape($_POST["leavetype"]);
		$comment = $DB->escape($_POST["comment"]);
		
		
		$sql = "select m , user_id from leave_requests where leave_id = '$leave_id'";
		$DB->query($sql,"leavemanage.php");
		$row = $DB->get();
		$_m = $row["m"];
		$_user_id = $row["user_id"];
			
		if ($m != $_m || $_user_id != $leaveuser) {
			//trying to update record from another module
                    echo "Cannot submit leave for someone else";
			exit();
		}
	
	
		$now = time();
		$nowdate = strftime("%x",$now);
		
		
		$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
		
	



	if ($_POST["total_hours"] == 0 || $_POST["total_hours"] == "")
		
		{
			$ERROR =  "<br>Leave submission failed. No hours submitted";
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
			
			
				if (isset($_REQUEST["hours"]))
		{
		
		$hours =  $_REQUEST["hours"];
		$date =  $_REQUEST["date"];
		$starthour =  $_REQUEST["starthour"];
		$startminute =  $_REQUEST["startminute"];
		$endhour =  $_REQUEST["endhour"];
		$endminute =  $_REQUEST["endminute"];
		$break =  $_REQUEST["break"];
		
			
			
			
		
			foreach ($hours as $key => $value)
			{
				
			$_duration = (( (($endhour[$key] * 60) + ($endminute[$key] * 1) )  - (($starthour[$key] * 60) + ($startminute[$key] * 1) ) )  - ($break[$key] * 60) ) / 60;
			$_duration = round($_duration,2);
			
			$durations[$key] = $_duration; 
				
			$total = $total + $_duration;
			}
			}
		
			if ($total > 0 ) {$total_hours = $total;} else {$total_hours = $DB->escape($_POST["total_hours"]);}
			
			
				if ($DB->type == "mysql")
			{
		$sql= "update leave_requests set leave_type = '$leavetype',approval_status = '$approvalstatus', totalhours = '$total_hours',startdate = '$startdate',enddate = '$enddate',comment = CONCAT(comment,'$_comment') where leave_id = $leave_id and user_id = '$leaveuser'";
			}
			
				else
			{
		$sql= "update leave_requests set leave_type = '$leavetype',approval_status = '$approvalstatus',totalhours = '$total_hours',startdate = '$startdate',enddate = '$enddate',comment = comment || '$_comment' where leave_id = $leave_id and user_id = '$leaveuser'";
		
			}	
	
		
	
		

		$DB->query($sql,"leave.php");
		$row = $DB->get();
		
		$dateslist = "";
		
		//echo "leaveid is $leaveid";
		
	
		
		$sql = "delete from leave_dates where leave_id = $leave_id";
		$DB->query($sql,"leave.php");
		$row = $DB->get();
		//print_r($startminute);
		
		if (isset($hours))
		{
		foreach ($hours as $key => $value)
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
				$_hour = $DB->escape($durations[$key]);
				//$_hour = $DB->escape($value);	
				//$_date = $DB->escape($date[$key]);	
				$_startminute = $DB->escape($startminute[$key]);	
				$_starthour = $DB->escape($starthour[$key]);	
				$_endhour = $DB->escape($endhour[$key]);	
				$_endminute = $DB->escape($endminute[$key]);	
				$_break = $DB->escape($break[$key]);	
				
				$dayname = strftime("%a",mktime(0,0,0,$_m,$_d,$_y));
				
				$sql = "insert into leave_dates values('$_hour','$_startminute','$_starthour','$_endminute','$_endhour','$_break','$leave_id','$_date')";
				
				$am1 = "am";
				$am2 = "am";
				
				if ($_starthour > 12) {$start_hour = $start_hour - 12 ; $am1 = "pm";}
				if ($_endhour > 12) {$_endhour = $_endhour - 12 ; $am2 = "pm";}
				if ($_startminute < 10) {$_startminute = "0" . $_startminute; }
				if ($_endminute < 10) {$_endminute = "0" . $_endminute;}
				
				
				$dateslist .= "$dayname $date[$key] $_hour $phrase[474] $_starthour:$_startminute$am1 - $_endhour:$_endminute$am2 $phrase[1003] $_break
";
				//echo $sql;
				$DB->query($sql,"leave.php");
				
				}
			}
		}

		
			$status[0] = "$phrase[1006]";
			$status[1] = "$phrase[1007]"; //approved
			$status[2] = "$phrase[1008]"; //rejected
			$status[3] = "$phrase[1071]"; //draft
			$status[4]= "$phrase[152]"; //cancelled
			
		//	echo "approvalstatus is $approvalstatus";
			if ($approvalstatus == 0)
			{
			
				$sql = "select first_name, last_name, cat_name from user, leave_requests, leave_category
			where leave_requests.leave_id = '$leave_id' and user.userid = leave_requests.user_id
			and leave_category.cat_id = leave_requests.leave_type";
			$DB->query($sql,"leave.php");
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
$phrase[1002]: $total_hours						
";
			
if (trim($comment) != "") {$message .= "$comment_label: $_POST[comment]

";}
if (trim($dateslist) != "") {$message .= "$phrase[1001]: 
$dateslist
";} 
			
	
			
			
			//alert submission
				//alert submission
			$sql = "select distinct user.userid,email from user, leave_members, leave_alertees
			where leave_members.userid = '$leaveuser' and leave_members.location_id = leave_alertees.location_id
			and leave_alertees.userid = user.userid";
			//echo $sql;
			
		//	$sql = "select email from user where userid = '2235'";
			
			
			$DB->query($sql,"leave.php");
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
			
			$DB->query($sql,"leave.php");
			$row = $DB->get();
			$fromemail = $row["email"];
			
			$headers = "From: $fromemail";
			//updated
			if (isset($_REQUEST["submitdraft"])) {
				$subject = "$modname - $phrase[1063]  #$leave_id";
			}
			
			else 
			{
			$subject = "$modname - $phrase[1064]  #$leave_id";
			}
	
		//echo $message;
				send_email($DB,$emailaddress, $subject, $message,$headers);				
			
			
			
			
			
			
			
				//log submission
			
			$ip = ip("pc");
			$message = $DB->escape($message);
			$sql = "insert into leave_log values(NULL,'$now','$_SESSION[username]','$ip','$leave_id','$message')";
			
			$DB->query($sql,"leave.php");
			}
			}
			
			
			
} else {$ERROR =  "Leave update failed. Invalid dates provided";}
			
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
			
			
			
		
		
	
		echo "<span class=\"leave1\">";
		
			
			
			if (isset($_REQUEST["event"]) && $_REQUEST["event"]== "apply")
			{
				echo "<a href=\"leave.php?m=$m\">$phrase[998]</a> | <a href=\"leave.php?m=$m&view=cal\">$phrase[999]</a>";
			}
			elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "view")
			{
				echo "<a href=\"leave.php?m=$m&event=apply\">$phrase[994]</a> | <a href=\"leave.php?m=$m\">$phrase[998]</a> | <a href=\"leave.php?m=$m&view=cal\">$phrase[999]</a>";
			}
			elseif (isset($_REQUEST["view"]) && $_REQUEST["view"] == "cal")
			{
				echo "<a href=\"leave.php?m=$m&event=apply\">$phrase[994]</a> |  <a href=\"leave.php?m=$m\">$phrase[998]</a>";
			}
			else
			{
				echo "<a href=\"leave.php?m=$m&event=apply\">$phrase[994]</a> | <a href=\"leave.php?m=$m&view=cal\">$phrase[999]</a>";
			}
			echo "</span><br><br>";
			
			
			if (in_array($_SESSION["userid"],$managers))
			
		{
		
				echo "<span class=\"leave2\">";
			//if ($authority > 1) {echo "<a href=\"leavemanage.php?m=$m&event=apply&leaveuser=$leaveuser\">$phrase[1011]</a> | ";}
		
					
			echo "<a href=\"leavemanage.php?m=$m&filter=people";
			if (isset($year)) { echo "&year=$year";}
			echo "\">People</a> | <a href=\"leavemanage.php?m=$m&status=0";
			if (isset($year)) { echo "&year=$year";}
			echo "\">$phrase[1006]</a> | <a href=\"leavemanage.php?m=$m&status=1";
			if (isset($year)) { echo "&year=$year";}
			echo "\">$phrase[1007]</a> | <a href=\"leavemanage.php?m=$m&status=2";
			if (isset($year)) { echo "&year=$year";}
			echo "\">$phrase[1008]</a> | <a href=\"leavemanage.php?m=$m&status=4";
			if (isset($year)) { echo "&year=$year";}
			echo "\">$phrase[152]</a> | <a href=\"leavemanage.php?m=$m&event=closed";
			if (isset($year)) { echo "&year=$year";}
			echo "\">$phrase[494]</a> | <a href=\"leavemanage.php?m=$m&filter=cat";
			if (isset($year)) { echo "&year=$year";}
			echo "\">Categories</a>  | <a href=\"leavemanage.php?m=$m&filter=location";
			if (isset($year)) { echo "&year=$year";}
			echo "\">Groups</a> </span>";
		}

echo "<p style=\"font-size:1.8em;margin: 0.8em 0\">$first_name $last_name</p>";

		if (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "apply"))
		{
			

			
			//$sql = "select mode from leave_options where m = '$m' ";
			
			//$DB->query($sql,"leave.php");
			///$row = $DB->get();
			
			//$mode = $row["mode"];
		
		
			
	//	echo "authority is $authority postleaveuser is " . $_REQUEST["leaveuser"] . " final is $leaveuser";
			
			
			
		//	print_r($_SESSION);
			/*
			$sql = "select * from user where userid = '$leaveuser'";
			echo $sql;
			$DB->query($sql,"leave.php");
			$row = $DB->get();
		
			$first_name = $row["first_name"];
			$last_name = $row["last_name"];
			*/
			
			echo "
		<h2>$phrase[1011]</h2>
			<form action=\"leave.php\" method=\"post\">";
			

			
				
				//$sql = "select * from user where userid = '$leaveuser'";
				//$DB->query($sql,"leave.php");
					//$row = $DB->get();
					
					//$first_name= $row["first_name"];
					//$last_name= $row["last_name"];
					
					//$userid= $row["userid"];
					
					
					//echo "<span >$first_name $last_name</span> <input type=\"hidden\" name=\"leaveuser\" value=\"$userid\">";
			
			
			echo "
		<br><br>
			<b>$phrase[1000] </b><br>
			<select name=\"leavetype\"  id=\"leavetype\">";
			$sql = "select * from leave_category where m = '$m' and status = '1' order by position";
			//echo $sql;
			$DB->query($sql,"leave.php");
			while ($row = $DB->get())
			{
			$cat_name = $row["cat_name"];
			$cat_id = $row["cat_id"];
			echo "<option value=\"$cat_id\"";
			if (isset($leave_type) && $leave_type == $cat_id) {echo " selected";}
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
			
			if ($mode == 1)
			{
			//echo "<b>First day </b><span id=\"firstday\"><input type=\"hidden\" name=\"startdate\" value=\"\"></span><br>
			//<b>Last day</b> <span id=\"lastday\"><input type=\"hidden\" name=\"enddate\" value=\"\"></span><br>
			//<b>$phrase[1002]</b> <span id=\"totalhours\"><input type=\"hidden\" name=\"total_hours\" value=\"\"></span>
			
			//";
			$rowcounter = 1;
			$weekcounter = 1;
			
			echo "<br>
			
		
			<span onclick=\"addday();return false;\"><img src=\"../images/add.png\" alt=\"$phrase[176]\" title=\"$phrase[176]\">$phrase[1010] </span><br><br>
		
			<table class=\"colourtable\" id=\"table\">
			<tr ><td style=\"width:10em\">$phrase[182]</td><td >$phrase[217]</td><td>$phrase[494]</td><td>$phrase[242]</td><td>$phrase[243]</td><td>$phrase[1003]</td><td>Hours</td><td></td></tr>
			</table>
			<input type=\"hidden\" id=\"rowcount\" name=\"rowcount\" value=\"0\">
			";
		
			
			echo "
			
			<script type=\"text/javascript\">
			

	
			
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
			cellDate.innerHTML = '<input type=\"text\"  id=\"' + dateid + '\" name=\"date[' + rowcount + ']\" size=\"10\" readonly >'
			cellDate.onclick=check_leave
                     
                        var cellClosed = newRow.insertCell(2);
                        cellClosed.id = 'closed_' + rowcount;
			
				datepicker(dateid);
			
			var cellStart = newRow.insertCell(3);
			cellStart.innerHTML = '<select id=\"sh_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\" name=\"starthour[' + rowcount + ']\"><option value=\"\"> </option>'";
			
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

			
			echo "
			
			+ '</select> <select id=\"sm_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"  name=\"startminute[' + rowcount + ']\">'";
			
			$temp = 0;
			while ($temp <60) 
			{
				if ($temp < 10) {$display = "0" . $temp ;} else {$display = $temp;}
				
				echo " 
				+ '<option value=\"$temp\">$display</option>'";
			$temp = $temp + 15;
			}

			
			echo "
			
			+ '</select>'     ;
			
			var cellStart = newRow.insertCell(4);
			cellStart.innerHTML = '<select id=\"eh_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"  name=\"endhour[' + rowcount + ']\"><option value=\"\"> </option>'";
			
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

			
			echo "
			
			+ '</select> <select id=\"em_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"  name=\"endminute[' + rowcount + ']\">'";
			
			$temp = 0;
			while ($temp <60) 
			{
				if ($temp < 10) {$display = "0" . $temp ;} else {$display = $temp;}
				
				echo " 
				+ '<option value=\"$temp\">$display</option>'";
				$temp = $temp + 15;
			}

			
				
			
			//echo "break is $break";
			$breaks = explode("||",$break);
			$string = "";
			if (isset($breaks))
			{
				foreach ($breaks as $i => $b)
				{
				$string .= "<option value=\"$b\">$b</option>";	
				}
			}
			
			
			
			
			echo "
			
			+ '</select>'     ;
			
			var cellBreak = newRow.insertCell(5);
			cellBreak.innerHTML = '<select id=\"b_' + rowcount + '\" onchange=\"calc(' + rowcount + ')\"    name=\"break[' + rowcount + ']\" >$string</select>';
			
			var cellHours = newRow.insertCell(6);
			cellHours.innerHTML = '<input type=\"text\" readonly=\"readonly\" class=\"hours\" onkeyup=\"check_leave()\" id=\"hours_' + rowcount + '\" name=\"hours[' + rowcount + ']\" size=\"5\"> ';
			
			var cellDelete = newRow.insertCell(7);
			cellDelete.innerHTML = '<span onclick=\"deleterow(\'' + rid + '\'); return false;\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></span>';
			
			document.getElementById('rowcount').value = rowcount;
			}
			
			function hello()
			{
			alert('hello');
			}
                        
                        
                        
			function calc(id)
			{
			var element;
			var sel;
			
			
			element = 'sm_' + id;
			sel = document.getElementById(element);
			var sm = sel.options[sel.selectedIndex].value;
			if (sm == '00') {sm = 0;}
			
			element = 'sh_' + id;
			sel = document.getElementById(element);
			var sh = sel.options[sel.selectedIndex].value;
			
			element = 'em_' + id;
			sel = document.getElementById(element);
			var em = sel.options[sel.selectedIndex].value;
			if (em == '00') {em = 0;}
			
			element = 'eh_' + id;
			sel = document.getElementById(element);
			var eh = sel.options[sel.selectedIndex].value;
			
			element = 'b_' + id;
			sel = document.getElementById(element);
			var br = sel.options[sel.selectedIndex].value;
			
			
			";
			
	
			
			
			
			echo "
			//if (bsel.value != '' && bsel.value != '0' $string)
			//{
			//bsel.style.background = 'red';
			//} else {bsel.style.background = 'white';}
			
			
			//alert('sm is ' + sm + ' sh is ' + sh + ' em is ' + em + ' eh is ' + eh + ' b is ' + br)
			
			var time = (( ((eh * 60) + (em * 1) )  - ((sh * 60) + (sm * 1) ) )  - (br * 60) ) / 60;
			//var time = ((eh * 60) + (em * 1) )  ;
			//alert(time);
			
			time = (Math.floor(time * 100) /100)
			
			var hours = document.getElementById('hours_' + id);
			hours.value = time;
			
			check_leave()
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
			
			
			function checkhour (value)
			{
			if (value.length == 1)
			{
			testRegExp = /[0-9]/i;
			}
			if (value.length == 2)
			{
			testRegExp = /[0-9][\.0-9]/i;
			}
			if (value.length == 3)
			{
			testRegExp = /^[\.0-9][\.0-9][\.0-9]$/i;
			}
			if (value.length == 4)
			{
			testRegExp = /^[\.0-9][\.0-9][\.0-9][\.0-9]$/i;
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
			alert(d.getUTCDay());
			}
			
			
                       // function check_closed()
                        //{
                        //alert('hello')
                       //   var targ;
                       // if (!e) var e = window.event;
                       // if (e.target) targ = e.target;
                       // else if (e.srcElement) targ = e.srcElement;
                       // alert(targ.id)
                        
                        
                        
                        
                       // }
                        
                        
			function check_leave()
			{
		
			
                      
                        
                        
			var total_hours = 0 ;
		
			
			var startdate = '';
			var enddate = '';
			
                        window.datevalues =new Array();
                        window.dateids =new Array();
			
			var rows = document.getElementsByTagName('tr');	
                      
		
			for (var i = 0; i < rows.length; i++) 
			{
			
			var rowid = rows[i].id;
	
			if (rowid == '') {continue;}
		
			var id1 = 'hours_' + rowid.substr(1);
		
			var hour = document.getElementById(id1);
			
			var id2 = 'date_' + rowid.substr(1);
			
			var date = document.getElementById(id2);
			
			if (hour.value != '') 
				{	
				
				
				if (checkhour (hour.value)) 
					{
				
				hour.style.backgroundColor = 'white';
			
				
				total_hours = Number(total_hours) + Number(hour.value)		
				
			
				
				
					} 
				else 
					{
				hour.style.backgroundColor = '#FF593F';
			
					}
				
				}
			else //hour value blank
				{
			if (date.value != '') {	hour.style.backgroundColor = '#FF593F';} else {hour.style.backgroundColor = 'white';}
		
				}
		
		
			var dayid = 'day_' + rowid.substr(1);
			var datespan = document.getElementById(dayid)	
				
				
				
			if (date.value == '' && hour.value != '')  
				{
				date.style.backgroundColor = '#FF593F'; 
				} else 
				{
				date.style.backgroundColor = 'white'
				
					
				}
			
			
				if (date.value.length == 10) 
					{
					
                                        var key = rowid.substr(1);
                                      
					window.datevalues.push(date.value);
                                        window.dateids.push(key);
                             
                        
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
		
			

			var totalhours_display = document.getElementById('total_hours');
			totalhours_display.value =  total_hours;
			totalhours_display.readOnly = true;
			
			//alert ('week1 on etotal is ' + week1_total);
			//alert ('week2 on etotal is ' + week2_total);
			
			//check for closed dates
                        
                       
                        var menu = document.getElementById('leavetype');
                         var leavetype  = menu.options[menu.selectedIndex].value;

                        var dateValuesString = datevalues.join();
                        var dateIdsString = dateids.join();
                        
                      

                       if (dateValuesString != '')
                        {
                        var timestamp = new Date();
                        var url = 'ajax_leave.php?m=$m&event=checkdate&type=' + leavetype + '&datevalues=' + dateValuesString + '&dateids=' + dateIdsString + '&rt=' + timestamp.getTime();
                        //alert(url)
                        ajax(url,blockedRows);
                       
                        }
                    

			}//end check_leave
			
			
                        
                        function blockedRows(response)
                        {
                        var n=response.indexOf('blocked'); 
                        if (n > -1)
                        {
                        //alert(response)
                        eval(response)
                       // alert(blocked.length)
                        
                        var rows = document.getElementsByTagName('tr');	
                        
                            	for (var i = 0; i < rows.length; i++) 
			{
                        //var text = '';
                        rows[i].style.background = 'white';
                        }
                        
                        
                        
                        
                       if  (document.getElementById('submit')) {document.getElementById('submit').disabled = false}
                       if  (document.getElementById('submitdraft')) {document.getElementById('submitdraft').disabled = false}
                   
                        
                        
                        
                        
                      	for (var i = 0; i < rows.length; i++) 
			{
                        var text = '';
                        for (var j = 0; j < blocked.length; j++) 
                            {
                            if (i == blocked[j]) {
                        text = '<span style=\"font-weight:bold\">$phrase[494]</span>';
                        
                         rows[i].style.background = 'red';
                        
                          if  (document.getElementById('submit')) {document.getElementById('submit').disabled = true}
                       if  (document.getElementById('submitdraft')) {document.getElementById('submitdraft').disabled = true}
                     
                        
                        }
                            
                     
                        
                            
                            }
                       if (i > 0)
                        {
                        var blockid = 'closed_' + i;
                        //alert(rowid)
                        document.getElementById(blockid).innerHTML = text;
                        }
                        }
                        
                        
                        }
                        
                        }
		
			addday()
			</script>";
			
			} //end detailed mode2
			
			
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
			<span style=\"width:10em;display: inline-block;\">$phrase[1002]</span><input type=\"text\" name=\"total_hours\" id=\"total_hours\"";
			if (isset($total_hours)) {echo " value=\"$total_hours\"";}
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo " > 
			
			
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
	datepicker('enddate');
	
	</script>
				
			";
			
			
			echo "	<br><br>
		<b>	$comment_label</b><br>
			<textarea name=\"comment\" cols=\"60\" rows=\"6\">";
			if (isset($comment) && $comment != '') {echo $comment;}
			echo "</textarea>
					
			
		<br><br>
			<input type=\"hidden\" name=\"leaveuser\" value=\"$leaveuser\">
			<input type=\"hidden\" name=\"m\" value=\"$m\">";
			
			if ($_REQUEST["event"] == "apply")
			{
			echo "<input type=\"hidden\" name=\"update\" value=\"addleave\">
			<input type=\"submit\" value=\"Submit\" id=\"submit\"> <input type=\"submit\" name=\"draft\" value=\"Save as draft\" id=\"submitdraft\">";	
			
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
			
			//$DB->query($sql,"leave.php");
			//$row = $DB->get();
			
			//$mode = $row["mode"];
		
			
			$leave_id = $DB->escape($_REQUEST["leave_id"]);		
		
		
			
			$sql = "select * from leave_requests where user_id = '$leaveuser' and leave_id = '$leave_id' ";
			//echo $sql;
			$DB->query($sql,"leave.php");
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
			
			
			
			$total_hours = $row["totalhours"];
			$approval_status = $row["approval_status"];
			$leave_type = $row["leave_type"];
			$leave_id = $row["leave_id"];	
			$comment = $row["comment"];	
			$processed = $row["processed"];	
		
			
			
			//	$sql = "select * from user where userid = $leaveuser";
			//$DB->query($sql,"leave.php");
			//$row = $DB->get();
		
			//$first_name = $row["first_name"];
			//$last_name = $row["last_name"];
			//$payroll_number = trim($row["payroll_number"]);
		
				
				echo "<br><br>
		<b>$phrase[1066] #</b> $leave_id<br><br>
				<b>$phrase[401]</b><br>	<form action=\"leave.php\" method=\"post\">";
				
				
				
				
			if ($approval_status == 3) {echo "<span  class=\"partoption\">$phrase[1071]</span>";} //draft
			if ($approval_status == 0) {echo "<span  class=\"partoption\">$phrase[1006]</span>";} //pending
			if ($approval_status == 1) {echo "<span class=\"yesoption\">$phrase[1007]</span>";} //approved
			if ($approval_status == 2) {echo "<span class=\"nooption\">$phrase[1008]</span>";} //rejected
			if ($approval_status == 4) {echo "<span class=\"canceloption\">$phrase[152]</span>";} //cancelled
				
			echo "<br><br>";
				
			
		
			
		//	print_r($_SESSION);
			
		
			
			echo "
		
		
			
			<b>$phrase[1004]</b><br>
			$payroll_number
			<br><br>
			<b>$phrase[1000] </b> <br>
			<select name=\"leavetype\"  id=\"leavetype\">";
			$sql = "select * from leave_category where m = '$m' and status = '1' order by position";
			//echo $sql;
			$DB->query($sql,"leave.php");
			while ($row = $DB->get())
			{
			$cat_name = $row["cat_name"];
			$cat_id = $row["cat_id"];
			echo "<option value=\"$cat_id\"";
			if (isset($leave_type) && $leave_type == $cat_id) {echo " selected";}
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
			
			//echo "startdate $startdate enddate $enddate";
			//if ($mode == 1)
			//{
			
		
			//}
			if ($mode == 1)
			{
			//echo "<b>First day </b><span id=\"firstday\"><input type=\"hidden\" name=\"startdate\" value=\"\"></span><br>
			//<b>Last day</b> <span id=\"lastday\"><input type=\"hidden\" name=\"enddate\" value=\"\"></span><br>
			//<b>$phrase[1002]</b> <span id=\"totalhours\"><input type=\"hidden\" name=\"total_hours\" value=\"\"></span>
			
			//";
			$rowcounter = 1;
			$weekcounter = 1;
			
			echo "<br>
			
			<br><br>
			<span onclick=\"addday();return false;\"><img src=\"../images/add.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\">$phrase[1010] </span><br><br>
		
			<table class=\"colourtable\" id=\"table\">
			<tr class=\"daterow\"><td style=\"width:10em\">$phrase[182]</td><td >$phrase[217]</td><td>$phrase[494]</td><td>$phrase[242]</td><td>$phrase[243]</td><td>$phrase[1003]</td><td>$phrase[474]</td><td></td></tr>";
			
			
			$sql = "select * from leave_dates where leave_id = '$leave_id' order by leave_date";
			//echo $sql;
			$DB->query($sql,"leave.php");
			$counter = 0;
			while ($row = $DB->get())
			{
			
			$date = $row["leave_date"];
			$_date = $row["leave_date"];
			$_break = $row["break"];
			$startminute = $row["startminute"];
			$starthour = $row["starthour"];
			$endminute = $row["endminute"];
			$endhour = $row["endhour"];
			$hours = $row["hours"];
		
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
			echo "  readonly></td><td id=\"blocked_$counter\">
			<td><select name=\"starthour[$counter]\" id=\"sh_$counter\" onclick=\"calc('$counter')\"><option value=\"\"> </option>";
			
			$temp = 6;
			while ($temp <23) 
			{
				if ($temp < 12) {$displayhour = $temp . " am";}
				elseif ($temp ==  12) {$displayhour = $temp . " pm";}
				elseif ($temp > 12 && $temp < 23) {$displayhour = $temp - 12 . " pm";}
				
				echo " 
			<option value=\"$temp\"";
			if ($starthour == $temp) {echo " selected";}
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo ">$displayhour</option>";
			$temp++;
			}

			
			echo "
			
		</select> <select name=\"startminute[$counter]\"  id=\"sm_$counter\" onclick=\"calc('$counter')\">";
			
			$temp = 0;
			while ($temp <60) 
			{
				if ($temp < 10) {$display = "0" . $temp ;} else {$display = $temp;}
				
				echo " 
				<option value=\"$temp\"";
				if ($startminute == $temp) {echo " selected";}
				if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
				echo ">$display</option>";
				$temp = $temp + 15;
			}

			
			echo "
			
		</select></td>
			<td><select name=\"endhour[$counter]\"  id=\"eh_$counter\" onclick=\"calc('$counter')\"><option value=\"\"> </option>";
			
			$temp = 6;
			while ($temp <23) 
			{
				if ($temp < 12) {$displayhour = $temp . " am";}
				elseif ($temp ==  12) {$displayhour = $temp . " pm";}
				elseif ($temp > 12 && $temp < 23) {$displayhour = $temp - 12 . " pm";}
				
				echo " 
			<option value=\"$temp\"";
			if ($endhour == $temp) {echo " selected";}
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo ">$displayhour</option>";
			$temp++;
			}

			
			echo "
			
		</select> <select name=\"endminute[$counter]\"  id=\"em_$counter\" onclick=\"calc('$counter')\">";
			
			$temp = 0;
			while ($temp <60) 
			{
				if ($temp < 10) {$display = "0" . $temp ;} else {$display = $temp;}
				
				echo " 
				<option value=\"$temp\"";
				if ($endminute == $temp) {echo " selected";}
				if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
				echo ">$display</option>";
				$temp = $temp + 15;
			}

			
			echo "
			
		</select></td>
			<td><select name=\"break[$counter]\"  id=\"b_$counter\" onchange=\"calc('$counter')\"  ";
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo ">";
			
				$breaks = explode("||",$break);
			
			if (isset($breaks))
			{
				foreach ($breaks as $i => $b)
				{
				echo "<option value=\"$b\"";
				if ($b == $_break) {echo " selected";}
				echo ">$b</option>";	
				}
			}
			
			
			echo "</select></td>
			<td><input type=\"text\" class=\"hours\" onkeyup=\"check_leave()\" id=\"hours_$counter\" name=\"hours[$counter]\" size=\"5\" value=\"$hours\" ";
			//if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo " readonly></td>
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
			
			

	";
	
	while ($c < $counter)
			{
			
				echo "datepicker('date_$c')
				";
			$c++;
			}
	
	echo "
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
                        newRow.class= 'daterow';
			var dateid = 'date_' + rowcount;
			cellDate.innerHTML = '<input type=\"text\" id=\"' + dateid + '\" name=\"date[' + rowcount + ']\" size=\"10\" readonly >'
			cellDate.onclick=check_leave
		
                         var cellClosed = newRow.insertCell(2);
                        cellClosed.id = 'closed_' + rowcount;
			
			datepicker(dateid);
			
			var cellStart = newRow.insertCell(3);
			cellStart.innerHTML = '<select id=\"sh_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"  name=\"starthour[' + rowcount + ']\"><option value=\"\"> </option>'";
			
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

			
			echo "
			
			+ '</select> <select id=\"sm_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"  name=\"startminute[' + rowcount + ']\">'";
			
			$temp = 0;
			while ($temp <60) 
			{
				if ($temp < 10) {$display = "0" . $temp ;} else {$display = $temp;}
				
				echo " 
				+ '<option value=\"$temp\">$display</option>'";
				$temp = $temp + 15;
			}

			
			echo "
			
			+ '</select>'     ;
			
			var cellStart = newRow.insertCell(4);
			cellStart.innerHTML = '<select  id=\"eh_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"  name=\"endhour[' + rowcount + ']\"><option value=\"\"> </option>'";
			
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

			
			echo "
			
			+ '</select> <select id=\"em_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"  name=\"endminute[' + rowcount + ']\">'";
			
			$temp = 0;
			while ($temp <60) 
			{
				if ($temp < 10) {$display = "0" . $temp ;} else {$display = $temp;}
				
				echo " 
				+ '<option value=\"$temp\">$display</option>'";
				$temp = $temp + 15;
			}

			
			
			//echo "break is $break";
			$breaks = explode("||",$break);
			$string = "";
			if (isset($breaks))
			{
				foreach ($breaks as $i => $b)
				{
				$string .= "<option value=\"$b\">$b</option>";	
				}
			}
			
			
			echo "
			
			+ '</select>'     ;
			
			var cellBreak = newRow.insertCell(5);
			cellBreak.innerHTML = '<select id=\"b_' + rowcount + '\" onchange=\"calc(' + rowcount + ')\"    name=\"break[' + rowcount + ']\" >$string</select>';
			
			var cellHours = newRow.insertCell(6);
			cellHours.innerHTML = '<input type=\"text\" readonly=\"readonly\"  class=\"hours\"  id=\"hours_' + rowcount + '\" name=\"hours[' + rowcount + ']\" size=\"5\"> ';
			
			var cellDelete = newRow.insertCell(7);
			cellDelete.innerHTML = '<span onclick=\"deleterow(\'' + rid + '\'); return false;\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></span>';
			
			document.getElementById('rowcount').value = rowcount;
			}
			
			
			
			
			function calc(id)
			{
		
			var element;
			var sel;
			
			
			element = 'sm_' + id;
			sel = document.getElementById(element);
			var sm = sel.options[sel.selectedIndex].value;
			if (sm == '00') {sm = 0;}
			
			element = 'sh_' + id;
			sel = document.getElementById(element);
			var sh = sel.options[sel.selectedIndex].value;
			
			element = 'em_' + id;
			sel = document.getElementById(element);
			var em = sel.options[sel.selectedIndex].value;
			if (em == '00') {em = 0;}
			
			element = 'eh_' + id;
			sel = document.getElementById(element);
			var eh = sel.options[sel.selectedIndex].value;
			
			element = 'b_' + id;
			sel = document.getElementById(element);
			var br = sel.options[sel.selectedIndex].value;
			
		
			
			";
			
			
			
		
			echo "
		
			
			//alert('sm is ' + sm + ' sh is ' + sh + ' em is ' + em + ' eh is ' + eh + ' b is ' + br)
			
			var time = (( ((eh * 60) + (em * 1) )  - ((sh * 60) + (sm * 1) ) )  - (br * 60) ) / 60;
			//var time = ((eh * 60) + (em * 1) )  ;
			//alert(time);
			
			time = (Math.floor(time * 100) /100)
			
			var hours = document.getElementById('hours_' + id);
			hours.value = time;
			
			check_leave();
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
			
			
			
			
			function checkhour (value)
			{
			if (value.length == 1)
			{
			testRegExp = /[0-9]/i;
			}
			if (value.length == 2)
			{
			testRegExp = /[0-9][\.0-9]/i;
			}
			if (value.length == 3)
			{
			testRegExp = /^[\.0-9][\.0-9][\.0-9]$/i;
			}
			if (value.length == 4)
			{
			testRegExp = /^[\.0-9][\.0-9][\.0-9][\.0-9]$/i;
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
		
		
			
			var total_hours = 0 ;
		
			
			var startdate = '';
			var enddate = '';
			
                        window.datevalues =new Array();
                        window.dateids =new Array();
			
			var rows = document.getElementsByTagName('tr');	
		
		
			for (var i = 0; i < rows.length; i++) 
			{
			
			var rowid = rows[i].id;
	
			if (rowid == '') {continue;}
		
			var id1 = 'hours_' + rowid.substr(1);
                     //   alert(id1)
			var hour = document.getElementById(id1);
			
			var id2 = 'date_' + rowid.substr(1);
			
			var date = document.getElementById(id2);
			
			if (hour.value != '') 
				{	
				
				
				if (checkhour (hour.value)) 
					{
				
				hour.style.backgroundColor = 'white';
			
				
				total_hours = Number(total_hours) + Number(hour.value)		
				
			
				
				
					} 
				else 
					{
				hour.style.backgroundColor = '#FF593F';
			
					}
				
				}
			else //hour value blank
				{
			if (date.value != '') {	hour.style.backgroundColor = '#FF593F';} else {hour.style.backgroundColor = 'white';}
		
				}
		
		
			var dayid = 'day_' + rowid.substr(1);
			var datespan = document.getElementById(dayid)	
				
				
				
			if (date.value == '' && hour.value != '')  
				{
				date.style.backgroundColor = '#FF593F'; 
				} else 
				{
				date.style.backgroundColor = 'white'
				
					
				}
			
			
				if (date.value.length == 10) 
					{
					 var key = rowid.substr(1);
                                      
					window.datevalues.push(date.value);
                                        window.dateids.push(key);
					
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
		
			

			var totalhours_display = document.getElementById('total_hours');
			totalhours_display.value =  total_hours;
			totalhours_display.readOnly = true;
			
			//alert ('week1 on etotal is ' + week1_total);
			//alert ('week2 on etotal is ' + week2_total);
			
                           var menu = document.getElementById('leavetype');
                         var leavetype  = menu.options[menu.selectedIndex].value;
                        
                        
                           var dateValuesString = datevalues.join();
                        var dateIdsString = dateids.join();
                        
                      

                       if (dateValuesString != '')
                        {
                        var timestamp = new Date();
                        var url = 'ajax_leave.php?m=$m&event=checkdate&type=' + leavetype + '&datevalues=' + dateValuesString + '&dateids=' + dateIdsString + '&rt=' + timestamp.getTime();
                        //alert(url)
                        ajax(url,blockedRows);
                       
                        }
                        
                        
                        
			
			}//end check_leave
			
		   function blockedRows(response)
                        {
                        var n=response.indexOf('blocked'); 
                        if (n > -1)
                        {
                      //  alert(response)
                        eval(response)
                       // alert(blocked.length)
                        
                        var rows = document.getElementsByTagName('tr');	
                        
                        	for (var i = 0; i < rows.length; i++) 
			{
                        //var text = '';
                        rows[i].style.background = 'white';
                        }
                        
                       if  (document.getElementById('submit')) {document.getElementById('submit').disabled = false}
                       if  (document.getElementById('submitdraft')) {document.getElementById('submitdraft').disabled = false}
                       if  (document.getElementById('updatedraft')) {document.getElementById('updatedraft').disabled = false}
                        
                        
                      	for (var i = 0; i < rows.length; i++) 
			{
                        var text = '';
                        
                        for (var j = 0; j < blocked.length; j++) 
                            {
                            if (i == blocked[j])
                                {
                              //  alert('i is' +  i)
                                text = '<span style=\"font-weight:bold\">$phrase[494]</span>';
                                 rows[i + 1].style.background = 'red';
                           if  (document.getElementById('submit')) {document.getElementById('submit').disabled = true}
                       if  (document.getElementById('submitdraft')) {document.getElementById('submitdraft').disabled = true}
                       if  (document.getElementById('updatedraft')) {document.getElementById('updatedraft').disabled = true}
                                //  alert('colouring red is' +  (i + 1))
                                }
                        
                            }

                        
                        var blockid = 'blocked_' + i;
                        
                       if (document.getElementById(blockid))
                            {
                             //  alert(blockid)
                        //alert(i)
                             document.getElementById(blockid).innerHTML = text;
                           
                            }
                        
                        }
                        
                        
                        }
                        }//end function
                        
			";
                        
                        if ($counter > 0)
                        {
                    echo "
                        window.onload=check_leave
                        ";
			
                        }
                        
                        echo "</script>";
                        
                        
			} //end detailed mode2
			
			
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
			<span style=\"width:10em;display: inline-block;\">$phrase[1002]</span><input type=\"text\" size=\"10\" name=\"total_hours\" id=\"total_hours\"";
			if (isset($total_hours)) {echo " value=\"$total_hours\"";}
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
		<b>	$comment_label</b><br>";
			if (isset($comment) && $comment != '') 
			{
			$_comment = nl2br($comment);
			echo $_comment;
			}
                        
                        if ($approval_status == 3 && isset($comment) && $comment != '') { echo "<p><a href=\"leave.php?m=$m&update=dm&leave_id=$leave_id\">$phrase[1109]</a></p><br>";}
                        
			echo "<textarea name=\"comment\" cols=\"60\" rows=\"6\">";
			
			echo "</textarea><br><br>	
				
				
			<input type=\"hidden\" name=\"year\" value=\"$sy\">	
			<input type=\"hidden\" name=\"update\" value=\"updateleave\">
			<input type=\"hidden\" name=\"leave_id\" value=\"$leave_id\">";
		if ($approval_status == 0) { echo "<input type=\"submit\" value=\"$phrase[16]\" id=\"submit\">"; } //update
		if ($approval_status == 3) { echo "<input type=\"submit\" name=\"submitdraft\" value=\"$phrase[192]\" id=\"submitdreaft\">  <input type=\"submit\" name=\"updatedraft\" value=\"$phrase[1072]\" id=\"updatedreaft\">  ";}	
			
			}
			
		
			echo "</form>
			
			
			";
			
			$sql = "select * from leave_log where leave_id = '$leave_id'";
			//echo $sql;
			echo "<div id=\"folder\"><h2>$phrase[728]</h2>";
			$DB->query($sql,"leave.php");
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
			$leave_id = $_REQUEST["leave_id"];
			
		echo "<h2>Are you sure you want to cancel application # $leave_id?</h2>
		
		<a href=\"leave.php?m=$m&update=cancel&leave_id=$leave_id\">$phrase[12]</a> | <a href=\"leave.php?m=$m\">$phrase[13]</a>";
		
		
	
			
			
		}
			
		
		elseif (isset($_REQUEST["view"]) && $_REQUEST["view"] == "cal")
		{
			
			
	echo "<br><br>";

	
	
	 $sql  = "select id, name,colour,pdays, pdate from leave_periods where m = '$m'";	
	$DB->query($sql,"leaveadmin.php");
	
	
		while ($row = $DB->get())
	{
		$id = $row["id"];
	$pname[$id] = $row["name"];
	$pcolour[$id] = $row["colour"];
	//$pdate[$id] = $row["pdate"];
	$pdays[$id] = $row["pdays"];
	
		$_y = substr($row["pdate"],0,4);
		$_m = substr($row["pdate"],5,2);
		$_d = substr($row["pdate"],8,4);	
	
	$pstart[$id] = mktime(0,0,0,$_m,$_d,$_y);
	}
	
	
	
	
	
	
	
	
	
			
			
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
		
		$DB->query($sql,"leave.php");
		$num = $DB->countrows();
		
		while ($row = $DB->get()) 
		{
			
		$a_holiday[] = $row["holiday"];
	
		$a_name[] = formattext($row["name"]);
		}
		
		
   
   
     $sql = "select distinct cat_name,cat_code,colour, approval_status, leave_requests.leave_id as leave_id,leave_type, totalhours, 	startdate, enddate from leave_category,leave_requests left join leave_dates
   on leave_requests.leave_id = leave_dates.leave_id
   where leave_requests.m = '$m' and user_id = '$leaveuser' 
 	and leave_dates.leave_id is NULL
   and ((startdate >= '$_fd' and startdate <= '$_ld') or (enddate >= '$_fd' and enddate <= '$_ld') or (startdate < '$_fd' and enddate > '$_ld'))
   and leave_category.cat_id = leave_requests.leave_type
   ";
   
     
     
 //echo $sql;

     
  $DB->query($sql,"leave.php");
	while ($row = $DB->get())
			{
			$_leave_mode[] = "simple";
			$_leave_cat[] = $row["cat_name"];	
			$_leave_status[] = $row["approval_status"];	
			$_leave_code[] = $row["cat_code"];	
			$_leave_colour[] = $row["colour"];	
			$_leave_id[] = $row["leave_id"];   
			$_leave_day[] = "";  
			$_leave_hours[] = $row["totalhours"];  
			$_leave_startdate[] = str_replace("-",'',$row["startdate"]); 
			$_leave_enddate[] = str_replace("-",'',$row["enddate"]); 
			//echo "end is " . str_replace("-",'',$row["enddate"]) ." bbb ";
			}
   

if ($DB->type == "mysql")
				{
   $sql = "select cat_name,cat_code,colour, approval_status, leave_requests.leave_id as leave_id,leave_type, hours, dayofmonth(leave_date) as day from leave_requests, leave_dates , leave_category
   where leave_requests.m = '$m' and user_id = '$leaveuser' 
   and leave_requests.leave_id = leave_dates.leave_id
   and leave_date >= '$_fd' and leave_date <= '$_ld'
   and leave_category.cat_id = leave_requests.leave_type
   ";
   
				}

else
				{
				$sql = "select cat_name,cat_code,colour, approval_status, leave_requests.leave_id as leave_id,leave_type, hours, strftime('%d',leave_date) as day from leave_requests, leave_dates , leave_category
   where leave_requests.m = '$m' and user_id = '$leaveuser' 
   and leave_requests.leave_id = leave_dates.leave_id
   and leave_date >= '$_fd' and leave_date <= '$_ld'
   and leave_category.cat_id = leave_requests.leave_type
   ";	
				}
				
				//echo $sql;
   
  $DB->query($sql,"leave.php");
	while ($row = $DB->get())
			{
			$_leave_mode[] = "detailed";
			$_leave_cat[] = $row["cat_name"];	
			$_leave_status[] = $row["approval_status"];	
			$_leave_code[] = $row["cat_code"];	
			$_leave_colour[] = $row["colour"];	
			$_leave_id[] = $row["leave_id"];   
			$_leave_day[] = $row["day"];  
			$_leave_hours[] = $row["hours"]; 
			$_leave_startday[] = ""; 
			$_leave_endday[] = "";  
			
			}
			
		
	//print_r($_leave_colour);			
  echo "<table  style=\"margin-left:2px\" class=\"colourtable\" id=\"calendar\" cellpadding=\"3\">
 <tr class=\"accent\"><td style=\"text-align:left\" valign=\"bottom\"> 
 <a href=\"leave.php?m=$m&amp;view=cal&amp;t=$lastmonth\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" >
   
   

   <form style=\"display:inline;\" action=\"leave.php\" method=\"get\">
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

	//$sql = "select first_name, last_name from user where userid = '$leaveuser'";
		//$DB->query($sql,"leaveadmin.php");
		//$row = $DB->get();
		//$last_name = $row["last_name"];
		//$first_name = $row["first_name"];
		
		//echo "<br><br><span style=\"font-size:1.6em\">$first_name $last_name</span>";

echo "</td>
   <td style=\"text-align:right\" valign=\"middle\">
   <a href=\"leave.php?m=$m&amp;view=cal&amp;t=$nextmonth\" class=\"hide\">$phrase[155]</a> </td></tr>
   
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
	if (isset($_leave_id))
	{	

	
		
	foreach ($_leave_id as $key => $id) 
		{
			//echo "date is $date start is $_leave_startdate[$key]	end is $_leave_enddate[$key]  mode is $_leave_mode[$key]";
			
			//echo "cat is $_leave_cat[$key] hours is $_leave_hours[$key] hours BBB<br>";
						echo "";
			
		if ($_leave_mode[$key] == "detailed" && $_leave_day[$key] == $daycount)
			{
			echo "<a href=\"leave.php?m=$m&event=view&leave_id=$id\" title=\"$_leave_cat[$key] $_leave_hours[$key] hours\"  style=\"color:#$_leave_colour[$key]\" >$_leave_code[$key]</a> $_leave_hours[$key]";
			if ($_leave_status[$key] == 1) {echo "<img src=\"../images/tick16.png\" alt=\"$phrase[1007]\" style=\"vertical-align: text-bottom; \">";}
			if ($_leave_status[$key] == 2 || $_leave_status[$key] == 4) {echo "<img src=\"../images/cross16.png\" alt=\"$phrase[1008]\" style=\"vertical-align: text-bottom; \">";}
			echo "<br>";	
			}
			
		if ($_leave_mode[$key] == "simple" && ($_leave_enddate[$key] >= $date && $_leave_startdate[$key] <= $date))
			{
			echo "<a href=\"leave.php?m=$m&event=view&leave_id=$id\" title=\"$_leave_cat[$key] $_leave_hours[$key] hours\" style=\"color:#$_leave_colour[$key]\">$_leave_code[$key]</a> ";
			if ($_leave_status[$key] == 1) {echo "<img src=\"../images/tick16.png\" alt=\"$phrase[1007]\" style=\"vertical-align: text-bottom; \">";}
			if ($_leave_status[$key] == 2 || $_leave_status[$key] == 4) {echo "<img src=\"../images/cross16.png\" alt=\"$phrase[1008]\" style=\"vertical-align: text-bottom; \">";}
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
    <tr class=\"accent\"><td style=\"text-align:left\" valign=\"bottom\">  <a href=\"leave.php?m=$m&amp;view=cal&amp;t=$lastmonth\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" ><span style=\"font-size:150%\"> $display</span></td>
   <td style=\"text-align:right\" valign=\"middle\">   <a href=\"leave.php?m=$m&amp;view=cal&amp;t=$nextmonth\" class=\"hide\">$phrase[155]</a></td></tr>
   </table>";
			
		}
		
		else 
		{
			
		
		if (isset($_REQUEST["category"])) {$category = $DB->escape($_REQUEST["category"]);}	
		if (isset($_REQUEST["status"])) {$status = $DB->escape($_REQUEST["status"]);}	
		if (isset($_REQUEST["filter"])) {$filter = $DB->escape($_REQUEST["filter"]);}	
		if (isset($_REQUEST["location"])) {$location = $DB->escape($_REQUEST["location"]);}		
			

		//if (isset($leaveuser))
		//{
		//$sql = "select first_name, last_name from user where userid = '$leaveuser'";
		//$DB->query($sql,"leaveadmin.php");
		//$row = $DB->get();
		//$last_name = $row["last_name"];
		//$first_name = $row["first_name"];
		
		//echo "<br><br><span style=\"font-size:1.6em\">$phrase[141] $first_name $last_name</span><br><br>
	//";	}
			
		
			
		
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
		<form action=\"leave.php\" style=\"display:inline\">
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
			if (!isset($leaveuser)) {echo "<td><a href=\"leave.php?m=$m&orderby=last_name";
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[131] </a></td>";}
			echo "<td><a href=\"leave.php?m=$m&orderby=startdate";
			if (isset($leaveuser)) {echo "&leaveuser=$leaveuser";}
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[267] </a></td><td>$phrase[268] </td><td>$phrase[1002]</td>
			<td style=\"width:20em\"><a href=\"leave.php?m=$m&orderby=leave_type";
			if (isset($leaveuser)) {echo "&leaveuser=$leaveuser";}
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[1000] </a></td><td><a href=\"leave.php?m=$m&orderby=approval_status";
			if (isset($leaveuser)) {echo "&leaveuser=$leaveuser";}
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[401]</a></td>";
			
			
			echo "<td></td><td></td></tr>
			";
			
			
			
			
			
			$sql = "select * from leave_category where m = '$m'";
			$DB->query($sql,"leave.php");
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
			$_status[4] = "$phrase[152]";
			
			
			if (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "startdate") { $orderby  = "startdate" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "approval_status") { $orderby  = "approval_status" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "leave_type") { $orderby  = "leave_type" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "last_name") { $orderby  = "last_name" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "processed") { $orderby  = "processed" ;}
			else {$orderby  = "startdate" ;}
			
			
		
				if ($DB->type == "mysql")
			{
		$sql = "select * from leave_requests where m = '$m' and user_id = '$leaveuser' and year(startdate) = '$year' order by $orderby";
			}
			
				else
			{
		$sql = "select * from leave_requests where m = '$m' and user_id = '$leaveuser' and strftime('%Y',startdate) = '$year' order by $orderby";
		
			}
			
		//echo $sql;
			
			
			
		//	echo $sql;
			$DB->query($sql,"leave.php");
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
			$total_hours = $row["totalhours"];
			$approval_status = $row["approval_status"];
			$leave_type = $row["leave_type"];
			$leave_id = $row["leave_id"];
		
			echo "<tr><td>$leave_id</td>";
			//if (isset($status) || isset($category) || isset($location))
			if (!isset($leaveuser))
			{
				$first_name = $row["first_name"];
				$last_name = $row["last_name"];	
				echo "<td>$last_name, $first_name</td>";
			} 
			echo "<td>$startdate</td><td>$enddate </td><td>$total_hours</td><td>$types[$leave_type]</td><td";
			
			if ($approval_status == 3) {echo " class=\"partoption\"";}
			if ($approval_status == 0) {echo " class=\"partoption\"";}
			if ($approval_status == 1) {echo " class=\"yesoption\"";}
			if ($approval_status == 2) {echo " class=\"nooption\"";}
			if ($approval_status == 4) {echo " class=\"canceloption\"";}
			
			echo ">$_status[$approval_status] </td>";
			//
			
			echo "<td><a href=\"leave.php?m=$m&event=view&leave_id=$leave_id\">$phrase[284]</a></td>";
			
			if ($approval_status == 1) {$type_totals[$leave_type] = $type_totals[$leave_type] + $total_hours;}
			
			
			
		
			
			echo "<td>";
			if ($approval_status== 0 || $approval_status== 3) {echo "<a href=\"leave.php?m=$m&event=cancel&leave_id=$leave_id\">$phrase[146]</a>";}
			echo "</td></tr>";
			}
			
			echo "</table>";
			
			if (isset($type_totals) && $summary == 1)
			{
			echo "<h4 style=\"margin-top:4em\">Approved for this year</h4><table class=\"colourtable\"><tr style=\"font-weight:bold\"><td style=\"padding: 10px;\">$phrase[1000] </td><td style=\"padding: 10px;\">$phrase[1002]</td></tr>";
			
			
			foreach ($type_totals   as $key => $value)
				{
				if ($value != 0) {echo "<tr><td style=\"padding: 10px;\">$types[$key] </td><td style=\"padding: 10px;\">$value</td></tr>";}
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