<?php
function strip($array)
{
 
   foreach ($array as $key=>$value)
   {
   	if (!is_array($value))
 		 {
   		$array[$key]=stripslashes($value);
  		}
    else 
   	 {
     foreach ($value as $key2=>$value2)
     	{
     		if (!is_array($value2))
 		 {
     	$array[$key][$key2]=stripslashes($value2);
 		 }
 		 else
 		 {
 		 	foreach ($value2 as $key3=>$value3)
     	{
     	$array[$key][$key2][$key3]=stripslashes($value3);	
     	}
 		 	
 		 }
     	}
     
   	 }
    
   	
  }
  return $array;
  
}


if (get_magic_quotes_gpc()) { 

$_POST = strip($_REQUEST);


} 	
extract($_REQUEST);
extract($_SERVER);


if (isset($submit))
{




if ($level == 2) { $levelname = "Adult";}
if ($level == 3) { $levelname = "Teen/child";}

	$db = mysql_pconnect("192.168.10.14", "web","sc00terz54") or die ("could not connect to server.");
	mysql_select_db("intranet", $db) or die ("could not select database.");

$text = "Name: $name
Telephone: $telephone
Branch: $branch
Level: $levelname 
Question: $question";
	
	$displaytext = nl2br($text);
	$text = addslashes($text);
	$datereported = date("Y-m-d H:i:s");
	$sql = "INSERT INTO helpdesk (probnum,m, query, solution, datefixed, datereported,ip,proxy,username,status,cat)  
		    			VALUES(NULL,'74','$text', '',NULL,'$datereported','','','web','1','$level')"; 
	
	
	mysql_query($sql);
	
	$displaytext = nl2br($text);
	echo "
	<h2>Form submitted</h2>
	You have submiited the following request.<br>
<br>
$displaytext
	
	";
}

else {
	

?>

<script type="text/javascript">
    function check() {
	
    var branch = document.getElementById('branch').value	
    var level = document.getElementById('level').value
    	
    if (branch == "none"){
		  	 alert("Please select a branch")
		  	 return false
   	
   			}	
    
   	if (level == "none"){
		  	 alert("Please select a level")
		  	 return false
   	
   			}	
    	
	var email = document.getElementById('email').value
			
			if (email.length == 0){
		   alert("Invalid email address")
		   return false
		}
		
	
	
   				//check address has @
   			if (email.indexOf("@")==-1){
		  	 alert("Invalid email address")
		  	 return false
   	
   			}
			
   			//check address has .
   			if (email.indexOf(".")==-1){
		  	 alert("Invalid email address")
		  	 return false
   	
   			}
   			
   			//trim whitespace
   			email = email.replace(/^\s*|\s*$/g,"");
   			 
   			 //check address has  no blanks
   			if (email.indexOf(" ") > -1){
		  	 alert("Invalid email address")
		  	 return false
   	
   			
   			}
    }
   			
   			</script>

<form method="post" action="<?php echo $PHP_SELF?>" onSubmit="return check()">
<fieldset><legend>Ask a librarian</legend>

<table cellpadding="5">
<tr><td>Name (optional)</td><td><input type="text" name="name" size="60"></td></tr>
<tr><td>Email address</td><td><input type="text" name="email" id="email" size="60"></td></tr>
<tr><td>Telephone (optional)</td><td><input type="text" name="telephone"></td></tr>
<tr><td>Your branch</td><td>
<select name="branch" id="branch">
<option value="none">Please select</option>
<option value="Belgrave Library">Belgrave Library</option>
<option value="Boronia Library">Boronia Library</option>
<option value="Croydon Library">Croydon Library</option>
<option value="Ferntree Gully">Ferntree Gully</option>
<option value="Healesville Library">Healesville Library</option>
<option value="Lilydale Library">Lilydale Library</option>
<option value="Knox Library">Knox Library</option>
<option value="Knox Mobile Library">Knox Mobile Library</option>
<option value="Montrose Library">Montrose Library</option>
<option value="Mooroolbark Library">Mooroolbark Library</option>
<option value="Mt Evelyn">Mt Evelyn</option>
<option value="Ranges Mobile Library">Ranges Mobile Library</option>
<option value="Ringwood Library">Ringwood Library</option>
<option value="Rowville Library">Rowville Library</option>
<option value="Valley Mobile LIbrary">Valley Mobile LIbrary</option>
</select>
</td></tr>
<tr><td>Level</td><td>
<select name="level" id="level">
<option value="none">Please select</option>
<option value="2">Adult</option>
<option value="3">Teen/child</option>
</select>
</td></tr>
<tr><td valign="top">Question</td><td><textarea name="question" cols="60" rows="10"></textarea></td></tr>
<tr><td></td><td><input type="submit" name="submit" value="Ask a question"></td></tr>
</table>
</fieldset>
</form>

<?php
}
?>
