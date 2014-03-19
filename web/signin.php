<?php


if(!include("../includes/initiliaze_page.php"))
 {
 	include("includes/initiliaze_page.php");
 }

#########################################################
#########################################################




 //if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "signup")
 //{
 //new user	
 	
 //}
//elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "remind")
//{
	//forgotten password
	
	
//}
//else 
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "password")
{
	
$url = "webcal.php";
 	 	if (isset($_REQUEST["event_id"])) 
 	{
  	 	$url = $url . "?event_id=" . $_REQUEST["event_id"];
 	}	
	
 	$password1 = $_REQUEST["password2"];
 	$password2 = $_REQUEST["password2"];
 	$p = $DB->escape($_REQUEST["p"]);
 	$r = $DB->escape($_REQUEST["r"]);
 	
 
		
	if ($password1 != $password2)
	{
		$ERROR = "$phrase[706] ";
	}
	else 
	{
			$sql = "select reg_email from registered_users where  reg_blocked = '0' and reg_password = '$p' and reg_random = '$r'";
		//echo $sql;
		$DB->query($sql,"signin.php");
		$row = $DB->get();
		$reg_email = $row["reg_email"];	
		$password = sha1($password1);
		
		$sql = "update registered_users set reg_password = '$password' where  reg_email = '$reg_email'";
		//echo $sql;
		$DB->query($sql,"signin.php");
		
		$_SESSION['eventlogin'] = $reg_email;
		header("Location: $url");	
		exit();
		
	}
		
		$sql = "select reg_email from registered_users where  reg_blocked = '0' and reg_random = '$password'";
		//echo $sql;
		$DB->query($sql,"signin.php");
		$num = $DB->countrows();
		if ($num == 1)
		{
		$row = $DB->get();
	
		$reg_email = $row["reg_email"];	
		$reg_id = $row["reg_id"];
			
		$sql = "update registered_users set reg_activated = '1' where reg_id = '$reg_id'";
		$DB->query($sql,"signin.php");
		
		$_SESSION['eventlogin'] = $reg_email;
		header("Location: $url");	
		exit();		
		}
}


if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "confirm")
{
	
$url = "webcal.php";
 	 	if (isset($_REQUEST["event_id"])) 
 	{
  	 	$url = $url . "?event_id=" . $_REQUEST["event_id"];
 	}	
	
	
	$random = $DB->escape($_REQUEST["random"]);
		
		$sql = "select reg_id, reg_email from registered_users where reg_activated = '0' and reg_blocked = '0' and reg_random = '$random'";
		//echo $sql;
		$DB->query($sql,"signin.php");
		$num = $DB->countrows();
		if ($num == 1)
		{
		$row = $DB->get();
	
		$reg_email = $row["reg_email"];	
		$reg_id = $row["reg_id"];
			
		$sql = "update registered_users set reg_activated = '1' where reg_id = '$reg_id'";
		$DB->query($sql,"signin.php");
		
		$_SESSION['eventlogin'] = $reg_email;
		header("Location: $url");	
		exit();		
		}
}


//echo $_REQUEST["event"];


if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "reset")

{
	
$email = $DB->escape($_REQUEST["email"]);		
	
	
if (preg_match('/@/', $email) == 0)
{
	$ERROR = $phrase[681] ;
}
else {
	
	$sql = "select reg_password, reg_random from registered_users where reg_email = '$email'";
	$DB->query($sql,"myevent.php");
	$num = $DB->countrows();
	if ($num == 0) {$ERROR = "No such email in use";}
	else 
	{
			$row = $DB->get();
	//$e = urlencode($email);
		$password = $row["reg_password"];	
		$r = $row["reg_random"];	
		//echo "email is $email";
		$url = $EMAIL_CONFIRMATION_URL . "signin.php?event=resetpassword&p=$password&r=$r";
	
	$message = "$phrase[979] $url
	
$EVENT_EMAIL_FOOTER";
	
	$headers = "From:$EVENTBOOKINGS_FROM";
	
	send_email($DB,$email,$phrase[979], $message,$headers);
	
	$ERROR = "message sent";
	}
}
	
}


