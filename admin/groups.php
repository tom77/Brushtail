<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");


include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>
		<h2>User Groups</h2>";

	
	
	
if (isset($_POST["updategroup"]))
	{
	
	$group_id = $DB->escape($_POST["group_id"]);
	$sql = "delete from group_members where groupid = \"$group_id\"";
;
	$DB->query($sql,"groups.php");
	
	
	//update permissions for intranet modules
	if (isset($_POST["id"]))
						{
	foreach ($_POST["id"] as $key => $value) 
				{
				
				$sql = "insert into group_members values('$key','$group_id')";
				
				$DB->query($sql,"groups.php");
				}
				}
	}
	

	




if (isset($_POST["submitgroup"]))
	{
		
		
		$group_name = $DB->escape($_POST["group_name"] );
		
		
	if ($group_name == "")
		{
		$WARNING = "$phrase[39] <br><br>$phrase[40]";
		}
	
	else 
	{
		$sql = "insert into groups values(NULL,'$group_name')";
		$DB->query($sql,"groups.php");
	}
	
	}
	

	


if (isset($_GET["delete2"]) && $_GET["delete2"] <> 1)
	{
	//group
	$delete2 = $DB->escape($_GET["delete2"]);
	$sql = "delete from groups where group_id = \"$delete2\"";
	$DB->query($sql,"groups.php");
	
	$DB->tidy("groups");
	
	$sql = "delete from permissions where id = \"$delete2\" and type = \"g\"";
	$DB->query($sql,"groups.php");
	
	$DB->tidy("permissions");
	
	
	$sql = "delete from group_members where groupid = \"$delete2\" ";
	$DB->query($sql,"groups.php");
	
	$DB->tidy("group_members");
	
	}

	
	
	
	
	
	if(isset($ERROR))
	{
	
		error($ERROR);
	}
	elseif (isset($WARNING))
	{
		warning($WARNING);
	}
	
	
	elseif(isset($_GET["delete"]))
		{
			
	echo "<a href=\"groups.php\"><img src=\"../images/group.png\" title=\"$phrase[43]\"  alt=\"$phrase[43]\"></a><br><br><b>$phrase[14]</b><br><br>
	<a href=\"groups.php?delete2=$_GET[delete]\">$phrase[12]</a> | <a href=\"groups.php\">$phrase[13]</a>";
	
		}
	


////////////////////////////////
//remove
////////////////////////////////	

