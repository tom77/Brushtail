<?php


if(!include("../includes/initiliaze_page.php"))
 {
 	include("includes/initiliaze_page.php");
 }

#########################################################
#########################################################


  if (isset($SPAMTOOLS) && ($SPAMTOOLS == "all" || $SPAMTOOLS == "web"))
   {
$salt = uniqid(); 


salt($salt);                
   } 

 //if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "signup")
 //{
 //new user	
 	
 //}
//elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "remind")
//{
	//forgotten password
	
	
//}
//else 
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "signup")
{
	
$password1 = $DB->escape($_REQUEST["password1"]);	
$password2 = $DB->escape($_REQUEST["password2"]);	
$lname = $DB->escape($_REQUEST["lname"]);
$fname = $DB->escape($_REQUEST["fname"]);	
$telephone = $DB->escape($_REQUEST["telephone"]);	
$email1 = $DB->escape($_REQUEST["email1"]);		
$email2 = $DB->escape($_REQUEST["email2"]);	

/*
require_once('recaptchalib.php');   
	$privatekey = "6Le3R9ASAAAAAD9x-iGc2n3bdNSB_rAyVc17W0D-";  
	 $resp = recaptcha_check_answer ($privatekey,  $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"],             $_POST["recaptcha_response_field"]);    
	 if (!$resp->is_valid) {     // What happens when the CAPTCHA was entered incorrectly   
             
	   $ERROR = "The reCAPTCHA wasn't entered correctly! Please try again.";  
	  } 
 * 
 * 
 * 
 */


  if (isset($SPAMTOOLS) && ($SPAMTOOLS == "all" || $SPAMTOOLS == "web"))
  {
       $result = spamTools($phrase);
       
     if ($result["status"] == 0) {$ERROR = $result["error"];} 
      
  }

if (isset($CAPTCHA) && function_exists('imageftbbox') &&  ($CAPTCHA == "all" || $CAPTCHA == "web")) 
{
include('securimage/securimage.php');
$securimage = new Securimage();

if ($securimage->check($_POST['captcha_code']) == false)
{
    
    $ERROR = "The CAPTCHA wasn't entered correctly. Try again"; 
}
}



if ($email1 != $email2)
{
	$ERROR =  $phrase[967];
}
elseif (preg_match('/@/', $email1) == 0)
{
	$ERROR = $phrase[681] ;
}

	

elseif ($email1 != $email2)
{
	$ERROR = $phrase[967];
}

elseif ($password1 != $password2)
{
	$ERROR = $phrase[706];
}

elseif (trim($email1) == "" || trim($password1) == "")
{
	$ERROR = $phrase[60];
}


if (!isset($ERROR))
{
	$sql = "select reg_id from registered_users where reg_email = '$email1'";
	//echo $sql;
	$DB->query($sql,"myevent.php");
	$num = $DB->countrows();
	if ($num > 0) {$ERROR = "$phrase[968]";}
}



if (!isset($ERROR))
{
	$now = time();
	$random = random(32);
	$password = sha1($password1);
	
	$sql = "insert into registered_users values(NULL,'$email1','$fname','$lname','$telephone','0','0','$now','$random','$password')";
	//echo $sql;
	$DB->query($sql,"myevent.php");
	
	$url = $EMAIL_CONFIRMATION_URL . "signin.php?event=confirm&random=" . $random;
	
	$message = "$phrase[971] $url
	
$EVENT_EMAIL_FOOTER";
	
	$headers = "From:$EVENTBOOKINGS_FROM";
	
	send_email($DB,$email1,$phrase[972], $message,$headers);
	
	$confirmation_message = $phrase[973];
}




}


