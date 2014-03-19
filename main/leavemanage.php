<?php


if (!isset($DATEFORMAT)) {$DATEFORMAT = "%d-%m-%Y";}

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$now = time();
$ip = ip("pc");

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
		
		
            if (isset($_SESSION["alias"])) {unset($_SESSION["alias"]);}
            
            
            
		$sql = "select * from modules where m = '$m'";
$DB->query($sql,"leavemanage.php");
$row = $DB->get();
$modname = formattext($row["name"]);
		
		
		
		
				 $sql  = "select * from leave_options where m = '$m'";	
	$DB->query($sql,"editform.php");
	
	$row = $DB->get();

	$mode = $row["mode"];
	$break = $row["break"];
	$introduction = $row["introduction"];
	$comment_label = $row["comment_label"];
	//$label = $row["label"];
	//$label_updated = $row["label_updated"];
	//$label_cancelled = $row["label_cancelled"];
	//$label_submitted = $row["label_submitted"];
	//$label_myleave = $row["label_myleave"];
		
		
		
		if (isset($_REQUEST["leave_id"]))
		{
			
			$leave_id = $DB->escape($_REQUEST["leave_id"]);	
			
			$sql = "select user_id from leave_requests where leave_id = '$leave_id'";
			$DB->query($sql,"leavemanage.php");
			$row = $DB->get();
			$user_id = $row["user_id"];
			$_REQUEST["leaveuser"] = $user_id;
			
			
			
		}
		
		
		$managers = array();
	
		$sql = "select userid, authority from leave_managers where userid = '$_SESSION[userid]'";

		$authority = 0;
		$DB->query($sql,"leaveadmin.php");
		while ($row = $DB->get())
		{
			$managers[] = $row["userid"];
			
			if ($row["authority"] > $authority) {$authority = $row["authority"];}
		}
		
		

		
		
		$members = array();
	
			$sql = "select distinct user.userid as userid, user.first_name as first_name, user.last_name as last_name from leave_members, leave_managers, user 
			where
			leave_members.location_id = leave_managers.location_id
			and user.userid = leave_members.userid
			and leave_managers.userid = '$_SESSION[userid]' order by last_name,first_name";
			//echo $sql;
			$DB->query($sql,"leavemanage.php");
			while ($row = $DB->get())
			{
			$members[] = $row["userid"];
			$index = $row["userid"];
			$memberfname[$index] = $row["first_name"];
			$memberlname[$index] = $row["last_name"];
			}
		
		//print_r($memberlname);
		
		if (isset($_REQUEST["leaveuser"]))
		{
		
	
			
		
			if (in_array($_REQUEST["leaveuser"],$members)) {$leaveuser = $DB->escape($_REQUEST["leaveuser"]);}
			

		}
		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deleteclosed" && (in_array($_SESSION["userid"],$managers)))
		{		
		
			$id = $DB->escape($_REQUEST["id"]);
	
		$sql = "delete from leave_closed where id = $id";
		$DB->query($sql,"leavemanage.php");
		
		
		}
                
                
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete" && ($_SESSION["userid"] == 1 || $authority == 3))
		{		
		
			$leave_id = $DB->escape($_REQUEST["leave_id"]);
	
		$sql = "delete from leave_dates where leave_id = $leave_id";
		$DB->query($sql,"leavemanage.php");
		
		$sql = "delete from leave_requests where leave_requests.m = '$m' and leave_id = $leave_id";
		$DB->query($sql,"leavemanage.php");
		
		
		}
		
		/*
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addleave" && isset($leaveuser) && $authority > 1)
		{		
		

		
		$total_hours = $DB->escape($_POST["total_hours"]);
		$startdate = $DB->escape($_POST["startdate"]);
		$enddate = $DB->escape($_POST["enddate"]);
		$leavetype = $DB->escape($_POST["leavetype"]);
		$comment = $DB->escape($_POST["comment"]);
		$now = time();
		$nowdate = strftime("%x",$now);
		
		$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
		
		//print_r($_REQUEST);
		
		
		if ($total_hours == 0 || $total_hours == "")
		
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

	
		$sql= "insert into leave_requests values(NULL,'$leaveuser','$leavetype','0','$_SESSION[username]','$now','$m','$total_hours','$startdate','$enddate','$_comment','0')";
		//echo $sql;
		$DB->query($sql,"leavemanage.php");
		$row = $DB->get();
		
		$leave_id = $DB->last_insert();
		
		//echo "leaveid is $leaveid";
		
		$hours =  $_REQUEST["hours"];
		$date =  $_REQUEST["date"];
		$starthour =  $_REQUEST["starthour"];
		$startminute =  $_REQUEST["startminute"];
		$endhour =  $_REQUEST["endhour"];
		$endminute =  $_REQUEST["endminute"];
		$break =  $_REQUEST["break"];
		
		//print_r($startminute);
		
		$dateslist = "";
		
		foreach ($hours as $key => $value)
			{
			if ($value != "" || $date[$key] != "") //insert row
				{
				
				$_hour = $DB->escape($value);	
				$_date = $DB->escape($date[$key]);	
				
				
			if ($DATEFORMAT == "%d-%m-%Y")
             {
				
				$_d = substr($date[$key],0,2); 
				$_m = substr($date[$key],3,2);
				$_y = substr($date[$key],6,4);
             }
             
              if ($DATEFORMAT == "%m-%d-%Y")
             {
             	$_d = substr($date[$key],3,2); 
				$_m = substr($date[$key],0,2);
				$_y = substr($date[$key],6,4);
             	
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
				if ($_startminute < 10) {$_startminute = "0" . $_startminute; }
				if ($_endminute < 10) {$_endminute = "0" . $_endminute;}
				$dateslist .= "$dayname $date[$key] $_hour $phrase[474] $_starthour:$_startminute$am1 - $_endhour:$_endminute$am2 $phrase[1003] $_break
";
				$DB->query($sql,"leavemanage.php");
				
				}
			}
			

				$sql = "select first_name, last_name, cat_name from user, leave_requests, leave_category
			where leave_requests.leave_id = '$leave_id' and user.userid = leave_requests.user_id
			and leave_category.cat_id = leave_requests.leave_type";
			$DB->query($sql,"leavemanage.php");
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
			
if (trim($comment) != "") {$message .= "$comment_label: $comment

";}
if (trim($dateslist) != "") {$message .= "$phrase[1001]: 
$dateslist
";} 
			
			
			
			
			
			
			
			
			
			
		
			
			
			//alert submission
			$sql = "select distinct user.userid,email from user, leave_members, leave_alertees
			where leave_members.userid = '$leaveuser' and leave_members.location_id = leave_alertees.location_id
			and leave_alertees.userid = user.userid";
			
			$DB->query($sql,"leavemanage.php");
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
			
			$DB->query($sql,"leavemanage.php");
			$row = $DB->get();
			$fromemail = $row["email"];
			
			$headers = "From: $fromemail";
			
		
			
			//echo $sql;
			
			$subject = "$phrase[1005] submitted";
			
		//echo $message;
				send_email($DB,$emailaddress, $subject, $message,$headers);				
			
			}
			
			
				//log submission
			$message = $DB->escape($message);	
			$ip = ip("pc");
			$sql = "insert into leave_log values(NULL,'$now','$_SESSION[username]','$ip','$leave_id','$message')";
		//	echo $sql;
			$DB->query($sql,"leavemanage.php");
			
			
			
			
			
			
			} else { $ERROR =  "Leave submission failed. Invalid dates provided";}
			
		}
		)
		*/
                
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updateclosed" && in_array($_SESSION["userid"],$managers)  && $authority > 1)
		{
    //print_r($_POST);
                $startdate = toMysqlDateFormat($_POST["startdate"],$DATEFORMAT);
		$enddate = toMysqlDateFormat($_POST["enddate"],$DATEFORMAT);
    
                if (isset($_POST["loc"]))
                {
                    $loc = $_POST["loc"] ;
                    
                } else {$loc = array();}
               
                if (isset($_POST["cat"]))
                {
                   // echo "hello";
                    $cat = $_POST["cat"] ;
                //    print_r($cat);
                    
                } else {$cat = array();}
    
                $category = "";
                $counter = 0;
               // print_r($_POST["cat"]);
               foreach ($cat as $key => $value)
               {
                
               if ($value == "on"){
               if ($counter > 0) {$category .= ":";}
               $category .= $key;
               $counter++;  
               }
               }
                
                  $location = "";
                $counter = 0;
               foreach ($loc as $key => $value)
               {
               if ($value == "on"){
               if ($counter > 0) {$location .= ":";}
               $location .= $key;
               $counter++;  
               }
               }
               
               $location = $DB->escape($location);
               $category = $DB->escape($category);
               
            
               if ($startdate != "0000-00-00" && $enddate != "0000-00-00")
               {
               $sql = "update leave_closed set startdate = '$startdate', enddate = '$enddate', leavegroup = '$location',leavetypes = '$category' where m = '$m' and id = '$id'";
             //  echo $sql;
               $DB->query($sql,"leavemanage.php");
               }
               
               
               
                
}                
                
                
                
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addclosed" && in_array($_SESSION["userid"],$managers)  && $authority > 1)
		{
    //print_r($_POST);
                $startdate = toMysqlDateFormat($_POST["startdate"],$DATEFORMAT);
		$enddate = toMysqlDateFormat($_POST["enddate"],$DATEFORMAT);
    
                if (isset($_POST["loc"]))
                {
                    $loc = $_POST["loc"] ;
                    
                } else {$loc = array();}
               
                if (isset($_POST["cat"]))
                {
                   // echo "hello";
                    $cat = $_POST["cat"] ;
                //    print_r($cat);
                    
                } else {$cat = array();}
    
                $category = "";
                $counter = 0;
               // print_r($_POST["cat"]);
               foreach ($cat as $key => $value)
               {
                
               if ($value == "on"){
               if ($counter > 0) {$category .= ":";}
               $category .= $key;
               $counter++;  
               }
               }
                
                  $location = "";
                $counter = 0;
               foreach ($loc as $key => $value)
               {
               if ($value == "on"){
               if ($counter > 0) {$location .= ":";}
               $location .= $key;
               $counter++;  
               }
               }
               
               $location = $DB->escape($location);
               $category = $DB->escape($category);
               
            
               if ($startdate != "0000-00-00" && $enddate != "0000-00-00" && $location != "" && $category != "")
               {
               $sql = "insert into leave_closed values(NULL,'$startdate','$enddate','$location','$category','$m')";
              // echo $sql;
               $DB->query($sql,"leavemanage.php");
               }
               
               
               
                
}
		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updateleave" && isset($leaveuser)  && $authority > 1)
		{		
		

		
		$leave_id = $DB->escape($_POST["leave_id"]);
		$total_hours = $DB->escape($_POST["total_hours"]);
		$startdate = $DB->escape($_POST["startdate"]);
		$enddate = $DB->escape($_POST["enddate"]);
		$leavetype = $DB->escape($_POST["leavetype"]);
		$comment = $DB->escape($_POST["comment"]);
		$approval_status = $DB->escape($_POST["approval_status"]);
		$processed = $DB->escape($_POST["processed"]);
		$now = time();
		$nowdate = strftime("%x",$now);
		//print_r($_REQUEST);
		
		
		$sql = "select m from leave_requests where leave_id = '$leave_id'";
		$DB->query($sql,"leavemanage.php");
		$row = $DB->get();
		$_m = $row["m"];
		
		if ($m != $_m) {
			//trying to update record from another module
			exit();
		}
		
		
		
		
		
		$pattern = '/^\d\d-\d\d-\d\d\d\d$/';



	if ($total_hours == 0 || $total_hours == "")
		
		{
			$ERROR =  "<br>Leave update failed. No hours submitted";
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
		
		
	
	
		$insert = "";
		
			if (trim($comment) != "") {$_comment =  "
$comment
<i>$_SESSION[username] $nowdate</i>
";} else {$_comment = "";}
			
			
			//echo "comment is $comment _comment is $_comment";
			
			//print_r($members);
			
		if (isset($members) && in_array($leaveuser,$members)) {$insert = " , approval_status = '$approval_status' , processed = '$processed'";}
		
	
		if ($DB->type == "mysql")
			{
		$sql= "update leave_requests set leave_type = '$leavetype',totalhours = '$total_hours',startdate = '$startdate',enddate = '$enddate',comment = CONCAT(comment,'$_comment') $insert where leave_requests.m = '$m' and leave_id = $leave_id and user_id = '$leaveuser'";
			}
			
				else
			{
		$sql= "update leave_requests set leave_type = '$leavetype',totalhours = '$total_hours',startdate = '$startdate',enddate = '$enddate',comment = comment || '$_comment' $insert where leave_requests.m = '$m' and leave_id = $leave_id and user_id = '$leaveuser'";
		
			}	
		
		
		//$sql= "update leave_requests set leave_type = '$leavetype',totalhours = '$total_hours',startdate = '$startdate',enddate = '$enddate',comment = CONCAT(comment,'$_comment') $insert where leave_id = $leave_id and user_id = '$leaveuser'";
	//echo $sql;
		$DB->query($sql,"leavemanage.php");
		$row = $DB->get();
		
		$dateslist = "";
		
		//echo "leaveid is $leaveid";
		//  print_r($_REQUEST);

		
		
		$sql = "delete from leave_dates where leave_id = $leave_id";
		$DB->query($sql,"leavemanage.php");
		$row = $DB->get();
		
		
		
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
				$_m = substr($date[$key],0,2);
				$_y = substr($date[$key],6,4);
             
             }
             
             
           
             
					$_date =  $DB->escape("$_y-$_m-$_d");
				
			//	$_hour = $DB->escape($value);	
			$_hour = $DB->escape($durations[$key]);
				//$_date = $DB->escape($date[$key]);	
				$_startminute = $DB->escape($startminute[$key]);	
				$_starthour = $DB->escape($starthour[$key]);	
			//	echo "key is $key endhours is $endhour[$key] <br>";
				
				$_endhour = $DB->escape($endhour[$key]);	
				$_endminute = $DB->escape($endminute[$key]);	
				$_break = $DB->escape($break[$key]);	
				
				$dayname = strftime("%a",mktime(0,0,0,$_m,$_d,$_y));
				
				$sql = "insert into leave_dates values('$_hour','$_startminute','$_starthour','$_endminute','$_endhour','$_break','$leave_id','$_date')";
				
				$am1 = "am";
				$am2 = "am";
				
				if ($_starthour > 12) {$_starthour = $_starthour - 12 ; $am1 = "pm";}
				if ($_endhour > 12) {$_endhour = $_endhour - 12 ; $am2 = "pm";}
				if ($_startminute < 10) {$_startminute = "0" . $_startminute; }
				if ($_endminute < 10) {$_endminute = "0" . $_endminute;}
				
				
				$dateslist .= "$dayname $date[$key] $_hour $phrase[474] $_starthour:$_startminute$am1 - $_endhour:$_endminute$am2 $phrase[1003] $_break
";
				$DB->query($sql,"leavemanage.php");
				
				}
			}
		}

		
			$status[0] = "$phrase[1006]";
			$status[1] = "$phrase[1007]"; //approved
			$status[2] = "$phrase[1008]"; // rejected by manager
			$status[4] = "$phrase[152]"; // cancelled by user
			
			
				$sql = "select email,first_name, last_name, cat_name from user, leave_requests, leave_category
			where leave_requests.leave_id = '$leave_id' and user.userid = leave_requests.user_id
			and leave_category.cat_id = leave_requests.leave_type";
			$DB->query($sql,"leavemanage.php");
			$row = $DB->get();
			$first_name = $row["first_name"];
			$cat_name = $row["cat_name"];
			$last_name = $row["last_name"];
			$email = $row["email"];
			
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




