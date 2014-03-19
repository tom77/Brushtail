<?php



include ("../includes/initiliaze_page.php");


if (!isset($_REQUEST["ip"])) {echo "No ip address supplied"; exit();}



if ($_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"])
{
	
	
	
	$ip = $_REQUEST["ip"];
	$pcno = $_REQUEST["pcno"];

ini_set('zlib.output_compression', 0);
ini_set('implicit_flush', 1);
ob_start();
 
echo "<html><head></head><body>";
echo str_repeat(" ", 256); 
echo "Client polled!</body></html>";

ob_end_flush();
flush();

$data_to_send = "event=check&pcno=$pcno";
		
$output = "";
$fp = @fsockopen($ip,12000, $errno, $errstr, 10);
@fputs($fp, "POST /index.php HTTP/1.1\n" );
@fputs($fp, "Host: $ip\n" );
@fputs($fp, "Content-type: application/x-www-form-urlencoded\n" );
@fputs($fp, "Content-length: ".strlen($data_to_send)."\n" );
@fputs($fp, "Connection: close\n\n" );
@fputs($fp, $data_to_send);

 if (gettype($fp) == "resource") {
  while (!feof($fp)) {
        $output .= @fgets($fp, 128);
    }
 }
@fclose($fp);

//echo  $output;


	
}
else 
{
	echo "access not allowed from " . $_SERVER["REMOTE_ADDRESS"];
}

?>