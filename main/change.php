<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$sql = "select m, menupath from modules where frontpage = '1'";  
$DB->query($sql,"change.php"); 
$row = $DB->get(); 
$path = $row["menupath"];
$m = $row["m"];



if (!isset($_SESSION['change']) || $_SESSION['change'] == "False")
{

	

	
	
	
	$path =  "?m=" . $m;

	
header("Location: $url" . $path);


	


		
}
else{

include ("../includes/htmlheader.php");	

$sql = "select *  from user where userid = $_SESSION[userid]";
$DB->query($sql,"change.php");	
$row = $DB->get();

if (isset($_POST["change"]))
{
	
	//do test for password complexity 
	$complexcounter = 0;
	
	if (preg_match('/[a-z]/',$_POST["password1"]))	
	{
		$complexcounter++;	
	
	}
	
	if (preg_match('/[A-Z]/',$_POST["password1"]))	
	{
		$complexcounter++;	
			
	}
	
	if (preg_match('/[0-9]/',$_POST["password1"]))	
	{
		$complexcounter++;	
			
	}
	if (preg_match('/([^A-Za-z0-9 ])/',$_POST["password1"]))	
	{
		$complexcounter++;	
			
	}
	

	
	
	
	if ($_POST["password1"] <> $_POST["password2"])
	{
		$WARNING = "$phrase[47]<br><br>";
	}
	elseif (strlen($_POST["password1"]) < $PREFERENCES['passwordlength'])
	{
		$WARNING = "$phrase[9] <br><br>";
	}
	
	
	elseif ($complexcounter < 3 && $PREFERENCES['complex'] == "1")	
	{
		$WARNING = "$phrase[44] $phrase[672] ";			
	}
	
	else 
	{
		
	if ($row["authtype"] == "mysql")
		{
		$password = md5($_POST["password1"]);
		$now = time();
		$sql = "update user set password = '$password', logonchange = '0', expiry = '0', lastchanged = '$now' where userid = $_SESSION[userid]";	
		$DB->query($sql,"change.php");
	
		$MESSAGE = $phrase[49];	
		}
	
	elseif ($row["authtype"] == "ldap" || $row["authtype"] == "Active Directory")
		{
		
			//echo "admin ldap";	
			
		$ldapconn = @ldap_connect($LDAPHOST);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		
		if ($ldapconn) 
			{ 
				
			//echo "admin connected  $LDAPADMINDN, $LDAPADMINPASS";	
			
			$ldapbind = @ldap_bind($ldapconn, $LDAPADMINDN, $LDAPADMINPASS);

   			// verify binding
   			if ($ldapbind) 
   				{	
				
			
				
   				if ($row["authtype"] == "Active Directory")
   				{	
				$newPassw =	"";	
				$newPassword = 	$_POST["password1"];
				$newPassword = "\"" . $newPassword . "\"";
    			$len = strlen($newPassword);

    			for($i = 0; $i < $len; $i++)
  					{
       		 		$newPassw .= "{$newPassword{$i}}\000";
  					}
    			$newPassword = $newPassw;
    			$userdata['unicodePwd'] = $newPassword;
   				}
   				
			else	//($row["authtype"] == "active directory")	
				{
				$userdata['userPassword'] = $_POST["password1"];	
				
				}
    	
		$result = @ldap_mod_replace($ldapconn,$row["ldapdn"], $userdata);

		
		if (!$result)
		{
		//failed to change password
		$error = "user $row[ldapdn] user $newPassword $LDAPADMINDN, $LDAPADMINPASS ";
   		$error .= (ldap_errno($ldapconn) !== 0);
		//echo "$error  ";
		//print_r($userdata);
		errorlog($DB,"change.php",$error);	
		}
		else 
		{
		
		$password = md5($_POST["password1"]);
		$now = time();
		$sql = "update user set password = '$password',logonchange = '0', expiry = '0', lastchanged = '$now' where userid = '$_SESSION[userid]'";	
		$DB->query($sql,"change.php");
		$MESSAGE = $phrase[49];	
		$_SESSION['change'] == "False";
		}
   			}
   			else 
   				{
   				//failed to bind
   				$error = "failed to bind to ldap server ";
   				$error .= "$LDAPHOST $LDAPADMINDN $LDAPADMINPASS";
   				$error .= (ldap_errno($ldapconn) !== 0);
				errorlog($DB,"change.php",$error);
				$MESSAGE = $phrase[698];

   				}
		}

}
	}
}
echo "<div style=\"margin:8em;text-align:center\">

<h1>$phrase[22]</h1>";


$sql = "select * from user where userid = $_SESSION[userid]";  //???
$DB->query($sql,"change.php");  //???
$row = $DB->get();  //???





if(isset($MESSAGE))
{
	$_SESSION['change'] = "False";	
	
	
	$url = $path . "?m=" . $m;
	
	
echo "$MESSAGE
<br><br><a href=\"$url\">Continue</a>

";
}
else {

	
	if(isset($WARNING)) {warning($WARNING);}
	
	?>
	
			
		
		
			
			

	
			
			
 <FORM method="POST" action="change.php" name="change"   style="margin-left:auto;margin-right:auto">
			


<table style="width:80%;margin:2em auto">
			<tr><td class="label" style="width:50%"><?php echo $phrase[32]?></td><td class="input"><INPUT type="password" name="password1" id="password1" size="30" maxlength="50"></td></tr>
			<tr><td class="label"><?php echo $phrase[17]?></td><td class="input"><INPUT type="password" name="password2" id="password2" size="30" maxlength="50" ></td></tr>
			<tr ><td class="label"><span id="lengthlabel" style="display:none"><?php echo $phrase[835]?></span></td>
			<td class="input"><img src="../images/cross.png" id="lengthresult" style="display:none"></td></tr>
<?php			
			if ($PREFERENCES['complex'] == "1")
{
			echo "<tr  ><td class=\"label\"><span id=\"testlabel\" style=\"display:none\">$phrase[833]</span></td>
			<td class=\"input\" style=\"vertical-align:middle\"><img src=\"../images/cross.png\"  id=\"testresult\" style=\"display:none\"></td></tr>
			<tr><td></td><td class=\"input\"> <span id=\"details\" >$phrase[672]</span></td></tr>
			";
}	
	?>	

	<tr ><td class="label" ><span id="matchlabel" style="display:none"><?php echo $phrase[834]?></span></td>
			<td class="input"><img src="../images/cross.png" id="matchresult" style="display:none"></td></tr>
			
			<tr ><td class="label" ><span id="duplicate"><?php echo $phrase[836]?></span></td>
			<td class="input"><img src="../images/tick.gif" id="dulicateresult" ></td></tr>
			<tr><td></td><td class="input"><INPUT type="submit" name="change" value="<?php echo $phrase[28]?>" id="submit"></td></tr>
			
			
			</table>
			</form>
			
<script type="text/javascript">
			<!--

			
			function test(e)
			{
			if (!e) var e = window.event;
			//alert(e.keyCode)
				var passed = "true";
				var string = password1.value;
				var test;
				var username = "<?php echo $_SESSION["username"] ?>";
				var splitusername = new Array();
				var duplicatematch = "false";
				
				if (e.keyCode == 46 || e.keyCode == 8)
				//dlete or backspace
				{test = string.length}
				else
				{
				test = string.length
				}
				
				
				
				
				//check that username is not in password
				if (string.indexOf(username) !=-1)
				{
					var duplicatematch = "true";
				}
				
				splitusername = username.split('.');
				for (y=0; y<splitusername.length; y++)
					{
					//alert(splitusername[y]);
					if (string.indexOf(splitusername[y]) !=-1)
						{
					var duplicatematch = "true";
					//alert("match")
						}
					} 
				
						splitusername = username.split(' ');
				for (y=0; y<splitusername.length; y++)
					{
					//alert(splitusername[y]);
					if (string.indexOf(splitusername[y]) !=-1)
						{
					var duplicatematch = "true";
					//alert("match")
						}
					}
				
				if (duplicatematch == "true")
				{
				document.getElementById("dulicateresult").src= "../images/cross.png";	
				passed = "false"
				}	
				else
				{
				document.getElementById("dulicateresult").src= "../images/tick.gif";	
				}
				
				
				
				if ( test < <?php echo $PREFERENCES["passwordlength"] ?>)
				{
				document.getElementById("lengthresult").src= "../images/cross.png";	
				passed = "false"
				}	
				else
				{
				document.getElementById("lengthresult").src= "../images/tick.gif";	
				}
				
				
				<?php
				if ($PREFERENCES['complex'] == "1")
{
?>

				//if (passed == "true")
				//{
				//do complexity test	
				var upper = /[A-Z]/;
			var lower = /[a-z]/;
			var num = /[0-9]/ ;
			var special = /([^A-Za-z0-9])/ ;
			
			var complexcounter = 0;
			
	
	if (upper.test(password1.value))
		
	{
		complexcounter++	;	
		
	}
	
	if (lower.test(password1.value))
	{
		complexcounter++	;
	
	}
	
	if (num.test(password1.value))
	{
		complexcounter++;	
			
	}
	if (special.test(password1.value))
	{
		complexcounter++;		
		
	}	
				
	if(complexcounter < 3) 
	{passed = "false";
	document.getElementById("testresult").src= "../images/cross.png";
	details.style.display = "inline";
	}
	else 
	{
	document.getElementById("testresult").src= "../images/tick.gif";	
	details.style.display = "none";	
	}
	//alert(complexcounter)
				//}


				
				
			
				
				

<?php
}			
?>				
				
				if (passed == "true")
				{
					
				password2.disabled = false;
				password2.style.background = "white"
				}
				
				if (password2.value != "")	
				{
					
				if (password2.value != password1.value)
					{
				
					submit.disabled = true
				
					document.getElementById("matchresult").src= "../images/cross.png";
					}
				else 
					{
						//alert("passwords match")
					if (passed == "true")
						{
						submit.disabled = false
						} 
					else 
						{
						submit.disabled = true
						}
					document.getElementById("matchresult").src= "../images/tick.gif";
					}
				}
				
				
				
				
			
				
			}
			
		
			
			
					if (document.getElementById("password1"))
{

var password1 = document.getElementById("password1");
var password2 = document.getElementById("password2");
var details = document.getElementById("details");
var lengthlabel = document.getElementById("lengthlabel");
var lengthresult = document.getElementById("lengthresult");

var matchlabel = document.getElementById("matchlabel");
var matchresult = document.getElementById("matchresult");

lengthlabel.style.display = "inline";
lengthresult.style.display = "inline";

matchlabel.style.display = "inline";
matchresult.style.display = "inline";




<?php
			if ($PREFERENCES['complex'] == "1")
{
?>
var testlabel = document.getElementById("testlabel");
var testresult = document.getElementById("testresult");
testlabel.style.display = "inline";
testresult.style.display = "inline";
details.style.display = "inline";

<?php

}

?>


//details.style.display = "none";
//complexity.style.position = "absolute";
//complexity.style.background = "white";
//complexity.style.padding = "1em";
//complexity.style.margin-left = "5em";




var submit = document.getElementById("submit")
submit.disabled = true
password2.style.background = "#d9dee1"
password2.disabled  = true
password1.focus()
//password1.onkeypress=test
password1.onkeyup=test
password2.onkeyup=test
//password2.onfocus=red


}	
			//-->
			</script>
			<?php
}



echo "</div>";

include ("../includes/footer.php");
}
?>

