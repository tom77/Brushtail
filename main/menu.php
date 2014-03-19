<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");



$integers[] = "m";



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}



	
		$sql = "select  name from modules where m = '$m'";

	$DB->query($sql,"page.php");
	$num_rows = $DB->countrows();
	$row = $DB->get();

	$modname = htmlspecialchars($row["name"]);


	




/*
	
if ((isinteger($m)))
{
	
	$access->check($m);
	
	if ($access->thispage < 1)
		{
		
		$ERROR  =  "<div style=\"text-align:center;font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
		}
	elseif ($access->iprestricted == "yes")
		{
	$ERROR  =  $phrase[0];
		}
	
	
}	

*/

	
if (isset($WARNING))
	{
	echo "$WARNING";
	}
	

elseif (isset($ERROR))
	{
	echo "$ERROR";
	}
else 
	{

	
		
		
		
		include('../includes/leftsidebar.php');
		
	
		

	  	echo "<div id=\"content\"><div>";
	  	echo "<h1>$modname</h1>";	
	
	echo "<ul style=\"list-style:none;line-height:1.6em;margin-left:0\">";
		
	   foreach ($navnames as $mod => $name)
	{
		
		if ($navparents[$mod] == $m)
		{
		echo "<li class=\"mainmenu\"><a ";
		
		if ($navtypes[$mod] == "y")
			{
		echo "href=\"$navlinks[$mod]\"";
				}
				
		elseif ($navtypes[$mod] == "x")
			{
		echo "href=\"custom.php?m=$mod\"";
				}
		else 
			{
			echo "href=\"../main/$navlinks[$mod]?m=$mod\"";
			}
			
		echo ">$name</a></li>";	
			
		}
		
		
	}
	     echo "</ul>";
	
	echo "</div></div>";
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	}

	

	include('../includes/rightsidebar.php');


	
include ("../includes/footer.php");

?>


  