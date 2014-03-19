<?php


  if (isset($_REQUEST["l"]))
    {
         if(!include("../includes/initiliaze_page.php"))
 {
 	include("includes/initiliaze_page.php");
 }
    }    
$stylesheet = "../stylesheets/" . $PREFERENCES["stylesheet"];

 

echo "<!DOCTYPE html>
<html><head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<style>
body {margin:0;padding:0;text-align:center;font-family:arial;width:100%;height:100%}
.container { position: fixed;
    width: 100%;
    height: 100%;
}

</style>
 <LINK REL=\"STYLESHEET\" TYPE=\"text/css\" HREF=\"$stylesheet\"> 
</head>";

   
    if (isset($_REQUEST["l"]))
    {
 $l = $DB->escape($_REQUEST["l"]);
        
    $sql = "select distinct(pc_bridge.pcnum) from pc_bridge, pc_computers, pc_usage where 
 outoforder = '0' 
 and  pc_computers.branch = '$l' 
 and pc_bridge.pcnum = pc_computers.pcno
 and pc_usage.useno = pc_bridge.useno
 and pc_usage.web = '1'
 
 "; 
    
   // echo $sql ;
    $DB->query($sql,"slide.php");
    $counter = 0;
    $pcs = array();
     while ($row = $DB->get())
{
    $pcs[] = $row["pcnum"]; 
    $counter++;
    }
    }
    
    //print_r($pcs);
    
   
    
    if (isset($_REQUEST["count"])) { $count = $_REQUEST["count"];} else {$count = 0;}
    
    $pc = $pcs[$count];
    $d = date("d");
    $m = date("m");
    $Y = date("Y");
    
    
    $sql = "select name, colour from pc_computers where pcno = '$pc'";
    
    //echo $sql;
    $DB->query($sql,"slide.php");
    $row = $DB->get();
    $name = $row["name"]; 
    $colour= $row["colour"]; 
    
    
   echo " <body ";
   if ($colour == "none") { echo "class=\"accent\"";}
   else { echo "style=\"background:#$colour\"";}
   echo "><p id=\"clock\" style=\"font-size:2em;background:white;width:6em;margin:1em auto;padding:0.5em;-moz-border-radius: 15px;border-radius: 15px;\">Time</p>
    <div style=\"background:white;width:50%;margin:2em auto;padding:1em;-moz-border-radius: 15px;border-radius: 15px;overflow: hidden;\">
    <h1>$name</h1><p style=\"width:25%;float:left;margin:0 0 1em 1em\">";
   
   
   $sql = "select * from images where  page = '$pc' and modtype = 't'";

//put usages in array
$DB->query($sql,"slide.php");
 while ($row = $DB->get())
             {
                $image_id = $row["image_id"];
                echo "<img src=\"calimage.php?module=pc&image_id=$image_id\" style=\"width:200px;vetical-align:middle\">";
                
                }
   
   echo "</p>";
   
    
    
    $daystart = mktime(0,0,1,$m,$d,$Y);
     $dayend = mktime(23,59,59,$m,$d,$Y);
    $now = time();
    
    $sql = "select bookingno,cardnumber,bookingtime,endtime from pc_bookings, pc_usage where
        pc_usage.web = 1 
        and pc_usage.useno = pc_bookings.pcusage
     and pcno = '$pc'
        and bookingtime > '$daystart' and endtime < '$dayend'
     and cancelled = '0' order by bookingtime";
    //echo $sql;
     $DB->query($sql,"slide.php");
   $later = "";
   
     while ($row = $DB->get())
{
    $cardnumber = $row["cardnumber"];
    $bookingtime = $row["bookingtime"];
    $endtime = $row["endtime"];
  $displaystart = date("g:ia",$bookingtime);
$displayend = date("g:ia",$endtime);
    

    
    if ($now > $bookingtime && $now < $endtime) { $current = "<h2><span style=\"color:grey\">Current booking</span><br> patron $cardnumber <br>$displaystart - $displayend</h2>";}
    if ($now < $bookingtime ) { $later .=  "<br>patron $cardnumber <br>$displaystart - $displayend <br>"; }
  
    }
  
    
    if (!isset($current)) {$current = "<br><br><h2 style=\"color:green\">Available now!</h2>";}
    
    echo "$current";
    
    if ($later != "") {echo "<h2><span style=\"color:grey\">Next bookings</span>$later</h2>";}
    
    $count++;
    if ($count >= $counter){$count = 0;}
    
   echo "<script>
       parent.slidecounter = $count;
    
   window.onload=init;
    
    
    function init()
    {
    setInterval('updateClock()', 1000 )
    }
    
function updateClock ( )
{
  var currentTime = new Date ( );

  var currentHours = currentTime.getHours ( );
  var currentMinutes = currentTime.getMinutes ( );
 
    if (currentMinutes < 10) { currentMinutes = '0' + currentMinutes;}
 if ( currentHours < 12 ) { var AMPM = 'AM';} else {{ var AMPM = 'PM';} }
if ( currentHours > 12 ) { currentHours = currentHours - 12;}



  // Convert an hours component of '0' to '12''
  currentHours = ( currentHours == 0 ) ? 12 : currentHours;

  // Compose the string for display
  var currentTimeString = currentHours + ':' + currentMinutes + ' ' + AMPM;

  // Update the time display
  document.getElementById('clock').firstChild.nodeValue = currentTimeString;
}

    
    
</script>"; 
    
?>
</div>
</body>
</html>