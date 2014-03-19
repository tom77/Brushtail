<?php

//tests file was called properly
if (isset($m))
{

      $templates = array();
	$sql = "select id, template_name from helpdesk_templates where m = '$m'";
        $DB->query($sql,"edithelpdesk.php");
        while ($row = $DB->get())
		{
		$id = $row["id"];
		$templates[$id] = $row["template_name"];
                }
                

echo "

	
    
	  

		<form action=\"helpdesk.php\" method=\"post\" style=\"width:80%;margin: 0 auto\"> 
		<fieldset><legend>$phrase[198]</legend>
                <table style=\"width:80%;margin: 0 auto;\">


                <tr><td>
	
		<b>$phrase[205]</b></td></tr>
<tr><td>
		<textarea name=\"query\"  id=\"query\" cols=\"60\" rows=\"5\" style=\"float:left\"></textarea>";
                
                               if (count($templates) > 0)
        {
	echo "<div style=\"float:right;text-align:left;line-height:1.5em\"><b>$phrase[316]</b><br>";
	foreach ($templates as $id => $template_name)
                {
		echo "<span onclick=\"updatePage('ajax.php?event=helpdesktemplate&amp;id=$id&amp;m=$m','query','text','append')\">$template_name</span><br>";
		}
		echo "</div>";
        }
		
		echo "</td></tr>
		<tr><td>
		<table cellpadding=\"5\" >
		
		<tr><td style=\"width:30%\"><b>$phrase[279]</b><br>

		<select name =\"category\">
		<option value=\"0\">$phrase[691]</option>";
		$sql = "select * from helpdesk_cat where m = '$m'";
	$DB->query($sql,"helpdeskadd.php");

while ($row = $DB->get())
      {	
      	$cat_name =  $row["cat_name"];
      	$id =  $row["id"];
      	echo "<option value=\"$id\">$cat_name</option>";
      }
		
		echo "</select>
		
		</td><td><b>$phrase[401]</b><br>

		<select name =\"status\">
		<option value=\"1\">$phrase[220]</option>
		<option value=\"2\">$phrase[400]</option>
		<option value=\"3\">$phrase[221]</option>
		
		</select>
		</td><td>";
		
		if ($assignment == 1)
		{
			echo "<b>$phrase[1059]</b><br>
	
			<select name =\"assignedtouser\">
		<option value=\"\">$phrase[495]</option>";
		
		$sql = "select distinct(user.userid) as userid, first_name, last_name from permissions, user, group_members where permlevel > '1' and m = '$m'  
		and (
		(type = 'i' and permissions.id = user.userid)
		or 
		(type= 'g' and permissions.id = group_members.groupid and group_members.userid = user.userid)
		) group by user.userid";
		
		$DB->query($sql,"helpdeskadd.php");
		
		while ($row = $DB->get())
      {	
      	$first_name =  $row["first_name"];
      	$last_name =  $row["last_name"];
      	$userid =  $row["userid"];
      	echo "<option value=\"$userid\">$last_name, $first_name</option>";
      }
		
		echo "
		</select>";
		}
		echo "</td></tr></table>
                
                </td></tr>

		
		<tr><td>

		<b>$phrase[206]</b></td></tr>
                <tr><td>

		<textarea name=\"solution\" id=\"response\" cols=\"60\" rows=\"5\" style=\"float:left\"></textarea>";
                
                             if (count($templates) > 0)
        {
	echo "<div style=\"float:right;text-align:left;line-height:1.5em\"><b>$phrase[316]</b><br>";
	foreach ($templates as $id => $template_name)
                {
		echo "<span onclick=\"updatePage('ajax.php?event=helpdesktemplate&amp;id=$id&amp;m=$m','response','text','append')\">$template_name</span><br>";
		}
		echo "</div>";
        }
                ?>
                
                
                
                </td></tr>
<tr><td>

		
	
		<input type="hidden" name="m" value="<?php echo $m?>">
		
		<input type="submit" name="add" value="<?php echo $phrase[176]?>" >
		
		
    </td></tr></table>
	
		
		
		</fieldset>
    	</form> 	

<?php
}
?>


