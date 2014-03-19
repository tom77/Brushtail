<?php




include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	


if (isset($_REQUEST["m"]))
	{	
	
	if (isinteger($_REQUEST["m"]))
		{	
		$m = $_REQUEST["m"];	
		$access->check($m);
	
	if ($access->thispage < 3)
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
	}
else {
	$ERROR  =  $phrase[72];
}	

$integers[] = "noshow";
$integers[] = "startday";
$integers[] = "startmonth";
$integers[] = "startyear";
$integers[] = "endday";
$integers[] = "endmonth";
$integers[] = "endyear";
$integers[] = "closureid";
$integers[] = "mintime";
$integers[] = "maxtime";
$integers[] = "defaulttime";
$integers[] = "powerview";
$integers[] = "stats";
$integers[] = "useno";
$integers[] = "pcnum";
$integers[] = "displayorder";
$integers[] = "branchno";
$integers[] = "id";
$integers[] = "print";
$integers[] = "flexible";
$integers[] = "warning_1";
$integers[] = "warning_2";
$integers[] = "web";
$integers[] = "extend";
$integers[] = "bookinginterval";
$integers[] = "weblimit";
$integers[] = "lockout";
$integers[] = "displaypc";
$integers[] = "displaytype";
$integers[] = "clientbooking";
$integers[] = "createpin";
$integers[] = "logoff";
$integers[] = "timelimit";
$integers[] = "cleanup";

foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer $value $_REQUEST[$value]";}
	else {$$value = $_REQUEST[$value];}
	}
}	

if (isset($bookinginterval) && $bookinginterval < 0) {$bookinginterval = 60;}

if (isset($ERROR))
	{
	echo "$ERROR";
	}
else 
	{

	
	$hourdisplay = array('6 am', '7 am', '8 am','9 am','10 am','11 am', '12', '1 pm', '2 pm', '3 pm', '4 pm', '5pm',
		'6 pm', '7 pm', '8 pm', '9 pm', '10 pm', '11 pm');
$hoursvalue = array('06', '07', '08','09','10','11', '12', '13', '14', '15', '16', '17',
		'18', '19', '20', '21', '22', '23');
$monthsvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12');
$daysvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12', '13', '14', '15', '16', '17',
		'18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31');
		
		
	
		//display dynamic module adminstration page
//background colours array
$colours[] = "none";
$colours[] = "#ffffff";
$colours[] = "#CCE7FF";
$colours[] = "#FFFFE5";
$colours[] = "#FFFFCC";
$colours[] = "#ffcc99";
$colours[] = "FFC2C2";
$colours[] = "#E5FFF3";
$colours[] = "#FFCCB3";
$colours[] = "#EDDEDE";
$colours[] = "#ccffcc";
$colours[] = "#E6FFCC";
$colours[] = "#ffcccc";
$colours[] = "#CCE5E5";
$colours[] = "#BBDDBB";
$colours[] = "#E6E6E6";


$pinoptions[0] = $phrase[881]; //Do not create PIN
$pinoptions[1] = $phrase[882]; //"Create PIN if barcode field empty";
$pinoptions[2] = $phrase[883]; //"Automatically create barcode";



//booking link colours array

// this uses default link colour of style sheet 
$linkcolour[] = "default"; 

$linkcolour[] = "#3300ff";
$linkcolour[] = "#006699";
$linkcolour[] = "#cc3333";
$linkcolour[] = "#ff6600";
$linkcolour[] = "#666633";
$linkcolour[] = "#007038";
$linkcolour[] = "#660066";
$linkcolour[] = "#660033";
$linkcolour[] = "#330000";





//array for minimum time drop down menu
$min[]= 5;
$min[]= 10;
$min[]= 15;
$min[]= 30;
$min[]= 60;
$min[]= 10;
$min[]= 15;
$min[]= 30;
$min[]= 60;

//array for maximum time drop down menu
$max[]= 5;
$max[]= 10;
$max[]= 15;
$max[]= 30;
$max[]= 60;
$max[]= 90;
$max[]= 120;
$max[]= 150;
$max[]= 180;
$max[]= 210;
$max[]= 240;
$max[]= 270;
$max[]= 300;
$max[]= 330;
$max[]= 360;
$max[]= 390;
$max[]= 420;
$max[]= 450;
$max[]= 480;
$max[]= 510;
$max[]= 540;
$max[]= 570;
$max[]= 600;
$max[]= 700;
$max[]= 800;
$max[]= 900;
$max[]= 1000;
$max[]= 1200;


 $sql = "select name from modules where m = \"$m\"";
	$DB->query($sql,"pcadmin.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);

echo "<div style=\"text-align:center\"><br><h1 class=\"red\">$modname</h1>";


echo " <a href=\"pcadmin.php?m=$m&amp;event=types\">$phrase[476]</a> | <a href=\"pcadmin.php?m=$m\">$phrase[410]</a>  | <a href=\"pcadmin.php?m=$m&amp;event=banned\">$phrase[477]</a>  | <a href=\"pcadmin.php?m=$m&amp;event=closures\">$phrase[478]</a> 
  | <a href=\"pcadmin.php?m=$m&amp;event=pc_stats\">$phrase[271]</a>";


if (isset($_REQUEST["reorder"]))
	{
	//reorders coloumns on pc booking sheet
	$reorder = explode(",",$_REQUEST["reorder"]);
	
	foreach ($reorder as $index => $value)
		{
		if (isinteger($index) && isinteger($value))
		{
		$sql = "update pc_computers set displayorder = \"$index\" WHERE pcno = \"$value\"";	
		$DB->query($sql,"pcadmin.php");
		}				
		}
	
	
	
	}	

        
        if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deleteimage")
   {	
	
		
	
        if ($PREFERENCES["storage"] == "file")
         {
         $filepath = $PREFERENCES['docdir']."/pc/".$pcnum ;  
        // echo " deleted $filepath";
         delDir($filepath);
         } 

 $sql = "delete from images where page = '$pcnum' and modtype = 't'";	
 //echo $sql;
$DB->query($sql,"tvedit.php");

	
	
   }
        
        
 if(isset($_REQUEST["update"]) && $_REQUEST["update"] == "editbranch")
	{
		
	$opening = $_REQUEST["opening"];
	$closing = $_REQUEST["closing"];
	$branchopen = $_REQUEST["branchopen"];
	//	print_r($opening);
	//print_r($branchopen);
	
	include("../classes/PcBookings.php");
	$pcbookings = new PcBookings($DB);
	
	
    $sql = "delete from branch_openinghours where branchid = '$branchno'";
    
    $DB->query($sql,"pcadmin.php");
	
     $counter = 1;
    foreach ($opening  as $index => $openinghour)
            {
             //closingtime cannot be earlier than opening
            if ($openinghour >  $closing[$index])
               {
               $closing[$index] = $openinghour;
           		}
				
			
           	
		   $openinghour = $DB->escape($openinghour);
		   $closing[$index] = $DB->escape($closing[$index]);
		  $branchopen[$index] = $DB->escape($branchopen[$index]);
		
		
           	
             $sql = "INSERT INTO branch_openinghours VALUES('$branchno','$openinghour','$closing[$index]','$index','$branchopen[$index]')";
            // echo "<br>$sql <br>";
             
             $DB->query($sql,"pcadmin.php");
	
              $counter++;
        }
		$branchname = $DB->escape($_REQUEST["branchname"]);
		$telephone = $DB->escape($_REQUEST["telephone"]);
		$earlyfinish = $DB->escape($_REQUEST["earlyfinish"]);
        $sql = "update pc_branches set name = '$branchname', pc_booking_interval = '$bookinginterval', telephone = '$telephone', earlyfinish = '$earlyfinish' where branchno ='$branchno'";

             $DB->query($sql,"pcadmin.php");
             
             
             $sql2 = "select pcno from pc_computers where branch = '$branchno'";
     
              $DB->query($sql2,"pcadmin.php");
              while ($row = $DB->get())
              {
				$pcs[] = $row["pcno"] ;
				      
              }
        		
			if (isset($pcs))
				{
     			foreach ($pcs as $index => $value)
             		 {
              		$pcbookings->Status($value,$phrase);
              		
              		}
				}
              
              
        
	}
	
	//print_r($_REQUEST);
	
   if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addpc")
    {
	$name = $DB->escape($_REQUEST["name"]);
	$ip = $DB->escape(trim($_REQUEST["ip"]));
	$secret = $DB->escape($_REQUEST["secret"]);
        $itemsout = $DB->escape($_REQUEST["itemsout"]);
	if (isset($_REQUEST["default_usage"]))
	{	$default_usage = $DB->escape($_REQUEST["default_usage"]);} else {$default_usage = '';}
	$poll_client = $DB->escape($_REQUEST["poll_client"]);
	

	
	
	if ($name == "")
	{
		$WARNING = "$phrase[479]";
	}
	elseif ($default_usage == "")
	{
		$WARNING = "$phrase[943]";
		
	}
	else 
	{
	

		
	$colour = $DB->escape($_REQUEST["colour"]);
	
	if (isset($_REQUEST["none"])) {$colour = "none";}
	
      $sql = "INSERT INTO pc_computers VALUES(NULL,'$name','$branchno','0','','0','$colour','1','$ip',
      '$displaypc','$secret','1','$default_usage','$poll_client','$itemsout')";
      $DB->query($sql,"pcadmin.php");
echo $sql;
   
      $pcno = $DB->last_insert();
      
		if (isset($_REQUEST["auseno"])){
      foreach ($_REQUEST["auseno"] as $index => $value)
              {
			  if (isinteger($value))
			  {
              $sql = "INSERT INTO pc_bridge VALUES('$pcno','$value')";
      $DB->query($sql,"pcadmin.php");
			  }
	
          }
		  }
	}
  }
  
  if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addlocation")
   {
   	if ($_REQUEST["bname"] == "")
   	{
   		$WARNING = "$phrase[480]";
   	}
   	else {
	$bname = $DB->escape($_REQUEST["bname"]);
	$telephone = $DB->escape($_REQUEST["telephone"]);
	$earlyfinish = $DB->escape($_REQUEST["earlyfinish"]);
	$sql = "INSERT INTO pc_branches VALUES(NULL,'$bname','$m','$bookinginterval','$telephone','$earlyfinish')";
	$DB->query($sql,"pcadmin.php");
	
   	}
		
	}
	
	
	
  if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delbranch")
  {
  	
 	
  	
  	$sql = "delete from pc_branches where branchno = '$branchno'";
  	$DB->query($sql,"menu.php");
  
  	$DB->tidy("branches");
  	
  	$sql = "delete from branch_openinghours where branchid = '$branchno'";
  	$DB->query($sql,"menu.php");
  	
  
	$DB->tidy("branch_openinghours");
  	
  	$sql = "delete from pc_bookings where branchid = '$branchno'";
  	$DB->query($sql,"pcadmin.php");
  	
 $DB->tidy("pc_bookings");
  	
  	
  	
  }
  
    if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "editpc")
 
    {
    	//print_r($_REQUEST);
	$pcname = $DB->escape($_REQUEST["name"]);
	$colour = $DB->escape($_REQUEST["colour"]);

	$ip = $DB->escape(trim($_REQUEST["ip"]));
	$displaypc = $DB->escape($_REQUEST["displaypc"]);
	$secret = $DB->escape($_REQUEST["secret"]);
	$default_usage = $DB->escape($_REQUEST["default_usage"]);
	$poll_client = $DB->escape($_REQUEST["poll_client"]);
        $itemsout = $DB->escape($_REQUEST["itemsout"]);
	
	if (isset($_REQUEST["none"])) {$colour = "none";}
	
	  $sql = "update pc_computers set name='$pcname', colour='$colour', flexible = '1', ip = '$ip',
	   displaypc = '$displaypc', secret = '$secret', default_usage = '$default_usage', poll_client = '$poll_client'
        , itemsout = '$itemsout' where pcno = '$pcnum'";
    $DB->query($sql,"pcadmin.php");
	

     $sql = "delete from pc_bridge where pcnum = \"$pcnum\"";
   $DB->query($sql,"pcadmin.php");
	

	
	
	if (isset($_REQUEST["auseno"]))
	{
     foreach ($_REQUEST["auseno"] as $index => $value)
              {
			 if (isinteger($value))
			 {
              $sql = "INSERT INTO pc_bridge VALUES('$pcnum','$value')";
              $DB->query($sql,"pcadmin.php");
          
			 }
              }

          }
        //  print_r($_FILES);
          
          	 if (isset($_FILES["upload"]["size"][0] ) && $_FILES["upload"]["size"][0] > 0)
	 
	 {
	 	//echo "uploading image  ";
	 	upload($m,$pcnum,'0',$PREFERENCES,$DB,"pcimage",$ip,$phrase);	
	 
	 }
          
          
          
          
          
          
	}


