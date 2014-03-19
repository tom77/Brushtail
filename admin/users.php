<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");



include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>
		<h2>$phrase[6]</h2>";


	
	
	if (isset($_POST["updategroups"]))

	{
	$uid = $DB->escape($_POST["uid"]);
	
	$sql = "delete from group_members where userid = \"$uid\"";
	
	$DB->query($sql,"users.php");
	
	
	//update permissions for intranet modules
	if (isset($_POST["id"]))
						{
	foreach ($_POST["id"] as $key => $value) 
				{
				
				$sql = "insert into group_members values('$uid','$key')";
				
				$DB->query($sql,"users.php");
				}
				}
	}
	

	




if (isset($_POST["submituser"]))
	{
		
		
		$name = $DB->escape($_POST["name"] );
		
		$expiry = $DB->escape($_POST["expiry"] );
		$authtype = $DB->escape($_POST["authtype"] );
		$logonchange = $DB->escape($_POST["logonchange"] );
		$first_name = $DB->escape($_POST["first_name"] );
		$last_name = $DB->escape($_POST["last_name"] );
		$email = $DB->escape($_POST["email"] );
		$payroll_number = $DB->escape($_POST["payroll_number"] );
		
		//$disabled = $DB->escape($_POST["disabled"] );
		$lastchanged = time();
		if (isset($_POST["ldapdn"])) {$ldapdn = $DB->escape($_POST["ldapdn"] );}
		else {$ldapdn = "";}
		
		
		if($expiry == 1)
		{
		$today = date("j");
		$month = date("n");
		$year = date("Y");	
		$expiry = mktime(1, 1, 1, $month, $today + $day, $year);
		
		}
	
		
			$password = random(20);
	
	if ($name == "")
		{
		$WARNING = "$phrase[7]<br><br>$phrase[8]";
		}
	elseif ($name == "guest")
		{
		$WARNING = "$phrase[7]<br><br>$phrase[680]";
		}
	
	else 
	{
		
		$sql = "select count(*) as count from user where username = '$name'";
		$DB->query($sql,"users.php");
		$row = $DB->get();
		$count = $row["count"];
		//echo $sql;
		if ($count == 0)
		{
		
		$password = md5($password);
		$sql = "insert into user values(NULL,'$name','$password','$authtype','0','$logonchange','$expiry','$ldapdn','','$lastchanged','$first_name','$last_name','$email','$payroll_number')";
		$DB->query($sql,"users.php");
		} else {echo "<h2>ERROR - User account \"$name\" already exists</h2>";}
		
		//echo $count ;
	}
	
}


if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "import")
{
	
	$fields = $_REQUEST["col"];
	
	//print_r($fields);
	$counter = 1;
	$usersadded = 0;
	
$lines = @file($_FILES['upload']['tmp_name']);
if (is_array($lines))
{
foreach ($lines as $value)
{
	
	$value = rtrim($value, "\r\n");
	
	$cols = explode(",", $value);
	
	foreach ($cols as $key => $val)
	{
		if ($fields[$key] == "username") {$username = $DB->escape($cols[$key]);}
		if ($fields[$key] == "first_name") {$first_name = $DB->escape($cols[$key]);}
		if ($fields[$key] == "last_name") {$last_name = $DB->escape($cols[$key]);}
		if ($fields[$key] == "email") {$email = $DB->escape($cols[$key]);}
		if ($fields[$key] == "pd") {$pd = $cols[$key];}
		if ($fields[$key] == "ldap") {$ldap = $DB->escape($cols[$key]);}
		if ($fields[$key] == "payroll_number") {$payroll_number = $DB->escape($cols[$key]);}
		
	}
	//echo "$username ...$first_name..$last_name...$email...$pd..$ldap";
	$password = md5($pd);
	
	if ($username != "" && $username != "guest" && $username != "administrator")
	
	{
		$sql = "select count(*) as number from user where username = '$username'";
			$DB->query($sql,"users.php");
			
			$row = $DB->get();
			$number= $row["number"];
			
			//echo $sql;
			
			if ($number == 0)
			{
			$sql = "insert into user values(NULL,'$username','$password','mysql','0','0','0','$ldap','','0','$first_name','$last_name','$email','$payroll_number')";
			
			//echo $sql;
			$DB->query($sql,"users.php");	
			$usersadded++;
			}
			else {
			$ERRORS[] =  "Error adding row $counter, User exists<br><br><br>";	
			}
		
		
	} else 
	{
		$ERRORS[] =  "Error adding row $counter, invalid username. <br>";
	}
	
	$counter++;
}
} else {$ERRORS[] = "Error importing csv file.";}


echo "<h4>$phrase[917]</h4>
<p style=\"font-weight:bold\">$usersadded users added.</p>
<p style=\"color:red\">
";
	
	if (isset($ERRORS))
	{
		foreach ($ERRORS as $error)
		{
			echo "$error <br>";
		}
	}
	
echo "</p>";	
}



