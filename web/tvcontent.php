<?php 

include ("../includes/initiliaze_page.php");


if (isset($_REQUEST["c"])) {
    
   $channel =  $_REQUEST["c"];
    
} else {echo "<h1>Display content id not specified!</h1>"; exit();}



if (!isset($_SESSION['count'])) {
  $_SESSION['count'] = 0;
}
$time = date("Ymd h:i s");

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

echo "<!DOCTYPE html>
<html><head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
 <style>
body { font-family:Arial, Helvetica, sans-serif; text-align:center;}
div {display:none}
img {width:100%;height:100%}

</style>
</head>
<body>
";
 //today is $time count is $_SESSION[count]

$timeouts = array();

$firstslide = 0;

$itemid = array();
$channel = $DB->escape($_REQUEST["c"]);
$today = date("Y-m-d");
$sql = "select * from tv_items where channel = '$channel' order by displayorder, expiry";
//echo $sql;
$counter  = 0;
$timeoutstring = "";
$DB->query($sql,"tvedit.php");
while ($row = $DB->get())
		{
                if ($row["type"] == "cal" || $row["expiry"] > $today)
                {
		$itemid[] = $row["id"];
		$type[] = $row["type"];
		$expiry[] = $row["expiry"];
                
		$content[] = $row["content"];
                $daysahead[] = $row["daysahead"];
                $location[] = $row["location"];
                $keywords[] = $row["keywords"];
                $showDetails[] = $row["showDetails"];
                $timeout[] = $row["timeout"] * 1000;
                $reloads[] = $row["reload"];
                }
                }
                
             
                foreach ($itemid as $key => $id)
                {
                
  
                    
                if ($type[$key] == "url")
                {
                echo "
<div id=\"div_$counter\" style=\"";
                
                 if ($firstslide == 0) {$firstslide = 1; echo "display:block";}
                
                echo "\"><iframe src=\"$content[$key]\" style=\"width:100%;height:100%;border-style:none;margin:0;padding:0\"></iframe></div>";
$timeouts[$counter] = $timeout[$key];
$counter++;
            
 
             
                }
                
                
                 if ($type[$key] == "html")
                {
                echo "
<div id=\"div_$counter\" style=\"";
                 if ($firstslide == 0) {$firstslide = 1; echo "display:block";}
                echo "\">$content[$key]</div>";
$timeouts[$counter] = $timeout[$key];
$counter++;
 
             
                }
                
                
                
                
                 if ($type[$key] == "image")
                {
                $sql = "select image_id from images where modtype = 'q' and page = '$id'";
               // echo "$sql;";
                $DB->query($sql,"tvdisplay.php");
                $row = $DB->get();
                $image_id =  $row["image_id"];

                
                echo "
<div id=\"div_$counter\" style=\"";
                if ($firstslide == 0) {$firstslide = 1; echo "display:block";}
                echo "\"><img src=\"calimage.php?module=tv&image_id=$image_id\" ></div>";
$timeouts[$counter] = $timeout[$key];
$counter++;                
                
                
                }
                
                
                if ($type[$key] == "cal")
                {
                    ///////////////////
              //echo "hello calendar";      
            $branches = array();	
	$sql = "select branchno, bname from cal_branches ";
	
	$DB->query($sql,"calendars.php");
	while ($row = $DB->get())
	     {
	     	$_bname = $row["bname"];
	     	$_bno = $row["branchno"];
	     	
            $branches[$_bno] =$_bname;
			
		}	
	        
//print_r($branches);
	

	
	if (isset($daysahead[$key])) { $UPCOMING_EVENTS_DAYS_AHEAD = $daysahead[$key];} else {$UPCOMING_EVENTS_DAYS_AHEAD= 30;}
	$now = time();
	$cutoff = $now + ($UPCOMING_EVENTS_DAYS_AHEAD * 24 *60 *60);
		//get list of this months events and put in array
	
	$insert = "";
	if ($location[$key] != "0" && $location[$key] != "") 
	{
	
	$locations = explode(":",$location[$key]);
		
		$insert .= " and event_location IN (";
		$count = 0;
		
		if (isset($locations)){
		foreach ($locations as $index => $value)
				{
				if ($count > 0) {$insert .= ",";}
				$insert .= $value;	
					
				$count++;
				}
		}
		$insert .= ") ";	
		
		//$insert .= " and event_location = '$location'";
	}
	
	
	if ($keywords[$key] != "" ) 
	{
	$keywords = sqltext($DATABASE_FORMAT,$keywords[$key]);	
		
			if ($DB->type == "mysql")
			{
		$insert .= " and MATCH (event_name,event_description,event_location,tags) AGAINST (\"$keywords\" IN BOOLEAN MODE) ";
			}
				
				
				
		else	{	
			$keywords = trim($keywords);
       			
       			$words = explode(" ",$keywords);
       			
       			$string = "";
       			
       			//$counter = 0;
       			foreach ($words as $index => $value)
				{
				$insert .= " and (event_name like '%$value%' or event_description like '%$value%' or event_location like '%$value%' or tags like '%$value%') ";	
				//$counter++;
				}	
				if ($keywords == "") {$insert = " and 1 = 2";}
			
			}
	}
	

				//$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  
				//where cal_cat.cat_id = cal_events.event_catid and month(FROM_UNIXTIME(event_start)) = '$month' and year(FROM_UNIXTIME(event_start)) = '$year' and template = '0' and cal_cat.cat_web > '0' and cancelled = '0' order by event_start";	
				
		
				if ($DB->type == "mysql")
			{		
			
				$sql = "SELECT event_name,event_start,event_location ,image_id FROM cal_events, cal_cat , images where  images.page = event_id and images.modtype = 'c' and cal_cat.cat_web > '0' and cal_cat.cat_id = cal_events.event_catid and event_start > '$now' and event_start < '$cutoff' and cancelled = '0' and template = '0' $insert  order by event_start";	
		
			}
		if ($DB->type == "sqlite")
			{
			
				$sql = "select event_name,event_start,event_location, image_id FROM cal_events, cal_cat,images  where  images.page = event_id and images.modtype = 'c'  cal_cat.cat_web > '0' and cal_cat.cat_id = cal_events.event_catid and datetime ( event_start , 'unixepoch','localtime' ) > '$now' and event_start < '$cutoff' and cancelled = '0'  and template = '0' $insert order by event_start";	
			
			}		
		
			
			//echo $sql;
			
			
		$DB->query($sql,"tvdisplay.php");
		
		while ($row = $DB->get()) 
		{
		$image_id =$row["image_id"];
                $event_name =$row["event_name"];
                $event_location = trim($row["event_location"]);
                $event_day = strftime("%A %e %B", $row["event_start"]);
		$event_time = date("g:i a", $row["event_start"]);
                
                if ($showDetails[$key] == 1)
                {
                    /*
                 echo "
<div id=\"box_$counter\" ><h1 >$event_name</h1> <table ><tr><td style=\"width:50%;text-align:right\">
                    <img src=\"calimage.php?image_id=$image_id\"></td>
                    <td >
					
					<p>";
                 if (array_key_exists($event_location,$branches)) {echo $branches[$event_location];} else {echo $event_location;}
                    
                    echo "</p>
					<p>$event_time <br><br>$event_day</p></td></tr></table></div>
               ";
                     * 
                     * 
                     */
                }
                else {
                    echo "
                    
<div id=\"div_$counter\" style=\"";
                     if ($firstslide == 0) {$firstslide = 1; echo "display:block";}
                    echo "\"><img src=\"calimage.php?image_id=$image_id\" ></div>
"; 
$timeouts[$counter] = $timeout[$key];
$counter++;
                    
                }
                 
             
                 
                 
                 
                 
                 
                }
                    
                    
                    
                  ////////////////////////  
                }
                }
        
        
       
      
               
    
 if (count($timeouts) == 0) { echo "<h1>Empty slideshow</h1>";
     }

