<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$ip = ip("pc");
$proxy = ip("proxy");
	
$integers[] = "cat_id";
$integers[] = "content_id";
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



	
	if (isset($ERROR))
	{
	header("Location: $url" . "error.php?error=input");
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
	$sql = "select name from modules where m = '$m'";
	$DB->query($sql,"links.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);
		
	page_view($DB,$PREFERENCES,$m,"");	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

		
		include("../includes/leftsidebar.php");
		
		echo "<div id=\"content\"><div> <h1 >$modname</h1>  ";


		
				$sql = "SELECT * FROM page where m = '$m' order by pageorder, page_title";
	$DB->query($sql,"links.php");
			
			
	
	

		
		echo "<br><br><ul id=\"horizontalnav\">";
	
	while ($row = $DB->get())
	{
		$cat = $row["page_id"];
		if (!isset($cat_id))
	{
		$cat_id = $cat;
	}
	$name = $row["page_title"];
	
	echo "<li><a href=\"links.php?m=$m&cat_id=$cat\"";
	
	if (isset($cat_id) && $cat_id == $cat) {echo " id=\"hcurrent\"";}
	echo ">$name</a></li>"; 
	}	
	
	echo "</ul><br><br>";
	
	
	if (isset($cat_id))
	{
echo "<br><p style=\"margin-top:2em;clear:both\">";


			$sql = "SELECT * FROM content where page_id = '$cat_id'";
	$DB->query($sql,"links.php");
	$num = $DB->countrows();
	

		$sql = "SELECT * FROM content where page_id = '$cat_id' order by page_order, title";
	$DB->query($sql,"links.php");
	while ($row = $DB->get())
	{
		$id = $row["content_id"];
	$title = $row["title"];
	$body = $row["body"];
	$body = substr_replace($body, '', 0,5);
	$pos = strpos($body,"[/url] ");

	$url = urlencode(substr($body, 0, $pos));
	$pos = $pos + 7;
	$body = substr_replace($body, '', 0,$pos);
	//echo "<p><strong><a href=\"javascript: click('$url','$id')\" >$title</a></strong><br>$body</p><br>"; 
	echo "<p style=\"clear:both\"><strong><a href=\"redirect.php?url=$url&content_id=$id\">$title</a></strong><br>$body</p><br>"; 
	
	
	
	
	}	
	
	

	}
	
	
		//end contentbox
		echo "</div></div>";

		include("../includes/rightsidebar.php");   
		
}
	

include ("../includes/footer.php");

?>

