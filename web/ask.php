<?php

###############################################
#CONFIG SECTION
###############################################


//helpdesk module ID
$module_id = 91;

//is form enabled: true or false
$enabled = false;



##Include here the custom html you want to include on the page banner
$banner = <<<EOF


<p>
	Banner text (if any) goes here.
</p>

EOF;

##########################################################
#END CONFIG SECTION
##########################################################






if ($enabled != true)
{
	echo "<h1>Disabled</h1>";
	exit();

}



//by default this page will use intranet includes folder 
//If you want to move this bookings page to another web server.
//You will need a copy of the intranet includes folder to be in the 
//same directory as this page. The MySQL connection details in the config.php
//may also need to be updated if conecting from another server.

 if(!include("../includes/initiliaze_page.php"))
 {
 	include("includes/initiliaze_page.php");
 }

 
 

#########################################################
#########################################################

    	 $sql = "select * from helpdesk_options where m = '$module_id'"; 
    	 $DB->query($sql,"ask.php");
    	 $row = $DB->get();
		$email = $DB->escape($row["email"]);
		$email_action = $DB->escape($row["email_action"]);


		
		 $sql = "select * from forms where module = '$module_id'"; 
    	 $DB->query($sql,"ask.php");
    	 $row = $DB->get();
		$intro = nl2br($row["banner"]);

                
                
   if (isset($SPAMTOOLS) && ($SPAMTOOLS == "all" || $SPAMTOOLS == "web"))
   {
$salt = uniqid(); 


salt($salt);                
   }              
                
                

echo "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/transitional.dtd\">
<html>
<head>
<title>Online Request form</title>
   <LINK REL=\"STYLESHEET\" TYPE=\"text/css\" HREF=\"bookings.css\"> 
   <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"../main/jsDatePick_ltr.min.css\" />
  	<script type=\"text/javascript\" src=\"../main/jsDatePick.min.1.3.js\"></script>

</head>

<body ><div id=\"container\"><div id=\"banner\">
<a href=\"$HOME_LINK\" title=\"$HOME_LABEL\" alt=\"$HOME_LABEL\"><img src=\"logo.gif\"></a>
<h1>Online Request form</h1> ";



if ($banner != ""){
echo "<div style=\"width:50%;margin-left:auto;margin-right:auto\">$banner</div>";
}
echo "<p id=\"footer\"></p></div>
";



if (isset($_REQUEST["submit"]))