if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "signin")
{

 	
$password = $_REQUEST["password"];	
$username = $_REQUEST["username"];			
	
	if (trim($password) == "" || trim($username) == "")
	{
		$ERROR = $phrase[60];
	}
	else {
		
		

		
	$url = "myevent.php";
 	 	if (isset($_REQUEST["event_id"])) 
 	{
  	 	$url = $url . "?event_id=" . $_REQUEST["event_id"];
 	}
 	
 
 	if ($EVENTAUTHENTICATION == "disabled")
 	{
 		exit();	
 	}
	elseif ($EVENTAUTHENTICATION == "registration")
	{
		

		
		$password = sha1($password);
		$username = $DB->escape($username);
		
		$sql = "select reg_id, reg_email from registered_users where reg_email = '$username' and reg_password = '$password' and reg_blocked = '0' and reg_activated = '1'";
		//echo $sql;
		


		$DB->query($sql,"signin.php");
		$num = $DB->countrows();
		if ($num == 1)
		{
		$row = $DB->get();
	
		$reg_email = $row["reg_email"];
		$reg_id = $row["reg_id"];
		
		$_SESSION['eventlogin'] = $reg_email;
		
				//echo "url is $url";
				//	print_r($_REQUEST);
			//	print_r($_SESSION);
//exit();
 	
		
		
		header("Location: $url");	
		exit();		
		}
		
	}
        
        
           elseif($PCAUTHENTICATION == "soap")
         {
               $barcode = $username;
               
         if (!file_exists("../includes/soap.php"))
         {
              echo "Soap file missing.";
             $ERROR = "Soap file missing.";
         }
         else {
             $password = substr(trim($_REQUEST["password"]),0,100);
         include("../includes/soap.php");
         
         if ($result["auth"] == "no")
                    {
		$ERROR = $result["failure"];
                    }
         
              
            else {
                    $_SESSION['eventlogin'] = $username;
                    header("Location: $url");	
                    exit();
                }
         }  
         
         }
        
        
	else {
	include("../classes/AuthenticatePatron.php");

	$CHECK = new AuthenticatePatron($password,$username,$PREFERENCES,$DB,"event");
	//echo "checking";
	//echo $CHECK->auth;
	if ($CHECK->auth == "no")
	{ 
		$ERROR = $CHECK->failure;

	}
		
	
	if ($CHECK->auth == "yes")
	{
	$_SESSION['eventlogin'] = $username;
	
	
 	
 	
	header("Location: $url");	
	exit();	
	}
	}
	
		
	}
	
	
}

	$heading = $phrase[961];
	
	
	include('calheader.php');
	

	
	 echo "<div><a href=\"$HOME_LINK\" style=\"margin-bottom:1em;\">$HOME_LABEL</a> | <a href=\"webcal.php\">$phrase[118]</a>"; 
	
	echo "<div style=\"margin-top:1em;color:red;font-size:200%\"> ";
	if (isset($ERROR)) {echo $ERROR;}
	echo "</div></div><br>";
	//print_r($_REQUEST);
	
	
if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "reset")
	{
	echo "<h3>$phrase[979]</h3>";	
		
	echo "<br><div style=\"margin-left:30%;text-align:left\">
	<form action=\"signin.php\" method=\"post\">
	<label></label> <input type=\"text\" name=\"email\">
	<input type=\"hidden\" name=\"update\" value=\"reset\">
	<input type=\"submit\" value=\"$phrase[259]\"><br><br>
	</form>
	An email will be sent to your email address allowing you to reset your password.
	</div>";	
	}
	
	
	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "resetpassword")
	{
		//int_r($_REQUEST);
	$p = $_REQUEST["p"];
	$r = $_REQUEST["r"];			
		
	echo "<h3>$phrase[22]</h3>";	
		
	echo "<div style=\"margin-left:40%;text-align:left\">
	<form action=\"signin.php\" method=\"post\">
	<label>$phrase[4]</label> <input type=\"password\" name=\"password1\" ><br>
	<label>$phrase[17]</label> <input type=\"password\" name=\"password2\"><br>
	<input type=\"hidden\" name=\"update\" value=\"password\">
	<input type=\"hidden\" name=\"p\" value=\"$p\">
	<input type=\"hidden\" name=\"r\" value=\"$r\">
	<input type=\"submit\" value=\"$phrase[22]\"><br><br>
	</form>

	</div>";	
	}
	else 
	{
	//signin
	echo "<div style=\"width:40%;float:left;\"><div style=\"text-align:left;padding:2em 10% 0 30%\">";
	
	if ($EVENTAUTHENTICATION == "registration")
	{
	echo "<b>Not registered?</b><br>
	To place a online booking in a library activity, you need to register with an email address.<br><br>
	<a href=\"signup.php";
	if (isset($_REQUEST["event_id"]))
	{	$event_id = $_REQUEST["event_id"]; echo "?event_id=$event_id";}
	echo "\">Register now!</a><br><br><br>
	<b>Forgotten password?</b><br><br>
	Click here to <a href=\"signin.php?event=reset\">$phrase[979]</a>.
	";
	
	
	
	}
	
	echo "</div></div><div><form action=\"signin.php\" style=\"text-align:left;width:60%padding-left:5em;float:left\" class=\"eventform\" method=\"post\">
	<h2>Sign In</h2>
	<label for=\"username\">";
	if ($EVENTAUTHENTICATION == "registration")
	{
		echo $phrase[259];
	}
	elseif ($EVENTAUTHENTICATION == "web")
	{
		echo $phrase[810];
	}
	else {
	echo "$phrase[3]";
	}
	echo "</label><input type=\"text\" name=\"username\">
	<label for=\"password\">$phrase[986]</label><input type=\"password\" name=\"password\">
	";
	
	if (isset($_REQUEST["event_id"]))
	{
		$event_id = $_REQUEST["event_id"];
		echo "<input type=\"hidden\" name=\"event_id\" value=\"$event_id\">";
	}
	
	echo "<input type=\"hidden\" name=\"event\" value=\"signin\">
	<input type=\"submit\" name=\"submit\" value=\"submit\" style=\"display:block\">

	</form><div>
		 

	
	";
	}
	

 ?>
 

 
 
 
</div>

</body>
</html>
