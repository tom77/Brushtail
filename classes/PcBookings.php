<?php

Class PcBookings
{
	
	public $DB;

	public $barcode;

	
	
	public function __construct($DB)
    {
   //$this->m = $m;

   $this->DB = $DB;
 
  
   }
   
   
 
		public function pc_stats($month,$year,$DB,$phrase)
		{
	$formatmonth = strftime("%B %Y", mktime(0,0,0,$month,1,$year));

		$output = "<h2>$formatmonth</h2>
		<br>
		<br><br>";
		
		
		
		 if ($this->DB->type == "mysql")
		{ 
		$sqlhours = "select pc_branches.name as branch, pc_usage.name as usename, (sum(endtime - bookingtime) /3600 ) as hours from pc_bookings, pc_branches, pc_usage where pc_bookings.branchid = pc_branches.branchno and pc_bookings.pcusage = pc_usage.useno and month(from_unixtime(bookingtime,'%Y%m%d')) = \"$month\" and year(from_unixtime(bookingtime,'%Y%m%d')) = '$year' and pc_usage.stats = '1' and cancelled = '0' and finished = '0' group by branchid, pc_usage.name";
		}
			else
		{
		$sqlhours = "select pc_branches.name as branch, pc_usage.name as usename, (sum(endtime - bookingtime) /3600 ) as hours from pc_bookings, pc_branches, pc_usage where pc_bookings.branchid = pc_branches.branchno and pc_bookings.pcusage = pc_usage.useno and strftime('%m',datetime ( bookingtime , 'unixepoch','localtime' )) = '$month' and strftime('%Y',datetime ( bookingtime , 'unixepoch','localtime' )) = '$year' and pc_usage.stats = '1' and cancelled = '0' and finished = '0' group by branchid, pc_usage.name";
			
			
		}
		
 		
		 $this->DB->query($sqlhours,"pc.php");
		while ($row = $this->DB->get()) 
			{
			$branch = $row["branch"];
			$usage = $row["usename"];
			$hours = $row["hours"];
			$arrayhours["$branch"]["$usage"] = $hours;
			//echo "hours $branch $usename $hours <br>";
			}
		
if ($this->DB->type == "mysql")
		{ 
		$sqlcount = "select pc_branches.name as branch, pc_usage.name as usename, count(*) as number from pc_bookings, pc_branches, pc_usage where pc_bookings.branchid = pc_branches.branchno and pc_bookings.pcusage = pc_usage.useno and month(from_unixtime(bookingtime,'%Y%m%d')) = \"$month\" and year(from_unixtime(bookingtime,'%Y%m%d')) = \"$year\" and pc_usage.stats = \"1\" and cancelled = \"0\" group by branchid, pc_usage.name";
		}
		
else
		{ 
		$sqlcount = "select pc_branches.name as branch, pc_usage.name as usename, count(*) as number from pc_bookings, pc_branches, pc_usage where pc_bookings.branchid = pc_branches.branchno and pc_bookings.pcusage = pc_usage.useno and strftime('%m',datetime ( bookingtime , 'unixepoch','localtime' )) = '$month' and strftime('%Y',datetime ( bookingtime , 'unixepoch','localtime' )) = '$year' and pc_usage.stats = '1' and cancelled = '0' group by branchid, pc_usage.name";
		}
			
		
		
		 $this->DB->query($sqlcount,"pc.php");
	
		//echo "$sqlcount";
		while ($row = $this->DB->get()) 
			{
			$branch = $row["branch"];
			$usage = $row["usename"];
			$number = $row["number"];
			//echo " number $branch $usename $number <br>";
			$arraynumber["$branch"]["$usage"] = $number;
			}
		$output .=  "<table  class=\"colourtable\" style=\"margin:0 auto;text-align:right\" cellpadding=\"5\">";
		
		
		//print_r($arraynumber);
		$usehours = array();
		$usenum = array();
		if (isset($arraynumber))
		{
		foreach ($arraynumber as $branch => $usage)
			{
				$branchhours = 0;
				$branchnum = 0;
			$output .=  "<tr class=\"accent\"><td><b>$branch</b></td><td>$phrase[462]</td><td>$phrase[473]</td><td>$phrase[474]</td></tr>";
			
				foreach ($usage as $use => $num)
					{
					$hour = $arrayhours["$branch"]["$use"];
					$output .=  "<tr><td></td><td>$use</td><td>$num</td><td>";
					$output .=  round($hour);
			
					$output .=  "</td></tr>";
					$branchhours = $branchhours + $hour;
					$branchnum = $branchnum	+ $num;
					
					if (!(array_key_exists($use, $usehours)))
					{
					$usehours["$use"]= 0;		
					}
					$usehours["$use"] = $usehours["$use"] + $hour;	

					
					if (!(array_key_exists($use, $usenum)))
					{
					$usenum["$use"]= 0;		
					}
					$usenum["$use"] = $usenum["$use"] + $num;
					
						
						
						
					
					
					}
			$output .=  "<tr><td></td><td><b>Total</b></td><td>$branchnum</td><td>";
			
			$output .=  round($branchhours);
			$output .=  "</td></tr>";
			
			}
		}
		$output .= "<tr class=\"accent\"><td><b>$phrase[475]</b></td><td>$phrase[454]</td><td>$phrase[473]</td><td>$phrase[474]</td></tr>";
		//add stats for all branches
		$totalhours = 0;
		$totalnum = 0;
		
		foreach ($usenum as $use => $num)
			{
			$hour = $usehours["$use"];			
			$output .=  "<tr><td></td><td>$use</td><td>$num</td><td>";
			$output .=  round($hour);
			$output .=  "</td></tr>";
			$totalhours = $totalhours + $hour;
			$totalnum = $totalnum + $num;
				
			
			}
			$totalhours = round($totalhours);
		$output .=  "<tr><td></td><td><b>$phrase[213]</b></td><td><b>$totalnum</b></td><td><b>";
		$output .=  round($totalhours,1);
		$output .=  "</b></td></tr>";
		$output .=  "</table> <br><br>";
		
		if ($this->DB->type == "mysql")
		{ 
		$sql = "select count(distinct cardnumber) as count, DATE(from_unixtime(bookingtime)) as datestring
		from pc_bookings, pc_usage where pc_bookings.pcusage = pc_usage.useno and month(from_unixtime(bookingtime,'%Y%m%d')) = '$month' and year(from_unixtime(bookingtime,'%Y%m%d')) = '$year' and pc_usage.stats = '1' and cancelled = '0' group by datestring";
		}
		
			else
		{ 
		//$sql = "select count(patrons) as count, strftime('%d/%m/%Y',datetime ( bookingtime , 'unixepoch' )) as date
		//from (SELECT DISTINCT cardnumber as patrons FROM pc_bookings),pc_bookings, pc_usage where pc_bookings.pcusage = pc_usage.useno and strftime('%m',datetime ( bookingtime , 'unixepoch' )) = '$month' and strftime('%Y',datetime ( bookingtime , 'unixepoch' )) = '$year' and pc_usage.stats = '1' and cancelled = '0' group by date";
		
		$sql = "select count(patrons) as count, datestring
		from (SELECT DISTINCT cardnumber as patrons, strftime('%d/%m/%Y',datetime ( bookingtime , 'unixepoch' ,'localtime')) as datestring FROM pc_bookings, pc_usage where pc_bookings.pcusage = pc_usage.useno and strftime('%m',datetime ( bookingtime , 'unixepoch','localtime' )) = '$month' and strftime('%Y',datetime ( bookingtime , 'unixepoch','localtime' )) = '$year' and pc_usage.stats = '1' and cancelled = '0') group by datestring";
		}
	
	
			
		
		$output .=  "<table class=\"colourtable\" style=\"margin:1em auto;text-align:left\" >
		
		<tr class=\"accent\"><td><b>$phrase[186]</b></td><td><b>$phrase[213]</b></td></tr>";
			 $this->DB->query($sql,"pc.php");
		while ($row = $this->DB->get()) 
			{
			$count = $row["count"];
			$date = $row["datestring"];
			$output .=  "<tr><td>$date</td><td>$count</td></tr>";
			}
			
		$output .=  "</table>";
		
		$output .=  "<table class=\"colourtable\" style=\"margin:1em auto;text-align:left\" >
			<caption>$phrase[919]</caption>
		<tr class=\"accent\"><td><b>$phrase[3]</b></td><td><b>$phrase[213]</b></td></tr>";
		if ($this->DB->type == "mysql")
		{ 
		$sql = "select username, count(*) as number from pc_bookings, pc_usage where  pc_bookings.pcusage = pc_usage.useno and month(from_unixtime(bookingtime,'%Y%m%d')) = '$month' and year(from_unixtime(bookingtime,'%Y%m%d')) = '$year' and pc_usage.stats = '1' and cancelled = '0' group by username";
		}
		else
		{ 
		$sql = "select username, count(*) as number from pc_bookings, pc_usage where  pc_bookings.pcusage = pc_usage.useno and strftime('%m',datetime ( bookingtime , 'unixepoch' ,'localtime')) = '$month' and strftime('%Y',datetime ( bookingtime , 'unixepoch','localtime' )) = '$year' and pc_usage.stats = '1' and cancelled = '0' group by username";
		}
		
		
			 $this->DB->query($sql,"pc.php");
			
		while ($row = $this->DB->get()) 
			{
			$username = $row["username"];
			$number = $row["number"];
			$output .=  "<tr><td>$username</td><td>$number</td></tr>";
			}
			$output .=  "</table>";
			
			return $output;
		}
   
   
   
   public function dailylimit($cardnumber,$timelimit,$type)
   {
   $total = 0;
   	
   	$day = date("d");
   	$month = date("m");
   	$year = date("Y");
   	
   	$startofday = mktime(0,0,0,$month,$day,$year);
   	$endofday = mktime(23,59,59,$month,$day,$year); 
   	
   	$sql = "select bookingtime, endtime from pc_bookings where pcusage = '$type' and bookingtime > $startofday and bookingtime < $endofday and  cardnumber = '$cardnumber' and cancelled = '0' and finished = '0'";
   	
   	$this->DB->query($sql,"PcBookings.php");
	while ($row = $this->DB->get())
		{
		$bookingtime = $row["bookingtime"];
		$endtime = $row["endtime"];
		
		$temp = $endtime - $bookingtime;
		$total = $total + $temp;
		}

                $timelimit = $timelimit * 60 * 60;
                
               
	if ($total >= ($timelimit))
	{ return "error";} else {return "ok";}


   	
   	
   }
   
   
   
   public function checkDuration($type,$start,$finish)
   
   	{
   	
   		     $sql = "select maxtime,mintime  from pc_usage where useno = '$type' ";
	    
		 $this->DB->query($sql,"PcBookings.php") ;
		 $row = $this->DB->get();
		 $max = $row["maxtime"] * 60;
		 $min = $row["mintime"] * 60;
   		
   		$duration = $finish - $start;
   		if ($duration  > $max)
	{ return "Booking duration too long type.";}
	
	
		elseif ($duration  < $min)
	{ return "Booking duration too short";} 
	
	else {return "ok";}
	
   		
   	}
   
   
   
   
   
   
   public function BookingLimit($type,$cardnumber,$bid,$start)
   {
   	
   	 	$type = $this->DB->escape($type);	
   	 	$bid = $this->DB->escape($bid);	
   	 	
   	$sql = "select weblimit,weblimitperday from pc_usage where useno = '$type'";
		$this->DB->query($sql,"PcBookings.php");
		$row = $this->DB->get();
		$weblimit = $row["weblimit"];
                $weblimitperday = $row["weblimitperday"];
		$now = time();
                
		$sql = "select count(bookingno) as num from pc_bookings where cardnumber = '$cardnumber' and pcusage = '$type' 
                and branchid = '$bid' and  $now < bookingtime and cancelled = '0' and finished = '0'";
		$this->DB->query($sql,"pcwbookings.php");
		$row = $this->DB->get();
		$branchnum = $row["num"];
                
                
                $day = date("d",$start);
                $month = date("m",$start);
                $year = date("Y",$start);
                
                $daystart = mktime(0,0,0,$month,$day,$year);
                $dayend = mktime(23,59,59,$month,$day,$year);
		
               
                
                $sql = "select count(bookingno) as num from pc_bookings where cardnumber = '$cardnumber' and pcusage = '$type' 
                and  $daystart < bookingtime  and  $dayend > bookingtime and cancelled = '0' and finished = '0'";
		$this->DB->query($sql,"pcwbookings.php");
		$row = $this->DB->get();
		$daynum = $row["num"];
		
		
		if ($branchnum >= $weblimit && $weblimit != 0)
		{
			
		
		$limit['status'] = "1"; 	
			
		}
                elseif ($daynum >= $weblimitperday && $weblimitperday != 0)
		{
			
		
		$limit['status'] = "2"; 	
			
		}
		else {$limit['status'] = "3";}
		return $limit;
		
		
   }
   
   
   
   
   
   
   
   
   
   
   
    public function OpeningHours($start,$end,$branch,$pc,$phrase)
   {
   if ($end == FALSE) {$end = time();}	

   //booking finish time
  $hours["endtime"] = $end; 
    
   
   	$weekday = date("w",$start);
   	
   	
   	

 //echo "pc is $pc weekday is $weekday branch is $branch  pc is $pc MM";
if ($branch == TRUE)
   	{
   	$branch = $this->DB->escape($branch);	
   	
   
   	
	$sql = "select earlyfinish,open, openinghour,closinghour from branch_openinghours,pc_branches where branch_openinghours.branchid = pc_branches.branchno and branch_openinghours.branchid = '$branch' and day = '$weekday'";

	//echo $sql;
	//echo "<hr>";
   	}
   	
   	
elseif ($pc == TRUE)
   	{
   	$pc = $this->DB->escape($pc);	
   
// echo "pc is $pc branch is $branch early is $earlyfinish $sql;";
   	 
	$sql = "select earlyfinish, open, openinghour,closinghour from branch_openinghours, pc_computers, pc_branches where pc_computers.branch = pc_branches.branchno and pc_computers.branch = pc_branches.branchno and branch_openinghours.branchid = pc_computers.branch and pc_computers.pcno ='$pc' and day = '$weekday'";
	
	//echo $sql; 	
  
   	}
 
$this->DB->query($sql,"PcBookings.php");
$row = $this->DB->get(); 

$earlyfinish = $row["earlyfinish"];



$hours['open'] = $row["open"];
$openinghour = $row["openinghour"];
$closinghour = $row["closinghour"];

$day = date("d",$start);
$month = date("m",$start);
$year = date("Y",$start);
$cm = substr($closinghour, 2, 2 );
$ch =  substr($closinghour, 0, 2 );
$om = substr($openinghour, 2, 2 );
$oh =  substr($openinghour, 0, 2 );



$hours['closing'] = mktime($ch,$cm,0,$month,$day,$year)  - ($earlyfinish * 60);



$hours['opening'] = mktime($oh,$om,0,$month,$day,$year);

$hours['closehour'] = $ch;
$hours['closeminute'] = $cm;
$hours['openhour'] = $oh;
$hours['openminute'] = $om;



$hours['error'] = "";
   	

if ($hours['open'] == 0) { $hours['error'] = "$phrase[528]";} //closed all day
//if (($start < $hours['opening'] - 3600 )  || $end > $hours['closing']) { $hours['error'] = "$phrase[528]";}

if (($start < $hours['opening'] - 3600 ) || ($start > $hours['closing']) ) { $hours['error'] = "$phrase[528]";}

//echo "end is $end";
if ($end > $hours['closing']) {$hours["endtime"] = $hours['closing'];}


   	
//is library open now


return $hours;
   }
   
   
   
   
   
   
   
   
   
   
   
   
   
   
    public function Closure($branch,$bookingtime)
   {
   	 	$bookingtime = $this->DB->escape($bookingtime);	
   	 	$branch = $this->DB->escape($branch);	
   	
   	
 	if ($this->DB->type == "mysql")
		{    
		$sql = "select UNIX_TIMESTAMP(date_blocked) as date_blocked, UNIX_TIMESTAMP(date_finish) as date_finish,reason from pc_closures where branch = '$branch' and UNIX_TIMESTAMP(date_blocked) < '$bookingtime' and '$bookingtime' < UNIX_TIMESTAMP(date_finish)";
		}
else
	{
			$sql = "select strftime('%s',date_blocked) as date_blocked,strftime('%s',date_finish) as  date_finish, reason from pc_closures where branch = '$branch' and strftime('%s',date_blocked) < '$bookingtime' and '$bookingtime' < strftime('%s',date_finish)";
	}

//echo $sql;

$this->DB->query($sql,"PcBookings.php");

$row = $this->DB->get();
	$reason = $row["reason"];
	$now = time();
	$start = strftime('%x',$row["date_blocked"]);
	$end = strftime('%x',$row["date_finish"]);
	
	  if ($this->DB->countrows() > 0)
			  {
			  $closure['closed'] = "yes";	
			  $closure['start'] = $start;
			  $closure['end'] = $end;
			  }
			  else 
			  {
			  $closure['closed'] = "no";	
			
			  }
	  
	return $closure;
   	
   	
   }
   
   
   public function checkPatronBan($cardnumber)
   {
  
   	
   	 if ($this->DB->type == "mysql")
		{ 
		 $now = date("Ymd");
			 
 $sql = "select *, UNIX_TIMESTAMP(date_blocked) as start, UNIX_TIMESTAMP(date_finish) as end  from pc_blacklist where barcode = \"$cardnumber\"  and (\"$now\" >= date_blocked and \"$now\" <= date_finish)";
		}
 
else
		{   
		 $now = date("Y-m-d");
		 
 $sql = "select reason, strftime('%s',date_blocked) as start, strftime('%s',date_finish) as end  from pc_blacklist where barcode = \"$cardnumber\"  and ('$now' >= date_blocked and '$now' <= date_finish)";
		}
		
		
   //	$sql = "select *, date_format(date_blocked,'%e/%m/%Y' ) as start, date_format(date_finish,'%e/%m/%Y' ) as end  from pc_blacklist where barcode = \"$cardnumber\" and (NOW() >= date_blocked and NOW() <= date_finish)";
   
 
			  $this->DB->query($sql,"PcBookings.php");
			
			  
			  if ($this->DB->countrows() > 0)
			  {
			  $row = $this->DB->get();
			
			  $ban['banned'] = "yes";	
			  $ban['reason'] = $row["reason"];
			  $ban['start'] = strftime('%x',$row["start"]);
			  
			  $ban['end'] = strftime('%x',$row["end"]);
			  }
			  else 
			  {
			  	 $ban['banned'] = "no";	
			  }
			
			return $ban;
			
			
   
   }
   
   
   
 
  public function Status($pcno,$phrase)
   {
   	

   	
   	
   	$sql = "select  poll_client,ip from pc_computers
   	 where pc_computers.pcno  = '$pcno' ";

//echo "$sql <br><br>";



$this->DB->query($sql,"PcBookings.php");
$row = $this->DB->get();


$poll_client = $row["poll_client"];
$ip = $row["ip"];
   	
   	
   	
   	if ($poll_client == 1 && $ip != "")
	{
	global $url;
	
	
	$string = strrev($url);
	
	$pos = strpos($string,"/") + 1;
	$length = strlen($string);
	$string = substr($string,$pos,$length);
	
	$pos = strpos($string,"/") + 1;
	$length = strlen($string);
	$string = substr($string,$pos,$length);
	
	

	$url = strrev($string);
	$url = $url . "/main/pollclient.php?ip=" . $ip . "&pcno=" . $pcno;
	
	
	
	
	$ctx = stream_context_create(array('http' => array('timeout' => 1)));

	
	$html =  @file_get_contents($url,0,$ctx);
	//echo "url is $url html is $html";
	}

   }
   

   
}

?>