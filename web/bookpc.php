<?php


//$td = date("Ymd");

//if ($td == "20090425")
//{
//header("Location: http://www.erl.vic.gov.au/nobook.htm");	
//exit();	
//}

include('text.php');



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





echo "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html>
<head>
 <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<title>PC Bookings</title>
   <LINK REL=\"STYLESHEET\" TYPE=\"text/css\" HREF=\"bookings.css\"> 
     <script type=\"text/javascript\" src=\"bookings.js\"></script>
</head>

<body id=\"pc_body\"><div id=\"container\"><div id=\"banner\"><a href=\"$HOME_LINK\" title=\"$HOME_LABEL\" alt=\"$HOME_LABEL\"><img src=\"logo.gif\"></a>
<h1>Book a library computer</h1></div>

";

if ($PCAUTHENTICATION == "disabled")
{
	echo "<p><h2>web bookings disabled.</h2></p>
	<p id=\"footer\"></div>
	</div>
	</body>
	</html>";
	exit();
}


//if (!isset($_REQUEST["event"])) {

	//echo $banner;
//}	


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

//print_r($_REQUEST);

if (isset($_REQUEST["event"])) {
$event = $_REQUEST["event"];}

$integers[] = "bid";
$integers[] = "type";
$integers[] = "pc";
$integers[] = "day";
$integers[] = "start";
$integers[] = "end";
$integers[] = "bookingno";
$integers[] = "duration";


//if (!isset($event)) {$event = "choose";}



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = substr($_REQUEST[$value],0,100);}
	}
}	

if (isset($ERROR)) 
{echo $ERROR;
exit();
}


include("../classes/PcBookings.php");
$pcbookings = new PcBookings($DB);


if (isset($start) && !isset($day)) {$day = $start;}
//print_r($_REQUEST);

 $sql = "select  branchno, pc_branches.name as bname from pc_branches,pc_computers,pc_bridge, pc_usage where pc_branches.branchno = pc_computers.branch and pc_bridge.pcnum = pc_computers.pcno and pc_bridge.useno = pc_usage.useno and pc_usage.web = '1' group by bname";

 
 
 $DB->query($sql,"bookpc.php");
$branchcount = 0;
while ($row = $DB->get())
      {
	
      $branchno = $row["branchno"];
      $branch[$branchno] = $row["bname"];
      $branchcount++;
      }
    
	 	 $sql = "select * from pc_usage where web = 1 order by name";
	 	
		$DB->query($sql,"bookpc.php");
		$typecounter = 0;
	
while ($row = $DB->get())
      {

     
      $useno = $row["useno"];
      $types[$useno] = $row["name"];
      $typecounter++;
	  }	    

	  echo "<div id=\"navlinks\">";
	  
if (!isset($event))

{ echo "<a href=\"bookpc.php\">$phrase[285]</a> | <a href=\"bookpc.php?event=check\">$phrase[812]</a>";} 

elseif (isset($event) && ($event == "book" || $event == "booking"))
{
echo "<a href=\"bookpc.php\">$phrase[285]</a> | <a href=\"bookpc.php?event=check\">$phrase[812]</a>";	
}
    
elseif ((isset($event) && ($event == "check" || $event == "checking"   || $event == "cancel" || $event == "cancelling")))
{
	echo "<a href=\"bookpc.php\">$phrase[285]</a> | <a href=\"bookpc.php?event=choose\">$phrase[807]</a>";
}

elseif ((isset($event) && ( $event == "list" || $event == "choose")))
{
echo "<a href=\"bookpc.php\">$phrase[285]</a> | <a href=\"bookpc.php?event=check\">$phrase[812]</a>";

}



		echo "</div><p id=\"footer\"></p></div>";

		
	
		

