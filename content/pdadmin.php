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
	
	
if (isset($_REQUEST["field"]))
{
if ((isinteger($_REQUEST["field"])))
	{
	$field = $_REQUEST["field"];	
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
	
	 $sql = "INSERT INTO pd_categories VALUES(NULL,'$cat_name')"; 
	
	$DB->query($sql,"pdadmin.php");	
	}		
		
		
		
		
		
		
if (isset($_POST["update"]) && $_POST["update"] == "updatefield")
	{										
	
	$menu = $DB->escape($_POST["menu"]);
	$label = $DB->escape($_POST["label"]);
	$comment = $DB->escape($_POST["comment"]);
		$print = $DB->escape($_POST["print"]);
	//$compulsory = $DB->escape($_POST["compulsory"]);
	//$output = $DB->escape($_POST["output"]);
	
	
	
	if (!isset($ERROR))
	{
	
	$sql = "update pd_custom_fields set label = '$label',menu = '$menu', comment = '$comment', output = '$print'  where fieldno=\"$field\"";


	$DB->query($sql,"pdoptions.php");
	} else {$ERROR = $phrase[72];} 
	}
	
	
		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "forder")
	{
	
	//reorders form fields
	$fieldreorder = explode(",",$_REQUEST["fieldreorder"]);
	

	
	foreach ($fieldreorder as $index => $value)
		{
		if (isinteger($value))
		{
		$sql = "update pd_custom_fields set ranking = \"$index\" WHERE fieldno = \"$value\"";	
	
		$DB->query($sql,"pdadmin.php");
		}
		else {$ERROR = $phrase[72];}
		}
		
	
	
	
	}

   
	if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "renamecat" )
		{

		$cat_name = $DB->escape($_POST["cat_name"]);
		$sql = "update pd_categories set cat_name = '$cat_name' where id =  '$id'";
		$DB->query($sql,"pdadmin.php");
		
		
		
		
		}
	
	if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletecat" )
		{

		
		$sql = "select * from pd_sessions where pd_category = '$id'";
		$DB->query($sql,"pdadmin.php");
		$num = $DB->countrows();
		if ($num == 0)
		{
		
		$sql = "DELETE FROM pd_categories WHERE id = \"$id\"";
	
		$DB->query($sql,"pdamin.php");
	
			
		} else {$WARNING = "$phrase[293]";
		
		}
		}

	
	
	
   	
if (isset($_POST["update"]) && $_POST["update"] == "addfield")
	{

		
	$fieldtype = $DB->escape($_POST["fieldtype"]);
	$label = $DB->escape($_POST["label"]);
	//$compulsory = $_POST["compulsory"];
	$comment = $DB->escape($_POST["comment"]);
	$print = $DB->escape($_POST["print"]);
	
	//$output = $DB->escape($_POST["output"]);

	
	
	if (isset($_POST["values"]))
	{
		$values = $DB->escape($_POST["values"]);
	}
	else {$values = "";}
		
		//if (isinteger($compulsory))
		//{	
		$sql = "insert into pd_custom_fields values (NULL,'$m','$fieldtype','1','$label','0','$values','$comment','$print') ";
		
		$DB->query($sql,"pdadmin.php");
		
		
		//}
	
	}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletefield")
	{
	
		
	 $sql = "delete from pd_custom_fields where fieldno = \"$field\" "; 

		 	$DB->query($sql,"pdadmin.php");		
		
	}	
	
	
	
	
	
	
		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "email"  )
   {
    
    	 $sql = "delete from pd_options where m = '$m'"; 
			$DB->query($sql,"helpdeskedit.php");
    

			
		
    
	$email = $DB->escape($_REQUEST["email"]);
	$email_action = $DB->escape($_REQUEST["email_action"]);
	$showprint = $DB->escape($_REQUEST["showprint"]);
	//$duplicate = $DB->escape($_REQUEST["duplicate"]);
	$instructions = $DB->escape($_REQUEST["instructions"]);
	$showlogo = $DB->escape($_POST["showlogo"]);
	$email_user = $DB->escape($_REQUEST["email_user"]);
	$replacement_text = $DB->escape($_REQUEST["replacement_text"]);
	$print_heading = $DB->escape($_REQUEST["print_heading"]);
	
	
	 $sql = "INSERT INTO pd_options VALUES('$m','$email','$email_action','$showprint','$instructions','$showlogo','$email_user','$replacement_text','$print_heading')"; 
	 

	$DB->query($sql,"pdadmin.php");	
	}

