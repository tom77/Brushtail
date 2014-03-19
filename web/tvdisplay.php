<?php 
session_start();
if (!isset($_SESSION['count'])) {
  $_SESSION['count'] = 0;
}
$time = date("Ymd h:i s");

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);


$link = "tvcontent.php?c=";
if (isset($_REQUEST["c"])) {$link .= $_REQUEST["c"];}

echo "<!DOCTYPE html>
<html><head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<style>
body {background:white;margin:0;padding:0;text-align:center;font-family:arial;width:100%;height:100%}
#frame2 { position: fixed;
    width: 100%;
    height: 100%;
}

</style>
</head>
<body>

 <script type=\"text/javascript\">

 
  //window.slidetimer = 30000;
  
 // window.ts = setTimeout(refresh,window.slidetimer);
 
 function refresh()
 {
 try
  {
 // alert('loading page')
document.getElementById('frame').src = '$link';

}
catch(err)
  {
   window.slidetimer = 60000;
window.ts = setTimeout(refresh,window.slidetimer);
 }
// alert(window.slidetimer)
  
 }
 
window.onload=refresh
 
 </script>";
 //today is $time count is $_SESSION[count]


echo "<iframe id=\"frame\" src=\"\" style=\"width:100%;height:100%;border-style:none;\"></iframe>
</body>
</html>


";

?>