<?php





class AuthenticatePatron
{	

private $PREFERENCES;
public $auth;
public $failure;
private $method;
private $password;
private $barcode;
private $formContent;
private $phrase;

	function __construct($password,$barcode,$PREFERENCES,$DB,$type)
		{
		include("../includes/config.php");
		
                global $phrase;
                $this->phrase = $phrase;
                
                
		if ($type == "pc")
		{
			
			if ($PCAUTHENTICATION == "web") {$this->web($password,$barcode,$WEBHOST,$WEBPATH,$STRINGACTION,$STRING,$USERFIELD,$PASSWORDFIELD,$OTHERFIELDS,$PROXY_IP,$PROXY_PORT,$DB);} 
		
		elseif ($PCAUTHENTICATION == "local") {$this->local($password,$barcode,$DB);}
		elseif ($PCAUTHENTICATION == "email") {$this->email($password,$barcode,$MAILSERVER);}
		elseif ($PCAUTHENTICATION == "ldap") {$this->ldap($password,$barcode,$LDAPHOST);}
		else {  $this->auth = "no";
				$this->failure = $this->phrase[1111]; 
				}
		}
		
		
			elseif ($type == "event")
		{
				if ($EVENTAUTHENTICATION == "web") {$this->web($password,$barcode,$WEBHOST,$WEBPATH,$STRINGACTION,$STRING,$USERFIELD,$PASSWORDFIELD,$OTHERFIELDS,$PROXY_IP,$PROXY_PORT,$DB);} 
		
		elseif ($EVENTAUTHENTICATION == "local") {$this->local($password,$barcode,$DB);}
		elseif ($EVENTAUTHENTICATION == "email") {$this->email($password,$barcode,$MAILSERVER);}
		elseif ($EVENTAUTHENTICATION == "ldap") {$this->ldap($password,$barcode,$LDAPHOST);}
		else {  $this->auth = "no";
				$this->failure = $this->phrase[1111]; 
				}	
		}
		else 
		{
		 $this->auth = "no";
				$this->failure = $this->phrase[1111]; 	
		}
			
		
		}
	
                
                private function get_web_page( $url )
{
    $options = array( 'http' => array(
        'user_agent'    => 'spider',    // who am i
        'max_redirects' => 10,          // stop after 10 redirects
        'timeout'       => 120,         // timeout on response
    ) );
    $context = stream_context_create( $options );
    $page    = @file_get_contents( $url, false, $context );
 
    $result  = array( );
    if ( $page != false )
        $result['content'] = $page;
    else if ( !isset( $http_response_header ) )
        return null;    // Bad url, timeout

    // Save the header
    $result['header'] = $http_response_header;

    // Get the *last* HTTP status code
    $nLines = count( $http_response_header );
    for ( $i = $nLines-1; $i >= 0; $i-- )
    {
        $line = $http_response_header[$i];
        if ( strncasecmp( "HTTP", $line, 4 ) == 0 )
        {
            $response = explode( ' ', $line );
            $result['http_code'] = $response[1];
            break;
        }
    }
 
   // $this->formContent = $result;
    return $result;
}
                
                
                
                
                
