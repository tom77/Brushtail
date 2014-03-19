<?php

//set_time_limit(5);

include("../includes/initiliaze_page.php");


$now = time();

/*
$log = "";
foreach ($_REQUEST as $key => $value)
{
	$log .= "$key $value
";
}
 

$sql = "insert into pc_debug values(NULL,'1','$log')";
$DB->query($sql,"session.php");
*/


if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{ $remoteip = $_SERVER["HTTP_X_FORWARDED_FOR"];}
		else {
			$remoteip = $_SERVER["REMOTE_ADDR"];
		}

$passauth = 0;
		
if (isset($_REQUEST["buffer"])) { $buffer = $_REQUEST["buffer"] + 30;}
		
//$remoteip = "192.168.10.197";	
$date = date("Ymd");



if (isset($_REQUEST["pcno"]) && isset($_REQUEST["secret"]) && trim($_REQUEST["secret"]) != "" && $passauth ==0)
{
	$secret = $DB->escape($_REQUEST["secret"]);
	$pc = $DB->escape($_REQUEST["pcno"]);
	
	$sql = "select  count(*) as numrows, client, pcno, name, branch, outoforder,  ip, flexible from pc_computers where pcno = '$pc' and secret = '$secret' ";

	$DB->query($sql,"session.php");
	//$numrows = DB->countrows();
	$row = $DB->get();
	
	$flexible = $row["flexible"];
	$branch = $row["branch"];
	$numrows = $row["numrows"];
	$outoforder = $row["outoforder"];
	$client = $row["client"];


	if ($numrows == 1) 
{

	$pcno = $row["pcno"];
	$passauth = 1;
	
	
}
}

//echo "passauth is $passauth remote is $remoteip";

if ($passauth ==0)
{
$sql = "select  count(*) as numrows, client, pcno, name, branch, outoforder,  ip, flexible from pc_computers where ip = '$remoteip' group by ip";
//echo $sql;
	$DB->query($sql,"session.php");
	//$numrows = DB->countrows();
	$row = $DB->get();
	
	$flexible = $row["flexible"];
	$branch = $row["branch"];
	$numrows = $row["numrows"];
	$outoforder = $row["outoforder"];
	$client = $row["client"];

if ($numrows > 1) 
{
echo "Error. Duplicate computers with same ip addresss defined in content management.";
exit();

}	
elseif ($numrows == 1) 
{
	$pcno = $row["pcno"];
	$passauth = 1;
} 
}





if (!isset($pcno))
{
	
echo 	"<?xml version=\"1.0\"?>
<check>
<booked>offline</booked>
<error>PC authentication failed.</error>
</check>";
exit();
	
}

	include("../classes/PcBookings.php");
	$pcbookings = new PcBookings($DB);
	
