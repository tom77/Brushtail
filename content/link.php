<?php
include ("../includes/initiliaze_page.php");
extract($_GET);


if (!isset($_SESSION['userid'])) {exit();}

if (isset($url))
{

if($headers = @get_headers($url,0))
{
//echo $headers[0];
$parts = explode( " ",$headers[0]);
//print_r($parts);
echo substr($parts[1],-3);

}
else 
{
	echo "504";
}
}

if (isset($page_id) && isinteger($page_id))
{
	
	$string = "{";
	

	$sql = "SELECT * FROM content where page_id = '$page_id'";
	$DB->query($sql,"editlinks.php");
	while ($row = $DB->get())
	{
	$content_id = $row["content_id"];
	$hits = $row["archive"];
	
     $string .= "\"$content_id\": \"$hits\",";
    

	}
	$string = substr($string, 0, -1);
	$string .=  "}";
	
	echo $string;
}
?>