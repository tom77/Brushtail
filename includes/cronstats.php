<?php

include ("../includes/initiliaze_page.php");
$time = time();
$array = array();




if ($PREFERENCES['loghits'] == 1 && trim($PREFERENCES['hitslog']) != "" && (($PREFERENCES['statslog'] + 86400 ) < $time))
{
    
    echo "<h1>Updating statistics</h1>";
    

                
$lines = file($PREFERENCES['hitslog'], FILE_IGNORE_NEW_LINES);
                
 foreach ($lines as $line_num => $line)               
{


$p = explode(",",$line);

//print_r($parts);
//echo "hello $line<br>";



if (array_key_exists(2,$p))
{
    //print_r($parts);
   // echo "<br>";
$m = $p[0];
$page_id = $p[1];
$view_date = $p[2];

$key = $m . ":" . $page_id . ":" . $view_date;
//echo "key is $key <br>";

if (array_key_exists($key,$array)) {$array[$key] = $array[$key] + 1;} else {$array[$key] = 1;}
}
}

file_put_contents($PREFERENCES['hitslog'], '');
   
 //print_r($array);           
  
 $twodaysago = time() - 172800; 
 $now = time();
 
 if ($DATABASE_FORMAT == "mysql")
 {
 $sql = "select * from page_views where unix_timestamp(view_date) > '$twodaysago' ";
}
else
{
  $sql = "select * from page_views where strftime('%s',view_date) > '$twodaysago' ";   
    
}

//echo $sql;

 $dbArray = array();
 $DB->query($sql,"cronstats.php");	  
	while ($row = $DB->get())
        {
	$m = $row["m"];
        $page_id = $row["page_id"];
        $view_date = $row["view_date"];
        $string = $m . ":" . $page_id . ":" . $view_date;
        
    //   echo "$string <br>";
        $dbArray[] = $string;
        }
 
 
     //   print_r($dbArray);
      
        
        
        
 foreach($array  as $k => $value)
 {
     $k = trim($k);
    $parts = explode(":",$k);
  //  print_r($parts);
    echo "$k<br>";
    
   
    
    
    
    
    $m = $parts[0];
    $page_id = $parts[1];
    $view_date = $parts[2];
    
    if (in_array($k,$dbArray))
    {
     $sql = "update page_views set totalviews = totalviews + $value where m = '$m' and page_id = '$page_id' and view_date = '$view_date'";   
    }
    else
    {
    $sql = "insert into page_views values('$m','$page_id','$view_date','$value')"; 
    }
    //echo $sql;
    $DB->query($sql,"cronstats.php"); 
     
     
     
 }
            

           // $sql = "update preferences set pref_value = '$time' where pref_name = 'statslog'";
         //   $DB->query($sql,"cronstats.php");
} else {  echo "<h1>Statistics disabled</h1>";}


?>