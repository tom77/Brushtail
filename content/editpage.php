<?php

//print_r($_REQUEST);

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
$integers[] = "page_id";
$integers[] = "delpage";
$integers[] = "restorepage";
$integers[] = "comments";
$integers[] = "displaydate";



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (isset($_REQUEST["m"]))
	{	
	
	if (isinteger($_REQUEST["m"]))
		{	
		$m = $_REQUEST["m"];	
		$access->check($m);
	
	if ($access->thispage < 3)
			{
		
			$ERROR  =  "<div style=\"text-align:center;font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
			}
	elseif ($access->iprestricted == "yes")
			{
			$ERROR  =  $phrase[0];
			}
	
		}
	else 
		{
		$ERROR  =  $phrase[72];
	
		}		
	}
else {
	$ERROR  =  "<h1>$phrase[866]</h1>";
	
}	


	$now = time();
$ip = ip("pc");
	

		
		
		$access->cmenu();
	
		
		include("../includes/leftsidebar.php");

echo "<div id=\"content\"><div>";
				



		
	if (!isset($ERROR))
	{	


	
	if (isset($_REQUEST["pagereorder"]))
	{
	//reorders pages
	$pagereorder = explode(",",$_REQUEST["pagereorder"]);
	
	foreach ($pagereorder as $index => $value)
		{
		
			$index = $DB->escape($index);	
		$value = $DB->escape($value);
		
		$sql = "update page set pageorder = '$index' WHERE page_id = '$value'";	
		$DB->query($sql,"editpage.php");
		
		
	
		}
	
	}	


	if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "restorepage")
	{
		
restorepage($restorepage,$ip,$DB);
		
		}
		
		
		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletepage")
	{
	

deletepage($delpage,$ip,$DB);
	
		}
		
			

	





	
if (isset($_REQUEST["addpage"]) )
	{
	//print_r($_REQUEST);
	$page_title = trim($_REQUEST["page_title"]);
	if ($page_title == "")
		{
		$ERROR = "$phrase[82]";
		
		}
	else
		{
		if (isset($_REQUEST["published"]) && $_REQUEST["published"] == "on")
			{
			$published = 1;
			}
		else
			{
			$published = 0;
			}
			
			
			if (isset($_REQUEST["feed"]) && $_REQUEST["feed"] == "on")
			{
			$feed = 1;
			}
		else
			{
			$feed = 0;
			}
		 //print_r($_REQUEST);
			
		 if (isset($_REQUEST["frontpage"]) && $_REQUEST["frontpage"] == "on")
			{
			$frontpage = 1;
			$sql = "update page set frontpage = '0' where m = '$m'";	
			$DB->query($sql,"editpage.php");
			}
		else
			{
			$frontpage = 0;
			}	
			
			
		 $page_title = $DB->escape($page_title);
		 
		 
		 
		 
		  if (!isset($ERROR)) 
		  {
		  $page_title = $DB->escape($page_title);
		  
		 // $type = $DB->escape($_REQUEST["type"]);
		  $paraordering = $DB->escape($_REQUEST["paraordering"]);
			$displaydate = $DB->escape($_REQUEST["displaydate"]);
			
		
		 
		  $sql = "INSERT INTO page VALUES(NULL,'$page_title','$m','$published','$frontpage','0','$paraordering','$page_title','0','$displaydate','$feed')"; 
		//echo $sql;
   			$DB->query($sql,"editpage.php");
   			
   			$page_id = $DB->last_insert();
   				 
		$sql = "insert into content values (NULL,'$page_title','','$page_id','0','$_SESSION[username]','$ip','$now','$page_id','12','0','1','0')";	
	$DB->query($sql,"editcontent.php");
		
   			
   			unset($page_id);			}	

	}
	}
	
	
	
	
