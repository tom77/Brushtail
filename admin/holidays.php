<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");


include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>";



	
	
	
if (isset($_REQUEST["d"]))
{ 
	
$d = $DB->escape($_REQUEST["d"]);
$m = $DB->escape($_REQUEST["m"]);
$y = $DB->escape($_REQUEST["y"]);
	
	
}	


			if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete")
			
				{
					if (!isset($ERROR))
					{
					
					//if ($DB->type == "mysql")
     //  	{
	//	$date = $y.$m.$d;
     //  	}
     //  	if ($DB->type == "sqlite")
     //  	{
		$date = $y."-".$m."-".$d;;
       //	}	
						
						
						
					$sql = "delete from holidays where holiday = \"$date\"";	
					
					$DB->query($sql,"holidays.php");
					
					$DB->tidy("holidays");
					}
				}
				
				
			elseif (isset($_REQUEST["update"]) && $_REQUEST["update"] == "add")
				{
				$day = $DB->escape($_REQUEST["day"]);
				$month = $DB->escape($_REQUEST["month"]);
				$year = $DB->escape($_REQUEST["year"]);
			
				if ($_REQUEST["name"] == "")
				{
					$ERROR = "You did not enter a name.";
				}
				else {	$name = $DB->escape($_REQUEST["name"]);}
				
			if ($DB->type == "mysql")
			{
				$holiday =  date("Ymd", mktime(0, 0, 0, $month, $day, $year));
				}
			else
			{
			$holiday =  date("Y-m-d", mktime(0, 0, 0, $month, $day, $year));	
			}
			// echo "hello world $holiday $ERROR";
				
				
				if (!isset($ERROR))
					{ 
				$sql = "insert into  holidays values (\"$holiday\", \"$name\")";	
					$DB->query($sql,"holidays.php");
					
					}
				}
				
				
				
			if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delete")
				{
				
	echo "<br><b>$phrase[14]</b><br><br>
	<a href=\"holidays.php?update=delete&amp;d=$d&amp;m=$m&amp;y=$y\">$phrase[12]</a> | <a href=\"holidays.php\">$phrase[13]</a>";
	
				}
				
				
				else
				{		
	
			echo "<h2>$phrase[175]</h2>
			<div >
			<form action=\"holidays.php\" method=\"POST\" ><fieldset>
			<legend>$phrase[176]</legend><table  cellpadding=\"4\">";
			?>
			<tr><td><b>Holiday name</b></td><td><input type="text" name="name" size="50" maxlength="100"></td></tr>
		
			<tr><td><b><?=$phrase[186]?></b> </td><td><select name="day">
			<?php
			$counter = 1;
			while ($counter < 32)
				{
				$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
				echo "<option value=\"$counter\">$counter</option>";
				$counter++;
				}
			
			$year = date("Y"); 
			echo "</select>
			Month<select name=\"month\">";
			$counter = 1;
			while ($counter < 13)
				{
				$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
				echo "<option value=\"$counter\">$counter</option>";
				$counter++;
				}
			
			echo "</select>
			Year<select name=\"year\">";
			
			echo "<option value=\"$year\">$year</option>";
			$year++; 
			echo "<option value=\"$year\">$year</option>";
			$year++; 
			echo "<option value=\"$year\">$year</option>";
			
			echo "</select>";
			
			
			?>
		</td></tr><tr><td></td><td>
			<input type="hidden" name="update" value="add">
			<input type="submit" name="submit" value="Add holiday">
			</td></tr></table></fieldset>
			</form></div>
			
<?php

echo "
<div  style=\"clear:both;\">
<form action=\"holidays.php\" ><fieldset><legend>$phrase[175]</legend><br><table  cellpadding=\"5\" style=\"text-align:left\";><tr><td><b>Date</b></td><td><b>Weekday</b></td><td><b>Holiday name</b></td><td></td></tr>";

if ($DB->type == "mysql")
       		{
	// $sql = "select name,holiday,  UNIX_TIMESTAMP(holiday) as fholiday from holidays order by holiday desc";
    $sql = "select name,holiday from holidays order by holiday desc";
       		}
       		
   else
       		{
	$sql = "select name,holiday from holidays order by holiday desc";	
       		}
		
		
		
		$DB->query($sql,"holidays.php");
			
		$num= $DB->countrows();
		
		while ($row = $DB->get()) 
		{
		$holiday = $row["holiday"];
                $_array = date_parse($holiday);
                
		$holiday = str_replace("-","",$holiday);
                
                //$d = date("d",$row["fholiday"]);
                $d = $_array['day'];
		//$m = date("m",$row["fholiday"]);
                $m = $_array['month'];
		//$y = date("Y",$row["fholiday"]);
                $y = $_array['year'];
                
                
                
		$day = strftime("%A",mktime(1,1,1,$m,$d,$y));
		$date = strftime("%x",mktime(1,1,1,$m,$d,$y));
		$name = formattext($row["name"]);
		$linkname = urlencode($name);
		
		        
		echo "<tr><td>$date</td><td>$day</td><td>$name</td><td> <a href=\"holidays.php?event=delete&amp;d=$d&amp;m=$m&amp;y=$y&amp;delete=yes&amp;linkname=$linkname\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"></a></td></tr>";
		}
		
		echo "</table></fieldset></form></div>";

				}

echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

