<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);




//if (isset($_REQUEST["front"]))
//{
	//$front = $_REQUEST["front"];
//}



$integers[] = "content_id";
$integers[] = "image_id";
$integers[] = "page_id";
$integers[] = "scroll";
$integers[] = "f";
$integers[] = "s";



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}








	
	 if (isset($ERROR))
	{
   
	header("Location: $url" . "error.php?error=input");
	exit();
	}
        
        elseif (!isset($m))
		{
		
		
		header("Location: $url" . "error.php?error=module");
		exit();
		}



	
	$access->check($m);
	
	if ($access->thispage < 1)
		{
		
		
		header("Location: $url" . "error.php?error=permissions");
		exit();
		}
	elseif ($access->iprestricted == "yes")
		{
		header("Location: $url" . "error.php?error=ipaccess");
		exit();
		}
	
		
		
		
	
if (!isset($ERROR)) 
	{
		
	
		
		
		//echo "menu is $menu";
		
		if (!(isset($_REQUEST["view"]) && $_REQUEST["view"] == "pagelist"))
		{
	
		$sql = "select  page_id, page_title from page  where page.frontpage= 1 and published = '1' and page.m = '$m'";
	//$sql = "select  page_id, page_title from page  where page.frontpage= 1 and page.m = '$m'";
	$DB->query($sql,"page.php");
	//$num_rows = $DB->countrows();
	while ($row = $DB->get())
		{
		$front_id = $row["page_id"];
		$page_title = formattext($row["page_title"]);
		}
		
		}


	if (isset($page_id))
		{
		$sql = "select  page_title from page  where page.page_id = '$page_id' and page.m = '$m'";
		//$sql = "select  page_id, page_title from page  where page.frontpage= 1 and page.m = '$m'";
		$DB->query($sql,"page.php");
		//$num_rows = $DB->countrows();
		while ($row = $DB->get())
			{
		
			$page_title = formattext($row["page_title"]);
			}
		}
	
	elseif  (isset($front_id))
		{
		$page_id = $front_id;
		}
	//elseif( $menu == "horizontal")
		//{
		
		//$sql = "select  page_title from page  where page.page_id = '$page_id' and published = '1' and page.m = '$m' limit 1";
		//$sql = "select  page_id, page_title from page  where page.frontpage= 1 and page.m = '$m'";
		////$DB->query($sql,"page.php");
	//	$num_rows = $DB->countrows();
	//	while ($row = $DB->get())
			//{
			//$page_id = $row["page_id"];
			//$page_title = formattext($row["page_title"]);
			//}
		//}
		
		
		

	

		$sql = "select * from modules where m = '$m'";
		$DB->query($sql,"page.php");
		$row = $DB->get();
		$modname = formattext($row["name"]);
		$ordering = $row["ordering"];
		$input = $row["input"];
		$menu = $row["menu"];

		if ($ordering == "t")
			{
			$insert = "order by page_title";
			}
		elseif ($ordering == "a")
			{
	$insert = " order by page_id asc";
			}
		elseif ($ordering == "d")
			{
	$insert = "order by page_id desc";
			} 
		elseif ($ordering == "c")
			{
	$insert = "order by pageorder";
			}
		else { $insert = "";}
		
		$counter = 0;
		$sql = "SELECT * FROM page where m = '$m' and deleted = '0' and published = 1 $insert";
				$DB->query($sql,"page.php");
		while ($row = $DB->get())
					{
					$array_id[] = $row["page_id"];
					$array_title[] = formattext($row["page_title"]);
					
				
					
					$counter++;
					}
		
			
					
	if (!isset($page_id) || $menu == "horizontal")
	{
		
			
			if ($counter > 1)
			{	
		if ($menu == "horizontal")
		{
		$pagemenu =  "<ul class=\"horizontal_list\">";	
		if (!isset($page_id)) { $page_id = $array_id[0];}
		}
		else 
		{
			$pagemenu = "<ul id=\"verticalnav\">";
		}
		
	
		
				//display contents menu	
				foreach ($array_id as $index => $value)
							{
					
					$_page_title = $array_title[$index];
						
					if ($_page_title == "")
						{
						$_page_title = "$phrase[58]";
						} 
						
						
					$_page_title = formattext($_page_title);
					//$page_title = str_replace(' ', '&nbsp;', $page_title);
						
					$pagemenu .= "<li><a href=\"page.php?m=$m&amp;page_id=$value\"";
					if (isset($page_id) && $page_id == $value) {$pagemenu .= " class=\"current\"";}
					$pagemenu .=  ">";
					if ($_page_title != "") {$pagemenu .= $_page_title;} else {$pagemenu .= $phrase[953];}
					$pagemenu .=  "</a></li>";
						
					}
						
				$pagemenu .=  "</ul>";	
			}
			elseif ($counter == 1) 
				{ 
				$page_id = $array_id[0];
				}
				
						
				}
	
			
		//echo "Page_title is $page_title";
			if (isset($page_id)) {$_page_id = $page_id;} else {$_page_id = "";}
			page_view($DB,$PREFERENCES,$m,$_page_id);
		
		

		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");		
		
		
		
		
	
		
		
		
		
		
		
		
		include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div>";
			
	
		
				
		echo "<h1>$modname</h1>";		
		
		if (isset($pagemenu)) {echo $pagemenu;}
				
		if (isset($page_id))
				{
			
					
					
					$sql = "SELECT * FROM page where page_id = '$page_id' and deleted = '0' and published = 1 $insert";
	$DB->query($sql,"page.php");
	$row = $DB->get();
	
	$ordering = $row["ordering"];
	$page_title = $row["page_title"];
	$frontpage = $row["frontpage"];	
	$displaydate = $row["displaydate"];	
	$feed = $row["feed"];
	
			//if ($type == "n") {echo "<h1>$modname</h1>";}
			
		//	if (($frontpage <> 1	) && (isset($counter) && $counter > 1))
	//{ 
	//echo "<span id=\"backtocontents\"><a href=\"page.php?m=$m\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"></a></span>&nbsp;&nbsp;&nbsp;";	
		
	//}
		
	
	
			if ($menu != "horizontal" && $page_title != "")	echo "<h2 >$page_title</h2>";
				
			if ($feed == 1)
			{	echo "<div style=\"float:right\"><a href=\"feed.php?m=$m&amp;page_id=$page_id\"><img src=\"../images/rss.png\" alt=\"$phrase[925]\" title=\"$phrase[925]\"></a></div>";
			}	
			
					include 'pagecontent.php';
				
				}
		


	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		//end contentbox
		echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

		
	

	
	
echo "


  	<script type=\"text/javascript\">
addEvent(window, 'load', loadfeeds);
";

if (isset($scroll))
{
	

$scroll = "c_" . $scroll;	

echo "addEvent(window, 'load', function () {scroll(\"$scroll\")});";
	
}

echo "</script>";


//end container div

	
include ("../includes/footer.php");

//print_r($_SESSION);
}
?>


  