if (isset($event) && $event == "book")
{
	
	//print_r($_REQUEST);
	
        	 	$sql = "select name from pc_branches where branchno = '$bid'";
		$DB->query($sql,"bookpc.php");
		$row = $DB->get();
		$branchname = $row["name"];
	
	
		
        $displaydate = $date = strftime("%x",$start);
         $displaystart = date("g:i a",$start);
         
        // $displayend = date("g:i a",$end);
         echo "
               

         <form action=\"bookpc.php\" method=\"post\" style=\"width:60%;margin:0 auto\">

         <table style=\"margin:0 auto;text-align:left;border-style:none\"
cellspacing=\"0\" cellpadding=\"5\">
<tr><td align=\"right\"><b>$phrase[185]</b></td><td><b>$displaystart</b></td></tr>
<tr><td align=\"right\"><b>$phrase[745]</b></td><td><select name=\"duration\">";



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
	

echo "</select> minutes</td></tr>


<tr><td align=\"right\"><b>$phrase[186]</b></td><td><b>$displaydate</b></td></tr><br>
<tr><td align=\"right\"><b>$phrase[121]</b></td><td><b>$branchname</b></td></tr>
  <tr><td align=\"right\"><b>$phrase[141]</b></td><td>
         <input type=\"text\" name=\"name\" id=\"focus\">
         </td></tr>
         <tr><td align=\"right\"><b>$phrase[810]</b></td><td>
         <input type=\"text\" name=\"barcode\" id=\"barcode\">
         </td></tr>
        ";
         
        
                        if ($PCAUTHENTICATION != "autoaccept")
                        {
        echo "

        <tr><td align=\"right\"><b>$phrase[811]</b></td><td>
         <input type=\"password\" name=\"password\">
         </td></tr>";
                        }
         
         echo "<tr><td></td><td align=\"left\">";
         
          if ($PCAUTHENTICATION == "autoaccept")
                        {
                         echo "
      
         <input type=\"hidden\" name=\"password\" value=\"\">";	
                        
                        }
         echo "<input type=\"submit\" name=\"submit\" value=\"$phrase[807] \">
        <input type=\"hidden\" name=\"type\" value=\"$type\">
        <input type=\"hidden\" name=\"pc\" value=\"$pc\">
        <input type=\"hidden\" name=\"start\" value=\"$start\">

         <input type=\"hidden\" name=\"bid\" value=\"$bid\">
                <input type=\"hidden\" name=\"event\" value=\"booking\">
         </td></tr></table>
         

                  </form>";
        

}