if(isset($_REQUEST["update"]) && $_REQUEST["update"] == "import")
{
	
	//$lines = @file($_FILES['upload']['tmp_name']);
	
 $sql = "SELECT userid, username from user";
		$DB->query($sql,"pdedit.php");
		while ($row = $DB->get()) 
					{
					$username = $row["username"];
					$userid = $row["userid"];
					$users[$username] = $userid;
					}

//print_r($users);
	
	$rows = 0;
	
	//$data = file($_FILES['upload']['tmp_name'], "r");
	$file = $_FILES['upload']['tmp_name'];
	
	//$row = 1;
$handle = fopen($file, "r");


echo "<div style=\"text-align:center\">";


while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    //$num = count($data);
   
    
        $title = $DB->escape($data[0]);
 		$description = $DB->escape($data[1]);
 		$date = $DB->escape($data[2]);
		 if (strtolower($data[3]) == "yes") {$external = 1;} else {$external = 0;}
		$hours = $DB->escape($data[4]);
		$cost = $DB->escape($data[5]);
		 if (strtolower($data[6]) == "yes") {$approved = 1;} else {$approved = 0;}
		if (strtolower($data[7]) == "yes") {$attended = 1;} else {$attended = 0;}
		$csvuser = $data[8];
       $user = $users[$csvuser];
       $replacement = $DB->escape($data[9]);
      
       
     
       $pattern = '/^[0-9][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]$/';
		preg_match($pattern, $date, $result);

if ($result) {
	   	$sql = "insert into pd_sessions values
		(NULL,'$title','$description','$date','$external','$hours','$cost','$approved','$attended','$user','','$replacement','','0')";
	//echo $sql;

		} else {echo "Failed row insert. Invalid date format. Should be yyy-mm-dd<br>";}
        
    echo "</div>";
       //	echo $sql;
		
		$DB->query($sql,"pdadmin.php");
		$rows++;
}
fclose($handle);

