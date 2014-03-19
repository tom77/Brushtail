<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$bodyjavascript = "onLoad=\"self.focus()\"";

include ("../includes/htmlheader.php");



$ip = ip("pc"); 

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
	
		
	


if(!isset($ERROR))
	{
	
	 $sql = "select jobtime  from helpdesk where  m = \"$m\" and probnum = \"$probnum\"";
	
$DB->query($sql,"helpview.php");
	$row = $DB->get();
	//$branch = $row["branch"];
    // $name = $row["name"];
     
     // $allocatedto = $row["allocatedto"];
      $jobtime = $row["jobtime"];
      
			
		echo "<div style=\"padding:2em\"><h1 style=\"margin-bottom:0\">$phrase[890]</h1>
		
		<br>
		<table style=\"border-style:none\">
		<tr><td>$phrase[340]</td><td>$probnum</td></tr>
		<tr><td></td><td><span id=\"time\">0:00</span></td></tr>
		</table>
		
		<button value=\"stop\" type=\"button\">$phrase[891]</button>
	

		
	<script type=\"text/javascript\" src=\"clock.js\"></script>


		
		";
		
	
	
	}
	

include ("../includes/footer.php");
	
?>