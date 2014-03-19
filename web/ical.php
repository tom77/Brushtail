<?php

if(!include("../includes/initiliaze_page.php"))
 {
 	include("includes/initiliaze_page.php");
 }

 //get list of locations if any
	$branches = array();	
	$sql = "select branchno, bname from cal_branches ";
	
	$DB->query($sql,"calfeed.php");
	while ($row = $DB->get())
	     {
	     	$_bname = $row["bname"];
	     	$_bno = $row["branchno"];
	     	
            $branches[$_bno] =$_bname;
			
		}		 

 
 $event_id = $DB->escape($_REQUEST["event_id"]);
 
 $sql = "select * from cal_events , cal_cat where  cal_cat.cat_web > '0' and cal_cat.cat_id = cal_events.event_catid and event_id = '$event_id' and cancelled = '0'";
 
 $DB->query($sql,"calfeed.php");
	//$num_rows = $DB->countrows();
	$row = $DB->get();
        
      

	$event_start = gmdate('Ymd\THis', $row["event_start"]) . "Z";
        $event_end = $row["event_start"] + (60 * 60);
     
        $event_end = gmdate('Ymd\THis',$event_end) . "Z";
  
        $event_id = $row["event_id"];
        $event_description = str_replace("
"," ",$row["event_description"]);
        $event_location = $row["event_location"];
        $event_name = $row["event_name"];
     
        if (key_exists($event_location,$branches)) {$event_location = $branches[$event_location];}
        
        $todaystamp = gmdate('Ymd\THis',time()) . "Z";
        
      //  $event_end = date('Ymd\THis',$event_start + (60 * 60));

        
$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//www.brushtail.org.au//Brushtail//EN
BEGIN:VEVENT
UID:$event_id
DTSTAMP:$todaystamp
DTSTART:$event_start
DTEND:$event_end
LOCATION:$event_location
DESCRIPTION:$event_description
SUMMARY:$event_name
END:VEVENT
END:VCALENDAR";
 
//echo $ical;
//exit();
$filename = "event" . $event_id . ".ics";
header("Content-Type: text/calendar;name=\"$filename\";method=REQUEST\n");
header("content-disposition: attachment; filename=\"$filename\" \n");
header("Content-Transfer-Encoding: 8bit\n"); 
			
echo "$ical";
?>