if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletepc")	  

    {


     $sql = "delete from pc_bridge where pcnum = \"$pcnum\"";
    $DB->query($sql,"pcadmin.php");
	
	
	$sql = "delete from pc_computers where pcno = \"$pcnum\"";
   $DB->query($sql,"pcadmin.php");
	
   
     if ($PREFERENCES["storage"] == "file")
         {
         $filepath = $PREFERENCES['docdir']."/pc/".$pcnum ;  
        // echo " deleted $filepath";
         @delDir($filepath);
         } 

 $sql = "delete from images where page = '$pcnum' and modtype = 't'";	
 //echo $sql;
$DB->query($sql,"tvedit.php");
   

    }

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deleteban")
    {


     $sql = "delete from pc_blacklist where id = \"$id\"";
    $DB->query($sql,"pcadmin.php");
	}
	
 if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addban")
	{

	$barcode = trim($_REQUEST["barcode"]);
	$barcode = $DB->escape(str_replace(" ", "", $barcode)); 
	
	
	
	$starttime =  date("Y-m-d", mktime(0, 0, 0, $_REQUEST["startmonth"], $_REQUEST["startday"], $_REQUEST["startyear"]));
	$endtime =  date("Y-m-d", mktime(0, 0, 0, $_REQUEST["endmonth"], $_REQUEST["endday"], $_REQUEST["endyear"]));
	
		
	
	
     if ($barcode == "")
       {
	   $WARNING = "$phrase[481]";
		
	   }
	  elseif ($starttime >= $endtime)
	  	{
		$WARNING = "$phrase[482]";
		
		}
	else {
	   
	   
	$last_name = $DB->escape($_REQUEST["last_name"]);
	$first_name = $DB->escape($_REQUEST["first_name"]);
	$barcode = $DB->escape($_REQUEST["barcode"]);
	$reason = $DB->escape($_REQUEST["reason"]);
	
	
	
	
	$sql = "INSERT INTO pc_blacklist VALUES(NULL,'$first_name','$last_name','$barcode','$reason','$starttime','$endtime','$m')";
	

  $DB->query($sql,"pcadmin.php");
	
	}
	}
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addclosure")


	{

	$starttime =  date("Y-m-d", mktime(0, 0, 0, $startmonth, $startday, $startyear));
	
	
	
	$endtime =  date("Y-m-d", mktime(0, 0, 0, $endmonth, $endday, $endyear));
	
   if ($starttime > $endtime)
	  	{
		$WARNING = "$phrase[482]";
		
		}
	else {
	   
	   
	
	
	$reason = $DB->escape($_REQUEST["reason"]);
	
	
	
	
	$sql = "INSERT INTO pc_closures VALUES(NULL,'$branchno','$reason','$starttime','$endtime')";


   $DB->query($sql,"pcadmin.php");
	
	}
	}
	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deleteclosure")
    {
	
    $sql = "delete from pc_closures where id = \"$closureid\"";
    $DB->query($sql,"pcadmin.php");
	
    }
	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updateban")

	{

	$barcode = trim($_REQUEST["barcode"]);
	$barcode = str_replace(" ", "", $barcode); 
	
	
	$starttime =  date("Ymd", mktime(0, 0, 0, $_REQUEST["startmonth"], $_REQUEST["startday"], $_REQUEST["startyear"]));
	
	
	
 	
	$endtime =  date("Ymd", mktime(0, 0, 0, $_REQUEST["endmonth"], $_REQUEST["endday"], $_REQUEST["endyear"]));
	
	
     if ($barcode == "")
       {
	   $WARNING = "$phrase[481]";
		//error($error);
	   }
	  elseif ($starttime > $endtime)
	  	{
		$WARNING = "$phrase[482]";
		//error($error);
		}
	else {
	   
	   
	$last_name = $DB->escape($_REQUEST["last_name"]);
	$first_name = $DB->escape($_REQUEST["first_name"]);
	$barcode = $DB->escape($barcode);
	$reason = $DB->escape($_REQUEST["reason"]);


	$sql = "update pc_blacklist set first_name = '$first_name', last_name ='$last_name', barcode ='$barcode', reason = '$reason', date_blocked = '$starttime', date_finish ='$endtime' where id = '$id'";

   $DB->query($sql,"pcadmin.php");

	}
	}
	
 if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "editclosure")
	{

	
	$starttime =  date("Y-m-d", mktime(0, 0, 0, $startmonth, $startday, $startyear));
	
	
 	
	$endtime =  date("Y-m-d", mktime(0, 0, 0, $endmonth, $endday, $endyear));
	
	
    if ($starttime > $endtime)
	  	{
		$WARNING= "$phrase[482]";
	
		}
	else {
	   
	   
	
	$reason = $DB->escape($_REQUEST["reason"]);


	$sql = "update pc_closures set branch = '$branchno', reason = '$reason', date_blocked = '$starttime', date_finish ='$endtime' where id = '$closureid'";

   $DB->query($sql,"pcadmin.php");
	
	}
	}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "pc_stats")
    {
    	
    $enabled = $DB->escape($_REQUEST["enabled"]);
	$address = $DB->escape($_REQUEST["address"]);
    	
	$sql = "delete from pc_emailreport where m = '$m'";
      $DB->query($sql,"pcadmin.php");	
      
      $sql = "insert into pc_emailreport values ('$m','$enabled','$address')";

       $DB->query($sql,"pcadmin.php");
    }


if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addtype")
    {

     if ($_REQUEST["fee"] == "")
        {
     $fee = 0;
    } else { $fee = $_REQUEST["fee"]; } 
	$fee = $DB->escape($_REQUEST["fee"]);
	$name = $DB->escape($_REQUEST["name"]);
	$usecolour = "#" . $DB->escape($_REQUEST["usecolour"]);
	$telephone = $DB->escape($_REQUEST["telephone"]);
	$warning_1_text = $DB->escape($_REQUEST["warning_1_text"]);
	$warning_2_text = $DB->escape($_REQUEST["warning_2_text"]);
        $weblimitperday = $DB->escape($_REQUEST["weblimitperday"]);
  
	if ($lockout < 0) {$lockout = 0;}
	
	
	
	
	if ($defaulttime > $maxtime)
	{
	$WARNING = "$phrase[483]";
	
	}
	elseif ($defaulttime < $mintime)
	{
	$WARNING = "$phrase[484]";

	}
	else
	{
		$sql = "INSERT INTO pc_usage VALUES(NULL,'$fee','$name','$mintime','$maxtime','$powerview','$stats','$defaulttime',
            '$usecolour','$print','$m','$telephone','$web','$warning_1','$warning_2','$warning_1_text','$warning_2_text','$extend',
            '$weblimit','$noshow','$lockout','$displaytype','$clientbooking','$createpin','$timelimit','$weblimitperday','$logoff','$cleanup')";

	}



    	$DB->query($sql,"pcadmin.php");

	}

if (isset($_REQUEST["updatetype"]))
		{
		
		if ($mintime > $maxtime)
			{
			$WARNING = "$phrase[485]";
			
			}
		
		elseif ($defaulttime > $maxtime)
			{
			$WARNING = "$phrase[486]";
		
			}
		elseif ($defaulttime < $mintime)
			{
			$WARNING = "$phrase[487]";
			
			}
		else
			{
					$fee = $DB->escape($_REQUEST["fee"]);
				$uname = $DB->escape($_REQUEST["uname"]);
				$usecolour = "#" .$DB->escape($_REQUEST["usecolour"]);
				$telephone = $DB->escape($_REQUEST["telephone"]);
				$warning_1_text = $DB->escape($_REQUEST["warning_1_text"]);
				$warning_2_text = $DB->escape($_REQUEST["warning_2_text"]);
                                $weblimitperday = $DB->escape($_REQUEST["weblimitperday"]);
			
			
				if ($lockout < 0) {$lockout = 0;}
			
				
			  	$sql = "update pc_usage set name = \"$uname\", fee=\"$fee\", mintime=\"$mintime\", maxtime=\"$maxtime\", 
                                power=\"$powerview\", stats=\"$stats\", defaulttime = \"$defaulttime\", usecolour = \"$usecolour\", 
                                print = '$print', telephone = '$telephone' , web = '$web', warning_1 = '$warning_1', warning_2 = '$warning_2', 
                                warning_1_text = '$warning_1_text', warning_2_text = '$warning_2_text', extend = '$extend',  
                                weblimit = '$weblimit', noshow = '$noshow', lockout = '$lockout', displaytype = '$displaytype', 
                                clientbooking = '$clientbooking', createpin = '$createpin', timelimit = '$timelimit',
                                weblimitperday = '$weblimitperday',logoff = $logoff, cleanup = $cleanup where useno=\"$useno\"";
                                
                          
                               // echo "$sql";
				
				$DB->query($sql,"pcadmin.php");

				$sql = "delete from pc_status";	
				$DB->query($sql,"pcadmin.php");
				
				
			}

		}

		

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletetype")
		{
	
  		$sql = "delete from pc_usage where useno=\"$useno\"";
		$DB->query($sql,"pcadmin.php");
	
		
		$sql = "delete from pc_bridge where useno=\"$useno\"";
		$DB->query($sql,"pcadmin.php");
	
		}
		
if (isset($WARNING))

