<?php



//tests file was called properly
if (isset($m))
{
	
$limit = 10 ;



	$sql = "select * from helpdesk_cat where m = '$m'";
	$DB->query($sql,"edithelpdesk.php");

        $category = array();
while ($row = $DB->get())
      {	
     
      	$id =  $row["id"];
      
		$category[$id] = $row["cat_name"];
      }


      
      // get list of user with edit permissions
      $sql = "select distinct(user.userid) as userid, first_name, last_name from permissions, user, group_members where permlevel > '1' and m = '$m'  
		and (
		(type = 'i' and permissions.id = user.userid)
		or 
		(type= 'g' and permissions.id = group_members.groupid and group_members.userid = user.userid)
		) group by user.userid";
		
      
    //  echo $sql;
    
		$DB->query($sql,"helpdeskadd.php");
		
		$last_names = array();
		while ($row = $DB->get())
      {	
      	$userid =  $row["userid"];
      	$first_names[$userid] =  $row["first_name"];
      	$last_names[$userid] =  $row["last_name"];
      	
      }
      

//if (isset($keywords)) { $keywords = remove($keywords);}




if (isset($_REQUEST["offset"]))
{ $offset = $_REQUEST["offset"];}




echo "<div style=\"text-align:center;margin-bottom:2em;\">";

	
if (isset($keywords))
	{
	$event = "search";
	$status = "";
	$sqlkeywords = $DB->escape($keywords);
	
	echo "<h2>$phrase[218]</h2><b>$keywords</b> <br>


";
	if ($DB->type == "mysql")
			{
				$sqlinsert = "MATCH (query,solution) AGAINST (\"$sqlkeywords\" IN BOOLEAN MODE)";
	$sql = "select *, UNIX_TIMESTAMP(datereported) AS datereported from helpdesk where m = \"$m\" and $sqlinsert order by probnum";
			}
			
	else
			{
				$keywords = trim($keywords);
       			
       			$words = explode(" ",$keywords);
       			
       			$string = "";
       			
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
				$string .= " and (query like '%$value%' or solution like '%$value%') ";	
				$counter++;
				}	
				if ($keywords == "") {$string = " and 1 == 2";}
				
	$sql = "select *, strftime('%s',datereported) AS datereported from helpdesk where m = \"$m\" $string order by probnum";
			}
				
			
	}
else
	{
		//print_r($_REQUEST);
		
		
	if(isset($_REQUEST["event"]) && $_REQUEST["event"] == "comp")
	{
		echo "<h2>$phrase[221]</h2>";
		$banner = $phrase[221];
		$status = 3;
		$event="comp";
		$statusinsert = " and status = '3'";
		//$javascriptinsert = "";
	}	
		
	elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "search")
	{
	
		$event ="search";
		
		//$javascriptinsert = "";
	}	
			

	elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "open")
	{
		echo "<h2>$phrase[400]</h2>";
		$event = "open";
	$banner = $phrase[400];
	$status = 2;
	$statusinsert = " and status = '2' ";
	//$javascriptinsert = "+ '&event=open'";
	}
	else 
	{
		echo "<h2>$phrase[220]</h2>";
	$event = "new";
	$banner = $phrase[220];
	$statusinsert = " and status = '1' ";
	$status = 1;
	//$javascriptinsert = "+ '&event=new'";
	}
	
		
		
	if(isset($_REQUEST["assigned"]))
	{
	$assigned = $_REQUEST["assigned"];
	if ($_REQUEST["assigned"] == "") {$assigninsert = "";}
	else{
		
		$assigninsert = " and assigned = '" . $DB->escape($_REQUEST["assigned"]) . "'";
		}	
	}
	else {$assigninsert = "";}
	
	
	if(isset($_REQUEST["cat"]))
	{
	$cat = $_REQUEST["cat"];
	if ($_REQUEST["cat"] == "") {$catinsert = "";}
	else{
		
		$catinsert = " and cat = '" . $DB->escape($_REQUEST["cat"]) . "'";
		}	
	}
	else {$catinsert = "";}
	
	
	
	
	//print_r($_REQUEST);
		
		if (isset($cat) && $cat != 0) {$insert = " and cat = '$cat' ";} else {$insert = "";}	
	if ($DB->type == "mysql")
			{	
	$sql = "select *, UNIX_TIMESTAMP(datereported) AS datereported from helpdesk where m = \"$m\" $catinsert $assigninsert $statusinsert order by probnum";
			}
			
		else
			{	
	$sql = "select *, strftime('%s',datereported) AS datereported from helpdesk where m = \"$m\" $catinsert $assigninsert $statusinsert order by probnum";
			}		
			
			
	
	}


	
	//echo $sql;
	