if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "password")
{

	$password = trim($_REQUEST["password1"]);
	$uid = $DB->escape($_REQUEST["uid"]);
	
//do test for password complexity 
	$complexcounter = 0;
	
	
		
	
	if (isset($password) &&  preg_match('/[a-z]/',$password))	
	{
		$complexcounter++;		
	}
	
	if (isset($password) &&  preg_match('/[A-Z]/',$password))	
	{
		$complexcounter++;		
	}
	
	if (isset($password) &&  preg_match('/[0-9]/',$password))	
	{
		$complexcounter++;			
	}
	if (isset($password) &&  preg_match('/([^A-Za-z0-9 ])/',$password))	
	{
		$complexcounter++;			
	}
		
		
		
		

	
		if  (isset($password) && ((strlen($password) < $PREFERENCES['passwordlength']) || (strlen($password) == 0)))
		{
		$WARNING = "$phrase[10]<br><br>$phrase[9]";	
		}
		if ($complexcounter < 3 && $PREFERENCES['complex'] == "1")	
	{
		$WARNING = "$phrase[44] $phrase[672] ";			
	}
	
	if (!isset($WARNING))
	{
		$password = md5($password);
		$now = time();
		$sql = "update user set password = '$password', lastchanged = '$now' where userid = '$uid' ";	
		
	
		$DB->query($sql,"users.php");	
	}
	
}

	
if (isset($_POST["updateuser"]))

	{
		$name = $DB->escape($_POST["name"] );
		
		$expiry = $DB->escape($_POST["expiry"] );
		$authtype = $DB->escape($_POST["authtype"] );
		$logonchange = $DB->escape($_POST["logonchange"] );
		$first_name = $DB->escape($_POST["first_name"] );
		$last_name = $DB->escape($_POST["last_name"] );
		$email = $DB->escape($_POST["email"] );
		$payroll_number = $DB->escape($_POST["payroll_number"] );
	
		$disabled = $DB->escape($_POST["disabled"] );
                $uid = $DB->escape($_POST["uid"]);
                
                if ($disabled == 1)
                    
                {
                 $sql = "delete from leave_members where userid = '$uid'";   
                 $DB->query($sql,"users.php");   
                    
                  $sql = "delete from leave_managers where userid = '$uid'";   
                 $DB->query($sql,"users.php");  
                 
                 
                  $sql = "delete from travel_members where userid = '$uid'";   
                 $DB->query($sql,"users.php");  
                 
                  $sql = "delete from travel_members where userid = '$uid'";   
                 $DB->query($sql,"users.php");  
                    
                    
                }
                
                
                
                
                
                
                
                
                
		
		
	
		if ($name == "")
		{
		$WARNING = "$phrase[10] <br><br>$phrase[8]";
		}
		else {
		$sql = "update user set username = '$name', expiry = '$expiry', logonchange = '$logonchange', disabled = '$disabled', authtype = '$authtype', first_name = '$first_name', last_name = '$last_name', email = '$email', payroll_number = '$payroll_number'";
		
		if (isset($_POST["ldapdn"])) {
			$ldapdn = $DB->escape($_POST["ldapdn"] );
		$sql .= ", ldapdn='$ldapdn'";
		}
		
		//$sql .= ", lastchanged = UNIX_TIMESTAMP() where userid = '$uid' ";	
		$sql .= " where userid = '$uid' ";
		$DB->query($sql,"users.php");
		}
		
			
		


	}
	


