<?php
//
$limit = 10;

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$ip = ip("pc");
$proxy = ip("proxy");

$integers[] = "offset";
$integers[] = "doc_id";
$integers[] = "cat_id";


foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (isset($_REQUEST["keywords"]))
{ 
	if ($_REQUEST["keywords"] == "")
	{ $ERROR = $phrase[219];} else {
	$keywords = $_REQUEST["keywords"];}
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
	$sql = "select name from modules where m = '$m'";
	$DB->query($sql,"files.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);	
		
	page_view($DB,$PREFERENCES,$m,"");	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

echo "<div style=\"text-align:center\">";


	
if (!isset($ERROR) && $access->thispage > 1)
	{
	
	

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "uploaddoc")

{		

	
	upload($m,$cat_id,"0",$PREFERENCES,$DB,"document",$ip,$phrase);	
		

				
						
			

}

			
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addcat"  )
   {
	$cat_name = $DB->escape($_REQUEST["cat_name"]);
	
	 $sql = "INSERT INTO page VALUES(NULL,'$cat_name','$m','1','0','0','a','','0','0','0')"; 
	
	$DB->query($sql,"files.php");	
	}
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updatedoc" )
   {
	$description = $DB->escape($_REQUEST["description"]);
	$sql = "update documents set keywords = \"$description\" where doc_id = \"$doc_id\"";
	
	$DB->query($sql,"files.php");	
	}
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "restoredoc" )
		{
		restoredocument($doc_id,"0",$ip,$cat_id,$DB);
		}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "rename" )
		{
		$cat_name = $DB->escape($_REQUEST["cat_name"]);
		
  		$sql = "update page set page_title = \"$cat_name\" where page_id=\"$cat_id\"";
  	
  		
		$DB->query($sql,"locations.php");
		}


if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletecat" )
		{
	
		
		$sql = "select * from documents where page = \"$cat_id\"";
		$DB->query($sql,"files.php");
		$num = $DB->countrows();
		if ($num == 0)
		{
		
		$sql = "DELETE FROM page WHERE page_id = \"$cat_id\"";
		$DB->query($sql,"files.php");
		$path = $PREFERENCES["docdir"] ."/".$m."/".$cat_id;
		delDir($path);	
			
		} else {$WARNING = "$phrase[293]";
		
		}
		}

		
		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletedoc" )
		{
			
			
	deletedoc($ip,$doc_id,$DB);

		

		}
		}
		



	
$sql = "select name, ordering from modules where m = \"$m\"";
		$DB->query($sql,"helpdesk.php");
		$row = $DB->get();
		$modname = formattext($row["name"]);	
		
				$sql = "select * from page where m = \"$m\" order by page_title";
		$DB->query($sql,"files.php");
		while ($row = $DB->get())
      {
      $array_cat_id[] = $row["page_id"];
      $array_cat_name[] = $row["page_title"];
    
      }
		
