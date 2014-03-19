<?php


if (!isset($DATEFORMAT)) {$DATEFORMAT = "%d-%m-%Y";}

include ("../includes/initiliaze_page.php");
include ("resource_paid.php");



$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$ip = ip("pc"); 

$integers[] = "month";
$integers[] = "year";
$integers[] = "resource_no";
$integers[] = "bookingno";
$integers[] = "t";
$integers[] = "checkout";
$integers[] = "startminute";
$integers[] = "starthour";
$integers[] = "endminute";
//$integers[] = "endhour";
$integers[] = "number";
$integers[] = "bookinggroup";
$integers[] = "duplicate";
$integers[] = "existingBookingGroup";

foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value; }
	else {$$value = $_REQUEST[$value];}
	}
}	

//print_r($_REQUEST);


if (isset($_REQUEST["endhour"]))
{
    $endhour = $_REQUEST["endhour"];
    if ($endhour == "midnight")
    {
        $endhour = 23;
        $endminute = 59;
       
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
		
		$sql = "select name from modules where m = '$m'";
	$DB->query($sql,"resourcebooking.php");
	$row = $DB->get();
	$modname = $row["name"];
$datepicker = "yes";
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");
		
		
		
		echo "<div style=\"text-align:center\"><h1>$modname</h1>";	
                

           

$monthsvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12');
$daysvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12', '13', '14', '15', '16', '17',
		'18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31');
		
	
	//display dynamic module adminstration page
	
	
	$sql = "select name from modules where m = \"$m\"";
	$DB->query($sql,"resourcebooking.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);
	if (isset($_REQUEST["custom"])) { $custom = $_REQUEST["custom"];}
	
	
	
	
			
 if ( isset($_REQUEST["addbooking"])  && $access->thispage > 1)
 
 
       {

      
     //  	print_r($_REQUEST);
       	
        $name = urlencode($_REQUEST["name"]);
        
     //create return link
     
     $link = "<p style=\"margin:1em\"><a href=\"resourcebooking.php?m=$m&event=book&starthour=$starthour&startminute=$startminute&endhour=$endhour&endminute=$endminute&t=$t&resource_no=$resource_no&name=$name\"><button>Try again</button></a>
          </p>";
     
     
     
     
     
       	
       	
       	  	$sql = "select * from resource where m = '$m'";
     
		$DB->query($sql,"resourcebooking.php");
	while ($row = $DB->get())
		{
		$index= $row["resource_no"];
                $openinghours = $row["openinghours"];
		$rnames[$index] = $row["resource_name"];
		}
		
		
			$venue = $_REQUEST["venue"];
      	$venues = array();
      		foreach($venue as $id => $value)
      		{
      		if ($value == "on") {$venues[] = $id;}	
      		}
      	
		
		$resource_no = $DB->escape($venues[0]);
                
                
                
                $closure_resource= array();
                 $opening_resource = array();
        
                 
            
                 
                 
                 
                if ($DB->type == "mysql")
			{   
                        $sql = "select resource_no, UNIX_TIMESTAMP(date_blocked) as start, UNIX_TIMESTAMP(date_finish) as finish from resource_closures";
                        }
                else
                {
                   $sql = "select resource_no, strftime('%s',date_blocked) as start, strftime('%s',date_finish) as finish from resource_closures";   
                }
                $DB->query($sql,"resourcebooking.php");
                while ($row = $DB->get()) 
		{
                 $closure_resource[] = $row["resource_no"];   
                 $closure_start[] = $row["start"];
                 $closure_finish[] = $row["finish"] + 86399;
                }
                
                
            if ($openinghours == 1)
            {
               
                
                $sql = "select * from resource_openinghours";
                $DB->query($sql,"resourcebooking.php");
                while ($row = $DB->get()) 
		{
                 $opening_resource[] = $row["resource_no"];   
                 $opening_hour[] = substr($row["openinghour"],0,2);
                 $opening_minute[] = substr($row["openinghour"],2,2);
                 $closing_hour[] = substr($row["closinghour"],0,2);
                 $closing_minute[] = substr($row["closinghour"],2,2);
                 $opening_open[] = $row["open"];
                 $opening_day[] = $row["day"];
                 }		
                  //  print_r($opening_day);
            }
                
                
                
                
                
                
                
       	
   //    	$sql = "select * from resource_custom_fields where resource = '$resource_no'";
        
        $sql = "select * from resource_custom_fields,resource_fields  where 
resource_custom_fields.fieldno = resource_fields.field and                           
resource = '$resource_no' order by resource, ranking";
        
        
     //echo $sql;
		$DB->query($sql,"resourcebooking.php");
	while ($row = $DB->get())
		{
		$field = htmlspecialchars($row["fieldno"]);
		$custom_label[$field] = htmlspecialchars($row["label"]);
		$custom_type[$field] = htmlspecialchars($row["type"]);
		$custom_menu[$field] = htmlspecialchars($row["menu"]);
		
	}
	
	//print_r($custom_type);
       	
       	
       	  $name = $DB->escape($_REQUEST["name"]);
							  
							  $paid = $DB->escape($_REQUEST["paid"]);
							  $staffname = $DB->escape($_REQUEST["staffname"]);
						
								$displaystaffname = $_REQUEST["staffname"];
		$displayname = $_REQUEST["name"];
       	/*
       	 	$xml_output  = "<?xml version=\"1.0\"?>\n";
			$xml_output .= "<fields>\n"; 
			
		if (isset($custom))
		{
			foreach($custom as $key => $value)
			{
			
				
			$xml_output .= "<field>\n"; 
			$xml_output .= "<field_id><![CDATA[$key]]></field_id>\n"; 
			$xml_output .= "<field_label><![CDATA[$custom_label[$key]]]></field_label>\n"; 
			$xml_output .= "<field_value><![CDATA[$value]]></field_value>\n"; 
			$xml_output .= "<menu><![CDATA[$custom_menu[$key]]]></menu>\n"; 
			$xml_output .= "<type><![CDATA[$custom_type[$key]]]></type>\n";
			$xml_output .= "</field>\n"; 
			
			}	
		}
		
		$xml_output .= "</fields>\n"; 
		
		$xml_output = $DB->escape($xml_output);
		
		//echo $xml_output;
	*/	
       	
        //enter booking in database
        
      	if (isset($_REQUEST["number"])) {$number = $_REQUEST["number"];}
      	else {$number = 1;}
	 
		   
      
      	
      	$recurrent = $_REQUEST["recurrent"];
		    $enddate = $_REQUEST["enddate"];
		   if (isset($_REQUEST["interval"])) { $interval = $_REQUEST["interval"];}
		   
		   $dates = $_REQUEST["dates"];
		   
                   
                   $GETBOOKINGGROUP = "no";
                   $UPDATEBOOKINGGROUP = "no";
                   
                   if (isset($existingBookingGroup))
                   {
                   $bookinggroup = $existingBookingGroup;
                   }
                  
                   
                    elseif (isset($duplicate))
                   {
                    $GETBOOKINGGROUP = "yes";  
                    $UPDATEBOOKINGGROUP = "yes";
                   }
                   
                   elseif (count($venues) > 1 || $recurrent == "yes" )
                   {
                    $GETBOOKINGGROUP = "yes";   
                   }
                   
		   
                   
		   else {
		   		
		$bookinggroup = 0;
		   }
                   
                   if ($GETBOOKINGGROUP == "yes")
                   {
                       
                     $sql = "select max(bookinggroup) as bookinggroup from resourcebooking";
		   	 $DB->query($sql,"resourcebooking.php");
		   	 $row = $DB->get();
			$bookinggroup =$row["bookinggroup"] + 1;  
                   }
                   
                   if ($UPDATEBOOKINGGROUP == "yes")
                   {
                       $sql = "update resourcebooking set bookinggroup = '$bookinggroup' where bookingno = '$duplicate'";
                       $DB->query($sql,"resourcebooking.php");
                   }
                   
		   
		   	$counter = 1;
		   	$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
		   	
		   	if (preg_match ($pattern ,$enddate))
                             		{
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
						

                             		} else {$ERROR = "Invalid date format e";}
                             		
                             		
                $startdate = $dates[0];   
               // echo "startdate is" . gettype($startdate);         		
            	if (preg_match ($pattern ,$startdate))
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
						

                             		} else {$ERROR = "Invalid date formats";}
		  // 	exit();
                             		//echo "<br>$endhour,$endminute,0,$em,$ed,$ey $enddate<br>";
                             		
                                        
            $_start =  mktime ($starthour,$startminute,0,$sm,$sd,$sy);
            
            
			$_end =  mktime ($endhour,$endminute,0,$em,$ed,$ey);   
//$_end =  mktime (24,0,0,1,1,2014);                          
                   //   echo "_end is $_end";       		
			
			if ($interval == "monthlybyweekday")
                                {					  
                                $weekcounter = 0;
                                
                                 $daysinmonth = date("t",$_start);
                                
                                 $daynum = date("j",$_start);
                                $weekday = date("D",$_start);
                           
								for($x=1;$x < $daysinmonth + 1;$x++)
								
								{
								
								$dayname =  date("D",mktime (0,0,0,$sm,$x,$sy));

								 if($dayname == $weekday)
 								{
								 
								 $weekcounter ++;	
 								}
								
								if ($daynum == $x) break;

								}
                                	
                                		
                                }
			
			
			
            $duration = $_end - $_start;		
           if ($duration < 0 ) {$duration = 0; $ERROR = "$phrase[432] $link"; }                  		
           
                             		
            while ($counter < $number && $number < 52)
            {
            
            if ($interval == "day")
            {
            $array_start[$counter] =  mktime ($starthour,$startminute,0,$sm,$sd + $counter,$sy);
            }
            
            elseif ($interval == "week")
            {
            $array_start[$counter] =  mktime ($starthour,$startminute,0,$sm,$sd + ($counter * 7),$sy);
            }
             elseif ($interval == "fortnight")
            {
            $array_start[$counter] =  mktime ($starthour,$startminute,0,$sm,$sd + ($counter * 14),$sy);
            }		
            elseif ($interval == "monthlybydate")
            {
            $array_start[$counter] =  mktime ($starthour,$startminute,0,$sm + $counter,$sd,$sy);
            }
             elseif ($interval == "monthlybyweekday")
            {
          	$daysinmonth =  date("t",mktime(0,0,0,$sm + $counter,01,$sy));
            $match = 0;	
            	for($x=1;$x < $daysinmonth + 1;$x++)

										{
										
										$test =  date("D",mktime (0,0,0,$sm + $counter,$x,$sy));

										 if($test == $weekday)
 										{
								 
										 $match++;	
										
 										}
										//echo "$test $weekday $weekdaycounter match is $match $x $startmonth $startyear<br>";
										if ($weekcounter == $match) 
										{
										$array_start[$counter] =  mktime ($starthour,$startminute,0,$sm + $counter,$x,$sy);	
										break;
										}
										}
            	
            	
            }
            $array_end[$counter] =  $array_start[$counter] + $duration;
            $array_eventtime[$counter] = date("YmdHis",$array_start[$counter]);	
            	
            	
            $counter++;	
            	
            	
            }
                             		
                             		
                             		
		   	foreach($dates as $index => $value)
                             	{
                             		//echo "value is $value";
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
                             			
						//	echo "value is $value sd is $sd sm is $sm sy is $sy";
						    $array_start[$counter] =  mktime ($starthour,$startminute,0,$sm,$sd,$sy);
						   $array_end[$counter] =  $array_start[$counter] + $duration;
                            $array_eventtime[$counter] = date("YmdHis",$array_start[$counter]);

                             		} //else {$ERROR = "Invalid date format s";}





                             		$counter++;
                             	}
		
	
				//print_r($_REQUEST);
				//print_r($array_eventtime);
				
				//exit();
	
		 
	

					// print_r($array_start);
					// print_r($array_end);
							
						//print_r($rnames);	
					  
							
							     
								$day =  date("Ymd", $t);
							   //add booking
						
							   
						 if ($_REQUEST["name"] == "")
							       {
							       $ERROR = $phrase[434];  
								   
							       }
								
							if (isset($ERROR))
							{
							//	echo "<span class=\"red\">$ERROR  tt</span>";
							}
							else 
							{
							foreach($array_start as $index => $value)
							{
								
							
								
							foreach($venues as $i => $resource_no)
							{	
							$closed = "no";
							if (!is_integer($resource_no) || strlen($resource_no) > 11)		{break;}
								
							$date = strftime("%x", $value);
                                                        $time = date("g:ia",$value);
                                                        $sdaynum = date("w",$value);
                                                        $sday = date("d",$value);
                                                        $smonth = date("m",$value);
                                                        $syear = date("Y",$value);
                                                        $edaynum = date("w",$array_end[$index]);
                                                        $eday = date("d",$array_end[$index]);
                                                        $emonth = date("m",$array_end[$index]);
                                                        $eyear = date("Y",$array_end[$index]);
                                
                                                          //check that location opening hours 
                                                          foreach($opening_resource as $c => $r)
                       { if ($r == $resource_no && $openinghours == 1) {
                           if ($opening_day[$c] == $sdaynum && $opening_open[$c] == 0) { $closed = "yes"; }
                           if ($opening_day[$c] == $edaynum && $opening_open[$c] == 0) { $closed = "yes";}
                           $open = mktime($opening_hour[$c], $opening_minute[$c],0,$smonth,$sday,$syear);
                           if ($opening_day[$c] == $edaynum && $value < $open) { $closed = "yes";}
                           $close = mktime($closing_hour[$c], $opening_minute[$c],0,$emonth,$eday,$eyear);
                           if ($opening_day[$c] == $edaynum && $value > $close) { $closed = "yes";}
                           
                           
                            //check no closed days in booking duration
                                $counter = $value;
                                while ($counter < $array_end[$index])
                                {
                               $counterday = date("w",$counter);
                                if ($opening_day[$c] == $counterday && $opening_open[$c] == 0)  {$closed = "yes";}
                                $counter = $counter + (24 * 60 * 60);
                                }
                           
                           
                      }}
                        
                            //check for closure
                           foreach($closure_resource as $c => $r)
                                    { if ($r == $resource_no) 
                                        {
                                    
                                                        
                                     if     (($closure_finish[$c] >= $array_end[$index] and $closure_start[$c] < $array_end[$index])
							   or ($closure_start[$c] <= $value and $closure_finish[$c] > $value)
							   or ($closure_start[$c] >= $value and $closure_finish[$c] <= $array_end[$index]))
                                            { $closed = "yes";}

                                    }
                                    }                        
                                  	 
								
								 $sql = "select bookingno from resourcebooking where resource = \"$resource_no\"
							  and ((endtime >= \"$array_end[$index]\" and starttime < \"$array_end[$index]\")
							   or (starttime <= \"$value\" and endtime > \"$value\")
							   or (starttime >= \"$value\" and endtime <= \"$array_end[$index]\"))
							  and cancelled = \"0\" ";
								
							  $DB->query($sql,"resourcebooking.php");
							 
							
	
							  $num_rows = $DB->countrows();
                                                          
                                                     
							  if ($num_rows > 0 || $closed == "yes")
							  {
							 
							
								$displayresult[] = "<span class=\"red\">$phrase[224] $rnames[$resource_no] $time $date
								$phrase[727]";  	
									
                                                      $displayTryAgain = true;
							  	
							  }
							else 
							{	
							
							
								
								if (!isset($ERROR))
								{
								
								$emailresult[] = $displayresult[] = "$phrase[225] $rnames[$resource_no] $time $date";
							
							  $requeststart = $value;
							  $requestend = $array_end[$index];
							if ($checkout != 0) {$checkout = time();}	
							  		  
							  							  
					$now = time();
							
							
							   $sql = "INSERT INTO resourcebooking VALUES(NULL,'$requeststart','$requestend','$name','$paid', '$resource_no',
							   '$ip','$staffname','$_SESSION[username]','$now','','','0','0','$bookinggroup','$checkout','0')";
						
							    $DB->query($sql,"resourcebooking.php");
							//echo $sql;
					  
                                                            $bookingno = $DB->last_insert();
                                                            
                                                            
                                                         //   print_r($_REQUEST);
                                                            
                                                            
                                                            if (isset($_REQUEST["custom"]))
                                                            {
      
                                                            $custom= $_REQUEST["custom"];	
                                                            foreach($custom as $key => $_value)
                                                            {
                                                            $key  = $DB->escape($key);
                                                            $value  = $DB->escape($value);
                                                             
                                                            $sql = "insert into resource_custom_values values('$key','$resource_no','$bookingno','$value')";
                                                          // echo $sql;
                                                            $DB->query($sql,"resourcebooking.php");   
                                                            }
                                                            }
                                                            
                                                            
                                                            
                                                            
                                                            
								}
							}
							
							}		
						}// ends for each
       }
								
								
					
								
				
							  

							  
							
   }
   
      // echo "<hr>";					 
 if (isset($_REQUEST["updatebooking"]) && $access->thispage > 1)


       {
       	
 //      	print_r($_REQUEST);
       	
		$name = $DB->escape($_REQUEST["name"]);		  
		$paid = $DB->escape($_REQUEST["paid"]);
		$displaystaffname = $_REQUEST["staffname"];
		$displayname = $_REQUEST["name"];				  
		$staffname = $DB->escape($_REQUEST["staffname"]);
	    $cancelled = $DB->escape($_REQUEST["cancelled"]);
	    $bookinggroup = $DB->escape($_REQUEST["bookinggroup"]);
	   
 		
       	
 		$startdate = $_REQUEST["startdate"];
 		$enddate = $_REQUEST["enddate"];	
 		
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
 		
           
         
             /*
 		
 		$sql = "select resource,openinghours from resourcebooking, resource where resource.resource_no = resourcebooking.resource and bookingno = '$bookingno'";
                $DB->query($sql,"resourcebooking.php");
		$row = $DB->get();
		//$custom_data =$row["custom_data"];
                $resource_no =$row["resource"];
                $openinghours =$row["openinghours"];
       	echo $sql;
            */ 
                /*
 		if (isset($_REQUEST["custom"]))
		{
      
		$custom= $_REQUEST["custom"];			
			
		
		 if ( $custom_xml = simplexml_load_string($custom_data))
		 {
		 //	echo "hello custom data ";
		 //update xmlobject	
		 foreach ($custom_xml as $field) 
		 {
		 foreach($custom as $key => $value)
			{
			if ($key == $field->field_id)
				{
				$field->field_value = $value;
				}
				
			}
		 	
		 }
		
		 
		 //create xml string fro database
		 	 	$xml_output  = "<?xml version=\"1.0\"?>\n";
			$xml_output .= "<fields>\n"; 
			
		
			foreach ($custom_xml as $field) 
			{
			
				
			$xml_output .= "<field>\n"; 
			$xml_output .= "<field_id><![CDATA[$field->field_id]]></field_id>\n"; 
			$xml_output .= "<field_label><![CDATA[$field->field_label]]></field_label>\n"; 
			$xml_output .= "<field_value><![CDATA[$field->field_value]]></field_value>\n"; 
			$xml_output .= "<menu><![CDATA[$field->menu]]></menu>\n"; 
			$xml_output .= "<type><![CDATA[$field->type]]></type>\n";
			$xml_output .= "</field>\n"; 
			
			}	
		
		
		$xml_output .= "</fields>\n"; 
		
		$custom_data = $DB->escape($xml_output);
		//echo $custom_data;
		 
		 }
		}
       	*/
			
	
		$sql = "select * from resourcebooking where bookingno = '$bookingno'";
		//echo $sql;
			 $DB->query($sql,"resourcebooking.php");
			$row = $DB->get();
			$booking[] =$row["bookingno"];	//$bookingno
			$_s =  $row["starttime"];
		        $resources[] = $row["resource"];
			$checkedin[] =$row["checkin"];
			$checkedout[] =$row["checkout"];
       	  //  $bookinggroup = $row["bookinggroup"];
    
       	$_start = mktime($starthour,$startminute,0,$sm,$sd,$sy)  ;  
       	 $starttime[] = $_start;
       	
         $_end = mktime($endhour,$endminute,0,$em,$ed,$ey)  ;  
         $endtime[] = $_end;
         
         $duration = $_end - $_start; 
       	 $startchange =  ($_start - $_s) / 86400; //how many days booking has moved
       	 if ($startchange < 0) {$startchange = ceil($startchange);} else {{$startchange = floor($startchange);}}
       	 
     
      // 	 echo "start is $_start end is $_end";
       	
       	
       	if ($bookinggroup > 0)
       	{
       		
       		$sql = "select * from resourcebooking where bookinggroup = '$bookinggroup' and bookingno != '$bookingno'";
       		 $DB->query($sql,"resourcebooking.php");
			while ($row = $DB->get()) 
		{
			$booking[] =$row["bookingno"];	
                        $resources[] = $row["resource"];	
			$oldstart =$row["starttime"] ;
			
			$_d = date("d",$oldstart);
			$_m = date("m",$oldstart);
			$_y = date("Y",$oldstart);
			
			$newstart = mktime($starthour,$startminute,0,$_m,$_d + $startchange,$_y)  ; 
			$starttime[] = $newstart;
			$newend = $newstart + $duration;
			$endtime[] =$newend;
                        
                      //  echo "newend is $newend";
			$checkedin[] =$row["checkin"];
			$checkedout[] =$row["checkout"];
		}
	
	
       	}
       
     
		
		foreach($starttime as $index => $start)
							{
								
	

                     $closure_resource = array();
                    
                         if ($DB->type == "mysql")
			{   
                        $sql = "select resource_no, UNIX_TIMESTAMP(date_blocked) as start, UNIX_TIMESTAMP(date_finish) as finish from resource_closures";
                        }
                else
                {
                   $sql = "select resource_no, strftime('%s',date_blocked) as start, strftime('%s',date_finish) as finish from resource_closures";   
                }
                $DB->query($sql,"resourcebooking.php");
                while ($row = $DB->get()) 
		{
                 $closure_resource[] = $row["resource_no"];   
                 $closure_start[] = $row["start"];
                 $closure_finish[] = $row["finish"] + 86399;
                }
                
                $opening_resource = array();
                
            //if ($openinghours == 1)
          //  {
               
                
                $sql = "select * from resource, resource_openinghours where resource_openinghours.resource_no = resource.resource_no and openinghours = 1";
                $DB->query($sql,"resourcebooking.php");
                while ($row = $DB->get()) 
		{
                 $opening_resource[] = $row["resource_no"];   
                 $opening_hour[] = substr($row["openinghour"],0,2);
                 $opening_minute[] = substr($row["openinghour"],2,2);
                 $closing_hour[] = substr($row["closinghour"],0,2);
                 $closing_minute[] = substr($row["closinghour"],2,2);
                 $opening_open[] = $row["open"];
                 $opening_day[] = $row["day"];
                 }		
                  //  print_r($opening_day);
           // }
               
           
            
  
	
				$date = strftime("%x", $start);
				$time = date("g:ia",$start);
                                $sdaynum = date("w",$start);
                                $sday = date("d",$start);
                                $smonth = date("m",$start);
                                $syear = date("Y",$start);
                                $edaynum = date("w",$endtime[$index]);
                                $eday = date("d",$endtime[$index]);
                                $emonth = date("m",$endtime[$index]);
                                $eyear = date("Y",$endtime[$index]);
		   
	    
                                $closed = "no";
                                echo "";
                                
                                
                               
                                  //check that location opening hours 
                                                          foreach($opening_resource as $c => $r)
                       {
                                                       
                           if ($r == $resource_no )
                               {
                        
                             if ($opening_day[$c] == $sdaynum && $opening_open[$c] == 0) { $closed = "yes"; }    
                               
                               
                               
                           if ($opening_day[$c] == $sdaynum && $opening_open[$c] == 0) { $closed = "yes"; }
                           if ($opening_day[$c] == $edaynum && $opening_open[$c] == 0) { $closed = "yes";}
                           $open = mktime($opening_hour[$c], $opening_minute[$c],0,$smonth,$sday,$syear);
                           if ($opening_day[$c] == $edaynum && $start < $open) { $closed = "yes";}
                              $close = mktime($closing_hour[$c], $opening_minute[$c],0,$emonth,$eday,$eyear);
                           if ($opening_day[$c] == $edaynum && $value > $close) { $closed = "yes";}
                           

                              //check no closed days in booking duration
                                $counter = $start;
                                while ($counter < $endtime[$index])
                                {
                               $counterday = date("w",$counter);
                                if ($opening_day[$c] == $counterday && $opening_open[$c] == 0)  {$closed = "yes";}
                                $counter = $counter + (24 * 60 * 60);
                                }
                    
                               }
                             
                             
                       }
                            //check for closure
                           foreach($closure_resource as $c => $r)
                                    { if ($r == $resource_no) 
                                        {
                                    
                                                        
                                     if     (($closure_finish[$c] >= $endtime[$index] and $closure_start[$c] < $endtime[$index])
							   or ($closure_start[$c] <= $start and $closure_finish[$c] > $start)
							   or ($closure_start[$c] >= $start and $closure_finish[$c] <= $endtime[$index]))
                                            { $closed = "yes";}

                                    }
                                    }                      
                                
                                
	   
	   						 $sql = "select bookingno from resourcebooking where resource = \"$resources[$index]\"
							  and ((endtime >= \"$endtime[$index]\" and starttime < \"$endtime[$index]\")
							   or (starttime <= \"$start\" and endtime > \"$start\")
							   or (starttime >= \"$start\" and endtime <= \"$endtime[$index]\"))
							  and cancelled = \"0\" and bookingno <> \"$booking[$index]\"";
							
						
							 // echo $sql;
							  
								$DB->query($sql,"resourcebooking.php");
	
							  $num_rows = $DB->countrows();
							
                                                          if ($closed == "yes")
                                                          {
                                                           $displayresult[] = "<span class=\"red\">$phrase[770] $time $date
	      							 $phrase[494]</span>";   //closed
                                                           $displayTryAgain = true;
                                                          }
                                                          
                                                            elseif ($_start >= $_end)
							
	      							{
	      							$displayresult[] = "<span class=\"red\">$phrase[770] $time $date
	      							 $phrase[222]</span>"; //The finish time must be later than the start time.
	      							//echo "failed";
                                                                $displayTryAgain = true;
	      							}
							   elseif ($name == "")
							
	      							{
	      							$displayresult[] = "<span class=\"red\">$phrase[770] $time $date
	      							$phrase[230]</span>"; //Name must be supplied.";
	      							//echo "failed";
                                                                $displayTryAgain = true;
	      							}
							   elseif ($num_rows > 0 && $cancelled == 0)
							
							      {
							     $displayresult[] = "<span class=\"red\">$phrase[770] $time $date
							     $phrase[727]</span>"; //"Time not available";
                                                             $displayTryAgain = true;
							    // echo "failed";
                                                             
                                                              // if ($num_rows > 0) {echo "num_rows $num_rows resource $resource_no";}
                                                       // if ($cancelled == 0)  	{echo "cancelled == 0";}
							      }
							
							
							
							      else
							      {
									//echo "start is $start";
							   if ($cancelled == 2)
							   {
							   	 $sql = "delete from  resourcebooking  where bookingno = '$booking[$index]'";
													  
							   $DB->query($sql,"resourcebooking.php");
                                                           
                                                           // resource_custom_fields.fieldno = resource_fields.field and resource_fields.resource = '$rno'
                                                              $sql = "delete from resource_custom_values where booking = '$booking[$index]' "; 
                                                                $DB->query($sql,"resourcebooking.php");
							   	
							   	$displayresult[] = "<span class=\"red\">$phrase[773] $time $date</span> "; 
								$emailresult[] = "$phrase[773] $date";
							   	
								}
								else 
								{
									
								$now = time();
								
								 if ($cancelled == 1)
							   			{
							   			   $displayresult[] = "<span class=\"red\">$phrase[772] $time $date</span> "; 
								$emailresult[] = "$phrase[772] $time $date";
										}
								else 
										{
									 $emailresult[] = $displayresult[] = "$phrase[771] $time $date "; 
										}
										
										  $sql = "update resourcebooking set starttime = '$start', endtime = '$endtime[$index]', name ='$name', paid= '$paid', staffname = '$staffname'
							 , ip = '$ip', username = '$_SESSION[username]', bookeddate = '$now', cancelled = $cancelled where bookingno = '$booking[$index]'";
										  $DB->query($sql,"resource.php");
										//  echo "sql is $sql";
										  
									     if (isset($_REQUEST["custom"]))
                                                            {
                                                            
                                                           //get list of cutom_values no record to update then insert   
                                                              $fieldlist = array();                   
                                                              $sql = "select  field_no from resource_custom_values where booking = '$booking[$index]'";   
                                                              $DB->query($sql,"resource.php");
                                                                while ($row = $DB->get())
                                                                {
                                                                  $fieldlist[] = $row["field_no"];  
                                                                    
                                                                }
	
	
                                                                                 
                                                            $custom= $_REQUEST["custom"];	
                                                            foreach($custom as $key => $value)
                                                            {
                                                            $key  = $DB->escape($key);
                                                            $value  = $DB->escape($value);
                                                           
                                                            if (in_array($key,$fieldlist))
                                                            {
                                                             $sql2 = "update resource_custom_values set field_value = '$value' where field_no = '$key' and room = '$resources[$index]' and booking ='$booking[$index]'";
                                                            }
                                                            else
                                                            {
                                                             $sql2 = "insert into  resource_custom_values values('$key','$resources[$index]','$booking[$index]','$value')";   
                                                            
                                                            }
                                                        //  echo "$sql2<br>";
                                                         //   $sql = "update resourcebooking resource_custom_values set value  = '$value' where booking = '$booking[$index]' and
                                                          //      room = '$resource_no' and fieldno = '$key'";
                                                            $DB->query($sql2,"resourcebooking.php");   
                                                            }
                                                            }
                                                                                  
                                                                                  
                                                                                  
                                                                                  
										  	  	
									if ($checkedout[$index] > $checkedin[$index])
									{ $current = "out";} else { $current = "in";}
									
									if ($current == "in" && $checkout == 1)
									{
									//checking out	
										  $sql = "update resourcebooking set starttime = '$start', endtime = '$endtime[$index]', name ='$name', paid= '$paid', staffname = '$staffname', ip = '$ip', username = '$_SESSION[username]', bookeddate = '$now',checkout = '$now', cancelled = $cancelled where bookingno = '$booking[$index]'";
									
                                                                                  $DB->query($sql,"resourcebooking.php");
                                                                        }
									
									if ($current == "out" && $checkout == 0)
									{
									//checking in
										  $sql = "update resourcebooking set starttime = '$start', endtime = '$endtime[$index]', name ='$name', paid= '$paid',  staffname = '$staffname', ip = '$ip', username = '$_SESSION[username]', bookeddate = '$now',checkin = '$now', cancelled = $cancelled where bookingno = '$booking[$index]'";
									
                                                                                  $DB->query($sql,"resourcebooking.php");
                                                                        }
							
							
                                                                        
                                                                        
                                                                        
                                                                        
                                                                        
							   
							   		
										
								}
							 
							
							   
							  }
        
							}

   
       
	}
   

   
 if (isset($ERROR)) {  error($ERROR);}
  

 
	
 if (isset($_REQUEST["deletebooking"]) && $access->thispage > 1)

       {	
    	
	
	
	echo "<b>$phrase[14]</b><br><br>
	<a href=\"resourcebooking.php?m=$m&amp;update=delete&bookingno=$bookingno&amp;t=$t&event=cal&amp;resource_no=$resource_no\">$phrase[12]</a> | <a href=\"resourcebooking.php?m=$m&amp;t=$t&amp;event=cal&amp;resource_no=$resource_no\">$phrase[13]</a>";

	
    	}
	
elseif (isset($displayresult))
    	{
    		$name  = htmlspecialchars($_REQUEST["name"]) ;
	echo "<div style=\"text-align:center\"><h2>$phrase[240]</h2>
	
	<b>$name</b><br><br><ul style=\"margin-left:40%\">
	";
	
	
	
    	
    		
    	
    	  foreach($displayresult as $index => $result)
						

              {
              	
				$result = nl2br($result);
				echo "<li>$result</li> ";
              	
               }
               
    	     
        
     
           echo "</ul><br>";
               
             if (isset($displayTryAgain)) {echo $link;}
           
           
           echo "    
           <form method=\"post\" action=\"resourcebooking.php\">
            <input type=\"hidden\" name=\"m\" value=\"$m\">";
           if (isset($_REQUEST["view"]) && $_REQUEST["view"] == "cal")
           {
          echo " <input type=\"hidden\" name=\"event\" value=\"cal\">";
           }
    
           
           
            echo "<input type=\"hidden\" name=\"resource_no\" value=\"$resource_no\">
            <input type=\"hidden\" name=\"t\" value=\"$t\">
           <input type=\"submit\" name=\"submit\" value=\"$phrase[34]\">
           </form>";
            
          
            
           echo "</div>";
           
           
 
}



 elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "search" && $access->thispage > 1)
	{
       
  
     
    
     
     
	echo "<h2>";
        if (isset($_REQUEST["view"])) {echo $phrase[1100];}  else {echo $phrase[282];}    
             
                     
                     echo "</h2>
                    
 <a href=\"resourcebooking.php?m=$m\" >$phrase[1019]</a>  <br><br>
               <form style=\"display:inline\">";
        if (isset($_REQUEST["view"]))
        {echo "<input type=\"hidden\" name=\"browse\" value=\"\"><input type=\"hidden\" name=\"view\" value=\"summary\">";}
 else {
        echo "<input type=\"text\" name=\"keywords\"";
        if (isset($_REQUEST["keywords"])) { echo " value=\"" . $_REQUEST["keywords"] . "\"";}

        echo ">"; 
        
         }
        echo "<select name=\"rno\"><option value=\"all\">All</option>";
        
         $sql = "select * from resource where m = '$m'";
	 $DB->query($sql,"resourcebooking.php");
		while ($row = $DB->get())
		{
			$rno =$row["resource_no"];
			$resource_name =$row["resource_name"];
                        
                        $rnames[$rno]= $resource_name;
                        echo "<option value=\"$rno\"";
                        if (isset($_REQUEST["rno"]) && $_REQUEST["rno"] == $rno) { echo " selected";}
                        echo ">$resource_name</option>";
                }
        
        echo "</select>";
        
        if (isset($_REQUEST["month"]))  {$selectmonth = $_REQUEST["month"];} else {$selectmonth = date("n");}
        echo "<select name=\"month\">";
        
        
        // create month array

$counter = 1;
while ($counter <13)
{
$monthname = strftime("%B",mktime(0, 0, 0, $counter, 01,  $year));
echo "<option value=\"$counter\"";
if ($selectmonth == $counter) { echo " selected";}
echo ">$monthname</option>";
$counter++;
}
        
       echo "<option value=\"0\"";
        if ($selectmonth == "0") {echo " selected";}
        echo ">$phrase[1101]</option> </select> 
<select name=\"year\">";
        $year = date("Y") - 10;
        $endyear  = date("Y") + 10;
       if (isset($_REQUEST["year"]))  {$selectyear = $_REQUEST["year"];} else {$selectyear = date("Y");}
       while ($year < $endyear) {
           echo "<option value=\"$year\" ";
           if ($year == $selectyear) { echo " selected";}
           echo ">$year</option>";
           $year++;
       }
       
        echo "<option value=\"0\"";
        if ($selectyear == "0") {echo " selected";}
        echo ">$phrase[1101]</option></select>

<input type=\"hidden\" name=\"m\" value=\"$m\"> <input type=\"hidden\" name=\"event\" value=\"search\">  <input type=\"submit\"  value=\"";
        
             if (isset($_REQUEST["view"])) {echo $phrase[1100];}  else {echo $phrase[282];}  
        echo "\"></form>
            
<br><br>  
                ";
        
     if (isset($_REQUEST["keywords"]) || isset($_REQUEST["browse"]))
     {
         
        
         $rno = $DB->escape($_REQUEST["rno"]);
         
         $month = $DB->escape($_REQUEST["month"]);
         $year = $DB->escape($_REQUEST["year"]);
         
         $url = "resourceCSV.php?m=$m&rno=$rno&year=$year&month=$month";
         if (isset($_REQUEST["keywords"])) {$url .= "&keywords=" . urlencode($_REQUEST["keywords"]);}
         if (isset($_REQUEST["view"])) {$url .= "&browse=browse";}
         
         echo "<h4>$phrase[329]</h4> <a href=\"$url\">$phrase[1102]</a><br><br>";
         
           //	$sql = "select * from resource_custom_fields where resource = '$rno'";
             
         $custom_label = array();
         
         if ($rno != "all")
         
         {
                $sql = "select distinct * from resource_custom_fields,resource_fields  where 
resource_custom_fields.fieldno = resource_fields.field and resource_fields.resource = '$rno' order by resource, ranking";
                
   // echo $sql;
		$DB->query($sql,"resourcebooking.php");
	while ($row = $DB->get())
		{
		$field = htmlspecialchars($row["fieldno"]);
		$custom_label[$field] = htmlspecialchars($row["label"]);
		$custom_type[$field] = htmlspecialchars($row["type"]);
		$custom_menu[$field] = htmlspecialchars($row["menu"]);
		
	}
     
        
         }
        		
		
			
     $string1 = "";
     $string2 =  "";
       		      if ( $year != "0") {
                          $string1 .= " and FROM_UNIXTIME(starttime,\"%Y\") = '$year' "; 
                          $string2 .= " and FROM_UNIXTIME(starttime,\"%Y\") = '$year' "; 
                      }
                        if ( $month != "0") {
                            $string1 .= "and FROM_UNIXTIME(starttime,\"%c\") = '$month' ";
                            $string2 .= "and FROM_UNIXTIME(starttime,\"%c\") = '$month' ";
                        }
       			
                        if (isset($_REQUEST["keywords"]))
                        {
                             $keywords = trim($_REQUEST["keywords"]);
       			$words = explode(" ",$keywords);
       			
                        
                  
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
                                $value = $DB->escape($value);
				$string1 .= " and (name like '%$value%' or field_value like '%$value%') ";
                                $string2 .= " and (name like '%$value%') ";
				$counter++;
				}	
				if ($keywords == "") {$string1 = " and '1' = '2'"; $string2 = " and '1' = '2'";}
                        }       
                     
              $field_values = array();
              
                if ($rno != "all")
                        
                        {
                     $sql = "select bookingno, field_no, field_value from resource_custom_values, resourcebooking 
                         where resourcebooking.bookingno = resource_custom_values.booking and resource = '$rno' $string1 ";
                        
                    // echo $sql;
  $DB->query($sql,"resourcebooking.php");
        //$field_ids = array();
        
	while	($row = $DB->get())
        {
       	$field_id =$row["field_no"];	
        $bookingno =$row["bookingno"];
        
        $index = $bookingno . "_" . $field_id;
	$field_values[$index]  =$row["field_value"];	
	}                  
                        }                        
                                
    $linktext = "resourcebooking.php?m=$m&rno=$rno&event=" . $_REQUEST["event"] . "&month=" . $_REQUEST["month"] . "&year=" . $_REQUEST["year"];
    if (isset($_REQUEST["browse"])) {$linktext .= "&browse=" . $_REQUEST["browse"];}
    if (isset($_REQUEST["view"])) {$linktext .= "&view=" . $_REQUEST["view"];}
    if (isset($_REQUEST["keywords"])) {$linktext .= "&keywords=" . urlencode($_REQUEST["keywords"]);}
        
        
   echo "<table class=\"colourtable\" style=\"width:90%;margin:0 auto;background:white\">";
        
   $bnamelink = $linktext . "&orderby=bname";
   $rnamelink = $linktext . "&orderby=rname";
   $paidlink = $linktext . "&orderby=paid";
   $startlink = $linktext . "&orderby=start";
   $endlink = $linktext . "&orderby=end";
   $stafflink = $linktext . "&orderby=staff";
   
   echo "<tr><td><a href=\"$bnamelink\">$phrase[226]</a></td><td><a href=\"$rnamelink\">$phrase[233]</a></td><td><a href=\"$startlink\">$phrase[242]</a></td>
       <td><a href=\"$endlink\">$phrase[243]</a></td>";
            foreach ($custom_label as $key => $value) {
                echo "<td>$value</td>";
            }
            echo "<td><a href=\"$paidlink\">$phrase[138]</a></td><td><a href=\"$stafflink\">$phrase[227]</a></td></tr>";
     
           
            
            $orderby = "";
            if (isset($_REQUEST["orderby"]))
            {
            if ($_REQUEST["orderby"] == "bname") { $orderby = " order by name";}
            if ($_REQUEST["orderby"] == "rname") { $orderby = " order by resource";}
            if ($_REQUEST["orderby"] == "paid") { $orderby = " order by paid";}
            if ($_REQUEST["orderby"] == "starttime") { $orderby = " order by starttime";}
            if ($_REQUEST["orderby"] == "endtime") { $orderby = " order by endtime";}
            if ($_REQUEST["orderby"] == "staffname") { $orderby = " order by staffname";}
            }
            
             if ($rno == "all")
                        
                        {
                 $sql = "select resource, name,staffname,resource,bookingno,starttime,endtime,paid from resourcebooking, resource where resource.m = '$m'
                     and resourcebooking.resource = resource.resource_no and cancelled = '0'  $string2";
                        }
 else {
 
            
            $sql = "select resource, name,staffname,resource,bookingno,starttime,endtime,paid from resourcebooking where cancelled = '0' and  resource = '$rno' $string2
                    union
                    select resource, name,staffname,resource,bookingno,starttime,endtime,paid from resource_custom_values, resourcebooking 
                         where resourcebooking.bookingno = resource_custom_values.booking and resource = '$rno' $string1 $orderby
                    ";
            
 }
           //z echo $sql;
            //resource.resource_no = resourcebooking.resource and
             $DB->query($sql,"resourcebooking.php");
      
      
	while	($row = $DB->get())
        {
            $name = htmlspecialchars($row["name"]);
            $staffname = htmlspecialchars($row["staffname"]);
           // $cancelled = $row["cancelled"];
            $bookingno = $row["bookingno"];
             $resource = $row["resource"];
            $starttime = $row["starttime"];
            $start = date("g:ia",$row["starttime"]);
	   $start.= strftime(" %x",$row["starttime"]);	
           $paid = $row["paid"];
           $end = date("g:ia",$row["endtime"]);
	   $end.= strftime(" %x",$row["endtime"]);
           
            
        echo "<tr><td><a href=\"resourcebooking.php?m=$m&event=edit&bookingno=$bookingno&t=$starttime&resource_no=$rno\">$name</a></td>
            <td>$rnames[$resource]</td><td>$start</td><td>$end</td>";
        
         foreach ($custom_label as $key => $value) {
                echo "<td>";
                
                $index = $bookingno . "_" . $key;
                
                if (array_key_exists($index,$field_values)) {echo $field_values[$index];}
                echo "</td>";
            }
       
            echo "<td>";
          if (array_key_exists($paid,$paid_label)) {echo  $paid_label[$paid];}
            
          
           
            
echo "</td><td>$staffname</td></tr>
";
        
        }
       echo "</table>";   
        
     }
     
 }

 elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit" && $access->thispage > 1)
	{
	
		
	 $display = strftime("%A %x",$t);
         $daynumber = date("w",$t);
         
	$resource_no = $DB->escape($resource_no);
	 $sql = "select * from resource where resource_no = '$resource_no'";
	 $DB->query($sql,"resourcebooking.php");
	
		$row = $DB->get();
	$resource_name =$row["resource_name"];
	$description = $row["description"];
			$fee_applicable = $row["fee_applicable"];
			
			//$custom_data = $row["custom_data"];
			$book_multiple_days = $row["book_multiple_days"];
			$notify = $row["notify"];
			$email = $row["email"];
			$print = $row["print"];
			$allowscheckout = $row["checkout"];
                        $openinghours = $row["openinghours"];
			
	
	$hidden = "";
	
	
   if ($openinghours == 1)
            {
       
                $sql = "select * from resource_openinghours where resource_no = '$resource_no'";
              //  echo "$sql";
                $DB->query($sql,"resourcebooking.php");
                while ($row = $DB->get()) 
		{
            
              if ($daynumber == $row["day"]) 
                    {
                  $openinghour = substr($row["openinghour"],0,2);$closinghour = substr($row["closinghour"],0,2);
                     }
                 }		
                
            }
	
            if (!isset($openinghour)) {$openinghour = "1";}
            if (!isset($closinghour)) {$closinghour = "23";}
	
	
			
	echo "
	<script type=\"text/javascript\">
 function pop_window(url) {
  var roompop = window.open(url,'','status,resizable,scrollbars,width=350,height=400');
  	if (window.focus) {roompop.focus()}
 }
</script>
	
 <a href=\"resourcebooking.php?m=$m&amp;t=$t\" >$phrase[1019]</a>  | 
	 <a href=\"resourcebooking.php?m=$m&amp;t=$t&amp;resource_no=$resource_no&amp;event=cal\">$phrase[1020]</a>";
	
	
						 if ($access->thispage > 1) {
						
						//}
						
	if ($print == 1 || $print == 3) {echo " |  <a href=\"rbprint.php?m=$m&amp;bookingno=$bookingno\">$phrase[256]</a> ";} 
	if ($print == 2 || $print == 3) {echo " | <a href=\"javascript:pop_window('rbslip.php?m=$m&amp;bookingno=$bookingno&amp;resource_no=$resource_no')\">$phrase[255]</a> <br>";} 
						}
						
	echo "<h2>$resource_name<br>$display</b></h2>";
        
        
         $sql = "select * from resource_custom_values where booking = '$bookingno' ";
  $DB->query($sql,"resourcebooking.php");
        //$field_ids = array();
        $field_values = array();
	while	($row = $DB->get())
        {
       	$field_id =$row["field_no"];	
	$field_values[$field_id]  =$row["field_value"];	
	}
        
       // print_r($field_values);
        
   $sql = "select * from resourcebooking where bookingno = '$bookingno' ";
  $DB->query($sql,"resourcebooking.php");
		$row = $DB->get();

	$name =$row["name"];	
	
	$starttime =$row["starttime"];	
	$startminute = date("i",$starttime);
	$starthour = date("H",$starttime);
	$startday = date("d",$starttime);
	$startmonth = date("m",$starttime);
	$startyear = date("Y",$starttime);
	$endtime =$row["endtime"];
	$staffname =$row["staffname"];
	$endminute = date("i",$endtime);
	$endhour = date("H",$endtime);	
	$endday = date("d",$endtime);
	$endmonth = date("m",$endtime);
	$endyear = date("Y",$endtime);
	$paid =$row["paid"];	
	//$custom_data =$row["custom_data"];	
	$cancelled =$row["cancelled"];	
	$bookinggroup = $row["bookinggroup"];
	$checkout = $row["checkout"];
	$checkin = $row["checkin"];	
	$ip = $row["ip"];	
	$username = $row["username"];
	$book =$row["bookeddate"];
	$bookeddate = date("g:ia",$book);
	$bookeddate.= strftime(" %x",$book);	
	
	
	
		if ($DATEFORMAT == "%d-%m-%Y")
             {
             $startdate = "$startday-$startmonth-$startyear";
             $enddate = "$endday-$endmonth-$endyear";
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $startdate = "$startmonth-$startday-$startyear";
         $enddate = "$endmonth-$endday-$endyear";
             } else {   $enddate = "$endday-$endmonth-$endyear";}
	
	
	

	$total = 0;
	if ($bookinggroup > 0)
	{
	$sql = "select starttime from resourcebooking where bookinggroup = '$bookinggroup' ";
		$DB->query($sql,"resourcebooking.php");
		while ($row = $DB->get())
		{
		//$times[] = $displaydate = strftime("%A %x",$row["starttime"]);
		$total++;
		}
                
         $hidden .= "<input type=\"hidden\" name=\"existingBookingGroup\" value=\"$bookinggroup\">";
	}
	

	
					
	
	echo "<br>


	
<div class=\"swouter\">
	<FORM method=\"POST\" action=\"resourcebooking.php\" class=\"swinner\"><fieldset><legend>$phrase[26]</legend>
	
";
		if ($cancelled == 1) { echo "<span style=\"color:red;font-size:2em\">$phrase[152]</span>";}
echo <<<EOF
<table  >
	<tr><td class="label">$phrase[226]</td><td align="left">
	<input type="text" name="name" maxlength="100" value="$name" size="60"></td></tr>
	<tr><td  class="label">$phrase[242]</td><td align="left">
	<select name="starthour">
EOF;
	
	
	
		$counter = $openinghour;
		while ($counter <= $closinghour)
		{
		echo "<option value= \"$counter\"";
	if ($counter == $starthour) { echo " selected";} 
	 $hourdisplay = date("g a",(mktime($counter,0,0,0,0,0)));
         if ($counter == 12) {$hourdisplay = $phrase[1123];}
       
		echo ">$hourdisplay</option>
";
                $counter++;
		}
		
		
		
		
		echo "</select>
		
		<select name=\"startminute\">";
		
		$counter = 0;
		
		while ($counter < 60)
		{
		$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
		echo "<option value= \"$counter\"";
		if ($counter == $startminute) { echo " selected";}
		echo ">$counter</option>";	
		$counter++;
		}
	
		
		
		echo "</select>";
		
		
			if ($book_multiple_days > 0)
			{
			echo " <input type=\"text\"  name=\"startdate\" id=\"startdate\" value=\"$startdate\" readonly class=\"datepicker\" size=\"10\"> ";
			}
			
			else { $hidden .= "<input type=\"hidden\" name=\"startdate\" value=\"$startdate\">";}
			
		
		
		echo "</td></tr>
	<tr><td class=\"label\"><b>$phrase[243]</b></td><td >
	<select name=\"endhour\">";

	
	
		$counter = $openinghour;
		while ($counter <= $closinghour)
		{
		echo "<option value= \"$counter\"";
	if ($counter == $endhour) { echo " selected";} 
	 $hourdisplay = date("g a",(mktime($counter,0,0,0,0,0)));
         if ($counter == 12) {$hourdisplay = $phrase[1123];}
          if ($counter == 24) {$hourdisplay = $phrase[1124];}
		echo ">$hourdisplay</option>
";
                $counter++;
		}
		
		echo "</select>
		
		<select name=\"endminute\">";
		
		$counter = 0;
		
		while ($counter < 60)
		{
		$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
		echo "<option value= \"$counter\"";
		if ($counter == $endminute) { echo " selected";}
		echo ">$counter</option>";	
		$counter++;
		}
	
		
		
		echo "</select>";
		
				if ($book_multiple_days > 0)
			{
			echo " <input type=\"text\"  name=\"enddate\" id=\"enddate\" value=\"$enddate\" readonly class=\"datepicker\" size=\"10\"> ";
			}
			
			else { $hidden .= "<input type=\"hidden\" name=\"enddate\" value=\"$enddate\">";}
			
			
		echo "</td></tr>";

                       $sql = "select * from resource_custom_fields,resource_fields  where 
resource_custom_fields.fieldno = resource_fields.field and                           
resource = '$resource_no' order by resource, ranking";
                     //  echo $sql;
  $DB->query($sql,"resourcebooking.php");
    
	while	($row = $DB->get())
        {
                $fieldno = $row["fieldno"];
		$type = $row["type"];
                $menu = $row["menu"];
                $label = $row["label"];
                
             if (array_key_exists($fieldno,$field_values))  {$fvalue = $field_values[$fieldno];} else {$fvalue = '';} 
                
			echo "<tr><td class=\"label\">$label</td><td>";
		if ($type == "t") {echo "<input type=\"text\" name=\"custom[$fieldno]\" size=\"50\" value=\"$fvalue\">";}
	if ($type == "a") {echo "<textarea name=\"custom[$fieldno]\" cols=\"45\" rows=\"6\">$fvalue</textarea>";}
	if ($type == "m") {echo "<select name=\"custom[$fieldno]\">";
	$values = explode("\n", $menu);
	
	 foreach ($values as $indexa => $value)
							{
         $value = rtrim($value);
         $value = rtrim($value);
							echo "
<option value=\"$value\"";
							if ($value == $fvalue) {echo " selected";}
							
							echo "> $value</option>";
							}
	
	echo "</select>";}
	

	echo "</td></tr>";
		 	
		 	
		 }
	
		
	
	if ($fee_applicable == 1)
		{
		echo "	<tr><td  class=\"label\"><b>$phrase[138]</b></td><td >
		<select name=\"paid\">";
		if (isset($paid_label))
		{
		foreach ($paid_label as $index => $label)
		{
			echo "<option value=\"$index\" ";
			if ($paid == $index) {echo " selected";}
			echo ">$label</option>";
		}
		}
		
		
		echo "</select>
		</td></tr>";		
		}
	
		
	else { $hidden .= "	<input type=\"hidden\" name=\"paid\" value=\"0\">";}
	
	//if ($book_multiple_days <> 1)
	//{
	//$hidden .=	"<input type=\"hidden\" name=\"startday\" value=\"$startday\">
	//<input type=\"hidden\" name=\"startmonth\" value=\"$startmonth\">";
	//}
	
	echo "<tr><td class=\"label\" valign=\"top\"><b>$phrase[227]</b></td><td ><input type=\"text\" name=\"staffname\" maxlength=\"50\" value=\"$staffname\">
	</td></tr>";
	if ($bookinggroup > 0)
	{
			echo "<tr><td class=\"label\" valign=\"top\"><b>$phrase[16]</b></td><td >
			<select name=\"bookinggroup\">
		<option value=\"0\" checked> $phrase[768]</option>
		<option value=\"$bookinggroup\"> $phrase[767]  ($total)	</option>	
			</select>					   
</td></tr>";
	}
	if ($allowscheckout == 1)
	{
			echo "<tr><td class=\"label\" valign=\"top\"><b>$phrase[776]</b></td><td ><select name=\"checkout\">";
	  
			if ($checkout > $checkin) {echo "<option value=\"1\">$phrase[12]</option><option value=\"0\">$phrase[13]</option>";}
		else {echo "<option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option>";}
		echo "</select>";
		
		$intime = strftime("%X%p %A %x",$checkin);
		$outtime = strftime("%X%p %A %x",$checkout);
		
		if ($checkout > $checkin) 
		{ 
			
			echo "<br>$phrase[776] $outtime";
		}	
		if ($checkin > $checkout) 
		{ 
			
			echo "<br>$phrase[776] $outtime
			<br>$phrase[777] $intime";
		}
		 echo "</td></tr>";
	}	
						   
						   

	
	
	
	
	echo "<tr><td class=\"label\" valign=\"top\"><b>$phrase[401]</b></td><td ><select name=\"cancelled\">
	<option value=\"0\" ";
	if ($cancelled == 0) {echo "selected";}
	echo ">$phrase[774]</option>
	<option value=\"1\" ";
	if ($cancelled == 1) {echo "selected";}
	echo ">$phrase[152]</option>
	<option value=\"2\">$phrase[24]</option>
	</select></td></tr>
	
	
	
	
	
	<tr><td></td><td >$hidden

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"email\" value=\"$email\">";
	
	$displaydate = strftime("%A %x",$t);
	
	echo "<input type=\"hidden\" name=\"displaydate\" value=\"$displaydate\">";
	if ($allowscheckout == 0) 
	{
	echo "<input type=\"hidden\" name=\"checkout\" value=\"0\">";	
	
	}
	if ($bookinggroup == 0) 
	{
	echo "<input type=\"hidden\" name=\"bookinggroup\" value=\"0\">";	
	}
	
	if (isset($_REQUEST["view"]) && $_REQUEST["view"] == "cal") { echo "<input type=\"hidden\" name=\"view\" value=\"cal\">";}
	
	echo "
	<input type=\"hidden\" name=\"resource_name\" value=\"$resource_name\">
	
	<input type=\"hidden\" name=\"bookingno\" value=\"$bookingno\">
	<input type=\"hidden\" name=\"resource_no\" value=\"$resource_no\">
	<input type=\"hidden\" name=\"t\" value=\"$t\">
	
	
	
	<input type=\"submit\" name=\"updatebooking\" value=\"$phrase[28]\"> <input type=\"submit\" name=\"duplicatebooking\" value=\"$phrase[1103]\" style=\"padding-left:2em\">"; 
	
	
	echo "
	</td></tr>
	
	</table></fieldset></form></div>";
	
	
	
	
	if ($book_multiple_days > 0)
	{

		echo "
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
	}
	

	
	

	
	
if (($_SESSION['userid'] == 1 || $access->thispage > 1))
		{
	echo "<div style=\"margin:2em;clear:both;text-align:center;\">$phrase[532] $username, $bookeddate";
		}
	if ($_SESSION['userid'] == 1) { echo ", $ip ";}
	
	echo "</div>";
	}


	

	 

  elseif ( ( (isset($_REQUEST["event"]) && $_REQUEST["event"] == "book")   || isset($_REQUEST["duplicatebooking"])    ) && $access->thispage > 1)
 
    {
	
	   $hidden = "";
    $day = date("j",$t);
    $daynumber = date("w",$t);
	   
	 $sql = "select * from resource where m = '$m'";
	 $DB->query($sql,"resourcebooking.php");
		while ($row = $DB->get())
		{
			$rno =$row["resource_no"];
			$resource_name[$rno] =$row["resource_name"];
			$description[$rno] = formattext($row["description"]);
			$fee_applicable[$rno] = $row["fee_applicable"];
			$location[$rno] = $row["location"];
			$book_multiple_days[$rno] = $row["book_multiple_days"];
			$notify[$rno] = $row["notify"];
			$email[$rno] = $row["email"];
			$link_name[$rno] = urlencode($row["resource_name"]);
			$recur[$rno] = $row["recur"];
			$allowscheckout[$rno] = $row["checkout"];
                        $openinghours[$rno] = $row["openinghours"];
		}
		
                
                 if ($openinghours[$resource_no] == 1)
            {
       
                $sql = "select * from resource_openinghours where resource_no = '$resource_no'";
             //   echo "$sql";
                $DB->query($sql,"resourcebooking.php");
                while ($row = $DB->get()) 
		{
            
              if ($daynumber == $row["day"]) 
                    {
                  $openinghour = substr($row["openinghour"],0,2);$closinghour = substr($row["closinghour"],0,2);
                     }
                 }		
                
            }
	
            if (!isset($openinghour)) {$openinghour = "1";}
            if (!isset($closinghour)) {$closinghour = "23";}
            

          //  print_r($_REQUEST["custom"]);
            if (isset($_REQUEST["custom"])) {$custom = $_REQUEST["custom"];} else {$custom = array();}
       //  print_r($custom);
            

        $display = strftime("%A %x",$t);
	$y = $t - 86400;
	$n = $t + 86400;
	
	echo "
	 <a href=\"resourcebooking.php?m=$m&amp;t=$t\" >$phrase[1019]</a>  | 
	 <a href=\"resourcebooking.php?m=$m&amp;t=$t&amp;resource_no=$resource_no&amp;event=cal\">$phrase[1020]</a><h2>";
	if (!isset($_REQUEST["duplicatebooking"]))
        { echo "$resource_name[$resource_no]<br>";} 
	$currentmonth = date("m");
	$month = date("m",$t);
	
	if ($currentmonth <> $month) {echo "<span style=\"color:#ff6666\">";}
	echo "$display";
	if ($currentmonth <> $month) {echo "</span>";}
	echo "	</h2>
<div class=\"swouter\">
	<FORM method=\"POST\" action=\"resourcebooking.php\" class=\"swinner\"><fieldset ><legend>";
        if (isset($_REQUEST["duplicatebooking"])) { echo $phrase[1103];} else { echo $phrase[960];}
        
        
        echo "</legend>
	<table  >
	<tr><td ></td><td style=\"width:35em\">
	$description[$resource_no]
	</td></tr>
	<tr><td class=\"label\"><b>$phrase[226]</b></td><td >
	<input type=\"text\" name=\"name\" maxlength=\"100\" size=\"50\"";  
        if (isset($_REQUEST["name"])) {echo "  value=\"" . $_REQUEST["name"]  ."\"";}
        echo "></td></tr>
	<tr><td class=\"label\"><b>$phrase[121]</b></td><td >$location[$resource_no]</td></tr>
	<tr><td class=\"label\"><b>$phrase[233]</b></td><td >";
        
        
       if (isset($existingBookingGroup))  {$hidden .= "<input type=\"hidden\" name=\"existingBookingGroup\" value=\"$existingBookingGroup\">";}
    
       /*
      if (isset($_REQUEST["duplicatebooking"])) {
          
           foreach ($resource_name as $rno => $rname)
			{
			
			echo "<input type=\"checkbox\" name=\"venue[$rno]\"";
                           if ( $rno == $resource_no) {echo " checked";}
echo "> $rname<br>";	
			
			
			}
          
          $hidden .= "<input type=\"hidden\" name=\"duplicate\" value=\"$bookingno\">";
        //  print_r($_REQUEST);
          }
        else
        {
        * 
        */
        echo "
 <input type=\"checkbox\" name=\"venue[$resource_no]\"  checked> $resource_name[$resource_no] 
 <div id=\"show\" onclick=\"showvenues();return false;\"><span class=\"accent\" >$phrase[1017]</span></div>
 <div style=\"display:none\" id=\"venues\">";
 foreach ($resource_name as $rno => $rname)
			{
			if ($rno != $resource_no)
                        {
			echo "<input type=\"checkbox\" name=\"venue[$rno]\"> $rname<br>";	
                        }
			
			}
 echo "<span class=\"accent\" onclick=\"hidevenues();return false;\">$phrase[1018]</span></div>";
      //  }
	
	
	
	echo "</td></tr>
	<tr><td class=\"label\"><b>$phrase[242]</b></td><td >
	<select name=\"starthour\">";
	
         $selected = 0;       
	$counter = $openinghour;
		while ($counter <= $closinghour)
		{
		echo "<option value= \"$counter\"";
                if (isset($_REQUEST["starthour"]) && $_REQUEST["starthour"] == $counter) {echo " selected"; $selected = 1;}
	if ($counter == $openinghour && $selected == 0) { echo " selected"; } 
	 $hourdisplay = date("g a",(mktime($counter,0,0,0,0,0)));
         if ($counter == 12) {$hourdisplay = $phrase[1123];}
        //  if ($counter == 24) {$hourdisplay = $phrase[1124];}
         
		echo ">$hourdisplay</option>                     
";
                $counter++;
		}
		
		
		
		echo "</select>";
		
		echo "<select name=\"startminute\">";
		
		$counter = 0;
		
		while ($counter < 60)
		{
		$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
		echo "<option value= \"$counter\"";
                if (isset($_REQUEST["startminute"]) && $_REQUEST["startminute"] == $counter) {echo " selected";}
		echo ">$counter</option>
";	
		$counter++;
		}
	
		
		
		echo "</select>";
		
		
	  $day = date("d",$t);
       $month = date("m",$t);
       $year = date("Y",$t);
		
		if ($DATEFORMAT == "%d-%m-%Y")
             {
             $date = "$day-$month-$year";
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $date = "$month-$day-$year";
             } else { $date = "$day-$month-$year";}
		
		
	
		
		
		
		
		if ($book_multiple_days[$resource_no] >  0 || isset($_REQUEST["duplicatebooking"]))
			{
			echo " <input type=\"text\"  name=\"dates[0]\" id=\"startdate\" value=\"$date\" readonly class=\"datepicker\" size=\"10\">";
			}
			
			else { $hidden .= "<input type=\"hidden\" name=\"dates[0]\" value=\"$date\">";}
		
		echo "</td></tr>
	<tr><td class=\"label\"><b>$phrase[243]</b></td><td >
	<select name=\"endhour\">";

	$selected = 0;
        
	$counter = $openinghour;
		while ($counter <= $closinghour)
		{
		echo "<option value= \"$counter\"";
                if (isset($_REQUEST["endhour"]) && $_REQUEST["endhour"] == $counter) {echo " selected"; $selected = 1;}
	if ($counter == $closinghour && $selected == 0) { echo " selected";} 
	 $hourdisplay = date("g a",(mktime($counter,0,0,0,0,0)));
         if ($counter == 12) {$hourdisplay = $phrase[1123];}
          
		echo ">$hourdisplay</option>
";
                $counter++;
		}   
		
		
		echo "<option value=\"midnight\">midnight</option></select>
		
		<select name=\"endminute\">";
		
		$counter = 0;
		
		while ($counter < 60)
		{
		$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
		echo "<option value= \"$counter\"";
		if (isset($_REQUEST["endminute"]) && $_REQUEST["endminute"] == $counter) {echo " selected";}
		echo ">$counter</option>";	
		$counter++;
		}
	
		
		
		echo "</select>";
		
		if ($book_multiple_days[$resource_no] > 0 || isset($_REQUEST["duplicatebooking"]))
			{
			echo " <input type=\"text\"  name=\"enddate\" id=\"enddate\"  value=\"$date\" readonly class=\"datepicker\" size=\"10\"> ";
			}
			else { $hidden .= "<input type=\"hidden\" name=\"enddate\" value=\"$date\">";}
		
		echo "</td></tr>
                    ";
		
		
		//$sql = "select * from resource_custom_fields where resource = \"$resource_no\" order by ranking";
                
                $sql = "select * from resource_custom_fields,resource_fields  where 
resource_custom_fields.fieldno = resource_fields.field and                           
resource = '$resource_no' order by resource, ranking";
                
		
	$counter = 0;	
						
	$DB->query($sql,"resource.php");
	while ($row = $DB->get())
	{
	$field = $row["fieldno"]; 
	$label = htmlspecialchars($row["label"]);
	$type = $row["type"];
	$menu = $row["menu"];
	$values = explode("\r\n", $menu);
	$comment =  htmlspecialchars($row["comment"]);
	$custom_ranking[$counter] = $field;
	
	
	echo "<tr><td class=\"label\">$label</td><td>";
	
		if ($type == "t") {echo "<input type=\"text\" name=\"custom[$field]\" size=\"50\"";
               
        if (array_key_exists($field,$custom)) {echo "  value=\"" . $custom[$field]  ."\"";}
       
                echo ">";}
	if ($type == "a") {echo "<textarea name=\"custom[$field]\" cols=\"45\" rows=\"6\">";
        if (array_key_exists($field,$custom)) {echo $custom[$field];}
        echo "</textarea>";}
	if ($type == "m") {echo "<select name=\"custom[$field]\">";
	 foreach ($values as $indexa => $value)
							{
								//$value = substr($value, 0, -1);
								//$length = strlen($value);
							echo "<option value=\"$value\"";
                                                        if (array_key_exists($field,$custom) && $custom[$field] == $value) {echo "  selected";}
                                                        echo "> $value</option>";
							}
	
	echo "</select>";}
	
	if ($comment != "") {echo "<br>$comment";}
	echo "</td></tr>
            ";
		
	}
		
		
		
		
		
		
		
		
		
		
		
		
		
		if  ($recur[$resource_no] > 1)
		{
		echo "	<tr><td  class=\"label\"><b>$phrase[244]</b></td><td >&nbsp; $phrase[13] <input type=\"radio\" name=\"recurrent\" value=\"no\" checked id=\"recurrent_no\"> $phrase[12] <input type=\"radio\" name=\"recurrent\" value=\"yes\" id=\"recurrent_yes\">
		    <div id=\"recurrentoptions\" style=\"padding: 1em 0;display:none\"><ul><li>
$phrase[245] <select name=\"interval\"><option value=\"day\">$phrase[246]</option><option value=\"week\">$phrase[247]</option><option value=\"fortnight\">$phrase[248]</option><option value=\"monthlybydate\">$phrase[1031]</option> <option value=\"monthlybyweekday\">$phrase[1030]</option></select> $phrase[679]
<select name=\"number\">";
$counter = 1;
while ($counter < ($recur[$resource_no]  + 1))
{
echo "<option value=\"$counter\">$counter</option>";
$counter++;
}

echo "</select>

</li><li>


$phrase[997]  <button onclick=\"addday();return false;\">$phrase[176]</button>

<div id=\"datebox\"></div>
		 

</li></ul>



</div>

        </td></tr>
        ";		}
	else { $hidden .= "	<input type=\"hidden\" name=\"recurrent\" value=\"no\"><input type=\"hidden\" name=\"interval\" value=\"none\">";}
	

	
	if ($fee_applicable[$resource_no] == 1)
		{
		echo "	<tr><td  class=\"label\"><b>$phrase[138]</b></td><td >
		<select name=\"paid\">";
		
	
		if (isset($paid_label))
		{
		foreach ($paid_label as $index => $label)
		{
			echo "<option value=\"$index\" ";
                        if (isset($_REQUEST["paid"]) && $_REQUEST["paid"] == $index) {echo " selected";}
                        echo ">$label</option>";
		}
		}
		
		
		echo "</select></td></tr>";		}
	else { $hidden .= "	<input type=\"hidden\" name=\"paid\" value=\"0\">";}
	
	echo "<tr><td class=\"label\" ><b>$phrase[227]</b></td><td >
	<input type=\"text\" name=\"staffname\" size=\"50\"";
        if (isset($_REQUEST["staffname"])) { echo " value=\"" . $_REQUEST["staffname"]   . "\"";}
        echo "></td></tr>";
	
	if ($allowscheckout[$resource_no] == 1)
		{
		echo "	<tr><td  class=\"label\"><b>$phrase[776]</b></td><td ><select name=\"checkout\"><option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option></select></td></tr>";		}
	else { $hidden .= "	<input type=\"hidden\" name=\"checkout\" value=\"0\">";}
	
	
	
	


		

	
	
	echo "
	
		<tr><td align=\"right\" valign=\"top\"></td><td align=\"left\">
$hidden
    ";
        if (isset($bookinggroup)) {echo "<input type=\"hidden\" name=\"bookinggroup\" value=\"" . $bookinggroup . "\">";}
        echo "
	<input type=\"hidden\" name=\"m\" value=\"$m\">
            <input type=\"hidden\" name=\"resource_no\" value=\"$resource_no\">
	<input type=\"hidden\" name=\"t\" value=\"$t\">
	
	<input type=\"hidden\" name=\"resource_name\" value=\"$resource_name[$resource_no]\">";
	

	if (isset($_REQUEST["view"]) && $_REQUEST["view"] == "cal") { echo "<input type=\"hidden\" name=\"view\" value=\"cal\">";}
	
	echo "
	<input type=\"submit\" name=\"addbooking\" value=\"$phrase[960]\"></td></tr>

	</table>	</fieldset></form></div>
	
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
	
	function init()
	{
	
	recurrent();
	";
	if ($book_multiple_days[$resource_no] > 0  || isset($_REQUEST["duplicatebooking"]))
			{
			echo "
				datepicker('startdate');
	  			datepicker('enddate');
			
			";	
			}

	echo "
	}

	function showvenues()
	{
	document.getElementById('venues').style.display = 'block';	
	document.getElementById('show').style.display = 'none';		
	}
	
	function hidevenues()
	{
	document.getElementById('venues').style.display = 'none';	
	document.getElementById('show').style.display = 'block';		
	}
	
	var picker = '0';
	
	
	 function recurrent()
	 {
	 if (document.getElementById('recurrent_yes')) { document.getElementById('recurrent_yes').onclick=show
	 hide()
	 }
	
	 if (document.getElementById('recurrent_yes')) {	document.getElementById('recurrent_no').onclick = hide
	 }
	 
	 }
	 
	 function hide()
	 {
	 if (document.getElementById('recurrentoptions'))
	 {
	 document.getElementById('recurrentoptions').style.display = 'none';	
	 }
	 }
	 
	 
	 function show()
	 {
	 if (document.getElementById('recurrentoptions'))
	 {
	//datepicker('datepicker');
	 document.getElementById('recurrentoptions').style.display = 'inline';	
	 }
	 }
	 
	  
	 window.onload=init
	 </script>
	
	";

   }
  
   
   /*
    elseif ((isset($_REQUEST["event"]) && $_REQUEST["event"] == "all") || (isset($_REQUEST["back"]) && $_REQUEST["back"] == "all"))
	
	
		{
			
			page_view($DB,$PREFERENCES,$m,"");
			
		echo "
		  <script type=\"text/javascript\">
 function pop_window(url) {
  var popit = window.open(url,'console','status,resizable,scrollbars,width=350,height=400');
 }
</script>


";	
				// put  bookings for this day in an array
				
		 $day = date("d",$t);
       $month = date("n",$t);
       $year = date("Y",$t);
	   $daystart =  mktime(0, 0, 1, $month,$day, $year);
	    $dayend =  mktime(23, 59, 59, $month,$day, $year);
		
	    
	    
   $sql = "select resource,bookingno, starttime, endtime, resourcebooking.name as name,paid,ip,staffname,username,bookeddate,cancelip,cancelname,canceltime,cancelled,bookinggroup,resourcebooking.checkout as checkout,checkin from resourcebooking, resource where resource.m = \"$m\" and resource.resource_no = resourcebooking.resource and  ((starttime >= \"$daystart\" and starttime < \"$dayend\") or   (endtime > \"$daystart\" and endtime <= \"$dayend\") or   (starttime < \"$daystart\" and endtime > \"$dayend\")) order by starttime ";
   

	$DB->query($sql,"resourcebooking.php");
	


	$i = 1;
	while ($row = $DB->get()) 
		{
			
			$bookno[$i] =$row["bookingno"];		
			$bookname[$i] = formattext($row["name"]);
			$sname[$i] = formattext($row["staffname"]);
			$book =$row["bookeddate"];
			$bookeddate[$i] = date("g:ia",$book);
			$bookeddate[$i] .= strftime(" %x",$book);
			$username[$i] =$row["username"];
			$cancelled[$i] =$row["cancelled"];
			$cancelname[$i] =$row["cancelname"];
			$cancelip[$i] =$row["cancelip"];
			$cancel =$row["canceltime"];
			$canceltime[$i] = date("g:ia",$cancel);	
			$canceltime[$i] .= strftime(" %x",$cancel);	
			$ip[$i] =$row["ip"];
			$bookpaid[$i] =$row["paid"];
			//$bookcontact[$i] =$row["contact"];
			//$booktelephone[$i] =$row["telephone"];
			//$bookaddress[$i] = formattext($row["address"]);
			//$booknotes[$i] = formattext($row["notes"]);
			$start[$i] =$row["starttime"];
			$end[$i] =$row["endtime"];
			//$print[$i] =$row["print"];
			$resource[$i] =$row["resource"];
			
			
			$starttime[$i] = date("g:ia",$row["starttime"]);
			$endtime[$i] = date("g:ia",$row["endtime"]);
			
			$startdate[$i] = strftime("%x",$row["starttime"]);
			$enddate[$i] = strftime("%x",$row["endtime"]);
			$checkin[$i] =$row["checkin"];
			$checkout[$i] =$row["checkout"];
			
			$i++;
			}
	
		
			//display next and previous day  links
			
			
$today = mktime (0,0,0,$month,$day,$year);

$display = mktime (0,0,0,$month,$day,$year);
 //$displaydate = date("l jS M Y", $display) ;
 $displaydate = strftime("%A &nbsp; %x", $display);

 
 $prev7days =  mktime (0,0,0,$month,$day -7,$year);
 $formatprev7days = date("D", $prev7days);
  $prev6days =  mktime (0,0,0,$month,$day -6,$year);
 $formatprev6days = date("D", $prev6days);
  $prev5days =  mktime (0,0,0,$month,$day -5,$year);
 $formatprev5days = date("D", $prev5days);
  $prev4days =  mktime (0,0,0,$month,$day -4,$year);
 $formatprev4days = date("D", $prev4days);
  $prev3days =  mktime (0,0,0,$month,$day -3,$year);
 $formatprev3days = date("D", $prev3days);
  $prev2days =  mktime (0,0,0,$month,$day -2,$year);
 $formatprev2days = date("D", $prev2days);
  $prev1days =  mktime (0,0,0,$month,$day -1,$year);
 $formatprev1days = date("D", $prev1days);

 
  $next7days =  mktime (0,0,0,$month,$day +7,$year);
 $formatnext7days = date("D", $next7days);
  $next6days =  mktime (0,0,0,$month,$day +6,$year);
 $formatnext6days = date("D", $next6days);
  $next5days =  mktime (0,0,0,$month,$day +5,$year);
 $formatnext5days = date("D", $next5days);
  $next4days =  mktime (0,0,0,$month,$day +4,$year);
 $formatnext4days = date("D", $next4days);
  $next3days =  mktime (0,0,0,$month,$day +3,$year);
 $formatnext3days = date("D", $next3days);
  $next2days =  mktime (0,0,0,$month,$day +2,$year);
 $formatnext2days = date("D", $next2days);
  $next1days =  mktime (0,0,0,$month,$day +1,$year);
 $formatnext1days = date("D", $next1days);
 
 //if the day displayed is not today then display the date in in a warning colour like orange
   $daytest = date("j");
   $monthtest = date("n");
   $yeartest = date("Y");
   if (($day <> $daytest) || ($month <> $monthtest) || ($year <> $yeartest))
   	{ $daycolour = "color:#ff3333;";} else {$daycolour = "";}



 echo " <a href=\"resourcebooking.php?m=$m\" >$phrase[642]</a><br><br>
<table width=\"100%\" style=\"background:white\" ><tr><td align=\"center\">";
 

 echo "<b>$phrase[525]</b><br><a href=\"resourcebooking.php?t=$prev7days&amp;m=$m&amp;event=all\">$formatprev7days</a>&nbsp;
 <a href=\"resourcebooking.php?t=$prev6days&amp;m=$m&amp;event=all\">$formatprev6days</a>&nbsp;
 <a href=\"resourcebooking.php?t=$prev5days&amp;m=$m&amp;event=all\">$formatprev5days</a>&nbsp;
 <a href=\"resourcebooking.php?t=$prev4days&amp;m=$m&amp;event=all\">$formatprev4days</a>&nbsp;
 <a href=\"resourcebooking.php?t=$prev3days&amp;m=$m&amp;event=all\">$formatprev3days</a>&nbsp;
 <a href=\"resourcebooking.php?t=$prev2days&amp;m=$m&amp;event=all\">$formatprev2days</a>&nbsp;
 <a href=\"resourcebooking.php?t=$prev1days&amp;m=$m&amp;event=all\">$formatprev1days</a>";
 
 
 echo "</td><td align=\"center\"><span style=\"font-size:24pt;$daycolour\">$displaydate</span></td><td align=\"center\">";
 

 
  echo "<b>$phrase[526]</b><br> <a href=\"resourcebooking.php?t=$next1days&amp;m=$m&amp;event=all\">$formatnext1days</a>&nbsp;
   <a href=\"resourcebooking.php?t=$next2days&amp;m=$m&amp;event=all\">$formatnext2days</a>&nbsp;
    <a href=\"resourcebooking.php?t=$next3days&amp;m=$m&amp;event=all\">$formatnext3days</a>&nbsp;
	 <a href=\"resourcebooking.php?t=$next4days&amp;m=$m&amp;event=all\">$formatnext4days</a>&nbsp;
	 <a href=\"resourcebooking.php?t=$next5days&amp;m=$m&amp;event=all\">$formatnext5days</a>&nbsp;
 <a href=\"resourcebooking.php?t=$next6days&amp;m=$m&amp;event=all\">$formatnext6days</a>&nbsp;
 <a href=\"resourcebooking.php?t=$next7days&amp;m=$m&amp;event=all\">$formatnext7days</a>&nbsp;";
 echo "</td></tr></table>";
			
			
			
			
			
			
			// list table of rooms
			 $sql = "select * from resource where m=\"$m\" order by resource_name";
	$DB->query($sql,"resourcebooking.php");
	while ($row = $DB->get()) 
		{
			$res_no[] =$row["resource_no"];		
			$res_name[] =$row["resource_name"];
			
		}
	
	$num_rows = $DB->countrows();
	if ($num_rows > 0)
		{
		echo "<br><table style=\"margin:0 auto;text-align:left;background:white\" class=\"colourtable\" cellpadding=\"5\" ><tr>";
		
		
		
	foreach ($res_name as $index => $name)
	{
			
			
			echo "<td><b><a href=\"resourcebooking.php?m=$m&amp;event=cal&amp;t=$t&amp;resource_no=$res_no[$index]\" style=\"font-size:120%;text-decoration:none\">$name</a></b></td>";
		}	

		echo "</tr><tr>";
		foreach ($res_name as $i => $name)
		{
			
			
			
			echo "<td>";
			
			if (isset($bookno))
	{
		foreach ($bookno as $index => $value)
		
			{
			
				if ($resource[$index] == $res_no[$i])
				{
					
							if ($access->thispage > 1)
									{
									echo "<a href=\"resourcebooking.php?m=$m&amp;event=edit&amp;bookingno=$bookno[$index]&amp;t=$t&amp;resource_no=$res_no[$i]&amp;back=all\" ";
									
									if ($checkout[$index] > $checkin[$index]) {echo " style=\"color:red\"";}
									echo ">";
									} 
							
									echo "$bookname[$index]";
								
				
								if ($access->thispage > 1)
								{
								
								  echo "</a>";
								  
								}
						
				
					//echo "<span style=\"font-size:0.9em";
					//if ($checkout[$index] > $checkin[$index]) {echo ";color:red;";}
					//echo "\">";
					
				if ($startdate[$index] == $enddate[$index])
					{
					echo "<br> $starttime[$index] - $endtime[$index]";
					}
				else { 
				//$diff = (floor(($end[$index] - $start[$index])/60/60/24) + 1);
				//	echo "$diff $phrase[244]</span>";
					
					}
			//	echo "</span>";
				
			if ($checkout[$index] > $checkin[$index]) {echo "<br><span style=\"color:red;\">$phrase[776]</span>";}
		
				
				
						if ($cancelled[$index] == 1) {echo "<br><span style=\"color:red;\">$phrase[152]</span>";}
						
						
						
				
						echo "<br><br>";
					
				}
				
				
				
			}
	}
	
	if ($access->thispage > 1)
	{
	echo "<a href=\"resourcebooking.php?m=$m&amp;t=$t&amp;event=book&amp;resource_no=$res_no[$i]&amp;back=all\" class=\"add\"> </a>";
}		
			
			
		echo "</td>";
		}
		echo "</tr></table>";
		}
		
		
		
		}
		
		*/
	 elseif ((isset($_REQUEST["event"]) && $_REQUEST["event"] == "cal") )
	
	
		{
			//print_r($_REQUEST);
			
			if (function_exists('page_view'))
			{
			page_view($DB,$PREFERENCES,$m,"");
			}
		
		// put this months bookings in an array
		if (!isset($month)) { $month = date("n",$t);}
			if (!isset($year)) { $year = date("Y",$t);}
      
			
     
	   $monthstart =  mktime(0, 0, 0, $month,1, $year);
	    $monthend =  mktime(0, 0, 0, $month + 1,1, $year);
		
   $sql = "select bookingno, starttime, endtime, resourcebooking.name as name,paid,ip,staffname,username,bookeddate,cancelip,cancelname,canceltime,cancelled,bookinggroup,resourcebooking.checkout as checkout,checkin from resourcebooking, resource 
   where resourcebooking.resource = '$resource_no' and resource.m = '$m' and resource.resource_no = resourcebooking.resource 
   and  ((starttime >= '$monthstart' and starttime < '$monthend')
   or   (endtime > '$monthstart' and endtime <= '$monthend') 
   or   (starttime <= '$monthstart' and endtime >= '$monthend'))  order by starttime ";
	$DB->query($sql,"resourcebooking.php");
	
	$i = 1;
	while ($row = $DB->get()) 
		{
			$bookno[$i] =$row["bookingno"];		
			$bookname[$i] = formattext($row["name"]);
			$sname[$i] = formattext($row["staffname"]);
			$book =$row["bookeddate"];
			$bookeddate[$i] = date("g:ia",$book);
			$bookeddate[$i] .= strftime(" %x",$book);
			$username[$i] =$row["username"];
			$cancelled[$i] =$row["cancelled"];
			$cancelname[$i] =$row["cancelname"];
			$cancelip[$i] =$row["cancelip"];
			$cancel =$row["canceltime"];
			$canceltime[$i] = date("g:ia",$cancel);	
			$canceltime[$i] .= strftime(" %x",$cancel);	
			$ip[$i] =$row["ip"];
			$bookpaid[$i] =$row["paid"];
			//$bookcontact[$i] =$row["contact"];
			//$booktelephone[$i] =$row["telephone"];
			//$bookaddress[$i] = formattext($row["address"]);
			//$booknotes[$i] = formattext($row["notes"]);
			$start[$i] =$row["starttime"];
			$end[$i] =$row["endtime"];
			//$print[$i] =$row["print"];
			$checkout[$i] =$row["checkout"];
			$checkin[$i] =$row["checkin"];
		
			
			$starttime[$i] = date("g:ia",$row["starttime"]);
			$endtime[$i] = date("g:ia",$row["endtime"]);
			
			$startdate[$i] = strftime("%x",$row["starttime"]);
			$enddate[$i] = strftime("%x",$row["endtime"]);
			
			$sd[$i] = date("Ymd",$row["starttime"]);
			$ed[$i] = date("Ymd",$row["endtime"]);
			
			$i++;
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
$display = strftime("%B %Y", $t);
//$display = date("F Y",$t);
$day = date("d",$t);
$month = date("n",$t);
$monthname = date("F",$t);
$year = date("Y",$t);  
$daysinmonth = date("t",$t);  
 //$weekday = date("w");   

   
//$t = mktime(0, 0, 0, 01, 31,  $year);

$lastmonth = mktime(0, 0, 0, $month -1, 01,  $year);
$nextmonth = mktime(0, 0, 0, $month +1, 01,  $year);
  

   
   $fd  = mktime(0, 0, 0, $month  , 01, $year);
   $fd = date("w",$fd);
   //echo "fd is $fd<br>";
   
   $ld  = mktime(0, 0, 0, $month  , $daysinmonth, $year);
   $ld = date("w",$ld);
   //echo "ld is $ld";
	
	
	 $sql = "select * from resource where resource_no = '$resource_no'";
	$DB->query($sql,"resourcebooking.php");
	$row = $DB->get();
			
	$resource_name =$row["resource_name"];
	$fee_applicable = $row["fee_applicable"];
			//$display_contact = $row["display_contact"];
			//$display_telephone = $row["display_telephone"];
			//$display_address = $row["display_address"];
			//$display_notes = $row["display_notes"];
	$book_multiple_days = $row["book_multiple_days"];
        $openinghours = $row["openinghours"];
			
	$closure_resource= array();
        $opening_resource = array();
        
        
         $sql = "select * from resource_closures where resource_no = '$resource_no'";
                $DB->query($sql,"resourcebooking.php");
                while ($row = $DB->get()) 
		{
                 $closure_resource[] = $row["resource_no"];   
                 $closure_start[] = str_replace("-","",$row["date_blocked"]);
                 $closure_finish[] = str_replace("-","",$row["date_finish"]);
                }
                
                
            if ($openinghours == 1)
            {
               
                
                $sql = "select * from resource_openinghours where resource_no = '$resource_no'";
                $DB->query($sql,"resourcebooking.php");
                while ($row = $DB->get()) 
		{
                 $opening_resource[] = $row["resource_no"];   
                 $opening_openinghour[] = $row["openinghour"];
                 $opening_closinghour[] = $row["closinghour"];
                 $opening_open[] = $row["open"];
                 $opening_day[] = $row["day"];
                 }		
                  //  print_r($opening_day);
            }
   echo "

 <a href=\"resourcebooking.php?m=$m&amp;t=$t\" >$phrase[1019]</a>  
   <form style=\"display:inline;margin-left:2em\" action=\"resourcebooking.php\" method=\"get\">
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
     <input type=\"hidden\" name=\"event\" value=\"cal\">
       <input type=\"hidden\" name=\"m\" value=\"$m\">
        <input type=\"hidden\" name=\"resource_no\" value=\"$resource_no\">
     <input type=\"submit\" value=\"View\">
   
   </form> <a href=\"resourcebooking.php?m=$m&event=search\" style=\"padding:1em\">$phrase[282]</a>  <a href=\"resourcebooking.php?m=$m&event=search&view=summary\" style=\"padding:1em\">$phrase[1100]</a>   
 <br><br>";
   
 

echo "<table  class=\"colourtable\" id=\"resource\" width=\"100%\" style=\"float:left;height:100px\" cellpadding=\"3\">
 <tr class=\"accent\"><td style=\"text-align:left\"> <a href=\"resourcebooking.php?m=$m&amp;t=$lastmonth&amp;event=cal&amp;resource_no=$resource_no\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" ><span style=\"font-size:2em\">$resource_name<br>$display</span>
   

   </td><td style=\"text-align:right\"><a href=\"resourcebooking.php?m=$m&amp;t=$nextmonth&amp;event=cal&amp;resource_no=$resource_no\" class=\"hide\">$phrase[155]</a> </td></tr>

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
	echo "<td >";
	
	
	
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
        $padmonth = str_pad($month, 2, "0", STR_PAD_LEFT);
	$dayname  = strftime("%a",mktime(0, 0, 0, $month, $daycount,  $year));
        $daynumber  = strftime("%w",mktime(0, 0, 0, $month, $daycount,  $year));
        $date = $year . $padmonth . $day;
	//$dayname  = date("l",mktime(0, 0, 0, $month, $daycount,  $year));
	$t = mktime(0, 0, 0, $month, $day,  $year);
	$et = mktime(23, 59,59, $month, $day,  $year); 
	$day_booked_out = "no";
	
	if ($endline == 1) { echo "<tr>";}
	echo "<td  valign=\"top\"  ";
        $closed = "no";
          foreach($opening_resource as $c => $r)
                        { 
                     if ($opening_day[$c] == $daynumber && $opening_open[$c] == '0' && $openinghours == 1) { $closed = "yes";}
                        }
                 foreach($closure_resource as $c => $r)
                        { 
                     if ( $date >= $closure_start[$c] && $date <= $closure_finish[$c]) {$closed = "yes";}

                      }              
                       
        if ($closed == "yes") {echo " class=\"greybackground\"";}
        echo ">";
        
     
        
        echo "<br>&nbsp;&nbsp;<b style=\"font-size:16pt\">$daycount</b> $dayname<br><br>";
	
	
	if (isset($bookno))
	{
		foreach ($bookno as $index => $value)
			{
			
			
			$test = $start[$index];
                        $firstday = $test;
			while ($test < $end[$index])
				{
				$testday  = date("j",$test);
				$testmonth  = date("n",$test);
				$testyear  = date("Y",$test);
				
                                //
			
				$testdate  = date("Ymd",$test);
				
				if (($testday == $daycount) && ($month == $testmonth) && ($year == $testyear) && (($book_multiple_days < 2) || ($book_multiple_days == 2 && $test == $firstday)))
					{
					
							if ($access->thispage > 1)
									{
									echo "<a href=\"resourcebooking.php?m=$m&amp;event=edit&amp;bookingno=$bookno[$index]&amp;t=$t&amp;resource_no=$resource_no&amp;view=cal\" ";
									
									
										if ($startdate[$index] != $enddate[$index])
										{ echo " title=\"$startdate[$index] - $enddate[$index]\"";}
									
								
				
									
									$spanstyle="";
									
									if ($fee_applicable == 1)
									{
										
										
										if (isset($paid_colour))
										{
										foreach ($paid_colour as $ic => $colour)
												{
												if ($ic == $bookpaid[$index]) { echo " style=\"color:$colour\""; $spanstyle = " style=\"color:$colour\"";}
												}
										}
										
							
									}
									
									
									
									
									
									
									//if ($checkout[$index] > $checkin[$index]) {echo " style=\"color:red\"";}
									echo ">";
									} 
							
									echo "$bookname[$index]";
							
				
								if ($access->thispage > 1)
								{
								
								  echo "</a>";
								  
								}
						
				
					//echo "<span style=\"font-size:0.9em";
					//if ($checkout[$index] > $checkin[$index]) {echo ";color:red;";}
					//echo "\">";
					
			
					
					
					
					if ($startdate[$index] == $enddate[$index])
					{
					echo "<br><span $spanstyle> $starttime[$index] - $endtime[$index]</span>";
					}
				elseif  ($sd[$index] == $testdate)
				{
					echo "<br><span $spanstyle> $starttime[$index] &gt;</span>";
					
					}			 
					elseif  ($ed[$index] == $testdate)
				{
					echo "<br><span $spanstyle> &lt;  $endtime[$index]</span>";
					
					}
					
					elseif  ($sd[$index] != $testdate && $ed[$index] != $testdate)
				{
					echo "<br><span $spanstyle>$phrase[1016]</span>";
				}
						
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
				
			if ($checkout[$index] > $checkin[$index]) {echo "<br><span style=\"color:red;\">$phrase[776]</span>";}
		
				
				
						if ($cancelled[$index] == 1) {echo "<br><span style=\"color:red;\">$phrase[152]</span>";}
						
						
						
				
						echo "<br><br>";
					}
				$test = mktime(0, 0, 0, $testmonth, $testday + 1,  $testyear);
				
				}
			
				
				if ($start[$index] < $t && $end[$index] > $et && $cancelled[$index] == 0 ) { $day_booked_out = "yes";}
				
			}
	}
	
	
	
	if ($access->thispage > 1 && $day_booked_out == "no" && $closed == "no")	{ 
		
	echo "<a href=\"resourcebooking.php?m=$m&amp;t=$t&amp;event=book&amp;resource_no=$resource_no&amp;view=cal\" class=\"addevent\"> </a>";
	}
	echo"</td>";
	
   				
				
				
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
   echo "</table>";
   
		}
		

elseif (!isset($ERROR))
    {
    //display week view
	
  $opening_resource = array();
  $closure_resource  = array();

   $sql = "select * from resource where m='$m' order by resource_name";
  
	$DB->query($sql,"resourcebooking.php");
        $openinghours = array();
		while ($row = $DB->get()) 
		{
			$resource_no =$row["resource_no"];		
			$resource_name[$resource_no] =$row["resource_name"];
			$fee_applicable[$resource_no] =$row["fee_applicable"];
			$location[$resource_no] =$row["location"];
			$openinghours[$resource_no] =$row["openinghours"];
			$book_multiple_days[$resource_no] =$row["book_multiple_days"];
			
			
			
		}
               
                $insert = " in (";
                $counter = 0;
            foreach($openinghours as $i => $o)
      			{
               
                   if ($counter != 0) {$insert .= ",";} 
                    $insert .= "$i";
                    
               $counter++; 
            }    
                $insert .= ") ";
                
                $sql = "select * from resource_closures where resource_no $insert";
                $DB->query($sql,"resourcebooking.php");
                while ($row = $DB->get()) 
		{
                 $closure_resource[] = $row["resource_no"];   
                 $closure_start[] = str_replace("-","",$row["date_blocked"]);
                 $closure_finish[] = str_replace("-","",$row["date_finish"]);
                }
                
                
                //get opening hours where openinghours is enabled
                if (in_array("1",$openinghours))
                {       
                $insert = " in (";
                $counter = 0;
            foreach($openinghours as $i => $o)
      			{
                if ($o == 1) {
                   if ($counter != 0) {$insert .= ",";} 
                    $insert .= "$i";
                    }
               $counter++; 
            }    
                $insert .= ") ";
                
                $sql = "select * from resource_openinghours where resource_no $insert";
                $DB->query($sql,"resourcebooking.php");
                while ($row = $DB->get()) 
		{
                 $opening_resource[] = $row["resource_no"];   
                 $opening_openinghour[] = $row["openinghour"];
                 $opening_closinghour[] = $row["closinghour"];
                 $opening_open[] = $row["open"];
                 $opening_day[] = $row["day"];
                 }
                }
                
             //   echo "$sql";
		
		$pattern = '/^\d\d-\d\d-\d\d\d\d$/';
	
		if (isset($_REQUEST["date"]) && preg_match ($pattern ,$_REQUEST["date"]))
		{
		$date = $_REQUEST["date"];	
			
		if ($DATEFORMAT == "%d-%m-%Y")
             {
             $_sd = substr($date,0,2);
             $_sm = substr($date,3,2);
             $_sy = substr($date,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $_sd = substr($date,3,2);
         $_sm = substr($date,0,2);
         $_sy = substr($date,6,4);
             }	
			
		$now = mktime(0,0,0,$_sm,$_sd,$_sy);
              //  echo "now is $now $_sm $_sd $_sy";
		}
		
		
		elseif (isset($t))
		{
			
		
			$now = $t;
		}
		else 
		{$now = time(); }
		
	  
      $day = date("j",$now);
      $dayofweek = date("w",$now);
      $month = date("n",$now);
      $year = date("Y",$now);
      
		//echo "month $month,day $day - dayofweek $dayofweek + counter $counter, year $year";
      
      $counter = 0;
      
      while ($counter < 7)
      {
      	$ws[$counter] = mktime(0,0,0,$month,$day - $dayofweek + $counter,$year);
      	$we[$counter] = mktime(23,59,59,$month,$day - $dayofweek + $counter,$year);
      	$wd[$counter] = strftime("%x",$ws[$counter]);
      	$wn[$counter] = strftime("%a",$ws[$counter]);
      	
      	$wsymd[$counter] = date("Ymd",$ws[$counter]);
      	$weymd[$counter] = date("Ymd",$ws[$counter]);
      	
      	//echo "$date <br>";
      $counter++;	
      }
      
      
       
      $weekstart =  $ws[0];
      $weekend = mktime(23,23,59,$month,$day - $dayofweek + 6,$year);
      
      //echo "weekend is $weekend";
   //   print_r($ws);
      
   $sql = "select bookingno, starttime, endtime, resourcebooking.name as name,resource,paid,ip,staffname,username,bookeddate,cancelip,cancelname,canceltime,cancelled,bookinggroup,resourcebooking.checkout as checkout,checkin from resourcebooking, resource 
   where  resource.m = '$m' and resource.resource_no = resourcebooking.resource 
   and  ((starttime >= '$weekstart' and starttime < '$weekend')
   or   (endtime > '$weekstart' and endtime <= '$weekend') 
   or   (starttime <= '$weekstart' and endtime >= '$weekend'))  order by starttime ";
   
  //echo "<p>$sql</p>";
	$DB->query($sql,"resourcebooking.php");
	
	$i = 1;
	while ($row = $DB->get()) 
		{
			$bookno[$i] =$row["bookingno"];		
			$bookname[$i] = formattext($row["name"]);
			$sname[$i] = formattext($row["staffname"]);
			$book =$row["bookeddate"];
			$bookeddate[$i] = date("g:ia",$book);
			$bookeddate[$i] .= strftime(" %x",$book);
			$username[$i] =$row["username"];
			$resource[$i] =$row["resource"];
			$cancelled[$i] =$row["cancelled"];
			$cancelname[$i] =$row["cancelname"];
			$cancelip[$i] =$row["cancelip"];
			$cancel =$row["canceltime"];
			$canceltime[$i] = date("g:ia",$cancel);	
			$canceltime[$i] .= strftime(" %x",$cancel);	
			$ip[$i] =$row["ip"];
			$bookpaid[$i] =$row["paid"];
			//$bookcontact[$i] =$row["contact"];
			//$booktelephone[$i] =$row["telephone"];
			//$bookaddress[$i] = formattext($row["address"]);
			//$booknotes[$i] = formattext($row["notes"]);
			$start[$i] =$row["starttime"];
			$end[$i] =$row["endtime"];
			//$print[$i] =$row["print"];
			$checkout[$i] =$row["checkout"];
			$checkin[$i] =$row["checkin"];
		
			
			$starttime[$i] = date("g:ia",$row["starttime"]);
			$endtime[$i] = date("g:ia",$row["endtime"]);
			
			$startdate[$i] = strftime("%x",$row["starttime"]);
			$enddate[$i] = strftime("%x",$row["endtime"]);
			
			$sd[$i] = date("Ymd",$row["starttime"]);
			$ed[$i] = date("Ymd",$row["endtime"]);
			
			
			$i++;
			}
      
      //print_r($bookname);
      
      $nextweek = $now + 604800;
      $lastweek = $now - 604800;
      
      
      echo "<div style=\"width:33%;float:left;text-align:center;\"><a href=\"resourcebooking.php?m=$m&t=$lastweek\" >$phrase[1058]</a> </div>
       <div style=\"width:33%;float:left;text-align:center;\"><form style=\"display:inline\" method=\"get\" action=\"resourcebooking.php\">
       <input type=\"hidden\" name=\"m\" value=\"$m\"><input type=\"text\" name=\"date\" class=\"datepicker\" id=\"date\" size=\"10\"><input type=\"submit\" value=\"Go\"></form>
<a href=\"resourcebooking.php?m=$m&event=search\" style=\"padding:1em\">$phrase[282]</a>  <a href=\"resourcebooking.php?m=$m&event=search&view=summary\" style=\"padding:1em\">$phrase[1100]</a>          
</div>
      <div style=\"width:33%;float:left;text-align:center;\"><a href=\"resourcebooking.php?m=$m&t=$nextweek\" >$phrase[1057]</a> </div>
      <br>
      <table class=\"colourtable\" style=\"margin:2em auto;text-align:left\">";
      
      echo "<tr style=\"text-align:left\"><td style=\"width:23%\"></td>";
        foreach($wd as $index => $date)
      			{
      			echo "<td><p style=\"font-size:2em;width:11%;\">$wn[$index]</p>$date</td>";	
      			}
      echo "</tr>";
      
      if (isset($resource_name))
      {
      foreach($resource_name as $rno => $rname)
      {
      	
      	echo "<tr><td><b><a href=\"resourcebooking.php?m=$m&amp;event=cal&amp;resource_no=$rno&amp;t=$now\" title=\"Month view\">$rname</a></b></td>";
      	
      	  foreach($wd as $index => $date)
      			{
      			$closed = "no";	
      			echo "<td";
                        foreach($opening_resource as $c => $r)
                        { if ($r == $rno) {
                            if ($opening_day[$c] == $index && $opening_open[$c] == 0) { $closed = "yes";}
                        }}
                        
                           foreach($closure_resource as $c => $r)
                        { if ($r == $rno) {
                            if ( $wsymd[$index] >= $closure_start[$c] && $wsymd[$index] <= $closure_finish[$c]) { $closed = "yes";}

                        }}
                        
                        if ($closed == "yes") {echo " class=\"greybackground\"";}
                        echo ">";
                    
                        
      			$day_booked_out = "no";
      			
      			echo "";
      			
      			
      			if (isset($bookno))
					{
					foreach ($bookno as $i => $value)
					{
                                         $room = $resource[$i];   
                                            
					if ($resource[$i] == $rno && 
                                                (($book_multiple_days[$room] < 2) || ($book_multiple_days[$room] == 2 && $wsymd[$index] == $sd[$i]))
                                             &&   (
					($start[$i] >= $ws[$index] && $start[$i] < $we[$index]) || ($end[$i] > $ws[$index] && $end[$i] < $we[$index]) || ($start[$i] < $ws[$index] && $end[$i] > $we[$index])
				//($start[$i] >= $ws[$index] && $start[$i] < $we[$index])
				//($end[$i] > $ws[$index] && $end[$i] < $we[$index])	
				//($start[$i] < $ws[$index] && $end[$i] > $we[$index])
				))	
					     {
					
						
						
						
						
							if ($access->thispage > 1)
									{
										//$nowday = date("D",$ws[$index]);
									echo "<a href=\"resourcebooking.php?m=$m&amp;event=edit&amp;bookingno=$bookno[$i]&amp;t=$ws[$index]&amp;resource_no=$rno\" ";
									
									
									if ($startdate[$i] != $enddate[$i])
										{ echo " title=\"$startdate[$i] - $enddate[$i]\"";}
									
									
									$spanstyle="";
									
									if ($fee_applicable[$rno] == 1)
									{
										
										if (isset($paid_colour))
										{
										foreach ($paid_colour as $ii => $colour)
												{
												if ($ii == $bookpaid[$i]) { echo " style=\"color:$colour\""; $spanstyle = " style=\"color:$colour\"";}
												}
										}
										
										
										
										
									
									}
									
									//if ($checkout[$i] > $checkin[$i]) {echo " style=\"color:red\"";}
									echo ">";
									} 
							
									echo "$bookname[$i]";
							
				
								if ($access->thispage > 1)
								{
								echo "</a>";
								 }
								
							
					//	echo "$ed[$i] == $weymd[$index] ";
						
						
					if ($startdate[$i] == $enddate[$i])
					{
					echo "<br><span $spanstyle> $starttime[$i] - $endtime[$i]</span>";
					}
				elseif  ($sd[$i] == $wsymd[$index])
				{
					echo "<br><span $spanstyle> $starttime[$i] &gt;</span>";
					
					}			 
					elseif  ($ed[$i] == $weymd[$index])
				{
					echo "<br><span $spanstyle> &lt;  $endtime[$i]</span>";
					
					}
					
					elseif  ($sd[$i] != $wsymd[$index] && $ed[$i] != $wsymd[$index])
				{
					echo "<br><span $spanstyle>$phrase[1016]</span>";
				}
								 
								 
							
					if ($checkout[$i] > $checkin[$i]) {echo "<br><span style=\"color:red;\">$phrase[776]</span>";}
					if ($cancelled[$i] == 1) {echo "<br><span style=\"color:red;\">$phrase[152]</span>";	}	
						echo "<br><br>";
						
						
						
						
					}
					
					//echo "$day_booked_out";
					//	echo "i is $i $start[$i] < $ws[$index] && $end[$i] > $we[$index] $cancelled[$i]";
					if ($start[$i] < $ws[$index] && $end[$i] > $we[$index] && $cancelled[$i] == 0 && $resource[$i] == $rno) { $day_booked_out = "yes"; }
					//echo "$day_booked_out";
					}
					}
      			
				//	echo "xxxxx $access->thispage  $day_booked_out xxxxxxxx";
      			
				if ($access->thispage > 1 && $day_booked_out == "no" && $closed == "no")	{ 
		
	echo "<a href=\"resourcebooking.php?m=$m&amp;t=$ws[$index]&amp;event=book&amp;resource_no=$rno\" class=\"addevent\"> </a>";
	}	
					
      			echo "</td>";	
      			}
      	 
      	
      	echo "</tr>";
      	
      }
      }
      echo "</table>
      
      
      
      
      
      
      
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
	
	
	datepicker('date');
      </script>
      
      
      
      
      
      
      ";  
	/*	
		
	$num_rows = $DB->countrows();
	if ($num_rows > 0)
		{
		echo "
	<h4>$phrase[642]</h4><ul class=\"listingcenter\">";
		}
	while ($row = $DB->get()) 
		{
			$resource_no =$row["resource_no"];		
			$resource_name =$row["resource_name"];
			$description =$row["description"];
			$location =$row["location"];
			
			
			echo "<li><a href=\"resourcebooking.php?m=$m&amp;event=cal&amp;resource_no=$resource_no&amp;t=$t\">$resource_name</a></li>";
		}
	
	if ($num_rows > 0)
		{
		
		echo "<li><a href=\"resourcebooking.php?m=$m&amp;event=all&amp;t=$t\">$phrase[676]</a></li>
		</ul><br><br>";
		}	
		
		*/
    }




  
if (isset($emailresult))
    	{
    		
        		
    	 $sql = "select * from resource where resource_no = '$resource_no' and m = '$m'";
    	$DB->query($sql,"resourcebooking.php");
		$row = $DB->get();
		$notify =$row["notify"];		
		$email =$row["email"];
		$resource_name =$row["resource_name"];	
              //  echo $sql;
    	//print_r($row);
  	if ($notify == 1)
									{
									
							$message = 
"$resource_name

$phrase[226] $displayname
$phrase[227] $displaystaffname
";	


foreach($emailresult as $index => $value)
	{
                          
                        
$message .= "
$value";
	}
      
	$headers = "$phrase[229] $phrase[641]";
	$emailaddress = $email;
	$subject = "$phrase[228]";

	if (trim($emailaddress) == "")
	{
		errorlog($DB, "resourcebooking.php", $phrase[910]);
	}
	else 
	{
		send_email($DB,$emailaddress, $subject, $message,$headers);
	}



  
	}
    	}



   echo "</div>";
include ("../includes/footer.php");


	}
	
	
?>

