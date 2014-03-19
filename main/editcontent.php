<?php

//content event codes
// 1 updated text
// 2 add image
// 3 image deleted
// 4 doc uploaded
// 5 doc deleted
// 6 doc keywords updated
// 7 image restored
// 8 restored doc
// 9 delete paragraph
// 10 added paragraph 
// 11 restored paragraph
// 12 added page
// 13 deleted page
// 14 restored page




if (isset($page_id))
	{

		
$integers[] = "content_id";
$integers[] = "image_id";
$integers[] = "doc_id";



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}	

	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletedoc")
{	

	deletedoc($ip,$doc_id,$DB);

	
}


if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "restoredoc")
{	

	restoredocument($doc_id,$content_id,$ip,$page_id,$DB);
	
	
}

if (isset($_GET["update"]) && $_GET["update"] == "delete")
{	

	deleteparagraph($content_id,$page_id,$ip,$now,$DB);
	
	
	
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
	
		$sql = "insert into content values (NULL,'$imagename','$image_id','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','7','0')";	
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
	
		$sql = "insert into content values (NULL,'$doc_id','$description','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','6','0')";	
	$DB->query($sql,"editcontent.php");
	}

	
	



if (isset($_POST["update"]) && $_POST["update"] == "uploaddoc")

{		
		upload($m,$page_id,$content_id,$PREFERENCES,$DB,"document",$ip,$phrase);		

}








if (isset($_POST["update"]) && $_POST["update"] == "uploadimage")

{		
	
	upload($m,$page_id,$content_id,$PREFERENCES,$DB,"image",$ip,$phrase);
		
			

}








if (isset($_POST["update"]) && $_POST["update"] == "add paragraph")

