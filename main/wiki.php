<?php

function wikilink($m,$text,$pages_array,$access,$phrase,$view)
{
	//echo "text is $text";
	
//$array = explode(" ", $text);
$array = preg_split("/[\n\r\t <>]+/", $text);

$wikiwords = array();

	foreach ($array as $index => $value)
{
  if (preg_match("/^[A-Z][A-Z0-9]*[a-z]+[A-Z0-9]+[a-zA-Z0-9]*$/",$value))
  {
  	$wikiwords[] = $value;
  
  
  }

}



if(isset($wikiwords))
{
	foreach ($wikiwords as $index => $value)
{
	 $matches = 0;
  if (isset($pages_array))
  	{
  	foreach ($pages_array as $page_id => $pagename)
  		{
  		if ($pagename == $value)
  		{
  			$new = "<a href=\"wiki.php?m=$m&amp;page_id=$page_id\">" .  $value . "</a>";
  			$text = str_replace($value,$new, $text);
  			$matches++;	
  		}
  		
  		//replace wikiword with link to page
  		
  		}
  		
  		}
  	

  	if ($matches == 0 && $access->thispage > 2 && $view == "edit")
  {
  	//replace wikiword with link to newpage
  	$linkname = urlencode($value);
  	$new = "<a href=\"wiki.php?m=$m&update=newpage&amp;page_title=$linkname\" title=\"$phrase\" alt=\"$phrase\">" .$value ."</a>";
  		$text = str_replace($value,$new, $text);
  }

}
}





return $text;
}






include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$integers[] = "content_id";
$integers[] = "image_id";
$integers[] = "page_id";
$integers[] = "doc_id";
$integers[] = "page";
$integers[] = "restorepage";
$integers[] = "imagealign";


foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (isset($_REQUEST["edit"])) {$edit = $_REQUEST["edit"]; }



	
$ip = ip("pc");
$proxy = ip("proxy");





	
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
	$sql = "select name from modules where m = '$m'";
	$DB->query($sql,"wiki.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);
		
	page_view($DB,$PREFERENCES,$m,"");	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");




$now = time();




	



		
		include("../includes/leftsidebar.php");

				
		
			

	  	echo "<div id=\"content\"><div>";
		

		
		//check module details
		
			$sql = "select * from modules where m = \"$m\"";
		$DB->query($sql,"wiki.php");
		$row = $DB->get();
		$modname = formattext($row["name"]);
		$input = $row["input"];
		$menu = $row["menu"];
	
		
		
	
		
	
	
					
				
		echo "<h1>$modname</h1>
		
		
<a href=\"wiki.php?m=$m&amp;event=pagelist\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"  alt=\"$phrase[57]\"></a>";
		
		if ($access->thispage > 2)
		{
		echo "&nbsp;&nbsp;&nbsp;<a href=\"wiki.php?m=$m&amp;event=recent\"><img src=\"../images/clock.png\" title=\"$phrase[831]\"  alt=\"$phrase[831]\"></a><br>";	
		}
		$pages_array = array();
			$sql = "select page_id,page_title from page where m = \"$m\"";
		$DB->query($sql,"wiki.php");
		while ($row = $DB->get())
		{
			$pi = $row["page_id"];
			$pt = $row["page_title"];
		
		$pages_array[$pi] = $pt;	
		}
		
		
		
		
					
	if (!isset($page_id) && !isset($_REQUEST["event"]))
	{
		
		$sql = "SELECT page_id FROM page where m = \"$m\" and pageorder = 1 and deleted = '0' and published = 1";
				$DB->query($sql,"page.php");
	$row = $DB->get();
		
					$page_id = $row["page_id"];
				
						
		}
				
				
	
				
				
			

foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}	


if ($access->thispage > 1)	//power users and edit users
{
	
	if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addcomment")

{


$body = $DB->escape($_REQUEST["body"]);

$ip = ip("pc");	
addcomment($body,$page_id,$ip,$DB);

 
}

	
}


if ($access->thispage > 2)	//edit users
{
	

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletedoc")
{	

	deletedoc($ip,$doc_id,$DB);

	
}

if (($access->thispage == 3) && isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletecomment")

{




$ip = ip("pc");	
deletecomment($content_id,$page_id,$ip,$DB);

 
}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "restoredoc")
{	

	restoredocument($doc_id,$content_id,$ip,$page_id,$DB);
	
	
}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "newpage")
{	

	$page_title = $DB->escape($_REQUEST["page_title"]);
	
	 if (preg_match("/^[A-Z][A-Z0-9]*[a-z]+[A-Z0-9]+[a-zA-Z0-9]*$/",$page_title))
	{
		  $sql = "INSERT INTO page VALUES(NULL,'$page_title','$m','1','0','0','c','$page_title','0','0','0')"; 
		
   			$DB->query($sql,"editpage.php");
   			$page_id = $DB->last_insert();
   			
	}
	else 
	{
		$ERROR = "<br><span style=\"font-weight:bold;color:red;\">Unable to add new page. Not a valid wiki name.</span><br><br>";
	}
	
	
	
}

if (isset($_GET["update"]) && $_GET["update"] == "delete")
{	

	deleteparagraph($content_id,$page_id,$ip,$now,$DB);
	
	
	
}

if (isset($_GET["update"]) && $_GET["update"] == "wikihomepage")
{
$sql = "insert into page values (NULL,'Home','$m','1','0','1','c','Home','0','0','0')";

	$DB->query($sql,"menu.php");
	$page_id = $DB->last_insert();
	
}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "restoreimage")

{
//print_r($_REQUEST);
	
		
 	
		  
	//query filename
	$sql = "select name from images where image_id = \"$image_id\" "; 
	$DB->query($sql,"editcontent.php");	  
	$row = $DB->get();
	$imagename = $row["name"];

	
	$sql = "update images set deleted = '0' where image_id = \"$image_id\" "; 
	$DB->query($sql,"editcontent.php");
	
		$sql = "insert into content values (NULL,'$imagename','$image_id','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','7','0','1','0')";	
	$DB->query($sql,"editcontent.php");

	
}


if (isset($_GET["update"]) && $_GET["update"] == "deleteimage")

{

	deleteimage($now,$ip,$image_id,$DB,$PREFERENCES,$m);
		
 
	
	
}



if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updatedoc"  )
   {
   
	$description = $DB->escape($_REQUEST["description"]);
	$sql = "update documents set keywords = \"$description\" where doc_id = \"$doc_id\"";
	
	$DB->query($sql,"editcontent.php");	
	
		$sql = "insert into content values (NULL,'$doc_id','$description','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','6','0','1','0')";	
	$DB->query($sql,"editcontent.php");
	}

	
	



if (isset($_POST["update"]) && $_POST["update"] == "uploaddoc")

{		

		upload($m,$page_id,$content_id,$PREFERENCES,$DB,"document",$ip,$phrase);		

}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "restorepage")
	{
		
restorepage($restorepage,$ip,$DB);
		
		}
		
		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletepage")

