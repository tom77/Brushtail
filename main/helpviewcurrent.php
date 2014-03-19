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

if (isset($_REQUEST["probnum"])) 
	{
	if (!(isinteger($_REQUEST["probnum"]))) 
	{$ERROR  = $phrase[72];}
	else {$probnum = $_REQUEST["probnum"];}
	}

if (isset($_REQUEST["status"])) 
	{
	if (!(isinteger($_REQUEST["status"]))) 
	{$ERROR  = $phrase[72];}
	else {$status = $_REQUEST["status"];}
	}

if(!isset($ERROR))
	{
		
			$sql = "select name from modules where m = '$m'";
	$DB->query($sql,"helpviewcurrent.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);	
		
	
	echo "<div style=\"padding:2em\"><div class=\"hide\"><h1 style=\"margin-bottom:0\">$modname</h1>
		<span style=\"float:right\" ><a href=\"javascript:window.print()\">$phrase[250]</a> &nbsp;
<a href=\"javascript:window.close()\">$phrase[266]</a></span></div><br><br>";
	
	
	 
	 
	 	 
	 if ($DB->type == "mysql")
			{
 
	 $sql = "select *, UNIX_TIMESTAMP(datereported) as datelogged from helpdesk where  m = \"$m\" and status= '$status' order by probnum desc";
			}
			
else
			{
	
	 $sql = "select *, strftime('%s',datereported) as datelogged from helpdesk where  m = \"$m\" and status= '$status' order by probnum desc";
	 
	 


			}	
	 
	
$DB->query($sql,"helpview.php");
	while ($row = $DB->get()) 
		{
			$probnum = $row["probnum"];
	//$branch = $row["branch"];
    $status = $row["status"];
     
     // $allocatedto = $row["allocatedto"];
      $query = nl2br($row["query"]);
       $answer = nl2br($row["solution"]);
      $date = strftime("%x",$row["datelogged"]);
			
	
		
		echo "<br><hr class=\"hide\">	<br><table cellpadding=\"3\" width=\"90%\" STYLE=\"page-break-after:always\">
		<tr><td width=\"30%\"><b>$phrase[340]</b></td><td>$probnum</td></tr>
	
<tr><td><b>$phrase[186]</b></td><td>$date</td></tr>

<tr><td><b>$phrase[401]</b></td><td>";

if ($status == 1) {echo $phrase[220];}
		elseif ($status == 2) {echo $phrase[400];}
		elseif ($status == 3) {echo $phrase[221];}
		
echo "</td></tr>
<tr><td colspan=\"2\"><br><b>$phrase[205]</b></td></tr>
<tr><td colspan=\"2\">$query</td><td></td></tr>";
		
			if ($answer <> "")
		{ echo "<tr><td colspan=\"2\"><br><b>$phrase[206]</b></td></tr>
<tr><td colspan=\"2\">$answer</td><td></td></tr>";}

		
		echo "</table>
		
		";	
		}
	echo "</div>";
	}
	

include ("../includes/footer.php");
	
?>