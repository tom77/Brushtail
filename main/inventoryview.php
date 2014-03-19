<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$bodyjavascript = "onLoad=\"self.focus()\"";

include ("../includes/htmlheader.php");



$ip = ip("pc"); 

if (isinteger($_REQUEST["m"]))
{
	$m = $_REQUEST["m"];
	$access->check($m);
	
	if ($access->thispage < 1)
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
$ERROR  =  $phrase[72];	
}

if (isset($_REQUEST["no"])) 
	{
	if (!(isinteger($_REQUEST["no"]))) 
	{$ERROR  = $phrase[72];}
	else {$no = $_REQUEST["no"];}
	}



if(!isset($ERROR))
	{
	
	 $sql = "select * from hardware where  m = \"$m\" and no = \"$no\"";
	
$DB->query($sql,"inventoryview.php");
	$row = $DB->get();
		$id = $row["id"];
      $location = $row["location"];
       $category = $row["category"];
      $notes = nl2br($row["notes"]);
			
		echo "<br><table border=\"0\" align=\"center\" cellpadding=\"7\">	
	
	
	<tr><td align=\"right\"><b>ID</b></td><td align=\"left\"><b>$id</b></td></tr>
	<tr><td align=\"right\"><b>Category</b></td><td align=\"left\">$category</td></tr>
	<tr><td align=\"right\"><b>Location</b></td><td align=\"left\">$location</td></tr>
	 <tr><td valign=\"top\" align=\"right\"><b>Notes</b></td><td align=\"left\">$notes<br>

			
</td></tr>
	</table>";	
	
	
	}
	

include ("../includes/footer.php");
	
?>