
<?php
//tests file was called properly
echo "<h1>IT calllllll</h1>";
	
	echo "<div style=\"text-align:center\">";



	
	echo "<h1 class=\"wide\">Call counter</h1>";
	




	if (isset($_REQUEST["monthnumber"]))
		
	{
		$year = $DB->escape($_REQUEST["year"]);
		$monthnumber = $DB->escape($_REQUEST["monthnumber"]);
	
	 
	 
		

	
	echo "<table border=\"1\" cellpadding=\"5\" align=\"center\"><tr><td><b>Weekday</b></td><td><b>Date</b></td><td><b>Total</b></td>";
	

	
	 echo "</tr>";
	 
	 
	 
	  $daysinmonth = date("t", mktime(0, 0, 0,  $monthnumber  , 01 , $year));	
		
		$dayofmonth = 1;
		
		$sql = "SELECT count( * ) AS total, DAY(day) AS day FROM itcalls
		WHERE m= \"$m\" and month( day ) = \"$monthnumber\" AND year( day ) = \"$year\" GROUP BY day" ;
		$DB->query($sql,"helpdeskstats.php");
		
		while ($row = $DB->get()) 
		{
		$total[$row["day"]] = $row["total"];
		}

		
		while ($dayofmonth <= $daysinmonth) 
		{
		
		$date = strftime("%x", mktime(0, 0, 0,  $monthnumber  , $dayofmonth , $year));	
		$dayname = strftime("%A", mktime(0, 0, 0,  $monthnumber  , $dayofmonth , $year));	
        
		echo "<tr><td>$dayname</td><td>$date</td>";
		
		
		
		//counts intranet jobs
		echo "<td> ";
		if (array_key_exists($dayofmonth,$total))
		{
			echo "$total[$dayofmonth]" ;
		}	else {echo "&nbsp;";}

		echo "</td>";
				
			
				
		
				
			echo "</tr>";
			$dayofmonth++;
           }
	echo "</table><br>
<br>
";
	}
	else
	{
	
	
	
		
	
	
	echo "<table border=\"1\" cellpadding=\"5\" align=\"center\"><tr><td><b>Month</b></td><td><b>Total</b></td>";
	
	
	
	
	echo "</tr>";

	//$sql = "select month(datereported) as monthnumber, year(datereported) as year from helpdesk where m = \"$m\" group by year(datereported) desc, month(datereported) desc ";	
	$sql = "SELECT count( * ) as total , month( day ) AS month , year( day) AS year FROM itcalls where m= \"$m\" GROUP BY year desc, month desc";	

		$DB->query($sql,"helpdeskstats.php");
		$num= $DB->countrows();
		//$count = 0;
		while ($row = $DB->get()) 
		{
		$total = $row["total"];
		$month = $row["month"];
		$monthnumber = str_pad($month, 2, "0", STR_PAD_LEFT);
		
		$year = $row["year"];
		 $monthname  = strftime("%B",mktime(0, 0, 0, $monthnumber, 01,  $year));
        
		echo "<tr><td align=\"left\"> <a href=\"helpdesk.php?m=$m&amp;event=stats&amp;monthnumber=$monthnumber&amp;year=$year\">$monthname $year</a></td><td>$total</td>";
		
		
				
				
				//counts jobsheet jobs	
		//	echo "<td>";
		//	$sql = "select count(*) as count from helpdesk where m= \"$m\" and month(datereported) = \"$monthnumber\" and year(datereported) = \"$year\"";
		//	$DB->query($sql,"helpdeskstats.php");
		//
		//	$row2 = $row = $DB->get();
				
		//
			//	$count = $row2["count"];
			//	echo "$count";
///	echo "</td>";
				
				
				
				
				
				echo "</tr>";
		}

	echo "</table>";

	

echo "</div><br>
<br>
";		
	
	}
	?>	


