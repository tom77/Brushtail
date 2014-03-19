<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");

$integers[] = "m";
$integers[] = "cat_id";
$integers[] = "branchno";




foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (isset($ERROR)) { echo $ERROR; exit();}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletetype")

	{
	
		$sql = "select count(*) as count from cal_events where event_catid = '$cat_id'";
 	$DB->query($sql,"calendars.php");
	$row = $DB->get();
	$count = $row["count"];
	
	if ($count == 0)
	{
	
	$sql = "delete from cal_cat where cat_id = \"$cat_id\"";
	
	$DB->query($sql,"calendars.php");
		
	$sql = "delete from cal_bridge where cat = \"$cat_id\"";
	$DB->query($sql,"calendars.php");
	
	}
	else {$ERROR =  "<br><br><h1 class=\"red\">$phrase[1032]</h1>
	<a href=\"calendars.php?event=types\">$phrase[34]</a>
	
	";}
	}

  elseif (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addlocation")
   {
   	if ($_REQUEST["bname"] == "")
   	{
   		$WARNING = "$phrase[480]";
   	}
   	else {
	$bname = $DB->escape($_REQUEST["bname"]);
	$telephone = $DB->escape($_REQUEST["telephone"]);

	$sql = "INSERT INTO cal_branches VALUES(NULL,'$bname','$telephone')";

	$DB->query($sql,"editcalendar.php");
	
   	}
		
	}
	
elseif (isset($_REQUEST["update"]) && $_REQUEST["update"] == "edittype")

	{	
		
		//print_r($_REQUEST);
		
		$cat_name = $DB->escape($_REQUEST["cat_name"]);
			$cat_colour = "#" . $DB->escape($_REQUEST["cat_colour"]);
			$cat_takesbookings = $DB->escape($_REQUEST["cat_takesbookings"]);
			$cat_waitinglist = $DB->escape($_REQUEST["cat_waitinglist"]);
			$cat_cost = $DB->escape($_REQUEST["cat_cost"]);
			$cat_confirmation = $DB->escape($_REQUEST["cat_confirmation"]);
			$cat_multiple = $DB->escape($_REQUEST["cat_multiple"]);
			$cat_age = $DB->escape($_REQUEST["cat_age"]);
			$cat_notes = $DB->escape($_REQUEST["cat_notes"]);
			$cat_address = $DB->escape($_REQUEST["cat_address"]);
			$cat_comments = $DB->escape($_REQUEST["cat_comments"]);
			$cat_staffname = $DB->escape($_REQUEST["cat_staffname"]);
			$cat_trainer = $DB->escape($_REQUEST["cat_trainer"]);
			$cat_print = $DB->escape($_REQUEST["cat_print"]);
			$cat_web = $DB->escape($_REQUEST["cat_web"]);
			$cat_email = $DB->escape($_REQUEST["cat_email"]);
			$cat_receipt = $DB->escape($_REQUEST["cat_receipt"]);
                        $cat_finishtime = $DB->escape($_REQUEST["cat_finishtime"]);
			
			if (!isset($ERROR))
			{
			$sql = "update cal_cat set cat_name = '$cat_name' ,cat_colour = '$cat_colour' , cat_takesbookings = '$cat_takesbookings', cat_waitinglist = '$cat_waitinglist', 
                            cat_cost = '$cat_cost', cat_confirmation = '$cat_confirmation', cat_multiple = '$cat_multiple', cat_age = '$cat_age',cat_notes = '$cat_notes',
                            cat_staffname = '$cat_staffname', cat_comments = '$cat_comments', cat_address = '$cat_address', cat_trainer = '$cat_trainer', cat_print = '$cat_print', 
                            cat_web = '$cat_web', cat_email = '$cat_email', cat_receipt = '$cat_receipt', cat_finishtime = '$cat_finishtime' where cat_id = '$cat_id'";
		//	echo $sql;
			$DB->query($sql,"calendars.php");	
			}
		
	}
	
 elseif (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delbranch")
  {
  	
 	$branchno = $DB->escape($_REQUEST["branchno"]);
  	
 	$sql = "select count(*) as count from cal_events where event_location = '$branchno'";
 	$DB->query($sql,"calendars.php");

	$row = $DB->get();
	$count = $row["count"];
	
	if ($count == 0)
	{
 	
  	$sql = "delete from cal_branches where branchno = '$branchno'";
  	$DB->query($sql,"editcalendar.php");
  	
  	$DB->tidy("branches");
  	
  	$sql = "delete from cal_branch_bridge where location = '$branchno'";
  	$DB->query($sql,"editcalendar.php");
  	
  	$DB->tidy("branches");
	}
	else {$ERROR =  "<br><br><h1 class=\"red\">$phrase[1033]</h1>
	<a href=\"calendars.php?event=locations\">$phrase[34]</a>
	
	";}
  	
  }

  
  elseif (isset($_REQUEST["update"]) && $_REQUEST["update"] == "editbranch")
  {
  	
 	
  	
  	$branchname = $DB->escape($_REQUEST["branchname"]);
		$telephone = $DB->escape($_REQUEST["telephone"]);
		
        $sql = "update cal_branches set bname = '$branchname', telephone = '$telephone' where branchno ='$branchno'";

             $DB->query($sql,"editcalendar.php");
  	
  } 
  
elseif (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addtype")

	{
	
			$cat_name = $DB->escape($_REQUEST["cat_name"]);
			$cat_colour = "#" . $DB->escape($_REQUEST["cat_colour"]);
			$cat_takesbookings = $DB->escape($_REQUEST["cat_takesbookings"]);
			$cat_waitinglist = $DB->escape($_REQUEST["cat_waitinglist"]);
			$cat_cost = $DB->escape($_REQUEST["cat_cost"]);
			$cat_confirmation = $DB->escape($_REQUEST["cat_confirmation"]);
			$cat_multiple = $DB->escape($_REQUEST["cat_multiple"]);
			$cat_age = $DB->escape($_REQUEST["cat_age"]);
			$cat_notes = $DB->escape($_REQUEST["cat_notes"]);
			$cat_address = $DB->escape($_REQUEST["cat_address"]);
			$cat_comments = $DB->escape($_REQUEST["cat_comments"]);
			$cat_staffname = $DB->escape($_REQUEST["cat_staffname"]);
			$cat_trainer = $DB->escape($_REQUEST["cat_trainer"]);
			$cat_print = $DB->escape($_REQUEST["cat_print"]);
			$cat_web = $DB->escape($_REQUEST["cat_web"]);
			$cat_email = $DB->escape($_REQUEST["cat_email"]);
			$cat_receipt = $DB->escape($_REQUEST["cat_receipt"]);
                        $cat_finishtime = $DB->escape($_REQUEST["cat_finishtime"]);
			
			if (!isset($ERROR))
			{
			$sql = "insert into cal_cat values (NULL,'$cat_name','$cat_colour','$cat_takesbookings','$cat_waitinglist','$cat_cost','$cat_confirmation','$cat_multiple',
                            '$cat_age','$cat_notes','$cat_address','$cat_comments','$cat_staffname','$cat_trainer','$cat_print','$cat_web','$cat_email','$cat_receipt','$cat_finishtime')";
			$DB->query($sql,"calendars.php");
			}
	}
	

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "calendar")

	{
	
	$sql = "delete from cal_bridge where m = \"$m\"";
	$DB->query($sql,"calendars.php");
					
				if (isset($_REQUEST["uses"]))
						{	
					foreach ($_REQUEST["uses"] as $key => $value) 
						{
					if (isinteger($key) && (!isset($ERROR)))
					{
					$sql = "insert into cal_bridge (cat,m)values('$key','$m')";
					
					$DB->query($sql,"calendars.php");
					} else { $ERROR = "<div style=\"text-align:center\">$phrase[72]</div>";}
						
						}	
						}
						
						
						
		$sql = "delete from cal_branch_bridge where module = \"$m\"";
	$DB->query($sql,"calendars.php");
					
				if (isset($_REQUEST["loc"]))
						{	
					foreach ($_REQUEST["loc"] as $key => $value) 
						{
					if (isinteger($key) && (!isset($ERROR)))
					{
					$sql = "insert into cal_branch_bridge values('$m','$key')";
					
					$DB->query($sql,"calendars.php");
					} else { $ERROR = "<div style=\"text-align:center\">$phrase[72]</div>";}
						
						}	
						}
	
	}




include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div ><div><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>

";		
	
	  	
	  	
	  	
	  	
	  		if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "locations" )
	{
		echo "<a href=\"calendars.php\">Calendars</a> | <a href=\"calendars.php?event=types\">$phrase[1034]</a>";
	}
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "types"  ) 
	{
		echo "<a href=\"calendars.php\">Calendars</a> | <a href=\"calendars.php?event=locations\">$phrase[179]</a>";
	}
	elseif (isset($_REQUEST["event"]) && ($_REQUEST["event"] == "calendar"  || $_REQUEST["event"] == "edittype" || $_REQUEST["event"] == "addtype" || $_REQUEST["event"] == "editbranch" || $_REQUEST["event"] == "addlocation")) 
	{
	echo "<a href=\"calendars.php\">$phrase[159]</a> | <a href=\"calendars.php?event=types\">$phrase[1034]</a> | <a href=\"calendars.php?event=locations\">$phrase[179]</a>";	
	}
	else 
	{
		echo "<a href=\"calendars.php?event=types\">$phrase[1034]</a> | <a href=\"calendars.php?event=locations\">$phrase[179]</a>";
	}

	
	

if (isset($ERROR))
{
echo $ERROR;
}


elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deletetype")
{
			
	echo "<h2>$_REQUEST[name]</h2><br><b>$phrase[14]</b><br><br>
	<a href=\"calendars.php?update=deletetype&amp;event=types&amp;cat_id=$cat_id\">$phrase[12]</a> | <a href=\"calendars.php?&amp;event=types\">$phrase[13]</a>";

}


elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addlocation")		 
		 {
		 	echo "<h2>$phrase[179]</h2><form action=\"calendars.php\" style=\"width:80%;\"><fieldset><legend>$phrase[177]</legend><br>
<table style=\"margin:0 auto;text-align:left\">
<tr><td class=\"formlabels\"><b>$phrase[180] </b></td><td> <input type=\"text\" name=\"bname\" size=\"50\" maxlength=\"100\"></td></tr>
<tr><td class=\"formlabels\"><b>$phrase[132] </b></td><td> <input type=\"text\" name=\"telephone\" size=\"20\" maxlength=\"100\"></td></tr>

<tr><td></td><td>
<input type=\"submit\" name=\"addbranch\" value=\"$phrase[177]\">

<input type=\"hidden\" name=\"update\" value=\"addlocation\">
<input type=\"hidden\" name=\"event\" value=\"locations\">

</td></tr></table>
</fieldset>
</form>";


		 }

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edittype")
{
	
	
			
		$sql = "select * from cal_cat where cat_id = '$cat_id'";
		$DB->query($sql,"editcalendar.php");
		$row = $DB->get();
			
			$cat_id = $row["cat_id"];
			$cat_name = formattext($row["cat_name"]);
			$cat_colour = substr($row["cat_colour"],1);
			$cat_takesbookings = $row["cat_takesbookings"];
			$cat_waitinglist = $row["cat_waitinglist"];
			$cat_cost = $row["cat_cost"];
			$cat_confirmation = $row["cat_confirmation"];
			$cat_multiple = $row["cat_multiple"];
			$cat_age = $row["cat_age"];
			$cat_notes = $row["cat_notes"];
			$cat_staffname = $row["cat_staffname"];
			$cat_comments = $row["cat_comments"];
			$cat_address = $row["cat_address"];
			$cat_print = $row["cat_print"];
			$cat_trainer = $row["cat_trainer"];
			$cat_web = $row["cat_web"];
			$cat_email = $row["cat_email"];
			$cat_receipt = $row["cat_receipt"];
                        $cat_finishtime = $row["cat_finishtime"];
			
			
			echo "<h2>$phrase[1034]</h2><FORM method=\"POST\" action=\"calendars.php\" style=\"width:80%\">
			<fieldset><legend>$cat_name</legend><br><table style=\"text-align:left;\">
			
			<tr><td> $phrase[160]</td><td><input type=\"text\" name=\"cat_name\" size=\"50\" maxlength=\"100\" value=\"$cat_name\"></td></tr>
			<tr><td> $phrase[161]</td><td style=\"vertical-align:top\">
			
			<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
<input type=\"text\" name=\"cat_colour\" class=\"color\" value=\"$cat_colour\">";

			 $optionno =  "<option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option>";
			 $optionyes = "<option value=\"1\">$phrase[12]</option><option value=\"0\">$phrase[13]</option>";
			 echo "</td></tr>      
		
		<tr><td>$phrase[162]</td><td><select name=\"cat_takesbookings\">";
		$counter = 0;
		while ($counter < 2)
		{
		echo "<option value=\"$counter\"";
		if ($cat_takesbookings == $counter) {echo " selected";}
		echo ">";
		if ($counter == 0) {echo "$phrase[13]";}
		if ($counter == 1) {echo "$phrase[12]";}
	
		echo "</option>";
		$counter++;	
		}
		
		
		echo "</select></td></tr>
			<tr><td>$phrase[818]</td><td><select name=\"cat_web\">";
		$counter = 0;
		while ($counter < 3)
		{
		echo "<option value=\"$counter\"";
		if ($cat_web == $counter) {echo " selected";}
		echo ">";
		if ($counter == 0) {echo "$phrase[820]";}
		if ($counter == 1) {echo "$phrase[819]";}
		if ($counter == 2) {echo "$phrase[821]";}
		echo "</option>";
		$counter++;	
		}
		
		
		echo "</select></td></tr>
		
		<tr><td>$phrase[258]</td><td><select name=\"cat_email\">";
		$counter = 0;
		while ($counter < 2)
		{
		echo "<option value=\"$counter\"";
		if ($cat_email == $counter) {echo " selected";}
		echo ">";
		if ($counter == 0) {echo "$phrase[13]";}
		if ($counter == 1) {echo "$phrase[12]";}
	
		echo "</option>";
		$counter++;	
		}
		
		
		echo "</select></td></tr>
		
		
		
	
		<tr><td>$phrase[163]</td><td><select name=\"cat_confirmation\">";
			if ($cat_confirmation == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></td></tr>
		
		<tr><td>$phrase[253]</td><td><select name=\"cat_print\">
		<option value=\"0\"";
			if ($cat_print == 0)	{echo " selected";}
			echo ">$phrase[254]</option>
			<option value=\"1\"";
			if ($cat_print == 1)	{echo " selected";}
			echo ">$phrase[256]</option>
			<option value=\"2\"";
			if ($cat_print == 2)	{echo " selected";}
			echo ">$phrase[255]</option>
			<option value=\"3\"";
			if ($cat_print == 3)	{echo " selected";}
			echo ">$phrase[257]</option>";
		echo "</select></td></tr>
		
		
		<tr><td>$phrase[165]</td><td><select name=\"cat_waitinglist\">";
		if ($cat_waitinglist == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></td></tr>
		<tr><td>$phrase[166]</td><td><select name=\"cat_cost\">";
		if ($cat_cost == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></td></tr>
		
			<tr><td>$phrase[169]</td><td><select name=\"cat_staffname\">";
		if ($cat_staffname == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></td></tr>
		
				<tr><td>$phrase[170]</td><td><select name=\"cat_trainer\">";
		if ($cat_trainer == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></td></tr>
		
		<tr><td>$phrase[171]</td><td><select name=\"cat_comments\">";
		if ($cat_comments == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></td></tr>
		
		<tr><td>$phrase[172]</td><td><select name=\"cat_address\">";
		if ($cat_address == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></td></tr>
		
		
		<tr><td>$phrase[173]</td><td><select name=\"cat_age\">";
		if ($cat_age == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></td></tr>
		
		<tr><td>$phrase[167]</td><td><select name=\"cat_multiple\">";
		$counter = 1;
		while ($counter < 101)
		{
		echo "<option value=\"$counter\"";
		if ($counter == $cat_multiple) {echo " selected";}
		echo ">$counter</option>";
		$counter++;	
		}
		echo "</select></td></tr>
			<tr><td>$phrase[1070]</td><td><select name=\"cat_receipt\">";
		if ($cat_receipt == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></td></tr>
		
		
		
		<tr><td valign=\"top\">$phrase[168]</td><td><select name=\"cat_notes\">";
		if ($cat_cost == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></select></td></tr>
                        
                <tr><td valign=\"top\">Display finish time</td><td><select name=\"cat_finishtime\">";
		if ($cat_finishtime == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></select></td></tr>        
                        
                        
                        
		<tr><td></td><td>
		<input type=\"hidden\" name=\"cat_id\" value =\"$cat_id\">
		<input type=\"hidden\" name=\"event\" value =\"types\">
		<input type=\"hidden\" name=\"update\" value =\"edittype\"><br>
		<input type=\"submit\" name=\"updatecal\" value =\"$phrase[157]\">
		</td></tr>

			</table></fieldset></form>";
		
	
	
	
	
	
	
	

}


elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editbranch")
       {
       	
     
		     $sql = "select bname, telephone from cal_branches where branchno = '$branchno'";
			$DB->query($sql,"pcadmin.php");
			$row = $DB->get();
    		 $name = $row["bname"];
    		 $telephone = $row["telephone"]; 
    		 
      echo "<h2>$phrase[179]</h2>";
     

      echo "<form action=\"calendars.php\" method=\"post\" >
     <table >
     
     <tr><td class=\"formlabels\"> <b>$phrase[488]</b> </td><td><input type=\"text\" name=\"branchname\" value=\"$name\" size=\"60\" maxlength=\"100\"></td></tr>
      
       <tr><td class=\"formlabels\"> <b>$phrase[132]</b> </td><td><input type=\"text\" name=\"telephone\" value=\"$telephone\" size=\"60\" maxlength=\"100\"></td></tr>
   
<tr><td></td><td>

        <input type=\"hidden\" name=\"branchno\" value=\"$branchno\">
       <input type=\"hidden\" name=\"event\" value=\"locations\">  
      <input type=\"hidden\" name=\"update\" value=\"editbranch\">
      <input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
</td></tr></table>	 
</form>";
        
       
       }


elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addtype")
{
	
echo "<h2>$phrase[1034]</h2><FORM method=\"POST\" action=\"calendars.php\" style=\"width:80%\"><fieldset ><legend>$phrase[158]</legend>
<br><table style=\"text-align:left;\">
			<tr><td>$phrase[160]</td><td><input type=\"text\" name=\"cat_name\" size=\"50\" maxlength=\"100\"></td></tr>
			<tr><td>$phrase[161]</td><td>
			
			<script type=\"text/javascript\" src=\"../jscolor/jscolor.js\"></script>
<input type=\"text\" name=\"cat_colour\" class=\"color\" value=\"000000\">
			
			</td></tr>
       
      
	
		<tr><td>$phrase[162]</td><td>
			
		<select name=\"cat_takesbookings\">";
			
				$counter = 0;
		while ($counter < 2)
		{
		echo "<option value=\"$counter\">";
		if ($counter == 0) {echo "$phrase[13]";}
		if ($counter == 1) {echo "$phrase[12]";}
	
		echo "</option>";
		$counter++;	
		}
		
			echo "</select></td></tr>
				
			<tr><td>$phrase[818]</td><td>
			
		<select name=\"cat_web\">";
			
				$counter = 0;
		while ($counter < 3)
		{
		echo "<option value=\"$counter\">";
		if ($counter == 0) {echo "$phrase[820]";}
		if ($counter == 1) {echo "$phrase[819]";}
		if ($counter == 2) {echo "$phrase[821]";}
		echo "</option>";
		$counter++;	
		}
		
			
				
				
			echo "	</select></td></tr>	
				
				
				
					
		<tr><td>$phrase[258]</td><td><select name=\"cat_email\">";
		$counter = 0;
		while ($counter < 2)
		{
		echo "<option value=\"$counter\">";
		if ($counter == 0) {echo "$phrase[13]";}
		if ($counter == 1) {echo "$phrase[12]";}
	
		echo "</option>";
		$counter++;	
		}
		
		
		echo "</select></td></tr>";
	
		?>
					
				
		<tr><td><?php echo $phrase[163]?></td><td><select name="cat_confirmation"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select></td></tr>
		<tr><td><?php echo $phrase[253]?></td><td><select name="cat_print">
			<option value="0"><?php echo $phrase[254]?></option>
			<option value="1"><?php echo $phrase[256]?></option>
			<option value="2"><?php echo $phrase[255]?></option>
			<option value="3"><?php echo $phrase[257]?></option>
		
</select></td></tr>	
		<tr><td><?php echo $phrase[165]?></td><td><select name="cat_waitinglist"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select></td></tr>
		<tr><td><?php echo $phrase[166]?></td><td><select name="cat_cost"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select></td></tr>

		<tr><td><?php echo $phrase[167]?></td><td>
			<select name="cat_multiple">
			<?php
			$counter = 1;
		while ($counter < 101)
		{
		echo "<option value=\"$counter\">$counter</option>";
		$counter++;	
		}
			?>
			</select></td></tr>
		
		<tr><td valign="top"><?php echo $phrase[168]?></td><td><select name="cat_notes"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select></td></tr>
		<tr><td><?php echo $phrase[169]?></td><td><select name="cat_staffname"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select></td></tr>
		<tr><td><?php echo $phrase[170]?></td><td><select name="cat_trainer"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select></td></tr>
		<tr><td><?php echo $phrase[171]?></td><td><select name="cat_comments"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select></td></tr>
		<tr><td><?php echo $phrase[172]?></td><td><select name="cat_address"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select></td></tr>
		<tr><td valign="top"><?php echo $phrase[173]?></td><td><select name="cat_age"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select>
		<tr><td><?php echo $phrase[1070]?></td><td><select name="cat_receipt"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select></td></tr>
                 <tr><td valign="top">Display finish time</td><td><select name="cat_finishtime"><option value="0"><?php echo $phrase[13]?></option><option value="1"><?php echo $phrase[12]?></option></select></td></tr>     
                
                
				<tr><td></td><td>
				<input type="hidden" name="event" value ="types">
		<input type="hidden" name="update" value ="addtype"><br><br>
		<input type="submit" name="addcal" value ="<?php echo $phrase[158]?>">
		</td></tr>
		

			</table></fieldset></form>
			<?php
			
	
	
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "calendar")
{

	$locations = array();
	
	 $sql = "select location from cal_branch_bridge where module = \"$m\"";
	
	$DB->query($sql,"calendars.php");
	while ($row = $DB->get())
	     {
            $locations[] =$row["location"];
			
		}
	
	
	 $sql = "select cat from cal_bridge where m = \"$m\"";
	
	$DB->query($sql,"editcalendar.php");
	while ($row = $DB->get())
	     {
            $cat[] =$row["cat"];
			
		}
	$sql = "select name from modules where m = \"$m\"";
		$DB->query($sql,"editcalendar.php");
		$row = $DB->get();
		
		
	echo "
	<h2>$row[name]</h2><FORM method=\"POST\" action=\"calendars.php?m=$m\"  ><table><tr><td>";
	echo "
<table class=\"colourtable\" style=\"text-align:left;\"><tr><td colspan=\"2\"><b>$phrase[630]</b></td></tr>";
	 $sql = "select cat_id, cat_name from cal_cat order by cat_name";
	$DB->query($sql,"editcalendar.php");
	while ($row = $DB->get())
	     {
            $cat_id =$row["cat_id"];
            $cat_name =$row["cat_name"];
			echo "<tr><td>$cat_name</td><td><input type=\"checkbox\" name=\"uses[$cat_id]\"";
			
			if (isset($cat))
						{
						foreach ($cat as $key => $value) 
						{
									if ($cat_id == $value)
									{
									echo " checked";
									}
						
						}
						}
			
			echo "></td></tr>";
		}
	 echo "
	
	 </table>
	 
	  <script type=\"text/javascript\">
	  
	  function uncheck_top()
	  {
	  document.getElementById(\"loc0\").checked = false;
	  }
	  
	  
	  function clear_inputs()
	  {
	  
	  var inputs = document.getElementById('locationtable').getElementsByTagName('input');
			  for(i=0;i<inputs.length;i++){
    			inputs[i].checked = false;
  			}
	  
	  document.getElementById(\"loc0\").checked = true;
	  }
	  
	  
	  </script>
	
	  <input type=\"hidden\" name=\"m\" value=\"$m\">
	  <input type=\"hidden\" name=\"update\" value=\"calendar\"><br>

	
	  <input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
	 
	 
	 
	 </td><td>
	 
	 <table class=\"colourtable\" style=\"text-align:left;margin-left:3em\" id=\"locationtable\"><tr><td colspan=\"2\"><b>Locations limited to (optional)</b><br>By default all locations available</td></tr>
<tr><td><b>All locations</b></td><td><input type=\"checkbox\" id=\"loc0\" onclick=\"clear_inputs()\" name=\"loc[0]\"";
      
     if (in_array('0',$locations)) { echo " checked";}
      
      
      echo "></td></tr>	 
	 
	 
	 ";
	 
$sql = "select * from cal_branches  order by bname";

 $DB->query($sql,"calendars.php");
 while ($row = $DB->get())
      {
      $branchno = $row["branchno"];
      $bname = formattext($row["bname"]);
     
      echo "<tr><td>$bname</td><td><input type=\"checkbox\" onclick=\"uncheck_top()\" name=\"loc[$branchno]\"";
      
     if (in_array($branchno,$locations)) { echo " checked";}
      
      
      echo "></td></tr>";

}
	 
	 
	 echo "</table></td></tr></table>
	
	 </form>";	
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "types")
{
	
	 echo "
		<h2>$phrase[273]</h2>
		
		<br>
<a href=\"calendars.php?event=addtype\"><img src=\"../images/add.png\" title=\"$phrase[176]\"  alt=\"$phrase[176]\"></a><br><br>


		<table cellspacing=\"5\" style=\"text-align:left\">
		<tr style=\"font-weight:bold\"><td>$phrase[340]</td><td>$phrase[160]</td><td></td><td></td></tr>
		
		";

		$sql = "select * from cal_cat order by cat_name";
		$DB->query($sql,"editcalendar.php");
		while ($row = $DB->get())
		{
			$cat_id = $row["cat_id"];
			$cat_name = formattext($row["cat_name"]);
			$cat_colour = $row["cat_colour"];
			$linkname = urlencode($cat_name);
		
				echo "<tr><td>$cat_id</td><td ><span style=\"color:$cat_colour\">$cat_name</span></td><td> 
				<a href=\"calendars.php?event=edittype&amp;cat_id=$cat_id\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a> </td>
				<td> <a href=\"calendars.php?event=deletetype&amp;cat_id=$cat_id&amp;name=$linkname\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a> </td></tr>";
			
		}
	
	echo  "</table>
";
	
	
	
	
	
	

}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "locations")
{
	echo "<h2>$phrase[179]</h2>

";


$sql = "select * from cal_branches  order by bname";

 $DB->query($sql,"pc.php");


 echo "<a href=\"calendars.php?event=addlocation\"><img src=\"../images/add.png\" title=\"$phrase[522]\" alt=\"$phrase[522]\"></a><br><br>
 <table class=\"colourtable\" >";
while ($row = $DB->get())
      {
      $branchno = $row["branchno"];
      $bname = formattext($row["bname"]);
     
      echo "<tr><td>$bname</td><td>$branchno</td>
      <td><a href=\"calendars.php?event=editbranch&amp;branchno=$branchno\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
    
      <td><a href=\"calendars.php?update=delbranch&amp;event=locations&amp;branchno=$branchno\"><img src=\"../images/cross.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td></tr>";
      }
echo "

 </table>
 
";
}
else
{
		

	
	echo "
		<br>
		
		
	<h2>$phrase[159]</h2> Calendar modules are created via <a href=\"menu.php\">Intranet administration > Navigation menu</a><br><br>
	Once a calender module has been created. Calendar types and locations may be allocated by clicking on the calendar below.
	
	
	<br><br>
		<ul  class=\"listing\"  >";

		$sql = "select * from modules where menupath = \"calendar.php\" order by name";
		$DB->query($sql,"editcalendar.php");
		while ($row = $DB->get())
		{
		echo "<li><a href=\"calendars.php?m=$row[m]&amp;event=calendar\">$row[name]</a></li>";	
			
		}
	
	echo  "</ul>";
  
 	
	
	
	
	
	
	
	
	
	
	
}
	

		

echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

