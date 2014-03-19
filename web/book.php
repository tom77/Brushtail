<?php




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
<h1>$phrase[1115]</h1></div>

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

 
 
 $DB->query($sql,"pcweb.php");
$branchcount = 0;
while ($row = $DB->get())
      {
	
      $branchno = $row["branchno"];
      $branch[$branchno] = $row["bname"];
      $branchcount++;
      }
    
	 	 $sql = "select * from pc_usage where web = 1 order by name";
	 	
		$DB->query($sql,"pcweb.php");
		$typecounter = 0;
	
while ($row = $DB->get())
      {

     
      $useno = $row["useno"];
      $types[$useno] = $row["name"];
      $typecounter++;
	  }	    

	  echo "<div id=\"navlinks\">";
          
          
          if (isset($_REQUEST["default"])) {$_SESSION["defaultLocation"] = $_REQUEST["default"];}
          
          
          
          if (isset($_SESSION["defaultLocation"])) {$fullSuffix  = "?default=" . $_SESSION["defaultLocation"];
          $partSuffix  = "&default=" . $_SESSION["defaultLocation"];
          
               
          
          } else {$fullSuffix  = ""; $partSuffix  = "";}
          
          
	  
if (!isset($event))

{ echo "<a href=\"book.php$fullSuffix\">$phrase[859]</a> | <a href=\"book.php?event=check$partSuffix\">$phrase[812]</a>";} 

elseif (isset($event) && ($event == "book" || $event == "booking"))
{
echo "<a href=\"book.php$fullSuffix\">$phrase[859]</a> | <a href=\"book.php?event=check$partSuffix\">$phrase[812]</a>";	
}
    
elseif ((isset($event) && ($event == "check" || $event == "checking"   || $event == "cancel" || $event == "cancelling")))
{
	echo "<a href=\"book.php$fullSuffix\">$phrase[859]</a> | <a href=\"book.php?event=choose$partSuffix\">$phrase[807]</a>";
}

elseif ((isset($event) && ( $event == "list" || $event == "choose")))
{
echo "<a href=\"book.php$fullSuffix\">$phrase[859]</a> | <a href=\"book.php?event=check$partSuffix\">$phrase[812]</a>";

}



		echo "</div><p id=\"footer\"></p></div>";

		
	
		



if (isset($event) && $event == "cancel")
{
	
$time = $_REQUEST["time"];
	$date = $_REQUEST["date"];
	$branch = $_REQUEST["branch"];
	
	echo "
	
	<form action=\"book.php\" method=\"post\" style=\"width:60%;margin:0 auto\">
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


elseif (isset($event) && $event == "cancelling")
{
	
	
		$time = $_REQUEST["time"];
	$date = $_REQUEST["date"];
	$branch = $_REQUEST["branch"];
	$barcode = substr($_REQUEST["barcode"],0,100);	
	
	   if (trim($_REQUEST["barcode"]) == "")
	 {
	 	$ERROR = "You cannot login unless you enter a login id and password";
	 
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
            
            
            $link = "book.php?event=cancel&bookingno=$bookingno&time=$time&date=$date&branch=$branch" . $partSuffix;
            
		$ERROR =  "
		<h3>$phrase[670]</h3>
		
		$CHECK->failure
		<br><br>
		"; 
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
		<form action=\"book.php$fullSuffix\" method=\"post\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[34]\" id=\"focus\"></form>
			
			";
						
				
				
	
            }
               }



elseif (isset($event) && $event == "check")
{
	
	
	echo "
	
	<form action=\"book.php\" method=\"post\" style=\"width:60%;margin:0 auto\">

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
		<a href=\"book.php?event=check$partSuffix\">$phrase[813]</a>";
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
		<a href=\"book.php?event=check$partSuffix\">$phrase[813]</a>";
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
		<a href=\"book.php?event=check$partSuffix\">$phrase[813]</a>"; 
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
                                                        $link = "book.php?event=cancel&bookingno=$bookingno&time=$linktime&date=$linkdate&branch=$linkbranch" . $partSuffix;
							echo "<a href=\"$link\">$phrase[670]</a>";
						
						}
					
						echo "</td></tr>";
						}
						if ($num != 0){echo "</table><br><br>";}
	
            }
               }
		

}

else {
    

echo "    


<form action=\"book.php\" method=\"get\" id=\"searchform\" name=\"searchform\"  style=\"width:90%;margin: 0 auto\">


<div id=\"panel\" ><h2 style=\"text-align:center;color:grey\">$phrase[751]</h2><p style=\"width:30%;margin:0 auto;text-align:left;\">";


	foreach($branch as $branchno => $name)
              {	
            
if (isset($BOOKBY) && $BOOKBY == "PC")   
{
 echo "<button onclick=\"getPCs('$branchno')\" style=\"margin:0.2em\">$name</button> <br>";    
}
 else
 {
  echo "<button onclick=\"getTypes('$branchno')\" style=\"margin:0.2em\">$name</button> <br>";   
 }
            
 
	  }



echo "</p>
</div>
";

    if (isset($_REQUEST["default"]))
     {
 $l = $_REQUEST["default"]; 
 
 if (isset($BOOKBY) && $BOOKBY == "PC")   
    {
     echo "<script>
   
     
       window.onload=getPCs('$l') ; 
     </script>";
    }
else {
    echo "<script>
       window.onload=getTypes('$branchno');
    </script>";
     
     }   
     } 
    
}











?>
</div>
</body>
</html>