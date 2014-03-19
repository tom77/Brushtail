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

#########################################################
#########################################################

 




$_REQUEST =  trimtext($_REQUEST);

extract($_REQUEST);
if (isset($event_id)){
$event_id = $DB->escape($event_id);
}


if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "logout")
{
	
unset($_SESSION['eventlogin']);	
	
}



if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "list")
{
	$heading = $phrase[980];
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "view")
{
	
	    $sql = "SELECT cat_web, cat_name,cat_colour,cat_cost, cat_age, cat_staffname, cat_address, cat_multiple,cat_confirmation, cat_print, cat_comments, cal_cat.cat_notes as cat_notes, cat_takesbookings, cat_waitinglist,  maxbookings, event_name, event_cost, event_location,event_description, cat_trainer, trainer, event_staffnotes, cancelled , event_start, m  FROM cal_events, cal_cat, cal_bridge  where cal_cat.cat_web > '0' and cal_cat.cat_id = cal_bridge.cat and cal_cat.cat_id = cal_events.event_catid and cal_events.event_id = '$event_id'";
		$DB->query($sql,"calweb.php");
		
		if ($DB->countrows() == 0) 
		{
		//this event does not take web bookings 
		//exit();
		}
		
		$row = $DB->get();
		
		$m = formattext($row["m"]);
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
		$displaydate = strftime("%A %x", $row["event_start"]);
		$displaytime = date("g:i a", $row["event_start"]);
		$event_start = $row["event_start"];
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
		$cat_web = $row["cat_web"];
		$cancelled = $row["cancelled"];
	
	$heading = $event_name;
	
}
else {$heading = $phrase[118];}



include('calheader.php');

if ($EVENTAUTHENTICATION == "disabled")
{
	echo "<p><h2>web bookings disabled.</h2></p>
	<p id=\"footer\"></div>
	</div>
	</body>
	</html>";
	exit();
}


	
	

if (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "view" || $_REQUEST["event"] == "list") )
{
	echo "<a href=\"webcal.php\" style=\"margin-bottom:1em;\">$phrase[427]</a> | ";
}

if (!isset($_REQUEST["event"]) || ( isset($_REQUEST["event"]) && $_REQUEST["event"] == "view"))
{
	echo "<a href=\"webcal.php?event=list\" style=\"margin-bottom:1em;\">$phrase[980]</a> |";
}


echo " <a href=\"myevent.php\" style=\"margin-bottom:1em;\">$phrase[961]</a><br><br>";


function trimtext($array)
{
 
   foreach ($array as $key=>$value)
   {
   	if (!is_array($value))
 		 {
   		$array[$key]=substr($value,0,100);
  		}
    else 
   	 {
     foreach ($value as $key2=>$value2)
     	{
     		if (!is_array($value2))
 		 {
     	$array[$key][$key2]=substr($value2,0,100);
 		 }
 		 else
 		 {
 		 	foreach ($value2 as $key3=>$value3)
     	{
     	$array[$key][$key2][$key3]=substr($value3,0,100);	
     	}
 		 	
 		 }
     	}
     
   	 }
    
   	
  }
  return $array;
  
}





if (isset($_REQUEST["event"])) {
$event = $_REQUEST["event"];}





$integers[] = "t";
$integers[] = "year";
$integers[] = "month";
$integers[] = "event_id";
$integers[] = "places";
$integers[] = "bookingno";


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
{echo $ERROR;
exit();
}




if (isset($start) && !isset($day)) {$day = $start;}


	

				//get list of locations if any
	$branches = array();	
	$sql = "select branchno, bname from cal_branches order by bname";
	
	$DB->query($sql,"calendars.php");
	while ($row = $DB->get())
	     {
	     	$_bname = $row["bname"];
	     	$_bno = $row["branchno"];
	     	
            $branches[$_bno] =$_bname;
			
		}		 












