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




include ("adminpermit.php");



if (isset($month))
{
	
	 if ($_REQUEST["view"] == "m")
         {
             
              $sql = "select m , name from modules" ;
          $DB->query($sql,"statistics.php");
          while ($row = $DB->get()) 
		{
		
		$_m = $row["m"];
		$module_names[$_m] = $row["name"];
                }
             
	if ($DB->type == "mysql")
       		{
		
$sql = "select sum(totalviews)  as module_views, m from page_views where  month(view_date) = '$month' and year(view_date) = '$year'  group by m order by module_views desc";
       		}
       		
      else
       		{
		
$sql = "select sum(totalviews)  as module_views, m from page_views where  strftime('%m',view_date) = '$month' and strftime ( '%Y' , view_date  ) = '$year'  group by m order by module_views desc";
       		} 		
       
      
	$DB->query($sql,"statistics_cvs.php");
	 $out = fopen('php://output', 'w');

header("content-type: text/csv \n");
header("Content-Transfer-Encoding: binary\n"); 
header("content-disposition: attachment; filename=\"module_statistics_$year$month.csv\" \n");
        
        
        
        
        
		$total = 0;
		while ($row = $DB->get()) 
		{
		
		$m = $row["m"];
		$module_views = $row["module_views"];
		$total = $total + $module_views;
	
                $line = array();
		$line[0] = $m;
		if (array_key_exists($m,$module_names)) {$line[1] = $module_names[$m];} else {$line[1] = $phrase[1041];}
		$line[2] = $module_views;
                fputcsv($out, $line,',','"');
		}
		$line[0] = $phrase["213"];
                $line[1] = "";
                $line[2] = $total;
                
		fputcsv($out, $line,',','"');
                fclose($out);
         }
         
          if ($_REQUEST["view"] == "p")
         {
         
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
		
$sql = "select sum(totalviews) as page_views, page_id from page_views where  month(view_date) = '$month' and year(view_date) = '$year' and page_id != '' group by page_id order by page_views desc";
       		}
       		
  else
       		{
		
$sql = "select sum(totalviews)  as page_views, page_id from page_views where  strftime('%m',view_date ) = '$month' and strftime ( '%Y' , view_date  ) = '$year' and page_id != '' group by page_id order by page_views desc";
       		} 		
       
     // echo $sql;
	$DB->query($sql,"statistics.php");
	
        
        $DB->query($sql,"statistics_cvs.php");
	 $out = fopen('php://output', 'w');

header("content-type: text/csv \n");
header("Content-Transfer-Encoding: binary\n"); 
header("content-disposition: attachment; filename=\"page_statistics_$year$month.csv\" \n");
      
        
        
        
	$total = 0;
		while ($row = $DB->get()) 
		{
		
		$page_id = $row["page_id"];
		$page_views = $row["page_views"];
		$total = $total + $page_views;
		
                $line[0] = $page_id;
                
		if (isset($page_titles) && array_key_exists($page_id,$page_titles)) { $line[1] = $page_titles[$page_id];} else {$line[1] = $page_id;}
                $line[2] = $page_views;
                fputcsv($out, $line,',','"');
		}
	
		 $line[0] = $phrase["213"];
                $line[1] = "";
                $line[2] = $total;
                
		fputcsv($out, $line,',','"');
                fclose($out);
         }
         
        
}


?>