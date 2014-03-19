<?php
// Set time limit to indefinite execution
set_time_limit (0);

include("../includes/initiliaze_page.php");
include("../classes/AuthenticatePatron.php");

// Set the ip and port we will listen on
$address = '192.168.10.233';
$port = 110;

// Create a TCP Stream socket
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
// Bind the socket to an address/port
socket_bind($sock, $address, $port) or die('Could not bind to address');
// Start listening for connections
socket_listen($sock);

/* Accept incoming requests and handle them as child processes */
listen($sock,$PREFERENCES,$DB);

function listen($sock,$PREFERENCES,$DB)
{

$client = socket_accept($sock);


	
$myFile = "poplog.txt";
$fh = fopen($myFile, 'a');
fwrite($fh, "socket_accept" );



socket_write($client,"+OK POP3 Server Ready\r\n");

$username = "";

$state = "username";

//


$string = "";
while($client)
{
// Read the input from the client &#8211; 1024 bytes
$input = socket_read($client, 1024);

//$integer = ord($input);

$string .= $input;
//socket_write($client, $integer);	
// Strip all white spaces from input

$last = ord(substr($string, strlen($string)-1, 1));
//socket_write($client, $last);

if ($last == 13 || $last == 10)
{
	
	
$myFile = "poplog.txt";
$fh = fopen($myFile, 'a');

fwrite($fh, "$string" );	
	
	
	
//socket_write($client, "$string");
$peices = explode(" ", $string);




if (strtoupper(trim($peices[0])) == "QUIT")
{
socket_write($client, "+OK POP3 server signing off\r\n");
$string = "";	



socket_close($client);
listen($sock,$PREFERENCES,$DB);

}


if ($state == "transaction") {
	


if (strtoupper(trim($peices[0])) == "STAT")
{


socket_write($client, "+OK 0 0\r\n");	
$string = "";

}
else 

	//if (strtoupper(trim($peices[0])) == "NOOP")
{


socket_write($client, "+OK\r\n");
$string = "";	

}
	
}







elseif (strtoupper($peices[0]) == "PASS" && strlen($peices[1]) > 0 )
	{
	$password = trim($peices[1]);

	if ($username == "")
	{
	socket_write($client, "-ERR Missing username\r\n");	
	$string = "";	
	}
	else 
	{
		
		$CHECK = new AuthenticatePatron($username,$password,$PREFERENCES,$DB,"pc");
	
	$message = "$username $password" . $CHECK->auth;	
	ssocket_write($client, "-ERR $message\r\n");	
		
	$myFile = "poplog.txt";
$fh = fopen($myFile, 'a');
fwrite($fh, $message );
	
	
	
		if ($CHECK->auth == "yes")
	{
		

	socket_write($client, "+OK 0 messages\r\n");	
	$state = "transaction";
	$string = "";
	}
	else 
	{
	socket_write($client, "-ERR Authentication failed\r\n");	
	$string = "";
	}
	}
	//test password
	
	
	
	}







elseif (strtoupper($peices[0]) == "USER" && strlen($peices[1]) > 0)
	{
	$username = trim($peices[1]);
	$state = "password";
	


	socket_write($client, "+OK Please send PASS command\r\n");
	$string = "";
	}
else 
	{
	socket_write($client, "-ERR Invalid Command\r\n");
	$string = "";
	}








	
	


}
// Display output back to client


}

}
// Close the client (child) socket


// Close the master sockets
socket_close($sock);
?> 