if (isset($_REQUEST["updatemodule"]))
	{
	$pageordering = $DB->escape($_REQUEST["pageordering"]);	
	$sql = "update modules set ordering = '$pageordering' where m = '$m'";
	$DB->query($sql,"editpage.php");	
		
		
	}
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updatetitle")
	{
		
		
	$page_title = trim($_REQUEST["page_title"]);
	//if ($page_title == "")
	// { $ERROR = $phrase[72];}
	//else
	//	{
		
		if (isset($_REQUEST["published"]) and $_REQUEST["published"] == "on")
			{
			$published = 1;
			}
		else
			{
			$published = 0;
			}
		
			
		if (isset($_REQUEST["feed"]) and $_REQUEST["feed"] == "on")
			{
			$feed = 1;
			}
		else
			{
			$feed = 0;
			}		
			
	
		if (isset($_REQUEST["frontpage"]) and $_REQUEST["frontpage"] == "on")
			{
			$frontpage = 1;
			//unset current frontpage
			$sql = "update page set frontpage = '0' where m = '$m'";	
			$DB->query($sql,"editpage.php");
			}
		else
			{
			$frontpage = 0;
			}
			 
		
		
		
		
		
		
		
		$metadata = "$page_title"
		
		;
		$sql = "select * from content where event = '0' and page_id = \"$page_id\"";
		$DB->query($sql,"editpage.php");

	
		while ($row = $DB->get()) 
		{
		$metadata .= "$row[title]
		$row[body]
	
	";
		}
		$metadata = $DB->escape($metadata);
		$page_title = $DB->escape($page_title);
		$paraordering = $DB->escape($_REQUEST["paraordering"]);
		$displaydate = $DB->escape($_REQUEST["displaydate"]);
		
		$sql = "update page set page_title = '$page_title', published = '$published', displaydate = '$displaydate', frontpage = '$frontpage' , ordering = '$paraordering', metadata = '$metadata', feed = '$feed' WHERE page_id = '$page_id'";
		
		$DB->query($sql,"editpage.php");
			
	//}
	}

$sql = "select * from modules where m = '$m'";
$DB->query($sql,"page.php");
$row = $DB->get();
$modname = formattext($row["name"]);
$input = $row["input"];
$ordering = $row["ordering"];



echo " <h1 class=\"red\">$modname</h1>  ";


	

	
	



} //end error test

	

	


