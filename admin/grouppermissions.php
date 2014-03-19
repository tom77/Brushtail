<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");


include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div>";

echo "<h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>
		<h2>$phrase[56]</h2>";

unset($mod);
	
	
if(isset($_POST["update"]))
	
	{
	
	$group_id = $DB->escape($_POST["group_id"]);
	$sql = "delete from permissions where id = \"$group_id\" and type=\"g\"";
	
	$DB->query($sql,"permissions.php");
	
	$limit = $_POST["limitip"];
	
	//update permissions for intranet modules
	if (isset($_POST["perm"]))
						{
	foreach ($_POST["perm"] as $key => $value) 
				{
				
				if (array_key_exists($key,$limit))
				{
					$ip = $limit["$key"];
				}
				else {
					$ip = "";
				}
				$sql = "insert into permissions values(NULL,'$group_id','$key','$limit[$key]','g','$value')";
				$DB->query($sql,"permissions.php");
				
				}
				}
	}
	


	elseif (isset($WARNING))
	{
		warning($WARNING);
	}
	
	
	if(isset($_GET["group_id"]))
	
	{
	
		echo "<a href=\"grouppermissions.php\">$phrase[43]</a><br><br>";
		
		
		
		$options[0] = "None";
		$options[1] = "Read";
		$options[2] = "Power User";
		$options[3] = "Edit";
		$group_id = $DB->escape($_GET["group_id"]);			
				
		$sql = "select * from permissions where id = '$group_id' and type=\"g\"";
	
				$DB->query($sql,"permissions.php");
				
				while ($row = $DB->get())
					{
						//echo "mm is $row[m]<br>";
					$mod[] = $row["m"];
					$permlevel[] = $row["permlevel"];
					$ip[] = $row["ip"];
					}
			
					$sql = "select * from modules where type <> 'z' order by position, name ";
					$DB->query($sql,"permissions.php");
					
				echo "
				<div >
				<FORM method=\"POST\" action=\"grouppermissions.php\" >
				<fieldset>
				<legend>$_GET[group_name]</legend><table style=\"text-align:left\">
				<tr><td><b>$phrase[52]</b></td><td><b>$phrase[54]</b></td><td><b>$phrase[55]</b></td></tr>";
				
				while ($row = $DB->get())
					{
					//$m[] = $row["m"];	
					
					echo "<tr><td> $row[name] </td><td> <select name=\"perm[$row[m]]\">";
					
					foreach ($options as $key=>$value)
						{
   						echo "<option value=\"$key\"";
   						if (isset($mod))
   							{
   							foreach ($mod as $key2=>$value2)
								{
								if ($value2 == $row["m"])	
									{
									if (array_key_exists($key2,$permlevel))
										{ 
										if ($permlevel[$key2] == $key)
											{
											echo " selected";
											}
										}
									else 
										{
									if ($key == 0)
											{	
											echo " selected";
											}
							
							
										}
									}	
							}
   							}
   						echo">$value</option>";	
						}
					echo "</select></td><td> ip <input type=\"text\" name=\"limitip[$row[m]]\" value=\"";
					if (isset($mod))
   							{
					foreach ($mod as $key2=>$value2)
								{
								
								if ($value2 == $row["m"])
											{
											echo $ip[$key2] ;
											}
								
								}
   							}
					
					echo "\"></td></tr>
					";
					}
				
			
				echo "<tr><td><input type=\"hidden\" name=\"group_id\" value=\"$_GET[group_id]\">
				<br><input type=\"submit\" value=\"$phrase[53]\" name=\"update\"></td><td></td><td></td></tr></table></fieldset>
				</form></div><br>";
				
		
		
	}	
	else
		{
			
		echo "
	
	<ul class=\"listing\">

	";
	$sql = "select * from groups order by group_name";
	$DB->query($sql,"permissions.php");
				
		while ($row = $DB->get())
			{
			$linkname = urlencode($row["group_name"]);
			echo "<li><a href=\"grouppermissions.php?group_id=$row[group_id]&amp;group_name=$linkname\">$row[group_name]</a> </li>";	
			}
	echo "</ul>";
	
	
				
				
		}

		

echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

