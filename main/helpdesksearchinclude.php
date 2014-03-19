<?php

if ($include == "yes")

{
	$limit = 10 ;
		$sql = "select name, ordering from modules where m = \"$m\"";
		$DB->query($sql,"page.php");
		$row = $DB->get();
		$modname = formattext($row["name"]);
		
		
echo "<div style=\"text-align:center\"><h1>$modname</h1><form action=\"helpdesksearch.php\" method=\"get\"><p><input type=\"hidden\" name=\"m\" value=\"$m\"> 

<input type=\"text\" size=\"25\" name=\"keywords\"> <input type=\"submit\" name=\"refsearch\" value=\"$phrase[282]\"></p> </form>";



if (isset($_REQUEST["keywords"]))
	{





if (isset($_REQUEST["offset"]))
{ $offset = $_REQUEST["offset"];}





	

	
	 if ($DB->type == "mysql")
			{
 	$keywords = $DB->escape($_REQUEST["keywords"]);
	$sqlinsert = "MATCH (query,solution) AGAINST (\"$keywords\" IN BOOLEAN MODE)";
	$sql = "select *, UNIX_TIMESTAMP(datereported) AS datereported from helpdesk where m = '$m' and $sqlinsert order by status, probnum";
				}
	
				
else
			{
			$keywords = trim($keywords);
       			
       			$words = explode(" ",$keywords);
       			
       			$sqlinsert = "";
       			
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
				$value  = $DB->escape($value);	
				$sqlinsert .= " and (query like '%$value%' or solution like '%$value%') ";	
				$counter++;
				}	
				if ($keywords == "") {$sqlinsert = " and 1 == 2";}	
			$sql = "select *, strftime('%s',datereported) AS datereported from helpdesk where m = '$m' $sqlinsert order by probnum";
			}
	

	
$DB->query($sql,"helpdesksearchinclude.php");
$numrows= $DB->countrows();




if (!isset($offset)) {$offset = 0;}
$displayoffset = 1 + $offset;
$displayend = $limit + $offset;
if ($displayend > $numrows) {$displayend = $numrows;}





	echo "<h2>$phrase[218]</h2><b>$keywords</b> <br>


";
	
	$keywords = urlencode($keywords);
	$linkkeywords = "&keywords=$keywords";
	

if ($displayend == 0) {$displayoffset = 0;}
echo "$displayoffset to $displayend of $numrows";



if (empty($offset)) {
    $offset=0;}

if ($numrows > $limit)
	{
	$prevoffset = $offset - $limit;
	$nextoffset = $offset + $limit;	
	
	if (isset($keywords))
		{
		$insert = "&keywords=$keywords";
		}
	else {$insert = "";}
	
if ($offset == 0)
			{
			//$offset = $limit;
			}
	else	
		{
		 $printprev = "&nbsp; <a href=\"helpdesksearch.php?m=$m&amp;offset=$prevoffset$insert\">$phrase[215]</a> \n";
		}
	if ($nextoffset < $numrows)
			{
			$printnext = "<a href=\"helpdesksearch.php?m=$m&amp;offset=$nextoffset$insert\">$phrase[216]</a> &nbsp; \n";	
			}
	 
	}	
if (!isset($printprev)) { $printprev = "";}
if (!isset($printnext)) { $printnext = "";}

$linkname= urlencode($modname);
	
echo "<table border=\"0\" width=\"96%\"  style=\"margin: 0 auto\"><tr><td align=\"left\">&nbsp;$printprev</td><td align=\"right\">$printnext&nbsp;</td></tr></table>";
//echo "offset is $offset";
echo "

<script type=\"text/javascript\">
 function pop_window(url) {
  var helppop = window.open(url,'','status,resizable,scrollbars,width=800,height=500');
  if (window.focus) {helppop.focus()}
 }
</script>
<table class=\"colourtable\" cellpadding=\"8\" width=\"96%\"  style=\"margin: 0 auto\">
<tr class=\"accent\"><td >$phrase[340]</td>
<td >$phrase[186]</td>
<td ></td><td >&nbsp;</td></tr>";


	


if ($offset == 0)
	{
		$ender = " desc limit $limit";
	}
else	
	{
	
	$ender = " desc  limit $offset, $limit";
	}

$sql = $sql . $ender;
$DB->query($sql,"helpdeskall.php");

	$statusArray[1] = $phrase[220]; 
	$statusArray[2] = $phrase[400]; 
	$statusArray[3] = $phrase[221];

		$statuscolour[1] = "#ff3333"; 
	$statuscolour[2] = "#009966"; 
	$statuscolour[3] = "#000000"; 


while ($row = $DB->get()) 
	{
	$probnum = $row["probnum"];
	//$pcnum = $row["pcnum"];
	//$branch = formattext($row["branch"]);
	$datereported = strftime("%x",$row["datereported"]);
	//$name = formattext($row["name"]);
	$query = formattext($row["query"]);
	$solution = formattext($row["solution"]);
	$status = formattext($row["status"]);
	
		if ($texttolink == "yes")
	{
	$query = linktotext($query);
	$solution = linktotext($solution);
	}
	
	//$allocatedto = formattext($row["allocatedto"]);
	$proxy = $row["proxy"];
	$ip = $row["ip"];
	$user = $row["username"];
	
	
	echo "<tr><td valign=top>$probnum</td><td valign=top>$datereported</td><td valign=top align=\"left\">
	 $query<br>";
		if ($_SESSION['userid'] == 1)
						{
						echo "<br><span style=\"color:#999999\">$user<br>$ip $proxy</span>";
						}
	
	echo "<br><span style=\"color:$statuscolour[$status];\">
	<b>$phrase[401]:</b> $statusArray[$status]</span><br><br><span style=\"color:#0000cc;\">$solution</span></td>
	
	<td  valign=top><a href=\"javascript:pop_window('helpview.php?m=$m&amp;probnum=$probnum&amp;linkname=$linkname')\">$phrase[250]</a><br>
<br><br>

	</td></tr>";
	}
	
	
	echo "</table>";
	
echo "<table border=\"0\" width=\"96%\"  style=\"margin: 0 auto\"><tr><td align=\"left\">&nbsp;$printprev</td><td align=\"right\">$printnext&nbsp;</td></tr></table>";


	}


echo "</div>";
	}


?>