if (isset($ERROR))
{
	echo "$ERROR";
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletepage")
	{
	
	echo "<br><b>$phrase[14]</b><br><br> $_GET[linkname]<br><br>
	<a href=\"editpage.php?m=$m&amp;update=deletepage&amp;delpage=$delpage\">$phrase[12]</a> | <a href=\"editpage.php?m=$m\">$phrase[13]</a>";
	}





elseif (isset($_GET["event"]) && $_GET["event"] == "modulehistory")
	{
	
	$displayrestorebutton = "yes";
	
	echo "<a href=\"editpage.php?m=$m\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"  alt=\"$phrase[57]\"></a><br><br><h4>$phrase[782]</h4><br><table class=\"colourtable\" style=\"width:70%;padding:0.5em\">";
	

	
	$sql = "select * from page where m  = \"$m\" ";

$DB->query($sql,"editpage.php");

	while ($row = $DB->get())
	{
	$page_id = $row["page_id"];	

	$deleted[$page_id] = $row["deleted"];
	
	}

	
	
	$sql = "select * from content,page where m= '$m' and page.page_id  = content.page_id  and ( '11' < event ) and ( event < '15') order by updated_when desc";

	$DB->query($sql,"editpage.php");

	while ($row = $DB->get())
	{
	//$content_id = $row["content_id"];	
	$title = $row["title"];
	//$body = $row["body"];
	
	
	$page_id = $row["page_id"];
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
	
	if ($event == 14)
	{
	//page restored	
	echo "$phrase[799] <br><br><i><b>$title</b><br></i><br>
		";	
	}
	
	
	if ($event == 12)
	{
		//Page added
		echo "$phrase[797]  <br><i><b>$title</b></i><br>
		";
	}

	
	if ($event == 13)
	{
		//page deleted
		echo "$phrase[798]<br><br><i><b>$title</b></i>";
		if (isset($deleted) && array_key_exists($page_id,$deleted))
		{
		 if ($deleted[$page_id] == 1 && $displayrestorebutton == "yes")
		 {
			echo "<br><br><form action=\"editpage.php\"method=\"post\">

	<input type=\"hidden\" name=\"restorepage\" value=\"$page_id\">

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"restorepage\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[780]\">
	</form>
		";
		 $displayrestorebutton = "no";
		 }
		}
		}
		
	}
	
	echo "</table>";
	
}



elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "newpage")
	{
	
	echo "<a href=\"editpage.php?m=$m\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[57]\"  alt=\"$phrase[57]\"></a>
<br><br>
	<form action=\"editpage.php\" method=\"get\" ><fieldset><legend>$phrase[71]</legend><br>

						
						
						<input type=\"hidden\" name=\"m\" value=\"$m\">
						<table>
						<tr><td>$phrase[87]</td><td><input type=\"text\" name=\"page_title\" size=\"50\" maxlength=\"100\" value=\"$phrase[580]\"></td></tr>
						<tr><td>$phrase[855]</td><td><select name=\"displaydate\">
						<option value=\"1\">$phrase[12]</option>
						<option value=\"0\">$phrase[13]</option>
						
						</select>
					
						
						<tr><td>$phrase[88]</td><td><input type=\"checkbox\" name=\"published\" checked></td></tr>
						<tr><td>$phrase[925]</td><td><input type=\"checkbox\" name=\"feed\"></td></tr>
						<tr><td>$phrase[582]</td><td><input type=\"checkbox\" name=\"frontpage\"></td></tr>
						<tr><td>$phrase[89]</td><td> <select name=\"paraordering\">
								<option value=\"t\">$phrase[90]</option>
								<option value=\"a\">$phrase[91]</option>
								<option value=\"d\">$phrase[92]</option>
								<option value=\"c\" selected>$phrase[93]</option>
								</select></td></tr>
								
						<tr><td valign=\"top\">
						<input type=\"submit\" name=\"addpage\" value=\"$phrase[71]\" ></td></tr>
						
					
						</table></fieldset></form>";
	}


	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editproperties")
	{
		
	
	//diplays individual pages
	$sql = "SELECT  page_title, published, displaydate, name, page.frontpage as frontpage, page.ordering as ordering, feed FROM page, modules where modules.m = page.m and page_id = \"$page_id\"";
	$DB->query($sql,"editpage.php");
	$row = $DB->get();
	$page_title = $row["page_title"];	
	$modname= $row["name"];	
	$published= $row["published"];	
	$frontpage = $row["frontpage"];		
	$displaydate = $row["displaydate"];
	$paraordering = $row["ordering"];
	$feed = $row["feed"];	

	

	
	
	echo "
	
	<br><form action=\"editpage.php\" method=\"get\"><fieldset><legend>$phrase[581]</legend><br>

	<table>
	<tr><td><b>$phrase[87]</b> </td><td><input type=\"text\" name=\"page_title\" value=\"$page_title\" size=\"40\" maxlength=\"100\"></td></tr>
	<tr><td><strong>$phrase[855]</strong></td><td><select name=\"displaydate\">
						<option value=\"1\">$phrase[12]</option>
						<option value=\"0\"";
						if ($displaydate == 0) {echo " selected";}
						echo ">$phrase[13]</option>
						</select></td></tr>
					
	<tr><td><strong>$phrase[88]</strong></td><td>
	
	
	
	 <input type=\"checkbox\" name=\"published\"";
	if ($published == 1)
		{
		echo "checked";
		}
	echo "></td></tr>
	
					
	<tr><td><strong>$phrase[925]</strong></td><td>
	
	
	
	 <input type=\"checkbox\" name=\"feed\"";
	if ($feed == 1)
		{
		echo "checked";
		}
	echo "></td></tr>
	
	
	<tr><td><strong>$phrase[582]</strong></td><td>
	 <input type=\"checkbox\" name=\"frontpage\"";
	if ($frontpage == 1)
		{
		echo "checked";
		}
	echo "></td></tr>
	
	<tr><td valign=\"top\">";
	
	 echo "<strong>$phrase[89]</strong></td><td> <select name=\"paraordering\">
	<option value=\"t\"";
	if ($paraordering == "t") {echo " selected";}
	echo ">$phrase[584]</option>
	<option value=\"a\"";
	if ($paraordering == "a") {echo " selected";}
	echo ">$phrase[585]</option>
	<option value=\"d\"";
	if ($paraordering == "d") {echo " selected";}
	echo ">$phrase[586]</option>
	<option value=\"c\"";
	if ($paraordering == "c") {echo " selected";}
	echo ">$phrase[93]</option>
	</select>
	

	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"page_id\" value=\"$page_id\">
	<input type=\"hidden\" name=\"update\" value=\"updatetitle\"><br><br>
	<input type=\"submit\" name=\"updatetitle\" value=\"$phrase[16]\"></td></tr>
	</table></fieldset>
	</form><p></p>
	";	
	}

