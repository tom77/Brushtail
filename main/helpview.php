<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$bodyjavascript = "onLoad=\"self.focus()\"";

include ("../includes/htmlheader.php");



$ip = ip("pc"); 



if (isset($_REQUEST["probnum"])) 
	{
	if (!(isinteger($_REQUEST["probnum"]))) 
	{$ERROR  = $phrase[72];}
	else {$probnum = $_REQUEST["probnum"];}
	}


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
	
		
		$sql  = "select * from helpdesk_options where m = '$m'";	
	$DB->query($sql,"helpdesk.php");
	
	$row = $DB->get();
	$callcounter = $row["callcounter"];
	$email= $row["email"];	
	$email_action= $row["email_action"];
	$search = $row["search"];
	$delbutton = $row["delbutton"];
	$showclock = $row["showclock"];	
		
		
		
	
	 
	 
	 if ($DB->type == "mysql")
			{
 $sql = "select *, UNIX_TIMESTAMP(datereported) as datelogged, UNIX_TIMESTAMP(datefixed) as datefixed  from helpdesk where  m = \"$m\" and probnum = \"$probnum\"";
			}
			
else
			{
 $sql = "select *, strftime('%s',datereported) as datelogged, strftime('%s',datefixed) as datefixed  from helpdesk where  m = \"$m\" and probnum = \"$probnum\"";

			}	
	 
	 
	 
	 
	 
	
$DB->query($sql,"helpview.php");
	$row = $DB->get();
	//$branch = $row["branch"];
    // $name = $row["name"];
     
     // $allocatedto = $row["allocatedto"];
      $status = $row["status"];
      $query = nl2br($row["query"]);
       $answer = nl2br($row["solution"]);
      $date = strftime("%x",$row["datelogged"]);
     //$datefixed = $row["datefixed"];
     $dateclosed = strftime("%x",$row["datefixed"]);
     $jobtime = $row["jobtime"];
     	$display = secondsToMinutes($jobtime);
			
		echo "<div style=\"padding:2em\"><h1 style=\"margin-bottom:0\">$_REQUEST[linkname]</h1>
		<span style=\"float:right\" class=\"hide\"><a href=\"javascript:window.print()\">$phrase[250]</a> &nbsp;
<a href=\"javascript:window.close()\">$phrase[266]</a></span><br><br>
		
		<table cellpadding=\"3\">
		<tr><td><b>$phrase[340]</b></td><td>$probnum</td></tr>
		
<tr><td><b>$phrase[186]</b></td><td>$date</td></tr>
<tr><td><b>$phrase[401]</b></td><td>";
		if ($status == 1) {echo $phrase[220];}
		elseif ($status == 2) {echo $phrase[400];}
		elseif ($status == 3) {echo $phrase[221];}
		
		echo "</td></tr>";
		if ($status == 3)
		{
		echo "<tr><td><b>$phrase[402]</b></td><td>$dateclosed</td></tr>";	
		}
		
		if ($showclock == 1)
		{
			echo "<tr><td>	<span id=\"clock\"><button id=\"clockbutton\" onclick=\"updatePage('ajax.php?event=starttimer&amp;m=$m&amp;probnum=$probnum','clock');runclock($jobtime);return false;\">Start timer</button> <span id=\"timer\">$display</span> $phrase[904]</span></td></tr>";
		}
		
echo "<tr><td colspan=\"2\"><br><b>$phrase[205]</b></td></tr>
<tr><td colspan=\"2\">$query</td><td></td></tr>";


		if ($answer <> "")
		{ echo "<tr><td colspan=\"2\"><br><b>$phrase[206]</b></td></tr>
<tr><td colspan=\"2\">$answer</td><td></td></tr>";}
		
		echo "</table></div>";	
	
	
	}
	
echo "<script type=\"text/javascript\" src=\"helpdesk.js\"></script></div>";
include ("../includes/footer.php");
	
?>