echo "

<script>


var timeouts = new Array();
";
$total = 0;

 foreach ($timeouts as $key => $timeout)
                {
                
echo "
timeouts[$key] = $timeout;";
//echo "
//timeouts[$key] = 10000;";   
  $total = ($total + $timeout) * 1;   
 }

 if (count($timeouts) == 0) { echo "
     timeouts[0] = 3000;";
      }

echo "
    

if (timeouts.length > 1) {
    var count = 1;
  }
else {
    var count = 0;
  }

 var count = 0;
   
function turn()
{


if (count >= (timeouts.length ))
{
count = 0;
}

var divs = document.getElementsByTagName('div');
for(var i = 0; i < divs.length; i++){
   //do something to each div like
   divs[i].style.display = 'none';
}
////alert(count)
var id = 'div_' + count;




var box = document.getElementById(id);
//alert(count)

box.style.display = 'block';



//alert('count is ' + count)
ts = setTimeout(turn,timeouts[count]);
count++;



}

function reload()
{
window.location.reload();
}


function init()
{
var total=0;
for(var i in timeouts) { total = (total * 1) + (timeouts[i] * 1); }
parent.slidetimer = (total * 10);
parent.ts = setTimeout(parent.refresh,parent.slidetimer);
turn();
}

window.onload=init



</script>





</body>
</html>


";

?>