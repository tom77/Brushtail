<?php

include ("../includes/initiliaze_page.php");

$url = $url . "pollclient.php";

echo "url is $url";

$html =  file_get_contents($url);

echo $html;



?>