if (isset($event) && $event == "list")
{
	

	if (isset($_REQUEST["daysahead"])) {$UPCOMING_EVENTS_DAYS_AHEAD = $_REQUEST["daysahead"];}
	

	
	if (!isset($UPCOMING_EVENTS_DAYS_AHEAD)) {$UPCOMING_EVENTS_DAYS_AHEAD= 30;}
	$now = time();
	$cutoff = $now + ($UPCOMING_EVENTS_DAYS_AHEAD * 24 *60 *60);
		//get list of this months events and put in array
	
	$insert = "";
	if (isset($_REQUEST["location"]) && $_REQUEST["location"] != "0") 
	{
	$location = $DB->escape($_REQUEST["location"]);
	$locations = explode(":",$location);
		
		$insert .= " and event_location IN (";
		$counter = 0;
		
		if (isset($locations)){
		foreach ($locations as $index => $value)
				{
                                if (is_numeric($value))
                                {
				if ($counter > 0) {$insert .= ",";}
				$insert .= $value;	
					
				$counter++;
                                }
				}
		}
		$insert .= ") ";
                
                if ($counter == 0) {$insert = "";}
		
		//$insert .= " and event_location = '$location'";
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
			
			
		$DB->query($sql,"webcal.php");
		
		
			while ($row = $DB->get()) 
					{
					$array_id[] =$row["event_id"];
				
					$array_name[] =$row["event_name"];
					$array_location[] =$row["event_location"];
					$array_cost[] =$row["event_cost"];
					$array_description[] =formattext($row["event_description"]);
					$array_day[] = strftime("%A %x", $row["event_start"]);
					$array_event_start[] = $row["event_start"];		
					$array_time[] = date("g:i a", $row["event_start"]);
					$array_cancelled[] =$row["cancelled"];
					$array_colour[] =$row["cat_colour"];
					$array_cat_web[] =$row["cat_web"];
					$array_maxbookings[] =$row["maxbookings"];
					$array_takesbookings[] =$row["cat_takesbookings"];
					$array_cat_waitinglist[] =$row["cat_waitinglist"];
					}

			//get list of events and their total bookings
		
			//$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					//where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					//$where and	month(FROM_UNIXTIME(event_start)) = \"$month\" and year(FROM_UNIXTIME(event_start)) = \"$year\"
					//group by event_id";
					
			$count_total = array();		
			$waiting_total = array();		
					
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
			
		
			//echo $sql;
			
			$DB->query($sql,"webcal.php");
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
			
			$DB->query($sql,"webcal.php");
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
					
					$sql .= ") and content_id = '0'";
					
					//echo $sql;
						 
		$DB->query($sql,"webcal.php");	
		$num = $DB->countrows();
		
		
		while ($row = $DB->get())
		{
		$image = $row["image_id"];
		$event = $row["page"];
		$images[$event] = $image;
		}
			
					
					
				}
					
	
		
		
	
				echo "<div id=\"listmargin\"><div>
				<form action=\"webcal.php\" method=\"get\">
				<h3>$phrase[981]</h3><span>$phrase[121]</span>
				<select name=\"location\">
				<option value=\"0\">$phrase[475]</option>";
			
			foreach ($branches as $bno => $bname)
					

			{
		
			echo "<option value=\"$bno\"";
			if (isset($_REQUEST["location"]) && $_REQUEST["location"] == $bno) {echo " selected";}
			echo ">$bname</option>";
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
				
				
				
				
				
				$url = "calfeed.php";
				
				if (isset($_REQUEST["location"])) {	$loc = urlencode($_REQUEST["location"]);}
				if (isset($_REQUEST["keywords"])) {  $kwords = urlencode($_REQUEST["keywords"]);}
				
				
				if (isset($_REQUEST["location"])  && isset($_REQUEST["daysahead"]))
				{
					$url .= "?location=$loc&daysahead=". $_REQUEST["daysahead"];
				}
				
				elseif (isset($_REQUEST["daysahead"]))
				{
					$url .= "?daysahead=". $_REQUEST["daysahead"];
				}
				
				
				elseif (isset($_REQUEST["location"])) {$url .= "?location=$loc";}
				
				elseif (isset($_REQUEST["keywords"])) {$url .= "?keywords=$kwords";}
				
				
				
				
				echo "</select>
				<input type=\"hidden\" name=\"event\" value=\"list\">
				<input type=\"submit\" value=\"$phrase[982]\" name=\"submit\">
				</form>
				
				<form action=\"webcal.php\" method=\"get\">
				<h3>$phrase[863]</h3>
				<input type=\"hidden\" name=\"event\" value=\"list\">
				<input type=\"text\" name=\"keywords\">
				<input type=\"submit\" value=\"$phrase[982]\" name=\"submit\">
				</form>
				<p>
				<a href=\"$url\"><img src=\"rss.gif\" alt=\"rss\" title=\"rss\"></a>
				</p>
				</div></div>
				
				<div class=\"eventlist\"><div>"	;
				if (isset($array_id))
				{	
				foreach ($array_id as $key => $id) 
				
				{
					
					echo "<h3>$array_name[$key]</h3><p>";
				
					if (isset($images) && array_key_exists($id,$images))
					{ 
						
						echo "<br><a href=\"webcal.php?event=view&amp;event_id=$id\">
						<img  src=\"calimage.php?image_id=$images[$id]&width=400\" alt=\"$array_name[$key]\"></a><br><br>";
					}
					echo "
					$array_description[$key]<br><br>
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


   echo " <a href=\"ical.php?event_id=$id\" class=\"ical\"><img src=\"cal36.png\" title=\"$phrase[1084]\"  alt=\"$phrase[1084]\" ></a>";

					
					if ($array_cancelled[$key] == 1)
						{
						echo "<br><span style=\"color:red;\">$phrase[152]</span><br>";
						}
					elseif ($array_takesbookings[$key] == 1)
						{
					
						//checking if event full
						
						//echo "$array_maxbookings[$key] $count_total[$id]";
						if (isset($count_total) && array_key_exists($id,$count_total) && array_key_exists($key,$array_maxbookings))
						{
							if ($count_total[$id] >= $array_maxbookings[$key] && $array_maxbookings[$key] <> 0)
								{
								echo "<br><span style=\"color:red;\">$phrase[156]</span>";	
								}
						}
						
								
					
						
						//echo "key is $key max is $array_maxbookings[$key]";
						
						
						 if ($array_cat_web[$key] > 1  && $EVENTAUTHENTICATION != "disabled" &&  $array_event_start[$key] > $now)
						 {		
								
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
								echo "<a href=\"myevent.php?event_id=$id\">$phrase[150]</a><br>$phrase[964]";
							
							}
						else
							{
							echo "<a href=\"myevent.php?event_id=$id\" class=\"bookinglink\">$phrase[859]</a>";
							}
							
						 }
			
							//echo "array_maxbookings[$key] $array_maxbookings[$key] count_total[$id] $count_total[$id]";	
							
						//	print_r($count_total);
						//	if (!array_key_exists($id,$count_total)) {echo "no bookings $id";}	
						}
							
					
						
			
						
						
						//
							echo "</p>
							
							";
						//}
				
					
				
					
				}
				}
	
				echo "</div></div>";
}




