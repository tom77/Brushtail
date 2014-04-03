<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);



$status_label[1] = $phrase[631] ;
$status_label[2] = $phrase[632] ;
$status_label[3] = $phrase[633] ;


$integers[] = "month";
$integers[] = "year";
$integers[] = "event_id";
$integers[] = "bookingno";
$integers[] = "t";

$phrase[130] = htmlspecialchars($phrase[130],ENT_QUOTES);
$phrase[131] = htmlspecialchars($phrase[131],ENT_QUOTES);
$phrase[132] = htmlspecialchars($phrase[132],ENT_QUOTES);
$phrase[133] = htmlspecialchars($phrase[133],ENT_QUOTES);
$phrase[134] = htmlspecialchars($phrase[134],ENT_QUOTES);
$phrase[135] = htmlspecialchars($phrase[135],ENT_QUOTES);
$phrase[110] = htmlspecialchars($phrase[110],ENT_QUOTES);


foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}	
	
if (!isset($t))
	{
	if (isset($month))
	{
		$t = mktime(0, 0, 0, $month, 1,  $year);
	}
	else {
		$t = time();
	}
	}
	

	
	$ip = ip("pc");  

        
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

	
		
	page_view($DB,$PREFERENCES,$m,"");	
	
	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

echo "<div style=\"text-align:center\">";

		
		if (isset($_REQUEST["deleteuser2"]) && ($access->thispage > 1))
       { 
       	
       	$barcode = $DB->escape(str_replace(" ", "", $_REQUEST["barcode"]));

       $sql = "delete from  pc_users  where barcode = '$barcode'";
      	$DB->query($sql,"calendar.php"); 	
       }
		
		
		
		   if (isset($_REQUEST["updateuser"]) && ($access->thispage > 1))
       {   
       	  
       	$password = md5($_REQUEST["password"]);
       	$barcode = $DB->escape(str_replace(" ", "", $_REQUEST["barcode"]));
       	
       	if ($barcode == "")
       	{ 
       		echo "";
       	}
       	else 
       	{
		$sql = "update pc_users set change = '$change', password = '$password' where barcode = '$barcode'";
      	$DB->query($sql,"calendar.php"); 	
      	//echo $sql;
       	}
       	 	
       }
       
           
   if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "adduser" && ($access->thispage > 1))
       {   
       	  
       	$password = md5($_REQUEST["password"]);
       	$change = $DB->escape($_REQUEST["change"]);
       	$barcode = $DB->escape(str_replace(" ", "", $_REQUEST["barcode"]));
       	
       	if ($barcode == "")
       	{ 
       		echo "";
       	}
       	else 
       	{
		$sql = "insert into pc_users values ('$barcode','$password','$change')";
      	$DB->query($sql,"calendar.php"); 	
       	}
       }
       	 	
	
	if (isset($_GET["update"]) && $_GET["update"] == "delete" && $access->thispage > 1)
		{
		if (isinteger($_GET["bookingno"]))
		{
		$sql = "delete from cal_bookings where bookingno = \"$bookingno\"";	
		$DB->query($sql,"calendar.php");
		} else { $ERROR = "<div style=\"text-align:center\">$phrase[72]</div>";}
		}
	
	
	
	
	
	if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "update" && $access->thispage > 1)
		{
			
			
				//check booking belongs to an event of this module
			$sql = "select count(*) as count from cal_events cal_events,cal_cat, cal_bridge 
			 where cal_cat.cat_id = cal_events.event_catid and cal_bridge.m = '$m' and cal_bridge.cat = cal_cat.cat_id and cal_events.event_id = '$event_id'";
			//echo $sql;
			$DB->query($sql,"calendar.php");
			$row = $DB->get();
			$total = $row["count"];
			
			if ($total > 0)
			{
			
			
			$maxbookings = $_REQUEST["maxbookings"];
			
		if ($_REQUEST["status"] == 1 && $_REQUEST["oldstatus"] <> 1)
		{	
			//count how many booked 
			$sql = "SELECT count(*) as count FROM cal_bookings  where eventno = \"$event_id\" and status = '1'";
			$DB->query($sql,"calendar.php");
			$row = $DB->get();
			$count = $row["count"];
			
			
			if ($maxbookings <= $count && $maxbookings <> 0)
			{$WARNING = "$phrase[262]";}
			
			//check position on queue
			//count how many booked and waiting people there are
			$sql = "SELECT min(bookingno) as top FROM cal_bookings  where eventno = \"$event_id\" and status = '2'";
			$DB->query($sql,"calendar.php");
			$row = $DB->get();
			$top = $row["top"];
			
			if ($_REQUEST["status"] == 2 && $bookingno <> $top && !isset($_REQUEST["proceed"]))
			{
				
				
			
				
			
			$fname = urlencode($_REQUEST["firstname"]);
		$lname = urlencode($_REQUEST["lastname"]);
		$phone = urlencode($_REQUEST["telephone"]);
		$add = urlencode($_REQUEST["address"]);
		$pay = urlencode($_REQUEST["paid"]);
		$com = urlencode($_REQUEST["comments"]);
		$conf = urlencode($_REQUEST["confirmation"]);
		$staff = urlencode($_REQUEST["staffname"]);
		$ag = urlencode($_REQUEST["age"]);
		$stat = urlencode($_REQUEST["status"]);
		$oldstat = urlencode($_REQUEST["oldstatus"]);	
		$maxbook = urlencode($maxbookings);
				
				
				$WARNING = "<br><br>$phrase[635]<br>$phrase[636]<br><br>
				
				<a href=\"calendar.php?m=$m&amp;t=$t&amp;bookingno=$bookingno&amp;event_id=$event_id&amp;event=book&amp;update=update&proceed=yes&firstname=$fname&lastname=$lname&telephone=$phone&address=$add&paid=$pay&confirmation=$conf&staffname=$staff&age=$ag&amp;status=$stat&maxbookings=$maxbook&oldstatus=$oldstat&comments=$com\"><b>$phrase[12]</b></a>
				 | <a href=\"calendar.php?m=$m&amp;t=$t&amp;event_id=$event_id&amp;event=book\"><b>$phrase[13]</b></a>
			";
			}
		}
		
			
		
		
		
			
		$firstname = $DB->escape($_REQUEST["firstname"]);
		$lastname = $DB->escape($_REQUEST["lastname"]);
		$telephone = $DB->escape($_REQUEST["telephone"]);
		$address = $DB->escape($_REQUEST["address"]);
		$paid = $DB->escape($_REQUEST["paid"]);
		$comments = $DB->escape($_REQUEST["comments"]);
		$confirmation = $DB->escape($_REQUEST["confirmation"]);
		$staffname = $DB->escape($_REQUEST["staffname"]);
		$age = $DB->escape($_REQUEST["age"]);
		$status = $DB->escape($_REQUEST["status"]);
		$dateupdated = time();
		$receipt = $DB->escape($_REQUEST["receipt"]);
                $email = $DB->escape($_REQUEST["email"]);
                
         
                
			
		if (!isset($ERROR))
		{
		if (isset($_REQUEST["bookinggroup"]) && $_REQUEST["bookinggroup"] != 0)
		{
		$bookinggroup = $DB->escape($_REQUEST["bookinggroup"]);	
		$sql = "update cal_bookings set  
		 paid = '$paid', status = '$status', age = '$age',confirmation = '$confirmation', comments = '$comments', 
		 time_updated = '$dateupdated', staffname = '$staffname', ip = '$ip', username = '$_SESSION[username]', 
		 receipt = '$receipt', email = '$email' where webid = '$bookinggroup'";	
				
		}
		else {
				
		$sql = "update cal_bookings set  firstname = '$firstname', lastname = '$lastname', telephone = '$telephone', address = '$address',
		 paid = '$paid', status = '$status', age = '$age',confirmation = '$confirmation', comments = '$comments', 
		 time_updated = '$dateupdated', staffname = '$staffname', ip = '$ip', username = '$_SESSION[username]', 
		 receipt = '$receipt',email = '$email' where bookingno = '$bookingno'";	
			
		}
		
			
		$DB->query($sql,"calendar.php");
		} 
		
		
		
			}
		}
	


	
	
		
	
		
	if (isset($_POST["update"]) && $_POST["update"] == "book" && $access->thispage > 1)
	
	
		{
		
            $branches = array();	
	$sql = "select branchno, bname from cal_branches ";
	
	$DB->query($sql,"calendars.php");
	while ($row = $DB->get())
	     {
	     	$_bname = $row["bname"];
	     	$_bno = $row["branchno"];
	     	
            $branches[$_bno] =$_bname;
			
		}
            
            
            
			$sql = "select max(webid) as webid from cal_bookings";
 			$DB->query($sql,"calendar.php");
			$row = $DB->get();
			$webid = $row["webid"] + 1;
			
			//check booking belongs to an event of this module
			$sql = "select count(*) as count, event_start, event_name, event_location, event_description, trainerEmail from cal_events cal_events,cal_cat, cal_bridge 
			 where cal_cat.cat_id = cal_events.event_catid and cal_bridge.m = '$m' and cal_bridge.cat = cal_cat.cat_id and cal_events.event_id = '$event_id'";
			//echo $sql;
			$DB->query($sql,"calendar.php");
			$row = $DB->get();
			$total = $row["count"];
			
			if ($total > 0)
			{
			
			
			$firstname = $_POST["firstname"];
			$lastname = $_POST["lastname"];
			$telephone = $_POST["telephone"];
			$address = $_POST["address"];
			$comments = $_POST["comments"];
			
			$paid = $DB->escape($_POST["paid"]);
			$receipt = $DB->escape($_POST["receipt"]);
			$confirmation = $DB->escape($_POST["confirmation"]);
			$staffname = $DB->escape($_POST["staffname"]);
			$age = $_POST["age"];
			$status = $DB->escape($_POST["status"]);
			$email = $DB->escape($_POST["email"]);
			$trainerEmail = trim($row["trainerEmail"]);
                        $event_name = $row["event_name"];
                        $event_description = $row["event_description"];
                        $event_name = $row["event_name"];
                        $event_location = $row["event_location"];
                        $displaydate = strftime("%a %x", $row["event_start"]);
		        $displaytime = date("g:i a", $row["event_start"]);
				
			
			//if	(($_POST["firstname"] == "") && ($_POST["lastname"] == "") )
			//{
			
			//$WARNING = "$phrase[264]";
				
			//}
			//counter counts the number of successful bookings for the group
									$counter = 0;
									if(isset($_POST["group"])) 
										{ $group = $_POST["group"];}
										else {$group = 1; }
									
									$sql = "SELECT count(*) as count FROM cal_bookings  where eventno = \"$event_id\" and status = 1 ";
									
									//echo $sql;
													$DB->query($sql,"calendar.php");
													$row = $DB->get();
													$total = $row["count"];
													
									if ($total + $group > $_REQUEST["maxbookings"] && $_REQUEST["maxbookings"] <> 0 && !isset($WARNING) && $status == 1 )
										
									{		
										if ($group ==1)
										{	
										$WARNING = "
										$phrase[262]"; //could not complete booking
										}
										else 
										{
											$WARNING = "$phrase[263]"; //not enough places available
										}
									}			
											
									while ($counter < $group && !isset($WARNING) )
									{
										
										
										$_age = $DB->escape($age[$counter]); 
										$_firstname = $DB->escape($firstname[$counter]); 
										$_lastname = $DB->escape($lastname[$counter]); 
										$_telephone = $DB->escape($telephone[$counter]); 
										$_address = $DB->escape($address[$counter]); 
										$_comments = $DB->escape($comments[$counter]); 
										
					
													//tests if class is full before adding
													
													$sql = "SELECT count(*) as count FROM cal_bookings  where eventno = '$event_id' and status = 1 ";
													$DB->query($sql,"calendar.php");
													$row = $DB->get();
													$total = $row["count"];
													 $datebooked = time();
																					
													
													if ($status == 1 && $_POST["maxbookings"] <> 0 && ($total >= $_POST["maxbookings"]))
														{
														$WARNING = "$phrase[263]";  //Not enough places available to complete all bookings
														}
														
														 if (!isset($WARNING))
														 {
															
														$sql = "INSERT INTO cal_bookings  (bookingno, firstname, lastname, paid, status, eventno, telephone,address,comments,confirmation, age,staffname,ip,username,cardnumber,time_booking,time_updated,email,webid,receipt)
											    		VALUES(NULL,'$_firstname','$_lastname','$paid','$status', '$event_id', '$_telephone','$_address','$_comments','$confirmation','$_age','$staffname','$ip','$_SESSION[username]','','$datebooked','$datebooked','$email','$webid','$receipt')";
														//echo $sql;			
														$DB->query($sql,"calendar.php");

                                                        $counter++;
                                                        
                                                     
                                                       if (filter_var($trainerEmail, FILTER_VALIDATE_EMAIL)) {
                                                         
                                                             if (key_exists($event_location,$branches)) { $event_location = $branches[$event_location];}
                                                           
                                                           	$message = "
$phrase[141]: $_firstname $_lastname
$phrase[132]: $_telephone
$phrase[134]: $_address
    
$event_name
$displaytime
$displaydate
$event_location
$event_description

";
       if (isset($EVENTBOOKINGS_FROM)) {$EMAIL = $EVENTBOOKINGS_FROM;}
       elseif (isset($EMAIL_FROM)) {$EMAIL = $EMAIL_FROM;}
       else {echo "Email from not configured!";}
                                                                
        
        $headers = "From:$EMAIL";
send_email($DB,$trainerEmail,$phrase[976], $message,$headers);
	//echo "send_email(DB,$trainerEmail,$phrase[976], $message,$headers);";
        
                                                       }
                                                        
                                                        
                                                        
														 }
     
														
						
														
														
														
														}
													
										//end while
										}
										
	}

			}
	
			
					//get list of locations if any
	$branches = array();	
	$sql = "select branchno, bname from cal_branches ";
	
	$DB->query($sql,"calendars.php");
	while ($row = $DB->get())
	     {
	     	$_bname = $row["bname"];
	     	$_bno = $row["branchno"];
	     	
            $branches[$_bno] =$_bname;
			
		}	
	
	 $sql = "select location from cal_branch_bridge where  module = \"$m\"";
	
	 $locations = array();
	$DB->query($sql,"calendars.php");
	while ($row = $DB->get())
	     {
	     	
	      
            $locations[] = $row["location"];
			
		}
		
		

		
		if (!in_array('0',$locations))
		{
			$count = 1;
			
			$locationinsert = " and event_location in (";
			foreach($locations as $location)
			{
				if ($count == 1)
						{
						$locationinsert .= "'$location'";
						}
					else
						{
						$locationinsert .= ",'$location'";
						}
					$count++;
			}
			
			$locationinsert .= ") ";
		} else {$locationinsert = "";}
	
		
		
		
		
			
	if (isset($ERROR))
	{		
	echo $ERROR;		
	}
	elseif (isset($WARNING))
	{		
		
				
		$sql = "SELECT cat_name,cat_colour, event_name FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and cal_events.event_id = \"$event_id\"";
		$DB->query($sql,"calendar.php");
		$row = $DB->get();
		
		
		$cat_name = formattext($row["cat_name"]);
		$cat_colour = $row["cat_colour"];
		$event_name = formattext($row["event_name"]);
		
		
	echo "<h1 >$modname</h1>
<a href=\"calendar.php?m=$m&amp;t=$t\">$phrase[118]</a> | <a href=\"calendar.php?m=$m&amp;event=all\">$phrase[864]</a> | <a href=\"calendar.php?m=$m&amp;event=upcoming\">$phrase[980]</a> 
| <a href=\"calendar.php?m=$m&amp;event=search\">$phrase[863]</a> | <a href=\"calendar.php?m=$m&amp;t=$t&amp;event_id=$event_id&amp;event=book\">$phrase[699]</a>
<h2 style=\"color:$cat_colour;\">$cat_name</h2><br><b>$event_name</b><br>
<br>
<br>
<br>
 <span style=\"font-size:2em;color:#E6002E;\">$WARNING</span>";		
	}
	
	else {

	if(isset($_GET["event"]) && $_GET["event"] == "search")
		{
	
		echo "<h1>$modname</h1>
		
<a href=\"calendar.php?m=$m&amp;t=$t\">$phrase[118]</a> | <a href=\"calendar.php?m=$m&amp;event=upcoming\">$phrase[980]</a> | <a href=\"calendar.php?m=$m&amp;event=all\">$phrase[864]</a><br><br>    <script type=\"text/javascript\">
										function setfocus()
										{
										document.getElementById(\"focusfield\").focus()
										}
										window.onload=setfocus
										</script>";
			if ($DB->type == "mysql")
			{
		$sql = "SELECT year(FROM_UNIXTIME(event_start)) AS yeargroup FROM `cal_events` GROUP BY yeargroup order by yeargroup desc";
			}
			
				else
			{
		$sql = "SELECT strftime('%Y',datetime ( event_start , 'unixepoch','localtime' )) AS yeargroup FROM cal_events GROUP BY yeargroup order by yeargroup desc";
			}
			
			
		$DB->query($sql,"calendar.php");
		$numrows = $DB->countrows();
		while ($row = $DB->get()) 
					{
					if (strlen($row["yeargroup"]) == 4)
					{
					$yeargroup[] =$row["yeargroup"];
					}
					}
		if (isset($_REQUEST["year"]))	
		{
			$thisyear = $_REQUEST["year"];
		}
		else {
		$thisyear = date("Y");	
		}
		
		

		if ($numrows == 0) { echo $phrase[495];}
		else {
		
		 
		echo "<form action=\"calendar.php?m=$m&amp;=event=search\" method=\"get\">
		<input type=\"text\" name=\"keywords\" id=\"focusfield\"";
		if (isset($_REQUEST["keywords"])) 
		{$value = $_REQUEST["keywords"];
		 echo " value=\"$value\"";
		}
		echo ">
		<select name=\"year\">";
			foreach ($yeargroup as $index => $value)
				{
				echo "<option value=\"$value\"";
				if ($value == $thisyear) {echo " selected";}
				echo ">$value</option>";	
				}
		
		echo "
		</select><input type=\"hidden\" name=\"event\" value=\"search\">
		<input type=\"hidden\" name=\"m\" value=\"$m\">
		<input type=\"submit\" name=\"submit\" value=\"$phrase[863]\">
		
		
		</form>";
		}
		
		if (isset($_REQUEST["keywords"])) {
		
			
			
				 //get list of usages for this calendar
		$where = " and event_catid IN ("; 
		$typecount = 1;
		$sql = "SELECT cat FROM cal_bridge where m = \"$m\"";
		$DB->query($sql,"calendar.php");
		
		$numrows = $DB->countrows();
		while ($row = $DB->get()) 
					{
					
					$cat =$row["cat"];
					if ($typecount == 1)
						{
						$where .= "'$cat'";
						}
					else
						{
						$where .= ",'$cat'";
						}
					$typecount++;
					
					}
					
		$where .= " )";
		if ($numrows == 0) {$where = "";}
		
		if (isset($_REQUEST["keywords"])) {
	
		$keywords = $DB->escape($_REQUEST["keywords"]);
	
		} else { $keywords = "";}
		
		$year = $DB->escape($_REQUEST["year"]);
		
		echo "<h2>$phrase[329] \"$_REQUEST[keywords]\" $year</h2>";
		
		
		if ($DB->type == "mysql")
		{
		$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_name, cat_colour, cat_waitinglist, cat_takesbookings, event_start FROM cal_events, cal_cat,cal_bridge  where cal_cat.cat_id = cal_events.event_catid and cal_bridge.m = '$m' and cal_bridge.cat = cal_cat.cat_id and year(FROM_UNIXTIME(event_start)) = \"$year\" and MATCH (event_name,event_description,event_location,tags) AGAINST (\"$keywords\" IN BOOLEAN MODE) and template = \"0\" and event_catid $where $locationinsert order by cat_name, event_start";	
		}
		
		else
		{
			
			
		$keywords = trim($keywords);
       			
       			$words = explode(" ",$keywords);
       			
       			$string = "";
       			
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
				$string .= " and (event_name like '%$value%' or event_description like '%$value%' or event_location like '%$value%' or tags like '%$value%') ";	
				$counter++;
				}	
				if ($keywords == "") {$string = " and '1' = '2'";}
			
			
		$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_name, cat_colour, cat_waitinglist, cat_takesbookings, event_start FROM cal_events, cal_cat,cal_bridge  where cal_cat.cat_id = cal_events.event_catid and cal_bridge.m = '$m' and cal_bridge.cat = cal_cat.cat_id and strftime('%Y',datetime ( event_start , 'unixepoch' ,'localtime')) = \"$year\" $string and template = \"0\" and event_catid $where $locationinsert order by cat_name, event_start";	
		}
		//echo $sql;
		
		$DB->query($sql,"calendar.php");
		
	
			while ($row = $DB->get()) 
					{
					$array_id[] =$row["event_id"];
					$array_eventname[] =$row["event_name"];
					$array_location[] =$row["event_location"];
					$array_day[] = strftime("%x", $row["event_start"]);			
					$array_time[] = date("g:i a", $row["event_start"]);
					$array_cancelled[] =$row["cancelled"];
					$array_colour[] =$row["cat_colour"];
					$array_catname[] =$row["cat_name"];
					$array_maxbookings[] =$row["maxbookings"];
					$array_takesbookings[] =$row["cat_takesbookings"];
					$array_waitinglist[] =$row["cat_waitinglist"];
					}
					
				if (!isset($array_id)) { echo $phrase[333];}

			//get list of events and their total bookings
				if ($DB->type == "mysql")
		{
			$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					$where  and year(FROM_UNIXTIME(event_start)) = \"$year\"
					group by event_id";
		}
					else
		{
			$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					$where  and strftime('%Y',datetime ( event_start , 'unixepoch','localtime' )) = \"$year\"
					group by event_id";
		}
			
			$DB->query($sql,"calendar.php");
			while ($row = $DB->get()) 
					{
					$count_eventid[] = $row["event_id"];
					$count_total[] = $row["total"];
					
					}	
			
			//get list of events and their waiting list
		
					if ($DB->type == "mysql")
		{
					$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status =2
					$where  and year(FROM_UNIXTIME(event_start)) = \"$year\"
					group by event_id";
		}
			
		
					else
		{
					$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status =2
					$where  and strftime('%Y',datetime ( event_start , 'unixepoch' ,'localtime')) = \"$year\"
					group by event_id";
		}
		
		
		
			$DB->query($sql,"calendar.php");
			while ($row = $DB->get()) 
					{
					$wait_eventid[] = $row["event_id"];
					$wait_total[] = $row["total"];
					
					}		
					
					
//					
//			echo "<a href=\"calendar.php?m=$m\">$phrase[118]</a>  | <a href=\"calendar.php?m=$m&amp;event=search\">$phrase[863]<br><br> 
//			
//			<a href=\"calendar.php?m=$m&amp;event=all&amp;year=$previous\" style=\"margin-right:4em\">$phrase[525]</a> 
//			
//			<span style=\"font-size:2em\">$year</span> <a href=\"calendar.php?m=$m&amp;event=all&amp;year=$next\" style=\"margin-left:4em\">$phrase[526] </a> 
//		
//			<br><br>	
//			
//			
//			";
			$catname = "";
			
		if (isset($array_id))
			{
			foreach ($array_id as $index => $eventid)
			{
			$count = 0;
			$max = 0;
			$display = $array_catname[$index];
			if ($display <> $catname) {
				
				if ($catname <> "") { echo "</table>";}
				
				$catname = $display;
				echo "<h2 style=\"color:$array_colour[$index];font-size:1.5em\">$display</h2>
				<table style=\"margin:0 auto;text-align:left;width:90%\" class=\"colourtable\" cellpadding=\"7\">
					<tr style=\"font-weight:bold\"><td>$phrase[311]</td><td>$phrase[121]</td><td>$phrase[186]</td><td>$phrase[185]</td>";
				
				if ($array_takesbookings[$index] > 0)
				{
					
				echo "<td>$phrase[140]</td><td>$phrase[127]</td>";
				if ($array_waitinglist[$index] == 1)
				{
				
				echo "<td>$phrase[148]</td>";
				}
				}
				echo "</tr>";
				
			}
			
			echo "<tr style=\"color:$array_colour[$index]\"><td><a href=\"calendar.php?m=$m&amp;event=book&amp;event_id=$array_id[$index]\" style=\"color: $array_colour[$index]\">$array_eventname[$index]</a></td><td>";
			
			$_location = $array_location[$index];
					
					if (key_exists($_location,$branches)) {echo "$branches[$_location]<br>";}
					else {
					
						if ($array_location[$index] != "") {echo "$array_location[$index]<br> ";}
						}	
			echo "</td><td>$array_day[$index]</td><td>$array_time[$index]</td>";

			if ($array_takesbookings[$index] > 0)
				{
			echo "<td>";
			
			if (isset($count_eventid))
			{
				foreach ($count_eventid as $i => $id)
				{
				if ($id == $eventid) {
					$count = $count_total[$i];
					}
				}
			}
			echo "$count</td><td>";
		
			if ($array_maxbookings[$index] > 0) 
			{
				$max = $array_maxbookings[$index];
				echo $max;
					if ($count > 0 && $max > 0 && $count >= $max)
				{
								echo " <span style=\"color:#ff3333;\">$phrase[156]</span><br>";	
								}
				
			}  else {echo $phrase[453];}
			echo "</td>";
			
			
		
		
			
				if ($array_waitinglist[$index] == 1)
				{
			echo "<td>";
			
				if (isset($wait_eventid))
			{
				foreach ($wait_eventid as $i => $id)
				{
				if ($id == $eventid) {echo "$wait_total[$i]";}
				}
			}
			echo "</td>";
			
				}
			
				}
			echo "</tr>";	
				
			} 
			}	
			echo "</table><br>";		
					
					
		}
			
		}
	elseif(isset($_GET["event"]) && $_GET["event"] == "all")
		{
		
				if (isset($_REQUEST["year"]))
					{ $year = $DB->escape($_REQUEST["year"]);
						
						} 
			else 	{ 
					$year = date("Y"); 
				
					}	
			$next = $year + 1;
			$previous = $year -1;
			
			
		echo "<h1>$modname</h1>";
			
			 //get list of usages for this calendar
		$where = " and event_catid IN ("; 
		$typecount = 1;
		$sql = "SELECT cat FROM cal_bridge where m = \"$m\"";
		$DB->query($sql,"calendar.php");
		
		$numrows = $DB->countrows();
		while ($row = $DB->get()) 
					{
					
					$cat =$row["cat"];
					if ($typecount == 1)
						{
						$where .= "'$cat'";
						}
					else
						{
						$where .= ",'$cat'";
						}
					$typecount++;
					
					}
					
		$where .= " )";
		if ($numrows == 0) {$where = "";}
		
		
		if ($DB->type == "mysql")
		{
		
				$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_name, cat_colour, cat_waitinglist, cat_takesbookings, event_start FROM cal_events, cal_cat, cal_bridge  where cal_cat.cat_id = cal_events.event_catid and cal_bridge.m = '$m' and cal_bridge.cat = cal_cat.cat_id and year(FROM_UNIXTIME(event_start)) = \"$year\" and template = \"0\" and event_catid $where $locationinsert order by cat_name, event_start";	
		$DB->query($sql,"calendar.php");
		
		}
		
		else
		{
		
				$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_name, cat_colour, cat_waitinglist, cat_takesbookings, event_start FROM cal_events, cal_cat,cal_bridge  where cal_cat.cat_id = cal_events.event_catid and cal_bridge.m = '$m' and cal_bridge.cat = cal_cat.cat_id and strftime('%Y',datetime ( event_start , 'unixepoch','localtime' )) = \"$year\" and template = \"0\" and event_catid $where $locationinsert order by cat_name, event_start";	
		$DB->query($sql,"calendar.php");
		
		}
		
			while ($row = $DB->get()) 
					{
					$array_id[] =$row["event_id"];
					$array_eventname[] =$row["event_name"];
					$array_location[] =$row["event_location"];
					$array_day[] = strftime("%x", $row["event_start"]);			
					$array_time[] = date("g:i a", $row["event_start"]);
					$array_cancelled[] =$row["cancelled"];
					$array_colour[] =$row["cat_colour"];
					$array_catname[] =$row["cat_name"];
					$array_maxbookings[] =$row["maxbookings"];
					$array_takesbookings[] =$row["cat_takesbookings"];
					$array_waitinglist[] =$row["cat_waitinglist"];
					}

			//get list of events and their total bookings
			if ($DB->type == "mysql")
		{
		
			$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					$where  and year(FROM_UNIXTIME(event_start)) = \"$year\"
					group by event_id";
		}
			
			
			else
		{
		
			$sql = "SELECT count(*) as total, cal_events.event_id as event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					$where  and strftime('%Y',datetime ( event_start , 'unixepoch','localtime' )) = \"$year\"
					group by event_id";
		}
			
		
		
			$DB->query($sql,"calendar.php");
			while ($row = $DB->get()) 
					{
					$count_eventid[] = $row["event_id"];
					$count_total[] = $row["total"];
					
					}	
			
			//get list of events and their waiting list
				if ($DB->type == "mysql")
		{
			$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status =2
					$where  and year(FROM_UNIXTIME(event_start)) = \"$year\"
					group by event_id";
		}
		
				else
		{
			$sql = "SELECT count(*) as total, cal_events.event_id as event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status =2
					$where  and strftime('%Y',datetime ( event_start , 'unixepoch' ,'localtime')) = \"$year\"
					group by event_id";
		}
			
			$DB->query($sql,"calendar.php");
			while ($row = $DB->get()) 
					{
					$wait_eventid[] = $row["event_id"];
					$wait_total[] = $row["total"];
					
					}		
					
					
					
			echo "<a href=\"calendar.php?m=$m\">$phrase[118]</a> | <a href=\"calendar.php?m=$m&amp;event=upcoming\">$phrase[980]</a>   | <a href=\"calendar.php?m=$m&amp;event=search\">$phrase[863]</a><br><br> 
			
			<a href=\"calendar.php?m=$m&amp;event=all&amp;year=$previous\" style=\"margin-right:4em\">$phrase[525]</a> 
			
			<span style=\"font-size:2em\">$year</span> <a href=\"calendar.php?m=$m&amp;event=all&amp;year=$next\" style=\"margin-left:4em\">$phrase[526] </a> 
		
			<br><br>	
			
			
			";
			$catname = "";
			
		if (isset($array_id))
			{
			foreach ($array_id as $index => $eventid)
			{
			$count = 0;
			$max = 0;
			$display = $array_catname[$index];
			if ($display <> $catname) {
				
				if ($catname <> "") { echo "</table>";}
				
				$catname = $display;
				echo "<h2 style=\"color:$array_colour[$index];font-size:1.5em\">$display</h2>
				<table style=\"margin:0 auto;text-align:left;width:90%\" class=\"colourtable\" cellpadding=\"7\">
					<tr style=\"font-weight:bold\"><td>$phrase[311]</td><td>$phrase[121]</td><td>$phrase[186]</td><td>$phrase[185]</td>";
				
				if ($array_takesbookings[$index] > 0)
				{
					
				echo "<td>$phrase[140]</td><td>$phrase[137]</td>";
				if ($array_waitinglist[$index] == 1)
				{
				
				echo "<td>$phrase[148]</td>";
				}
				}
				echo "</tr>";
				
			}
			
			echo "<tr style=\"color:$array_colour[$index]\"><td><a href=\"calendar.php?m=$m&amp;event=book&amp;event_id=$array_id[$index]\" style=\"color: $array_colour[$index]\">$array_eventname[$index]</a></td><td>";
					$_location = $array_location[$index];
					
					if (key_exists($_location,$branches)) {echo "$branches[$_location]<br>";}
					else {
					
						if ($array_location[$index] != "") {echo "$array_location[$index]<br> ";}
						}	
			echo "</td><td>$array_day[$index]</td><td>$array_time[$index]</td>";

			if ($array_takesbookings[$index] > 0)
				{
			echo "<td>";
			
			if (isset($count_eventid))
			{
				foreach ($count_eventid as $i => $id)
				{
				if ($id == $eventid) {
					$count = $count_total[$i];
					}
				}
			}
			echo "$count</td><td>";
		
			if ($array_maxbookings[$index] > 0) 
			{
				$max = $array_maxbookings[$index];
				
					if ($count > 0 && $max > 0 && $count >= $max)
				{
								echo " <span style=\"color:#ff3333;\">$phrase[156]</span><br>";	
								}
								else {echo $max - $count;}
				
			}  else {echo $phrase[453];}
			echo "</td>";
			
			
		
		
			
				if ($array_waitinglist[$index] == 1)
				{
			echo "<td>";
			
				if (isset($wait_eventid))
			{
				foreach ($wait_eventid as $i => $id)
				{
				if ($id == $eventid) {echo "$wait_total[$i]";}
				}
			}
			echo "</td>";
			
				}
			
				}
			echo "</tr>";	
				
			} 
			}	
			echo "</table><br>";		
					
					
		}
	
	
	
	elseif(isset($_GET["event"]) && $_GET["event"] == "addbooking")
		{
			
		
			
		$sql = "SELECT cat_name,cat_email, cat_colour,cat_cost, cat_age, cat_staffname, cat_address, cat_multiple,cat_confirmation, cat_print,cat_receipt, cat_comments, cat_notes, cat_takesbookings, cat_waitinglist,  maxbookings, event_name, event_cost, event_location,event_description, cat_trainer, trainer, event_staffnotes, cancelled , event_start,age_range  FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and cal_events.event_id = \"$event_id\"";
		
		//echo $sql;
		$DB->query($sql,"calendar.php");
		$row = $DB->get();
		
		$cat_name = formattext($row["cat_name"]);
		$cat_colour = $row["cat_colour"];
		$event_name = formattext($row["event_name"]);
		$event_location = formattext($row["event_location"]);
		$event_cost = formattext($row["event_cost"]);
		$event_description = formattext($row["event_description"]);
		$cat_cost = $row["cat_cost"];
		$cat_notes = $row["cat_notes"];
		$event_staffnotes = formattext($row["event_staffnotes"]);
		$agerange = $row["age_range"];
		$trainer = formattext($row["trainer"]);
		$displayday = strftime("%A %x", $row["event_start"]);
		$displaytime = date("g:i a", $row["event_start"]);
		$maxbookings = $row["maxbookings"];
		$cat_takesbookings = $row["cat_takesbookings"];
		$cat_age = $row["cat_age"];
		$cat_address = $row["cat_address"];
		$cat_multiple = $row["cat_multiple"];
		$cat_comments = $row["cat_comments"];
		$cat_staffname = $row["cat_staffname"];
		$cat_confirmation = $row["cat_confirmation"];
		$cat_waitinglist = $row["cat_waitinglist"];
		$cat_print = $row["cat_print"];
		$cat_trainer = $row["cat_trainer"];
		$cat_email = $row["cat_email"];
		$cat_receipt = $row["cat_receipt"];
		$cancelled = $row["cancelled"];
                
                $minage = 1;
		$maxage = 1;
		
			if ($event_cost == "" || $event_cost == "0") {$event_cost = $phrase[963];} else {$event_cost =  $moneysymbol . $event_cost;}
		
		echo "<h1 >$modname</h1>
<a href=\"calendar.php?m=$m&amp;t=$t\">$phrase[118]</a>| <a href=\"calendar.php?m=$m&amp;event=upcoming\">$phrase[980]</a>  | <a href=\"calendar.php?m=$m&amp;event=all\">$phrase[864]</a>
| <a href=\"calendar.php?m=$m&amp;event=search\">$phrase[863]</a> | <a href=\"calendar.php?m=$m&amp;t=$t&amp;event_id=$event_id&amp;event=book\">$phrase[699]</a>
<h2 style=\"color:$cat_colour;\">$cat_name</h2><br><b>$event_name</b>";
			
		//count how many booked and waiting people there are
			$sql = "SELECT count(*) as count FROM cal_bookings  where eventno = \"$event_id\" and status < 3";
			$DB->query($sql,"calendar.php");
			$row = $DB->get();
			$count = $row["count"]
			;

			
			
			echo "
			<h2>";
			
			
			if ($maxbookings == 0)
					{
				//add booking
		 echo $phrase[129];			}
			elseif ($cat_waitinglist == 1)
			{
				if ($count >= $maxbookings)
				{
					//event full 
					//add to waiting list
					echo "$phrase[150]";
				}
				else {
					//add booking
		 echo $phrase[129];			}
			}
			else {
				
					//add booking
			 echo $phrase[129];		
				
			}
			
			
			echo "</h2>
			<FORM method=\"POST\" action=\"calendar.php\">";
			
		
    
    
  
	echo " 
	<div style=\"width:50%;float:left;\"><div style=\"text-align:left;padding:0 0 1em 25%;\">";

		if (($maxbookings <> 0) && ($cat_waitinglist <> 1) && ($count >= $maxbookings))
					{
				//add booking
				$WARNING = $phrase[262];
				}
			
				
			if (!isset($WARNING))
			{
			
			
			
			
			
										
										
										echo "<div id=\"replace\" style=\"margin:0;padding:0;\">
									<div class=\"formdiv\"><b>$phrase[130]</b><br><INPUT type=\"text\" name=\"firstname[0]\" id=\"fn_0\" onchange=\"_required()\" onkeyup=\"_required()\"  maxlength=\"50\" class=\"required\"> <span style=\"color:red\" id=\"fn_0_w\">$phrase[110]</span></div>
									<div class=\"formdiv\"><b>$phrase[131]</b><br><INPUT type=\"text\" name=\"lastname[0]\" id=\"ln_0\" onchange=\"_required()\" onkeyup=\"_required()\"  maxlength=\"50\" class=\"required\"> <span style=\"color:red\" id=\"ln_0_w\">$phrase[110]</span></div>";
			
			
			
	
			
											if ($cat_age == 1)
											{
											
											$pos = strpos($agerange,":");

                                                                                        if ($pos === FALSE) {$minage = 0; $maxage = 120;}
                                                                                        else {
											$minage = substr($agerange,0,$pos);
											$maxage = trim(substr($agerange,$pos + 1));	
                                                                                        }
                                                                                       
											
											//check input values are sensible
											if (!(($maxage > $minage) && ($minage > 0 && $minage < 120) && ($maxage > 1 && $maxage < 120))) 
											{$minage = 0; $maxage = 120;}	
												
												
												
											echo "
											<div class=\"formdiv\"><b>$phrase[133]</b><br><select class=\"age\" name=\"age[0]\" id=\"age_0\" onchange=\"_required()\"><option value=\"noselection\">$phrase[885]</option>";
											$c = $minage;
											while ($c <= $maxage)
											{
											echo "<option value=\"$c\">$c</option>
";	
											$c++;
											
											}
											
											
											echo "</select> <span style=\"color:red\" id=\"age_0_w\">$phrase[110]</span></div>";
											} else { echo "
											<INPUT type=\"hidden\" name=\"age[0]\"  value=\"0\" size=\"2\" maxlength=\"2\">";
											$minage = 1;
											$maxage = 1;
											}
											
											
											
										echo "	
										<div class=\"formdiv\"><b>$phrase[132]</b><br>
				
										<INPUT type=\"text\" name=\"telephone[0]\" id=\"t_0\" onchange=\"_required()\" onkeyup=\"_required()\" size=\"20\" maxlength=\"20\" class=\"required\"> <span style=\"color:red\" id=\"t_0_w\">$phrase[110]</span></div>";
										
                                                                              
										
										if ($cat_address == 1)
											{
											echo "
											<div  class=\"formdiv\"><b>$phrase[134]</b><br><textarea cols=\"40\" rows=\"5\" name=\"address[0]\" id=\"address_0\"></textarea></div>";
											} 	else	{echo "
											<INPUT type=\"hidden\" name=\"address[0]\" value=\"\">";}
									
										if ($cat_comments == 1)
											{
											echo "<div class=\"formdiv\"><b>$phrase[135]</b><br><textarea cols=\"40\" rows=\"5\" id=\"comments_0\" name=\"comments[0]\"></textarea></div>	
											";
											} else	{echo "
											<INPUT type=\"hidden\" name=\"comments[0]\" value=\"\">";}
										
										
										
										echo "
										</div>";	//end replacediv							
										
										
											if ($cat_multiple  > 1)
											{
											echo "
											<div class=\"formdiv\"><b>$phrase[137]</b><br><select name=\"group\" id=\"group\" onchange=\"replace()\">";
											
											if (($cat_waitinglist == 1) && ($maxbookings <= $count)) 
											{
											$limit = $cat_multiple;
											
											//echo "hello one max is $maxbookings count is $count limit is $limit";	
											}
											elseif ($maxbookings == 0) 
											//unlimited bookings
											{
											$limit = $cat_multiple;
											}
											
											
											else
											{   
												$vacancies = $maxbookings - $count;
											if ($vacancies > $cat_multiple) 
											{$limit = $cat_multiple;} else {$limit = $vacancies;}
											
											}
											
											
								
											
										
											$counter = 1;
											while ($counter <= $limit)
											{
											 echo "<option value=\"$counter\">$counter</option>
											 ";	
											$counter++;
											}
											
											
											echo "</select></div>
											";
											
											}
											
										if ($cat_email == 1 )
											{
											echo "<div class=\"formdiv\"><b>$phrase[259]</b><br><input type=\"text\" onchange=\"_required()\" onkeyup=\"_required()\"id=\"email\" name=\"email\" size=\"40\" class=\"required\" maxlength=\"250\">
                                                                                    <span style=\"color:red\" id=\"email_w\">$phrase[110]</span></div>	
											";
											}	
										else	{echo "<INPUT type=\"hidden\" name=\"email\" value=\"\">";}
											
									
											
											
										if ($cat_staffname == 1)
											{
											echo "<div class=\"formdiv\"><b>$phrase[136]</b><br><input type=text  maxlength=\"60\" name=\"staffname\" class=\"required\" id=\"s_0\" onchange=\"_required()\" onkeyup=\"_required()\"><span style=\"color:red\" id=\"s_0_w\"> $phrase[110]</span></div>
											";
											
											}
										else	
											{echo "<INPUT type=\"hidden\" name=\"staffname\" value=\"\">";}
	
	
										
										
										
										if ($cat_cost == 1 )
											{
											echo "<div class=\"formdiv\"><b>$phrase[138]</b><br><select name=\"paid\">
											<option value=\"0\">$phrase[13]</option>
											<option value=\"1\">$phrase[12]</option>
											</select></div> 
											";
											}
											
										if ($cat_receipt == 1)
											{
											echo "<div class=\"formdiv\"><b>$phrase[1070]</b><br><input type=text size=\"10\" maxlength=\"20\" name=\"receipt\" ></div>
											";
											
											}
										
											
											
											
										if ($cat_confirmation == 1 )
											{
											echo "<div class=\"formdiv\"><b>$phrase[139]</b><br><select name=\"confirmation\">
											<option value=\"0\">$phrase[13]</option>
											<option value=\"1\">$phrase[12]</option>
											</select></div> 
											";
											}
									
										echo "<div  class=\"formdiv\">";
										if ($cat_cost <> 1 )
											{ echo "<INPUT type=\"hidden\" name=\"paid\" value=\"0\">"; }
											
										if ($cat_confirmation <> 1 )
											{	
											echo "
											<INPUT type=\"hidden\" name=\"confirmation\" value=\"0\">";	
											}	
											
										if ($cat_receipt <> 1 )
											{	
											echo "
											<INPUT type=\"hidden\" name=\"receipt\" value=\"0\">";	
											}				
										//if ($cat_age <> 1)
										//{
											//echo "
											//<INPUT type=\"hidden\" name=\"age\"  value=\"0\" size=\"2\" maxlength=\"2\">";
										//}
									
											
									
										
											
										
										
											
										
			if ($maxbookings == 0)
					{
					//add booking
					 echo "<INPUT type=\"hidden\" name=\"status\" value=\"1\">";		
					}
			elseif ($cat_waitinglist == 1)
					{
					if ($count >= $maxbookings)
						{
						//event full 
						//add to waiting list
						echo "<INPUT type=\"hidden\" name=\"status\" value=\"2\">";
						}
					else 
						{
						//add booking
						 echo "<INPUT type=\"hidden\" name=\"status\" value=\"1\">";			
						}
					}
			else 
					{
				
			 		echo "<INPUT type=\"hidden\" name=\"status\" value=\"1\">";	
				
					}	
											
								?>
										<INPUT type="hidden" name="event" value="book">
										<INPUT type="hidden" name="maxbookings" value="<?php echo $maxbookings?>">
										<INPUT type="hidden" name="event_id" value="<?php echo $event_id?>">
										<INPUT type="hidden" name="t" value="<?php echo $t?>">
										<INPUT type="hidden" name="m" value="<?php echo $m?>">
									
										<INPUT type="hidden" name="update" value="book">
										<INPUT type="hidden" name="event" value="book">
										<?php
										echo "<INPUT type=\"submit\" id=\"submit\" name=\"submit\" value=\"";
							
			if ($maxbookings == 0)
					{
				//add booking
		 echo $phrase[129];			}
			elseif ($cat_waitinglist == 1)
			{
				if ($count >= $maxbookings)
				{
					//event full 
					//add to waiting list
					echo "$phrase[150]";
				}
				else {
					//add booking
		 echo $phrase[129];			}
			}
			else {
				
					//add booking
			 echo $phrase[129];		
				
			}				
		
				
			echo "\">
			</div>";
			}
			echo "

			<script  type=\"text/javascript\">	
		
             
			      function _required()
            {
			
            
            var formInputs = document.getElementsByTagName('input');
			var disabled = false;
    		for (var i = 0; i < formInputs.length; i++) 
    			{
				var theInput = formInputs[i];
		
				if (theInput.type == 'text' && theInput.className == 'required')
					{
					
					var warning =  theInput.id + '_w';
					//alert(warning)
					if ( theInput.value == '') 
						{
						disabled = true;
						document.getElementById(warning).style.display = 'inline';
						}
						
					else {
						document.getElementById(warning).style.display = 'none';
						}
					
					
					}
				}
				
		";
				
			
			if ($cat_age == 1)
				{
						echo "
			var selectMenus = document.getElementsByTagName('select');	
			for (var i = 0; i < selectMenus.length; i++) 
    			{
				var menu = selectMenus[i];
				var warning =  menu.id + '_w';
				//alert(menu.id)
				
				if (menu.className == 'age')
				{
				
				//alert(menu.options[menu.selectedIndex].value)	
				
					if (menu.options[menu.selectedIndex].value == \"noselection\")
					{
					//alert(\"nothing selected\")
					//alert(menu.options[menu.selectedIndex].value)
					disabled = true;
					document.getElementById(warning).style.display = 'inline';
    				}
    				else
    				{
    				//alert(\"selected\")
					document.getElementById(warning).style.display = 'none';
    				}	
    			}					
    			}

				";
				}
			
			
	
			echo "if (disabled == true) {document.getElementById('submit').disabled = true;}
			else {document.getElementById('submit').disabled = false;}
				
				
            } 

			function copy(id)
			{
		
			document.getElementById('ln_' + id).value = document.getElementById('ln_0').value
			document.getElementById('t_' + id).value = document.getElementById('t_0').value
			if (document.getElementById('a_' + id))
				{
				document.getElementById('a_' + id).value = document.getElementById('a_0').value
			
				}
				
				_required();
			}	
			
				
			function replace() {
			
			if (document.getElementById('replace') && document.getElementById('group'))
			{
			
			var lname =  document.getElementById('ln_0').value
			var telephone =  document.getElementById('t_0').value
			var firstname =  document.getElementById('fn_0').value
			var comments;
			var address;
			
			
			if (document.getElementById('comments_0'))
			{
			comments = document.getElementById('comments_0').value;
			} else {comments = '';}
			
			if (document.getElementById('address_0'))
			{
			address = document.getElementById('address_0').value;
			} else {address = '';}
			
			if (document.getElementById('age_0'))
			{
			var agemenu = document.getElementById('age_0');
			var selectedAge = agemenu.options[agemenu.selectedIndex].value
			}
		
		
    		 var html = '';
    		 
    		
    		 var places = document.getElementById('group').value
    		 
    		for (var i=0;i<places;i++)
    		{
    		var displaycount = i + 1;
    		html = html + '<div style=\"border:1px solid grey;margin:0 1em 1em 0;padding:0.5em\"><b>' + displaycount + '</b>\\n';
    	
    		html = html + '<br>\\n';
    		html = html + '<div style=\"margin:1em;\"><b>$phrase[130]</b><br><INPUT type=\"text\" name=\"firstname[' + i + ']\"';
			if (i == 0) { html = html + ' value=\"' + firstname + '\" ';}
    		html = html + 'id=\"fn_' + i + '\" onchange=\"_required()\" onkeyup=\"_required()\" size=\"30\" maxlength=\"50\" class=\"required\"> <span style=\"color:red\" id=\"fn_' + i + '_w\">$phrase[110]</span></div>\\n';
			html = html + '<div style=\"margin:1em;\"><b>$phrase[131]</b><br><INPUT type=\"text\" name=\"lastname[' + i + ']\"  value=\"' + lname + '\"  id=\"ln_' + i + '\"  onchange=\"_required()\" onkeyup=\"_required()\" size=\"30\" maxlength=\"50\" class=\"required\"> <span style=\"color:red\" id=\"ln_' + i + '_w\">$phrase[110]</span><br></div>\\n';
			if (document.getElementById('age_0'))
				{
    			html = html + '<div style=\"margin:1em;\"><b>$phrase[133]</b><br><select class=\"age\" name=\"age[' + i + ']\" id=\"age_' + i + '\" onchange=\"_required()\"><option value=\"noselection\">$phrase[885]</option>';
    		
    			
    				
			var minage = $minage;
			var maxage = $maxage;
			
			while (minage <= maxage)
					{ 
					html = html + '<option value=\"' + minage + '\"';
					
					if (typeof(selectedAge) != 'undefined' && selectedAge == minage && i == 0) {html = html + ' selected';}
					html = html + '>' + minage + '</option>\\n';
					minage++;
					}
			
			
    			
					
    			html = html + '</select> <span style=\"color:red\" id=\"age_' + i + '_w\">$phrase[110]</span></div>\\n';
				
				
				}
			else
				{
				html = html + '<INPUT type=\"hidden\" name=\"age[' + i + ']\"  value=\"0\">\\n';
				}
				
			html = html + '<div style=\"margin:1em;\"><b>$phrase[132]</b><br><INPUT type=\"text\" name=\"telephone[' + i + ']\" value=\"' + telephone + '\" id=\"t_' + i + '\" onchange=\"_required()\" onkeyup=\"_required()\" size=\"20\" maxlength=\"20\" class=\"required\">  <span style=\"color:red\" id=\"t_' + i + '_w\">$phrase[110]</span></div>\\n';
			
			
			if (document.getElementById('address_0'))
				{
    			html = html + '<div  style=\"margin:1em;\"><b>$phrase[134]</b><br><textarea cols=\"40\" rows=\"5\" name=\"address[' + i + ']\" id=\"address_' + i + '\">' + address + '</textarea></div>\\n';
				}
			else
				{
				html = html + '<INPUT type=\"hidden\" name=\"address[' + i + ']\" value=\"\">\\n';
				}
				
			
			if (document.getElementById('comments_0'))
				{
    			html = html + '<div  style=\"margin:1em;\"><b>$phrase[135]</b><br><textarea cols=\"40\" rows=\"5\" name=\"comments[' + i + ']\" id=\"comments_' + i + '\">' + comments + '</textarea></div>\\n';
				}
			else
				{
				html = html + '<INPUT type=\"hidden\" name=\"comments[' + i + ']\" value=\"\">\\n';
				}		
			
			html = html + '</div>\\n';
    		}
    		
     		
			document.getElementById('replace').innerHTML = html;
			_required();
			}
			
			}



addEvent(window, 'load', _required);

</script>
		
	
	</div></div>
			 <div style=\"width:35%;float:left;\"><div style=\"text-align:left;padding:2em;border:solid 1px black;\">
			  $event_description<br><br>
    
			<b>$phrase[121]</b> <br>";
			
			
			
					
					if (key_exists($event_location,$branches)) {echo "$branches[$event_location]<br>";}
					else {
					
						if ($event_location != "") {echo "$event_location<br> ";}
						}	
			echo "<br>
<b>$phrase[122]</b><br> $displaytime $displayday<br><br>
		
	";
		if ($cat_notes && $event_staffnotes != "")
			{
			echo "<b>$phrase[124]</b><br>$event_staffnotes<br><br>";
			}
		
		if ($cat_trainer == 1)
			{
			echo "<b>$phrase[125]</b><br>$trainer<br><br>";
			}	
		if ($cat_takesbookings > 0)
			{
				
				
					
				if ($cat_cost)
					{
					echo "<b>$phrase[126]</b><br>$event_cost<br><br>";
					}
				echo "<b>$phrase[127]</b><br>";
				if ($maxbookings == 0)
					{
					echo " $phrase[128]";
					}
				else
					{
					echo " <b>$maxbookings </b>";
					}	
			}		
					
		echo "<br><br>
			
	
		
			
			</div></div>";
			
			
		}

		/*
	elseif(isset($_GET["event"]) && $_GET["event"] == "cancel")
		{
		
		
	echo "<center><div ><h1>$modname</h1></div><br><b>$phrase[239]</b><br><br>
	<a href=\"calendar.php?m=$m&amp;t=$t&amp;update=cancel&amp;event=book&amp;event_id=$event_id&bookinno=$_GET[bookingno]\">$phrase[12]</a> | <a href=\"calendar.php?m=$m&amp;t=$t&amp;event=book&amp;event_id=$event_id\">$phrase[13]</a></center>";
		
	}
	*/
		
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "lookup"  && $access->thispage > 1)
		{
			
			//display form	
	echo "<h1>$modname</h1>
	
		<a href=\"calendar.php?m=$m&amp;t=$t\">$phrase[118]</a> | <a href=\"calendar.php?m=$m&amp;event=all\">$phrase[864]</a> | <a href=\"calendar.php?m=$m&amp;event=search\">$phrase[863]</a> 
	<h2>$phrase[731]</h2><br><br>";

	
	echo "<form action=\"calendar.php\" method=\"get\"  style=\"width:200px;padding:1em;margin:1em auto;text-align:left;border:solid 1px grey\">
      <p style=\"padding:0;margin:0\">
						  <label style=\"width:200px;display:block\"><b>$phrase[130]</b></label> <input type=\"text\" name=\"firstname\" > 
		
			  <label style=\"width:200px;display:block\"><b>$phrase[131]</b></label> <input type=\"text\" name=\"lastname\" > 
	
						       <input type=\"hidden\" name=\"m\" value=\"$m\">
						          <input type=\"hidden\" name=\"event\" value=\"lookup\">
						  <input type=\"hidden\" name=\"type\" value=\"name\">
						    <input type=\"submit\" name=\"submit\" value=\"$phrase[282]\" >
					
					</p>
					
						   </form>";
						   
						   /* start of Wyndham Edit 20140404 */

echo "<form action=\"calendar.php\" method=\"get\"  style=\"width:200px;padding:1em;margin:1em auto;text-align:left;border:solid 1px grey\">
      <p style=\"padding:0;margin:0\">
						  <label style=\"width:200px;display:block\"><b>$phrase[132]</b></label> <input type=\"text\" name=\"telephone\" > 
		
			  			       <input type=\"hidden\" name=\"m\" value=\"$m\">
						          <input type=\"hidden\" name=\"event\" value=\"lookup\">
						  <input type=\"hidden\" name=\"type\" value=\"phone\">
						    <input type=\"submit\" name=\"submit\" value=\"$phrase[282]\" >
					
					</p>
					
						   </form>";

/* end of Wyndham Edit 20140404 */
	
	if ($EVENTAUTHENTICATION == "local"  || $EVENTAUTHENTICATION == "web"  || $EVENTAUTHENTICATION == "ldap" )
 {
	echo "<form action=\"calendar.php\" method=\"get\"   style=\"width:200px;padding:1em;margin:1em auto;text-align:left;border:solid 1px grey\">
      <p style=\"padding:0;margin:0\">
						  <label style=\"width:200px;display:block\"><b>$phrase[460]</b></label> <input type=\"text\" name=\"cardnumber\" > 
			
						       <input type=\"hidden\" name=\"m\" value=\"$m\">
						          <input type=\"hidden\" name=\"event\" value=\"lookup\">
						  <input type=\"hidden\" name=\"type\" value=\"cardnumber\">
						    <input type=\"submit\" name=\"submit\" value=\"$phrase[282]\" >
						
					</p>
					
						   </form>";
 }


 if ($EVENTAUTHENTICATION == "email")
 {
 
 echo "<form action=\"calendar.php\" method=\"get\"   style=\"width:200px;padding:1em;margin:1em auto;text-align:left;border:solid 1px grey\">
      	<p>
						  <label style=\"width:200px;display:block\"><b>$phrase[259]</b></label> <input type=\"text\" name=\"email\" > 
								  <input type=\"hidden\" name=\"type\" value=\"email\">
						       <input type=\"hidden\" name=\"m\" value=\"$m\">
						          <input type=\"hidden\" name=\"event\" value=\"lookup\">
						 
						    <input type=\"submit\" name=\"submit\" value=\"$phrase[282]\" >
						
						</p>
						   </form>";
 }
 

 
 
 	if ($EVENTAUTHENTICATION == "email"  || $EVENTAUTHENTICATION == "web" )
 {
	echo "<form action=\"calendar.php\" method=\"get\"   style=\"width:200px;padding:1em;margin:1em auto;text-align:left;border:solid 1px grey\">
      <p style=\"padding:0;margin:0\">
						  <label style=\"width:200px;display:block\"><b>$phrase[1021]</b></label> <input type=\"text\" name=\"webid\" > 
			
						       <input type=\"hidden\" name=\"m\" value=\"$m\">
						          <input type=\"hidden\" name=\"event\" value=\"lookup\">
						  <input type=\"hidden\" name=\"type\" value=\"webid\">
						    <input type=\"submit\" name=\"submit\" value=\"$phrase[282]\" >
						
					</p>
					
						   </form>";
 }

 
 $sql = "SELECT count(*) as total FROM cal_cat, cal_bridge WHERE cal_bridge.m = '$m' and cal_bridge.cat = cal_cat.cat_id and cal_cat.cat_receipt = '1'";
 $DB->query($sql,"calendar.php");
 $row = $DB->get();
		
 $total = formattext($row["total"]);
 
if ($total > 0)
{
		echo "<form action=\"calendar.php\" method=\"get\"  style=\"margin:2em 0 0 40%;text-align:left\">
      <p style=\"padding:0;margin:0\">
						  <label style=\"width:200px;display:block\"><b>$phrase[1070]</b></label> <input type=\"text\" name=\"receipt\" > 
			
						       <input type=\"hidden\" name=\"m\" value=\"$m\">
						          <input type=\"hidden\" name=\"event\" value=\"lookup\">
						  <input type=\"hidden\" name=\"type\" value=\"receipt\">
						    <input type=\"submit\" name=\"submit\" value=\"$phrase[282]\" >
						
					</p>
					
						   </form>";
	
	
}
 //<script type=\"text/javascript\">
	//function setfocus() 
				 //{
			
				// document.getElementById('ff').focus(); 
				// }

				// setfocus();
				//</script>
	if (isset($_REQUEST["submit"]))
	{
		echo "<br><h2>Bookings for ";
		if ($_REQUEST["type"] == "cardnumber" && $_REQUEST["cardnumber"] != "") 
		{
			echo $_REQUEST["cardnumber"];
			$cardnumber =  $DB->escape(str_replace(" ", "", $_REQUEST["cardnumber"]));
			$sql = "SELECT firstname, lastname,event_name,status,eventno,time_booking, event_start,cat_cost,paid from cal_bookings, cal_events ,cal_cat  where cal_events.event_catid = cal_cat.cat_id and cal_events.event_id = cal_bookings.eventno and cardnumber  = \"$cardnumber\" order by bookingno";

		}
		
		
		    elseif ($_REQUEST["type"] == "name" && ($_REQUEST["firstname"] != "" || $_REQUEST["lastname"] != "")) 
{
			
			$firstname=  $DB->escape(trim($_REQUEST["firstname"]));
			$lastname=  $DB->escape(trim($_REQUEST["lastname"]));
			echo "$firstname $lastname";
		
			$insert = "";
			if ($DB->type == "mysql")
			{
				if ($firstname != "") {$insert .= " and firstname like '%$firstname%'"; }
			if ($lastname != "") {$insert .= " and lastname like '%$lastname%'"; }	
			}
			else
			{
			
			if ($firstname != "") {$insert .= " and lowercase(firstname) like '%$firstname%'"; }
			if ($lastname != "") {$insert .= " and lowercase(lastname) like '%$lastname%'"; }
			}
			$sql = "SELECT firstname, lastname,event_name,status,eventno,time_booking, event_start,cat_cost,paid from cal_bookings, cal_events ,cal_cat  where cal_events.event_catid = cal_cat.cat_id and cal_events.event_id = cal_bookings.eventno  $insert order by bookingno limit 200";

		}
	
	/* start of Wyndham edit 20140404 */

		    elseif ($_REQUEST["type"] == "phone" && ($_REQUEST["telephone"] != "")) 
{
			
			$firstname=  $DB->escape(trim($_REQUEST["firstname"]));
			$lastname=  $DB->escape(trim($_REQUEST["lastname"]));
                        $telephone=  $DB->escape(trim($_REQUEST["telephone"]));
			echo "$firstname $lastname $telephone";
		
			$insert = "";
			if ($DB->type == "mysql")
			{
				if ($firstname != "") {$insert .= " and firstname like '%$firstname%'"; }
			if ($lastname != "") {$insert .= " and lastname like '%$lastname%'"; }	
                        if ($telephone!= "") {$insert .= " and telephone like '%$telephone%'"; }	
			}
			else
			{
			
			if ($firstname != "") {$insert .= " and lowercase(firstname) like '%$firstname%'"; }
			if ($lastname != "") {$insert .= " and lowercase(lastname) like '%$lastname%'"; }
                        if ($telephone!= "") {$insert .= " and lowercase(telephone) like '%$telephone%'"; }
			}
			$sql = "SELECT firstname, lastname,telephone, event_name,status,eventno,time_booking, event_start,cat_cost,paid from cal_bookings, cal_events ,cal_cat  where cal_events.event_catid = cal_cat.cat_id and cal_events.event_id = cal_bookings.eventno  $insert order by bookingno limit 200";

		}

/*end of Wyndham Edit 20140404 */

		elseif ($_REQUEST["type"] == "email" && $_REQUEST["email"] != "") 
		{
			echo $_REQUEST["email"];
			$email =  $DB->escape($_REQUEST["email"]);
					$sql = "SELECT firstname, lastname,event_name,status,eventno,time_booking, event_start,cat_cost,paid from cal_bookings, cal_events,cal_cat   where cal_events.event_catid = cal_cat.cat_id and cal_events.event_id = cal_bookings.eventno and email  = \"$email\" order by bookingno";
		}
		
		elseif ($_REQUEST["type"] == "webid"  && $_REQUEST["webid"] != "" && $_REQUEST["webid"] != 0) 
		{
			echo "$phrase[1021] " . $_REQUEST["webid"];
			$webid =  $DB->escape($_REQUEST["webid"]);
					$sql = "SELECT firstname, lastname,event_name,status,eventno,time_booking, event_start,cat_cost,paid from cal_bookings, cal_events,cal_cat  where cal_events.event_catid = cal_cat.cat_id and cal_events.event_id = cal_bookings.eventno and webid  = \"$webid\" order by bookingno";
		}
		
		elseif ($_REQUEST["type"] == "receipt"  && $_REQUEST["receipt"] != "" && $_REQUEST["receipt"] != 0) 
		{
			echo "$phrase[1070] " . $_REQUEST["receipt"];
			$receipt =  $DB->escape($_REQUEST["receipt"]);
					$sql = "SELECT firstname, lastname,event_name,status,eventno,time_booking, event_start,cat_cost,paid from cal_bookings, cal_events,cal_cat  where cal_events.event_catid = cal_cat.cat_id and cal_events.event_id = cal_bookings.eventno and receipt  = \"$receipt\" order by bookingno";
					//echo $sql;
		}
		
		else {$sql = "select * from cal_bookings where 1 = 2";}
		
		
		echo "</h2>";
		
		
		
			
			
			
								
			$DB->query($sql,"calendar.php");
			$num = $DB->countrows();
			
			if ($num > 0) {echo "<table cellpadding=\"5\" class=\"colourtable\" style=\"margin: 0 auto;text-align:left\">
			<tr ><td><b>$phrase[311]</b></td><td><b>$phrase[141]</b></td><td><b>$phrase[122]</b></td><td><b>$phrase[142]</b></td><td><b>$phrase[138]/$phrase[1014]</b></td><td></td></tr>";}
			
			while ($row = $DB->get())
		
			{
		$event_name = formattext($row["event_name"]);
		$eventno = $row["eventno"];
		$status = $row["status"];
		$event_time = $row["event_start"];
		$bookday = strftime("%x", $row["time_booking"]);
		$booktime = date("g:i a", $row["time_booking"]);
		$displayday = strftime("%A %x", $row["event_start"]);
		$paid = $row["paid"];
		$cat_cost = $row["cat_cost"];
		$firstname = $row["firstname"];
		$lastname = $row["lastname"];
		//$displaytime = date("g:i a", $row["event_time"]);
		
		echo "<tr";
			if ($status == 2) {echo " class=\"accent\"";}
							if ($status == 3) {echo " class=\"greybackground\"";}
		echo "><td><a href=\"calendar.php?m=$m&amp;event=book&amp;event_id=$eventno&amp;t=$event_time\">$event_name</a></td>
		<td>$firstname $lastname</td>
		<td>$displayday</td><td>$bookday</td><td>";
		
		if ($cat_cost == 1 && $paid == 1) {echo $phrase[138];}
		elseif ($cat_cost == 1 && $paid == 0) {echo $phrase[1014];}
		else {echo $phrase[963];}
		
		echo "</td><td>";
		
		if ($status > 1)
							 {echo "$status_label[$status]";}
		echo "</td></tr>";
			}
		echo "</table>";
			
	}
			
		}
		
			
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "book")
		{
		
			$sql = "SELECT cat_name,cat_email,cat_colour,cat_cost,cat_notes, cat_age, cat_staffname, cat_address,cat_web, cat_receipt,cat_multiple,cat_confirmation, cat_print, cat_comments, cal_cat.cat_notes, cat_takesbookings, cat_waitinglist,  maxbookings, event_name, event_cost, event_location,event_description, cat_trainer, trainer, event_staffnotes, cancelled , event_start  FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and cal_events.event_id = \"$event_id\"";
		$DB->query($sql,"calendar.php");
		$row = $DB->get();
		
		$cat_name = formattext($row["cat_name"]);
		$cat_colour = $row["cat_colour"];
		$event_name = formattext($row["event_name"]);
		$event_location = formattext($row["event_location"]);
		$event_cost = formattext($row["event_cost"]);
		$event_description = formattext($row["event_description"]);
		$cat_cost = $row["cat_cost"];
		$cat_notes = $row["cat_notes"];
		$event_staffnotes = formattext($row["event_staffnotes"]);
		
		$trainer = formattext($row["trainer"]);
		$displayday = strftime("%A %x", $row["event_start"]);
		$displaytime = date("g:i a", $row["event_start"]);
		
		$maxbookings = $row["maxbookings"];
		$cat_takesbookings = $row["cat_takesbookings"];
		$cat_age = $row["cat_age"];
		$cat_address = $row["cat_address"];
		$cat_multiple = $row["cat_multiple"];
		$cat_comments = $row["cat_comments"];
		$cat_staffname = $row["cat_staffname"];
		$cat_confirmation = $row["cat_confirmation"];
		$cat_waitinglist = $row["cat_waitinglist"];
		$cat_print = $row["cat_print"];
		$cat_trainer = $row["cat_trainer"];
		$cat_web = $row["cat_web"];
		$cat_email = $row["cat_email"];
		$cancelled = $row["cancelled"];
		$cat_receipt = $row["cat_receipt"];
		
		//print_r($row);
		
		if ($event_cost == "" || $event_cost == "0") {$event_cost = $phrase[963];} else {$event_cost =  $moneysymbol . $event_cost;}
		
		
		$now = time();
		
		
		echo "<h1 >$modname</h1>
		<a href=\"calendar.php?m=$m&amp;t=$t\">$phrase[118]</a> | <a href=\"calendar.php?m=$m&amp;event=upcoming\">$phrase[980]</a> | <a href=\"calendar.php?m=$m&amp;event=all\">$phrase[864]</a> | <a href=\"calendar.php?m=$m&amp;event=search\">$phrase[863]</a>";
if ($EVENTAUTHENTICATION == "local" || $EVENTAUTHENTICATION == "web" || $EVENTAUTHENTICATION == "ldap") {echo " | <a href=\"calendar.php?m=$m&amp;event=lookup\">$phrase[731]</a>";}
		
		echo " | <a href=\"calendar.php?m=$m&amp;event=book&amp;event_id=$event_id&amp;t=$t\">$phrase[119]</a>
		
		
		<h2 style=\"color:$cat_colour;\">$cat_name</h2>
		";
		if ($cancelled == 1)
			{
			echo "

			<span style=\"color:#ff3300;font-size:2em;\">$phrase[120]</span>

";
			}
		
			
			
		
			
			
			echo "
			<h2>$event_name</h2>";
			
			
		$sql = "select image_id from images where page = \"$event_id\" and modtype = 'c' "; 
		$DB->query($sql,"calendar.php");	
		$num = $DB->countrows();
		
		if ($num != 0)
		{  
		$row = $DB->get();
		$image_id = $row["image_id"];
		$display_picture = "yes";
		}
		
		else {$display_picture = "no";}

			if ($display_picture == "no") {echo "<div style=\"width:30%;float:left;margin-top:1em;\"><div> </div> </div>";} 
    else {echo "<div style=\"width:50%;float:left;\"><div style=\"text-align:right;padding:1em\">
    <img src=\"../main/image.php?m=$m&image_id=$image_id&module=cal\" style=\"vertical-align:middle\"></div> </div>";}
    
    
  
	echo " <div style=\"width:45%;float:left;margin-top:1em;\"><div style=\"text-align:left;padding:2em;border:solid 1px black;\">
    $event_description<br><br>
    
			<b>$phrase[121]</b> <br>";
	
			
					
					if (key_exists($event_location,$branches)) {echo "$branches[$event_location]<br>";}
					else {
					
						if ($event_location != "") {echo "$event_location<br> ";}
						}	
	echo "<br>
<b>$phrase[122]</b><br> $displaytime $displayday<br><br>
		
	";
		if ($cat_notes && $event_staffnotes != "")
			{
			echo "<b>$phrase[124]</b><br>$event_staffnotes<br><br>";
			}
		
		if ($cat_trainer == 1)
			{
			echo "<b>$phrase[125]</b><br>$trainer<br><br>";
			}	
		if ($cat_takesbookings > 0)
			{
				
				
					
				if ($cat_cost)
					{
					echo "<b>$phrase[126]</b><br>$event_cost<br><br>";
					}
				echo "<b>$phrase[127]</b><br>";
				if ($maxbookings == 0)
					{
					echo " $phrase[128]";
					}
				else
					{
					echo " <b>$maxbookings </b>";
					}	
					
					
		echo "<br><br>";
			
				
			
					//count how many booked and waiting people there are
			$sql = "SELECT count(*) as count FROM cal_bookings  where eventno = \"$event_id\" and status < 3";
			$DB->query($sql,"calendar.php");
			$row = $DB->get();
			$count = $row["count"]
			;
			//count is number of existing bookings for this event
		
				
			if ($cancelled != 1 )
			{
			if ($maxbookings == 0 && $access->thispage > 1 )
					{
				//add booking
				echo "<a href=\"calendar.php?m=$m&amp;event_id=$event_id&amp;event=addbooking\" style=\"font-size:1.5em\">$phrase[451]</a><br>";
				}
			elseif ($cat_waitinglist == 1 && $access->thispage > 1)
			{
				if ($count >= $maxbookings)
				{
					//event full 
					//add to waiting list
					echo "<div style=\"color:#ff6666;font-size:large;\">$phrase[147]</div><br>
					<a href=\"calendar.php?m=$m&amp;event_id=$event_id&amp;event=addbooking\" style=\"font-size:1.5em\">$phrase[150]</a></br>";
				}
				else {
					//add booking
					echo "<a href=\"calendar.php?m=$m&amp;event_id=$event_id&amp;event=addbooking\" style=\"font-size:1.5em\">$phrase[451]</a></br>";
				}
			}
			else {
				if (($maxbookings == 0 && $access->thispage > 1) || ($count < $maxbookings && $access->thispage > 1))
				{
					//add booking
					echo "<a href=\"calendar.php?m=$m&amp;event_id=$event_id&amp;event=addbooking\" style=\"font-size:1.5em\">$phrase[451]</a></br>";  //add booking
				}
				elseif ($count >= $maxbookings && $maxbookings != 0) {
					//event full
					echo "<br><div style=\"color:#ff6666;font-size:large;\">$phrase[147]</div>";  //fully booked
				}
			}
					
			}		
			
			echo "</div></div>";
			
			
					//CURRENT BOOKINGS
					
					$insert = "bookingno";
					
					if (isset($_REQUEST["orderby"]))
					{
					if ($_REQUEST["orderby"] == "name")
					{$insert = "lastname";}
					elseif ($_REQUEST["orderby"] == "bookedby")
					{$insert = "staffname";}
					elseif ($_REQUEST["orderby"] == "date")
					{$insert = "time_booking";}
					}
					
									
									$sql = "SELECT * from cal_bookings where eventno = '$event_id' order by status, $insert";
//echo $sql;
			$DB->query($sql,"calendar.php");
			$num = $DB->countrows();
			
			if ($num > 0)
						{	
							
							
						echo "<script type=\"text/javascript\">
 						function pop_window(url) { var calpop = window.open(url,'','status,resizable,scrollbars,width=350,height=400');
 						if (window.focus) {calpop.focus()}
						 }  </script>";
				
						$counter = 0;
					
								echo "<br> <br><h2  style=\"clear:both;padding-top:3em\">$phrase[140]</h2>
							
								<table  class=\"colourtable\" cellpadding=\"5\"   style=\"margin:0 auto;text-align:left\">
								<tr style=\"text-align:center\"><td></td>
								<td><a href=\"calendar.php?m=$m&event=book&event_id=$event_id&t=$t&orderby=name\">$phrase[141]</a></td>
								<td>$phrase[132]&nbsp;</td>";
								
								if ($cat_email == 1)
								{echo "<td>$phrase[276]</td>";} //Email
								if ($cat_cost == 1)
								{echo "<td>$phrase[138]</td>";}//paid
								
								if ($cat_receipt == 1)
								{echo "<td>$phrase[1070]</td>";}//paid
								
								
								if ($cat_age == 1)
								{echo "<td>$phrase[133]&nbsp;</td>";}
								if ($cat_address == 1)
								{echo "<td>$phrase[134]</td>";}
								if ($cat_comments == 1)
								{echo "<td>$phrase[135]</td>";}
								echo "<td><a href=\"calendar.php?m=$m&event=book&event_id=$event_id&t=$t&orderby=date\">$phrase[142]</a></td>";
								if ($cat_staffname == 1 || $cat_web == 2)
								{echo "<td><a href=\"calendar.php?m=$m&event=book&event_id=$event_id&t=$t&orderby=bookedby\">$phrase[136]</a></td>";}
								
								if ($cat_confirmation == 1)
								{echo "<td>$phrase[139]</td>";}
								if ($cat_print > 0)
								{echo "<td>$phrase[143]</td>";}
								
								
								
								
									if ($access->thispage > 1)	{echo "<td></td>";}
								if ($_SESSION['userid'] == 1)  	{echo "<td>$phrase[3]</td>";}
								
								echo "</tr>"; 


								
								while ($row = $DB->get()) 
					{
					
					$bookingno = $row["bookingno"];
					$firstname = formattext($row["firstname"]);
					$lastname = formattext($row["lastname"]);
					$telephone = formattext($row["telephone"]);
					$time_booking = $row["time_booking"];
					$bookday = strftime("%x", $row["time_booking"]);
					$booktime = date("g:i a", $row["time_booking"]);
					$updateday = strftime("%x", $row["time_updated"]);
					$updatetime = date("g:i a", $row["time_updated"]);
					$email = formattext($row["email"]);
					$time_updated = $row["time_updated"];
					$paid = $row["paid"];
					$age = $row["age"];
					$comments = formattext($row["comments"]);
					$address = formattext($row["address"]);
					$confirm = formattext($row["confirmation"]);
					$staff = formattext($row["staffname"]);
					$status = $row["status"];
					$ip = $row["ip"];
					$cardnumber = $row["cardnumber"];
					$webid = $row["webid"];
					$receipt = $row["receipt"];
					
					$username = $row["username"];
					
													
							$counter++;	
							
							
							if (!($cat_waitinglist == 0 && $status == 2))
							{
							//print row	
							echo "<tr";
							if ($status == 2) {echo " class=\"accent\"";}
							if ($status == 3) {echo " class=\"greybackground\"";}
							echo "><td>$counter </td><td >$lastname, $firstname ";
							
							if ($cardnumber != "")
							 {echo "<br>$cardnumber";}
							 
							if ($status > 1)
							 {echo "<br><b>$status_label[$status]</b>";}
							
							echo "</td><td>$telephone</td>";
							if ($cat_email == 1)
								{echo "<td>$email</td>";}
							
							
							if ($cat_cost == 1)
								{
									echo "<td>";
								if ($paid == 1)
									{
									echo "$phrase[12]";
									}
								else
									{
									echo "$phrase[13]";
									}
								echo "</td>";	
								}
								
							if ($cat_receipt == 1)
								{	
									echo "<td>";
								if ($receipt != "" && $receipt != '0')
								{
									echo "$receipt";
								}
									echo "</td>";		
								}
								
							if ($cat_age == 1 )
								{
								echo "<td>";
								if ($age <> 0) { echo "$age";}
								echo "</td>";
								}	
									
								
							if ($cat_address == 1)
								{
								echo "<td> $address</td>";
								}
							if ($cat_comments == 1)
								{
								echo "<td>$comments</td>";
								}
							
							echo "<td align=\"left\">Booked<br>
							<span style=\"font-size:0.85em\">$booktime $bookday</span> ";
							if ($time_booking <> $time_updated)
							{ echo "<br>Last updated<br>
								<span style=\"font-size:0.85em\">$updatetime $updateday</span> ";
							}								
							
							echo "</td>";
							
							if ($cat_staffname == 1 || $cat_web == 2)
								{
								echo "<td>$staff";
								if ($staff = "www" && ($webid != 0 && $webid != "")) { echo "<br>#$webid";}
								echo "</td>";
								}
							if ($cat_confirmation == 1)
								{
								if ($confirm == 1)
									{
									echo "<td>$phrase[12]</td>";
									}
								else
									{
									echo "<td></td>";
									}
								}
							if ($cat_print == 1 )
								{
								echo "<td><a href=\"calprint.php?m=$m&amp;bookingno=$bookingno\">$phrase[256]</a></td>";
								
								}
							elseif ($cat_print == 2 )
								{
								echo "<td><a href=\"javascript:pop_window('calreceipt.php?m=$m&amp;bookingno=$bookingno')\">$phrase[255]</a></td>";
								}
							elseif ($cat_print == 3 )
								{
								echo "<td><a href=\"calprint.php?m=$m&amp;bookingno=$bookingno\" title=\"$phrase[256]\">$phrase[256]</a><br><br>
								<a href=\"javascript:pop_window('calreceipt.php?m=$m&amp;bookingno=$bookingno')\" title=\"$phrase[255]\">$phrase[255]</a></td>	";
								}
							
								
						
								
							if ($access->thispage > 1)	{
							echo "<td><a href=\"calendar.php?m=$m&amp;t=$t&amp;event=edit&amp;bookingno=$bookingno&amp;event_id=$event_id\">$phrase[26]</a></td>";
							}
							
							
								if ($_SESSION['userid'] == 1)
						{
						echo "<td><span style=\"color:#999999\">$username</span><br><span style=\"color:#999999\"> $ip</span></td>	";
						}
							echo "</tr>";	
							
							}
							
							}
							echo "</table>";
							
							
					//ends isset test
					}
					
			
			
			}
			
		}
	
		
	elseif (isset($_GET["event"]) && $_GET["event"] == "edit" && $access->thispage > 1)
		{	
		
			
			
					//count how many booked 
			$sql = "SELECT count(*) as count FROM cal_bookings  where eventno = '$event_id' and status = '1'";
			$DB->query($sql,"calendar.php");
			$row = $DB->get();
			$count = $row["count"];
			
			
			
		$sql = "SELECT cat_name, cat_colour,cat_cost, cat_age,cat_email, cat_staffname, cat_address, cat_multiple,cat_confirmation, cat_comments, cat_waitinglist,cal_cat.cat_notes
		, cat_takesbookings, maxbookings, event_name, event_cost,age_range, event_location,event_description, event_staffnotes, cancelled ,
		event_start, cat_receipt FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and cal_events.event_id = '$event_id'";
		$DB->query($sql,"calendar.php");
		$row = $DB->get();
		
		$cat_name = formattext($row["cat_name"]);
		$cat_colour = $row["cat_colour"];
		$event_name = formattext($row["event_name"]);
		$event_location = formattext($row["event_location"]);
		$event_cost = formattext($row["event_cost"]);
		$event_description = $row["event_description"];
		$event_description = formattext($event_description);
		$agerange = $row["age_range"];
		$event_staffnotes = $row["event_staffnotes"];
		$event_staffnotes = formattext($event_staffnotes);
		$cancelled = $row["cancelled"];
		$minute = date("i",$row["event_start"]);
		$hour = date("H",$row["event_start"]);
		$day = date("d",$row["event_start"]);
		$displayday = date("g:i a j",$row["event_start"]) . " " . strftime("%b %Y", $row["event_start"]);
		$maxbookings = $row["maxbookings"];
		
		$cat_takesbookings = $row["cat_takesbookings"];
		$cat_age = $row["cat_age"];
		$cat_address = $row["cat_address"];
		$cat_multiple = $row["cat_multiple"];
		$cat_comments = $row["cat_comments"];
		$cat_staffname = $row["cat_staffname"];
		$cat_confirmation = $row["cat_confirmation"];
		$cat_cost = $row["cat_cost"];
		$cat_notes = $row["cat_notes"];
		$cat_waitinglist = $row["cat_waitinglist"];
		$cat_receipt = $row["cat_receipt"];
                $cat_email = $row["cat_email"];
		
		
		
		$sql = "SELECT * FROM cal_bookings where bookingno = '$bookingno'";
		$DB->query($sql,"calendar.php");
		$row = $DB->get();
		
		$firstname =$row["firstname"];
		
		$lastname = $row["lastname"];
		$telephone = $row["telephone"];
		$paid = $row["paid"];
		$status = $row["status"];
		$address = $row["address"];
		$comments = $row["comments"];
		$age = $row["age"];
		$confirmation = $row["confirmation"];
		$staffname = formattext($row["staffname"]);
		$receipt = $row["receipt"];
		$bookinggroup = $row["webid"];
                $email = $row["email"];
		
		
		
			$total = 0;
	if ($bookinggroup > 0)
	{
	$sql = "SELECT count( * ) AS total FROM cal_bookings WHERE webid = '$bookinggroup'";
		$DB->query($sql,"resourcebooking.php");
		$row = $DB->get();
		$total = $row["total"];
	}
		
		
		
echo "

<h1 >$modname</h1>
<a href=\"calendar.php?m=$m&amp;t=$t\">$phrase[118]</a> | <a href=\"calendar.php?m=$m&amp;t=$t&amp;event_id=$event_id&amp;event=book\">$phrase[699]</a>
<h2 style=\"color:$cat_colour;\">$cat_name</h2><br><b>$event_name</b>
<FORM method=\"POST\" action=\"calendar.php\" style=\"margin-left:auto;margin-right:auto;width:80%\"><fieldset><legend>$phrase[153]</legend>
												<table cellpadding=\"5\" border=\"0\" align=\"center\" style=\"margin:0 auto;text-align:left\">
												
												
				
										<tr><td style=\"text-align:right\" width=\"50%\"><b>$phrase[121]</b></td><td>";

					
					if (key_exists($event_location,$branches)) {echo "$branches[$event_location]<br>";}
					else {
					
						if ($event_location != "") {echo "$event_location<br> ";}
						}	
 echo "</td></tr>
		</td></tr><tr><td style=\"text-align:right\" valign=\"top\"><b>$phrase[185]</b></td><td>$displayday <br>
		</td></tr>
										<tr><td style=\"text-align:right\" ><b>$phrase[130]</b></td><td>
				
										<INPUT type=\"text\" name=\"firstname\"  value=\"$firstname\" size=\"30\" maxlength=\"50\" class=\"single\"></td></tr>
										<tr><td style=\"text-align:right\"><b>$phrase[131]</b></td><td>
				
										<INPUT type=\"text\" name=\"lastname\"  value=\"$lastname\" size=\"30\" maxlength=\"50\" class=\"single\"></td></tr>
										<tr><td style=\"text-align:right\"><b>$phrase[132]</b></td><td>
				
										<INPUT type=\"text\" name=\"telephone\"  value=\"$telephone\" size=\"20\" maxlength=\"20\"  class=\"single\"></td></tr>
										";
											
										if ($cat_email == 1)
                                                                                {
                                                                                    
                                                                                 echo "<tr><td style=\"text-align:right\"><b>$phrase[259]</b></td><td>
				
										<INPUT type=\"text\" name=\"email\"  value=\"$email\" size=\"30\"   class=\"single\"></td></tr>   
                                                                                ";    
                                                                                    
                                                                                }
												
											if ($cat_age == 1)
											{
											
											$pos = strpos($agerange,":");

											$minage = substr($agerange,0,$pos);
											$maxage = trim(substr($agerange,$pos + 1));	
											
											//check input values are sensible
											if (!(($maxage > $minage) && ($minage > 0 && $minage < 120) && ($maxage > 1 && $maxage < 120))) 
											{$minage = 0; $maxage = 120;}	
												
												
												
											echo "
											<tr><td style=\"text-align:right\" ><b>$phrase[133]</b></td><td><select class=\"age\" name=\"age\" >";
											$c = $minage;
											while ($c <= $maxage)
											{
											echo "<option value=\"$c\"";
											if ($age == $c) {echo " selected";}
											echo ">$c</option>
";	
											$c++;
											
											}
											
											echo "</select></td></tr>";
											}
											
										
											
																		
										if ($cat_address == 1)
											{
											echo "<tr><td style=\"text-align:right\" valign=\"top\"><b>$phrase[134]</b></td><td><textarea cols=\"40\" rows=\"5\" name=\"address\">$address</textarea></td></tr>";
											}

										if ($cat_comments == 1)
											{
											echo "<tr><td style=\"text-align:right\" valign=\"top\"><b>$phrase[135]</b></td><td><textarea cols=\"40\" rows=\"5\" name=\"comments\">$comments</textarea></td></tr>	";
											}

											
										if ($cat_staffname == 1)
											{
											echo "<tr><td style=\"text-align:right\"><b>$phrase[136]</b></td><td><input type=text size=\"40\" maxlength=\"60\" name=\"staffname\" value=\"$staffname\"></td></tr>";
											
											}
										
		
										if ($cat_cost == 1 )
											{
											echo "<tr><td style=\"text-align:right\"><b>$phrase[138]</b></td><td><select name=\"paid\">";
											if ($paid == 0)
												{
												echo "<option value=\"0\">$phrase[13]</option>
												<option value=\"1\">$phrase[12]</option>";
												}
											else	
												{
												echo "
												<option value=\"1\">$phrase[12]</option><option value=\"0\">$phrase[13]</option>";
												}
											
											echo "</select></td></tr>";
											}
											
											
										if ($cat_receipt == 1 )
											{
											echo "<tr><td style=\"text-align:right\"><b>$phrase[1070]</b></td><td>
											<input type=\"text\" name=\"receipt\" value=\"$receipt\" maxlength=\"20\" sixe=\"10\"></td></tr>";
											}
											
											
										if ($cat_confirmation == 1 )
											{
											echo "<tr><td style=\"text-align:right\"><b>$phrase[139]</b></td><td><select name=\"confirmation\">";
											if ($confirmation == 0)
												{
												echo "<option value=\"0\">$phrase[13]</option>
												<option value=\"1\">$phrase[12]</option>";
												}
												else
													{
													echo "
												<option value=\"1\">$phrase[12]</option><option value=\"0\">$phrase[13]</option>";
													}
											echo "</select></td></tr> ";
											}
										echo "<tr><td style=\"text-align:right\"><b>$phrase[634]</b></td><td><select name=\"status\"> ";
										
										if (($status == 1) || (($maxbookings == 0) || ($count  < $maxbookings)))
										{
										echo "<option value=\"1\"";
										if ($status == 1) { echo " selected";} 
										echo ">$status_label[1]</option>";
										}
										
										
										if ($cat_waitinglist == 1 && $maxbookings != 0)
										{ echo "<option value=\"2\"";
										if ($status == 2) { echo " selected";} 
										echo ">$status_label[2]</option>";}
										
										echo "<option value=\"3\"";   
										if ($status == 3) { echo " selected";} 
										echo ">$status_label[3]</option></select></td></tr>";
										
										
											if ($total > 1)
	{
			echo "<tr><td class=\"label\" valign=\"top\"><b>$phrase[16]</b></td><td >
			<select name=\"bookinggroup\" id=\"bookinggroup\" onchange=\"changestatus()\">
		<option value=\"0\" checked> $phrase[768]</option>
		<option value=\"$bookinggroup\"> $phrase[767]  ($total)	</option>	
			</select>					   
</td></tr>";
	}
										
										
										
										
										echo "<tr><td></td><td>";	
																			
										if ($cat_age <> 1)
										{echo "<INPUT type=\"hidden\" name=\"age\"  value=\"$age\" >";}
										if ($cat_address<> 1)
										{echo "<INPUT type=\"hidden\" name=\"address\" value=\"$address\">";}
										if ($cat_comments<> 1)
										{echo "<INPUT type=\"hidden\" name=\"comments\" value=\"$comments\">";}
										if ($cat_staffname<> 1)
										{echo "<INPUT type=\"hidden\" name=\"staffname\" value=\"$staffname\">";}
										if ($cat_confirmation <> 1 )
										echo "<INPUT type=\"hidden\" name=\"confirmation\" value=\"0\">";
										if ($cat_cost <> 1 )
										echo "<INPUT type=\"hidden\" name=\"paid\" value=\"0\">";
										if ($cat_receipt <> 1 )
										echo "<INPUT type=\"hidden\" name=\"receipt\" value=\"0\">";
                                                                                
                                                                                if ($cat_email != 1)
                                                                                {
                                                                                
                                                                                echo "<INPUT type=\"hidden\" name=\"email\" value=\"\">";
                                                                                }
										?>
									
										
										<INPUT type="hidden" name="event_id" value="<?php echo $event_id?>">
										<INPUT type="hidden" name="t" value="<?php echo $t?>">
										<INPUT type="hidden" name="m" value="<?php echo $m?>">
										<INPUT type="hidden" name="maxbookings" value="<?php echo $maxbookings?>">
										<INPUT type="hidden" name="oldstatus" value="<?php echo $status?>">
										<INPUT type="hidden" name="bookingno" value="<?php echo $bookingno?>">
										<INPUT type="hidden" name="update" value="update">
										<INPUT type="hidden" name="event" value="book">
										<INPUT type="submit" name="submit" value="<?php echo $phrase[28]?>">
										</td></tr></table></fieldset></form>
										
										 <script type="text/javascript">
										 var value;
										 
										 function changestatus()
										 {
										 var menu = document.getElementById('bookinggroup');	
										 value = menu.options[menu.selectedIndex].value	
										 
										 var inputs = document.body.getElementsByTagName("input")
										  for( var i = 0; i < inputs.length; i++ ) 
 											{ 
 											if( inputs[i].className == 'single' ) 
 												{
 												if (value == '0')
 												{
 												inputs[i].readOnly = false;	
 												inputs[i].style.background = '#ffffff'
 												}
 												else
 												{
 												inputs[i].readOnly = true;
 												inputs[i].style.background = '#EFEFEF'		
 												}
 												}
 											}
										 
										 	
										 }
										 
										 
										</script>
										<?php

		
		}
		

		 elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "patron")
       {
     
       	
     echo "
	 <h1>$modname</h1>
	<a href=\"calendar.php?m=$m&amp;t=$t\">$phrase[118]</a> <br>";

     

     if (isset($_REQUEST["barcode"]))
     {
     	//$barcode = trim($_REQUEST["barcode"]);
     	$barcode =  $DB->escape(str_replace(" ", "", $_REQUEST["barcode"]));
     $sql = "select * from pc_users where barcode = '$barcode'";	
     $DB->query($sql,"calendar.php");
	$num = $DB->countrows();
	
	if ($num == 1)
	{
		
		
		$barcode =  $DB->escape(str_replace(" ", "", $_REQUEST["barcode"]));
		$urlbarcode = urlencode($barcode);
		
		$row = $DB->get();
      
	
      $change = $row["change"];
		
		
		echo "
		 
			
			<br><form action=\"calendar.php\" method=\"post\" style=\"width:80%;margin-left:auto;margin-right:auto\" onsubmit=\"return check()\"> 
      	<fieldset >
						   <legend>$phrase[22]</legend>
						   <table  style=\"margin:0 auto;text-align:left\" cellpadding=\"5\">
						  <tr><td align=\"right\"><b>$phrase[460]</b></td><td><b>$_REQUEST[barcode]</b></td></tr>
						    <tr><td align=\"right\"><b>$phrase[32]</b></td><td> <input type=\"password\" name=\"password\" id=\"password\"></td></tr>
						    
							 <tr><td align=\"right\"><b>$phrase[17]</b></td><td> <input type=\"password\" name=\"password2\" id=\"password2\"></td></tr>
						    <tr><td align=\"right\"><b>$phrase[739]</b></td><td><input type=\"radio\" name=\"change\" value=\"0\"";
							if ($change == 0) {echo " checked";}
							echo "> $phrase[13] <input type=\"radio\" name=\"change\" value=\"1\"";
								if ($change == 1) {echo " checked";}
							echo "> $phrase[12]</td></tr>
							<tr><td>	</td><td> 
						       <input type=\"hidden\" name=\"m\" value=\"$m\">

						    
						         <input type=\"hidden\" name=\"barcode\" value=\"$_REQUEST[barcode]\">
					     <input type=\"submit\" name=\"updateuser\" value=\"$phrase[22]\"> 	<a href=\"calendar.php?m=$m&amp;barcode=$urlbarcode&amp;event=patrons&amp;deleteuser=yes\" style=\"margin-left:5em\">$phrase[709]</a></td></tr>
						   </table>
						   </fieldset>
						   </form>
						   
						   <script type=\"text/javascript\">
			
			document.getElementById('password').focus();
			
			function check()
			{
		
			var password1 = document.getElementById('password').value;
			var password2 = document.getElementById('password2').value;
			 if (password1 != password2)
				{
				alert(\"$phrase[706]\");
				return false;
				}
				
			if (password1.length < 1)
				{
				alert(\"$phrase[707]\");
				return false;
				}
			}
			</script>";
						}
	else {
			echo "
			
						<p style=\"font-size:2em;color:red\">$phrase[705]</p>
						<form action=\"calendar.php\" method=\"post\" onsubmit=\"return check()\" style=\"width:60%;margin-left:auto;margin-right:auto\">
      	<fieldset >
						   <legend>$phrase[704]</legend>
						   <table style=\"margin:0 auto;text-align:left\" cellpadding=\"5\">
						   <tr><td align=\"right\"><b>$phrase[460]</b></td><td><input type=\"text\" name=\"barcode\" value=\"$_REQUEST[barcode]\"></td></tr>
						    <tr><td align=\"right\"><b>$phrase[4]</b></td><td> <input type=\"password\" name=\"password\" id=\"password\"></td></tr>
						    <tr><td align=\"right\"><b>$phrase[17]</b></td><td>  <input type=\"password\" name=\"password2\" id=\"password2\"></td></tr>
						    <tr><td align=\"right\"><b>$phrase[739]</b></td><td><input type=\"radio\" name=\"change\" value=\"0\" checked> $phrase[13] 
							<input type=\"radio\" name=\"change\" value=\"1\"> $phrase[12]</td></tr>
							<tr><td></td><td>
						       <input type=\"hidden\" name=\"m\" value=\"$m\">
						          
						       <input type=\"hidden\" name=\"update\" value=\"adduser\">
						   <input type=\"submit\" name=\"submit\" value=\"$phrase[704]\" ></td></tr>
						   </table>
						   </fieldset>
						   </form>
						   
						   <script type=\"text/javascript\">
			document.getElementById('password').focus();
			
			function check()
			{
		
			var password1 = document.getElementById('password').value;
			var password2 = document.getElementById('password2').value;
			 if (password1 != password2)
				{
				alert(\"$phrase[706]\");
				return false;
				}
			if (password1.length < 1)
				{
				alert(\"$phrase[707]\");
				return false;
				}	
			}
			</script>";
	}
			
     	
     }
      else 
      {
      	
      	echo "
      	<script type=\"text/javascript\">
      	
      			
								   								         
				function setfocus() 
				 {
				 document.getElementById('barcode').focus(); 
				 }

				 window.onload = setfocus;
			
			
			function check()
			{
		
			var barcode = document.getElementById('barcode').value;
			
			 
			if (barcode.length < 1)
				{
				alert(\"$phrase[708]\");
				return false;
				}	
			}
			</script>
			<h2>$phrase[733]</h2>
			<form action=\"calendar.php\" method=\"post\" style=\"width:80%;margin: 3em auto\" onsubmit=\"return check()\">
      <b>$phrase[460]</b>
						   <input type=\"text\" name=\"barcode\" id=\"barcode\">
						   <input type=\"hidden\" name=\"m\" value=\"$m\">
						    <input type=\"hidden\" name=\"event\" value=\"patron\">
						    <input type=\"submit\" name=\"search\" value=\"$phrase[282]\">
						 
						   </form>";
						 
      	
      	
      }
       }
       
      elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "upcoming")
{
	
	
	echo "<h1 >$modname</h1><p style=\"clear:both\">
<a href=\"calendar.php?m=$m&amp;t=$t\">$phrase[118]</a> | <a href=\"calendar.php?m=$m&amp;event=all\">$phrase[864]</a> 
| <a href=\"calendar.php?m=$m&amp;event=search\">$phrase[863]</a>
</p>";
	
	
	

	if (isset($_REQUEST["daysahead"])) {$UPCOMING_EVENTS_DAYS_AHEAD = $_REQUEST["daysahead"];}
	

	
	if (!isset($UPCOMING_EVENTS_DAYS_AHEAD)) {$UPCOMING_EVENTS_DAYS_AHEAD= 30;}
	$now = time();
	$cutoff = $now + ($UPCOMING_EVENTS_DAYS_AHEAD * 24 *60 *60);
		//get list of this months events and put in array
	
	$insert = "";
	if (isset($_REQUEST["location"]) && $_REQUEST["location"] != "0") 
	{
	$location = $DB->escape($_REQUEST["location"]);	
		
		$insert .= " and event_location = '$location'";
	}
	
	
	if (isset($_REQUEST["keywords"]) ) 
	{
	$keywords = $DB->escape($_REQUEST["keywords"]);	
		
			if ($DB->type == "mysql")
			{
		$insert .= " and MATCH (event_name,event_description,event_location,tags) AGAINST (\"$keywords\" IN BOOLEAN MODE) ";
			}
                        else
				
				
			{	
			$keywords = trim($keywords);
       			
       			$words = explode(" ",$keywords);
       			
       			$string = "";
       			
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
				$insert .= " and (event_name like '%$value%' or event_description like '%$value%' or event_location like '%$value%' or tags like '%$value%') ";	
				$counter++;
				}	
				if ($keywords == "") {$insert = " and 1 = 2";}
			
			}
	}
	
	
	
	$array_maxbookings = array();

				//$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  
				//where cal_cat.cat_id = cal_events.event_catid and month(FROM_UNIXTIME(event_start)) = '$month' and year(FROM_UNIXTIME(event_start)) = '$year' and template = '0' and cal_cat.cat_web > '0' and cancelled = '0' order by event_start";	
				
		
				if ($DB->type == "mysql")
			{		
			
				$sql = "SELECT event_id, event_cost,event_description, event_name,maxbookings, event_location,cancelled,cat_waitinglist, cat_colour, cat_takesbookings, event_start, m FROM cal_events, cal_cat ,cal_bridge where cal_bridge.m = '$m' and cal_cat.cat_id = cal_bridge.cat  and cal_cat.cat_id = cal_events.event_catid and event_start > '$now' and event_start < '$cutoff' and template = '0' $insert  $locationinsert order by event_start";	
		//echo $sql;
			}
		else
			{
			
				$sql = "SELECT event_id, event_cost, event_description, event_name,maxbookings, event_location,cancelled, cat_waitinglist, cat_colour, cat_takesbookings, event_start, m FROM cal_events, cal_cat, cal_bridge  where cal_bridge.m = '$m' and cal_cat.cat_id = cal_bridge.cat  and cal_cat.cat_id = cal_events.event_catid and event_start  > '$now' and event_start < '$cutoff'  and template = '0' $insert $locationinsert order by event_start";	
			
			}		
		//echo $sql;		
				
		$DB->query($sql,"calendar.php");
		
		
			while ($row = $DB->get()) 
					{
					$array_id[] =$row["event_id"];
					$array_m[] =$row["m"];
					$array_name[] =$row["event_name"];
					$array_location[] =$row["event_location"];
					$array_cost[] =$row["event_cost"];
					$array_description[] =formattext($row["event_description"]);
					$timestring = strftime("%A %x", $row["event_start"]);	
					
					//echo "string is $timestring <br>";
					 $array_day[] = $timestring;	
					$array_time[] = date("g:i a", $row["event_start"]);
					$array_cancelled[] =$row["cancelled"];
					$array_colour[] =$row["cat_colour"];
					$array_maxbookings[] =$row["maxbookings"];
					$array_takesbookings[] =$row["cat_takesbookings"];
					$array_cat_waitinglist[] =$row["cat_waitinglist"];
					}

					
					
			$count_total = array();		
			$waiting_total = array();		
					
			//get list of events and their total bookings
		
			//$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					//where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					//$where and	month(FROM_UNIXTIME(event_start)) = \"$month\" and year(FROM_UNIXTIME(event_start)) = \"$year\"
					//group by event_id";
					
							if ($DB->type == "mysql")
			{		
						$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					 and event_start > '$now'
					group by event_id";
			}
			
						else
			{		
						$sql = "SELECT count(*) as total, cal_events.event_id as event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					 and	datetime ( event_start , 'unixepoch','localtime' ) > '$now' group by event_id";
			}
			
		
			
			
			$DB->query($sql,"calendar.php");
			while ($row = $DB->get()) 
					{
					$count_total[$row["event_id"]] = $row["total"];
					//$count_total[] = ;
					
					}
   
		
					
					
					//get list of events and their waiting list
							
							if ($DB->type == "mysql")
			{		
						$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 2
					 and event_start > '$now'
					group by event_id";
			}
			
						else
			{		
						$sql = "SELECT count(*) as total, cal_events.event_id as event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 2
					 and	datetime ( event_start , 'unixepoch','localtime' ) > '$now' group by event_id";
			}
			
		
			//echo $sql;
			
			$DB->query($sql,"calendar.php");
			while ($row = $DB->get()) 
					{
					$waiting_total[$row["event_id"]] = $row["total"];
				
					}		
					
					
			//build image query	
			
				
				if (isset($array_id))
				{
					
					$length = count($array_id);
					
					$counter = 1;
					
					$sql = "select image_id, page from images where page in (";
					
					foreach ($array_id as $key => $id) 
					{
					$sql .= $id;
					if ($counter != $length) { $sql .= ",";} 
					$counter++;
					}
					
					$sql .= ") and modtype = 'c' ";
					
					//echo $sql;
						 
		$DB->query($sql,"calendar.php");	
		$num = $DB->countrows();
		
		
		while ($row = $DB->get())
		{
		$image = $row["image_id"];
		$event = $row["page"];
		$images[$event] = $image;
		}
			
					
					
				}
					
	
			
		
		
	
				echo "<div id=\"listmargin\"><div>
				<form action=\"calendar.php\" method=\"get\">
				<h4>$phrase[981]</h4><span>$phrase[121]</span>
				<select name=\"location\">
				<option value=\"0\">$phrase[475]</option>";
			
				$counter = 1;
				$matches = 0;
			
				foreach ($branches as $bno => $bname)
						{
							if (in_array('0',$locations) || in_array($bno,$locations))
							{
			
		
			echo "<option value=\"$bno\"";
			if (isset($_REQUEST["location"]) && $_REQUEST["location"] == $bno) {echo " selected";}
			echo ">$bname</option>";
							}
						}
				
				echo "</select>
				
				<span>$phrase[983]</span>
				<select name=\"daysahead\">";
				$counter = 1;
				
				while ($counter < 366)
				{
					echo "<option value=\"$counter\"";
					if ($counter == $UPCOMING_EVENTS_DAYS_AHEAD) {echo " selected";}
					echo ">$counter</option>";
					$counter++;
				}
				
				
				
				echo "</select>
				<input type=\"hidden\" name=\"m\" value=\"$m\">
				<input type=\"hidden\" name=\"event\" value=\"upcoming\">
				<input type=\"submit\" value=\"$phrase[982]\" name=\"submit\">
				</form>
				
				<form action=\"calendar.php\" method=\"get\">
				<h4>$phrase[863]</h4>
				<input type=\"hidden\" name=\"event\" value=\"upcoming\">
					<input type=\"hidden\" name=\"m\" value=\"$m\">
				<input type=\"text\" name=\"keywords\">
				<input type=\"submit\" value=\"$phrase[982]\" name=\"submit\">
				</form>
				</div></div>
				
				<div class=\"eventlist\"><div>"	;
				if (isset($array_id))
				{	
				foreach ($array_id as $key => $id) 
				
				{
					
					echo "<h4 style=\"color:$array_colour[$key]\">$array_name[$key]</h4><p>";
				
					if (isset($images) && array_key_exists($id,$images))
					{ 
						
						echo "<br><a href=\"calendar.php?m=$m&amp;event=addbooking&amp;event_id=$id\">
					
						<img src=\"../main/image.php?m=$m&amp;image_id=$images[$id]&module=cal\" style=\"vertical-align:middle\"></a>
						<br><br>";
						
						
					}
					echo "
					$array_description[$key]<br>
					<span class=\"bold\">$phrase[962]</span>
					$array_time[$key] 	$array_day[$key]<br>
					<span class=\"bold\">$phrase[806]</span>";
					$_location = $array_location[$key];
					
					if (key_exists($_location,$branches)) {echo "$branches[$_location]<br>";}
					else {
					
						if ($array_location[$key] != "") {echo "$array_location[$key]<br> ";}
						}	
						echo "<span class=\"bold\">$phrase[126]</span>";

if ($array_cost[$key] == 0 || $array_cost[$key] == "") {echo $phrase[963];} else {echo $moneysymbol . $array_cost[$key];}

					
					if ($array_cancelled[$key] == 1)
						{
						echo "<br><span style=\"color:red;\">$phrase[152]</span><br>";
						}
					if ($array_takesbookings[$key] == 1)
						{
					
						//checking if event full
						
						//echo "$array_maxbookings[$key] $count_total[$id]";
							if (isset($count_total) && isset($array_maxbookings) && in_array($id,$count_total)  && in_array($key,$array_maxbookings) && $count_total[$id] >= $array_maxbookings[$key] && $array_maxbookings[$key] <> 0)
								{
								echo "<br><span style=\"color:red;\">$phrase[156]</span>";	
								}
								
								
								
								
						//if ((isset($array_maxbookings) && in_array($key,$array_maxbookings) && $array_maxbookings[$key] == 0) || 
						//(isset($count_total) && isset($array_maxbookings) && in_array($id,$count_total) && in_array($key,$array_maxbookings) && $array_maxbookings[$key] > $count_total[$id]   ))
						
						       

							//{ 
							//echo "<a href=\"calendar.php?m=$m&amp;event=addbooking&amp;event_id=$id\" class=\"bookinglink\">$phrase[859]</a>";
						//	}
						//elseif ($array_cat_waitinglist[$key] == 1) 
						//	{
						//	echo "<a href=\"calendar.php?m=$m&amp;event=addbooking&amp;event_id=$id\">$phrase[150]</a><br>$phrase[964]";
						//	}
			
						
						
						if ( 
						
						($array_cat_waitinglist[$key] == 1 && array_key_exists($key,$array_maxbookings) && $array_maxbookings[$key] != 0) 
						&&
						(
						( array_key_exists($id,$count_total) && array_key_exists($key,$array_maxbookings) && $array_maxbookings[$key] <= $count_total[$id]    )
						||
						(array_key_exists($id,$waiting_total) && $waiting_total > 0)
						)
						)
						
						
						
					
							{ 
								echo "<a href=\"calendar.php?m=$m&amp;event=addbooking&amp;event_id=$id\">$phrase[150]</a><br>$phrase[964]";
							
							}
						else
							{
							echo "<a href=\"calendar.php?m=$m&amp;event=addbooking&amp;event_id=$id\" class=\"bookinglink\">$phrase[859]</a>";
							}
							
						 }
								
								
						
							
						
			
						
						
						//
							echo "</p>
							
							";
						//}
				
					
				
					
				}
				}
	
				echo "</div></div>";
}
 
    
		
		
		
	else
	
	{

	
	
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
   //echo "fd is $fd<br>";
   
   $ld  = mktime(0, 0, 0, $month  , $daysinmonth, $year);
   $ld = date("w",$ld);
   //echo "ld is $ld";
	
	

   
   //get list of usages for this calendar
		$where = "and event_catid IN ("; 
		$typecount = 1;
		$sql = "SELECT cat FROM cal_bridge where m = \"$m\"";
		$DB->query($sql,"calendar.php");
		
		$numrows = $DB->countrows();
		while ($row = $DB->get()) 
					{
					
					$cat =$row["cat"];
					if ($typecount == 1)
						{
						$where .= "'$cat'";
						}
					else
						{
						$where .= ",'$cat'";
						}
					$typecount++;
					
					}
					
		$where .= " )";
		if ($numrows == 0) {$where = "";}
		
               // echo "type is " . $DB->type;
                
                
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
		
		$DB->query($sql,"calendar.php");
		$num = $DB->countrows();
		
		while ($row = $DB->get()) 
		{
			
		$a_holiday[] = $row["holiday"];
	
		$a_name[] = formattext($row["name"]);
		}
		
	
		//$array_maxbookings = array();

		//get list of this months events and put in array
	
	
				if ($DB->type == "mysql")
			{		
			
				$sql = "SELECT event_id, event_name,maxbookings,event_description, event_location,cancelled, cat_colour, cat_finishtime,event_finish, cat_takesbookings, event_start FROM cal_events, cal_cat, cal_bridge  where cal_cat.cat_id = cal_events.event_catid and cal_bridge.m = '$m' and cal_bridge.cat = cal_cat.cat_id and month(FROM_UNIXTIME(event_start)) = \"$month\" and year(FROM_UNIXTIME(event_start)) = \"$year\" and template = \"0\"  $where $locationinsert order by event_start";	
		
			}
	else
			{
			
				$sql = "SELECT event_id, event_name,maxbookings,event_description, event_location,cancelled, cat_colour, cat_finishtime,event_finish,cat_takesbookings, event_start FROM cal_events, cal_cat, cal_bridge  where cal_cat.cat_id = cal_events.event_catid and cal_bridge.m = '$m' and cal_bridge.cat = cal_cat.cat_id  and strftime('%m',datetime ( event_start , 'unixepoch','localtime' )) = \"$month\" and strftime('%Y',datetime ( event_start , 'unixepoch','localtime' )) = \"$year\" and template = \"0\"  $where $locationinsert order by event_start";	
			
			}
				$DB->query($sql,"calendar.php");
		
			//echo "$sql";
			while ($row = $DB->get()) 
					{
					$array_id[] =$row["event_id"];
					$array_name[] =$row["event_name"];
					$array_location[] =$row["event_location"];
					$array_day[] = date("d", $row["event_start"]);			
					$array_time[] = date("g:i a", $row["event_start"]);
					$array_cancelled[] =$row["cancelled"];
					$array_colour[] =$row["cat_colour"];
					$array_maxbookings[] =$row["maxbookings"];
					$array_takesbookings[] =$row["cat_takesbookings"];
					$array_description[] = nl2br($row["event_description"]);
                                          $array_finishtime[] =$row["cat_finishtime"];
                                           $array_endtime[] = date("g:i a", $row["event_finish"]);
					}

			//get list of events and their total bookings
				if ($DB->type == "mysql")
			{		
						$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					$where and	month(FROM_UNIXTIME(event_start)) = \"$month\" and year(FROM_UNIXTIME(event_start)) = \"$year\"
					group by event_id";
			}
			
						else
			{		
						$sql = "SELECT count(*) as total, cal_events.event_id as event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					$where and	strftime('%m',datetime ( event_start , 'unixepoch' ,'localtime')) = \"$month\" and strftime('%Y',datetime ( event_start , 'unixepoch','localtime' )) = \"$year\"
					group by event_id";
			}
			
		
			$DB->query($sql,"calendar.php");
			while ($row = $DB->get()) 
					{
					$count_eventid[] = $row["event_id"];
					$count_total[] = $row["total"];
					
					}
   
					
   echo "<br>
   
   
   	<script type=\"text/javascript\">
addEvent(window, 'load', function () {scroll(\"scrollpoint\")});
</script>


   \n<table  style=\"margin-left:2px\" class=\"colourtable\" id=\"calendar\" cellpadding=\"3\">
 <tr class=\"accent\"><td style=\"text-align:left\" valign=\"bottom\"> <a href=\"calendar.php?m=$m&amp;t=$lastmonth\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" ><span style=\"font-size:150%\">$modname</span><br><span style=\"font-size:150%\"> $display</span><br>
   <br><a href=\"calendar.php?m=$m&amp;event=upcoming\">$phrase[980]</a> | <a href=\"calendar.php?m=$m&amp;event=all\">$phrase[864]</a> | <a href=\"calendar.php?m=$m&amp;event=search\">$phrase[863]</a>";
   if ($EVENTAUTHENTICATION == "local" || $EVENTAUTHENTICATION == "web" || $EVENTAUTHENTICATION == "ldap") {echo " | <a href=\"calendar.php?m=$m&amp;event=lookup\">$phrase[731]</a>";}
		if ($EVENTAUTHENTICATION == "local")
{ echo " | <a href=\"calendar.php?m=$m&amp;event=patron\">$phrase[703]</a> ";}
   echo " | 
   <form style=\"display:inline\" action=\"calendar.php\" method=\"get\">
   <select name=\"month\">";
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

echo "</select><select name=\"year\">";
while ($displayyear <= $endyear)
{
	echo "<option value=\"$displayyear\"";
	if ($displayyear == $year) { echo " selected";}
	echo ">$displayyear</option>";
	$displayyear++;
	
}


echo "</select>
   
       <input type=\"hidden\" name=\"m\" value=\"$m\">
      
     <input type=\"submit\" value=\"View\">
   
   </form></td>
   <td style=\"text-align:right\" valign=\"middle\"><a href=\"calendar.php?m=$m&amp;t=$nextmonth\" class=\"hide\">$phrase[155]</a> </td></tr>
   
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
   
   
   //display month as table cells
   $daycount = 1;
   while ($daycount <= $daysinmonth)
   	{
	$endline = (($counter + $daycount - $cal_offset) % 7);
	$day = str_pad($daycount, 2, "0", STR_PAD_LEFT);
	$dayname  = strftime("%A",mktime(0, 0, 0, $month, $daycount,  $year));
	$t = mktime(0, 0, 0, $month, $day,  $year);
	if ($endline == 1) { echo "<tr>";}
	echo "<td valign=\"top\"";
	if ($thisyear.$thismonth.$thisday == $year.$month.$day)
				{
				echo " id=\"scrollpoint\" class=\"accent\"";
				}
				
				
	
	
	echo "><span style=\"font-size:large\"><b>$daycount</b></span>&nbsp;&nbsp; $dayname<br><br>";
	
	if(isset($a_holiday))
		{
		foreach ($a_holiday as $index => $holiday)
			{
			
			if ($holiday == $year.$month.$day)
				{
				echo "<b>$a_name[$index]</b><br><br>";
				}
			}
		}
	
		
		
		
	//display events for today
	if (isset($array_id))
						{	
					
			foreach ($array_id as $key => $id) 
				{	
				if ($array_day[$key] == $daycount)
					{
					if ($array_name[$key] == "")
						{
						$array_name[$key] = "_____";
						}
				
					
					
					echo "<a href=\"calendar.php?m=$m&amp;event=book&amp;event_id=$id&amp;t=$t\" style=\"color: $array_colour[$key]\" ";

					if ($array_description[$key] != "") { echo "onmouseover=\"showelement('e$id','35')\" onmouseout=\"hideelement('e$id')\" ";}
					
					echo ">$array_name[$key]</a>
					
					
				<br>";
					if ($array_description[$key] != "")		{ echo "	<div style=\"position:relative;z-index:1;\" >
		<p class=\"textballoon\" id=\"e$id\">$array_description[$key]</p></div>";}
					echo "<span style=\"font-size:0.85em\">";
					
						$_location = $array_location[$key];
					
					if (key_exists($_location,$branches)) {echo "$branches[$_location]<br>";}
					else {
					
						if ($array_location[$key] != "") {echo "$array_location[$key]<br> ";}
						}	
										
					echo "$array_time[$key]";
                                           if ($array_finishtime[$key] == 1) {echo " - $array_endtime[$key]";}
                                        echo "<br>";
					if ($array_cancelled[$key] == 1)
						{
						echo "<span style=\"color:red;\">$phrase[152]</span><br>";
						}
					if ($array_takesbookings[$key] > 0)
						{
						
						//checking if event full
						if (isset($count_eventid)){
						foreach ($count_eventid as $countkey => $eventid) 
						{
						if ($eventid == $id)
							{
						
							if ($count_total[$countkey] >= $array_maxbookings[$key] && $array_maxbookings[$key] <> 0)
								{
								echo "<span style=\"color:red;\">$phrase[156]</span><br>";	
								}
							}
							
						}
						}
			
						}
					echo "</span><br>
					
		
					
					";
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
	while (($endline) < (7 - $cal_offset))
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
    <tr class=\"accent\"><td style=\"text-align:left\" valign=\"bottom\"> <a href=\"calendar.php?m=$m&amp;t=$lastmonth\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" ><span style=\"font-size:150%\"> $display</span></td>
   <td style=\"text-align:right\" valign=\"middle\"><a href=\"calendar.php?m=$m&amp;t=$nextmonth\" class=\"hide\">$phrase[155]</a> </td></tr>
   </table>";
   

   }
	}

 echo "</div>";  //line 12
include ("../includes/footer.php");

?>

