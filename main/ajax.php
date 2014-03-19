<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);



$integers[] = "t";
$integers[] = "probnum";
$integers[] = "time";
$integers[] = "locationid";
$integers[] = "id";
$integers[] = "process";

$ip = ip("pc");



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
		

if (isset($ERROR))
	{
	echo "$ERROR";
	}
else 
	{
	
	
		if 	($_REQUEST["event"] == "updatepd")
	{

		$id = $DB->escape($_REQUEST["id"]);
		$value = $DB->escape($_REQUEST["value"]);
		
		
	 	if ($_REQUEST["action"] == "attended")
	 	{
	 	$sql = "update pd_sessions set pd_attended = '$value' where pd_id = '$id'";	
	 	//echo $sql;
	 	$DB->query($sql,"ajax.php");
	 	if ($value == "1") {echo "#B1D8A9";}
	 	if ($value == "0") {echo "#E05C5C";}
	 	if ($value == "2") {echo "#bfc7ce";}	
	 	
	 	}
	 	
	 	
	 		if ($_REQUEST["action"] == "pdcat")
	 	{
	 	$sql = "update pd_sessions set pd_category = '$value' where pd_id = '$id'";	
	 	//echo $sql;
	 	$DB->query($sql,"ajax.php");
	 	
	 	echo "#ffffff";	
	 	
	 	}
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 		if ($_REQUEST["action"] == "approved")
	 	{
	 	$sql = "update pd_sessions set pd_approved = '$value' where pd_id = '$id'";	
	 	//	echo $sql;
	 	$DB->query($sql,"ajax.php");	
	 	if ($value == "1") {echo "#B1D8A9";}
	 	if ($value == "0") {echo "#E05C5C";}
	 	if ($value == "2") {echo "#bfc7ce";}
	 	}
	 	
	 
	 	
	}

	if 	($_REQUEST["event"] == "helpdesktemplate")
	{
	$sql = "select template from helpdesk_templates where  id = '$id' and m = '$m'";
	$DB->query($sql,"ajax.php");
	$row = $DB->get();
	$template = $row["template"];
	echo $template;
		
			
	}
		
	if 	($_REQUEST["event"] == "clicker")
	{
	 
		
		$counter = 0;
		echo "
		
		var json = {\"times\":[";
		foreach ($_REQUEST["c"] as $key => $value)
		{
			
			$values = explode(":",$value);
			$time = round($key / 1000);
			$cat = intval($values[0]);
			$num = intval($values[1]);
	
			
			$sql = "insert into clicker_clicks values(NULL,'$time','$locationid','$cat','$num','$ip')";
			//echo $sql;
			$DB->query($sql,"ajax.php");
			
			if ($counter != 0) {echo ",";}
			echo $key;
			
			
			
			$counter++;
			
		}
		echo "]}";

	}
	
	
	
	
	
	
	if 	($_REQUEST["event"] == "cal")
	{
	 
		$widget = new calWidget($m,$DB,$_REQUEST["name"],$_REQUEST["t"]);
		
		$widget->cal();
		$widget->listing();
	}

	
	 if ($_REQUEST["event"] == "callcounter") 
	 	{
		$datecalled = date("Y-m-d");
			
			
		 	$sql = "INSERT INTO itcalls (callno, day,m)
	    	VALUES(NULL,'$datecalled','$m')";
				$DB->query($sql,"helpdesk.php");
				
		$_month = date("m");
	$_year = date("Y");
	
				if ($DB->type == "mysql")
		{
			
	$sql = "SELECT count( * ) as total  FROM itcalls where month( day ) = \"$_month\" AND year( day ) = \"$_year\" and  \"$m\"";
		}
		
				else
		{
			
	$sql = "SELECT count( * ) as total  FROM itcalls where strftime('%m', day ) = \"$_month\" AND strftime('%Y',day ) = \"$_year\" and  \"$m\"";
		}
		
		
	$DB->query($sql,"ajax.php");
	$row = $DB->get();
	$counter = $row["total"];	
	echo "&#40;$counter&#41;";
		}

		
		
	 if ($_REQUEST["event"] == "starttimer") 
	 	{
	 		$now = time();
	 		
	 			
	 		$sql = "select jobtime from helpdesk where probnum = '$probnum'";
	 		$DB->query($sql,"ajax.php");
	 		$row = $DB->get();
			$jobtime = $row["jobtime"];
			
			$display = secondsToMinutes($jobtime);
			
	 		echo "<button id=\"clockbutton\" onclick=\"updatePage('ajax.php?event=stoptimer&m=$m&probnum=$probnum&time=$now','clock');stopclock();return false\">Stop timer</button> <span id=\"timer\">$display</span>";
	 	}
	 	
	 	 if ($_REQUEST["event"] == "stoptimer") 
	 	{
	 		$now = time();
	 		$duration = $now - $time;
	 		
	 		$sql = "update helpdesk set jobtime = jobtime + $duration where probnum = '$probnum'";
	 		$DB->query($sql,"ajax.php");
	 		
	 		$sql = "select jobtime from helpdesk where probnum = '$probnum'";
	 		$DB->query($sql,"ajax.php");
	 		$row = $DB->get();
			$jobtime = $row["jobtime"];
			
			$display = secondsToMinutes($jobtime);
			
	 		
	 		echo "<button id=\"clockbutton\" onclick=\"updatePage('ajax.php?event=starttimer&m=$m&probnum=$probnum','clock');runclock($jobtime);return false;\">Start timer</button> <span id=\"timer\">$display</span>";
	 	}
	 	
	 	
	 	
	 	
	 	 if ($_REQUEST["event"] == "processleave") 
	 	{
	 		
	 	$process++;
	 	if ($process == 3) {$process = 0;}	
	 		
	 		
	 		
	 	$sql = "update leave_requests set processed = '$process' where leave_id = '$id'";	
	 	$DB->query($sql,"ajax.php");
	 	//echo $sql;
	 	
	 	echo "<div onclick=\"process('$id','$process')\" style=\"padding:0.5em\"";
	 	
	 	if ($process == 1) { echo " class=\"yesoption\">$phrase[221]";}
	 	elseif ($process == 2) { echo " class=\"partoption\">$phrase[1067]";}
	 	else {echo  "class=\"nooption\">$phrase[13]";}
	 		//$message = "Processed:ye
	 	
	 
	 	echo "</div>";
	 	}
	 	
	 	
	 	
	 	
	 	 if ($_REQUEST["event"] == "processtravel") 
	 	{
	 		
	 	$process++;
	 	if ($process == 3) {$process = 0;}	
	 		
	 		
	 		
	 	$sql = "update travel_requests set processed = '$process' where travel_id = '$id'";	
	 	$DB->query($sql,"ajax.php");
	 	//echo $sql;
	 	
	 	echo "<div onclick=\"process('$id','$process')\" style=\"padding:0.5em\"";
	 	
	 	if ($process == 1) { echo " class=\"yesoption\">$phrase[221]";}
	 	elseif ($process == 2) { echo " class=\"partoption\">$phrase[1067]";}
	 	else {echo  "class=\"nooption\">$phrase[13]";}
	 		//$message = "Processed:ye
	 	
	 
	 	echo "</div>";
	 	}
	 	
	 		 if ($_REQUEST["event"] == "processstaffday") 
	 	{
	 		
	 	$sql = "update staffday set processed = '$process' where id = '$id'";	
	 	$DB->query($sql,"ajax.php");
	 	//echo $sql;
	 	if ($process == 1) {
	 		$message = "Processed:yes
	 		";
	 		echo "<p onclick=\"process('$id','1')\" class=\"yesoption\" style=\"margin:0;padding:1.2em\">processed</p>";}
	 	else 
	 	{
	 		$message = "Processed:no
	 		";
	 		echo "<p onclick=\"process('$id','0')\" class=\"noption\" style=\"margin:0;padding:1.2em\">pending</p>";}
	 		
	 	
	
	 	
	 	
	 	}
	 	
	 	
	 	
	 	
	 	
	 	
	}
	

?>