{
	warning($WARNING);
}
 
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletepc")
	{
	
	echo "<h2>Computers</h2><br><b>$phrase[14]</b><br><br> $_REQUEST[name]<br><br>
	<a href=\"pcadmin.php?m=$m&amp;pcnum=$pcnum&amp;update=deletepc&amp;event=viewpcs&amp;branchno=$branchno\">$phrase[12]</a> | <a href=\"pcadmin.php?m=$m&amp;event=viewpcs&amp;branchno=$branchno\">$phrase[13]</a>";
		
	}
  
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "pc_stats")
       {
       	echo "<h2>$phrase[271]</h2>";
       	
       	$sql = "select enabled, address from pc_emailreport where m = '$m'";
       	$DB->query($sql,"pcadmin.php");
       	$row = $DB->get();
    	$enabled = $row["enabled"];
    	$address = $row["address"];
    	
    	echo "
    	<form action=\"pcadmin.php\" method=\"get\" >
    	<table >
    	<tr><td class=\"label\" style=\"width:50%\">
    	$phrase[951] </td><td>
    	<select name=\"enabled\">
    	<option value=\"0\">$phrase[13]</option>
    	<option value=\"1\"";
    	if ($enabled == 1) {echo " selected";}
    	echo ">$phrase[12]</option>
    	</select></td></tr>
    	<tr><td  class=\"label\">
 	$phrase[259] </td><td>
    <input type=\"text\" name=\"address\" value=\"$address\" size=\"60\" maximum=\"250\"></td></tr>
      <input type=\"hidden\" name=\"m\" value=\"$m\">
      <input type=\"hidden\" name=\"update\" value=\"pc_stats\">
    
    <tr><td></td><td>
    <input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
    	</td></tr></table>
    	</form>
    	
    	";
       	
       	
       	
       }
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editbranch")
       {
     
       
       //array of time values to create drop down  time menu
       $optionlabel[]= " 6.00 am";  $optionvalue[] = "060000";
       $optionlabel[]= " 6.30 am";  $optionvalue[] = "063000";
       $optionlabel[]= " 7.00 am";  $optionvalue[] = "070000";
       $optionlabel[]= " 7.30 am";   $optionvalue[] = "073000";
       $optionlabel[]= " 8.00 am";  $optionvalue[] = "080000";
       $optionlabel[]= " 8.30 am"; $optionvalue[] = "083000";
       $optionlabel[]= " 9.00 am"; $optionvalue[] = "090000";
       $optionlabel[]= " 9.30 am";  $optionvalue[] = "093000";
       $optionlabel[]= "10.00 am";   $optionvalue[] = "100000";
       $optionlabel[]= "10.30 am"; $optionvalue[] = "103000";
       $optionlabel[]= "11.00 am";  $optionvalue[] = "110000";
       $optionlabel[]= "11.30 am";  $optionvalue[] = "113000";
       $optionlabel[]= "12.00 pm";  $optionvalue[] = "120000";
       $optionlabel[]= "12.30 pm";   $optionvalue[] = "123000";
       $optionlabel[]= " 1.00 pm";   $optionvalue[] = "130000";
       $optionlabel[]= " 1.30 pm";  $optionvalue[] = "133000";
       $optionlabel[]= " 2.00 pm"; $optionvalue[] = "140000";
       $optionlabel[]= " 2.30 pm";   $optionvalue[] = "143000";
       $optionlabel[]= " 3.00 pm"; $optionvalue[] = "150000";
       $optionlabel[]= " 3.30 pm"; $optionvalue[] = "153000";
       $optionlabel[]= " 4.00 pm";   $optionvalue[] = "160000";
       $optionlabel[]= " 4.30 pm";  $optionvalue[] = "163000";
       $optionlabel[]= " 5.00 pm";   $optionvalue[] = "170000";
       $optionlabel[]= " 5.30 pm"; $optionvalue[] = "173000";
       $optionlabel[]= " 6.00 pm";  $optionvalue[] = "180000";
       $optionlabel[]= " 6.30 pm";  $optionvalue[] = "183000";
       $optionlabel[]= " 7.00 pm"; $optionvalue[] = "190000";
       $optionlabel[]= " 7.30 pm"; $optionvalue[] = "193000";
       $optionlabel[]= " 8.00 pm";  $optionvalue[] = "200000";
       $optionlabel[]= " 8.30 pm";  $optionvalue[] = "203000";
       $optionlabel[]= " 9.00 pm";   $optionvalue[] = "210000";
       $optionlabel[]= " 9.30 pm";   $optionvalue[] = "213000";
       $optionlabel[]= " 10.00 pm";  $optionvalue[] = "220000";
       $optionlabel[]= " 10.30 pm";  $optionvalue[] = "223000";
       $optionlabel[]= " 11.00 pm";   $optionvalue[] = "230000";
       
       $option = "";
       foreach ($optionlabel as $index => $value)
               {
            $option .= "<option value=\"$optionvalue[$index]\">$value</option>";
            

           }


       
     /*
       $weekday[] = "Sunday";
       $weekday[] = "Monday";
       $weekday[] = "Tuesday";
       $weekday[] = "Wednesday";
       $weekday[] = "Thursday";
       $weekday[] = "Friday";
       $weekday[] = "Saturday";
       */

  //days of week array
$weekdays[0] = $phrase[425];
$weekdays[1] = $phrase[419];
$weekdays[2] = $phrase[420];
$weekdays[3] = $phrase[421];
$weekdays[4] = $phrase[422];
$weekdays[5] = $phrase[423];
$weekdays[6] = $phrase[424];
       
       
        
		     $sql = "select name,earlyfinish, pc_booking_interval,telephone from pc_branches where branchno = \"$branchno\"";
			$DB->query($sql,"pcadmin.php");
			$row = $DB->get();
    		 $name = $row["name"];
    		 $telephone = $row["telephone"];
    		 $bookinginterval= $row["pc_booking_interval"];
    		 $earlyfinish= $row["earlyfinish"];
    		 
    		 
      echo "<h2>$name</h2>";
     
        $sql = "select * from branch_openinghours where branchid = \"$branchno\" order by day";
$DB->query($sql,"pcadmin.php");
$num = $DB->countrows();	


while ($row = $DB->get())
      {

     $opening[] = $row["openinghour"];
     $closing[] = $row["closinghour"];
     $day[]  = $row["day"];
     $open[]  = $row["open"];
	
	
  }

      echo "<form action=\"pcadmin.php\" method=\"post\" >
     <table style=\"margin-left:auto;margin-right:auto;width:80%\">
     
     <tr><td class=\"formlabels\"> <b>$phrase[488]</b> </td><td><input type=\"text\" name=\"branchname\" value=\"$name\" size=\"60\" maxlength=\"100\"></td></tr>
      
       <tr><td class=\"formlabels\"> <b>$phrase[132]</b> </td><td><input type=\"text\" name=\"telephone\" value=\"$telephone\" size=\"60\" maxlength=\"100\"></td></tr>
   
     <tr><td class=\"formlabels\"> <b>$phrase[726]</b></td><td class=\"input\">
     	  <input type=\"text\" name=\"bookinginterval\" size=\"3\" maxlength=\"3\" value=\"$bookinginterval\"></td></tr>
	  
     	  <tr><td class=\"formlabels\"> <b>$phrase[927]</b></td><td class=\"input\">
     	  <input type=\"text\" name=\"earlyfinish\" size=\"1\" maxlength=\"1\" value=\"$earlyfinish\"></td></tr>
	  
	  
	  </table>
	 
      <table class=\"colourtable\"  style=\"margin-left:auto;margin-right:auto\" cellpadding=\"5\" >
      <tr class=\"accent\"><td><b>$phrase[209]</b></td><td><b>$phrase[489]</b></td><td><b>$phrase[490]</b></td><td><b>$phrase[491]</b></td></tr>";

      //cycle through days of week to display opening hours
      foreach ($weekdays as $daynum => $dayname)
      
              {



                
                  $addrow = "yes";
				  
				  if (isset($day))
				{
				
                  foreach($day as $i => $dayofweek)
                     {
                       if ($daynum == $dayofweek)
                         {
                         $addrow = "no";
                          echo "<tr";
                          if ($open[$daynum] == 0)
                             { echo " style=\"background:#cccccc\"";}

                          echo "><td align=\"left\"> <b>$dayname</b></td><td>
                          <select name=\"opening[$daynum]\">";
                                foreach ($optionvalue as $index => $time)
                                        {
                                     if ($time == $opening[$daynum])
                                               {
                                               echo "<option value=\"$optionvalue[$index]\"> $optionlabel[$index]";
                                                }
                                        }


                          echo "</option>$option</select>
                          </td><td><select name=\"closing[$daynum]\">";


                          foreach ($optionvalue as $index => $time)
                                        {
                                     if ($time == $closing[$daynum])
                                        {
                                        echo "<option value=\"$optionvalue[$index]\"> $optionlabel[$index]";
                                        }
                                    }

                          echo "</option>$option</select></td><td>";
                            if ($open[$i] == 1)
                                      {
                           //library open
                                     echo " <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"1\" checked> $phrase[493] <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"0\"> $phrase[494]";
                                      }
                           else
                                       {
                                       echo " <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"1\" > $phrase[493] <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"0\" checked> $phrase[494]";
                                       }

                            echo "</td>
							 
							
							
						
						 </tr>";
							   //echo " $opentime $closetime</td></tr>";
                                 }


                      
                      //ends foreach  $day line 249
                      }
					  //ends isset $day
					  }
                      
                      
                      if ($addrow == "yes")
                                 {
                                  echo "<tr><td> $dayname</td><td>
              <select name=\"opening[$daynum]\">

<option value=\"060000\"> 6.00 am</option>
<option value=\"063000\"> 6.30 am</option>
<option value=\"070000\"> 7.00 am</option>
<option value=\"073000\"> 7.30 am</option>
<option value=\"080000\"> 8.00 am</option>
<option value=\"083000\"> 8.30 am</option>
<option value=\"090000\"> 9.00 am</option>
<option value=\"093000\"> 9.30 am</option>
<option value=\"100000\" selected>10.00 am</option>
<option value=\"103000\">10.30 am</option>
<option value=\"110000\">11.00 am</option>
<option value=\"113000\">11.30 am</option>
<option value=\"120000\">12.00 pm</option>
<option value=\"123000\">12.30 pm</option>
<option value=\"130000\"> 1.30 pm</option>
<option value=\"140000\"> 2.00 pm</option>
<option value=\"143000\"> 2.30 pm</option>
<option value=\"150000\"> 3.00 pm</option>
<option value=\"153000\"> 3.30 pm</option>
<option value=\"160000\"> 4.00 pm</option>
<option value=\"163000\"> 4.30 pm</option>
<option value=\"170000\"> 5.00 pm</option>
<option value=\"173000\"> 5.30 pm</option>
<option value=\"180000\"> 6.00 pm</option>
<option value=\"183000\"> 6.30 pm</option>
<option value=\"190000\"> 7.00 pm</option>
<option value=\"193000\"> 7.30 pm</option>
<option value=\"200000\"> 8.00 pm</option>
<option value=\"203000\"> 8.30 pm</option>
<option value=\"210000\"> 9.00 pm</option>
<option value=\"213000\"> 9.30 pm</option>
<option value=\"220000\"> 10.00 pm</option>
<option value=\"223000\"> 10.30 pm</option>
<option value=\"230000\"> 11.00 pm</option>
</select>
              </td><td><select name=\"closing[$daynum]\" > 
              
              
              
              
              
<option value=\"060000\"> 6.00 am</option>
<option value=\"063000\"> 6.30 am</option>
<option value=\"070000\"> 7.00 am</option>
<option value=\"073000\"> 7.30 am</option>
<option value=\"080000\"> 8.00 am</option>
<option value=\"083000\"> 8.30 am</option>
<option value=\"090000\"> 9.00 am</option>
<option value=\"093000\"> 9.30 am</option>
<option value=\"100000\">10.00 am</option>
<option value=\"103000\">10.30 am</option>
<option value=\"110000\">11.00 am</option>
<option value=\"113000\">11.30 am</option>
<option value=\"120000\">12.00 pm</option>
<option value=\"123000\">12.30 pm</option>
<option value=\"130000\"> 1.30 pm</option>
<option value=\"140000\"> 2.00 pm</option>
<option value=\"143000\"> 2.30 pm</option>
<option value=\"150000\"> 3.00 pm</option>
<option value=\"153000\"> 3.30 pm</option>
<option value=\"160000\"> 4.00 pm</option>
<option value=\"163000\"> 4.30 pm</option>
<option value=\"170000\"> 5.00 pm</option>
<option value=\"173000\"> 5.30 pm</option>
<option value=\"180000\"> 6.00 pm</option>
<option value=\"183000\"> 6.30 pm</option>
<option value=\"190000\"> 7.00 pm</option>
<option value=\"193000\"> 7.30 pm</option>
<option value=\"200000\"> 8.00 pm</option>
<option value=\"203000\"> 8.30 pm</option>
<option value=\"210000\" selected> 9.00 pm</option>
<option value=\"213000\"> 9.30 pm</option>
<option value=\"220000\"> 10.00 pm</option>
<option value=\"223000\"> 10.30 pm</option>
<option value=\"230000\"> 11.00 pm</option>
</select>
              </select></td><td>$phrase[493] <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"1\" checked> $phrase[494] <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"0\"></td>
			  </tr>";


                                 }
               //ends foreach
              }


      echo "</table><p><input type=\"hidden\" name=\"m\" value=\"$m\">
        <input type=\"hidden\" name=\"branchno\" value=\"$branchno\">
         
      <input type=\"hidden\" name=\"update\" value=\"editbranch\">
      <input type=\"submit\" name=\"submit\" value=\"$phrase[28]\"></p></form>";
        
        
   }
  
   elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editpc")
   
   {  
   	
   	$sql = "select name from pc_branches where branchno = '$branchno'";
$DB->query($sql,"pcadmin.php");
$row = $DB->get();
$name = $row["name"];


$sql = "select * from pc_computers where pcno = \"$pcnum\"";
$DB->query($sql,"pcadmin.php");
$row = $DB->get();
$pcname = $row["name"];
$displayorder = $row["displayorder"];
$pccolour = $row["colour"];

$ip = $row["ip"];
$displaypc = $row["displaypc"];
$secret = $row["secret"];
$default_usage = $row["default_usage"];
$poll_client = $row["poll_client"];
$itemsout = $row["itemsout"];

echo "<h2>$name</h2>";



$sql = "select useno, name from pc_usage where m= '$m'";

//put usages in array
$DB->query($sql,"pcadmin.php");
 while ($row = $DB->get())
             {


              $arrayuseno[] = $row["useno"];
              $arrayname[] = $row["name"];

           

              }

//get usages for this pc
$sql = "select * from pc_bridge where pcnum= '$pcnum'";

$DB->query($sql,"pc.php");

 while ($row = $DB->get())
             {


              $a_useno[] = $row["useno"];
			
           }
	 
	  	
	  
	  echo "<form action=\"pcadmin.php\" method=\"post\" enctype=\"multipart/form-data\" style=\"width:50%;text-align:left;margin-left:auto;margin-right:auto\"><fieldset><legend>$phrase[26]</legend><br>
	  
	<b>$phrase[521]</b><br>

	  <input type=\"text\" name=\"name\" size=\"60\" maxlength=\"80\" value=\"$pcname\">

<br>
<br>

<b>$phrase[753]</b><br>
";
	
	
	
	if ($displaypc == 1)
								{
								
								echo "<input type=\"radio\" name=\"displaypc\" value=\"1\" checked> $phrase[12] &nbsp;  <input type=\"radio\" name=\"displaypc\" value=\"0\" > $phrase[13]";
								}
							else
								{
								echo "<input type=\"radio\" name=\"displaypc\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"displaypc\" value=\"0\" checked> $phrase[13]";
								}


echo "	<br>
<br>


	
	
	  <b>$phrase[520]</b>
";
	  if (isset($arrayuseno))
	  {
	  		echo "<table>
	   
	   <tr><td>Default</td><td>Allocated</td></tr>";
	  	
	   foreach ($arrayuseno as $index => $value)
               {
               echo "<tr><td><input type=\"radio\" name=\"default_usage\" class=\"default_usage\" id=\"def_$value\" value=\"$value\"";
               
               if ($default_usage == $value) { echo " checked";}
               
               echo "></td><td><input type=\"checkbox\" name=\"auseno[$value]\" class=\"allocated_usage\" id=\"all_$value\" value=\"$value\"";
                   if (isset($a_useno))
				   {
				     foreach   ($a_useno as $i => $use)
                               {
                                if ($value == $use)
                                   {
                                echo " checked";
                              	   }
                               }
					}
               echo "> $arrayname[$index]</td></tr> ";
           }
           
           echo "</table>";
	  }
	  else {echo "<br><span class=\"red\">$phrase[657] </span>";}
      echo "
      
      <br>
<br>
<fieldset style=\"font-size:1em;\"><legend style=\"font-size:1em;\">$phrase[877]</legend>
<b>$phrase[868] : $pcnum</b><br><br>
<b> $phrase[4]</b><br>
	  <input type=\"text\" name=\"secret\" size=\"16\" maxlength=\"16\" value=\"$secret\">
 
	
	<br><br>
	<b>$phrase[740]</b><br>
	 <input type=\"text\" name=\"ip\" size=\"16\" maxlength=\"16\" value=\"$ip\">
<span style=\"padding:2em\">$phrase[992]</span> ";

      
	
	
	if ($poll_client == 1)
								{
								
								echo "<input type=\"radio\" name=\"poll_client\" value=\"1\" checked> $phrase[12] &nbsp;  <input type=\"radio\" name=\"poll_client\" value=\"0\" > $phrase[13]";
								}
							else
								{
								echo "<input type=\"radio\" name=\"poll_client\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"poll_client\" value=\"0\" checked> $phrase[13]";
								}
      
      
	 echo "<br><br>$phrase[1043]<br>


 
	</fieldset>
                                                                
                                                                  <br><br><b>$phrase[1096]</b><br><select name=\"itemsout\">";
           $counter = 0;
      while ($counter < 9)
      {
          echo "<option value=\"$counter\"";
          if ($itemsout == $counter) {echo " selected";}
          echo ">$counter</option>";
          $counter++;
      }
      
         
 
      
      echo "
      </select> 
<br><br>
<b>$phrase[519]</b><br><br>
			<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
$phrase[495]
   <input type=\"checkbox\" name=\"none\"";
if ($pccolour == "none") {echo " checked";}
echo "><br><br>$phrase[496]<br><br>$phrase[867]
    <br><br>
<input type=\"text\" name=\"colour\" class=\"color\" value=\"$pccolour\"><br><br>
";
					/*
						  foreach ($colours as $index => $col)
						          {
								
								
						       echo "<div style=\"text-align:center;float:left;width:4em;height:4em;background:";
							    if ($col <> "none") {echo $col;} else {echo "#ffffff";}
							   
							   echo "\"><br><input type=\"radio\" name=\"colour\" value=\"$col\"";
						       if ($col == $pccolour)
						          {echo " checked ";}
			
						     	  echo ">";
								    if ($col == "none") {echo "<br>$phrase[495]";} else {echo "<br><br>";}
								  echo "</div>";
								
								
						     	 }
						*/
if ($PCIMAGE == "1")									
{
    
    echo "<b>$phrase[98]</b><br>";
$image = "no";
$sql = "select * from images where  page = '$pcnum'";

//put usages in array
$DB->query($sql,"pcadmin.php");
 while ($row = $DB->get())
             {
                $image_id = $row["image_id"];
                echo "<p><img src=\"../web/calimage.php?module=pc&image_id=$image_id\" style=\"width:150px;vetical-align:middle\">
              <span>	<a href=pcadmin.php?m=$m&amp;update=deleteimage&amp;image_id=$image_id&event=editpc&pcnum=$pcnum&branchno=$branchno> Delete</a></span></p>";
                $image = "yes";
             }

if ($image == "no")             
{
  echo "<input type=\"file\" name=\"upload[0]\" >"; 
}
   echo "<br><br>";          
}	  
      echo "<input type=\"hidden\" name=\"branchno\" value=\"$branchno\">
	
	   <input type=\"hidden\" name=\"m\" value=\"$m\">
	     <input type=\"hidden\" name=\"event\" value=\"viewpcs\">
	     <input type=\"hidden\" name=\"update\" value=\"editpc\">
      <input type=\"hidden\" name=\"pcnum\" value=\"$pcnum\">
	   <input type=\"hidden\" name=\"displayorder\" value=\"$displayorder\">
      <input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
   
      </fieldset>
	</form>
	<script type=\"text/javascript\">
	addEvent(window, 'load', addDefault);
	addEvent(window, 'load', addCheckAllocated);
	</script>
	
	
	";

      
	  
    	
   }
   
  
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "viewpcs")
       {
	  
	   
	   
      
    
    //edit existing pc allocations to branches

    

    //creates array of different usage types
//initialize pc usage arrays index
$i = 0;


$sql = "select useno, name from pc_usage where m= '$m'";

$DB->query($sql,"pcadmin.php");
 while ($row = $DB->get())
             {


              $arrayuseno[$i] = $row["useno"];
              $arrayname[$i] = $row["name"];

              $i++;

              }
    
//creates array of usage types associated with individualpcs
//initialize pc usage arrays index
$i = 0;

$sql = "select * from pc_bridge";

$DB->query($sql,"pc.php");

 while ($row = $DB->get())
             {


              $a_pcnum[$i] = $row["pcnum"];
              $a_useno[$i] = $row["useno"];
				
              $i++;
           }
           
           
           
           
//get array of images if any
           $images = array();
           $sql = "select image_id,pcno from images, pc_computers where  branch = \"$branchno\"  and pc_computers.pcno = images.page and modtype = 't'";
          
//put usages in array
$DB->query($sql,"pcadmin.php");
 while ($row = $DB->get())
             {
                $_image = $row["image_id"];
                $_pcno = $row["pcno"];
                $images[$_pcno] = $_image;
                
                
                }
        

$sql = "select name from pc_branches where branchno = \"$branchno\"";
$DB->query($sql,"pcadmin.php");
$row = $DB->get();
$name = $row["name"];


           
$sql = "select * from pc_computers where branch = \"$branchno\" order by displayorder, name";
$DB->query($sql,"pcdisplay.php");
$numrows = $DB->countrows();

echo "<br><br><b>$name</b><br><br>
<a href=\"pcadmin.php?m=$m&amp;event=addpc&amp;branchno=$branchno\"><img src=\"../images/add.png\" title=\"$phrase[498]\"  alt=\"$phrase[498]\"></a>
";


if ($numrows == 0)
   {
  
	 echo "<br><br><b>$phrase[499]</b> ";
	}
$counter = 0;

while ($row = $DB->get())
      {
	  $counter++;
      $array_pcno[$counter] = $row["pcno"];
	  $array_pccolour[$counter] = $row["colour"];
      $array_name[$counter] = formattext($row["name"]);
	  $array_displayorder[$counter] = $row["pcno"];
	  $array_linkname[$counter] = urlencode($row["name"]);
	$array_ip[$counter] = $row["ip"];
	$array_flexible[$counter] = $row["flexible"];
	$array_displaypc[$counter] = $row["displaypc"];
	$array_poll_client[$counter] = $row["poll_client"];
	  
	
	  }
	  
	//  print_r($array_ip);
	  
      /////////////////////
      
      	  $counter = 0;
//	  echo "<br>
//<br>
//<table   cellpadding=\"3\"><tr>";
echo "<div style=\"margin:2em auto;border-collapse:collapse;background:white\">";
	if (isset($array_pcno))
					{
					foreach ($array_pcno as $index => $pcno)
						{
						$counter++;
                                                
           //   print_r($array_pccolour);                                  
                                                
      echo "<div  class=\"box\" style=\"float:left;";
      if ($array_pccolour[$counter] != "none"){ echo "background-color:#$array_pccolour[$counter];";}
      echo "\">
<br>
 <img src=\"../images/computer.png\" alt=\"$phrase[452]\" style=\"float:clear\"><br><br><div style=\"text-align:left\">
      <b>$array_name[$counter]</b>  <br>ID: $array_pcno[$counter]<br>$array_ip[$counter]";
        
 	   if ($array_poll_client[$counter] ==1) {echo "<br>$phrase[992]";} else {echo "<br>";}
      if ($array_displaypc[$counter] ==1) {echo "<br>$phrase[861]";} else {echo "<br>$phrase[862]";}
    
      if (array_key_exists($pcno, $images))
      {
          echo "<img src=\"../web/calimage.php?module=pc&image_id=$images[$pcno]\" style=\"width:140px;margin-top:1em\">";
      }
      
   
      echo "</div><br><br>
      
      
      
    
      ";
      //display move left button
	  
	
	  
	  						if (($numrows > 0) && ($counter > 1))
							{
							
							//Change order array so the paragraph can be moved up page
							
							foreach ($array_displayorder as $index => $value)
									{
									
									if ($index == ($counter -1))
										{
										$left = $array_displayorder;
										$left[$index] = $array_displayorder[$counter];
										$left[$counter] = $value;
										}
									}

							$left = implode(",",$left);
							echo "<a href=\"pcadmin.php?m=$m&amp;event=viewpcs&amp;branchno=$branchno&amp;reorder=$left\"><img src=\"../images/left.png\" title=\"$phrase[901]\" alt=\"$phrase[901]\" style=\"float:left\"></a>";

							}
		
		
						
							
		//display move right button
			if (($numrows > 0) && ($counter < $numrows))
							{
							
							//echo "<br>";
						
							
							
							
								foreach ($array_displayorder as $index => $value)
									{
									
									if ($index == $counter)
										{
										$right = $array_displayorder;
										$temp = $right[$index];
										$right[$index] = $right[$index + 1];
										$right[$index + 1] = $temp;
										}
									}
								
								
							
											$right = implode(",",$right);
							echo "<a href=\"pcadmin.php?m=$m&amp;event=viewpcs&amp;branchno=$branchno&amp;reorder=$right\"><img src=\"../images/right.png\" title=\"$phrase[902]\" alt=\"$phrase[902]\" style=\"float:right\"></a>";
			}
			
					
	echo "<br><br><br><a href=\"pcadmin.php?m=$m&amp;event=editpc&amp;pcnum=$pcno&amp;branchno=$branchno\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\" alt=\"$phrase[26]\"></a> &nbsp;&nbsp;<a href=\"pcadmin.php?m=$m&amp;event=deletepc&amp;pcnum=$pcno&amp;branchno=$branchno&amp;name=$array_linkname[$counter]\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>
	<br><br></div>";
      
      }
	  }
    //  echo "</tr></table>";
  
    echo "</div>  <br>";  
      
  
      
   }

   

