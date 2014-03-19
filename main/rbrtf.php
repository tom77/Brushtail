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



$sql = "select name, location, resource_name, description, starttime from resource, resourcebooking where resource.resource_no = resourcebooking.resource and bookingno = \"$bookingno\"";


$sql_result = mysql_query($sql)	or die("Couldn't execute bookings query. ");
$row = mysql_fetch_array($sql_result);

					

$name = $row["name"];
//echo "$name";
$location = $row["location"];
$resource_name = $row["resource_name"];
$description = $row["description"];

$day = strftime("%A %x", $row["starttime"]);
$time = date("g:i a", $row["starttime"]);

$bookingdetails = " \par {\\b Name } \par $name \par \par {\\b Time } \par $time \par \par {\\b Date } \par $day \par \par {\\b Venue } \par $location ";		

$filename = "template.rtf";
$fp = fopen ($filename, "r");
$output = fread ($fp,filesize( $filename));
fclose ($fp);

//echo "$name $resource_name $location $description $daytime";
$output = str_replace("<<details>>", $bookingdetails, $output);
$output = str_replace("<<resource_name>>", $resource_name, $output);
//$output = str_replace("<<event_location>>", $location, $output);
//$output = str_replace("<<event_description>>", $description, $output);
//$output = str_replace("<<time>>", $time, $output);
//$output = str_replace("<<day>>", $day, $output);

header ("Content-type: application/msword");
header ("Content-disposition: inline, filename=booking.rtf");
echo "$output";
//echo $bookingdetails;

}
	
?>