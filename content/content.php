<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");



include("../includes/leftsidebar.php");
							



		echo "<div id=\"content\"><div><h1 class=\"red\">$phrase[278]</h1>";

$access->cmenu();                
           
$linkarray = $access->contentlink;		
$menuarray = $access->contentmenu;	
$typearray = $access->contenttype;
		
$count = count($menuarray);
if ($count > 0) 		
{

echo "<ul  class=\"listing\">";

foreach ($menuarray as $mod => $name)
	{
		
	if ($typearray[$mod] == "x")
	{
	echo "<li><a href=\"customedit.php?m=$mod\"> $name</a></li>";		
	}
	else 
	{
	echo "<li><a href=\"$linkarray[$mod]?m=$mod\"> $name</a></li>";	
	}
	
	}
echo "</ul>";
	
}
	echo "</div></div>";	
	
	include("../includes/rightsidebar.php");
		include ("../includes/footer.php");
?>

