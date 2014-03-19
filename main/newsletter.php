<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	

$integers[] = "content_id";
$integers[] = "issue_id";
$integers[] = "temp_id";






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
	



	$now = time();
$ip = ip("pc");
	

		
			 
	  	include("../includes/leftsidebar.php");   
		
		
	 
		
			

	  	echo "<div id=\"content\" style=\"text-align:left\"><div>";
	

	$sql = "select name, input from modules where m = \"$m\"";
$DB->query($sql,"noticeboard.php");
$row = $DB->get();
$modname = formattext($row["name"]);
$input = $row["input"];



echo " <h1 >$modname</h1>  ";


$newissue = "no";

if (!isset($ERROR))
{

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "add")
	{
		

		 $issue_name = $DB->escape($_REQUEST["issue_name"]);
		
		 
		 $sql = "INSERT INTO newsletter_issues VALUES(NULL,'$issue_name','$temp_id','$m')"; 
		//echo $sql;
   		 $DB->query($sql,"newsletter.php");
   			
		$newissue = "yes";
		$issue_id = $DB->last_insert();
		
		
	
	}
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addparagraph")
	{
		 $content_title = $DB->escape($_REQUEST["content_title"]);
		 $content_text = $DB->escape($_REQUEST["content_text"]);
		 
		 $sql = "INSERT INTO newsletter_content VALUES(NULL,'$content_title','$content_text','$issue_id','0')"; 
	
   		 $DB->query($sql,"newsletter.php");
   			
	}

	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "editparagraph")
	{
		 $content_title = $DB->escape($_REQUEST["content_title"]);
		 $content_text = $DB->escape($_REQUEST["content_text"]);

		  
		 $sql = "update newsletter_content set content_title = '$content_title', content_text = '$content_text' where content_id = '$content_id'"; 
		//echo $sql;
   		 $DB->query($sql,"newsletter.php");
   			
	
		
		
	
	}
	
	
	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "resort")
{
//reorder using button
$order = $_REQUEST["order"];
foreach ($order as $index => $value)
		{
		$value = $DB->escape($value);	
		$index = $DB->escape($index);
		
		
		$sql = "update newsletter_content set content_order = '$value' WHERE content_id = '$index'";	
		//echo "$sql <br> ";
		$DB->query($sql,"newsletter.php");	
		}	

}
	
	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "reorder" )
	{
	//resort using arrows
	
	//reorders paragraphs on the page
	$reorder = explode(",",$_REQUEST["reorder"]);
	$index = $DB->escape($index);	
	$value = $DB->escape($value);
	
	foreach ($reorder as $index => $value)
		{
		
		$sql = "update newsletter_content set content_order = '$index' WHERE content_id = '$value'";	
		$DB->query($sql,"newsletter.php"); 
		}
	
	}
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete")
	{

	$sql = "delete  from newsletter_content  where content_issue = '$issue_id'"; 
	$DB->query($sql,"menu.php");
		
		
	$sql = "delete from newsletter_issues where issue_id = '$issue_id'";
	$DB->query($sql,"menu.php");
			

	
		}
		

		
		
		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletepara")
	{

	$sql = "delete from newsletter_content  where content_id = '$content_id' and content_issue = '$issue_id'"; 
	$DB->query($sql,"menu.php");
		}		
		
		
		
		
		
		
		
		

}