	private function web($password,$barcode,$webhost,$webpath,$stringaction,$string,$userfield,$passwordfield,$otherfields,$proxy_ip,$proxy_port,$DB)
{
 
	
	$cookie = "";
	//$vars = "$userfield=" . urlencode($barcode) . "&$passwordfield=" . urlencode($password);	
	$vars = "$userfield=$barcode&$passwordfield=$password";		
	
	if (isset($otherfields) && is_array($otherfields))
	{
		
          
            
	 foreach($otherfields as $fieldname => $fieldvalue)
              {	
              	if ($fieldvalue <> "" && $fieldname != "formurl")
              	{
              	$vars .= "&$fieldname=" . urlencode($fieldvalue); 
              	}
              }
         
              
             if (array_key_exists("formurl",$otherfields))
	{
		///
                 
                 echo "";
	
		if (isset($proxy_ip) && $proxy_ip != "")
		{

			
		$html = GetViaProxy ($proxy_ip,$proxy_port,$otherfields["formurl"]);
		
		}
		else 
		{
		//$html = @file_get_contents($otherfields["formurl"]);
                 $result = $this->get_web_page($otherfields["formurl"]);
                 $html = $result["content"];  
                 $headers = $result["header"]; 
                 //print_r($headers);
               // echo "<h1>cookiepage is $html</h1>";
                 
                 
                 $cookie = "Cookie:";
               //  $lines = explode(PHP_EOL, $headers);
                 foreach ($headers as $value)
                 {
                  $pos = strpos($value, "Set-Cookie:"); 
                  if ($pos === false) {} else {
                      $words = explode(" ",$value);
                      //echo "value is $value";
                     // print_r($words);
                      
                      $cookie .= " " .$words[1];
                      
                      }
                      
                 }
                 $cookie = rtrim($cookie,";");
                 //echo "CC  $cookie CC";
                 //print_r($cookies);
		}
		
		//$string = strtolower($string);
		$html = str_replace  ("INPUT","input",$html);
		$html = str_replace  ("HIDDEN","hidden",$html);
		
		preg_match_all  ('/<[\s]*input[\s\S]*?>/' , $html  , $matches );

		$values = array();

		foreach ($matches[0] as $value)
		{
			if (preg_match  ('/[\s]*type[\s]*=[\s"]*hidden[\s"]/' , $value )) 
			{
		
			preg_match  ('/[\s]*value[\s]*=[\s"]*([^"]*)[\s"]/' , $value,$X );
			$v = $X[1];
		
			preg_match  ('/[\s]*name[\s]*=[\s"]*([^"]*)[\s"]/' , $value,$X );
			$n = $X[1];
		
			//$values[$n] = $v;
			$vars .= "&$n=$v";
		////$counter++;
		
		
			}
		}

$vars .= "&submit=Login";
//echo "<hr>$vars<hr>";
	}
		        
                   
	}
        
      
  
/*			# compose HTTP request header
$header = "Host: $webhost\r\n";
$header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: ".strlen($vars)."\r\n";
$header .= "Connection: close\r\n\r\n";
$output = "";
*/

if ($barcode == "" )
{

  $this->auth = "no";
	$this->failure = $this->phrase[1112]; 
}


elseif ($password == "" )
{

  $this->auth = "no";
	$this->failure = $this->phrase[1114]; 
}
else 
{
	
		//echo "<h1>hello puts $webhost</h1>";	
		
		
 	if (isset($proxy_ip) && $proxy_ip != "")
		{
		$webpath = "http://" . $webhost . $webpath;
		
		//echo $webpath ;
		//exit();
		$output = HTTP_POST($proxy_ip,$proxy_port, $webpath, $vars,$cookie);
		}
	else 
	{
          
            
		$output = HTTP_POST($webhost,'80', $webpath, $vars,$cookie);
	}
    
   
 
   
   
   //check for redirect
	
	//global $CHECKREDIRECT;

	
	//if (isset($CHECKREDIRECT) && $CHECKREDIRECT == "yes")
	if (isset($GLOBALS['CHECKREDIRECT']) && $GLOBALS['CHECKREDIRECT'] == "yes")
	{
		

//echo "hello redirect";



$tok = strtok($output, "\r\n");

while ($tok !== false) {
   // echo "line=$tok<br />";
    
//}



	//$lines = explode("\r\n", $output);
	// for ( $i=0; $i < strlen($lines); $i++){ 
	 	
	 
	 	
	 $chunks = @explode(" ", $tok);
	
	 
	 if ($chunks[0] == "Location:") {
	 	
            
             
	 	$parse = @parse_url($chunks[1]);
	 	//print_r($parse);
	 	$host = $parse["host"];
	 	
	 	if (array_key_exists("port",$parse)) {$port = $parse["port"];} else {$port = 80;}
	 	
	 	
	 	if (isset($proxy_ip) && $proxy_ip != "")
	 		{
	 		$output = GetViaProxy($proxy_ip,$port,$chunks[1]);
	 		
	 		}
	 	else 
	 		{
	   		//echo "path is " . $chunks[1];
                    
                    
                    $opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Accept-language: en\r\n" .
              "$cookie\r\n"
  )
);

$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$output = file_get_contents($chunks[1], false, $context);
                    
                    
                    
                    
                    
                    
	   		//$output = @file_get_contents($chunks[1]);
	   		if ($output  == "") {$output = "Redirect not found.";}
	   	
	   		
	 		}
	 		
	 	
	 break;
	 }
	 $tok = strtok("\r\n");
	 }
	 	
	 }	//end CHECKREDIECT
   
   
   
   
   
   
   
   
    
		if ($output == "")
		{
		$this->auth = "no";
		$this->failure = ""; 	
		}
		elseif ($stringaction == "deny")
		{
		if (!strstr($output,$string)) 
				{
				
				$this->auth = "yes";
				$failure = ""; 
				//echo "<h1>authentication passed</h1>";
				$ip = ip("pc");
				$now = time();
				$sql = "insert into web_auth_log values($now,'$ip')";	
				$DB->query($sql,"AuthenticatePatron.php");
				} 
				else 
				{ 
				$this->auth = "no";
				$this->failure = $this->phrase[1113]; 
				//echo "<h1>authentication failed</h1>";
				}
		}
		elseif ($stringaction == "accept")
		{
			
		if (strstr($output,$string)) 
				{
				
				$this->auth = "yes";
				$this->failure = ""; 
				
				$ip = ip("pc");
				$now = time();
				$sql = "insert into web_auth_log values($now,'$ip')";	
				$DB->query($sql,"AuthenticatePatron.php");
				} 
				else 
				{ 
				$this->auth = "no";
				$this->failure = $this->phrase[1113]; 
				//echo "<h1>authentication failed</h1>";
				}
		}
		
		

}



}



private function local($password,$barcode,$DB)
{
	
	 	$password = md5($password);				
	
		
		$barcode = $DB->escape(str_replace(" ", "", $barcode));

		$sql = "select count(*) as counter from pc_users where barcode = '$barcode' and password = '$password'";
		$DB->query($sql,"classes.php");
		$row = $DB->get();
		$counter = $row["counter"];
		
		
			//$num = $DB->countrows();
			
				if ($counter == 1) 
				{
				
				$this->auth = "yes";
				$this->failure = "";	
				}
				else { 
				
				$this->auth = "no";
				$this->failure = "Invalid password"; }	
}


private function ldap($password,$barcode,$LDAPHOST)
{
	
 	
		$ldapconn = ldap_connect($LDAPHOST);
		
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	
			
		$ldapbind = @ldap_bind($ldapconn, $barcode, $password);
		
			
   		// verify binding
   			if ($ldapbind) 
   				{
   				$this->auth = "yes";
 				
   				}
   			else {
   				$this->auth = "no";
 			$this->failure = "Password credentials not valid.";
   			}
		$close = @ldap_close($ldapconn); 
}

private function email($password,$barcode,$MAILSERVER) {
		
	
		$mbox = imap_open($MAILSERVER, $barcode, $password,1);
echo "barcode is $barcode password is $password";
 		if($mbox) 
 			{
 			$this->auth = "yes";
 		
 			
 			} 
 			else 
 			{
 			
 			$this->auth = "no";
 			$this->failure = "Password  credentials not valid.";
 			}
 		
 		@imap_close($mbox); 
}



}

?>