<?php
//

$WIDTH = 1000;
$HEIGHT = 300;



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$integers[] = "cat_id";
$integers[] = "location_id";
$integers[] = "offset";
$integers[] = "year";


if (isset($_REQUEST["day"])) 
	{
	$day = $DB->escape($_REQUEST["day"]);
	}	

if (isset($_REQUEST["month"])) 
	{
	$month = $DB->escape($_REQUEST["month"]);
	}	


foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}	
	

if (!isset($offset)) {$offset = 0;}

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

	
	$ip = ip("pc");  

	
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

	

if (!isset($ERROR)) 
	{

	
		
	page_view($DB,$PREFERENCES,$m,"");	
	
	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

echo "<div style=\"text-align:left;margin-left:2em\">";





if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deleteday" && $access->thispage > 2)
{

		if ($DB->type == "mysql")
			{
			$sql = "delete from clicker_clicks where location = '$location_id' and year(from_unixtime(timestamp,'%Y%m%d')) = '$year' and  month(from_unixtime(timestamp,'%Y%m%d')) = '$month' and dayofmonth(from_unixtime(timestamp,'%Y%m%d')) = '$day'";	
			}
			
	else
			{
			$sql = "delete from clicker_clicks where location = '$location_id' and strftime('%Y',datetime (timestamp , 'unixepoch' ,'localtime')) = '$year' and  strftime('%m',datetime (timestamp , 'unixepoch' ,'localtime')) = '$mon' and strftime('%d',datetime ( timestamp , 'unixepoch','localtime' )) = '$day'	";
			}
	
		
	$DB->query($sql,"clicker.php");
}


	
	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete_clicks" && $access->thispage > 2)
{
	
	//print_r($_REQUEST);
	
	if (isset($_REQUEST["click"]))
	{
		
		foreach ($_REQUEST["click"] as $key => $value)
		{
			if ($value == "on")
			{
				//echo $key;
				$key = intval($key);
				//print_r($peices);
				$sql = "delete from clicker_clicks where click_id = '$key'";
			//	echo "$sql <br>" ;
				$DB->query($sql,"clicker.php");
			
			}
		}
	}
}

	
$sql = "select name, ordering from modules where m = \"$m\"";
		$DB->query($sql,"clicker.php");
		$row = $DB->get();
		$modname = formattext($row["name"]);	
		
				$sql = "select cat_id, cat_name from  clicker_category where m = '$m' order by cat_name";
		$DB->query($sql,"clicker.php");
		while ($row = $DB->get())
      {
      $array_cat_id[] = $row["cat_id"];
      $array_cat_name[] = $row["cat_name"];
      $categories[$row["cat_id"]] = $row["cat_name"];
    
      }
      
      			$sql = "select location_id, location_name from clicker_location where m = '$m' order by location_name";
		$DB->query($sql,"clicker.php");
		while ($row = $DB->get())
      {
      $array_location_id[] = $row["location_id"];
      $array_location_name[] = $row["location_name"];
    
      }


      
echo "<h1>$modname</h1>
<script type=\"text/javascript\">

function checkmonth()
{

var monthmenu = document.getElementById('month');
var daymenu = document.getElementById('day');

 if (monthmenu.options[monthmenu.selectedIndex].value == 'all')
 	{
 	//alert(\"hello\" + monthmenu.options[monthmenu.selectedIndex].value)
 	daymenu.selectedIndex = 0
 	}


}
 function pop_window(url) {
 
  var clickit = window.open(url,'','status=no,resizable=yes,scrollbars=yes,toolbar=no,location=no,menubar=no,width=$WIDTH,height=$HEIGHT');
 	if (window.focus) {clickit.focus()}
 }
 
 
</script>



<form   action=\"clicker.php\" method=\"post\" style=\"display:inline;\">
	";

		$sql = "select mode from clicker_options where m = '$m'";
	$DB->query($sql,"clickeradmin.php");
	$row = $DB->get();
	$mode = $row["mode"];
	if ($mode == "") {$mode = 1;}
	

$this_year = date("Y");

//print_r($_REQUEST);

if(!isset($_REQUEST["year"])) {$year = $this_year; }
if(!isset($_REQUEST["month"]))  {$month = date("n");}
if(!isset($_REQUEST["day"])) {$day = "all";}

	if ($DB->type == "mysql")
			{
			$sql = "select year(from_unixtime(timestamp,'%Y%m%d')) as y from clicker_clicks group by y";
			}
			
	else
			{
			$sql = "select strftime('%Y',datetime (timestamp , 'unixepoch' ,'localtime')) as y from clicker_clicks group by y";
			}


			//echo "year is $year";
			
$DB->query($sql,"clicker.php");


echo "<select name=\"year\">";
		while ($row = $DB->get())
      {
      $y = $row["y"];

	echo "<option value=\"$y\"";
	if ($y == $year) {echo " selected"; $match = "yes";}
	echo ">$y</option>";

}
if (!isset($match)) {echo "<option value=\"$year\">$year</option>";}
echo "</select>
<select name=\"month\" id=\"month\" onchange=\"checkmonth()\">
<option value=\"all\">All year</option>";
$counter = 1;
while($counter<13)
{
	echo "<option value=\"$counter\"";
	if (isset($month) && $month == $counter) {echo " selected";}
	
	//echo "ZZ $year ZZ";
	
	$mktime = mktime(0,0,0,$counter,1,$year);
	$monthname = strftime("%B",$mktime);
	echo ">$monthname</option>";
	$counter++;
}

echo "</select>
<select name=\"day\" id=\"day\" onchange=\"checkmonth()\"><option value=\"all\">All month</option>";
$counter = 1;
while ($counter < 32)
{
	echo "<option value=\"$counter\"";
	if ($counter == $day) {echo " selected";}
	echo ">$counter</option>";
	$counter++;
}

echo "</select>



<input type=\"hidden\" name=\"m\" value=\"$m\"><input type=\"submit\" name=\"submit\" value=\"View Totals\" ></form>

<form style=\"display:inline\"><input type=\"submit\" value=\"Launch Clicker\" onclick=\"pop_window('clickbutton.php?m=$m');return false;\"></form>
";
if (isset($_REQUEST["view"]) && $_REQUEST["view"] == "branch")
{
	$sql = "select location_name from clicker_location where location_id = '$location_id'";
	$DB->query($sql,"clickeradmin.php");
	$row = $DB->get();
	$location_name = $row["location_name"];
	echo "<h2>$location_name $year $month</h2>";
	
	if ($DB->type == "mysql")
			{
				$sql = "select count(timestamp) as total,category, dayofmonth(from_unixtime(timestamp,'%Y%m%d')) as day from clicker_clicks where location = '$location_id' and year(from_unixtime(timestamp,'%Y%m%d')) = '$year' and  month(from_unixtime(timestamp,'%Y%m%d')) = '$month' group by day, category order by day";
			//$insert = "where year(from_unixtime(timestamp,'%Y%m%d')) = '$year'";	
			 //if (isset($month) && $month != "all") {$insert .= " and month(from_unixtime(timestamp,'%Y%m%d')) = '$month'";	}
			// if (isset($day) && $day != "all") {$insert .= " and dayofmonth(from_unixtime(timestamp,'%Y%m%d')) = '$day'";	}
			}
			
	else
			{
				if ($month < 10) { $mon = "0". $month;} else {$mon = $month;}
				$sql = "select count(timestamp) as total,category, strftime('%d',datetime (timestamp , 'unixepoch' ,'localtime')) as day from clicker_clicks where location = '$location_id' and strftime('%Y',datetime (timestamp , 'unixepoch' ,'localtime')) = '$year' and  strftime('%m',datetime (timestamp , 'unixepoch' ,'localtime')) = '$mon' group by day, category order by day";
			//$insert = "where strftime('%Y',datetime ( timestamp , 'unixepoch','localtime' )) = '$year'";	
			// if (isset($month) && $month != "all") {$insert .= " and strftime('%m',datetime ( timestamp , 'unixepoch','localtime' )) = '$month'";	}
			// if (isset($day) && $day != "all") {$insert .= " and strftime('%d',datetime ( timestamp , 'unixepoch','localtime' )) = '$day'";	}	
			}
		
//echo $sql;
	$DB->query($sql,"clicker.php");
		while ($row = $DB->get())
      {
      $result_total[] = $row["total"];
      $result_category[] = $row["category"];
      $result_day[] = $row["day"];
      }
	
	
		if ($DB->type == "mysql")
			{
				$sql = "select dayofmonth(from_unixtime(timestamp,'%Y%m%d')) as day from clicker_clicks where location = '$location_id' and year(from_unixtime(timestamp,'%Y%m%d')) = '$year' and  month(from_unixtime(timestamp,'%Y%m%d')) = '$month' group by day order by day";
	
			}
			
	else
			{
				if ($month < 10) { $mon = "0". $month;} else {$mon = $month;}
				$sql = "select strftime('%d',datetime (timestamp , 'unixepoch' ,'localtime')) as day from clicker_clicks where location = '$location_id' and strftime('%Y',datetime (timestamp , 'unixepoch' ,'localtime')) = '$year' and  strftime('%m',datetime (timestamp , 'unixepoch' ,'localtime')) = '$mon' group by day order by day";
	
			}
		//echo $sql;

	$DB->query($sql,"clicker.php");
		while ($row = $DB->get())
      {
      $days[] = $row["day"];
   
      }

	//print_r($days);
	
 if (isset($days))
 {
	
	echo "

	<table class=\"colourtable\">
	<tr><td><b>Day of month</b></td>";
	
		foreach ($array_cat_name as $key => $value)

	{
		$catid = $array_cat_id[$key];
		$col_total[$catid]=0;
		echo "<td><b>$value</b></td>";
	}
	
	$sum_total = 0;
	
	echo "<td><b>Total</b></td>";
	if ($access->thispage > 2) { echo "<td></td>";}
	echo "</tr>";
	
	if (isset($days))	{ 
		foreach ($days as $key => $day)

	{
		$row_total = 0;
		echo "<tr><td><b><a href=\"clicker.php?m=$m&year=$year&month=$month&day=$day&view=day&location_id=$location_id\">$day</a></b></td>";
			foreach ($array_cat_id as $k => $category)

			{
			echo "<td>";
				$total = 0;
				if (isset($result_total))
				{
				foreach ($result_total as $index => $t)
				{
				//	echo "$result_category[$index] $category $result_day[$index]  $day<br>";
				if ($result_category[$index] == $category && $result_day[$index] == $day)	{$total =  $t; } 
				}
				}
			$row_total += $total;
			$col_total[$category] += $total;
			echo "$total</td>";
			}
		echo "<td><b>$row_total</b></td>";
		if ($access->thispage > 2) { echo "<td><a href=\"clicker.php?m=$m&view=branch&year=$year&month=$month&location_id=$location_id&day=$day&view=delday\">$phrase[24]</a></td>";}
		echo "</tr>";
		$sum_total+= $row_total;
	} }
	
	echo "<tr><td><b>Total</b></td>";
	foreach ($array_cat_id as $key => $catid)

	{
		
		echo "<td><b>$col_total[$catid]</b></td>";
	}
	echo "<td><b>$sum_total</b></td></tr>";
	echo "</table>";
	
	
 } else {echo "No results.";}
	
	
	
	
	
}

elseif (isset($_REQUEST["view"]) && $_REQUEST["view"] == "delday")
{
	
	
	echo "<h2>$phrase[14]</h2>
	<a href=\"clicker.php?m=$m&view=branch&year=$year&month=$month&location_id=$location_id&day=$day&update=deleteday\">$phrase[12]</a> | <a href=\"clicker.php?m=$m&view=branch&year=$year&month=$month&location_id=$location_id&day=$day\">$phrase[13]</a>
	
	";
}


elseif (isset($_REQUEST["view"]) && $_REQUEST["view"] == "day")
{
	
	
	
	$sql = "select location_name from clicker_location where location_id = '$location_id'";
	$DB->query($sql,"clickeradmin.php");
	$row = $DB->get();
	$location_name = $row["location_name"];
	

 	if ($DB->type == "mysql")
			{
				 $sql = "select timestamp,click_ip,click_id, category, duration from clicker_clicks where location = '$location_id' and year(from_unixtime(timestamp,'%Y%m%d')) = '$year' and  month(from_unixtime(timestamp,'%Y%m%d')) = '$month' and dayofmonth(from_unixtime(timestamp,'%Y%m%d')) = '$day' order by timestamp";
			}
			
	else
			{
				
				if ($month < 10) { $mon = "0". $month;} else {$mon = $month;}
							 $sql = "select timestamp, click_ip,click_id, category, duration from clicker_clicks where location = '$location_id' and strftime('%Y',datetime (timestamp , 'unixepoch' ,'localtime')) = '$year' and  strftime('%m',datetime (timestamp , 'unixepoch' ,'localtime')) = '$mon' and strftime('%d',datetime ( timestamp , 'unixepoch','localtime' )) = '$day' order by timestamp";
				//$sql = "select strftime('%d',datetime (timestamp , 'unixepoch' ,'localtime')) as day from clicker_clicks where  group by day order by day";
		//echo "hello";
			}
	
	echo "
			<h2>$location_name $day/$month/$year</h2>
			<div style=\"float:left;margin-right:5em\">
		<form action=\"clicker.php\" method=\"post\"><table class=\"colourtable\" >
		<tr style=\"font-weight:bold\"><td>Time</td><td>Category</td><td>IP</td>";
		if ($mode != 1) {echo "<td></td>";}
		  if ($access->thispage > 1) {echo "<td></td>";}
		  echo "</tr>";
	$DB->query($sql,"clicker.php");
		while ($row = $DB->get())
      {
      $click_id = $row["click_id"];
      $ip = $row["click_ip"];
      $timestamp = $row["timestamp"];
      $time = strftime("%X",$timestamp);
      $duration = $row["duration"];
      $category = $row["category"];
      
      echo "<tr><td>$time</td><td>$categories[$category]</td><td>$ip</td>";
      if ($mode != 1) {echo "<td>$duration</td>";}
     if ($access->thispage > 1) {echo "<td><input type=\"checkbox\" name=\"click[$click_id]\"></td>";}
      
      echo "</tr>
";
      }
    echo "</table>";
    if ($access->thispage > 1) { echo "
    <input type=\"hidden\" name=\"m\" value=\"$m\">
    <input type=\"hidden\" name=\"year\" value=\"$year\">
    <input type=\"hidden\" name=\"month\" value=\"$month\">
    <input type=\"hidden\" name=\"day\" value=\"$day\">
      <input type=\"hidden\" name=\"location_id\" value=\"$location_id\">
    <input type=\"hidden\" name=\"view\" value=\"day\">
    <input type=\"hidden\" name=\"update\" value=\"delete_clicks\"><br>
    <input type=\"button\" onclick=\"checkboxes(true);\" value=\"$phrase[878]\">
&nbsp;&nbsp;
<input type=\"button\" onclick=\"checkboxes(false);\" value=\"$phrase[879]\">&nbsp;&nbsp;
     <input type=\"submit\" name=\"submit\" value=\"Delete selected\">
    </form>
    <script type=\"text/javascript\">
function checkboxes(value)
{
	
   var inputs = document.getElementsByTagName(\"input\");
   for(var t = 0;t < inputs.length;t++){
     if(inputs[t].type == \"checkbox\")
       inputs[t].checked = value;
   }
}
</script>";
    }
    
    
    
    echo "</div>
    <table class=\"colourtable\" style=\"float:left;margin-left:4em\">
	<tr style=\"font-weight:bold\"><td>Hour</td><td>Total</td></tr>
	";
    
    
 	if ($DB->type == "mysql")
			{
				 $sql = "select count(*) as usercount,  hour(from_unixtime( timestamp, '%Y-%m-%d %H:%i:%s' )) as hour from clicker_clicks where location = '$location_id' and year(from_unixtime(timestamp,'%Y%m%d')) = '$year' and  month(from_unixtime(timestamp,'%Y%m%d')) = '$month' and dayofmonth(from_unixtime(timestamp,'%Y%m%d')) = '$day' group by hour order by hour";
			}
			
	else
			{
				
				if ($month < 10) { $mon = "0". $month;} else {$mon = $month;}
							 $sql = "select count(*) as usercount, strftime('%H',timestamp) as hour from clicker_clicks where location = '$location_id' and strftime('%Y',datetime (timestamp , 'unixepoch' ,'localtime')) = '$year' and  strftime('%m',datetime (timestamp , 'unixepoch' ,'localtime')) = '$mon' and strftime('%d',datetime ( timestamp , 'unixepoch','localtime' )) = '$day' group by hour order by hour";
				//$sql = "select strftime('%d',datetime (timestamp , 'unixepoch' ,'localtime')) as day from clicker_clicks where  group by day order by day";
		//echo "hello";
			}
	//echo $sql;
	
$DB->query($sql,"clicker.php");
		while ($row = $DB->get())
      {
$usercount = $row["usercount"];
$hour = $row["hour"];

$hourplus1 = $hour + 1;
$hourminus12 = $hour - 12;
$hourminus11 = $hour - 11;

if ($hour == 12) {$displayhour = "12-1 pm";}
elseif ($hour > 12) { $displayhour = "&nbsp;" . $hourminus12 . "-" .  $hourminus11 . " pm";} 
else {$displayhour = $hour . "-" . $hourplus1 . " am";}

echo "<tr><td>$displayhour</td><td>$usercount</td></tr>";
}
	echo "</table>";
    
    
    
 
	
}
else {

	if ($DB->type == "mysql")
			{
			$insert = "where year(from_unixtime(timestamp,'%Y%m%d')) = '$year'";	
			 if (isset($month) && $month != "all") {$insert .= " and month(from_unixtime(timestamp,'%Y%m%d')) = '$month'";	}
			 if (isset($day) && $day != "all") {$insert .= " and dayofmonth(from_unixtime(timestamp,'%Y%m%d')) = '$day'";	}
			}
			
	else
			{
			$insert = "where strftime('%Y',datetime ( timestamp , 'unixepoch','localtime' )) = '$year'";	
			 if (isset($month) && $month != "all") {
			 	if ($month < 10) { $mon = "0". $month;} else {$mon = $month;}
			 	
			 	$insert .= " and strftime('%m',datetime ( timestamp , 'unixepoch','localtime' )) = '$mon'";	}
			 if (isset($day) && $day != "all") {
			 		if ($day < 10) { $d = "0". $day;} else {$d = $day;}
			 	
			 	$insert .= " and strftime('%d',datetime ( timestamp , 'unixepoch','localtime' )) = '$d'";	}	
			}

			
	
			
				
		if ($mode == "1" )
	{
	$sql = "select count(timestamp) as totals, location, category from clicker_clicks $insert group by location, category";	
	}
	else 
	{
	$sql = "select sum(duration) as totals, location, category from clicker_clicks $insert group by location, category";		
	}
	//echo $sql;

	$DB->query($sql,"clicker.php");
		while ($row = $DB->get())
      {
      $results_total[] = $row["totals"];
    
      $results_category[] = $row["category"];
      $results_location[] = $row["location"];
    
      }	
	//}	


	$sum_total=0;
	
	echo "<h2>$year ";
	
	if (isset($month) && $month != "all") 
	{
	$mktime = mktime(0,0,0,$month,1,$year);
	$monthname = strftime("%B",$mktime);
	echo $monthname;	
	}
	else {echo "Total";}
	
	
	if (isset($day) && $day != "all") 
	{
	$mktime = mktime(0,0,0,$month,$day,$year);
	$dayname = strftime("%A %d",$mktime);
	echo " $dayname";	
	}
	
	
	echo "</h2><table class=\"colourtable\" style=\"background:white\"><tr style=\"font-weight:bold\"><td>Location</td>";
	if (isset($array_cat_name))
	{foreach ($array_cat_name as $key => $value)

	{
		$catid = $array_cat_id[$key];
		$col_total[$catid]=0;
		echo "<td>$value</td>";
	}
	}
	echo "<td>Total</td></tr>";
	
	if (isset($array_location_id))
	{
		foreach ($array_location_id as $key => $locid)
	

	{
		$row_total = 0;
		echo "<tr><td>";
		if ($month != "all") {	echo "<a href=\"clicker.php?m=$m&view=branch&year=$year&month=$month&location_id=$locid\">";}
		echo $array_location_name[$key];
			if ($month != "all") {	echo "</a>";}
	
		
		echo "</td>";
		if (isset($array_cat_id))
		{
			foreach ($array_cat_id as $k => $catid)

			{
			echo "<td>";
			$t = 0;
			if (isset($results_total))
			{
			foreach ($results_total as $r => $total)

			{
			
			if ($results_category[$r] == $catid && $results_location[$r] == $locid)
				{
				$t =  $total;
				$row_total += $total;
				$col_total[$catid] += $total;
				}
			
			}
			} else {$t = 0;}
			echo "$t</td>";
			}
	}
		
		echo "<td>$row_total</td></tr>";
		$sum_total += $row_total;
	}}
	
	echo "<tr style=\"font-weight:bold\"><td>All</td>";
	if (isset($array_cat_id))
	{
		foreach ($array_cat_id as $key => $catid)
	

	{
		
		echo "<td>$col_total[$catid]</td>";
	} }
	echo "<td>$sum_total</td></tr></table><br>
	
	<a href=\"clickercsv.php?m=$m&year=$year";
if (isset($month) && $month != "all") {echo "&month=$month";}
if (isset($month) && $month != "all" && isset($day) && $day != "all") {echo "&day=$day";}
echo "\">Download CSV version</a>";
}
}



		//end contentbox
		echo "</div>";
//	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