elseif (isset($event) && $event == "view")
{
	
	
	
	$now = time();
	
	//get maximum number of bookings
				$sql = "SELECT count(*) as total from cal_bookings
 					where cal_bookings.eventno = '$event_id' and cal_bookings.status = '1'";
			//echo "$sql";
			$DB->query($sql,"calweb.php");
			$row = $DB->get();
			$total = $row["total"];
			
		//get waiting list count
				$sql = "SELECT count(*) as total from cal_bookings
 					where cal_bookings.eventno = '$event_id' and cal_bookings.status = '2'";
			//echo "$sql";
			$DB->query($sql,"calweb.php");
			$row = $DB->get();
			$waiting = $row["total"];
	
	
	
		$sql = "select image_id from images where page = '$event_id' and modtype = 'c'"; 
		$DB->query($sql,"webcal.php");	
		$num = $DB->countrows();
		
		if ($num != 0)
		{  
		$row = $DB->get();
		$image_id = $row["image_id"];
		$display_picture = "yes";
		}
		
		else {$display_picture = "no";}
      

	if ($cancelled == 1)
	{echo "<div style=\"color:red;font-size:2em;\">$phrase[120]</div>";}

	
    echo "<form action=\"webcal.php\" method=\"post\" >
    
    ";
    if ($display_picture == "no") {echo "<div style=\"width:25%;float:left;margin-top:1em;\"><div> </div> </div>";} 
    else {echo "<div style=\"width:50%;float:left;margin-top:1em;\"><div><img src=\"calimage.php?image_id=$image_id&width=450\"></div> </div>";}
    
    
  
	echo " <div style=\"width:50%;float:left;margin-top:1em;\"><div style=\"text-align:left;margin-right:1em;padding:1em;border:solid 1px black;\">

	



<b>$event_name</b><br><br>
$event_description<br><br>
<b>$phrase[962]</b
><br>$displaytime $displaydate<br><br>


<b>$phrase[806]</b><br>";




					
					if (key_exists($event_location,$branches)) {echo "$branches[$event_location]<br>";}
					else {
					
						if ($event_location != "") {echo "$event_location<br> ";}
						}
					



echo "<br><br>
<b>$phrase[126]</b><br>";

if ($event_cost == 0 || $event_cost == "") {echo $phrase[963];} else {echo $moneysymbol.$event_cost;}
echo "<br><br>";

	//echo "zzzzzz cat_takesbookings $cat_takesbookings == 1  && cat_web $cat_web > 1  && EVENTAUTHENTICATION $EVENTAUTHENTICATION != disabled && cancelled $cancelled == 0 && event_start $event_start >  now $now";

	if ($cat_takesbookings == 1 && $maxbookings != 0 && ($total >= $maxbookings || $waiting != 0))
	{
		echo "<div style=\"color:#ff6666;font-size:large;\">$phrase[147]</div>";
	}
        
    if ($cat_takesbookings == 1  && $cat_web > 1  && $EVENTAUTHENTICATION != "disabled" && $cancelled == 0 && $event_start > $now)
    {    

    	
    
    	
	
	
	
	
	echo " 
<br><a href=\"myevent.php?event_id=$event_id\" style=\"font-size:150%;font-family:verdana\">";
	
	if ($maxbookings == 0 || ($maxbookings > $total && $waiting == 0)) { echo "$phrase[859]</a><br><br>";}
	elseif ($cat_waitinglist == 1) {echo "$phrase[150]</a><br><br>$phrase[964]<br><br>";}
	
	
    }
	
        $url = $WEB_CALENDAR_DIRECTORY_URL . "webcal.php?event=view&event_id=" . $event_id;
        //$url = "http://www.erl.vic.gov.au/online/test.htm";
        $url = urlencode($url);
        
	   echo "
<br>
    
       <a href=\"ical.php?event_id=$event_id\" ><img src=\"cal36.png\" title=\"$phrase[1084]\"  alt=\"$phrase[1084]\" ></a><br><br>
    <div id=\"fb-root\" ></div><script src=\"http://connect.facebook.net/en_US/all.js#appId=243765652309047&amp;xfbml=1\"></script><fb:like href=\"$url\" send=\"false\" layout=\"button_count\"  width=\"8em\" show_faces=\"false\" font=\"\"></fb:like>
 
<span style=\"float:left;\"><a href=\"http://twitter.com/share\"   class=\"twitter-share-button\">Tweet</a></span>

    <script src=\"http://platform.twitter.com/widgets.js\" type=\"text/javascript\"></script>
    
    
 
         </div></div>

                  </form>";

}





