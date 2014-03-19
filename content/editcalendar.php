<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);
$datepicker = "yes";
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


$ip = ip("pc"); 


echo "<div style=\"text-align:center\">";

/*if (isset($_REQUEST["t"]))
{
if ((isinteger($_REQUEST["t"])))
	{
	$t = $_REQUEST["t"];	
	}
	else 
	{$ERROR  =  "$phrase[72]";	}	
	
}	

if (isset($_REQUEST["cat"]))
{
if ((isinteger($_REQUEST["cat"])))
	{
	$cat = $_REQUEST["cat"];	
	}
	else 
	{$ERROR  =  "$phrase[72]";	}	
	
}	



if (isset($_REQUEST["event_id"]))
{
if (!(isinteger($_REQUEST["event_id"])))
	{$ERROR  =  "$phrase[72]";	}
	else 	
	{
	$event_id = $_REQUEST["event_id"];	
	}
}
*/

$integers[] = "minute";
$integers[] = "hour";
$integers[] = "year";
$integers[] = "month";
$integers[] = "day";
$integers[] = "cat";
$integers[] = "t";
$integers[] = "event_id";
$integers[] = "branchno";
$integers[] = "image_id";
$integers[] = "reg_id";
$integers[] = "maxage";
$integers[] = "minage";
$integers[] = "fminute";
$integers[] = "fhour";






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




if (isset($_REQUEST["m"]))
	{	
	
	if (isinteger($_REQUEST["m"]))
		{	
		$m = $_REQUEST["m"];	
		$access->check($m);
	
	if ($access->thispage < 3)
			{
		
			$ERROR  =  "<div style=\"text-align:center;font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
			}
	elseif ($access->iprestricted == "yes")
			{
			$ERROR  =  $phrase[0];
			}
	
		}
	else 
		{
		$ERROR  =  "$m $phrase[72]";
	
		}		
	}
else {
	$ERROR  =  "no module chosen";
}	


if (!isset($ERROR))
{	
$sql = "select name from modules where m = \"$m\"";
$DB->query($sql,"editcalendar.php");
			$row = $DB->get();
$modname = formattext($row["name"]);
	
}
	

$hourdisplay = array('1 am','2 am' ,'3 am','4 am','5 am','6 am', '7 am', '8 am','9 am','10 am','11 am', '12', '1 pm', '2 pm', '3 pm', '4 pm', '5pm',
		'6 pm', '7 pm', '8 pm', '9 pm', '10 pm', '11 pm');
$hoursvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12', '13', '14', '15', '16', '17',
		'18', '19', '20', '21', '22', '23');





 if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deleteimage")
   {	
	
		
	
        if ($PREFERENCES["storage"] == "file")
         {
         $filepath = $PREFERENCES['docdir']."/calendars/".$event_id ;  
        // echo " deleted $filepath";
         delDir($filepath);
         } 

 $sql = "delete from images where page = '$event_id' and modtype = 'c'";	
$DB->query($sql,"tvedit.php");
	
	
   }
	
	

  
  
 	

  
if (isset($_GET["update"]) && $_GET["update"] == "deletereguser")

{
$sql = "delete from registered_users where reg_id = '$reg_id'";

  	$DB->query($sql,"editcalendar.php");
  	
  	$DB->tidy("registered_users");
		
 
	
	
}

  
  
   	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletetemplate")
	{
		
		$sql = "delete from cal_events where event_id = '$event_id'";
		 	$DB->query($sql,"editcalendar.php");
		
	}
  
  
  
  
  
  	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "add")
	{
	
	if (isset($_REQUEST["minage"]) && $_REQUEST["maxage"])
	{
	//$minage = $_REQUEST["minage"];
	//$maxage = $_REQUEST["maxage"];

	
	if (($maxage > $minage) && ($minage > 0 && $minage < 120) && ($maxage > 1 && $maxage < 120))	
		{
		$agerange = $minage . ":" . $maxage;
		}
		
	}
	if (!isset($agerange)) {$agerange = "0:119";}
	
	//print_r($_FILES);
		
	$day = date("d",$t);
	$month = date("m",$t);
	$year = date("Y",$t);
	//$hour = $_REQUEST["hour"];
	//$minute = $_REQUEST["minute"];
	$eventtime = $year.$month.$day.$hour.$minute;
	$eventtime = str_pad($eventtime, 14 , "00");
	//$addevent = sqlinteger($addevent);
	$event_name = $DB->escape($_REQUEST["event_name"]);
	
	if ($_REQUEST["location"] == "other")
	{
	$event_location = $DB->escape($_REQUEST["event_location"]);	
	}
	else 
	{
	$event_location = $DB->escape($_REQUEST["location"]);		
	}
	
	
	

	$event_cost = $DB->escape($_REQUEST["event_cost"]);
	$event_description = $DB->escape($_REQUEST["event_description"]);
        $tags = $DB->escape($_REQUEST["tags"]);
	$event_staffnotes = $DB->escape($_REQUEST["event_staffnotes"]);
	$trainer = $DB->escape($_REQUEST["trainer"]);
        $trainerEmail = $DB->escape($_REQUEST["trainerEmail"]);
	$maxbookings = $_REQUEST["maxbookings"];
	$template = $_REQUEST["template"];
	$event_catid = $_REQUEST["event_catid"];
	$recurrent = $_REQUEST["recurrent"];
	$recur_num = $_REQUEST["recur_num"];
	$interval = $_REQUEST["interval"];
	
	//if ($template == 1) {$eventgroup = 0;}
	
	if (!isinteger($eventtime) || !isinteger($maxbookings) || !isinteger($template) || !isinteger($event_catid))
	{$ERROR = "$phrase[72]";}
	
	$counter = 1;
	
	  if (isset($recur_num) && (!is_numeric($recur_num) || ($recur_num > 52)))
					  {
					  $ERROR = $phrase[268];
					  
					}
	if ($interval == "monthlybyweekday")
                                {					  
                                $weekcounter = 0;
                                
                                 $daysinmonth = date("t",$t);
                                
                                 $daynum = date("j",$t);
                                $weekday = date("D",$t);
                           
								for($x=1;$x < $daysinmonth + 1;$x++)
								
								{
								
								$dayname =  date("D",mktime (0,0,0,$month,$x,$year));

								 if($dayname == $weekday)
 								{
								 
								 $weekcounter ++;	
 								}
								
								if ($daynum == $x) break;

								}
                                	
                                		
                                }

                                

   
                                
                                

	if ($recurrent == "no") {$recur_num = 1;}
	
                        while ($counter <= $recur_num)
                            {
                              if ($counter == 1)
                                {
                                $array_eventtime[$counter] = $eventtime;
                                //$array_start[$counter] = $t;
                                $array_start[$counter] =  mktime ($hour,$minute,0,$month,$day,$year);
                                $array_end[$counter] =  mktime ($fhour,$fminute,0,$month,$day,$year);
                              //  echo "$fhour,$fminute,0,$month,$day,$year<br>" . $array_end[$counter];
                              }
                              else
                              {
                                $previous = $counter - 1;
                                
                                 $startminute = date("i",$array_start[$previous]);
                                $starthour = date("H",$array_start[$previous]);
                                $startday = date("d",$array_start[$previous]);
                                $startmonth = date("m",$array_start[$previous]);
                                $startyear = date("Y",$array_start[$previous]);



                                 if ($interval == "day")
                                {
                             $array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth,$startday +1,$startyear);
                             $array_end[$counter] =  mktime ($fhour,$fminute,0,$startmonth,$startday +1,$startyear);
                             $array_eventtime[$counter] = date("YmdHis",$array_start[$counter]);
                            
                                }
                            elseif ($interval == "week")
                                {
                             $array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth,$startday +7,$startyear);
                             $array_end[$counter] =  mktime ($fhour,$fminute,0,$startmonth,$startday +7,$startyear);
                            $array_eventtime[$counter] = date("YmdHis",$array_start[$counter]);
                                }
                            elseif ($interval == "fortnight")
                                {
                             $array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth,$startday +14,$startyear);
                             $array_end[$counter] =  mktime ($fhour,$fminute,0,$startmonth,$startday +14,$startyear);
                           $array_eventtime[$counter] = date("YmdHis",$array_start[$counter]);
                                }
                                
                               elseif ($interval == "monthlybydate")
            					{
            					$array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth + 1,$startday,$startyear);
                                                $array_end[$counter] =  mktime ($fhour,$fminute,0,$startmonth + 1,$startday,$startyear);
            					$array_eventtime[$counter] = date("YmdHis",$array_start[$counter]);
           						 }	
                            elseif ($interval == "monthlybyweekday")
                                {
                             /////////////
                             
                            
                            
                               
								if ($startmonth == 12)
									{
									$startyear = $startyear + 1;
									//$endyear = $endyear + 1;
									$startmonth = 1;
									//$endmonth = 1;
									}	
								else {
									 $startmonth = $startmonth + 1;
									//$endmonth = $endmonth + 1; 
								
									}
                                $nextmonthtimestamp =  mktime (0,0,0,$startmonth,1,$startyear); 
                                $daysinmonth = date("t",$nextmonthtimestamp);	
                                $match = 0;
                                
								
                                	for($x=1;$x < $daysinmonth + 1;$x++)

										{
										
										$test =  date("D",mktime (0,0,0,$startmonth,$x,$startyear));

										 if($test == $weekday)
 										{
								 
										 $match++;	
										
 										}
										//echo "$test $weekday $weekdaycounter match is $match $x $startmonth $startyear<br>";
										if ($weekcounter == $match) break;

										}
										
										if (!($match < $weekcounter))
										{
                            
                            
                             $array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth,$x,$startyear);
                             $array_end[$counter] =  mktime ($fhour,$fminute,0,$startmonth,$x,$startyear);
                             //$array_eventtime[$counter] = date("YmdHis",$array_start[$counter]);
										}
										else 
										{
										$nosuchday[$counter] = "yes";
										 $array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth,1,$startyear);
                                                                                 $array_end[$counter] =  mktime ($fhour,$fminute,0,$startmonth,1,$startyear);
                             			//$array_eventtime[$counter] = date("YmdHis",$array_start[$counter]);
                             
                             
										
										}
                             //////////
                            
                                }

                            

                              }
                              $counter++;
                            }
	
                            
                           // print_r($_REQUEST);
                            
                             //check for manual dates
                             if (isset($_REQUEST["dates"]))
                             {
                             	foreach($_REQUEST["dates"] as $index => $value)
                             	{
                             		$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
                             		if (preg_match ($pattern ,$value))
                             		{
                             			if ($DATEFORMAT == "%d-%m-%Y")
                             			{
                             				$sd = substr($value,0,2);
                             				$sm = substr($value,3,2);
                             				$sy = substr($value,6,4);

                             			}

                             			if ($DATEFORMAT == "%m-%d-%Y")
                             			{
                             				$sd = substr($value,3,2);
                             				$sm = substr($value,0,2);
                             				$sy = substr($value,6,4);
                             			}
							//echo "value is $value sd is $sd sm is $sm sy is $sy";
						    $array_start[$counter] =  mktime ($hour,$minute,0,$sm,$sd,$sy);
                            $array_eventtime[$counter] = date("YmdHis",$array_start[$counter]);

                             		}





                             		$counter++;
                             	}

                             }
	
	
	if (!isset($ERROR))
	{
	
	   if ($recurrent == "no")
                        {
                        $eventgroup = 0;
                        }
		   else {
		   	$sql = "select max(eventgroup) as eventgroup from cal_events";
		   	 $DB->query($sql,"resourcebooking.php");
		   	 $row = $DB->get();
			$eventgroup =$row["eventgroup"] + 1;	

		   }

	if (isset($array_start))
	{
	foreach($array_start as $index => $value)
							{
				if (isset($nosuchday) && $nosuchday[$index] == "yes")
							{
							
							//$timestamp = $array_start[$index];
							$addresult[$value] = "$phrase[267]";
							
							}
				else 
				{
					
					
					
					
					
	$sql = "insert into cal_events  values 
	(NULL,'$event_name','$event_location','$event_catid','$event_cost','$event_description','$event_staffnotes','0','$template', '$trainer','$maxbookings','$eventgroup','$value','$agerange','$tags','$array_end[$index]','$trainerEmail')";
	$DB->query($sql,"editcalendar.php");
	//echo $sql;
	if (isset($_REQUEST["addtemplate"]) && $_REQUEST["addtemplate"] == "yes")
	{ $addresult[$array_start[$index]] = $phrase[627]; }
	else 
	{ $addresult[$array_start[$index]] = $phrase[624]; }	
	
	$event_id = $DB->last_insert();
	
	
	
	//print_r($_FILES);
	
		
	
	
		 if (!isset($_REQUEST["addtemplate"]) && isset($_FILES["upload"]["size"][0] ) && $_FILES["upload"]["size"][0] > 0)
	 
	 {
	 	//echo "uploading image  ";
	 	upload($m,$event_id,'0',$PREFERENCES,$DB,"calimage",$ip,$phrase);	
	 
	 }
	
	
	}
	
				
	} //ends foreach
	}
	}
	}
	
	
	

	