$DB->query($sql,"helpdeskall.php");
$numrows= $DB->countrows();




if (!isset($offset)) {$offset = 0;}
$displayoffset = 1 + $offset;
$displayend = $limit + $offset;
if ($displayend > $numrows) {$displayend = $numrows;}




if (isset($keywords))
	{
	
	
	$keywords = urlencode($keywords);
	$linkkeywords = "&keywords=$keywords";
	}
else
	{
		
	
	echo "
<form method=\"get\" action=\"helpdesk.php\">
	<b>$phrase[401]</b>

		<select name =\"event\">
		<option value=\"new\""; if ($status == 1) { echo " selected";} echo ">$phrase[220]</option>
		<option value=\"open\""; if ($status == 2) { echo " selected";} echo ">$phrase[400]</option>
		<option value=\"comp\""; if ($status == 3) { echo " selected";} echo ">$phrase[221]</option>
		
		</select>";
	
	if ($assignment == 1)
	{
	echo "&nbsp;<b>$phrase[1059]</b>
			<select name =\"assigned\">
		<option value=\"\">$phrase[676]</option>";
		foreach ($last_names as $userid => $last_name) {
			echo "<option value=\"$userid\"";
			if (isset($assigned) && $assigned == $userid) {echo " selected";}
			echo ">$last_name, $first_names[$userid]</option>";
		}
		
		
		echo "
		</select> ";
	}
	
if (count($category) > 0) {
	
	echo "&nbsp;<b>$phrase[884]</b> <select name=\"cat\"><option value=\"\"";
	if (!isset($cat) ) {echo " selected";}
	echo ">$phrase[676]</option>";
	
	foreach ($category as $cat_id => $catname)
	{
		echo "<option value=\"$cat_id\"";
		if (isset($cat) && $cat == $cat_id) {echo " selected";}
		echo ">$catname</option>";
	}
	
	echo "</select> ";
}
	

echo "<input type=\"submit\" value=\"$phrase[982]\"><input type=\"hidden\" name=\"m\" value=\"$m\"></form><br>";

	}
if ($displayend == 0) {$displayoffset = 0;}
echo "$displayoffset to $displayend of $numrows";



$insert = "";
	
	
	if (isset($assigned))
		{
		$insert .= "&assigned=$assigned";
		}
		
	if (isset($cat))
		{
		$insert .= "&cat=$cat";
		}
	
	
	if (isset($keywords))
		{
		$insert .= "&keywords=$keywords";
		}

	if ($event == "new")
		{ $insert .= "&event=$event";}
	
	if ($event == "open")
		{ $insert .= "&event=open";}
	if ($event == "comp")
		{ $insert .= "&event=comp";}
	
	
		




if (empty($offset)) {
    $offset=0;}

if ($numrows > $limit)
	{
	$prevoffset = $offset - $limit;
	$nextoffset = $offset + $limit;	
	
	
		
if ($offset == 0)
			{
			//$offset = $limit;
			}
	else	
		{
		 $printprev = "&nbsp; <a href=\"helpdesk.php?m=$m" . $insert . "&amp;offset=$prevoffset\">$phrase[215]</a> \n";
		}
	if ($nextoffset < $numrows)
			{
			$printnext = "<a href=\"helpdesk.php?m=$m&amp;event=all&amp;offset=$nextoffset$insert\">$phrase[216]</a> &nbsp; \n";	
			}
	 
	}	
if (!isset($printprev)) { $printprev = "";}
if (!isset($printnext)) { $printnext = "";}