if (isset($_GET["udelete2"]) && $_GET["udelete2"] <> 1  && $_GET["udelete2"] <> 0)
	{
	//delete user
	$delete2 = $DB->escape($_GET["udelete2"]);
	$sql = "delete from user where userid = \"$delete2\"";
	$DB->query($sql,"users.php");
	
$DB->tidy("user");
	
	$sql = "delete from permissions where id = \"$delete2\" and type = \"i\"";
	$DB->query($sql,"users.php");
	
	$DB->tidy("permissions");
	
	
	$sql = "delete from group_members where userid = \"$delete2\"";
	$DB->query($sql,"users.php");
	
$DB->tidy("group_members");
	
	}

	
	
	
	
	
	if(isset($ERROR))
	{
	
		error($ERROR);
	}
	elseif (isset($WARNING))
	{
		warning($WARNING);
	}
	
	
	elseif(isset($_GET["udelete"]))
		{
			
	
        
        $uid = $DB->escape($_GET[udelete]);
        $sql = "select count(*) as leavetotal from leave_requests where user_id = '$uid'";
        $DB->query($sql,"users.php");
        $row = $DB->get();
        $leavetotal = $row["leavetotal"];
	//echo $sql;
        
          $sql = "select count(*) as traveltotal from travel_requests where user_id = '$uid'";
        $DB->query($sql,"users.php");
        $row = $DB->get();
        $traveltotal = $row["traveltotal"];
        
       
        
        if ($traveltotal == 0 && $leavetotal == 0)
        {
        echo "<a href=\"users.php\">$phrase[11]</a><br><br><b>$phrase[14]</b><br><br>$_REQUEST[name]<br><br>
	<a href=\"users.php?udelete2=$_GET[udelete]\">$phrase[12]</a> | <a href=\"users.php\">$phrase[13]</a>";
        }
        else
        {
         echo "<a href=\"users.php\">$phrase[11]</a><br><br><h2>$phrase[1094]</h2>" ;  
        }
        
		}

