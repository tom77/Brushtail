<?php

$lm = gmdate('D, d M Y H:i:s T', (time() - 432000));
    

header("Last-Modified: $lm GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");  
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");  



if (isset( $modstylesheet) && $modstylesheet!= ""  && substr($modstylesheet, -4) == ".css")
{
   $modstylesheet = str_replace("..","",$modstylesheet) ;
   $modstylesheet = str_replace("/","",$modstylesheet) ;
   $modstylesheet = str_replace("\\","",$modstylesheet) ;
    $stylesheet =  $modstylesheet;
} else {$stylesheet =$PREFERENCES["stylesheet"];}

 $csslink = "../stylesheets/" . $stylesheet;

 
 if (isset($page_title) && $page_title != "") 
	 {$doc_title = $page_title;} 
 elseif (isset($modname))
 	{$doc_title = $modname; }
 	else {$doc_title = $PREFERENCES["sitename"]; }


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
	<head>
            <meta charset="utf-8">
	
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">

	<title><?php echo $doc_title?></title>
    <LINK REL="STYLESHEET" TYPE="text/css" HREF="<?php echo $csslink?>"> 
  <?php
  
  if  (isset($datepicker))
  {
  	echo "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"../main/jsDatePick_ltr.min.css\" />
  	<script type=\"text/javascript\" src=\"../main/jsDatePick.min.1.3.js\"></script>
";
  }
  
  
  ?>

 

        
 
	<link href="../stylesheets/print.css" rel="stylesheet" type="text/css" media="print">
	<script type="text/javascript" src="../main/brushtail.js"></script>
	</head>
<?php 

	echo "<body ";
	if (isset($bodyjavascript)) {echo $bodyjavascript;}
	echo ">

	";
	
	?>
					