if (isset($_REQUEST["updateevent"]))
	{
	
	//print_r($_FILES);
	//print_r($_REQUEST);
	
	
	$now = time();
	$ip = ip("pc");
	
	
	
	
	
	//$eventtime = $year.$month.$day.$hour.$minute."00";
	$event_start =  mktime ($hour,$minute,0,$month,$day,$year);
	//$displaytime = strftime("%d %a %B",$eventtime);
	
	$event_name = $DB->escape($_REQUEST["event_name"]);
	
	
		if ($_REQUEST["location"] == "other")
	{
	$event_location = $DB->escape($_REQUEST["event_location"]);	
	}
	else 
	{
	$event_location = $DB->escape($_REQUEST["location"]);		
	}
	
	
	
	
	$event_type = $_REQUEST["event_type"];
	$event_cost = $DB->escape($_REQUEST["event_cost"]);
	$event_description = $DB->escape($_REQUEST["event_description"]);
	$event_staffnotes = $DB->escape($_REQUEST["event_staffnotes"]);
        $tags = $DB->escape($_REQUEST["tags"]);
	$trainer = $DB->escape($_REQUEST["trainer"]);
        $trainerEmail = $DB->escape($_REQUEST["trainerEmail"]);
	$maxbookings = $_REQUEST["maxbookings"];
	$cancelled = $DB->escape($_REQUEST["cancelled"]);
	$eventgroup = $DB->escape($_REQUEST["eventgroup"]);
	
	
	if (isset($_REQUEST["minage"]) && $_REQUEST["maxage"])
	{
	//$minage = trim($_REQUEST["minage"]);
	//$maxage = trim($_REQUEST["maxage"]);
	

	//settype($minage, "integer"); 
	//settype($maxage, "integer"); 
	
	if (($maxage > $minage) && ($minage > 0 && $minage < 120) && ($maxage > 1 && $maxage < 120))	
		{
		$agerange = $minage . ":" . $maxage;
		$ageinsert = ", age_range = '$agerange' ";
		
		
		} 
		else {$ageinsert = "";}
		
	} else {$ageinsert = "";}
	
	

	
	
	if (isset($_REQUEST["cancelled"])) {$cancelled = $DB->escape($_REQUEST["cancelled"]);}
	
	if (!isinteger($event_start) || !isinteger($maxbookings) || !isinteger($event_type))
	{$ERROR = "$phrase[72]";
	
	}
	
		if ($eventgroup > 0)
       	{
       		if ($DB->type == "mysql")
       		{
       		$sql = "select *, day(FROM_UNIXTIME(event_start)) as day, month(FROM_UNIXTIME(event_start)) as month from cal_events where eventgroup = '$eventgroup'";
       		}
       		else
       		{
       		$sql = "select *, strftime('%d',event_start) as day, strftime('%m',datetime ( event_start , 'unixepoch' ))) as month from cal_events where eventgroup = '$eventgroup'";
       		}
       		
       		
       		 $DB->query($sql,"editcalendar.php");
			while ($row = $DB->get()) 
		{
			$event[] =$row["event_id"];
			$day = $row["day"];
			$month = $row["month"];
			$day = str_pad($day, 2, "0", STR_PAD_LEFT);
			$month = str_pad($month, 2, "0", STR_PAD_LEFT);
			//$time[] = $year.$month.$day.$hour.$minute."00";
			$time[] = mktime ($hour,$minute,0,$month,$day,$year);
                        if (isset($fhour)) {$endtime[] = mktime ($fhour,$fminute,0,$month,$day,$year);} else {$endtime[] = 0;}
		}
	
	
       	}
       	else 
       	{
       		
       	$sql = "select * from cal_events where event_id = '$event_id'";
       	 $DB->query($sql,"editcalendar.php");
			$row = $DB->get();
	
			$event[] =$row["event_id"];	
			//$day = str_pad($day, 2, "0", STR_PAD_LEFT);
			//$month = str_pad($month, 2, "0", STR_PAD_LEFT);
			//$time[] = $year.$month.$day.$hour.$minute."00";
			$time[] = mktime ($hour,$minute,0,$month,$day,$year);
                         if (isset($fhour)) {$endtime[] = mktime ($fhour,$fminute,0,$month,$day,$year);} else {$endtime[] = 0;}
       	
       	}
	
       	
     
       	
       	//echo $sql;
		if (!isset($ERROR))
		{
			
		foreach($event as $index => $id)
							{
							
								
							if ($cancelled ==2)
							//delete
							{
							
	$sql = "select count(*) as bookings from cal_bookings where eventno = '$id' and status = '1'";
	$DB->query($sql,"editcalendar.php");
	$row = $DB->get();
	$bookings = $row["bookings"];

	if ($bookings > 0) {$deletefailure[] = "$displaytime $event_name ($bookings)";} 
	else {
		$sql = "delete from  cal_events where event_id = '$id'";
		$DB->query($sql,"editcalendar.php");
		
		//delete any uploaded images
		$sql = "delete from images where page = '$id' and modtype = 'c'"; 
		$DB->query($sql,"editcalendar.php");	  
	
		
		deletecalfolder($id,$PREFERENCES);
		//deletecalimage($image_id,$m,$PREFERENCES,$DB);
		
		
		}
								
									
							}
							else 
							{	
	$sql = "update cal_events set event_name = '$event_name', event_location = '$event_location', event_catid = '$event_type', event_cost = '$event_cost',
       event_description = '$event_description' , event_staffnotes = '$event_staffnotes', event_start= '$time[$index]',event_finish= '$endtime[$index]', cancelled = '$cancelled', trainer = '$trainer', 
     maxbookings = '$maxbookings', trainerEmail = '$trainerEmail', tags = '$tags' $ageinsert where event_id = '$id'";
	//echo "$sql <br>";
	$DB->query($sql,"editcalendar.php");
	

	//echo "<h1>updating event</h1>";
	
	//print_r($_FILES);
	//upload image
	 if (isset($_FILES["upload"]["size"][0] ) && $_FILES["upload"]["size"][0] > 0)
	 
	 {
	 
	//echo "uploading";
	 	
	 		//delete any uploaded images
		$sql = "select image_id from images where page = '$id' and modtype = 'c' "; 
		$DB->query($sql,"editcalendar.php");	  
		while ($row = $DB->get())
		{
		$image_id = $row["image_id"];	
	deletecalimage($image_id,$m,$PREFERENCES,$DB);
		}
		

			
		
	 	
	 	//echo "uploading image";
	 	upload($m,$id,'0',$PREFERENCES,$DB,"calimage",$ip,$phrase);	
	 
	 }
	
	
	
	
	
	
	
							}
							}
	}
		
	}
	
	
	
	
	
		$branches = array();
				
				$sql = "select bname, branchno from cal_branches order by bname";
			
				$DB->query($sql,"editcalendar.php");
				while ($row = $DB->get()) 
				{
				$_bname = $row["bname"];
				$_bno = $row["branchno"];
				$branches[$_bno] = $_bname;		
					
				}
				
				
				
				
				$locations = array();
				
				$sql = "select location from cal_branch_bridge where module = '$m'";
				//echo $sql;
				$DB->query($sql,"editcalendar.php");
				while ($row = $DB->get()) 
				{
				$locations[] = $row["location"];
				}
	
	
	
	
	
	
	
	
	
	echo "<h1 class=\"red\">$modname</h1>";

if (isset($ERROR))
{
	echo $ERROR;
}
	


	
	
	
elseif (isset($addresult))
{
		if (isset($_REQUEST["addtemplate"]) && $_REQUEST["addtemplate"] == "yes")
		{
		$insert = $phrase[627];
		}
	else 
		{
		$insert = $phrase[624];
		}
	
	echo "
	
	<a href=\"editcalendar.php?m=$m&amp;t=$t\">$phrase[118]</a>

	<h2>$insert</h2><br><span style=\"font-size:2em\">$event_name</span><br><br><ul style=\"margin-left:50%\">";
	
	
		if (!isset($_REQUEST["addtemplate"]) )
		{
    	
    	  foreach($addresult as $index => $result)
						

              {
              	
				if ($result == "none")
				{
					$month = strftime("%B", $index);
					$year = date("Y", $index);
				echo "<li>$phrase[270] $month $year</li> ";
				}
				else 
				{ 	
					
				$date = strftime("%x", $index);
				$day = strftime("%A", $index);
				$time = date("g:i A", $index);
				echo "<li>$result $time $day $date</li> ";}
              	
               }
               
		} 
           echo "</ul><br>
              <form method=\"post\" action=\"editcalendar.php\">
            <input type=\"hidden\" name=\"m\" value=\"$m\">
            <input type=\"hidden\" name=\"t\" value=\"$t\">
           <input type=\"submit\" name=\"submit\" value=\"$phrase[34]\">
           </form>";
}


