<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);







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

			

$name = $row["firstname"] ." ".$row["lastname"];
//echo "$name";
$venue = $row["event_location"];


	if (key_exists($venue,$branches)) { $venue =  "$branches[$venue]<br>";}
					



$event_name = $row["event_name"];


$date = strftime("%A %x", $row["time"]);
$time = date("g:i a", $row["time"]);

$filename = "template.htm";
$fp = fopen ($filename, "r");
$output = fread ($fp,filesize( $filename));
fclose ($fp);

//echo "$name $resource_name $location $description $daytime";

$output = str_replace("\$orgname", $PREFERENCES["orgname"], $output);
$output = str_replace("\$event", $event_name, $output);
$output = str_replace("\$receipt", $phrase[265], $output);
$output = str_replace("\$name", $name, $output);
$output = str_replace("\$venue", $venue, $output);
$output = str_replace("\$time", $time, $output);
$output = str_replace("\$date", $date, $output);


header ("Content-type: text/html");
header ("Content-disposition: inline, filename=booking.htm");
echo "$output";


}
	
?>