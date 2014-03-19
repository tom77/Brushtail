<?php

set_time_limit(6000);

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");



include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>

<h2>$phrase[1079]</h2>";

if (isset($_REQUEST["submit"]))
{
$sql = $_REQUEST["sql"];

$DB->exec($sql);
    
    
}


$sql = "select pref_value from  preferences where pref_name = 'version' ";

$DB->query($sql,"sqlite.php");

$row = $DB->get();
//print_r($row);
$version = $row["pref_value"];	
	
echo "
    <h3>Current version is Brushtail $version</h3>
<form method=\"post\" action=\"sqlite.php\">
<textarea name=\"sql\" cols=\"60\" rows=\"20\">


</textarea>
<br>
<input type=\"submit\" name=\"submit\" value=\"$phrase[1079]\">
</form>












</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

