<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

	
$ip = ip("pc");
$proxy = ip("proxy");

$integers[] = "no";
$integers[] = "id";
$integers[] = "branch_id";
$integers[] = "category";
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
		
		$locations = array();
		$sql = "select * from hardware_locations where m = '$m'";
		$DB->query($sql,"inventory.php");
		while ($row = $DB->get())
		{
		$id = $row["id"];
		$locations[$id] = $row["name"];		
		}
			
		$categories = array();
		$sql = "select * from hardware_categories where m = '$m'";
		$DB->query($sql,"inventory.php");
		while ($row = $DB->get())
		{
		$id = $row["id"];
		$categories[$id] = $row["name"];		
		}
		
		
		
		
		
		
		
		
		
		
$insert = "";

if (isset($_REQUEST["branch_id"]))
{
	$branch_id = $DB->escape($_REQUEST["branch_id"]);
	$insert = " where location = '$branch_id'";
}

if (isset($_REQUEST["category"]))
{
	$category = $DB->escape($_REQUEST["category"]);
	$insert = " where category = '$category'";
}


		
$sql = "select * from hardware $insert  and m = '$m' order by location";		

$DB->query($sql,"helpdeskall.php");


$out = fopen('php://output', 'w');


header("content-type: text/csv \n");
			
header("Content-Transfer-Encoding: binary\n"); 
			
			//header("content-length: $size \n");
			
header("content-disposition: attachment; filename=\"inventory.csv\" \n");



echo "number, name , notes,location,category
";


while ($row = $DB->get()) 
	{
	$line[0] = $row["no"];
	$line[1] = $row["id"];
	$line[2] = $row["notes"];
	$location = $row["location"];
	$category = $row["category"];
	
	if (array_key_exists($location,$locations)) {$line[3] = $locations[$location];} else {$line[3] = "";}
	if (array_key_exists($category,$categories)) {$line[4] = $categories[$category];} else {$line[4] = "";}
	
		fputcsv($out, $line,',','"');
	
	
	}
	
	fclose($out);
}
?>

