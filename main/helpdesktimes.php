<?php

if (isset($m))
{
if ($showclock == 1)
{
	$startmonth = date("m");
	$monthsvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12');
	
	//put categories in array

	$sql = "select * from helpdesk_cat where m = '$m'";
	$DB->query($sql,"edithelpdesk.php");

while ($row = $DB->get())
      {	
     
      	$id =  $row["id"];
      
		$category[$id] = $row["cat_name"];
      }

	
		echo "
		<h4>$phrase[903]</h4>
		
		<form action=\"helpdesk.php\" method=\"get\" class=\"swouter\">
		<table class=\"swinner\">
		<tr><td  class=\"label\">$phrase[186]</td><td>
		<select name=\"month\">";
		foreach ($monthsvalue as $index => $value)
				{
				echo "<option value=\"$value\"";
				if ($value == $startmonth)
					{ echo " selected";}
				echo ">$value</option>";
				}
		echo "</select> ";
		
		if ($DB->type == "mysql")
		{
		$sql = "SELECT year( datereported ) AS year  FROM helpdesk where m= \"$m\"  GROUP BY year order by year desc";	
		}
		
		
		else
		{
		$sql = "SELECT strftime('%Y', datereported ) AS year  FROM helpdesk where m= \"$m\"  GROUP BY year order by year";	
		}
		
		echo "<select name=\"year\">";
		
		
	$DB->query($sql,"helpdesktimes.php");
	
	
	while ($row = $DB->get()) 
		{
		$y = $row["year"];	
		echo "<option value=\"$y\">$y</option>";
		}
		
		echo "</select> </td></tr>";
		
		if (isset($category))
		{
			echo "<tr><td class=\"label\">$phrase[884]</td><td><select name=\"cat\">
		<option value=\"0\">$phrase[676]</option>";
	foreach ($category as $key => $value)
    echo "<option value=\"$key\">$value</option>";


			
		echo "</select></td></tr> ";
		}
	
		
	/*
		echo "<tr><td>Order by</td><td><select name=\"orderby\">
		<option value=\"id\">$phrase[340]</option>
		<option value=\"date\">$phrase[340]</option>
	";
	foreach ($category as $key => $value)
    echo "<option value=\"$key\">$value</option>";


			
		echo "</select></td></tr> ";
	*/	
		
		echo "<tr><td></td><td><input type=\"submit\" name=\"submit\" value=\"Show job durations\">
		<input type=\"hidden\" value=\"$m\" name=\"m\">
		<input type=\"hidden\" name=\"event\" value=\"timestats\">
	
		";
		
			if (!isset($category)) 
		{
			echo "<input type=\"hidden\" name=\"cat\" value=\"0\">";
		}
		
		echo "</td></tr></table></form>";


		
		if (isset($_REQUEST["submit"]))
		{
			
	
	echo "
	
	
	<div class=\"swouter\" style=\"clear:both;\">
	<table class=\"swinner colourtable\">
	<thead><tr><td>$phrase[340]</td><td>$phrase[186]</td><td>$phrase[884]</td><td>$phrase[890] $phrase[904]</td></tr></thead>
	";
		
	
	if ($cat != 0)
	{
	$insert = "and cat = '$cat'";	
		
	}
	if ($DB->type == "mysql")
		{
	$sql = "SELECT *  FROM helpdesk where helpdesk.m = \"$m\" $insert and  year( datereported ) = '$year' and   month( datereported ) = '$month'  order by cat,datereported";
		}
		
	else
		{
	$sql = "SELECT *  FROM helpdesk where helpdesk.m = \"$m\" $insert and  strftime('%Y', datereported ) = '$year' and   strftime('%m', datereported ) = '$month'  order by cat,datereported";
		}
	

	$DB->query($sql,"helpdeskstats.php");
	//echo $sql;
	$total = 0;
	while ($row = $DB->get()) 
		{
		$probnum = $row["probnum"];		
		$cat_name = $row["cat_name"];	
		$datereported = substr($row["datereported"],0,10);
		$cat	= $row["cat"];
		$jobtime = $row["jobtime"];	
		$total = $total +  $jobtime;
		$jtime = secondsToMinutes($jobtime);
		
		echo "<tr><td><a href=\"helpdesk.php?m=$m&amp;event=edit&amp;probnum=$probnum\">$probnum</a></td><td>$datereported</td><td>";
		if ($cat != "0" && $cat != "") {echo $category[$cat];}
		echo "</td><td style=\"text-align:right\">$jtime</td></tr>";
		
		}
	$total = secondsToMinutes($total);
		echo "
		<tfoot><tr><td>Total</td><td></td><td></td><td style=\"text-align:right\">$total</td></tr></tfoot>
		</table></div>";
		}
}


}
?>