elseif(isset($_GET["groups"]))
		{
			$groups = $DB->escape($_GET["groups"]);
			
	echo "<a href=\"users.php\">$phrase[11]</a><br>
	<div >
	<form method=\"POST\" action=\"users.php\" ><fieldset><legend><b>$_GET[name]</b> $phrase[15]</legend>
	";
	
	//put group membership in an array
	$counter = 0;
	$sql = "SELECT groupid FROM group_members where userid = \"$groups\"";
		$DB->query($sql,"groups.php");
		while ($row = $DB->get())
		{
				
				 $groupnum[$counter] =$row["groupid"];
				 $counter++;
				 }
				 
		$sql = "SELECT groups.group_id, groups.group_name FROM groups order by group_name";

		$result = mysql_query($sql,"groups.php")	or die("groups query failed.");
		$DB->query($sql,"groups.php");
		$DB->countrows;
		if ($DB->numrows > 0) {echo "<table cellspacing=\"5\" >";}
				while ($row = $DB->get())
		{
			
				
				 $group_id =$row["group_id"];
				 $group_name =$row["group_name"];
				
				 echo "<tr><td>$group_name</td><td><input type=\"checkbox\" name=\"id[$group_id]\" ";
				 if (isset($groupnum))
				{
				foreach ($groupnum as $index => $value)
					{
					 if ($value == $group_id) { echo " checked";}
					}
				
				}
				 
				 echo "> </td></tr>";
				 }
		if ($DB->numrows  > 0) {echo "</table>
		
		<input type=\"hidden\" name=\"admin\" value=\"p\">
	<input type=\"hidden\" name=\"uid\" value=\"$groups\"><br>
	<input type=\"submit\" value=\"Update\" name=\"updategroups\">
		</fieldset></form></div>";}
		
		
		
		
		}
			

	
	 elseif(isset($_GET["edit"]))
	 //display the members of a group
	{  
	$group_id = $DB->escape($_GET["edit"]);
	$sql = "SELECT * FROM groups where group_id = '$group_id' order by group_name ";
	$DB->query($sql,"groups.php");
	$row = $DB->get();
	$group_name = $row["group_name"];
	
	//put group membership in an array
	$counter = 0;
	$sql = "SELECT userid FROM group_members where groupid = \"$group_id\"";
	$DB->query($sql,"groups.php");
		while ($row = $DB->get())
				{
				
				 $id[$counter] =$row["userid"];
				 $counter++;
				 }
	
	
	

echo "
<a href=\"groups.php\"><img src=\"../images/group.png\" title=\"$phrase[43]\"  alt=\"$phrase[43]\"></a><br><br>
<div >
<FORM method=\"POST\" action=\"groups.php\" >
<fieldset>
<legend>$phrase[41]</legend><br>

<div class=\"row\" >			<b>$phrase[38]</b> <span ><input type=\"text\" name=\"group_name\" size=\"50\" maxlength=\"50\" value=\"$group_name\" /></span></div>
					<div class=\"row\">	<b>$phrase[42]</b></div>";
					
		
		$sql = "SELECT userid, username FROM user order by username";
		$DB->query($sql,"groups.php");
		
		while ($row = $DB->get())
		{
		$uid = $row["userid"];
		$name = $row["username"];
				
				 echo "<div class=\"row\"><span class=\"checkbox\"><input type=\"checkbox\" name=\"id[$uid]\" ";
				if (isset($id))
				{
				foreach ($id as $index => $value)
					{
					 if ($value == $uid) { echo " checked";}
					}
				
				}
				
				 
				 echo "></span> $name</div>";
		}
		
		
				
			echo "<div class=\"row\"><input type=\"submit\" name=\"updategroup\" value=\"$phrase[28]\" />
			<input type=\"hidden\" name=\"group_id\" value=\"$_REQUEST[edit]\" /></div>
		
		
				</fieldset>
</form></div>";
		
		   
	}  
	elseif(isset($_GET["addgroup"]))
	{	
		
echo "
<a href=\"groups.php\"><img src=\"../images/group.png\" title=\"$phrase[43]\"  alt=\"$phrase[43]\"></a><br>
<div >
<FORM method=\"POST\" action=\"groups.php\" >
<fieldset>
<legend>$phrase[37]</legend><br>
				<label>$phrase[38]</label> <input type=\"text\" name=\"group_name\" size=\"50\" maxlength=\"50\" /> <input type=\"submit\" name=\"submitgroup\" value=\"$phrase[37]\" /><br>
		
		
				</fieldset>
</form><div>
		
		
				
";
		
	}

		
		
	else
		{
		
					
				$sql = "select * from groups order by group_name";
				$DB->query($sql,"groups.php");
				echo "<a href=\"groups.php?addgroup=yes\"><img src=\"../images/add.png\" title=\"$phrase[37]\"  alt=\"$phrase[37]\"></a><br><br>
				
				<div >
				<form  action=\"\"><fieldset><legend>$phrase[43]</legend><br><table cellpadding=\"7\" >";
			while ($row = $DB->get())
									{
								 $group_id =$row["group_id"];
								 $group_name =$row["group_name"];
								$linkname = urlencode($group_name);
								echo "<tr><td>$group_name</td><td><a href=\"groups.php?edit=$group_id\" ><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
								<td><a href=\"groups.php?delete=$group_id&amp;name=$linkname\" ><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a></td></tr>\n";
										}	
				echo "</table></fieldset></form></div>";
				
				
				
		}

		



echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

