



<?php



	  if (dirname($_SERVER["PHP_SELF"]) <> "/")
{
$dirnow = dirname($_SERVER["PHP_SELF"]);
$dirup = dirname($dirnow);
	
 $cssurl = "http://" .$_SERVER["SERVER_NAME"] . $dirup."/stylesheets/";
}
else 
{
 $cssurl = "http://" .$_SERVER["SERVER_NAME"] ."/stylesheets/";
}

include("../includes/initiliaze_page.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">

<html>
	<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
	<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
	<title>$PREFERENCES[sitename]</title>
    <LINK REL=\"STYLESHEET\" TYPE=\"text/css\" HREF=\"$cssurl$PREFERENCES[stylesheet]\" >
	

	</head>

<body style=\"text-align:center;margin:20% 35%\">
<h1>$phrase[637]</h1>
$phrase[638]


</body>
</html>
";

?>