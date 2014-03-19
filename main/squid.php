<?php


$hostname = $_SERVER["HOSTNAME"];


//
//manually edit url if necessary
$url = "http://" . $hostname . "/main/". "webauth.php";
//



//echo $url;

if (! defined(STDIN)) {
        define("STDIN", fopen("php://stdin", "r"));
}
while (!feof(STDIN)) {
        $line = trim(fgets(STDIN));
        $fields = explode(' ', $line);
        $barcode = rawurldecode($fields[0]); //1738
        $password = rawurldecode($fields[1]); //1738
        
        	$url .= "?event=test&barcode=" . urlencode($barcode) . "&password=" . urlencode($password);
        	
        	$handle = @fopen($url, "rb");
			$contents = @stream_get_contents($handle);
			@fclose($handle);


	
if (strstr($contents,"OK")) 
				{
				
echo "OK\n";	
	}
	else {
		echo "ERR\n";
	}

}

?>