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
// 15 comment text
// 16 comment added
// 17 comment deleted
// 18 comment restored


if (isset($page_id))
	{

 
    
    		
		$months[1] = strftime("%b",mktime(01,01,01,1,01,2000)); 
		$months[2] = strftime("%b",mktime(01,01,01,2,01,2000)); 
		$months[3] = strftime("%b",mktime(01,01,01,3,01,2000)); 
		$months[4] = strftime("%b",mktime(01,01,01,4,01,2000)); 
		$months[5] = strftime("%b",mktime(01,01,01,5,01,2000)); 
		$months[6] = strftime("%b",mktime(01,01,01,6,01,2000)); 
		$months[7] = strftime("%b",mktime(01,01,01,7,01,2000)); 
		$months[8] = strftime("%b",mktime(01,01,01,8,01,2000)); 
		$months[9] = strftime("%b",mktime(01,01,01,9,01,2000)); 
		$months[10] = strftime("%b",mktime(01,01,01,10,01,2000)); 
		$months[11] = strftime("%b",mktime(01,01,01,11,01,2000)); 
		$months[12] = strftime("%b",mktime(01,01,01,12,01,2000));
		
$integers[] = "content_id";
$integers[] = "image_id";
$integers[] = "doc_id";
$integers[] = "imagealign";
$integers[] = "s";
$integers[] = "f";

 $now = time();

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

if (($access->thispage == 3) && isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletecomment")

{




$ip = ip("pc");	
deletecomment($content_id,$page_id,$ip,$DB);

 
}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "restoredoc")
{	

	restoredocument($doc_id,$content_id,$ip,$page_id,$DB);
	
	
}

if (isset($_GET["update"]) && $_GET["update"] == "delete")
{	

	deleteparagraph($content_id,$page_id,$ip,$now,$DB);
	
	
	
}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addwidget")
{	
	
	if (isset($_REQUEST["type"]))
	{

	$_name = $DB->escape($_REQUEST["name"]);
     
	$cmodule = $DB->escape($_REQUEST["cmodule"]);
	$bmodule = $DB->escape($_REQUEST["bmodule"]);
	$nmodule = $DB->escape($_REQUEST["nmodule"]);
	$_page = $DB->escape($_REQUEST["widgetpage"]);
	if (isset($_REQUEST["height"])) {	$_height = $DB->escape($_REQUEST["height"]);} else {$_height = 0;}
	
//	print_r($_REQUEST);
	
	if ($_REQUEST["type"] == "con") {	$_target = $_page;} 
	elseif ($_REQUEST["type"] == "roo") {$_target = "$bmodule";}
	elseif ($_REQUEST["type"] == "cal") {$_target = "$cmodule";}
	elseif ($_REQUEST["type"] == "not") {$_target = "$nmodule";}
	
	$sql = "insert into widgets values(NULL,'$m','$_target','c','module','$_height','$_name')";
	//echo $sql;
	$DB->query($sql,"menu.php");

	$_widgetid = $DB->last_insert();

	addparagraph('--WIDGET--' . $_name,$_widgetid,$page_id,$ip,$DB,'1','0');
	}
	
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








if (isset($_POST["update"]) && $_POST["update"] == "uploadimage")

{		
	
	upload($m,$page_id,$content_id,$PREFERENCES,$DB,"image",$ip,$phrase);
		
			

}

 






if (isset($_POST["update"]) && $_POST["update"] == "add paragraph")

{
	
$title = $DB->escape($_REQUEST["title"]);
$body = $DB->escape($_REQUEST["body"]);

if ($_REQUEST["expiry"] == "yes")
{ $expiry = mktime(0, 0, 0, $_REQUEST["month"], $_REQUEST["day"], $_REQUEST["year"]);}
else {$expiry = "0";}
    
	//print_r($_REQUEST);
	
addparagraph($title,$body,$page_id,$ip,$DB,$imagealign,$expiry);

 
}


if (isset($_REQUEST["update"]) && ($_REQUEST["update"] == "addcol" || $_REQUEST["update"] == "addbox"))

{
	$height = $DB->escape($_REQUEST["height"]);
		
if ($_REQUEST["update"] == "addcol")
{
$starttitle = $DB->escape("--COLUMNSTART--");
$endtitle = $DB->escape("--COLUMNEND--");
}
else {
$starttitle = $DB->escape("--BOXSTART--");
$endtitle = $DB->escape("--BOXEND--");
}


	$sql = "select max(page_order) as max from content where page_id = '$page_id'";
	//echo $sql;
	$DB->query($sql,"editcontent.php");
	$row = $DB->get();
	$max = $row["max"] + 1;
	
$sql = "insert into content values (NULL,'$starttitle','','$page_id','$max','$_SESSION[username]','$ip','$now',NULL,'0','0','$height','0')";	 
$DB->query($sql,"editcontent.php");

$start = $DB->last_insert();
$max++;
$sql = "insert into content values (NULL,'$endtitle','$start','$page_id','$max','$_SESSION[username]','$ip','$now',NULL,'0','0','$height','0')";	 
$DB->query($sql,"editcontent.php");

}



if (isset($_REQUEST["update"]) && ($_REQUEST["update"] == "removecol" || $_REQUEST["update"] == "removebox") )

{
$sql = "delete from content where content_id = '$s' and page_id = '$page_id'";
$DB->query($sql,"editcontent.php");

$sql = "delete from content where content_id = '$f' and page_id = '$page_id'";
$DB->query($sql,"editcontent.php");
}



if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "edit" )
	{

	$title = $DB->escape($_REQUEST["title"]);
	$body = $DB->escape($_REQUEST["body"]);	
        
        if ($_REQUEST["expiry"] == "yes")
{ $expiry = mktime(0, 0, 0, $_REQUEST["month"], $_REQUEST["day"], $_REQUEST["year"]);}
else {$expiry = "0";}

	$imagealign = $DB->escape($_REQUEST["imagealign"]);
	updateparagraph($content_id,$title,$body,$ip,$DB,$imagealign,$expiry);
	
		
		
		
	}

        
        
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updateheight" )
	{

	$height = $DB->escape($_REQUEST["height"]);
	$id = $DB->escape($_REQUEST["widget"]);	
        
        $sql = "update widgets set position = '$height' where id = '$id'";
	$DB->query($sql,"editcontent.php");
		
		
		
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

	
	restoreparagraph($content_id,$oldtitle,$page_id,$ip,$now,$DB);
	
	
	
		
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
		$index = $DB->escape($index);	
		$value = $DB->escape($value);
			
			
		$sql = "update content set page_order = \"$index\" WHERE content_id = \"$value\"";	
		$DB->query($sql,"editcontent.php"); 
		}
	
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
	<a href=\"editpage.php?m=$m&amp;update=deletedoc&amp;page_id=$page_id&amp;doc_id=$doc_id&amp;content_id=$content_id\">$phrase[12]</a> | <a href=\"editpage.php?m=$m&amp;page_id=$page_id\">$phrase[13]</a>";
	
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteimage")
{
	echo "<br><b>$phrase[14]</b><br><br> $_REQUEST[name]<br><br>
	<a href=\"editpage.php?m=$m&amp;update=deleteimage&amp;page_id=$page_id&image_id=$image_id&amp;content_id=$content_id\">$phrase[12]</a> | <a href=\"editpage.php?m=$m&amp;page_id=$page_id\">$phrase[13]</a>";
	
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletecomment")
{
	echo "<br><br><b>$phrase[14]</b><br><br> $_REQUEST[person]<br><br>
	<a href=\"editpage.php?m=$m&amp;update=deletecomment&amp;page_id=$page_id&content_id=$content_id&edit=yes\">$phrase[12]</a> | <a href=\"editpage.php?m=$m&amp;page_id=$page_id\">$phrase[13]</a>";
	
}


elseif (isset($_GET["event"]) && $_GET["event"] == "paragraphhistory")
	{
	
	$restoredoc = "yes";	
	$restoreimage = "yes";
		
	echo "<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;event=editpage\">
	<img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a>
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
	$imagealign = $row["imagealign"];
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
		
	$body = str_replace("\"", "'",$body);		
		
		
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
		<input type=\"hidden\" name=\"imagealign\" value=\"$imagealign\">
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
	
	echo "<a href=\"editpage.php?m=$m\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"  alt=\"$phrase[57]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;event=editpage&amp;page_id=$page_id\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a><br><br><h4>$phrase[781]</h4><br><table class=\"colourtable\" style=\"width:70%;padding:0.5em\">";
	

	
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
		echo "$phrase[793] <br><br><i><b>$titles[$archive]</b><br>$bodies[$archive]</i><br>
		";
	}

	if ($event == 16)
	{
		//comment added
		echo "$phrase[823] <br><br><i>$bodies[$archive]</i><br>
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
			echo "<br><form action=\"editpage.php\"method=\"post\">
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

elseif (isset($_GET["event"]) && $_GET["event"] == "image")
	{
	echo "
	<a href=\"editpage.php?m=$m\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"  alt=\"$phrase[57]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;page_id=$page_id\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a>

	
	<form action=\"editpage.php\" method=\"post\" enctype=\"multipart/form-data\" style=\"width:90%;margin-top:1em\">
	<fieldset><legend>$phrase[98]</legend><br>
						
						
						
						

						<p><input type=\"file\" name=\"upload[0]\" > <button onclick=\"addchooser();return false;\">$phrase[996]</button></p>
						<div id=\"fs\">
						
						</div>
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"update\" value=\"uploadimage\">
					
						<input type=\"submit\" name=\"submit\" value=\"$phrase[98]\" ></fieldset>
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
	
elseif (isset($_GET["event"]) && ($_GET["event"] == "addcol" || $_GET["event"] == "addbox"))
	{
		
		
		if ($_GET["event"] == "addbox")	{	echo "<h2>$phrase[1037]</h2>";}
		if ($_GET["event"] == "addcol")	{	echo "<h2>$phrase[1036]</h2>";}
		
		echo "<form action=\"editpage.php\">
		$phrase[1069] <input type=\"text\" name=\"height\" value=\"0\" style=\"text-align:right\">px <p>$phrase[1040]</p>
	
		<input type=\"hidden\" name=\"m\" value=\"$m\">
		<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">";
		if ($_GET["event"] == "addbox")	{	echo "<input type=\"hidden\" name=\"update\" value=\"addbox\">";}
		if ($_GET["event"] == "addcol")	{	echo "<input type=\"hidden\" name=\"update\" value=\"addcol\">";}
		
		echo "<br>
		<input type=\"submit\" name=\"submit\" value=\"";
		
		if ($_GET["event"] == "addbox")	{	echo "$phrase[1037]";}
		if ($_GET["event"] == "addcol")	{	echo "$phrase[1036]";}
		echo "\">
		</form>";
		
		
	
	}
elseif (isset($_GET["event"]) && $_GET["event"] == "doc")
	{
		
	
			
	echo "<a href=\"editpage.php?m=$m\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"  alt=\"$phrase[57]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;page_id=$page_id\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a><br>

	
	
	<form action=\"editpage.php\" method=\"post\" enctype=\"multipart/form-data\" style=\"width:90%;margin-top:1em\">

	<fieldset><legend>$phrase[80]</legend><br>
		<p><input type=\"file\" name=\"upload[0]\" > <button onclick=\"addchooser();return false;\">$phrase[996]</button></p>
						<div id=\"fs\">
						
						</div><br>
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
url = '../main/ajax_tags.php?m=$m&event=gettags';
document.getElementById('taglist').innerHTML = '<textarea id=\"TagListTextArea\" class=\"red\" cols=\"60\" rows=\"4\"></textarea>';
ajax(url,updateTagListTextArea);
document.getElementById('editlink').style.display = 'none';
document.getElementById('savelink').style.display = 'inline';
                    


}
                                
function savelist()
{
var tags = encodeURIComponent(document.getElementById('TagListTextArea').value)
 
url = '../main/ajax_tags.php?m=$m&event=updatetags&tags=' + tags;
updatePage(url,'taglist');                                

document.getElementById('taglist').style.display = 'block';
document.getElementById('editlink').style.display = 'inline';
document.getElementById('savelink').style.display = 'none';
                    
//window.tagdisplay = 'off'

}                                
                                
                                
                                
</script>
						<br><br>
						

						
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"update\" value=\"uploaddoc\">
						
						
						<input type=\"submit\" name=\"submit\" value=\"$phrase[80]\" ></fieldset>
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
elseif (isset($_GET["event"]) && $_GET["event"] == "delete")
	{
	
	echo "<br><b>$phrase[14]</b><br><br> $_REQUEST[title]<br><br>
	<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;content_id=$content_id&amp;update=delete\">$phrase[12]</a> | <a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;content_id=$content_id\">$phrase[13]</a>";
	
	}

elseif (isset($_GET["event"]) && $_GET["event"] == "editparagraph")

{
	
	echo "<a href=\"editpage.php?m=$m\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"  alt=\"$phrase[57]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;page_id=$page_id\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a>";
	
	if ($input == 0)
	{
	echo "&nbsp;&nbsp;&nbsp;

	<script type=\"text/javascript\">
 function pop_window(url) {
  var popit = window.open(url,'','status,resizable,scrollbars,width=400,height=500');
 }
	</script><a href=\"javascript:pop_window('../main/tags.php')\" ><img src=\"../images/help.png\" title=\"$phrase[337]\"  alt=\"$phrase[337]\"></a><br><br>
	
	";
	}
	
	
	$content_id = $_GET["content_id"];
	$sql = "SELECT title, body, page_title,imagealign,expiry FROM content, page where page.page_id = content.page_id and content_id = \"$content_id\"";
	$DB->query($sql,"editcontent.php"); 
	$row = $DB->get();
	$title = $row["title"];	
	$body = $row["body"];	
	$page_title = $row["page_title"];		
	$image_align = $row["imagealign"];
        $expiry = $row["expiry"];
		
	
			
		
		echo "<br><br>	<form method=\"POST\" action=\"editpage.php\" style=\"float:left\"><fieldset><legend>$phrase[81]</legend><br>
	
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
						
						
						
						echo "</select><br><br><b>$phrase[75]</b><br>
					<textarea name=\"body\" id=\"body\" class=\"ckeditor\" cols=\"60\" rows=\"20\" style=\"float:left\">$body</textarea><br><br>
                                                <p style=\"float:left\">
                                                <b>$phrase[1080] </b><input type=\"radio\" name=\"expiry\" value=\"no\" ";
                                                if ($expiry == "0") {echo " checked";}
                                                echo " onclick=\"displaynone('date')\"> No <input type=\"radio\" name=\"expiry\" value=\"yes\" onclick=\"displayinline('date')\"";
                                                if ($expiry > 0) {echo " checked";
                                                $_y = date("Y",$expiry);
                                                $_d = date("j",$expiry);
                                                $_m = date("n",$expiry);
                                                
                                                } else {$_y = 0;$_m= 0; $_d=0;}
                                                echo "> Yes
                                               <span id=\"date\"";
                                                if ($expiry == 0) { echo " style=\"display:none\"";}
                                                echo "> <select name=\"year\">";
                                                $year= date("Y");
                                                $counter = 0;
                                                while ($counter < 10)
                                                {
                                                  echo "<option value=\"$year\"";  if ($year == $_y) { echo "selected"; } echo ">$year</option>
";
                                                  $year++;  
                                                 $counter++;   
                                                }
                                                echo "</select>
                                                    

<select name=\"month\">";
			$i = 1;
			while ($i < 13)
			{
				echo "<option value=\"$i\"";
				if ($i == $_m ) {echo " selected";}
				echo ">$months[$i]</option>";
			$i++;
			}
			
			$previous = $year - 1;
			$next = $year + 1;
			echo "</select>

<select name=\"day\">";
			$i = 1;
			while ($i < 32)
			{
			echo "<option value=\"$i\"";
			if ($i == $_d) { echo " selected";}
			echo ">$i</option>";	
			$i++;	
			}
			
			echo "</select></span>
                            </p>
                             <p style=\"clear:both\"></p>
                                          <br>
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"update\" value=\"edit\">
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
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "keywords")
{
	
		 $sql = "select * from documents where  documents.m = \"$m\" and doc_id = \"$doc_id\"";
	
$DB->query($sql,"filesview.php");
	$row = $DB->get();
        $tags = $row["keywords"];
	echo "
	<a href=\"editpage.php?m=$m\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"  alt=\"$phrase[57]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;page_id=$page_id\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a>&nbsp;&nbsp;&nbsp;
	<br><br><form action=\"editpage.php\" method=\"post\" style=\"width:90%\"><fieldset><legend>$phrase[1081]</legend>
						
					<br>
						
						<b>$phrase[317]</b> $row[doc_name]<br><br>
					
<br>
						<b>$phrase[1081]</b><br>
			
			<textarea name=\"metadata\" id=\"tags\" cols=\"60\" rows=\"2\">$tags</textarea>
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
url = '../main/ajax_tags.php?m=$m&event=gettags';
document.getElementById('taglist').innerHTML = '<textarea id=\"TagListTextArea\" class=\"red\" cols=\"60\" rows=\"4\"></textarea>';
ajax(url,updateTagListTextArea);
document.getElementById('editlink').style.display = 'none';
document.getElementById('savelink').style.display = 'inline';
                    


}
                                
function savelist()
{
var tags = encodeURIComponent(document.getElementById('TagListTextArea').value)
   
url = '../main/ajax_tags.php?m=$m&event=updatetags&tags=' + tags;
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
						
						<input type=\"hidden\" name=\"update\" value=\"updatedoc\">
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\" >
						</fieldset></form>";
}



elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addwidget")

{
	

		$sql = "select * from modules";
	$DB->query($sql,"editcontent.php"); 

			while (	$row = $DB->get()) 
						{
						
						$__m[] = $row["m"];
						$__name[] = $row["name"];
						$__type[] = $row["type"];

						}
	
	
	
	
	echo "<h4>Add widget</h4>
	<form method=\"post\" action=\"editpage.php\">
	Widget Name
	<input type=\"text\" name=\"name\">
	<p ><input type=\"radio\" name=\"type\" value=\"cal\" onclick=\"showcal()\"> $phrase[427] <select name=\"cmodule\" id=\"cal\" style=\"display:none\">";
	foreach ($__m as $key => $value)
	{
		if ($__type[$key] == "c") {echo "<option value=\"$value\">$__name[$key]</option>";}
		
	}
	echo "</select></p>
	<p ><input type=\"radio\" name=\"type\" value=\"con\" onclick=\"showcon()\"> $phrase[95] <span id=\"con\" style=\"display:none\"> <select name=\"widgetpage\"  >";
		$sql = "select page_id, page_title,page.m from page,modules where page.m = modules.m and modules.type = 'p' and deleted = 0";
	$DB->query($sql,"editcontent.php"); 

			while (	$row = $DB->get()) 
						{
						
						$_page_id = $row["page_id"];
						$_m = $row["m"];
						$page_title = $row["page_title"];
						echo "<option value=\"$_m-$_page_id\">$page_title</option>
";
						}
	echo "</select> $phrase[1069] <input type=\"text\" name=\"height\" value=\"20\" size=\"3\">em</span></p>
	<p ><input type=\"radio\" name=\"type\" value=\"roo\" onclick=\"showroo()\"> $phrase[547] <select name=\"bmodule\" id=\"roo\" style=\"display:none\">";
	foreach ($__m as $key => $value)
	{
		if ($__type[$key] == "b") {echo "<option value=\"$value\">$__name[$key]</option>";}
		
	}
	echo "</select></p>
	<p ><input type=\"radio\" name=\"type\" value=\"not\" onclick=\"shownot()\"> $phrase[97] <select name=\"nmodule\" id=\"not\" style=\"display:none\">";
	foreach ($__m as $key => $value)
	{
		if ($__type[$key] == "n") {echo "<option value=\"$value\">$__name[$key]</option>";}
		
	}
	echo "</select></p><br>
	

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
	<input type=\"hidden\" name=\"update\" value=\"addwidget\"><br><br>
	<input type=\"submit\" name=\"submit\" value=\"$phrase[176]\">
	</form>
		<script type=\"text/javascript\">
		
		function showcon () {
		var con = document.getElementById(\"con\");
		con.style.display = 'inline';
		var cal = document.getElementById(\"cal\");
		cal.style.display = 'none';
		var roo = document.getElementById(\"roo\");
		roo.style.display = 'none';
		var not = document.getElementById(\"not\")
		not.style.display = 'none';
		}
		
		
			function showroo () {
		var con = document.getElementById(\"con\");
		con.style.display = 'none';
		var cal = document.getElementById(\"cal\");
		cal.style.display = 'none';
		var roo = document.getElementById(\"roo\");
		roo.style.display = 'inline';
		var not = document.getElementById(\"not\")
		not.style.display = 'none';
		}
		
			function shownot () {
		var con = document.getElementById(\"con\");
		con.style.display = 'none';
		var cal = document.getElementById(\"cal\");
		cal.style.display = 'none';
		var roo = document.getElementById(\"roo\");
		roo.style.display = 'none';
		var not = document.getElementById(\"not\")
		not.style.display = 'inline';
		}
		
			function showcal () {
		var con = document.getElementById(\"con\");
		con.style.display = 'none';
		var cal = document.getElementById(\"cal\");
		cal.style.display = 'inline';
		var roo = document.getElementById(\"roo\");
		roo.style.display = 'none';
		var not = document.getElementById(\"not\")
		not.style.display = 'none';
		}
		
		
		</script>
	
	
	";
	
	
}



elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editheight")

{
    	$id = $DB->escape($_REQUEST["widget"]);
    $sql = "select * from widgets where id = '$id'";
   $DB->query($sql,"editcontent.php");
    $row = $DB->get(); 
$height = $row["position"];
    
    echo "<h4>$phrase[1087]</h4>
<form action=\"editpage.php\">
Height <input type=\"text\" name=\"height\" value=\"$height\" size=\"4\">
<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"widget\" value=\"$id\">
<input type=\"hidden\" name=\"update\" value=\"updateheight\"><br><br>
<input type=\"submit\" name=\"submit\" value=\"$phrase[16]\">
</form>
       
";
    
    
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addparagraph")

{
	echo "<a href=\"editpage.php?m=$m\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"  alt=\"$phrase[57]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;page_id=$page_id\"><img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\"></a>&nbsp;&nbsp;&nbsp;";
	if (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "editparagraph" || $_REQUEST["event"] == "addparagraph") && $input == 0)
	{
		echo "<script type=\"text/javascript\">
 function pop_window(url) {
  var popit = window.open(url,'console','status,resizable,scrollbars,width=400,height=500');
 }
	</script><a href=\"javascript:pop_window('../main/tags.php')\" ><img src=\"../images/help.png\" title=\"$phrase[337]\"  alt=\"$phrase[337]\"></a><br><br>
	
	";
	}
	
		echo "<br><br><form method=\"POST\" action=\"editpage.php\" style=\"float:left\"><fieldset ><legend>$phrase[73]</legend>
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
						<textarea name=\"body\" id=\"body\" class=\"ckeditor\" cols=\"50\" rows=\"15\" style=\"float:left\"></textarea>
					
                                    
                                                               <b>$phrase[1080]</b> <input type=\"radio\" name=\"expiry\" value=\"no\" checked onclick=\"displaynone('date')\"> No <input type=\"radio\" name=\"expiry\" value=\"yes\" onclick=\"displayinline('date')\"> Yes
                                  
                                        
                                        
                                        <span id=\"date\" style=\"display:none\">            <select name=\"year\">";
                                                $year= date("Y");
                                                $counter = 0;
                                                while ($counter < 10)
                                                {
                                                  echo "<option value=\"$year\">$year</option>
";
                                                  $year++;  
                                                 $counter++;   
                                                }
                                                echo "</select>
                                                    

<select name=\"month\">";
			$i = 1;
			while ($i < 13)
			{
				echo "<option value=\"$i\">$months[$i]</option>";
			$i++;
			}
			
			$previous = $year - 1;
			$next = $year + 1;
			echo "</select>

<select name=\"day\">";
			$i = 1;
			while ($i < 32)
			{
			echo "<option value=\"$i\">$i</option>";	
			$i++;	
			}
			
			echo "</select></span>
                                        
                                        
                                        <br><br>
						<input type=\"submit\" value=\"$phrase[73]\" name=\"submit\">
							<input type=\"hidden\" name=\"page_id\" value=\"$page_id\"><br>
								<input type=\"hidden\" name=\"update\" value=\"add paragraph\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">  
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
else 
{
	echo "";
	
		$sql = "select image_id, content.content_id as content_id, images.name  as name from images, content, page where content.page_id = page.page_id and content.content_id = images.content_id and content.page_id = \"$page_id\" and images.deleted = '0'";
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
	
	$sql = "select doc_id, size, content.content_id as content_id, doc_name from documents, content, page where content.page_id = page.page_id and content.content_id = documents.content_id and page.page_id = \"$page_id\" and documents.deleted = '0' order by doc_name";
	$DB->query($sql,"editcontent.php"); 
	$i = 0;	
			while (	$row = $DB->get()) 
						{
						
						$darray_content[$i] = $row["content_id"];
						$darray_docname[$i] = htmlspecialchars($row["doc_name"]);
						$darray_linkname[$i] = urlencode($row["doc_name"]);
						$darray_docid[$i] = $row["doc_id"];
						$darray_size[$i] = $row["size"];
						
						$i++;
						}
	

	
	




$page_title = urlencode($page_title);



	
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
	$insert = "order by page_order asc, content_id asc";
	}
else { $insert = "";}
	
	
	
	//display page contents
		$sql = "SELECT * FROM content where page_id = '$page_id' and deleted = '0' $insert";
		
		// echo $sql;
				
					
					$DB->query($sql,"editcontent.php");

					
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
					
							$array_body[$counter]  = $row["body"];
						$array_imagealign[$counter] = $row["imagealign"];
						$array_updatedby[$counter]  = formattext($row["updated_by"]);
						$array_updatedwhen[$counter]= date("g:ia j",$row["updated_when"]) . strftime(" %B %Y",$row["updated_when"]);
						$array_ip[$counter]  = $row["updated_ip"];
                                                $array_expiry[$counter]  = $row["expiry"];
					
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
						
					if (isset($array_order)){	$array_order= array_merge($array_order);}
	
	
						
echo "<a href=\"editpage.php?m=$m\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"  alt=\"$phrase[57]\"></a>";	

if ($pagechange > 0) { echo "&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;event=pagehistory&amp;title=$page_title\"><img src=\"../images/clock.png\" title=\"$phrase[781]\"  alt=\"$phrase[781]\"></a>";}	


	if ($paraordering == "c")
	{ echo "
	&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;event=addcol\"><img src=\"../images/columns.png\" title=\"$phrase[1036]\"  alt=\"$phrase[1036]\"></a>
	
	&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;event=addbox\"><img src=\"../images/box.png\" title=\"$phrase[1037]\"  alt=\"$phrase[1037]\"></a>
	
	
	";}
	
	echo "&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;event=addwidget\"><img src=\"../images/process_add.png\" title=\"$phrase[1035]\"  alt=\"$phrase[1035]\"></a>
	&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;event=addparagraph\"><img src=\"../images/paragraph_add.png\" title=\"$phrase[73]\"  alt=\"$phrase[73]\"></a>";
					

	echo "<p style=\"clear:both\"></p>";
						
					$counter = 0;
					if (isset($array_content_id))
					{
					foreach ($array_content_id as $index => $content_id)
						{
							if ($array_event[$index] == 0 && $array_deleted[$index] == 0)
						{
						
						
					
							
						if (substr($array_title[$index],0,15) == "--COLUMNSTART--")
						{
							echo "<div class=\"colstart\" ";
							if ($array_imagealign[$index] != "0") {echo "style=\"height:$array_imagealign[$index]px;overflow:auto\"";}
							echo ">";
							$counter++;
						
						}
						elseif (substr($array_title[$index],0,15) == "--COLUMNEND--")
						{
							
							$startindex = $array_body[$index];
							
							echo "<br><a href=\"editpage.php?m=$m&amp;update=removecol&amp;event=editpage&amp;page_id=$page_id&amp;s=$startindex&amp;f=$content_id\"><img src=\"../images/columnremove.png\" title=\"$phrase[1039]\" alt=\"$phrase[1039]\"></a></div>";
							$counter++;
							
						}
						
						elseif (substr($array_title[$index],0,12) == "--BOXSTART--")
						{
							echo "<div class=\"boxstart\" ";
							if ($array_imagealign[$index] != "0") {echo "style=\"height:$array_imagealign[$index]px;overflow:auto\"";}
							echo ">";
							$counter++;
						
						}
						elseif (substr($array_title[$index],0,10) == "--BOXEND--")
						{
							
							$startindex = $array_body[$index];
							
							echo "<br><a href=\"editpage.php?m=$m&amp;update=removecol&amp;event=editpage&amp;page_id=$page_id&amp;s=$startindex&amp;f=$content_id\"><img src=\"../images/boxremove.png\" title=\"$phrase[1038]\" alt=\"$phrase[1038]\"></a></div>";
							$counter++;
							
						}
						
						
						
						else {
							
						$linktitle = urlencode($array_title[$index]);
						
						
						
						if ($array_imagealign[$index] == 3) {$class = "contentcenter";} 
						elseif ($array_imagealign[$index] == 2) {$class = "contentright";}  
						elseif ($array_imagealign[$index] == 4) {$class = "contentfloatleft";}  
						elseif ($array_imagealign[$index] == 5) {$class = "contentfloatright";}  
						else {$class = "contentleft";} 
						
						
						
						
						
						 echo "<div class=\"$class\" style=\"clear:both\">";
						
							if (substr($array_title[$index],0,10) == "--WIDGET--")
						{
							
							
							$t = time();
							if (isset($content_widgets_id))
								{
								$match = 0;
								foreach ($content_widgets_id as $key => $_widgetid)
								{
									
								//echo "wwww $_widgetid $array_body[$index]<br>";	
									
								if ($_widgetid == $array_body[$index])
									{
								echo "<div style=\"margin-top:2em;clear:both\">";
									$_widgets[] = new Widget($content_widgets_target[$key],$DB,$access,$t,$content_widgets_height[$key]);	
								echo "</div>";
                                                               if ($content_widgets_target_type[$key] == "p") {echo "<br><a href=\"editpage.php?m=$m&event=editheight&widget=$_widgetid&page_id=$page_id\">$phrase[1087]</a>";} 
                                                                
								$match = 1;
									break;
									}
								}
								if ($match == "0") {echo "<p style=\"font-size:2em;margin-top2em\">$phrase[1024]</p>";}
							
								}
						
							
						}
	
						else {
						 if ($array_title[$index] != "")
						 {
                                                    
                                                 
						 echo "<h3 id=\"c_$content_id\" style=\"text-align:left\"";
                                                 if ($array_expiry[$index]!= 0 && $array_expiry[$index] < $now) {echo " class=\"grey\"";}
                                                 echo ">$array_title[$index]</h3>";
						 }
						 
							
						 
					
						
						if ($displaydate == 1 || $access->thispage == 3)
						{
						if ($array_title[$index] == "") {echo "<br><br>";}
							
							echo "<div class=\"primary\" style=\"text-align:left;padding:0 0 0.5em 0\">$array_updatedwhen[$index]";
							
							if ($access->thispage == 3)  {echo "<span class=\"leftspan\">$array_updatedby[$index]</span><span class=\"leftspan\"> $array_ip[$index]</span>";} 
							echo "</div>";
						}
						
						}	


									if (isset($iarray_imageid))
									{
									foreach ($iarray_imageid as $i => $image_id)
										{
										
									
										if ($iarray_content[$i] == $content_id)
												{
												$name = urlencode($iarray_name[$i]);
												
												$insert = "";
												if ($array_imagealign[$index] == 4) {$insert = "margin:0 1.5em 1em 0;float:left";} 
												elseif ($array_imagealign[$index] == 5) {$insert = "padding:0 0 1em 1em;float:right";} 
												else { $insert = "margin:2em 0";}
												
												
												echo "<img src=\"../main/image.php?m=$m&image_id=$image_id\" style=\"vertical-align:middle;$insert\">  <a href=editpage.php?event=deleteimage&amp;name=$name&amp;image_id=$image_id&amp;page_id=$page_id&amp;m=$m&content_id=$content_id>
												<img src=\"../images/cross.png\" title=\"$phrase[590]\"></a><br>";
												}
										
										}
									}
						
					
							if($input != 0)
						{
				
						$body  = formattext_html($array_body[$index]);
						}
						else {
							$body = formattext($array_body[$index]);
						}
						
						
							
						$insert = "";
						if ($array_imagealign[$index] == 2 || $array_imagealign[$index] == 3) {$insert = "text-align:left";} 
						
						echo "<div style=\"$insert\"";
                                                  if ($array_expiry[$index]!= 0 && $array_expiry[$index] < $now) {echo " class=\"grey\"";}
                                                echo ">";
						
						
						if (substr($array_title[$index],0,15) != "--COLUMNEND--" && substr($array_title[$index],0,15) != "--COLUMNSTART--" && substr($array_title[$index],0,10) != "--WIDGET--") {echo $body;}
						if ($array_expiry[$index]!= 0)
                                                {     
                                                $year = date("Y",$array_expiry[$index]);
                                                $month = date("m",$array_expiry[$index]);
                                                $day = date("d",$array_expiry[$index]);
                                                $date = strftime("%x",$array_expiry[$index]);
                                                
						echo "<br>$phrase[1080] $date";
                                                }
						echo "</div>
				<div style=\"text-align:left;padding-top:1em;clear:both\">";
						
						if (($total > 0) && ($counter > 0) && ($paraordering == "c"))
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
							echo "<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;update=reorder&amp;reorder=$up\"><img src=\"../images/up.png\" title=\"$phrase[77]\"  alt=\"$phrase[77]\"></a>&nbsp;&nbsp;&nbsp;";
							
							
							
							
							}
						
						
						
						if (($total > 0) && ($counter < $total - 1) && ($paraordering == "c"))
							{
							
							//echo "<br>";
						
							
							
								foreach ($array_order as $ind => $value)
									{
									
									if ($ind == ($counter))
										{
										$down = $array_order;
										$temp = $down[$ind];
										$down[$ind] = $down[$ind + 1];
										$down[$ind + 1] = $temp;
										}
									}
								
								
							
							$down = implode(",",$down);
							echo "<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;reorder=$down&amp;update=reorder\"><img src=\"../images/down.png\" title=\"$phrase[78]\"  alt=\"$phrase[78]\"></a>&nbsp;&nbsp;&nbsp;";
							}
						
	if (substr($array_title[$index],0,10) != "--WIDGET--" )
							{
						
						
						echo "<a href=\"editpage.php?m=$m&amp;event=doc&amp;content_id=$content_id&amp;page_id=$page_id\"><img src=\"../images/attach.png\" title=\"$phrase[80]\"  alt=\"$phrase[80]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;content_id=$content_id&amp;event=image&amp;page_id=$page_id\"><img src=\"../images/picture_add.png\" title=\"$phrase[79]\"  alt=\"$phrase[79]\"></a>
						&nbsp;&nbsp;&nbsp;<a href=\"editpage.php?m=$m&amp;event=editparagraph&amp;content_id=$content_id&amp;page_id=$page_id\"><img src=\"../images/page_white_edit.png\" title=\"$phrase[81]\"  alt=\"$phrase[81]\"></a>	&nbsp;&nbsp;&nbsp;";
						
						if (isset($archive) && in_array($content_id,$archive)) {
							echo "<a href=\"editpage.php?m=$m&amp;event=paragraphhistory&amp;content_id=$content_id&amp;page_id=$page_id&amp;title=$linktitle\"><img src=\"../images/clock.png\" title=\"$phrase[779]\"  alt=\"$phrase[779]\"></a>&nbsp;&nbsp;&nbsp;";}
							
							}
							
						echo "<a href=\"editpage.php?m=$m&amp;content_id=$content_id&amp;event=delete&amp;title=$linktitle&amp;&amp;page_id=$page_id&amp;\"><img src=\"../images/page_remove.png\" title=\"$phrase[650]\"  alt=\"$phrase[650]\"></a>";
						
						
							echo "</div>";				
							
						
							if (isset($darray_docid))
							{
						
							if (in_array($content_id,$darray_content) ) 
								{
								echo "<table> ";
								}
							
							foreach ($darray_docid as $index => $doc_id)
							{
							if ($darray_content[$index] == $content_id)
									{
									//create hash so images canot be directly viewed
									$h = md5($darray_docid[$index]);
									$size = round($darray_size[$index] / 1000) . " Kb";
									echo "<tr><td><a href=\"../main/doc.php?m=$m&amp;doc_id=$darray_docid[$index]\">$darray_docname[$index]</a>&nbsp; $size</td><td><a href=\"editpage.php?m=$m&amp;event=keywords&amp;doc_id=$doc_id&amp;page_id=$page_id&amp;content_id=$content_id\"  style=\"padding-left:1em\"><img src=\"../images/letter.png\" title=\"$phrase[99]\"  alt=\"$phrase[99]\"></a></td><td><a href=\"editpage.php?m=$m&amp;event=deletedoc&amp;doc_id=$doc_id&amp;page_id=$page_id&amp;content_id=$content_id&amp;filename=$darray_linkname[$index]&amp;content_id=$content_id\"  style=\"padding-left:1em\"><img src=\"../images/cross.png\" title=\"$phrase[651]\"  alt=\"$phrase[651]\"></a></td></tr>";
									}
							}
								if (in_array($content_id,$darray_content) ) 
							{
							echo "</table><br>";
							
							}
							}
							

						
						$counter++;
						
						
					
						
						echo "</div>";
							}
							}
						
						}//ends for each
						}
						if ($total == 0) 
						{
							echo "<p style=\"color:Red\">$phrase[843]</p><p>$phrase[844]<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;event=addparagraph\" ><img src=\"../images/paragraph_add.png\" title=\"$phrase[73]\"  alt=\"$phrase[73]\"></a></p>";	
							
						}
						
							echo "<div style=\"clear:both;float:none\">";
						
						
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
	
		if ($access->thispage > 1 || $counter > 0)
		{
		echo "<h3>$phrase[135] <img src=\"../images/comments.png\"  title=\"$phrase[135]\"  alt=\"$phrase[135]\"></h3>";
		//if ($access->thispage > 1) 
			//{
		//	echo "<a href=\"page.php?m=$m&amp;page_id=$page_id&amp;event=addcomment\"><img src=\"../images/comment_add.png\" title=\"$phrase[822]\"></a>";	
			//}
		
			if (isset($_content_id))
					{
						foreach ($_content_id as $index => $id)
							{

						
						echo "<br><br><span style=\"color:black\"><b>$_updated_by[$index]</b></span> <br><span class=\"grey\">$_updated_when[$index]</span><br>
						$_body[$index]<br>";
						if ($access->thispage == 3)
						{
							echo "<a href=\"editpage.php?m=$m&amp;page_id=$page_id&amp;event=editpage&amp;event=deletecomment&content_id=$id&person=$link_updated_by[$index]\"><img src=\"../images/comment_delete.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>	";
						}
							}
						}
		
		
		}
	 echo "</div>";

if ($published <> 1) {echo "</div>";}





}

}
?>
