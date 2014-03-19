<?php

if (isset ($pcdisplay) && $pcdisplay == "yes")
{



//function getMicrotime()
    //{
        //list($usec, $sec) = explode(" ",microtime());
        //return ((float)$usec + (float)$sec);
   // }

//$startscript = getMicrotime();


if (isset($m))
//checks that this file has been called appropriately

     {

 $allowbooking = "yes";

	 
if (isset($t))
   {
    $day = date("j",$t);
	$twodigitday = date("d",$t);
       $month = date("m",$t);
       $year = date("Y",$t);
       $weekday = date("w",$t);
	 
   }

	 
//previous
if (isset($ps))
         {
        //calculates dates from sent from previous link
       $day = date("j",$ps);
	   $twodigitday = date("d",$ps);
       $month = date("m",$ps);
       $year = date("Y",$ps);
       $weekday = date("w",$ps);
		$t = $ps; 
		//echo "false";
     }

//next
if (isset($nt))
         {
        //calculates dates from time sent from next link
       $day = date("j",$nt);
	   $twodigitday = date("d",$nt);
       $month = date("m",$nt);
       $year = date("Y",$nt);
       $weekday = date("w", $nt);
		$t = $nt;
		
     }
//echo date("l dS of F Y h:i:s A");
//echo date("l dS of F Y h:i:s A",$t);
//echo "t is $t";	

//creates array of pcs
$pcsql = "select * from pc_computers where branch = \"$bid\" and displaypc = \"0\" ";

$DB->query($pcsql,"pcadmin.php");


//initialize pc details arrays  index
$hidden = 0;
while ($row = $DB->get())
      {
      $hidden++;
      }


 if (isset($_REQUEST["s"]))
	 {$s = $_REQUEST["s"];} else {$s = "0";}

	 
	 $navtext =  "<a href=\"pc.php?m=$m\">$phrase[410]</a> | <a href=\"pc.php?m=$m&amp;bid=$bid&amp;t=$t\">$phrase[438]</a>";
	 if ($access->thispage > 1)
            {$navtext .= " | <a href=\"pc.php?m=$m&amp;bid=$bid&amp;t=$t&amp;event=quick\">$phrase[710]</a>";}
	  $navtext .= " | <a href=\"pc.php?m=$m&amp;event=getbookings&amp;bid=$bid\">$phrase[731]</a>";
	
	 
	
	 if ($hidden > 0)
	 { 
	 	if ($s == "0")
	 	{	 	
	 	$navtext .= " | <a href=\"pc.php?m=$m&amp;bid=$bid&amp;t=$t&amp;s=1\">$phrase[801]</a>";
	 	}
	 	else 
	 	{
	 	$navtext .= " | <a href=\"pc.php?m=$m&amp;bid=$bid&amp;t=$t&amp;s=0\">$phrase[802]</a>";	
	 	}
	 }
	 
	if ($PCAUTHENTICATION == "local")
{ $navtext .= " | <a href=\"pc.php?m=$m&amp;event=patron\">$phrase[703]</a> ";}


echo $navtext;

echo "<br><br>";
	 

     
if (!$day)
   {
   $day = date("j");
   $weekday = date("w");
   
   }
if (!$month)
   {
   $month = date("n");
  
   }
if (!$year)
   {
   $year = date("Y");
  
   }



//query branch name and booking display interval

$sql = "select * from pc_branches where branchno = \"$bid\" and m = '$m'";
$DB->query($sql,"pcdisplay.php");
	$row = $DB->get();
$bname = $row["name"];
$interval = $row["pc_booking_interval"] * 60;
if (!isset($interval) || $interval == 0) { $interval = 1800;}



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
 
 echo "
 	<script type=\"text/javascript\">
 	
      	function pop_window(url) 
      	{ 
      	var popit = window.open(url,'','status,resizable,scrollbars,width=350,height=400')
      	if (window.focus) {popit.focus()}

      	}
    </script>
 
 ";
 
 //if the day displayed is not today then display the date in in a warning colour like orange
   $daytest = date("j");
   $monthtest = date("n");
   $yeartest = date("Y");
   
	if (($day <> $daytest) || ($month <> $monthtest) || ($year <> $yeartest))
   	{ $daycolour = "color:#ff3333;";
   	$not_today = "yes";
   	
   	} else {
   		$daycolour = "";
   	$not_today = "no";
   	
   	$timeout = $REFRESH * 60000;
   	
   	//60000 milliseconds = 1 minute
   	
echo "

	<script type=\"text/javascript\">


     
      	
      					
      			
	
function timer(){

";


//set page refresh ??
if ($timeout != 0)
		{	
	//echo " alert($timeout); ";	
echo "var t=setTimeout(\"window.location='pc.php?m=$m&bid=$bid&t=$t";
if ($s == 1) {echo "&s=1";}

echo "'\",$timeout)";
		}
		
//scroll page		

echo "

		}
addEvent(window, 'load', timer);
addEvent(window, 'load', function () {scroll(\"scrollpoint\")});
</script>
";
   			
     }
   	
   



 echo "<table width=\"100%\" ><tr><td align=\"center\">";
 
$previoustext =  "<b>$phrase[525]</b><br><a href=\"pc.php?ps=$prev7days&amp;bid=$bid&amp;m=$m\">$formatprev7days</a>&nbsp;
 <a href=\"pc.php?ps=$prev6days&amp;bid=$bid&amp;m=$m\">$formatprev6days</a>&nbsp;
 <a href=\"pc.php?ps=$prev5days&amp;bid=$bid&amp;m=$m\">$formatprev5days</a>&nbsp;
 <a href=\"pc.php?ps=$prev4days&amp;bid=$bid&amp;m=$m\">$formatprev4days</a>&nbsp;
 <a href=\"pc.php?ps=$prev3days&amp;bid=$bid&amp;m=$m\">$formatprev3days</a>&nbsp;
 <a href=\"pc.php?ps=$prev2days&amp;bid=$bid&amp;m=$m\">$formatprev2days</a>&nbsp;
 <a href=\"pc.php?ps=$prev1days&amp;bid=$bid&amp;m=$m\">$formatprev1days</a>";

echo $previoustext;
 
 
 echo "</td><td align=\"center\"><span style=\"font-size:16pt\">$bname</span><br><span style=\"font-size:24pt;$daycolour\">$displaydate
</span></td><td align=\"center\">";
 

 $nexttext =  "<b>$phrase[526]</b><br> <a href=\"pc.php?nt=$next1days&amp;bid=$bid&amp;m=$m\">$formatnext1days</a>&nbsp;
   <a href=\"pc.php?nt=$next2days&amp;bid=$bid&amp;m=$m\">$formatnext2days</a>&nbsp;
    <a href=\"pc.php?nt=$next3days&amp;bid=$bid&amp;m=$m\">$formatnext3days</a>&nbsp;
	 <a href=\"pc.php?nt=$next4days&amp;bid=$bid&amp;m=$m\">$formatnext4days</a>&nbsp;
	 <a href=\"pc.php?nt=$next5days&amp;bid=$bid&amp;m=$m\">$formatnext5days</a>&nbsp;
 <a href=\"pc.php?nt=$next6days&amp;bid=$bid&amp;m=$m\">$formatnext6days</a>&nbsp;
 <a href=\"pc.php?nt=$next7days&amp;bid=$bid&amp;m=$m\">$formatnext7days</a>&nbsp;";

echo $nexttext;

 echo "</td></tr></table>";



 //query branch opening hours
 //php calculate day of week
$sql = "select * from branch_openinghours where branchid = \"$bid\" and day =\"$weekday\"";
//echo $sql;
$DB->query($sql,"pcdisplay.php");
$hoursnumrows = $DB->countrows(); 
$row = $DB->get();

$open = $row["open"];
$openinghour = $row["openinghour"];
$closinghour = $row["closinghour"];




//check public holiday
$datestring = $year . "-" . $month . "-" . $twodigitday;
$sql = "select * from holidays where holiday = \"$datestring\"";

$DB->query($sql,"pcdisplay.php");

$row = $DB->get();
$holiday = $row["name"];
 
 
 //branch closure 
 $now = time();
 					if ($DB->type == "mysql")
		{
$sql = "select *, date_format(date_blocked,'%e/%m/%Y' ) as start, date_format(date_finish,'%e/%m/%Y' ) as end  from pc_closures where branch = \"$bid\" and (\"$datestring\" >= date_blocked and \"$datestring\" <= date_finish)";

		}

				else
		{
$sql = "select reason, strftime('%s',date_blocked) as start, strftime('%s',date_finish) as end  from pc_closures where branch = '$bid' and ('$datestring' >= date_blocked and '$datestring' <= date_finish)";

		}
		
$DB->query($sql,"pcdisplay.php");
$numm = $DB->countrows(); 
$row = $DB->get();
	$reason = $row["reason"];
	
	$start = $row["start"];
	$end = $row["end"];
	
	if ($numm > 0)	{ $closure = "yes"; } else {$closure = "no";}
			

	
	//creates array of usage types associated with individualpcs
//this is used to display if a pc has a dedicated usage
//displayed at top row of booking table
//initialize pc usage arrays index
//$i = 0;

//$sql = "select mintime,fee, name, pcnum from pc_usage, pc_bridge where pc_usage.useno = pc_bridge.useno";
//$sql = "select mintime from pc_usage, pc_bridge, pc_computers where pc_usage.useno = pc_bridge.useno and pc_computers.branch = \"$bid\" and pc_computers.pcno = pc_bridge.pcnum";
//$DB->query($sql,"pcadmin.php");
//$usagecount = 0;
// while ($row = $DB->get())
           //  {

				
             // $a_pcnum[$i] = $row["pcnum"];
             // $a_pcfee[$i] = $row["fee"];
             // $a_name[$i] = $row["name"];
           //   $a_mintime[$i] = $row["mintime"];
           //   $i++;
				//echo "mintime $row[mintime] <br>";
				
         //     }
              
// $usagecount = $i;
              



	
	
//if ($usagecount == 0)
//{	
	//echo "<h2>$phrase[656]</h2>";
//}
	
if ($hoursnumrows == 0)
  	{
	echo "<h2>$phrase[527]</h2>";
	}
elseif (isset($holiday))
   {
   echo "<h2>$holiday</h2>";
   }
   
elseif ($open == 0)
   {
   echo "<h2>$phrase[494]</h2>";
   }
   
elseif ($closure == "yes")
   {
   echo "<h2>$phrase[528]</h2><br><br> $start - $end <br><br> $reason";
   }
else
    {
     //display booking table
 


$now = time();

//alternating row color
$altcolour = "#ccffff";
$topcolour = "#9999ff";


//creates array of existing bookings

$startofday =  mktime (0,0,1,$month,$day,$year);
$endofday =  mktime (23,59,59,$month,$day,$year);



//$starttt = getMicrotime();

//$bookingssql = "select *, date_format(bookingtime,'%Y%m%d%H%i%s') as time from pc_bookings where branchid = \"$branchid\" and bookingtime between \"$startofday\" and \"$endofday\"";
$bookingssql = "select * from pc_bookings where branchid = '$bid' and bookingtime between '$startofday' and '$endofday' order by bookingtime";



//echo "$bookingssql";
$DB->query($bookingssql,"pcadmin.php");


//$endttt = getMicrotime();

//initialize bookings details arrays  index
$i = 0;
while ($row = $DB->get())
      {


      $bookingsno[$i] = $row["bookingno"];
      $bookingsname[$i] = htmlspecialchars($row["name"]);
      $time[$i] = date("Ymdhis",$row["bookingtime"]);
      $bookingsstart[$i] = $row["bookingtime"];
      $bookingsend[$i] = $row["endtime"];
      $bookingsbranchid[$i] = $row["branchid"];
      $bookingspcno[$i] = $row["pcno"];
      $bookingspaid[$i] = $row["paid"];
      $bookingsusage[$i] = $row["pcusage"];
      $bookingstelephone[$i] = $row["telephone"];
      if (trim($row["cardnumber"]) == "")
     { $bookingscardnumber[$i] = $phrase[723];}
      else { $bookingscardnumber[$i] =  $row["cardnumber"];}
 
     // $bookingscardnumber[$i] = $row["cardnumber"];
	  $bookingsip[$i] = $row["ip"];
		
	$bookingsusername[$i] = $row["username"];
	$bookingscancelled[$i] = $row["cancelled"];
	$bookingsmodtime[$i] = $row["modtime"];
	$bookingscancelip[$i] = $row["cancelip"];
	$bookingscancelname[$i] = $row["cancelname"];
	$bookingscanceltime[$i] = $row["canceltime"];
	$bookingsfinished[$i] = $row["finished"];
	$bookingscheckedin[$i] = $row["checkedin"];
	$bookingspin[$i] = $row["pin"];

	  
      $i++;
      }
      
    

//creates array of pcs
if ($s == "0")
{
$pcsql = "select * from pc_computers where branch = '$bid' and displaypc = '1' order by displayorder, name";
}
else 
{
$pcsql = "select * from pc_computers where branch = '$bid'  order by displayorder, name";	
}

$DB->query($pcsql,"pcdisplay.php");


//initialize pc details arrays  index
$i = 0;
while ($row = $DB->get())
      {
		$arraypcno[$i] = $row["pcno"];
      $arraypcname[$i] = $row["name"];
      $temp = $row["colour"];
      if ($temp != "none") {$temp  = str_replace ("#", "" , $temp ); $temp = "#" . $temp;}
	   $arraypccolour[$i] = $temp;
	   $arraypcoutoforder[$i] = $row["outoforder"];
	   $arraypcflexible[$i] = $row["flexible"];
	    $arraypcdisplay[$i] = $row["displaypc"];
	    $arraypmessage[$i] = formattext($row["outofordermessage"]);
	  
	 	 $pcno = $row["pcno"];
         $name = $row["name"];
         $pcnames[$pcno] = $name; 
	 	
      $i++;
      }

 
      
//creates array of different usage types
//this array is used to display the usage of each booking
//initialize pc usage arrays index
$i = 0;

$usagesql = "select * from pc_usage";

$DB->query($usagesql,"pcadmin.php");




 while ($row = $DB->get())
             {


              $arraypcfee[$i] = $row["fee"];
              $arrayuseno[$i] = $row["useno"];
              $arraypcusage[$i] = $row["name"];
              $temp = $row["usecolour"];
              
               if ($temp != "none") {$temp  = str_replace ("#", "" , $temp ); $temp = "#" . $temp;}
               $arrayusecolour[$i] = $temp;
              $arrayusetelephone[$i] = $row["telephone"];
              $arrayusedisplay[$i] = $row["displaytype"]; 
              $arrayuseclient[$i] = $row["clientbooking"]; 
              $arrayuseprint[$i] = $row["print"]; 
            
              $i++;

             
              }








//creates an array checking number of usages assigened to each computer
//if a computer has only one usage the table cells will display in that usage colour 


$sql = "select count(*) as number, max(maxtime) as maxtime, pcnum from pc_usage, pc_bridge where power = '0' and pc_usage.useno = pc_bridge.useno group by pcnum";
//echo $sql;

$DB->query($sql,"pcdisplay.php");

$u_number = array();
$u_maxtime = array();

 while ($row = $DB->get())
             {


              $pcnum = $row["pcnum"];
              $u_number[$pcnum] = $row["number"];
			  $u_maxtime[$pcnum] = $row["maxtime"];
			  }
			  
	  
//print_r($u_maxtime);

//<div style='width:90%;margin:0 auto;overflow-x:scroll;overflow-y:scroll;height:1200px;position:relative' id=\"scrollbox\">

echo "  <table class=\"colourtable\" cellpadding=\"3\"  style=\"white-space:nowrap;text-align:left; vertical-align:top;margin-left:auto;margin-right:auto;background:white\">";


//display row with pc names
  echo "<tr class=\"accent\" align=\"center\"> <td class=\"accent\" ><b>$phrase[185]</b></td>";
   $bname = urlencode($bname);
   			if (isset($arraypcno))
   			{
          foreach($arraypcno as $index => $pcno)

                        {
						$pname = urlencode($arraypcname[$index]);
                        echo "<td><b>";
                         if ($access->thispage > 1)
                         { echo "<a href=\"pc.php?bid=$bid&amp;m=$m&amp;cpc=$pcno&amp;t=$today\">";}
                        echo "$arraypcname[$index]";
                         if ($access->thispage > 1)
                         {
                         echo "</a>";	
                         }
                        echo "</b></td>";
                        }
   			}

          echo "<td class=\"accent\"><b>$phrase[185]</b></td> </tr>";



$minute = substr("$openinghour", 2, 2 );
$hour =  substr("$openinghour", 0, 2 );

$cminute = substr("$closinghour", 2, 2 );
$chour =  substr("$closinghour", 0, 2 );





$opening = mktime($hour,$minute,0,$month,$day,$year);
$closing = mktime($chour,$cminute,0,$month,$day,$year);





  function isfree($time,$bookingsstart,$bookingsend,$bookingscancelled,$bookingspcno,$pcno)
                           {
                           
                          
                           	foreach($bookingsstart as $i => $starting)
                           				{	
                           					
                             			 $ending = $bookingsend[$i];
                             			// echo "hello $time $starting $ending";
                             			if (($time >= $starting && $time < $ending) && $bookingspcno[$i] == $pcno && $bookingscancelled[$i] == 0)
                            			 {
                            			 	//time is in a booking
                            			 	//echo "<br>$match";
                            			 	return false;	
                            			 }
                             			}
                           	return true;
                           	
                       
                           }




$test = $opening;
$intervalstart = $test;
$increment  = 0;
$rowcounter = 1;
while ($test < $closing)
            {
             //creates a loop that runs through the opening hours

           
            //$test is the time for each row which changes as loop continues
         
			
			$test = $opening + $increment;
            //end is the end time of each table cell , needed for testing if time is booked out
          
			$end = $test + $interval;
         
			$displaytime =  date("g:i a",$test);

            if($test < $closing)
            // this test stops closing hour being displayed
                  {
                  //displays rows in booking table
                  echo "<tr";
				  
			
		                   if (($rowcounter % 2) == 0) 
		                      {
		                      echo " class=\"accent\"";
							  }
							 
					
				if($now >= $test + $interval && $now < $end + $interval)	{echo " id=\"scrollpoint\"";}		 
				
                  echo "><td ";
				 
					
				  		
				  
				  echo "><b";
				  if ($not_today == "yes") { echo " class=\"red\"";}
				  echo ">$displaytime</b></td>";
					
				  	if (isset($arraypcno))
   			{
                    foreach($arraypcno as $index => $pcno)

                        {
                        echo "<td ";
                                 //if pc has dedicated usage display coloured cell
                               
									
										if ($arraypccolour[$index] <> "none" ) 
											{
											 
											echo "style=\"background-color:$arraypccolour[$index]\"";
													
											}
										
											
									
                                   
                        echo ">";
						
						//checks if pc is out of order
						if ($arraypcoutoforder[$index] == 1)
							{
							echo "<span style=\"color:#ff3300\">$phrase[469]<br>
							$arraypmessage[$index]
							</span><br>";
							}
						
						$time = $test;	
						$counter = 0;	
						
					
						if (isset($ignoreminutes)) {unset($ignoreminutes);}
					
						
						//execute loop every minute to check for bookings and available time
						while ($time < $test + $interval)
						//while ($counter < 30)
						{
						
						$sessionstarted = false;	
							
								
					
						
						// check for bookings begin this minute
						if (isset($bookingsstart))
                           {
                           	foreach($bookingsstart as $i => $starting)
                           	{
                            
                            // if (( ($starting < $time && $bookingsend[$i] > $time && $time = $intervalstart) || (($starting >= $time) && ($starting < $time + 60))  || ($time > $starting && $time == $opening)) && $bookingspcno[$i] == $pcno)
                             if (( (($starting >= $time) && ($starting < $time + 60))  || ($time > $starting && $time == $intervalstart && $time < $bookingsend[$i])) && $bookingspcno[$i] == $pcno)
                            
                              {
                             	///////////////////////
                             	
                             	if ($bookingscancelled[$i] == 0)
                             	{
                             	$sessionstarted = true;
                             	}
                             	
                                 // if ($bookingsfinished[$i] == 0) {$booked = 1;}
								   

									foreach($arrayuseno as $ind => $useno)
				                    {
								   if ($useno == $bookingsusage[$i]) 
								   {
								   $usecolour = $arrayusecolour[$ind];
								  
								   }
								  	}
								
								if ($bookingsfinished[$i] == 1 || $bookingscancelled[$i] == 1  || (isset($usecolour)  && $usecolour <> "default"))
									{	echo "<span "; }
									
									if($bookingsfinished[$i] == 1 || $bookingscancelled[$i] == 1)
										{ echo "title=\"Finished\" style=\"color:#999999\"";}
									
									elseif (isset($usecolour)  && $usecolour <> "default" )
										{  echo "style=\"color:$usecolour\"";}
										
								if ($bookingsfinished[$i] == 1 || $bookingscancelled[$i] == 1 || (isset($usecolour)  && $usecolour <> "default"))
									{	echo ">"; }
								 
								  
								  
										if($bookingsfinished[$i] == 1)
										 {echo "$phrase[529]<br>";}
								   
										
										 
										 
								  if ($bookingsname[$i] != "")  { echo "$bookingsname[$i]<br>";}
								
								if($bookingsfinished[$i] <> 1 && $access->thispage > 1)
                                   	
										{
											
								 		 echo "<a href=\"pc.php?m=$m&amp;u=$bookingsno[$i]&amp;t=$test";
								 		    if ($s == "1" && $arraypcdisplay[$index] == 0) { echo "&amp;s=1";}
								 		 echo "\" title=\"$pcnames[$pcno]\"";
										
								 		if ($bookingscancelled[$i] == 1) {  echo " style=\"color:#999999\"";}
								  		elseif (isset($usecolour) && $usecolour <> "default" )	{  echo " style=\"color:$usecolour\"";}
								  			echo ">";
								  			//$booked = 0;
										}
                                  
									
								
										echo "$bookingscardnumber[$i]";
										
										
										 
                                        
                                        	if($bookingsfinished[$i] <> 1)
										{
								 		 echo "</a>";
										}
                                        
										 if ($bookingspin[$i] != "" && $bookingspin[$i] != "auto") {echo "<br>$phrase[880] $bookingspin[$i]";}

										 
										 
										 
									
                                  
                                        
                                        
                                    if ($bookingscheckedin[$i] == 1 && $bookingscancelled[$i] == 0)
                                    	{echo " <img src=\"../images/tick.gif\" alt=\"$phrase[716]\">";}
                                      
                                        
                                        foreach($arrayuseno as $ind => $useno)
				                    {
								   if ($useno == $bookingsusage[$i]) 
								   {
								   $telephone = $arrayusetelephone[$ind];
								  
								   }
								  	}
                                        
                                        
                                      if (isset($telephone)  && $telephone == "1")
                                        {
										if ($bookingstelephone[$i] != "")
											{
                                        	echo "<br>$bookingstelephone[$i]";
                                        	}
										}
										
										
									
									
									 if ($arraypcflexible[$index] == 1)
                                		{
										$start = date("g:ia",$bookingsstart[$i]);
										$end = date("g:ia",$bookingsend[$i]);
											echo "<br>$start-$end";
										}
								
										
										
								// if($bookingsfinished[$i] == 1) {echo "</span>";}
												//if computer has more than one usage display 
												if (array_key_exists($pcno,$u_number) && $u_number[$pcno] > 1)
												{
				                                  //display booking usage
				                                  if ($bookingsusage[$i])
				                                     {
				                                     // echo "start $bookingsusage[$i] <br>";
				                                         foreach($arrayuseno as $ind => $useno)
				                                         {
				
				                                          if ($useno == $bookingsusage[$i] &&  $arrayusedisplay[$ind] == 1)
				                                                     {
				                                                     echo "<br>$arraypcusage[$ind]";
				                                                     }
				                                         }
				
				                                     }
												}
											
										if ($bookingspaid[$i] == 1)
											{echo "<br>$phrase[138]";}	
											
											
									      
                                    if ($bookingsfinished[$i] == 1 || $bookingscancelled[$i] == 1 || (isset($usecolour)  && $usecolour <> "default"))
                                    {echo "</span>";}
                                        		
									// if ($bookingscancelled[$i] == 1) {  echo "<br>$phrase[152]</span>";}		
											
											
									 foreach($arrayuseno as $ind => $useno)
				                    {
								   if ($useno == $bookingsusage[$i] && $arrayuseprint[$ind] == 1) 
								   		{
								
								   	 echo "<br> <a href=\"javascript:pop_window('pcslip.php?m=$m&amp;bookingno=$bookingsno[$i]')\"><img src=\"../images/printer.png\" alt=\"$phrase[466]\" title=\"$phrase[466]\"></a>";}
								  
								   }
												
									
                             	
                             	/////////////////////////////
                             
                             echo "<br><br>";
                             
                           	} 		
                           }
                           }
						
                           
                           
                         
                     
                                       
                           
                           	//If no sessions start this minute check if this time not within existing bookings
                             	 
                             	if ($sessionstarted == false && $time > $now)
                             	{
                             		
                             	//if (isset($bookingsstart))
                           			//	{
                           				//if (isfree($time,$bookingsstart,$bookingsend,$bookingscancelled,$bookingspcno,$pcno) == true) {echo "available";}
                           			//	}
                             	
                           		$temp = $time;
                           		
                           		//if (isset($ignoreminutes)) and $temp skip check
                           		
                           		
                           		if (array_key_exists($pcno,$u_maxtime)) {$max = $u_maxtime[$pcno]; }
                           		else {$max = $interval / 60; }
                           		
                           	//	$max= $interval / 60;
                           		$min = 2;
                           		$count = 0;
                           		while (($count < $max) && $temp < $closing)
                           		{
                           		
                           		if (isset($ignoreminutes)  && in_array($temp,$ignoreminutes)) {break;}
                           		
                           		
                           			
                           		
                           			if (isset($bookingsstart))
                           				{
                           		if (isfree($temp,$bookingsstart,$bookingsend,$bookingscancelled,$bookingspcno,$pcno) == false) { break;}
                           		else {$ignoreminutes[] = $temp;}
                           				} else {$ignoreminutes[] = $temp;}
                           				
                           				
                           				
                           		$count++;			
                           		$temp = $temp + 60;	
                           		}
                           		
                             	 if ($count > $min) {
                             	 		
                             	 	if ($access->thispage > 1)
                                		{
                                echo "<div class=\"add\"><a href=\"pc.php?m=$m&amp;t=$time&amp;f=$pcno";
                                
                                if ($s == "1" && $arraypcdisplay[$index] == 0) { echo "&amp;s=1";}
                               $displaytime = date("g:ia",$time); 
                            //   echo "\" title=\"$count $phrase[985] $displaytime\" >&nbsp; $count $pcno</a> </div>";
                                  echo "\" title=\"$arraypcname[$index] &nbsp;$displaytime\" ></a> </div>";
                                		}
                             	 	
                             	 
                             	}
                             	// echo "count is $count <br>";
                           		
                             } 
							//print_r($ignoreminutes);
							
						//echo " $counter <br>";	
						$counter++;	
						$time = $time + 60;
               
							
						}
					
						
						
						
						
							
                        echo "</td>";
                        }
                  }
                   echo "<td ";
 
								
							
				   echo "><b";
				  if ($not_today == "yes") { echo " class=\"red\"";}
				  echo ">$displaytime</b></td>";
                  echo "</tr> \n";

                  }
                  $rowcounter++;
            $increment = $increment + $interval;
            $intervalstart = $intervalstart + $interval;
            }
            
            
//display row with pc names
  echo "<tr class=\"accent\" align=\"center\"> <td class=\"accent\"><b>$phrase[185]</b></td>";
   $bname = urlencode($bname);
   	if (isset($arraypcno))
   			{
          foreach($arraypcno as $index => $pcno)

                        {
						$pname = urlencode($arraypcname[$index]);
                        echo "<td><b>";
                          if ($access->thispage > 1)
                                { echo "<a href=\"pc.php?bid=$bid&amp;m=$m&amp;cpc=$pcno&amp;t=$today\">";}
                       echo "$arraypcname[$index]";
                       if ($access->thispage > 1)
                                		{echo "</a>";}
                         echo "</b></td>";
                        }
   			}
          echo "<td class=\"accent\"><b>$phrase[185]</b></td> </tr>";

 echo "</table><br>";
 



	
/*

echo $navtext;

echo "<br><br><table style=\"margin:0 auto;width:100%\"><tr><td align=\"center\">$previoustext</td><td align=\"center\">

	 
 </td><td align=\"center\">$nexttext</td></tr></table><br><br>";
 * 
 */

}


}

//$endscript= getMicrotime();


//$diff = $endttt - $starttt;
//echo "query took $diff";

//$diff2 = $endscript - $startscript;
//echo "<br>script took $diff2";


}
?>


