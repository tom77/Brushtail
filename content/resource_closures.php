<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");







if (isset($_REQUEST["m"]))
	{	
	
	if (isinteger($_REQUEST["m"]))
		{	
		$m = $_REQUEST["m"];	
		$access->check($m);
	
	if ($access->thispage < 3)
			{
		
			$ERROR  =  "<div style=\"text-align:center;font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
			}
	elseif ($access->iprestricted == "yes")
			{
			$ERROR  =  $phrase[0];
			}
	
		}
	else 
		{
		$ERROR  =  "$phrase[72]";
	
		}		
	}
else {
	$ERROR  =  "no module chosen";
}	

$integers[] = "resource";
$integers[] = "field";
$integers[] = "fee_applicable";
$integers[] = "book_multiple_days";
$integers[] = "print";
$integers[] = "recur";
$integers[] = "checkout";
$integers[] = "notify";


foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (!isset($ERROR))
{	
$sql = "select name from modules where m = \"$m\"";
$DB->query($sql,"resource.php");
			$row = $DB->get();
$modname = formattext($row["name"]);
	

	


include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div> <h1 class=\"red\">$modname</h1>
<h2>$phrase[478] </h2>
";
                
}                 
echo "</div></div>";
		
	 
	  	

include ("../includes/footer.php");