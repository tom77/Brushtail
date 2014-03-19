<?php

if (isset($m))
	{

	
    
    
    
        if (isset($SPAMTOOLS) && ($SPAMTOOLS == "all" || $SPAMTOOLS == "internal"))
 {
   $salt = uniqid(); 


salt($salt);
 }
    
    
    
    
    
	
echo "<h1>$modname</h1>";
	
	if (isset($search) && $search > 1)
	{echo "<p><b><a href=\"helpdesksearch.php?m=$m\">$phrase[282]</a></b><br><br></p>";}
	
	$sql = "select * from forms where module = \"$m\"";

$DB->query($sql,"editform.php");

$row = $DB->get();
$email = $row["email"];
$banner = nl2br(trim($row["banner"]));
$subject = $row["subject"];
$from = $row["emailfrom"];
$cc_user = $row["cc_user"];

	
		
if (isset($_REQUEST["submit"]))

{

	$text = "";
	
$sql = "select * from fieldset where form = \"$m\" order by ranking";
$DB->query($sql,"editform.php");


$counter = 0;
while ($row = $DB->get()) 
		{
		$counter++;
		$fieldsetno[] = $row["fieldset"];
		$legend[] = $row["legend"];
		$fieldordering[] = $row["fieldordering"];
		$ranking[] = $row["ranking"];
		
		}	
	//print_r($_REQUEST);
	
	if (isset($fieldsetno))
	{
	foreach($fieldsetno as $index => $fieldsetid)	
	{
	$paragraph = "";

		if ($fieldordering[$index] == "c") {$order = "ranking";} else { $order = "label";}
		
		
		$sql = "select * from formfields where fieldset = \"$fieldsetno[$index]\" order by $order";
		$DB->query($sql,"editform.php");
		
	
		
		while ($row2 = $DB->get()) 
		{
		$counter++;
		$fieldno = $row2["fieldno"];
		$label = $row2["label"];
		$compulsory = $row2["compulsory"];
		$output = $row2["output"];
		$type = $row2["type"];
		
		
		
		
		
		
		//check for empty fields
    	
	
		if (isset($_REQUEST["other"])) {$other = $_REQUEST["other"];}
		 
		foreach($_REQUEST["input"] as $fno => $fvalue)	
			{
			
			
			
				
			if ($from == $fno)	
			{
			$from = $fvalue;	
		
			}

			if ($subject == $fno)	
			{
			$subject = $fvalue;	
			
			}
				
			if ($fno == $fieldno) 
				{
				//test if compulsory field is empty
				if ($compulsory ==1 )
				{
				if ((($type == "a" || $type == "t") && $fvalue == "") || ($type == "m" && $fvalue == "0")){
					
				
					$WARNING = "
				$phrase[59]. <br>$phrase[60].<br><br>";	
				$labels[] = $label;
				}
				}
					
				if ($fvalue != "")
				{
					
					
					
				if (strtoupper($fvalue) == "OTHER")
				{
					
				$fvalue = $other[$fno];	
				
				}
					
					
					
				if ($output == "1") 
					{
					$paragraph .= "$label: $fvalue
";
					}
				else {
					$paragraph .= "$fvalue
";
				
					
				}
				}
				}
			}
		

		}
		//////
		
	
		
	if ($paragraph != "")
	{			
	if ($legend[$index] != "")
	{
if ($index != 0) {$text .="

";}		

$text .= "$legend[$index]
		
";
	}
	$text .= $paragraph;
	}	
		
	}
	
	}
	
	if(isset($WARNING))
	{
	warning($WARNING);
	echo "";
	foreach($labels as $index => $value)	
			{
			echo "$value<br>";	
			}
			
			echo "<br><br>
			<a href=\"helpdesk.php?m=$m\">$phrase[65]</a>
			";
	}
	else {
		
		

	
		
	if ($moduletype == "e")
	{
		
	
		$sql = "select email as user_email from user where userid = '$_SESSION[userid]'";
	
		$DB->query($sql,"form.php");
		$row = $DB->get();
		$user_email = $row["user_email"];	
		
		
	if ($subject == "0") {$subject = "$modname"; }
	if ($from == "i") {
		if (!isset($EMAIL_FROM) || $EMAIL_FROM== "") {$error = "\$EMAIL_FROM not set in config.php";}
		else {	$from = "$EMAIL_FROM";}
	
	} 
	elseif ($from == "u") {
		if ($user_email == "") {$error = "intranet user email address not set in intranet administration";}
		else {$from = $user_email;}
		
		
	}
	


	//echo "sending";
	
	$headers = "From: " . $from . "\n" ;

	
	if ($cc_user == 1) 
		{
	
		$headers .= "Cc: $user_email". "\n" ;
		}
	
	
	//test for banned keywords
	if (isset($badwords))
	{
		$testtext = $subject . " " . $text;
		$result = email_filter($badwords,$testtext);
	
		if ($result)
		{
			$error = "$phrase[64] $phrase[887] ....<br><br>";
			foreach ($result as $index => $value)
				{
				$error .= " $value";
				}
		}
	}
   
        
 if (isset($SPAMTOOLS) && ($SPAMTOOLS == "all" || $SPAMTOOLS == "internal"))
 {
     $result = spamTools($phrase);
     if ($result["status"] == 0) {$error = $result["error"];}
 }
        
        
        
  if (isset($CAPTCHA) && function_exists('imageftbbox') &&  ($CAPTCHA == "all" || $CAPTCHA == "internal"))       
  {
include('../web/securimage/securimage.php');
$securimage = new Securimage();

if ($securimage->check($_POST['captcha_code']) == false)
{
    
    $error = "The CAPTCHA wasn't entered correctly. Try again"; 
}        
        
  }      
        
        
        
        
        
        

  if (!isset($error))
  {
	
	//$send =  mail($email, $subject, $text,$headers) ;
      $send =  send_email($DB,$email, $subject, $text,$headers);
	
	if (!$send) {errorlog($DB,"form.php","$phrase[64] KK");
		$error =  $phrase[64];
  }
		
		}
		
	if (isset($error)) {$error =  "<div style=\"color:red;font-size:2em;margin-bottom:1em\">$error</div>";}
		
		
	}
	if ($moduletype == "h")
	{
		
		
	$sql = "select * from helpdesk_options where m = '$m'";
$DB->query($sql,"form.php");

$row = $DB->get();

	
		$email = $row["email"];
		$duplicate = $row["duplicate"];
		$email_action = $row["email_action"];
		
	if ($email_action == 3)
	
	{
	//send email notification
	$headers = "From: $email" . "\n" . "Reply-To: $email";
	$headers = "From: $email". "\n" ;
	
	
	
	//echo $headers;
	
	$emailaddress = $email;
	$message = htmlspecialchars($text);
	$subject = $modname;
	send_email($DB,$emailaddress, $subject, $message,$headers);	
		
	}
		
	
	
	$inserttext = $DB->escape($text);
	$datereported = date("Y-m-d H:i:s");
	$sql = "INSERT INTO helpdesk (probnum,m, query, solution, datefixed, datereported,ip,proxy,username,status,cat,jobtime)  
		    			VALUES(NULL,'$m','$inserttext', '',NULL,'$datereported','$ip','$proxy','$_SESSION[username]','1','0','0')"; 
	$DB->query($sql,"form.php");
			
	
	}
	
	$clear_url = "$formpage?m=$m";
	$url = "$formpage?m=$m";
	
	if (isset($error))
	{
		echo $error;
	}
	else {
	echo "
	

	
	<h2>$phrase[639]</h2><br>

	";

	$text = nl2br($text);
	$clear_url = "$formpage?m=$m";
	$url = "$formpage?m=$m";
	
	if (isset($_REQUEST["input"]))
	{
		foreach ($_REQUEST["input"] as $fieldid => $value)
							{
							$value = urlencode($value);
							$url .= "&input[$fieldid]=$value";
									
							}
	}
	
	if ($_REQUEST["cat"]) { $url .= "&cat=". $_REQUEST["cat"];}
	
	echo "$text<br><br>";
	
	}
	
	
	
	if (isset($duplicate) && $duplicate == 1) {
		
		echo "<a href=\"$url\">$phrase[892]</a><br><br><a href=\"$clear_url\">$phrase[893]</a>";
	}
	else 
	{
		echo "<a href=\"$clear_url\">$phrase[34]</a>";
	}
	
	
	//
	
	}
	
}
	else 
	{
		
	
	
	

$required = 0;



if ($banner != ""){
 echo "$banner<br>";}

echo "
	 <script type=\"text/javascript\" src=\"ask.js\"></script>
	 
		<script type=\"text/javascript\">
	
	function datepicker(id){
			
		var targid = id
		
		new JsDatePick({
			useMode:2,
			target:targid,
			dateFormat:\"$DATEFORMAT\",
			yearsRange:[2010,2020],
			limitToToday:false,
			isStripped:false,

			imgPath:\"img/\",
			weekStartDay:1
		});
	};
	
	</script>
<form action=\"$formpage\" method=\"post\"  >";






$sql = "select * from fieldset where form = \"$m\" order by ranking";
$DB->query($sql,"editform.php");


$counter = 0;
while ($row = $DB->get()) 
		{
		$counter++;
		$fieldsetno[] = $row["fieldset"];
		$legend[] = $row["legend"];
		$fieldordering[] = $row["fieldordering"];
		$ranking[] = $row["ranking"];
		
		
		
		}	
	
	
	if (isset($fieldsetno))
	{
	foreach($fieldsetno as $index => $fieldsetid)	
	{
		
		
	
		
		
		echo "<fieldset><legend>$legend[$index]</legend><br>
	
		
<br>

	
		<table  >
		

		";
		if ($fieldordering[$index] == "c") {$order = "ranking";} else { $order = "label";}
		
		
		$sql = "select * from formfields where fieldset = \"$fieldsetno[$index]\" order by $order";
		$DB->query($sql,"editform.php");
		
		$counter = 0;
		$compdisplay = false;
		
		while ($row2 = $DB->get()) 
		{
		$counter++;
		$fieldno = $row2["fieldno"];
		
		
		$type = $row2["type"];
		$ranking = $row2["ranking"];
		$compulsory = $row2["compulsory"];
		if ($compulsory == 1) { $required = 1;}
		$menu = $row2["menu"];
		$comment = nl2br($row2["comment"]);
		$label = $row2["label"];
		$values = explode("\n", $menu);
		
		if (isset($_REQUEST["input"]))
		{
			$input = $_REQUEST["input"];
		}
		$emailarray[$fieldno] = $label;
		
		echo "<tr><td  class=\"formlabels\">$label";
		if (($compulsory == 1 && $type != "r") ) {echo "<br><span id=\"warning_$fieldno\" style=\"color:red;\">$phrase[895]</span>";}
		echo "</td><td>";
		if ($comment <> "") {echo "$comment<br><br>";}
		if ($type == "t") { echo "<input type=\"text\" name=\"input[$fieldno]\" id=\"field_$fieldno\" size=\"60\"";
		if (isset($input))
		{
		echo " value=\"$input[$fieldno]\"";
		}
		
		if ($compulsory == 1 ) {echo " class=\"required\"";}
		
		echo ">";}
		
		
		
		
		if ($type == "d") { echo "<input type=\"text\" name=\"input[$fieldno]\" id=\"field_$fieldno\" size=\"10\" readonly ";
		if (isset($input))
		{
		echo " value=\"$input[$fieldno]\"";
		}
		
		if ($compulsory == 1 ) {echo " class=\"required datepicker\"";} else {echo " class=\"datepicker\"";}
		
		echo " style=\"z-index:-1;\">
		
		
		<script type=\"text/javascript\">

	 
	 datepicker('field_$fieldno');
</script>

		
		
		
		
		
		";}
		
		
		
		
		
		if ($type == "a") { echo "<textarea name=\"input[$fieldno]\" id=\"field_$fieldno\" cols=\"40\" rows=\"8\"";
		if ($compulsory == 1 ) {echo " class=\"required\"";}
		echo ">";
		
		if (isset($input))
		{
		echo $input[$fieldno];
		}
		
		echo "</textarea>";}
		if ($type == "m") 
		{//select menu
			 echo "<select name=\"input[$fieldno]\" style=\"width:300px\" id=\"field_$fieldno\" ";
			if ($compulsory == 1 ) {echo " class=\"required\"";}
			 echo " onclick=select_other($fieldno)>
			  <option value=\"0\" selected>$phrase[885]</option>
			 ";
			 foreach ($values as $indexa => $value)
							{
							echo "<option value=\"$value\"";
							if (isset($input))
								{
								if ( $input[$fieldno] == $value) {echo " selected";}
								}
							
							echo "> $value</option>";
							}
			echo "</select>";
			
			if (strtoupper($value) == "OTHER")
			{
				echo "<br>Other: <input type=\"text\" name=\"other[$fieldno]\" id=\"other_$fieldno\" style=\"display:none\">";
			}
			
					
		}
		if ($type == "r") {
			//radio buttons
			foreach ($values as $indexa => $value)
							{
								$value = trim($value);
							echo "<input type=\"radio\" name=\"input[$fieldno]\"  value=\"$value\" ";
								if (isset($input))
								{
								if ( $input[$fieldno] == $value) {echo " checked";}
								}
							if ($compulsory == 1 ) {echo " class=\"required\"";}
							echo " onclick=radio_other($fieldno,\"$value\")> $value<br>";
							
							}
					if (strtoupper($value) == "OTHER")
			{
				echo "Other: <input type=\"text\" name=\"other[$fieldno]\" id=\"other[$fieldno]\" style=\"display:none\">";
			}		
							
			}
		echo "</td></tr>";
		
		}
		
		
		echo "</table>";
		
		echo "</fieldset><br><br>";
		}
	}	

	
	//echo "<div style=\"text-align:center\" >";
	
	if ($moduletype == "e")
	{
	echo "<input type=\"hidden\" name=\"from\" value=\"$from\">
	<input type=\"hidden\" name=\"subject\" value=\"$subject\">";
	}

	
	
	
	
		$sql = "select * from helpdesk_cat where m = '$m;'";
	$DB->query($sql,"ask.php");

while ($row = $DB->get())
      {	
     
      	$id =  $row["id"];
      
		$category[$id] = $row["cat_name"];
		
      }
	
      echo "<p style=\"text-align:center\">";
      
      if (isset($category) && count($category))
      {
      	echo "<select name=\"cat\" class=\"required\" id=\"field_0\"><option value=\"0\"";
	if (isset($cat) && $cat == "0") {echo " selected";}
	echo ">$phrase[885]</option>";
	
	foreach ($category as $cat_id => $catname)
	{
		echo "<option value=\"$cat_id\"";
		if (isset($cat) && $cat == $cat_id) {echo " selected";}
		echo ">$catname</option>";
	}
	
	echo "</select>
<br><span id=\"warning_0\" style=\"color:red;font-weight:bold\">$phrase[895]</span>
	";
      }
      else 
      {
      	echo "<input type=\"hidden\" name=\"cat\" value=\"0\">";
      }
      
      
	
	echo "</p>
	<input type=\"hidden\" name=\"m\" value=\"$m\">";

    
       if (isset($CAPTCHA)  && function_exists('imageftbbox') &&  ($CAPTCHA == "all" || $CAPTCHA == "internal")) 
        {
echo "<p style=\"text-align:center;margin-bottom:2em\">	
<b>CAPTCHA: please retype text in image below</b><br><br><img id=\"captcha\" src=\"../web/securimage/securimage_show.php\" alt=\"CAPTCHA Image\" /><br>
<input type=\"text\" name=\"captcha_code\" id=\"captcha_code\" size=\"10\" maxlength=\"6\" class=\"required\" />
	<a href=\"#\" onclick=\"document.getElementById('captcha').src = '../web/securimage/securimage_show.php?' + Math.random(); return false\">[ Different Image ]</a>   <br> 
	</p>
	";
        }
        
        
         if (isset($SPAMTOOLS) && ($SPAMTOOLS == "all" || $SPAMTOOLS == "internal"))
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
        
        
	echo "<p style=\"text-align:center\"><input type=\"submit\" name=\"submit\" value=\"$phrase[192]\" id=\"submit\"></p><br>
	
	
	
		 
";
	
	

	
	
		 
		 
		 echo "</form> ";
	

	
	}
	
	}							

?>
