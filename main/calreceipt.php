<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$page_title = $phrase[466];

include ("../includes/htmlheader.php");



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
	


if (isset($_REQUEST["bookingno"])) 
	{
	if (!(isinteger($_REQUEST["bookingno"]))) 
	{$ERROR  = $phrase[72];}
	else {$bookingno = $_REQUEST["bookingno"];}
	}

if(!isset($ERROR))
{
	
	
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
		

$sql = "select event_name, event_location, event_start as time, firstname, lastname from cal_events, cal_bookings,cal_bridge where cal_events.event_id = cal_bookings.eventno and bookingno = \"$bookingno\" and cal_events.event_catid = cal_bridge.cat and m = \"$m\"";

$DB->query($sql,"calprint.php");
	$row = $DB->get();

			

$name = $row["firstname"] . " ". $row["lastname"];
//echo "$name";
$venue = $row["event_location"];

if (key_exists($venue,$branches)) { $venue =  "$branches[$venue]<br>";}

$event_name = $row["event_name"];


$date = strftime("%A %x", $row["time"]);
$time = date("g:i a", $row["time"]);
			

	
	echo "<div style=\"padding:1em;\" >
		<span style=\"float:right\" class=\"hide\">
		<a href=\"javascript:window.print()\">$phrase[255]</a> &nbsp; <a href=\"javascript:window.close()\">$phrase[266]</a></span><br><br>
	
		
	<div style=\"text-align:left\">$PREFERENCES[orgname]<br>

$phrase[265]</div>
		
		
	
	
<br><br>	<span style=\"font-size:150%\">$event_name</span><br>";
	
	
	echo "<br><b>$phrase[296]</b><br>$name<br>";
	
				
					echo "<b>$phrase[297]</b><br>$time<br>
					<b>$phrase[298]</b><br> $date<br>
					";
					
				
					echo "
					
					<b>$phrase[299]</b><br>$venue<br><br>
<br>
<br>
<br>
<br>
<br><br>
<br>&nbsp;</div>";	
			
	}
	

include ("../includes/footer.php");
	
?>