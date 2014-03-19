<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$page_title = "$phrase[466]";

include ("../includes/htmlheader.php");



$ip = ip("pc"); 

if (isinteger($_REQUEST["m"]))
{
	$m = $_REQUEST["m"];
	$access->check($m);
	
	if ($access->thispage < 1)
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
$ERROR  =  $phrase[72];	
}



if (isset($_REQUEST["bookingno"])) 
	{
	if (!(isinteger($_REQUEST["bookingno"]))) 
	{$ERROR  = $phrase[72];}
	else {$bookingno = $_REQUEST["bookingno"];}
	}

if(!isset($ERROR))
{
	

//$sql = "select pc_computers.name as computer, pc_bookings.name as patron, pc_bookings.cardnumber as barcode ,pc_branches.name as branch,pc_branches.telephone as telephone,  pc_bookings.pin as PIN, pc_bookings.bookingtime as bookingtime,pc_bookings.endtime as endtime, pc_usage.name as type from pc_bookings, pc_usage, pc_branches, pc_computers where pc_computers.branch = pc_bookings.branchid and pc_bookings.branchid = pc_branches.branchno and pc_bookings.pcusage = pc_usage.useno and pc_bookings.bookingno = '$bookingno' ";


$sql = "select pc_computers.name as computer, pc_bookings.name as patron, pc_bookings.cardnumber as barcode ,pc_branches.name as branch,pc_branches.telephone as telephone,  pc_bookings.pin as PIN, pc_bookings.bookingtime as bookingtime,pc_bookings.endtime as endtime, pc_usage.name as type from pc_bookings, pc_usage, pc_branches, pc_computers 
where 
pc_computers.branch = pc_bookings.branchid 
and pc_bookings.branchid = pc_branches.branchno 
and pc_bookings.pcusage = pc_usage.useno 
and pc_computers.pcno = pc_bookings.pcno
and pc_bookings.bookingno = '$bookingno' ";

//echo $sql;
$DB->query($sql,"calprint.php");
	$row = $DB->get();

$branch = $row["branch"] ;
$telephone = $row["telephone"] ;			
$computer = $row["computer"] ;
$name = $row["patron"] ;
$venue = $row["branch"];
$type = $row["type"];
$barcode = $row["barcode"];
$PIN = $row["PIN"];
$date = strftime("%A %x", $row["bookingtime"]);
$time = date("g:i a", $row["bookingtime"]);
$endtime = date("g:i a", $row["endtime"]);
			

	
	echo "<div style=\"padding:0;margin:0;\" >
		<span style=\"float:right\" class=\"hide\">
		<script type=\"text/javascript\">

	function printer()
	{
	window.print()
	window.close()
	}
	</script>
		<a href=\"javascript:printer()\">$phrase[250]</a> &nbsp; <a href=\"javascript:window.close()\">$phrase[266]</a></span><br><br>
	
		
	<div style=\"text-align:left;font-size:14.pt;margin-bottom:2em\">
	$branch<br>
	$phrase[132] $telephone

<br><br>";

		
if ($PIN != "" && $PIN != "auto") 
	{
		echo "<span style=\"font-size:1.5em\">$phrase[922]: $PIN </span><br>";
	}	
elseif ($barcode != "") 
	{
		echo "$phrase[460]: $barcode<br>";
	}	
	echo "<br>

$computer<br><br>
	$time - $endtime<br>
					$date<br>
					";
					
				$PCFOOTER = nl2br($PCFOOTER);
					echo "
					
					$PCFOOTER</div>";	
			
	}
	

include ("../includes/footer.php");
	
?>