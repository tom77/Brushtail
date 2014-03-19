<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");



include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div>";


if (isset($_REQUEST["m"]))
	{	
	
	if (isinteger($_REQUEST["m"]))
		{	
		$m = $_REQUEST["m"];	
		$access->check($m);
	
	if ($access->thispage < 3)
			{
		
			$ERROR  =  "<div style=\"font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
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
	$ERROR  =  $phrase[72];
}	


	
if (isset($_REQUEST["fieldset"]))
{
if ((isinteger($_REQUEST["fieldset"])))
	{
	$fieldset = $_REQUEST["fieldset"];	
	}
	else 
	{$ERROR  =  "$phrase[72]";	}	
	
}


if (isset($_REQUEST["fieldno"]))
{
if ((isinteger($_REQUEST["fieldno"])))
	{
	$fieldno = $_REQUEST["fieldno"];	
	}
	else 
	{$ERROR  =  "$phrase[72]";	}	
	
}

if (isset($ERROR))
	{
	echo "$ERROR";
	}
else 
	{
		
		$access->cmenu();
	
		
	$sql = "select * from modules where m = '$m'";
	$DB->query($sql,"editform.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);
	$formtype = $row["type"];
	echo "<h1 class=\"red\">$modname</h1>
		<a href=\"editform.php?m=$m\">$phrase[686]</a>";
	if ($formtype == "h")
	{
	echo " | <a href=\"edithelpdesk.php?m=$m&amp;event=cat\">$phrase[884]</a> |  <a href=\"edithelpdesk.php?m=$m&amp;event=options\">$phrase[696]</a>";
	}
		
	echo "<h2>$phrase[686]</h2>";
			//user has edit permissions		
			
			
			
			
if (isset($_GET["update"]) && $_GET["update"] == "forder")
	{
	//reorders paragraphs on the page

	$fieldreorder = explode(",",$_GET["fieldreorder"]);
	

	
	foreach ($fieldreorder as $index => $value)
		{
		if (isinteger($index) && isinteger($value))
			{		
			$sql = "update formfields set ranking = \"$index\" WHERE fieldno = \"$value\"";	
			$DB->query($sql,"editform.php");
			
			}
		else 
		{
		$ERROR = $phrase[72];
				}
		}
	
	
	
	}	
	
	
if (isset($_POST["update"]) && $_POST["update"] == "updatebanner")
	{										
	
	$banner = $DB->escape($_POST["banner"]);
	if (!isset($ERROR))
	{
	$sql = "update forms set banner = \"$banner\" where module=\"$m\"";
	
	$DB->query($sql,"editform.php");
	} else {$ERROR = $phrase[72];} 
	}


if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "sorder")
	{
	//reorders form fieldsets
	
	$setreorder = explode(",",$_REQUEST["setreorder"]);
	
	foreach ($setreorder as $index => $value)
		{
		if (isinteger($value))
		{
		$sql = "update fieldset set ranking = \"$index\" WHERE fieldset = \"$value\"";	
		$DB->query($sql,"editform.php");
		}
		else {$ERROR = $phrase[72];}
		}
		
	
	
	
	}
	
	
	
	
		if (isset($_REQUEST["forder"]) && !isset($ERROR))
	{
	//reorders form fields
	$fieldreorder = explode(",",$_REQUEST["fieldreorder"]);
	
	foreach ($fieldreorder as $index => $value)
		{
		if (isinteger($value))
		{
		$sql = "update formfields set ranking = \"$index\" WHERE fieldno = \"$value\"";	
		$DB->query($sql,"editform.php");
		}
		else {$ERROR = $phrase[72];}
		}
		
	
	
	
	}
	
	
	if (isset($_POST["update"]) && $_POST["update"] == "updatefield")
	{										
	
	$menu = $DB->escape($_POST["menu"]);
	$label = $DB->escape($_POST["label"]);
	$comment = $DB->escape($_POST["comment"]);
	$compulsory = $DB->escape($_POST["compulsory"]);
	$output = $DB->escape($_POST["output"]);
	
	
	if (!isset($ERROR))
	{
	
	$sql = "update formfields set label = '$label',menu = '$menu', compulsory = '$compulsory', comment = '$comment', output = '$output' where fieldno=\"$fieldno\"";
	$DB->query($sql,"editform.php");
	} else {$ERROR = $phrase[72];} 
	}
	
	
	
	if (isset($_POST["update"]) && $_POST["update"] == "updatefieldset")
	{										
	
	$ordering = $DB->escape($_POST["ordering"]);
	$leg = $DB->escape($_POST["legend"]);

	if (!isset($ERROR))
	{
	
	$sql = "update fieldset set legend = '$leg', fieldordering = '$ordering' where fieldset=\"$fieldset\"";
	$DB->query($sql,"editform.php");
	} else {$ERROR = $phrase[72];} 
	}
	
	
	
	
	if (isset($_POST["update"]) && $_POST["update"] == "addset")
	{
	
	$newlegend = $DB->escape($_POST["legend"]);
	$ordering = $DB->escape($_POST["ordering"]);
	
		if (!isset($ERROR))
		{	
		$sql = "insert into fieldset values (NULL,'$m','1','$ordering','$newlegend') ";
		$DB->query($sql,"editform.php");
		
	
		}
	}
	
if (isset($_POST["update"]) && $_POST["update"] == "addfield")
	{
	
	$fieldtype = $DB->escape($_POST["fieldtype"]);
	$label = $DB->escape($_POST["label"]);
	$compulsory = $_POST["compulsory"];
	$comment = $DB->escape($_POST["comment"]);
	$output = $DB->escape($_POST["output"]);

	
	
	if (isset($_POST["values"]))
	{
		$values = $DB->escape($_POST["values"]);
	}
	else {$values = "";}
		
		if (isinteger($compulsory))
		{	
		$sql = "insert into formfields values (NULL,'$fieldset','$fieldtype','1','$label','$compulsory','$values','$comment','$output') ";
		
		$DB->query($sql,"editform.php");
		
		
		}
	
	}
	
if (isset($_GET["update"]) && $_GET["update"] == "deletefield")
	{
	
	if (!isset($ERROR))
	{
	$sql = "delete from formfields where fieldno = \"$fieldno\" ";
	
	$DB->query($sql,"editform.php");
	} else {$ERROR = $phrase[72];}  
		  
   
	
	}	

	if (isset($_GET["update"]) && $_GET["update"] == "deletefieldset")
	{
	
	if (!isset($ERROR))
	{
	$sql = "delete from fieldset where fieldset = \"$fieldset\" ";

	$DB->query($sql,"editform.php");
	} else {$ERROR = $phrase[72];}  
		  
   
	
	}	

if (isset($_POST["update"]) && $_POST["update"] == "email")
	{
	$subject = $DB->escape($_POST["subject"]);
	$email = $DB->escape($_POST["email"]);
	$emailfrom = $DB->escape($_POST["emailfrom"]);
	$cc_user = $DB->escape($_POST["cc_user"]);

	if (!isset($ERROR))
	{
	
	$sql = "update forms set email = '$email', subject = '$subject', emailfrom = '$emailfrom', cc_user = '$cc_user' where module=\"$m\"";
//	echo $sql;
	
	$DB->query($sql,"editform.php");
	} else {$ERROR = $phrase[72];} 
		
		
	}
	
	if (isset($ERROR))
	{
		echo $ERROR;
	}
	
	
	elseif (isset($_GET["event"]) && $_GET["event"] == "editbanner")
	{
	$sql = "select * from forms where module = '$m'";
	$DB->query($sql,"editform.php");
	
	$row = $DB->get();
	$banner = $row["banner"];
	
	echo "<div >
	<form action=\"$_SERVER[PHP_SELF]\" method=\"post\" >
	<fieldset><legend>$phrase[554]</legend>
	<br>$phrase[555]<br><br>

	
	<strong>$phrase[556]</strong><br>
<textarea name=\"banner\" cols=\"60\" rows=\"10\" >$banner</textarea><br>

	
		
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"updatebanner\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">

	</fieldset></form></div><br>";
	
	
	}
	elseif (isset($_GET["event"]) && $_GET["event"] == "editfieldset")
	{
	$sql = "select * from fieldset where fieldset = '$fieldset'";
	$DB->query($sql,"editform.php");
	
	$row = $DB->get();
	$legend = $row["legend"];
	$fieldordering = $row["fieldordering"];	
	
	echo "
	<div >
	<form action=\"$_SERVER[PHP_SELF]\" method=\"post\" >
	<fieldset><legend>$phrase[570]</legend>
	<br>
<table cellspacing=\"10\" style=\"text-align:left;\">
	
	<tr><td><strong>$phrase[571] </strong></td><td>
<input type=\"text\" name=\"legend\" size=\"80\" maxlength=\"200\" value=\"$legend\"></td></tr>
 <tr><td><strong>$phrase[572]</strong></td><td>
 <input type=\"radio\" name=\"ordering\" value=\"c\"";
 if ($fieldordering == "c") {echo "checked";}
 echo "> $phrase[573]<br>
 <input type=\"radio\" name=\"ordering\" value=\"a\"";
  if ($fieldordering == "a") {echo "checked";}

echo " > $phrase[574]</td></tr>
<tr><td></td><td>
	<input type=\"hidden\" name=\"fieldset\" value=\"$fieldset\">
		
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"updatefieldset\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\"></td></tr>
</table>
	</fieldset></form></div>";	
		
	}
	


	elseif (isset($_GET["event"]) && $_GET["event"] == "editfield")
	{
	$sql = "select * from formfields where fieldno = '$fieldno'";
	$DB->query($sql,"editform.php");
	
	$row = $DB->get();
	$label = $row["label"];
	$compulsory = $row["compulsory"];
	
	$menu = $row["menu"];
	$comment = $row["comment"];
	$output = $row["output"];
	$type = $row["type"];
	
	
echo "

<div >
	<form action=\"$_SERVER[PHP_SELF]\" method=\"post\" >
	<fieldset><legend>$phrase[575]</legend>

<table cellspacing=\"7\" style=\"text-align:left;\">
	
	<tr><td align=\"right\"><strong>$phrase[109]</strong></td><td>
<input type=\"text\" name=\"label\" size=\"80\" maxlength=\"200\" value=\"$label\"></td></tr>
<tr><td align=\"right\"><strong>$phrase[110]</strong></td><td>
 <input type=\"radio\" name=\"compulsory\" value=\"1\"";
 if ($compulsory == 1) {echo "checked";}
 echo "> $phrase[12]<br>
 <input type=\"radio\" name=\"compulsory\" value=\"0\"";
  if ($compulsory == 0) {echo "checked";}

echo " > $phrase[13]</td></tr>

<tr><td align=\"right\"><strong>$phrase[766]</strong></td><td>
 <input type=\"radio\" name=\"output\" value=\"1\"";
 if ($output == 1) {echo "checked";}
 echo "> $phrase[12]<br>
 <input type=\"radio\" name=\"output\" value=\"0\"";
  if ($output == 0) {echo "checked";}

echo " > $phrase[13]</td></tr>
 <tr><td align=\"right\"><strong>$phrase[576]</strong></td><td><textarea name=\"comment\" cols=\"60\" rows=\"4\">$comment</textarea></td></tr>
";
if ($type == "m" || $type == "r")
{ echo "<tr><td align=\"right\"><strong>$phrase[577]</strong></td><td><textarea name=\"menu\" cols=\"60\" rows=\"8\">$menu</textarea></td></tr>";}

echo "

<tr><td></td><td><input type=\"hidden\" name=\"values\" value=\"\"></td></tr>
<tr><td></td><td>
	<input type=\"hidden\" name=\"fieldno\" value=\"$fieldno\">
		
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"updatefield\">";
if ($type == "t" || $type == "a")  {echo "<input type=\"hidden\" name=\"menu\" value=\"\">";}

echo "<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\"></td></tr></table>
	</fieldset></form></div><br>";
	}


	
elseif (isset($_GET["event"]) && $_GET["event"] == "addset")
	{
print <<<EOF

	<div class="swouter">
	<form action="editform.php" method="post" class="swinner">
	<fieldset><legend>$phrase[578]</legend><br>
<br>

	<table cellspacing="7" style="text-align:left">
	<tr><td><strong>$phrase[571]</strong></td><td><input type="text" name="legend" maxlength="200"></td></tr>
<tr><td><strong>$phrase[572]</strong></td><td>
 <input type="radio" name="ordering" value="a"> $phrase[574]<br>
 <input type="radio" name="ordering" value="c" checked> $phrase[573]</td></tr>


<tr><td></td><td>
	<input type="hidden" name="m" value="$m">
	<input type="hidden" name="update" value="addset">

<input type="submit" name="submit" value="$phrase[578]"></td></tr></table>
</fieldset>
	</form></div>
	
EOF;
	
	}

elseif (isset($_GET["event"]) && $_GET["event"] == "deletefield")
	{	

	echo "<b>$phrase[14]</b><br><br>
	<a href=\"editform.php?m=$m&amp;update=deletefield&amp;fieldno=$fieldno\">$phrase[12]</a> | <a href=\"editform.php?m=$m\">$phrase[13]</a>";
	
		
		
	}
	
	
	
	
elseif (isset($_GET["event"]) && $_GET["event"] == "deletefieldset")
	{	
	$sql = "select count(*) as num from formfields where fieldset = '$fieldset'";
	$DB->query($sql,"editform.php");
	$row = $DB->get();
	if ($row["num"] > 0)
	{
		warning("$phrase[579]");
	}
	else {
	echo "<b>$phrase[14]</b><br><br>
	<a href=\"editform.php?m=$m&amp;update=deletefieldset&amp;fieldset=$fieldset\">$phrase[12]</a> | <a href=\"editform.php?m=$m\">$phrase[13]</a>";
	}
		
		
	}
elseif (isset($_GET["event"]) && $_GET["event"] == "addfield")
	{
print <<<EOF

	
	<script type="text/javascript">
	
function menu(){
document.getElementById('labelspan').innerHTML = '<b>Menu values</b>';
document.getElementById('menuvalues').innerHTML = '<textarea name="values" rows="5" cols="60"></textarea><br>Separate each value with a line break.';
document.getElementById('required').innerHTML = '<input type="radio" name="compulsory" value="1" > $phrase[12]<br> <input type="radio" name="compulsory" value="0" checked> $phrase[13]';
}

function radio(){
document.getElementById('labelspan').innerHTML = '<b>Menu values</b>';
document.getElementById('menuvalues').innerHTML = '<textarea name="values" rows="5" cols="60"></textarea><br>Separate each value with a line break.';
document.getElementById('required').innerHTML = '<input type="radio" name="compulsory" value="1"> $phrase[12]<br> <input type="radio" name="compulsory" value="0" checked> $phrase[13]';
}

function text(){
document.getElementById('labelspan').innerHTML = '';
document.getElementById('menuvalues').innerHTML = '<input type="hidden" name="values" value=""';
document.getElementById('required').innerHTML = '<input type="radio" name="compulsory" value="1"> $phrase[12]<br> <input type="radio" name="compulsory" value="0" checked> $phrase[13]';

}

function area(){
document.getElementById('labelspan').innerHTML = '';
document.getElementById('menuvalues').innerHTML = '<input type="hidden" name="values" value=""';
document.getElementById('required').innerHTML = '<input type="radio" name="compulsory" value="1"> $phrase[12]<br> <input type="radio" name="compulsory" value="0" checked> $phrase[13]';

}
</script>
<div >
	<form action="editform.php" method="post" >
	<fieldset><legend>$phrase[100]</legend><br>
<br>

	<table cellspacing="7" style="text-align:left">
	
	<tr><td align="right"><strong>$phrase[109]</strong></td><td align="left">
<input type="text" name="label" size="40" maxlength="200"></td></tr>
<tr><td align="right"><strong>$phrase[110]</strong></td><td align="left" id="required">
 <input type="radio" name="compulsory" value="1"> $phrase[12]<br>
 <input type="radio" name="compulsory" value="0" checked> $phrase[13]</td></tr>
 
 <tr><td align="right"><strong>$phrase[766]</strong></td><td align="left">
 <input type="radio" name="output" value="1" checked> $phrase[12]<br>
 <input type="radio" name="output" value="0"> $phrase[13]</td></tr>
 
 
 
 <tr><td align="right"><strong>$phrase[576]</strong></td><td><textarea name="comment" cols="40"></textarea></td></tr>
<tr><td align="right"><strong>$phrase[111]</strong></td><td align="left">

<input type="radio" name="fieldtype" value="t" checked onClick="text()"> $phrase[105] <br>
<input type="radio" name="fieldtype" value="a" onClick="area()"> $phrase[106] <br>
<input type="radio" name="fieldtype" value="m" onClick="menu()"> $phrase[107]<br>
<input type="radio" name="fieldtype" value="r" onClick="radio()"> $phrase[108] <br>
<input type="radio" name="fieldtype" value="d" onClick="text()"> $phrase[186] </td></tr>




<tr><td><span id="labelspan"></td><td><span id="menuvalues"><input type="hidden" name="values" value=""></span></td></tr>
<tr><td></td><td align="left">

		<input type="hidden" name="fieldset" value="$fieldset">
	<input type="hidden" name="m" value="$m">
	<input type="hidden" name="update" value="addfield">

<input type="submit" name="submit" value="$phrase[100]"></td></tr></table></fieldset>
	</form></div>
	
EOF;
	
	}
else
	{



$sql = "select * from forms where module = \"$m\"";




$DB->query($sql,"editform.php");

$row = $DB->get();
$email = formattext($row["email"]);
$banner = nl2br(trim($row["banner"]));
$subject = $row["subject"];
$from = $row["emailfrom"];
$cc_user = $row["cc_user"];
//$formtype = $row["type"];

 echo "


<div >





<p >$banner <br><br><br>
<a href=\"editform.php?m=$m&amp;event=editbanner\"><img src=\"../images/notes_edit.png\" title=\"$phrase[554]\" alt=\"$phrase[554]\"></a> 
 
 <a href=\"editform.php?m=$m&amp;event=addset\"><img src=\"../images/page_add.png\" title=\"$phrase[578]\"  alt=\"$phrase[578]\"></a><br>
 </p>
</div>



<div >

<form action=\"editpage.php\" method=\"post\" >";

$emailarray = array();


$sql = "select * from fieldset where form = \"$m\" order by ranking";
$DB->query($sql,"editform.php");


$counter = 0;
while ($row = $DB->get()) 
		{
		$counter++;
		$fieldsetno[] = $row["fieldset"];
		$legend[] = $row["legend"];
		$fieldordering[] = $row["fieldordering"];
		$ranking[] = $row["ranking"];
		$set_ranking[$counter] = $row["fieldset"];
		}	
	
	$settotal = $counter;	
		
	$setcounter = 0;	
	if (isset($fieldsetno))
	{
	foreach($fieldsetno as $index => $fieldsetid)	
	{
		$setcounter++;
		
		if ($fieldordering[$index] == "c") 
		{
		$sql2 = "select fieldno from formfields where fieldset = '$fieldsetid' order by ranking";
		$DB->query($sql2,"editform.php");
		$counter = 0;
		while ($row = $DB->get()) 
			{
			$counter++;
			$array_order[$counter] = $row["fieldno"];
			}	
		$total = $counter;
	
		
		}
		
		
		echo "<fieldset ><legend>$legend[$index]</legend>
	<table cellspacing=\"15\" ><tr><td valign=\"top\">";
		if ($settotal > 0 && $setcounter > 1)
		{
		
		
		foreach ($set_ranking as $index2 => $value)
									{
									//echo "index is $index $value pagecount is $pagecount";
									if ($index2 == ($setcounter - 1))
										{
										
										$up = $set_ranking;
										$up[$index2] = $set_ranking[$setcounter];
										$up[$setcounter] = $value;
										
										//$up = $array_order;
										//$up[$index2] = $array_order[$counter];
										//$up[$counter] = $value;
										}
									}
			
			
			
			
			$setup = implode(",",$up);
			echo "<a href=\"editform.php?m=$m&amp;update=sorder&&amp;setreorder=$setup\"><img src=\"../images/up.png\" title=\"$phrase[558]\"  alt=\"$phrase[558]\"></a>";
			

		
		} 
		echo "</td><td>";
		if ($settotal > 0 && $setcounter < $settotal)
		{
			
			
			
			
			
								foreach ($set_ranking as $index2 => $value)
									{
									//echo 
									if ($index2 == ($setcounter))
										{
										$down = $set_ranking;
										//echo "$index2 is index2 down is $down[$index2]";
										$temp = $down[$index2];
										$down[$index2] = $down[$index2 + 1];
										$down[$index2 + 1] = $temp;
										}
									}
								
								
							
							$setdown = implode(",",$down);
							echo "<a href=\"editform.php?m=$m&amp;update=sorder&amp;setreorder=$setdown\"><img src=\"../images/down.png\" alt=\"$phrase[559]\"  title=\"$phrase[559]\"></a>";
					
			
			
			
			
			
		} 
		
		echo "</td><td><a href=\"editform.php?m=$m&amp;event=editfieldset&amp;fieldset=$fieldsetid\"><img src=\"../images/page_edit.png\" title=\"$phrase[560]\"  alt=\"$phrase[560]\"></a> </td><td>
		<a href=\"editform.php?m=$m&amp;event=deletefieldset&amp;fieldset=$fieldsetid\"><img src=\"../images/page_delete.png\" title=\"$phrase[561]\"  alt=\"$phrase[561]\"></a>
		
		</td>
		<td>
		<a href=\"editform.php?m=$m&amp;event=addfield&amp;fieldset=$fieldsetno[$index]\"><img src=\"../images/add.png\" title=\"$phrase[557]\" alt=\"$phrase[557]\"></a></td>
		</tr></table><br>
<br>

	
		<table cellpadding=\"5\" cellspacing=\"0\"  >
		

		";
		if ($fieldordering[$index] == "c") {$order = "ranking";} else { $order = "label";}
		
		
		$sql = "select * from formfields where fieldset = \"$fieldsetno[$index]\" order by $order";
		$DB->query($sql,"editform.php");
	
		$counter = 0;
		$compdisplay = false;
		
		while ($row2 = $DB->get()) 
		{
		$counter++;
		$fieldno = $row2["fieldno"];
		
		$type = $row2["type"];
		$ranking = $row2["ranking"];
		$compulsory = $row2["compulsory"];
		if ($compulsory == 1) { $compdisplay = true;}
		$menu = $row2["menu"];
		$comment = $row2["comment"];
		$label = $row2["label"];
		$values = split("\n", $menu);
		
		
		$emailarray[$fieldno] = $label;
		
		echo "<tr><td><b>$label</b>";
		if ($compulsory == 1) {echo " <span style=\"color:#cc3333;\"><b>*</b></span>";}
		echo "</td><td>";
		if ($comment <> "") {echo "$comment<br>";}
		if ($type == "t") { echo "<input type=\"text\" disabled>";}
		if ($type == "d") { echo "<img src=../images/clock.png>";}
		if ($type == "a") { echo "<textarea disabled rows=\"2\" cols=\"10\"></textarea>";}
		if ($type == "m") 
		{//select menu
			 echo "<select style=\"width:20em\">";
			 foreach ($values as $indexa => $value)
							{
							echo "<option disabled> $value</option>";
							}
			echo "</select>";		
		}
		if ($type == "r") {
			//radio buttons
			foreach ($values as $indexa => $value)
							{
							echo "<input type=\"radio\" disabled > $value<br>";
							
							}
			}
		echo "</td><td>";
		if ($fieldordering[$index] == "c" && $total > 0 && $counter > 1 ) {
			
				foreach ($array_order as $index2 => $value)
									{
									//echo "index is $index $value pagecount is $pagecount";
									if ($index2 == ($counter - 1))
										{
										
										$up = $array_order;
										$up[$index2] = $array_order[$counter];
										$up[$counter] = $value;
										
										}
									}
			
			
			
			
			$fieldup = implode(",",$up);
			echo "<a href=\"editform.php?m=$m&amp;update=forder&amp;fieldreorder=$fieldup\"><img src=\"../images/up.png\" alt=\"$phrase[562]\"  title=\"$phrase[562]\"></a>";
							
			}
		echo "</td><td>";
		if ($fieldordering[$index] == "c" && $total > 0 && $counter < $total) { 
			
			
								foreach ($array_order as $index2 => $value)
									{
									//echo 
									if ($index2 == ($counter))
										{
										$down = $array_order;
										//echo "$index2 is index2 down is $down[$index2]";
										$temp = $down[$index2];
										$down[$index2] = $down[$index2 + 1];
										$down[$index2 + 1] = $temp;
										}
									}
								
								
							
							$fielddown = implode(",",$down);
							echo "<a href=\"editform.php?m=$m&amp;update=forder&amp;fieldreorder=$fielddown\"><img src=\"../images/down.png\" alt=\"$phrase[563]\"  title=\"$phrase[563]\"></a>";
						
			
			
			}
		echo "</td><td><a href=\"editform.php?m=$m&amp;event=editfield&amp;fieldno=$fieldno\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\" alt=\"$phrase[26]\"></a></td><td><a href=\"editform.php?m=$m&amp;event=deletefield&amp;fieldno=$fieldno\"><img src=\"../images/cross.png\" title=\"$phrase[24]\" alt=\"$phrase[24]\"></a></td></tr>";
		
		}
		
		if ($compdisplay == true) {
		 echo "<tr><td style=\"color:#cc3333;text-align:center\" colspan=\"6\"><b>*</b> $phrase[564]</td></tr>";}
		echo "</table><br>";
		
		echo "</fieldset>";
		}
	}	





	echo "</form></div>
";
	
	if ($formtype == "e")
	{
	
		
		
		echo "
		<div ><br>
		<form action=\"$_SERVER[PHP_SELF]\" method=\"post\" ><fieldset><legend>$phrase[565]</legend>
		<br><br><strong>$phrase[566]</strong> $phrase[567]<br>
<input type=\"text\" name=\"email\" value=\"$email\" size=\"100\" maxlength=\"200\"><br><br>";
	
echo "<table>";
		

	
	echo "<tr><td><b>$phrase[568]</b> </td><td><select name=\"subject\">
	<option value=\"0\">\"$modname\"</option>
	";
	foreach($emailarray as $index => $value)
		{
		echo "<option value=\"$index\" ";
		if ($subject == $index) { echo "selected";}
		echo ">$value field</option>";
		}
	
	echo "</select><br></td></tr><tr><td><b>$phrase[569]</b></td><td><select name=\"emailfrom\">
	<option value=\"i\"";
	if ($from == "i") {echo " selected";}
	echo ">$phrase[1028]</option><option value=\"u\""; if ($from == "u") {echo " selected";} echo ">$phrase[1029]</option>";
	
	foreach($emailarray as $index => $value)
		{
		echo "<option value=\"$index\" ";
		if ($from == $index) { echo "selected";}
		echo ">$value field</option>";
		}
	
	echo "</select><br></td></tr>
	";

	
	echo "<tr><td><b>CC intranet user</b></td><td><select name=\"cc_user\">
	<option value=\"0\" ";  if ($cc_user == "0") {echo " selected";}   echo ">$phrase[13] </option>
	<option value=\"1\" ";  if ($cc_user == "1") {echo " selected";}   echo ">$phrase[12] </option>
	
	</select></td></tr></table>";
	
	
	
print <<<EOF
<input type="hidden" name="m" value="$m">
<input type="hidden" name="update" value="email"><br>
<input type="submit" name="submit" value="$phrase[28]"> </fieldset>
</form><br>
<br></div>
EOF;
		
		
		
		
		
	}

		}
		}

		
		
echo "</div></div>";
		
	 
	     

	    
		
		
include("../includes/rightsidebar.php");
	
include ("../includes/footer.php");

?>