$heading = $phrase[966];

	include('calheader.php');
	 	echo "<a href=\"$HOME_LINK\" style=\"margin-bottom:1em;\">$phrase[984]</a> | ";
	 echo "<a href=\"webcal.php\">$phrase[118]</a>"; 
	
	echo "<div  style=\"margin-top:1em;color:red;font-size:200%\">";
	if (isset($ERROR)) {echo $ERROR . "<br><br>";}
	echo "</div>";
	//print_r($_REQUEST);
	
	
	if (isset($confirmation_message))
	{
		echo $confirmation_message;
	}
	else 
	{
	//signin
	echo "
             <script type=\"text/javascript\">
 var RecaptchaOptions = {
    theme : 'clean'
 };
 </script>
            
            <form action=\"signup.php\" class=\"eventform centerform\" method=\"post\">

<label for=\"email1\">$phrase[259]</label><input type=\"text\" size = \"50\" name=\"email1\" id=\"email1\" onchange=\"check()\" onkeyup=\"check()\"  "; if (isset($_REQUEST["email1"])) {echo " value=\"$_REQUEST[email1]\"";} echo "><br><br>
<label for=\"email2\">$phrase[965]</label><input type=\"text\" size = \"50\" name=\"email2\" id=\"email2\" onchange=\"check()\" onkeyup=\"check()\"  "; if (isset($_REQUEST["email2"])) {echo " value=\"$_REQUEST[email2]\"";} echo "><br><br>
<label for=\"fname\">$phrase[130]</label><input type=\"text\" size = \"40\" name=\"fname\" "; if (isset($_REQUEST["fname"])) {echo " value=\"$_REQUEST[fname]\"";} echo "><br><br>
<label for=\"lname\">$phrase[131]</label><input type=\"text\" size = \"40\" name=\"lname\" "; if (isset($_REQUEST["lname"])) {echo " value=\"$_REQUEST[lname]\"";} echo "><br><br>
<label for=\"telephone\">$phrase[132]</label><input type=\"text\" name=\"telephone\""; if (isset($_REQUEST["telephone"])) {echo " value=\"$_REQUEST[telephone]\"";} echo "><br><br>
<label for=\"password1\">$phrase[32]</label><input type=\"password\" name=\"password1\" id=\"password1\" onchange=\"check()\" onkeyup=\"check()\"  "; if (isset($_REQUEST["password1"])) {echo " value=\"$_REQUEST[password1]\"";} echo "><br><br>
<label for=\"password2\">$phrase[17]</label><input type=\"password\" name=\"password2\" id=\"password2\" onchange=\"check()\" onkeyup=\"check()\"  "; if (isset($_REQUEST["password1"])) {echo " value=\"$_REQUEST[password1]\"";} echo "><br><br>";

if (isset($CAPTCHA)  && function_exists('imageftbbox') &&  ($CAPTCHA == "all" || $CAPTCHA == "web")) 
{
echo "To prevent spam joinups retype the text in the captcha field below.

<b>CAPTCHA: please retype text in image below</b><br><br><img id=\"captcha\" src=\"securimage/securimage_show.php\" alt=\"CAPTCHA Image\" /><br>
<input type=\"text\" name=\"captcha_code\" size=\"10\" maxlength=\"6\" />
	<a href=\"#\" onclick=\"document.getElementById('captcha').src = 'securimage/securimage_show.php?' + Math.random(); return false\">[ Different Image ]</a>    


<br>";
}

             if (isset($SPAMTOOLS) && ($SPAMTOOLS == "all" || $SPAMTOOLS == "web"))
 {
   
             
             echo "<label style=\"display:block;position:absolute;top:2em;left:-9999px\">
$phrase[1121]
  <input type=\"text\" name=\"spam1\">
</label>
<label style=\"display:block;position:absolute;top:4em;left:-9999px\">
  $phrase[1121]
  <input type=\"text\" name=\"salt\" value=\"$salt\" >
</label>";
             
 }




	if (isset($_REQUEST["event_id"]))
	{
		$event_id = $_REQUEST["event_id"];
		echo "<input type=\"hidden\" name=\"event_id\" value=\"$event_id\">";
	}
	
	echo "<input type=\"hidden\" name=\"update\" value=\"signup\">
	<input type=\"submit\" name=\"submit\" value=\"submit\" style=\"display:block\" id=\"submit\"><span id=\"warning\" style=\"color:red\"></span>

	</form>
	
	
		
		 
 <script type=\"text/javascript\">
	 
	 function check()
	 {
	 if (!document.getElementById) {alert(\"bye\"); return true;}
	 
	 var disabled = false;
	 document.getElementById('warning').innerHTML = '';
	 
	 var password1 = document.getElementById('password1').value;
	 var password2 = document.getElementById('password2').value;
	 var email1 = document.getElementById('email1').value;
	 var email2 = document.getElementById('email2').value;
	 
	 if (password1 == '' || password2 == '' || email1 == '' || email2 == '') { disabled = true;}
	 
	 if (password1 != '' && password2 != '' && password1 != password2) { disabled = true; document.getElementById('warning').innerHTML = '$phrase[706]';}
	 if (email1 != '' && email2 != '' && email1 != email2) { disabled = true; document.getElementById('warning').innerHTML = '$phrase[967]';}
	 
	if (email2 != '' && email2.indexOf('@') == -1) { disabled = true; document.getElementById('warning').innerHTML = '$phrase[682]';}
	 
	 if (disabled == true) {document.getElementById('submit').disabled = true;}
			else {document.getElementById('submit').disabled = false;}
	  
	 }
	 
	 window.onload=check
	 
	 </script>
	
	
	";
	
	}

 ?>
 

 
 
 
</div>

</body>
</html>