{	

deletepage($page,$ip,$DB);

}

if (isset($_POST["update"]) && $_POST["update"] == "uploadimage")

{		
	
	upload($m,$page_id,$content_id,$PREFERENCES,$DB,"image",$ip,$phrase);
		
			

}








if (isset($_POST["update"]) && $_POST["update"] == "add paragraph")

{
$title = $DB->escape($_REQUEST["title"]);
$body = $DB->escape($_REQUEST["body"]);

	
	
addparagraph($title,$body,$page_id,$ip,$DB,$imagealign,'0');

 
}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "edit" )
	{

	$title = $DB->escape($_REQUEST["title"]);
	$body = $DB->escape($_REQUEST["body"]);	
$imagealign = $DB->escape($_REQUEST["imagealign"]);
	
	updateparagraph($content_id,$title,$body,$ip,$DB,$imagealign);
	
		
		
		
	}
	

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "restoreparagraph" )
	{
		
	
	
	//get old record 
	$sql = "select * from content where content_id = \"$content_id\"";
	
	$DB->query($sql,"editcontent.php");

	$row = $DB->get();
	$oldtitle = $DB->escape($row["title"]);	
	$oldbody = $DB->escape($row["body"]);	

	$oldpage_order = $row["page_order"];

	
	restoreparagraph($content_id,$oldtitle,$page_id,$oldpage_order,$ip,$DB);
	
	
	
		
	}
		
	

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "restorecomment" )
	{
		
	

	
	restorecomment($content_id,$page_id,$ip,$DB);
	
	
	
		
	}
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "reorder" )
	{
	
	//reorders paragraphs on the page
	$reorder = explode(",",$_REQUEST["reorder"]);
	
	foreach ($reorder as $index => $value)
		{
		
		$sql = "update content set page_order = \"$index\" WHERE content_id = \"$value\"";	
		$DB->query($sql,"editcontent.php"); 
		}
	
	
	/*$metadata = "$page_title"
		
		;
		$sql = "select * from content where page_id = \"$page_id\"";
	$DB->query($sql,"editpage.php");

	
		while ($row = $DB->get()) 
		{
		$metadata .= "$row[title]
		$row[body]
	
	";
		}
		$metadata = $DB->escape($metadata);
		$sql = "update page set metadata = \"$metadata\" where page_id = \"$pageid\"";
		$DB->query($sql,"editpage.php");*/
	}
				}


	if (isset($page_id))
				{
			
					
					
					$sql = "SELECT * FROM page where page_id = \"$page_id\" and deleted = '0' and published = 1";
	$DB->query($sql,"page.php");
	$row = $DB->get();
	

	$page_title = $row["page_title"];
	$linktitle = urlencode($page_title);
	
			//if ($type == "n") {echo "<h1>$modname</h1>";}
			
	

		
			
				echo "<h2>$page_title</h2>";
				
				}
				
if (isset($ERROR))
{
echo $ERROR;	
}
if (isset($WARNING))
{
warning($WARNING);	
}

