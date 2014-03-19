<?php


##########################################################
#END CONFIG SECTION
##########################################################


//by default this page will use intranet includes folder 
//If you want to move this bookings page to another web server.
//You will need a copy of the intranet includes folder to be in the 
//same directory as this page. The MySQL connection details in the config.php
//may also need to be updated if conecting from another server.

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

	
	
	
	$now = time();
	$cutoff = $now + 5184000;
	//sql = "select  event_name from cal_events where event_start >  = '$now' and event-start < '$cutoff' and cancelled = '0'";
	//
	//$DB->query($sql,"cal.php");
	//$row = $DB->get();
	//$event_name = htmlspecialchars($row["page_title"]);	
	//$feed = $row["feed"];

	//if ($feed == 0) {exit();}
	
	
$mainurl = $WEB_CALENDAR_DIRECTORY_URL . "calfeed.php";
	
	
	
	
		
	
			
			$pubdate = mktime(01, 01, 01,date("n"),date("j"),date("Y")); 
			//echo 
			$pubdate = strftime("%a, %d %b %Y",$pubdate);
			
		
		echo "<?xml version=\"1.0\"?>
<rss version=\"2.0\"  xmlns:atom=\"http://www.w3.org/2005/Atom\">
<channel>
<title>Events Calendar</title>
<link><![CDATA[$mainurl]]></link>
<description>Events</description>
<pubDate>$pubdate</pubDate>
";
		
	
		if (isset($_REQUEST["daysahead"])) {$UPCOMING_EVENTS_DAYS_AHEAD = $_REQUEST["daysahead"];}
	//	if (isset($_REQUEST["imageonly"])) {$imageonly = $_REQUEST["imageonly"];}
		
		//imageonly = only display events with an uploaded image
	

	
	if (!isset($UPCOMING_EVENTS_DAYS_AHEAD)) {$UPCOMING_EVENTS_DAYS_AHEAD= 30;}
	$now = time();
	$cutoff = $now + ($UPCOMING_EVENTS_DAYS_AHEAD * 24 *60 *60);
		//get list of this months events and put in array
	
	$insert = "";
	
	
	
	
	
	
	if (isset($_REQUEST["location"]) && $_REQUEST["location"] != "0") 
	{
            
            if (!is_numeric($_REQUEST["location"]))
            {
            echo "    <item>
<title>Error</title>
<link></link>
<guid></guid>
<description>This feed url needs to be updated.</description>
</item>
</channel>
</rss>";
                
                exit();
            }
            
            
            
	$location = $DB->escape($_REQUEST["location"]);
	$locations = explode(":",$location);
		
		$insert .= " and event_location IN (";
		$counter = 0;
		
		if (isset($locations)){
		foreach ($locations as $index => $value)
				{
				if ($counter > 0) {$insert .= ",";}
				$insert .= $value;	
					
				$counter++;
				}
		}
		$insert .= ") ";	
		
		//$insert .= " and event_location = '$location'";
	}
	
	
	//$sql = "select cat_id from cal_cat where cat_web = 1";
	
	
	
	
	if (isset($_REQUEST["type"]) ) 
	{
	$type = $DB->escape($_REQUEST["type"]);	
	
	$types = explode(":",$type);
		
		$insert .= " and event_catid IN (";
		$counter = 0;
		
		if (isset($types)){
		foreach ($types as $index => $value)
				{
				if ($counter > 0) {$insert .= ",";}
				$insert .= $value;	
					
				$counter++;
				}
		}
		$insert .= ") ";
		
	}
	
	
	
	if (isset($_REQUEST["keywords"]) ) 
	{
	$keywords = $DB->escape($_REQUEST["keywords"]);	
		
			if ($DB->type == "mysql")
			{
		$insert .= " and MATCH (event_name,event_description,event_location,tags) AGAINST ('$keywords' IN BOOLEAN MODE) ";
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
				if ($keywords == "") {$insert = " and 1 == 2";}
			
			}
	}
	

				//$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  
				//where cal_cat.cat_id = cal_events.event_catid and month(FROM_UNIXTIME(event_start)) = '$month' and year(FROM_UNIXTIME(event_start)) = '$year' and template = '0' and cal_cat.cat_web > '0' and cancelled = '0' order by event_start";	
				
		
				if ($DB->type == "mysql")
			{		
			
				$sql = "SELECT event_id, event_cost,event_description, event_name,maxbookings, event_location,cancelled,cat_waitinglist, cat_web, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  where   cal_cat.cat_web > '0' and cal_cat.cat_id = cal_events.event_catid and event_start > '$now' and event_start < '$cutoff' and template = '0' $insert  order by event_start";	
		
			}
		else
			{
			
				$sql = "SELECT event_id, event_cost, event_description, event_name,maxbookings, event_location,cancelled, cat_waitinglist, cat_web, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  where   cal_cat.cat_web > '0' and cal_cat.cat_id = cal_events.event_catid and datetime ( event_start , 'unixepoch','localtime' ) > '$now' and event_start < '$cutoff'  and template = '0' $insert order by event_start";	
			
			}		
	
	
		//echo $sql;		
		//exit();		
		
	$DB->query($sql,"calfeed.php");
	//$num_rows = $DB->countrows();
	while ($row = $DB->get())
	{
		
	$_location = $row["event_location"]	;

					
	if (key_exists($_location,$branches)) {$_location = $branches[$_location];}
				
		
		
		
		
		
	$event_id[] = $row["event_id"];
	//$event_name[] = date("j M", $row["event_start"]) . " " . htmlspecialchars(utf8_encode($row["event_name"]));
	$event_name[] = utf8_encode($row["event_name"]);
	$event_location[] = htmlspecialchars(utf8_encode($_location));
	$event_description[]= nl2br(htmlspecialchars(utf8_encode($row["event_description"])));	
	$event_date[] = date("g:ia ", $row["event_start"]) . strftime("%A %x", $row["event_start"]);

	}
	
	
	
		
	//build image query	
			
			 $images = array();
			 	
				if (isset($event_id))
				{
					
					$length = count($event_id);
					
					$counter = 1;
					
					$sql = "select image_id, page from images where page in (";
					
					foreach ($event_id as $key => $id) 
					{
					$sql .= $id;
					if ($counter != $length) { $sql .= ",";} 
					$counter++;
					}
					
					$sql .= ") and modtype = 'c'";
					
					//echo $sql;
						 
		$DB->query($sql,"calfeed.php");	
		$num = $DB->countrows();
		
		
		while ($row = $DB->get())
		{
		$image = $row["image_id"];
		$event = $row["page"];
		$images[$event] = $image;
		}
			
					
					
				}

				//print_r($images);
				
//echo "$sql;";
	
		if (isset($event_id))
		{

		foreach ($event_id as $key => $id) 
		{
			
		$article_url = $WEB_CALENDAR_DIRECTORY_URL . "webcal.php?event=view&event_id=" . $id; 
	
		//echo "<br>$id";
		//if (array_key_exists($id,$images)) {echo "<h1>in array</h1>";}
		
		
		//if (!isset($imageonly) || (array_key_exists($id,$images)))
		//{	
	
	echo "
<item>
<title><![CDATA[$event_name[$key]]]></title>
<link><![CDATA[$article_url]]></link>
<guid><![CDATA[$article_url]]></guid>
<description><![CDATA[";

if (array_key_exists($id,$images))	
{
$image = $WEB_CALENDAR_DIRECTORY_URL . "calimage.php?image_id=" . $images[$id] . "&width=300";	
echo "<img src=\"$image\"><br><br>";
}	
	
echo "$event_description[$key]<br><br>
<b>$event_date[$key]<br>
$event_location[$key]</b><br><br>]]></description>
</item>
";
	//}
		}
		}
	
	echo "
</channel>
</rss>";	
exit();
	
	
	
	
	
	?>