elseif (isset($event) && $event == "cancel")
{
	
$time = $_REQUEST["time"];
	$date = $_REQUEST["date"];
	$branch = $_REQUEST["branch"];
	
	echo "
	
	<form action=\"bookpc.php\" method=\"post\" style=\"width:60%;margin:0 auto\">
 <fieldset><legend>$phrase[670]</legend><br>
         <table style=\"text-align:left;margin:0 auto;border-style:none\"
cellspacing=\"0\" cellpadding=\"10\">   

<tr><td align=\"right\"><b>$phrase[185]</b></td><td><b>$time</b></td></tr>
<tr><td align=\"right\"><b>$phrase[186]</b></td><td><b>$date</b></td></tr><br>
<tr><td align=\"right\"><b>$phrase[121]</b></td><td><b>$branch</b></td></tr>
 <tr><td align=\"right\"><b>$phrase[810]</b></td><td>
         <input type=\"text\" name=\"barcode\" id=\"barcode\">
         </td></tr>    
        ";
         
        
        echo "
 
        <tr><td align=\"right\"><b>$phrase[811]</b></td><td>
         <input type=\"password\" name=\"password\">
         </td></tr>";
    
         
         echo "<tr><td></td><td align=\"left\">";
         
        
         echo "<input type=\"submit\" name=\"submit\" value=\"$phrase[670] \">

 <input type=\"hidden\" name=\"time\" value=\"$time\">
  <input type=\"hidden\" name=\"date\" value=\"$date\">
 <input type=\"hidden\" name=\"branch\" value=\"$branch\">
  <input type=\"hidden\" name=\"bookingno\" value=\"$bookingno\">
                <input type=\"hidden\" name=\"event\" value=\"cancelling\">
         </td></tr></table>
         
</fieldset>
                  </form>";
         
	

}
elseif (isset($event) && $event == "booking")
{

		if (!isset($type)) {$type = 0;}
		if (!isset($bid)) {$bid = 0;}
		if (!isset($pc)) {$pc = 0;}
		
		$name = $DB->escape($_REQUEST["name"]);	
		
		$end = $start + ($duration * 60);
		
		$now = time();
		
		
echo "<h3>$phrase[240] </h3>";

	 //print_r($_REQUEST);
	 
	 
	 //get branch info
	 	$sql = "select pc_branches.name as name, pc_branches.branchno as branchno from pc_branches, pc_computers where pc_branches.branchno = pc_computers.branch and pc_computers.pcno = '$pc'";
	 	
	
	 	
		$DB->query($sql,"bookpc.php");
		$row = $DB->get();
		$DB->query($sql,"bookpc.php");
		$branchname = $row["name"];
		$bid = $row["branchno"];
	
		$barcode = $_REQUEST["barcode"];
		
	   if (trim($_REQUEST["barcode"]) == "")
	 {
	 	$ERROR = "No barcode entered";
	 
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
$sql = "select pc_usage.useno as useno , timelimit from pc_usage, pc_bridge where pc_bridge.pcnum = '$pc' and pc_bridge.useno = pc_usage.useno
and web = 1";
$DB->query($sql,"session.php");
$row = $DB->get();
$type = $row["useno"] ;
$timelimit = $row["timelimit"] ;
	
	if (!isset($type))
	{
	 $ERROR = "Booking failed. Booking type not available on this computer.";
	}	

	
	if ($timelimit != 0)
			{
			//  check that the user is not exceeding daily time limit
			
			$result = $pcbookings->dailylimit($cardnumber,$timelimit,$type);
			if ($result == "error")
				{
				$ERROR =  "Booking failed. Daily usage limit reached.";
				//$failure = "$result";
				}
			
			
			}
	
	/*		
	 //check pc has usage
	$sql = "select count(*) as num from pc_bridge where  pcnum = '$pc' and useno = '$type'";
$DB->query($sql,"bookpc.php");
$row = $DB->get();
$num= $row["num"];
	 
if ($num == 0) { $ERROR = "Booking failed. Booking type not available on this computer.";}	 


*/
	 //check pc not out of order

$sql = "select * from pc_computers where pcno = '$pc'";
$DB->query($sql,"bookpc.php");
$row = $DB->get();
$outoforder = $row["outoforder"];
$pcname = $row["name"];


if ($outoforder == 1) { $ERROR = "$phrase[469]";}
	 
	 
	 	
	 
	 
	 
	 //check library hours
$hours = $pcbookings->OpeningHours($start,$end,FALSE,$pc,$phrase);
	 

	 
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
		$ERROR =  "$phrase[224] $phrase[914] ";
		}
		elseif ($limit['status'] == "2")
		{
		$ERROR =  "$phrase[224] $phrase[1118] ";
		}
	  	}
	  	
	 
	  	
		 //check computer available
	
	  	if (!isset($ERROR))
	  	{
		$sql = "select count(bookingno) as num from pc_bookings where pcno = '$pc' and (($start <= bookingtime and $end > bookingtime) or ($end > endtime and $start < endtime)  or ($start >= bookingtime and $end <= endtime)) and cancelled = '0' and finished = '0' ";
		$DB->query($sql,"bookpc.php");
		$row = $DB->get();
		$num = $row["num"];
	
		
		if ($num == 1) { $ERROR = "$phrase[440]";}
	 	}
	  	
	  	
	  	 		 //check patron not in session
	  		 //if in session cannot book another until 2 hours after this session ends
	if (!isset($ERROR))
	  	{
		$sql = "select * from pc_bookings where cardnumber = '$cardnumber' and $now >= bookingtime and $now < endtime and cancelled = '0' and finished = '0' ";
		$DB->query($sql,"bookpc.php");
		while ($row = $DB->get())
		{
		if ($start < $now + 7200)
			{$ERROR = "$phrase[934]";}
		
		}
	  	}
	
		
	 //display results
	 
	  	if (isset($ERROR))
	  	{
	  
	  	echo "<br><span style=\"color:red;font-size:1.5em\">$ERROR</span>	<br><br>";
	  	
	  	if (isset($limit) && in_array("status",$limit) && $limit['status'] == "exceeded")
	  	{
	  		echo "<a href=\"bookpc.php\">$phrase[34]</a>";
	  	}
	  	else 
	  	{
	  		echo "<a href=\"bookpc.php?event=book&start=$start&duration=$duration&bid=$bid&type=$type&pc=$pc\">$phrase[813]</a>";
	  	}
	
		
		}
		else
		{
		
			
			echo "<span style=\"font-size:2em;color:red\">Booking successfull!</span><br><br><br>";
			
			
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

	$DB->query($sql,"bookpc.php");
	
	
	
		$sql = "select count(bookingno) as num from pc_bookings where pcno = '$pc' and (($start <= bookingtime and $end > bookingtime) or ($end > endtime and $start < endtime)  or ($start >= bookingtime and $end <= endtime)) and cancelled = '0' and finished = '0' ";
		$DB->query($sql,"bookpc.php");
		$row = $DB->get();
		$num = $row["num"];
	
		
		if ($num == 1) {
			
				echo "<span style=\"color:red;font-size:1.5em\">$phrase[440]</span>	<br><br>";
		}

		else 
		{

                    $itemsout = '0';
    $insertsql = "INSERT INTO pc_bookings VALUES(NULL,'$name','', '$start','$bid', '$pc', '0', '$type', '$cardnumber', '$end','$ip','web','0','$now','','','0','0','2','0','0','0','$pin','$itemsout')";
	$DB->query($insertsql,"bookpc.php");
	$bookingno = $DB->last_insert();
	
	
		
		
	if ($DB->type == "mysql")
		{  	
		 $sql = "unlock tables";
		}
								
else
		{  	
		$sql = "commit";
		}
		
	$DB->query($sql,"bookpc.php");	
	
	
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
	
	$DB->query($sql,"bookpc.php");
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
	<br>";
		
	
			echo "You may commence your computer session by logging onto the computer with your PIN number.<br><br>";
	
		
	echo "<form action=\"bookpc.php\" method=\"post\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[34]\" id=\"focus\"></form>
		";
		
		}
	
	
		
		}
	 
}

