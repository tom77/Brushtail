<?php




include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$now = time();
$ip = ip("pc");

$integers[] = "content_id";
$integers[] = "image_id";
$integers[] = "page_id";
$integers[] = "imagealign";
$integers[] = "scroll";


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
		
$sql = "select * from modules where m = '$m'";
$DB->query($sql,"noticeboard.php");
$row = $DB->get();
$modname = formattext($row["name"]);
$input = $row["input"];

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
			page_view($DB,$PREFERENCES,$m,"");


		
			 
	  	include("../includes/leftsidebar.php");   
		
		
	 
		
			

	  	echo "<div id=\"content\"><div>";
	





echo " <h1 >$modname</h1>  ";



	$sql = "select page_id from page where m = '$m'";
$DB->query($sql,"noticeboard.php");
$row = $DB->get();
$page_id = $row["page_id"];



		
if (isset($_POST["update"]) && $_POST["update"] == "update" && ($access->thispage > 1))
	{
	
	$title = $DB->escape($_REQUEST["title"]);
	$body = $DB->escape($_REQUEST["body"]);	
	//$page_title = $DB->escape($page_title);
	
	updateparagraph($content_id,$title,$body,$ip,$DB,$imagealign);
	
	}		
		
if (isset($_POST["update"]) && $_POST["update"] == "add" && ($access->thispage > 1))
	{
	//$page_title = $DB->escape($page_title);	
	$title = $DB->escape($_POST["title"]);
	$body = $DB->escape($_POST["body"]);
	

	addparagraph($title,$body,$page_id,$ip,$DB,$imagealign,'0');
	}

	
	
	if (isset($_GET["update"]) && $_GET["update"] == "deleteimage")

{


		  
	deleteimage($now,$ip,$image_id,$DB,$PREFERENCES,$m);

	
}
	
	
		if (isset($_GET["update"]) && $_GET["update"] == "deleteparagraph" && ($access->thispage > 1))
{	
	
		deleteparagraph($content_id,$page_id,$ip,$now,$DB);
	
}
		



if (isset($_POST["update"]) && $_POST["update"] == "uploadimage" && ($access->thispage > 1))





{		
	
		upload($m,$page_id,$content_id,$PREFERENCES,$DB,"image",$ip,$phrase);
}




	if (isset($_REQUEST["event"]))
	{
			echo "<a href=\"noticeboard.php?m=$m&page_id=$page_id\"><img src=\"../images/page.png\" title=\"$phrase[827]\" alt=\"$phrase[827]\"></a>&nbsp;&nbsp;&nbsp;";
	}


	
	
	if (isset($ERROR))	
	{
		
		
	}
		


elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteimage")
{
	echo "<br><b>$phrase[14]</b><br><br> $_REQUEST[name]<br><br>
	<a href=\"noticeboard.php?m=$m&amp;update=deleteimage&amp;page_id=$page_id&amp;image_id=$image_id&amp;content_id=$content_id\">$phrase[12]</a> | <a href=\"noticeboard.php?m=$m&page_id=$page_id\">$phrase[13]</a>";
	
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteparagraph")
{
	echo "<br><br><b>$phrase[14]</b><br><br> $_REQUEST[title]<br><br>
	<a href=\"noticeboard.php?m=$m&amp;update=deleteparagraph&amp;page_id=$page_id&content_id=$content_id\">$phrase[12]</a> | <a href=\"noticeboard.php?m=$m&page_id=$page_id\">$phrase[13]</a>";
	
}
	
		
		
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "post" && ($access->thispage > 1))
	{
		
		echo "
	
	<form method=\"POST\" action=\"noticeboard.php\" style=\"width:90%\"><fieldset><legend>$phrase[592]</legend><br>
	
						<b>$phrase[67]</b><br>
						<input type=\"text\" name=\"title\"  size=\"50\" maxlength=\"100\">
						<br><br>
					 <b>$phrase[1022]</b><br><select name=\"imagealign\">";
	
					if (isset($align_label))
						{
					foreach ($align_label as $ii => $label)
							{
							echo "<option value=\"$ii\">$label</option>";
							}
					}
	
	
	echo "</select><br><br>
						
						<b>$phrase[68]</b><br>
						<textarea name=\"body\" id=\"body\" class=\"ckeditor\" cols=\"50\" rows=\"15\"></textarea><br><br>
						
						<input type=\"submit\" value=\"$phrase[176]\" name=\"submit\">
							<input type=\"hidden\" name=\"page_id\" value=\"$page_id\"><br>
								<input type=\"hidden\" name=\"update\" value=\"add\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
		
		
		</fieldset></form>
	
	";
		
		
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
	
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit" && ($access->thispage > 1))
	{
		
		
	$sql = "SELECT * FROM content where content_id = \"$content_id\"";
	$DB->query($sql,"noticeboard.php");
	$row = $DB->get(); 
	
						
						$content_id = $row["content_id"];
						$title = $row["title"];
						$body = $row["body"];
						$image_align = $row["imagealign"];
						$title = $title;
						$body = $body;
						
		echo "		
		<form method=\"POST\" action=\"noticeboard.php\" style=\"width:90%\"><fieldset><legend>$phrase[591]</legend><br>
	
						<b>$phrase[67]</b><br>
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
						
						
						
						echo "</select>		<br><br>
						
						<b>$phrase[68]</b><br>
						<textarea name=\"body\" id=\"body\" class=\"ckeditor\" cols=\"60\" rows=\"15\">$body</textarea><br><br>
						<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
						<input type=\"hidden\" name=\"update\" value=\"update\">
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
	
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delete" && ($access->thispage > 1))
	{
		echo "<br><b>$phrase[14]</b><br><br>
	<a href=\"noticeboard.php?m=$m&amp;content_id=$content_id&amp;page_id=$page_id&amp;update=delete\">$phrase[12]</a> | <a href=\"noticeboard.php?m=$m&amp;content_id=$content_id&amp;page_id=$page_id\">$phrase[13]</a>";

	
	}
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "image" && ($access->thispage > 1))
	{
			echo "
				<form action=\"noticeboard.php\" method=\"post\" enctype=\"multipart/form-data\" style=\"width:80%\">
						
				<fieldset><legend>$phrase[98]</legend>		
						
						<br>

						<p><input type=\"file\" name=\"upload[0]\" > <button onclick=\"addchooser();return false;\">$phrase[996]</button></p>
						<div id=\"fs\">
						
						</div>
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
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
	else 
	{
		
	
	// find images associated with this page and put into array
	
	$sql = "select image_id, content.content_id as content_id, images.name as name from images, content, page where content.page_id = page.page_id and content.content_id = images.content_id and page.page_id = \"$page_id\" and images.deleted = '0'";
	$DB->query($sql,"pagecontent.php");
	//echo "$sql";
	$i = 0;	
		while ($row = $DB->get()) 
						{
						
						$iarray_content[$i] = $row["content_id"];
						$iarray_name[$i] = $row["name"];
						$iarray_imageid[$i] = $row["image_id"];
						$i++;
						}
	
	
	
	
	// find documents associated with this page and put into array
	
	$sql = "select doc_id, content.content_id as content_id, doc_name from documents, content, page where content.page_id = page.page_id and content.content_id = documents.content_id and page.page_id = \"$page_id\" and documents.deleted = '0' order by doc_name";
	$DB->query($sql,"pagecontent.php");
	$i = 0;	
		while ($row = $DB->get()) 
						{
						
						$darray_content[$i] = $row["content_id"];
						$darray_docname[$i] = $row["doc_name"];
						$darray_docid[$i] = $row["doc_id"];
						
						$i++;
						}
	
	
	
		if ($access->thispage > 1)
		{
			
		echo "<a href=\"noticeboard.php?m=$m&amp;event=post&amp;page_id=$page_id\"><img src=\"../images/comment_add.png\" title=\"$phrase[592]\"  alt=\"$phrase[592]\"></a>";	
		}
		

	
	
	$sql = "SELECT * FROM content where page_id = \"$page_id\" and archive is null and deleted = '0' order by content_id desc";
	$DB->query($sql,"noticeboard.php");
	//echo $sql;
				//echo $sql;
					while ($row = $DB->get()) 
						{
						
						$content_id = $row["content_id"];
						$title = $row["title"];
						$linktitle = urlencode($title);
						$body = $row["body"];
						$title = formattext($title);
						if ($input != 0)
						{
						$body = formattext_html($body);
						
						}
						else 
						{
						$body = formattext($body);	
						}
					
					
						$updated_when= date("g:ia j",$row["updated_when"]) . strftime(" %B %Y",$row["updated_when"]);
						$updated_by = $row["updated_by"];
						$updated_ip = $row["updated_ip"];
						 
					
						
									
						echo "<h3 id=\"c_$content_id\">$title</h3>";
						
						
							echo "<span class=\"primary\">$updated_when";
							
							if ($access->thispage == 3)  {echo "<span class=\"leftspan\">$updated_by</span><span class=\"leftspan\"> $updated_ip</span>";} 
							echo "</span><br><br>";
					
						
						
												
						//display images
						
						if (isset($iarray_imageid))
					{
						foreach ($iarray_imageid as $index => $image_id)
							{
						$linkname = urlencode($iarray_name[$index]);
						
							if ($iarray_content[$index] == $content_id)
									{
								
									
									echo "<img src=\"../main/image.php?m=$m&amp;image_id=$image_id\" style=\"margin-bottom:20px;vertical-align:middle\" alt=\"uploaded image\">";

						if ($access->thispage == 3 || ($access->thispage == 2 && $_SESSION["username"] == $updated_by))  
								{									
								echo " <a href=\"noticeboard.php?event=deleteimage&amp;image_id=$image_id&amp;page_id=$page_id&amp;m=$m&amp;content_id=$content_id&amp;name=$linkname\"><img src=\"../images/picture_delete.png\" title=\"$phrase[590]\"  alt=\"$phrase[590]\"></a><p></p> ";
								}	
									}
							
							}
					}
						
						
						echo "<p>$body<br><br>";
					if ($access->thispage == 3 || ($access->thispage == 2 && $_SESSION["username"] == $updated_by))  
						
						{
							
					
							
							echo "<a href=\"noticeboard.php?m=$m&amp;content_id=$content_id&amp;event=image&amp;page_id=$page_id\"><img src=\"../images/picture_add.png\" title=\"$phrase[79]\"  alt=\"$phrase[79]\"></a>&nbsp;&nbsp;&nbsp;<a href=\"noticeboard.php?m=$m&amp;content_id=$content_id&amp;page_id=$page_id&amp;event=edit\"><img src=\"../images/comment_edit.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a>
							&nbsp;&nbsp;&nbsp;<a href=\"noticeboard.php?m=$m&amp;content_id=$content_id&amp;page_id=$page_id&amp;event=deleteparagraph&amp;title=$linktitle\"><img src=\"../images/comment_delete.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\">
							</a>";} 
						echo "</p>";
									
							
					
						
						
						if (isset($darray_docid))
						{
						
							{$displaydocs = 0;}
						foreach ($darray_docid as $index => $doc_id)
							{
							if ($darray_content[$index] == $content_id)
									{$displaydocs = 1;}
							}
						
							
						
						
						if ($displaydocs ==1)
						
							{echo "<br><span class=\"sub\">Related documents:</span><br>";}
							
							
						foreach ($darray_docid as $index => $doc_id)
							{
							
						
							if ($darray_content[$index] == $content_id)
									{
									
									
									echo " <a href=\"../main/doc.php?m=$m&doc_id=$darray_docid[$index]\" >$darray_docname[$index]</a><br>";
									
									}
							
							}
						}
							
							
							
							
							//end of paragraph  create white space
						echo "<br>";
						}
	
	
	//end read content 
	}


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



include ("../includes/footer.php");
?>