<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

	
$integers[] = "cat_id";
$integers[] = "catid";
$integers[] = "id";
$integers[] = "cat";




foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}	
	

if (!isset($offset)) {$offset = 0;}

	

	
	$ip = ip("pc");  

	
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
	$DB->query($sql,"clicker.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);
		
	//page_view($DB,$PREFERENCES,$m,"");	
	
	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

	

		
		
		include("../includes/leftsidebar.php");
		
			echo "<div id=\"content\"><div>";
		
		


		
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



if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete")
	{
	
$sql = "delete from page where page_id = '$catid' and m = '$m'";

	$DB->query($sql,"editlinks.php");
//deletepage($delpage,$ip,$DB);
	
		}
		
		
		
		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletelink")
	{
	
$sql = "delete from content where content_id = '$id' and page_id = '$cat_id'";

	$DB->query($sql,"editlinks.php");
//deletepage($delpage,$ip,$DB);
	
		}
		
			
	if (isset($_REQUEST["update"]) &&  $_REQUEST["update"] == "addlink")
	{

	$name = trim($DB->escape($_REQUEST["name"]));
	$url = trim($DB->escape($_REQUEST["url"]));	
	
	$description = trim($DB->escape($_REQUEST["description"]));	
	
	$description = "[url]" . $url . "[/url] " . $description;
	$time = time();
	$ip = ip("pc");
	
	  $sql = "INSERT INTO content VALUES(NULL,'$name','$description','$cat_id','0','$_SESSION[username]','$ip','$time','0','0','0','1','0')"; 
	
   	$DB->query($sql,"editlinks.php");
	
	}

	
if (isset($_REQUEST["update"]) &&  $_REQUEST["update"] == "reset")
	{
		if ($DB->type == "mysql")
		{
	  $sql = "update content, page set archive = '0'where page.page_id = content.page_id and page.m = '$m'"; 
		}
	else
		{
	  $sql = "update content set archive = '0' where  content.page_id  in ( select page_id from page where m = '$m')"; 
		}
		

   	$DB->query($sql,"editlinks.php");	
	}

if (isset($_REQUEST["update"]) &&  $_REQUEST["update"] == "editlink")
	{

	$title = trim($DB->escape($_REQUEST["name"]));
	$url = trim($DB->escape($_REQUEST["url"]));	
	
	$description = trim($DB->escape($_REQUEST["description"]));	
	
	$description = "[url]" . $url . "[/url] " . $description;
	$time = time();
	$ip = ip("pc");
	
	  $sql = "update content set title = '$title', body = '$description' where content_id = '$id'"; 

   	$DB->query($sql,"editlinks.php");
	
	}

	
	if (isset($_REQUEST["update"]) &&  $_REQUEST["update"] == "rename")
	{

	$page_title = trim($DB->escape($_REQUEST["page_title"]));

	


	
	  $sql = "update page set page_title = '$page_title' where page_id = '$cat_id'"; 

   	$DB->query($sql,"editlinks.php");
	
	}

	
if (isset($_REQUEST["update"]) &&  $_REQUEST["update"] == "add")
	{

	$name = trim($_REQUEST["name"]);
	if ($name == "")
		{
		$ERROR = "$phrase[82]";
		
		}
	else
		{
		 
		 
		  if (!isset($ERROR)) 
		  {
		 $name = $DB->escape($name);
		 
		  $sql = "INSERT INTO page VALUES(NULL,'$name','$m','1','0','0','c','$name','0','0','0')"; 
	
   			$DB->query($sql,"editlinks.php");
   			
   			
			}	

	}
	}
	
	
	
	
	
	

	$sql = "select * from modules where m = \"$m\"";
$DB->query($sql,"page.php");
$row = $DB->get();
$modname = formattext($row["name"]);



echo " <h1 class=\"red\">$modname</h1>  ";


if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delete")
	{
	
	echo "<br><b>$phrase[14]</b><br><br><br><br>
	<a href=\"editlinks.php?m=$m&amp;update=delete&amp;catid=$cat\">$phrase[12]</a> | <a href=\"editlinks.php?m=$m&amp;cat_id=$cat\">$phrase[13]</a>";
	}




elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "check")
	{
		
	
				$sql = "SELECT page_id, page_title FROM page where m = '$m' order by pageorder, page_title";
	$DB->query($sql,"editlinks.php");
			
			
	
	
	echo "<a href=\"editlinks.php?event=newcat&amp;m=$m\"><img src=\"../images/add.png\" title=\"$phrase[845]\" alt=\"$phrase[845]\"></a> 
	<a href=\"editlinks.php?event=check&amp;m=$m\"><img src=\"../images/search.png\" title=\"$phrase[856]\" alt=\"$phrase[856]\"></a>
	";
		
		echo "<br><br><ul id=\"horizontalnav\">";
	
	while ($row = $DB->get())
	{
		$cat = $row["page_id"];
		
		if (!isset($cat_id))
	{
		$cat_id = $cat;
	}
	$name = $row["page_title"];
	
	echo "<li><a href=\"editlinks.php?m=$m&cat_id=$cat\"";
	
	if (isset($cat_id) && $cat_id == $cat) {echo " id=\"hcurrent\"";}
	echo ">$name</a></li>"; 
	}	

	unset ($cat_id);
	
	echo "</ul><br>
	<h2 style=\"margin-top:2em;clear:both\">$phrase[857] <span id=\"message\"></span></h2>
	";
			
			$sql = "SELECT title, body,content_id FROM content,page where content.page_id = page.page_id and page.m = '$m'  order by title";
	$DB->query($sql,"editlinks.php");
	while ($row = $DB->get())
	{
		$content_id = $row["content_id"];
	$title = $row["title"];
	$body = $row["body"];
	$body = substr_replace($body, '', 0,5);
	$pos = strpos($body,"[/url] ");
	$array_order[$counter]  = $row["content_id"];

	$url[$content_id] = substr($body, 0, $pos);
	
	$span_id = "span_" . $content_id;
	
	//echo "<p><span id=\"$span_id\"><img src=\"../images/help.png\"></a></span><a href=\"$url[$content_id]\">$title</a></p>";
	echo "<p><span id=\"$span_id\"><img src=\"../images/help.png\" title=\"checking\" alt=\"checking\"></span><a href=\"$url[$content_id]\">$title</a></p>";
	}
	
	
	
	echo "
	<script type=\"text/javascript\">
	var path = \"link.php?url=\";
	var urls = new Array();
	var ids = new Array();
	var counter = 0;
	var span = document.getElementById(\"message\");;
	
	
	";
	
	

	
	$counter = 0;
	  foreach($url as $content_id => $url)
	{
		echo "urls[$counter] = \"$url\";
ids[$counter] =  $content_id;
		 ";
		
		$counter++;
	}
	
	
?>





	function processXML(url,id) {
	
	

		var req = null; 
		var id = "span_" + id
	

	
 
		if (window.XMLHttpRequest)
		{
 			req = new XMLHttpRequest();

		} 
		else if (window.ActiveXObject) 
		{
			try {
				req = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e)
			{
				try {
					req = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			}
        	}

		
		
		req.onreadystatechange = function()
		{ 
			
	
		
			if(req.readyState == 4)
			{
				if(req.status == 200)
				{
				//alert(id)
				var elem = document.getElementById(id);
				//
				
				var str = req.responseText;
			
				if (str.charAt(0) == "3" || str.charAt(0) == "2")
				
				{ 
				
				
				elem.innerHTML = '<img src="../images/accept.png" title="OK">';
				
				}
				else 
				{
				//alert(req.responseText + "some kind of error")
				
				
				var error = "req.responseText";
				if (req.responseText == "404") { error = "Page not found";}
				if (req.responseText == "403") { error = "Forbidden";}
				if (req.responseText == "504") { error = "URL not found";}
				
				
				elem.innerHTML = '<img src="../images/cross.png" title="' + req.responseText + error + '">';
				
				}
				
				counter = counter + 1
				
				var message = counter + '/' + urls.length
				//alert(message)
				span.innerHTML = message
				
				
					
			} 
			else 
			{
			//alert("Error checking links")
			}
		}
			}
			var timestamp = new Date();
		var fullurl = path + encodeURI(url) + "&t=" + timestamp.getTime()
		//alert(fullurl)
		req.open("GET", fullurl, true); 
		req.send(null); 
	
	
			
	}

function init()
{

span.innerHTML = "0/" + urls.length

for(i=0;i<urls.length;i++)
{
try
	{
	processXML(urls[i],ids[i]);
	}
catch(e)
	{
	var elem = 	document.getElementById(ids[i]);
	elem.innerHTML = '<img src="../images/cross.png" title="Error">';
	}

}
}


<?php






echo "


window.onload=init
	</script>
	
	
	
	";
		
	}


elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "rename")
	{
		
		$sql = "select page_title from page where page_id = '$cat'";
		$DB->query($sql,"editpage.php");
	$row = $DB->get();
	$page_title = $row["page_title"];	
	
	echo "

	<form action=\"editlinks.php\" method=\"get\" ><fieldset><legend>$phrase[850]</legend><br>

						<input type=\"hidden\" name=\"update\" value=\"rename\">
							<input type=\"hidden\" name=\"cat_id\" value=\"$cat\">
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<table><tr><td>$phrase[87]</td><td><input type=\"text\" name=\"page_title\" size=\"50\" maxlength=\"100\" value=\"$page_title\"></td></tr>
						
								
						<tr><td valign=\"top\"></td><td>
					
						<input type=\"submit\" name=\"addpage\" value=\"$phrase[850]\" ></td></tr>
						
					
						</table></fieldset></form>";
	}





		
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "newcat")
		{
		echo "<form method=\"post\" action=\"editlinks.php\" style=\"width:50%;\">
	<fieldset>
	<legend>$phrase[845]</legend>
	<table style=\"margin:0 auto\" ><tr><td style=\"text-align:left\">
	
	<input type=\"text\" name=\"name\" > <input type=\"submit\" value=\"$phrase[176]\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"add\">
	</td></tr>
	</table>
	</fieldset>
	
	
	
	</form>";	
		}
		
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editcat")
		{
	$sql = "SELECT page_title FROM page_id = \"$cat_id\" and m = '$m'";
	$DB->query($sql,"page.php");
	$row = $DB->get();
	$name = $row["pagetitle"];
	
	echo "
	
	
	<form method=\"post\" action=\"editlinks.php\" style=\"width:50%;margin:0 auto\">
	<fieldset>
	<legend>$phrase[178]</legend>
	<table style=\"margin:0 auto\" ><tr><td style=\"text-align:left\">
	
	<input type=\"text\" name=\"name\" value=\"$name\"> <input type=\"submit\" value=\"$phrase[178]\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">
	<input type=\"hidden\" name=\"update\" value=\"edit\">
	</td></tr>
	</table>
	</fieldset>
	
	
	
	</form>";
	
		}
		else {
			
				$sql = "SELECT * FROM page where m = '$m' order by pageorder, page_title";
	$DB->query($sql,"editlinks.php");
			
			
	
	
	echo "<a href=\"editlinks.php?event=newcat&amp;m=$m\"><img src=\"../images/add.png\" title=\"$phrase[845]\"  alt=\"$phrase[845]\"></a>
	 <a href=\"editlinks.php?event=check&amp;m=$m\"><img src=\"../images/search.png\" title=\"$phrase[856]\"  alt=\"$phrase[856]\"></a>
	 <a href=\"editlinks.php?update=reset&amp;m=$m\"><img src=\"../images/repeat.png\" title=\"$phrase[865]\"  alt=\"$phrase[865]\"></a>";
		
		echo "<br><br><ul id=\"horizontalnav\">";
	
	while ($row = $DB->get())
	{
		$cat = $row["page_id"];
		if (!isset($cat_id))
	{
		$cat_id = $cat;
	}
	$name = $row["page_title"];
	
	echo "<li><a href=\"editlinks.php?m=$m&cat_id=$cat\"";
	
	if (isset($cat_id) && $cat_id == $cat) {echo " id=\"hcurrent\"";}
	echo ">$name</a></li>"; 
	}	
	
	echo "</ul><br><br>";
		}
	
	
	
	if (isset($cat_id))
	{
		
		if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editlink")
		{
			
		$sql = "SELECT * FROM content where content_id = '$id'";
	$DB->query($sql,"editlinks.php");
	$row = $DB->get();
	
		$id = $row["content_id"];
	$title = $row["title"];
	$body = $row["body"];
	$body = substr_replace($body, '', 0,5);
	$pos = strpos($body,"[/url] ");

	$url = substr($body, 0, $pos);
	$pos = $pos + 7;
	$body = substr_replace($body, '', 0,$pos);	
				echo "<form method=\"post\" action=\"editlinks.php\" >
	<fieldset>
	<legend>$phrase[849]</legend>
	<table  >
	<tr><td class=\"label\">$phrase[848]</td><td><input type=\"text\" name=\"name\" value=\"$title\" size=\"50\"> </td></tr>
	<tr><td class=\"label\">$phrase[847]</td><td><input type=\"text\" name=\"url\" value=\"$url\" size=\"50\"> </td></tr>
		<tr><td class=\"label\">$phrase[123]</td><td><textarea name=\"description\" cols=\"50\" rows=\"10\">$body</textarea></td></tr>
	<tr><td></td><td>
	<input type=\"submit\" value=\"$phrase[849]\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"id\" value=\"$id\">
	<input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">
	<input type=\"hidden\" name=\"update\" value=\"editlink\">
	</td></tr>
	</table>
	</fieldset></form>";
	
		}
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletelink")
{
	echo "<br><b>$phrase[14]</b><br><br><br><br>
	<a href=\"editlinks.php?m=$m&amp;update=deletelink&amp;cat_id=$cat_id&id=$id\">$phrase[12]</a> | <a href=\"editlinks.php?m=$m&cat_id=$cat_id\">$phrase[13]</a>";
	
}
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addlink")
		{

			echo "<form method=\"post\" action=\"editlinks.php\" >
	<fieldset>
	<legend>$phrase[846]</legend>
	<table  >
	<tr><td class=\"label\">$phrase[848]</td><td><input type=\"text\" name=\"name\"  size=\"50\"> </td></tr>
	<tr><td class=\"label\">$phrase[847]</td><td><input type=\"text\" name=\"url\"  size=\"50\"> </td></tr>
		<tr><td class=\"label\">$phrase[123]</td><td><textarea name=\"description\" cols=\"50\" rows=\"10\"></textarea></td></tr>
	<tr><td></td><td>
	<input type=\"submit\" value=\"$phrase[846]\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"cat_id\" value=\"$cat_id\">
	<input type=\"hidden\" name=\"update\" value=\"addlink\">
	</td></tr>
	</table>
	</fieldset></form>";
	
		}
		else 
		{
			$sql = "SELECT * FROM content where page_id = '$cat_id'";
	$DB->query($sql,"editlinks.php");
	$num = $DB->countrows();
	
		echo "
		
	<br><p style=\"clear:both;margin-top:2em\">
		
		<a href=\"editlinks.php?m=$m&cat=$cat_id&event=rename\"><img src=\"../images/page_edit.png\" title=\"$phrase[850]\"></a>";
 if ($num == 0) { echo " <a href=\"editlinks.php?m=$m&cat=$cat_id&event=delete\"><img src=\"../images/page_delete.png\" title=\"$phrase[851]\"></a>";}
		echo " <a href=\"editlinks.php?m=$m&amp;cat_id=$cat_id&event=addlink\"><img src=\"../images/paragraph_add.png\" title=\"$phrase[846]\"></a>";
		
		$counter = 0;
		$sql = "SELECT * FROM content where page_id = '$cat_id'  order by page_order, title";
	$DB->query($sql,"editlinks.php");
	while ($row = $DB->get())
	{
		$counter++;
		$array_id[] = $row["content_id"];
	$array_title[] = $row["title"];
	$body = $row["body"];
	$body = substr_replace($body, '', 0,5);
	$pos = strpos($body,"[/url] ");
	$array_order[$counter]  = $row["content_id"];
	$array_hits[] = $row["archive"];
	$array_url[] = substr($body, 0, $pos);
	$pos = $pos + 7;
	$array_body[] = substr_replace($body, '', 0,$pos);
	}
	
	$counter = 0;
	if (isset($array_id))
	{
	foreach ($array_id as $index => $content_id)
	{
	echo "<p><strong><a href=\"$array_url[$index]\">$array_title[$index]</a></strong><br>$array_body[$index]<br>
	(<span id=\"count_$content_id\">$array_hits[$index]</span>) ";
	$counter++;
	
	if (($num > 0) && ($counter > 1))
							{
							
							//Change order array so the paragraph can be moved up page
							//echo "<p></p>";
						
								
							
								
							foreach ($array_order as $ind => $value)
									{
									
									if ($ind == ($counter -1))
										{
									
										$up = $array_order;
										$up[$ind] = $array_order[$counter];
										$up[$counter] = $value;
										}
									}
							
							
							
							
						
								

							$up = implode(",",$up);
							echo "<a href=\"editlinks.php?m=$m&amp;cat_id=$cat_id&amp;update=reorder&amp;reorder=$up\"><img src=\"../images/up.png\" title=\"$phrase[77]\"></a>&nbsp;&nbsp;&nbsp;";
							
							
							
							
							}
	
	
	
						if (($num > 0) && ($counter < $num))
							{
							
							//echo "<br>";
						
							
							
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
							echo "<a href=\"editlinks.php?m=$m&amp;cat_id=$cat_id&amp;reorder=$down&amp;update=reorder\"><img src=\"../images/down.png\" title=\"$phrase[78]\"></a>&nbsp;&nbsp;&nbsp;";
							}
						
	
	
	
	
	echo "<a href=\"editlinks.php?m=$m&cat_id=$cat_id&id=$array_id[$index]&event=editlink\"><img src=\"../images/page_white_edit.png\" title=\"$phrase[26]\"></a>
	  
	<a href=\"editlinks.php?m=$m&cat_id=$cat_id&id=$array_id[$index]&event=deletelink\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"></a> 
	</p><br>"; 
	
	
	
	?>
		<script type="text/javascript">
		
		
		
		
		function update()
		{
		
			
		ajax("link.php?page_id=<?=$cat_id?>",updatehits)
		
		
		function updatehits(text)
		{
			//alert(text)
			var hits = eval("(" + text + ")");
			
			
			for (var content_id in hits) {
			//alert(hits[content_id]);
			document.getElementById("count_" + content_id).innerHTML = hits[content_id];
				}
				
			
			//for (var index = 0, index < hits.length,index++)
			
			//{
			//	alert(hits.array[index]);
			//}
			
			
		}
		
		
			
			
		function ajax(url,callback,id)

{
			var req = null; 
	
		if (window.XMLHttpRequest)
		{
 			req = new XMLHttpRequest();
		} 
		else if (window.ActiveXObject) 
		{
			try {
				req = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e)
			{
				try {
					req = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			}
        	}
		
		if (callback) {
			req.onreadystatechange = function process(){
				if (req.readyState == 4 && req.status == 200) {
					callback(req.responseText,id)
				}
			}
		}
		var timestamp = new Date();
		url = url + "&t=" + timestamp.getTime();
		//alert(url)
		req.open("GET", url, true); 
		req.send(null); 

}
		
		}
		
		window.onload=update
		</script>
		
		
		
		<?php
		
		
	
	
	}
	}	
	}
	}
	
	} //!issset($ERROR)
	
		//end contentbox
		echo "</div></div>";
		
		
	include("../includes/rightsidebar.php");

include ("../includes/footer.php");

?>