if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "check" )
{ 

	

$error = "";	

//get usage assigned to computer
$sql = "select  pc_usage.useno as useno,logoff,cleanup,outoforder,poll_client, pc_usage.fee as fee,pc_usage.createpin as createpin from pc_computers, pc_usage, pc_bridge where pc_bridge.pcnum = pc_computers.pcno and pc_bridge.pcnum = '$pcno' and pc_bridge.useno = pc_usage.useno
and clientbooking = 1";
$DB->query($sql,"session.php");
$row = $DB->get();

$type = $row["useno"] ;
$fee = $row["fee"] ;
$createpin = $row["createpin"] ;

$outoforder = $row["outoforder"] ;
$poll_client = $row["poll_client"];
$logoff = $row["logoff"];
$cleanup = $row["cleanup"];





$now = time();
$sql = "select bookingno,cardnumber, paid,pin, bookingtime, pc_usage.noshow, checkedin from pc_bookings, pc_usage where pc_usage.useno = pc_bookings.pcusage and pcno = '$pcno' and ('$now' >= bookingtime and '$now' < endtime) and cancelled = '0' and finished = '0' ";
$DB->query($sql,"session.php");

$row = $DB->get();
$noshow = $row["noshow"] * 60;
$checkedin = $row["checkedin"];	
$bookingtime = $row["bookingtime"];	
$bookingno = $row["bookingno"];	
$cardnumber = $row["cardnumber"];	
$paid = $row["paid"];
$pin = $row["pin"];		
$numrows = $DB->countrows();


	
	if (is_numeric($noshow) && $noshow <> 0 && $checkedin == 0 && $client == 1)
	{
	if ($now > ($bookingtime + $noshow))
		{
		//cancel booking	
		$sql = "update pc_bookings set cancelled = '1', noshow= '1' where bookingno = '$bookingno' and pcno= '$pcno'";
	
		$DB->query($sql,"session.php");
		$numrows = 0;
	
		}
		
		
	//	$pcbookings->Status($pcno,$phrase);
		
		
	}		
	





$hours = $pcbookings->OpeningHours($now,FALSE,FALSE,$pcno,$phrase);

	

//print_r($hours);


//check pc is not out of order

if (!isset($type))
{

$booked = "offline";
$next = 0;	
$error = "This computer has no booking types allocated to it that allow client bookings"; 	
}

elseif (($fee != "" && $fee != 0) && ($paid == 0 || $paid == ""))

{
$booked = "locked";
$next = 0;	
$error = "Please book via service desk"; 	

}

elseif ($outoforder == 1)

{
$booked = "outoforder";
$next = 0;	
$error = "Computer out of order"; 	

}

elseif (!isset($type))

{

$booked = "offline";
$next = 0;	
$error = "Booking type not allocated"; 	
	
	
}

//self bookings cannot start more than 1 hour before branch opens
elseif ($hours['error'] != "")
{

$booked = "closed";
$next = 0;	
$error = "Library closed"; 
	
}	
		

		




elseif ($numrows > 0)
{
	$booked = "yes";
	
		
		
		
	}
	

	
	
	
	



else 
{

$day = date("j");
$month = date("n");
$year = date("Y");


//find bookings for this computer and put in array	
if ($DB->type == "mysql")
		{

$sql = "select bookingtime, endtime from pc_bookings, pc_usage where pc_usage.useno = pc_bookings.pcusage and pcno = \"$pcno\" and DAYOFMONTH(bookingtime) = '$day' and MONTH(bookingtime) = '$month' and YEAR(bookingtime) = '$year' and cancelled = \"0\" and finished = \"0\" ";
		}

		else
		{

$sql = "select bookingtime, endtime from pc_bookings, pc_usage where pc_usage.useno = pc_bookings.pcusage and pcno = \"$pcno\" and strftime('%d',datetime ( bookingtime , 'unixepoch','localtime' )) = '$day' and strftime('%m',datetime ( bookingtime , 'unixepoch','localtime' )) = '$month' and strftime('%Y',datetime ( bookingtime , 'unixepoch','localtime' )) = '$year' and cancelled = '0' and finished = '0' ";
		}

	
		
		
$DB->query($sql,"session.php");

while ($row = $DB->get())
{
$existingstart[] = $row["bookingtime"];		
$existingend[] = $row["endtime"];	
}


	
	
	
	
	
	
//computer available find out times	
 $booked = "no";
 
	$sql = "select Min(bookingtime) as bookingtime from pc_bookings where pcno = '$pcno' and '$now' < bookingtime and cancelled = '0' and finished = '0' ";
	$DB->query($sql,"session.php");
	$row = $DB->get();
	$next =	$row["bookingtime"];
	
if ($next == "") {$next = $now + 86400;}

//find out booking interval
$sql = "select pc_booking_interval, client from pc_branches, pc_computers where pc_branches.branchno = pc_computers.branch and pc_computers.pcno ='$pcno'";
//echo $sql;
$DB->query($sql,"pcdisplay.php");
$row = $DB->get();
$interval = $row["pc_booking_interval"] * 60;
$client = $row["client"];



//find out maximum booking time allowed
$sql = "select  * from pc_usage where pc_usage.useno = '$type'";	

$DB->query($sql,"pcdisplay.php");
$row = $DB->get();
//$interval = $row["mintime"] * 60;	
$max = $row["maxtime"] * 60;
$default = $row["defaulttime"] * 60;

	if ($default > $interval) {$increment = $interval;} else{$increment = $default;}

if ($flexible == 1)
{
$now = time();

$maxlimit = $now + $max;
	
	//echo "max is $maxlimit";
	

	$time = $now + $increment;
	
	//$distime = date("Ymd H:i:s",$time);
	//$disnext = date("Ymd H:i:s",$next);
	//$disclosing = date("Ymd H:i:s",$hours['closing']);
	
	//echo "time $distime closing $disclosing next $disnext <br>";
	
	if ($time >= $hours['closing']) {$time = $hours['closing']; }
	
	//$distime = date("Ymd H:i:s",$time);
	//$disnext = date("Ymd H:i:s",$next);
	//$disclosing = date("Ymd H:i:s",$hours['closing']);
	
	//echo "time $distime closing $disclosing next $disnext <br>";
	
	
	if ($next < $time) {$time = $next; }
	
	//$distime = date("Ymd H:i:s",$time);
	//$distime = date("Ymd H:i:s",$time);
	//$disnext = date("Ymd H:i:s",$next);
	//echo "time $distime closing $disclosing next $disnext <br><br><br>";
	
	//if ($next >= $time + $increment ) { $time = $time + $increment; echo "hello AA $time TTT";}
	//else {$time = $next;}
	
	
	$exitloop = "no";

	
	while (  ($next >= $time) && ($time <= $maxlimit) && ($time <= $hours['closing']) )
	{ 
//	echo "hello $time BB";
	//check that time is not in use
	$available = "yes";
	if (isset($existingstart))
	{
		foreach($existingstart as $index => $value)
	
                    
 		{
 			if ($time + $buffer > $value && $time + $buffer <= $existingend[$index])
 			{
 			//finish time is in middle of booking
 			$available = "no";	
 			}
 		
 		if ($time > $value && $time <= $existingend[$index])
 			{
 			//finish time is in middle of booking
 			$available = "no";	
 			}
 		
 		
 		if ($time > $existingend[$index] && $now < $value )
 			{
 			//booking exits within next interval
 			$available = "no";	
 			}
 		}
	}
 	
 		if ($available == "yes")
	{
	 $gap = $time - $now;
	 $times[] = $gap;
	// echo "time is $time now is $now $gap is $gap<br>
	// ";
	 //add polling interval
	}
	 
	
	
	
	
		if ($exitloop == "yes"  || $time == $next || $hours['closing'] == $time)
		{
			break;
			//echo "exiting";
			
		}
 		
 	
	//echo date("His",$time);
	$time = $time + $increment;
	
	if ($next < $time) 
		{
		$time = $next; 
		$exitloop = "yes";
				}
	if ($hours['closing'] < $time) 
		{
		$time = $hours['closing']; 
		$exitloop = "yes"; 
		}
	}
	
}
else 
{
	$time = $hours['opening'];
	
	//echo "hello1 time is $time start is $start $hours[opening]  nesxt is $next";
while ($time < $next && $time < $hours['closing']  )

{
	//echo "hello2";
	$time = $time + $increment;
	$display = date("H:is",$time);	

	if ($time > $now && $time < ($now + $max + $interval) && ($time > $hours['opening'] && $time <= $hours['closing']) &&  !($hours['closedforlunch'] == 1 && $now > $hours['lunchstart'] && $now < $hours['lunchfinish']))
	{
		$available = "yes";
	
 	if (isset($existingstart))
	{
		foreach($existingstart as $index => $value)
	          
 		{
 			if ($time + $buffer > $value && $time + $buffer <= $existingend[$index])
 			{
 			//finish time is in middle of booking
 			$available = "no";	
 			}
 		
 		if ($time > $value && $time <= $existingend[$index])
 			{
 			//finish time is in middle of booking
 			$available = "no";	
 			}
 		
 		if ($time > $existingend[$index] && $now < $value )
 			{
 			//booking exits within next interval
 			$available = "no";	
 			}
 		}
	}
 		if ($available == "yes")
			{	
				
			 $times[] = $time - $now;
	 			//add polling interval
			}
	}
}	
}





if (!isset($times))  
{$numtimes = 0;
//echo "hello";
}	
else {
$numtimes = count($times);}

}



$closed = $now -  $hours['closing'];

//30 minutes

if ($closed > 0 && $closed < 1800 && $client == 1)
{
$command = "shutdown";	
	
}




if ($pin == "auto") { $booked = "auto"; }

elseif ($pin != "") { $booked = "pin";}

if ($checkedin == 1 &&  $createpin == 0) {$booked = "auto";}

if ($booked == "yes" && $cardnumber == "") {$booked = "locked";}

if ($booked == "no" || $booked == "offline") {$bookingno = "0";}

if ($booked == "no" && $createpin == 2 ) {$booked = "locked";}


echo "<?xml version=\"1.0\"?>
<check>
<booked>$booked</booked>
<bookingno>$bookingno</bookingno>
<cardnumber>$cardnumber</cardnumber>
<checkedin>$checkedin</checkedin>
<error>$error</error>
<pin>$pin</pin>
";
if ($booked == "no")
{
	
echo "<numtimes>$numtimes</numtimes>
";
if (isset($times))  
{
 foreach($times as $index => $value)
                    
 {
 	
 	echo "<time>$value</time>
";
 }
}	
	
}
if (isset($command))  
{
echo "<command>$command</command>
";

}	

echo "<client>$client</client>
<pollclient>$poll_client</pollclient>
<logoff>$logoff</logoff>
<cleanup>$cleanup</cleanup>
</check>";
	
}






elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "book")
{ 

	

	$sql = "select pcno, name from pc_computers";
	$DB->query($sql,"session.php");
	while ($row = $DB->get())
	
	{
	$_pcno = $row["pcno"];	
	$_name = $row["name"];
	$pcs[$_pcno] = $_name;
	}
	
	
	
	
	
	$barcode = $DB->escape(str_replace(" ", "", substr($_REQUEST["barcode"],0,100)));		
	$failure = "";
	
	
		$sql = "select changepassword from  pc_users where barcode = '$barcode'";
		//echo "barcode is $barcode<br>";
		
			 $DB->query($sql,"session.php");
			$row = $DB->get();
			$change = $row["bookingno"];
			if (isset($change) && $change == 1) {$change = "yes";} else {$change = "no";}
		
	
	$now = time();
	$time = $DB->escape($_REQUEST["time"]);
	$endtime = $now + $time;	


	//check endtime does not overlap next booking
	
	$sql = "select Min(bookingtime) as bookingtime from pc_bookings where pcno = '$pcno' and '$now' < bookingtime and cancelled = '0' and finished = '0' ";
	$DB->query($sql,"session.php");
	$row = $DB->get();
	$next =	$row["bookingtime"];
	
	
	// if change endtime if next booking does not overlap more than 50% of this booking
	
	if ($next != ""  && $endtime > $next )
	{
		$newtime = $next - $now;
		
		if (($time / 2) < $newtime)
		{
		$endtime = $next;
		
		}
	}
	


	
	
	//$ticket = $_REQUEST["ticket"];	
		
	
	//if ($ticket == md5($barcode.$pcno.$date))	{$auth = "yes";} else {$auth = "no"; $failure = "Invalid ticket";}
	$auth = "yes";			
	//check user not banned	
				
	$ban = $pcbookings->checkPatronBan($barcode);
	
		if ($ban['banned'] == "yes")
	{
		$auth = "no";
		$failure = "Barcode disabled";
	}
	
	//get usage assigned to computer
$sql = "select pc_usage.useno  as useno from pc_usage, pc_bridge where pc_bridge.pcnum = '$pcno' and pc_bridge.useno = pc_usage.useno
and clientbooking = 1";
$DB->query($sql,"session.php");
$row = $DB->get();
$type = $row["useno"] ;
	
	if (!isset($type))
	{
		
		
		$auth = "no";
		$failure = "No booking type assigned to this computer";
	}
	
	
	
	 
	//check use available
	
	$sql = "select useno, name,mintime, maxtime,lockout from pc_usage where useno = '$type'";

	$DB->query($sql,"session.php");
	$numrows = $DB->countrows();
	if ($numrows != 1)
	{
		$auth = "no";
		$failure = "This booking type unavailable on this computer";
	}
	else 
	{
	//checks maximum duration
	$row = $DB->get();
	$useno = $row["useno"];
	
	$mintime = $row["mintime"];
	$lockoutminutes = $row["lockout"];
	

	$duration = $row["maxtime"] * 60;
		if ($endtime - $now > $duration + $buffer )
				{
				$auth = "no";
				$length = $endtime - $now ;
				$total = $duration + $buffer;
				$failure = "Booking duration not allowed.";	
		
				}
	
	
		
	}
	
	
		//check user not not locked out
		
		if ($lockoutminutes != 0)
		{
		$lockoutseconds = $lockoutminutes * 60;
		
		//$sql = "select max(endtime) as lastsession from pc_bookings where cardnumber = '$barcode' and pcno = '$pcno' and endtime < '$now' and finished = '0' and cancelled = '0'";
			//echo "barcode is $barcode";
		$sql = "select max(endtime) as lastsession from pc_bookings where cardnumber = '$barcode'  and pcno = '$pcno' and endtime < '$now' and finished = '0' and cancelled = '0'";
		//echo $sql;
		$DB->query($sql,"session.php");
		$row = $DB->get();
	
		
		if (isset($row["lastsession"]))
		{
	
		$lockout = $row["lastsession"] + $lockoutseconds;	
		if ($lockout > $now) 
			{
			$auth = "no";
			$failure = "New bookings cannot be within $lockoutminutes minutes of the previous booking on this computer.";	
			}
		}
		}
	//echo "lockout is $sql";
	
	//check not out of order
	
	

	
	$sql = "select outoforder, branch from pc_computers where pcno = '$pcno'";
	$DB->query($sql,"session.php");
	$row = $DB->get();
	$branch = $row["branch"];
	//$quick = $row["quick"];
	
	
	
	
	if ($row["outoforder"] == 1)
	{
		
		
		$auth = "no";
		$failure = "Computer out of order";
	}
	
	//check time is free
	
	
	//check time start
	//find out opening and closing times
	
	$hours = $pcbookings->OpeningHours($now,$endtime,$branch,FALSE,$phrase);
	$hours['closing'] = $hours['closing'] - 60;
	

	
	if ($hours['error'] != "")
	{
	$auth = "no";
	$failure = $hours['error'];	
		
	}
		
		
	//check requested time does not overlap other bookings for this barcode
	$sql = "select bookingno, pcno, bookingtime,endtime from pc_bookings where cardnumber = '$barcode' and (($now >= bookingtime and  $now < endtime ) or ( $endtime <= endtime  and bookingtime < $endtime) or ($now >= bookingtime and $endtime <= endtime)  or ($now < bookingtime and $endtime > endtime)) and cancelled = '0' and finished = '0'";
	

	$DB->query($sql,"session.php");
	while ($row = $DB->get())
	{
	$_pn = $row["pcno"];	
	$bookingtime = $row["bookingtime"];
	
	
	if ($bookingtime > $now)
	{
	//if self bookng begins before existing booking, allow but change end time to be the start time of existing booking
	$endtime = $bookingtime;	
	}
	else 
	{
		//if self booking begins after existing booking block new booking
	$auth = "no";
	$failure = "You have an existing booking on $pcs[$_pn].";	
	break;		
	}
	}
	
	
	
	
	 if ($DB->type == "mysql")
									{  	
								$sql = "LOCK TABLE pc_bookings WRITE";
									}
								 else
									{  	
								$sql = "begin transaction";
									}
									
			$DB->query($sql,"session.php");							
	
	//check requested time does not overlap other bookings for this computer
	$sql = "select bookingno from pc_bookings where pcno = '$pcno' and 
		(($now >= bookingtime and  $now < endtime ) or ( $endtime <= endtime  and bookingtime < $endtime) or ($now >= bookingtime and $endtime <= endtime)  or ($now < bookingtime and $endtime > endtime)) and cancelled = '0' and finished = '0'";
	
	$DB->query($sql,"session.php");
	$num_rows = $DB->countrows();
	if ($num_rows > 0)
		{
		$auth = "no";
		$failure = "Time requested overlaps with existing booking.";					      	
		}
	

		
//$auth = "no";
//$failure = "computer already booked";		
		
		
	//add booking
	if ($auth == "yes")
	{
	$ip = $_SERVER["REMOTE_ADDR"];
	
	$now = time();
	
    $insertsql = "INSERT INTO pc_bookings VALUES(NULL,'','', '$now','$branch', '$pcno', '0', '$useno', '$barcode', '$endtime','$ip','public','0','$now','','','0','0','3','1','0','0','','0')";
    
    
	$DB->query($insertsql,"session.php");

	$bookingno = $DB->last_insert();	

	

		
		
	}
	else 
	{$bookingno = 0;}
	
	if ($DB->type == "mysql")
		{  	
		 $sql = "unlock tables";
		}
								
else
		{  	
		$sql = "commit";
		}
				
		$DB->query($sql,"session.php");
		
		
			$sql = "select bookingtime, pcno from pc_bookings where bookingno = '$bookingno'";
			$DB->query($sql,"session.php");
			$row = $DB->get();
			$start = $row["bookingtime"];
			$pc = $row["pcno"];
			
		
		$pcbookings->Status($pcno,$phrase);
	

	
		echo "<?xml version=\"1.0\"?>
<book>
<bookingno>$bookingno</bookingno>
<auth>$auth</auth>
<failure>$failure</failure>
<change>$change</change>
</book>";


		  if (isset($PCDEBUG) && $PCDEBUG == "on" && $auth == "yes")
					{
					pc_debug($DB,$bookingno,$insertsql);	
					
					
					}

	
	
}



elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "authenticate" )
{ 

	
	$auth = "no";
	$failure = "Authentication needs to be configured.";
	$change = "no";
	
		
	
	$now = time();
	
	$endtime = $DB->escape($now + $_REQUEST["time"]);
	
	
	
	
	
	$password = $DB->escape(substr($_REQUEST["password"],0,100));	
	$barcode = $DB->escape(substr($_REQUEST["barcode"],0,100));	
	$barcode = str_replace(" ", "", $barcode);
        
        
      if($PCAUTHENTICATION == "soap")
         {
         if (!file_exists("../includes/soap.php"))
         {
              $auth = "no";
             $failure = "Soap file missing.";
         }
         else {
             $password = substr(trim($_REQUEST["password"]),0,100);
         include("../includes/soap.php");
         
        $auth = $result["auth"];
	$failure = $result["failure"];
         
                }
         }
        
        else
        
	{
	include("../classes/AuthenticatePatron.php");

	$CHECK = new AuthenticatePatron($password,$barcode,$PREFERENCES,$DB,"pc");
	
	$auth = $CHECK->auth;
	$failure = $CHECK->failure;
        }
	
	//check barcoode is booked
	if (isset($_REQUEST["mode"])) {$mode = $_REQUEST["mode"];}

	if ($mode == "check")
	
	{
		$now = time();
	
	
$sql = "select bookingno, endtime from pc_bookings where pcno = '$pcno' and cardnumber = '$barcode' and ('$now' >= bookingtime and '$now' < endtime) and cancelled = '0' and finished = '0' ";
			 $DB->query($sql,"session.php");
		//echo $sql;
			$numrows = $DB->countrows();	
			$row = $DB->get();
			
			//echo "numrows is $numrows";
			if ($numrows != 1)
			
				{
				$auth = "no";	
				$failure = "Invalid barcode";
			
				}
	
	}
	
			
	
	
	
	
	//check user not banned	
	$ban = $pcbookings->checkPatronBan($barcode);
	
		if ($ban['banned'] == "yes")
	{
		$auth = "no";
		$failure = "Barcode disabled";
	}
	
	//get usage assigned to computer
$sql = "select pc_usage.useno as useno from pc_usage, pc_bridge where pc_bridge.pcnum = '$pcno' and pc_bridge.useno = pc_usage.useno
and clientbooking = 1";
$DB->query($sql,"session.php");
$row = $DB->get();
$type = $row["useno"] ;
	
	if (!isset($type))
	{
		
		
		$auth = "no";
		$failure = "No booking type assigned to this computer";
	}
	
	
	
	 
	//check use available
	
	$sql = "select * from pc_usage where useno = '$type'";
	$DB->query($sql,"session.php");
	$numrows = $DB->countrows();
	if ($numrows != 1)
	{
		$auth = "no";
		$failure = "This booking type unavailable on this computer";
	}
	elseif ($mode == "new")
	{
	//checks maximum duration
	$row = $DB->get();
	$useno = $row["useno"];
	
	$mintime = $row["mintime"];
	$lockoutminutes = $row["lockout"];
	$timelimit = $row["timelimit"];
	

	$duration = $row["maxtime"] * 60;
		if ($endtime - $now > $duration + 40)
				{
				$auth = "no";
				$failure = "Booking duration not allowed.";	
		
				}
	
	if ($timelimit != 0)
			{
			//  check that the user is not exceeding daily time limit
			
			$result = $pcbookings->dailylimit($barcode,$timelimit,$type);
			if ($result == "error")
				{
				$auth = "no";
				$failure = "Daily usage limit reached.";
				//$failure = "$result";
				}
			
			
			}
			
			
			
	
	$sql = "select pcno, name from pc_computers";
	$DB->query($sql,"session.php");
	while ($row = $DB->get())
	
	{
	$_pcno = $row["pcno"];	
	$_name = $row["name"];
	$pcs[$_pcno] = $_name;
	}

	
	
			
		//check requested time does not overlap other bookings for this barcode
	$sql = "select bookingno, pcno, bookingtime,endtime from pc_bookings where cardnumber = '$barcode' and 
		(($now >= bookingtime and  $now < endtime ) or ( $endtime <= endtime  and bookingtime < $endtime) or ($now >= bookingtime and $endtime <= endtime)  or ($now < bookingtime and $endtime > endtime)) and cancelled = '0' and finished = '0'";
	

	$DB->query($sql,"session.php");
	while ($row = $DB->get())
	{
	$_pn = $row["pcno"];	
	$bookingtime = $row["bookingtime"];
	
	
	if ($bookingtime > $now)
	{
	//if self bookng begins before existing booking, allow but change end time to be the start time of existing booking
	$endtime = $bookingtime;	
	}
	else 
	{
		//if self booking begins after existing booking block new booking
	$auth = "no";
	$failure = "You have an existing booking on $pcs[$_pn].";	
	break;		
	}
	}		
			
			
		
	}
	
	
		//check user not not locked out
		
		if (isset($lockoutminutes) && $lockoutminutes != 0 && $mode == "new")
		{
		$lockoutseconds = $lockoutminutes * 60;
	
		//$sql = "select max(endtime) as lastsession from pc_bookings where cardnumber = '$barcode' and pcno = '$pcno'  and endtime < '$now' and finished = '0' and cancelled = '0'";
		
		$sql = "select max(endtime) as lastsession from pc_bookings where cardnumber = '$barcode' and pcno = '$pcno' and endtime < '$now' and finished = '0' and cancelled = '0'";
		
		$DB->query($sql,"session.php");
		$row = $DB->get();
	//echo $sql;
		
		if (isset($row["lastsession"]) && $mode != "check")
		{
	 $last = $row["lastsession"];
		$lockout = $row["lastsession"] + $lockoutseconds;	
		if ($lockout > $now) 
			{
			$auth = "no";
			$failure = "New bookings cannot be within $lockoutminutes minutes of the previous booking.";	
			}
		}
		}

	

	
		echo "<?xml version=\"1.0\"?>
<book>
<auth>$auth</auth>
<failure>$failure</failure>
<change>$change</change>
</book>";



	
	
}





	

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "session" )
{ 


	
$bookingno =  $DB->escape(substr($_REQUEST["bookingno"],0,100));

$sql = "select endtime, warning_1, warning_2, warning_1_text, warning_2_text, extend from pc_bookings, pc_usage 
where bookingno = '$bookingno' and pc_bookings.pcusage = pc_usage.useno";	
	
$DB->query($sql,"session.php");		
$numrows = $DB->countrows();	
$row = $DB->get();

$endtime = $row["endtime"];
$warning_1 = $row["warning_1"];
$warning_2 = $row["warning_2"];
$warning_1_text = $row["warning_1_text"];
$warning_2_text = $row["warning_2_text"];
$extend = $row["extend"];
if ($extend == 1) {$extend = "yes";} else {$extend = "no";} 
$duration = $endtime - time();
if ($duration < 0) {$duration = 0;} 


	echo "<?xml version=\"1.0\"?>
<session>
<duration>$duration</duration>
<warning_1>$warning_1</warning_1>
<warning_2>$warning_2</warning_2>
<warning_1_text><![CDATA[$warning_1_text]]></warning_1_text>
<warning_2_text><![CDATA[$warning_2_text]]></warning_2_text>
<extend>$extend</extend>
</session>";
	
	
}