elseif  (isset($_REQUEST["deletetype"]) )
	{
	echo "<h2>$phrase[476]</h2><b>$phrase[14]</b><br><br>
	<a href=\"pcadmin.php?m=$m&useno=$useno&amp;update=deletetype&amp;event=types\">$phrase[12]</a> | <a href=\"pcadmin.php?m=$m&amp;event=types\">$phrase[13]</a>";
		
	}

elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteban")

	{
	echo "<h2>$phrase[477]</h2><b>$phrase[14]</b><br><br>
	<a href=\"pcadmin.php?m=$m&amp;update=deleteban&amp;event=banned&amp;id=$_REQUEST[id]\">$phrase[12]</a> | <a href=\"pcadmin.php?m=$m&amp;event=banned\">$phrase[13]</a>";
		
	}

elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteclosure")	

	{
	echo "<h2>$phrase[478]</h2><b>$phrase[14]</b><br><br>
	<a href=\"pcadmin.php?m=$m&amp;update=deleteclosure&amp;closureid=$closureid&amp;event=closures\">$phrase[12]</a> | <a href=\"pcadmin.php?m=$m&amp;event=closures\">$phrase[13]</a>";
		
	}
	


elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delbranch")
			 {	
			 $sql = "select pcno from pc_computers where branch = '$branchno'";
			 $DB->query($sql,"pcadmin.php");
			 $num = $DB->countrows();
			 
			 if ($num > 0)
			 {
			 	warning("$phrase[500]");
			 }
			 else {	
			 echo "<h2>$phrase[410]</h2><b>$phrase[14]</b><br><br>
	<a href=\"pcadmin.php?m=$m&amp;update=delbranch&amp;branchno=$branchno\">$phrase[12]</a> | <a href=\"pcadmin.php?m=$m\">$phrase[13]</a>";

			 		
			 }
			 	
			 	
			 	
			 	
			 		
			 }
elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editban")
			 {
		
			 $year = date("Y");	 
			 
			 if ($DB->type == "mysql")
		{   
			  $sql = "select *, DATE_FORMAT(date_blocked, '%d') as startday , DATE_FORMAT(date_blocked, '%m') as startmonth , DATE_FORMAT(date_blocked, '%Y') as startyear , DATE_FORMAT(date_finish, '%d') as endday  , DATE_FORMAT(date_finish, '%m') as endmonth , DATE_FORMAT(date_finish, '%Y') as endyear from pc_blacklist where id = \"$id\"";
		}
			  
			else
		{   
			  $sql = "select *, strftime( '%d',date_blocked) as startday , strftime('%m',date_blocked) as startmonth , strftime('%Y',date_blocked) as startyear , strftime('%d',date_finish) as endday  , strftime('%m',date_finish) as endmonth , strftime('%Y',date_finish) as endyear from pc_blacklist where id = \"$id\"";
		}	  
			  
		$DB->query($sql,"pcadmin.php");
			$row = $DB->get();
						
		      $id = $row["id"];
			 
		      $first_name = formattext($row["first_name"]);
		      $last_name = formattext($row["last_name"]);
		      $reason = $row["reason"];
		      $barcode = $row["barcode"];
			  //$date_blocked = $row["date_blocked"];
			 // $date_finish = $row["date_finish"];
			$startday = $row["startday"];
			$startmonth = $row["startmonth"];
			$startyear = $row["startyear"];
			
			$endday = $row["endday"];
			$endmonth = $row["endmonth"];
			$endyear = $row["endyear"];
			
				
				
				
						echo "<h2>$phrase[477]</h2>
						<form action=\"pcadmin.php\" method=\"post\" style=\"width:80%;margin-left:auto;margin-right:auto\"><fieldset><legend>$phrase[501]</legend><br>
								<table  style=\";margin-left:auto;margin-right:auto\">
								<tr><td class=\"formlabels\"><b>$phrase[130]</b></td><td align=\"left\"><input name=\"first_name\" value=\"$first_name\" length=\"50\"></td></tr>
								<tr><td class=\"formlabels\"><b>$phrase[131]</b></td><td align=\"left\"><input name=\"last_name\"  value=\"$last_name\" length=\"50\"></td></tr>
								<tr><td class=\"formlabels\"><b>$phrase[460]</b></td><td align=\"left\"><input name=\"barcode\" value=\"$barcode\" length=\"30\"></td></tr>
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[502]</b></td><td align=\"left\"><textarea name=\"reason\" cols=\"40\" rows=\"5\">$reason</textarea></td></tr>
								<tr><td class=\"formlabels\"><b>$phrase[267]</b> </td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"startday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $startday)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"startmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $startmonth)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"startyear\"><option value=\"$startyear\">$startyear</option>";
								
								$year = $startyear;
								for ( $counter = 0; $counter <= 20; $counter += 1) 
								{
								$year = $year + 1;
									echo "<option value=\"$year\">$year</option>";	
								}
								
							
								
							
								
								echo "</select></td></tr>
								<td valign=\"top\" class=\"formlabels\"><b>$phrase[268] </b></td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"endday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $endday)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"endmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $endmonth)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"endyear\">";
								
								$year = $startyear;
								for ( $counter = 0; $counter <= 20; $counter += 1) 
								{
								$year = $year + 1;
									echo "<option value=\"$year\"";
									if ($endyear == $year) {echo " selected";}
									echo ">$year</option>";	
								}
									
								
								echo "</select><br><br>
								<input type=\"hidden\" name=\"m\" value=\"$m\">
								<input type=\"hidden\" name=\"id\" value=\"$id\">
								<input type=\"hidden\" name=\"event\" value=\"banned\">
								<input type=\"hidden\" name=\"update\" value=\"updateban\">
								<input type=\"submit\" name=\"updatepatron\" value=\"$phrase[16]\"></td>
								</form></table>";
								//add patron goes here 
							
			 
			 }








elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "banned")
								 {
						$year = date("Y");	
						if ($DB->type == "mysql")
		{    
						  $sql = "select *, UNIX_TIMESTAMP(date_blocked) as date_blocked , UNIX_TIMESTAMP(date_finish) as date_finish from pc_blacklist where m= '$m'";
		}
		
		else
		{    
						  $sql = "select *, strftime('%s',date_blocked) as date_blocked , strftime('%s',date_finish) as date_finish from pc_blacklist where m= '$m'";
		}
		
		
						$DB->query($sql,"pcadmin.php");
			
						$numbans = $DB->countrows();
						
						
						echo "<h2>$phrase[504]</h2>";
						
						
								echo "<form action=\"pcadmin.php\" method=\"post\"  class=\"swouter\" >
								<fieldset class=\"swinner\"><legend>$phrase[505]</legend><br>
								<table >
								<tr><td class=\"formlabels\"><b>$phrase[130]</b></td><td align=\"left\"><input name=\"first_name\" type=\"text\" size=\"50\"></td></tr>
								<tr><td class=\"formlabels\"><b>$phrase[131]</b></td><td align=\"left\"><input name=\"last_name\" type=\"text\" size=\"50\"></td></tr>
								<tr><td class=\"formlabels\"><b>$phrase[460]</b></td><td align=\"left\"><input name=\"barcode\" type=\"text\" size=\"30\"></td></tr>
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[502]</b></td><td align=\"left\"><textarea name=\"reason\" cols=\"40\" rows=\"5\"></textarea></td></tr>
								<tr><td class=\"formlabels\"><b>$phrase[267]</b> </td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"startday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"startmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"startyear\">
								<option value=\"$year\">$year</option>";
							for ( $counter = 0; $counter <= 20; $counter += 1) 
								{
								$year = $year + 1;
									echo "<option value=\"$year\">$year</option>";	
								}
								
									
								
								echo "</select></td></tr>
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[268]</b> </td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"endday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"endmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"endyear\">
								
								";
								$year = date("Y");		
								for ( $counter = 0; $counter <= 20; $counter += 1) 
								{
								
									echo "<option value=\"$year\">$year</option>";	$year = $year + 1;
								}
								
								
									echo "</select><br><br>
								<input type=\"hidden\" name=\"m\" value=\"$m\">
								<input type=\"hidden\" name=\"update\" value=\"addban\">
								<input type=\"hidden\" name=\"event\" value=\"banned\">
								<input type=\"submit\" name=\"addpatron\" value=\"$phrase[176]\"></td>
								</table></fieldset></form>";

								
						if ($numbans > 0)
							{ 
						
								
						
						echo "<div class=\"swouter\"><table class=\"colourtable swinner\" cellpadding=\"5\" >
						<tr><td><b>$phrase[131]</b></td><td><b>$phrase[130]</b></td><td><b>$phrase[460]</b></td><td><b>$phrase[267]</b></td><td><b>$phrase[268]</b></td><td><b>$phrase[502]</b></td><td></td><td></td>";
						while ($row = $DB->get())
						      {
						      $id = $row["id"];
						      $first_name = formattext($row["first_name"]);
						      $last_name = formattext($row["last_name"]);
						      $reason = $row["reason"];
						      $barcode = $row["barcode"];
							  $date_blocked = strftime("%x",$row["date_blocked"]);
							  $date_finish = strftime("%x",$row["date_finish"]);
							  echo "<tr><td>$last_name</td><td>$first_name</td><td>$barcode</td><td>$date_blocked</td><td>$date_finish</td><td>$reason</td><td><a href=\"pcadmin.php?m=$m&amp;id=$id&amp;event=editban\">Edit</a></td><td><a href=\"pcadmin.php?m=$m&amp;id=$id&amp;event=deleteban\">Delete</a></td></tr>";
								}
								echo "</table><br><br>";
								}
								
								echo "</div>";
							}


elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "closures")
								 {
						
						
						 $year = date("Y");	 
								
								echo "<h2>$phrase[478]</h2>
								<div class=\"swouter\">
								<form action=\"pcadmin.php\" method=\"post\" class=\"swinner\"><fieldset><legend>$phrase[176]</legend><br>
								<table  >
								<tr><td class=\"formlabels\"><b>$phrase[121]</b></td><td align=\"left\"><select name=\"branchno\">";
								
								 $sql = "select * from pc_branches where m = '$m' order by name";
								$DB->query($sql,"pcadmin.php");
								while ($row = $DB->get())
									      {
									      $branchno = $row["branchno"];
										  $name = $row["name"];
										  echo "<option value=\"$branchno\">$name</option>";
										 }
								echo "</select></td></tr>
								
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[502]</b></td><td align=\"left\"><textarea name=\"reason\" cols=\"40\" rows=\"5\"></textarea></td></tr>
								<tr><td class=\"formlabels\"><b>$phrase[267]</b> </td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"startday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"startmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"startyear\">
								<option value=\"$year\">$year</option>";
									$nextyear = $year + 1;
									echo "<option value=\"$nextyear\">$nextyear</option></select>";
								
								echo "</td></tr>
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[268]</b> </td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"endday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"endmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"endyear\">
								<option value=\"$year\">$year</option>";
									$nextyear = $year + 1;
									echo "<option value=\"$nextyear\">$nextyear</option></select>";
								
								echo "<br><br>
								<input type=\"hidden\" name=\"m\" value=\"$m\">
							<input type=\"hidden\" name=\"update\" value=\"addclosure\">
								<input type=\"hidden\" name=\"event\" value=\"closures\">
							
								<input type=\"submit\" name=\"submit\" value=\"$phrase[176]\"></td>
								</table></fieldset></form>
								
								  </div>
	  <div class=\"swouter\">";
						   
						
								 
						if ($DB->type == "mysql")
		{
						  $sql = "select pc_closures.id as closureid, pc_branches.name as branchname, reason, UNIX_TIMESTAMP(date_blocked) as date_blocked ,UNIX_TIMESTAMP(date_finish) as date_finish from pc_closures, pc_branches where m = '$m' and pc_closures.branch = pc_branches.branchno";
		}
			else
		{
						  $sql = "select pc_closures.id as closureid, pc_branches.name as branchname, reason, strftime('%s',date_blocked) as date_blocked ,strftime('%s',date_finish) as date_finish from pc_closures, pc_branches where m = '$m' and pc_closures.branch = pc_branches.branchno";
		}
		
	
						 $DB->query($sql,"pcadmin.php");
						 		
						$numrows = $DB->countrows();
						if ($numrows > 0)
						{
						
						echo "<table class=\"colourtable swinner\" cellpadding=\"10\"   style=\"margin: 2em auto;\">
						<tr><td><b>$phrase[121]</b></td><td><b>$phrase[267]</b></td><td><b>$phrase[268]</b></td><td><b>$phrase[502] </b></td><td></td><td></td>";
						}
						while ($row = $DB->get())
						      {
						      $closureid = $row["closureid"];
							 
						      $branchname = formattext($row["branchname"]);
						      
						      $reason = $row["reason"];
							  $date_blocked = strftime("%x", $row["date_blocked"]);
							  $date_finish = strftime("%x", $row["date_finish"]);
							  echo "<tr><td>$branchname</td><td>$date_blocked</td><td>$date_finish</td><td>$reason</td><td><a href=\"pcadmin.php?m=$m&amp;event=editclosure&amp;closureid=$closureid\">Edit</a></td><td><a href=\"pcadmin.php?m=$m&amp;event=deleteclosure&amp;closureid=$closureid\">Delete</a></td></tr>";
								}
								
								if ($numrows > 0)
								{
						
								echo "</table><br><br>";
								}
								
								echo "</div>";
								//add patron goes here 
							}


elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editclosure")
			 {
		
			 $year = date("Y");	    
			 				if ($DB->type == "mysql")
		{
			 $sql = "select pc_closures.id as id, pc_branches.name as branchname, pc_branches.branchno as branchid, reason, DATE_FORMAT(date_blocked, '%d') as startday , DATE_FORMAT(date_blocked, '%m') as startmonth , DATE_FORMAT(date_blocked, '%Y') as startyear , DATE_FORMAT(date_finish, '%d') as endday  , DATE_FORMAT(date_finish, '%m') as endmonth , DATE_FORMAT(date_finish, '%Y') as endyear from pc_closures, pc_branches where pc_closures.branch = pc_branches.branchno and pc_closures.id = \"$closureid\"";
			 
		}
		
				 				else
                                                                {
			 $sql = "select pc_closures.id as id, pc_branches.name as branchname, pc_branches.branchno as branchid, reason, strftime('%d',date_blocked) as startday , strftime('%m',date_blocked) as startmonth , strftime('%Y',date_blocked) as startyear , strftime('%d',date_finish) as endday  , strftime('%m',date_finish) as endmonth , strftime('%Y',date_finish) as endyear from pc_closures, pc_branches where pc_closures.branch = pc_branches.branchno and pc_closures.id = '$closureid'";
			 
		}
		
	
		
			 $DB->query($sql,"pcadmin.php");
			$row = $DB->get();
						
		      $id = $row["id"];
			 $branchid = $row["branchid"];
		      $branchname = formattext($row["branchname"]);
		      $reason = $row["reason"];
		      

			$startday = $row["startday"];
			$startmonth = $row["startmonth"];
			$startyear = $row["startyear"];
			
			$endday = $row["endday"];
			$endmonth = $row["endmonth"];
			$endyear = $row["endyear"];
			
				
				
				
								echo "<h2>$phrase[478]</h2><br>
								<div class=\"swouter\"><form action=\"pcadmin.php\" method=\"post\" class=\"swinner\">
								<fieldset><legend>$phrase[26]</legend><br>
								<table  >
								
								<tr><td class=\"formlabels\"><b>$phrase[121]</b></td><td align=\"left\"><select name=\"branchno\">";
								
								 $sql = "select * from pc_branches where m= '$m'";
								 
								 $DB->query($sql,"pcadmin.php");
								
								while ($row = $DB->get())
									      {
									      $branchno = $row["branchno"];
										  $name = $row["name"];
										  echo "<option value=\"$branchno\" ";
										  if ($branchno == $branchid) {echo "selected";}
										  echo ">$name</option>";
										 }
								echo "</select></td></tr>
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[502]</b></td><td align=\"left\"><textarea name=\"reason\" cols=\"40\" rows=\"5\">$reason</textarea></td></tr>
								<tr><td class=\"formlabels\"><b>$phrase[267]</b> </td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"startday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $startday)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"startmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $startmonth)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"startyear\"><option value=\"$startyear\">$startyear</option>";
								if ($year <> $startyear) { echo "<option value=\"$year\">$year</option>";}
								$nextyear = $year + 1;
								if ($nextyear <> $startyear) {echo "<option value=\"$nextyear\">$nextyear</option>"; }
								
							
								
								echo "</select></td></tr>
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[268] </b></td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"endday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $endday)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"endmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $endmonth)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"endyear\"><option value=\"$endyear\">$endyear</option>";
								if ($year <> $endyear) { echo "<option value=\"$year\">$year</option>";}
									$nextyear = $year + 1;
								if ($nextyear <> $endyear) {echo "<option value=\"$nextyear\">$nextyear</option>";}
									
								
								echo "</select><br><br>
								<input type=\"hidden\" name=\"m\" value=\"$m\">
								<input type=\"hidden\" name=\"closureid\" value=\"$closureid\">
								<input type=\"hidden\" name=\"event\" value=\"closures\">
								<input type=\"hidden\" name=\"update\" value=\"editclosure\">
								<input type=\"submit\" name=\"updateclosure\" value=\"$phrase[28]\"></td>
								</table></fieldset></form></div>";
								//add patron goes here 
							
			 
			 }

                         
                         
                         
						
elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edittype")
								 {
							
						  
						  	  
							   
							   
						  $sql = "select * from pc_usage where m = '$m' and useno = '$id' order by name";
						 $DB->query($sql,"pcadmin.php");
						$numusages = $DB->countrows();
						
						 echo "<h2>$phrase[476]</h2>";
						
						
						
						$row = $DB->get();
						      
						      $useno = $row["useno"];
						      $fee = $row["fee"];
						      $uname = formattext($row["name"]);
						      $mintime = $row["mintime"];
						      $maxtime = $row["maxtime"];
							  $defaulttime = $row["defaulttime"];
							  $powerview = $row["power"];
							  $stats = $row["stats"];
							  $usecolour = substr($row["usecolour"],1);
							  $print = $row["print"];
							  $telephone = $row["telephone"];
							  
							  $web = $row["web"];
							  $extend = $row["extend"];
							  $warning_1 = $row["warning_1"];
							  $warning_2 = $row["warning_2"];
							  $warning_1_text = $row["warning_1_text"];
							  $warning_2_text = $row["warning_2_text"];
							
							$weblimit = $row["weblimit"];
							$noshow = $row["noshow"];
							$lockout = $row["lockout"];
							$displaytype = $row["displaytype"];
							$clientbooking = $row["clientbooking"];
							$createpin = $row["createpin"];
							
							$timelimit = $row["timelimit"];
							  $weblimitperday = $row["weblimitperday"];
                                                          $logoff = $row["logoff"];
                                                           $cleanup = $row["cleanup"];
							  
						    
							 echo "<form action=\"pcadmin.php\" method=\"post\" style=\"width:80%;margin-left:auto;margin-right:auto\"><fieldset ><legend>$uname</legend>
								 <table  cellpadding=\"5\" style=\"border-collapse:collapse;margin-left:auto;margin-right:auto\">
						<tr><td class=\"formlabels\"><strong>$phrase[516]</strong> </td><td align=\"left\"><input type=\"text\" name=\"uname\" value=\"$uname\" size=\"50\" maxlength=\"100\"></td></tr>
						<tr><td class=\"formlabels\"><strong>$phrase[455]</strong></td><td align=\"left\"> <input type=\"text\" name=\"fee\" size=\"6\" maxlength=\"6\" value=\"$fee\"></td></tr>
						
						
						
						
						 <tr><td class=\"formlabels\"> <strong>$phrase[507]</strong></td><td align=\"left\"><select name=\"mintime\">
						 ";
						 
							 $counter = 1;
							 
							 while ($counter < 61)
							 
							 {
							 	echo "<option value=\"$counter\"";
									if ($counter == $mintime) {echo " selected";}
									echo ">$counter</option>";
							 	
							 $counter++;	
							 }

							 //foreach ($min as $index => $value)
						        //  {
								  
									//echo "<option value=\"$value\"";
									//if ($value == $mintime) {echo " selected";}
									//echo ">$value </option>";
									
								//  }
						  
						  
						
						   echo " </select> $phrase[510]  </td></tr>
						  <tr><td class=\"formlabels\">  <strong>$phrase[508]</strong></td><td align=\"left\">
						    <select name=\"maxtime\">";
						 
						
						   foreach ($max as $index => $value)
						          {
								 // if ($value != $maxtime)
								  	//{
									echo "<option value=\"$value\" ";
									if ($value == $maxtime) {echo " selected";}
									echo ">$value </option>";
									//}
								  }
						 
						    echo "</select>  $phrase[510] </td></tr>
							 <tr><td class=\"formlabels\"><strong>$phrase[509]</strong></td><td align=\"left\">
						    <select name=\"defaulttime\">
						  
						 ";
						   foreach ($max as $index => $value)
						          {
								  //if ($value != $maxtime)
								  	//{
									echo "<option value=\"$value\"";
									if ($value == $defaulttime) {echo " selected";}
									echo ">$value </option>";
									//}
								  }
						 
						    echo "</select>$phrase[510] </td></tr>  
							<tr><td class=\"formlabels\"><strong>$phrase[511]</strong> </td><td align=\"left\">";
							if ($powerview == 1)
								{
								echo " <input type=\"radio\" name=\"powerview\" value=\"1\" checked> $phrase[12] &nbsp;<input type=\"radio\" name=\"powerview\" value=\"0\"> $phrase[13]  ";
								}
							else
								{
								echo "<input type=\"radio\" name=\"powerview\" value=\"1\" > $phrase[12]  &nbsp; <input type=\"radio\" name=\"powerview\" value=\"0\" checked> $phrase[13]";
								}
							
							 echo "</td></tr>
							   
							
							<tr><td class=\"formlabels\"><strong>$phrase[512]</strong> </td><td align=\"left\">";
							if ($stats == 1)
								{
								
								echo "<input type=\"radio\" name=\"stats\" value=\"1\" checked> $phrase[12] &nbsp; <input type=\"radio\" name=\"stats\" value=\"0\" >  $phrase[13]";
								}
							else
								{
								echo "<input type=\"radio\" name=\"stats\" value=\"1\" > $phrase[12]  &nbsp; <input type=\"radio\" name=\"stats\" value=\"0\" checked> $phrase[13]";
								}	
								
								echo "</td></tr><tr><td class=\"formlabels\"><strong>$phrase[513]</strong> </td><td align=\"left\">";
							if ($print == 1)
								{
								
								echo "<input type=\"radio\" name=\"print\" value=\"1\" checked> $phrase[12] &nbsp;  <input type=\"radio\" name=\"print\" value=\"0\" > $phrase[13]";
								}
							else
								{
								echo "<input type=\"radio\" name=\"print\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"print\" value=\"0\" checked> $phrase[13]";
								}
							
							echo "</td></tr>
							
							
							<tr><td class=\"formlabels\"><strong>$phrase[675]</strong> </td><td align=\"left\">";
							if ($telephone == 1)
								{
								
								echo "<input type=\"radio\" name=\"telephone\" value=\"1\" checked> $phrase[12] &nbsp;  <input type=\"radio\" name=\"telephone\" value=\"0\" > $phrase[13]";
								}
							else
								{
								echo "<input type=\"radio\" name=\"telephone\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"telephone\" value=\"0\" checked> $phrase[13]";
								}
							
							echo "</td></tr>
	<tr><td class=\"formlabels\"><strong>$phrase[757]</strong> </td><td align=\"left\">


";
	
	
	
	if ($displaytype == 1)
								{
								
								echo "<input type=\"radio\" name=\"displaytype\" value=\"1\" checked> $phrase[12] &nbsp;  <input type=\"radio\" name=\"displaytype\" value=\"0\" > $phrase[13]";
								}
							else
								{
								echo "<input type=\"radio\" name=\"displaytype\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"displaytype\" value=\"0\" checked> $phrase[13]";
								}

							
							
							
								echo "</td></tr><tr><td class=\"formlabels\"><strong>$phrase[719]</strong> </td><td align=\"left\">";
							if ($web == 1)
								{
								
								echo "<input type=\"radio\" name=\"web\" value=\"1\" checked> $phrase[12] &nbsp;  <input type=\"radio\" name=\"web\" value=\"0\" > $phrase[13]";
								}
							else
								{
								echo "<input type=\"radio\" name=\"web\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"web\" value=\"0\" checked> $phrase[13]";
								}
							
							echo"</td></tr>
                                                                
                                                                
                                                                
                                                                
                                                                
                                                                
                                                                
									<tr ><td class=\"formlabels\"><strong>$phrase[730] </strong> </td><td align=\"left\">
								<select name=\"weblimit\">";
						
								$counter = "0";
								while  ($counter < 100) {
								echo "<option value=\"$counter\"";
								if ($counter == $weblimit) {echo " selected";}
								echo "> $counter</option>
								";
							$counter++;
							}
						
							echo"</select></td></tr>
                                                        
                                                        
                                                        
                                                        
                                                        <tr ><td class=\"formlabels\"><strong>$phrase[1097]</strong> </td><td align=\"left\">
								<select name=\"weblimitperday\">";
						
								$counter = "0";
								while  ($counter < 10) {
								echo "<option value=\"$counter\"";
								if ($counter == $weblimitperday) {echo " selected";}
								echo "> $counter</option>
								";
							$counter++;
							}
						
							echo"</select> </td></tr>
                                                        
                                                        
                                                        
                                                        
                                                            <tr ><td class=\"formlabels\"><strong>$phrase[926]</strong> </td><td align=\"left\">
							<input type=\"text\" name=\"timelimit\" value=\"$timelimit\" size=\"4\">
						
							
					</td></tr>
							
							<tr><td class=\"formlabels\"><strong>$phrase[514]</strong></td><td align=\"left\">
							<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
<input type=\"text\" name=\"usecolour\" class=\"color\" value=\"$usecolour\">
							
							</td></tr>
							
							
							<tr class=\"accent\"><td class=\"formlabels\" style=\"font-size:1.5em\">$phrase[899]</td><td></td></tr>
							<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[809]</strong> </td><td align=\"left\">";
							if ($clientbooking == 1)
								{
								
								echo "<input type=\"radio\" name=\"clientbooking\" value=\"1\" checked> $phrase[12] &nbsp;  <input type=\"radio\" name=\"clientbooking\" value=\"0\" > $phrase[13]";
								}
							else
								{
								echo "<input type=\"radio\" name=\"clientbooking\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"clientbooking\" value=\"0\" checked> $phrase[13]";
								}
							
							echo"</td></tr>
							
								
							
							
								<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[900]</strong> </td><td align=\"left\">
							
							  <select name=\"createpin\">";
							$counter = 0;
							
							while ($counter < 3)
							{
								echo " <option value=\"$counter\"";
								
								if ($counter == $createpin) {echo " selected";}
								
								echo ">$pinoptions[$counter]</option>";
								$counter++;
							}
							
							
								
							echo"  </select>
							</td></tr>
						
							
									<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[1125]</strong> </td><td align=\"left\">
							<select name=\"logoff\">
                                                        <option value=\"0\">$phrase[13]</option>
                                                        <option value=\"1\"";
                                                            if ($logoff == "1") {echo " selected";}
                                                            echo ">$phrase[12]</option>
                                                        </select>
						
							
					</td></tr>
										<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[1126]</strong> </td><td align=\"left\">
							<select name=\"cleanup\">
                                                        <option value=\"0\">$phrase[13]</option>
                                                        <option value=\"1\"";
                                                            if ($cleanup == "1") {echo " selected";}
                                                            echo ">$phrase[12]</option>
                                                        </select>
						
							
					</td></tr>
							
						
						
								<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[741]</strong> </td><td align=\"left\">
							<input type=\"text\" name=\"lockout\" value=\"$lockout\" size=\"4\">
						
							
					</td></tr>
							
					
					
								<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[738] </strong> </td><td align=\"left\">
								<select name=\"noshow\">";
						
								$counter = "0";
								while  ($counter < 100) {
								echo "<option value=\"$counter\"";
								if ($counter == $noshow) {echo "selected";}
								echo "> $counter</option>
								";
							$counter++;
							}
						
							echo"</select></td></tr>
							
							
							
							
							
							
							
							
							
							
							
								<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[722] </strong> </td><td align=\"left\">";
							if ($extend == 1)
								{
								
								echo "<input type=\"radio\" name=\"extend\" value=\"1\" checked> $phrase[12] &nbsp;  <input type=\"radio\" name=\"extend\" value=\"0\" > $phrase[13]";
								}
							else
								{
								echo "<input type=\"radio\" name=\"extend\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"extend\" value=\"0\" checked> $phrase[13]";
								}
							
							echo"</td></tr>
							
							
							<tr class=\"accent\"><td class=\"formlabels\"> <strong>$phrase[720]</strong></td><td align=\"left\"><select name=\"warning_1\">
						<option value=\"0\"";
									if (0 == $warning_1) {echo " selected";}
									echo ">$phrase[495] </option>
						<option value=\"10\"";
									if (10 == $warning_1) {echo " selected";}
									echo ">10 $phrase[804] </option>
						<option value=\"20\"";
									if (20 == $warning_1) {echo " selected";}
									echo ">20 $phrase[804] </option>
						<option value=\"30\"";
									if (30 == $warning_1) {echo " selected";}
									echo ">30 $phrase[804] </option>";
						 $counter = 1;
						   while ($counter < 61)
						          {
								   $value = $counter * 60;
									echo "<option value=\"$value\"";
									if (($counter * 60) == $warning_1) {echo " selected";}
									echo ">$counter $phrase[510] </option>";
									$counter++;
								  }
						  
						  
						
						   echo " </select> <br><br>
							<textarea name=\"warning_1_text\" cols=\"50\" rows=\"3\">$warning_1_text</textarea>
						   
						   
						   
						   </td></tr>
						   <tr  class=\"accent\"><td class=\"formlabels\"> <strong>$phrase[721]</strong></td><td align=\"left\"><select name=\"warning_2\">
						 ";
						 		echo "<option value=\"0\"";
									if (0 == $warning_2) {echo " selected";}
									echo ">$phrase[495] </option>
									<option value=\"10\"";
									if (10 == $warning_2) {echo " selected";}
									echo ">10 $phrase[804] </option>
									<option value=\"20\"";
									if (20 == $warning_2) {echo " selected";}
									echo ">20 $phrase[804] </option>
									<option value=\"30\"";
									if (30 == $warning_2) {echo " selected";}
									echo ">30 $phrase[804] </option>";
						 $counter = 1;
						   while ($counter < 61)
						          {
								  $value = $counter * 60;
									echo "<option value=\"$value\"";
									if (($counter * 60) == $warning_2) {echo " selected";}
									echo ">$counter  $phrase[510]</option>";
									$counter++;
									
								  }
						  
						  
						
						   echo " </select> <br><br>
							<textarea name=\"warning_2_text\" cols=\"50\" rows=\"3\">$warning_2_text</textarea>
						     </td></tr>
							
							
						
							
							
						
						
					<tr><td></td><td align=\"left\">
							<input type=\"hidden\" name=\"useno\" value=\"$useno\">
							<input type=\"hidden\" name=\"m\" value=\"$m\">
							<input type=\"hidden\" name=\"event\" value=\"types\">
						 <input type=\"submit\" name=\"updatetype\" value=\"$phrase[28]\"> 
						  </td></tr></table></fieldset></form>
						      
						          
						
						          
						           <br> <br><br>";
							
		 
		 
  		}                         
                         
                         
                         
elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "types")
								 {
							
						  
						  	  
							   
							   
						  $sql = "select * from pc_usage where m = '$m' order by name";
						 $DB->query($sql,"pcadmin.php");
						$numusages = $DB->countrows();
						
						if ($numusages == 0)
							{ echo "<h2>$phrase[506]</h2>";}
						else	{ echo "<h2>$phrase[476]</h2>";}
						
						echo "<a href=\"pcadmin.php?m=$m&amp;event=addtype\"><b>$phrase[517]</b></a><br><br><table class=\"colourtable\" style=\"width:20%;margin:0 auto\">";
						
						while ($row = $DB->get())
						      {
						      $useno = $row["useno"];
						      $fee = $row["fee"];
						      $uname = formattext($row["name"]);
						      echo "<tr><td>$uname</td>
                                                      <td><a href=\"pcadmin.php?m=$m&id=$useno&event=edittype\">$phrase[26]</a></td>
                                                      <td><a href=\"pcadmin.php?m=$m&useno=$useno&event=types&deletetype=1\">$phrase[24]</a></td></tr>
                                                      
                                                      ";
                                                      
							
		 
		 }
                 echo "</OL>";
  		}

elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addtype")
		 {
		 			echo "<h2>$phrase[476]</h2>
						 
						  
							
						
						   <form action=\"pcadmin.php\" method=\"post\" style=\"width:90%;margin-left:auto;margin-right:auto\"><fieldset ><legend>$phrase[517]</legend>
						   <table  cellpadding=\"5\" style=\"border-collapse:collapse;margin-left:auto;margin-right:auto\">
						<tr><td class=\"formlabels\"><strong>$phrase[516]</strong></td> <td align=\"left\"><input type=\"text\" name=\"name\" size=\"50\" maxlength=\"100\"></td></tr>
						<tr><td class=\"formlabels\"><strong>$phrase[455]</strong> </td> <td align=\"left\"><input type=\"text\" name=\"fee\" size=\"6\" maxlength=\"6\" value=\"0\"></td></tr>
						 <tr><td class=\"formlabels\"><strong> $phrase[507]</strong></td> <td align=\"left\"><select name=\"mintime\">";
					
						
							   foreach ($min as $index => $value)
						          {
								  
									echo "<option value=\"$value\">$value</option>";
									
								  }
							
							
						
						   echo " </select> $phrase[510]</td></tr>
						   <tr><td class=\"formlabels\"> <strong>$phrase[508]</strong></td> <td align=\"left\">
						    <select name=\"maxtime\">";
						
							   foreach ($max as $index => $value)
						          {
								  
									echo "<option value=\"$value\">$value</option>";
									
								  }
							
							
							echo "
						    </select> $phrase[510]</td></tr>   <tr><td class=\"formlabels\">
							
							
						  <strong>  $phrase[509]</strong></td><td align=\"left\">
						  						    <select name=\"defaulttime\">";
							   foreach ($max as $index => $value)
						          {
								  
									echo "<option value=\"$value\">$value</option>";
									
								  } 
								  echo "
								  </select> $phrase[510] </td> </tr>  
						<tr><td class=\"formlabels\"><strong>$phrase[511] </strong></td><td align=\"left\"><input type=\"radio\" name=\"powerview\" value=\"1\"> $phrase[12] &nbsp; <input type=\"radio\" name=\"powerview\" value=\"0\" checked> $phrase[13]</td></tr>
						<tr><td class=\"formlabels\"><strong>$phrase[512] </strong></td><td align=\"left\"><input type=\"radio\" name=\"stats\" value=\"1\" checked> $phrase[12] &nbsp; <input type=\"radio\" name=\"stats\" value=\"0\"> $phrase[13]</td></tr>
							<tr><td class=\"formlabels\"><strong>$phrase[513] </strong></td><td align=\"left\"><input type=\"radio\" name=\"print\" value=\"1\" checked> $phrase[12] &nbsp; <input type=\"radio\" name=\"print\" value=\"0\" > $phrase[13]</td></tr>
									<tr><td class=\"formlabels\"><strong>$phrase[675] </strong></td><td align=\"left\"><input type=\"radio\" name=\"telephone\" value=\"1\" checked> $phrase[12] &nbsp; <input type=\"radio\" name=\"telephone\" value=\"0\" > $phrase[13]</td></tr>
						
	
									<tr><td class=\"formlabels\"><strong>$phrase[757] </strong></td><td align=\"left\"><input type=\"radio\" name=\"displaytype\" value=\"1\" checked> $phrase[12] &nbsp; <input type=\"radio\" name=\"displaytype\" value=\"0\" > $phrase[13]</td></tr>								
									
									
							
							
								<tr><td class=\"formlabels\"><strong>$phrase[719]</strong> </td><td align=\"left\">";
						
								echo "<input type=\"radio\" name=\"web\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"web\" value=\"0\" checked> $phrase[13]
							
							
					</td></tr>
						<tr ><td class=\"formlabels\"><strong>$phrase[730] </strong> </td><td align=\"left\">
								<select name=\"weblimit\">";
						
								$counter = "0";
								while  ($counter < 100) {
								echo "<option value=\"$counter\"";
								if ($counter == 1) {echo "selected";}
								echo "> $counter</option>
								";
							$counter++;
							}
						
							echo"</select></td></tr>
                                                            	<tr ><td class=\"formlabels\"><strong>$phrase[926]</strong> </td><td align=\"left\">
							<input type=\"text\" name=\"timelimit\"  value=\"0\" size=\"4\"></td></tr>
											<tr><td class=\"formlabels\"><strong>$phrase[514]</strong></td><td align=\"left\">
											<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
<input type=\"text\" name=\"usecolour\" class=\"color\" value=\"000000\">
											</td></tr>
						
							<tr class=\"accent\"><td class=\"formlabels\" style=\"font-size:1.5em\">$phrase[899]</td><td></td></tr>
						
			
								<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[809]</strong> </td><td align=\"left\">";
						
								echo "<input type=\"radio\" name=\"clientbooking\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"clientbooking\" value=\"0\" checked> $phrase[13]
							
							
					</td></tr>
					
					
						<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[900]</strong> </td><td align=\"left\">
						
							    <select name=\"createpin\">
							    <option value=\"0\">$pinoptions[0]</option>
							    <option value=\"1\">$pinoptions[1]</option>
							    <option value=\"2\">$pinoptions[2]</option>
							
								  </select>
							
							
					</td></tr>		
							
									<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[1125]</strong> </td><td align=\"left\">
							<select name=\"logoff\">
                                                        <option value=\"0\">$phrase[13]</option>
                                                        <option value=\"1\">$phrase[12]</option>
                                                        </select>
						
							
					</td></tr>
									<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[1126]</strong> </td><td align=\"left\">
							<select name=\"cleanup\">
                                                        <option value=\"0\">$phrase[13]</option>
                                                        <option value=\"1\">$phrase[12]</option>
                                                        </select>
						
							
					</td></tr>	
								<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[741]</strong> </td><td align=\"left\">
							<input type=\"text\" name=\"lockout\"  value=\"0\" size=\"4\"></td></tr>
							
							
							
							<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[738] </strong> </td><td align=\"left\">
								<select name=\"noshow\">";
						
								$counter = "0";
								while  ($counter < 100) {
								echo "<option value=\"$counter\"";
								if ($counter == 1) {echo "selected";}
								echo "> $counter</option>
								";
							$counter++;
							}
						
							echo"</select></td></tr>
							
							
								<tr class=\"accent\"><td class=\"formlabels\"><strong>$phrase[722]</strong> </td><td align=\"left\">";
						
								echo "<input type=\"radio\" name=\"extend\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"extend\" value=\"0\" checked> $phrase[13]";
							
							
							echo"</td></tr>
							
							
							<tr class=\"accent\"><td class=\"formlabels\"> <strong>$phrase[720]</strong></td><td align=\"left\"><select name=\"warning_1\">
							 <option value=\"0\">$phrase[495] </option>
						<option value=\"10\">10 $phrase[804] </option>
						<option value=\"20\">20 $phrase[804] </option>
							<option value=\"30\">30 $phrase[804] </option>";
						 $counter = 1;
						   while ($counter < 61)
						          {
								   $value = $counter * 60;
									echo "<option value=\"$value\">$counter </option>";
									$counter++;
								  }
						  
						  
						
						   echo " </select>  <br><br>
						   	<textarea name=\"warning_1_text\" cols=\"50\" rows=\"3\"></textarea>
					 </td></tr>
						   <tr class=\"accent\"><td class=\"formlabels\"> <strong>$phrase[721]</strong></td><td align=\"left\"><select name=\"warning_2\">
						   <option value=\"0\">$phrase[495] </option>
						<option value=\"10\">10 $phrase[804] </option>
						<option value=\"20\">20 $phrase[804] </option>
						<option value=\"30\">30 $phrase[804] </option>";
						 $counter = 1;
						   while ($counter < 61)
						          {
								   $value = $counter * 60;
									echo "<option value=\"$value\">$counter </option>";
									$counter++;
								  }
						  
						  
						
						   echo " </select>  <br><br>

						    <textarea name=\"warning_2_text\" cols=\"50\" rows=\"3\"></textarea></td></tr>
							
						
						
									
									
									
			
						
						
						
						
						<tr><td></td><td align=\"left\">
							<input type=\"hidden\" name=\"m\" value=\"$m\">
							<input type=\"hidden\" name=\"event\" value=\"types\">
							<input type=\"hidden\" name=\"update\" value=\"addtype\">
						 <input type=\"submit\" name=\"submit\" value=\"$phrase[517]\"></td></tr></table>
						 </fieldset> </form>
						  
						   
							
							
						
						  
						  	 
					<br>$phrase[518]<br><br>
					";
		 
		 
		 
		 }
		 
elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addlocation")		 
		 {
		 	echo "<h2>Locations</h2><form action=\"pcadmin.php\" style=\"width:80%;margin-left:auto;margin-right:auto;\"><fieldset><legend>$phrase[177]</legend><br>
<table style=\"margin:0 auto;text-align:left\">
<tr><td class=\"formlabels\"><b>$phrase[180] </b></td><td> <input type=\"text\" name=\"bname\" size=\"50\" maxlength=\"100\"></td></tr>
<tr><td class=\"formlabels\"><b>$phrase[132] </b></td><td> <input type=\"text\" name=\"telephone\" size=\"20\" maxlength=\"100\"></td></tr>

<tr><td class=\"formlabels\"><b>$phrase[726]</b></td><td><input type=\"text\" name=\"bookinginterval\" value=\"30\" size=\"3\" maxlength=\"3\" >
</td></tr>
<tr><td class=\"formlabels\"><b>$phrase[927]</b></td><td><input type=\"text\" name=\"earlyfinish\" value=\"0\" size=\"1\" maxlength=\"1\" >
</td></tr>
<tr><td></td><td>
<input type=\"submit\" name=\"addbranch\" value=\"$phrase[177]\">

<input type=\"hidden\" name=\"update\" value=\"addlocation\">
<input type=\"hidden\" name=\"m\" value=\"$m\">
</td></tr></table>
</fieldset>
</form>";


		 }
elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addpc")
		 {
			
   	$sql = "select name from pc_branches where branchno = \"$branchno\"";
$DB->query($sql,"pcadmin.php");
$row = $DB->get();
$name = $row["name"];



echo "<h2>$name</h2>";



$sql = "select useno, name from pc_usage where m = '$m'";

//put usages in array
$DB->query($sql,"pcadmin.php");
 while ($row = $DB->get())
             {

              $arrayuseno[] = $row["useno"];
              $arrayname[] = $row["name"];

             }


	 
	  	
	  
	  echo "<form action=\"pcadmin.php\" method=\"post\" style=\"text-align:left;width:50%;margin-left:auto;margin-right:auto\"><fieldset><legend>$phrase[498]</legend><br>
	  <b>$phrase[521]</b> <br><input type=\"text\" name=\"name\" size=\"60\" maxlength=\"80\" >
	   <br>
<br>

	
							

	


	 <b> $phrase[753]</b><br>

	 <input type=\"radio\" name=\"displaypc\" value=\"1\" checked> $phrase[12] &nbsp;
	 <input type=\"radio\" name=\"displaypc\" value=\"0\" > $phrase[13]
							 
	
	  
	  <br><br>   
	   <b>$phrase[520] </b> ";
	 // $typecount = count($arrayuseno);
	  if (isset($arrayuseno))
	  { 
	  
	  	echo "<table>
	   
	   <tr><td>Default</td><td>Allocated</td></tr>";
	  	
	   foreach ($arrayuseno as $index => $value)
               {
               echo "<tr><td><input type=\"radio\" name=\"default_usage\" class=\"default_usage\" id=\"def_$value\" value=\"$value\"></td>
               <td><input type=\"checkbox\" name=\"auseno[$value]\" value=\"$value\" class=\"allocated_usage\" id=\"all_$value\"> $arrayname[$index] </td></tr>";
           }
           
           echo "</table>";
	  }    
      else { echo "<br> $phrase[657]";}    
           
      echo "<br><br>
	
	  <fieldset style=\"font-size:1em;\"><legend style=\"font-size:1em;\">$phrase[920]</legend>
<b> $phrase[4]</b><br>
	  <input type=\"text\" name=\"secret\" size=\"16\" maxlength=\"16\" ><br><br>
	<b>$phrase[740]</b><br>
	 <input type=\"text\" name=\"ip\" size=\"16\" maxlength=\"16\" >
	 <span style=\"padding:2em\">$phrase[992]</span> 
<input type=\"radio\" name=\"poll_client\" value=\"1\" > $phrase[12] &nbsp;  <input type=\"radio\" name=\"poll_client\" value=\"0\" checked> $phrase[13]
					
<br>

	<br><br>$phrase[1043]<br>

 
	
	<br>

 
	</fieldset>
        <br><br><b>$phrase[1096]</b><br><select name=\"itemsout\">";
      $counter = 0;
      while ($counter < 9)
      {
          echo "<option value=\"$counter\">$counter</option>";
          $counter++;
      }
      
      
      echo "
      </select>

	  
	  
	  
	  <br><br><b>$phrase[519]</b><br>

	  <script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
<input type=\"text\" name=\"colour\" class=\"color\" ><br><br>
$phrase[867] \"none\"  <input type=\"checkbox\" name=\"none\" checked>

	  
	  ";
						/* 
						 foreach ($colours as $index => $colour)
	          {
			 
								 
	       echo "<div style=\"text-align:center;float:left;width:4em;height:4em;background:";
		   if ($colour <> "none") {echo "$colour";} else  {echo "#ffffff";}
		   echo "\"><br><input type=\"radio\" name=\"colour\" value=\"$colour\"";
		    if ($colour == "none"){echo " checked";}
		   echo ">";
		    if ($colour == "none") {echo "<br>none";} else {echo "<br>";}
		   echo "</div>";
		  
	  
		  }
			*/	
						
									
					
	  echo "<div style=\"clear:left\">	<br>
	$phrase[496]</div>	 <br><br>";
	  
      echo "<input type=\"hidden\" name=\"branchno\" value=\"$branchno\">
	
	   <input type=\"hidden\" name=\"m\" value=\"$m\">
	     <input type=\"hidden\" name=\"event\" value=\"viewpcs\">
	     <input type=\"hidden\" name=\"update\" value=\"addpc\">
    
      <input type=\"submit\" name=\"submit\" value=\"$phrase[176]\">
      </fieldset>
	</form>
	
	
	
	<script type=\"text/javascript\">
	addEvent(window, 'load', addDefault);
	addEvent(window, 'load', addCheckAllocated);
	</script>
	
	
	";

      

		
				
		
		 
		 
		 
		}
else   {






$sql = "select * from pc_branches  where m = '$m' order by name";
 $DB->query($sql,"pc.php");


 echo "<h2>$phrase[410]</h2> <a href=\"pcadmin.php?m=$m&amp;event=addlocation\"><img src=\"../images/add.png\" title=\"$phrase[522]\" alt=\"$phrase[522]\"></a><br><br>
 <table class=\"colourtable\" cellpadding=\"6\"  style=\"margin-left:auto;margin-right:auto\">";
while ($row = $DB->get())
      {
      $branchno = $row["branchno"];
      $bname = formattext($row["name"]);
     
      echo "<tr><td>$bname</td>
        <td><a href=\"pcadmin.php?m=$m&amp;event=viewpcs&amp;branchno=$branchno\"><img src=\"../images/computers.png\" title=\"$phrase[523]\"  alt=\"$phrase[523]\"></a></td>
     <td><a href=\"pcadmin.php?m=$m&amp;event=editbranch&amp;branchno=$branchno\"><img src=\"../images/clock.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
    
      <td><a href=\"pcadmin.php?m=$m&amp;event=delbranch&amp;branchno=$branchno\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a></td></tr>";
      }


 echo "</table>
  <br>";
  
      
      
      
      
      
      
     // }
  }


  
echo " </div>";
 
	
	}	
		

   
   include ("../includes/footer.php");
?>
