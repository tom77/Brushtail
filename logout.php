<?php
session_start();



include("includes/config.php");
include("includes/classes.php");
include("includes/functions.php");

//if ($DATABASE_FORMAT == "sqlite" && substr($SQLITE,0, 3) == "../" && $_SERVER["SCRIPT_NAME"] == "/logout.php")
//{



//}

		

if ($DATABASE_FORMAT == "sqlite")
{
if (substr($SQLITE,0,3) == "../")	
{	
$SQLITE = substr_replace($SQLITE,"", 0,3 );
}

$DB = new SQLITE_DB($DATABASE_FORMAT);	
$connection = $DB->connect($SQLITE);

}


if ($DATABASE_FORMAT == "sqlite3")
{
if (substr($SQLITE,0,3) == "../")	
{	
$SQLITE = substr_replace($SQLITE,"", 0,3 );
}

$DB = new SQLITE3_DB($DATABASE_FORMAT);	
$connection = $DB->connect($SQLITE);

}


if ($DATABASE_FORMAT == "mysql")
{
	
$DB = new MYSQL_DB($DATABASE_FORMAT);	
$connection = $DB->connect($HOST, $DATABASE, $DBUSER, $DBPASSWORD);
}

if ($DATABASE_FORMAT == "mysqli")
{
	
$DB = new MYSQLI_DB($DATABASE_FORMAT);	
$connection = $DB->connect($HOST, $DATABASE, $DBUSER, $DBPASSWORD);
}



//new preferences();
$sql = "select * from preferences";
$DB->query($sql,"initialize_page.php");
$numrows = $DB->countrows();

//Put preferences into an array;
while ($row = $DB->get()) {
$PREFERENCES[$row["pref_name"]] = $row["pref_value"]; 
}



if (isset($_SESSION['userid']))
{
	


unset($_SESSION['userid']);
unset($_SESSION['username']);
unset($_SESSION['hash']);


$logouturl = trim($PREFERENCES["logout"]);

if ($logouturl <> "")
{
header("Location: $PREFERENCES[logout]");
}
else 

{

	
		header("Location: index.php");
}
}


?>