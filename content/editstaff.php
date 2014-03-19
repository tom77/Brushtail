<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


	  	
	  	


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


$integers[] = "id";
$integers[] = "user";
$integers[] = "shiftid";

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
		
		echo $ERROR;
	//header("Location: $url" . "error.php?error=input");
	exit();
	}

	
	include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
		include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div>";

		
		$statusdisplay[1] = $phrase[416];
	$statusdisplay[2] = $phrase[417];
	$statusdisplay[3] = $phrase[418];
	
	
$weekdays[0] = $phrase[425];
$weekdays[1] = $phrase[419];
$weekdays[2] = $phrase[420];
$weekdays[3] = $phrase[421];
$weekdays[4] = $phrase[422];
$weekdays[5] = $phrase[423];
$weekdays[6] = $phrase[424];

		
		
		
		$access->cmenu();
	
		
	$sql = "select name from modules where m = \"$m\"";
	$DB->query($sql,"editstaff.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);
	echo "<h1 class=\"red\">$modname</h1>";
	

	
//delete postings older than a month	
$monthago = time() - 2592000;
$sql = "delete from staffday where starttime < '$monthago'";	
$DB->query($sql,"staff.php");	
	


	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updateuser")	
	{

	$sql = "delete from stafftemplate where staff = '$user'";
	$DB->query($sql,"staff.php");
		
	
		
		if (isset($_REQUEST["deleteuser"]))
		{
		$sql = "delete from staffday where staffid = '$user'";
	$DB->query($sql,"staff.php");	
		

	$sql = "delete from staff where staffnum = '$user'";
	$DB->query($sql,"staff.php");
		}
		
	if (isset($_REQUEST["updateuser"]))
		{	
		
	//$statusarray = $_REQUEST["daystatus"];
	$commentarray = $_REQUEST["comment"];
	


	
	
	
	foreach($commentarray as $index => $value)
	{
	$value = $DB->escape($value);
	$index = $DB->escape($index);
	
	
	
	$sql = "insert into stafftemplate values ('$index','$user','0','$value')";	
	$DB->query($sql,"editstaff.php");
	//echo $sql;
	
	}
	
		}
	
	}

//print_r($_REQUEST);	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "adduser")	
	{

	

	
	
	$sql = "insert into staff values ('$user','')";	
	$DB->query($sql,"editstaff.php");
	//echo "$sql";

	}
	

	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "send")	
	{
	$message = $_POST["message"];
	


	$sql = "select email from user where userid = '$user'";
	 $DB->query($sql,"staff.php");
	$row = $DB->get();
	$subject = "$modname";
	$staffemail = $row["email"];
	
	$sql = "select email from user
			where userid = '$_SESSION[userid]'";
			
			$DB->query($sql,"editstaff.php");
			$row = $DB->get();
			$fromemail = $row["email"];
			
			$headers = "From: $fromemail";
	//echo "sending";
		send_email($DB,$staffemail, $subject, $message,$headers);		
	
	


	}
		
	

	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "cancel")
		{
			
			$sql = "delete from staffday where id = '$shiftid'";	
			
			//echo $sql;
			$DB->query($sql,"staff.php");	
		
		}
		
	

	

	
	

	  	  		 $sql = "select staffnum, first_name, last_name from staff, user where staffnum = userid order by last_name";

	 $DB->query($sql,"staff.php");
	 $staffnum = array();
	while ($row = $DB->get())
		{
		$staffnum[] = $row["staffnum"];
		$first_name[] = $row["first_name"];
		$last_name[] = $row["last_name"];
	
		}	
