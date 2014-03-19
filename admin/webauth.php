<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");



include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1><h2>$phrase[915]</h2>

	";


$day = date("d");
$month = date("m");

$year = date("Y");  




if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "100")
{
	
	echo "<a href=\"webauth.php\">Today's authentications</a><br><br><h4>Last 100 Web authentications</h4>";

	
				if ($DB->type == "mysql")
       	{
	$sql = "select * from web_auth_log  order by authtime desc limit 100";
       	}
	
       	
       				else
       	{
		$sql = "select * from web_auth_log  order by authtime desc limit 100";
       	}

		
}
else 
{
	
	echo "<a href=\"webauth.php?event=100\">Last 100 Web authentications</a><br><br><h4>Today's authentications</h4>";
	
				if ($DB->type == "mysql")
       	{
	$sql = "select * from web_auth_log where day(FROM_UNIXTIME(authtime)) = \"$day\" and month(FROM_UNIXTIME(authtime)) = \"$month\" and year(FROM_UNIXTIME(authtime)) = \"$year\" order by authtime desc ";
       	}
	
       	
       				else
       	{
	$sql = "select * from web_auth_log where strftime('%d',datetime ( authtime , 'unixepoch' )) = \"$day\" and strftime('%m',datetime ( authtime , 'unixepoch' )) = \"$month\" and strftime('%Y',datetime ( authtime , 'unixepoch' )) = \"$year\" order by authtime desc ";
       	}
	
	
}



	
	
	
	 $DB->query($sql,"webauth.php");
	 
	$counter = 0;
	while ($row = $DB->get())
	{
	
	$ips[] = 	$row["ip"];
		
	if ($counter == 0) {$end = $row["authtime"];}	
	$start = $row["authtime"];	
	$authtime = date("h.i:s a",$row["authtime"]);
	$authdate = date("d/m/Y",$row["authtime"]);
	$ip = $row["ip"];
	$authtimes[] = 	$authtime;
	$authdates[] = 	$authdate;	

	$counter++;	
	}
	
	
	
	if ($counter == 0){$rate = 0;}
	elseif ($counter == 1){$rate = 0;}
	
	else {
		$diff = $end - $start;
		
		$rate = round($counter / ($diff / 60),1);
	
	}  
	
	if (!isset($_REQUEST["event"]))
	{
	echo "<br>Average rate: $rate authentications per minute.<br>";	
		
	}
	
	
	
	
	echo "	<table style=\"text-align:left;\" class=\"colourtable\">
	<tr><td><b>$phrase[185]</b>  (hour.minute:second)</td><td><b>$phrase[186]</b></td><td><b>$phrase[144]</b></td></tr>";
	
	
	if (isset($authtimes))
	{
	foreach($authtimes as $index => $value)
	{
		echo "<tr><td>$value</td><td>$authdates[$index]</td><td>$ips[$index]</td></tr>";
	}
	}
	echo "</table>";

	
echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");
	

?>

