<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);





		
		
	
	if (isset($ERROR))
	{
	header("Location: $url" . "error.php?error=input");
	exit();
	}
else 
	{
		
	

	
			
		$page_title  = $phrase[990];
		
		

		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");		
		
		
		
		
	
		
		
		
		
		
		
		
		include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div>";
			
	
		
				
		echo "<h1>$page_title</h1>";		
				
		


if ((!isset($modhidemenu) || (isset($modhidemenu) && $modhidemenu == 0)))
{

echo "<ul id=\"sitemap_modules\">";

foreach ($navnames as $mod => $name)
	{
	if ($navtypes[$mod] != "z")
	{echo "<li><a href=\"page.php?m=$mod\">$name</a>";
	
	//echo "type is " . $navtypes[$mod];
	
		if ($navtypes[$mod] == "p" ||  $navtypes[$mod] == "w" )
		{
			echo "<ul>";
			
			
			
		if ($modordering[$mod] == "t")
			{
	$insert = "order by page_title";
			}
		elseif ($modordering[$mod] == "a")
			{
	$insert = " order by page_id asc";
			}
		elseif ($modordering[$mod] == "d")
			{
	$insert = "order by page_id desc";
			} 
		elseif ($modordering[$mod] == "c")
			{
	$insert = "order by pageorder";
			}
		else { $insert = "";}
			
			
		$sql = "select page_id, page_title from page where m = '$mod' and deleted != '1' and published = '1' $insert";	
		//echo $sql;
		$DB->query($sql,"sitemap.php");
		$num_rows = $DB->countrows();
		while ($row = $DB->get())
			{
		
			$page_title = formattext($row["page_title"]);
			$page_id = formattext($row["page_id"]);
			echo "<li><a href=\"";
			if ($navtypes[$mod] == "p") { echo "page.php?m=$mod&page_id=$page_id";}
			
			if ($navtypes[$mod] == "w") { echo "wiki.php?m=$mod&page_id=$page_id";}
			
			
			
			echo "\">$page_title</a></li>
";
			
			}
			echo "</ul>";
		}
		
		if ( $navtypes[$mod] == "b")
		{
			$time = time();
			echo "<ul>";
		$sql = "select resource_no,resource_name from resource where m = '$mod'";	
		//echo $sql;
		$DB->query($sql,"sitemap.php");
		$num_rows = $DB->countrows();
		while ($row = $DB->get())
			{
		
			$resource_name = formattext($row["resource_name"]);
			$resource_no = $row["resource_no"];
			echo "<li><a href=\"resourcebooking.php?m=$mod&event=cal&resource_no=$resource_no&t=$time\">$resource_name</a></li>
";
			
			}
			echo "</ul>";
		}
	echo "</li>
";}	
		
		
	}
	
	echo "</ul>";
		
} else {echo "Navigation menu hidden";}
	

	
		//end contentbox
		echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

		
	



//end container div

	
include ("../includes/footer.php");

//print_r($_SESSION);
}
?>


  