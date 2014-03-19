<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


if (isset($_REQUEST["resource_no"])) 
	{
	if (!(isinteger($_REQUEST["resource_no"]))) 
	{$ERROR  = $phrase[72];}
	else {$resource_no = $_REQUEST["resource_no"];}
	}

if (isset($_REQUEST["bookingno"])) 
	{
	if (!(isinteger($_REQUEST["bookingno"]))) 
	{$ERROR  = $phrase[72];}
	else {$bookingno = $_REQUEST["bookingno"];}
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
	





if(!isset($ERROR))
	{



$sql = "select name, location,resource_no, resource_name, description, starttime from resource, resourcebooking where resource.resource_no = resourcebooking.resource and bookingno = \"$bookingno\"";

$DB->query($sql,"rbprint.php");
	$row = $DB->get();

					

$name = $row["name"];
//echo "$name";
$venue = $row["location"];
$resource_name = $row["resource_name"];

$resource_no = $row["resource_no"];

$description = $row["description"];

$date = strftime("%A %x", $row["starttime"]);
$time = date("g:i a", $row["starttime"]);


$custom  = "";



      
         $sql = "select * from resource_custom_values where  booking = '$bookingno' ";
       //  echo $sql;
  $DB->query($sql,"resourcebooking.php");
       
        $field_values = array();
	while	($row = $DB->get())
        {
       	$field_id =$row["field_no"];	
	$field_values[$field_id]  =$row["field_value"];	
	}

    //    print_r($field_values);
     //   exit();
        
        	//$sql = "select * from resource_custom_fields where  output = '1' and resource = '$resource_no'  order by ranking";
                
                $sql = "select * from resource_custom_fields,resource_fields  where output = '1' and
resource_custom_fields.fieldno = resource_fields.field and                           
resource = '$resource_no' order by resource, ranking";
		
	$counter = 0;	
						
	$DB->query($sql,"resource.php");
	while ($row = $DB->get())
	{
	$field = $row["fieldno"]; 
	$label = htmlspecialchars($row["label"]);
	$type = $row["type"];
	$menu = $row["menu"];
	$values = explode("\r\n", $menu);
        
        $custom .= "<br><br><strong>$label</strong><br>";
        
        
        foreach ($field_values as $key => $value)
        {
         if ($field == $key)   
         {
           if ($type == "t" || $type == "m") {$custom .= "$value";}
        if ($type == "a") {$custom .= nl2br($value)  ;}   
         }
        }
  
        
        }




$filename = "template.htm";
$fp = fopen ($filename, "r");
$output = fread ($fp,filesize( $filename));
fclose ($fp);

//echo "$name $resource_name $location $description $daytime";

$output = str_replace("\$orgname", $PREFERENCES["orgname"], $output);
$output = str_replace("\$event", $resource_name, $output);
$output = str_replace("\$receipt", $phrase[265], $output);
$output = str_replace("\$name", $name, $output);
$output = str_replace("\$venue", $venue, $output);
$output = str_replace("\$time", $time, $output);
$output = str_replace("\$date", $date, $output);
$output = str_replace("\$custom", $custom, $output);







header ("Content-type: text/html");
header ("Content-disposition: inline, filename=booking.htm");
echo "$output";
//echo $bookingdetails;

}
	
?>