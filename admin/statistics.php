<?php

set_time_limit(6000);

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

	
$integers[] = "year";
$integers[] = "month";
$integers[] = "day";

foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (isset($month) && isset($year))
{
$_t = mktime(0, 0, 0, $month, 1,  $year);
	$monthname = strftime("%B",$_t);
}

if (isset($month) && isset($year) && isset($day))
{
$_t = mktime(0, 0, 0, $month, $day,  $year);
	$monthname = strftime("%B",$_t);
	$dayname = strftime("%A",$_t);

	
}


include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");


include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>
	  	<h2>$phrase[685]</h2>";

	  $sql = "select m , name from modules" ;
          $DB->query($sql,"statistics.php");
          while ($row = $DB->get()) 
		{
		
		$_m = $row["m"];
		$module_names[$_m] = $row["name"];
                }
	  	
if (isset($day))
{
	
	
echo "<h3 style=\"margin-top:1em;background:white\">$dayname $day $monthname $year</h3><a href=\"statistics.php\">Month listing</a><br><br><form action=\"statistics.php\" method=\"post\"><select name=\"day\">";
	$counter = 1;
	while ($counter < 32)
	{
		echo "<option value=\"";
		if ($counter < 10) {$val = "0" . $counter;} else {$val = $counter;}
		echo "$val\">$counter</option>
";
	$counter++;
	}
	echo "</select><input type=\"hidden\" name=\"month\" value=\"$month\">
	<input type=\"hidden\" name=\"year\" value=\"$year\">
	<input type=\"submit\" name=\"submit\" value=\"Select day\"> </form><br><br>";
	
	if ($DB->type == "mysql")
       		{
		
$sql = "select sum(totalviews) as module_views, m from page_views where dayofmonth(view_date) = '$day' and month(view_date) = '$month' and year(view_date) = '$year'  group by m order by module_views desc";
       		}
       		
     else
       		{
		
$sql = "select sum(totalviews) as module_views, m from page_views where strftime('%d',view_date) = '$day' and   strftime('%m',view_date) = '$month' and strftime ( '%Y' ,view_date) = '$year'  group by m order by module_views desc";
       		} 		
       
      
	$DB->query($sql,"statistics.php");
	
	echo "<table class=\"colourtable\" style=\"float:left;margin-right:5em\">
	<tr><td><b>By module</b></td><td ><b>ID</b></td><td ><b>Count</b></td></tr>
	";
	//echo $sql;
	$total = 0;
		while ($row = $DB->get()) 
		{
		
		$m = $row["m"];
		$module_views = $row["module_views"];
		$total = $total + $module_views;
		echo  "<tr><td>$module_names[$m]</td><td>$m</td><td> $module_views</td></tr>";
		}
		echo "<tr><td>$phrase[213]</td><td></td><td>$total</td></tr></table>";
		

	$sql = "select page_title, page_id from page";	
	$DB->query($sql,"statistics.php");
	while ($row = $DB->get()) 
		{
		
		$page_id = $row["page_id"];
		$page_title = $row["page_title"];
		
		$page_titles[$page_id] = $page_title;
		}	
	
	if ($DB->type == "mysql")
       		{
		
$sql = "select totalviews, page_id from page_views where dayofmonth(view_date) = '$day' and month(view_date) = '$month' and year(view_date) = '$year' and page_id != '' group by page_id order by view_date desc";
       		}
       		
     else
       		{
		
$sql = "select totalviews, page_id from page_views where strftime('%d', view_date ) = '$day' and strftime('%m', view_date ) = '$month' and strftime ( '%Y' ,view_date) = '$year' and page_id != '' group by page_id order by view_date desc";
       		} 		
       
      //echo $sql;
	$DB->query($sql,"statistics.php");
	
	echo "<table class=\"colourtable\" >
	<tr><td><b>By content page</b></td><td><b>ID</b></td><td><b>Count</b></td></tr>
	";
		$total = 0;
		while ($row = $DB->get()) 
		{
		
		$page_id = $row["page_id"];
		$page_views = $row["totalviews"];
		$total = $total + $page_views;
		echo  "<tr><td>";
		if (isset($page_titles) && array_key_exists($page_id,$page_titles)) { echo $page_titles[$page_id];} else {echo $page_id;}
		echo "</td><td>$page_id</td><td> $page_views</td></tr>";
		}
		echo "<tr><td>$phrase[213]</td><td></td><td>$total</td></tr></table>";	
		
	
	
}

