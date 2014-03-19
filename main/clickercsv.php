<?php
//
$limit = 10;

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);







if (isset($_REQUEST["m"]))	
{
	
$m = $_REQUEST["m"];

if ((isinteger($m)))
{
	
	$access->check($m);
	
	if ($access->thispage < 1)
		{
		
		$ERROR  =  "<div style=\"font-size:200%;margin-top:3em\">$phrase[1]</div>";
		
		}
	elseif ($access->iprestricted == "yes")
		{
	$ERROR  =  $phrase[0];
		}
	
	
}
else 
	{	
		$ERROR  =  "<h1>$phrase[72]</h1>";
	}
} else { $ERROR  =  "<h1>$phrase[866]</h1>";}




	
	
if (isset($_REQUEST["year"])) 
	{
	if (!(isinteger($_REQUEST["year"]))) 
	{$ERROR  = "not an integer";}
	else {$year = $_REQUEST["year"];}
	}

	
if (isset($_REQUEST["month"])) 
	{
	if (!(isinteger($_REQUEST["month"]))) 
	{$ERROR  = "not an integer";}
	else {$month = $_REQUEST["month"];}
	}

if (isset($_REQUEST["day"])) 
	{
	if (!(isinteger($_REQUEST["day"]))) 
	{$ERROR  = "not an integer";}
	else {$day = $_REQUEST["day"];}
	}
	
$ip = ip("pc");
$proxy = ip("proxy");







		
if (isset($ERROR))		
{
	echo "$ERROR";
	}
elseif (isset($WARNING))		
{
	warning($WARNING);
	}
else 
{	


    $array_cat_name = array();
    $array_cat_id = array();
	
$sql = "select name, ordering from modules where m = \"$m\"";
		$DB->query($sql,"helpdesk.php");
		$row = $DB->get();
		$modname = formattext($row["name"]);	
		
				$sql = "select cat_id, cat_name from  clicker_category where m = '$m' order by cat_name";
		$DB->query($sql,"clicker.php");
		while ($row = $DB->get())
      {
      $array_cat_id[] = $row["cat_id"];
      $array_cat_name[] = $row["cat_name"];
    
      }
      
      			$sql = "select location_id, location_name from clicker_location where m = '$m' order by location_name";
		$DB->query($sql,"clicker.php");
		while ($row = $DB->get())
      {
      $array_location_id[] = $row["location_id"];
      $array_location_name[] = $row["location_name"];
    
      }
		


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
			  	if ($day < 10) { $d = "0". $day;} else {$day = $d;}
			  	$insert .= " and strftime('%d',datetime ( timestamp , 'unixepoch','localtime' )) = '$d'";	}	
				
			}
		
			
			$sql = "select mode from clicker_options where m = '$m'";
	$DB->query($sql,"clickeradmin.php");
	$row = $DB->get();
	$mode = $row["mode"];
			
				
		if ($mode == "1" || $mode == "")
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
	}	


	$sum_total=0;
	
	$data =  "Location,";
	foreach ($array_cat_name as $key => $value)

	{
		$catid = $array_cat_id[$key];
		$col_total[$catid]=0;
		$data .=  "$value,";
	}
	$data .= "Total
";
	
	foreach ($array_location_id as $key => $locid)

	{
		$row_total = 0;
		$data .= "$array_location_name[$key],";
		
			foreach ($array_cat_id as $k => $catid)

			{
			$data .= "";
			if (isset($results_total))
			{
			$t = 0;
			foreach ($results_total as $r => $total)

			{
			if ($results_category[$r] == $catid && $results_location[$r] == $locid)
				{
				$t= $total;
				$row_total += $total;
				$col_total[$catid] += $total;
				}
			
			}
			} else {$t = 0;}
			$data .= "$t,";
			}
		
		
		$data .= "$row_total
";
		$sum_total += $row_total;
	}
	
	$data .= "All,";
	foreach ($array_cat_id as $key => $catid)

	{
		
		$data .= "$col_total[$catid],";
	}
	$data .= "$sum_total
";


	
	
	
	 header("content-disposition: attachment; filename=export.csv");		
	 					
	 header("content-type: text/csv");
    header('content-length: ' . strlen($data));
	
	echo($data);
?>

