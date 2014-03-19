<?php

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$ip = ip("pc");
$proxy = ip("proxy");

if (isset($_REQUEST["t"]))
	{	$t = $_REQUEST["t"];	}
	else {$t = time();}
	

$integers[] = "startminute";
$integers[] = "starthour";
$integers[] = "endminute";
$integers[] = "endhour";
$integers[] = "startsecond";
$integers[] = "endsecond";	
$integers[] = "type";
$integers[] = "group";
$integers[] = "day";
$integers[] = "bookingno";	
$integers[] = "bid";
$integers[] = "year";
$integers[] = "month";
$integers[] = "paid";	
$integers[] = "requeststart";
$integers[] = "requestend";
$integers[] = "p";	
$integers[] = "u";
$integers[] = "f";
$integers[] = "usage";
$integers[] = "recurnumber";
$integers[] = "pcno";
$integers[] = "nt";
$integers[] = "ps";
$integers[] = "bookingtime";
$integers[] = "endtime";
$integers[] = "pcusage";
$integers[] = "cpc";
$integers[] = "outoforder";
$integers[] = "checkedin";
$integers[] = "cancelled";
$integers[] = "change";
$integers[] = "client";
$integers[] = "flexible";
$integers[] = "oldpcno";

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

	$sql = "select name from modules where m = '$m'";
	$DB->query($sql,"pc.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);
		
	page_view($DB,$PREFERENCES,$m,"");	
	
	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

echo "

<div style=\"text-align:center\" >
";

	
	include("../classes/PcBookings.php");
	$pcbookings = new PcBookings($DB);
	
	
	
	
			
	
		
		$_month = date("n",$PREFERENCES["pc_stats"]);
		$_year = date("Y",$PREFERENCES["pc_stats"]);
		$thismonth = date("n");
		$thisyear = date("Y");
		
		if ($_month != $thismonth || $_year != $thisyear)
		{
		
		$thismonth	 = $thismonth - 1;
		
		
		
		if ($thismonth == 0) 
		{
		$thismonth = 12; 
		$thisyear = $thisyear - 1;
		}
			
			
			$sql = "select enabled, address from pc_emailreport where m = '$m'";
       	$DB->query($sql,"pcadmin.php");
       	$row = $DB->get();
    	$enabled = $row["enabled"];
    	$address = $row["address"];
			
    	if ($enabled == 1)
    	//email stats
    	{
    	
		  $now = time();
	$sql = "update preferences set pref_value = '$now' where pref_name = 'pc_stats'";
	$DB->query($sql,"pc.php");
    //echo $sql;	
    
    		
		
		$html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html>
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
		<title>Untitled Document</title>
	</head>";
    	$html .= $pcbookings->pc_stats($thismonth,$thisyear,$DB,$phrase);	
    	$html .= "</body></html>";
    	
    	
    	
    		
		
		
	$headers = "From: Intranet";
	$random_hash = md5(date('r', time())); 
	
 

	//$html = chunk_split(base64_encode($html));
	

//$headers .= "\r\nContent-Type: multipart/alternative; boundary=\"PHP-alt-".$random_hash."\"";

//$message = "--PHP-alt-$random_hash
//Content-Type: text/plain; charset=\"iso-8859-1\"
//Content-Transfer-Encoding: 7bit

//$phrase[271]



//--PHP-alt-$random_hash
//Content-Type: text/html; charset=\"iso-8859-1\"
//Content-Transfer-Encoding: 7bit

//$html
//--PHP-alt-$random_hash";
	
	//mail("david.funnell@erl.vic.gov.au", 'PC Booking stats', $html,"From: Intranet\n" .  "MIME-Version: 1.0\n" .  "Content-type: text/html; charset=iso-8859-1");  
	@mail($address, 'PC Booking stats', $html,"From: Intranet\n" .  "MIME-Version: 1.0\n" .  "Content-type: text/html; charset=iso-8859-1");  
	

	//echo "$temp_address, $issue_name, $headers";
	//echo $headers;
	//
	//ini_set('sendmail_from', $temp_from);
	//send_email($DB,$address,$phrase[271], $message,$headers);
  	
    	}
			
			
			
		}
		
		
		
		
		
		
		
		
		$tablelocking = 1;
		
		
		
		
	
		//set this variable so included files for this module can be loaded
	$pcdisplay = "yes";
	
	//display dynamic module adminstration page
	
	
	$sql = "select name from modules where m = '$m'";
	$DB->query($sql,"pc.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);
	
	




 
if(isset($outoforder) && ($access->thispage > 1))
	{
	
	$outofordermessage = $DB->escape($_REQUEST["outofordermessage"]);
	
	if ($outoforder == "0") { $outofordermessage = "";}
	$sql = "update pc_computers set outoforder ='$outoforder', outofordermessage = '$outofordermessage', client = '$client' where pcno = '$pcno'";

	$DB->query($sql,"pc.php");
	
	
	$pcbookings->Status($pcno,$phrase);
	
	
	
	
	/*
	$sql = "select ip from pc_computers where pcno = \"$pcno\"";
	$DB->query($sql,"pc.php");
	$row = $DB->get();
	$ip = $row["ip"];
	
	if ($ip != "")
	{
	$url = "http://" . $ip . "/check/";
	ini_set('default_socket_timeout', 1);
	//$handle = @fopen($url,"r");
	@fclose($handle); 
	}*/
	
	}


	
 if (isset($_REQUEST["book"]) && ($access->thispage > 1))
       {

       	
      
       	
 		//print_r($_REQUEST);
 		
 		
		$starting = time();

        //enter booking in database
		$requeststart =  $t;
		
		  $month = date("m",$t);
		$year = date("Y",$t);
        $day = date("d",$t);
		$pcs = ($_REQUEST["pcs"])	;
		
		
		if (isset($_REQUEST["itemsout"]))
                {
                 $itemsout = $DB->escape($_REQUEST["itemsout"]);   
                } else {$itemsout = 0;}
		
		//get branch booking interval
								$sql = "select pc_booking_interval from pc_branches where branchno = '$bid'";
								//echo $sql;
								$DB->query($sql,"pc.php");
								$row = $DB->get();
                                $interval = $row["pc_booking_interval"];

	
		
		if ((isset($pcs) && count($pcs) > 1) || (isset($_REQUEST["recurrent"]) && $_REQUEST["recurrent"] == "yes"))
				{
				
			 	$sql = "select max(bookinggroup) as bookinggroup from pc_bookings";
		   	 	$DB->query($sql,"pc.php");
		   		 $row = $DB->get();
				$bookinggroup =$row["bookinggroup"] + 1;
			
				} 
		else 
				{
					
				$bookinggroup = 0;
				}
       
		
        if (isset($_REQUEST["starthour"]))
                                    {
                                      $startminute = $_REQUEST["startminute"];
                                      $starthour = $_REQUEST["starthour"];
                                      $endminute = $_REQUEST["endminute"];
                                      $endhour = $_REQUEST["endhour"];

                                    $requeststart = mktime($starthour, $startminute, 00, $month, $day,  $year);

                                     $requestend = mktime($endhour, $endminute, 00, $month, $day,  $year);
                                      }
                                      else
                                      {
									$requeststart = $t;	
									}
      
		
	   
	   //query checks maximum length
	      $sql = "select maxtime,createpin,weblimit, clientbooking  from pc_usage where useno = '$usage' ";
	    
	 $DB->query($sql,"pc.php") ;
		$row = $DB->get();
		$maxminutes = $row["maxtime"];
		$clientbooking = $row["clientbooking"];
		$createpin = $row["createpin"];
			$weblimit = $row["weblimit"];
		 $maxseconds = $maxminutes * 60;
							  
							  
							//checks patron not banned
								if (isset($_REQUEST["cardnumber"]))
							    {
						$cardnumber = $DB->escape($_REQUEST["cardnumber"]);	 
							    } else {$cardnumber = "";}
							
							$ban = $pcbookings->checkPatronBan($cardnumber);
							
							
						
								if ($ban['banned'] == "yes")	
										{ 
									
										$WARNING = "<h2>$phrase[429]</h2>
										$reason<br><br>
										$phrase[430] <br> $ban[start] - $ban[end].<br><br>
										<a href=\"pc.php?m=$m&amp;t=$t&amp;bid=$bid\">$phrase[431]</a>";
													   
										 }
								
							  
								if 	($cardnumber != "")
								{	 
								$now = time();		 
								$sql = "select count(*) as numbookings from pc_bookings where cardnumber = \"$cardnumber\" and pcusage = '$usage' and branchid = '$bid' and bookingtime > $now and cancelled = \"0\" and finished = \"0\"";
								
								$DB->query($sql,"pc.php");
								$row = $DB->get();
								$numbookings = $row["numbookings"];
						
								if ($numbookings >= $weblimit && $access->thispage == 2)	
										{ 
									
										$WARNING = "<h1>$phrase[224]</h1>
										$phrase[923]<br><br>
										<a href=\"pc.php?m=$m&amp;t=$t&amp;bid=$bid\">$phrase[431]</a>";
													   
										 }
										 
								}
		
				
							
							
						
							//check duration
							if (($requestend - $requeststart) > $maxseconds)
											{
											$WARNING = "<h1 class=\"red\">$phrase[433]</h1><br><br>
													<a href=\"pc.php?m=$m&amp;t=$t&amp;bid=$bid\">$phrase[431]</a>";
													
											}
							//check start and finish times are logical 
							if ($requestend <= $requeststart)
										{
										$WARNING = "<h1 class=\"red\">$phrase[432]</h1><br><br>
													<a href=\"pc.php?m=$m&amp;t=$t&amp;bid=$bid\">$phrase[431]</a>";
													
										}
							
							if (isset($_REQUEST["recurrent"]) && $_REQUEST["recurrent"] == "yes")
							{
										
							 if ($recurnumber > 53) 
								  {
								
								  $WARNING = "<h1 class=\"red\">$phrase[435]</h1><br><br>
													<a href=\"pc.php?m=$m&amp;t=$t&amp;bid=$bid\">$phrase[431]</a>";
								  }		
								  
							if ($access->thispage < 2 )
							 
								  {
								
								    $WARNING = "<h1 class=\"red\">$phrase[436]</h1><br><br>
													<a href=\"pc.php?m=$m&amp;t=$t&amp;bid=$bid\">$phrase[431]</a>";
								  }	
							}			
							
										
						
								
						//prepare data for sql query
						$patronname = $DB->escape($_REQUEST["patronname"]);
					
						
					
						    
					if (isset($_REQUEST["telephone"]))
							    {
						$telephone = $DB->escape($_REQUEST["telephone"]);	 
							    } else {$telephone = "";}
								
								
					
						
							
					if (!isset($WARNING)) {	
							
                  if ($_REQUEST["recurrent"] <> "yes")
                        	{
                        $array_start[] = $requeststart;
                        $array_end[] = $requestend;
                   //  echo "$requeststart $requestend;";
                      		}
                      else {
                        
                      
                      	
						if ($_REQUEST["recurinterval"] == "month")
                                {					  
                                $weekdaycounter = 0;
                                 $daysinmonth = date("t",$requeststart);
                                
                                 $day = date("j",$requeststart);
                                $weekday = date("D",$requeststart);
                                $month = date("n",$requeststart);
                                $year = date("Y",$requeststart);
								for($x=1;$x < $daysinmonth + 1;$x++)
								
								{
								
								$test =  date("D",mktime (0,0,0,$month,$x,$year));

								 if($test == $weekday)
 								{
								 
								 $weekdaycounter ++;	
 								}
								
								if ($day == $x) break;

								}
                                	
                                		
                                }
                      	
                      	
                     
                      					
                        $counter = 1;
                        while ($counter <= $recurnumber)
                            {
                              if ($counter == 1)
                                {
                                $array_start[$counter] = $requeststart;
                                $array_end[$counter] = $requestend;
                              }
                              else
                              {
                               
                                $previous = $counter - 1;
                                
                                $startminute = date("i",$array_start[$previous]);
                                $starthour = date("H",$array_start[$previous]);
                                $startday = date("d",$array_start[$previous]);
                                $startmonth = date("m",$array_start[$previous]);
                                $startyear = date("Y",$array_start[$previous]);

                                $endminute = date("i",$array_end[$previous]);
                                $endhour = date("H",$array_end[$previous]);
                                $endday = date("d",$array_end[$previous]);
                                $endmonth = date("m",$array_end[$previous]);
                                $endyear = date("Y",$array_end[$previous]);
                

                                if ($_REQUEST["recurinterval"] == "day")
                                {
                             
                                $array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth,$startday +1,$startyear);
                            	
                                $array_end[$counter] =  mktime ($endhour,$endminute,0,$endmonth,$endday +1,$endyear);
                                }
                            elseif ($_REQUEST["recurinterval"] == "week")
                                {
                             $array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth,$startday +7,$startyear);
                            $array_end[$counter] =  mktime ($endhour,$endminute,0,$endmonth,$endday +7,$endyear);
                                }
                            elseif ($_REQUEST["recurinterval"] == "fortnight")
                                {
                             $array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth,$startday +14,$startyear);
                            $array_end[$counter] =  mktime ($endhour,$endminute,0,$endmonth,$endday +14,$endyear);
                                }
                            elseif ($_REQUEST["recurinterval"] == "month")
                                {
                               /////////////////////////////////
                               
                               
								if ($startmonth == 12)
									{
									$startyear = $startyear + 1;
									$endyear = $endyear + 1;
									$startmonth = 1;
									$endmonth = 1;
									}	
								else {
									 $startmonth = $startmonth + 1;
									$endmonth = $endmonth + 1; 
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
										if ($weekdaycounter == $match) break;

										}
										
										if (!($match < $weekdaycounter))
										{
                             $array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth,$x,$startyear);
                            $array_end[$counter] =  mktime ($endhour,$endminute,0,$endmonth,$x,$endyear);
										
								
                           
                            		
										
										}
										else 
										{
										$nosuchday[$counter] = "yes";
										
										$array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth,1,$startyear);
                            			$array_end[$counter] =  mktime ($endhour,$endminute,0,$endmonth,1,$endyear);	
										}
                               
                               ////////////////////////////////
                           //  $array_start[$counter] =  mktime ($starthour,$startminute,0,$startmonth + 1,$startday,$startyear);
                           // $array_end[$counter] =  mktime ($endhour,$endminute,0,$endmonth + 1,$endday,$endyear);
                                
                                
                                
                                
                                
                                
                                }
                                
                                
                              }
                          
                            $counter++;
                            
                            }
                      }
                           

						
					
					
					
				
						
								
                            foreach($array_start as $index => $start)
						
                            	
                        {
                            
                            $validhours = "yes";
								     
								//get branch hours
							$weekday = date("w",$start);   
							
							
							
		
						
						
					
						if (isset($pcs))
							{
							 				
						
						
							foreach($pcs as $pcno => $pcname)
							{
							if (is_numeric($pcno))
								{	
							
								
								
						$weekday = date("w",$start);
							
					
						$closure = $pcbookings->Closure($bid,$start);
                        $hours = $pcbookings->OpeningHours($start,$array_end[$index],$bid,FALSE,$phrase);
						
								

        				if (isset($nosuchday) && $nosuchday[$index] == "yes")
						{
						$validhours = "no";
						//echo "noday";
						}
						
						elseif ($closure['closed'] == "yes" && $hours['error'] != "")
						{
						$validhours = "no";
						
						
						}
						

						
	if ($validhours == "no")
						{	
						  $result_status[] = "bookingfailed"; //booking added
								 $result_pc[] = "$pcno";
								 $result_start[] = $start;
								$result_end[] = $array_end[$index];  
						}			
						else {
							
							  if ($tablelocking == 1)
								{
								 if ($DB->type == "mysql")
									{  	
								$sql = "LOCK TABLE pc_bookings WRITE";
									}
								else
									{  	
								$sql = "begin transaction";
									}
								
								
								$DB->query($sql,"pc.php");
								}
								
								
						$sql = "select bookingno from pc_bookings where pcno = '$pcno' and (($start <= bookingtime and $array_end[$index] > bookingtime) or ($array_end[$index] > endtime and $start < endtime)  or ($start >= bookingtime and $array_end[$index] <= endtime)  or ($start < bookingtime and $array_end[$index] > endtime)) and cancelled = '0' and finished = '0'";
					
							  
							   $DB->query($sql,"pc.php");
		
							  $num_rows = $DB->countrows();
							
					
								
							   if ($num_rows > 0)
							
							      {
						
							     $result_status[] = "bookingfailed"; //booking added
								 $result_pc[] = "$pcno";
								 $result_start[] = $start;
								$result_end[] = $array_end[$index];  	
							      
                        		}
                        	else 
                        		{
                        		$now = time();
                        		$temp = $start - $interval * 60;	
                        		//echo "$starting $temp $createpin z $cardnumber z interval is $interval";
                        		
                        
                        		
                        		if (($checkedin == 1) && $createpin == 0) { $pin = "auto";}
                        		
                        		//($now >= ($start - $interval * 60)
                        		elseif (($cardnumber == "" && $createpin == 1) || $createpin == 2)	{   $pin = rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) ; } 
                        		
                        		else { $pin = "";}                     			
                        	
                        		
                        		  $sql2 = "INSERT INTO pc_bookings VALUES(NULL,'$patronname','$telephone', '$start',
							   '$bid', '$pcno', '$paid', '$usage', '$cardnumber', '$array_end[$index]','$ip','$_SESSION[username]','0',$now,'','','0','0','1','$checkedin','0','$bookinggroup','$pin','$itemsout')";
						
                        		 
                        		 
							      $bookingadded = $DB->query($sql2,"pcdisplay.php");
							      
							      if (isset($PCDEBUG) && $PCDEBUG == "on")
							      {
							      $bookingno = $DB->last_insert();
								$insertqueries[$bookingno] = $sql2;
							      }
							      
							      
							      
							      if ($bookingadded) 
							      {
							      
							     $result_status[] = "bookingadded"; //booking added
								 $result_pc[] = "$pcno";
								 $result_start[] = $start;
								$result_end[] = $array_end[$index];  
								
								
								
							     
							      }
							      
							      $four_hours_from_now = time() + 14400;
							     // echo "start is $start  four_hours_from_now is  $four_hours_from_now";
					
			
								if ($start < $four_hours_from_now)
									{
									$pcs_to_update[] = $pcno;
									}
							      
							      
							      
							      
							      
									
                        			
                        		}
                        		
                        		  if ($tablelocking == 1)
										{
											 if ($DB->type == "mysql")
									{  	
								 $sql = "unlock tables";
									}
								
											else
									{  	
								 $sql = "commit";
									}
								 		
								  		$DB->query($sql,"pc.php");
								  		} 
								             		
								
								} //validhours = yes
								} //isnumeric
								
								 
								} //for each pc
								}
								
									/////////////////// 
							
							
						
                        } 
                          
                        
                      if (isset($pcs_to_update))
                      {
                        foreach($pcs_to_update as $index => $pcno)
							{
							$pcbookings->Status($pcno,$phrase);	
								
							}
                      }
                        
                      
                      
                       if (isset($insertqueries))
                      {
                        foreach($insertqueries as $bookingno => $query)
							{
							
							pc_debug($DB,$bookingno,$query);
								
							}
                      }
										//echo "<h1>updating status pc $pcno</h1>";
										//if (property_exists($pcbookings,"DB")) {echo " DB exists";} else {echo " DB exists";} 
									
									//$pcstatus = new PcBookings($DB2);
									//$pcstatus->Status($pcno,$phrase);
                      
                  /*    	foreach($pcs as $pcno => $pcname)
							{
							if (is_numeric($pcno))
								{
								$sql = "select ip from pc_computers where pcno = \"$pcno\"";
					$DB->query($sql,"pc.php");
	$row = $DB->get();
	$ip = $row["ip"];
	
	if ($ip != "")
	{
	 echo "";
	 $ip = "192.168.10.233";
	$url = "http://" . $ip . "/check/";
	ini_set('default_socket_timeout', 1);
	$handle = @fopen($url,"r");
	@fclose($handle); 
	}
								 
								 }
							}*/
            
                    	
						
                                
       }
                            
     							
			
							  		
								  				
				
   }
   
  if (isset($_REQUEST["finish"]) && ($access->thispage > 1))
       {
       	
    
		$now = time();
		
	   	$sql = "update pc_bookings set endtime = '$now' where bookingno = '$bookingno'";
		
		
		$DB->query($sql,"pc.php");
		
		   if (isset($PCDEBUG) && $PCDEBUG == "on")
			{
			pc_debug($DB,$bookingno,$sql . " finished by staff $now");	
			}
		
		
						     
		
       	
       	$sql = "select bookingtime, pcno from pc_bookings where bookingno = '$bookingno'";
			$DB->query($sql,"pc.php");
			$row = $DB->get();
			$start = $row["bookingtime"];
			$pc = $row["pcno"];
			
	
		$pcbookings->Status($pc,$phrase);
		
       	
       	
       } 
       
  if (isset($_REQUEST["shortenbooking"]) && ($access->thispage > 1))
       {
       	
      $sql = "update pc_bookings set endtime = '$t' where bookingno = '$bookingno'";
						     
		$DB->query($sql,"pc.php");
		
		   if (isset($PCDEBUG) && $PCDEBUG == "on")
			{
			pc_debug($DB,$bookingno,$sql . " booking shortened by staff $now");	
			}
		
       	
       	$sql = "select bookingtime, pcno from pc_bookings where bookingno = '$bookingno'";
			$DB->query($sql,"pc.php");
			$row = $DB->get();
			$start = $row["bookingtime"];
			$pc = $row["pcno"];
			
		
		$pcbookings->Status($pc,$phrase);
		
       	
       	
       } 
    if (isset($_REQUEST["deleteuser2"]) && ($access->thispage > 1))
       { 
       	
       	$barcode = $DB->escape(str_replace(" ", "", $_REQUEST["barcode"]));

       $sql = "delete from  pc_users  where barcode = '$barcode'";
      	$DB->query($sql,"pc.php"); 	
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
      	$DB->query($sql,"pc.php"); 	
      	//echo $sql;
       	}
       	 	
       }
       
           
   if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "adduser" && ($access->thispage > 1))
       {   
       	  
       	$password = md5($_REQUEST["password"]);
       	$barcode = $DB->escape(str_replace(" ", "", $_REQUEST["barcode"]));
       	
       	if ($barcode == "")
       	{ 
       		echo "";
       	}
       	else 
       	{
		$sql = "insert into pc_users values ('$barcode','$password','$change')";
      	$DB->query($sql,"pc.php"); 	
       	}
       	 	
       }
       
     if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "checkin")
       {   
       	  
       
       	$barcode = str_replace(" ", "", $_REQUEST["barcode"]);
       	
       	$sql = "select cardnumber,pin from pc_bookings where bookingno = '$bookingno'";
       	$DB->query($sql,"pc.php"); 	
       	$row = $DB->get();
		$cardnumber = trim($row["cardnumber"]);
		$pin = trim($row["pin"]);
		
			if (($cardnumber != $barcode) && $_REQUEST["override"] != 1 &&  $cardnumber != "")
			{
				$event = "overridebarcode";
			
			}
	//	echo "$cardnumber $barcode $_REQUEST[overide]";
		
		if (($cardnumber == $barcode) || $_REQUEST["override"] == 1 || $cardnumber == "")
      {
      	if ($pin == "" && $barcode == "") {$pin = "auto";}
      	
		$sql = "update pc_bookings set checkedin = '1',cardnumber = '$barcode', pin = '$pin'  where bookingno = '$bookingno'";
	
      	$DB->query($sql,"pc.php"); 
      	
      	   if (isset($PCDEBUG) && $PCDEBUG == "on")
			{
			$now = time();
			pc_debug($DB,$bookingno,$sql . " checked in by staff $now");	
			}
		
      	
      	
      		$sql = "select bookingtime, pcno from pc_bookings where bookingno = '$bookingno'";
			$DB->query($sql,"pc.php");
			$row = $DB->get();
			$start = $row["bookingtime"];
			$pc = $row["pcno"];
			
		
		$pcbookings->Status($pc,$phrase);
		
      	
      	//echo $sql;
		  }	
      	
      
       	 	
       }
          
        
  if (isset($_REQUEST["updatebooking"]) && ($access->thispage > 1))
       {
	  // print_r($_REQUEST);
	  
	 
	   
	  $pin = $DB->escape($_REQUEST["pin"]);
	  
       		$bookinggroup = $DB->escape($_REQUEST["bookinggroup"]);
       		$cardnumber = $DB->escape(str_replace(" ", "", $_REQUEST["cardnumber"])); 
			if (!isset($bookingtime)) {
				
				
				
				$month = date("m",$t);
				$day = date("d",$t);
				$year = date("Y",$t);
			
				 
                $bookingtime = mktime($starthour, $startminute, $startsecond, $month, $day,  $year);

                $endtime = mktime($endhour, $endminute, $endsecond, $month, $day,  $year); 
			
			}
       
  
       	
       	if ($bookinggroup > 0)
       	{
       		
       		$sql = "select * from pc_bookings where bookinggroup = '$bookinggroup'";
       		 $DB->query($sql,"pc.php");
			while ($row = $DB->get()) 
		{
			$booking[] =$row["bookingno"];	
			$btime[] =$row["bookingtime"];
			$etime[] =$row["endtime"];
			$pc[] = $row["pcno"];
			$ipaddress[] = $ip;
			//$pin[] = $row["pin"];
		}
	
	
       	}
       	else 
       	{
       	$sql = "select * from pc_bookings where bookingno = '$bookingno'";
     
     
       		 $DB->query($sql,"pc.php");
			$row = $DB->get();
			//print_r($row);
			$booking[] =$bookingno;	
			$btime[] =$t;
			$etime[] =$endtime;
			$pc[] = $pcno;
			$ipaddress[] = $ip;
		//	$pin[] = $row["pin"];
		
		
		
			if ($pcno != $oldpcno)
			{
			$sql = "delete from pc_status where pcno = '$oldpcno'";	
			//echo $sql;
			$DB->query($sql,"pc.php");	
			}
		
			
		}
	   
	

	   
	 
	 
	   
	   
	   if (isset($_REQUEST["cardnumber"]))
			{
			
			
		
			
			
			if (isset($barcode))	
				{ 
				 $WARNING = "<h1 class=\"red\">$phrase[430]</h1>
				$phrase[429]<br>
				 $start - $end.<br><br>
				<a href=\"pc.php?m=$m&amp;t=$bookingtime&amp;bid=$bid\">$phrase[431]</a>";
				}
			}
	   
	   
	if (isset($_REQUEST["cardnumber"]))
							    {
						$cardnumber = $DB->escape($_REQUEST["cardnumber"]);	 
							    } else {$cardnumber = "";}
						    
					if (isset($_REQUEST["telephone"]))
							    {
						$telephone = $DB->escape($_REQUEST["telephone"]);	 
							    } else {$telephone = "";}
	    if (isset($_REQUEST["itemsout"]))
							    {
						$itemsout = $DB->escape($_REQUEST["itemsout"]);	 
							    } else {$itemsout = "0";}
                                                            
                                                            
	    $patron = $DB->escape($_REQUEST["patron"]);   
	

	  	//query checks maximum length
	      $sql = "select *  from pc_usage where useno = '$pcusage' ";
	     
						    $DB->query($sql,"pc.php");
								$row = $DB->get();
						      $usename = $row["name"];
						      $fee = $row["fee"];
						      $usage = $row["useno"];
						      $maxminutes = $row["maxtime"];
						      $createpin = $row["createpin"];
							  $maxseconds = $maxminutes * 60;
							 
				  
					  
   
	  
	  
		if ($endtime <= $bookingtime)
		{
			$WARNING = "<h1 class=\"red\">$phrase[443]</h1>
					<a href=\"pc.php?t=$bookingtime&amp;m=$m&amp;bid=$bid\">$phrase[431]</a>";
					
		}
		
								   
	/*
		elseif (($endtime - $bookingtime) > $maxseconds + 60)
			{
				//$diff = $endtime - $bookingtime;
				$WARNING = "<h1 class=\"red\">$phrase[444]</h1>
					<a href=\"pc.php?t=$bookingtime&amp;m=$m&amp;bid=$bid\">$phrase[431]</a>";
					
			}
		
		*/
			
		
	

		if (!isset($WARNING))
				{	
					
		foreach($btime as $index => $start)
							{	
					
								
								 
						$month = date("m",$start);
						$day = date("d",$start);
						$year = date("Y",$start);
						$weekday = date("w",$start);
                        

							
							  		
								
				    if (isset($_REQUEST["starthour"]))
                                    {
                                
                                    $startsecond = $_REQUEST["startsecond"];
                                    $endsecond = $_REQUEST["endsecond"];
                                                       
                                  
                                      }
                        else 
                        {
                        $startsecond = date("s",$bookingtime);
                        $endsecond = date("s",$endtime);
                        
                        $startminute = date("i",$bookingtime);
                        $endminute = date("i",$endtime);
                        
                        $starthour = date("H",$bookingtime);
                        $endhour = date("H",$endtime);
                        
                        
                        
                        
                        	
                        }
                        
                        
                        $bookingtime = mktime($starthour, $startminute, $startsecond, $month, $day,  $year);

                        $endtime = mktime($endhour, $endminute, $endsecond, $month, $day,  $year); 
                                  								
                        
                        $closure = $pcbookings->Closure($bid,$start);
                        $hours = $pcbookings->OpeningHours($start,$endtime,$bid,FALSE,$phrase);
                        
                       // $open = $pcbookings->library_open($weekday,$bid,$start,$endtime);
		
        			
                        
                        if ($tablelocking == 1)
							{
							
							
								 if ($DB->type == "mysql")
									{  	
								$sql = "LOCK TABLE pc_bookings WRITE";
									}
								else
									{  	
								$sql = "begin transaction";
									}
							}
				
	
		//check that time is still available to change booking times

		$sql = "select bookingno from pc_bookings where pcno = '$pc[$index]' and (($bookingtime <= bookingtime and $endtime > bookingtime) or ($endtime > endtime and $bookingtime < endtime)  or ($bookingtime >= bookingtime and $endtime <= endtime)) and cancelled = '0' and finished = '0' and bookingno <> '$booking[$index]'";
			 $DB->query($sql,"pc.php");
		
			$num_rows = $DB->countrows();	
			
			
			
			
			
		
			if (($num_rows == 0 && $closure['closed'] == "no" && $hours['error'] == "") || $cancelled == 1)
			
				{
				
				if ($createpin == 0) //barcode mode
				{
				if ($checkedin == 1 && $pin == "") {$pin = "auto";}	
				if ($checkedin == 0 && $pin == "auto") {$pin = "";}
				}	
				
				if ($createpin == 1) //mixed mode
				{
					if  (($pin == "" || $pin == "auto") && $cardnumber == "") { $pin = rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) ;}
					if ($cardnumber != "") {$pin = "";}
				}
			
				if ($createpin == 2) //pin mode
				{
					if  ($pin == "" || $pin == "auto") { $pin = rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) ;}
				}
				
				
			$now = time();
			
			if ($cancelled == 1)
			{
			$cancelip = ip("pc");
			$cancelname = $_SESSION["username"];
			$canceltime = time();	
			}
			else 
			{
			$cancelip = "";
			$cancelname = "";
			$canceltime = "0";	
			}
			
			
					
				$sql = "update pc_bookings set name= '$patron', telephone = '$telephone',
				branchid = '$bid', pcno = '$pc[$index]', paid = '$paid', pcusage ='$pcusage',  bookingtime = '$bookingtime', endtime ='$endtime',
				cardnumber = '$cardnumber' , modtime = '$now', ip = '$ip', username = '$_SESSION[username]', checkedin = '$checkedin', cancelled = '$cancelled'
				, cancelip = '$cancelip', cancelname = '$cancelname', canceltime = '$canceltime', pin = '$pin',itemsout = '$itemsout' where bookingno = '$booking[$index]'";
				
				//echo $sql;
						     
				  $DB->query($sql,"pc.php");  ;
			
				 $result_status[] = "bookingupdated"; //booking added
				$result_pc[] = $pc[$index];
				 $result_start[] = $bookingtime;
				$result_end[] = $endtime; 	 
				
				  if (isset($PCDEBUG) && $PCDEBUG == "on")
							      {
							      $bookingno = $booking[$index];
								$updatequeries[$bookingno] = $sql;
							      }
	
				
				
				
			
			if ($bookingtime < time() + 14400)
			{
			$pcbookings->Status($pc[$index],$phrase);
			}
				
				
				
				}
			
				
				else {
					
				$result_status[] = "updatefailed"; //booking added
				$result_pc[] = $pc[$index];
				 $result_start[] = $bookingtime;
				$result_end[] = $endtime; 
				}
				                	
				
				//unlock tables
					if ($tablelocking == 1)
						{	
				 if ($DB->type == "mysql")
									{  	
								$sql = "unlock tables";
									}
								 else
									{  	
								$sql = "commit";
									}
				}
				
							
				} //end loop 
							
				} //end warning condition
				
				
				  if (isset($updatequeries))
                      {
                        foreach($updatequeries as $bookingno => $query)
							{
							
							pc_debug($DB,$bookingno,$query);
								
							}
                      }
				
				/**
				//get self booking PCs to do a status check
				$pclist = array_unique($pc);
				
				foreach($pclist as $i => $pcnum)
							{
							if ($ipaddress[$i] != "")
								{
								$url = "http://" . $ip . "/check/";
								ini_set('default_socket_timeout', 1);
								$handle = @fopen($url,"r");
								@fclose($handle); 
								}
										
						
							}
							
							**/
				

			
				
				
				 
   }


   
   if (isset($WARNING))
		{
		warning($WARNING);	
		}	
  
 elseif (isset($_REQUEST["deleteuser"]))
 	{
	$barcode = urlencode($_REQUEST["barcode"]);
	
	
	echo "<h1 >$modname</h1><br><b>$phrase[14]</b><br><br>
	<a href=\"pc.php?m=$m&amp;event=patron&amp;barcode=$barcode&deleteuser2=yes\">$phrase[12]</a> | <a href=\"pc.php?m=$m&amp;event=patron\">$phrase[13]</a>";
	
	}  
   
  elseif (isset($event) && $event == "overridebarcode")
 	{
	$barcode = urlencode($barcode);
	

	echo "
	
	<h1 >$modname</h1><h2>$phrase[711]</h2><div style=\"font-size:1.4em\"><br><span class=\"red\" >$phrase[717]</span><br><br>
	<a href=\"pc.php?m=$m&amp;update=checkin&amp;bookingno=$bookingno&amp;barcode=$barcode&override=1&amp;bid=$bid&amp;t=$t\">$phrase[12]</a> | <a href=\"pc.php?m=$m&amp;bid=$bid&amp;t=$t\">$phrase[13]</a></div>
	";
	
	}  


  elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "group")
 	{
	$pcs = unserialize($_REQUEST["pcs"]);
	

	                      	



				
				
							
				echo "<h1>$modname</h1>
				<a href=\"pc.php?m=$m\" >$phrase[461]</a> | <a href=\"pc.php?m=$m&amp;bid=$bid&amp;t=$t&amp;event=quick\" >$phrase[710]</a>";
							
						
						  
						  echo " | <a href=\"pc.php?m=$m&amp;event=getbookings\">$phrase[731]</a>";
						  
						  
						
						if ($PCAUTHENTICATION == "local") { echo " |
						<a href=\"pc.php?m=$m&amp;event=patron\">$phrase[703]</a><br>";}
						
	
	
	echo "<h2>$phrase[754]</h2><form action=\"pc.php\" method=\"post\" onsubmit=\"return check()\">
	<ul class=\"nodec\" style=\"margin:20px 0 20px 50%\">";
	
		foreach($pcs as $pcno => $pcname)
							{
							 
							 if (is_numeric($pcno))
							 {
							
							echo "<li> <input type=\"checkbox\" name=\"pcs[$pcno]\" value=\"$pcname\"> $pcname</li>";	
							}
							 
							 }
	
	echo "</ul><p>
	<input type=\"hidden\" name=\"m\" value=\"$m\">
		<input type=\"hidden\" name=\"group\" value=\"1\">
	<input type=\"hidden\" name=\"type\" value=\"$type\">
	<input type=\"hidden\" name=\"bid\" value=\"$bid\">
	<input type=\"hidden\" name=\"requeststart\" value=\"$requeststart\">
	<input type=\"hidden\" name=\"requestend\" value=\"$requestend\">
		<input type=\"hidden\" name=\"f\" value=\"1\">
	<input type=\"submit\" name=\"Choose\" value=\"$phrase[756]\"></p>
	</form>
	
	<script type=\"text/javascript\">

	function check()
	{
	 
	var empty = true;
	var inputs = document.getElementsByTagName(\"input\");
	for(var i=0;i<inputs.length;i++)
		{
		 if (inputs[i].checked == true)
		 	{
			empty = false;	
			}
		}
		
	
	if (empty == true)
		{
		 	alert(\"$phrase[755]\")
			return false;
		}
	}
		
	</script>
	
	
	
	
	
	";
	
	
	}
   
elseif (isset($_REQUEST["finishbooking"]))
 	{
	
	
	
	echo "<h1 >$modname</h1><br><b>$phrase[448]</b><br><br>
	<a href=\"pc.php?m=$m&amp;t=$t&amp;bid=$bid&amp;flexible=$flexible&finish=yes&amp;bookingno=$bookingno\">$phrase[12]</a> | <a href=\"pc.php?m=$m&amp;t=$t&amp;bid=$bid\">$phrase[13]</a>";
	
	}  
		
	
	
	
	
	
	
  elseif (isset( $result_status) && 
  (count($result_status) > 1 || in_array("bookingfailed", $result_status) || in_array("updatefailed", $result_status))
  )

  
  {
  	$sql = "select pcno, name from pc_computers";
			  $DB->query($sql,"pc.php");
		while($row = $DB->get())
		{
			$pcnames[$row["pcno"]] = $row["name"];
		}
			
  
  	
	echo "<h1 >$modname</h1>
	
		<script type=\"text/javascript\">
							   								         
				function setfocus() 
				 {
				 document.getElementById('ff').focus(); 
				 }

				 window.onload = setfocus;
				</script>
				
				<h2>$phrase[240]</h2><table style=\"margin-right:auto;margin-left:auto;text-align:left\" cellpadding=\"5\" class=\"colourtable\">
				";
//asort($result_status);

	  foreach($result_status as $index => $status)
	  {
	  	$date = strftime("%x", $result_start[$index]);
			
				$start = date("g:ia", $result_start[$index]);
				$end = date("g:ia", $result_end[$index]);
	  	
				echo "<tr ";
				if ($status == "bookingfailed" || $status == "updatefailed") { echo " class=\"red\"";}
				echo "><td>";
				if ($status == "bookingfailed") { echo $phrase[769];}
				if ($status == "bookingadded") { echo $phrase[736];}
				if ($status == "updatefailed") { echo $phrase[770];}
				if ($status == "bookingupdated") { echo $phrase[771];}
				
				
				$pc = $result_pc[$index];
			
				echo "</td><td>$start - $end</td><td>$date</td><td>$pcnames[$pc]</td></tr>";
	  }

	 
	  
	  
	  echo "</table>
	  <br><form action=\"pc.php\" method=\"get\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"t\" value=\"$t\">
	<input type=\"hidden\" name=\"bid\" value=\"$bid\">
	<input type=\"submit\" id=\"ff\" name=\"submit\" value = \"$phrase[431]\">
	</form><br><br>
	  ";
	

    	

	

          
}


  elseif (isset($f) && ($access->thispage > 1))
    {
	
    	

    	
    	
    	
    	
	if ($f != 1) {
		
			$sql = "select name from pc_computers where pcno = '$f' ";
			$DB->query($sql,"pc.php");
			$row = $DB->get();
			$pcs[$f] = $row["name"]; 
	}
	
	
  	if (isset($_REQUEST["pcs"]))
  		{
  		 
  		 if ($group == 0)
  		 {
		$pcs = unserialize($_REQUEST["pcs"]);
		}
		else {$pcs = $_REQUEST["pcs"];}
		
	
		
			$counter = 1;
			foreach($pcs as $pcno => $pcname)
                {
				if ($counter == 1)
					{
					$f = $pcno;		
					}
				$counter++;
				}	
		
		}
		
	
  //print_r($_REQUEST);
  
    // display booking form
 	
					//query to display branch location and pc  names
								$sql = "select pc_branches.branchno as branchno, pc_branches.name as branchname, pc_computers.name as pcname,itemsout, pc_computers.flexible as flexible, pc_booking_interval from pc_branches, pc_computers where pcno = '$f' and pc_branches.m = '$m' and pc_branches.branchno = pc_computers.branch";
								$DB->query($sql,"pc.php");
								$row = $DB->get();
								$branchname = formattext($row["branchname"]);
								$branchno = $row["branchno"];
								$pcname = formattext($row["pcname"]);
								$flexible = $row["flexible"];
                                                                $itemsout = $row["itemsout"];
                                $interval = $row["pc_booking_interval"];
                                
                                
                                if (isset($group))
                                {
                                	$counter = 1;
                                	$count = count($pcs);
                                	$pcname = "";
                                	foreach($pcs as $pcno => $pname)
               							 {
               							 $pcname.= "$pname";
               							 if ($counter < $count) { $pcname .= ", ";}
               							 $counter++;
               							 
               							 }
					
                                	
                                }
                                
                                
                                
                                
                                
                   //display nav links             
      
				
							
				echo "<h1>$modname</h1>
				<a href=\"pc.php?m=$m&amp;bid=$branchno&amp;t=$t\" >$branchname</a> | <a href=\"pc.php?m=$m&amp;bid=$branchno&amp;t=$t&amp;event=quick\" >$phrase[710]</a>";
							
						
						  
						  echo " | <a href=\"pc.php?m=$m&amp;event=getbookings\">$phrase[731]</a>";
						  
						  
						
						if ($PCAUTHENTICATION == "local") { echo " |
						<a href=\"pc.php?m=$m&amp;event=patron\">$phrase[703]</a><br>";}
						
						echo "<br>";
						
						
       
								
								//query to find out usages available for computer
								if ($access->thispage < 3 )  {$insert = "and pc_usage.power = 0"; } else { $insert = "";}
								// $sql = "select pc_usage.useno  from pc_computers, pc_usage, pc_bridge where pc_computers.default_usage = '1' and pc_computers.pcno = pc_bridge.pcnum and pc_bridge.pcnum = '$f' and pc_bridge.useno = pc_usage.useno $insert order by default desc";
								
								
								
							$sql = "SELECT  pc_usage.fee AS fee, pc_usage.telephone AS telephone, pc_usage.clientbooking AS clientbooking,pc_usage.useno AS useno,pc_usage.mintime AS mintime,pc_usage.maxtime AS maxtime,pc_usage.defaulttime AS defaulttime, pc_usage.name as uname , pc_computers.default_usage as default_usage
FROM pc_computers, pc_usage, pc_bridge
WHERE pc_computers.pcno = pc_bridge.pcnum
AND pc_bridge.pcnum = '$f'
AND pc_bridge.useno = pc_usage.useno $insert
ORDER BY uname";		
									
						//echo $sql;
								
							
								 
								$DB->query($sql,"pc.php");
								$num_rows = $DB->countrows();
							
								while ($row = $DB->get())	
								{
								
								$useno = $row["useno"];	
								$default = $row["default_usage"];	
									
								$uses_name[$useno] = $row["uname"];
								$uses_fee[$useno] = $row["fee"];	;
								$uses_maximum[$useno] = $row["maxtime"];
								$uses_minimum[$useno] = $row["mintime"];
								$uses_defaulttime[$useno] = $row["defaulttime"];
								$uses_telephone[$useno] = $row["telephone"];	
								$uses_clientbooking[$useno] = $row["clientbooking"];	
								}
								
							
								
									
								
								
								
							
								
								
							
								if ($num_rows == 0)
									{
									echo "<p>$phrase[450]</p>"; //no usages allocated to this computer
									}
								
																
							//	elseif (($t + (180 * 60)) < $now)
                        					//{
                        					// echo "<h2 class=\"red\">$phrase[735]</h2>";
                        					// }
								else
									{
                                                                    
                                                                    
                                                                    
                                                                    if (isset($type)) {$default = $type;} 
								
							
								if (is_numeric($default) != True) 	{$default = $useno; }
								
								
									$displaydate = strftime("%A %x", $t);
								$displaytime = date("g:i A", $t);
								//$displaytime =  date("g:i a l jS M Y", $t);
								
								$now = time();
                                                                    
                                                                    
                                                                    
                                                                    
                                                                    
										
										$usename = $uses_name[$default];
								      $fee = $uses_fee[$default];
								      $type = $default;
								      $maximum = $uses_maximum[$default];
								      $minimum = $uses_minimum[$default];
									  $defaulttime = $uses_defaulttime[$default];
									  $telephone = $uses_telephone[$default];
									  $clientbooking= $uses_clientbooking[$default];
										
										
										 //add booking
						
							//print_r($_REQUEST);
								
       	                      
								
								if (isset($_REQUEST["starthour"]))
                                    {
                                      $startminute == $_REQUEST["startminute"];
                                      $starthour == $_REQUEST["starthour"];
                                     

                                    $t = mktime($starthour, $startminute, 01, $month, $day,  $year);


                                      }
                                      
                                       
                             if (!isset($requeststart)) { $requeststart = $t;    }
                             else {
                             	$t = $requeststart; 
                             	
								}
                                
                                      
                                if (isset($requestend))
                                    {
                                    $endtime = $requestend;
                                  
                                   }
                                   else {
                                    $endtime = $t + ($defaulttime * 60); 
                            
                                   }
                                
                                   
                          // echo "default is $defaulttime";
                                   
                                   
                                 $month = date("m",$t);
								$year = date("Y",$t);
								$day = date("d",$t);

                                	$displayday = strftime("%A %x", $t);
								$displaytime = date("g:i A", $t);
								
								$now = time();

								
								

							
								
							
			     echo  "
			     	<div class=\"swouter\">
			     	<form class=\"swinner\" action=\"pc.php\" method=\"get\" style=\"margin-top:1em;\"><fieldset><legend>$phrase[451]</legend>
								  
								 
								   								         	<script type=\"text/javascript\">
				function init()
				{
				recurrent();
				setfocus() ;
				
				}
								   								         	   
				function setfocus() 
				 {
				 
				  	document.getElementById('ff').focus(); }
				
				
				
			var start = new Array();
			var end = new Array();
	";
	
			$startofday = mktime(01, 01, 01, $month, $day,  $year);
								$endofday = mktime(23, 59, 59, $month, $day,  $year);
								
								$sql = "select * from pc_bookings where pcno = '$f' and bookingtime > '$startofday' and bookingtime < '$endofday'  and cancelled = '0' and finished = '0' ";
								
								$DB->query($sql,"pc.php");
								$index= 0;
								while ($row = $DB->get())
									{
									
									$bookingtime = $row["bookingtime"];
									$etime = $row["endtime"];
									$sh = date("H", $bookingtime);
									$sm = date("i", $bookingtime);
									$eh = date("H", $etime);
									$em = date("i", $etime);
									
									
								
$start = ($sh * 60 ) + ($sm * 1);
$end =  ($eh * 60 ) + ($em * 1);

echo "
start[$index] = $start
end[$index] = $end
";

									$index++;
								
									}
	
	echo "
	
	function test()
	{
	document.getElementById('warning1').className = '';
	  document.getElementById('warning2').className = '';
	  
	  
	var index = document.getElementById('starthour').selectedIndex
	var starthour = document.getElementById('starthour').options[index].value	
	index = document.getElementById('startminute').selectedIndex
	var startminute = document.getElementById('startminute').options[index].value
	index = document.getElementById('endhour').selectedIndex
	var endhour = document.getElementById('endhour').options[index].value
	index = document.getElementById('endminute').selectedIndex
	var endminute = document.getElementById('endminute').options[index].value
	

	



	var startcount = (starthour * 60) + (startminute * 1)
 	var endcount = (endhour * 60) + (endminute * 1)
 	
 	
 	

	
	


	//var duration = endcount - startcount;
	//if ( startcount >= endcount || duration > $maximum)
	//{
	
	//  document.getElementById('warning1').className = 'redbackground';
	//   document.getElementById('warning2').className = 'redbackground';
	// }
	
	
	var error1 = \"no\"
	var error2 = \"no\"
	var x=0;
	for (x=0; x < start.length; x++)
	{
	
	
	if ((startcount <= start[x] && endcount >= end[x]) ||  (endcount > start[x] && endcount < end[x]) || 
	(startcount >= start[x] && endcount < end[x]))
	{
	
	  document.getElementById('warning1').className = 'redbackground';
	   document.getElementById('warning2').className = 'redbackground';
	 }	
		

	


	}	
	 }						

	 
	
	 
	 function recurrent()
	 {
	if (document.getElementById('recurrent_yes'))
	{
	 document.getElementById('recurrent_yes').onclick=show
	 document.getElementById('recurrent_no').onclick=hide
	}
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
	 document.getElementById('recurrentoptions').style.display = \"inline\";	
	 }
	 }
	 
	  
	 addEvent(window, 'load', init);
	 </script>
								   
								   
								   
								   
								     
					
								<table >
							    
								
								
	<tr><td class=\"formlabels\" >$phrase[141]</td><td style=\"width:20em\"> <input type=\"text\" id=\"ff\" name=\"patronname\" size=\"40\" maxlength=\"80\">  </td></tr>
							";
							
										   	if ($telephone == "1")
								   {
								   echo "<tr><td class=\"formlabels\">$phrase[132]</td><td > <input type=\"text\" name=\"telephone\" size=\"40\"></td>";
								   }
                                                                   
                                                                   
                                                                   
                                                                   
                                                                     		   	if ($itemsout > 0)
								   {
								   echo "<tr><td class=\"formlabels\">$phrase[1096]</td><td > <select name=\"itemsout\">";
                                                                                                $counter = 0;
      while ($counter < 9 && $counter <= $itemsout)
      {
          echo "<option value=\"$counter\">$counter</option>";
          $counter++;
      }
                                                                                            
                                                                                    echo "        </select></td></tr>";
								   }
                                                                   
                                                                   
								   
								echo "<tr><td class=\"formlabels\">$phrase[460]</td><td > <input type=\"text\"  name=\"cardnumber\" size=\"40\" maxlength=\"100\"></td></tr>";   					
							$hours = $pcbookings->OpeningHours($t,$endtime,FALSE,$f,$phrase);
							
						
							
									//next booking starts
								$sql = "select min(bookingtime) as next from pc_bookings where branchid = '$branchno' and pcno = '$f' and bookingtime >= '$t' and cancelled = '0' and finished = '0'";
								 $DB->query($sql,"pc.php");
									$row = $DB->get();
								$next = $row["next"];
								if (!isset($next)) {$next = $t + 86400;}
							
							
						//print_r($hours);
						//exit();
						
							
								
								if (!isset($defaulttime)) {$defaulttime = $interval;}
								
								
								if (!isset($requestend)) {$requestend = $t + ($defaulttime * 60); $disp = $requestend;}   
									
                                if ($next != "" && $next < $requestend) {$requestend = $next;}
                                
                                if ($requestend > $hours["closing"]) {$requestend = $hours["closing"];}
                                
                                
                       
								if ($now > $requeststart && $now < $requestend)
								{
									
									//echo "hello";
									$requeststart = $now;
								}
								
								echo "
								
								<tr  id=\"warning1\"><td class=\"formlabels\">$phrase[242]</td><td >
								";
								
								//if (!isset($defaulttime)) {echo "set";}

								//creates drop down list of booking durations
								$day = date("d",$t);
								$month = date("m",$t);
								$year = date("Y",$t);

								
								
								


                                $requestminute = date("i",$requeststart);
                                $requesthour = date("G",$requeststart);
                                
                               // minute is $requestminute
                                
                                echo "<select id=\"starthour\" name=\"starthour\" onchange=\"test()\">
                                ";

                                $counter = $hours["openhour"];
                                while ($counter <= $hours["closehour"])
                                    {
                                    $time = date("G",mktime($counter, 0, 0, $month, $day,  $year));
									$displayhour = date("g",mktime($counter, 0, 0, $month, $day,  $year));
                                      echo "<option value=\"$counter\"";
                                      if ($requesthour == $time) { echo " selected";}
                                      echo ">$displayhour</option>";
                                      $counter++;
                                    }

																     echo "</select>

                                    <select id=\"startminute\" name=\"startminute\" onchange=\"test()\">";
                                    $counter = 0;
                                    while ($counter< 60)
                                        {
                                        $test = str_pad($counter, 2, "0", STR_PAD_LEFT);
                                        echo "<option value=\"$test\"";
                                        if ($test == $requestminute) { echo " selected";}
                                        echo ">$test</option>";
                                        $counter++;
                                        }
                                    
                                    
                                    echo "                               </select>  </td></tr>

								<tr id=\"warning2\"><td class=\"formlabels\">$phrase[243]</td><td >
								 <select id=\"endhour\" name=\"endhour\" onchange=\"test()\">
							";
							
                                  //determine finish time;  
                                
                                  
                            
                                  //get time of next booking if exists
                                //	$now = time();
                                //	$sql = "select Min(bookingtime) as bookingtime from pc_bookings where pcno = '$f' and '$t' < bookingtime and cancelled = '0' and finished = '0' ";
                                //	$DB->query($sql,"session.php");
                                //	$row = $DB->get();
                                //	$next =	$row["bookingtime"];
                                	
                             
                                    
                                    
                                $endminute = date("i",$requestend);
                                $endhour = date("G",$requestend);
                           

                                $counter = $hours["openhour"];
                                while ($counter <= $hours["closehour"])
                                    {
                                    $time = date("G",mktime($counter, 0, 0, $month, $day,  $year));
                                    $displayhour = date("g",mktime($counter, 0, 0, $month, $day,  $year));
                                    
                                      echo "<option value=\"$counter\"";
                                      if ($endhour == $time) { echo " selected";}
                                      echo ">$displayhour</option>";
                                      $counter++;
                                    }



																     echo "</select>

                                    <select id=\"endminute\" name=\"endminute\" onchange=\"test()\">";
                                    $counter = 0;
                                    while ($counter< 60)
                                        {
                                        $test = str_pad($counter, 2, "0", STR_PAD_LEFT);
                                        echo "<option value=\"$test\"";
                                        if ($test == $endminute) { echo " selected";}
                                        echo ">$test</option>";
                                        $counter++;
                                        }


                                    echo "                               </select> </td></tr>
                                    <tr><td class=\"formlabels\">$phrase[186]</td><td >$displayday</td></tr>";
                                    
							
									
								
									
									echo "
								     <tr><td class=\"formlabels\">$phrase[186]<td >$displaytime $displayday</td></tr>";
							//}
                                    
                                    
                                    
                                      echo " 
								      <tr><td class=\"formlabels\">$phrase[452]</td><td >$pcname</td></tr>
								       <tr><td class=\"formlabels\">$phrase[121]</td><td >$branchname</td></tr>
										 <tr><td class=\"formlabels\">$phrase[454]</td><td><select name=\"usage\" id=\"usage\">";
										 
                                      	foreach($uses_name as $useno => $usename)
												{
                                      			echo "<option value=\"$useno\"";
                                      			if ($type == $useno) {echo " selected";}
                                      			echo ">$usename</option>";
													
												}
										echo "</select>
										 </td></tr>
								    

									
								";

								     
								if ($access->thispage > 1 ) 
								   
								   {
								   	
								   	echo "	<tr><td  class=\"formlabels\">$phrase[456]</td><td ><input type=\"radio\" name=\"recurrent\" value=\"no\" checked id=\"recurrent_no\">  $phrase[13] <input type=\"radio\" name=\"recurrent\" value=\"yes\" id=\"recurrent_yes\"> $phrase[12] &nbsp;&nbsp;&nbsp;<span id=\"recurrentoptions\">
$phrase[457] <select name=\"recurinterval\"><option value=\"day\">$phrase[182]</option><option value=\"week\">$phrase[458]</option><option value=\"fortnight\">$phrase[459]</option><option value=\"month\">$phrase[181]</option></select> $phrase[679]
<select name=\"recurnumber\">";
$counter = 2;
while ($counter < 53)
{
echo "<option value=\"$counter\">$counter</option>";
$counter++;
}

echo "</select></span>      </td></tr>";
								   	
								   }
								
								
								
								
								   
								            if ($fee > 0)
								               {
								               echo "<tr><td class=\"formlabels\">$phrase[455]  $moneysymbol$fee $phrase[138]</td>";
								                echo " <td  ><select name=\"paid\">
								                <option value=\"0\">$phrase[13]</option>
								                <option value=\"1\">$phrase[12]</option></select></td></tr>";
								           }
								
		  
								 
								  
								  
								  echo "  <tr><td class=\"formlabels\">$phrase[716]</td><td >";
			
					echo "<input type=\"radio\" name=\"checkedin\" value=\"0\" checked> $phrase[13]
						   	  <input type=\"radio\" name=\"checkedin\" value=\"1\"> $phrase[12]	";
				
	
						   
						   echo " </td></tr>					  
								  
							  
								  <tr><td></td><td >";
								  
								  if ($fee == 0 ) { echo "<input type=\"hidden\" name=\"paid\" value=\"0\">";}
									if ($access->thispage < 2 )  { echo "<input type=\"hidden\" name=\"recurrent\" value=\"no\">";}
								 
							
								 
								 	foreach ($pcs as $pcno => $pcname)
								 
								 	{
									echo "<input type=\"hidden\" name=\"pcs[$pcno]\" value=\"$pcname\">";	
									}
									
									if  (isset($_REQUEST["s"]))
								 
								 	{
								 	$s = $_REQUEST["s"];
									echo "<input type=\"hidden\" name=\"s\" value=\"$s\">";	
									} 	
									
									 
								  echo "
								
								
								
							
								<input type=\"hidden\" name=\"bid\" value=\"$branchno\">
								
								<input type=\"hidden\" name=\"t\" value=\"$t\">
								
								<input type=\"hidden\" name=\"m\" value=\"$m\">
							<input type=\"submit\" name=\"book\" value=\"$phrase[451]\">
								</td></tr></table></fieldset></form>
								
						
								</div><br>";
                                                                        }  
								 
								  if (isset($uses_defaulttime) )
									{
										echo "<script type=\"text/javascript\">
										var hours = new Array();
										var minutes = new Array();
										";
										
										foreach ($uses_defaulttime as $useno => $defaulttime)
										
										{
										$end = $t + ($defaulttime * 60);
										
										
										if ($end > $next) {$end = $next;}
										
										
										//echo "t is $t defaulttime is $defaulttime end is $end next is $next";	
										
										
										$endminute = date("i",$end);
										if (substr($endminute,0,1) == "0") {$endminute = substr($endminute,1,1);}
										
											
										$endhour = date("G",$end);	
										
										echo "hours[$useno] = $endhour;
										minutes[$useno] = $endminute;
										";
											
										}
										
										
										
										
										
										echo "
										function changeend()
										{
										var endhour = document.getElementById(\"endhour\")	
										var endminute = document.getElementById(\"endminute\")	
										var menu = document.getElementById(\"usage\")
										
	 									var type = menu.options[menu.selectedIndex].value;
	 									
	 									
	 									for (var i = 0; i < endhour.options.length; i++) 
	 									{
	 									
 										 if (endhour.options[i].value == hours[type]) 
 										 	{
											endhour.options[i].selected = true;
  											}
										}
	 									
								
	 									
										for (var i = 0; i < endminute.options.length; i++) 
	 									{
	 								
 										 if (endminute.options[i].value == minutes[type]) 
 										 	{
											endminute.options[i].selected = true;
  											}
										}
	 									
	 									
	 								
	 									}
															
										 addEvent(document.getElementById(\"usage\"), 'change',changeend);
											
										
										
										</script>";	
									}
								  
								
								
								
				
							
    	
    	
    }




  
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "getbookings")
   	{
	   echo "<h1>$modname</h1>
		<a href=\"pc.php?m=$m\">$phrase[410]</a>";
	    if ($access->thispage > 1)
	    { echo "
| <a href=\"pc.php?m=$m";
	    if (isset($bid)) {echo "&amp;bid=$bid";}
	    
	    echo "&amp;t=$t&amp;event=quick\">$phrase[710]</a>";
	    
	    
	    
	       	 $sql = "select  branchno, pc_branches.name as name from pc_branches,pc_computers where pc_branches.m = '$m' and pc_branches.branchno = pc_computers.branch  group by pc_branches.name";
$DB->query($sql,"pc.php");
		
		
while ($row = $DB->get())
      {
	
      $branchno = $row["branchno"];
     // $branchcount++;
      $branch[$branchno] = $row["name"];
 
      }
	    
	      if (isset($bid) && $bid != 0)
	   {
	   	echo " | <a href=\"pc.php?m=$m&amp;bid=$bid&amp;t=$t\"> ";
	   		foreach($branch as $branchno => $name)
              {	
	 
 if ($bid == $branchno) {echo $branch[$branchno];}

	  }
	   	echo "</a>";
	   }
	    
	    
	    
	    }
	   
	 
	   
	   	

	if ($PCAUTHENTICATION == "local")
{ echo " | <a href=\"pc.php?m=$m&amp;event=patron\">$phrase[703]</a> ";}
	   
	   echo "<br>
	   
	   
	   "; 


	//display form	
	echo "<h2>$phrase[731]</h2><br><br>
	<form action=\"pc.php\" method=\"get\"  style=\"width:80%;margin-left:auto;margin-right:auto\">
	<p>
      	
						  <b>$phrase[460]</b> <input type=\"text\" name=\"cardnumber\" id=\"ff\"> 
			
						       <input type=\"hidden\" name=\"m\" value=\"$m\">
						          <input type=\"hidden\" name=\"event\" value=\"getbookings\">
						 
						    <input type=\"submit\" name=\"submit\" value=\"$phrase[282]\" >
						
						</p>
						   </form>
							<script type=\"text/javascript\">
 
				function setfocus() 
				 {
			
				 document.getElementById('ff').focus(); 
				 }
					 addEvent(window, 'load', setfocus);
			
				</script>
						   ";
	if (isset($_REQUEST["cardnumber"]))
	{
		//display bookings
		echo "<h2>Bookings for $_REQUEST[cardnumber]</h2>";
			$cardnumber =  $DB->escape(str_replace(" ", "", $_REQUEST["cardnumber"]));
		$sql = "select bookingno, checkedin, bookingtime, endtime,pc_bookings.noshow as noshow, pc_usage.name as usename, pc_computers.name as pcname, pc_branches.name as branchname, pc_bookings.mode as mode, cancelled from pc_bookings, pc_usage, pc_computers, pc_branches where cardnumber = '$cardnumber' and pc_bookings.pcusage = pc_usage.useno
		 and pc_bookings.branchid = pc_branches.branchno and pc_bookings.pcno = pc_computers.pcno order by bookingtime desc limit 25";
					
						$DB->query($sql,"pc.php");
						$num = $DB->countrows();
						
						if ($num == 0) {echo "<br><br>$phrase[732]";} else {echo "<table class=\"colourtable\" cellpadding=\"7\" style=\"margin-left:auto;margin-right:auto\">
					 <tr><td><b>$phrase[185]</b></td><td><b>$phrase[186]</b></td><td><b>$phrase[454]</b></td>
						   <td><b>$phrase[452]</b></td><td><b>$phrase[121]</b></td> <td><b>$phrase[715]</b></td><td><b>$phrase[716]</b></td><td><b>$phrase[152]</b></td></tr>	
						";}
						while ($row = $DB->get())
						{
						 $bookingno = $row["bookingno"];
						$bookingtime = $row["bookingtime"];
						$endtime = $row["endtime"];
						$start = date("g:ia",$bookingtime);
						$date = strftime("%x",$bookingtime);
						$end = date("g:ia",$endtime);
						$branchname = $row["branchname"];
						$usename = $row["usename"];
						$pcname = $row["pcname"];
						$mode = $row["mode"];
						$cancelled = $row["cancelled"];
						$checkedin = $row["checkedin"];
						$noshow = $row["noshow"];
						
						
						echo "<tr";
						if ($cancelled == 1) { echo " class=\"grey\"";}
						echo "><td>";
						 if ($access->thispage > 1)
						 { echo "<a href=\"pc.php?m=$m&amp;u=$bookingno\">";}
						 echo "$start-$end";
						  if ($access->thispage > 1)
						  {
						 echo "</a>";
						 
						  }
						  echo "</td><td>$date</td><td>$usename</td><td>$pcname</td><td>$branchname</td><td>";
						 if ($mode == 1) {echo $phrase[712];}
						   if ($mode == 2) {echo $phrase[713];}
						   if ($mode == 3) {echo $phrase[714];}
						echo "</td><td>";
						if ($checkedin == 1){ echo "<img src=\"../images/tick.gif\" alt=\"Checked in\">";}
						echo "</td><td>";
						if ($cancelled == 1) { echo "$phrase[152]";}
						if ($noshow == 1) { echo " $phrase[742]";}
						echo "</td></tr>";
						}
						if ($num != 0){echo "</table><br><br>";}
	}
		
		
	}    

elseif (isset($u) && ($access->thispage > 1))
      {
		

      	
      
					
      	
    
	  
	  
       // change booking
			//$sql = "select *, UNIX_TIMESTAMP(endtime) as unixetime, UNIX_TIMESTAMP(bookingtime) as unixbtime, pc_bookings.name as patron, branches.name as branchname, pc_computers.name as pcname from pc_bookings, branches, pc_computers where pc_bookings.bookingno = \"$u\" and pc_bookings.branchid = branches.branchno and pc_bookings.pcno = pc_computers.pcno";
			 $sql = "select bookingno, branchno,pc_bookings.telephone as telephone, cardnumber,pc_bookings.pcno as pcno,paid,pcusage,bookingtime,endtime,mode,checkedin,modtime,username,pc_bookings.ip as ip,flexible,cancelled,bookinggroup,pin, pc_bookings.name as patron, pc_branches.name as branchname, pc_computers.name as pcname,
    pc_computers.itemsout as maxitems, pc_bookings.itemsout as itemsout 
    from pc_bookings, pc_branches, pc_computers where pc_bookings.bookingno = '$u' and pc_bookings.branchid = pc_branches.branchno and pc_bookings.pcno = pc_computers.pcno";
			 
			
						
						$DB->query($sql,"pc.php");
						$row = $DB->get();
						$bookingno = $row["bookingno"];
						$branchname = $row["branchname"];
						$branchno = $row["branchno"];
						$pcname = $row["pcname"];
						$patron = $row["patron"];
						$telephone= $row["telephone"];
						$cardnumber = $row["cardnumber"];
						$pcno = $row["pcno"];
						$paid = $row["paid"];
						$pcusage = $row["pcusage"];
						$bookingtime = $row["bookingtime"];
						$endtime = $row["endtime"];
						$mode = $row["mode"];
						$checkedin = $row["checkedin"];
						$modtime = $row["modtime"];
						$username = $row["username"];
						$ip = $row["ip"];
						$flexible = $row["flexible"];
						$cancelled = $row["cancelled"];
						$bookinggroup = $row["bookinggroup"];
						$pin = $row["pin"];
                                                 $maxitems = $row["maxitems"];
                                                $itemsout = $row["itemsout"];
					
							$total = 0;
	if ($bookinggroup > 0)
	{
	$sql = "select bookingtime from pc_bookings where bookinggroup = '$bookinggroup' and cancelled = '0'";
		$DB->query($sql,"resourcebooking.php");
		while ($row = $DB->get())
		{
		$times[] = $displaydate = strftime("%A %x",$row["bookingtime"]);
		$total++;
		}
	}
							//query branch opening hours
							
						$day = date("d",$t);
						$month = date("m",$t);
						$monthname = date("F",$t);
						$year = date("Y",$t); 	
						
						
						
						
					$hours = $pcbookings->OpeningHours($bookingtime,$endtime,$branchno,$pcno,$phrase);
					
				//	print_r($hours);
					
						$sql = "select * from pc_branches where branchno = '$branchno'";
							$DB->query($sql,"page.php");
							$row = $DB->get();
						    $interval = $row["pc_booking_interval"] * 60;
							




						//query to find out usagebooked
						    $sql = "select useno, name, fee, maxtime, mintime,print, telephone, createpin  from pc_usage where useno = '$pcusage' ";
						  $DB->query($sql,"pc.php");
							$row = $DB->get();
						      $usename = $row["name"];
						      $fee = $row["fee"];
						      $usage = $row["useno"];
						      $maximum = $row["maxtime"];
							  $minimum = $row["mintime"];
							  $print = $row["print"];
							   $createpin = $row["createpin"];
							   $showtelephone= $row["telephone"];
							
					
						
						//query to find out usages available to be able to change booking type
							if ($access->thispage < 3) {$insert = "and pc_usage.power = 0"; } else { $insert = "";}
						    $sql = "select pc_usage.useno as useno, pc_usage.name  as name from pc_usage, pc_bridge where pc_usage.useno = pc_bridge.useno and pc_bridge.pcnum = '$pcno' $insert";
						
						   $DB->query($sql,"pc.php");
							
						    while ($row = $DB->get())
										{
						      $arrayuseno[] = $row["useno"];
							  $arrayusename[] = $row["name"];
						   
									}
						
						//$displaytime =  date(" jS M Y", $t);
						$displaydate = strftime("%A %x", $t);
						echo "<h1>$modname</h1>
							<script type=\"text/javascript\">
     
      					function pop_window(url) {
      					 var pcpop = window.open(url,'','status,resizable,scrollbars,width=350,height=400')
      					
 						 if (window.focus) {pcpop.focus()}
      					}
      					
      					</script>
		
		<a href=\"pc.php?m=$m&amp;bid=$branchno&amp;t=$t\" >$branchname</a> | <a href=\"pc.php?m=$m&amp;bid=$branchno&amp;t=$t&amp;event=quick\" >$phrase[710]</a>";
							
							if ($print == 1){
						  echo " | <a href=\"javascript:pop_window('pcslip.php?m=$m&amp;bookingno=$bookingno')\">$phrase[466]</a>";}
						  
						  echo " | <a href=\"pc.php?m=$m&amp;event=getbookings\">$phrase[731]</a>";
						  
						  
						
						if ($PCAUTHENTICATION == "local") { echo " |
						<a href=\"pc.php?m=$m&amp;event=patron\">$phrase[703]</a>";}
						
						echo "<br>";
						  
					/*	  
					if ($checkedin <> 1 && $cancelled <> 1 && $createpin == 0)
					{			
					echo "								
      	<script type=\"text/javascript\">
      	
      				         
				function setfocus() 
				 {
				 document.getElementById('b1').focus(); 
				 }

				 window.onload = setfocus;
				</script>
								
								
										<form action=\"pc.php\" method=\"get\"  style=\"width:30em;margin:1em auto\">
				<fieldset><legend>$phrase[711]</legend><br>
			
					<input type=\"text\" name=\"barcode\"  id=\"b1\"> 
						<input type=\"submit\" name=\"submit\" value=\"$phrase[711]\" onclick=\"return check()\">
						<input type=\"hidden\" id=\"cardnumber\" name=\"cardnumber\" value=\"$cardnumber\">
						<input type=\"hidden\" name=\"update\" value=\"checkin\">
						 <input type=\"hidden\" name=\"bid\" value=\"$branchno\">
						
						  <input type=\"hidden\" name=\"bookingno\" value=\"$u\">
						  <input type=\"hidden\" name=\"t\" value=\"$t\">
						    <input type=\"hidden\" id=\"override\" name=\"override\" value=\"0\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						</fieldset>
						</form>
						
						<script type=\"text/javascript\">
						
						function stripper(string)
						{
						var newstring = '';
						len = string.length

						for (i = 0; i < len; i++) {
						if (string[i] != ' ')
						{
						newstring = newstring + (string[i])	;
						}
						}

						return newstring ;
						}
					
						function check()
						{
						var cardnumber = \"$cardnumber\";
						var barcode = document.getElementById('barcode').value.toString();
						barcode = stripper(barcode);
					if (barcode != cardnumber)
					{
						var agree = confirm(\"$phrase[717]\");
						if (agree)
						{
						document.getElementById('override').value = 1;	
						}
						else
						{
						return false;
						}
					}
						}
						
						
						
						</script>
					
							";}
                                                        
                                                        
                                                        
                                                        */
					
					if ($createpin != 0)
					{
						
						echo "							
      	<script type=\"text/javascript\">
      	
      				         
				function setfocus() 
				 {
				 document.getElementById('b2').focus(); 
				 }

				 window.onload = setfocus;
				</script>";
						
					}
							
		if ($cancelled == 1) { echo "<br><br><span class=\"red\" style=\"font-size:2em\">$phrase[152]</span>";}
		
		
					echo "	<script type=\"text/javascript\">
					
					var start = new Array();
			var end = new Array();
			";
					
					
						$startofday = mktime(01, 01, 01, $month, $day,  $year);
								$endofday = mktime(23, 59, 59, $month, $day,  $year);
								
								$sql = "select * from pc_bookings where pcno = '$pcno' and bookingtime > '$startofday' and bookingtime < '$endofday'  and cancelled = '0' and finished = '0' and bookingno <>  '$u'";
								
								$DB->query($sql,"pc.php");
								$index= 0;
								while ($row = $DB->get())
									{
									
									$btime = $row["bookingtime"];
									$etime = $row["endtime"];
									$sh = date("H", $btime);
									$sm = date("i", $btime);
									$eh = date("H", $etime);
									$em = date("i", $etime);
									
									
								
$s = ($sh * 60 ) + ($sm * 1);
$e =  ($eh * 60 ) + ($em * 1);

echo "
start[$index] = $s
end[$index] = $e
";

									$index++;
								
									}
								echo "
					
									
	function test()
	{
	
	  document.getElementById('warning1').className = '';
	  document.getElementById('warning2').className = '';
	  
	  
	var index = document.getElementById('starthour').selectedIndex
	var starthour = document.getElementById('starthour').options[index].value	
	index = document.getElementById('startminute').selectedIndex
	var startminute = document.getElementById('startminute').options[index].value
	index = document.getElementById('endhour').selectedIndex
	var endhour = document.getElementById('endhour').options[index].value
	index = document.getElementById('endminute').selectedIndex
	var endminute = document.getElementById('endminute').options[index].value
	

	

	

	var startcount = (starthour * 60) + (startminute * 1)
 	var endcount = (endhour * 60) + (endminute * 1)
 	
 	
 	
	//var duration = endcount - startcount;
	//if ( startcount >= endcount || duration > $maximum)
	//{
	
	 // document.getElementById('warning1').className = 'redbackground';
	 //  document.getElementById('warning2').className = 'redbackground';
	// }

	
	
	var error1 = \"no\"
	var error2 = \"no\"
	var x=0;
	for (x=0; x < start.length; x++)
	{
	var duration = endcount - startcount;
	
	if ((startcount <= start[x] && endcount >= end[x]) ||  (endcount > start[x] && endcount < end[x]) || 
	(startcount >= start[x] && endcount < end[x]))
	{
	
	  document.getElementById('warning1').className = 'redbackground';
	   document.getElementById('warning2').className = 'redbackground';
	 }	
		

	


	}	
	 }	
</script>
					
					
					
					<form action=\"pc.php\" method=\"get\"  style=\"width:30em;margin:1em auto\"><fieldset><legend>$phrase[26]</legend>
					<table >
						<tr><td class=\"formlabels\"> $phrase[460]</td><td><input type=\"text\" id=\"b2\" name=\"cardnumber\" value=\"$cardnumber\">  </td></tr>";
								if ($pin != "") {echo "	<tr><td class=\"formlabels\">$phrase[880] </td><td>$pin</td></tr>	";}
						
								
	
						echo "<tr><td class=\"formlabels\">$phrase[141] </td><td ><input type=\"text\" name=\"patron\" value=\"$patron\"></td></tr>";
				
						 if ( $showtelephone == "1")
						   {
						   echo "<tr><td class=\"formlabels\">$phrase[132]</td><td> <input type=\"text\" name=\"telephone\" value=\"$telephone\"> </td></tr>";
						   }
						
						if ( $maxitems  > 0)
						   {
						   echo "<tr><td class=\"formlabels\">$phrase[1096]</td><td> <select name=\"itemsout\" >";
                                                         $counter = 0;
      while ($counter < 9 && $counter <= $maxitems)
      {
          echo "<option value=\"$counter\"";
          if ($itemsout == $counter) {echo " selected";}
          echo ">$counter</option>";
          $counter++;
      }
                                                   echo "</select></td></tr>";
						   }
		
						  
						  echo "<tr><td class=\"formlabels\">$phrase[452] </td><td ><select name=\"pcno\">";
						   
						    $sql = "select pc_computers.pcno as pcno, pc_computers.name as name from pc_computers where branch = '$branchno' order by name";
						  
						    $DB->query($sql,"pc.php");
						    while ( $row = $DB->get())
										{
						      			$pcnumber = $row["pcno"];
										$name = $row["name"];
										echo "<option value=\"$pcnumber\" ";
										if ($pcnumber == $pcno) {
											echo " selected";
										
										}
										echo ">$name</option>";
							  			}
						
						   
						   
						   echo "</select></td></tr>
						
						<tr id=\"warning1\"><td class=\"formlabels\">$phrase[242]</td><td>";
						
						
					
						
							
								//creates drop down list of booking durations
								$startsecond = date("s",$bookingtime);
                                $requestminute = date("i",$bookingtime);
                                $requesthour = date("G",$bookingtime);
                                
                                
								echo "<select id=\"starthour\" name=\"starthour\" onchange=\"test()\">";

							
                                

                                $counter = $hours["openhour"] -1;
                                while ($counter <= $hours["closehour"])
                                    {
                                    $time = date("G",mktime($counter, 0, 0, $month, $day,  $year));
									$displayhour = date("g",mktime($counter, 0, 0, $month, $day,  $year));
                                      echo "<option value=\"$counter\"";
                                      if ($requesthour == $time) { echo " selected";}
                                      echo ">$displayhour</option>";
                                      $counter++;
                                    }

																     echo "</select>

                                    <select id=\"startminute\" name=\"startminute\" onchange=\"test()\">";
                                    $counter = 0;
                                    while ($counter< 60)
                                        {
                                        $test = str_pad($counter, 2, "0", STR_PAD_LEFT);
                                        echo "<option value=\"$test\"";
                                        if ($test == $requestminute) { echo " selected";}
                                        echo ">$test</option>";
                                        $counter++;
                                        }
                                    
                                    
                                    echo " </select> 
                                     <input type=\"hidden\" name=\"endtime\" value=\"$endtime\">
                                    <input type=\"hidden\" name=\"startsecond\" value=\"$startsecond\">";	
						
					
						
						echo "</td></tr>
					
					
						<tr id=\"warning2\"><td class=\"formlabels\">$phrase[243]</td><td>";
						
						
					
						echo "<select id=\"endhour\" name=\"endhour\" onchange=\"test()\">";
							
								$endsecond = date("s",$endtime);
                                $requestminute = date("i",$endtime);
                                $requesthour = date("G",$endtime);
                           

                                $counter = $hours["openhour"] - 1;
                                while ($counter <= $hours["closehour"])
                                    {
                                    $time = date("G",mktime($counter, 0, 0, $month, $day,  $year));
                                    $displayhour = date("g",mktime($counter, 0, 0, $month, $day,  $year));
                                    
                                      echo "<option value=\"$counter\"";
                                      if ($requesthour == $time) { echo " selected";}
                                      echo ">$displayhour</option>";
                                      $counter++;
                                    }



																     echo "</select>

                                    <select id=\"endminute\" name=\"endminute\" onchange=\"test()\">";
                                    $counter = 0;
                                    while ($counter< 60)
                                        {
                                        $test = str_pad($counter, 2, "0", STR_PAD_LEFT);
                                        echo "<option value=\"$test\"";
                                        if ($test == $requestminute) { echo " selected";}
                                        echo ">$test</option>";
                                        $counter++;
                                        }


                                    echo "</select> <input type=\"hidden\" name=\"endsecond\" value=\"$endsecond\">";
						
				
						
						echo "</td></tr>
							<tr><td class=\"formlabels\">$phrase[186]</td><td >$displaydate</td></tr>
						";
						
						 
						
						 if (isset($arrayuseno))
						 			{
									 $rowcount = count($arrayuseno);
									}
									
						if (isset($rowcount) && $rowcount > 1)
									{
									
									echo "<tr><td class=\"formlabels\">$phrase[462] </td><td ><select name=\"pcusage\">";
									echo "<option value=\"$pcusage\">$usename</option>";
									foreach ($arrayuseno as $i => $pcusageno)
										{
										if ($pcusageno <> $pcusage)
											{
											echo "<option value=\"$pcusageno\">$arrayusename[$i]</option>";
											}
										
										
										
										}
									echo "</select></td></tr>";
									
									} 
						  if ($fee <> 0)
						  	{
							echo "<tr><td class=\"formlabels\">$phrase[463] $$fee:</td><td ><select name=\"paid\">";
							if ($paid == 1)
								{
								echo "<option value=\"1\">$phrase[12]</option><option value=\"0\">Not paid</option>";
								}
							else
								{
								echo "<option value=\"0\">$phrase[13]</option><option value=\"1\">Paid</option>";
								}
							
							echo "</select></td></tr>";
							}
						  
						 
						 echo "
						
						      <tr><td class=\"formlabels\">$phrase[716] </td><td ><select name=\"checkedin\">";
						 if ($checkedin == 1 || (time() >= $t - ($interval * 60)))
						   {
						   	echo "
<option value=\"1\">$phrase[12]</option><option value=\"0\">$phrase[13]</option>
						   ";
						   }
						   else {
						   	echo "<option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option>
						   ";
						   }
						  echo"</select> </td></tr>
						  
						  
						  <tr><td class=\"formlabels\"><b>$phrase[152]</b> </td><td ><select name=\"cancelled\">";
						   if ($cancelled == 1)
						   {
						   	echo "
<option value=\"1\">$phrase[12]</option><option value=\"0\">$phrase[13]</option>
						   ";
						   }
						   else {
						   	echo "<option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option>
						   ";
						   }
						  echo" </select></td></tr>";
						  
						  if ($bookinggroup > 0)
	{
			echo "<tr><td class=\"formlabels\" valign=\"top\">$phrase[16]</td><td >
			<select name=\"bookinggroup\">
		<option value=\"0\" checked> $phrase[768]</option>
		<option value=\"$bookinggroup\"> $phrase[767]  ($total)	</option>	
			</select>					   
</td></tr>";
	}
						 
						    echo "
						     <tr><td class=\"formlabels\">$phrase[715] </td><td >";
						   if ($mode == 1) {echo $phrase[712];}
						   if ($mode == 2) {echo $phrase[713];}
						   if ($mode == 3) {echo $phrase[714];}
						   
						   echo "</td></tr>
						  <tr><td class=\"formlabels\">  
						   <input type=\"hidden\" name=\"bid\" value=\"$branchno\">
						      <input type=\"hidden\" name=\"bookingno\" value=\"$bookingno\">";
						    if ($bookinggroup == 0)
							  	{
						       echo "<input type=\"hidden\" name=\"bookinggroup\" value=\"$bookinggroup\">";
							   }
							  if ($fee == 0)
							  	{
						       echo "<input type=\"hidden\" name=\"paid\" value=\"$paid\">";
							   }
							   
							 
							   
						      if (isset($rowcount) && $rowcount < 2)
							  	{
								 echo "<input type=\"hidden\" name=\"pcusage\" value=\"$pcusage\">";
								}
						         echo "<input type=\"hidden\" name=\"m\" value=\"$m\">
						         <input type=\"hidden\" name=\"t\" value=\"$t\">
						           <input type=\"hidden\" name=\"pin\" value=\"$pin\">
						             <input type=\"hidden\" name=\"flexible\" value=\"$flexible\">
						       <input type=\"hidden\" name=\"oldpcno\" value=\"$pcno\">
						
						 </td><td><input type=\"submit\" name=\"updatebooking\" value=\"$phrase[28]\">";
						
					
						 
						 
						 	 $now = time(); 
						 	 
						
						  if ($now < $endtime && $now > $bookingtime)
						  {
						  
						    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"finishbooking\" value=\"$phrase[464]\">";
						  	
						  }
						 
						         
						         
						         
						         
						         
						         
						         
						     if (isset($_REQUEST["s"]))
							  	{
						       echo "
						       <input type=\"hidden\" name=\"s\" value=\"1\">";
							   }
							   
						  
						 echo "</td></tr></table>   </fieldset>";
						 
						 if ($_SESSION['userid'] == 1)	
									{
									 //$modtime = date("g:i a d/m/y",$bookingsmodtime[$i]);
									  	
									  $time = date("g:ia ",$modtime);	
									  $time .= strftime("%x",$modtime);			 
									echo "<p><strong>$phrase[532] $username, $time, $ip</strong> <br><br>
									";
									}
						 
						 echo "$phrase[467]<br>";
						 
					
						
						echo "</p></form><br><br>";
						 	
						
						
						
						
      }
      
      
      
   
      
      
      
elseif (isset($cpc))
       {
	   
	   $sql = "select client, pc_computers.name as cname, pc_branches.name as bname, outoforder, outofordermessage from pc_computers,pc_branches where pc_branches.branchno = pc_computers.branch and pcno = '$cpc'";
	 	$DB->query($sql,"pc.php");
		$row = $DB->get();
		$bname = $row["bname"];
		$cname = $row["cname"];
		$client = $row["client"];
		$outoforder = $row["outoforder"];
		$outofordermessage = $row["outofordermessage"];
		
		
	   echo "<h1 >$modname</h1>
	   <h2>$bname</h2>
	   <h4> $cname</h4>
	   <form action=\"pc.php\" method=\"post\">
		<table style=\"margin:0 auto;\">
		<tr><td class=\"label\"> $phrase[469]</td>
		<td>  <input type=\"radio\" name=\"outoforder\" value=\"1\"
	   ";
	   if ($outoforder == 1)
	   	{
		echo " checked";
		}
	   echo "> $phrase[12] 
	   
	   <input type=\"radio\" name=\"outoforder\" value=\"0\"";
	    if ($outoforder == 0)
	   	{
		echo " checked";
		}
	   
	   echo "> $phrase[13] </td></tr>
	  <tr><td class=\"label\">    $phrase[470]</td>
		<td>  
	
	 <input type=\"text\" name=\"outofordermessage\" value=\"$outofordermessage\" size=\"30\" maxlength=\"30\"> </td></tr>
	   <tr><td class=\"label\">
	   $phrase[913]
	   </td>
		<td>
		<input type=\"radio\" name=\"client\" value=\"1\"
	   ";
	   if ($client == 1)
	   	{
		echo " checked";
		}
	   echo "> $phrase[12] 
	   
	   <input type=\"radio\" name=\"client\" value=\"0\"";
	    if ($client == 0)
	   	{
		echo " checked";
		}
	   
	   echo "> $phrase[13]   </td></tr>
		
		 <tr><td class=\"label\"> </td>
		<td>  
		
		   <input type=\"hidden\" name=\"pcno\" value=\"$cpc\">
		<input type=\"hidden\" name=\"m\" value=\"$m\">
		<input type=\"hidden\" name=\"t\" value=\"$t\">
		<input type=\"hidden\" name=\"bid\" value=\"$bid\">
	    <input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
		
		</td></tr>
		</table>
	   
	   
	 
	   </form>
	   
	   ";
	   }
	
 	
 elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "quick")
   	{
   	 $branchcount = 0;
   $sql = "select  branchno, pc_branches.name as name from pc_branches,pc_computers where pc_branches.m = '$m' and pc_branches.branchno = pc_computers.branch  group by pc_branches.name";
$DB->query($sql,"pc.php");
		
		
while ($row = $DB->get())
      {
	
      $branchno = $row["branchno"];
      $branchcount++;
      $branch[$branchno] = $row["name"];
 
      }
   	 
   	 $typecount =0;
   	 if ($access->thispage < 3 )  {$insert = "where pc_usage.power = 0"; } else { $insert = "";}
   	  $sql = "select * from pc_usage $insert order by name";
		$DB->query($sql,"pc.php");
		
	
while ($row = $DB->get())
      {
      $useno = $row["useno"];
      $type[$useno] = $row["name"];
      $typecount++;
	  }					
   	 
   	 
	?>
		
		<script type="text/javascript">
			var bid;
			var type;
			var t;
			var day
			var month;
			var year;
			var group;
			
		
		
		function quickbook()
		{
	//	alert("hello")
	
		  //var frm = document.forms["searchform"];
		  
		  
			
			var bidmenu = document.getElementById("bid");
			bid = bidmenu.options[bidmenu.selectedIndex].value; 
			
			
			if (document.getElementById("type"))
			{
			type = document.getElementById("type").value; 	
			}
			else
			{
			var typemenu = document.getElementById("bookingtype");
			type = typemenu.options[typemenu.selectedIndex].value; 
			}
			
			
			var groupmenu = document.getElementById("group");
			group = groupmenu.options[groupmenu.selectedIndex].value; 
			
			var daymenu = document.getElementById("day");
			day = daymenu.options[daymenu.selectedIndex].value; 
			
			var monthmenu = document.getElementById("month");
			month = monthmenu.options[monthmenu.selectedIndex].value; 
			
			var yearmenu = document.getElementById("year");
			year = yearmenu.options[yearmenu.selectedIndex].value; 
		

			string = 'pclist.php?m=<?php echo $m ?>&bid=' + bid + '&group=' + group + '&type=' + type + '&day=' + day + '&month=' + month + '&year=' + year;
			//alert(string)
		
		//document.getElementById('iframe').src = string
		updatePage(string,"results");
		return false;
		}
		//if (document.getElementById("type"))
		//	{
		//window.onload=quickbook
		 addEvent(window, 'load', quickbook);
		//	}
		</script>
		
	<?php
		echo "<h1>$modname</h1> 
	 
		
		<a href=\"pc.php?m=$m\">$phrase[410]</a>"; 
	   if (isset($bid) && $bid != 0)
	   {
	   	echo " | <a href=\"pc.php?m=$m&amp;bid=$bid&amp;t=$t\"> ";
	   		foreach($branch as $branchno => $name)
              {	
	 
 if ($bid == $branchno) {echo $branch[$branchno];}

	  }
	   	echo "</a>";
	   }
		
		echo " | <a href=\"pc.php?m=$m&amp;event=getbookings\">$phrase[731]</a>";
	   
	  	if ($PCAUTHENTICATION == "local")
{ echo " | <a href=\"pc.php?m=$m&amp;event=patron\">$phrase[703]</a> ";}
	   
	   echo "<br><br>
	   
	   
	<form  method=\"get\" id=\"searchform\" name=\"searchform\" onsubmit=\"return false\" style=\"width:90%;margin-left:auto;margin-right:auto\">

		<table   cellpadding=\"15\"  cellspacing=\"0\" style=\"margin-left:auto;margin-right:auto\">
		<tr><td valign=\"top\" ><select name=\"bid\" id=\"bid\">
		<option value=\"0\">$phrase[758]</option>
		";

		
	foreach($branch as $branchno => $name)
              {	
 echo "<option value=\"$branchno\"";
 if ($bid == $branchno) {echo " selected";}
 echo ">$name</option>";
	  }
	
	  
	 echo "</select></td><td valign=\"top\" >";
	 
	 if (count($type) > 1) 
	 {
	 echo"<select name=\"type\" id=\"bookingtype\">
	 <option value=\"0\">$phrase[759]</option>";
	 
	 foreach($type as $useno => $name)
              {	
	 	 echo "<option value=\"$useno\"";
	  if ($typecount == 1) {echo " selected";}
	  echo "> $name</option>";
	  }
							
	 echo "</select>";
	 }
	 else { 
	 	$typevalue = array_shift(array_keys($type));
	 	
	 	echo "<input type=\"hidden\" name=\"type\" id=\"type\"  value=\"$typevalue\">";}
	 echo "</td><td valign=\"top\" >";
	 
	 $tday = date("d",$t);
	$tmonth = date("m",$t);
	$tyear = date("Y",$t);
	 
	 
	 
	 
	/*  $t2 = time();
	$day = date("d",$t2);
	$month = date("m",$t2);
	$weekday = date("w",$t2);
	$year = date("Y",$t2); 
	$dates = array();	
	
	$counter = 0;
	$daysahead = 6;
	while ($counter <= $daysahead)
		{
		 
	
			$dates[] = $t2;
			$counter++;	
			
		$day = $day + 1;
	
		$t2 =  mktime(01, 01, 0, $month, $day,  $year);
		$weekday =  date("w",$t2);
		}  
		
	$num = count($dates);
	if ($num == 0) {echo "Branch closed";}
	else
	{
	$counter = 1;
	foreach($dates as $index => $timestamp)
						

              {	
			$display = date("l j M Y",$timestamp);
			$day = date("d",$timestamp);
			$month = date("m",$timestamp);
			$year = date("Y",$timestamp);
			echo "<input type=\"radio\" name=\"t\" value=\"$timestamp\"";
			
			//if ($day == $tday && $month == $tmonth && $year == $tyear) {echo " checked";}
			if ($counter ==1) {echo " checked";}
			
			echo "> $display<br>";
			$counter++;
			}*/
	
	
	//$day = date("j");
	//$month = date("n");
	//$weekday = date("w",$t2);
	//$year = date("Y");
	$nextyear = $tyear + 1; 
	echo "
	<select name=\"day\" id=\"day\">";
	$counter = 1;
	while ($counter < 32)
	{
	echo "<option value=\"$counter\"";
	if ($counter == $tday) {echo " selected";}
	echo ">$counter</option>";
	$counter++;	
	}
	
	
	
	
	echo "</select>
	<select name=\"month\" id=\"month\">";
	$counter = 1;
		while ($counter < 13)
	{
	 $monthname = strftime("%b",mktime(01,01,01,$counter,01,$tyear));
	echo "<option value=\"$counter\"";
	if ($counter == $tmonth) {echo " selected";}
	echo ">$monthname</option>";
	$counter++;	
	}
	echo "</select>
	<select name=\"year\" id=\"year\">
	<option value=\"$tyear\">$tyear</option>
	<option value=\"$nextyear\">$nextyear</option>
	</select>
	
	";
	
	
	
		
	   echo "</td><td ><select name=\"group\" id=\"group\">
	   <option value=\"0\" selected>$phrase[746]</option>
	    <option value=\"1\"> $phrase[747]</option>
	   </select>
	   </td><td>   <input type=\"hidden\" name=\"m\" value=\"$m\">
	     <input type=\"hidden\" name=\"event\" value=\"check\">
	   
	   <button onclick=\"quickbook()\">$phrase[750]</button></td></tr></table>
	
	 
	   </form> <br><br>
	   <div id=\"results\"></div>
	
	 

		

";
	}
	



elseif (isset($bid))
       {

    //display bookings table
    include ('pcdisplay.php');
 

   }
elseif (isset($_REQUEST["bookingstats"]) && ($_REQUEST["bookingstats"] == "yes"))
       {
	   if ((!isset($month)) && (!isset($year)))
	   {
	   echo "<h1>$modname</h1>	 
		
		<a href=\"pc.php?m=$m\">$phrase[410]</a>  | <a href=\"pc.php?m=$m&amp;bid=0&amp;t=$t&amp;event=quick\">$phrase[710]</a>
	   | <a href=\"pc.php?m=$m&amp;event=getbookings\">$phrase[731]</a>";
	   
		if ($PCAUTHENTICATION == "local")
{ echo " | <a href=\"pc.php?m=$m&amp;event=patron\">$phrase[703]</a> ";}
	   
	   echo " | <a href=\"pc.php?m=$m&amp;bookingstats=yes\">$phrase[428]</a><h2>$phrase[471]</h2>";
	   //display months
		//$sql = "select count(*) as number, month(from_unixtime(bookingtime,'%Y%m%d')) as month, year(from_unixtime(bookingtime,'%Y%m%d')) as year from pc_bookings where cancelled = \"0\" group by year, month";
		
		 if ($DB->type == "mysql")
		{  
		$sql = "select count(*) as number, month(from_unixtime(bookingtime,'%Y%m%d')) as month, year(from_unixtime(bookingtime,'%Y%m%d')) as year from pc_bookings, pc_usage where pc_bookings.pcusage = pc_usage.useno and pc_usage.stats = \"1\" and cancelled = \"0\" group by year desc, month desc";
		}
			else
		{  
		$sql = "select count(*) as number, strftime('%m',datetime ( bookingtime , 'unixepoch','localtime' )) as month, strftime('%Y',datetime ( bookingtime , 'unixepoch','localtime' )) as year from pc_bookings, pc_usage where pc_bookings.pcusage = pc_usage.useno and pc_usage.stats = \"1\" and cancelled = \"0\" group by year, month order by year, month desc";
		
	
		}
		

		echo "<div class=\"swouter\"><ul class=\"listing swinner\" >";
		
		 $DB->query($sql,"pc.php");
		$num = $DB->countrows();
		while ($row = $DB->get()) 
						{
						$month = $row["month"];
						$year = $row["year"];
						//$display = date("F Y", mktime(0,0,0,$month,1,$year));
						$display = strftime("%B %Y", mktime(0,0,0,$month,1,$year));
						echo "<li><a href=\"pc.php?m=$m&amp;bookingstats=yes&amp;month=$month&amp;year=$year\">$display</a></li>";
						}
						
		echo "</ul></div>";
		if ($num == 0) {echo "$phrase[472]";}
		}
		else
		{
		
		
		
		echo "<h1>$modname</h1>

	<a href=\"pc.php?m=$m\">$phrase[410]</a>  | <a href=\"pc.php?m=$m&amp;bid=0&amp;t=$t&amp;event=quick\">$phrase[710]</a>  | <a href=\"pc.php?m=$m&amp;event=getbookings\">$phrase[731]</a>";
		
	if ($PCAUTHENTICATION == "local")
{ echo " | <a href=\"pc.php?m=$m&amp;event=patron\">$phrase[703]</a> ";}


echo " | <a href=\"pc.php?m=$m&amp;bookingstats=yes\">$phrase[428]</a>";
		

	
		
		$html = $pcbookings->pc_stats($month,$year,$DB,$phrase);
		
		echo $html;
		}

   }
     	
  elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "patron")
       {
     
       	
     echo "
	 <h1>$modname</h1>
	  <a href=\"pc.php?m=$m\">$phrase[410]</a>  | <a href=\"pc.php?m=$m&amp;bid=0&amp;t=$t&amp;event=quick\">$phrase[710]</a>
	   | <a href=\"pc.php?m=$m&amp;event=getbookings\">$phrase[731]</a>";
	   
		if ($PCAUTHENTICATION == "local")
{ echo " | <a href=\"pc.php?m=$m&amp;event=patron\">$phrase[703]</a> ";}
	   
	   echo " | <a href=\"pc.php?m=$m&amp;bookingstats=yes\">$phrase[428]</a><br>";

     

     if (isset($_REQUEST["barcode"]))
     {
     	//$barcode = trim($_REQUEST["barcode"]);
     	$barcode =  $DB->escape(str_replace(" ", "", $_REQUEST["barcode"]));
     $sql = "select * from pc_users where barcode = '$barcode'";	
     $DB->query($sql,"pcadmin.php");
	$num = $DB->countrows();
	
	if ($num == 1)
	{
		
		
		$barcode =  $DB->escape(str_replace(" ", "", $_REQUEST["barcode"]));
		$urlbarcode = urlencode($barcode);
		
		$row = $DB->get();
      
	
      $change = $row["change"];
		
		
		echo "
		 
			
			<div class=\"swouter\"><form action=\"pc.php\" method=\"post\" class=\"swinner\" onsubmit=\"return check()\"> 
      	<fieldset >
						   <legend>$phrase[22]</legend>
						   <table  style=\"margin:0 auto;text-align:left\" cellpadding=\"5\">
						  <tr><td class=\"formlabels\"><b>$phrase[460]</b></td><td><b>$_REQUEST[barcode]</b></td></tr>
						    <tr><td class=\"formlabels\"><b>$phrase[32]</b></td><td> <input type=\"password\" name=\"password\" id=\"password\"></td></tr>
						    
							 <tr><td class=\"formlabels\"><b>$phrase[17]</b></td><td> <input type=\"password\" name=\"password2\" id=\"password2\"></td></tr>
						    <tr><td class=\"formlabels\"><b>$phrase[739]</b></td><td><input type=\"radio\" name=\"change\" value=\"0\"";
							if ($change == 0) {echo " checked";}
							echo "> $phrase[13] <input type=\"radio\" name=\"change\" value=\"1\"";
								if ($change == 1) {echo " checked";}
							echo "> $phrase[12]</td></tr>
							<tr><td>	</td><td> 
						       <input type=\"hidden\" name=\"m\" value=\"$m\">

						    
						         <input type=\"hidden\" name=\"barcode\" value=\"$_REQUEST[barcode]\">
					     <input type=\"submit\" name=\"updateuser\" value=\"$phrase[22]\"><br><br><a href=\"pc.php?m=$m&amp;barcode=$urlbarcode&amp;event=patrons&amp;deleteuser=yes\">$phrase[709]</a></td></tr>
						   </table>
						   </fieldset>
						   </form></div>
						   
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
						<form action=\"pc.php\" method=\"post\" onsubmit=\"return check()\" style=\"width:60%;margin-left:auto;margin-right:auto\">
      	<fieldset >
						   <legend>$phrase[704]</legend>
						   <table style=\"margin:0 auto;text-align:left\" cellpadding=\"5\">
						   <tr><td class=\"formlabels\"><b>$phrase[460]</b></td><td><input type=\"text\" name=\"barcode\" value=\"$_REQUEST[barcode]\"></td></tr>
						    <tr><td class=\"formlabels\"><b>$phrase[4]</b></td><td> <input type=\"password\" name=\"password\" id=\"password\"></td></tr>
						    <tr><td class=\"formlabels\"><b>$phrase[17]</b></td><td>  <input type=\"password\" name=\"password2\" id=\"password2\"></td></tr>
						    <tr><td class=\"formlabels\"><b>$phrase[739]</b></td><td><input type=\"radio\" name=\"change\" value=\"0\" checked> $phrase[13] 
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
			<form action=\"pc.php\" method=\"post\" style=\"width:80%;margin: 3em auto\" onsubmit=\"return check()\">
			<p>
      <b>$phrase[460]</b>
						   <input type=\"text\" name=\"barcode\" id=\"barcode\">
						   <input type=\"hidden\" name=\"m\" value=\"$m\">
						    <input type=\"hidden\" name=\"event\" value=\"patron\">
						    <input type=\"submit\" name=\"search\" value=\"$phrase[282]\"></p>
						 
						   </form>";
						 
      	
      	
      }
       }
       
   
   
   
elseif(isset($WARNING))
{
echo "$warning";	
//warning($WARNING);
}
  else

    {
    //list branches
	
    echo "<h1>$modname</h1>
 
    
    ";

    //'$phrase[734]'
    if (isset($_REQUEST["day"])) {$day = $_REQUEST["day"];} else {$day = date("j");}
    if (isset($_REQUEST["month"])) {$month = $_REQUEST["month"];} else {$month = date("n");}
    if (isset($_REQUEST["year"])) {$year = $_REQUEST["year"];} else {$year = date("Y");}
    
if (isset($_REQUEST["t"])) {$t = $_REQUEST["t"];} else {
    
   
 
    $t = mktime(0,0,0,$month,$day,$year);
    
}
echo "
	<a href=\"pc.php?m=$m\">$phrase[410]</a>  | <a href=\"pc.php?m=$m&amp;bid=0&amp;t=$t&amp;event=quick\">$phrase[710]</a>  | <a href=\"pc.php?m=$m&amp;event=getbookings\">$phrase[731]</a>";

		if ($PCAUTHENTICATION == "local")
{ echo " | <a href=\"pc.php?m=$m&amp;event=patron\">$phrase[703]</a> ";}


echo " | <a href=\"pc.php?m=$m&amp;bookingstats=yes\">$phrase[428]</a>
<br><br>

<form action=\"pc.php\" methos=\"post\">";

$_year = date("Y") - 10;
$yeartext = "<select name=\"year\">";
while ($_year < date("Y") + 10)
{
   $yeartext .= "<option value=\"$_year\"";
   if ($year == $_year) {$yeartext .= " selected";}
   $yeartext .= ">$_year</option>
"; 
    $_year++;
}
$yeartext .= "</select>";

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
                
$monthtext = "<select name=\"month\">";
$_month = 1;
while ($_month < 13)
{
   $monthtext .= "<option value=\"$_month\"";
   if ($month == $_month) {$monthtext .= " selected";}
   $monthtext .= ">$months[$_month]</option>
"; 
    $_month++;
}
$monthtext .= "</select>";

$daytext = "<select name=\"day\">";
$_day = 1;
while ($_day < 32)
{
   $daytext .= "<option value=\"$_day\"";
       if ($day == $_day) {$daytext .= " selected";}
       $daytext .= ">$_day</option>
"; 
    $_day++;
}
$daytext .= "</select>";
if (!isset($DATEFORMAT)) {$DATEFORMAT = "%d-%m-%Y";}
if ($DATEFORMAT == "%d-%m-%Y")
             {
              echo "$daytext / $monthtext / $yeartext";
             }
             else
             { echo "$monthtext / $daytext / $yeartext";}
echo "<input type=\"hidden\" name=\"m\" value=\"$m\"><input type=\"submit\" value=\"$phrase[1082]\"></form>

<table  class=\"center\">
<tr><td><h2>locations</h2></td></tr>";
//extract($_SERVER);
$sql = "select count(*), branchno, pc_branches.name as name from pc_branches,pc_computers where pc_branches.branchno = pc_computers.branch and pc_branches.m = '$m' group by pc_branches.name";
 $DB->query($sql,"pc.php");
while ($row = $DB->get())
      {
	
      $branchno = $row["branchno"];
      $name = formattext($row["name"]);
      echo "<tr><td><a href=\"pc.php?m=$m&amp;bid=$branchno&amp;t=$t\">$name</a></td></tr>";
	  

	 
      }
	  echo "
	  
	  </table>";
      
      
    }

		
		
		
		
		
		
		
		
		
	}

   echo "</div>";
include ("../includes/footer.php");





?>

