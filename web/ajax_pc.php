<?php


 if(!include("../includes/initiliaze_page.php"))
 {
 	include("includes/initiliaze_page.php");
 }

 
 //print_r($_REQUEST);
 
 
 
 if (isset($_REQUEST["start"])) {$start = $DB->escape($_REQUEST["start"]);} else {$start = "";} 
 
 
  if (isset($_REQUEST["duration"])) {$duration = $DB->escape($_REQUEST["duration"]);} else {$duration = "";} 
 
 if (isset($_REQUEST["pcno"])) {$pcno = $DB->escape($_REQUEST["pcno"]);} else {$pcno = "";} 
 
  if (isset($_REQUEST["bid"])) {$bid = $DB->escape($_REQUEST["bid"]);} else {$bid = "";}  
  
   if (isset($_REQUEST["bookingtype"])) {$bookingtype = $DB->escape($_REQUEST["bookingtype"]);} else {$bookingtype = "";} 
   
   if (isset($_REQUEST["day"])) {$day = $DB->escape($_REQUEST["day"]);} else {$day = "";}  
   
   
     if (isset($_SESSION["defaultLocation"])) {$fullSuffix  = "?default=" . $_SESSION["defaultLocation"];
          $partSuffix  = "&default=" . $_SESSION["defaultLocation"];
          
               
          
          } else {$fullSuffix  = ""; $partSuffix  = "";}
 
  
 
 if (isset($_REQUEST["get"]) && $_REQUEST["get"] == "types")
 {
  
     
 $sql = "select  name  from pc_branches where branchno = '$bid'";

 $DB->query($sql,"ajax_pc.php");
$row = $DB->get();
    $name = $row["name"];     
     
     echo "<h3 style=\"text-align:center;margin-top:0\"> $name </h3>
    
<h2 style=\"text-align:center;color:grey\"> $phrase[752]</h2><p style=\"width:30%;margin:0 auto;text-align:left\">";
     

 
 
 	 	 $sql = "select pc_usage.useno,pc_usage.name from pc_usage,pc_bridge, pc_computers where 
 pc_computers.branch = '$bid' 
 and pc_computers.pcno = pc_bridge.pcnum
 and pc_bridge.useno = pc_usage.useno
 and web = 1 group by pc_usage.name order by pc_usage.name";
	 	
		$DB->query($sql,"ajax_types.php");
		$typecounter = 0;
	
while ($row = $DB->get())
      {

     
      $useno = $row["useno"];
      $name = $row["name"];
     echo "
<button onclick=\"getDays('$bid','$useno','')\" style=\"margin:0.2em\">$name</button><br>";
	  }
 echo "

     
</p>";
 }
 
 
 
 
  if (isset($_REQUEST["get"]) && $_REQUEST["get"] == "PCs")
 {
  
     
 $sql = "select  name  from pc_branches where branchno = '$bid'";

 $DB->query($sql,"ajax_pc.php");
$row = $DB->get();
    $name = $row["name"];     
     
     echo "<h3 style=\"text-align:center;margin-top:0\"> $name </h3>
    
<h2 style=\"text-align:center;color:grey\">$phrase[754]</h2><p style=\"width:30%;margin:0 auto;text-align:left\">";
     

            $images = array();
           $sql = "select image_id,pcno from images, pc_computers where  branch = \"$bid\"  and pc_computers.pcno = images.page and modtype = 't'";
          
//put usages in array
$DB->query($sql,"pcadmin.php");
 while ($row = $DB->get())
             {
                $_image = $row["image_id"];
                $_pcno = $row["pcno"];
                $images[$_pcno] = $_image;
                
                
                }
 
 	 	   
 $sql = "select  pc_computers.name,pc_computers.pcno,default_usage  from pc_computers,pc_usage 
          where branch = '$bid' and pc_usage.useno = pc_computers.default_usage and web = '1'";

	 //	echo $sql;
		$DB->query($sql,"ajax_types.php");
		$typecounter = 0;
	
while ($row = $DB->get())
      {

     
      $useno = $row["default_usage"];
      $name = $row["name"];
      $pcno = $row["pcno"];
      
       if (array_key_exists($pcno, $images))
      {
          echo "<img src=\"../web/calimage.php?module=pc&image_id=$images[$pcno]\" 
           style=\"width:130px;\" onclick=\"getDays('$bid','$useno','$pcno')\" title=\"Click to book $name\"><br><br>";
      }
      else
      
      {
     echo "
<button onclick=\"getDays('$bid','$useno','$pcno')\" style=\"margin:0.2em\">$name</button><br>";
      }
          
      }
 echo "

     
</p>";
 }
 
 
 
 
 if (isset($_REQUEST["get"]) && $_REQUEST["get"] == "days")
 {
     
     
     $sql = "select  name  from pc_branches where branchno = '$bid'";

 $DB->query($sql,"ajax_pc.php");
$row = $DB->get();
    $name = $row["name"];     
    

    if (isset($BOOKBY) && $BOOKBY == "PC")   
{
  
 $sql = "select name from pc_computers where pcno = '$pcno'";  
 //echo $sql;
  $DB->query($sql,"ajax_pc.php");
		$row = $DB->get();
		$text = $row["name"];
}
else
{
 $sql = "select * from pc_usage where useno = '$bookingtype'";
 //echo $sql;
     $DB->query($sql,"ajax_pc.php");
    // echo $sql ;
     $row = $DB->get();
    $text = $row["name"];   
}
    
      
     echo "<h3 style=\"text-align:center;margin-top:0\"> $name <br>
    $text</h3>
    
 <h2 style=\"text-align:center;color:grey\">$phrase[232]</h2>";
     
     
     
     
     
     //get list of days library is closed
     $closeddays = array();
     $sql = "select day from branch_openinghours where branchid = '$bid' and open = '0'";
     $DB->query($sql,"ajax_types.php");
		
	
while ($row = $DB->get())
      {
      $closeddays[] = $row["day"];
      }
     
    //get list of public holidays
      $holidays = array();
      $sql = "select holiday from holidays";
        $DB->query($sql,"ajax_types.php");
		
	
while ($row = $DB->get())
      {
      $holidays[] = $row["holiday"];
      }
      

//get list of branch closures
    $date_blocked = array(); 
     $date_finish = array();  
     
     				if ($DB->type == "mysql")
		{
		$sql = "select UNIX_TIMESTAMP(date_blocked) as date_blocked ,UNIX_TIMESTAMP(date_finish) as date_finish from pc_closures where  pc_closures.branch = '$bid'";
		}
			else
		{
		$sql = "select  strftime('%s',date_blocked) as date_blocked ,strftime('%s',date_finish) as date_finish from pc_closures, where pc_closures.branch = '$bid'";
		}
          $DB->query($sql,"ajax_types.php");
		
	
while ($row = $DB->get())
      {
      $date_blocked[] = $row["date_blocked"];
      $date_finish[] = $row["date_finish"];
      }
      
      
  $t = time();
	$d = date("j",$t);
	$month = date("n",$t);
	$weekday = date("w",$t);
	$year = date("Y",$t); 
	$dates = array();	
	
	$counter = 0;
	
        // if bookings limited to current week check to see if daysahead needs to be shortened
        if (isset($CURRENTWEEKONLY) && $CURRENTWEEKONLY == 1)
        {
        if (isset($CALENDAR_START) && $CALENDAR_START =="MON")
        {$nextweek = strtotime("next Monday");} else {$nextweek = strtotime("next Sunday");}
        
        $today = strtotime("today");
        
        
        $gap = (($nextweek - $today) /24/60/60) - 1;
        
        
           if ($DAYSAHEAD > $gap) {$DAYSAHEAD = $gap;}
        }
        
     
        
        
	while ($counter <= $DAYSAHEAD)
		{
		 $status = "open";
                 $weekday =  date("w",$t);
                 $date = date("Y-m-d",$t); 
                 
                 foreach($closeddays as $index => $_d)
                 {
                     if ($_d == $weekday)	{ $status = "closed";}			
                 }
              
                  foreach($holidays as $index => $h)
                 {
                     if ($h == $date)	{ $status = "closed";}			
                 }
                 
                   foreach($date_blocked as $index => $start)
                 {
                     if ($start <= $t && $date_finish[$index] >= $t)	{ $status = "closed";}			
                 }
                 
                 
                 
                 if ($status == "open") {$dates[] = $t;}
                 
			
		$counter++;	
			
		$d = $d + 1;
	
		$t =  mktime(01, 01, 0, $month, $d,  $year);
		
		}  
		
	$num = count($dates);
	$count = 0;
       echo "<p style=\"width:30%;margin:0 auto;text-align:left\">";
	foreach($dates as $index => $timestamp)
						

              {	
			$display = date("l j M Y",$timestamp);
			echo "
                        <button onclick=\"getTimes('$bid','$bookingtype','$timestamp','$pcno')\" style=\"margin:0.2em\">$display</button><br>";;
                        
                        
                       
			}
	
                        
		echo "
                        </p>
                       ";	
     
     
     
 }
 
 
  if (isset($_REQUEST["get"]) && $_REQUEST["get"] == "times")
 
 {
     
    
	//print_r($_REQUEST);
	
	
	if (!isset($day)) {$day == "01";}
	if (!isset($bookingtype)) {$bookingtype == 0;}
	if (!isset($bid)) {$bid == 0;}

	 	

	      $sql = "select  name  from pc_branches where branchno = '$bid'";

 $DB->query($sql,"ajax_pc.php");
$row = $DB->get();
    $name = $row["name"];     
    
    
        if (isset($BOOKBY) && $BOOKBY == "PC")   
{
   
 $sql = "select name from pc_computers where pcno = '$pcno'";  
 //echo $sql;
  $DB->query($sql,"ajax_pc.php");
		$row = $DB->get();
		$text = $row["name"];
}
else
{
 $sql = "select * from pc_usage where useno = '$bookingtype'";
 //echo $sql;
     $DB->query($sql,"ajax_pc.php");
    // echo $sql ;
     $row = $DB->get();
    $text = $row["name"];   
}
    
    
   $display = date("l j M Y",$day);
      echo "<h3 style=\"text-align:center;margin-top:0\"> $name <br>
    $text<br>$display</h3>";
	 

	
	
	if ($day == 1) { $ERROR = "<h2>Day not selected</h2>";}
	 if ($bookingtype == 0) { $ERROR = "<h2>$phrase[752]</h2>";}
	  if ($bid == 0) { $ERROR = "<h2>$phrase[751]</h2>";}
	  

	if (isset($ERROR)) { echo $ERROR;}
	 else {
	 
	 	
	 	$sd = date("d",$day);
$sm = date("m",$day);
$sy = date("Y",$day);
$startday =  mktime(01, 01, 01, $sm, $sd,  $sy);

$ed = date("d",$day);
$em = date("m",$day);
$ey = date("Y",$day);
$endday =  mktime(23, 59, 59, $em, $ed,  $ey);


$datestring = $sy . "-" . $sm . "-" .$sd;
	 	
	 	//check public holiday
$sql = "select * from holidays where holiday = '$datestring'";
$DB->query($sql,"pcdisplay.php");

$row = $DB->get();
$holiday = $row["name"];
 
 include("../classes/PcBookings.php");
$pcbookings = new PcBookings($DB);
  
$closure = $pcbookings->Closure($bid,$startday);



if (isset($pcno) && $pcno != "") {$insert = " and pc_computers.pcno = '$pcno' ";} else {$insert = "";}

	 
	$sql = "select pcno, flexible from pc_computers, pc_bridge where pc_computers.branch = '$bid' $insert and pc_computers.outoforder = 0 and pc_bridge.useno = '$bookingtype' and pc_computers.pcno = pc_bridge.pcnum order by pcno desc";
	
       // echo $sql;
        
        $pc_array_no = array();
        $pc_array_flexible = array();
        
	$DB->query($sql,"pcbookings.php");
	$numpcs = $DB->countrows();
	if ($numpcs == 0) { $ERROR = "$phrase[748]";}
	while ($row = $DB->get()) 
		{
		
		$pc_array_no[] = $row["pcno"];
		$pc_array_flexible[] = $row["flexible"];
		
		}
	
	//check the default booking length for this booking type.
		$sql = "select * from pc_usage where useno = '$bookingtype'";
	
		$DB->query($sql,"pcbookings.php");
		$row = $DB->get();
		$default = $row["defaulttime"] * 60;
	
	
	//find out booking interval
	//	$sql = "select * from pc_branches where branchno = '$bid'";
	//	$DB->query($sql,"pcweb.php");
	//	$row = $DB->get();
	//	$interval = $row["pc_booking_interval"] * 60;
	$increment = $BOOKINGINTERVAL * 60;
		
		
		//if ($default > $interval) {$increment = $interval;} else{$increment = $default;}


		//$increment = 1800;
	
//find out maximum booking time allowed
$sql = "select  * from pc_usage where pc_usage.useno = '$bookingtype'";	

$DB->query($sql,"pcweb.php");
$row = $DB->get();
//$interval = $row["mintime"] * 60;	
$maxtime = $row["maxtime"];
$max = $maxtime * 60;
$default = $row["defaulttime"] * 60;



//put bookings in array
$sql = "select * from pc_bookings where branchid = '$bid' and cancelled = '0' and bookingtime > '$startday' and bookingtime < '$endday'"; 

$DB->query($sql,"pcweb.php");
while ($row = $DB->get())

	{
	$booking_pc[] = $row["pcno"];
	$booking_st[] = $row["bookingtime"];
	$booking_et[] = $row["endtime"];	
	}	
	

$hours =   $pcbookings->OpeningHours($startday,0,$bid,$pc_array_no[0],$phrase);

//print_r($hours);

if ($closure['closed'] == "yes")
  	{
	echo "<h2 style=\"text-align:center\">$phrase[528] </h2>";
	}
elseif (isset($holiday))
   {
   echo "<h2 style=\"text-align:center\">$holiday holiday</h2>";
   }


elseif ($hours['open'] == 0) 
	{
	echo "<h2 style=\"text-align:center\">$phrase[528]</h2>";
	}

elseif(!isset($pc_array_no))
	{ 
				
	echo "<p style=\"text-align:center\">$phrase[808]</p>";
	}

elseif ($hours['open'] == 1)


 {
 	//list available times
 

		//print_r($hours);
	
	$now = time();
	$t = $hours['opening'];
echo "<h2 style=\"text-align:center;color:grey\">$phrase[743]</b></h2><ul id=\"times\"  style=\"width:30%;margin:0 auto;line-height:1.6em\">";


		$closingtime = $hours['closing'];

			$sessioncount = 0;
			
			while (($t) < $closingtime )
			{
			
				if  ($t > $now )
			{
	 	
				//echo "t is $t <br>";
			$booked = False;
				
			//for each computer calculate minutes available
	 		if(isset($pc_array_no))
	 		{
 			foreach($pc_array_no as $index => $pc)
                     
				 {
 					$minutes = 0;
 					$booked = False;
 					
 					//echo "PC $pc <br>";
 					//	echo "zzzzz $hours[closing]  ZZZ";
				 	
 					while ($minutes <= $maxtime && $booked == False)
 					{
 					$end = $t + ($minutes * 60);	
 					//echo "$minutes <br>";
 					
 				
 						
 					if ($end > $closingtime )
 						{
 						$times[$pc] = $minutes - 1;	
 						//echo "closed <br>";	
 						break;
 						
 						}
 						
 						
 						
 						if (isset($booking_pc))
 						{
						foreach($booking_pc as $i => $pcno)
							{
								
						
							//if ($pcno == $pc )
							//{
								//echo "$t $booking_st[$i] $booking_et[$i]<br>";
							//}
							
						
						if ($pcno == $pc && ($end >= $booking_st[$i] && $end < $booking_et[$i]) )
						
							
					
							{
						
							$times[$pc] = $minutes;	
							//echo " booked - $pcno $minutes -- test is $t<br> ";	
							$booked = True;
 							//break ;
 						
							}
							
							
							
							}	
 						}
 						
 						if ($booked == True) {break;}
 						
 					
 						//echo "<br>";
 						
 						if ($minutes == $maxtime)
 							{
 							$times[$pc] = $minutes;	
 							//echo "maximum reached";	
 							break;
 							}
 						
 						
 						
 					$minutes++;	
 					}
 					
 						
 					
				 }	
				  //echo "<br>";
				// print_r($times);
				// echo " ti $t<br>";
				 
				 $duration = 0 ;
				 if (isset($times))
 						{
 						
						foreach($times as $i => $mins)
							{
							if ($mins > $duration) 
								{
								//echo "i is $i mins is $mins available is $available<br>";
								$duration = $mins;
								$pcno = $i;
								
								}	
								
							}
 						}
				 
 						
 						if ($duration > 0)
 						{
 						$end = $t + ($duration * 60);
 						
 						  $displaystart =  date("g:ia", $t );
			  $displayend = date("g:ia", $end);
			  if (strlen($displayend) == 6) { $displayend = "&nbsp;&nbsp;".$displayend;} 
		
			 $list =  "<span class=\"timeButton\" onclick=\"bookingForm($bookingtype,$t,$duration,$pcno,$bid)\">$displaystart</span>";
				if (strlen($displaystart) == 6) { $list = "&nbsp;&nbsp;" . $list;}
				$list = "<li>" . $list .  "<span class=\"times\" > $duration minutes available</span></li>";
		
			 echo $list;
                         
                         $sessioncount++;
 						}
 					
 					
		
 				
			}
 				
 				
			}
			
		$t = $t + $increment;
		
			 }
		
			}


	
	echo "</ul><br><br>";
	

}
  	if ($sessioncount == 0) {echo "<p style=\"text-align:center\">No sessions available on this date. <br><br><a href=\"book.php\">Start again</a></p>"; } 
     
     
 }
 
 
 
 
 
 
  if (isset($_REQUEST["get"]) && $_REQUEST["get"] == "bookingForm")
{
	
	//print_r($_REQUEST);
	
        	 	$sql = "select name from pc_branches where branchno = '$bid'";
		$DB->query($sql,"pcweb.php");
		$row = $DB->get();
		$branchname = $row["name"];
	
	
		
        $displaydate = $date = strftime("%x",$start);
         $displaystart = date("g:i a",$start);
         
        // $displayend = date("g:i a",$end);
         echo "
               

         

         <table style=\"margin:0 auto;text-align:left;border-style:none\"
cellspacing=\"0\" cellpadding=\"5\">
<tr><td align=\"right\"><b>$phrase[185]</b></td><td><b>$displaystart</b></td></tr>
<tr><td align=\"right\"><b>$phrase[745]</b></td><td><select name=\"duration\"  id=\"duration\">";



$mod = $duration % $BOOKINGINTERVAL;
if ($mod != 0) 

	{ 
		echo "<option value=\"$duration\">$duration</option>";
		$duration = $duration - $mod;
	
	}
 else 
 {
 	//echo "<option value=\"duration\">$duration</option>";
 	
 }
	 
	
	while ($duration >= 0 + $BOOKINGINTERVAL)
	{
	
	echo "<option value=\"$duration\">$duration</option>";
	$duration = $duration - $BOOKINGINTERVAL;	
	}
	

echo "</select> minutes</td></tr>";

if (isset($BOOKBY) && $BOOKBY == "PC")   
{
   
 $sql = "select name from pc_computers where pcno = '$pcno'";   
  $DB->query($sql,"pcweb.php");
		$row = $DB->get();
		$pcname = $row["name"];  
    
echo "<tr><td align=\"right\"><b>Game</b></td><td><b>$pcname</b></td></tr>";
}
echo "


<tr><td align=\"right\"><b>$phrase[186]</b></td><td><b>$displaydate</b></td></tr>
<tr><td align=\"right\"><b>$phrase[121]</b></td><td><b>$branchname</b></td></tr>
  <tr><td align=\"right\"><b>$phrase[141]</b></td><td>
         <input type=\"text\" name=\"name\" id=\"name\">
         </td></tr>
         <tr><td align=\"right\"><b>$phrase[810]</b></td><td>
         <input type=\"text\" name=\"barcode\" id=\"barcode\">
         </td></tr>
        ";
         
        
                        if ($PCAUTHENTICATION != "autoaccept")
                        {
        echo "

        <tr><td align=\"right\"><b>$phrase[811]</b></td><td>
         <input type=\"password\" name=\"password\" id=\"password\">
         </td></tr>";
                        }
         
         echo "<tr><td></td><td align=\"left\">";
         
          if ($PCAUTHENTICATION == "autoaccept")
                        {
                         echo "
      
         <input type=\"hidden\" name=\"password\" value=\"\" id=\"password\">";	
                        
                        }
                        
                        //<input type=\"submit\" name=\"submit\" value=\"\" onclick=\"booking();return(false)\">
                        
         echo "<button onclick=\"booking()\">$phrase[807]</button>
        <input type=\"hidden\" name=\"type\" name=\"bookingtype\" id=\"bookingtype\" value=\"$bookingtype\">
        <input type=\"hidden\" name=\"pcno\" id=\"pcno\" value=\"$pcno\">
        <input type=\"hidden\" name=\"start\" value=\"$start\" id=\"start\">

         <input type=\"hidden\" name=\"bid\" value=\"$bid\" id=\"bid\">
                <input type=\"hidden\" name=\"event\" value=\"booking\">
         </td></tr></table>
         

                 
                        
                        
                        ";
        

}
 
 if (isset($_REQUEST["get"]) && $_REQUEST["get"] == "booking")
{

		include("../classes/PcBookings.php");
$pcbookings = new PcBookings($DB);

	//print_r($_REQUEST);	
		$name = $DB->escape($_REQUEST["name"]);	
		
		$end = $start + ($duration * 60);
		
		$now = time();
		
		
echo "<h3>$phrase[240] </h3>";

	 //print_r($_REQUEST);
	 
	 
	 //get branch info
	 	$sql = "select pc_branches.name as name, pc_branches.branchno as branchno from pc_branches, pc_computers where pc_branches.branchno = pc_computers.branch and pc_computers.pcno = '$pcno'";
	 	
	
	 	
		$DB->query($sql,"pcweb.php");
		$row = $DB->get();
		$DB->query($sql,"pcweb.php");
		$branchname = $row["name"];
		$bid = $row["branchno"];
	
		$barcode = $_REQUEST["barcode"];
		
	   if (trim($_REQUEST["barcode"]) == "")
	 {
	 	$ERROR = "You cannot login unless you enter a login id and password";
	 
	 }
	
	 	 
	 
	 elseif ($PCAUTHENTICATION != "autoaccept")
               {
               	
      if (trim($_REQUEST["password"]) == "")
	 {
	 	$ERROR = "No password entered";
	 
	 }
         
      elseif($PCAUTHENTICATION == "soap")
         {
         if (!file_exists("../includes/soap.php"))
         {
              echo "Soap file missing.";
             $ERROR = "Soap file missing.";
         }
         else {
             $password = substr(trim($_REQUEST["password"]),0,100);
         include("../includes/soap.php");
         
         if ($result["auth"] == "no")
                    {
		$ERROR = $result["failure"];
                    }
         
                }
         }
         
	 else 
	 {
	$password = substr(trim($_REQUEST["password"]),0,100);	
	
		
		
	include("../classes/AuthenticatePatron.php");
	


	$CHECK = new AuthenticatePatron($password,$barcode,$PREFERENCES,$DB,"pc");
	
	if ($CHECK->auth == "no")
	{
		$ERROR = $CHECK->failure;

	
	
	}
	 }
               }
            

		$cardnumber = $DB->escape(str_replace(" ", "", substr($_REQUEST["barcode"],0,100)));				
			
			

		//get usage assigned to computer
$sql = "select pc_usage.useno as useno , timelimit from pc_usage, pc_bridge where pc_bridge.pcnum = '$pcno' and pc_bridge.useno = pc_usage.useno
and web = 1";
$DB->query($sql,"session.php");
while ($row = $DB->get())
{
$type = $row["useno"] ;
if ($type == $bookingtype)
{
$timelimit = $row["timelimit"] ;
$typeMatch = "yes";
$type = $bookingtype;
}

}
	
	if (!isset($typeMatch))
	{
	 $ERROR = "$phrase[224] $phrase[1117]"; //Booking failed. Booking type not available on this computer.
	}	

	
	if ($timelimit != 0)
			{
			//  check that the user is not exceeding daily time limit
			
			$result = $pcbookings->dailylimit($cardnumber,$timelimit,$type);
			if ($result == "error")
				{
				$ERROR =  "$phrase[224] $phrase[1116]";
				//$failure = "$result";
				}
			
			
			}
	
	/*		
	 //check pc has usage
	$sql = "select count(*) as num from pc_bridge where  pcnum = '$pc' and useno = '$type'";
$DB->query($sql,"pcweb.php");
$row = $DB->get();
$num= $row["num"];
	 
if ($num == 0) { $ERROR = "Booking failed. Booking type not available on this computer.";}	 

  */

	 //check pc not out of order

$sql = "select * from pc_computers where pcno = '$pcno'";
$DB->query($sql,"pcweb.php");
$row = $DB->get();
$outoforder = $row["outoforder"];
$pcname = $row["name"];


if ($outoforder == 1) { $ERROR = "$phrase[469]";}
	 
        
       
	//check booking does not clash with existing bookings already held by patron

	
	  	if (!isset($ERROR))
	  	{
		$sql = "select count(bookingno) as num from pc_bookings where cardnumber = '$cardnumber' and (($start <= bookingtime and $end > bookingtime) or ($end > endtime and $start < endtime)  or ($start >= bookingtime and $end <= endtime)) and cancelled = '0' and finished = '0' ";
		
              //  echo $sql;
                $DB->query($sql,"pcweb.php");
		$row = $DB->get();
		$num = $row["num"];
	
		
		if ($num > 0) { $ERROR = "$phrase[1110]";}
	 	}

	 	
	 
	 
	 
	 //check library hours
$hours = $pcbookings->OpeningHours($start,$end,FALSE,$pcno,$phrase);
	 
//echo "checking stuff $start,$end,FALSE,$pcno,$phrase)";
	 
if ($hours['error'] != "") {$ERROR = $hours['error'];} 





	 
	//check patron not banned
	$ban = $pcbookings->checkPatronBan($cardnumber);

		if ($ban['banned'] == "yes")	
	 { $ERROR = "$phrase[429]";}
	  	
	 
	 if (!isset($ERROR))
	  	{
	 $durationtest = $pcbookings->checkDuration($type,$start,$end);
	 
	 if ($durationtest !=  "ok")	
	 { $ERROR = "$durationtest";}
	  	}
	 
	 
	 
	 //check booking limit not exceeded
	 if (!isset($ERROR))
	  	{
	 	
		$limit = $pcbookings->Bookinglimit($type,$cardnumber,$bid,$start);

		
		if ($limit['status'] == "1")
		{
		$ERROR =  " $phrase[224] <br> $phrase[914] ";
		}
		elseif ($limit['status'] == "2")
		{
		$ERROR =  "$phrase[224] <br> $phrase[1118] ";
		}
	  	}
	  	
	 
	  	
		 //check computer available
	
	  	if (!isset($ERROR))
	  	{
		$sql = "select count(bookingno) as num from pc_bookings where pcno = '$pcno' and (($start <= bookingtime and $end > bookingtime) or ($end > endtime and $start < endtime)  or ($start >= bookingtime and $end <= endtime)) and cancelled = '0' and finished = '0' ";
		$DB->query($sql,"pcweb.php");
		$row = $DB->get();
		$num = $row["num"];
	
		
		if ($num == 1) { $ERROR = "$phrase[440]";}
	 	}
	  	
	  	
                $day = date("d",$start);
                $month = date("m",$start);
                $year = date("Y",$start);
   	
   	$startofday = mktime(0,0,0,$month,$day,$year);
   	$endofday = mktime(23,59,59,$month,$day,$year);
	  		 
                if (!isset($DELAY)) {$DELAY = 1;}
                // cannot book another until $DELAY hours after any existing bookings
                
                
	if (!isset($ERROR))
	  	{
		$sql = "select * from pc_bookings where pcusage = '$type' and cardnumber = '$cardnumber' and $startofday < bookingtime and $endofday > endtime and cancelled = '0' and finished = '0' ";
		
               // echo $sql;
               // echo "start is $start";
                $DB->query($sql,"pcweb.php");
		while ($row = $DB->get())
		{
                
                $_endtime = $row["endtime"];
                $_delay = $_endtime + ($DELAY * 3600);
               // echo "(($start >= $_endtime ) && ($start < $_delay))";
		if (($start >= $_endtime ) && ($start < $_delay))
			{$ERROR = "$phrase[934]";}
		
		}
	  	}
	
		
	 //display results
	 
	  	if (isset($ERROR))
	  	{
	  
	  	echo "<br><span style=\"color:red;font-size:1.5em\">$ERROR</span>	<br><br>";
	  	
	  	if (isset($limit) && in_array("status",$limit) && $limit['status'] == "exceeded")
	  	{
	  		echo "<a href=\"book.php$fullSuffix\">$phrase[34]</a>";
	  	}
	  	else 
	  	{
                    $link = "book.php?event=book&start=$start&duration=$duration&bid=$bid&type=$type&pc=$pc" . $partSuffix;
	  		echo "<a href=\"$link\">$phrase[813]</a>";
	  	}
	
		
		}
		else
		{
		
			
			echo "<span style=\"font-size:2em;color:red\">Booking successfull!</span><br><br>";
			
			
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
{
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else
{
$ip = $_SERVER['REMOTE_ADDR'];	
}
		//if (($cardnumber == "" && $createpin == 1) || $createpin == 2)	{   $pin = rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) ; } else { $pin = "";}                     			
$pin = "";



 if ($DB->type == "mysql")
									{  	
								$sql = "LOCK TABLE pc_bookings WRITE";
									}
								else
									{  	
								$sql = "begin transaction";
									}

	$DB->query($sql,"book.php");
	
	
	
		$sql = "select count(bookingno) as num from pc_bookings where pcno = '$pcno' and (($start <= bookingtime and $end > bookingtime) or ($end > endtime and $start < endtime)  or ($start >= bookingtime and $end <= endtime)) and cancelled = '0' and finished = '0' ";
		$DB->query($sql,"pcweb.php");
		$row = $DB->get();
		$num = $row["num"];
	
		
		if ($num == 1) {
			
				echo "<span style=\"color:red;font-size:1.5em\">$phrase[440]</span>	<br><br>";
		}

		else 
		{

                    $itemsout = '0';
    $insertsql = "INSERT INTO pc_bookings VALUES(NULL,'$name','', '$start','$bid', '$pcno', '0', '$type', '$cardnumber', '$end','$ip','web','0','$now','','','0','0','2','0','0','0','$pin','$itemsout')";
	$DB->query($insertsql,"book.php");
	$bookingno = $DB->last_insert();
	
	
		
		
	if ($DB->type == "mysql")
		{  	
		 $sql = "unlock tables";
		}
								
else
		{  	
		$sql = "commit";
		}
		
	$DB->query($sql,"book.php");	
	
	
	  if (isset($PCDEBUG) && $PCDEBUG == "on")
					{
					pc_debug($DB,$bookingno,$insertsql);	
					}

		}
		

	
	
		if ($start < time() + 14400)
		{
		$pcbookings->Status($pc,$phrase);
		}
	
	
	$sql = "select * from pc_bookings where bookingno = '$bookingno' and cardnumber = '$cardnumber'";
	
	$DB->query($sql,"pcweb.php");
	$num = $DB->countrows();

	

	
	if ($num <> 1) { echo "$phrase[224]";}
	else {
			$row = $DB->get();
			$start = $row["bookingtime"];
			$end = $row["endtime"];
			$cardnumber = $row["cardnumber"];
			
			 $date =  strftime("%a %x", $start);
			 $start =  date("g:ia", $start);
			  $end = date("g:ia", $end);
			  
		
		echo "

		<table cellspacing=\"5\" style=\"margin: 0 auto;text-align:left\">
		<tr><td align=\"right\"><b>$phrase[460]</b></td><td> $cardnumber</td></tr>
		<tr><td align=\"right\">	<b>$phrase[121]</b></td><td> $branchname</td></tr>";
		
		if ($DISPLAYPCNAME == 1) {echo "	<tr><td align=\"right\"><b>PC</b></td><td> $pcname</td></tr>";}
		
		echo "	<tr><td align=\"right\"><b>$phrase[186]</b></td><td> $date</td></tr>
			<tr><td align=\"right\"><b>$phrase[185]</b></td><td> $start - $end</td></tr>
	</table>
	";
		
	
			echo "<br>$phrase[1098]<br>";
	
		
	echo "<a href=\"book.php$fullSuffix\" style=\"font-size:2em;text-decoration:none\">$phrase[34]</a>
                        
                   
		";
		
		}
	
	
		
		}
	 
} 
 
 
 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