if (isset($ERROR))
{
	echo "$ERROR";
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addparagraph")

{
	
	echo "<br><form method=\"POST\" action=\"newsletter.php\"><fieldset ><legend>$phrase[73]</legend>
		<table >
	<tr><td>
						<b>$phrase[74]</b><br>
						<input type=\"text\" name=\"content_title\"  size=\"50\" maxlength=\"200\">
						</td></tr><tr><td><br>
						<b>$phrase[75]</b><br>
						<textarea name=\"content_text\" class=\"ckeditor\" id=\"body\" cols=\"70\" rows=\"20\" ></textarea><br><br>
						
						<input type=\"submit\" value=\"$phrase[73]\" name=\"submit\">
							<input type=\"hidden\" name=\"issue_id\" value=\"$issue_id\"><br>
								<input type=\"hidden\" name=\"update\" value=\"addparagraph\">
								<input type=\"hidden\" name=\"event\" value=\"edit\">
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






elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editparagraph")

{
	
		$sql = "SELECT content_id, content_title, content_text FROM newsletter_content where content_id = \"$content_id\"";
		$DB->query($sql,"page.php");
		$row = $DB->get();
		$content_title = $row["content_title"];
		$content_text = $row["content_text"];
		
				
	echo "<br><form method=\"POST\" action=\"newsletter.php\"><fieldset ><legend>$phrase[81]</legend>
		<table >
	<tr><td>
						<b>$phrase[81]</b><br>
						<input type=\"text\" name=\"content_title\" value=\"$content_title\" size=\"50\" maxlength=\"200\">
						</td></tr><tr><td><br>
						<b>$phrase[75]</b><br>
						<textarea name=\"content_text\" class=\"ckeditor\" id=\"body\" cols=\"70\" rows=\"20\" >$content_text</textarea><br><br>
						
						<input type=\"submit\" value=\"$phrase[81]\" name=\"submit\">
						<input type=\"hidden\" name=\"issue_id\" value=\"$issue_id\">
						<input type=\"hidden\" name=\"content_id\" value=\"$content_id\">
						<input type=\"hidden\" name=\"update\" value=\"editparagraph\">
						<input type=\"hidden\" name=\"event\" value=\"edit\">
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









elseif ((isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit") || $newissue == "yes")
{
	
	
	$sql = "select issue_name from newsletter_issues where issue_id = '$issue_id'";
	$DB->query($sql,"page.php");
	$row = $DB->get();
	$issue_name = $row["issue_name"];	
	echo "<h2>$issue_name</h2>
	<form action=\"newsletter.php\" method=\"post\">
	<a href=\"newsletter.php?m=$m\" style=\"padding-right:1em\"><img src=\"../images/page_white_stack.png\" title=\"$phrase[1044]\"  alt=\"$phrase[1044]\"></a> 
	
	<a href=\"newsletter_preview.php?m=$m&amp;issue_id=$issue_id\"><img src=\"../images/search.png\" title=\"$phrase[949]\"  alt=\"$phrase[949]\"></a> 
<input type=\"submit\" value=\"resort\" name=\"submit\" style=\"margin:1em\">	
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"issue_id\" value=\"$issue_id\">
<input type=\"hidden\" name=\"update\" value=\"resort\">
<input type=\"hidden\" name=\"event\" value=\"edit\">

<a href=\"newsletter.php?m=$m&amp;issue_id=$issue_id&amp;event=addparagraph\"><img src=\"../images/paragraph_add.png\" title=\"$phrase[73]\"  alt=\"$phrase[73]\"></a> 
";
	
	
		$sql = "SELECT content_id, content_title, content_text FROM newsletter_content where content_issue = '$issue_id' order by content_order, content_id";
		
		$DB->query($sql,"page.php");

					
					//these two variables are for up and down arrows that allow reordering of a page
					$total = 0;
					$counter = 0;
					$pagechange = 0;
					
					while (	$row = $DB->get()) 
						{
						
						$array_order[$counter]  = $row["content_id"];
						$array_content_id[$counter] = $row["content_id"];
						$array_title[$counter] = formattext($row["content_title"]);
						
							if ($input == 0)
						{
						$array_text[$counter] = formattext($row["content_text"]);
						}
						else 
						{
						$array_text[$counter] = formattext_html($row["content_text"]);	
						}
						$counter++;
						$total++;
						}
	
					//print_r($array_content_id);
						
					$counter = 0;
					$count =0;
					if (isset($array_content_id))
					{
					foreach ($array_content_id as $index => $content_id)
						{
						$count++;	
						echo "<h3>$array_title[$index]</h3>
						<p>$array_text[$counter]</p><br>
						<input type=\"text\" name=\"order[$content_id]\" value=\"$count\" size=\"2\">
						";
						
						
						
						
						
						
							if (($total > 0) && ($counter > 0))
							{
							
							//Change order array so the paragraph can be moved up page
							//echo "<p></p>";
						
								
							
							foreach ($array_order as $ind => $value)
									{
									//echo "$array_order[$index] $counter<br>";
									if ($ind == ($counter -1))
										{
										$up = $array_order;
										$up[$ind] = $array_order[$counter];
										$up[$counter] = $value;
										}
									}
							
							
							//	print_r($up);

							$up = implode(",",$up);
							echo "<a href=\"newsletter.php?m=$m&amp;issue_id=$issue_id&amp;event=edit&amp;update=reorder&amp;reorder=$up\"><img src=\"../images/up.png\" title=\"$phrase[77]\"  alt=\"$phrase[77]\"></a>&nbsp;&nbsp;&nbsp;";
							
							
							
							
							}
						
						
						
						if (($total > 0) && ($counter < $total - 1) )
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
							echo "<a href=\"newsletter.php?m=$m&amp;issue_id=$issue_id&amp;event=edit&amp;reorder=$down&amp;update=reorder\"><img src=\"../images/down.png\" title=\"$phrase[78]\"  alt=\"$phrase[78]\"></a>&nbsp;&nbsp;&nbsp;";
							}
						
						
						
						
						
						
						
						
						
											echo "&nbsp;&nbsp;&nbsp;<a href=\"newsletter.php?m=$m&amp;event=editparagraph&amp;content_id=$content_id&amp;issue_id=$issue_id\"><img src=\"../images/page_white_edit.png\" title=\"$phrase[81]\"  alt=\"$phrase[81]\"></a>	
											
									<a href=\"newsletter.php?m=$m&amp;content_id=$content_id&amp;event=deletepara&amp;issue_id=$issue_id\"><img src=\"../images/page_remove.png\" title=\"$phrase[650]\"  alt=\"$phrase[650]\"></a>
						";	
						$counter++;
						}
	
					}
					
					echo "</form>";	
}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delete")
	{
	
	echo "<br><b>$phrase[14]</b><br><br><br><br>
	<a href=\"newsletter.php?m=$m&amp;update=delete&amp;issue_id=$issue_id\">$phrase[12]</a> | <a href=\"newsletter.php?m=$m\">$phrase[13]</a>";
	}
	
	
	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletepara")
	{
	
	echo "<br><b>$phrase[14]</b><br><br><br><br>
	<a href=\"newsletter.php?m=$m&amp;update=deletepara&amp;content_id=$content_id&amp;event=edit&amp;issue_id=$issue_id\">$phrase[12]</a> | <a href=\"newsletter.php?m=$m&amp;event=edit&amp;issue_id=$issue_id\">$phrase[13]</a>";
	}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "send")
	{
	
	echo "<h2>$phrase[639]</h2>
	
	
	<a href=\"newsletter.php?m=$m\">$phrase[34]</a>
	";
	
	$sql = "select temp_subject, issue_name, temp_from, temp_address from newsletter_templates, newsletter_issues where temp_id = issue_template and issue_id = '$issue_id'";
		$DB->query($sql,"newsletter.php");
		$row = $DB->get();
		$temp_subject  = $row["temp_subject"];
		$temp_from  = $row["temp_from"];
		$temp_address  = $row["temp_address"];
		$issue_name  = $row["issue_name"];
		
		$newsletter = newsletter($input,$issue_id,$DB);
		

	$random_hash = md5(date('r', time())); 
	
 

	//$encoded_newsletter = chunk_split(base64_encode($newsletter));
	

$headers .= "\r\nContent-Type: multipart/alternative; boundary=\"PHP-alt-".$random_hash."\"";
/*
//$message = "--PHP-alt-<?php echo $random_hash; ?> 
//Content-Type: text/plain; charset=\"iso-8859-1\"
//Content-Transfer-Encoding: 7bit

//Email newsletter

//$issue_name

//--PHP-alt-$random_hash
//Content-Type: text/html; charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit

$newsletter
--PHP-alt-$random_hash";
	*/
	
$headers  = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= "From: $temp_from \n";	

	//echo "$temp_address, $issue_name, $headers";
	//echo $newsletter;
	//
	ini_set('sendmail_from', $temp_from);
	send_message($DB,$temp_address, $issue_name, $newsletter,$headers);
	$now = time();
	$ip = ip("pc");
	$sql = "insert into newsletter_log values(NULL,'$now','$issue_name','$ip','$_SESSION[username]')";
	$DB->query($sql,"newsletter.php");
	}
	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "history")
{
	
	echo "<h2>$phrase[950]</h2>";
	
	$sql = "select log_id, log_name, log_date,log_ip, log_user from newsletter_log order by log_id";
	$DB->query($sql,"newsletter.php");
	
	echo "<table class=\"colourtable\">";
	while ($row = $DB->get())
	{
		$log_name = $row["log_name"];
		$log_date = strftime("%x %X",$row["log_date"]);
		$log_ip = $row["log_ip"];
		$log_user = $row["log_user"];
		echo "<tr><td>$log_date</td><td>$log_name</td><td>$log_user</td><td>$log_ip</td></tr>";
	}
	echo "</table>";
	
}
	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "add")
{
	
	
	
		$sql = "select temp_id, temp_name, temp_file, temp_subject, temp_address, temp_from from newsletter_templates where temp_m = '$m'";
		$DB->query($sql,"newsletter.php");
		$num = $DB->countrows();
		
		if ($num == 0) {echo "<span style=\"color:red\">$phrase[947]</span>";} 
			
			while ($row = $DB->get())
			{
			$temp_id[] = $row["temp_id"];	
			$temp_name[] = $row["temp_name"];		
				
			}
	
		if ($num != 0)
		{
	echo "
	<form method=\"post\" action=\"newsletter.php\" >
	<fieldset>
	<legend>$phrase[176]</legend>
	<table >
	<tr><td class=\"formlabels\">$phrase[141]</td><td style=\"text-align:left\"><input type=\"text\" name=\"issue_name\" size=\"50\"></td></tr>
	<tr><td class=\"formlabels\">$phrase[948]</td><td style=\"text-align:left\"><select name=\"temp_id\">";
			
	if (isset($temp_id))	
		{	
			foreach($temp_id as $key => $temp)

			{
			echo "<option value=\"$temp\">$temp_name[$key]</option>";
			}
		}
			
	echo "</select>
		</td></tr>

	<tr><td></td><td style=\"text-align:left\">
	<input type=\"submit\" value=\"$phrase[176]\">
	
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"add\">
	</td></tr>
	

	</td></tr>
	</table>
	</fieldset>
	
	
	
	</form>";
		}
	
			
			
	
}


else {
echo 	"<a href=\"newsletter.php?event=add&amp;m=$m\"><img src=\"../images/add.png\" title=\"$phrase[176]\"></a> 
<a href=\"newsletter.php?event=history&amp;m=$m\"><img src=\"../images/clock.png\" title=\"$phrase[950]\"></a>";
	


	$sql = "select issue_id, issue_name from newsletter_issues where issue_m = '$m'";
			$DB->query($sql,"page.php");
			$num = $DB->countrows();
			if ($num > 0) {
				
				echo "<form action=\"newsletteradmin.php\" method=\"post\"><br><table class=\"colourtable\">";
			}
			while ($row = $DB->get())
			{
			$issue_id = $row["issue_id"];	
			$issue_name = $row["issue_name"];	
		
			
			echo "<tr><td>$issue_name</td><td><a href=\"newsletter_preview.php?m=$m&amp;issue_id=$issue_id\"><img src=\"../images/search.png\" title=\"$phrase[949]\"  alt=\"$phrase[949]\"></a></td>
			<td><a href=\"newsletter.php?m=$m&amp;event=edit&amp;issue_id=$issue_id\"><img src=\"../images/page_white_edit.png\" title=\"$phrase[26]\"></a></td>
			<td><a href=\"newsletter.php?m=$m&amp;event=delete&amp;issue_id=$issue_id\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"></a></td>
			<td><a href=\"newsletter.php?m=$m&amp;event=send&amp;issue_id=$issue_id\"\"><img src=\"../images/window_next.png\" title=\"$phrase[63]\"  alt=\"$phrase[63]\"></a></td>
			
			</tr>";
				
			}
			
				if ($num > 0) {
				
				echo "</table>
				</form>
				";
			}
			
	

}
	//end contentbox
		echo "</div></div>";
		
	 
	     

	     	 
	  	include("../includes/rightsidebar.php");   
		
		
	


include ("../includes/footer.php");
?>