$startdate = $sy . $sm . $sd;
$enddate = $ey . $em . $ed;	
			
			$message = "
$phrase[141]: $first_name $last_name
$phrase[1000] : $cat_name
$phrase[267]:  $start	
$phrase[268]: $end
$phrase[1002]: $total_hours	
Status: $status[$approval_status]							
";
			
if (trim($comment) != "") {$message .= "Comment: $_POST[comment]

";}
if (trim($dateslist) != "") {$message .= "$phrase[1001]: 
$dateslist
";} 
			
	
			
			
			//alert submission
			
			$sql = "select email from user where userid = '2235'";
			//echo $sql;
			
			//$DB->query($sql,"leavemanage.php");
			//while ($row = $DB->get())
			//{
			//$email = $row["email"];
			//if (filter_var($email, FILTER_VALIDATE_EMAIL))
				//{
				//$emails[] = $email;	
				//}
			//}
			
			$emails[] = $email;	
			
			if (isset($emails))
			{
				$emailaddress = "";
				$counter= 0;
	
			foreach ($emails as $key => $value) {
			if ($counter != 0) {$emailaddress .= ",";}
			$emailaddress .= "$value";
			$counter++;
			}

		//	echo "Sending $emailaddress";
			
			
			$sql = "select email from user
			where userid = '$_SESSION[userid]'";
			
			$DB->query($sql,"leavemanage.php");
			$row = $DB->get();
			$fromemail = $row["email"];
			
			$headers = "From: $fromemail";
			//updated
			$subject = "$modname - $phrase[1064] - $status[$approval_status]";
	
		//echo $message;
				send_email($DB,$emailaddress, $subject, $message,$headers);				
			
			}
			
			
			
			
			
				//log submission
			
			$ip = ip("pc");
			$message = $DB->escape($message);
			$sql = "insert into leave_log values(NULL,'$now','$_SESSION[username]','$ip','$leave_id','$message')";
			
			$DB->query($sql,"leavemanage.php");
			
			
			
			
} else {$ERROR =  "Leave update failed. Invalid dates provided";}
			
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
$sql = "select * from modules where m = '$m'";
$DB->query($sql,"leavemanage.php");
$row = $DB->get();
$modname = formattext($row["name"]);
//$input = $row["input"];


$datepicker = "yes";

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
			page_view($DB,$PREFERENCES,$m,"");


		
			 
	  	include("../includes/leftsidebar.php");   
		
		
	 
		
			

	  	echo "<div style=\"float:left;	width:78%;	padding: 1em 0;	margin:0;\" class=\"leave2\"><div>";
	





echo " <h1>$modname</h1>  


		
	
		";


			if (isset($_REQUEST["filter"])) {$filter = $_REQUEST["filter"];}
	
			if (isset($_REQUEST["year"])) {$year = $DB->escape($_REQUEST["year"]);} else {$year = date("Y");}
			
			//list leave
			
			
			//check if manager
				
	$me = $_SESSION["userid"];
		
		
	echo "<span class=\"leave1\"><a href=\"leave.php?m=$m&event=apply\">$phrase[994]</a>
	 | <a href=\"leave.php?m=$m&view=cal&leaveuser=$me\">$phrase[999]</a>
	  | <a href=\"leave.php?m=$m&event=cal&leaveuser=$me\">$phrase[998]</a>
		
		</span><br><br>";
			
			
			if (in_array($_SESSION["userid"],$managers))
			
		{
		echo "<span class=\"leave2\">";
		//	if ($authority > 1) {echo "<a href=\"leavemanage.php?m=$m&event=apply&leaveuser=$leaveuser\">$phrase[1011]s</a>  | ";}
		
			
			
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
			echo "\">$phrase[152]</a>  | <a href=\"leavemanage.php?m=$m&event=closed";
			if (isset($year)) { echo "&year=$year";}
			echo "\">$phrase[494]</a> | <a href=\"leavemanage.php?m=$m&filter=cat";
			if (isset($year)) { echo "&year=$year";}
			echo "\">Categories</a>  | <a href=\"leavemanage.php?m=$m&filter=location";
			if (isset($year)) { echo "&year=$year";}
			echo "\">Groups</a></span><br><br> ";
		}


                if (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "closed"))
		{
                       echo " <h2>$phrase[494]</h2>
                    
               <a href=\"leavemanage.php?m=$m&event=addclosed\">Add</a><br><br>
                    ";
			$locations = array();
                       $sql = "select * from leave_location where m = '$m' ";
                       $DB->query($sql,"leavemanage.php");
                       while ($row = $DB->get())
                        {
                        $location_id = $row["location_id"];
			$locations[$location_id]  = $row["location_name"];
                       
                        }
                       
                        
                        $types = array();
                       $sql = "select * from leave_category where m = '$m' ";
                       $DB->query($sql,"leavemanage.php");
                       while ($row = $DB->get())
                        {
                        $cat_id = $row["cat_id"];
			$types[$cat_id]  = $row["cat_name"];
                       
                        }
                       
                       $sql = "select * from leave_closed";
                       $DB->query($sql,"leavemanage.php");
                       echo "<table class=\"colourtable\"><tr style=\"font-weight:bold\"><td>start</td><td>finish</td><td>Groups</td><td>Leave types</td><td colspan=\"2\"></td></tr>";
			while ($row = $DB->get())
                        {
			$id = $row["id"];
			$startdate = fromMysqlDateFormat($row["startdate"],$DATEFORMAT);
                        $enddate = fromMysqlDateFormat($row["enddate"],$DATEFORMAT);
                        $leavegroup = $row["leavegroup"];
                        $leavetypes = $row["leavetypes"];
                        echo "<tr><td>$startdate</td><td>$enddate</td><td><ul>";
                        $peices = explode(":",$leavegroup);
                        foreach ($peices as $peice)
                        {
                            if (array_key_exists($peice,$locations)) {echo "<li>$locations[$peice] </li>";}
                        }
                        
                        
                        
                        echo "</ul></td><td><ul>";
                        
                         $peices = explode(":",$leavetypes);
                        foreach ($peices as $peice)
                        {
                            if (array_key_exists($peice,$types)) {echo "<li>$types[$peice] </li>";}
                        }
                        
                        
                        echo "</ul></td><td><a href=\"leavemanage.php?m=$m&event=editclosed&id=$id\">edit</td>
                        <td><a href=\"leavemanage.php?m=$m&event=closed&update=deleteclosed&id=$id\">delete</a></td></tr>";
                        }
                       echo "</table>";
                }
               
                elseif (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "editclosed"))
                {
                    
                    
                       $sql = "select * from leave_closed where id = '$id'";
                       $DB->query($sql,"leavemanage.php");
                     
			$row = $DB->get();
                     
			$id = $row["id"];
			$startdate = fromMysqlDateFormat($row["startdate"],$DATEFORMAT);
                        $enddate = fromMysqlDateFormat($row["enddate"],$DATEFORMAT);
                        $leavegroup = $row["leavegroup"];
                        $leavetypes = $row["leavetypes"];
                    
                    
                         $peices = explode(":",$leavegroup);
                         $locations = array();
                        foreach ($peices as $peice)
                        {
                           $locations[] = $peice;
                        }
                        
                         $peices = explode(":",$leavetypes);
                         $types = array();
                        foreach ($peices as $peice)
                        {
                           $types[] = $peice;
                        }
                        
                //    print_r($types);
                //     print_r($locations);
                    
                       echo " <h2>$phrase[494] </h2>
                    
                    <form action=\"leavemanage.php\" method=\"post\"><table >
                    <tr><td>Start </td><td><input type=\"text\" name=\"startdate\" id=\"startdate\" value=\"$startdate\" > </td><tr>
                     <tr><td>End </td><td><input type=\"text\" name=\"enddate\" id=\"enddate\" value=\"$enddate\" > </td><tr>
                     <tr><td>Types </td><td style=\"border:solid 1px grey\">";
                       
                       
                           $sql = "select distinct(cat_name), cat_id  from leave_category, leave_managers where
                       ((authority = '4') or (authority = '3') or (authority = 2 and restricted = '0')) and userid = '$_SESSION[userid]' and
                                    m = '$m'
                              ";
                                
                                
			//echo $sql;
                      //  print_r($_SESSION);
					$DB->query($sql,"leavemanage.php");
					while ($row = $DB->get())
					{
					$cat_name = $row["cat_name"];
                                        $cat_id = $row["cat_id"];
                                        echo "<input type=\"checkbox\" name=cat[$cat_id]";
                                         foreach ($types as $type)
                                            {
                                             if ($type == $cat_id) {echo " checked";}
                                            }
                                         
                                        echo "> $cat_name<br>";
                                        }
                       
                       echo "</td><tr>
                    <tr><td>Groups </td><td style=\"border:solid 1px grey\">";
                       
                         $sql = "select location_name, leave_location.location_id as id from leave_location, leave_managers where
                                    leave_managers.location_id = leave_location.location_id
                                    and userid = '$_SESSION[userid]' and m = '$m'
                                and authority > '0'";
                                
                                
			//echo $sql;
                      //  print_r($_SESSION);
					$DB->query($sql,"leavemanage.php");
					while ($row = $DB->get())
					{
					$location_name = $row["location_name"];
                                        $loc_id = $row["id"];
                                        echo "<input type=\"checkbox\" name=loc[$id]";
                                         foreach ($locations as $location)
                                            {
                                             if ($location == $loc_id) {echo " checked";}
                                            }
                                         
                                        echo "> $location_name<br>";
                                        }
                       
                       echo "</td><tr>
                    
                    <tr><td> </td><td><input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
                    <input type=\"hidden\" name=\"m\" value=\"$m\">
                   <input type=\"hidden\" name=\"id\" value=\"$id\">
                   <input type=\"hidden\" name=\"event\" value=\"closed\">
                    <input type=\"hidden\" name=\"update\" value=\"updateclosed\">
                    </td><tr>
                    
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
                    
                    datepicker('startdate')
                    datepicker('enddate')
                    
	</script>
                    "; 
                    
                    
                }
                
                
                
                
                elseif (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "addclosed"))
                {
                       echo " <h2>$phrase[494]</h2>
                    
                    <form action=\"leavemanage.php\" method=\"post\"><table >
                    <tr><td>Start </td><td><input type=\"text\" name=\"startdate\" id=\"startdate\"> </td><tr>
                     <tr><td>End </td><td><input type=\"text\" name=\"enddate\" id=\"enddate\"> </td><tr>
                     <tr><td>Types </td><td style=\"border:solid 1px grey\">";
                       
                       
                           $sql = "select distinct(cat_name), cat_id  from leave_category, leave_managers where
                       ((authority = '4') or (authority = '3') or (authority = 2 and restricted = '0')) and userid = '$_SESSION[userid]' and
                                    m = '$m'
                              ";
                                
                                
			//echo $sql;
                      //  print_r($_SESSION);
					$DB->query($sql,"leavemanage.php");
					while ($row = $DB->get())
					{
					$cat_name = $row["cat_name"];
                                        $cat_id = $row["cat_id"];
                                        echo "<input type=\"checkbox\" name=cat[$cat_id]> $cat_name<br>";
                                        }
                       
                       echo "</td><tr>
                    <tr><td>Groups </td><td style=\"border:solid 1px grey\">";
                       
                         $sql = "select location_name, leave_location.location_id as id from leave_location, leave_managers where
                                    leave_managers.location_id = leave_location.location_id
                                    and userid = '$_SESSION[userid]' and m = '$m'
                                and authority > '0'";
                                
                                
			//echo $sql;
                      //  print_r($_SESSION);
					$DB->query($sql,"leavemanage.php");
					while ($row = $DB->get())
					{
					$location_name = $row["location_name"];
                                        $id = $row["id"];
                                        echo "<input type=\"checkbox\" name=loc[$id]> $location_name<br>";
                                        }
                       
                       echo "</td><tr>
                    
                    <tr><td> </td><td><input type=\"submit\" name=\"submit\" value=\"Add\">
                    <input type=\"hidden\" name=\"m\" value=\"$m\">
                   <input type=\"hidden\" name=\"event\" value=\"closed\">
                    <input type=\"hidden\" name=\"update\" value=\"addclosed\">
                    </td><tr>
                    
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
                    
                    datepicker('startdate')
                    datepicker('enddate')
                    
	</script>
                    "; 
                    
                    
                }
                
		elseif (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "apply"))
		{
			

			

			
			if (!isset($leaveuser)) {$leaveuser = $_SESSION["userid"];}
			
			
			
		//	print_r($_SESSION);
			
			$sql = "select first_name, last_name from user where userid = $leaveuser";
			//echo $sql;
			$DB->query($sql,"leavemanage.php");
			$row = $DB->get();
		
			$first_name = $row["first_name"];
			$last_name = $row["last_name"];
			
			
			echo "
		<h2>$phrase[1011]</h2>
			<form action=\"leavemanage.php\" method=\"post\"><br>
		<b>$phrase[141]</b><br> ";
			
			if (in_array($_SESSION["userid"],$managers))
			{
			
			echo "<select name=\"leaveuser\" >";
			
$sql = "SELECT user.userid as userid,first_name, last_name 
FROM  user , leave_managers, leave_members
where 
(leave_managers.userid = '$_SESSION[userid]' and  leave_managers.location_id = leave_members.location_id and leave_members.userid = user.userid)
or user.userid ='$_SESSION[userid]'
GROUP BY user.userid order by last_name";
			
			
					$DB->query($sql,"leavemanage.php");
					while ($row = $DB->get())
					{
					$first_name= $row["first_name"];
					$last_name= $row["last_name"];
					$total= $row["total"];
					$userid= $row["userid"];
					echo "<option value=\"$userid\" ";
					if ($userid == $leaveuser) {echo " selected";}
					echo ">$first_name $last_name</option>";
					}
			
			echo "
		</select>";
			
			}
			else 
			{
				
				$sql = "select first_name,last_name, total, userid from user where userid = '$_SESSION[userid]'";
				$DB->query($sql,"leavemanage.php");
					$row = $DB->get();
					
					$first_name= $row["first_name"];
					$last_name= $row["last_name"];
					$total= $row["total"];
					$userid= $row["userid"];
					
					
					echo "<span >$first_name $last_name</span <input type=\"hidden\" name=\"leaveuser\" value=\"$userid\">";
			}
			
			echo "
		<br><br>
			<b>$phrase[1000] </b> <br>
			<select name=\"leavetype\">";
			if ($authority == 3)
			{
			$sql = "select cat_name, cat_id from leave_category where m = '$m' and status = '1' order by position";
			}
			else {
				
			$sql = "select cat_name, cat_id from leave_category where m = '$m' and status = '1' and (restricted = '0' or cat_id = '$leave_type') order by position";	
			}
			//echo $sql;
			$DB->query($sql,"leavemanage.php");
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
			
	
			<span onclick=\"addday();return false;\"><img src=\"../images/add.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\">$phrase[1010]</span><br><br>
		
			<table class=\"colourtable\" id=\"table\">
			<tr ><td style=\"width:10em\">$phrase[182]</td><td >$phrase[217]</td><td>$phrase[242]</td><td>$phrase[242]</td><td>$phrase[1003]</td><td>$phrase[474]</td><td></td></tr>
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
			cellDate.innerHTML = '<input type=\"text\" size=\"10\" id=\"' + dateid + '\" name=\"date[' + rowcount + ']\" size=\"10\" >'
			cellDate.onclick=check_leave
		
			
		datepicker(dateid);
	
			
			var cellStart = newRow.insertCell(2);
			cellStart.innerHTML = '<select name=\"starthour[' + rowcount + ']\"  id=\"sh_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"> <option value=\"\"> </option>'";
			
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
			
			+ '</select> <select name=\"startminute[' + rowcount + ']\"  id=\"sm_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\">'";
			
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
			
			var cellStart = newRow.insertCell(3);
			cellStart.innerHTML = '<select name=\"endhour[' + rowcount + ']\"  id=\"eh_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"><option value=\"\"> </option>'";
			
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
			
			+ '</select> <select name=\"endminute[' + rowcount + ']\"  id=\"em_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\">'";
			
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
			
			var cellBreak = newRow.insertCell(4);
			cellBreak.innerHTML = '<input type=\"text\" name=\"break[' + rowcount + ']\" size=\"5\" id=\"b_' + rowcount + '\" onkeyup=\"calc(' + rowcount + ')\"> ';
			
			var cellHours = newRow.insertCell(5);
			cellHours.innerHTML = '<input type=\"text\" class=\"hours\" onkeyup=\"check_leave()\" id=\"hours_' + rowcount + '\" name=\"hours[' + rowcount + ']\" size=\"5\"> ';
			
			var cellDelete = newRow.insertCell(6);
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
			
			var bsel = document.getElementById('b_' + id);
			var br = bsel.value
			";
			
		
			
			//echo "break is $break";
			$breaks = explode("||",$break);
			$string = "";
			if (isset($breaks))
			{
				foreach ($breaks as $i => $b)
				{
				$string .= " && bsel.value != '$b'";	
				}
			}
			echo "
			if (bsel.value != '' && bsel.value != '0' $string)
			{
			bsel.style.background = 'red';
			} else {bsel.style.background = 'white';}
			
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
			//alert(d.getUTCDay());
			}
			
			
			function check_leave()
			{
		
			
			var total_hours = 0 ;
		
			
			var startdate = '';
			var enddate = '';
			
		
			
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
			
			
			}//end check_leave
			
			
		//	var inputs = document.getElementsByTagName('input');
			// for (var i = 0; i < inputs.length; i++) {
		//var theInput = inputs[i];
		///if (theInput.className == 'hours')
			// {
		//	theInput.onkeyup=check_leave;
		//	}
			// }
			
		
			addday()
			</script>";
			
			} //end detailed mode2
			
		
						echo "
			