elseif (($access->thispage > 2) && isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletepage")
{
	echo "<br><br><b>$phrase[14]</b><br><br> $_REQUEST[title]<br><br>
	<a href=\"wiki.php?m=$m&amp;update=deletepage&amp;page=$page&amp;edit=yes\">$phrase[12]</a> | <a href=\"wiki.php?m=$m&amp;page_id=$page&amp;edit=yes\">$phrase[13]</a>";
	
}


elseif (($access->thispage > 1) && isset($_REQUEST["event"]) && $_REQUEST["event"] == "addcomment")
	{
		
		echo "<br><b>$phrase[822]</b><form method=\"POST\" action=\"wiki.php\" >
	
	<textarea name=\"body\" rows=\"10\" cols=\"50\"></textarea> <br><br>
	<input type=\"submit\" name=\"submit\" value=\"$phrase[176]\">
		<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
		<input type=\"hidden\" name=\"m\" value=\"$m\">
		<input type=\"hidden\" name=\"update\" value=\"addcomment\">
		
	
		
		</form>";
	}
elseif (($access->thispage > 2) && isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletedoc")
{
	echo "<br><b>$phrase[14]</b><br><br> $_REQUEST[filename]<br><br>
	<a href=\"wiki.php?m=$m&amp;update=deletedoc&amp;page_id=$page_id&doc_id=$doc_id&amp;content_id=$content_id&amp;edit=yes\">$phrase[12]</a> | <a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;edit=yes\">$phrase[13]</a>";
	
}

elseif (($access->thispage > 2) && isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteimage")
{
	echo "<br><b>$phrase[14]</b><br><br> $_REQUEST[name]<br><br>
	<a href=\"wiki.php?m=$m&amp;update=deleteimage&amp;page_id=$page_id&image_id=$image_id&amp;content_id=$content_id&amp;edit=yes\">$phrase[12]</a> | <a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;edit=yes\">$phrase[13]</a>";
	
}

elseif (($access->thispage > 2) && isset($_GET["event"]) && $_GET["event"] == "paragraphhistory")
	{
	
	$restoredoc = "yes";	
	$restoreimage = "yes";
		
	echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;edit=yes\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a>
	<br><br>
	<h4>$phrase[779]</h4><br><table class=\"colourtable\" style=\"width:70%;padding:0.5em\">";
	
	$sql = "select * from images where content_id = \"$content_id\"";
	
	$DB->query($sql,"editcontent.php");
	while ($row = $DB->get())
	{
	$image_id = $row["image_id"];
	$images[$image_id] =   $row["deleted"];	
	$imagenames[$image_id] =   $row["name"];		
	}
	
	
	$sql = "select * from documents where content_id = \"$content_id\"";
	$DB->query($sql,"editcontent.php");
	//echo $sql;
	while ($row = $DB->get())
	{
	$doc_id = $row["doc_id"];
	$docs[$doc_id] =   $row["deleted"];	
	$docnames[$doc_id] =   $row["doc_name"];		
	}
	
	$sql = "select * from content where archive = \"$content_id\" and (event < '9') order by updated_when desc";
	
	$DB->query($sql,"editcontent.php");

	while ($row = $DB->get())
	{
	//$content_id = $row["content_id"];	
	$title = $row["title"];	
	$body = $row["body"];	
	$page_id = $row["page_id"];
	$page_order = $row["page_order"];
	$updated_by = $row["updated_by"];
	$updated_ip = $row["updated_ip"];
	$event = $row["event"];
	$imagealign = $row["imagealign"];
	$updated_when = date("g:ia",$row["updated_when"]) . strftime(" %x",$row["updated_when"]);
	echo "<tr><td style=\"padding:2em\">
	<span style=\"color:red;\"><b>$updated_when</b><br>
	<b>$updated_by</b> 
	$updated_ip</span><br><br>
	
	";
	if ($event == 8)
	{
		//Restored document
		echo "$phrase[790] <b>$title</b><br>
		";
	}
	if ($event == 7)
	{
		//Restored image
		echo "$phrase[789] <b>$title</b><br>
		";
	}
	if ($event == 6)
	{
		//Keywords updated
		echo "$phrase[783]";
		if (isset($docs) && array_key_exists($title,$docs))
		{
		 echo " <b>$docnames[$title]</b><br><br><i>$body</i><br><br>
			<form action=\"wiki.php\" method=\"post\">
	<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
	<input type=\"hidden\" name=\"doc_id\" value=\"$body\">
<input type=\"hidden\" name=\"description\" value=\"$body\">
	<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"updatedoc\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[780]\">
	</form>
		";
		
		}
		
	}
	elseif ($event == 5)
	{
		//Document deleted
		echo "$phrase[785] <b>$docnames[$body]</b><br>";
		if (isset($docs) && array_key_exists($body,$docs))
		{
		if 	($docs[$body] == 1 && $restoredoc == "yes")
		{ echo "<br>
			<form action=\"wiki.php\"method=\"post\">
	<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
	<input type=\"hidden\" name=\"doc_id\" value=\"$body\">

	<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"restoredoc\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[780]\">
	</form>
		";
		$restoredoc = "no";
		}
		}
		
	}
	if ($event == 4)
	{
		//document uploaded
		echo "$phrase[784] <b>$title</b><br>";
	
	}
	if ($event == 3)
	{
		//Image deleted
		echo "$phrase[787] <b>$imagenames[$body]</b><br>";
		if (isset($images) && array_key_exists($body,$images))
		{
		if 	($images[$body] == 1 && $restoreimage == "yes")
		{ echo "<br>
		<form action=\"wiki.php\"method=\"post\">
	<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
	<input type=\"hidden\" name=\"image_id\" value=\"$body\">

	<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"restoreimage\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[780]\">
	</form>
		";
		$restoreimage = "no";
		}
		}
	}
	if ($event == 2)
	{
		//image added
		echo "$phrase[786] <b>$title</b><br>";
	}
	elseif ($event == 1)
	{
		
		$body = str_replace("\"", "'",$body);	
		
	echo "$phrase[794]<br><br><i><b>$title</b><br>
	$body</i><br><br>

	<form action=\"wiki.php\"method=\"post\">
	<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
	<input type=\"hidden\" name=\"title\" value=\"$title\">
	<input type=\"hidden\" name=\"body\" value=\"$body\">
	<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
	<input type=\"hidden\" name=\"page_order\" value=\"$page_order\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"edit\">
	<input type=\"hidden\" name=\"imagealign\" value=\"$imagealign\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[780]\">
	</form>";
	}
	echo "</td></tr>";
	}
	
	echo "</table>";
	
}



elseif (($access->thispage > 2) &&  isset($_GET["event"]) && $_GET["event"] == "pagehistory")
	{
	
	//$displayrestorebutton = "yes";
	
	echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;edit=yes\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a><br><br><h4>$phrase[781]</h4><br><table class=\"colourtable\" style=\"width:70%;padding:0.5em\">";
	

	
	$sql = "select * from content where page_id  = \"$page_id\" ";

	$DB->query($sql,"editcontent.php");

	while ($row = $DB->get())
	{
	$content_id = $row["content_id"];	
	$titles[$content_id] = $row["title"];
	$bodies[$content_id] = $row["body"];
	$deleted[$content_id] = $row["deleted"];
	
	}

	
	
	$sql = "select * from content where page_id  = \"$page_id\"  and 
	(event = '9' or event = '10' or event = '11' or event = '16' or event = '17' or event = '18') 
	order by updated_when desc";

	$DB->query($sql,"editcontent.php");

	while ($row = $DB->get())
	{
	//$content_id = $row["content_id"];	
	$title = $row["title"];
	$body = $row["body"];
	
	
	$content_id = $row["content_id"];
	$archive = $row["archive"];
	$page_order = $row["page_order"];
	$updated_by = $row["updated_by"];
	$updated_ip = $row["updated_ip"];
	$event = $row["event"];
	
	$updated_when = date("g:ia",$row["updated_when"]) . strftime(" %x",$row["updated_when"]);
	echo "<tr><td style=\"padding:2em\">
	<span style=\"color:red;\"><b>$updated_when</b><br>
	<b>$updated_by</b> 
	$updated_ip</span><br><br>
	
	";
	
	if ($event == 11)
	{
	//paragraph restored	
	echo "$phrase[795] <br><br><i><b>$titles[$archive]</b><br>$bodies[$archive]</i><br>
		";	
	}
	
		if ($event == 18)
	{
	//comment restored	
	echo "$phrase[825] <br><br><i>$bodies[$archive]</i><br>
		";	
	}
	
	if ($event == 10)
	{
		//Paragraph added
		echo "$phrase[793] <br><i><b>$titles[$archive]</b><br>$bodies[$archive]</i><br>
		";
	}

	if ($event == 16)
	{
		//comment added
		echo "$phrase[823] <br><i>$bodies[$archive]</i><br>
		";
	}
	
	if ($event == 9)
	{
		//paragraph deleted
		echo "$phrase[792]<br><br><i><b>$titles[$archive]</b><br>$bodies[$archive]</i><br>";
		if (isset($deleted) && array_key_exists($archive,$deleted))
		{
		//if ($deleted[$archive] == 1 && $displayrestorebutton == "yes")
		 if ($deleted[$archive] == 1)
		 {
			echo "<br><form action=\"wiki.php\"method=\"post\">
	<input type=\"hidden\" name=\"content_id\" value=\"$archive\">
	<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"restoreparagraph\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[780]\">
	</form>
		";
		// $displayrestorebutton = "no";
		 }
		}
		}
		
		
	
	if ($event == 17)
	{
		//comment deleted
		echo "$phrase[824]<br><br><i><b>$bodies[$archive]</i><br>";
		if (isset($deleted) && array_key_exists($archive,$deleted))
		{
		//if ($deleted[$archive] == 1 && $displayrestorebutton == "yes")
		 if ($deleted[$archive] == 1)
		 {
			echo "<br><form action=\"wiki.php\"method=\"post\">
	<input type=\"hidden\" name=\"content_id\" value=\"$archive\">
	<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"restorecomment\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[780]\">
	</form>
		";
		// $displayrestorebutton = "no";
		 }
		}
		}
		
		
	}
	
	echo "</table>";
	
}

elseif (($access->thispage > 2) &&  isset($_GET["event"]) && $_GET["event"] == "image")
	{
	echo "<a href=\"wiki.php?m=$m&amp;edit=yes&amp;page_id=$page_id\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a>
	
	
	
	<form action=\"wiki.php\" method=\"post\" enctype=\"multipart/form-data\" style=\"width:90%\">
	<fieldset><legend>$phrase[98]</legend><br>
						
						
						
						

						
						<p><input type=\"file\" name=\"upload[0]\" > <button onclick=\"addchooser();return false;\">$phrase[996]</button></p>
						<div id=\"fs\">
						
						</div>
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<input type=\"hidden\" name=\"edit\" value=\"yes\">
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"update\" value=\"uploadimage\">
						
						<input type=\"submit\" name=\"submit\" value=\"$phrase[98]\" >
						</fieldset>
						</form>	
						
						<script type=\"text/javascript\">
						var counter = 1;
						
						function addchooser()
						{
						var fs = document.getElementById(\"fs\")
					
						fs.innerHTML = fs.innerHTML + '<p id=\"p_' + counter + '\"><input type=\"file\" name=\"upload[' + counter + ']\" id=\"f_' + counter + '\"> <img src=\"../images/remove.png\" onclick=\"hide(\'' + counter + '\')\"></p>';
						counter++;
						}
						
						
						 function hide(id)
							 {

	 					document.getElementById('p_' + id).style.display = \"none\";	
						document.getElementById('f_' + id).disabled=true;
	 					}
						</script>
						";
		
	}
	
elseif (isset($_GET["event"]) && $_GET["event"] == "recent")
	{
		echo "<br><br><b>$phrase[831]</b><br>";
	//$sql = "SELECT distinct(page.page_id),page_title FROM page, content where page.page_id = content.page_id and m = \"$m\" and page.deleted = '0' order by updated_when desc";
	
		$sql = "SELECT distinct(content.content_id) as content_id,page_title,content.page_id as page_id, updated_by,updated_when FROM page, content where page.page_id = content.page_id and m = \"$m\" and page.deleted = '0' order by updated_when desc limit 100";
	//echo $sql;
	
	
	$restore_page_button = array();
	
	$sql = "SELECT content.content_id as content_id,event,page_title,page.deleted as deleted,content.page_id as page_id, updated_by,updated_when FROM page, content where page.page_id = content.page_id and m = \"$m\" and (event != 0 and event != 15) order by updated_when desc limit 100";
	
	echo "<table cellspacing=\"10\">";
				$DB->query($sql,"wiki.php");
					while ($row = $DB->get()) 
						{
							
						$_pageid= $row["page_id"];
						$_page_title = formattext($row["page_title"]);
						$by = $row["updated_by"];
						$deleted[$_pageid] = $row["deleted"];
						$event = $row["event"];
						$when= date("g:ia",$row["updated_when"]) . strftime(" %x",$row["updated_when"]);
						
						
						
						echo "<tr><td><a href=\"wiki.php?m=$m&amp;page_id=$_pageid\">$_page_title</a></td><td>";
						
						
						if ($event == 1) {echo $phrase[794];}
						if ($event == 2) {echo $phrase[786];}
						if ($event == 3) {echo $phrase[787];}
						if ($event == 4) {echo $phrase[784];}
						if ($event == 5) {echo $phrase[785];}
						if ($event == 6) {echo $phrase[783];}
						if ($event == 7) {echo $phrase[789];}
						if ($event == 8) {echo $phrase[790];}
						if ($event == 9) {echo $phrase[792];}
						if ($event == 10) {echo $phrase[793];}
						if ($event == 11) {echo $phrase[795];}
						if ($event == 12) {echo $phrase[797];}
						if ($event == 13) {echo $phrase[798];}
						if ($event == 14) {echo $phrase[799];}
						if ($event == 16) {echo $phrase[823];}
						if ($event == 17) {echo $phrase[824];}
						if ($event == 18) {echo $phrase[825];}
						
						echo "</td><td>";
						
						if ($event == 13)
						{
												if (isset($deleted) && array_key_exists($_pageid,$deleted))
		{
		 
		//if ($deleted[$_pageid] == 1)	
		 if ($deleted[$_pageid] == 1 && !array_key_exists($_pageid,$restore_page_button))
		 {
			echo " <form action=\"wiki.php\"method=\"post\" style=\"display:inline\">

	<input type=\"hidden\" name=\"restorepage\" value=\"$_pageid\">

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"restorepage\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[780]\">
	</form>
		";
		 $restore_page_button[$_pageid] = "yes";
		 }
						}
						}
						echo "</td><td class=\"grey\"> $by</td><td class=\"grey\"> $when</td></tr>";
						}
				echo "</table>";
	}
	
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletecomment")
{
	echo "<br><br><b>$phrase[14]</b><br><br> $_REQUEST[person]<br><br>
	<a href=\"wiki.php?m=$m&amp;update=deletecomment&amp;page_id=$page_id&content_id=$content_id&amp;edit=yes\">$phrase[12]</a> | <a href=\"wiki.php?m=$m&amp;edit=yes&amp;page_id=$page_id\">$phrase[13]</a>";
	
}

elseif (isset($_GET["event"]) && $_GET["event"] == "pagelist")
	{
		echo "<br><br><b>$phrase[57]</b><br>";
	$sql = "SELECT * FROM page where m = \"$m\" and deleted = '0'";
				$DB->query($sql,"wiki.php");
					while ($row = $DB->get()) 
						{
							
						$_pageid= $row["page_id"];
						$_page_title = formattext($row["page_title"]);
						
						
						echo "<a href=\"wiki.php?m=$m&amp;page_id=$_pageid\">$_page_title</a><br>";
						}
				
	}
elseif (($access->thispage > 2) &&  isset($_GET["event"]) && $_GET["event"] == "doc")
	{
	echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;edit=yes\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a>
	
	
	
	<form action=\"wiki.php\" method=\"post\" enctype=\"multipart/form-data\" style=\"width:90%\">
	<fieldset><legend>$phrase[80]</legend><br>
						<p><input type=\"file\" name=\"upload[0]\" > <button onclick=\"addchooser();return false;\">$phrase[996]</button></p>
						<div id=\"fs\">
						
						</div>
						<br>
						<b>$phrase[1081]</b><br>
			
			<textarea name=\"metadata\" id=\"tags\" cols=\"60\" rows=\"2\"></textarea>
                        <span onclick=\"spanedit()\">$phrase[1081]</span>
                        <div id=\"taglist\" class=\"primary\" style=\"margin:1em 0;padding:1em;border:solid 1px;display:none;width:600px\">";
                        
                        $sql = "select tags from tags where m = '$m'";
			
				$DB->query($sql,"editcalendar.php");
				$row = $DB->get(); 
				
				$tags = trim($row["tags"]);
                                $tag = array();
                                $tag = explode(" ", $tags);
                                foreach ($tag as $value)
                                {
                                   $value = trim($value);
                                    if ($value != "")
                                    {
                                    echo " <span style=\"line-height:2.5em;margin:0.5em;padding: 0.3em 0.8em;\" class=\"accent\" onclick=\"addtag('$value','tags')\">$value</span> "; 
                                    }
                                }
                        
                        
                        echo "                           
</div> <span  id=\"editlink\" style=\"display:none\" onclick=\"editlist()\">$phrase[26]</span>
<span  id=\"savelink\" style=\"display:none\" onclick=\"savelist()\">$phrase[906]</span> 
<script>                            
window.tagdisplay = 'off';

function spanedit()
{
 
if (window.tagdisplay == 'off')
{
document.getElementById('taglist').style.display = 'block';

window.tagdisplay = 'on'

}
else
{
document.getElementById('taglist').style.display = 'none';

window.tagdisplay = 'off'

}
 
                            
 if (document.getElementById('TagListTextArea'))
     {
     document.getElementById('editlink').style.display = 'none';
     if (window.tagdisplay == 'off'){ document.getElementById('savelink').style.display = 'none';}
         else { document.getElementById('savelink').style.display = 'inline';}                          
                                
     } else
      {
       if (window.tagdisplay == 'off'){ document.getElementById('editlink').style.display = 'none';}
         else { document.getElementById('editlink').style.display = 'inline';}
    document.getElementById('savelink').style.display = 'none';                         
      }
}

function updateTagListTextArea(result)
          {
                       
          document.getElementById('TagListTextArea').value = result;   
           }
                                
function editlist()
{
url = 'ajax_tags.php?m=$m&event=gettags';
document.getElementById('taglist').innerHTML = '<textarea id=\"TagListTextArea\" class=\"red\" cols=\"60\" rows=\"4\"></textarea>';
ajax(url,updateTagListTextArea);
document.getElementById('editlink').style.display = 'none';
document.getElementById('savelink').style.display = 'inline';
                    


}
                                
function savelist()
{
var tags = encodeURIComponent(document.getElementById('TagListTextArea').value)
 
url = 'ajax_tags.php?m=$m&event=updatetags&tags=' + tags;
updatePage(url,'taglist');                                

document.getElementById('taglist').style.display = 'block';
document.getElementById('editlink').style.display = 'inline';
document.getElementById('savelink').style.display = 'none';
                    
//window.tagdisplay = 'off'

}                                
                                
                                
                                
</script><br><br>
			
						
							<input type=\"hidden\" name=\"edit\" value=\"yes\">

						
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"update\" value=\"uploaddoc\">
						
						<input type=\"submit\" name=\"submit\" value=\"$phrase[80]\" >
						</fieldset>
						</form>	
						<script type=\"text/javascript\">
						var counter = 1;
						
						function addchooser()
						{
						var fs = document.getElementById(\"fs\")
					
						fs.innerHTML = fs.innerHTML + '<p id=\"p_' + counter + '\"><input type=\"file\" name=\"upload[' + counter + ']\" id=\"f_' + counter + '\"> <img src=\"../images/remove.png\" onclick=\"hide(\'' + counter + '\')\"></p>';
						counter++;
						}
						
						
						 function hide(id)
							 {

	 					document.getElementById('p_' + id).style.display = \"none\";	
						document.getElementById('f_' + id).disabled=true;
	 					}
						</script>
						
						";
		
	}
elseif (($access->thispage > 2) && isset($_GET["event"]) && $_GET["event"] == "delete")
	{
	
	echo "<br><b>$phrase[14]</b><br><br> $_REQUEST[title]<br><br>
	<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;content_id=$content_id&amp;update=delete\">$phrase[12]</a> | <a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;content_id=$content_id\">$phrase[13]</a>";
	
	}

elseif (($access->thispage > 2) && isset($_GET["event"]) && $_GET["event"] == "editparagraph")

{
	
	echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;edit=yes\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a>";
	
	if ($input == 0)
	{
	echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:pop_window('../main/tags.php')\" ><img src=\"../images/help.png\" title=\"$phrase[337]\"  alt=\"$phrase[337]\"></a>
	
	
	<script type=\"text/javascript\">
 function pop_window(url) {
  var tagspop = window.open(url,'','status,resizable,scrollbars,width=400,height=500');
  	if (window.focus) {tagspop.focus()}
 }
	</script>
	";
	}
	
	
	
	$content_id = $_GET["content_id"];
	$sql = "SELECT title, body,imagealign, page_title FROM content, page where page.page_id = content.page_id and content_id = \"$content_id\"";
	$DB->query($sql,"editcontent.php"); 
	$row = $DB->get();
	$title = $row["title"];	
	$body = $row["body"];	
	$page_title = $row["page_title"];			
	$image_align = $row["imagealign"];	
	
			
		
		echo "
	<br><br>
	
	<form method=\"POST\" action=\"wiki.php\" style=\"float:left\"><fieldset><legend>$phrase[81]</legend><br>
	
						<b>$phrase[74]</b><br>
						<input type=\"text\" name=\"title\" value=\"$title\" size=\"60\" maxlength=\"100\">
						<br><br>
						
					
						<b>$phrase[1022]</b><br><select name=\"imagealign\">";
						
						
			if (isset($align_label))
						{
					foreach ($align_label as $ii => $label)
							{
			echo "<option value=\"$ii\" ";
			if ($image_align == $ii) {echo " selected";}
			echo ">$label</option>";
							}
						}
						
						
						
						echo "</select>	<br><br>
						
						<b>$phrase[75]</b><br>
						<textarea name=\"body\" id=\"body\" class=\"ckeditor\" cols=\"60\" rows=\"15\" style=\"float:left\">$body</textarea><br><br>
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"update\" value=\"edit\">
							<input type=\"hidden\" name=\"edit\" value=\"yes\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"submit\" value=\"$phrase[28]\" name=\"submit\">
		
		
		</fieldset></form>";
	
	
	
	
	if ($input == 1)
		{
			//display editor GUI
			if (isset($WYSIWYG) && $WYSIWYG == "openWYSIWYG")
			{
		
		echo "<script type=\"text/javascript\" src=\"../wysiwyg/wysiwyg.js\"></script> 
		  <script type=\"text/javascript\">
  generate_wysiwyg('body');

</script> ";
			}
			
			elseif (isset($WYSIWYG) && $WYSIWYG == "CKEditor")
			{
				
				echo "
				<script type=\"text/javascript\" src=\"../wysiwyg/ckeditor/ckeditor_basic.js\"></script>
			
";

				
			}

	else		{
echo "<script type=\"text/javascript\" src=\"../wysiwyg/nicEdit.js\"></script>

<script type=\"text/javascript\">
	bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>
";
			}
		}
		

	
	
}
elseif (($access->thispage > 2) && isset($_REQUEST["event"]) && $_REQUEST["event"] == "keywords")
{
	
		 $sql = "select * from documents where  documents.m = \"$m\" and doc_id = \"$doc_id\"";
	
$DB->query($sql,"wiki.php");
	$row = $DB->get();
$tags = $row["keywords"];


	echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;edit=yes\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a><form action=\"wiki.php\" method=\"post\" style=\"width:90%\"><fieldset><legend>$phrase[291]</legend>
						
					<br>
						
					
					<b>$phrase[317]</b>  $row[doc_name]<br><br>

						<b>$phrase[1081]</b><br>
			
			<textarea name=\"description\" id=\"tags\" cols=\"60\" rows=\"2\">$tags</textarea>
                        <span onclick=\"spanedit()\">$phrase[1081]</span>
                        <div id=\"taglist\" class=\"primary\" style=\"margin:1em 0;padding:1em;border:solid 1px;display:none;width:600px\">";
                        
                        $sql = "select tags from tags where m = '$m'";
			
				$DB->query($sql,"editcalendar.php");
				$row = $DB->get(); 
				
				$tags = trim($row["tags"]);
                                $tag = array();
                                $tag = explode(" ", $tags);
                                foreach ($tag as $value)
                                {
                                   $value = trim($value);
                                    if ($value != "")
                                    {
                                    echo " <span style=\"line-height:2.5em;margin:0.5em;padding: 0.3em 0.8em;\" class=\"accent\" onclick=\"addtag('$value','tags')\">$value</span> "; 
                                    }
                                }
                        
                        
                        echo "                           
</div> <span  id=\"editlink\" style=\"display:none\" onclick=\"editlist()\">$phrase[26]</span>
<span  id=\"savelink\" style=\"display:none\" onclick=\"savelist()\">$phrase[906]</span> 
<script>                            
window.tagdisplay = 'off';

function spanedit()
{
 
if (window.tagdisplay == 'off')
{
document.getElementById('taglist').style.display = 'block';

window.tagdisplay = 'on'

}
else
{
document.getElementById('taglist').style.display = 'none';

window.tagdisplay = 'off'

}
 
                            
 if (document.getElementById('TagListTextArea'))
     {
     document.getElementById('editlink').style.display = 'none';
     if (window.tagdisplay == 'off'){ document.getElementById('savelink').style.display = 'none';}
         else { document.getElementById('savelink').style.display = 'inline';}                          
                                
     } else
      {
       if (window.tagdisplay == 'off'){ document.getElementById('editlink').style.display = 'none';}
         else { document.getElementById('editlink').style.display = 'inline';}
    document.getElementById('savelink').style.display = 'none';                         
      }
}

function updateTagListTextArea(result)
          {
                       
          document.getElementById('TagListTextArea').value = result;   
           }
                                
function editlist()
{
url = 'ajax_tags.php?m=$m&event=gettags';
document.getElementById('taglist').innerHTML = '<textarea id=\"TagListTextArea\" class=\"red\" cols=\"60\" rows=\"4\"></textarea>';
ajax(url,updateTagListTextArea);
document.getElementById('editlink').style.display = 'none';
document.getElementById('savelink').style.display = 'inline';
                    


}
                                
function savelist()
{
var tags = encodeURIComponent(document.getElementById('TagListTextArea').value)
   
url = 'ajax_tags.php?m=$m&event=updatetags&tags=' + tags;
updatePage(url,'taglist');                                

document.getElementById('taglist').style.display = 'block';
document.getElementById('editlink').style.display = 'inline';
document.getElementById('savelink').style.display = 'none';
                    
//window.tagdisplay = 'off'

}                                
                                
                                
                                
</script>
						<br><br>
			
						
						
							<input type=\"hidden\" name=\"m\" value=\"$m\">
							<input type=\"hidden\" name=\"doc_id\" value=\"$doc_id\">
							<input type=\"hidden\" name=\"edit\" value=\"yes\">
						<input type=\"hidden\" name=\"update\" value=\"updatedoc\">
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\" >
						</fieldset></form>";
}

elseif (($access->thispage > 2) && isset($_REQUEST["event"]) && $_REQUEST["event"] == "addparagraph")

{
	echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;edit=yes\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a>";
	
	if ($input == 0)
	{
		echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:pop_window('../main/tags.php')\" ><img src=\"../images/help.png\" title=\"$phrase[337]\"  alt=\"$phrase[337]\"></a><br><br>
	
	<script type=\"text/javascript\">
 function pop_window(url) {
  var popit = window.open(url,'console','status,resizable,scrollbars,width=400,height=500');
 }
	</script>
	";
	}
	
	
		echo "<form method=\"POST\" action=\"wiki.php\" style=\"float:left\"><fieldset><legend>$phrase[73]</legend>
		<table >
	<tr><td>
						<b>$phrase[74]</b><br>
						<input type=\"text\" name=\"title\"  size=\"50\" maxlength=\"100\">
						</td></tr>
						
						
					<tr><td> <b>$phrase[1022]</b><br><select name=\"imagealign\">";
	
					if (isset($align_label))
						{
					foreach ($align_label as $ii => $label)
							{
							echo "<option value=\"$ii\">$label</option>";
							}
					}
	
	
	echo "</select></td></tr>
						
						
						
						<tr><td><br>
						<b>$phrase[75]</b><br>
						<textarea name=\"body\" id=\"body\" class=\"ckeditor\" cols=\"50\" rows=\"15\" style=\"float:left\"></textarea><br><br>
						
						<input type=\"submit\" value=\"$phrase[73]\" name=\"submit\">
							<input type=\"hidden\" name=\"page_id\" value=\"$page_id\"><br>
								<input type=\"hidden\" name=\"update\" value=\"add paragraph\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<input type=\"hidden\" name=\"edit\" value=\"yes\">
		</td></tr>		
		</table>
		
		</fieldset></form>";
	
if ($input == 1)
		{
				//display editor GUI
			if (isset($WYSIWYG) && $WYSIWYG == "openWYSIWYG")
			{
		
		echo "<script type=\"text/javascript\" src=\"../wysiwyg/wysiwyg.js\"></script> 
		  <script type=\"text/javascript\">
  generate_wysiwyg('body');

</script> ";
			}
			
			elseif (isset($WYSIWYG) && $WYSIWYG == "CKEditor")
			{
				
				echo "
				<script type=\"text/javascript\" src=\"../wysiwyg/ckeditor/ckeditor_basic.js\"></script>
			
";

				
			}

	else		{
echo "<script type=\"text/javascript\" src=\"../wysiwyg/nicEdit.js\"></script>

<script type=\"text/javascript\">
	bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>
";
			}
		
		}
						
					
}

elseif (($access->thispage > 2) && isset($_REQUEST["event"]) && $_REQUEST["event"] == "newpage")

{
	
		echo "<br><br><form method=\"POST\" action=\"wiki.php\" style=\"width:90%\"><fieldset><legend>$phrase[71]</legend>
		<table >
	<tr><td><br>
						<b>$phrase[87]</b><br>
					
						<input type=\"text\" name=\"page_title\"  size=\"50\" maxlength=\"100\">
						</td></tr><tr><td>
					
						
						<input type=\"submit\" value=\"$phrase[71]\" name=\"submit\">
						
								<input type=\"hidden\" name=\"update\" value=\"newpage\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<input type=\"hidden\" name=\"edit\" value=\"yes\">
		</td></tr>		
		</table><br>
	$phrase[830]
		
		</fieldset></form>";
	

						
					
}
else 
{
	echo "";
	
		$sql = "select image_id, content.content_id as content_id, images.name as name from images, content, page where content.page_id = page.page_id and content.content_id = images.content_id and content.page_id = \"$page_id\" and images.deleted = '0'";
	$DB->query($sql,"editcontent.php"); 
	
	$i = 0;	
			while (	$row = $DB->get()) 
						{
						
						$iarray_content[$i] = $row["content_id"];
						$iarray_name[$i] = $row["name"];
						$iarray_imageid[$i] = $row["image_id"];
						$i++;
						}
	
	
	
	// find documents associated with this page and put into array
	
	$sql = "select doc_id,size, content.content_id as content_id, doc_name from documents, content, page where content.page_id = page.page_id and content.content_id = documents.content_id and page.page_id = \"$page_id\" and documents.deleted = '0' order by doc_name";
	$DB->query($sql,"editcontent.php"); 
	$i = 0;	
			while (	$row = $DB->get()) 
						{
						
						$darray_content[$i] = $row["content_id"];
						$darray_docname[$i] = $row["doc_name"];
						$darray_linkname[$i] = urlencode($row["doc_name"]);
						$darray_docid[$i] = $row["doc_id"];
						$darray_size[$i] = $row["size"];
						$i++;
						}
	

	
	


if (isset($page_title))
{

$page_title = urlencode($page_title);
}
	
	
	
	//display page contents
		$sql = "SELECT * FROM content where page_id = \"$page_id\"  and event = '0' and deleted = '0' order by page_order";
		
		 //
					if(isset($edit)) {$view = "edit";} else {$view ="normal";}
					
					$DB->query($sql,"page.php");

					
					//these two variables are for up and down arrows that allow reordering of a page
					$total = 0;
					$counter = 0;
					$pagechange = 0;
					
					while (	$row = $DB->get()) 
						{
						$counter++;
						
						
						$array_content_id[$counter] = $row["content_id"];
						$array_title[$counter] = formattext($row["title"]);
						$array_linktitle[$counter] = urlencode($row["title"]);
					
						if ($input != 0)
						{
						$array_body[$counter]  = wikilink($m,formattext_html($row["body"]),$pages_array,$access,$phrase[71],$view);	
						}
						else 
						{
							
						$array_body[$counter]  = wikilink($m,formattext($row["body"]),$pages_array,$access,$phrase[71],$view);	
						}
						
						$array_updatedby[$counter]  = formattext($row["updated_by"]);
						$array_updatedwhen[$counter]= date("g:ia",$row["updated_when"]) . strftime(" %x",$row["updated_when"]);
						$array_ip[$counter]  = $row["updated_ip"];
					
						//this will be used in ordering page paragraphs
						if ($row["event"] == 0) 
						{
						$array_order[$counter]  = $row["content_id"];
						$total++;
						}
						$id = $row["content_id"];
						$array_archive[$counter]  = $row["archive"];
						$array_deleted[$counter]  = $row["deleted"];
						$array_event[$counter]  = $row["event"];
						if ($row["archive"] != "" && $row["event"] < 8) 
						{
						$archive[$id]  = $row["archive"];
						}
						
						if ($row["event"] == 9 || $row["event"] == 10  || $row["event"] == 11 )
						{
						$pagechange++;
						//echo "deleted $id " ;
						}
						}
						
						if (isset($array_order))
						{
						$array_order = array_merge($array_order);
						}
	
if ($access->thispage > 2 && isset($page_id)) 
	{
	if (!isset($edit) ) {echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;edit=yes\"><img src=\"../images/page_edit.png\" title=\"$phrase[828]\"  alt=\"$phrase[828]\"></a>&nbsp;&nbsp;&nbsp;";}
	else {echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id\"><img src=\"../images/page.png\" title=\"$phrase[827]\"  alt=\"$phrase[827]\"></a>&nbsp;&nbsp;&nbsp;";}


	}
			


if ($pagechange > 0 && $access->thispage > 2 && isset($edit)) { echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;event=pagehistory&amp;title=$page_title\"><img src=\"../images/clock.png\" title=\"$phrase[781]\"  alt=\"$phrase[781]\"></a>&nbsp;&nbsp;&nbsp;";}	


if ($access->thispage > 2 && isset($edit) && isset($page_id))
{
	echo "<a href=\"wiki.php?m=$m&amp;event=deletepage&amp;page=$page_id&amp;title=$linktitle\"><img src=\"../images/page_delete.png\" title=\"$phrase[832]\"  alt=\"$phrase[832]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;event=addparagraph&amp;edit=yes\"><img src=\"../images/paragraph_add.png\" title=\"$phrase[73]\"  alt=\"$phrase[73]\"></a>";
}
					
					$counter = 0;
					if (isset($array_content_id))
					{
					foreach ($array_content_id as $index => $content_id)
						{
						
							if ($array_event[$index] == 0 && $array_deleted[$index] == 0)
							{
						//$counter++;
						
						$linktitle = urlencode($array_title[$index]);
						?>
						
						
						<h3><?php echo $array_title[$index]?></h3>
						
						
						<?php
						echo "<span class=\"primary\">$array_updatedwhen[$index]";
							
							if ($access->thispage == 3)  {echo "<span class=\"leftspan\">$array_updatedby[$index]</span><span class=\"leftspan\"> $array_ip[$index]</span>";} 
							echo "</span><br><br>";
						
						
						
					
						
						


									if (isset($iarray_imageid))
									{
									foreach ($iarray_imageid as $i => $image_id)
										{
										
									
										if ($iarray_content[$i] == $content_id)
												{
												$name = urlencode($iarray_name[$i]);
												echo "<img src=\"../main/image.php?m=$m&image_id=$image_id\" align=\"center\"> "; 
												if ($access->thispage > 1 && isset($edit))
												{
												echo "<a href=\"wiki.php?event=deleteimage&amp;name=$name&amp;image_id=$image_id&amp;page_id=$page_id&amp;m=$m&content_id=$content_id&amp;edit=yes\" style=\"color:grey\"><img src=\"../images/picture_delete.png\" title=\"$phrase[590]\"  alt=\"$phrase[590]\"></a><br><br><br>";
												}
												}
										
										}
									}
						
					
						
						
						echo "<p style=\"margin: 2em 0 2em 0\">						
						$array_body[$index]
						
						
						</p>";
						
						
						
						if ($access->thispage > 2 && isset($edit))
						{
							
						
						
							
						if (($total > 0) && ($counter > 0) )
							{
							
							//Change order array so the paragraph can be moved up page
							//echo "<p></p>";
						
								
							
							foreach ($array_order as $ind => $value)
									{
									
									//echo "$array_order[$index] <br>";
									if ($ind == ($counter -1))
										{
										$up = $array_order;
										$up[$ind] = $array_order[$counter];
										$up[$counter] = $value;
										}
									
									}
							
							
								

							$up = implode(",",$up);
							echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;update=reorder&amp;reorder=$up&amp;edit=yes\"><img src=\"../images/up.png\" title=\"$phrase[77]\"  alt=\"$phrase[77]\"></a>&nbsp;&nbsp;&nbsp;";
							
							
							
							
							}
						
						
						
						if (($total > 0) && ($counter < $total - 1) )
							{
							
						
							
							
								foreach ($array_order as $ind => $value)
									{
									//echo "$array_order[$index] <br>";
									
									if ($ind == ($counter))
										{
										$down = $array_order;
										$temp = $down[$ind];
										$down[$ind] = $down[$ind + 1];
										$down[$ind + 1] = $temp;
										}
									
									}
								
								
							
							$down = implode(",",$down);
							echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;reorder=$down&amp;update=reorder&amp;edit=yes\"><img src=\"../images/down.png\" title=\"$phrase[78]\"  alt=\"$phrase[78]\"></a>&nbsp;&nbsp;&nbsp;";
							}	
							
					
						if ($array_updatedby[$index] != "")
						{
						$tag = "$phrase[778] $array_updatedby[$index], $phrase[185]: $array_updatedwhen[$index], $phrase[144]:$array_ip[$index].";
						
						
						echo "<img src=\"../images/user_orange.png\" title=\"$tag\"  alt=\"$tag\">&nbsp;&nbsp;&nbsp;";
					 }	
						
						echo "<a href=\"wiki.php?m=$m&amp;event=doc&amp;content_id=$content_id&amp;page_id=$page_id&amp;edit=yes\" style=\"color:grey\"><img src=\"../images/attach.png\" title=\"$phrase[80]\"  alt=\"$phrase[80]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"wiki.php?m=$m&amp;content_id=$content_id&amp;event=image&amp;page_id=$page_id&amp;edit=yes\" style=\"color:grey\"><img src=\"../images/picture_add.png\" title=\"$phrase[79]\"  alt=\"$phrase[79]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"wiki.php?m=$m&amp;event=editparagraph&amp;content_id=$content_id&amp;page_id=$page_id&amp;edit=yes\" style=\"color:grey\"><img src=\"../images/page_white_edit.png\" title=\"$phrase[81]\"  alt=\"$phrase[81]\"></a>&nbsp;&nbsp;&nbsp;";
						
						if (isset($archive) && in_array($content_id,$archive)) {
							echo "<a href=\"wiki.php?m=$m&amp;event=paragraphhistory&amp;content_id=$content_id&amp;page_id=$page_id&amp;title=$linktitle\" style=\"color:grey\"><img src=\"../images/clock.png\" title=\"$phrase[779]\"  alt=\"$phrase[779]\"></a>&nbsp;&nbsp;&nbsp;";}
						echo "<a href=\"wiki.php?m=$m&amp;content_id=$content_id&amp;event=delete&amp;title=$linktitle&amp;&amp;page_id=$page_id&amp;\" style=\"color:grey\"><img src=\"../images/page_remove.png\" title=\"$phrase[650]\"  alt=\"$phrase[650]\"></a><br><br>";
						
					

						
						} //end edit test
													
							
						
							if (isset($darray_docid))
							{
							
							$count = count($darray_docid);	
							if ($count > 0 ) 
								{
								echo "<table > ";
								}
							
							foreach ($darray_docid as $index => $doc_id)
							{
							if ($darray_content[$index] == $content_id)
									{
									//create hash so images canot be directly viewed
									$h = md5($darray_docid[$index]);
									$size = round($darray_size[$index] / 1000) . " Kb";
									echo "<tr><td><a href=\"../main/doc.php?m=$m&doc_id=$darray_docid[$index]\">$darray_docname[$index]</a>&nbsp; $size</td>";
							if ($access->thispage > 2 && isset($edit))
						{				
									echo "<td > <a href=\"wiki.php?m=$m&amp;event=keywords&doc_id=$doc_id&amp;page_id=$page_id&amp;content_id=$content_id\" style=\"padding-left:1em\"><img src=\"../images/letter.png\" title=\"$phrase[99]\"  alt=\"$phrase[99]\"></a></td><td> <a href=\"wiki.php?m=$m&amp;event=deletedoc&doc_id=$doc_id&amp;page_id=$page_id&amp;content_id=$content_id&amp;filename=$darray_linkname[$index]&amp;content_id=$content_id\" style=\"padding-left:1em\"><img src=\"../images/cross.png\" title=\"$phrase[651]\"  alt=\"$phrase[651]\"></a></td>"; }
									echo "</tr>";
									}
							}
							if ($count > 0) 
							{
							echo "</table><br>";
							
							}
							}
							

						
						
						
						$counter++;
							}
						
						}//ends for each
						}
						
							if ($total == 0 && ($access->thispage > 2 && isset($_REQUEST["edit"]))) 
						{
							echo "<p style=\"color:Red\">$phrase[843]</p><p>$phrase[844]<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;event=addparagraph&amp;edit=yes\" ><img src=\"../images/paragraph_add.png\" title=\"$phrase[73]\"  alt=\"$phrase[73]\"></a></p>";	
							
						}
						
						
									$sql = "SELECT * FROM content where event = '15' and deleted = '0' and page_id  = \"$page_id\"";
	$DB->query($sql,"pagecontent.php");
	$counter = 0;
				
					while ($row = $DB->get()) 
						{
						
						$_content_id[] = $row["content_id"];
						
						$body = $row["body"];
						$_body[] = formattext($body);
						$_updated_by[] = $row["updated_by"];
						$link_updated_by[] = urlencode($row["updated_by"]);
						$_updated_ip[] = $row["updated_ip"];
						$_updated_when[] =  strftime(" %x ",$row["updated_when"]) . date("g:ia",$row["updated_when"]) ;
						$counter++;
						}
	
		if  (isset($page_id))
		{
		echo "<h3 style=\"border:none;\">$phrase[135] <img src=\"../images/comments.png\" title=\"$phrase[135]\"  alt=\"$phrase[135]\"></h3>";
		if ($access->thispage > 1 ) 
			{
			echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;event=addcomment\"><img src=\"../images/comment_add.png\" title=\"$phrase[822]\"  alt=\"$phrase[822]\"></a>";	
			}
		
			if (isset($_content_id))
					{
						foreach ($_content_id as $index => $id)
							{

						
						echo "<br><br><span style=\"color:black\"><b>$_updated_by[$index]</b></span> <br><span class=\"grey\">$_updated_when[$index]</span><br>
						$_body[$index]<br>";
						if ($access->thispage == 3 && isset($edit))
						{
							echo "<a href=\"wiki.php?m=$m&amp;page_id=$page_id&amp;event=deletecomment&content_id=$id&person=$link_updated_by[$index]\" style=\"color:grey\"><img src=\"../images/comment_delete.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>	";
						}
							}
						}
		
		
		}
	 






}
	}
				
				
				
	if (!isset($page_id) && !isset($_REQUEST["event"]) && $access->thispage > 2 )
				
				{
					echo "<br><br><a href=\"wiki.php?m=$m&update=wikihomepage\">$phrase[829]</a>";
				}


	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
			//end contentbox
		echo "
		
		  	<script type=\"text/javascript\">
addEvent(window, 'load', loadfeeds);
	</script>
		</div></div>";
		
	 
	     

	    
		
		
include("../includes/rightsidebar.php");
	
include ("../includes/footer.php");

?>

