<?php

session_start();

 


include("config.php");
include("phrases.php");
include("classes.php");
include("functions.php");


if  (basename($_SERVER['PHP_SELF']) != "index.php") {include('../classes/Widgets.php');}

if ($COMPRESSION == "on" && function_exists('ob_start')) {ob_start("ob_gzhandler");}

if (($DATABASE_FORMAT == "sqlite" || $DATABASE_FORMAT == "sqlite3") && substr($SQLITE,0, 3) == "../" && (basename($_SERVER["PHP_SELF"]) == "index.php" || basename($_SERVER["PHP_SELF"]) == "logout.php"))
{

$SQLITE = substr_replace($SQLITE,"", 0,3 );

}


$curdir = str_replace("\\", "/",dirname($_SERVER["PHP_SELF"]));
if ($curdir != "/") { $curdir = $curdir . "/";}
	
$servername  = $_SERVER["SERVER_NAME"];
$port = $_SERVER["SERVER_PORT"];

	
if ($port != 80) {$port = ":$port";} else {$port = "";}
	
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "" &&  $_SERVER["HTTPS"] != "off") {$url = "https";} else {$url = "http";}
	
$url .= "://" .$servername .$port. $curdir;




$money = localeconv ();
$moneysymbol = $money['currency_symbol'];

if (get_magic_quotes_gpc()) { 
 $_GET    = strip($_GET); 
 $_POST   = strip($_POST); 
 $_REQUEST   = strip($_REQUEST); 
} 		

//new mysql();



if ($DATABASE_FORMAT == "sqlite")
{

$DB = new SQLITE_DB($DATABASE_FORMAT);	
$connection = $DB->connect($SQLITE);

}

if ($DATABASE_FORMAT == "sqlite3")
{

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
$DATABASE_FORMAT = "mysql"; //both versions mysql interface can use same sql queries

}

//  echo "type is " . $DB->type;

if (!$DB) {
 echo "Failed to load $DATABASE_FORMAT database!";
	exit();
}


//new preferences();
$sql = "select * from preferences";
$DB->query($sql,"initialize_page.php");
//$numrows = $DB->countrows();

//Put preferences into an array;
while ($row = $DB->get()) {
$PREFERENCES[$row["pref_name"]] = $row["pref_value"]; 
}

if (!isset($PREFERENCES))
{
echo " Database tables are missing.";	
exit();
}

if ($PREFERENCES["guest"] == "1" && !(isset($_SESSION['userid']))	)
{
//$_SESSION = array();	

$sql = "select userid from user where username = \"guest\"";
$DB->query($sql,"initialize_page.php");
$row = $DB->get();

$_SESSION['userid'] = $row["userid"];
$_SESSION['username'] = "guest";	

}


if (isset($_SESSION["userid"]))
{
$access = new Access($DB) ;
$access->mmenu();

$access->cmenu();



if (isset($_REQUEST["m"]) )
{
 $m = $_REQUEST["m"] * 1;    
    

 
$sql = "select  * from modules ";
	
$_modtypeslist = array();          

$DB->query($sql,"page.php");
	//$num_rows = $DB->countrows();
	while ($row = $DB->get())
        {
          
	if ($row["m"] == $m)
        {
        $modname = formattext($row["name"]);
        $modstylesheet = trim($row["stylesheet"]);
        $modhidemenu = $row["hidemenu"];
        $moduletype = $row["type"];	
        }
        
        $_m = $row["m"];
        $_modtypeslist[$_m] = $row["type"];
        }
	

 
//print_r($_modtypeslist);

$sql = "select id ,position ,target,sidebar, type  from widgets where m = '$m'  order by position";	

//echo $sql;
$DB->query($sql,"initialize_page.php");
while ($row = $DB->get())
{
$_type = trim($row["type"]);	
$_target = $row["target"];
$_id = $row["id"];
//echo "id is $id";
$pos = strpos($_target, "-");
if ($pos === false) { $_targetm = $_target;} else {$_peices = explode("-",$_target); $_targetm = $_peices[0];}

//echo "target is $_targetm <br>";
foreach ($_modtypeslist as $key => $value)
    {
    if ($_targetm == $key) {$_targetType = $value; }
    }
$_sidebar = $row["sidebar"];
$_height = $row["position"];



if ($_sidebar == "c" ) //c = content
{
$content_widgets_type[] = $_type;
$content_widgets_target[] = $_target;
$content_widgets_target_type[] = $_targetType;	
$content_widgets_height[] = $_height;
$content_widgets_id[] = $_id;	


}
//print_r($content_widgets_id);

if ($_sidebar == "l") //left sidebar
{
$left_widgets_type[] = $_type;
$left_widgets_target[] = $_target;	
}

if ($_sidebar == "r") //right sidebar
{
$right_widgets_type[] = $_type;
$right_widgets_target[] = $_target;	
}

}
//print_r($right_widgets_target);

}

}

	$align_label[1] = $phrase[936]; //left align
	$align_label[2] = $phrase[938]; //right align
	$align_label[3] = $phrase[1025]; //Centre align
	$align_label[4] = $phrase[1026]; //Right wrap
	$align_label[5] = $phrase[1027]; //Left wrap
	
	
	
	
if (isset($SMTP)){
ini_set('SMTP', $SMTP);
}

if (isset($EMAIL_FROM)){
ini_set('sendmail_from', $EMAIL_FROM);






}
?>