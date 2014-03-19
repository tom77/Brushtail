<?php

ini_set('display_errors', '1');




include("includes/initiliaze_page.php");





if (isset($_SESSION['userid']))
{
	


//session_unregister('userid') ;
//session_unregister('username') ;
//session_unregister('hash') ;
if (!(isset($_REQUEST["event"]) && $_REQUEST["event"] == "login"))
{
$homeurl = $url . homepath($DB);
header("Location: $homeurl");
}
}



if (isset($_COOKIE['loginid']))
{
$id = $_COOKIE['loginid'];

}

	
	

	
	
	

if (isset($_POST["username"]) && isset($_POST["password"]))
	{

	$auth = new Auth;
	$username = substr($_POST["username"],0,100);
	$password = substr($_POST["password"],0,100);
	
	$auth->check($DB,$username,$password,$PREFERENCES,$LDAPHOST,$LDAPUPDATE,$LDAPADMINDN,$LDAPADMINPASS,$LDAPDOMAIN,$MAILSERVER);
//	echo $auth->result;
	if ($auth->result <> "change" && $auth->result <> "passed")
	
	{
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{ $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];}
		else {
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		
		if ($DB->type == "mysql")
       	{
		$now = date("YmdHis");
       	}
       else
       	{
		$now = date("Y-m-d H:i:s");
       	}
       	$username = $DB->escape($username);
		$sql = "insert into loginsfailed values ('$username','$now','$ip');";
		$DB->query($sql,"login.php");
		
		
	}
	
	if ($auth->result == "expired")
		{
			
		header("Location: $url" . "main/expired.php");
		//ob_end_flush();
		exit();
		
		
		}
	elseif ($auth->result == "change" || $auth->result == "passed")
		{
		
		$_SESSION = array();
		//session_destroy();

		$_SESSION['userid'] = $auth->userid;
		$_SESSION['username'] = $auth->username;
		$_SESSION['hash'] = md5($auth->userid.$auth->username.$PREFERENCES["key"]);
		
		$cookie = md5(date("Ymdl").$auth->username.$PREFERENCES["key"]);
		
		
		
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{ $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];}
		else {
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		
		if ($DB->type == "mysql")
       	{
		$now = date("YmdHis");
       	}
    else
       	{
		$now = date("Y-m-d H:i:s");
       	}
       	
		$sql = "insert into logins values ('$auth->username','$now','$ip');";
		$DB->query($sql,"index.php");
		
		//set loginid
		setcookie ("cookieid",$cookie,"0","/" );
		
		$sql = "update user set cookieid = '$cookie' where userid = '$_SESSION[userid]'";
		$DB->query($sql,"index.php");
		
		if ($auth->result == "change")
		
		{
		
		$_SESSION['change'] = "True";	
		
		header("Location: $url" . "main/change.php");
		
		exit();
			
		}
		
		elseif ($auth->result == "passed")
		{
		//run any cron jobs if needed
		
		include 'includes/cron.php';
			
		$homeurl = $url . homepath($DB);
		
		
			header("Location: $homeurl");
		
		
		}
	}
		
		
		}
	

	
	

  $display = "stylesheets/".$PREFERENCES["stylesheet"];
 $print = "stylesheets/print.css";
		
		
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"
\"http://www.w3.org/TR/html4/strict.dtd\">
<html>
	<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
	<title>$PREFERENCES[sitename]</title>
    <LINK REL=\"STYLESHEET\" TYPE=\"text/css\" HREF=\"$display\" >
	<link href=\"$print\" rel=\"stylesheet\" type=\"text/css\" media=\"print\">

	</head>


<body>
		
<h1 style=\"text-align:center;margin-top:10em \">$PREFERENCES[sitename]</h1>
<div style=\"margin:4em auto;width:40%;\" >






	<div style=\"height:3em;\"> ";

if (isset($auth->result)) { echo "  <span style=\"font-size:200%;color:#ff3333\"><b>$phrase[2]</b></span>";}
echo "</div>



										
<FORM method=\"POST\" action=\"index.php\" name=\"login\" >
			<table  style=\"width:100%;margin:0 auto\"><tr><td style=\"width:50%;text-align:right;padding-right:1em\"><label>$phrase[3]</label></td><td><INPUT type=\"text\" name=\"username\" size=\"30\" maxlength=\"50\" id=\"username\">";


echo "</td></tr>
			<tr><td style=\"width:50%;text-align:right;padding-right:1em\"><label>$phrase[4]</label></td><td ><INPUT type=\"password\" name=\"password\"  size=\"30\" maxlength=\"50\"><br>
			
			</td></tr>
			<tr><td></td><td><INPUT type=\"submit\" name=\"submitlogin\" value=\"$phrase[29]\">
			</td></tr>
			</table>
			
		
			</form>
 <script type=\"text/javascript\">
 
 if (document.getElementById('username'))
 {
 var username =document.getElementById('username').focus();
}	
		
</script>
</div>	
</body>
</html>	
";
			
		

		
			
			
?>