{
$title = $DB->escape($_REQUEST["title"]);
$body = $DB->escape($_REQUEST["body"]);
$page_title = $DB->escape($page_title);
	
	
addparagraph($page_title,$title,$body,$page_id,$ip,$DB);

 
}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "edit" )
	{

	$title = $DB->escape($_REQUEST["title"]);
	$body = $DB->escape($_REQUEST["body"]);	
	$page_title = $DB->escape($page_title);
	
	updateparagraph($page_title,$content_id,$title,$body,$ip,$DB);
	
		
		
		
	}
	

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "restoreparagraph" )
	{
		
	
	
	//get old record 
	$sql = "select * from content where content_id = \"$content_id\"";
	
	$DB->query($sql,"editcontent.php");

	$row = $DB->get();
	$oldtitle = $DB->escape($row["title"]);	
	$oldbody = $DB->escape($row["body"]);	
	$oldpage_id = $row["page_id"];
	$oldpage_order = $row["page_order"];

	
	
	$sql = "update content set deleted = '0' WHERE content_id = \"$content_id\"";	
	//echo $sql;	
	$DB->query($sql,"editcontent.php");
	
	
									
$sql = "insert into content values (NULL,'$oldtitle','','$oldpage_id','$oldpage_order','$_SESSION[username]','$ip','$now','$content_id','11','0')";	
	$DB->query($sql,"editcontent.php");
	//echo $sql;
	
	$metadata = "$page_title"
		
		;
		$sql = "select * from content where page_id = \"$page_id\" and archive is null and deleted = '0'";
		$DB->query($sql,"editpage.php");

	
		while ($row = $DB->get()) 
		{
		$metadata .= "$row[title]
		$row[body]
	
	";
		}
		$metadata = $DB->escape($metadata);
		$sql = "update page set metadata = \"$metadata\" where page_id = \"$page_id\"";
		$DB->query($sql,"editpage.php");
		
		
		
		
		
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




if (isset($ERROR))
{
echo $ERROR;	
}
if (isset($WARNING))
{
warning($WARNING);	
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletedoc")
{
	echo "<br><b>$phrase[14]</b><br><br> $_REQUEST[filename]<br><br>
	<a href=\"editpage.php?m=$m&amp;update=deletedoc&amp;page_id=$page_id&doc_id=$doc_id&amp;content_id=$content_id\">$phrase[12]</a> | <a href=\"editpage.php?m=$m\">$phrase[13]</a>";
	
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteimage")
{
	echo "<br><b>$phrase[14]</b><br><br> $_REQUEST[name]<br><br>
	<a href=\"editpage.php?m=$m&amp;update=deleteimage&amp;page_id=$page_id&image_id=$image_id&amp;content_id=$content_id\">$phrase[12]</a> | <a href=\"editpage.php?m=$m\">$phrase[13]</a>";
	
}

elseif (isset($_GET["event"]) && $_GET["event"] == "paragraphhistory")
	{
	
	$restoredoc = "yes";	
	$restoreimage = "yes";
		
	echo "<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;event=editpage\">$phrase[796]</a>
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
			<form action=\"editpage.php\"method=\"post\">
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
			<form action=\"editpage.php\"method=\"post\">
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
		<form action=\"editpage.php\"method=\"post\">
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
	echo "$phrase[794]<br><br><i><b>$title</b><br>
	$body</i><br><br>

	<form action=\"editpage.php\"method=\"post\">
	<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
	<input type=\"hidden\" name=\"title\" value=\"$title\">
	<input type=\"hidden\" name=\"body\" value=\"$body\">
	<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
	<input type=\"hidden\" name=\"page_order\" value=\"$page_order\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"edit\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[780]\">
	</form>";
	}
	echo "</td></tr>";
	}
	
	echo "</table>";
	
}



elseif (isset($_GET["event"]) && $_GET["event"] == "pagehistory")
	{
	
	//$displayrestorebutton = "yes";
	
	echo "<a href=\"editpage.php?m=$m\">$phrase[57]</a> | <a href=\"editpage.php?m=$m&amp;event=editpage&amp;page_id=$page_id\">$phrase[796]</a><br><br><h4>$phrase[781]</h4><br><table class=\"colourtable\" style=\"width:70%;padding:0.5em\">";
	

	
	$sql = "select * from content where page_id  = \"$page_id\" ";

	$DB->query($sql,"editcontent.php");

	while ($row = $DB->get())
	{
	$content_id = $row["content_id"];	
	$titles[$content_id] = $row["title"];
	$bodies[$content_id] = $row["body"];
	$deleted[$content_id] = $row["deleted"];
	
	}

	
	
	$sql = "select * from content where page_id  = \"$page_id\"  and (event = '9' or event = '10' or event = '11') order by updated_when desc";

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
	
	
	if ($event == 10)
	{
		//Paragraph added
		echo "$phrase[793] <br><i><b>$titles[$archive]</b><br>$bodies[$archive]</i><br>
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
			echo "<br><form action=\"editpage.php\"method=\"post\">
	<input type=\"hidden\" name=\"content_id\" value=\"$archive\">
	<input type=\"hidden\" name=\"doc_id\" value=\"$body\">
<input type=\"hidden\" name=\"description\" value=\"$body\">
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
		
	}
	
	echo "</table>";
	
}

elseif (isset($_GET["event"]) && $_GET["event"] == "image")
	{
	echo "
	<a href=\"editpage.php?m=$m\">$phrase[57]</a> | <a href=\"editpage.php?m=$m&amp;evrnt=editpage&page_id=$page_id\">$phrase[796]</a><br><br><br>
	<h4>$phrase[98]</h4>
	
	
	<form action=\"editpage.php\" method=\"post\" enctype=\"multipart/form-data\"><p><br>
						
						
						
						

						<input type=\"file\" name=\"upload\" ><br><br>
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"update\" value=\"uploadimage\">
						
						<input type=\"submit\" name=\"submit\" value=\"$phrase[98]\" ></p>
						</form>	";
		
	}
	
	
elseif (isset($_GET["event"]) && $_GET["event"] == "doc")
	{
	echo "<a href=\"editpage.php?m=$m\">$phrase[57]</a> | <a href=\"editpage.php?m=$m&amp;evrnt=editpage&page_id=$page_id\">$phrase[796]</a><br><br><br>
	<h4>$phrase[80]</h4>
	
	
	<form action=\"editpage.php\" method=\"post\" enctype=\"multipart/form-data\"><p>
						<input type=\"file\" name=\"upload\" ><br><br>
						$phrase[99]<br><textarea name=\"metadata\" cols=\"50\" rows=\"5\"></textarea><br><br>
						
						

						
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"update\" value=\"uploaddoc\">
						
						<input type=\"submit\" name=\"submit\" value=\"$phrase[80]\" ></p>
						</form>	";
		
	}
elseif (isset($_GET["event"]) && $_GET["event"] == "delete")
	{
	
	echo "<br><b>$phrase[14]</b><br><br> $_REQUEST[title]<br><br>
	<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;content_id=$content_id&amp;update=delete\">$phrase[12]</a> | <a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;content_id=$content_id\">$phrase[13]</a>";
	
	}

elseif (isset($_GET["event"]) && $_GET["event"] == "editparagraph")

{
	
	echo "<a href=\"editpage.php?m=$m\">$phrase[57]</a> | <a href=\"editpage.php?m=$m&amp;evrnt=editpage&page_id=$page_id\">$phrase[796]</a> | ";
	if (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "editparagraph" || $_REQUEST["event"] == "addparagraph"))
	{
		echo "<script type=\"text/javascript\">
 function pop_window(url) {
  var popit = window.open(url,'console','status,resizable,scrollbars,width=400,height=500');
 }
	</script>

	
	<a href=\"javascript:pop_window('../main/tags.php')\" >$phrase[337]</a><br><br>
	
	";
	}
	
	
	$content_id = $_GET["content_id"];
	$sql = "SELECT title, body, page_title FROM content, page where page.page_id = content.page_id and content_id = \"$content_id\"";
	$DB->query($sql,"editcontent.php"); 
	$row = $DB->get();
	$title = $row["title"];	
	$body = $row["body"];	
	$page_title = $row["page_title"];			
			
	
			
		
		echo "<form method=\"get\" action=\"editpage.php\"><fieldset><legend>$phrase[81]</legend><br>
		<table>
	<tr><td>
						<b>$phrase[74]</b></td><td>
						<input type=\"text\" name=\"title\" value=\"$title\" size=\"60\" maxlength=\"100\">
						</td></tr><tr><td>
						<b>$phrase[75]</b></td><td>
						<textarea name=\"body\" cols=\"60\" rows=\"15\">$body</textarea></td></tr><tr><td></td><td>
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"update\" value=\"edit\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"submit\" value=\"$phrase[28]\" name=\"submit\">
		</td></tr>		
		</table>
		
		</fieldset></form>";
	
		
	
	
	
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "keywords")
{
	
		 $sql = "select * from documents where  documents.m = \"$m\" and doc_id = \"$doc_id\"";
	
$DB->query($sql,"filesview.php");
	$row = $DB->get();

echo "<form action=\"editpage.php\" method=\"post\" ><fieldset><legend>$phrase[291]</legend>
						
					<br>
						
						<table>
						<tr><td><b>$phrase[317]</b></td><td>$row[doc_name]</td></tr>
						<tr><td></td><td>&nbsp;</td></tr>
    <tr><td valign=\"top\"><b>$phrase[280]</b></td><td>
						<textarea name=\"description\" cols=\"60\" rows=\"12\">$row[keywords]</textarea></td></tr>
						<tr><td></td><td>
						
							<input type=\"hidden\" name=\"m\" value=\"$m\">
							<input type=\"hidden\" name=\"doc_id\" value=\"$doc_id\">
						
						<input type=\"hidden\" name=\"update\" value=\"updatedoc\">
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\" ></td></tr></table>
						</fieldset></form>";
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addparagraph")

{
	echo "<a href=\"editpage.php?m=$m\">$phrase[57]</a> | <a href=\"editpage.php?m=$m&amp;evrnt=editpage&page_id=$page_id\">$phrase[796]</a> | ";
	if (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "editparagraph" || $_REQUEST["event"] == "addparagraph"))
	{
		echo "<script type=\"text/javascript\">
 function pop_window(url) {
  var popit = window.open(url,'console','status,resizable,scrollbars,width=400,height=500');
 }
	</script>

	
	<a href=\"javascript:pop_window('../main/tags.php')\" >$phrase[337]</a><br><br>
	
	";
	}
	
		echo "<br><br><form method=\"POST\" action=\"editpage.php\" style=\"width:90%\"><fieldset><legend>$phrase[73]</legend>
		<table >
	<tr><td>
						<b>$phrase[74]</b><br>
						<input type=\"text\" name=\"title\"  size=\"50\" maxlength=\"100\">
						</td></tr><tr><td><br>
						<b>$phrase[75]</b><br>
						<textarea name=\"body\" cols=\"50\" rows=\"15\"></textarea><br><br>
						
						<input type=\"submit\" value=\"$phrase[73]\" name=\"submit\">
							<input type=\"hidden\" name=\"page_id\" value=\"$page_id\"><br>
								<input type=\"hidden\" name=\"update\" value=\"add paragraph\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
		</td></tr>		
		</table>
		
		</fieldset></form>";
	

						
					
}
else 
{
	echo "";
	
		$sql = "select image_id, content.content_id, images.name from images, content, page where content.page_id = page.page_id and content.content_id = images.content_id and content.page_id = \"$page_id\" and images.deleted = '0'";
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
	
	$sql = "select doc_id, content.content_id as content_id, doc_name from documents, content, page where content.page_id = page.page_id and content.content_id = documents.content_id and page.page_id = \"$page_id\" and documents.deleted = '0' order by doc_name";
	$DB->query($sql,"editcontent.php"); 
	$i = 0;	
			while (	$row = $DB->get()) 
						{
						
						$darray_content[$i] = $row["content_id"];
						$darray_docname[$i] = $row["doc_name"];
						$darray_linkname[$i] = urlencode($row["doc_name"]);
						$darray_docid[$i] = $row["doc_id"];
						
						$i++;
						}
	

	
	




$page_title = urlencode($page_title);


if ($type == "n") {$paraordering = "d";}
	
if ($paraordering == "t")
	{
	$insert = "order by title";
	}
elseif ($paraordering == "a")
	{
	$insert = " order by content_id asc";
	}
elseif ($paraordering == "d")
	{
	$insert = "order by content_id desc";
	} 
elseif ($paraordering == "c")
	{
	$insert = "order by page_order";
	}
else { $insert = "";}
	
	
	
	//display page contents
		$sql = "SELECT * FROM content where page_id = \"$page_id\" and deleted = '0' $insert";
		
		 //
					
					
					$DB->query($sql,"page.php");

					
					//these two variables are for up and down arrows that allow reordering of a page
					$total = $DB->countrows();
					$counter = 0;
					$pagechange = 0;
					
					while (	$row = $DB->get()) 
						{
						$counter++;
						
						
						$array_content_id[$counter] = $row["content_id"];
						$array_title[$counter] = formattext($row["title"]);
						$array_linktitle[$counter] = urlencode($row["title"]);
						$array_body[$counter]  = formattext($row["body"]);
						$array_updatedby[$counter]  = formattext($row["updated_by"]);
						$array_updatedwhen[$counter]= date("g:ia",$row["updated_when"]) . strftime(" %x",$row["updated_when"]);
						$array_ip[$counter]  = $row["updated_ip"];
					
						//this will be used in ordering page paragraphs
						$array_order[$counter]  = $row["content_id"];
						$id = $row["content_id"];
						$array_archive[$counter]  = $row["archive"];
						$array_deleted[$counter]  = $row["deleted"];
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
						

	
	
						
echo "<a href=\"editpage.php?m=$m\">$phrase[57]</a> ";	

if ($pagechange > 0) { echo " | <a href=\"editpage.php?m=$m&page_id=$page_id&event=pagehistory&title=$page_title\">$phrase[781]</a>";}	


	
	echo " | <a href=\"editpage.php?m=$m&amp;page_id=$page_id&event=addparagraph\">$phrase[73]<a>";
						
						
					$counter = 0;
					if (isset($array_content_id))
					{
					foreach ($array_content_id as $index => $content_id)
						{
							if ($array_archive[$index] == "" && $array_deleted[$index] == 0)
							{
						$counter++;
						
						$linktitle = urlencode($array_title[$index]);
						?>
						
						
						<h3><?php echo $array_title[$index]?>  www</h3>
						
						
						
					
						<?php
								
						echo "<a href=\"editpage.php?m=$m&amp;content_id=$content_id&amp;event=image&amp;page_id=$page_id\">$phrase[79]</a><br><br>";


									if (isset($iarray_imageid))
									{
									foreach ($iarray_imageid as $i => $image_id)
										{
										
									
										if ($iarray_content[$i] == $content_id)
												{
												$name = urlencode($iarray_name[$i]);
												echo "<img src=\"../main/image.php?m=$m&image_id=$image_id\" align=\"center\">  <a href=editpage.php?event=deleteimage&amp;name=$name&amp;image_id=$image_id&amp;page_id=$page_id&amp;m=$m&content_id=$content_id>$phrase[590]</a><br><br><br>";
												}
										
										}
									}
						
					
						
						
						echo "<p style=\"margin: 2em 0 1em 0\">						
						$array_body[$index]
						
						
						</p>";
						if ($array_updatedby[$index] != "")
						{
						echo "<br><span style=\"color:grey\">$phrase[778] $array_updatedby[$index], $phrase[185]: $array_updatedwhen[$index], $phrase[144]:$array_ip[$index].</span><br><br>"; }
						
						echo "<a href=\"editpage.php?m=$m&amp;event=doc&amp;content_id=$content_id&amp;page_id=$page_id\">$phrase[80]</a> &nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;event=editparagraph&amp;content_id=$content_id&amp;page_id=$page_id\">$phrase[81]</a>	&nbsp;&nbsp;&nbsp;";
						
						if (isset($archive) && in_array($content_id,$archive)) {
							echo "<a href=\"editpage.php?m=$m&amp;event=paragraphhistory&amp;content_id=$content_id&amp;page_id=$page_id&title=$linktitle\">$phrase[779]</a>&nbsp;&nbsp;&nbsp;";}
						echo "<a href=\"editpage.php?m=$m&amp;content_id=$content_id&amp;event=delete&title=$linktitle&amp;&amp;page_id=$page_id&amp;\">$phrase[650]</a><br><br>";
						
						
						if (($total > 0) && ($counter > 1) && ($paraordering == "c"))
							{
							
							//Change order array so the paragraph can be moved up page
							//echo "<p></p>";
						
								
							
							foreach ($array_order as $index => $value)
									{
									//echo "$array_order[$index] <br>";
									if ($index == ($counter -1))
										{
										$up = $array_order;
										$up[$index] = $array_order[$counter];
										$up[$counter] = $value;
										}
									}
							
							
								

							$up = implode(",",$up);
							echo "<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;update=reorder&amp;reorder=$up\"><img src=\"../images/up.png\" alt=\"$phrase[77]\"></a>";
							
							
							
							
							}
						
						
						
						if (($total > 0) && ($counter < $total) && ($paraordering == "c"))
							{
							
							//echo "<br>";
						
							
							
							
								foreach ($array_order as $index => $value)
									{
									//echo "$array_order[$index] <br>";
									if ($index == ($counter))
										{
										$down = $array_order;
										$temp = $down[$index];
										$down[$index] = $down[$index + 1];
										$down[$index + 1] = $temp;
										}
									}
								
								
							
							$down = implode(",",$down);
							echo "<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;reorder=$down&amp;update=reorder\"><img src=\"../images/down.gif\" alt=\"$phrase[78]\"></a>";
							}
						

						
						
													
							
						
							if (isset($darray_docid))
							{
							
							$count = count($darray_docid);	
							if ($count > 0 ) 
								{
								echo "<br><br><b>Documents</b><table cellspacing=\"10\"> ";
								}
							
							foreach ($darray_docid as $index => $doc_id)
							{
							if ($darray_content[$index] == $content_id)
									{
									//create hash so images canot be directly viewed
									$h = md5($darray_docid[$index]);
									echo "<tr><td><a href=\"../main/doc.php?m=$m&doc_id=$darray_docid[$index]\">$darray_docname[$index]</a></td><td><a href=\"editpage.php?m=$m&amp;event=keywords&doc_id=$doc_id&amp;page_id=$page_id&amp;content_id=$content_id\">$phrase[99]</a></td><td><a href=\"editpage.php?m=$m&amp;event=deletedoc&doc_id=$doc_id&amp;page_id=$page_id&amp;content_id=$content_id&amp;filename=$darray_linkname[$index]&amp;content_id=$content_id\">$phrase[651]</a></td></tr>";
									}
							}
							if ($count > 0) 
							{
							echo "</table><br>";
							
							}
							}
							

						
						
						
						?>
						
					

						<?php
							}
						
						}//ends for each
						}
						?><br><br>
						
						
						
						
						
						
<?php

if ($published <> 1) {echo "</div>";}

}

}
?>
