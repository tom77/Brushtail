<?php

set_time_limit(6000);

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");



include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>

<h2>$phrase[921]</h2>";

if (isset($_REQUEST["event"]))
{
echo "Vacuum operation may take some time....<br><br>";	
$DB->vacuum();
	
	
}
else {
	echo "<a href=\"vacuum.php?event=vacuum\">$phrase[921]</a> $SQLITE";
}



	
	
echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