else 
{
 
if (isset($month) && isset($year))
{
$t = mktime(0, 0, 0, $month, 01,  $year);
}

if (!isset($t))
{
$t = time();

}
	$display = strftime("%B %Y", $t);
	

$day = date("d",$t);
$month = date("m",$t);
$monthname = strftime("%B",$t);
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
		
		$DB->query($sql,"webcal.php");
		$num = $DB->countrows();
		
		while ($row = $DB->get()) 
		{
		$a_holiday[] = $row["holiday"];
		$a_name[] = formattext($row["name"]);
		}
		
		

   
   //get list of usages for this calendar
		$where = "and event_catid IN ("; 
		$typecount = 1;
		$sql = "SELECT cat_id FROM cal_cat where cal_cat.cat_web > 0";
		$DB->query($sql,"webcal.php");
	
		$numrows = $DB->countrows();
		while ($row = $DB->get()) 
					{
					
					$cat =$row["cat_id"];
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
		if ($numrows == 0) {$where = " and  1 = 2";}
		
		
			
		
		
		
		//get list of this months events and put in array
	
	

				//$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  
				//where cal_cat.cat_id = cal_events.event_catid and month(FROM_UNIXTIME(event_start)) = '$month' and year(FROM_UNIXTIME(event_start)) = '$year' and template = '0' and cal_cat.cat_web > '0' and cancelled = '0' order by event_start";	
				
		
				if ($DB->type == "mysql")
			{		
			
				$sql = "SELECT event_id, event_name,maxbookings,event_description, event_location,cancelled, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and month(FROM_UNIXTIME(event_start)) = \"$month\" and year(FROM_UNIXTIME(event_start)) = \"$year\" and template = \"0\"  $where order by event_start";	
		
			}
		else
			{
			
				$sql = "SELECT event_id, event_name,maxbookings,event_description, event_location,cancelled, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and strftime('%m',datetime ( event_start , 'unixepoch','localtime' )) = '$month' and strftime('%Y',datetime ( event_start , 'unixepoch' ,'localtime')) = '$year' and template = '0' $where  order by event_start";	
			
			}		
		//echo $sql;		
				
		$DB->query($sql,"webcal.php");
		
		
			while ($row = $DB->get()) 
					{
					$array_id[] =$row["event_id"];
					$array_name[] =$row["event_name"];
					$array_location[] =$row["event_location"];
					$array_description [] = nl2br($row["event_description"]);
					$array_day[] = date("d", $row["event_start"]);			
					$array_time[] = date("g:i a", $row["event_start"]);
					$array_cancelled[] =$row["cancelled"];
					$array_colour[] =$row["cat_colour"];
					$array_maxbookings[] =$row["maxbookings"];
					$array_takesbookings[] =$row["cat_takesbookings"];
					}

			//get list of events and their total bookings
		
			//$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					//where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					//$where and	month(FROM_UNIXTIME(event_start)) = \"$month\" and year(FROM_UNIXTIME(event_start)) = \"$year\"
					//group by event_id";
					
							if ($DB->type == "mysql")
			{		
						$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					 and	month(FROM_UNIXTIME(event_start)) = \"$month\" and year(FROM_UNIXTIME(event_start)) = \"$year\"
					group by event_id";
			}
			
							else
			{		
						$sql = "SELECT count(*) as total, cal_events.event_id as event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					 and	strftime('%m',datetime ( event_start , 'unixepoch','localtime' )) = '$month' and strftime('%Y',datetime ( event_start , 'unixepoch','localtime' )) = '$year'
					group by event_id";
			}
			
		
			$DB->query($sql,"webcal.php");
			while ($row = $DB->get()) 
					{
					$count_total[$row["event_id"]] = $row["total"];
					//$count_total[] = ;
					
					}
   
			
					
   echo "
   \n<table    id=\"calendar\" cellpadding=\"3\" border=\"1\">
 <tr class=\"accent\"><td style=\"text-align:left\" colspan=\"2\">
 <a href=\"webcal.php?t=$lastmonth\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"3\" style=\"text-align:center\" >
   <span style=\"font-size:1.5em\"> 
$monthname $year</span></td>
   <td style=\"text-align:right\" valign=\"middle\" colspan=\"2\"><a href=\"webcal.php?t=$nextmonth\" class=\"hide\">$phrase[155]</a> </td></tr>
   
   "; 
   
   
      if (isset($CALENDAR_START) && $CALENDAR_START =="MON") 
       {$cal_offset = 1; $timestamp = strtotime('next Monday');} 
       else 
           {$cal_offset = 0; $timestamp = strtotime('next Sunday');}
   
  
   if (isset($CALENDAR_DAY_NAMES) && $CALENDAR_DAY_NAMES  == "off")
   {
    //display day name cells at start of month
   
   echo "<tr class=\"accent\">";
   
  
$days = array();
for ($i = 0; $i < 7; $i++) {
 $_dayName = strftime('%A', $timestamp);
 echo "<td><b>$_dayName</b></td>";
 $timestamp = strtotime('+1 day', $timestamp);
}
   
   echo "</tr>";
   }
   
   
   
   
   
   //display blank cells at start of month
   $counter = 0;
   if ($fd <> 0)
   	{
	echo "<tr >";
	}
   while ($counter < $fd)
   	{
	echo "<td>";
	
	
	
	echo "</td>";
	if ($counter == 6)
		{
		echo "</tr>\n";
		}
	$counter++;
	}
   
   
   //display month as table cells
   $daycount = 1;
   while ($daycount <= $daysinmonth)
   	{
	$endline = (($counter + $daycount) % 7);
	$day = str_pad($daycount, 2, "0", STR_PAD_LEFT);
	$dayname  = strftime("%A",mktime(0, 0, 0, $month, $daycount,  $year));
	$t = mktime(0, 0, 0, $month, $day,  $year);
	if ($endline == 1) { echo "<tr>";}
	echo "<td valign=\"top\"";
	
	if ($thisyear.$thismonth.$thisday == $year.$month.$day)
				{
				echo " id=\"scrollpoint\" class=\"accent\"";
				}
	
	
	echo "><span style=\"font-size:large\"><b>$daycount</b></span>";
        
           if (isset($CALENDAR_DAY_NAMES) && $CALENDAR_DAY_NAMES  == "off") { echo "<br><br>";} else
         {
        echo "&nbsp;&nbsp; $dayname<br><br>";
         }
        
        
       
	
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
					echo "<a href=\"webcal.php?event=view&amp;event_id=$id&amp;t=$t\" ";
						if ($array_description[$key] != "") { echo "onmouseover=\"showelement('e$id','35')\" onmouseout=\"hideelement('e$id')\" ";}
					echo ">$array_name[$key]</a><br>";
                                        
                                            if (!isset($WEB_CALENDAR_POPUP)) {$WEB_CALENDAR_POPUP = "on";}
                                        
                                        
					if ($array_description[$key] != "" && $WEB_CALENDAR_POPUP == "on")		{ echo "	<div style=\"position:relative;z-index:1;\" >
		<p class=\"textballoon\" id=\"e$id\">$array_description[$key]</p></div>";}
					
					echo "<span style=\"font-size:0.85em\">";
					
					
					$_location = $array_location[$key];
					
					if (key_exists($_location,$branches)) {echo "$branches[$_location]<br>";}
					else {
					
						if ($array_location[$key] != "") {echo "$array_location[$key]<br> ";}
						}
					
					
					
										
					echo "$array_time[$key]<br>";
					if ($array_cancelled[$key] == 1)
						{
						echo "<span style=\"color:red;\">$phrase[152]</span><br>";
						}
					if ($array_takesbookings[$key] == 1)
						{
						
						//checking if event full
					//	echo "count_total is $count_total[$id]";
						//print_r($array_maxbookings);
						
						if (isset($count_total) && array_key_exists($id,$count_total) && array_key_exists($key,$array_maxbookings)){
						//foreach ($count_eventid as $countkey => $eventid) 
						//{
						//if ($eventid == $id)
							{
								//print_r($count_total);
								
								//echo " $count_total[$id] id is $id<br>";
								
						//echo "$array_maxbookings[$key] ";
							if ($count_total[$id] >= $array_maxbookings[$key] && $array_maxbookings[$key] <> 0)
								{
								echo "<span style=\"color:red;\">$phrase[156]</span><br>";	
								}
							}
							
						//}
						}
			
						}
					echo "</span><br>";
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
	while (($endline) < 7)
		{
		echo "<td></td>";
		
		if ($endline == 7)
		{
		echo "</tr>";
		}
	$endline++;
		
		
		
		}
	
	
	}
   echo "</table>
	<script type=\"text/javascript\">
   	function showelement(element,width)
			{
			
			var sel = document.getElementById(element);
			sel.style.width = width + 'em';
			sel.style.display = 'block';
			sel.style.zIndex = '500';
			
			
			
			}
			
	function hideelement(element)
			{
			
			var sel = document.getElementById(element);
			sel.style.display = 'none';
			sel.style.zIndex = '1';
			}
   </script>
   
   
   
   
   "; 
}

?>
</div>

</body>
</html>