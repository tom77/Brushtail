<?php


echo "<div id=\"leftsidebar\"><div>";






if ($PREFERENCES["navlocation"] == "l"  && (!isset($modhidemenu) || (isset($modhidemenu) && $modhidemenu == 0)))
							{
								

$nav = "sidenav";
include("../includes/nav.php");

							}
							
							
							
	if (isset($left_widgets_type))
	{
		foreach ($left_widgets_type as $index => $wtype)
		{
			
		if ($wtype == "html")
			{
			echo $left_widgets_target[$index];
			}

		if ($wtype == "php")
			{
			//remove any directory traversal
			$left_widgets_target[$index] = 	str_replace  ("..","",$left_widgets_target[$index]);
			$cpath = "../main/custom/" . $left_widgets_target[$index];
			include($cpath);
			}


		if ($wtype == "module")
			{
			$t = time();
			$_widgets[] = new Widget($left_widgets_target[$index],$DB,$access,$t,'0');	
	
			}	
			
		}
		
		
	}


//echo "<br>$counter type is $type target is $target<br>";




					
							
							
							
							
							
							
							
							
							
							
							
echo "&nbsp;</div></div>";



?>