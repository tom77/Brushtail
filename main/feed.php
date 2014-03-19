<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);







//print_r($_REQUEST);


$integers[] = "page_id";



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
		
		
		
			$sql = "select  * from modules where m = '$m'";
	
	$DB->query($sql,"feed.php");
	$row = $DB->get();
	$menupath = $row["menupath"];
	$type = $row["type"];	
	$input = $row["input"];
	
	
		$curdir = str_replace("\\", "/",dirname($_SERVER["PHP_SELF"]));
	
	if ($curdir != "/") { $curdir = $curdir . "/";}
	
	$servername  = $_SERVER["SERVER_NAME"];
	$port = $_SERVER["SERVER_PORT"];
	if (isset($_SERVER["HTTPS"])) { $https = $_SERVER["HTTPS"];}
	

	
	
	if ($port != 80) {$port = ":$port";} else {$port = "";}
	
	if (isset($https) && $https == "on") {$url = "https";} else {$url = "http";}
	
	$url .= "://" .$servername .$port. $curdir ;
	

	$pageurl = $url .  $menupath . "?m=" . $m . "&amp;page_id=" . $page_id;
	$feedurl = $url .  "feed.php?m=" . $m . "&amp;page_id=" . $page_id;
	
	
	
	
	
	//get array of images
		$sql = "select image_id,type, size,content.content_id as content_id, name from images, content, page where content.page_id = page.page_id and content.content_id = images.content_id and content.page_id = \"$page_id\" and images.deleted = '0'";
		
		
	$DB->query($sql,"editcontent.php"); 
	
	$i = 0;	
			while (	$row = $DB->get()) 
						{
						
						$iarray_content[$i] = $row["content_id"];
						$iarray_name[$i] = $row["name"];
						$iarray_type[$i] = $row["type"];
						$iarray_size[$i] = $row["size"];
						$iarray_imageid[$i] = $row["image_id"];
						$i++;
						}
	
	
	
	
	
	
	
	
	
	
	//<atom:link href=\"$feedurl\" rel=\"self\" type=\"application/rss+xml\" />
	
	
	
	
		
		$sql = "select  page_title, feed, ordering from page where page_id = '$page_id'";
	//echo $sql;
	$DB->query($sql,"feed.php");
	$row = $DB->get();
	$page_title = htmlspecialchars(utf8_encode($row["page_title"]));	
	$feed = $row["feed"];
	$ordering = $row["ordering"];

	if ($feed == 0) {exit();}
		
		echo "<?xml version=\"1.0\"?>
<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">
<channel>
<title>$page_title</title>
<description>$page_title</description>
<link>$pageurl</link>
";

		
		
		if ($ordering == "t")
	{
	$insert = "order by title";
	}
elseif ($ordering == "a")
	{
	$insert = " order by content_id asc";
	}
elseif ($ordering == "d")
	{
	$insert = "order by content_id desc";
	} 
elseif ($ordering == "c")
	{
	$insert = "order by page_order asc, content_id asc";
	}
else { $insert = "";}
		
		
	$now = time();	
		
		
		$sql = "select * from content where ( expiry = 0 or expiry > '$now') and  page_id = '$page_id' and event = '0' and deleted='0' $insert"; 
		
		
	$DB->query($sql,"feed.php");
	//$num_rows = $DB->countrows();
	while ($row = $DB->get())
	{
	$content_id = $row["content_id"];
	$title = formattext(utf8_encode($row["title"]));
	$text = utf8_encode($row["body"]);
	if($input != 0)
			{
			$body = formattext_html($text);				
			}
	else 
			{
			$body = formattext($text);	
			}
	
	$updated_when = date("r",$row["updated_when"]);	

	$article_url = $pageurl . "&scroll=" . $content_id; 
	$article_url = htmlspecialchars_decode($pageurl) . "&scroll=" . $content_id;
	
	echo "
<item>
<title><![CDATA[$title]]></title>
<link><![CDATA[$article_url]]></link>
<guid><![CDATA[$article_url]]></guid>
<description><![CDATA[";

if (isset($iarray_imageid))
{
foreach ($iarray_imageid as $i => $image_id)
		{
										
									
		if ($iarray_content[$i] == $content_id)
			{
			echo "<img src=\"$url/image.php?m=$m&amp;image_id=$image_id\"><br><br>
";
			}
												
		}
}
echo "$body]]></description>
<pubDate>$updated_when</pubDate>
";
//if (isset($iarray_imageid))
//{
//foreach ($iarray_imageid as $i => $image_id)
		//{
										
									
		//if ($iarray_content[$i] == $content_id)
		//	{
		//	echo "<enclosure url=\"http://192.168.10.233/brushtail_5/main/image.php?m=$m&amp;image_id=$image_id\" length=\"$iarray_size[$i]\" type=\"$iarray_type[$i]\" />	
//";
			//}
												
		//}
//}
echo "</item>
 

	";
	}
	
	echo "
</channel>
</rss>";	
	}


//
	
//end container div
	


?>


  