<?php



include ("../includes/initiliaze_page.php");
include ("resource_paid.php");
$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

	
$ip = ip("pc");
$proxy = ip("proxy");

  $rno = $DB->escape($_REQUEST["rno"]);

$integers[] = "m";



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (isset($_REQUEST["keywords"]))
{ 
	if ($_REQUEST["keywords"] == "")
	{ $ERROR = $phrase[219];} else {
	$keywords = $_REQUEST["keywords"];}
}


	
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
			$sql = "select * from resource where m = '$m'";
     
		$DB->query($sql,"resourcebooking.php");
	while ($row = $DB->get())
		{
		$index= $row["resource_no"];
                $openinghours = $row["openinghours"];
		$rnames[$index] = $row["resource_name"];
		}
                
                
                $custom_label = array();
                
                if ($rno != "all")
                {
                   	//$sql = "select * from resource_custom_fields where resource = '$rno'";
                        $sql = "select * from resource_custom_fields,resource_fields  where 
resource_custom_fields.fieldno = resource_fields.field and resource_fields.resource = '$rno order by resource, ranking";
                        
                        
                        
                        
     //echo $sql;
		$DB->query($sql,"resourcebooking.php");
	while ($row = $DB->get())
		{
		$field = htmlspecialchars($row["fieldno"]);
		$custom_label[$field] = htmlspecialchars($row["label"]);
		$custom_type[$field] = htmlspecialchars($row["type"]);
		$custom_menu[$field] = htmlspecialchars($row["menu"]);
		
	}
	
                }
		
		$year = $DB->escape($_REQUEST["year"]);
		$month = $DB->escape($_REQUEST["month"]);
			
     $string1 = "";
     $string2 =  "";
       		      if ( $year != "0") {
                          $string1 .= " and FROM_UNIXTIME(starttime,\"%Y\") = '$year' "; 
                          $string2 .= " and FROM_UNIXTIME(starttime,\"%Y\") = '$year' "; 
                      }
                        if ( $month != "0") {
                            $string1 .= "and FROM_UNIXTIME(starttime,\"%c\") = '$month' ";
                            $string2 .= "and FROM_UNIXTIME(starttime,\"%c\") = '$month' ";
                        }
       			
                        if (isset($_REQUEST["keywords"]))
                        {
                             $keywords = trim($_REQUEST["keywords"]);
       			$words = explode(" ",$keywords);
       			
                        
                  
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
                                $value = $DB->escape($value);
				$string1 .= " and (name like '%$value%' or field_value like '%$value%') ";
                                $string2 .= " and (name like '%$value%') ";
				$counter++;
				}	
				if ($keywords == "") {$string1 = " and '1' = '2'"; $string2 = " and '1' = '2'"; }
                        }       
                 
                        
                           $field_values = array();
              
                if ($rno != "all")
                        
                        {
                        
                        
                     $sql = "select bookingno, field_no, field_value from resource_custom_values, resourcebooking where 
                         resourcebooking.bookingno = resource_custom_values.booking and resource = '$rno' $string1 ";
                    // echo $sql;
  $DB->query($sql,"resourcebooking.php");
        //$field_ids = array();
      
	while	($row = $DB->get())
        {
       	$field_id =$row["field_no"];
        
        $bookingno =$row["bookingno"];
        
        $index = $bookingno . "_" . $field_id;
        
	$field_values[$index]  =$row["field_value"];	
	}                  
                        }                        
                                
                                
           if ($rno == "all")
                        
                        {
                 $sql = "select resource, name,staffname,resource,bookingno,starttime,endtime,paid from resourcebooking, resource where resource.m = '$m'
                     and resourcebooking.resource = resource.resource_no and cancelled = '0'  $string2";
                        }
 else {
                   
           
          $sql = "select resource, name,staffname,bookingno,starttime,endtime,paid from resourcebooking where cancelled = '0' and  resource = '$rno' $string2
                    union
                    select resource, name,staffname,bookingno,starttime,endtime,paid from resource_custom_values, resourcebooking 
                         where resourcebooking.bookingno = resource_custom_values.booking and resource = '$rno' $string1 
                    ";
          
 }
            //resource.resource_no = resourcebooking.resource and
             $DB->query($sql,"resourcebooking.php");
      
      

                      
     


$out = fopen('php://output', 'w');


header("content-type: text/csv \n");
			
header("Content-Transfer-Encoding: binary\n"); 
			
			//header("content-length: $size \n");
			
header("content-disposition: attachment; filename=\"bookings.csv\" \n");





   echo "$phrase[226],$phrase[233],$phrase[242],$phrase[243],";
            foreach ($custom_label as $key => $value) {
                echo "$value,";
            }
            echo "$phrase[138],$phrase[227]
";                


while ($row = $DB->get()) 
	{
    
    
      $name = htmlspecialchars($row["name"]);
            $staffname = htmlspecialchars($row["staffname"]);
           // $cancelled = $row["cancelled"];
            $bookingno = $row["bookingno"];
            $resource = $row["resource"];
            $starttime = $row["starttime"];
            $start = date("g:ia",$row["starttime"]);
	   $start.= strftime(" %x",$row["starttime"]);	
           $paid = $row["paid"];
           $end = date("g:ia",$row["endtime"]);
	   $end.= strftime(" %x",$row["endtime"]);
           
            
           $line[0] = $name;
           $line[1] = $rnames[$resource];
           $line[2] = $start;
           $line[3] = $end;
           
       
        $counter = 3;
         foreach ($custom_label as $key => $value) {
             $counter++; 
             
             $index = $bookingno . "_" . $key;
                if (array_key_exists($index,$field_values)) {$text =  $field_values[$index];} else {$text = "";}
             $line[$counter] = $text;
             
            
            }
       
          $counter++;
          
           if (array_key_exists($paid,$paid_label)) {$text = $paid_label[$paid];} else {$text = "";}
          
       
           $line[$counter] = $text;
           $counter++;
            $line[$counter] = $staffname; 
       
    
    
	

	
		fputcsv($out, $line,',','"');
	
	
	}
	
	fclose($out);
}
?>