elseif (isset($event) && $event == "cancelling")
{
	
	
		$time = $_REQUEST["time"];
	$date = $_REQUEST["date"];
	$branch = $_REQUEST["branch"];
	$barcode = substr($_REQUEST["barcode"],0,100);	
	
	   if (trim($_REQUEST["barcode"]) == "")
	 {
	 	$ERROR = "No barcode entered";
	 
	 }
	
	 	elseif ($PCAUTHENTICATION == "autoaccept")
	 	{
	 	$ERROR = "Cancellations not allowed";
	 	}
	 
		 elseif ($PCAUTHENTICATION != "autoaccept")
		{
		 	
		  if (trim($_REQUEST["password"]) == "")
	 {
	 	$ERROR = "No password entered";
	 
	 }
     else {          
	$password = substr($_REQUEST["password"],0,100);	
	
	
	include("../classes/AuthenticatePatron.php");

	$CHECK = new AuthenticatePatron($password,$barcode,$PREFERENCES,$DB,"pc");
	
	if ($CHECK->auth == "no")
	{ 
		$ERROR =  "
		<h3>$phrase[670]</h3>
		
		$CHECK->failure
		<br><br>
		<a href=\"bookpc.php?event=cancel&bookingno=$bookingno&time=$time&date=$date&branch=$branch\">$phrase[813]</a>"; 
	}
     }
		}
           
     if (isset($ERROR))
     {
     	echo $ERROR;
     }
            else 
            {

		//$barcode = $DB->escape(str_replace(" ", "", $_REQUEST["barcode"]));				
			

			$barcode =  $DB->escape(str_replace(" ", "", substr($_REQUEST["barcode"],0,100)));
		
			$sql = "update pc_bookings set cancelled = '1' where bookingno = '$bookingno' and cardnumber = '$barcode'";
					
			$DB->query($sql,"pc.php");
			
			  if (isset($PCDEBUG) && $PCDEBUG == "on")
					{
					pc_debug($DB,$bookingno,$sql);	
					}
			
			
			$sql = "select bookingtime, pcno from pc_bookings where bookingno = '$bookingno'";
			$DB->query($sql,"pc.php");
			$row = $DB->get();
			$start = $row["bookingtime"];
			$pc = $row["pcno"];
			
			if ($start < time() + 14400)
		{
		$pcbookings->Status($pc,$phrase);
		}
			
			echo "<br><br><b>$phrase[772]</b><br>
			<br><br>
		<form action=\"bookpc.php\" method=\"post\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[34]\" id=\"focus\"></form>
			
			";
						
				
				
	
            }
               }



