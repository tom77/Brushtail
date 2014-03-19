<?php


include ("../includes/initiliaze_page.php");

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");		

include("../includes/leftsidebar.php");

$error["input"] = "$phrase[988]";
$error["ipaccess"] = "$phrase[0]";
$error["permissions"] = "$phrase[1]"; 
$error["module"] = $phrase[1086];

$index = $_REQUEST["error"];
		
echo "<div id=\"content\">
<div style=\"color:red;font-size:200%;margin-top:3em\"><h1>$phrase[989]</h1>$error[$index]</div>


<div>";
	  	

echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   
include ("../includes/footer.php");


?>