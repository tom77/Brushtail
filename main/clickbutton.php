<?php
//
$limit = 10;

	
				
	

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


	
			



if (isset($_REQUEST["m"]))	
{
	
$m = $_REQUEST["m"];

if ((isinteger($m)))
{
	
	$access->check($m);
	
	if ($access->thispage < 1)
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
		$ERROR  =  "<h1>$phrase[72]</h1>";
	}
} else { $ERROR  =  "<h1>$phrase[866]</h1>";}

if (isset($ERROR))		
{
	echo "$ERROR";
	}
else		
{
	
	
	$sql = "select mode from clicker_options where m = '$m'";
	$DB->query($sql,"clickeradmin.php");
	$row = $DB->get();
	$mode = $row["mode"];
	
	if ($mode == "") {$mode = "1";}
	
	
	 $csslink = "../stylesheets/".$PREFERENCES["stylesheet"];
	 
	 
	$sql = "select name, ordering from modules where m = '$m'";
		$DB->query($sql,"helpdesk.php");
		$row = $DB->get();
		$modname = formattext($row["name"]);
		
		echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/transitional.dtd\">
<html>
	<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">

	<title>$modname</title>
 

         <LINK REL=\"STYLESHEET\" TYPE=\"text/css\" HREF=\"$csslink\"> 
 
	<link href=\"../stylesheets/print.css\" rel=\"stylesheet\" type=\"text/css\" media=\"print\">
	<script type=\"text/javascript\" src=\"../main/brushtail.js\"></script>
	</head><body class=\"accent\">";
	
	
	


	
	
	echo "<div id=\"clickbanner\" style=\"display:block;padding:0.3em;\">
	<h1 style=\"color:white;padding:0;margin:0;\">$modname</h1>
	<form   action=\"clickbutton.php\" method=\"post\" style=\"display:inline;margin:0;\">
	<select name=\"time\">
	<option value=\"now\">Now</option>";
	$counter = 1;
	while ($counter < 25)
	{
		echo "<option value=\"$counter\"";
		if(isset($_REQUEST["time"]) )
	{   
	if ($_REQUEST["time"] == "$counter") {echo " selected";}
	}
		echo ">$counter hours ago</option>";
		$counter++;
	}
	
	echo "</select> 
<select name=\"location_id\"><option value=\"none\">Select location</option>";
	
	$sql = "select location_id, location_name from clicker_location where m = '$m' and status = '1' order by position";
	$DB->query($sql,"clickbutton.php");
	while ($row = $DB->get())
	{
	
	$location_id = $row["location_id"];
	$location_name = $row["location_name"];
	echo "<option value=\"$location_id\"";
		
	if(isset($_REQUEST["location_id"]) )
	{   
	if ($_REQUEST["location_id"] == $location_id) {echo " selected";}
	}
	
	echo ">$location_name</option>";
	}
	echo " </select><input type=\"submit\" value=\"Go\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	</form></div><div style=\"float:left;padding:1em\">";
	if(isset($_REQUEST["location_id"]) && $_REQUEST["location_id"] != "none")
	{ 
	
	$sql = "select cat_id, cat_name from clicker_category where m = '$m' and status = '1' order by position";
	$DB->query($sql,"clickbutton.php");
	while ($row = $DB->get())
	{
	
	$cat_id = $row["cat_id"];
	$cat_name = $row["cat_name"];
	$categories[$cat_id] = "$cat_name";
	}
	
	if (!isset($categories))
	
	{
	echo "No categories defined.";	
	}
	else 
	{
	foreach ($categories as $id => $name)
    {
    	echo "<div style=\"float:left;text-align:left;padding:1em\">";
   
    	
    	
    	
    	if ($mode != "1") { echo "<input type=\"text\" id=\"input_$id\" size=\"2\" value=\"0\">"; }
    	echo "  	<span id=\"display_$id\"> 0</span><br>
    	<button id=\"bt_$id\" value=\"$id\" onclick=\"count($id)\">$name</button>
    	</div>";
    }
	}
	
   
	echo "</div>
	<script type=\"text/javascript\">
var locationid = $_REQUEST[location_id];
	var counter=0; 
	var mode='$mode';
	var display=new Array();
	var req = null;
	var num_buttons =";
	
	echo count($categories);
	echo "
";

	
	if (isset($_REQUEST["time"]) && $_REQUEST["time"] == "now")
	{
		echo "var offset = 0;
		";
	}
	else 
	{
		echo "var offset = $_REQUEST[time] * 3600000;
		";
	}
	
	
	
	foreach ($categories as $id => $name)
	{
		echo "display[$id] = 0;
		";
	}
?>
	

	
	

	function count(id)
	{
		
	
		var timestamp = new Date().getTime() - offset;
		//alert(timestamp)
	
		
		
		
		
		
		
		
		if (mode == "1") 
			{
			display[id]++;
			var num = ":0";
			}
		else
		{
		
		
		var input= document.getElementById('input_' + id);
		 var num = ":" + input.value;
		 //alert(input.value)
		 //alert(display[id])
		 display[id] = display[id] + parseInt(input.value);
		 input.value = 0;
		}
		
		var text= document.getElementById('display_' + id);
		text.innerHTML = display[id];
		
		var expires = new Date();
		expires.setDate(expires.getDate() + 30); // 7 = days to expire
		
		document.cookie="c[" + timestamp + "]=" + id + num + ";expires=" + expires.toGMTString();
		//alert("c[" + timestamp + "]=" + id)
	
		
	}
	

function send()
	{
	
	//alert("sending")
	
	if (req != null) { 
	//alert("still waiting for response"); 
	req = null;
	setTimeout("send()",5000);
	return;}
	
		var clicks = 0;
		var data = "";
		
		//for (key in c)
		//	{
		//	if (c[key] != null)
			//{
		//	data = data + "&c[" + key + "]=" + c[key];
		//	
		//	}
		//	}
		var cookies =document.cookie.split("; ");

		for (key in cookies)
			{
			if (cookies[key].indexOf("c[") == 0)
			{
			data = data + "&" + cookies[key]
			clicks++;
			}
			//alert("key is " + key + " value is " + cookies[key])
			}
	
			// alert(data)
	if (clicks == 0) { 
	//alert("no clicks"); 
	req = null;
	setTimeout("send()",5000);
	return;
	}

	
		
	
		
		
		
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
			
		}
		else 
			if (window.ActiveXObject) {
				try {
					req = new ActiveXObject("Msxml2.XMLHTTP");
				} 
				catch (e) {
					try {
						req = new ActiveXObject("Microsoft.XMLHTTP");
					} 
					catch (e) {
					}
				}
			}
		
		
		
		req.onreadystatechange = function(){
		
		
			if (req.readyState == 4) 
			{
			
			if (req.status == 200)
				{
				//alert("response is" + req.responseText)
				eval(req.responseText);
			

				for (key in json.times)
					{
				//	alert("json " + key + " " + json.times[key]);
			
					var string = "c[" + json.times[key] + "]=0"  + ";expires=Fri, 27 Jul 1970 02:47:11 UTC";
					//alert(string)
					document.cookie= string;
			
					}	
					
				req = null;
				setTimeout("send()",5000);
				
					}
				
				
			
			
			
			}
			
		}
		
		var timestamp = new Date();
	
		var url = "ajax.php?m=<?php echo $m ?>&event=clicker&locationid=" + locationid + "&rt=" + timestamp.getTime();
	
		req.open("POST", url, true); 
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");                  
		req.send(data);
		

	
		
		
	}
	
	


	
	window.onload=function()
	{

	setTimeout("send()",5000);
	}
	</script>
	
	<?php
}

}

echo "</body></html>";

?>