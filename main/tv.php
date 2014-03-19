<?php
//





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


include("../includes/leftsidebar.php");

echo "<div id=\"content\"><div> <h1 >$modname</h1>  ";



		
		$sql = "select * from tv_channels where m = '$m' order by name";
				
	//echo $sql;			

		$DB->query($sql,"tvedit.php");
		$num = $DB->countrows();
		$counter = 0;
		
		while ($row = $DB->get())
		{
			
		$tv_name = $row["name"];
		
		$tv_id = $row["id"];
		//$position[$counter] = $row["location_id"];	
		//$count[$counter] = $row["count"];		
		
		echo "<a href=\"../web/tvdisplay.php?c=$tv_id\">$tv_name</a><br>";
	
		}


        }//end contentbox
		echo "</div>";
//	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