{
	
		function filter($text)
				{	
      
	  $keywords[] = "to:";
	  $keywords[] = "cc:";
	  $keywords[] = "from:";
	  $keywords[] = "bcc:";
	  $keywords[] = "Content-Transfer-Encoding:";
	  $keywords[] = "Content-Type:";
	  $keywords[] = "Mime-Version:";				

		
      $text = strtolower($text);
      
      	if (isset($keywords))
			{
			foreach ($keywords as $index => $word)
				{
					if (strstr($text, $word)) { return true;}			
				}
			}
      
      
				}
		
				$input = $_REQUEST["input"];	
				
	$text = "";

$sql = "select fieldno,compulsory,type, label from formfields,fieldset where formfields.fieldset = fieldset.fieldset and fieldset.form = '$module_id'";

 $DB->query($sql,"ask.php");
    	 while ($row = $DB->get())
    	 {
    	 	$fieldno = $row["fieldno"];
    	 	$label = $row["label"];	
    	 	$type = $row["type"];	
    	 	$compulsory = $row["compulsory"];	
    	 	
    	 			$text .= "$label: $input[$fieldno]

";
    	 	//check for empty fields
    	 	if ($compulsory == 1)
    	 	{
    	 		if ($type == "m" && $input[$fieldno] == "0" )	{ $error = $phrase[886];}
    	 		if (($type == "a" || $type == "t") && $input[$fieldno] == "" )	{ $error = $phrase[886];}
    	 	}
    	 }
	
         
  if (isset($SPAMTOOLS) && ($SPAMTOOLS == "all" || $SPAMTOOLS == "web"))
  {
       $result = spamTools($phrase);
     if ($result["status"] == 0) {$error = $result["error"];} 
      
  }
         
         
if (isset($CAPTCHA)  && function_exists('imageftbbox') &&  ($CAPTCHA == "all" || $CAPTCHA == "web")) 
{
  include('securimage/securimage.php');
$securimage = new Securimage();

if ($securimage->check($_POST['captcha_code']) == false)
{
    
    $error = "The CAPTCHA field wasn't entered correctly."; 
}       
         
}        

	
		
	
				
	$displaytext = nl2br($text);				

if (!isset($error))
{
if (!filter($text)) { 
	
	$cat = addslashes($_REQUEST["cat"]);
	$sqltext = addslashes($text);
	$datereported = date("Y-m-d H:i:s");


	
	
	$sql = "INSERT INTO helpdesk (probnum,m, query, solution, datefixed, datereported,ip,proxy,username,status,cat,jobtime,requestedby,assigned)  
		    			VALUES(NULL,'$module_id','$sqltext', '',NULL,'$datereported','','','web','1','$cat','0','','')"; 
	

	$DB->query($sql,"ask.php");
	
	
	if ($email_action == 3)
	{
	//send email message
	$mailmessage = "
$phrase[872]
	
$text";  
	
		$send = @mail($email, "Online request", $mailmessage, $email);
		
		
		if (!$send)
		{
			$error = "email error to $emailaddress from $headers";
			errorlog($DB,"online request",$error);
	
		}
		
	}	
		
		
	
	$result =  "
<div id=\"results\">
<br><br><h2>$phrase[196]</h2><p>
	<br>
$displaytext
	</p><p><a href=\"ask.php\">$phrase[34]</a></p></div>";
	}
	
	else 
	{
		$error = $phrase[887];
	
	}
}

}