elseif(isset($_GET["import"]))
		{		
		
			
			
			echo "	<form action=\"users.php\" method=\"post\" enctype=\"multipart/form-data\" style=\"text-align:left;width:80%;margin-top:1em\">

	<fieldset><legend>$phrase[916]</legend><br>
	
	<select name=\"col[0]\">
						<option value=\"username\">$phrase[3]</option>
						<option value=\"first_name\">$phrase[130]</option>
						<option value=\"last_name\">$phrase[131]</option>
						<option value=\"email\">$phrase[259]</option>
						<option value=\"pd\">$phrase[703]</option>
						<option value=\"ldap\">$phrase[765]</option>
						<option value=\"payroll_number\">$phrase[995]</option>
						</select> 
						<select name=\"col[1]\">
						<option value=\"username\">$phrase[3]</option>
						<option value=\"first_name\"   selected>$phrase[130]</option>
						<option value=\"last_name\">$phrase[131]</option>
						<option value=\"email\">$phrase[259]</option>
						<option value=\"pd\">$phrase[703]</option>
						<option value=\"ldap\">$phrase[765]</option>
						<option value=\"payroll_number\">$phrase[995]</option>
						</select> 
					
						<select name=\"col[2]\">
						<option value=\"username\">$phrase[3]</option>
						<option value=\"first_name\">$phrase[130]</option>
						<option value=\"last_name\"  selected>$phrase[131]</option>
						<option value=\"email\">$phrase[259]</option>
						<option value=\"pd\">$phrase[703]</option>
						<option value=\"ldap\">$phrase[765]</option>
						<option value=\"payroll_number\">$phrase[995]</option>
						</select> 
					
						<select name=\"col[3]\">
						<option value=\"username\">$phrase[3]</option>
						<option value=\"first_name\">$phrase[130]</option>
						<option value=\"last_name\">$phrase[131]</option>
						<option value=\"email\"  selected>$phrase[259]</option>
						<option value=\"pd\">$phrase[703]</option>
						<option value=\"ldap\">$phrase[765]</option>
						<option value=\"payroll_number\">$phrase[995]</option>
						</select> 
					
						<select name=\"col[4]\">
						<option value=\"username\">$phrase[3]</option>
						<option value=\"first_name\">$phrase[130]</option>
						<option value=\"last_name\">$phrase[131]</option>
						<option value=\"email\">$phrase[259]</option>
						<option value=\"pd\" selected>$phrase[703]</option>
						<option value=\"ldap\">$phrase[765]</option>
						<option value=\"payroll_number\">$phrase[995]</option>
						</select> 
						
						
						<select name=\"col[5]\">
						<option value=\"username\">$phrase[3]</option>
						<option value=\"first_name\">$phrase[130]</option>
						<option value=\"last_name\">$phrase[131]</option>
						<option value=\"email\">$phrase[259]</option>
						<option value=\"pd\">$phrase[703]</option>
						<option value=\"ldap\" selected>$phrase[765]</option>
						<option value=\"payroll_number\">$phrase[995]</option>
						</select>
						
							<select name=\"col[6]\">
						<option value=\"username\">$phrase[3]</option>
						<option value=\"first_name\">$phrase[130]</option>
						<option value=\"last_name\">$phrase[131]</option>
						<option value=\"email\">$phrase[259]</option>
						<option value=\"pd\">$phrase[703]</option>
						<option value=\"ldap\">$phrase[765]</option>
						<option value=\"payroll_number\"  selected>$phrase[995]</option>
						</select>
						
						<br><br>
						<input type=\"file\" name=\"upload\" ><br><br>
					
						
						

						
				
				
					
						<input type=\"hidden\" name=\"update\" value=\"import\">
						
						
						<input type=\"submit\" name=\"submit\" value=\"$phrase[916]\" ></fieldset>
						</form>	";
		}
		