echo "<h1>$modname</h1>";


		
if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletecat" &&  $access->thispage > 1)
{
	echo "<h2>Categories</h2><br><b>$phrase[14]</b><br><br>
	<a href=\"files.php?m=$m&amp;update=deletecat&amp;cat_id=$cat_id&amp;event=categories\">$phrase[12]</a> | <a href=\"files.php?m=$m&amp;event=categories\">$phrase[13]</a>";
	
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletedoc" &&  $access->thispage > 1)
{
	echo "<h2>Documents</h2><br><b>$phrase[14]</b><br><br> $_REQUEST[filename]<br><br>
	<a href=\"files.php?m=$m&amp;update=deletedoc&doc_id=$doc_id&amp;event=categories\">$phrase[12]</a> | <a href=\"files.php?m=$m\">$phrase[13]</a>";
	
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit" &&  $access->thispage > 1)
{
		$sql = "select *  from page where page_id = '$cat_id'";
 	$DB->query($sql,"files.php");
 	$row = $DB->get();
	$cat_name = $row["page_title"];
		
	echo "<a href=\"files.php?m=$m\">$phrase[648]</a> | <a href=\"files.php?m=$m&cat_id=$cat_id&search=browse\">$cat_name</a>";
	
	
		 $sql = "select * from documents where  documents.m = \"$m\" and doc_id = \"$doc_id\"";
	
$DB->query($sql,"filesview.php");
	$row = $DB->get();
        $tags = $row["keywords"];

		
	echo "<form action=\"files.php\" method=\"get\" enctype=\"multipart/form-data\" style=\"margin-left:auto;margin-right:auto;width:80%\"><fieldset><legend>$phrase[1081]</legend>
						
					<br>
						
						<table cellpadding=\"5\">
						<tr><td><b>$phrase[553]</b></td><td align=\"left\"><b>$row[doc_name]</b></td></tr>
    <tr><td valign=\"top\"><b>$phrase[1081]</b></td><td>
						
			
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
						
			</td></tr>
						<tr><td></td><td>
						
							<input type=\"hidden\" name=\"m\" value=\"$m\">
							<input type=\"hidden\" name=\"doc_id\" value=\"$doc_id\">
						
						<input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">
							<input type=\"hidden\" name=\"search\" value=\"browse\">
						<input type=\"hidden\" name=\"update\" value=\"updatedoc\">
						
						<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\" ></td></tr></table>
						</fieldset></form>";
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "rename" &&  $access->thispage > 1)
{
	
		$sql = "select page_title  from page where page_id = '$cat_id'";
 	$DB->query($sql,"files.php");
 	$row = $DB->get();
	$cat_name = $row["page_title"];
		
		echo "<a href=\"files.php?m=$m\"><img src=\"../images/folder_table.png\" title=\"$phrase[648]\"></a>  <a href=\"files.php?m=$m&cat_id=$cat_id&search=browse\"><img src=\"../images/folder.png\" title=\"$phrase[648]\"></a> $cat_name";
	
	echo "<h2>$phrase[649]</h2>
	
	<form action=\"files.php\" method=\"get\">
	<input type=\"text\" name=\"cat_name\" value=\"$cat_name\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[649]\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">
	<input type=\"hidden\" name=\"update\" value=\"rename\">
	</form>";
	
}

elseif (isset($_GET["event"]) && $_GET["event"] == "folderhistory")
	{
	
	$restoredoc = "yes";	
	$sql = "select page_title  from page where page_id = '$cat_id'";
 	$DB->query($sql,"files.php");
 	$row = $DB->get();
	$cat_name = $row["page_title"];
		
	echo "<a href=\"files.php?m=$m\">$phrase[648]</a> | <a href=\"files.php?m=$m&cat_id=$cat_id&search=browse\">$cat_name</a>
	<br><br>
	<h4>$phrase[803]</h4><br><table class=\"colourtable\" style=\"width:50%;padding:0.5em;margin:0 auto\">";
	
	
	
	
	$sql = "select doc_id, deleted, doc_name from documents where page =' $cat_id'";
	$DB->query($sql,"editcontent.php");
	
	while ($row = $DB->get())
	{
	$doc_id = $row["doc_id"];
	$docs[$doc_id] =   $row["deleted"];	
	$docnames[$doc_id] =   $row["doc_name"];		
	}
	
	$sql = "select title, body, page_id, page_order, updated_by, updated_ip, event, updated_when from content where page_id = '$cat_id' and (event < '9') order by updated_when desc";
	
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
		//Restored Document
		echo "$phrase[790] <b>$title</b><br>
		";
	}
	if ($event == 6)
	{
		//Keywords updated
		echo "$phrase[783]";
		if (isset($docs) && array_key_exists($title,$docs))
		{
		 echo " <b>$docnames[$title]</b><br><br><i>$body</i><br><br>
			<form action=\"files.php\"method=\"post\">

	<input type=\"hidden\" name=\"doc_id\" value=\"$body\">
<input type=\"hidden\" name=\"description\" value=\"$body\">
	<input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">

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
			<form action=\"files.php\"method=\"post\">

	<input type=\"hidden\" name=\"doc_id\" value=\"$body\">

	<input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">

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

	echo "</td></tr>";
	}
	
	echo "</table>";
	
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "adddoc" &&  $access->thispage > 1)
{
		$sql = "select page_title  from page where page_id = '$cat_id'";
 	$DB->query($sql,"files.php");
 	$row = $DB->get();
	$cat_name = $row["page_title"];
		
	echo "<a href=\"files.php?m=$m\"><img src=\"../images/folder_table.png\" title=\"$phrase[648]\"></a>  <a href=\"files.php?m=$m&cat_id=$cat_id&search=browse\"><img src=\"../images/folder.png\" title=\"$cat_name\"></a> $cat_name";
	
	$sql = "select * from page  where m= \"$m\" order by page_title";
		$DB->query($sql,"files.php");
		$num = $DB->countrows();
		if ($num == 0)
		{ echo "$phrase[290]";}
		else 
		{echo "
	<h2>$_REQUEST[cat_name]</h2> 
	
	<form action=\"files.php\" method=\"post\" enctype=\"multipart/form-data\" style=\"margin-left:auto;margin-right:auto;width:50%;text-align:left\"><fieldset><legend>$phrase[80]</legend>
						
					<br>
						
<b>$phrase[553]</b><br>
						

						<input type=\"file\" name=\"upload[0]\" ><br><br>
						
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
                                
                                
                                
</script>
			<br><br>
						
						<input type=\"hidden\" name=\"m\" value=\"$m\">
					<input type=\"hidden\" name=\"update\" value=\"uploaddoc\">
					<input type=\"hidden\" name=\"search\" value=\"browse\">
					<input type=\"hidden\" name=\"cat_id\" value=\"$_REQUEST[cat_id]\">
						
						<input type=\"submit\" name=\"submit\" value=\"$phrase[80]\" ></td><tr></table>
						</fieldset></form>
						
						
						
						
						
						<br>	";	
		}
	
	
}

elseif (isset($_REQUEST["search"])) 
	{
	

	$sql = "select page_title  from page where page_id = '$cat_id'";
 	$DB->query($sql,"files.php");
 	$row = $DB->get();
	$cat_name = $row["page_title"];
	
		
		
	echo "<h2>$cat_name</h2>";	
$urlname = urlencode($cat_name);
		
echo " 	<form action=\"files.php\" method=\"get\" id=\"searchform\" style=\"display:inline\">
 
	<input type=\"text\" name=\"description\" size=\"30\" id=\"description\"> 
<input type=\"hidden\" name=\"search\" value=\"keyword\">
	
	<input type=\"hidden\" name=\"m\" value=\"$m\"><input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">
 	
<input type=\"submit\" name=\"searchdocs\" value=\"$phrase[282]\" id=\"dockeywords\"> 
 	</form>  <a href=\"files.php?m=$m\" style=\"margin-left:1em;\"><img src=\"../images/folder_table.png\" title=\"$phrase[648]\"></a>";

    if ($access->thispage > 1) 
      {
      			$sql = "select count(*) as total, page  from documents where m = \"$m\" group by page";
$DB->query($sql,"files.php");

while ($row = $DB->get())
      {	
      	$totals[$row["page"]] = $row["total"];
    
      }
    
       $linkname = urlencode($cat_name);
       
       
       
       //check history
      
		$sql = "SELECT * FROM content where page_id = \"$cat_id\" and event > '3' and event < 9";
		$DB->query($sql,"page.php");
		$total = $DB->countrows();
        
echo "<a href=\"files.php?m=$m&amp;event=adddoc&amp;cat_id=$cat_id&amp;cat_name=$urlname\" style=\"margin-left:1em;\"><img src=\"../images/attach.png\" title=\"$phrase[286]\"></a>";	
if ($total > 0) {echo "<a href=\"files.php?m=$m&amp;event=folderhistory&cat_id=$cat_id\" style=\"margin-left:1em;\"><img src=\"../images/clock.png\" title=\"$phrase[803]\"></a>";}
	
 	
 	
 	echo "<a href=\"files.php?m=$m&amp;cat_id=$cat_id&amp;event=rename&cat_name=$linkname\" style=\"margin:1em;\"><img src=\"../images/folder_edit.png\" title=\"$phrase[649]\"></a>";
 
 	if (isset($totals) )
 	{
 	
 		if (!array_key_exists($cat_id,$totals))
 		{
 	echo "	<a href=\"files.php?m=$m&amp;cat_id=$cat_id&amp;event=deletecat\"><img src=\"../images/cross.png\" title=\"$phrase[762]\"></a>";
 		}
 	} else {
 		
 		echo "<a href=\"files.php?m=$m&amp;cat_id=$cat_id&amp;event=deletecat\"><img src=\"../images/cross.png\" title=\"$phrase[762]\"></a>";
 	}

      }

      echo "<script type=\"text/javascript\">
							   								         
				function setfocus() 
				 {
				 document.getElementById('description').focus(); 
				 }

				 window.onload = setfocus;
				</script><br><br>


 ";
 
		
	
		
		
		
			
		
    
		if (!isset($offset) || $offset == 0)
			{
			$ender = " limit $limit";
			}
		else	
			{
	
			$ender = " limit $offset, $limit";
			}
	
		
	
		 if ($_REQUEST["search"] == "keyword" )
		{
	
		$description = $DB->escape($_REQUEST["description"]);
		
		if ($DB->type == "mysql")
			{
		
		$sqlinsert = "";
		if ($cat_id > 0) {  $sqlinsert .= "and documents.page = \"$cat_id\"";}
		$sqlinsert .= " and MATCH (keywords) AGAINST (\"$description\" IN BOOLEAN MODE)";
			}
		
		else
			{	
			$description = trim($description);
       			
       			$words = explode(" ",$description);
       			
       			$sqlinsert = "";
       			
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
				$sqlinsert .= " and keywords like '%$value%' ";	
				$counter++;
				}	
				if ($description == "") {$sqlinsert = " and 1 == 2";}
				
			}
		
		
	
		$sql = "select doc_id, page_title, doc_name, type, keywords, uploaddate  from documents, page where documents.deleted = '0' and documents.page = page.page_id and documents.m = \"$m\" $sqlinsert";
		
	//echo $sql;
		}
		elseif ($_REQUEST["search"] == "browse" )
		{
			if ($cat_id == 0)
			{
			$sql = "select doc_id, page_title, doc_name, type, keywords, uploaddate   from documents, page where documents.deleted = '0' and documents.page = page.page_id and documents.m = \"$m\"  order by doc_id desc";
			}
			else 
			{
			$sql = "select doc_id, page_title, doc_name, type, keywords, uploaddate  from documents,page where documents.deleted = '0' and documents.page = page.page_id and documents.m = \"$m\" and documents.page = \"$cat_id\" order by doc_id desc";	
			
			}
		}
		
		 $DB->query($sql,"files.php");
		 $numrows= $DB->countrows();
		
		 $sql = $sql . $ender;
		$DB->query($sql,"files.php");
		
		
		if (!isset($offset)) {$offset = 0;}
		$displayoffset = 1 + $offset;
		$displayend = $limit + $offset;
		if ($displayend > $numrows) {$displayend = $numrows;}
		 
		 

if ($_REQUEST["search"] == "keyword" )
	{
	$keywords = $_REQUEST["description"];
	echo "<br><b>$keywords</b><br>";
	
	//$keywords = urlencode($keywords);
	$insert = "&searchdocs=search&description=" . urlencode($keywords);
	}
else
	{
	echo "<b>$phrase[552]</b><br>";
	$insert = "&search=browse&amp;cat_id=$cat_id";
	}
	
	
if ($displayend == 0) {$displayoffset = 0;}






if ($numrows > $limit)
	{
	$prevoffset = $offset - $limit;
	$nextoffset = $offset + $limit;	
	

	
if ($offset == 0)
			{
			//$offset = $limit;
			}
	else	
		{
		 $printprev = "&nbsp; <a href=\"files.php?m=$m&offset=$prevoffset$insert\">$phrase[215]</a> \n";
		}
	if ($nextoffset < $numrows)
			{
			$printnext = "<a href=\"files.php?m=$m&offset=$nextoffset$insert\">$phrase[216]</a> &nbsp; \n";	
			}
	 
	}	
if (!isset($printprev)) { $printprev = "";}
if (!isset($printnext)) { $printnext = "";}
	
		 
		 echo "
		   <script type=\"text/javascript\">
 function pop_window(url) {
  var popfile = window.open(url,'','status,resizable,scrollbars,width=800,height=500');
  if (window.focus) {popfile.focus()}
 }
</script>

		<table border=\"0\" width=\"96%\" style=\"margin-left:auto;margin-right:auto;\"><tr><td align=\"left\" width=\"32%\">&nbsp;$printprev</td><td align=\"center\" width=\"32%\">
		$displayoffset - $displayend  Total $numrows
		</td><td align=\"right\" width=\"32%\">$printnext&nbsp;</td></tr></table>
		 <br>
<table class=\"colourtable\" cellpadding=\"3\" width=\"95%\" style=\"margin:0 auto;text-align:left;\">
		 
		 
		 <tr class=\"accent\"><td><b>$phrase[553]</b></td><td><b>$phrase[186]</b></td><td><b>$phrase[1081]</b></td></tr>";
		$counter = 0;
		 while ($row = $DB->get())
     	 {
     	 	$counter++;
     	$doc_id = $row["doc_id"];
      	$cat_name = $row["page_title"];
      	$doc_name = $row["doc_name"];
      	$linkname= urlencode($doc_name);
      	$type = $row["type"];
      	if (strlen($row["keywords"]) > 100) 
      	{ $keywords = substr($row["keywords"], 0, 100) . "..";
      	$modname = urlencode($modname);
      		
      	} 
      	else {$keywords = $row["keywords"];} 
      
      
      	$uploaddate = strftime("%x",$row["uploaddate"]);
      	echo "<tr";
      	if ($counter%2 == 0) {echo " class=\"accent\"";}
      	echo ">
      	<td><b><a href=\"doc.php?m=$m&doc_id=$doc_id\">$doc_name</a></b></td><td>$uploaddate</td>
      	<td align=\"left\">$keywords <span style=\"float:right\">";
      		if ($access->thispage > 1)
		{ echo "<a href=\"files.php?m=$m&doc_id=$doc_id&amp;event=edit&cat_id=$cat_id\"><img src=\"../images/page_edit.png\" title=\"$phrase[763]\"></a> <a href=\"files.php?m=$m&amp;update=deletedoc&doc_id=$doc_id&filename=$linkname&search=browse&cat_id=$cat_id\" ><img src=\"../images/cross.png\" title=\"$phrase[24]\"></a> ";}
      	 echo " <a href=\"javascript:pop_window('fileview.php?m=$m&doc_id=$doc_id&modname=$modname')\"><img src=\"../images/page_search.png\" title=\"$phrase[284]\"></a></span>
      	
     
      	</td></tr>";
      	//unset($displaylink);
     	 }
     	 echo "</table>";
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		}
		else 
		{

 
 

      
     

	
$sql = "select * from page where m= \"$m\" order by page_title ";

$DB->query($sql,"files.php");
$num = $DB->countrows();

 if ($num > 0) { echo "<table border=\"0\" cellpadding=\"10\"  style=\"margin: 0 auto;text-align:left\">";}
 

while ($row = $DB->get())
      {
      $cat_id = $row["page_id"];
      $cat_name = $row["page_title"];
      $urlname = urlencode($cat_name);
      echo "<tr>
     <td><a href=\"files.php?m=$m&amp;cat_id=$cat_id&amp;search=browse&amp;cat_name=$urlname\"><img src=\"../images/folder.png\"></a></td><td> <a href=\"files.php?m=$m&amp;cat_id=$cat_id&amp;search=browse&amp;cat_name=$urlname\"><b>$cat_name</b></a><td>";
   
echo "</td></tr>";
      }
 

 if ($num > 0) { echo "</table>";}
 
 
  if ($access->thispage > 1) 
      {
 print <<<EOF
 <br>
<form action="files.php" method="POST" style="display:inline">
		<input type="text" name="cat_name" size="25" maxlength="100">
			<input type="hidden" name="update" value="addcat">
			<input type="hidden" name="event" value="categories">
			<input type="hidden" name="m" value="$m">
			<input type="submit" name="submit" value="$phrase[289]">
			</form><br><br>
EOF;
}

 
 if (!isset($array_cat_id))
		{	
	echo "<h2>$phrase[290]</h2>";
		}
 	
 	
		}
		//}
			
	}	

	
	echo "</div>";

include ("../includes/footer.php");

?>

