<?php


echo "<div id=\"rightsidebar\"><div>";






if ($PREFERENCES["navlocation"] == "r"  && (!isset($modhidemenu) || (isset($modhidemenu) && $modhidemenu == 0)))
							{
								

$nav = "sidenav";
include("../includes/nav.php");

							}
							
							
							
	if (isset($right_widgets_type))
	{
		
		
		
		foreach ($right_widgets_type as $index => $wtype)
		{
			
		//print_r($right_widgets_target);	
			
		if ($wtype == "html")
			{
			echo $right_widgets_target[$index];
			}

		if ($wtype == "php")
			{
			//remove any directory traversal
			$right_widgets_target[$index] = 	str_replace  ("..","",$right_widgets_target[$index]);
			$cpath = "../main/custom/" . $right_widgets_target[$index];
			include($cpath);
			}


		if ($wtype == "module")
			{
			$t = time();
			$_widgets[] = new Widget($right_widgets_target[$index],$DB,$access,$t,'0');	
	
			}	
			
		}
		
		
	}


//echo "<br>$counter type is $type target is $target<br>";




					
							
							
							
							
							
							
							
							
							
							
							
echo "&nbsp;</div></div>";



?>