elseif (isset($month))
{
	
	echo "<h3 style=\"margin-top:1em;background:white\">$monthname $year</h3><form action=\"statistics.php\"><select name=\"day\">";
	$counter = 1;
	while ($counter < 32)
	{
		echo "<option value=\"";
		if ($counter < 10) {$val = "0" . $counter;} else {$val = $counter;}
		echo "$val\">$counter</option>
";
	$counter++;
	}
	echo "</select><input type=\"hidden\" name=\"month\" value=\"$month\">
	<input type=\"hidden\" name=\"year\" value=\"$year\">
	<input type=\"submit\" name=\"submit\" value=\"View by day\"></form><br>";
	
	if ($DB->type == "mysql")
       		{
		
$sql = "select sum(totalviews)  as module_views, m from page_views where  month(view_date) = '$month' and year(view_date) = '$year'  group by m order by module_views desc";
       		}
       		
      else
       		{
		
$sql = "select sum(totalviews)  as module_views, m from page_views where  strftime('%m',view_date) = '$month' and strftime ( '%Y' , view_date  ) = '$year'  group by m order by module_views desc";
       		} 		
       
      
	$DB->query($sql,"statistics.php");
	
	echo "<table class=\"colourtable\" style=\"float:left;margin-right:5em\">
	<tr><td ><b>By module</b></td><td ><b>ID</b></td><td ><b>Count</b></td></tr>
	";
		$total = 0;
		while ($row = $DB->get()) 
		{
		
		$m = $row["m"];
		$module_views = $row["module_views"];
		$total = $total + $module_views;
		echo  "<tr><td>";
		
		if (array_key_exists($m,$module_names)) {echo $module_names[$m];} else {echo $phrase[1041];}
		echo "</td><td>$m</td><td> $module_views</td></tr>";
		}
		echo "<tr><td>$phrase[213]</td><td></td><td>$total</td></tr></table>";
		

	$sql = "select page_title, page_id from page";	
	$DB->query($sql,"statistics.php");
	
	while ($row = $DB->get()) 
		{
		
		$page_id = $row["page_id"];
		$page_title = $row["page_title"];
		
		$page_titles[$page_id] = $page_title;
		
		}	
	
	if ($DB->type == "mysql")
       		{
		
$sql = "select count(view_date) as page_views, page_id from page_views where  month(view_date) = '$month' and year(view_date) = '$year' and page_id != '' group by page_id order by page_views desc";
       		}
       		
  else
       		{
		
$sql = "select count(view_date)  as page_views, page_id from page_views where  strftime('%m',view_date ) = '$month' and strftime ( '%Y' , view_date  ) = '$year' and page_id != '' group by page_id order by page_views desc";
       		} 		
       
     // echo $sql;
	$DB->query($sql,"statistics.php");
	
	echo "<table class=\"colourtable\" >
	<tr><td ><b>By content page</b></td><td ><b>ID</b></td><td ><b>Count</b></td></tr>
	";
	$total = 0;
		while ($row = $DB->get()) 
		{
		
		$page_id = $row["page_id"];
		$page_views = $row["page_views"];
		$total = $total + $page_views;
		echo  "<tr><td>";
		if (isset($page_titles) && array_key_exists($page_id,$page_titles)) { echo $page_titles[$page_id];} else {echo $page_id;}
		echo "</td><td>$page_id</td><td> $page_views</td></tr>";
		}
		echo "<tr><td>$phrase[213]</td><td></td><td>$total</td></tr></table>";	
		
		
}
else 
{
			echo "<ul class=\"listing\">";
			
			
			
	//if ($DB->type == "mysql")
       	//	{
	
	//$sql = "select month(view_date) as monthnumber, year(view_date) as year from page_views  group by year desc, monthnumber desc";	
       //		}	
	
	//else
       		//{
	
	//$sql = "select strftime('%m',view_date ) as monthnumber,   strftime('%Y', view_date) as year from page_views  group by year, monthnumber order by year DESC";	
       	//	}	
    	
                        
                        
        $sql = "select min(view_date) as min , max(view_date) as max from page_views";               
                        
       	
	$DB->query($sql,"statistics.php");
		//echo $sql;  	
		$num= $DB->countrows();
		//$count = 0;
		$row = $DB->get();
		
		$min = $row["min"];
		$max = $row["max"];
                
                $counter = 0;
                $endcounter = 0;
                
                
                $_min = explode("-",$min);
                if (array_key_exists(1,$_min))
                {
                $startmonth = $_min[1];
                $startyear = $_min[0];
                $counter = mktime(0,0,0,$startmonth,1,$startyear);
                }
                $_max = explode("-",$max);
                
                 if (array_key_exists(1,$_max))
                {
                $endmonth = $_max[1];
                $endyear = $_max[0];
                 $endcounter = mktime(0,0,0,$endmonth,1,$endyear);
                }
                
                
                
                
		//$month = str_pad($month, 2, "0", STR_PAD_LEFT);
		//$monthname = strftime("%B",mktime(0,0,0,$month,01,$year));
		//$year = $row["year"];
                
                
                
               
                
               
                $monthcounter = 1;
                if (is_integer($counter) && is_integer($endcounter) && $endcounter > $counter) {
          
              echo "<table class=\"colourtable\">";
                
                while ($counter <= $endcounter)
                {
                    $monthname = strftime("%B",$counter);
                    $month = date("m",$counter);
                    $year = date("Y",$counter);
		echo "<tr><td> <a href=\"statistics.php?month=$month&amp;year=$year\">$monthname $year</a> </td><td><a href=\"statistics_csv.php?view=m&month=$month&year=$year\">CSV by module</a> </td><td>
                    <a href=\"statistics_csv.php?view=p&month=$month&year=$year\">CSV by page</a></td></tr>";
                $monthcounter++;
                $counter = mktime(0,0,0,$startmonth + $monthcounter,1,$startyear);
		}
                }
		echo "</table><br><br><br>";	
}
	
	

echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