elseif (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "extend" || $_REQUEST["event"] == "next" ))
{ 
	
	
	
	$available= "no";
	$duration = 0;
	$failure = "";
	$now = time();
	$bookingno =  $DB->escape(substr($_REQUEST["bookingno"],0,100));		

	


	
	$sql = "select pcusage, mintime,timelimit,defaulttime, bookingtime, endtime from pc_usage, pc_bookings 
	where 
	pc_usage.useno = pc_bookings.pcusage and 
	pc_bookings.bookingno = '$bookingno'";
	//echo $sql;

	$DB->query($sql,"session.php");
	
	
	$row = $DB->get();
	$default = $row["defaulttime"] * 60;
	$endtime = $row["endtime"];
	$bookingtime = $row["bookingtime"];
	
	$timelimit = $row["timelimit"];
	$mintime = $row["mintime"] * 60;
	$type = $row["pcusage"];
	
		//echo "$sql $bookingtime";
	
	
//find out booking interval
$sql = "select pc_booking_interval from pc_branches, pc_computers where pc_branches.branchno = pc_computers.branch and pc_computers.pcno ='$pcno'";
$DB->query($sql,"pcdisplay.php");
$row = $DB->get();
$interval = $row["pc_booking_interval"] * 60;




$hours = $pcbookings->OpeningHours($now,FALSE,FALSE,$pcno,$phrase);
$closing = $hours['closing'];


if ($default > $interval) {$increment = $interval;} else{$increment = $default;}
$newfinish = $endtime + $increment;

//echo "newfinish is $newfinish endtime is $endtime increment is $increment<br>";

//echo "increment is $increment newfinish is $newfinish interval is $interval default is $default finish is $finish<br>";


//echo "increment is $increment newfinish is $newfinish interval is $interval default is $default  finish is $finish<br>";


	
	$time = time();
		
	//$sql = "select bookingtime as next from pc_bookings where pcno = '$pcno' and bookingno != '$bookingno' and
	//(($bookingtime >= bookingtime and  $bookingtime < endtime ) or ( $newfinish <= endtime  and bookingtime < $newfinish) or ($bookingtime >= bookingtime and $newfinish <= endtime)  or ($bookingtime < bookingtime and $newfinish > endtime)) and cancelled = '0' and finished = '0'";
	
	
	$sql = "select min(bookingtime) as next from pc_bookings where pcno = '$pcno' and bookingno != '$bookingno' and
	$bookingtime < bookingtime  and bookingtime < '$closing' and cancelled = '0' and finished = '0'  order by bookingtime";
	

	$DB->query($sql,"session.php");
	$row = $DB->get();
	$num_rows = $DB->countrows();
	$next = $row["next"];
	
	//echo $sql;
	
	if (!($next > 0)) {
		$next = $closing;
	//	 echo "next1 is $next <br>";
	}
	
	
	if ($newfinish > $closing) {
		$newfinish = $closing; //echo "CLOSINGGGGGG <br>";
	}
	
	if ($newfinish > $next && $next > $endtime) 
		{
		$newfinish = $next;
	//	echo "OVERLAP<br>";
		}
	
//echo "min is $mintime";
	if ($newfinish - $endtime < $mintime)
			{
			$newfinish = $endtime;
			$available = "no";	
			$failure = "Minimum time not available";	
	
			}
		else 
			{
			$available = "yes";	
			}
			//echo "next is $next AAAA ";
			
		
		
		
		
	
	if ($timelimit != 0)
			{
			//  check that the user is not exceeding daily time limit
			
			$result = $pcbookings->dailylimit($cardnumber,$timelimit,$type);
			if ($result == "error")
				{
				$available = "no";
				$failure = "Daily usage limit reached.";
				//$failure = "$result";
				}
			
			
			}

			
	//check new finish time does not occur within existing booking		
	$sql = "select bookingtime from pc_bookings where pcno = '$pcno' and bookingno != '$bookingno' and
	$newfinish > bookingtime and  $newfinish <= endtime and cancelled = '0' and finished = '0'  order by bookingtime";	
	$DB->query($sql,"session.php");
	
	while ($row = $DB->get()) 
		{
			$available = "no"; $failure = "Time already in use.";	
			
		}
	
		
	
			
			
			if ($available == "yes" && $_REQUEST["event"] == "extend")
			{
			
			$sql = "update pc_bookings set endtime = '$newfinish', modtime = $time where bookingno = '$bookingno' and pcno = '$pcno'";
			$DB->query($sql,"session.php");		
			
			   if (isset($PCDEBUG) && $PCDEBUG == "on")
					{
					pc_debug($DB,$bookingno,$sql . " booking extended by user $now");	
					}
		
				
			}

			
			
			
		
		$pcbookings->Status($pcno,$phrase);

			

	$duration = $newfinish - time() ;
	
	if ($duration < 0) {$available = "no";}
	
	echo "<?xml version=\"1.0\"?>
<next>
<nexttimeslot>$available</nexttimeslot>
<failure>$failure</failure>
<duration>$duration</duration>
</next>";	
	
}





elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "checkedin" )
{ 
	$now = time();
$sql = "select bookingno, endtime from pc_bookings where pcno = '$pcno' and ('$now' >= bookingtime and '$now' < endtime) and cancelled = '0' and finished = '0' ";
			 $DB->query($sql,"session.php");
		//echo $sql;
			$numrows = $DB->countrows();	
			$row = $DB->get();
			
			
			if ($numrows == 1)
				{
				
				$bookingno = $row["bookingno"];
				$endtime = $row["endtime"];
			
				$next  = $endtime - $now;
				$auth = "yes";
				$failure = "";
				}
			else 
				{
				$bookingno = 0;
				$auth = "no";	
				$failure = "Invalid barcode";
				$next = 0;
				}
	
				
		echo "<?xml version=\"1.0\"?>
<checkin>
<auth>$auth</auth>
<failure>$failure</failure>
<next>$next</next>
<bookingno>$bookingno</bookingno>
<change></change>
</checkin>";
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "checkin" )
{ 
	
	

		
$bookingno =  $DB->escape(str_replace(" ", "", substr($_REQUEST["bookingno"],0,100)));

if (isset($_REQUEST["barcode"]))
{
$barcode =  $DB->escape(str_replace(" ", "", substr($_REQUEST["barcode"],0,100)));
}		
$now = time();
	

//if ($bookingno == 0)
//{
//$sql = "select bookingno,endtime from pc_bookings where pcno = \"$pcno\" and cardnumber = '$barcode' and ('$now' >= bookingtime && '$now' < endtime) and cancelled = \"0\" and finished = \"0\" ";
//}
//else 
//{
$sql = "select endtime from pc_bookings where pcno = '$pcno' and bookingno = '$bookingno' and ('$now' >= bookingtime and '$now' < endtime) and cancelled = '0' and finished = '0' ";	
	
//}

//echo $sql;
$DB->query($sql,"session.php");
		
			$numrows = $DB->countrows();	
			$row = $DB->get();
			
			
			if ($numrows == 1)
				{
				
				//$bookingno = $row["bookingno"];
				$endtime = $row["endtime"];
			
				$next  = $endtime - $now;
				$auth = "yes";
				$failure = "";
				}
			else 
				{
			//	$bookingno = 0;
				$auth = "no";	
				$failure = "Invalid barcode";
				$next = 0;
				}

//	if (isset($barcode) && $barcode != "")
//	{			
//	$sql = "select changepassword from  pc_users where barcode = '$barcode'";
	//		 $DB->query($sql,"session.php");
	//		$row = $DB->get();
	//		$change = $row["bookingno"];
	//		if (isset($change) && $change == 1) {$change = "yes";} else {$change = "no";}
	//}
			
	
	//$ticket = $_REQUEST["ticket"];	
	
	
	//if ($ticket != md5($barcode.$pcno.$date)) {$auth = "no"; $failure = "Invalid ticket";}
	
	
	






if ($auth == "yes")
{
	
	
	$sql = "update pc_bookings set checkedin = '1' where bookingno = '$bookingno' and pcno = '$pcno'";
	
	$DB->query($sql,"session.php");
	
	  if (isset($PCDEBUG) && $PCDEBUG == "on")
					{
					pc_debug($DB,$bookingno,$sql . " checked in by user $now");	
					}

	$pcbookings->Status($pcno,$phrase);
	
	
}

	echo "<?xml version=\"1.0\"?>
<checkin>
<auth>$auth</auth>
<failure>$failure</failure>
<next>$next</next>
<bookingno>$bookingno</bookingno>
<change></change>
</checkin>";

}

