<?php

$access->mmenu();

$navlinks = $access->mainlink;
$navnames = $access->mainmenu;
$navtypes = $access->maintype;
$navparents = $access->mainparent;
$modordering = $access->ordering;



$counter = 0;
$counter2 = 0;
$navcount = count($navnames);

$mainzindex = 500;
$subzindex = 200;
//print_r($navparents);

if ($navcount > 0)
{
	echo "
	<ul id=\"$nav\">";

foreach ($navnames as $mod => $name)
	{
		if (($navtypes[$mod] != "z" && $navparents[$mod] == 0) || ($navtypes[$mod] == "z" && in_array($mod,$navparents)))
		{
	echo "<li class=\"mainmenu";
	

	
	if ($navtypes[$mod] == "z" && in_array($mod,$navparents) && $PREFERENCES["flyoutmenu"] == "yes") {echo " parent";}
	
	
	
	
	echo "\" style=\"z-index:$mainzindex\">";
	if ($navtypes[$mod] == "z" && in_array($mod,$navparents))
	{
	echo "<a  href=\"../main/menu.php?m=$mod\">$name";
	if ($PREFERENCES["flyoutmenu"] == "yes") { echo " &gt;&gt;";}
	echo "</a>";
	
	if ($PREFERENCES["flyoutmenu"] == "yes") {  echo "<ul>";

		foreach ($navparents as $navm => $par)
		{
		if ($par == $mod)
			{
			echo "<li><a ";
			
			if ($navtypes[$navm] == "y")
		{
		echo "href=\"$navlinks[$navm]\"";
		}
			elseif ($navtypes[$navm] == "x")
		{
		echo "href=\"custom.php?m=$navm\"";
		}
		
	else 
		{
		echo "href=\"../main/$navlinks[$navm]?m=$navm\"";
		}
			
			
			
			echo "class=\"menulink";
			if ($counter2 == 0) {echo " menutop";}
			echo "\"> $navnames[$navm]</a></li>
			";	
			$counter2++;		
			}
		}	
	
	echo "</ul>";
	}
	}
	else {
		
	echo "<a class=\"menulink";
	
	if ($counter ==0) {
			echo  " menutop";
	} 
	echo "\" ";
	
	if ($navtypes[$mod] == "y")
		{
		echo "href=\"$navlinks[$mod]\"";
		}
	elseif ($navtypes[$mod] == "x")
		{
		echo "href=\"../main/custom.php?m=$mod\"";
		}
	else 
		{
		echo "href=\"../main/$navlinks[$mod]?m=$mod\"";
		}
	
	echo ">$name</a>";
		
	}
	
	echo "</li>
	";
	}
	$counter++;
	$mainzindex--;
	}
							



		echo "</ul>
	";
		
	}	
	
echo "<div style=\"clear:both;margin-bottom:1em\"></div>";

		
//print_r($access->mainlink);
?>