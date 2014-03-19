<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	






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
	$ERROR  =  $phrase[72];
}	



if (isset($_REQUEST["id"]))
{
if ((isinteger($_REQUEST["id"])))
	{
	$id = $_REQUEST["id"];	
	}
	else 
	{$ERROR  =  "$phrase[72]";	}	
	
}	
	
	
if (isset($_REQUEST["enable"]))
{
if ((isinteger($_REQUEST["enable"])))
	{
	$enable = $_REQUEST["enable"];	
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

		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addcat"  )
   {
	$cat_name = $DB->escape($_REQUEST["cat_name"]);
	
	 $sql = "INSERT INTO helpdesk_cat VALUES(NULL,'$m','$cat_name')"; 
	
	$DB->query($sql,"helpdeskedit.php");	
	}


		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "email"  )
   {
    
    	 $sql = "delete from helpdesk_options where m = '$m'"; 
			$DB->query($sql,"helpdeskedit.php");
    

    
	$email = $DB->escape($_REQUEST["email"]);
	$email_action = $DB->escape($_REQUEST["email_action"]);
	$callcounter = $DB->escape($_REQUEST["callcounter"]);
	$search = $DB->escape($_REQUEST["search"]);
	$delbutton = $DB->escape($_REQUEST["delbutton"]);
	$showclock = $DB->escape($_REQUEST["showclock"]);
	$duplicate = $DB->escape($_REQUEST["duplicate"]);
	$texttolink = $DB->escape($_REQUEST["texttolink"]);
	$assignment = $DB->escape($_REQUEST["assignment"]);
	
	 $sql = "INSERT INTO helpdesk_options VALUES('$m','$email','$callcounter','$search','$email_action','$delbutton','$showclock','$duplicate','$texttolink','$assignment')"; 

	$DB->query($sql,"helpdeskedit.php");	
	}

	
	if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletecat" )
		{
	
		
		
		
		$sql = "DELETE FROM helpdesk_cat WHERE id = \"$id\"";
		$DB->query($sql,"helpdeskedit.php");
	
			
		
		}

		
		
		
	if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "rename"  )
   {
	$cat_name = $DB->escape($_REQUEST["cat_name"]);
	$sql = "update helpdesk_cat set cat_name = \"$cat_name\" where id = \"$id\"";
	
	$DB->query($sql,"helpdeskedit.php");	
	}	
		
		
	
			include("../includes/leftsidebar.php");
		
			
		
		$sql = "select name, ordering from modules where m = \"$m\"";
		$DB->query($sql,"page.php");
		$row = $DB->get();
		$modname = formattext($row["name"]);
		echo "<div id=\"content\"><div><h1 class=\"red\">$modname</h1>
		
		
		<a href=\"editform.php?m=$m\">$phrase[686]</a> | <a href=\"edithelpdesk.php?m=$m&amp;event=cat\">$phrase[884]</a> | <a href=\"edithelpdesk.php?m=$m&amp;event=options\">$phrase[696]</a>";
		