//////////
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "changepassword" )
{ 
		
$password = md5($_REQUEST["pass"]);
$bookingno = $DB->escape(substr($_REQUEST["bookingno"],0,100));


	
			$sql = "select cardnumber from pc_bookings where bookingno = '$bookingno' and pcno = '$pcno'";
			 $DB->query($sql,"session.php");
			$row = $DB->get();
			$cardnumber = $row["cardnumber"];
			
			$sql = "update pc_users set password = '$password', changepassword = '0' where barcode = '$cardnumber'";
			//echo $sql;
			 $DB->query($sql,"session.php");
			 $numrows = $DB->countrows();
			 if ($numrows == 1) {$response = "password updated";} else {$response = "";}
			
echo "<?xml version=\"1.0\"?>
<end>
<response>$response</response>
</end>";			
			
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "endbooking" )
{ 
	


$bookingno = $DB->escape($_REQUEST["bookingno"]);
$now = time();
	
$sql = "select bookingtime, endtime from pc_bookings where bookingno = '$bookingno' and ('$now' >= bookingtime and '$now' < endtime) and cancelled = '0' and finished = '0' ";
			 $DB->query($sql,"session.php");
		
			$numrows = $DB->countrows();	
			$row = $DB->get();
			
			
			if ($numrows == 1)
				{
					
$sql = "update pc_bookings set endtime = '$now' where bookingno = '$bookingno' and pcno = '$pcno'";
$DB->query($sql,"session.php");

  if (isset($PCDEBUG) && $PCDEBUG == "on")
					{
					pc_debug($DB,$bookingno,$sql . " booking ended by user $now");	
					}

			 
$response =  "session ended";
			
				} else {$response =  "no session to end";}
		


		$pcbookings->Status($pcno,$phrase);
					
				
				
echo "<?xml version=\"1.0\"?>
<end>
<response>$response</response>
</end>";


}


///////////

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "validate" )
{ 
	

	
	echo "<?xml version=\"1.0\"?>
<validate>
";
//1 - check patron booked session
//1 - authenticate patron 
//1 - update booking

$bookingno = $DB->escape($_REQUEST["bookingno"]);
$now = time();
	
$sql = "select bookingno, endtime from pc_bookings where pcno = '$pcno' and bookingno = '$bookingno' and ('$now' >= bookingtime and '$now' < endtime) and cancelled = '0' and finished = '0' ";
			 $DB->query($sql,"session.php");
		
			$numrows = $DB->countrows();	
			$row = $DB->get();
			
			
			if ($numrows == 1)
				{
			
				$endtime = $row["endtime"];
			
				$next  = $endtime - $now;
				$auth = "yes";
				$failure = "";
				}
			else 
				{
			
				$auth = "no";	
				$failure = "not currently booked";
				$next = 0;
				}



echo "<auth>$auth</auth>
<failure>$failure</failure>
<next>$next</next>
</validate>";

}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "test" )
{ 
echo "<?xml version=\"1.0\"?>
<test>
<echo>hello</echo>
</test>";
}




?>