elseif (isset($page_id) && !isset($_REQUEST["updatetitle"]))
	{
	

	
	//include('includes/dynamic_array.php');
	
	$sql = "SELECT  page_title, displaydate, published, name, page.frontpage, page.ordering as ordering FROM page, modules where modules.m = page.m and page_id = \"$page_id\"";
	$DB->query($sql,"page.php");
	$row = $DB->get();
	$page_title = $row["page_title"];	
	$modname= $row["name"];	
	$published= $row["published"];
	$displaydate = $row["displaydate"];
	
			
	
	$paraordering = $row["ordering"];	
	

	
	if ($published <> 1) { echo "<br><div style=\"background:#F2F2F2;padding:6px\">
	<span style=\"color:#8C8C8C;font-size:large\">$phrase[339]</span><br>";}
	echo "<h2>$page_title</h2>";
	

//	if ($type == "f")
//		{
//		//display edit form
//		$editform = "yes";
//		include 'editform.php';
//		}
//
//	else
//		{
//		//display edit form
		
		$editcontent = "yes";
		include 'editcontent.php';
		//}
	
	}
	
else
	{
		
		
				//check revision history
						$changes = 0;
							$sql = "SELECT * FROM content, page where page.m = '$m' and page.page_id = content.page_id and event > '11'";
							$DB->query($sql,"editpage.php");
							while ($row = $DB->get()) 
						{
						$changes++;
						}
						



	//display index of module pages
	
				if ($ordering == "t")
	{
	$insert = "order by page.page_title";
	}
elseif ($ordering == "a")
	{
	$insert = "order by page.page_id asc";
	}
elseif ($ordering == "d")
	{
	$insert = "order by page.page_id desc";
	} 
elseif ($ordering == "c")
	{
	$insert = "order by page.pageorder";
	} 
else { $insert = "";}
	
				$sql = "SELECT * FROM page where m = \"$m\" and deleted = '0' $insert";
				
			
				$DB->query($sql,"editpage.php");
			
						
						$pagetotal = $DB->countrows();
						
						
				
						
					echo "
				
					
				
				<a href=\"editpage.php?m=$m&amp;event=newpage\"><img src=\"../images/page_add.png\" title=\"$phrase[71]\"  alt=\"$phrase[71]\"></a>";
					
					if ($changes > 0) {echo " <a href=\"editpage.php?m=$m&amp;event=modulehistory\">
					<img src=\"../images/clock.png\" title=\"$phrase[782]\"  alt=\"$phrase[782]\"></a>
					</a>";}
			
				echo "<br><br>$phrase[69]<br><br>
				
				
				
				<form method=\"post\" action=\"editpage.php\"><p>
			<strong>$phrase[674]</strong> &nbsp;&nbsp; <select name=\"pageordering\">
	<option value=\"t\"";
	if ($ordering == "t") {echo " selected";}
	echo ">$phrase[661]</option>
	<option value=\"a\"";
	if ($ordering == "a") {echo " selected";}
	echo ">$phrase[662]</option>
	<option value=\"d\"";
	if ($ordering == "d") {echo " selected";}
	echo ">$phrase[663]</option>
	<option value=\"c\"";
	if ($ordering == "c") {echo " selected";}
	echo ">$phrase[93]</option>
	</select> 
	
	<input type=\"submit\" name=\"updatemodule\" value=\"$phrase[16]\">
					<input type=\"hidden\" name=\"m\" value=\"$m\"><br>
						<input type=\"hidden\" name=\"modname\" value=\"$modname\"><br></p>
					</form>
					
					
					<br>
					
						<form action=\"\"><fieldset><legend>$phrase[587]</legend><br>
						";	
					$pagecount = 0;		
					while ($row = $DB->get()) 
						{
						$pagecount++;	
						$array_page_id[$pagecount]= $row["page_id"];
						$array_page_title[$pagecount] = $row["page_title"];
					
						$array_published[$pagecount] = $row["published"];
						$array_ordering[$pagecount]  = $row["ordering"];
						$array_pageorder[$pagecount]  = $row["page_id"];
						
						}
					if ($pagecount > 0) 	{echo "<table border=\"0\" cellpadding=\"4\">";}
					$pagecount = 0;		
					if (isset($array_page_id))
					{
					foreach ($array_page_id as $index => $page_id)
						{
						$pagecount++;
						$linkname[$index] = urlencode($array_page_title[$index] );	
					
						echo "<tr><td>";
						if ($array_page_title[$index] != "")
						{
						echo $array_page_title[$index];
						} else {echo $phrase[953];}
						
						echo "</td><td>";
						if ($array_published[$index] <> 1) { echo "<span style=\"color:#999999\">$phrase[339]</span>";} 
						echo "</td><td><a href=\"editpage.php?m=$m&amp;event=editproperties&amp;page_id=$page_id\">
						<img src=\"../images/page_properties.png\" title=\"$phrase[76]\"  alt=\"$phrase[76]\"></a></td>
						<td> <a href=\"editpage.php?m=$m&amp;event=editpage&amp;page_id=$page_id\">
						<img src=\"../images/page_edit.png\" title=\"$phrase[796]\"  alt=\"$phrase[796]\">				
						</a></td><td><a href=\"editpage.php?m=$m&amp;event=deletepage&amp;delpage=$page_id&amp;linkname=$linkname[$index]\"><img src=\"../images/page_delete.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a></td> <td>";
						
							if (($pagetotal > 0) && ($pagecount > 1)  && ($ordering == "c"))
							{
								
							
							foreach ($array_pageorder as $index => $value)
									{
									//echo "index is $index $value pagecount is $pagecount";
									if ($index == ($pagecount - 1))
										{
										
										$up = $array_pageorder;
										$up[$index] = $array_pageorder[$pagecount];
										$up[$pagecount] = $value;
										
										}
									}
							
							
							//print_r($up);	

							$pageup = implode(",",$up);
							echo "<a href=\"editpage.php?m=$m&amp;pagereorder=$pageup\"><img src=\"../images/up.png\" title=\"$phrase[77]\"  alt=\"$phrase[77]\"></a>";
							
							
							
							
							}
						
						
						
						
						
						echo "</td><td>"; 
						
						if (($pagetotal > 0) && ($pagecount < $pagetotal) && ($ordering == "c"))
							{
							
							//echo "<br>";
						
							
							
							
								foreach ($array_pageorder as $index => $value)
									{
									//echo "$array_order[$index] <br>";
									if ($index == ($pagecount))
										{
										$down = $array_pageorder;
										$temp = $down[$index];
										$down[$index] = $down[$index + 1];
										$down[$index + 1] = $temp;
										}
									}
								
								
							
							$pagedown = implode(",",$down);
							echo "<a href=\"editpage.php?m=$m&amp;pagereorder=$pagedown\"><img src=\"../images/down.png\" title=\"$phrase[78]\"  alt=\"$phrase[78]\"></a>";
							}
						
						
						echo "</td></tr>";
						
						}
						}
					if ($pagecount > 0) 	{echo "</table> ";}
					else {
						echo "<p style=\"color:Red\">$phrase[841]</p><p>$phrase[842] <a href=\"editpage.php?m=$m&amp;event=newpage\"><img src=\"../images/page_add.png\" title=\"$phrase[71]\"  alt=\"$phrase[71]\"></a><p>";
					}
					
					
					
				
					
					
					 echo "</fieldset></form><br><br><br>";
					
					
					
				


	}

	
	
echo "
	
  	<script type=\"text/javascript\">
addEvent(window, 'load', loadfeeds);
	</script>
	
	
		
		</div></div>";
		//end contentbox
		
	include("../includes/rightsidebar.php");

include ("../includes/footer.php");

?>

