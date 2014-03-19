
<?php
//tests file was called properly
if (isset($m))
{

	
	
	
	if (!isset($year))
	{
	$year = date("Y",time()); 	
	}
	$next = $year + 1;
	$prev = $year - 1;
	
echo "
<span style=\"font-size:2em\">$phrase[685] </span><br>
<div style=\"text-align:center\"> <a href=\"helpdesk.php?m=$m&amp;year=$prev&amp;event=stats\">$phrase[525]</a> &nbsp;<span style=\"font-size:2em\">$year</span> &nbsp;<a href=\"helpdesk.php?m=$m&amp;year=$next&amp;event=stats\">$phrase[526]</a><br>
<br>
";



	
	
	
	
	
	if ($DB->type == "mysql")
			{
	$sql = "SELECT count( * ) as total , month( day ) AS month  FROM itcalls where m= \"$m\" and  year( day ) = '$year' GROUP BY month";

			}
			
	
	else
			{
	$sql = "SELECT count( * ) as total , strftime('%m', day ) AS month  FROM itcalls where m= \"$m\" and  strftime('%Y', day ) = '$year' GROUP BY month";

			}		
			
	$DB->query($sql,"helpdeskstats.php");
	
	
	while ($row = $DB->get()) 
		{
		$month = $row["month"];		
		$total = $row["total"];	
		$calls[$month]	= $total;
		
		}
		
		
	if ($DB->type == "mysql")
			{	
	$sql = "SELECT count( * ) as total , month( datereported ) AS month  FROM helpdesk where m= \"$m\" and  year( datereported ) = '$year' GROUP BY month";	
			}
			
			
	else
			{	
	$sql = "SELECT count( * ) as total ,  strftime('%m', datereported )  AS month  FROM helpdesk where m= \"$m\" and  strftime('%Y', datereported ) = '$year' GROUP BY month";	
			}		
			
	$DB->query($sql,"helpdeskstats.php");
	
	
	while ($row = $DB->get()) 
		{
		$month = $row["month"];		
		$total = $row["total"];	
		$totallogged[$month]	= $total;
		
		}
		

		if ($DB->type == "mysql")
			{	
	$sql = "SELECT count( * ) as total , month( datereported ) AS month  FROM helpdesk where m= \"$m\" and cat = '0' and  year( datereported ) = '$year' GROUP BY month";	
	
			}
			
			
		else
			{	
			$sql = "SELECT count( * ) as total , strftime('%m', datereported ) AS month  FROM helpdesk where m= \"$m\" and cat = '0' and  strftime('%Y',datereported ) = '$year' GROUP BY month";	
				
			}
	$DB->query($sql,"helpdeskstats.php");
	//echo $sql;
	
	while ($row = $DB->get()) 
		{
		$month = $row["month"];		
		$total = $row["total"];	
		$nocat[$month]	= $total;
		
		}
	//$month = date("m",$t);
//$monthname = date("F",$t);

		
////////
if ($DB->type == "mysql")
			{	
$sql = "SELECT count( * ) AS total, month( datereported ) AS 
month ,  id
FROM helpdesk, `helpdesk_cat` 
WHERE helpdesk.cat = helpdesk_cat.id
GROUP BY MONTH , cat_name";
			}
			
else
			{	
$sql = "SELECT count( * ) AS total, strftime('%m', datereported ) AS 
month ,  id
FROM helpdesk, helpdesk_cat 
WHERE helpdesk.cat = helpdesk_cat.id
GROUP BY MONTH , cat_name";
			}		
			
			
$DB->query($sql,"edithelpdesk.php");
while ($row = $DB->get()) 
		{
		
			$month = $row["month"];		
		$total = $row["total"];	
		$id = $row["id"];
			$cattotal[$month][$id] = $total;	
			
		}

		
	
		
		
		
		
//put categories in array

	$sql = "select * from helpdesk_cat where m = '$m'";
	$DB->query($sql,"edithelpdesk.php");

while ($row = $DB->get())
      {	
     
      	$id =  $row["id"];
      
		$category[$id] = $row["cat_name"];
      }
	
// create month array

$counter = 1;
while ($counter <13)
{
$montharray[$counter] = strftime("%B",mktime(0, 0, 0, $counter, 01,  $year));
$counter++;
}

echo "<table class=\"colourtable\" cellpadding=\"5\"  style=\"margin-left:auto;margin-right:auto;\">
<tr><td></td>";

if (isset($callcounter) && $callcounter == 1)
		{
			echo "<td><b>$phrase[687]</b></td>";
		}

if (isset($category))
								{
								foreach ($category as $index => $value)
								{
								echo "<td><b>$value</b></td>";
								}
								}
								
if (isset($category))
{
echo "<td><b>$phrase[691]</b></td>";
}

echo "<td><b>$phrase[210]</b></td>";

echo "</tr>";	

foreach ($montharray as $monthnum => $monthname)
							{
							echo "<tr><td>$monthname</td>";
							
							
							
							
												if (isset($callcounter) && $callcounter == 1)
		{
			echo "<td>";
			
			if (isset($calls))
								{
								foreach ($calls as $index => $value)
								{
									//echo "$monthnum $index<br>";
								if ($monthnum == $index)
									echo "$value";
								}
								}
			echo "</td>";
		}
					
							
							
							
								if (isset($category))
								{
								foreach ($category as $id => $value)
								{
								echo "<td>";
									if (isset($cattotal))
									{
									////
									
									foreach($cattotal as $mon =>$cats)
										{
										
											if ($monthnum == $mon)
											{	
											
											foreach($cats as $catid =>$value)
												{
												if ($catid == $id)
												{	
												echo "$value";	
												}
												}
											
											}
									//	echo "$list $things";
  										 //foreach($things as $newlist=>$counter)
  										 	//{
   											//echo $counter;
   											//}
										}
									
									
									///
								
								
									
									}
									echo "</td>";
								}
								}
								
							
							if (isset($category))
							{
							echo "<td>";
							
							if (isset($nocat))
								{
								foreach ($nocat as $index => $value)
								{
									//echo "$monthnum $index<br>";
								if ($monthnum == $index)
									echo "$value";
								}
								}
							
							echo "</td>";
							}
							echo "<td>";
							if (isset($totallogged))
								{
								foreach ($totallogged as $index => $value)
								{
									//echo "$monthnum $index<br>";
								if ($monthnum == $index)
									echo "<b>$value</b>";
								}
								}
							
							
							echo "</td>
							
				
							
							</tr>";	
							}

echo "</table>";	
		

///////////////////
	
	
	}
echo "</div>";
	?>	