<br><br>
			
			<span style=\"width:10em;display: inline-block;\">$phrase[267]</span><input type=\"text\" size=\"10\" id=\"startdate\" name=\"startdate\" ";
			if (isset($startdate)) {echo " value=\"$startdate\"";}
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo "><br>
			<span style=\"width:10em;display: inline-block;\">$phrase[268]</span><input type=\"text\" size=\"10\" name=\"enddate\" id=\"enddate\"";
			if (isset($enddate)) {echo " value=\"$enddate\"";}
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo "><br>
			<span style=\"width:10em;display: inline-block;\">$phrase[1002]</span><input type=\"text\" name=\"total_hours\" id=\"total_hours\"";
			if (isset($total_hours)) {echo " value=\"$total_hours\"";}
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo " > 
				<script type=\"text/javascript\">
				datepicker('startdate');
	datepicker('enddate');
			</script>
				
			
		<br><br>
		<b>$comment_label</b><br>
			<textarea name=\"comment\" cols=\"60\" rows=\"6\">";
			if (isset($comment) && $comment != '') {echo $comment;}
			echo "</textarea><br><br>
			<input type=\"hidden\" name=\"leaveuser\" value=\"$leaveuser\">
			<input type=\"hidden\" name=\"m\" value=\"$m\">";
			
		
			if ( $authority > 1)
			{
			echo "<input type=\"hidden\" name=\"update\" value=\"addleave\">
			<input type=\"submit\" value=\"Submit\"> ";	
			
			}
		
			echo "</form>
			
			
			
			
			";
			
		}
				elseif (isset($ERROR))
		{
		
		echo "<h1 style=\"color:#FF593F\">$ERROR</h1>";	
			
		}

		
		
				elseif (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "delete"))
		{
			
			
		
			
	echo "<br><br><b>$phrase[14]</b><br><br>Leave application # $leave_id <br><br>
	<a href=\"leavemanage.php?m=$m&amp;update=delete&amp;leave_id=$leave_id&amp;year=$year&amp;leave_user=$leaveuser\">$phrase[12]</a> | <a href=\"leavemanage.php?m=$m&amp;leave_id=$leave_id&amp;year=$year&amp;leave_user=$leaveuser\">$phrase[13]</a>";
			
		}
		
		
		
		
		elseif (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "view"))
		{
			
			
			
		
		
			
			
		
		
			
			$sql = "select * from leave_requests where approval_status != '3' and user_id = '$leaveuser' and leave_id = '$leave_id' ";
			//echo $sql;
			$DB->query($sql,"leavemanage.php");
			$row = $DB->get();
			
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

			
			$total_hours = $row["totalhours"];
			$approval_status = $row["approval_status"];
			$leave_type = $row["leave_type"];
			$leave_id = $row["leave_id"];	
			$comment = $row["comment"];	
			$processed = $row["processed"];	
		
			
			
				$sql = "select * from user where userid = $leaveuser";
			$DB->query($sql,"leavemanage.php");
			$row = $DB->get();
		
			$first_name = $row["first_name"];
			$last_name = $row["last_name"];
			$payroll_number = trim($row["payroll_number"]);
		
				echo "<p style=\"font-size:1.8em;margin: 0.8em 0\">$first_name $last_name</p>";
				echo "
		<b>$phrase[1066] #</b> $leave_id<br><br>
				<b>$phrase[401]</b><br>	<form action=\"leavemanage.php\" method=\"post\">";
				
				if (isset($members) && in_array($leaveuser,$members))
				{
				echo "<select name=\"approval_status\">
				<option value=\"0\"";
				if ($approval_status == "0") {echo " selected";}
				echo ">$phrase[1006] approval</option>
				<option value=\"1\"";
				if ($approval_status == "1") {echo " selected";}
				echo ">$phrase[1007]</option>
				<option value=\"2\"";
				if ($approval_status == "2") {echo " selected";}
				echo ">$phrase[1008]</option>
				<option value=\"4\"";
				if ($approval_status == "4") {echo " selected";}
				echo ">$phrase[152]</option>
				</select>";
				
				echo "<br><br><b>Processed</b><br><select  name=\"processed\">
			<option value=\"1\">$phrase[221]</option><option value=\"2\"";
			if ($processed == 2) {echo " selected";}
			echo ">$phrase[1067]</option><option value=\"0\"";
			if ($processed == 0) {echo " selected";}
			echo ">$phrase[13]</option>
				</select>";
				}
				else 
				{
				
				
				
			if ($approval_status == 0) {echo "<spanclass=\"partoption\">$phrase[1006]</span>";} //pending
			if ($approval_status == 1) {echo "<span class=\"yesoption\"\">$phrase[1007]</span>";} //approved
			if ($approval_status == 2) {echo "<span class=\"nooption\">$phrase[1008]</span>";} //rejected
			if ($approval_status == 4) {echo "<span class=\"canceloption\">$phrase[152]</span>";} //cncelled
			
				}
			echo "<br><br>";
				
			
		
			
		//	print_r($_SESSION);
			
		
			
			echo "
		
		
			
			<b>$phrase[1004]</b><br>
			$payroll_number
			<br><br>
			<b>$phrase[1000] </b> <br>
			<select name=\"leavetype\" id=\"leavetype\">";
			if ($authority > 2)
			{
			$sql = "select * from leave_category where m = '$m' and status = '1' order by position";
			}
			else {
				$sql = "select * from leave_category where m = '$m' and status = '1' and restricted = '0' order by position";
			}
			//echo $sql;
			$DB->query($sql,"leavemanage.php");
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
			
		var targid = id
		
		new JsDatePick({
			useMode:2,
			target:targid,
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
			
		
			<span onclick=\"addday();return false;\"><img src=\"../images/add.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\">$phrase[1010] </span><br><br>
		
			<table class=\"colourtable\" id=\"table\">
			<tr ><td style=\"width:10em\">$phrase[182]</td><td >$phrase[217]</td><td>$phrase[494]</td><td>$phrase[242]</td><td>$phrase[243]</td><td>$phrase[1003]</td><td>$phrase[474]</td><td></td></tr>";
			
			
			$sql = "select * from leave_dates where leave_id = '$leave_id' order by leave_date";
			$DB->query($sql,"leavemanage.php");
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
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo "></td><td id=\"blocked_$counter\"></td>
			<td><select name=\"starthour[$counter]\"  id=\"sh_$counter\" onclick=\"calc('$counter')\"><option value=\"\"> </option>";
			
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
			
		</select> <select name=\"startminute[$counter]\" id=\"sm_$counter\" onclick=\"calc('$counter')\">";
			
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
			<td><select name=\"endhour[$counter]\" id=\"eh_$counter\" onclick=\"calc('$counter')\"><option value=\"\"> </option>";
			
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
			
		</select> <select name=\"endminute[$counter]\" id=\"em_$counter\" onclick=\"calc('$counter')\">";
			
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
			echo "></td>
			<td>";
			//if (isset($approval_status) && $approval_status > 0) {} else {
			echo "<span onclick=\"deleterow('r$counter'); return false;\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></span>";
			//}
			
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
			var dateid = 'date_' + rowcount;
			cellDate.innerHTML = '<input type=\"text\" id=\"' + dateid + '\" name=\"date[' + rowcount + ']\" size=\"10\" >'
			cellDate.onclick=check_leave
		
			var cellClosed = newRow.insertCell(2);
                        cellClosed.id = 'closed_' + rowcount;
                        
			datepicker(dateid);
			
			var cellStart = newRow.insertCell(3);
			cellStart.innerHTML = '<select name=\"starthour[' + rowcount + ']\"  id=\"sh_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"><option value=\"\"> </option>'";
			
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
			
			+ '</select> <select name=\"startminute[' + rowcount + ']\"  id=\"sm_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\">'";
			
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
			cellStart.innerHTML = '<select name=\"endhour[' + rowcount + ']\"  id=\"eh_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\"><option value=\"\"> </option>'";
			
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
			
			+ '</select> <select name=\"endminute[' + rowcount + ']\"  id=\"em_' + rowcount + '\" onclick=\"calc(' + rowcount + ')\">'";
			
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
			cellHours.innerHTML = '<input type=\"text\" class=\"hours\" onkeyup=\"check_leave()\" id=\"hours_' + rowcount + '\" name=\"hours[' + rowcount + ']\" size=\"5\"> ';
			
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
			//alert(d.getUTCDay());
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
                        
                        echo "
		
		
			</script>";
			
			} //end detailed mode2
			
			
				echo "
			
			
<br><br>
			
			<span style=\"width:10em;display: inline-block;\">$phrase[267]</span><input type=\"text\" size=\"10\" id=\"startdate\" name=\"startdate\" ";
			if (isset($startdate)) {echo " value=\"$startdate\"";}
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
			echo "><br>
			<span style=\"width:10em;display: inline-block;\">$phrase[268]</span><input type=\"text\" size=\"10\" name=\"enddate\" id=\"enddate\"";
			if (isset($enddate)) {echo " value=\"$enddate\"";}
			if (isset($approval_status) && $approval_status > 0) {echo " readonly";}
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
			
			
				$restricted = array();
			$sql = "select cat_id from leave_category where m = '$m' and restricted = '1'";
			//echo $sql;
			$DB->query($sql,"leavemanage.php");
			while ($row = $DB->get())
			{
			$restricted[] = $row["cat_id"];
			}
		
			//$first_name = $row["first_name"];
			
			if ((in_array($leave_type,$restricted) && $authority > 2) || (!in_array($leave_type,$restricted) && $authority > 1))
		
	
		
			{
				
			echo "
		<b>$comment_label</b><br>";
			if (isset($comment) && $comment != '') 
			{
			$_comment = nl2br($comment);
			echo $_comment;
			}
			echo "<textarea name=\"comment\" cols=\"60\" rows=\"6\">";
			
			echo "</textarea><br><br>	
				
				
				
			<input type=\"hidden\" name=\"update\" value=\"updateleave\">
			<input type=\"hidden\" name=\"leave_id\" value=\"$leave_id\">
			<input type=\"hidden\" name=\"leaveuser\" value=\"$leaveuser\">
			<input type=\"hidden\" name=\"year\" value=\"$sy\">
			<input type=\"submit\" value=\"Update\"> ";	
			
			}
			
		
			echo "</form>
			
			
			";
			
			$sql = "select * from leave_log where leave_id = '$leave_id'";
			echo "<div id=\"folder\"><h2>$phrase[728]</h2>";
			$DB->query($sql,"leavemanage.php");
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
			echo "</div>";
			
		}

		
		
		
		
		
elseif (isset($_REQUEST["view"]) && $_REQUEST["view"] == "calgroup" && in_array($_SESSION["userid"],$managers))
		{
		
	$location = $DB->escape($_REQUEST["location"]);	
        
        $cstart = array();
        $cend = array();
        
        $sql  = "select * , unix_timestamp(enddate) as cend from leave_closed where m = '$m'";	
	$DB->query($sql,"leavemanage.php");
	while ($row = $DB->get())
	{
            $locations = $row["leavegroup"];
            $locations = explode(":",$locations);
            if (in_array($location,$locations))
            {
	$cstart[] = str_replace("-","",$row["startdate"]);
	$cend[] = str_replace("-","",$row["enddate"]) ;
            }
        }
			
				 $sql  = "select id, name,colour,pdays, pdate from leave_periods where m = '$m'";	
	$DB->query($sql,"leaveamanage.php");
	
	
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
	
			
			
			
			
			
			
			
			
			echo "<span style=\"font-size:1.6em\">$phrase[1013]</span><br><br>";	

		
			
		if (!isset($_REQUEST["t"]))
	{

	if (isset($_REQUEST["month"]))
	{
		$t = mktime(0, 0, 0, $_REQUEST["month"], 1, $_REQUEST["year"]);

	}
	elseif (isset($_REQUEST["year"]))
	{
		$t = mktime(0, 0, 0, 1, 1, $_REQUEST["year"]);

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
   $_fd = "$year-$month-01";
   //echo "fd is $fd<br>";
   
   $ld  = mktime(0, 0, 0, $month  , $daysinmonth, $year);
   $ld = date("w",$ld);
   $_ld = "$year-$month-$daysinmonth";
   //echo "ld is $ld";		
			

   
     $sql = "select distinct cat_name,last_name, first_name,cat_code,colour, approval_status, leave_requests.leave_id as leave_id,leave_type, totalhours, 	startdate, enddate from user, leave_category,leave_members,
      leave_requests left join leave_dates on leave_requests.leave_id = leave_dates.leave_id
   where  leave_requests.user_id = leave_members.userid and approval_status != '3'
   and leave_members.location_id = '$location'
 and leave_dates.leave_id is NULL
  and user.userid = leave_requests.user_id
  
   and ((startdate >= '$_fd' and startdate <= '$_ld') or (enddate >= '$_fd' and enddate <= '$_ld') or (startdate < '$_fd' and enddate > '$_ld'))
   and leave_category.cat_id = leave_requests.leave_type
   ";
   
    // echo $sql;

     
  $DB->query($sql,"leavemanage.php");
	while ($row = $DB->get())
			{
			$_leave_mode[] = "simple";
			$_leave_name[] = strtoupper(substr($row["first_name"],0,1) . substr($row["last_name"],0,1));
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

   $sql = "select last_name, first_name, cat_name,cat_code, colour ,approval_status, leave_requests.leave_id as leave_id,leave_type, hours, dayofmonth(leave_date) as day from user, leave_members, leave_requests, leave_dates , leave_category
   where  leave_requests.user_id = leave_members.userid
   and leave_members.location_id = '$location'
   and leave_requests.leave_id = leave_dates.leave_id
   and leave_date >= '$_fd' and leave_date <= '$_ld'
   and user.userid = leave_requests.user_id
   and leave_category.cat_id = leave_requests.leave_type
   and approval_status != '3'
   ";
				}
				
	else
				{
				   $sql = "select last_name, first_name, cat_name,cat_code, colour ,approval_status, leave_requests.leave_id as leave_id,leave_type, hours, strftime('%d',leave_date) as day from user, leave_members, leave_requests, leave_dates , leave_category
   where  leave_requests.user_id = leave_members.userid
   and leave_members.location_id = '$location'
   and leave_requests.leave_id = leave_dates.leave_id
   and leave_date >= '$_fd' and leave_date <= '$_ld'
   and user.userid = leave_requests.user_id
   and leave_category.cat_id = leave_requests.leave_type
   and approval_status != '3'";
					
					
				}
   
 // echo $sql;
   
  $DB->query($sql,"leavemanage.php");
	while ($row = $DB->get())
			{
			$_leave_mode[] = "detailed";	
			$_leave_name[] = strtoupper(substr($row["first_name"],0,1) . substr($row["last_name"],0,1));
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
		
		$DB->query($sql,"leavemanage.php");
		$num = $DB->countrows();
		
		while ($row = $DB->get()) 
		{
			
		$a_holiday[] = $row["holiday"];
	
		$a_name[] = formattext($row["name"]);
		}		
			
			
			
			
			
  echo "<table  style=\"margin-left:2px\" class=\"colourtable\" id=\"calendar\" cellpadding=\"3\">
 <tr class=\"accent\"><td style=\"text-align:left\" valign=\"bottom\"> 
 <a href=\"leavemanage.php?m=$m&amp;view=calgroup&amp;location=$location&amp;t=$lastmonth\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" >
   
   

   <form style=\"display:inline;\" action=\"leavemanage.php\" method=\"get\">
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
       <input type=\"hidden\" name=\"view\" value=\"calgroup\">
        <input type=\"hidden\" name=\"location\" value=\"$location\">

     <input type=\"submit\" value=\"Go\" style=\"font-size:1.5em\">";

   
   echo "</form>";

	
	$sql = "select location_name from leave_location where location_id = '$location'";
		$DB->query($sql,"leavemanage.php");
		$row = $DB->get();
		$location_name = $row["location_name"];

		
		echo "<br><br><span style=\"font-size:1.6em\">$location_name</span>";

echo "</td>
   <td style=\"text-align:right\" valign=\"middle\">
   <a href=\"leavemanage.php?m=$m&amp;view=calgroup&amp;location=$location&amp;t=$nextmonth\" class=\"hide\">$phrase[155]</a> </td></tr>
   
   "; 
   
if (isset($CALENDAR_START) && $CALENDAR_START =="MON") {$cal_offset = 1;} else {$cal_offset = 0;}

   //display blank cells at start of month
   $counter = 0 + $cal_offset;
    if ($cal_offset == 1 && $fd == 0) $fd=7; 
   if ($fd <> 0 + $cal_offset)
   	{
	echo "<tr >";
	}
   while ($counter < $fd )
   	{
	echo "<td>";
	
	
	
	echo "</td>";
	if ($counter == (6 + $cal_offset))
		{
		echo "</tr>\n";
		}
	$counter++;
	}
   
   
   //display month as table cells
   $daycount = 1;
   while ($daycount <= $daysinmonth)
   	{
	$endline = (($counter + $daycount - $cal_offset) % 7);
	$day = str_pad($daycount, 2, "0", STR_PAD_LEFT);
	$dayname  = strftime("%a",mktime(0, 0, 0, $month, $daycount,  $year));
	$t = mktime(0, 0, 0, $month, $day,  $year);
	if ($endline == 1) { echo "<tr>";}
        $today = $thisyear . $thismonth . $thisday;
        $daycounter = $year . $month . $day;
	echo "<td valign=\"top\"";
        
      
        
	if ($today == $daycounter)
				{
				echo " id=\"scrollpoint\" class=\"accent\"";
				}
				
				
	
	
	echo "><span style=\"font-size:large\"><b>$daycount</b></span>&nbsp;&nbsp; $dayname<br><br>";
        
        foreach ($cstart as $key => $value)
        {
        if ($value <= $daycounter && $cend[$key] >= $daycounter)
        {      
        echo "<p style=\"color:red;font-weight:bold;margin:0\">$phrase[494]</p>";
        }
        }
        
        
        
	
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
			
		if ($_leave_mode[$key] == "detailed" && $_leave_day[$key] == $daycount)
			{
			echo "<a href=\"leavemanage.php?m=$m&event=view&leave_id=$id\" title=\"$_leave_cat[$key] $_leave_hours[$key] hours\" style=\"color:#$_leave_colour[$key]\">$_leave_name[$key]</a> $_leave_hours[$key]";
			if ($_leave_status[$key] == 1) {echo " <img src=\"../images/tick12.png\" alt=\"$phrase[1007]\" style=\"vertical-align: text-bottom; \">";}
			if ($_leave_status[$key] == 2 || $_leave_status[$key] == 4) {echo " <img src=\"../images/cross12.png\" alt=\"$phrase[1008]\" style=\"vertical-align: text-bottom; \">";}
			echo "<br>";	
			}
			
		if ($_leave_mode[$key] == "simple" && ($_leave_enddate[$key] >= $date && $_leave_startdate[$key] <= $date))
			{
			echo "<a href=\"leavemanage.php?m=$m&event=view&leave_id=$id\" title=\"$_leave_cat[$key] $_leave_hours[$key] hours\"  style=\"color:#$_leave_colour[$key]\">$_leave_name[$key]</a> ";
			if ($_leave_status[$key] == 1) {echo " <img src=\"../images/tick12.png\" alt=\"$phrase[1007]\" style=\"vertical-align: text-bottom; \">";}
			if ($_leave_status[$key] == 2 || $_leave_status[$key] == 4) {echo " <img src=\"../images/cross12.png\" alt=\"$phrase[1008]\" style=\"vertical-align: text-bottom; \">";}
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
	while (($endline) < (7 -  $cal_offset))
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
    <tr class=\"accent\"><td style=\"text-align:left\" valign=\"bottom\"> <a href=\"leavemanage.php?m=$m&amp;view=calgroup&amp;location=$location&amp;t=$lastmonth\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" ><span style=\"font-size:150%\"> $display</span></td>
   <td style=\"text-align:right\" valign=\"middle\">  <a href=\"leavemanage.php?m=$m&amp;view=calgroup&amp;location=$location&amp;t=$nextmonth\" class=\"hide\">$phrase[155]</a> </td></tr>
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
		$DB->query($sql,"leaveadmin.php");
		$row = $DB->get();
		$last_name = $row["last_name"];
		$first_name = $row["first_name"];
		
		echo "<p style=\"font-size:1.8em;margin: 0.8em 0\">$first_name $last_name</p>";	}
			
		
			if (!isset($leaveuser) && !isset($filter) && !isset($status) && !isset($category) && !isset($location)) {$status = 0;}
		
		
		
		if (isset($status))
		{
			if ($status == 0) {echo "<span style=\"font-size:1.6em\">$phrase[1006]</span><br><br>";}
			if ($status == 1) {echo "<span style=\"font-size:1.6em\">$phrase[1007]</span><br><br>";}
			if ($status == 2) {echo "<span style=\"font-size:1.6em\">$phrase[1008]</span><br><br>";}
		    if ($status == 4) {echo "<span style=\"font-size:1.6em\">$phrase[152]</span><br><br>";}
		}
		
		if ((isset($filter) && $filter == "cat") || isset($category))
		{
			echo "<span style=\"font-size:1.6em\">$phrase[1012]</span><br><br>";
		}	
		
		if ((isset($filter) && $filter == "location") || isset($location))
		{
			echo "<span style=\"font-size:1.6em\">$phrase[1013]</span><br><br>";
		}		
	
			echo "
		<form action=\"leavemanage.php\" style=\"display:inline\">
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
			
			echo "</select> ";
			
			if (isset($filter) )
				{ echo "<input type=\"hidden\" name=\"filter\" value=\"$filter\">";}
			if (isset($status) )
				{ echo "<input type=\"hidden\" name=\"status\" value=\"$status\">";}
			
			echo "<input type=\"submit\" value=\"Go\">";
                        


    if (in_array($_SESSION["userid"],$managers) && $authority == 4 && isset($leaveuser)) {echo "<a href=\"leave.php?m=$m&event=apply&leaveuser=$leaveuser\" style=\"padding-left:3em\">Shadow</a>";} 

			
			echo "<input type=\"hidden\" name=\"m\" value=\"$m\"></form><br><br>
			<table class=\"colourtable\" >";
			
			if (isset($filter) && $filter == "people")
			{
				
				echo "<tr><td>$phrase[141]</td><td>$phrase[1002]</td></tr>";
				


$in  = " in ("; 
$counter= 0;
if (isset($members))
{
	foreach ($members as $key => $value) {
		if ($counter != 0) {$in .= ",";}
		$in .= "$value";
		$counter++;
	}
}
$in .= ") ";

if ($DB->type == "mysql")
				{

$sql = "SELECT leave_requests.user_id as user_id, sum( totalhours ) as total
FROM leave_requests
where 
approval_status != '3' and
leave_requests.m = '$m' and year(leave_requests.startdate) = '$year' and leave_requests.user_id $in
GROUP BY user_id ";
				}
else
				{
				$sql = "SELECT leave_requests.user_id as user_id, sum( totalhours ) as total
FROM leave_requests
where
approval_status != '3' and leave_requests.m = '$m' and strftime('%Y',startdate) = '$year' and leave_requests.user_id $in
GROUP BY user_id ";	
					
					
				}
			
				//echo $sql;
				
					$totals = array();
					$DB->query($sql,"leavemanage.php");
					while ($row = $DB->get())
					{
					//$first_name= $row["first_name"];
					//$last_name= $row["last_name"];
					$total= $row["total"];
					$userid= $row["user_id"];
					$totals[$userid] = $total;
					
					}
					
					
					foreach($members as $key => $value)
					{
						foreach($totals as $userid => $total)
						{
						if ($userid == $value)
							{
							echo "<tr><td><a href=\"leavemanage.php?m=$m&leaveuser=$userid&year=$year\">$memberlname[$userid], $memberfname[$userid]</a></td><td>$total</td></tr>";
							}
						
						}
				
					
					}
			}
			
			elseif (isset($filter) && $filter == "cat")
			{
				
				echo "<tr><td>$phrase[141]</td><td>$phrase[1002]</td></tr>";
				
				
				
$in  = " in ("; 
$counter= 0;
if (isset($members))
{
	foreach ($members as $key => $value) {
		if ($counter != 0) {$in .= ",";}
		$in .= "$value";
		$counter++;
	}
}
$in .= ") ";

if ($DB->type == "mysql")
				{
				
				$sql = "SELECT cat_id, cat_name, sum( totalhours ) as total
FROM leave_requests, leave_category 
where leave_requests.m = '$m' and
approval_status != '3' and
leave_category.cat_id = leave_requests.leave_type 
and year(leave_requests.startdate) = '$year' 
and leave_requests.user_id $in
GROUP BY cat_name order by cat_name";
				
				}
				
else
				{
					
								$sql = "SELECT cat_id, cat_name, sum( totalhours ) as total
FROM leave_requests, leave_category 
where leave_requests.m = '$m' and
approval_status != '3' and
leave_category.cat_id = leave_requests.leave_type 
and strftime('%Y',startdate) = '$year'
and leave_requests.user_id $in
GROUP BY cat_name order by cat_name";	
					
					
				}
				//echo $sql;
			
					$DB->query($sql,"leavemanage.php");
					while ($row = $DB->get())
					{
					$cat_name = $row["cat_name"];
					$category = $row["cat_id"];
					$total= $row["total"];
				
				
					echo "<tr><td><a href=\"leavemanage.php?m=$m&category=$category&year=$year\">$cat_name</a></td><td>$total</td></tr>";
					}
			}
			
				elseif (isset($filter) && $filter == "location")
			{
				
				echo "<tr><td>Group</td><td colspan=\"2\"></td></tr>";
				
	/*			
				$in  = " in ("; 
$counter= 0;
if (isset($members))
{
	foreach ($members as $key => $value) {
		if ($counter != 0) {$in .= ",";}
		$in .= "$value";
		$counter++;
	}
}
$in .= ") ";
			

	
	if ($DB->type == "mysql")
				{
				$sql = "SELECT leave_location.location_id as location_id, location_name
FROM leave_requests, leave_members, leave_location
where leave_requests.m = '$m' and
approval_status != '3' and
leave_location.location_id = leave_members.location_id
and year(leave_requests.startdate) = '$year' 
and leave_requests.user_id $in
GROUP BY location_name order by location_name";
			//	echo $sql;  , sum( totalhours ) as total
				}
				
	else
				{
				
				
				
								$sql = "SELECT leave_location.location_id as location_id, location_name
FROM leave_requests, leave_members, leave_location
where leave_requests.m = '$m' and
approval_status != '3' and
leave_location.location_id = leave_members.location_id
and strftime('%Y',startdate) = '$year'
and leave_requests.user_id $in
GROUP BY location_name order by location_name";
		
				}
                                
            */                    
                                
                                $sql = "select location_name, leave_location.location_id from leave_location, leave_managers where
                                    leave_managers.location_id = leave_location.location_id
                                    and userid = '$_SESSION[userid]' and m = '$m'
                                and authority > '0'";
                                
                                
			//echo $sql;
                      //  print_r($_SESSION);
					$DB->query($sql,"leavemanage.php");
					while ($row = $DB->get())
					{
					$location_name = $row["location_name"];
					$location_id = $row["location_id"];
					//$total= $row["total"];
				//	$userid= $row["userid"];
				
					echo "<tr><td>$location_name</td>
					<td><a href=\"leavemanage.php?m=$m&location=$location_id&year=$year\">$phrase[642]</a></td>
					<td><a href=\"leavemanage.php?m=$m&view=calgroup&location=$location_id&year=$year\">$phrase[427]</a></td>
					
					</tr>";
					}
			}
			else
			{
			echo "<tr style=\"font-weight:bold\"><td>$phrase[340]</td>";
			if (!isset($leaveuser)) {echo "<td><a href=\"leavemanage.php?m=$m&orderby=last_name";
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[131]</a></td>";}
			echo "<td style=\"width:7em\"><a href=\"leavemanage.php?m=$m&orderby=startdate";
			if (isset($leaveuser)) {echo "&leaveuser=$leaveuser";}
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[267]</a></td><td style=\"width:7em\">$phrase[268]</td><td>$phrase[1002]</td>
			<td style=\"width:10em\"><a href=\"leavemanage.php?m=$m&orderby=leave_type";
			if (isset($leaveuser)) {echo "&leaveuser=$leaveuser";}
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[1000] </a></td><td><a href=\"leavemanage.php?m=$m&orderby=approval_status";
			if (isset($leaveuser)) {echo "&leaveuser=$leaveuser";}
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[401]</a></td>";
			if (isset($members))
			{
			echo "<td><a href=\"leavemanage.php?m=$m&orderby=processed";
			if (isset($leaveuser)) {echo "&leaveuser=$leaveuser";}
			if (isset($filter)) {echo "&filter=$filter";}
			if (isset($year)) {echo "&year=$year";}
			if (isset($status)) {echo "&status=$status";}
			if (isset($category)) {echo "&category=$category";}
			if (isset($location)) {echo "&location=$location";}
			echo "\">$phrase[1009]</a></td>";
			}
			
			if ($_SESSION["userid"] == 1) {echo "<td></td>";}
			echo "<td></td></tr>
			";
			
			
			
			
			
			$sql = "select * from leave_category where m = '$m'";
			$DB->query($sql,"leavemanage.php");
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
			$_status[4] = "$phrase[152]";
			
			
			if (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "startdate") { $orderby  = "startdate" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "approval_status") { $orderby  = "approval_status" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "leave_type") { $orderby  = "leave_type" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "last_name") { $orderby  = "last_name" ;}
			elseif (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "processed") { $orderby  = "processed" ;}
			else {$orderby  = "startdate" ;}
			
			
	
			
			
			if (isset($leaveuser))
			{
		
			
			if ($DB->type == "mysql")
				{
				$sql = "select * from leave_requests where approval_status != '3' and leave_requests.m = '$m' and user_id = '$leaveuser' and year(startdate) = '$year' order by $orderby";
				}
			
				else
				{
				$sql = "select * from leave_requests where approval_status != '3' and leave_requests.m = '$m' and user_id = '$leaveuser' and strftime('%Y',startdate) = '$year' order by $orderby";
		
				}
			
			
			
			
			}
			elseif( isset($status) && in_array($_SESSION["userid"],$managers))
			{
				if ($DB->type == "mysql")
				{
				$sql = "select distinct leave_id, first_name, last_name, startdate, enddate,processed, totalhours, approval_status, leave_type 
			from leave_requests, leave_managers,leave_members, user
			where leave_requests.m = '$m' and approval_status != '3' and
			(leave_managers.userid = '$_SESSION[userid]' and  leave_managers.location_id = leave_members.location_id and leave_members.userid = leave_requests.user_id)
			
			and user.userid = leave_requests.user_id
			and leave_requests.approval_status = '$status' and year(leave_requests.startdate) = '$year' order by $orderby";	
				}
			
				else
				{
				
		
				$sql = "select distinct leave_id, first_name, last_name, startdate, enddate,processed, totalhours, approval_status, leave_type 
			from leave_requests, leave_managers,leave_members, user
			where leave_requests.m = '$m' and approval_status != '3' and
			(leave_managers.userid = '$_SESSION[userid]' and  leave_managers.location_id = leave_members.location_id and leave_members.userid = leave_requests.user_id)
			
			and user.userid = leave_requests.user_id
			and leave_requests.approval_status = '$status' and strftime('%Y',startdate) = '$year' order by $orderby";	
				
				
				}
			
		
			}
			
			elseif( isset($category) && in_array($_SESSION["userid"],$managers))
			{
		
			
			
			
						if ($DB->type == "mysql")
				{
				$sql = "select distinct leave_id, first_name, last_name, startdate, enddate, processed, totalhours, approval_status, leave_type  from leave_requests, leave_managers,leave_members, user
			where leave_requests.m = '$m' and approval_status != '3' and
			 (leave_managers.userid = '$_SESSION[userid]' and  leave_managers.location_id = leave_members.location_id and leave_members.userid = leave_requests.user_id)
			
			and user.userid = leave_requests.user_id
			and leave_requests.leave_type = '$category' and year(leave_requests.startdate) = '$year' order by $orderby";	
				}
			
				else
				{
					
				$sql = "select distinct leave_id, first_name, last_name, startdate, enddate, processed, totalhours, approval_status, leave_type  from leave_requests, leave_managers,leave_members, user
			where leave_requests.m = '$m' and approval_status != '3' and
			 (leave_managers.userid = '$_SESSION[userid]' and  leave_managers.location_id = leave_members.location_id and leave_members.userid = leave_requests.user_id)
			
			and user.userid = leave_requests.user_id
			and leave_requests.leave_type = '$category' and strftime('%Y',startdate) = '$year' order by $orderby";	
				
				
		
				}
	
			
			
			}
			
			
			
			elseif( isset($location) && in_array($_SESSION["userid"],$managers))
			{
		
			if ($DB->type == "mysql")
				{
			$sql = "select distinct leave_id, first_name, last_name, startdate, enddate,processed, totalhours, approval_status, leave_type  from leave_requests, leave_managers,leave_members, user
			where leave_requests.m = '$m' and approval_status != '3' and
			(leave_managers.userid = '$_SESSION[userid]' and  leave_managers.location_id = leave_members.location_id and leave_members.userid = leave_requests.user_id)
		
			and user.userid = leave_requests.user_id
			and leave_members.location_id = '$location' 
			and leave_members.userid = leave_requests.user_id
			and year(leave_requests.startdate) = '$year' order by $orderby";	
			}
				
			
			else
				{
			$sql = "select distinct leave_id, first_name, last_name, startdate, enddate,processed, totalhours, approval_status, leave_type  from leave_requests, leave_managers,leave_members, user
			where leave_requests.m = '$m' and approval_status != '3' and
			(leave_managers.userid = '$_SESSION[userid]' and  leave_managers.location_id = leave_members.location_id and leave_members.userid = leave_requests.user_id)
		
			and user.userid = leave_requests.user_id
			and leave_members.location_id = '$location' 
			and leave_members.userid = leave_requests.user_id
			and strftime('%Y',startdate) = '$year' order by $orderby";
		
				}
				
			}
				
				
				
				

			
		//	echo $sql;
			$DB->query($sql,"leavemanage.php");
			while ($row = $DB->get())
			{
			  
			//	print_r($row);
			 
			
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
			echo "<td >$startdate</td><td>$enddate</td><td>$total_hours</td><td>$types[$leave_type]</td><td";
			
			if ($approval_status == 0) {echo " class=\"partoption\"";}
			if ($approval_status == 1) {echo " class=\"yesoption\"";}
			if ($approval_status == 2) {echo " class=\"nooption\"";}
			if ($approval_status == 4) {echo " class=\"canceloption\"";}
			
			echo ">$_status[$approval_status]</td>";
			//
			if (isset($members)) 
			{
			echo "<td style=\"padding:0\"><div id=\"p_$leave_id\"><div style=\"padding:0.5em\" onclick=\"process('$leave_id','$processed')\" ";
			
			if ($processed == 1) {echo " class=\"yesoption\"> $phrase[221]";}
			elseif ($processed == 2) {echo " class=\"partoption\">$phrase[1067]";}
			else {echo " class=\"nooption\">$phrase[13]";}
			echo "</div>
			
			</div></td>";
			}
			echo "<td><a href=\"leavemanage.php?m=$m&event=view&leave_id=$leave_id\">$phrase[284]</a></td>";
			if ($_SESSION["userid"] == 1 || $authority == "3") {echo "<td><a href=\"leavemanage.php?m=$m&year=$year&event=delete&leave_id=$leave_id\">Delete</a></td>";}
			echo "</tr>";
			
			if ($approval_status == 1) {$type_totals[$leave_type] = $type_totals[$leave_type] + $total_hours;}
			}
			
			
		}
			
			echo "</table>";
			
			if (isset($type_totals))
			{
			echo "<h4 style=\"margin-top:4em\">Approved for this year</h4><table class=\"colourtable\"><tr style=\"font-weight:bold\"><td style=\"padding: 10px;\">$phrase[1000] </td><td style=\"padding: 10px;\">$phrase[1002]</td></tr>";
			
			
			foreach ($type_totals   as $key => $value)
				{
				if ($value != 0) {echo "<tr><td style=\"padding: 10px;\">$types[$key] </td><td style=\"padding: 10px;\">$value</td></tr>";}
				}
			echo "</table>";
			}
			
			echo "
			
			

		<script type=\"text/javascript\" >
			
			function process(id,value)
			{
			
			var element = 'p_' + id;
			//alert(element)
			var sel = document.getElementById(element);
			//var processed;
			//processed = sel.options[sel.selectedIndex].value;
			//alert(sel.options[sel.selectedIndex].text)
			//var processed = sel.options[sel.selectedIndex].text;
			//var processed
			if (value == 1) {sel.className = 'yesoption';}
			if (value == 0) {sel.className = 'nooption';}
			
			
			var timestamp = new Date();
			var url = 'ajax.php?m=$m&id=' +  id + '&event=processleave&process=' + value + '&rt=' + timestamp.getTime();
			//alert(url)
			updatePage(url,element)
			//alert
			}
			</script>
";
		}
		
		
		
		
			//end contentbox
		echo "</div></div>";
		
	 
	     

	     	 
	  //	include("../includes/rightsidebar.php");   
		
		
	


include ("../includes/footer.php");
	
	}
	
	
	
	
	

?>