if  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "send")
	{
		
		$sql = "select * from staffday,user where id = '$shiftid' and staffid = userid";
	//	echo $sql;
		$DB->query($sql,"staff.php");
		$row = $DB->get();
		
		$_first_name = $row["first_name"];	
		$_last_name = $row["last_name"];	
		$staffid = $row["staffid"];
		$shiftid = $row["id"];	
		$startdate = date("d/m/Y",$row["starttime"]);
		$enddate = date("d/m/Y",$row["endtime"]);
		$processed = $row["processed"];
		$comment = $row["details"];
		$status = $row["status"];
		$starttime = date("g.ia",$row["starttime"]);
		$endtime = date("g.ia",$row["endtime"]);
		
		$message = "Name: $_last_name, $_first_name
";
		
		if ($status == 0) { $message .= "Status: $phrase[416]
";} else {{ $message .= "Status: $phrase[418]
";}}
		

		
		if ($startdate != $enddate)
		{
		$message .= "When: $startdate - $endate
";
		}
		else {
			$message .= "When: $startdate $starttime-$endtime
";
		}
		
		$message .= "Comments: $comment

Response: ";
		
		echo "<h2>$phrase[63]</h2><form action=\"editstaff.php\" method=\"post\">
		<b>Message</b><br>
		<textarea cols=\"40\" rows=\"10\" name=\"message\">$message</textarea><br>
		<input type=\"submit\" value=\"send\">
			<input type=\"hidden\" name=\"update\" value=\"send\">
		<input type=\"hidden\" name=\"shiftid\" value=\"$shiftid\">
		<input type=\"hidden\" name=\"user\" value=\"$staffid\">
		<input type=\"hidden\" name=\"m\" value=\"$m\">
		</form>
		";
		
	}
	
	
	
elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delete")
	{
	
	echo "<br><br><br><br><b>$phrase[14]</b><br><br>$_REQUEST[name] <br><br>
	<a href=\"editstaff.php?m=$m&amp;event=day&amp;t=$t&amp;update=delete&amp;staffnum=$staffnum\">$phrase[12]</a> | <a href=\"editstaff.php?m=$m&amp;event=day&amp;t=$t\">$phrase[13]</a>";
		
	}
	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edituser")
		{
			
		
		$_staff = array();
		$_comment = array();	
				
		$sql = "select * from stafftemplate where staff = '$user'";	
		$DB->query($sql,"staff.php");
		while ($row = $DB->get())
		{
		$day = $row["dayname"];
		
		$comment = $row["comment"];
		
	
		$_comment[$day] = $comment;	
		}
			
		
		
			
			echo "<a href=\"editstaff.php?m=$m\">Listing</a>
			
			<br>
			<form action=\"editstaff.php\" method=\"post\">";
			
			 foreach ($staffnum as $key => $id)
	 {
	if ($id == $user) {echo "<h2>$last_name[$key], $first_name[$key]</h2>";}
	 }
			
			
			echo "<table>";
			
$w =  date("w");
$d = date("j");
$n = date("n");
$y = date("Y");

$sd = $d - $w + 2;
$time = mktime(0,0,0,$n,$sd,$y);

$counter = 0;

while($counter < 7)
{
$time = mktime(0,0,0,$m,$sd,$y);
$daynum = date("w",$time);
$dayname = strftime("%A",$time);

echo "<tr><td><b>$dayname</b></td><td><textarea name=\"comment[$daynum]\">";
if (key_exists($daynum,$_comment)) {echo $_comment[$daynum];}
echo "</textarea></td></tr>";
	
$sd++;
$counter++;	

}
echo "
<tr><td></td><td>
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"user\" value=\"$user\">
<input type=\"hidden\" name=\"update\" value=\"updateuser\">
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"submit\" name=\"updateuser\" value=\"$phrase[28]\">
<input type=\"submit\" name=\"deleteuser\" value=\"$phrase[24]\" style=\"margin-left:4em\">
</td></tr>

</table></form>";

		}
else {
	
	
			
		$processlabel[0] = "$phrase[1006]";	
		$processlabel[1] = "$phrase[1009]";		
			
		if (isset($_REQUEST["view"]) && $_REQUEST["view"] == "processed")
		{ 
		echo "<a href=\"editstaff.php?m=$m\">View pending</a>";
		$insert = "1";
		echo "<h2>Processed</h2>";
		$addtourl = "&amp;view=processed";
		}
		else {	
			echo "<a href=\"editstaff.php?m=$m&amp;view=processed\">View processed</a>";
				
		$insert = "0";
		echo "<h2>Pending</h2>";
		$addtourl = "";
		}

		
		echo "<table class=\"colourtable\">
		<tr style=\"font-weight:bold\"><td><a href=\"editstaff.php?m=$m&amp;orderby=last_name$addtourl\">Staff name</a></td><td><a href=\"editstaff.php?m=$m&amp;orderby=starttime$addtourl\">Start date</a></td><td>Finish date</td><td>Time</td><td>Change</td><td></td><td></td><td>Added</td><td></td><td></td></tr>";	
		
		$orderby = "";
		if (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "last_name") {$orderby = " order by last_name, first_name, starttime";}
		if (isset($_REQUEST["orderby"]) && $_REQUEST["orderby"] == "starttime") {$orderby = " order by starttime";}
		
		
		$sql = "select * from staffday,user where user.userid = staffday.staffid and processed = $insert $orderby";
		//echo $sql;
		$DB->query($sql,"staff.php");
		while ($row = $DB->get())
		{
		$_first_name = $row["first_name"];	
		$_last_name = $row["last_name"];	
		$staffid = $row["staffid"];
		$shiftid = $row["id"];	
		$startdate = date("d/m/Y",$row["starttime"]);
		$enddate = date("d/m/Y",$row["endtime"]);
		$processed = $row["processed"];
		$comment = $row["details"];
		$status = $row["status"];
		
		$starttime = date("g.ia",$row["starttime"]);
		$endtime = date("g.ia",$row["endtime"]);
		
		$updated = date("g.ia m/d/Y",$row["added"]);
		
		echo "<tr><td><b>$_last_name, $_first_name</b>";
		
		 
		
		echo "</td><td>$startdate</td><td>$enddate</td><td>";
		if ($startdate == $enddate) {echo "$starttime-$endtime";} else {echo "date range";}
		echo "</td><td";
		if ($status == 0) {echo " style=\"color:red\">$phrase[416]"; } else {echo  " style=\"color:green\">$phrase[418]";}
		echo "</td>
		<td>";
		if (trim($comment) != ""){
		echo "<span style=\"position:relative\" onmouseover=\"showelement('c$shiftid','30')\" onmouseout=\"hideelement('c$shiftid')\"><img src=\"../images/comments.png\">
		<p class=\"textballoon\" id=\"c$shiftid\">Comments: $comment</p></span>";
		}
		echo "</td><td><a href=\"editstaff.php?m=$m&amp;event=send&amp;shiftid=$shiftid\"><img src=\"../images/letter.png\"></a></td>
		
		<td style=\"color:grey;font-size:0.8em\">$updated</td><td style=\"padding:0\"><div style=\"padding:0\" id=\"p_$shiftid\"><p style=\"margin:0;padding:1.2em 0.5em\"";
	
		
		
		
	
		if ($processed == "0") {echo " onclick=\"process('$shiftid','1')\" class=\"partoption\"";} else {echo " onclick=\"process('$shiftid','0')\" class=\"yesoption\"";}
		echo ">$processlabel[$processed]</p></div></td><td><a href=\"editstaff.php?m=$m&amp;update=cancel&amp;shiftid=$shiftid";
		if (isset($_REQUEST["view"])) {echo "&amp;view=" . $_REQUEST["view"];}
		echo "\">Delete</a>
		</td></tr>
";
		}
		echo "</table>
		
		
		
		
		
		
			<script type=\"text/javascript\" >
			
			
		
			function process(id,value)
			{
			
			var element = 'p_' + id;
			//alert(element)
			var sel = document.getElementById(element);
			//var processed;
			//processed = sel.options[sel.selectedIndex].value;
			//alert(sel.options[sel.selectedIndex].text)
			//var processed = sel.options[sel.selectedIndex].text;
			//var processed
			if (value == 1) {sel.className = 'yesoption';}
			if (value == 0) {sel.className = 'nooption';}
			
			
			var timestamp = new Date();
			var url = '../main/ajax.php?m=$m&id=' +  id + '&event=processstaffday&process=' + value + '&rt=' + timestamp.getTime();
			//alert(url)
			updatePage(url,element)
			//alert
			}
			</script>
		
		
		
		
		
		
		
		
		
		";
	

}
	
   echo "</div></div>";
		
	 
	 echo "<div id=\"rightsidebar\"><div>";
	 
	 if ($access->thispage > 1)
	 {
	 echo "<h2>Staff</h2><ol class=\"listing\">";
	 
	 foreach ($staffnum as $key => $id)
	 {
	echo "<li><a href=\"editstaff.php?m=$m&amp;event=edituser&amp;user=$id\">$last_name[$key], $first_name[$key]</a></li>";
	 }
	 echo "</ol>
	 <br><br><form action=\"editstaff.php\" method=\"post\">
	 <b>Add user</b>
	  <select name=\"user\">
	 ";
	 $sql = "select * from user order by last_name";
	  $DB->query($sql,"staff.php");
	 
	while ($row = $DB->get())
		{
		$userid = $row["userid"];
		$first_name = $row["first_name"];	
		$last_name = $row["last_name"];
		
		if (!in_array($userid,$staffnum))
		{ echo "<option value=\"$userid\">$last_name, $first_name</option>";
		}
	 
	 }
	 
	 echo "</select>
 	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"adduser\">
	 
	 <input type=\"submit\" value=\"Add user\"></form>";
	 
	 
	 }
    echo "</div></div>";


include ("../includes/footer.php");
  
	
	

	
		
	
?>