$linkname= urlencode($modname);
$csv = "$m" . $insert;
echo "<a href=\"helpcsv.php?m=$csv\" style=\"margin-left:2em\"><img src=\"../images/calendar_empty.png\" alt=\"CSV\" title=\"CSV\"></a><table border=\"0\" width=\"96%\"  style=\"margin: 0 auto\"><tr><td align=\"left\">&nbsp;$printprev</td><td align=\"right\">$printnext&nbsp;</td></tr></table>";
//echo "offset is $offset";
echo "

<script type=\"text/javascript\">
 function pop_window(url) {
  var popit = window.open(url,'console','status,resizable,scrollbars,width=800,height=500');
 }
</script>
<table class=\"colourtable\" cellpadding=\"8\" width=\"96%\"  style=\"margin: 0 auto;text-align:left\">
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




while ($row = $DB->get()) 
	{
	$probnum = $row["probnum"];
	//$pcnum = $row["pcnum"];
	//$branch = formattext($row["branch"]);
	$datereported = strftime("%x",$row["datereported"]);
	//$name = formattext($row["name"]);
	$query = formattext($row["query"]);
	$solution = formattext($row["solution"]);
	
	if ($texttolink == 1)
	{
	$query = texttolink($query);
	$solution = texttolink($solution);
	}

	
	$status = formattext($row["status"]);
	
	//$allocatedto = formattext($row["allocatedto"]);
	$proxy = $row["proxy"];
	$ip = $row["ip"];
	$user = $row["username"];
	$jobtime = $row["jobtime"];
	$assignedto = $row["assigned"];
			
	$display = secondsToMinutes($jobtime);
	
	
	$statusArray[1] = $phrase[220]; 
	$statusArray[2] = $phrase[400]; 
	$statusArray[3] = $phrase[221]; 
	
	$statuscolour[1] = "#ff3333"; 
	$statuscolour[2] = "#009966"; 
	$statuscolour[3] = "#000000"; 
	
	echo "<tr><td valign=top>$probnum</td><td >$datereported";

	

	
	if ($assignedto != ""  && array_key_exists($assignedto,$last_names) && $assignment == 1)
	{
		
	echo "<br><br>$phrase[1059]<br>
	$last_names[$assignedto], $first_names[$assignedto]";	
		
	}

/*	
			if ( $showclock == 1)
{
	echo "<br><br>
<span class=\"timer\">$display</span>";
}
	*/
	
	echo "</td><td valign=top align=\"left\">$query<br>";
		if ($_SESSION['userid'] == 1)
						{
						echo "<br><span style=\"color:#999999\">$user<br>$ip $proxy</span>";
						}
						
										
if (isset($keywords))
	{
	echo "<br><span style=\"color:$statuscolour[$status]\"><b>$phrase[401]:</b> $statusArray[$status]</span>";
	}
	
	echo "<br><br><span style=\"color:#0000cc;\">$solution</span>
	
	
	</td>
	
	<td  valign=top><a href=\"javascript:pop_window('helpview.php?m=$m&amp;probnum=$probnum&amp;linkname=$linkname')\"><img src=\"../images/printer.png\" alt=\"$phrase[250]\" title=\"$phrase[250]\"></a><br>
<br><a href=\"helpdesk.php?m=$m&amp;event=edit&amp;probnum=$probnum";
	if (isset($event)) {echo "&amp;view=$event";}
	
	if (isset($offset))
		{
		echo "&amp;offset=$offset";
		}
	
	if (isset($linkkeywords)) {echo $linkkeywords;}	
		
	echo "\"><img src=\"../images/pencil.png\" alt=\"$phrase[26]\" title=\"$phrase[26]\"></a><br><br>
	<a href=\"helpdesk.php?m=$m&amp;probnum=$probnum&amp;event=delete";
	
	if (isset($offset))
		{
		echo "&amp;offset=$offset";
		}
	
	echo "\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></a>
	</td></tr>";
	}
	
	
	echo "</table>";
	
echo "<table border=\"0\" width=\"96%\"  style=\"margin: 0 auto\"><tr><td align=\"left\">&nbsp;$printprev</td><td align=\"right\">$printnext&nbsp;</td></tr></table>";




}
echo "</div>";
?>


	