elseif(isset($_GET["change"]))
		{
			
			$uid = $_REQUEST["change"];			

	
			

	echo "<a href=\"users.php\">$phrase[11]</a><br><br><h4 style=\"border:none;margin-top:0.5em\">$phrase[22] - $_REQUEST[name]</h4>


	
	
	<div>
 <FORM method=\"POST\" action=\"users.php\" name=\"change\"   >
			


<table style=\"width:60%;\">
			<tr><td class=\"label\" style=\"width:50%\">$phrase[32]</td><td class=\"input\"><INPUT type=\"password\" name=\"password1\" id=\"password1\" size=\"30\" maxlength=\"50\"></td></tr>
			<tr><td class=\"label\">$phrase[17]</td><td class=\"input\"><INPUT type=\"password\" name=\"password2\" id=\"password2\" size=\"30\" maxlength=\"50\" ></td></tr>
			<tr ><td class=\"label\"><span id=\"lengthlabel\" style=\"display:none\">$phrase[835]</span></td>
			<td class=\"input\"><img src=\"../images/cross.png\" id=\"lengthresult\" style=\"display:none\"></td></tr>	";
			
			if ($PREFERENCES['complex'] == "1")
{
			echo "<tr  ><td class=\"label\"><span id=\"testlabel\" style=\"display:none\">$phrase[833]</span></td>
			<td class=\"input\" style=\"vertical-align:middle\"><img src=\"../images/cross.png\"  id=\"testresult\" style=\"display:none\"></td></tr>
			<tr><td></td><td class=\"input\"> <span id=\"details\" >$phrase[672]</span></td></tr>
			";
}	

echo "
	<tr ><td class=\"label\" ><span id=\"matchlabel\" style=\"display:none\">$phrase[834]</span></td>
			<td class=\"input\"><img src=\"../images/cross.png\" id=\"matchresult\" style=\"display:none\"></td></tr>
			
				<tr ><td class=\"label\" ><span id=\"duplicate\">$phrase[836]</span></td>
			<td class=\"input\"><img src=\"../images/tick.gif\" id=\"dulicateresult\" ></td></tr>
			<tr><td></td><td class=\"input\">
			<input type=\"hidden\" name=\"update\" value=\"password\">
			<input type=\"hidden\" name=\"uid\" value=\"$uid\">
			<INPUT type=\"submit\" name=\"change\" value=\"$phrase[28]\" id=\"submit\"></td></tr>
			
			
			</table>
			</form></div>
			
<script type=\"text/javascript\">
			<!--

			
			function test(e)
			{
			if (!e) var e = window.event;
			//alert(e.keyCode)
				var passed = 'true';
				var string = password1.value;
				var test;
				var username = '$_REQUEST[name]';
				var splitusername = new Array();
				var duplicatematch = 'false';
				
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
					var duplicatematch = 'true';
				}
				
				splitusername = username.split('.');
				for (y=0; y<splitusername.length; y++)
					{
					//alert(splitusername[y]);
					if (string.indexOf(splitusername[y]) !=-1)
						{
					var duplicatematch = 'true';
					
						}
					} 
				
						splitusername = username.split(' ');
				for (y=0; y<splitusername.length; y++)
					{
					//alert(splitusername[y]);
					if (string.indexOf(splitusername[y]) !=-1)
						{
					var duplicatematch = 'true';
					
						}
					}
				
				if (duplicatematch == 'true')
				{
				document.getElementById('dulicateresult').src= '../images/cross.png';	
				passed = 'false';
				}	
				else
				{
				document.getElementById('dulicateresult').src= '../images/tick.gif';	
				}
				
				
				
				
				if ( test < $PREFERENCES[passwordlength])
				{
				document.getElementById('lengthresult').src= '../images/cross.png';	
				passed = 'false';
				}	
				else
				{
				document.getElementById('lengthresult').src= '../images/tick.gif';	
				}
				";
				
				
				
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

	
elseif(isset($_GET["groups"]))
		{
			$groups = $DB->escape($_GET["groups"]);
			
	echo "<a href=\"users.php\"><img src=\"../images/user.png\" title=\"$phrase[11]\"  alt=\"$phrase[11]\"></a><br><br>
	
		<div >
	<form method=\"POST\" action=\"users.php\" ><fieldset style=\"width:90%\"><legend><b>$_GET[name]</b> $phrase[15]</legend><br>
	";
	
	//put group membership in an array
	$counter = 0;
	$sql = "SELECT groupid FROM group_members where userid = \"$groups\"";
		$DB->query($sql,"users.php");
			while ($row = $DB->get())
									{
				
				 $groupnum[$counter] =$row["groupid"];
				 $counter++;
				 }
				 
		$sql = "SELECT group_id, group_name FROM groups order by group_name";

		$DB->query($sql,"users.php");
		
		//if ($DB->numrows > 0) {echo "<table cellspacing=\"5\" >";}
				while ($row = $DB->get())
									{
				
				 $group_id =$row["group_id"];
				 $group_name =$row["group_name"];
				
				 echo "<div class=\"row\"><input type=\"checkbox\" name=\"id[$group_id]\" ";
				 if (isset($groupnum))
				{
				foreach ($groupnum as $index => $value)
					{
					 if ($value == $group_id) { echo " checked";}
					}
				
				}
				 
				 echo "> $group_name</div>";
				 }
		//if ($DB->numrows > 0) {echo "</table>
		echo "<div class=\"row\"><input type=\"submit\" value=\"$phrase[16]\" name=\"updategroups\">
		<input type=\"hidden\" name=\"admin\" value=\"p\">
	<input type=\"hidden\" name=\"uid\" value=\"$groups\"></div>
	
		</fieldset></form></div>";
		
		
		
		
		}
			

	
	 elseif(isset($_GET["edit"]))
	{  
	$sql = "SELECT * FROM user where userid = '$_GET[edit]'";
	$DB->query($sql,"users.php");
	$row = $DB->get();
	$name= $row["username"];
	$first_name= $row["first_name"];
	$last_name= $row["last_name"];
	$email= $row["email"];
	$payroll_number= $row["payroll_number"];
	
	
	
	
	
	if ($row["expiry"] <> 0)
	{
	
	$expirechecked = " checked";
	$noexpirechecked = "";	
	
	}
	else
	{
	
	$expirechecked = "";
	$noexpirechecked = " checked";	
	}
	
	if ($row["logonchange"] <> 0)
	{
	$logchecked = " checked";
	$nologchecked = "";		
	}
	else {
	$logchecked = "";
	$nologchecked = " checked";	
	}
	
	if ($row["disabled"] <> 0)
	{
	$disablechecked = " checked";
	$nodisablechecked = "";		
	}
	else {
	$disablechecked = "";
	$nodisablechecked = " checked";	
	}
	
	
	
				
	
	 
echo "

<a href=\"users.php\">$phrase[11]</a><br><br>
<div >
<FORM method=\"POST\" action=\"users.php\" name=\"edituser\" >
<fieldset>
<legend>$phrase[27]</legend><table cellpadding=\"5\" style=\"text-align:left;\">";

if ($row["username"] == "guest")
{ echo $phrase[655];}
else {

				echo "<tr><td><label>$phrase[3]</label> </td><td> <input type=\"text\" name=\"name\" size=\"50\" maxlength=\"100\" value=\"$row[username]\"></td></tr>
				<tr><td>				<label>$phrase[130]</label></td><td> <input type=\"text\" name=\"first_name\" value=\"$first_name\" size=\"50\" maxlength=\"250\"></td></tr>
<tr><td>				<label>$phrase[131]</label></td><td> <input type=\"text\" name=\"last_name\" value=\"$last_name\"size=\"50\" maxlength=\"250\"></td></tr>
<tr><td>				<label>$phrase[259]</label></td><td> <input type=\"text\" name=\"email\" value=\"$email\" size=\"50\" maxlength=\"250\"></td></tr>

<tr><td>				<label>$phrase[995]</label></td><td> <input type=\"text\" name=\"payroll_number\" value=\"$payroll_number\" size=\"50\" maxlength=\"50\"></td></tr>
		
		<tr><td><label>$phrase[23]</label> </td><td><input type=\"radio\" name=\"authtype\" value=\"mysql\" onClick=\"mysql()\"";
if ($row["authtype"] == "mysql") { echo " checked";}

echo "> MySQL&nbsp; <input type=\"radio\" name=\"authtype\" value=\"Active Directory\" onClick=\"ldap()\"";
if ($row["authtype"] == "Active Directory") { echo " checked";}
echo "> Active directory
&nbsp; <input type=\"radio\" name=\"authtype\" value=\"ldap\" onClick=\"ldap()\"";
if ($row["authtype"] == "ldap") { echo " checked";}
echo "> Openldap
&nbsp; <input type=\"radio\" name=\"authtype\" value=\"email\" onClick=\"ldap()\"";
if ($row["authtype"] == "email") { echo " checked";}
echo "> $phrase[276]
</td></tr>
		
	


<tr><td ><label>$phrase[764]</label>  </td><td ><input type=\"text\" id=\"ldapdn\" name=\"ldapdn\" size=\"60\" maxlength=\"250\" value=\"$row[ldapdn]\"></td></tr>
		
		
		<tr><td><label>$phrase[18]</label>  </td><td><input type=\"radio\" name=\"logonchange\" value=\"0\" $nologchecked> $phrase[13]   <input type=\"radio\" name=\"logonchange\" value=\"1\" $logchecked> $phrase[12]</td></tr>
		<tr><td><label>$phrase[19]</label> </td><td>  <input type=\"radio\" name=\"expiry\" value=\"0\" $noexpirechecked> $phrase[13] <input type=\"radio\" name=\"expiry\" value=\"1\" $expirechecked> $phrase[12]</td></tr>
		
		
		
		
		
		
		<tr><td><label>$phrase[20]</label> </td><td> <input type=\"radio\" name=\"disabled\" value=\"0\" $nodisablechecked> $phrase[13] <input type=\"radio\" name=\"disabled\" value=\"1\" $disablechecked> $phrase[12]</td></tr>
			
		<tr><td>	<label>&nbsp;</label> </td><td> <input type=\"submit\" name=\"updateuser\" value=\"$phrase[28]\">
			<input type=\"hidden\" name=\"uid\" value=\"$row[userid]\"></td></tr>";
}
		
		
			echo 	"</table></fieldset>
</form></div>


<script  type=\"text/javascript\">


function mysql() {
     
	document.getElementById('ldapdn').disabled = true;
	
	document.getElementById('ldapdn').className = 'greybackground';
}

function ldap() {

  
	document.getElementById('ldapdn').disabled = false;
		
	document.getElementById('ldapdn').className = '';
    }

   ";
if ($row["authtype"] == "mysql")
{
echo "window.onload=mysql";
}

echo "</script>";
		
		   
	}  
	elseif(isset($_GET["adduser"]))
	{	
		$random = random(32);
		$daymenu = menu("day",1,365,0);
		
		
				

	
		 
echo "


<a href=\"users.php\">$phrase[11]</a><br><br>
<div >
<FORM method=\"POST\" action=\"users.php\" >
<fieldset>
<legend>$phrase[21]</legend>
<table cellpadding=\"5\" style=\"text-align:left;\">
<tr><td>				<label>$phrase[3]</label></td><td> <input type=\"text\" name=\"name\" size=\"50\" maxlength=\"100\"></td></tr>
<tr><td>				<label>$phrase[130]</label></td><td> <input type=\"text\" name=\"first_name\" size=\"50\" maxlength=\"250\"></td></tr>
<tr><td>				<label>$phrase[131]</label></td><td> <input type=\"text\" name=\"last_name\" size=\"50\" maxlength=\"250\"></td></tr>
<tr><td>				<label>$phrase[259]</label></td><td> <input type=\"text\" name=\"email\" size=\"50\" maxlength=\"250\"></td></tr>
<tr><td>				<label>$phrase[995]</label></td><td> <input type=\"text\" name=\"payroll_number\" size=\"50\" maxlength=\"50\"></td></tr>
	<tr><td>	<label>$phrase[23]</label></td><td><input type=\"radio\" name=\"authtype\" value=\"mysql\" onClick=\"mysql()\"  checked> MySQL &nbsp; <input type=\"radio\" name=\"authtype\" value=\"Active Directory\" onClick=\"ldap()\"/> LDAP/Active Directroy  &nbsp; <input type=\"radio\" name=\"authtype\" value=\"ldap\" onClick=\"ldap()\"/> Openldap
	
	 &nbsp; <input type=\"radio\" name=\"authtype\" value=\"email\" onClick=\"ldap()\"/> $phrase[276]
	</td></tr>
		
		
	<tr>	<td id=\"label\"><label>$phrase[764]</label></td><td> <input type=\"text\"  id=\"ldapdn\" name=\"ldapdn\" size=\"50\" maxlength=\"100\"></td></tr>

	<tr><td >	<label >$phrase[18]</label> </td><td ><input type=\"radio\" name=\"logonchange\" value=\"0\" checked> $phrase[13] <input type=\"radio\" name=\"logonchange\" value=\"1\"> $phrase[12]</td></tr>
		<tr><td>	<label>$phrase[19]</label> </td><td><input type=\"radio\" name=\"expiry\" value=\"0\" checked> $phrase[13] <input type=\"radio\" name=\"expiry\" value=\"1\" > $phrase[12]</td></tr>
		
		
			
			<tr><td>	<label>&nbsp;</label> </td><td><input type=\"submit\" name=\"submituser\" value=\"$phrase[21]\"></td></tr>
		
		</table>
				</fieldset>
</form></div>
		
		<script type=\"text/javascript\">



function mysql() {
     
	document.getElementById('ldapdn').disabled = true;
	
	document.getElementById('ldapdn').className = 'greybackground';
}

function ldap() {

	document.getElementById('ldapdn').disabled = false;
	
	document.getElementById('ldapdn').className = '';
    }
    
 
   
window.onload=mysql


</script>
				
";
		
	}

		
		
	else
		{
		
					
				$sql = "select * from user order by username";
				$DB->query($sql,"users.php");
				echo "<a href=\"users.php?adduser=mysql\"><img src=\"../images/user_add.png\" title=\"$phrase[21]\"  alt=\"$phrase[21]\"></a>
				<a href=\"users.php?import=csv\" style=\"padding-left:2em\"><img src=\"../images/calendar_empty.png\" title=\"$phrase[916]\"  alt=\"$phrase[916]\"></a>
				
				<br><br>
				<div >
				<form  action=\"\"><fieldset><legend>$phrase[11]</legend>
				<br><table cellpadding=\"7\" style=\"text-align:left\">";
			while ($row = $DB->get())
									{
								 $uid =$row["userid"];
								 $uname =$row["username"];
								 $disabled =$row["disabled"];
								 $authtype =$row["authtype"];
								 if ($row["lastchanged"] > 0)
								 { $lastchanged = strftime("%x", $row["lastchanged"]); } else { $lastchanged = "";}
								$name = urlencode($uname);
								echo "<tr";
								if ($disabled == 1) {echo " style=\"background:#E6E6E6\"";}
								echo "><td>$row[username]</td>
								<td><span class=\"grey\">$authtype</span></td>
								
								
								<td>";
								
								if ($uname != "guest")
								{
								echo "<a href=\"users.php?edit=$uid\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>";
								}
								
								
								
								echo "<td>";
								if ($authtype == "mysql" && $uname != "guest")
								{
									echo "
								
								<a href=\"users.php?change=$uid&amp;name=$name\"><img src=\"../images/key.png\" title=\"$phrase[22]\"  alt=\"$phrase[22]\"></a>";
								}
								
								echo "</td>
								
								<td>";
								if ($lastchanged != "")
								{ 
								echo "<img src=\"../images/clock.png\" title=\"$phrase[837] $lastchanged \" alt=\"$phrase[837] $lastchanged \">";}
								echo "</td>
								
								<td><a href=\"users.php?groups=$uid&amp;name=$name\"><img src=\"../images/group.png\" title=\"$phrase[25]\"  alt=\"$phrase[25]\"></a></td>
								
								<td>";
								if ($uid != 1 && $uname != "guest")
										{
										 echo "<a href=\"users.php?udelete=$uid&amp;name=$name\"><img src=\"../images/user_delete.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>";
										}
													 
								 echo "</td></tr>\n";
										}	
				echo "</table></fieldset></form></div>";
				
				
				
		}

		
echo "</div></div>";
		
	 
	  	include("../includes/rightsidebar.php");   

include ("../includes/footer.php");

?>