echo "<h4 style=\"text-align:center\">Import processed $rows rows!</h4>";
	 
	


}




		
		
	
		
		
	
			
		
			
		
		$sql = "select name, ordering from modules where m = \"$m\"";
		$DB->query($sql,"page.php");
		$row = $DB->get();
		$modname = formattext($row["name"]);
		echo "<div style=\"text-align:center;\"><h1 class=\"red\">$modname</h1>
		
		
		<a href=\"pdadmin.php?m=$m&amp;event=import\">Import PD</a> | <a href=\"pdadmin.php?m=$m&amp;event=options\">$phrase[696]</a> | <a href=\"pdadmin.php?m=$m&amp;event=custom\">Custom fields</a> | 	<a href=\"pdadmin.php?m=$m&amp;event=viewcat\">Categories</a> ";

	if(isset($_GET["event"]) && $_GET["event"] == "import")
		{		
		
			
			
			echo "	<form action=\"pdadmin.php\" method=\"post\" enctype=\"multipart/form-data\" style=\"margin:0 auto;text-align:left;width:80%;margin-top:1em\">

	<fieldset><legend>$phrase[916]</legend><br>
	
	
						
						
						<b>CSV format </b>
						
						<br><br>Title, Description,Date(yyyy-mm-dd),External(yes/no),Hours(numeric),Cost(0.00),Approved(yes/no),Attended(yes/no), Username,Replacement cost
						
<br><br>
						
					<input type=\"file\" name=\"upload\" ><br><br>
						
				
				
					
						<input type=\"hidden\" name=\"update\" value=\"import\">
							<input type=\"hidden\" name=\"m\" value=\"$m\">
						
						
						<input type=\"submit\" name=\"submit\" value=\"$phrase[916]\" ></fieldset>
						</form>	
						
						<b>Notes</b><br>
						If columns do not exist in import file, then insert empty columns where appropriate before import.
						
						";
		}

		
		
		 elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletefield")
	{ 
		
		
		$sql = "select label from pd_custom_fields where fieldno = '$field'";
		$DB->query($sql,"pdamin.php");
 		$row = $DB->get();
 		$name = $row["label"];
		
	 	echo "<br><br><br><b>$phrase[14]</b><br><br>$name  <br><br>
	 	
	 	$phrase[909]
	 	<br><br>
	<a href=\"pdadmin.php?m=$m&amp;update=deletefield&amp;&amp;field=$field&amp;event=custom\">$phrase[12]</a> | <a href=\"pdadmin.php?m=$m&amp;&amp;event=custom\">$phrase[13]</a>";
	
		
	}
	
			elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "renamecat")
			{
				
				
				$sql = "select id, cat_name  from pd_categories where  id = '$id'";
					
 						$DB->query($sql,"pdamin.php");
 						$row = $DB->get();
 					
						$cat_name = $row["cat_name"];
				
					 print <<<EOF
					 <br><br>
<h4>Rename category</h4>
<form action="pdadmin.php" method="POST" style="display:inline">
		<input type="text" name="cat_name" size="25" maxlength="100" value="$cat_name">
			<input type="hidden" name="update" value="renamecat">
			<input type="hidden" name="event" value="viewcat">
			<input type="hidden" name="m" value="$m">
			<input type="hidden" name="id" value="$id">
			<input type="submit" name="submit" value="Update category">
			</form><br><br>
EOF;
				
			}
		
			elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "viewcat")
			{
			
				echo "<h2>Categories</h2><table class=\"colourtable\" style=\"margin:0 auto\">";
				
						$sql = "select id, cat_name  from pd_categories order by cat_name";
					
 						$DB->query($sql,"pdamin.php");
 						while ($row = $DB->get())
 						{
						$cat_name = $row["cat_name"];
						$id = $row["id"];
						
						
						echo "<tr><td>$cat_name</td><td><a href=\"pdadmin.php?m=$m&amp;event=renamecat&amp;id=$id\">Rename</a></td><td>";
						if ($id != 1) { echo "
						<a href=\"pdadmin.php?m=$m&amp;event=viewcat&update=deletecat&amp;id=$id\">Delete</a></td></tr>";
						}
						
 						}
				
				echo "</table>";
				
				
				
				 print <<<EOF
 <br><br><br>
 <h4>$phrase[845]</h4>
<form action="pdadmin.php" method="POST" style="display:inline">
		<input type="text" name="cat_name" size="25" maxlength="100">
			<input type="hidden" name="update" value="addcat">
			<input type="hidden" name="event" value="viewcat">
			<input type="hidden" name="m" value="$m">
			<input type="submit" name="submit" value="$phrase[845]">
			</form><br><br>
EOF;
				
				
				
				
				
				
			}
		
		
			elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "custom")
			{
			
				
				echo "<h2>Custom Fields</h2><p><a href=\"pdadmin.php?m=$m&amp;event=addfield\"><img src=\"../images/add.png\" title=\"$phrase[557]\" alt=\"$phrase[557]\"></a></p>";	
				
				$sql = "select * from pd_custom_fields where m = '$m' order by ranking";
		
	$counter = 0;	
						
	$DB->query($sql,"pdadmin.php");
	while ($row = $DB->get())
	{
	$field = $row["fieldno"]; 
	$custom_labels[$field] = $row["label"];
	$custom_types[$field] = $row["type"];
	$custom_menu[$field] = $row["menu"];
	//$custom_ranking[$field] = $row["ranking"];
	$custom_comments[$field] = $row["comment"];
	$custom_ranking[$counter] = $field;
	$counter++;
	}
	$total = $counter;			
	
	
	echo "<form>";
	
	
	$counter = 0;


if (isset($custom_labels))
{
foreach ($custom_labels as $key => $value)
{
	
	echo "
	<div class=\"formrow\" >
<div class=\"left_col label\" ><label>$value</label></div><div class=\"right_col\" ><span >";
	if ($custom_types[$key] == "t") {echo "<input type=\"text\" disabled size=\"10\">";}
	if ($custom_types[$key] == "a") {echo "<textarea disabled cols=\"10\" rows=\"2\"></textarea>";}
	if ($custom_types[$key] == "m") {echo "<select disabled><option></option></select>";}
	echo " <a href=\"pdadmin.php?m=$m&amp;event=editfield&amp;field=$key\">
	<img src=\"../images/pencil.png\"  title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a> <a href=\"pdadmin.php?m=$m&amp;event=deletefield&amp;field=$key\"><img src=\"../images/cross.png\"  title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a> 
	";
	
	
	
			if ( $total > 0 && $counter > 0 ) {
			
				foreach ($custom_ranking as $index2 => $value)
									{
									//echo "index is $index $value pagecount is $pagecount";
									if ($index2 == $counter)
										{
										
										$up = $custom_ranking;
										$up[$counter] = $custom_ranking[$counter - 1];
										$up[$counter - 1] = $custom_ranking[$counter];
										
										}
									}
			
			
			
			
			$fieldup = implode(",",$up);
			echo "<a href=\"pdadmin.php?m=$m&amp&amp;update=forder&amp;event=custom&amp;fieldreorder=$fieldup\"><img src=\"../images/up.png\" alt=\"$phrase[562]\"  title=\"$phrase[562]\"></a>";
							
			}
			
			
			
			
				if ($total > 0 && $counter + 1 < $total) { 
			
			
								foreach ($custom_ranking as $index2 => $value)
									{
									//echo 
									if ($index2 == $counter)
										{
										$down = $custom_ranking;
										
										$down[$counter] = $custom_ranking[$counter + 1];
										$down[$counter + 1] = $custom_ranking[$counter];
									
										}
									}
								
								
							
							$fielddown = implode(",",$down);
							echo "<a href=\"pdadmin.php?m=$m&amp;event=custom&amp;update=forder&amp;fieldreorder=$fielddown\"><img src=\"../images/down.png\" alt=\"$phrase[563]\"  title=\"$phrase[563]\"></a>";
						
			$counter++;	
			
			}
	
	echo "<br>$custom_comments[$key]</span></div>
</div>";
	
	

	
}
}

	
	echo "</form>";
	
	
	
	
			}
			
			
			
			
			
			elseif (isset($_GET["event"]) && $_GET["event"] == "addfield")
	{
print <<<EOF

	
	<script type="text/javascript">
	
function menu(){
document.getElementById('labelspan').innerHTML = '<b>Menu values</b>';
document.getElementById('menuvalues').innerHTML = '<textarea name="values" rows="5" cols="60"></textarea><br>Separate each value with a line break.';


}

function radio(){
document.getElementById('labelspan').innerHTML = '<b>Menu values</b>';
document.getElementById('menuvalues').innerHTML = '<textarea name="values" rows="5" cols="60"></textarea><br>Separate each value with a line break.';

}

function text(){
document.getElementById('labelspan').innerHTML = '';
document.getElementById('menuvalues').innerHTML = '<input type="hidden" name="values" value=""';

}

function area(){
document.getElementById('labelspan').innerHTML = '';
document.getElementById('menuvalues').innerHTML = '<input type="hidden" name="values" value=""';

}
</script>

	gggg<form action="pdadmin.php" method="post" style="width:80%;margin:2em auto">
	<fieldset><legend>$phrase[100]</legend><br>
<br>

	<table cellspacing="7" style="text-align:left">
	
	<tr><td align="right"><strong>$phrase[109]</strong></td><td align="left">
<input type="text" name="label" size="40" maxlength="200"></td></tr>

 
 
 

<tr><td align="right"><strong>$phrase[111]</strong></td><td align="left">

<input type="radio" name="fieldtype" value="t" checked onClick="text()"> $phrase[105] <br>
<input type="radio" name="fieldtype" value="a" onClick="area()"> $phrase[106] <br>
<input type="radio" name="fieldtype" value="m" onClick="menu()"> $phrase[107]</td></tr>

	<tr><td align="right"><strong>Print</strong></td><td align="left">
<select name="print">
<option value="0">No</option>
<option value="1">Yes</option>
</select></td></tr>



<tr><td><span id="labelspan"></span></td><td><span id="menuvalues"><input type="hidden" name="values" value=""></span></td></tr>
 <tr><td align="right"><strong>Field comment</strong></td><td><textarea name="comment" cols="60"></textarea></td></tr>
<tr><td></td><td align="left">

		
	<input type="hidden" name="m" value="$m">
	<input type="hidden" name="update" value="addfield">
	<input type="hidden" name="event" value="custom">

<input type="submit" name="submit" value="$phrase[100]"></td></tr></table></fieldset>
	</form>
	
EOF;
	
	}
 
			
				elseif (isset($_GET["event"]) && $_GET["event"] == "editfield")
	{
		
$sql = "select * from pd_custom_fields where fieldno = '$field'";
	$DB->query($sql,"pdadmin.php");
	
	$row = $DB->get();
	$label = $row["label"];
	$compulsory = $row["compulsory"];
	
	$menu = $row["menu"];
	$comment = $row["comment"];
	$output = $row["output"];
	$type = $row["type"];
	
	
echo "


	<form action=\"$_SERVER[PHP_SELF]\" method=\"post\" style=\"width:80%;margin:2em auto\">
	<fieldset><legend>$phrase[575]</legend>
	
<table cellspacing=\"7\" >
	
	<tr><td align=\"right\"><strong>$phrase[109]</strong></td><td>
<input type=\"text\" name=\"label\" size=\"80\" maxlength=\"200\" value=\"$label\"></td></tr>

 <tr><td align=\"right\"><strong>$phrase[576]</strong></td><td><textarea name=\"comment\" cols=\"60\" rows=\"4\">$comment</textarea></td></tr>
";
if ($type == "m" || $type == "r")
{ echo "<tr><td align=\"right\"><strong>$phrase[577]</strong></td><td><textarea name=\"menu\" cols=\"60\" rows=\"8\">$menu</textarea></td></tr>";}

echo "
<tr><td align=\"right\"><strong>Print</strong></td><td align=\"left\">
<select name=\"print\">
<option value=\"0\">No</option>
<option value=\"1\"";
if ($output == 1) {echo " selected";}
echo ">Yes</option>
</select></td></tr>

<tr><td></td><td>
	<input type=\"hidden\" name=\"field\" value=\"$field\">
	<input type=\"hidden\" name=\"event\" value=\"custom\">
	
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"updatefield\">";
if ($type == "t" || $type == "a")  {echo "<input type=\"hidden\" name=\"menu\" value=\"\">";}

echo "<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\"></td></tr></table>
	</fieldset></form></div><br>";
	}
		
		
		elseif(isset($_GET["event"]) && $_GET["event"] == "options")
	{
	 $email = "";
	 $sql  = "select * from pd_options where m = '$m'";	
	$DB->query($sql,"editform.php");
	
	$row = $DB->get();
	$email = $row["email"];
	$email_action = $row["email_action"];
	$showprint = $row["showprint"];
	$instructions = $row["instructions"];
	$showlogo = $row["showlogo"];
	$email_user = $row["email_user"];
	$replacement_text = $row["replacement_text"];
	$print_heading = $row["print_heading"];
	
	
	
	echo "<br><br>
		<div class=\"swouter\">
	<form action=\"pdadmin.php\" method=\"post\" class=\"swinner\">
	<fieldset><legend>$phrase[696]</legend><table style=\"text-align:left\" cellpadding=\"5\">
	<tr><td align=\"right\"><b>$phrase[259]</b></td><td align=\"right\">
	 <input type=\"text\" name=\"email\" value=\"$email\" size=\"60\" maxlength=\"200\"></td></tr>
	 
	 <tr><td align=\"right\">
	 <b>Email alert when PD is added</b></td><td><select name=\"email_action\">";
	 
	 $lablels[1] = $phrase[869];
	 $lablels[2] = $phrase[871];


	 
	 $counter = 1;
	while ($counter < 3)
	{
		echo "<option value=\"$counter\" ";
		if ($counter == $email_action ) { echo "selected";}
		
		echo ">$lablels[$counter]</option>";
		$counter++;
	}

	echo "</select></td></tr>
	  <tr><td align=\"right\">
	 <b>Email users when PD approved</b></td><td><select name=\"email_user\">";
	 
	 $lablels[1] = $phrase[869];
	 $lablels[2] = "Prompt for action";
	 $lablels[3] = $phrase[871];


	 
	 $counter = 1;
	while ($counter < 4)
	{
		echo "<option value=\"$counter\" ";
		if ($counter == $email_user ) { echo "selected";}
		
		echo ">$lablels[$counter]</option>";
		$counter++;
	}

	echo "</select></td></tr>
	 
	 
	 
	 

	 	 
	 <tr><td align=\"right\">
	<b> $phrase[513]  </b></td><td><select name=\"showprint\">";
	if (isset($showprint) && $showprint == 1) { echo "<option value=\"1\"  selected>$phrase[12] </option><option value=\"0\">$phrase[13]</option>";}
	else {echo "<option value=\"0\"  selected>$phrase[13]</option><option value=\"1\">$phrase[12]</option>";}
	 
	 echo "</select></td></tr>
	 
	 <tr><td align=\"right\">
	<b> Certificate header </b></td><td><input type=\"input\" name=\"print_heading\" value=\"$print_heading\" size=\"60\" max=\"200\"></td></tr>
	 
	 	 <tr><td align=\"right\">
	<b>Display logo on certificates </b></td><td><select name=\"showlogo\">";
	if (isset($showlogo) && $showlogo == 1) { echo "<option value=\"1\"  selected>$phrase[12] </option><option value=\"0\">$phrase[13]</option>";}
	else {echo "<option value=\"0\"  selected>$phrase[13]</option><option value=\"1\">$phrase[12]</option>";}
	 
	 echo "</select> (pdbanner.gif)</td></tr>
	  <tr><td><b>Instructions</b></td><td><textarea cols=\"40\" rows=\"8\" name=\"instructions\">$instructions</textarea></td></tr>
	 <tr><td><b>Replacement cost instructions</b></td><td><textarea cols=\"40\" rows=\"8\" name=\"replacement_text\">$replacement_text</textarea></td></tr>
	 
	 <tr><td align=\"right\">
	</td><td><input type=\"submit\" name=\"submit\" value=\"submit\">
	<input type=\"hidden\" name=\"update\" value=\"email\">  
	<input type=\"hidden\" name=\"m\" value=\"$m\">  </td></tr></table>
	</fieldset>
	</form>";
	 
	}
	
	

	

	
	
	
	



	
	
	
	
	
		
	echo "</div>";	
		
	}

include ("../includes/footer.php");


//
?>

