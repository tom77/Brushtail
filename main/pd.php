<?php




include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

/*
//version check
if (!$PREFERENCES["pdmanager"])
{
//upgrade to 1.1	
echo "<h1 style=\"color:red;margin:auto 0\">PD Manager Database upgraded to 1.1!</h1<br><br>";



if ($DB->type == "sqlite")
			{
			
			$sql = "CREATE TABLE pd_options2 ( m INTEGER(11) NOT NULL, email VARCHAR(250) NOT NULL, email_action INTEGER(1) NOT NULL DEFAULT '2', showprint INTEGER(1) NOT NULL, instructions TEXT, showlogo TINYINT(1), email_user TINYINT(1),
 replacement_text VARCHAR(250) )";
			$DB->query($sql,"pd.php");

			$sql = "INSERT INTO pd_options2  SELECT m,email,email_action,showprint,instructions,showlogo,\"1\" as email_user,\"\" as replacement_text FROM pd_options";
			$DB->query($sql,"pd.php");

			$sql = "drop table pd_options";
			$DB->query($sql,"pd.php");

			$sql = "CREATE TABLE pd_options ( m INTEGER(11) NOT NULL, email VARCHAR(250) NOT NULL, email_action INTEGER(1) NOT NULL DEFAULT '2',
 showprint INTEGER(1) NOT NULL, instructions TEXT, showlogo TINYINT(1), email_user TINYINT(1), replacement_text VARCHAR(250) )";
			$DB->query($sql,"pd.php");

			$sql = "INSERT INTO pd_options SELECT m,email,email_action,showprint,instructions,showlogo,email_user,replacement_text FROM pd_options2";
			$DB->query($sql,"pd.php");

			$sql = "drop table pd_options2";
			$DB->query($sql,"pd.php");
			
			
			
						
$sql = "INSERT INTO preferences (pref_name ,pref_value)VALUES ('pdmanager', '1.1')";
$DB->query($sql,"pd.php");
				}
}
				
elseif ($PREFERENCES["pdmanager"] == 1.1)

{
	



if ($DB->type == "sqlite")
			{
			
			$sql = "CREATE TABLE pd_options2 ( m INTEGER (11) NOT NULL , email VARCHAR (250) NOT NULL , email_action INTEGER (1) NOT NULL DEFAULT '2' , showprint INTEGER (1) NOT NULL , instructions TEXT , showlogo TINYINT (1) , email_user TINYINT (1) , replacement_text VARCHAR (250) , print_heading VARCHAR (200) ) "; 
			$DB->query($sql,"pd.php");
			
$sql = "INSERT INTO pd_options2  SELECT m,email,email_action,showprint,instructions,showlogo,email_user,replacement_text,\"Prfoessional develpment certificate\" as print_heading FROM pd_options";
			$DB->query($sql,"pd.php");

			$sql = "drop table pd_options";
			$DB->query($sql,"pd.php");
			
			$sql = "CREATE TABLE pd_options ( m INTEGER (11) NOT NULL , email VARCHAR (250) NOT NULL , email_action INTEGER (1) NOT NULL DEFAULT '2' , showprint INTEGER (1) NOT NULL , instructions TEXT , showlogo TINYINT (1) , email_user TINYINT (1) , replacement_text VARCHAR (250) , print_heading VARCHAR (200) ) "; 
			$DB->query($sql,"pd.php");
			
$sql = "INSERT INTO pd_options  SELECT m,email,email_action,showprint,instructions,showlogo,email_user,replacement_text,\"Professional development certificate\" as print_heading FROM pd_options2";
			$DB->query($sql,"pd.php");

			$sql = "drop table pd_options2";
			$DB->query($sql,"pd.php");
			
			
			$sql = "update preferences set pref_value = '1.2' where pref_name = 'pdmanager'";
$DB->query($sql,"pd.php");

echo "<h1 style=\"color:red;margin:auto 0\">PD Manager Database upgraded to 1.2!</h1<br><br>";
			
}
}
*/

	
if (isset($ERROR))
	{
   	header("Location: $url" . "error.php?error=input");
	exit();
	}
        
  elseif (!isset($m))
    {
	header("Location: $url" . "error.php?error=module");
	exit();
    }


	$access->check($m);

if ($access->thispage < 1)
		{
		
		
		header("Location: $url" . "error.php?error=permissions");
		exit();
		}
	elseif ($access->iprestricted == "yes")
		{
		header("Location: $url" . "error.php?error=ipaccess");
		exit();
		}	





if ($access->thispage == 1)
	{
		echo "<div style=\"text-align:center\">";
	include ("../classes/PdRead.php");
	$pdread = new PdRead($m,$DB);
		echo "</div>";
	}
	
	elseif ($access->thispage > 1)
	{
			echo "<div style=\"text-align:center\">";
	include ("../classes/PdEdit.php");
	$pdedit = new PdEdit($m,$DB,$PREFERENCES);	
	echo "</div>";
	}
	
	
	
include ("../includes/footer.php");
?>