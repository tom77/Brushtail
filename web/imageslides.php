<?php 


if(!include("../includes/initiliaze_page.php"))
 {
 	include("includes/initiliaze_page.php");
 }


if (!isset($_SESSION['count'])) {
  $_SESSION['count'] = 0;
}
$time = date("Ymd h:i s");

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);


if (isset($_REQUEST["slide"]))
{
   

echo "<html><head>
<style>
body {background:white;margin:0;padding:0;text-align:center;font-family:arial;
";


$pics = array();
$counter = 0;



if (isset($_REQUEST["daysahead"])) {$UPCOMING_EVENTS_DAYS_AHEAD = $_REQUEST["daysahead"];}
	

	
	if (!isset($UPCOMING_EVENTS_DAYS_AHEAD)) {$UPCOMING_EVENTS_DAYS_AHEAD= 30;}
	$now = time();
	$cutoff = $now + ($UPCOMING_EVENTS_DAYS_AHEAD * 24 *60 *60);
		//get list of this months events and put in array
	
	$insert = "";
	if (isset($_REQUEST["location"]) && $_REQUEST["location"] != "0") 
	{
	$location = sqltext($DATABASE_FORMAT,$_REQUEST["location"]);
	$locations = explode(":",$location);
		
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
	
	
	if (isset($_REQUEST["keywords"]) ) 
	{
	$keywords = sqltext($DATABASE_FORMAT,$_REQUEST["keywords"]);	
		
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
			
				$sql = "SELECT image_id FROM cal_events, cal_cat , images where  images.page = event_id and images.modtype = 'c' and cal_cat.cat_web > '0' and cal_cat.cat_id = cal_events.event_catid and event_start > '$now' and event_start < '$cutoff' and template = '0' $insert  order by event_start";	
		
			}
		if ($DB->type == "sqlite")
			{
			
				$sql = "select image_id FROM cal_events, cal_cat,images  where  images.page = event_id and images.modtype = 'c'  cal_cat.cat_web > '0' and cal_cat.cat_id = cal_events.event_catid and datetime ( event_start , 'unixepoch','localtime' ) > '$now' and event_start < '$cutoff'  and template = '0' $insert order by event_start";	
			
			}		
		
			
			//echo $sql;
			
			
		$DB->query($sql,"slide.php");
		
		while ($row = $DB->get()) 
		{
		$pics[$counter] =$row["image_id"];
                $counter++;
                }
    
 //print_r($pics);   
    
if ($_SESSION['count'] >= $counter - 1 ) {$_SESSION['count'] = 0;} else {$_SESSION['count']++;}

$index = $_SESSION['count'];

if ($counter > 0)
{echo "


    background-image:url('calimage.php?image_id=$pics[$index]');
background-repeat:no-repeat;
background-attachment:fixed;
background-position:center; 

echo ";
    
}

echo "
img {width:90%;
zoom= 1;
opacity = 0; 
-moz-opacity: =  0; 
-khtml-opacity =  0; 
filter:alpha(opacity=0);




}

</style>
</head>
<body>
</body>
</html>


";
 
}

else {

echo "<!DOCTYPE html>
<html><head>
<style>
body {background:white;margin:0;padding:0;text-align:center;font-family:arial;width:100%;height:100%}
.container { position: fixed;
    width: 100%;
    height: 100%;
}

</style>
</head>
<body>

 <script type=\"text/javascript\">
 
 
  window.slidetimer = 60000;
  
  window.ts = setTimeout(refresh,window.slidetimer);
 
 function refresh()
 {
 try
  {
document.getElementById('frame').src = 'slide.php';
}
catch(err)
  {
 }
  window. ts = setTimeout(refresh,window.slidetimer);
 }
 
 
 </script>";
 //today is $time count is $_SESSION[count]

$url = "imageslides.php?slide=yes";

if (isset($_REQUEST["daysahead"])) {$url .= "&daysahead=" . $_REQUEST["daysahead"];}
if (isset($_REQUEST["location"])) {$url .= "&location=" . $_REQUEST["location"];}  
if (isset($_REQUEST["keywords"])) {$url .= "&keywords=" . urlencode($_REQUEST["keywords"]);}  

    
echo "<div class=\"container\"><iframe id=\"frame\" src=\"$url\" style=\"width:100%;height:100%;border-style:none;\"></iframe></div>
</body>
</html>


";
}

?>