elseif(isset($deletefailure))
		{
			echo "
				<span style=\"color:red;font-size:1.5em;\">$phrase[625]</span><br><br>
			<ul  style=\"margin-left:50%\">";
		foreach($deletefailure as $index => $result)
						

              {
              	
				
				echo "<li>$result</li> ";
				
				}
			echo "</ul>
			<br>
              <form method=\"post\" action=\"editcalendar.php\">
            <input type=\"hidden\" name=\"m\" value=\"$m\">
            <input type=\"hidden\" name=\"t\" value=\"$t\">
           <input type=\"submit\" name=\"submit\" value=\"$phrase[34]\">
           </form>";

		}
		
		
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteimage")
{
	echo "<br><b>$phrase[14]</b><br><br> $_REQUEST[name]<br><br>
	<a href=\"editcalendar.php?m=$m&amp;update=deleteimage&amp;event_id=$event_id&image_id=$image_id\">$phrase[12]</a> | <a href=\"editcalendar.php?m=$m\">$phrase[13]</a>";
	
}	
		
		
		
		
	elseif(isset($_GET["event"]) && $_GET["event"] == "all")
		{
		
			$t = time();
			
				if (isset($_REQUEST["year"]))
					{ $year = $DB->escape($_REQUEST["year"]);
						
						} 
			else 	{ 
					$year = date("Y"); 
				
					}	
			$next = $year + 1;
			$previous = $year -1;
			
			
	
			
			 //get list of usages for this calendar
		$where = " and event_catid IN ("; 
		$typecount = 1;
		$sql = "SELECT cat FROM cal_bridge where m = '$m'";
		$DB->query($sql,"editcalendar.php");
		
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
				$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_name, cat_colour, cat_waitinglist, cat_takesbookings, event_start FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and year(FROM_UNIXTIME(event_start)) = '$year' and template = '0' and event_catid $where order by cat_name, event_start";	
		$DB->query($sql,"editcalendar.php");
       		}
       		
       		
       		else
       		{
				$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_name, cat_colour, cat_waitinglist, cat_takesbookings, event_start FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and strftime ( '%Y' , datetime ( event_start , 'unixepoch' ) ) = '$year'  and template = '0' and event_catid $where order by cat_name, event_start";	
		$DB->query($sql,"editcalendar.php");
       		}
		
			while ($row = $DB->get()) 
					{
					$array_id[] =$row["event_id"];
					$array_eventname[] =$row["event_name"];	
				
					$array_location[] =$row["event_location"];
					$array_day[] = strftime("%x", $row["event_start"]);	
					$array_start[] = $row["event_start"];
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
					$where  and year(FROM_UNIXTIME(event_start)) = '$year'
					group by event_id";
       		}
			
       		
       			else
       		{
			$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					$where  and strftime ( '%Y' , datetime ( event_start , 'unixepoch' ) ) = '$year' 
					group by event_id";
       		}
			
			$DB->query($sql,"editcalendar.php");
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
					$where  and year(FROM_UNIXTIME(event_start)) = '$year'
					group by event_id";
       		}
       		
       			else
       		{
			$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status =2
					$where  and strftime ( '%Y' , datetime ( event_start , 'unixepoch' ) ) = '$year' 
					group by event_id";
       		}
			
			$DB->query($sql,"editcalendar.php");
			while ($row = $DB->get()) 
					{
					$wait_eventid[] = $row["event_id"];
					$wait_total[] = $row["total"];
					
					}		
					
					
					
			echo "<a href=\"editcalendar.php?m=$m\">$phrase[118]</a> | <a href=\"editcalendar.php?m=$m&amp;event=search\">$phrase[282]</a>
			 
			
 | <a href=\"editcalendar.php?m=$m&amp;event=templates&amp;t=$t\">$phrase[302]</a> | <a href=\"editcalendar.php?m=$m&amp;t=$t&amp;event=stats\">$phrase[315]</a> 
			
			
			<br><br> 
			
			<a href=\"editcalendar.php?m=$m&amp;event=all&amp;year=$previous\" style=\"margin-right:4em\">$phrase[525]</a> 
				
	
			<span style=\"font-size:2em\">$year</span> <a href=\"editcalendar.php?m=$m&amp;event=all&amp;year=$next\" style=\"margin-left:4em\">$phrase[526] </a> 
		
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
					<tr style=\"font-weight:bold\"><td>$phrase[311]</td><td>$phrase[121]</td><td>$phrase[186]</td><td style=\"width:6em\">$phrase[185]</td>";
				
				if ($array_takesbookings[$index] == 1)
				{
					
				echo "<td>$phrase[140]</td><td>$phrase[127]</td>";
				if ($array_waitinglist[$index] == 1)
				{
				
				echo "<td>$phrase[148]</td>";
				}
				}
				echo "</tr>";
				
			}
			
			echo "<tr style=\"color:$array_colour[$index]\">
			<td><a href=\"editcalendar.php?m=$m&amp;event=edit&amp;event_id=$array_id[$index]&amp;t=$array_start[$index]\" style=\"color: $array_colour[$index]\">$array_eventname[$index]</a></td>
			<td>";
			
				$_location = $array_location[$index];
					
					if (key_exists($_location,$branches)) {echo "$branches[$_location]<br>";}
					else {
					
						if ($array_location[$index] != "") {echo "$array_location[$index]<br> ";}
						}			
			
			
			
			echo "</td>
			<td>$array_day[$index]</td>
			<td>$array_time[$index]</td>";

			if ($array_takesbookings[$index] == 1)
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








	elseif(isset($_GET["event"]) && $_GET["event"] == "search")
		{
	$t = time();
	
		echo "
		
<a href=\"editcalendar.php?m=$m\">$phrase[118]</a> | <a href=\"editcalendar.php?m=$m&amp;event=all\">$phrase[676]</a>
 | <a href=\"editcalendar.php?m=$m&amp;event=templates&amp;t=$t\">$phrase[302]</a> | <a href=\"editcalendar.php?m=$m&amp;t=$t&amp;event=stats\">$phrase[315]</a>
<br><br>    <script type=\"text/javascript\">
										function setfocus()
										{
										document.getElementById(\"focusfield\").focus()
										}
										window.onload=setfocus
										</script>";
		if ($DB->type == "mysql")
       		{
		$sql = "SELECT year(FROM_UNIXTIME(event_start)) AS yeargroup FROM cal_events GROUP BY yeargroup order by yeargroup desc";
       		}
	
       		
       		else
       		{
		$sql = "SELECT strftime('%Y',datetime ( event_start , 'unixepoch' )) AS yeargroup FROM cal_events GROUP BY yeargroup order by yeargroup desc";
       		}
       
       		
		$DB->query($sql,"editcalendar.php");
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
		
		 
		echo "<br><form action=\"editcalendar.php?m=$m&amp;=event=search\" method=\"get\">
		<input type=\"text\" name=\"keywords\" id=\"focusfield\">
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
		<input type=\"submit\" name=\"submit\" value=\"$phrase[282]\">
		
		
		</form>";
		}
		
		if (isset($_REQUEST["keywords"])) {
		
			
			
				 //get list of usages for this calendar
		$where = " and event_catid IN ("; 
		$typecount = 1;
		$sql = "SELECT cat FROM cal_bridge where m = '$m'";
		$DB->query($sql,"editcalendar.php");
		
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
		
		
		
		
		
		
		echo "<h2>Search results \"$_REQUEST[keywords]\" $year</h2>";
		
		if ($DB->type == "mysql")
       		{
		
				$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_name, cat_colour, cat_waitinglist, cat_takesbookings, event_start FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and year(FROM_UNIXTIME(event_start)) = '$year' and MATCH (event_name,event_description,event_location,tags) AGAINST ('$keywords' IN BOOLEAN MODE) and template = '0' and event_catid $where $locationinsert order by cat_name, event_start";	
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
				
				if ($keywords == "") {$string = " and 1 == 2";}
       			
				$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_name, cat_colour, cat_waitinglist, cat_takesbookings, event_start 
				FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and strftime ( '%Y' , datetime ( event_start , 'unixepoch' ) ) = '$year'  
				$string
				and template = \"0\" and event_catid $where $locationinsert order by cat_name, event_start";	
       		}
       			
       		//echo $sql;
				$DB->query($sql,"editcalendar.php");
		
	
			while ($row = $DB->get()) 
					{
					$array_id[] =$row["event_id"];
					$array_eventname[] =$row["event_name"];
					$array_time[] =$row["event_start"];
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
					$where  and year(FROM_UNIXTIME(event_start)) = '$year'
					group by event_id";
       		}
			
       			else
       		{
			$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = 1
					$where  and strftime ( '%Y' , datetime ( event_start , 'unixepoch' ) ) = '$year'
					group by event_id";
       		}
       		
			$DB->query($sql,"editcalendar.php");
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
					$where  and year(FROM_UNIXTIME(event_start)) = '$year'
					group by event_id";
       		}
       		
       			else
       		{
		
			$sql = "SELECT count(*) as total, cal_events.event_id from cal_events, cal_bookings
 					where cal_bookings.eventno = cal_events.event_id and cal_bookings.status =2
					$where  and strftime ( '%Y' , datetime ( event_start , 'unixepoch' ) ) = '$year' 
					group by event_id";
       		}
       		
       		
			
			$DB->query($sql,"editcalendar.php");
			while ($row = $DB->get()) 
					{
					$wait_eventid[] = $row["event_id"];
					$wait_total[] = $row["total"];
					
					}		
					
					
//					
//			echo "<a href=\"calendar.php?m=$m\">$phrase[118]</a>  | <a href=\"calendar.php?m=$m&amp;event=search\">$phrase[282]<br><br> 
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
				
				if ($array_takesbookings[$index] == 1)
				{
					
				echo "<td>$phrase[140]</td><td>$phrase[127]</td>";
				if ($array_waitinglist[$index] == 1)
				{
				
				echo "<td>$phrase[148]</td>";
				}
				}
				echo "</tr>";
				
			}
			
			echo "<tr style=\"color:$array_colour[$index]\"><td><a href=\"editcalendar.php?m=$m&amp;event=edit&amp;event_id=$array_id[$index]&amp;t=$array_time[$index]\" style=\"color: $array_colour[$index]\">$array_eventname[$index]</a></td><td>";
			
			
				$_location = $array_location[$index];
					
					if (key_exists($_location,$branches)) {echo "$branches[$_location]<br>";}
					else {
					
						if ($array_location[$index] != "") {echo "$array_location[$index]<br> ";}
						}			
			
			
			echo "</td><td>$array_day[$index]</td><td>$array_time[$index]</td>";

			if ($array_takesbookings[$index] == 1)
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
	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "stats")

	{		
		if (isset($_REQUEST["t"]))
		{ $t = $_REQUEST["t"];} else 	{$t = time();	
	}
	if (isset($_REQUEST["month"]))
		{

	$_t = mktime(0, 0, 0, $month, 1,  $year);
	$monthname = strftime("%B",$_t);
			
			
			
//$month = date("n",$t);

//$year = date("Y",$t); 	

 
	if ($DB->type == "mysql")
       		{
		
$sql = "select count(*) as number, cal_cat.cat_name from cal_bookings, cal_cat, cal_events where cal_bookings.status = '1' and cal_bookings.eventno = cal_events.event_id and cal_events.event_catid = cal_cat.cat_id and month(FROM_UNIXTIME(event_start)) = '$month' and year(FROM_UNIXTIME(event_start)) = '$year' group by cat_name";
       		}
       		
    else
       		{
		
$sql = "select count(*) as number, cal_cat.cat_name as cat_name from cal_bookings, cal_cat, cal_events where cal_bookings.status = '1' and cal_bookings.eventno = cal_events.event_id and cal_events.event_catid = cal_cat.cat_id and strftime('%m',datetime ( event_start , 'unixepoch' )) = '$month' and strftime ( '%Y' , datetime ( event_start , 'unixepoch' ) ) = '$year'  group by cat_name";
       		} 		
       		
      
	$DB->query($sql,"editcalendar.php");
			
		echo "
		
			<a href=\"editcalendar.php?m=$m\">$phrase[118]</a> | <a href=\"editcalendar.php?m=$m&amp;event=all\">$phrase[676]</a> | <a href=\"editcalendar.php?m=$m&amp;event=search\">$phrase[282]</a>
 | <a href=\"editcalendar.php?m=$m&amp;event=templates&amp;t=$t\">$phrase[302]</a> | <a href=\"editcalendar.php?m=$m&amp;t=$t&amp;event=stats\">$phrase[315]</a> 
		<h2>$phrase[271]</h2><br>

		<b><span style=\"font-size:2em\">$monthname $year</span></b><br><br>
		<table class=\"colourtable\" cellpadding=\"5\"  style=\"margin-left:auto;margin-right:auto\" cellspacing=\"5\">
		<tr><td colspan=\"2\"><b>By event type</b></td></tr>";
		while ($row = $DB->get()) 
		{
		
		$number = $row["number"];
		$cat_name = $row["cat_name"];
		
		echo  "<tr><td>$cat_name</td><td> $number</td></tr>";
		}
		echo "</table>";
		
			if ($DB->type == "mysql")
       		{
		
$sql = "select count(*) as number, username from cal_bookings,cal_events  where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = '1' and month(FROM_UNIXTIME(event_start)) = '$month' and year(FROM_UNIXTIME(event_start)) = '$year' group by username";
       		}
       		
     else
       		{
		
$sql = "select count(*) as number, username from cal_bookings,cal_events  where cal_bookings.eventno = cal_events.event_id and cal_bookings.status = '1' and strftime('%m',datetime ( event_start , 'unixepoch' )) = '$month' and strftime ( '%Y' , datetime ( event_start , 'unixepoch' ) ) = '$year'  group by username";
       		} 		
       		$DB->query($sql,"editcalendar.php");	
       		
       		
      
	echo "<table class=\"colourtable\" cellpadding=\"5\"  style=\"margin:2em auto\" cellspacing=\"5\">
	<tr><td colspan=\"2\"><b>By username</b></td></tr>
	";
		while ($row = $DB->get()) 
		{
		
		$number = $row["number"];
		$username = $row["username"];
		
		echo  "<tr><td>$username</td><td> $number</td></tr>";
		}
		echo "</table>";
		
	
		}
		else
			{
			echo "
			<a href=\"editcalendar.php?m=$m\">$phrase[118]</a>  | <a href=\"editcalendar.php?m=$m&amp;event=all\">$phrase[676]</a>	
		 | <a href=\"editcalendar.php?m=$m&amp;event=search\">$phrase[282]</a>
			
 | <a href=\"editcalendar.php?m=$m&amp;event=templates&amp;t=$t\">$phrase[302]</a> 
			<h2>$phrase[271]</h2>
			
			<br>
			<ul class=\"listingcenter\">";
			
			
			
	if ($DB->type == "mysql")
       		{
	
	$sql = "select month(FROM_UNIXTIME(event_start)) as monthnumber, year(FROM_UNIXTIME(event_start)) as year from cal_bookings, cal_events where cal_bookings.eventno = cal_events.event_id group by year desc, monthnumber desc";	
       		}	
	
	else
       		{
	
	$sql = "select strftime('%m',datetime ( event_start , 'unixepoch' )) as monthnumber,   strftime('%Y',datetime ( event_start , 'unixepoch' )) as year from cal_bookings, cal_events where cal_bookings.eventno = cal_events.event_id group by year, monthnumber order by year DESC";	
       		}	
    		
     //echo $sql;  	
	$DB->query($sql,"editcalendar.php");
			
		$num= $DB->countrows();
		//$count = 0;
		while ($row = $DB->get()) 
		{
		
		$month = $row["monthnumber"];
		$month = str_pad($month, 2, "0", STR_PAD_LEFT);
		$year = $row["year"];
		$monthname = strftime("%B",mktime(0,0,0,$month,01,$year));
		
        
		echo "<li> <a href=\"editcalendar.php?m=$m&amp;event=stats&amp;month=$month&amp;year=$year&amp;monthname=$monthname&amp;view=month\">$monthname $year</a></li>";
		}
		
		echo "</ul><br><br><br>";
			}
			
		
		}


		
		
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit")

	{		
		
			
		$sql = "SELECT cat_name,cat_id,cat_age, cat_colour,cat_cost,cat_notes, cat_takesbookings, tags, event_id,event_finish, eventgroup, maxbookings, event_name, age_range, event_cost, 
                    event_location,event_description, event_staffnotes, cancelled, cat_trainer, trainer,event_start,trainerEmail   FROM cal_events, cal_cat  
                    where cal_cat.cat_id = cal_events.event_catid and cal_events.event_id = '$event_id'";
		//echo $sql;
		$DB->query($sql,"editcalendar.php");
			$row = $DB->get();
			
		$event_id = $row["event_id"];
		$cat_name = formattext($row["cat_name"]);
		$cat_id = $row["cat_id"];
		$cat_colour = $row["cat_colour"];
		$event_name = formattext($row["event_name"]);
		$agerange = $row["age_range"];
		$event_location = formattext($row["event_location"]);
		$event_cost = formattext($row["event_cost"]);
		$event_description = $row["event_description"];
		$cat_cost = $row["cat_cost"];
		$cat_notes = $row["cat_notes"];
		$event_staffnotes = $row["event_staffnotes"];
		$cancelled = $row["cancelled"];
		$minute = date("i",$row["event_start"]);
		$hour = date("G",$row["event_start"]);
                $fminute = date("i",$row["event_finish"]);
		$fhour = date("G",$row["event_finish"]);
                $event_finish = $row["event_finish"];
		$day = date("d",$row["event_start"]);
		$month = date("n",$row["event_start"]);
		$year = date("Y",$row["event_start"]);
		$eventgroup = $row["eventgroup"];
                $tags = $row["tags"];
		$maxbookings = $row["maxbookings"];
		$trainer = formattext($row["trainer"]);
		$cat_trainer = $row["cat_trainer"];
		$cat_age = $row["cat_age"];
		$cat_takesbookings = $row["cat_takesbookings"];
                $trainerEmail = $row["trainerEmail"];
		
		$total = 0;
	if ($eventgroup > 0)
	{
	$sql = "select * from cal_events where eventgroup = '$eventgroup'";
		$DB->query($sql,"cal_events.php");
		while ($row = $DB->get())
		{
	
		$total++;
		}
	}
		
		
		
		
		
		
		$months[1] = strftime("%b",mktime(01,01,01,1,01,2000)); 
		$months[2] = strftime("%b",mktime(01,01,01,2,01,2000)); 
		$months[3] = strftime("%b",mktime(01,01,01,3,01,2000)); 
		$months[4] = strftime("%b",mktime(01,01,01,4,01,2000)); 
		$months[5] = strftime("%b",mktime(01,01,01,5,01,2000)); 
		$months[6] = strftime("%b",mktime(01,01,01,6,01,2000)); 
		$months[7] = strftime("%b",mktime(01,01,01,7,01,2000)); 
		$months[8] = strftime("%b",mktime(01,01,01,8,01,2000)); 
		$months[9] = strftime("%b",mktime(01,01,01,9,01,2000)); 
		$months[10] = strftime("%b",mktime(01,01,01,10,01,2000)); 
		$months[11] = strftime("%b",mktime(01,01,01,11,01,2000)); 
		$months[12] = strftime("%b",mktime(01,01,01,12,01,2000));
		
	//print_r($months);
	
		if (isset($_REQUEST["edittemplate"])) { $edittemplate = $_REQUEST["edittemplate"];}
		
		
	
		
		
			
		
		echo "
		
 <a href=\"editcalendar.php?m=$m&amp;t=$t\">$phrase[118]</a> | 	 <a href=\"editcalendar.php?m=$m&amp;event=all\">$phrase[676]</a> | <a href=\"editcalendar.php?m=$m&amp;event=search\">$phrase[282]</a>  | <a href=\"editcalendar.php?m=$m&amp;event=templates&amp;t=$t\">$phrase[302]</a> | <a href=\"editcalendar.php?m=$m&amp;t=$t&amp;event=stats\">$phrase[315]</a>
		<h2 style=\"color:$cat_colour\">$cat_name</h2>
			
				
				";
		$displaydate = strftime("%A %x", $t);
		
		echo "<FORM method=\"POST\" enctype=\"multipart/form-data\" action=\"editcalendar.php\"><fieldset style=\"margin-left:auto;margin-right:auto;width:80%\"><legend>";
		if (isset($edittemplate) && $edittemplate == "yes")
			{ echo "$phrase[312]"; }
		else	{echo "$phrase[313]"; }
		echo "</legend><table style=\"margin-left:auto;margin-right:auto\" >
	
		<tr><td align=\"right\"><b>$phrase[311]</b></td><td align=\"left\"><input type=\"text\" name=\"event_name\" value=\"$event_name\" size=\"50\" maxlength=\"100\" ></td></tr>
		<tr><td align=\"right\"><b>$phrase[121]</b></td>
		
		<td align=\"left\">";
		
		$branches = array();
				
				$sql = "select bname, branchno from cal_branches order by bname";
			
				$DB->query($sql,"editcalendar.php");
				while ($row = $DB->get()) 
				{
				$_bname = $row["bname"];
				$_bno = $row["branchno"];
				$branches[$_bno] = $_bname;		
					
				}
				
				
				
				
				$locations = array();
				
				$sql = "select location from cal_branch_bridge where module = '$m'";
				//echo $sql;
				$DB->query($sql,"editcalendar.php");
				while ($row = $DB->get()) 
				{
				$locations[] = $row["location"];
				}
				
			
				
				$counter = 1;
				$matches = 0;
				
			
				
				
				foreach ($branches as $bno => $bname)
						{
							if (in_array('0',$locations) || in_array($bno,$locations))
							{
							$id = "b" . $counter;
							echo "<input type=\"radio\" name=\"location\" value=\"$bno\" id=\"$id\" ";
							if ($event_location == $bno) { echo " checked"; $matches++;}
							echo "> <label for=\"$id\">$bname</label><br>";
							$counter++;
							}
						}
		
		if (in_array('0',$locations))
		{
		$id = "b" . $counter;
		echo "<input type=\"radio\" name=\"location\" value=\"other\" id=\"$id\"";
		if ($matches == 0) {echo " checked";}
		echo "> <label for=\"$id\">Other</label>
		
		<input type=\"text\" name=\"event_location\" size=\"40\" maxlength=\"100\"";
		if ($matches == 0){ echo " value=\"$event_location\"";}
		echo ">";
		}
		echo "</td>
		</tr>
		";
		
		
				
		
		if (!(isset($edittemplate) && $edittemplate == "yes"))
			{
			echo "<tr><td align=\"right\"><b>$phrase[186]</b></td><td align=\"left\">
			<select name=\"day\">";
			$i = 1;
			while ($i < 32)
			{
			echo "<option value=\"$i\"";
			if ($i == $day) { echo " selected";}
			echo ">$i</option>";	
			$i++;	
			}
			
			echo "</select>
			<select name=\"month\">";
			$i = 1;
			while ($i < 13)
			{
				echo "<option value=\"$i\"";
				if ($i == $month ) {echo " selected";}
				echo ">$months[$i]</option>";
			$i++;
			}
			
			$previous = $year - 1;
			$next = $year + 1;
			echo "</select>
			<select name=\"year\">
			<option value=\"$previous\">$previous</option>
			<option value=\"$year\" selected>$year</option>
			<option value=\"$next\">$next</option>		
			</select>
			
			
			
			
			</td></tr>";
			}
			
			
		echo "<tr><td align=\"right\"><b>$phrase[242]</b></td><td align=\"left\"><select name=\"hour\">";
		
		foreach ($hoursvalue as $index => $value)
		{
		echo "<option value= \"$value\"";
		if ($hour == $value)
			{
			echo " selected";
			}
		
		echo ">$hourdisplay[$index]</option>";
		}
		
		echo "</select>
		<select name=\"minute\">";
		$counter = 0;
		
		while ($counter < 60)
		{
		$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
		echo "<option value= \"$counter\"";
		if ($minute == $counter)
			{
			echo " selected";
			}
		echo ">$counter</option>";	
		$counter++;
		}
		echo "</select></td></tr>
		";
		
                if ($event_finish != 0)
                {
                    echo "<tr><td align=\"right\"><b>$phrase[243]</b></td><td align=\"left\"><select name=\"fhour\">";
		
		foreach ($hoursvalue as $index => $value)
		{
		echo "<option value= \"$value\"";
		if ($fhour == $value)
			{
			echo " selected";
			}
		
		echo ">$hourdisplay[$index]</option>";
		}
		
		echo "</select>
		<select name=\"fminute\">";
		$counter = 0;
		
		while ($counter < 60)
		{
		$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
		echo "<option value= \"$counter\"";
		if ($fminute == $counter)
			{
			echo " selected";
			}
		echo ">$counter</option>";	
		$counter++;
		}
		echo "</select></td></tr>
		";
                }
                
		//query usages available
		$sql = "select cal_cat.cat_id as cat, cal_cat.cat_name as catname, cat_colour from cal_cat, cal_bridge where cal_cat.cat_id = cal_bridge.cat and cal_bridge.m = '$m'";
		$DB->query($sql,"editcalendar.php");
			
		$num_usages = $DB->countrows();
		
		$catname = $row["catname"];
		$cat_colour = $row["cat_colour"];
		
		if ($num_usages == 1)
			{
			
			$row = $DB->get();
			$catname = $row["catname"];
			$cat_colour = $row["cat_colour"];
			echo "<tr><td align=\"right\"><b>$phrase[310]</b></td><td align=\"left\"><span style=\"color:$cat_colour\"><b>$catname</b><input type=\"hidden\" name=\"event_type\" value=\"$cat_id\"></span></td></tr>";
			}
		else
			{
			echo "<tr><td align=\"right\"><b>$phrase[310]</b></td><td align=\"left\"><select name=\"event_type\">";
			while ($row = $DB->get()) 
				{
				$catid = $row["cat"];
				$catname = formattext($row["catname"]);
				$cat_colour = $row["cat_colour"];
				echo "<option value=\"$catid\"";
				if ($cat_id == $catid) { echo " selected";}
				echo " style=\"color:$cat_colour\">$catname</option>";
				}
			echo "</select></td></tr>";
			}
                        
                 echo "<tr><td align=\"right\"><b>$phrase[1108]</b></td><td align=\"left\"><input type=\"text\" name=\"trainerEmail\" value=\"$trainerEmail\" size=\"50\" maxlength=\"250\"></td></tr>";       
                        
                        
                        
                        
		if ($cat_cost == 1)
			{
			echo "<tr><td align=\"right\"><b>$phrase[126] $moneysymbol</b></td><td align=\"left\"><input type=\"text\" name=\"event_cost\" value=\"$event_cost\" size=\"8\" maxlength=\"8\"></td></tr>";
			}
		if ($cat_trainer == 1)
			{
			echo "<tr><td align=\"right\"><b>$phrase[125]</b></td><td align=\"left\"><input type=\"text\" name=\"trainer\" value=\"$trainer\" size=\"50\" maxlength=\"100\"></td></tr>";
			}
			
			echo "<tr><td align=\"right\" valign=\"top\"><b>$phrase[123]</b></td><td align=\"left\">
			
			<textarea name=\"event_description\" cols=\"60\" rows=\"8\">$event_description</textarea></td></tr>
                        <tr><td align=\"right\" valign=\"top\"><b>$phrase[1081]</b></td><td align=\"left\">
			
			<textarea name=\"tags\" id=\"tags\" cols=\"60\" rows=\"2\">$tags</textarea>
                        <span onclick=\"spanedit()\">$phrase[1081]</span>
                        <div id=\"taglist\" class=\"primary\" style=\"margin:1em 0;padding:1em;border:solid 1px;display:none;width:600px\">";
                        
                        $sql = "select tags from tags where m = '$m'";
			
				$DB->query($sql,"editcalendar.php");
				$row = $DB->get(); 
				
				$tags = trim($row["tags"]);
                                $tag = array();
                                $tag = explode(" ", $tags);
                                foreach ($tag as $value)
                                {
                                   $value = trim($value);
                                    if ($value != "")
                                    {
                                    echo " <span style=\"line-height:2.5em;margin:0.5em;padding: 0.3em 0.8em;\" class=\"accent\" onclick=\"addtag('$value','tags')\">$value</span> "; 
                                    }
                                }
                        
                        
                        echo "                           
</div> <span  id=\"editlink\" style=\"display:none\" onclick=\"editlist()\">$phrase[26]</span>
<span  id=\"savelink\" style=\"display:none\" onclick=\"savelist()\">$phrase[906]</span> 
<script>                            
window.tagdisplay = 'off';

function spanedit()
{
 
if (window.tagdisplay == 'off')
{
document.getElementById('taglist').style.display = 'block';

window.tagdisplay = 'on'

}
else
{
document.getElementById('taglist').style.display = 'none';

window.tagdisplay = 'off'

}
 
                            
 if (document.getElementById('TagListTextArea'))
     {
     document.getElementById('editlink').style.display = 'none';
     if (window.tagdisplay == 'off'){ document.getElementById('savelink').style.display = 'none';}
         else { document.getElementById('savelink').style.display = 'inline';}                          
                                
     } else
      {
       if (window.tagdisplay == 'off'){ document.getElementById('editlink').style.display = 'none';}
         else { document.getElementById('editlink').style.display = 'inline';}
    document.getElementById('savelink').style.display = 'none';                         
      }
}

function updateTagListTextArea(result)
          {
                       
          document.getElementById('TagListTextArea').value = result;   
           }
                                
function editlist()
{
url = '../main/ajax_tags.php?m=$m&event=gettags';
document.getElementById('taglist').innerHTML = '<textarea id=\"TagListTextArea\" class=\"red\" cols=\"60\" rows=\"4\"></textarea>';
ajax(url,updateTagListTextArea);
document.getElementById('editlink').style.display = 'none';
document.getElementById('savelink').style.display = 'inline';
                    


}
                                
function savelist()
{
var tags = encodeURIComponent(document.getElementById('TagListTextArea').value)
   
url = '../main/ajax_tags.php?m=$m&event=updatetags&tags=' + tags;
updatePage(url,'taglist');                                

document.getElementById('taglist').style.display = 'block';
document.getElementById('editlink').style.display = 'inline';
document.getElementById('savelink').style.display = 'none';
                    
//window.tagdisplay = 'off'

}                                
                                
                                
                                
</script>
</td></tr>";
			
			
			if (!isset($edittemplate))
			{
			//check if any images attached to event if not display upload form
			
			$sql = "select image_id , name from images where page = '$event_id' and modtype = 'c'"; 
			$DB->query($sql,"editcalendar.php");
			$num_images = $DB->countrows();
		
		
			echo "<tr><td align=\"right\" valign=\"top\"><b>$phrase[98]</b></td><td style=\"text-align:left\">";
				if ($num_images == 0)
			{
			echo "<input type=\"file\" name=\"upload[0]\" >";
			}
			else
			{
				$row = $DB->get();
				$image_id = $row["image_id"];
				$image_name = $row["name"];
                                $image_linkname = urlencode($row["name"]);
			echo "<img src=\"../main/image.php?m=$m&amp;image_id=$image_id&amp;module=cal\" style=\"vertical-align:middle\"> $image_name 
			<a href=editcalendar.php?event=deleteimage&amp;name=$image_linkname&amp;image_id=$image_id&amp;event_id=$event_id&amp;m=$m><img src=\"../images/cross.png\" title=\"$phrase[590]\"></a>	";
			}
		
		echo "</td></tr>";
			}
			
			
			
			
		if ($cat_notes == 1)
			{
			echo "<tr><td align=\"right\" valign=\"top\"><b>Event staff notes</b></td><td align=\"left\">
			<textarea name=\"event_staffnotes\" cols=\"60\" rows=\"8\">$event_staffnotes</textarea></td></tr>";
			}
	
	if ($eventgroup > 0)
	{
			echo "<tr><td align=\"right\" valign=\"top\"><b>$phrase[16]</b></td><td align=\"left\">
	  
	
		
			<select name=\"eventgroup\">
		<option value=\"0\" selected> $phrase[768]</option>
		<option value=\"$eventgroup\"> $phrase[767]  ($total)	</option>	
			</select>	
		
						   
						   
</td></tr>";
	}
	
	
	if (!(isset($edittemplate) && $edittemplate == "yes"))
						{
	
	echo "<tr><td align=\"right\" valign=\"top\"><b>$phrase[401]</b></td><td align=\"left\"><select name=\"cancelled\">
	<option value=\"0\" ";
	if ($cancelled == 0) {echo "selected";}
	echo ">$phrase[774]</option>
	<option value=\"1\" ";
	if ($cancelled == 1) {echo "selected";}
	echo ">$phrase[152]</option>
	<option value=\"2\">$phrase[24]</option>
	</select></td></tr>";
						}
		
		
		
		
		
		
		
		if (isset($cat_takesbookings) && $cat_takesbookings > 0)
			{
				
				
			echo "<tr><td align=\"right\"><b>$phrase[127]</b></td><td align=\"left\">
			<input name=\"maxbookings\" type=\"text\" size=\"3\" maxlength=\"3\" value=\"$maxbookings\"> $phrase[308]</td></tr>";
			}
		
			

			if ($cat_age == 1)
			{
				$pos = strpos($agerange,":");

			$minage = substr($agerange,0,$pos);
			$maxage = trim(substr($agerange,$pos + 1));
				
			echo "<tr><td align=\"right\"><b>$phrase[987]</b></td><td align=\"left\">
			<select name=\"minage\">
";
			$counter = 0;
			while ($counter < 100)
			{
			echo "<option value=\"$counter\"";
			if ($counter == $minage) { echo " selected";}
			echo ">$counter</option>
";	
			$counter++;
			}
			echo "</select> -
			<select name=\"maxage\">
";
				$counter = 1;
			while ($counter < 120)
			{
			echo "<option value=\"$counter\"";
			if ($counter == $maxage) { echo " selected";}
			echo ">$counter</option>
";	
			$counter++;
			}
			echo "</select>
			</td></tr>";
			}
			
			
			
			
		
		echo "<tr><td></td><td align=\"left\">";
		
		if (isset($cat_cost) && $cat_cost <> 1)
			{
			echo "<input type=\"hidden\" name=\"event_cost\" value=\"0\">";
			}
		
		if (isset($cat_notes) && $cat_notes <> 1)
			{
			echo "<input type=\"hidden\" name=\"event_staffnotes\" value=\"\">";
			}
			
		
		
		if (isset($cat_takesbookings) && $cat_takesbookings == 0)
			{ echo "<input type=\"hidden\" name=\"maxbookings\" value=\"0\">";}
		if ($eventgroup == 0)	{	echo "<input type=\"hidden\" name=\"eventgroup\" value=\"0\">";}
		if ($cat_cost == 0) { echo "<input type=\"hidden\" name=\"event_cost\" value=\"\">";}
		if ($cat_trainer == 0) { echo "<input type=\"hidden\" name=\"trainer\" value=\"\">";}
		if ($cat_notes == 0) { echo "<input type=\"hidden\" name=\"event_staffnotes\" value=\"\">";}
		
		
		if ((isset($edittemplate) && $edittemplate == "yes"))
		{
	$day = date("d",$t);
	$month = date("m",$t);
	$year = date("Y",$t);
			
			echo "<input type=\"hidden\" name=\"day\" value=\"$day\">
			<input type=\"hidden\" name=\"month\" value=\"$month\">
			<input type=\"hidden\" name=\"year\" value=\"$year\">";
			
		}
		
		
		
		
		echo "<input type=\"submit\" name=\"updateevent\" value=\"";
		
		if (isset($edittemplate) && $edittemplate == "yes")
						{ echo "$phrase[304]";}
						else { echo "$phrase[305]";}
		
		
		
		echo "\">";
		
		if (isset($edittemplate) && $edittemplate == "yes")
		{
		echo "<input type=\"hidden\" name=\"template\" value=\"yes\">
			<input type=\"hidden\" name=\"cancelled\" value=\"0\"> 
		";
		echo "<input type=\"hidden\" name=\"event\" value=\"templates\">";
		}
		echo "<input type=\"hidden\" name=\"t\" value=\"$t\">
		
		<input type=\"hidden\" name=\"m\" value=\"$m\">
		<input type=\"hidden\" name=\"update\" value=\"update\">
		<input type=\"hidden\" name=\"modname\" value=\"$modname\">
		<input type=\"hidden\" name=\"event_id\" value=\"$event_id\">
		
		</td></tr>
		
		</table></fieldset></form><br><br>"; 
		}
	

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "templates")

	{
	
		
		
	$linkmodname = urlencode($modname);
	
	echo "
	<a href=\"editcalendar.php?m=$m&amp;t=$t\">$phrase[118]</a> | 	 <a href=\"editcalendar.php?m=$m&amp;event=all\">$phrase[676]</a> | <a href=\"editcalendar.php?m=$m&amp;event=search\">$phrase[282]</a> | <a href=\"editcalendar.php?m=$m&amp;t=$t&amp;event=stats\">$phrase[315]</a><br><br> 
			
	<a href=\"editcalendar.php?m=$m&amp;addtemplate=yes&amp;event=add&amp;t=$t\" ><image src=\"../images/add.png\" title=\"$phrase[303]\"></a>
		
	<h2>$phrase[302]</h2><br>
	 
";
		//get list of usages for this calendar
		$where = " IN ("; 
		$typecount = 1;
		$sql = "SELECT cat FROM cal_bridge where m = '$m'";
		$DB->query($sql,"editcalendar.php");
			
		while ($row = $DB->get()) 
					{
					
					$cat =$row["cat"];
					$catarray[] = $cat;
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
	
		$sql = "SELECT event_id, event_name,cat_colour,cat_id  FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and template = '1' and event_catid $where order by event_name";
	
		$DB->query($sql,"editcalendar.php");
			
			while ($row = $DB->get()) 
					{
					$array_id[] =$row["event_id"];
					$array_name[] = formattext($row["event_name"]);
					$array_cat_id[] =$row["cat_id"];
					//$array_time[] =$row["time"];
					$array_colour[] =$row["cat_colour"];
					
					}
				
				if (isset($catarray))
				{
				echo "<table style=\"margin-left:45%\">";
				foreach ($catarray as $index => $value)
						{
						if (isset($array_cat_id))
							{
								
							foreach ($array_cat_id as $i => $id)
								{
								if ($value == $id)
									{
									echo "<tr><td><a href=\"editcalendar.php?m=$m&amp;edittemplate=yes&amp;event_id=$array_id[$i]&amp;event=edit&amp;t=$t\" style=\"color:$array_colour[$i];\">";
					if ($array_name[$i] == "") {echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; }	else {echo $array_name[$i];}			
									
									
									echo "</a></td><td><a href=\"editcalendar.php?update=deletetemplate&amp;event_id=$array_id[$i]&amp;event=templates&m=$m\">Delete</a></td></tr>";
									
									}
								
								
								
								}
						
						
						
							}
							
						}
					echo "</table>";	
				}				
				
				
								
				
	
	
	
	}
	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "add")

	{
	
	if(isset($_REQUEST["addtemplate"]))
	{$addtemplate = $_REQUEST["addtemplate"];}
	
	if(isset($_REQUEST["usetemplate"]))
	{$usetemplate = $_REQUEST["usetemplate"];}
	
	$linkmodname = urlencode($modname);
	
	//query usages available
	if ($DB->type == "mysql")
	{
	$sql = "select cal_cat.cat_id as cat, cal_cat.cat_name, cal_cat.cat_colour from cal_cat, cal_bridge where cal_cat.cat_id = cal_bridge.cat and cal_bridge.m = '$m' order by cal_cat.cat_name";
	}
	
	else
	{
	$sql = "select distinct(cal_cat.cat_id) as cat, cat_name, cat_colour from cal_cat, cal_bridge where cal_cat.cat_id = cal_bridge.cat and cal_bridge.m = '$m' order by cal_cat.cat_name";

	}
	
	
	$DB->query($sql,"editcalendar.php");
	       
        $num_rows = $DB->countrows(); 
       
	
			
	if ($num_rows == 1)
		{
		$row = $DB->get();
		$cat = $row["cat"];
		}
		
	
	if ((!isset($cat)) &&  $num_rows > 1)
		{
		//display menu of calendar usages
		
		echo "
		<div style=\"margin-left:40%;text-align:left\"><b  style=\"font-size:large\">";
		if (isset($addtemplate) && $addtemplate == "yes")
				{echo $phrase[626] ;}
		else {		 echo $phrase[300];}
		
		
echo "</b><br><br>";
		while ($row = $DB->get()) 
			{
			
			$cat_id = $row["cat"];
			$cat_name = formattext($row["cat_name"]);
			$cat_colour = $row["cat_colour"];
			echo "<a href=\"editcalendar.php?m=$m&amp;event=add&amp;t=$t&amp;cat=$cat_id";
			if (isset($addtemplate) && $addtemplate == "yes")
				{
				echo "&amp;addtemplate=yes";
				}
			
			echo "\" style=\"color:$cat_colour;line-height:1.5em\">$cat_name</a><br>";
			}
		echo "</div>";
		}
	elseif ($num_rows == 0) { echo "<br><br><h2>$phrase[301]</h2>";}
	
	else
		{
		
		if (isset($cat))
			{
			$sql = "select * from cal_cat where cal_cat.cat_id = '$cat'";
			$DB->query($sql,"editcalendar.php");
			
			
			}
		
		
		$row = $DB->get();
		$cat_name = $row["cat_name"];
		$cat_colour = $row["cat_colour"];
		$cat_cost = $row["cat_cost"];
		$cat_notes = $row["cat_notes"];
		$cat_takesbookings = $row["cat_takesbookings"];
		$cat_trainer = $row["cat_trainer"];
		$cat_age = $row["cat_age"];
		
		
		
		//display event creation form
		
		
	
			
			
			
		if (isset($usetemplate) && $usetemplate == "yes")
			{
				
			$sql = "SELECT * from cal_events where event_id = '$event_id'";
			$DB->query($sql,"editcalendar.php");
			while ($row = $DB->get()) 
				{
				
				$event_name = formattext($row["event_name"]);
				
				$event_location = formattext($row["event_location"]);
				$event_cost = formattext($row["event_cost"]);
				$event_description = $row["event_description"];
				$event_staffnotes = $row["event_staffnotes"];
				$hour = date("G",$row["event_start"]);
				$minute = date("i",$row["event_start"]);
				$maxbookings = $row["maxbookings"];
			
				}
			}
		
		
		
		
		//$displaydate = date("jS F Y",$t);
		$displaydate = strftime("%A %x", $t);
		
		echo "
		<a href=\"editcalendar.php?m=$m&amp;t=$t\">$phrase[118]</a><h2 style=\"color:$cat_colour\">$cat_name</h2>
			
				
			
			<FORM method=\"POST\"  enctype=\"multipart/form-data\" action=\"editcalendar.php\" style=\"margin-left:auto;margin-right:auto;width:80%\"><fieldset><legend>";
		 if (isset($addtemplate) && $addtemplate == "yes")
        {    echo $phrase[303];	  }
         else { echo $phrase[314];} 
		echo "</legend><table style=\"margin-left:auto;margin-right:auto;\" >
				<tr><td align=\"right\"></td><td align=\"left\">";
				
				if (!isset($addtemplate) )
					{
						
									$sql = "SELECT event_id, event_name from cal_events where event_catid = '$cat' and template='1' order by event_name";
		$DB->query($sql,"editcalendar.php");
		$num_rows = $DB->countrows();
		if ($num_rows > 0)
			{
			echo "<b>$phrase[302]</b><br>";
			}
		while ($row = $DB->get()) 
			{
			$id = $row["event_id"];
			$eventname = formattext($row["event_name"]);
			echo "<a href=\"editcalendar.php?m=$m&amp;event_id=$id&usetemplate=yes&amp;event=add&amp;cat=$cat&amp;t=$t\">$eventname</a><br>";
			
			}	
						
						
						
						
						
						
					echo "<br><b>$displaydate</b>";
					}
				
		
		
					
					
				
				echo "<br></td></tr>
		<tr><td align=\"right\"><b>$phrase[311]</b></td><td align=\"left\"><input type=\"text\" name=\"event_name\" size=\"50\" maxlength=\"100\"";
		if (isset($usetemplate) && $usetemplate == "yes"){ echo " value=\"$event_name\"";}
				echo "></td></tr>
		<tr><td align=\"right\"><b>$phrase[193]</b></td><td align=\"left\">";
		
				
				
			
				
				$counter = 1;
				$matches = 0;
				
			
				
				
				foreach ($branches as $bno => $bname)
						{
							if (in_array('0',$locations) || in_array($bno,$locations))
							{
							$id = "b" . $counter;
							echo "<input type=\"radio\" name=\"location\" value=\"$bno\" id=\"$id\" ";
							if (isset($usetemplate) && $usetemplate == "yes" && $bno == $event_location) {echo " checked";}
							elseif (!(isset($usetemplate) && $usetemplate == "yes") && $counter == 1) {echo " checked";}
						
							echo "> <label for=\"$id\">$bname</label><br>";
							$counter++;
							}
						}
						
						
			
				
		
				if (in_array('0',$locations))
		{
		
		
		$id = "b" . $counter;
		echo "
		
		<input type=\"radio\" name=\"location\" value=\"other\" id=\"$id\" checked";
		//if ($counter == 1 || (isset($usetemplate) && $usetemplate == "yes" && $matches == 0)) {echo " checked";}
		echo "> <label for=\"$id\">Other</label>
		
		<input type=\"text\" name=\"event_location\" size=\"40\" maxlength=\"100\"";
		if (isset($usetemplate) && $usetemplate == "yes" && $matches == 0){ echo " value=\"$event_location\"";}
		echo ">";
		}
		
		echo "</td></tr>
		";
		
		
		
				echo "<tr><td align=\"right\"><b>$phrase[242]</b></td><td align=\"left\"><select name=\"hour\">";
				foreach ($hoursvalue as $index => $value)
									{
		echo "<option value= \"$value\"";
		
		if (9 == $value)
			{
			echo " selected";
			}
			
		echo ">$hourdisplay[$index]</option>";
									}
		
		echo "</select>
		<select name=\"minute\">";

		$counter = 0;
		
		while ($counter < 60)
		{
		$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
		echo "<option value= \"$counter\">$counter</option>";	
		$counter++;
		}
		echo "</select></td></tr>";
                
                
                
                
				echo "<tr><td align=\"right\"><b>$phrase[243]</b></td><td align=\"left\"><select name=\"fhour\">";
				foreach ($hoursvalue as $index => $value)
									{
		echo "<option value= \"$value\"";
		
		if (10 == $value)
			{
			echo " selected";
			}
			
		echo ">$hourdisplay[$index]</option>";
									}
		
		echo "</select>
		<select name=\"fminute\">";

		$counter = 0;
		
		while ($counter < 60)
		{
		$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
		echo "<option value= \"$counter\">$counter</option>";	
		$counter++;
		}
	
		
		
		echo "</select></td></tr>";

        if (!isset($addtemplate) || $addtemplate <> "yes")
        {


    echo "<tr><td align=\"right\"><b>$phrase[677]</b></td><td align=\"left\">&nbsp;<input type=\"radio\" name=\"recurrent\" value=\"no\" checked id=\"recurrent_no\"> $phrase[13]  <input type=\"radio\" name=\"recurrent\" value=\"yes\" id=\"recurrent_yes\"> $phrase[12] 
    <div id=\"recurrentoptions\" style=\"padding: 1em 0\"><ul><li>
$phrase[457] <select name=\"interval\"><option value=\"day\">$phrase[182]</option><option value=\"week\">$phrase[458]</option><option value=\"fortnight\">$phrase[459]</option><option value=\"monthlybydate\">$phrase[1031]</option> <option value=\"monthlybyweekday\">$phrase[1030]</option></select> $phrase[679]
<select name=\"recur_num\">";
$counter = 1;
while ($counter < 53)
{
echo "<option value=\"$counter\">$counter</option>";
$counter++;
}

echo "</select></li><li>
$phrase[997] <button onclick=\"addday();return false;\">$phrase[176]</button>

<div id=\"datebox\"></div></li>
</ul>
</div>

        </td></tr>";
        }
  

		
		
		echo "<tr><td align=\"right\"><b>$phrase[1108]</b></td><td align=\"left\"><input type=\"text\" name=\"trainerEmail\" size=\"50\" maxlength=\"250\"></td></tr>";
		
		
		
		if ($cat_cost == 1)
			{
			echo "<tr><td align=\"right\"><b>$phrase[126] $moneysymbol</b></td><td align=\"left\"><input type=\"text\" name=\"event_cost\" size=\"8\" maxlength=\"8\"";
			if (isset($usetemplate) && $usetemplate == "yes"){ echo " value=\"$event_cost\"";}
			echo "></td></tr>";
			}
		
			if ($cat_trainer == 1)
			{
			echo "<tr><td align=\"right\"><b>$phrase[125]</b></td><td align=\"left\"><input type=\"text\" name=\"trainer\" size=\"50\" maxlength=\"100\"></td></tr>";
			}
			
			
			echo "<tr><td align=\"right\" valign=\"top\"><b>$phrase[123]</b></td><td align=\"left\">
			
			<textarea name=\"event_description\" cols=\"60\" rows=\"8\">";
			if (isset($usetemplate) && $usetemplate == "yes"){ echo "$event_description";}
			echo "</textarea></td></tr>
                            
                        <tr><td align=\"right\" valign=\"top\"><b>$phrase[1081]</b></td><td align=\"left\">
			
			<textarea name=\"tags\" id=\"tags\" cols=\"60\" rows=\"2\"></textarea>
                        <span onclick=\"spanedit()\">$phrase[1081]</span>
                        <div id=\"taglist\" class=\"primary\" style=\"margin:1em 0;padding:1em;border:solid 1px;display:none;width:600px\">";
                        
                        $sql = "select tags from tags where m = '$m'";
			
				$DB->query($sql,"editcalendar.php");
				$row = $DB->get(); 
				
				$tags = trim($row["tags"]);
                                $tag = array();
                                $tag = explode(" ", $tags);
                                foreach ($tag as $value)
                                {
                                    $value = trim($value);
                                    if ($value != "")
                                    {
                                   echo " <span style=\"line-height:2.5em;margin:0.5em;padding: 0.3em 0.8em;\" class=\"accent\" onclick=\"addtag('$value','tags')\">$value</span> "; 
                                    }
                                }
                        
                        
                        echo "                           
</div> <span  id=\"editlink\" style=\"display:none\" onclick=\"editlist()\">$phrase[26]</span>
<span  id=\"savelink\" style=\"display:none\" onclick=\"savelist()\">$phrase[906]</span> 
<script>                            
window.tagdisplay = 'off';

function spanedit()
{
 
if (window.tagdisplay == 'off')
{
document.getElementById('taglist').style.display = 'block';

window.tagdisplay = 'on'

}
else
{
document.getElementById('taglist').style.display = 'none';

window.tagdisplay = 'off'

}
 
                            
 if (document.getElementById('TagListTextArea'))
     {
     document.getElementById('editlink').style.display = 'none';
     if (window.tagdisplay == 'off'){ document.getElementById('savelink').style.display = 'none';}
         else { document.getElementById('savelink').style.display = 'inline';}                          
                                
     } else
      {
       if (window.tagdisplay == 'off'){ document.getElementById('editlink').style.display = 'none';}
         else { document.getElementById('editlink').style.display = 'inline';}
    document.getElementById('savelink').style.display = 'none';                         
      }
}

function updateTagListTextArea(result)
          {
                       
          document.getElementById('TagListTextArea').value = result;   
           }
                                
function editlist()
{
url = '../main/ajax_tags.php?m=$m&event=gettags';
document.getElementById('taglist').innerHTML = '<textarea id=\"TagListTextArea\" class=\"red\" cols=\"60\" rows=\"4\"></textarea>';
ajax(url,updateTagListTextArea);
document.getElementById('editlink').style.display = 'none';
document.getElementById('savelink').style.display = 'inline';
                    


}
                                
function savelist()
{
var tags = encodeURIComponent(document.getElementById('TagListTextArea').value)
   
url = '../main/ajax_tags.php?m=$m&event=updatetags&tags=' + tags;
updatePage(url,'taglist');                                

document.getElementById('taglist').style.display = 'block';
document.getElementById('editlink').style.display = 'inline';
document.getElementById('savelink').style.display = 'none';
                    
//window.tagdisplay = 'off'

}                                
                                
                                
                                
</script>
</td></tr>";



			
			if (!isset($addtemplate))
			{
			echo "<tr><td align=\"right\" valign=\"top\"><b>$phrase[98]</b></td><td align=\"left\"><input type=\"file\" name=\"upload[0]\" ></td></tr>";
			}
			
		if ($cat_notes == 1)
			{
			echo "<tr><td align=\"right\" valign=\"top\"><b>$phrase[124]</b></td><td align=\"left\">
			<textarea name=\"event_staffnotes\" cols=\"60\" rows=\"8\">";
			if (isset($usetemplate) && $usetemplate == "yes"){ echo "$event_staffnotes";}
			echo "</textarea></td></tr>";
			}




		
		if ($cat_takesbookings > 0)
			{
			echo "<tr><td align=\"right\"><b>$phrase[127]</b></td><td align=\"left\">
			<input name=\"maxbookings\" type=\"text\" size=\"3\" value=\"0\" maxlength=\"3\"";
			if (isset($usetemplate) && $usetemplate == "yes") {echo " value=\"$maxbookings\"";}
			echo ">$phrase[678]</td></tr>";
			}
		
		if ($cat_age == 1)
			{
			echo "<tr><td align=\"right\"><b>$phrase[987]</b></td><td align=\"left\">
			<select name=\"minage\">";
			$counter = 0;
			while ($counter < 100)
			{
			echo "<option value=\"$counter\">$counter</option>";	
			$counter++;
			}
			echo "</select> -
			<select name=\"maxage\">";
				$counter = 1;
			while ($counter < 120)
			{
			echo "<option value=\"$counter\"";
			if ($counter == 119) { echo " selected";}
			echo ">$counter</option>";	
			$counter++;
			}
			echo "</select>
			</td></tr>";
			}
			
			
			
			
		echo "<tr><td></td><td align=\"left\">";
		
		if ($cat_takesbookings == 0)
			{ echo "<input type=\"hidden\" name=\"maxbookings\" value=\"0\">";}
		
		if ($cat_cost <> 1)
			{
			echo "<input type=\"hidden\" name=\"event_cost\" value=\"\">";
			}
		
		if ($cat_notes <> 1)
			{
			echo "<input type=\"hidden\" name=\"event_staffnotes\" value=\"\">";
			}
		if (isset($addtemplate) && $addtemplate == "yes")
			{
			echo "<input type=\"hidden\" name=\"template\" value=\"1\">";
			}
		else
			{
			echo "<input type=\"hidden\" name=\"template\" value=\"0\">";
			}
		
		if ($cat_trainer <> 1) { echo "<input type=\"hidden\" name=\"trainer\" value=\"\">";}
		
		echo"<input type=\"submit\" name=\"addevent\" value=\"";
		if (isset($addtemplate) && $addtemplate == "yes")
						{ echo "$phrase[303]";}
						else { echo "$phrase[314]";}
		
		echo "\">";
		if (isset($addtemplate) && $addtemplate == "yes")
		{
		echo "<input type=\"hidden\" name=\"recurrent\" value=\"no\">
		<input type=\"hidden\" name=\"recur_num\" value=\"1\">
		<input type=\"hidden\" name=\"interval\" value=\"day\">
		<input type=\"hidden\" name=\"addtemplate\" value=\"yes\">
		";	
		}
		
		echo "<input type=\"hidden\" name=\"t\" value=\"$t\">
		<input type=\"hidden\" name=\"m\" value=\"$m\">
		<input type=\"hidden\" name=\"update\" value=\"add\">
		<input type=\"hidden\" name=\"modname\" value=\"$modname\">
		<input type=\"hidden\" name=\"event_catid\" value=\"$cat\">
		</td></tr>
		
		</table></fieldset></form><br><br>
		
	<script type=\"text/javascript\">	

		
var counter = 1;


	function addday()
	{
	

	var snippet = '<div id=\"div' + counter + '\" style=\"margin:0.5em\"><input type=\"text\" name=\"dates[' + counter + ']\" id=\"dates' + counter + '\" readonly=\"readonly\" class=\"datepicker\" size=\"10\">' +
	'<span onclick=\"HideDiv(\'div' + counter +'\');return false;\" style=\"position:relative;\"> <img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\" style=\"padding-left:2em;vertical-align: text-bottom;\"></span></div>';
	
	 var datesbox = document.getElementById('datebox');
	//alert(datesbox.innerHTML)
	//datesbox.innerHTML = datesbox.innerHTML + snippet;
	
	
	
	var div = document.createElement('div')
				div.innerHTML = snippet;
				//alert(div.innerHTML)
				datesbox.appendChild(div); 
	
	datepicker('dates' + counter);
	

	
	
	counter++;
	}

function DisableFormFields(myDiv)
			{
	
			var inputs = document.getElementById(myDiv).getElementsByTagName('input');
			  for(i=0;i<inputs.length;i++){
    			inputs[i].disabled = true;
  			}
			}
			
function HideDiv(id)
				{
				DisableFormFields(id);
				var div =document.getElementById(id);
		
				div.style.display = 'none';
				}
	 
	 
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
	
	//  datepicker('datepicker');
	
	
	 function recurrent()
	 {
	
	 document.getElementById('recurrent_yes').onclick=show
	 document.getElementById('recurrent_no').onclick=hide
	 hide()
	 }
	 
	 function hide()
	 {
	 if (document.getElementById('recurrentoptions'))
	 {
	 document.getElementById('recurrentoptions').style.display = \"none\";	
	 }
	 }
	 
	 
	 function show()
	 {
	 if (document.getElementById('recurrentoptions'))
	 {
	 document.getElementById('recurrentoptions').style.display = \"block\";	
	 }
	 }
	 
	  
	 window.onload=recurrent
	 </script>
		
		
		"; 
		}
	
	
	
	}
	
	


	
		 
	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "users" && $EVENTAUTHENTICATION == "registration")
	{
		
	echo "<h2>$phrase[977]</h2>";	
	
	$sql = "select reg_id, reg_email, reg_fname,reg_telephone, reg_lname, reg_blocked, reg_activated, reg_date from registered_users";
	//echo $sql;
 $DB->query($sql,"editcalendar.php");


 echo "
 
 
 
 <script type=\"text/javascript\">
		
		var queue = [];
		var xhr = null;

		
		
		
		
		
		
		
		
			function update_blocked(id)
		{
		
		var values = id.split('_')
		var action = values[0];
		var userid = values[1];
		//alert(action);
		//alert(userid)
		//var key = action + '_' + id
		var element = document.getElementById(id);
		//alert(element)
		var td = element.parentNode
		td.style.background = 'url(\"../images/ajax_progress2.gif.\")';
		//td.style.background-color: transparent
		var value = element.options[element.selectedIndex].value ;
		
	
		
		
		var url = \"../main/ajax.php?m=$m&event=UpdateRegisteredUser&action=\" + action + \"&id=\" + userid + \"&value=\" + value;
		var values = [id,url]
		
		 
		queue.push(values)
		queuecheck()
		
		}
		
		
		function queuecheck()
		{
			
			
		if (xhr == null)
		{
		
		if (queue.length > 0)
		{
		//alert (\"queue exists\")
		var values = queue.shift()
		ajax(values)
		}
		else 
		{
		//alert (\"no queue exists\")
		}
		
		}
		
		
		}
		
		function ajax(values){
	

	
		
		
		
		if (window.XMLHttpRequest) {
			xhr = new XMLHttpRequest();
			
		}
		else 
			if (window.ActiveXObject) {
				try {
					xhr = new ActiveXObject(\"Msxml2.XMLHTTP\");
				} 
				catch (e) {
					try {
						xhr = new ActiveXObject(\"Microsoft.XMLHTTP\");
					} 
					catch (e) {
					}
				}
			}
		
		
		
		xhr.onreadystatechange = function(){
		
		
			if (xhr.readyState == 4) {
			//alert(xhr.status)
			
			if (xhr.status == 200)
			{
			//alert(\"hello 200\")
			changecolour(values[0],xhr.responseText)
			//alert(xhr.responseText)
			}
			
			xhr = null;
			queuecheck();
			}
			
		}
		
		
		var timestamp = new Date();
		
		var fullurl = values[1] + \"&rt=\" + timestamp.getTime()
		//alert(fullurl)
		xhr.open(\"GET\", fullurl, true);
		xhr.send(null);
		
	
			
	}
	
		
		
		
		
		function changecolour(id,colour)
		{
		//alert(colour)
		var element = document.getElementById(id);
		element.parentNode.style.background = colour;
		
		}
		
		</script>
		
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 <table class=\"colourtable\" cellpadding=\"6\"  style=\"text-align:left;margin-left:auto;margin-right:auto\">
<tr style=\"font-weight:bold\">
      <td>$phrase[130]  $phrase[131] </td>
      <td>$phrase[259]</td>
      <td>$phrase[132]</td>
      <td>$phrase[217] </td>
      <td>$phrase[978]</td>
      <td>$phrase[430] </td>
   
      <td>$phrase[24]</td>
      </tr>
 
 ";
while ($row = $DB->get())
      {
      $reg_id = $row["reg_id"];
      $fname = formattext($row["reg_fname"]);
      $lname = formattext($row["reg_lname"]);
      $email = formattext($row["reg_email"]);
      $blocked = formattext($row["reg_blocked"]);
      $activated = formattext($row["reg_activated"]);
      $telephone = formattext($row["reg_telephone"]);
      $displaydate = strftime("%X %x", $row["reg_date"]);
      $blocked_id = "blocked_" . $reg_id;
      
      
      
      echo "<tr >
      <td>$fname $lname</td>
      <td>$email</td>
      <td>$telephone</td>
      <td>$displaydate</td>
      <td style=\"background:";
      if ($activated == 0) {echo "#E05C5C";} else {echo "#B1D8A9";}
      echo "\">";
      if ($activated == 1) {echo $phrase[12];} else {echo $phrase[13];}
      echo "</td>
   
      <td style=\"background:";
      if ($blocked == 1) {echo "#E05C5C";} else {echo "#B1D8A9";}
      echo "\"><select id=\"$blocked_id\" onchange=update_blocked('$blocked_id')>
					<option value=\"0\">No</option>
					<option";
					if ($blocked == 1) {echo " selected";}
					echo " value=\"1\">Yes</option>
					</select></td>
      <td><a href=\"editcalendar.php?m=$m&amp;update=deletereguser&amp;reg_id=$reg_id&amp;event=users\">Delete</a></td>
      </tr>";
      
      
      
      
      }
	echo "</table>";
		
		
		
	}

else
	{
	//display calendar

//initialize date variables 

if (!isset($t))
	{
	$t = time();
	//$t = mktime(0, 0, 0, 01, 29,  2004);
	}
$display = strftime("%B %Y", $t);
//$display = date("F Y",$t);
$day = date("d",$t);
$month = date("m",$t);
$monthname = strftime("%B ", $t);
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
		$where = " IN ("; 
		$typecount = 1;
		$sql = "SELECT cat FROM cal_bridge where m = '$m'";
		$DB->query($sql,"editcalendar.php");
		
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
		
		
		
		
		
			//get public holidays and put in array
		if ($DB->type == "mysql")
			{
		$sql = "select name,  date_format(holiday, '%D %b %Y') as fholiday,date_format(holiday, '%Y%m%d') as holiday from holidays ";
			}	
	
			
		else
			{
		$sql = "select name,  strftime('%Y%m%d',holiday) as holiday from holidays ";
			}	
	
		//$sql = "select date_format(holiday, '%D %b %Y') as fholiday, date_format(holiday, '%Y%m%d') as holiday, name from holidays ";	
		
		$DB->query($sql,"editcalendar.php");
		
		$numrows = $DB->countrows();
		while ($row = $DB->get()) 
	
		{
		$a_holiday[] = $row["holiday"];
		$a_name[] = formattext($row["name"]);
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
		
		
		//get list of this months events and put in array
	
	

			if ($DB->type == "mysql")
			{
			$sql = "SELECT event_id, event_catid,event_name,maxbookings, event_location,cancelled,event_finish, cat_finishtime, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat,cal_bridge  where cal_cat.cat_id = cal_bridge.cat and cal_bridge.cat = cal_events.event_catid and cal_bridge.m = '$m' and month(FROM_UNIXTIME(event_start)) = '$month' and year(FROM_UNIXTIME(event_start)) = '$year' and template = '0' and event_catid  $where $locationinsert order by event_start";	
			}
		else
			{
			$sql = "SELECT event_id, event_catid,event_name,maxbookings, event_location,cancelled, event_finish,cat_finishtime, cat_takesbookings, event_start FROM cal_events, cal_cat,cal_bridge  where cal_cat.cat_id = cal_bridge.cat and cal_bridge.cat = cal_events.event_catid and cal_bridge.m = '$m' and strftime('%m',datetime ( event_start , 'unixepoch' )) = '$month' and strftime ( '%Y' , datetime ( event_start , 'unixepoch' ) ) = '$year'  and template = \"0\" and event_catid $where $locationinsert order by event_start";	
			}
		$DB->query($sql,"editcalendar.php");
	//	echo $sql;
		
		while ($row = $DB->get()) 
		
					{
					
					$array_id[] =$row["event_id"];
					$array_name[] = formattext($row["event_name"]);
					$array_location[] =formattext($row["event_location"]);
					$array_day[] = date("d", $row["event_start"]);	
					$array_time[] = date("g:i a", $row["event_start"]);
                                        $array_endtime[] = date("g:i a", $row["event_finish"]);
					$array_cancelled[] =$row["cancelled"];
					$array_colour[] =$row["cat_colour"];
                                        $array_finishtime[] =$row["cat_finishtime"];
					}

 
   echo "
   <a href=\"editcalendar.php?m=$m&amp;event=all\">$phrase[676]</a> | <a href=\"editcalendar.php?m=$m&amp;event=search\">$phrase[282]</a> |

   <a href=\"editcalendar.php?m=$m&amp;event=templates&amp;t=$t\">$phrase[302]</a> | <a href=\"editcalendar.php?m=$m&amp;t=$t&amp;event=stats\">$phrase[315]</a>";
	if ($EVENTAUTHENTICATION == "registration") {echo  " | <a href=\"editcalendar.php?m=$m&amp;event=users\">$phrase[977]</a>";}
   
   echo " | 
    <form style=\"display:inline\" action=\"editcalendar.php\" method=\"get\">
   <p  style=\"display:inline\"><select name=\"month\">";
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
   </p>
   </form>
   <br><br>
   


  <table class=\"colourtable\" id=\"calendar\" width=\"100%\" style=\"float:left;height:100px\"  cellpadding=\"2\">
 <tr class=\"accent\"><td align=\"left\"><br>&nbsp;&nbsp;
   <a href=\"editcalendar.php?m=$m&amp;t=$lastmonth\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center;\" ><br><span style=\"font-size:2em\">$display</span>
   <br>
   <br>
   
   
   
   </td><td style=\"text-align:right\"><br><a href=\"editcalendar.php?m=$m&amp;t=$nextmonth\">$phrase[155]</a>&nbsp;&nbsp;</td></tr>
   
   
   
 
   
   "; 
   

if (isset($CALENDAR_START) && $CALENDAR_START =="MON") {$cal_offset = 1;} else {$cal_offset = 0;}


   //display blank cells at start of month
   $counter = 0 + $cal_offset;
    if ($cal_offset == 1 && $fd == 0) $fd=7; 
   if ($fd <> 0 + $cal_offset)
   	{
	echo "<tr>";
	}
   while ($counter < $fd)
   	{
	echo "<td   >";
	
	
	
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
	$endline = (($counter + $daycount - + $cal_offset) % 7);
	$day = str_pad($daycount, 2, "0", STR_PAD_LEFT);
	//$dayname  = date("l",mktime(0, 0, 0, $month, $daycount,  $year));
	$dayname  = strftime("%A",mktime(0, 0, 0, $month, $daycount,  $year));
	$t = mktime(0, 0, 0, $month, $day,  $year);
	
	if ($endline == 1) { echo "<tr>";}
	echo "<td  valign=\"top\"";
	// if ($thisyear.$thismonth.$thisday == $year.$month.$day)
				//{
				//echo " id=\"scrollpoint\" class=\"accent\"";
				//} 
				
	echo "><span style=\"font-size:large\"><b>$daycount</b></span>&nbsp;&nbsp; $dayname<br><br>";
	
	//display public holiday if any
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
					echo "<a href=\"editcalendar.php?m=$m&amp;event=edit&amp;event_id=$id&amp;t=$t\" style=\"color: $array_colour[$key]\">$array_name[$key]</a><br>
					<span style=\"font-size:smaller\">";
					
					$_location = $array_location[$key];
					
					if (key_exists($_location,$branches)) {echo "$branches[$_location]<br>";}
					else {
					
						if ($array_location[$key] != "") {echo "$array_location[$key]<br> ";}
						}			
					echo "$array_time[$key]";
                                        if ($array_finishtime[$key] == 1) {echo " - $array_endtime[$key]";}
                                        echo "</span><br>";
					if ($array_cancelled[$key] == 1)
						{
						echo "<span style=\"color:#ff3333;font-size:smaller\">$phrase[152]</span><br>";
						}
					echo "<br>";
					}
				}
				}

	echo "<a href=\"editcalendar.php?m=$m&amp;t=$t&amp;event=add\" class=\"addevent\"> </a>
	
	
	
	</td>";
	
   				
				
				
			   if ( $endline == 0)
			   	{
				echo "</tr>\n";
				}
				$daycount++;
   }
   
   //displays blank cells at end of month
   
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
    <tr class=\"accent\"><td align=\"left\" ><br>&nbsp;&nbsp;
   <a href=\"editcalendar.php?m=$m&amp;t=$lastmonth\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center;\" ><br><span style=\"font-size:2em\">$display</span>
   <br>
   <br>
   
   
   
   </td><td style=\"text-align:right\"><br><a href=\"editcalendar.php?m=$m&amp;t=$nextmonth\">$phrase[155]</a>&nbsp;&nbsp;</td></tr>
   </table><br><br>";
   
   
   //end display calendar
   }
   
   echo "</div>";
   
   include ("../includes/footer.php");