if (isset($error))
{
		echo "<div id=\"results\"><h2 style=\"color:#f40b0b\">$phrase[873]</h2><p ><br>$error
<br><br><a href=\"" . basename($_SERVER["SCRIPT_NAME"])  . "\">$phrase[813]</a>
	</p></div>";	
}
elseif(isset($result))
{
	echo $result;
}
else 
{
	

	
	

$compdisplay = false;

if ($intro != "")
{
	
		echo "<p id=\"intro\">$intro<p>";
}



echo "<form action=\"" . basename($_SERVER["SCRIPT_NAME"]) . "\" method=\"post\" id=\"requestform\" >";






$sql = "select * from fieldset where form = '$module_id' order by ranking";
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
		
		
	
		
		
		echo "
                    


<script type=\"text/javascript\">
			
			
			function datepicker(id){
			

		
		new JsDatePick({
			useMode:2,
			target:id,
			dateFormat:\"$DATEFORMAT\",
			yearsRange:[2010,2020],
			limitToToday:false,
			isStripped:false,

			imgPath:\"img/\",
			weekStartDay:1
		});
	};
	
	</script>








<fieldset style=\"width:60%;margin:0 auto;text-align:left\"><legend>$legend[$index]</legend>
	
		<table cellpadding=\"10\" cellspacing=\"0\"  style=\"margin-left:auto;margin-right:auto\">
		

		";
		if ($fieldordering[$index] == "c") {$order = "ranking";} else { $order = "label";}
		
		
		$sql = "select * from formfields where fieldset = '$fieldsetno[$index]' order by $order";
		$DB->query($sql,"editform.php");
		
		$counter = 0;
		//$compdisplay = false;
		
		while ($row2 = $DB->get()) 
		{
		$counter++;
		$fieldno = $row2["fieldno"];
		
		
		$type = $row2["type"];
		$ranking = $row2["ranking"];
		$compulsory = $row2["compulsory"];
		
		$menu = $row2["menu"];
		$comment = nl2br($row2["comment"]);
		$label = $row2["label"];
		$values = explode("\n", $menu);
		
		
		$emailarray[$fieldno] = $label;
		
		echo "<tr><td  align=\"right\" valign=\"top\"><b>$label</b><br><span id=\"warning_$fieldno\" style=\"color:red;";
		if ($compulsory == 0) {echo "display:none";}
		echo "\">$phrase[895]</span>";
		echo "</td><td>";
		if ($comment <> "") {echo "$comment<br>";}
		if ($type == "t") { echo "<input type=\"text\" name=\"input[$fieldno]\" id=\"field_$fieldno\" size=\"60\"";
		//if (isset($input))
		//{
		echo " value=\"\"";
		
		if ($compulsory == 1)
		{
			echo " class=\"required\"";
		}
		//}
		echo ">";}
		if ($type == "a") { echo "<textarea name=\"input[$fieldno]\" id=\"field_$fieldno\" cols=\"40\" rows=\"8\"";
		if ($compulsory == 1)
		{
			echo " class=\"required\"";
		}
		echo ">";
		
	
		
		echo "</textarea>";}
		if ($type == "m") 
		{//select menu
			 echo "<select name=\"input[$fieldno]\" style=\"width:300px\"  id=\"field_$fieldno\"";
			 if ($compulsory == 1)
				{
			echo " class=\"required\"";
				}
			 echo ">
			 <option value=\"0\" selected>$phrase[885]</option>";
			 
			 
			 
			 foreach ($values as $indexa => $value)
							{
							echo "<option value=\"$value\"> $value</option>";

							}
			echo "</select>";		
		}
		if ($type == "r") {
			//radio buttons
			foreach ($values as $indexa => $value)
							{
							echo "<input type=\"radio\" name=\"input[$fieldno]\" value=\"$value\"> $value<br>";
							
							}
			}
                        
                        
                 if ($type == "d") {
                     
                     
                     echo "<input type=\"text\" name=\"input[$fieldno]\"  id=\"f_$fieldno\" ";
                     
                     if ($compulsory == 1)
		{
			echo " class=\"required\"";
		}
                     echo ">
                             <script>
                             	datepicker('f_$fieldno');
                            </script>
                             ";
                     
                     
                     
                     
                     
                 }       
                        
		echo "</td></tr>
";
		
		}
		
		
		echo "</table>";
		
		echo "</fieldset>";
		}
	}	

	echo "<div>";
	



		$sql = "select * from helpdesk_cat where m = '$module_id'";
	$DB->query($sql,"ask.php");

while ($row = $DB->get())
      {	
     
      	$id =  $row["id"];
      
		$category[$id] = $row["cat_name"];
		
      }
	

      if (isset($category) && count($category))
      {
      	echo "<br><select name=\"cat\" class=\"required\" id=\"field_0\"><option value=\"0\"";
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
	
  if (isset($CAPTCHA)  && function_exists('imageftbbox') &&  ($CAPTCHA == "all" || $CAPTCHA == "web")) 
  {
	echo "
            
<br><br>To prevent spam retype the text in the captcha field below.<br><br><img id=\"captcha\" src=\"securimage/securimage_show.php\" alt=\"CAPTCHA Image\" /><br>
<input type=\"text\" name=\"captcha_code\" size=\"10\" maxlength=\"6\" />
	<a href=\"#\" onclick=\"document.getElementById('captcha').src = 'securimage/securimage_show.php?' + Math.random(); return false\">[ Different Image ]</a> "; 

  }

echo "
<br><br><input type=\"submit\" name=\"submit\" value=\"$phrase[192]\" id=\"submit\">
	
	
	</div>
	
	</form>
	   <script type=\"text/javascript\" src=\"ask.js\"></script>
	   ";
}

?>



</div>
</body>
</html>