<?php




include ("../includes/initiliaze_page.php");
 $csslink = "../stylesheets/".$PREFERENCES["stylesheet"];

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/transitional.dtd\">
<html>
	<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">

	<title>Professional development attendance certificate</title>

  <LINK REL=\"STYLESHEET\" TYPE=\"text/css\" HREF=\"$csslink\"> 
	<link href=\"../stylesheets/print.css\" rel=\"stylesheet\" type=\"text/css\" media=\"print\">
<style>

* {font-family:sans-serif;}
body {margin:2em;background:white;}
td {padding:0.5em}
</style>
	</head>
	<body>
	";


$m = $_REQUEST["m"];
$access->check($m);
	

	$sql = "select email,showlogo, email_action, showprint, print_heading from pd_options where m = '$m'";
	$DB->query($sql,"pdprint.php");
	$row = $DB->get()	;
	$showprint = $row["showprint"];	
	$showlogo = $row["showlogo"];	
	$print_heading = $row["print_heading"];
	
	

	if ($access->thispage < 1)
		{
		
		$ERROR  =  "<div style=\"font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
		}
	elseif ($access->iprestricted == "yes")
		{
	$ERROR  =  $phrase[0];
		}

	
	elseif ($access->thispage > 1 || $access->thispage == 1)
	{
		
		echo "<div class=\"hide\" style=\"margin:1em;\">	<a href=\"javascript:window.print()\">$phrase[250]</a> &nbsp; <a href=\"pd.php?m=$m\">Back</a></div>";
		
		
		
		$year = $DB->escape($_REQUEST["year"]);
		$users = array();
		
		if ($access->thispage == 1 && $showprint == 1)
		{
			
		$user = $DB->escape($_SESSION["userid"]);
		
		if ($DB->type == "mysql")
			{
		$sql = "select userid , last_name, first_name from user,pd_sessions where year(pd_date) = '$year' and pd_approved = '1' and pd_attended = '1'  and user.userid = pd_sessions.pd_user and user.userid = '$user' group by userid";
			}
			
			
				else
			{
		$sql = "select userid , last_name, first_name from user,pd_sessions where strftime('%Y',pd_date) = '$year' and pd_approved = '1' and pd_attended = '1'  and user.userid = pd_sessions.pd_user  and user.userid = '$user' group by userid";
		
		//echo $sql;
			}
		
			
		}
		
		
				elseif (isset($_REQUEST["cat"]))
		{
			$id = $DB->escape($_REQUEST["cat"]);
			
			if ($DB->type == "mysql")
			{
		$sql = "select userid , last_name, first_name, count(pd_id) as count from user,pd_sessions, pd_categories where year(pd_date) = '$year' and pd_approved = '1' and pd_attended = '1'  and user.userid = pd_sessions.pd_user and pd_sessions.pd_category = pd_categories.id and id = '$id' group by userid";
			}
			
			else
			{
		$sql = "select userid , last_name, first_name, count(pd_id) as count from user,pd_sessions,pd_categories where strftime('%Y',pd_date) = '$year' and pd_approved = '1' and pd_attended = '1'  and user.userid = pd_sessions.pd_user and pd_sessions.pd_category = pd_categories.id and pd_categories.id = '$id' group by userid";
			}
		
			
			
		}
		
		elseif ($_REQUEST["user"] == "all")
		{
		
			if ($DB->type == "mysql")
			{
		$sql = "select userid , last_name, first_name, count(pd_id) as count from user,pd_sessions where year(pd_date) = '$year' and pd_approved = '1' and pd_attended = '1'  and user.userid = pd_sessions.pd_user group by userid";
			}
			
			else
			{
		$sql = "select userid , last_name, first_name, count(pd_id) as count from user,pd_sessions where strftime('%Y',pd_date) = '$year' and pd_approved = '1' and pd_attended = '1'  and user.userid = pd_sessions.pd_user group by userid";
			}
		
				
			
		}
		else 
		{
			
			
			$user = $DB->escape($_REQUEST["user"]);
		//$sql = "select userid , last_name, first_name from user where userid = '$user'";	
		if ($DB->type == "mysql")
			{
		$sql = "select userid , last_name, first_name from user,pd_sessions where year(pd_date) = '$year' and pd_approved = '1' and pd_attended = '1'  and user.userid = pd_sessions.pd_user  and user.userid = '$user'  group by userid";
			}
			
			
				else
			{
		$sql = "select userid , last_name, first_name from user,pd_sessions where strftime('%Y',pd_date) = '$year' and pd_approved = '1' and pd_attended = '1'  and user.userid = pd_sessions.pd_user  and user.userid = '$user'  group by userid";
			}
		
	
		}
		
		//echo "<br><br> $sql";
		
		
		$DB->query($sql,"pdprint.php");
		while ($row = $DB->get()) 
					{
					$users[] = $row["userid"];	
					$first_name[] = htmlspecialchars($row["first_name"]);	
					$last_name[] = htmlspecialchars($row["last_name"]);	
					}
		
		
		foreach ($users as $key => $value)
		{
			
			if ($DB->type == "mysql")
			{
		$sql = "SELECT pd_id, UNIX_TIMESTAMP(pd_date) as pd_date, pd_title, pd_external, pd_hours, pd_cost, pd_approved, pd_attended, pd_user,last_name FROM pd_sessions,user where   pd_user = '$value' and year(pd_date) = '$year' and user.userid = pd_sessions.pd_user and pd_approved = '1' and pd_attended = '1' order by pd_date";
		
			}
		else
			{
		$sql = "SELECT pd_id, pd_title,strftime('%s',pd_date) as pd_date, pd_external, pd_hours, pd_cost, pd_approved, pd_attended, pd_user,last_name FROM pd_sessions,user where  pd_user = '$value' and strftime('%Y',pd_date) = '$year'  and user.userid = pd_sessions.pd_user and pd_approved = '1' and pd_attended = '1' order by pd_date ";
			}
		//	echo $sql;
				echo "<div STYLE=\"";
				
				if (count($users) > 1) { echo "page-break-after:always;";}
				
				if ($showlogo == 1)	{ echo "border:black 1px solid;padding:1em;";}
				else { echo "margin-top:16em;";}
				
				echo "\">";
				
			if ($showlogo == 1)	{ echo "<img src=\"../images/pdbanner.gif\">";}
				
echo "
<h2>$print_heading</h2>
<h2 style=\"color:gray;font-size:2em\">$first_name[$key] $last_name[$key]</h2>
<table class=\"colourtable\">
<tr style=\"font-weight:bold\"><td>Title</td><td>Date</td><td>Hours</td></tr>";
				
				$DB->query($sql,"pdedit.php");
				
		while ($row = $DB->get()) 
					{
					$pd_title = htmlspecialchars($row["pd_title"]);	
					$pd_id = htmlspecialchars($row["pd_id"]);	
					$pd_external = htmlspecialchars($row["pd_external"]);
					$pd_hours = htmlspecialchars($row["pd_hours"]);
					$pd_cost = htmlspecialchars($row["pd_cost"]);
					$pd_approved = htmlspecialchars($row["pd_approved"]);
					$pd_attended = htmlspecialchars($row["pd_attended"]);
					$pd_user = htmlspecialchars($row["pd_user"]);
					$pd_date = strftime("%x",$row["pd_date"]);
					
					
					echo "<tr><td>$pd_title</td><td>$pd_date</td><td>$pd_hours</td></tr>";
					}	
					
			echo "</table>
			<br><br>
			<span style=\"color:gray\">Printed ";
			echo strftime("%x");
			echo "</span></div>";
		}

			
			
			
			
		
		

		

			
		
	}
?>
</body>
</html>