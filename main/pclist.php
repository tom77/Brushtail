<?php

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
;
echo "

<div style=\"text-align:center;background:white;\">";

$ip = ip("pc");
$proxy = ip("proxy");




$integers[] = "day";
$integers[] = "month";
$integers[] = "year";
$integers[] = "type";
$integers[] = "group";
$integers[] = "bid";



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integerr" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];
	//echo "$_REQUEST[$value] $value <br>";
	}
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
	
	
			
	if (isset($ERROR))
	{		
	//echo $ERROR;		
	}
	else{
		

		
	include("../classes/PcBookings.php");
	$pcbookings = new PcBookings($DB);
	
	
		
	 $day = str_pad($day, 2, "0", STR_PAD_LEFT);
	 $month = str_pad($month, 2, "0", STR_PAD_LEFT);
	 
	 //($t == 1)
	// if (!isset($t)) { $t = mktime(01,01,0,$month,$day,$year); }
	 $t = mktime(01,01,0,$month,$day,$year);
	
	// echo "hello t is $t ...";
	 //$t = mktime(01,01,0,03,07,2009);
	// echo "day is $day month is $month year is $year XXt is $t XX";
	 
	// print_r($_REQUEST);
	 
	 	$sql = "select  name from pc_branches where branchno = '$bid'";
		$DB->query($sql,"pc.php");
		$row = $DB->get();
		$branchname = $row["name"];
	
	 
	 $date = date("D j M Y",$t);
	 
	


if ($bid == 0)
{
	echo "<h2>$phrase[751]</h2>";
}
elseif ($type == 0)
{
	echo "<h2>$phrase[752]</h2>";
}
else
{
	
	 

	 



	 
	$sql = "select pcno, name, flexible from pc_computers, pc_bridge where pc_computers.branch = '$bid' and pc_computers.outoforder = 0 and pc_bridge.useno = '$type' and pc_computers.pcno = pc_bridge.pcnum order by displayorder, name";
	//echo $sql;
	$DB->query($sql,"pc.php");
	$numpcs = $DB->countrows();
	
	if ($numpcs ==0) { $ERROR = "$phrase[748]";}
	while ($row = $DB->get()) 
		{
		$pcno = $row["pcno"];
		$pc_array_no[] = $pcno;
		$pc_array_flexible[] = $row["flexible"];
		$pc_array_name[] =  $row["name"];;
		
		}
	
	//check the default booking length for this booking type.
		$sql = "select * from pc_usage where useno = '$type'";
	$DB->query($sql,"pc.php");
		$row = $DB->get();
		$default = $row["defaulttime"] * 60;
		
	
	
	//find out booking interval
		$sql = "select * from pc_branches where branchno = '$bid'";
		$DB->query($sql,"pc.php");
		$row = $DB->get();
		$interval = $row["pc_booking_interval"] * 60;
		
		if ($default > $interval) {$increment = $interval;} else{$increment = $default;}


	
//find out maximum booking time allowed
$sql = "select  * from pc_usage where pc_usage.useno = '$type'";	

	$DB->query($sql,"pc.php");
$row = $DB->get();
//$interval = $row["mintime"] * 60;	
$max = $row["maxtime"] * 60;
$default = $row["defaulttime"] * 60;



$sd = date("d",$t);
$sm = date("m",$t);
$sy = date("Y",$t);
$startday =  mktime(01, 01, 01, $sm, $sd,  $sy);

$ed = date("d",$t);
$em = date("m",$t);
$ey = date("Y",$t);
$endday =  mktime(23, 59, 59, $em, $ed,  $ey);


	
	
	//find out opening and closing times

$hours =   $pcbookings->OpeningHours($startday,0,FALSE,$pc_array_no[0],$phrase);								



//print_r($hours);
	


//put bookings in array
$sql = "select * from pc_bookings where branchid = '$bid' and cancelled = '0' and bookingtime > '$startday' and bookingtime < '$endday'"; 

$DB->query($sql,"pc.php");
while ($row = $DB->get())

	{
	$booking_pc[] = $row["pcno"];
	$booking_st[] = $row["bookingtime"];
	$booking_et[] = $row["endtime"];	
	}	

	
	$now = time();
	$nowday = date("dmY");
	$t = $hours["opening"];
	$tday = date("dmY",$t);
	
	if ($now > $t && $nowday != $tday)  {$ERROR = "$phrase[749]";}
	
	if (isset($ERROR)) { echo $ERROR;}
	else
	{
	 
	
	$availablepc =Array();
	

 	while ($t < $hours["closing"])
 			{
 			
 		
			$available = "no";
			
			if ($t > $now)
			{
	 		unset($availablepc);
	 		
 			foreach($pc_array_no as $index => $pc)
                     
				 {
				  
 					if ($available == "yes" && $group == 0)
 						{ break;	}
						
						
						$available = "yes";
						//$availablepc = $pc;
					
						
						//loop through bookings
						// if match $available = "no";
 						
 						if (isset($booking_pc))
 						{
 						
						foreach($booking_pc as $i => $pcno)
							{
							 
						
							if ($pc == $pcno && ($t >= $booking_st[$i] && $t < $booking_et[$i]) )
							{
						
							$available = "no";	
							}
							
							
							}
					
							
						}
							
					if ($available == "yes")
						{
						
						$availablepc[$pc] =  $pc_array_name[$index];
					
						$pcname = $pc_array_name[$index];
						$mode = $pc_array_flexible[$index];	
						}	
						
 				}

			 if ($available == "yes")
			 {
			  $end = $t + $default;
			  
			  if ($end > $hours['closing']) {$end = $hours['closing']; }
			  $displaystart =  date("g:ia", $t);
			  $displayend = date("g:ia", $end);
			  
			   $pcs = urlencode(serialize($availablepc));
			   
//			  print_r($availablepc);
//			  echo "<br>";
//			   echo serialize($availablepc);
//			  echo "<br>";
//			   echo urlencode(serialize($availablepc));
//			  echo "<br>";
//			  echo $pcs;
//			  echo "<br>";
			 
			 
			 
			  if ($group == 0)
			  {
			  	
				if ($mode == 1) {$mode = "f";} else {$mode= "p";}
			
			echo "<div style=\"width:50%;float:left;text-align:right;padding:5px\">  <a href=\"pc.php?m=$m&amp;type=$type&amp;bid=$bid&amp;requeststart=$t&amp;requestend=$end&amp;$mode=1&amp;pcs=$pcs&amp;group=0\">$displaystart - $displayend</a></div><div style=\"float:left;width:40%;text-align:left;padding:5px\">";
			$counter =1;
			foreach($availablepc as $i => $pcname)
							{	
							if ($counter ==1) {echo " $pcname";}
							}
					
		
			
				}
				else
				{
				 
					echo " <div style=\"float:left;width:50%;text-align:right;padding:5px\"> <a href=\"pc.php?m=$m&amp;bid=$bid&amp;event=group&amp;type=$type&amp;requeststart=$t&amp;requestend=$end&amp;pcs=$pcs\">$displaystart - $displayend</a></div><div style=\"float:left;width:40%;text-align:left;padding:5px\"> ";
						foreach($availablepc as $i => $pcname)
							{	
							echo " $pcname";
							}
					
				}
			  echo " </div>";
			   }
			 }
			 $t = $t + $increment;
			}


	

	}
	
	
	
	echo "<div style=\"clear:both\">&nbsp;</div>";
	
	
}
	
	
	}

	


include ("../includes/footer.php");

?>