elseif (isset($event) && $event == "check")
{
	
	
	echo "
	
	<form action=\"bookpc.php\" method=\"post\" style=\"width:30%;margin:0 auto\">

         <table style=\"margin-left:auto;margin-right:auto;border-style:none\"
cellspacing=\"0\" >   
 <tr><td align=\"right\"><b>$phrase[810]</b></td><td>
         <input type=\"text\" name=\"barcode\" id=\"focus\">
         </td></tr>    
        ";
         
        
                        if ($PCAUTHENTICATION != "autoaccept")
                        {
        echo "
 
        <tr><td align=\"right\"><b>$phrase[811]</b></td><td>
         <input type=\"password\" id=\"password\" name=\"password\">
         </td></tr>";
                        }
         
         echo "<tr><td></td><td align=\"left\">";
         
          if ($PCAUTHENTICATION == "autoaccept")
                        {
                         echo "
                         
                         <tr><td align=\"right\"></td><td>
         <input type=\"hidden\" name=\"password\" id=\"password\" value=\"password\">
         </td></tr>
     
        ";	
                        
                        }
         echo "<tr><td></td><td><input type=\"submit\" name=\"submit\" id=\"zz\" value=\"$phrase[812] \">
  
                <input type=\"hidden\" name=\"event\" value=\"checking\">
         </td></tr></table>
         

                  </form>";

}
elseif (isset($event) && $event == "checking")
{

	
	
	
		if (trim($_REQUEST["barcode"]) == "")
		 {
		 	echo "No barcode entered.
		 	<br><br>
		<a href=\"bookpc.php?event=check\">$phrase[813]</a>";
		 }
		 else
               {
     	 
	$barcode = substr($_REQUEST["barcode"],0,100);          
	

	
		 if ($PCAUTHENTICATION != "autoaccept")
		 {
		 	
		if (trim($_REQUEST["password"]) == "")
		 {
		 	echo "No password entered.
		 	<br><br>
		<a href=\"bookpc.php?event=check\">$phrase[813]</a>";
		 	$ERROR = "yes";
		 }
		 else 
		 {
		$password = substr($_REQUEST["password"],0,100);
	
	include("../classes/AuthenticatePatron.php");

	$CHECK = new AuthenticatePatron($password,$barcode,$PREFERENCES,$DB,"pc");
	//echo "$CHECK->auth";

	
	if ($CHECK->auth == "no")
	{ 
		echo "
		<h3>$phrase[812]</h3>
		
		$CHECK->failure
		<br><br>
		<a href=\"bookpc.php?event=check\">$phrase[813]</a>"; 
	$ERROR = "yes";
	}
		 }
		 }          
          if (!isset($ERROR))
            {
			$now = time();
				
			
	echo "<h2>Bookings for $_REQUEST[barcode]</h2>";
			$barcode =  $DB->escape(str_replace(" ", "", substr($_REQUEST["barcode"],0,100)));
		$sql = "select bookingno, bookingtime, endtime,pc_bookings.noshow as noshow, pc_usage.name as usename, pc_computers.name as pcname, pc_branches.name as branchname, pc_bookings.mode as mode, cancelled from pc_bookings, pc_usage, pc_computers, pc_branches where cardnumber = '$barcode' and endtime > '$now' and pc_bookings.pcusage = pc_usage.useno
		 and pc_bookings.branchid = pc_branches.branchno and pc_bookings.pcno = pc_computers.pcno order by bookingtime desc limit 25";
					
						$DB->query($sql,"pc.php");
						$num = $DB->countrows();
						
						if ($num == 0) {echo "<br><br>$phrase[732]";} else {echo "<table id=\"resulttable\" >
					 <tr><td><b>$phrase[185]</b></td><td><b>$phrase[186]</b></td>";
						if ($DISPLAYPCNAME == 1)
						{
						echo "<td><b>$phrase[452]</b></td>";
						}
						   echo "<td><b>$phrase[121]</b></td><td></td></tr>	
						";}
						while ($row = $DB->get())
						{
						 $bookingno = $row["bookingno"];
						$bookingtime = $row["bookingtime"];
						$endtime = $row["endtime"];
						$start = date("g:ia",$bookingtime);
						 if (strlen($start) == 6) { $start = "&nbsp;&nbsp;".$start;} 
						$date = strftime("%x",$bookingtime);
						$end = date("g:ia",$endtime);
						  if (strlen($end) == 6) { $end = "&nbsp;&nbsp;".$end;} 
						$branchname = $row["branchname"];
					
						$pcname = $row["pcname"];
					
						$cancelled = $row["cancelled"];
					
						$noshow = $row["noshow"];
						
						$displaytime = "$start -$end";
						
						echo "<tr";
						if ($cancelled == 1) { echo " class=\"grey\"";}
						echo "><td>$displaytime</td><td>$date</td>";
						
						if ($DISPLAYPCNAME == 1)
						{
						echo "<td>$pcname</td>";
						}
						echo "<td>$branchname</td><td>";
						if ($cancelled == 1) { echo "$phrase[152]";} 
						if ($PCAUTHENTICATION != "autoaccept" && $cancelled == 0)
						{
							$linktime = urlencode($displaytime);
							$linkdate = urlencode($date);
							$linkbranch = urlencode($branchname);
							echo "<a href=\"bookpc.php?event=cancel&bookingno=$bookingno&time=$linktime&date=$linkdate&branch=$linkbranch\">$phrase[670]</a>";
						
						}
					
						echo "</td></tr>";
						}
						if ($num != 0){echo "</table><br><br>";}
	
            }
               }
		

}
else {
    
    
echo "    


<form action=\"bookpc.php\" method=\"get\" id=\"searchform\" name=\"searchform\"  style=\"width:90%;margin: 0 auto\">


<div id=\"panel\" ><h2 style=\"text-align:center;color:grey\">$phrase[751]</h2><p style=\"width:40%;margin:0 auto;text-align:left\">";


	foreach($branch as $branchno => $name)
              {	
 echo "<button onclick=getPCs('$branchno') style=\"margin:0.2em\">$name</button> <br>";
	  }



echo "</p>
</div>
";

    
    
}











?>
</div>
</body>
</html>