if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "cat")
	{
		
	echo "<h2>$phrase[884]</h2> 
";

	
$sql = "SELECT DISTINCT (cat)FROM helpdesk";
$DB->query($sql,"edithelpdesk.php");

while ($row = $DB->get())
      {	
      $cats[] = $row["cat"]; 	
      }	
	
	
$sql = "select *  from helpdesk_cat where m = \"$m\"";
$DB->query($sql,"edithelpdesk.php");
$num = $DB->countrows();

if ($num > 0) { echo "	<table cellpadding=\"10\" class=\"colourtable\">";} 

while ($row = $DB->get())
      {	
      	$id =  $row["id"];
      	$cat_name =  $row["cat_name"];
      	echo "<tr><td>$cat_name</td><td><a href=\"edithelpdesk.php?m=$m&amp;event=rename&amp;id=$id\">$phrase[178]</a></td><td>";
      	if (isset($cats))
      	{
      	if (!in_array ($id, $cats ))
      	{ echo "<a href=\"edithelpdesk.php?m=$m&amp;event=deletecat&amp;id=$id\">$phrase[24]</a>";
      	} else{echo "$phrase[693]";}
      	}
      	else 
      	{
      	echo "<a href=\"edithelpdesk.php?m=$m&amp;event=deletecat&amp;id=$id\">$phrase[24]</a>";	
      	}
      	
      	echo "</td></tr>";
    
      }
	
if ($num > 0) { echo "</table>";} 




echo "<div >
<form action=\"edithelpdesk.php\" method=\"POST\" >
<p>
";
print <<<EOF
			<input type="text" name="cat_name" size="50" maxlength="100">
			<input type="hidden" name="update" value="addcat">
			<input type="hidden" name="event" value="cat">
			<input type="hidden" name="m" value="$m"> 

			<input type="submit" name="submit" value="$phrase[176]"></p>
			</form></div>	
EOF;
	}
	
		elseif (isset($_GET["event"]) && $_GET["event"] == "options")
	{
	 $email = "";
	 $sql  = "select * from helpdesk_options where m = '$m'";	
	$DB->query($sql,"editform.php");
	
	$row = $DB->get();
	$email = $row["email"];
	$callcounter = $row["callcounter"];
	$search = $row["search"];
	$email_action = $row["email_action"];
	$delbutton = $row["delbutton"];
	$showclock = $row["showclock"];
	$duplicate = $row["duplicate"];
	$texttolink = $row["texttolink"];
	$assignment = $row["assignment"];
	
	echo "<br><br>
		<div class=\"swouter\">
	<form action=\"edithelpdesk.php\" method=\"post\" class=\"swinner\">
	<fieldset><legend>$phrase[696]</legend><table style=\"text-align:left\" cellpadding=\"5\">
	<tr><td align=\"right\"><b>$phrase[259]</b></td><td align=\"right\">
	 <input type=\"text\" name=\"email\" value=\"$email\" size=\"60\" maxlength=\"200\"></td></tr>
	 
	 <tr><td align=\"right\">
	 <b>$phrase[258]</b></td><td><select name=\"email_action\">";
	 
	 $lablels[1] = $phrase[869];
	 $lablels[2] = $phrase[870];
	 $lablels[3] = $phrase[871];
	 
	 $counter = 1;
	while ($counter < 4)
	{
		echo "<option value=\"$counter\" ";
		if ($counter == $email_action ) { echo "selected";}
		
		echo ">$lablels[$counter]</option>";
		$counter++;
	}

	echo "</select></td></tr>
	 
	 
	 
	 
	 
	 <tr><td align=\"right\">
	<b> $phrase[687] </b></td><td><select name=\"callcounter\">";
	if (isset($callcounter) && $callcounter == 1) { echo "<option value=\"1\" selected>$phrase[12]</option><option value=\"0\">$phrase[13]</option>";}
	else {echo "<option value=\"1\">$phrase[12]</option><option value=\"0\" selected>$phrase[13]</option>";}
	 
	 echo "</select></td></tr>
	 
	 	 
	 <tr><td align=\"right\">
	<b> $phrase[888] </b></td><td><select name=\"delbutton\">";
	if (isset($delbutton) && $delbutton == 1) { echo "<option value=\"1\"  selected>$phrase[12]</option><option value=\"0\">$phrase[13]</option>";}
	else {echo "<option value=\"1\">$phrase[12]</option><option value=\"0\"  selected>$phrase[13]</option>";}
	 
	 echo "</select></td></tr>
	 
	  <tr><td align=\"right\">
	<b> $phrase[894]  </b></td><td><select name=\"duplicate\">";
	if (isset($duplicate) && $duplicate == 1) { echo "<option value=\"1\"  selected>$phrase[12] </option><option value=\"0\">$phrase[13]</option>";}
	else {echo "<option value=\"0\"  selected>$phrase[13]</option><option value=\"1\">$phrase[12]</option>";}
	 
	 echo "</select></td></tr>
	 	 
	 <tr><td align=\"right\">
	<b> $phrase[889]  </b></td><td><select name=\"showclock\">";
	if (isset($showclock) && $showclock == 1) { echo "<option value=\"1\"  selected>$phrase[12] </option><option value=\"0\">$phrase[13]</option>";}
	else {echo "<option value=\"0\"  selected>$phrase[13]</option><option value=\"1\">$phrase[12]</option>";}
	 
	 echo "</select></td></tr>
	  <tr><td align=\"right\">
	<b> $phrase[952]  </b></td><td><select name=\"texttolink\">";
	if (isset($texttolink) && $texttolink == 1) { echo "<option value=\"1\"  selected>$phrase[12] </option><option value=\"0\">$phrase[13]</option>";}
	else {echo "<option value=\"0\"  selected>$phrase[13]</option><option value=\"1\">$phrase[12]</option>";}
	 
	 echo "</select></td></tr>
	 
		  <tr><td align=\"right\">
	<b> $phrase[1059] </b></td><td><select name=\"assignment\">";
	if (isset($assignment) && $assignment == 1) { echo "<option value=\"1\"  selected>$phrase[12] </option><option value=\"0\">$phrase[13]</option>";}
	else {echo "<option value=\"0\"  selected>$phrase[13]</option><option value=\"1\">$phrase[12]</option>";}
	 
	 echo "</select></td></tr>
	 
	 <tr><td align=\"right\">
	 <b>$phrase[214] </b></td><td><select name=\"search\">";
	 
	 $lablels[1] = $phrase[208];
	 $lablels[2] = $phrase[164];
	 $lablels[3] = $phrase[408];
	 
	 $counter = 1;
	while ($counter < 4)
	{
		echo "<option value=\"$counter\" ";
		if ($counter == $search ) { echo "selected";}
		
		echo ">$lablels[$counter]</option>";
		$counter++;
	}

	echo "</select><br><br><input type=\"submit\" name=\"submit\" value=\"submit\">
	<input type=\"hidden\" name=\"update\" value=\"email\">  
	<input type=\"hidden\" name=\"m\" value=\"$m\">  </td></tr></table>
	</fieldset>
	</form></div>";
	 
	}
	
	elseif (isset($_GET["event"]) && $_GET["event"] == "deletecat")
	{
	
	$sql = "select count(*) as jobnum  from helpdesk where helpdesk.cat = '$id'" ;
	$DB->query($sql,"editchelpdesk.php");
	$row = $DB->get();
	$jobnum = $row["jobnum"];
	
	
	echo "
<br>
";
		if ($jobnum > 0) {echo "
		<p style=\"color:#ff3333;font-size:large;margin-top:2em\">$phrase[692]</p>";} 
	else
	{
	echo "<h2>$phrase[884]</h2> <br><b>$phrase[14]</b><br><br><a href=\"edithelpdesk.php?m=$m&amp;update=deletecat&id=$id&amp;event=cat\">$phrase[12]</a> 
	| <a href=\"edithelpdesk.php?m=$m&amp;event=cat\">$phrase[13]</a>";
	
	}
	
	
	}	

	
	elseif (isset($_GET["event"]) && $_GET["event"] == "rename")
	{
	$sql = "select *  from helpdesk_cat where id = \"$id\"";
$DB->query($sql,"edithelpdesk.php");

$row = $DB->get();
  
      
      	$cat_name =  $row["cat_name"];
      	
	print <<<EOF
	<br>
<h2>$phrase[884]</h2> 
<div >
	<form action="edithelpdesk.php" method="POST" >
	<fieldset><legend>$phrase[178]</legend>
			<input type="text" name="cat_name" size="50" maxlength="100" value="$cat_name">
			<input type="hidden" name="update" value="rename">
			<input type="hidden" name="event" value="cat">
			<input type="hidden" name="m" value="$m"> 
			<input type="hidden" name="id" value="$id"> <br><br>


			<input type="submit" name="submit" value="$phrase[178]"></fieldset>
			</form></div>	
EOF;
	
	
	
	
	
}	
	
	
	
	
	elseif (isset($_GET["event"]) && $_GET["event"] == "editcat")
	{
		
	
	echo "<form action=\"edithelpdesk.php\" method=\"POST\" style=\"margin-left:auto;margin-right:auto;width:80%\">
";
print <<<EOF
			<input type="text" name="cat_name" size="50" maxlength="100">
			<input type="hidden" name="update" value="addcat">
			<input type="hidden" name="event" value="cat">
			<input type="hidden" name="m" value="$m"> 

			<input type="submit" name="submit" value="$phrase[176]">
			</form><br><br>	
EOF;

	
	
}
	



	
	
	
	
	
		
	echo "</div></div>";	
	include("../includes/rightsidebar.php");
	

		
	}

include ("../includes/footer.php");

?>

