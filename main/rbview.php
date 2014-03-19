<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$bodyjavascript = "onLoad=\"self.focus()\"";

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

if (isset($_REQUEST["resource_no"])) 
	{
	if (!(isinteger($_REQUEST["resource_no"]))) 
	{$ERROR  = $phrase[72];}
	else {$resource_no = $_REQUEST["resource_no"];}
	}

if (isset($_REQUEST["bookingno"])) 
	{
	if (!(isinteger($_REQUEST["bookingno"]))) 
	{$ERROR  = $phrase[72];}
	else {$bookingno = $_REQUEST["bookingno"];}
	}

if(!isset($ERROR))
	{
	
	 $sql = "select * from resource where resource_no = \"$resource_no\"";
	
$DB->query($sql,"rbview.php");
	$row = $DB->get();
			
	$resource_name =$row["resource_name"];
			$fee_applicable = $row["fee_applicable"];
			$location = $row["location"];
			$display_contact = $row["display_contact"];
			$display_telephone = $row["display_telephone"];
			$display_address = $row["display_address"];
			$display_notes = $row["display_notes"];
			$book_multiple_days = $row["book_multiple_days"];
			$print = $row["print"];
			
			
	
	
	$sql = "select * from resourcebooking where bookingno = \"$bookingno\" ";
	$DB->query($sql,"rbview.php");
	$row = $DB->get();
	$bookno =$row["bookingno"];		
			$bookname = formattext($row["name"]);
			$sname = formattext($row["staffname"]);
			$book =$row["bookeddate"];
			$bookeddate = date("g:ia",$book);
			$bookeddate .= strftime(" %x",$book);
			$username =$row["username"];
			$cancelled =$row["cancelled"];
			$cancelname =$row["cancelname"];
			$cancelip =$row["cancelip"];
			$cancel =$row["canceltime"];
			$canceltime = date("g:ia",$cancel);	
			$canceltime .= strftime(" %x",$cancel);	
			$ip =$row["ip"];
			$bookpaid =$row["paid"];
			$bookcontact =$row["contact"];
			$booktelephone =$row["telephone"];
			$bookaddress = formattext($row["address"]);
			$booknotes = formattext($row["notes"]);
			$start =$row["starttime"];
			$end =$row["endtime"];
			
			
			$starttime = date("g:ia",$row["starttime"]);
			$endtime = date("g:ia",$row["endtime"]);
			
			$startdate = strftime("%A %x",$row["starttime"]);
			$enddate = strftime("%A %x",$row["endtime"]);
			
	
	echo "<div style=\"padding:1em;\" >
		<span style=\"float:right\" >";

	echo "<a href=\"javascript:window.close()\">$phrase[266]</a></span><br><br>";
	if ($cancelled == "1") {echo "<span style=\"color:#ff6666;font-size:120%\"><b>$phrase[152]</b></span>";}
	elseif ($print == 2 || $print == 3){
		
	
	}
	echo "<br><br>	<span style=\"font-size:150%\">$resource_name</span><br>";
	
	
	echo "<br><b>$phrase[296]</b><br>$bookname<br>";
	
				if ($startdate == $enddate)
					{
					echo "<b>$phrase[297]</b><br>$starttime<br>
					<b>$phrase[298]</b><br> $startdate<br>
					";
					}
				else { 
				$diff = (floor(($end - $start)/60/60/24) + 1);
					
					echo "$phrase[269]<br>
					<b>$phrase[267]</b><br>$starttime $startdate<br> <b>$phrase[268]</b><br>$endtime $enddate<br>";
					}
					echo "
					
					<b>$phrase[299]</b><br>$location<br>
					
				";	
				if ($display_contact == 1) {echo "<b>$phrase[235]</b> <br>$bookcontact<br>";}
				if ($display_telephone == 1 ) {echo "<b>$phrase[132]</b><br> $booktelephone<br>";}
				if ($display_address == 1 && $bookaddress <> "") {echo "<b>$phrase[134]</b><br>$bookaddress<br>";}
				if ($display_notes == 1 && $booknotes <> "") {echo "<b>$phrase[236]</b><br> $booknotes<br>";}
				
						
				
							if ($fee_applicable == 1) 
								{
							
								if ($bookpaid == 1) {echo "<b>$phrase[138]</b><br>";}
								}
									echo "<b>$phrase[227]</b><br> $sname<br>
									
									
								";
						


						if ($_SESSION['userid'] == 1)
							{
							echo "<span class=\"hide\" style=\"color:#999999;\"><br>$phrase[232] $bookeddate<br>
							$phrase[144] $ip<br>
							$phrase[3] $username<br>";
							if ($cancelled == 1)
								{
								echo "<br> <b>$phrase[152]</b><br> $canceltime<br>
								$phrase[144] $cancelip<br>
							$phrase[3] $cancelname";
								}
							echo "</span>";
							}
					
					echo "<br>
<br>
<br>
<br>
<br>
<br>
<br><br>
<br>&nbsp;

";	
				//}
	}
	

include ("../includes/footer.php");
	
?>