<?php
//tests file was called properly
if (isset($m))
{



	
 	 

echo "
<form action=\"helpdesk.php\" method=\"post\"  style=\"width:80%;margin:0 auto\"> 
	<fieldset ><legend>$phrase[26]</legend><br>

	<table   style=\"width:80%;margin:0 auto\">
	<tr class=\"accent\"><td ><b>$phrase[340]</b></td>
	<td ><b>$phrase[217]</b></td><td><b>$phrase[690]</b></td><td ><b>$phrase[401]</b></td>";

if ($assignment == 1)
{
echo "<td ><b>$phrase[1059]</b></td>";
}
echo "</tr>";
	




	
	
	
	if ($DB->type == "mysql")
			{	
	$sql = "select *, UNIX_TIMESTAMP(datereported) AS datereported, UNIX_TIMESTAMP(datefixed) AS datefixed from helpdesk where probnum=\"$probnum\" ";
			}
	
	else
			{	
	$sql = "select *, strftime('%s',datereported) AS datereported, strftime('%s',datefixed) AS datefixed from helpdesk where probnum=\"$probnum\" ";
			}
			

	$DB->query($sql,"helpdesk.php");
	
	$row = $DB->get();
	$probnum = $row["probnum"];
	
	$datereported = strftime("%x",$row["datereported"]) . " " . date("g:i a",$row["datereported"]);
	
	$datefixed = strftime("%x",$row["datefixed"]);
	
	//$branch = formattext($row["branch"]);

	//$name = formattext($row["name"]);

	
	
	$query = $row["query"];

	$solution = $row["solution"];
	$status = $row["status"];
	$datefixed = $row["datefixed"];
	$cat = $row["cat"];
	$jobtime = $row["jobtime"];
	$assigned = $row["assigned"];	

			
	$display = secondsToMinutes($jobtime);
			
	
	//$allocatedto = formattext($row["allocatedto"]);
	
	$statusArray["1"] = $phrase[220]; 
	$statusArray["2"] = $phrase[400]; 
	$statusArray["3"] = $phrase[221]; 
	
        
         $templates = array();
	$sql = "select id, template_name from helpdesk_templates where m = '$m'";
        $DB->query($sql,"edithelpdesk.php");
        while ($row = $DB->get())
		{
		$id = $row["id"];
		$templates[$id] = $row["template_name"];
                }
                
              
	
	$category_name[] = $phrase[691] ;
	$category_id[] = 0 ;
	
	$sql = "select * from helpdesk_cat where m = '$m'";
	$DB->query($sql,"edithelpdesk.php");

while ($row = $DB->get())
      {	
      	$cat_name =  $row["cat_name"];
      	$id =  $row["id"];
      	$category_name[] = $cat_name ;
		$category_id[] = $id;
      }
	
	

	echo "<tr><td>$probnum</td><td>$datereported</td><td><select name=\"category\">";
	foreach ($category_name as $key => $value)
   {
   	echo "<option value=\"$category_id[$key]\" ";
   	if ($category_id[$key] == $cat) { echo " selected";}
   	echo ">$value</option>";
   }
	
	echo "</select>
	</td><td><select name=\"status\">";
	 foreach ($statusArray as $key => $value)
   {
   	echo "<option value=\"$key\" ";
   	if ($key == $status) { echo " selected";}
   	echo ">$value</option>";
   }
	echo "</select>
	
	

</td>";


if ($assignment == 1) 

{
	echo "<td >

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
      	echo "<option value=\"$userid\"";
      	if ($assigned == $userid) {echo " selected";}
      	echo ">$last_name, $first_name</option>";
      }
		
		echo "
		</select></td>";
		
}

echo "</tr>
<tr><td  colspan=\"5\" align=\"left\">";
	
		if ( $showclock == 1)
{
	//
	//onclick=\"(updatePage('ajax.php?event=starttimer&m=$m','clock');return false;)\"
	//onClick=\"alert('I\'ve been clicked!')\"
	echo "<br>
	<span id=\"clock\"><button id=\"clockbutton\" onclick=\"updatePage('ajax.php?event=starttimer&amp;m=$m&amp;probnum=$probnum','clock');runclock($jobtime);return false;\">Start timer</button> <span id=\"timer\">$display</span> $phrase[904]</span>";
}
	
		echo "</td></tr>
	
	<tr class=\"accent\"><td  ";
              if ($assignment == 1) { echo " colspan=\"5\" "; } else { echo " colspan=\"4\" "; }
 
        echo "align=\"left\"><b>$phrase[205]</b>
	
	
	
	</td></tr>


	<tr ><td  ";
         if ($assignment == 1) { echo " colspan=\"5\" "; } else { echo " colspan=\"4\" "; }
        echo "align=\"left\">
	<textarea name=\"query\"  id=\"query\" cols=\"70\" rows=\"10\" style=\"float:left\" >$query</textarea>";
                
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
	<tr class=\"accent\"><td  ";
         if ($assignment == 1) { echo " colspan=\"5\" "; } else { echo " colspan=\"4\" "; }
        echo "align=\"left\"><b>$phrase[206]</b></td></tr>
	 
	
	<tr><td ";
         if ($assignment == 1) { echo " colspan=\"5\" "; } else { echo " colspan=\"4\" "; }
        echo "align=\"left\">

	

		
		
		
		<textarea name=\"solution\" id=\"response\" cols=\"70\" rows=\"15\" style=\"float:left\">$solution</textarea>";
                
                if (count($templates) > 0)
        {
	echo "<div style=\"float:right;text-align:left;line-height:1.5em\"><b>$phrase[316]</b><br>";
	foreach ($templates as $id => $template_name)
                {
		echo "<span onclick=\"updatePage('ajax.php?event=helpdesktemplate&amp;id=$id&amp;m=$m','response','text','append')\">$template_name</span><br>";
		}
		echo "</div>";
        }
                echo "<p style=\"clear:both;float:left\">
                    <br>

		<input type=\"hidden\" name=\"probnum\" value=\"$probnum\">
		<input type=\"hidden\" name=\"m\" value=\"$m\">
	
		<input type=\"submit\" id=\"updatejob\" name=\"updatejob\" value=\"$phrase[28]\">";
		
	
		
		if ($email_action > 1)
		{
		echo "&nbsp;&nbsp;
		<input type=\"submit\" name=\"sendjob\" id=\"sendjob\" value=\"$phrase[63]\">";
		}
		
                echo "</p>";
		
		
		
if (isset($_REQUEST["keywords"]))
	{
	echo "
		<input type=\"hidden\" name=\"keywords\" value=\"$_REQUEST[keywords]\">
		";
	
	}		
		

if (isset($_REQUEST["view"]))
	{
		$view = $_REQUEST["view"]; 
	echo "
		<input type=\"hidden\" name=\"event\" value=\"$view\">
		";
	
	}
if (isset($_REQUEST["offset"]))
	{
	echo "
		<input type=\"hidden\" name=\"offset\" value=\"$_REQUEST[offset]\">
		";
	
	}
        
       
	//echo $sql;
	
  
echo "</td></tr></table></fieldset></form>




";
}
?>
