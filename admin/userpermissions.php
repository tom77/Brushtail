<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");


include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div>";
echo "<h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>
		<h2>$phrase[51] </h2>";

	
	
	
if(isset($_POST["update"]))
	
	{
	
	$id = $DB->escape($_POST["id"]);
	$sql = "delete from permissions where id = \"$id\" and type=\"i\"";
	
	$DB->query($sql,"permissions.php");
	
	$limit = $_POST["limitip"];
	//print_r($limit);
	//update permissions for intranet modules
	if (isset($_POST["perm"]))
						{
	foreach ($_POST["perm"] as $key => $value) 
				{
				
				//if (array_key_exists($key,$limit))
				//{
					//echo "test $limit[$key]";
					//$ip = $limit[$key];
				//}
				//else {
				//	$ip = "";
				//}
				$sql = "insert into permissions values(NULL,'$id','$key','$limit[$key]','i','$value')";
				
				$DB->query($sql,"permissions.php");
				//echo "$sql";
				//$DB->query($sql,"permissions.php");
				}
				}
	}
	


	elseif (isset($WARNING))
	{
		warning($WARNING);
	}
	
	
	if(isset($_GET["id"]))
	
	{
		
		unset($mod);
	
		echo "<a href=\"userpermissions.php\">$phrase[11]</a><br><br>";
		
		
		
		$options[0] = "None";
		$options[1] = "Read";
		$options[2] = "Power User";
		$options[3] = "Edit";
		$id = $DB->escape($_GET["id"]);			
				
		$sql = "select * from permissions where id = '$id' and type=\"i\"";
				$DB->query($sql,"permissions.php");
				
				while ($row = $DB->get())
					{
					$mod[] = $row["m"];
					$permlevel[] = $row["permlevel"];
					$ip[] = $row["ip"];
					}
				
				
					$sql = "select * from modules where type <> 'z' order by position, name";
					$DB->query($sql,"permissions.php");
					
				echo "
			
				<FORM method=\"POST\" action=\"userpermissions.php\"> 
				<fieldset>
				<legend>$_GET[username]</legend><br><table style=\"text-align:left;\">
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
					echo "\"></td></tr>";
					}
				
			
				echo "<tr><td><input type=\"hidden\" name=\"id\" value=\"$_GET[id]\"><input type=\"submit\" value=\"$phrase[53]\" name=\"update\"></td><td></td><td></td></tr></table></fieldset>
				</form>";
				
		
		
	}	
	else
		{
			
		echo "<ul class=\"listing\" >
	

	
	";
	$sql = "select * from user order by username";
	$DB->query($sql,"permissions.php");
				
		while ($row = $DB->get())
			{
			$linkname = urlencode($row["username"]);
			echo "<li><a href=\"userpermissions.php?id=$row[userid]&amp;username=$linkname\">$row[username]</a> </li>";	
			}
	echo "</ul><br>";
	
	
				
				
		}

		

		echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

