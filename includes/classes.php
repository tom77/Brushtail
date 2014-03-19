<?php


class SQLITE_DB 
{
	private $result; 
	private $numrows;
	private $row;
	private $db;
	public $type;
	
	function __construct($type)
	{
	$this->type= $type;	
	}
	
	public function connect($file)
	{


	 
  	$this->db = sqlite_open($file, 0666, $sqliteerror);
  
    
    

	
		
	}
	
	public 	function tidy($table)
	{
		
	//do nothing
	$query =  sqlite_exec($this->db, "VACUUM;");
	
	}
	
	public 		function vacuum()
	{
		
	//do nothing
	$query =  sqlite_exec($this->db, "VACUUM;");
	//sqlite_exec($this->db, "VACUUM;", 0, 0);
		
	}
	
        public function close()
        {
            sqlite_close($this->db);
        }
        
        public function exec($sql)
        
	{
		
	//do nothing
	$query =  sqlite_exec($this->db, $sql,$error);
        
        if ($error){
            
            $msg = sqlite_escape_string($error);
					$now = time();
					$errorsql = "insert into errors values (NULL,'$now','EXEC','$msg')";
					@sqlite_query($this->db, $errorsql);
        }
	//sqlite_exec($this->db, "VACUUM;", 0, 0);
		
	}
        
    
  public function escape ($msg)
        {
            
            return sqlite_escape_string($msg);
        }


        
	public function query($sql,$script)
		{
		//echo $sql;	
		
		$result = @sqlite_query($this->db, $sql,SQLITE_BOTH,$error);
		
		 
		
		
	
	if (!$result) {
			//echo "$sql <br>";
					$msg = sqlite_escape_string($error);
					$now = time();
					$errorsql = "insert into errors values (NULL,'$now','$script','$msg')";
					@sqlite_query($this->db, $errorsql);
					}
			else
			{
			$this->result = $result;
			
			}
		}
	
	
	public function countrows()
		{
		$this->numrows = @sqlite_num_rows($this->result);
		return $this->numrows;
		}
		
	public function get()
		{
		$this->row = sqlite_fetch_array($this->result);
	
		return $this->row;
		}
		
	public function last_insert()
	{
		return sqlite_last_insert_rowid($this->db);
	}
	
}




class SQLITE3_DB 
{
	private $result; 
	private $numrows;
	private $row;
	private $db;
	public $type;
	
	function __construct($type)
	{
	$this->type= $type;	
	}
	
	public function connect($file)
	{
        $this->db = new SQLite3($file);
         $this->db->busyTimeout(20000);
	}
	
	public function tidy($table)
	{
	$this->db->exec("VACUUM;");
	}

        
        public function close()
        {
        $this->db->close();    
        }
        
        public function vacuum()
	{
	$this->exec("VACUUM;");
	}
	
        public function exec($sql)
        {
	$this->db->exec($sql);
        }
        
        public function escape($string)
        {
        return $this->db->escapeString($string);
            
        }
        
        public function query($sql,$script)
	{
	$result = @$this->db->query($sql);	
	
	if (!$result) {
            
         
			//echo "$sql <br>";
		$msg = $this->db->lastErrorMsg() . $sql;
                $msg = $this->escape($msg);
		$now = time();
                $errorsql = "insert into errors values (NULL,'$now','$script','$msg')";
		$this->db->query($errorsql);
					}
			else
			{
			$this->result = $result;
			
			}
		}
	
        public function countrows()
		{
                $count = 0;
		
                while ($this->result->fetchArray())
                {
                 $count++;   
                }
                
		return $count;
		}
	
		
	public function get()
		{
		return $this->result->fetchArray();
		}
		
	public function last_insert()
	{
		return $this->db->lastInsertRowID();
	}
	
}


class MYSQL_DB 
{
	private $result; 
	private $numrows;
	private $row;
	private $link;
        public $type;
	
	function __construct($type)
	{
		
	$this->type  = $type;	
	

	}
	
	public function connect($host, $database, $dbuser, $dbpassword)
	{


	 
  
    $link = mysql_connect($host, $dbuser, $dbpassword);
	if (!$link) {
		//header("Location: $url" . "/main/offline.php");
   		
   			echo " Could not connect to database server.";		
   				
   				exit();
				}
	
	$db_selected = mysql_select_db($database, $link);
		if (!$db_selected) 
			{
			//	header("Location: $url" . "/main/offline.php");
  			
 			echo " Could not select database.";		
   				
   				exit();
	
                        }
         $this->link = $link;
	}
	
        
        public function close()
        {
          mysql_close($this->link);  
            
        }
	public function tidy($table)
	{
		
		$sql = "optimize table $table";
		$this->query($sql,"tidy");
		
	}
	
        public function escape ($msg)
        {
            
            return mysql_escape_string($msg);
        }
        
	public function query($sql,$script)
		{
		
		$result = @mysql_query($sql);
		if (!$result) {
			
				
					$msg = $sql . " 
					" . mysql_error();
					$msg = mysql_escape_string($msg);
					$errorsql = "insert into errors values (NULL,UNIX_TIMESTAMP(),'$script','$msg')";
					//echo "<h1>Woops $errorsql</h1>";
					@mysql_query($errorsql);
					}
			else
			{
			$this->result = $result;
			
			}
		}
	
	
	public function countrows()
		{
		$this->numrows = @mysql_num_rows($this->result);
		return $this->numrows;
		}
		
	public function get()
		{
		$this->row = @mysql_fetch_assoc($this->result);
		return $this->row;
		}
		
	public function last_insert()
	{
		return mysql_insert_id();
	}
	
}





class MYSQLI_DB 
{
	private $result; 
	private $numrows;
	private $row;
	private $link;
        public $type;
  
	
	function __construct($type)
	{
		
	$this->type  = "mysql";	
	

	}
	
	public function connect($host, $database, $dbuser, $dbpassword)
	{


	 
    $link = mysqli_connect($host,$dbuser, $dbpassword, $database);
    
	if (!$link) {
		//header("Location: $url" . "/main/offline.php");
   		
   			echo " Could not connect to database server.";		
   				
   				exit();
				}
	
         $this->link = $link;
	}
	
        
       public function close()
        {
          mysqli_close($this->link);  
            
        }
	public function tidy($table)
	{
		
		$sql = "optimize table $table";
		$this->query($sql,"tidy");
		
	}
	
        public function escape ($msg)
        {
            
            return mysqli_escape_string($this->link,$msg);
        }
        
	public function query($sql,$script)
		{
		
                //$this->statement = $this->link->prepare($sql); 
                //$this->statement->execute();
                
               // echo $sql;
		$result = mysqli_query($this->link,$sql);
		if (!$result) {
			
				
					$msg = $sql . " 
					" . mysqli_error($this->link);
					$msg = mysqli_escape_string($this->link,$msg);
					$errorsql = "insert into errors values (NULL,UNIX_TIMESTAMP(),'$script','$msg')";
					//echo "<h1>Woops $errorsql</h1>";
					@mysqli_query($this->link,$errorsql);
					}
			else
			{
			$this->result = $result;
			
			}
                        
                        
		}
	
	
	public function countrows()
		{
		$this->numrows = mysqli_num_rows($this->result);
		return $this->numrows;
		}
		
	public function get()
		{
                
		$this->row  = $this->result->fetch_array(MYSQLI_ASSOC);
		return $this->row;
		}
		
	public function last_insert()
	{
		//return $this->link->insert_id; 
                return mysqli_insert_id($this->link);
	}
	
}








class Validate
{


	
public $error;
public $login;

function __construct($DB,$key,$guest)
{

/*
	$temp = dirname($_SERVER["PHP_SELF"]);
	
	$updir = str_replace("\\", "/",dirname($temp));
	
	if ($updir != "/") {$updir = $updir ."/"; }
	
	$servername  = $_SERVER["SERVER_NAME"];

	$url = "http://" .$servername . $updir;
	
	*/
	
	
	
	
	
	

	
	$curdir = str_replace("\\", "/",dirname(dirname($_SERVER["PHP_SELF"])));
	
	if ($curdir != "/") { $curdir = $curdir . "/";}
	
	$servername  = $_SERVER["SERVER_NAME"];
	$port = $_SERVER["SERVER_PORT"];
	
	if (isset($_SERVER["HTTPS"]))
	{
	$https = $_SERVER["HTTPS"];
	}
	
	
	if ($port != 80) {$port = ":$port";} else {$port = "";}
	
	if (isset($https) && $https == "off") {$url = "https";} else {$url = "http";}
	
	$url .= "://" .$servername .$port. $curdir;


	
//echo "updir is $updir url  is $url";
 
 

 
	//If user has logged in and needs to change password access not allowed to other pages unless password changed.
	if (isset($_SESSION['change']) && $_SESSION['change'] == "True" && basename($_SERVER['PHP_SELF']) <> "change.php")
	{

header("Location: $url" . "index.php");

		exit();	
	}
	


	
	



if (isset($_SESSION['username']) && $_SESSION['username'] == "guest" && $guest == "1")	

{
	//allow guest access
}


elseif (isset($_SESSION['userid']))	
{
//validate seesion

//check that session variables have not changed
$test = md5($_SESSION['userid'].$_SESSION['username'].$key);

if (isset($_SESSION['hash']))
{	$hash = $_SESSION['hash'];} else {$hash = "";}

if ($hash <> $test)
{
	$ERROR = "bad session, hash failed test";
//echo "woops $_SESSION[userid] | $_SESSION[username] | $_SESSION[hash] | $test";
}

$userid = $_SESSION['userid'];



//check that random key on cookie matches the one stored in database
$sql = "select cookieid from user where userid = $_SESSION[userid]";

$DB->query($sql,"classes.php");
$row = $DB->get();
$cookieid = $row["cookieid"];

if (isset($_COOKIE["cookieid"]))
{	$cookie = $_COOKIE["cookieid"];} else {$cookie = "";}
		

if ($cookie <> $cookieid && $guest <> 1)
		{
		
		$ERROR = "no cookie";	
			
		}

}
else 
{
	$ERROR = "bad session"; 
}

	

if (isset($ERROR) && ($ERROR == "bad session" || $ERROR == "no cookie"))
	{
	//echo $ERROR;	
header("Location: $url" . "index.php");

		exit();	
	}
else 
	{
	//ob_end_flush();	
	}

	//check access permissions
	
	//check ip permissions
	
	
}


} 























class Auth
{

	public $result;
	public $userid;
	public $username;
	

	


public function check($DB,$username,$password,$PREFERENCES,$LDAPHOST,$LDAPUPDATE,$LDAPADMINDN,$LDAPADMINPASS,$LDAPDOMAIN,$MAILSERVER)
{

	
	
$this->ldapchange = 0;
	 
$username = trim($username);
$password = trim($password);

//check length of password and username
$ulength = strlen($username);
$plength = strlen($password);



if ($ulength < 100 && $plength < 100 && $plength >= $PREFERENCES['passwordlength'] && $ulength <> 0)
{


	
$sql = "select * from user where username = '$username' and disabled != 1";

$DB->query($sql,"classes.php");

$match = $DB->countrows(); 

$row = $DB->get();	



} else {$match = 0;}





if ($match == 1)
		{
			
		
		
		if 	($row["disabled"] == 1)
		{
		$this->result = "failed";		
		}
		
		elseif (($row["authtype"] == "email") )
		{

				$emailuser = $row["ldapdn"];
		
		
	$mbox = @imap_open($MAILSERVER, $emailuser, $password);

 if($mbox) {$this->result = "passed";} else {$this->result = "failed";}


                  imap_close($mbox); 

			
		}
		elseif (($row["authtype"] == "Active Directory") || ($row["authtype"] == "ldap"))
		{
		
		
   				
		$ldapconn = @ldap_connect($LDAPHOST);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		
		if ($ldapconn) 
			{ 
			
			
				$dn = $row["ldapdn"];
			
			$ldapbind = @ldap_bind($ldapconn, $dn, $password);
		
			
   			// verify binding
   			if ($ldapbind) 
   				{
   				$this->result = "passed";
   			
   				
   				
   				
   				//update stored mysql user password, this is done to check if a password has changed
				$password = md5($password);
				$userid = $row["userid"];
				$sql = "update user set password = '$password' where userid = '$userid'";
				$DB->query($sql,"index.php");
				
				if ($row["expiry"] == 1 && ($PREFERENCES['maxpasswordage'] * 86400 + $row["lastchanged"]) < time())
				{
				//password expired	
				if ($LDAPUPDATE == "yes") {	$this->result = "change";  }
				else {$this->result = "expired"; }
				
				}
				
			elseif ($row["logonchange"] == 1)
				{
				//password set to change on next logon
				if ($LDAPUPDATE == "yes") {	$this->result = "change";  }
				else {$this->result = "expired"; }
					
				}
			
				
				
				
				
				
				
   				}
   			else 
   				{
   				
   				$this->result = "failed";
   				
   				//if password matches password in database and it failed to bind by ldap it means that it may have expired
   				//check expiry date  or "change at next logon
   				if ($row["password"] == md5($password) )
   					{
   					
   					if ($row["authtype"] == "Active Directory")
   					{
   					
   					if ($LDAPUPDATE == "yes")
   						{	
   							
   					
   						//echo "test";	
   							
   							
   							
   				
   					// retrieve user account information	
   					$ldapbind = @ldap_bind($ldapconn, $LDAPADMINDN, $LDAPADMINPASS);		 
					$result = @ldap_search($ldapconn, $dn, "(objectclass=*)");
					 $entry = @ldap_get_entries($ldapconn, $result);	
   					 $pwdlastset = $entry[0]['pwdlastset'][0]; //in windows time
   					 $unixpwdlastset = win2unixtime($pwdlastset); //in unixtime
					 $accountexpiry = $entry[0]['accountexpires'][0]; //in windows time
					 $unixaccountexpiry = win2unixtime($accountexpiry); //in unixtime
					 
					 $accountcontrol = $entry[0]['useraccountcontrol'][0];
				     
				     
				     
				     
				     // retrieve domain password expiry policy
				     $domain = $LDAPDOMAIN;
				     $sr2=@ldap_read($ldapconn, $domain, "(objectclass=*)");
   					 $result = @ldap_get_entries($ldapconn, $sr2);
   					 $maxpasswordage = ($result[0]['maxpwdage'][0]) * 0.0000001;	
				 	 if ($maxpasswordage < 0) {$maxpasswordage = $maxpasswordage * -1;}
				     
				     //$pwdlastset == 0 means change at next logon in active directory
				    //echo "dn is $dn accountcontrol $accountcontrol";
				    
				   
				    
				    if (!$ldapbind)
					{
					//could not connect via ldap as administrator";
						$error = "failed to bind to ldap server as administrator to query user account status. <br>";
   						$error .= "$LDAPHOST $LDAPADMINDN $LDAPADMINPASS";
   						$error .= (ldap_errno($ldapconn) !== 0);
						errorlog($DB,"change.php",$error);
					 $this->result = "failed";
					 
					 }
				        elseif (!$entry)
					{
					 // no user of that LDAP DN found
					 
					 	$error = "No user of this LDAP DN found: $dn";
						errorlog($DB,"change.php",$error);
					 $this->result = "failed";
					 }
				     elseif ($accountcontrol == 2)
				     	{
				     	//user account disabled in active directory 
				     	$this->result = "failed";
				     	}
				     elseif ($accountcontrol == 16)
				     	{
				     	//user account locked out 
				     	$this->result = "failed";
				     	}
				      elseif ($accountcontrol == 64)
				     	{
				     	//user may not change password 
				     	$this->result = "expired";
				     	}
				     elseif ($unixaccountexpiry < time() && $accountexpiry <> 0)
				     	{
				     	//user account expired in active directory
				     	$this->result = "failed";
				     	}
				     
				     elseif ($pwdlastset == 0)
				     	{
				     	//password set to change at next logon in active directory
				     	$this->result = "change";
				     	
				     	}
				    elseif ((($unixpwdlastset + $maxpasswordage) < time())  && $accountcontrol <> 65536)
				    	{
				    	//password expired in active directory
				     	$this->result = "change";
				    	}
				    elseif ($accountcontrol == 8388608) 
				    	 {
				     	//password expired in active directory
				     	$this->result = "change";
				     	 }
				    	 
   						}
   					else
   						{
   						$this->result = "failed";
				    
   						}
   					
   					
   						
   					//end active directory event	
   					}
   					
   					
   					elseif ($row["authtype"] == "ldap")
   					{
   						
   					if ($LDAPUPDATE == "yes")
   						{	
   						$this->result = "change";
   						}
   					else
   						{
   						$this->result = "expiry";
				    
   						}
   					}
   					
   					//end old password check event
   					}
   					
   					
   					//end bind failed event
   					}
   				
   				
   				
   				
   				
   				
   				
   				
   				
   				
			$close = @ldap_close($ldapconn); 
			
			
			
			}
		else 
			{
			
			$this->result = "failed";	
			}
		}
		
			
		
		elseif ($row["authtype"] == "mysql")
		{
			
			
			$username =$DB->escape($username);	
			$password = md5($password);
	
			if ($row["password"] == $password)
			{
				
			
			if ($row["expiry"] == 1 && ($PREFERENCES['maxpasswordage'] * 86400 + $row["lastchanged"]) < time())
				{
				//password expired	
				$this->result = "change";
				}
			elseif ($row["logonchange"] == 1)
				{
				//password set to change on next logon
				$this->result = "change";	
				}
			else 	
				{
				$this->result = "passed";
				}
			
			}
			else 
				{
				$this->result = "failed";	
			
				}
		}
		}
		else 
		{
		$this->result = "failed";	
		}
if (isset($row))	
{	
$this->userid = $row["userid"];
$this->username = $row["username"];
$this->logonchange = $row["logonchange"];
$this->disabled = $row["disabled"];
$this->expiry = $row["expiry"];
$this->authtype = $row["authtype"];
$this->password = $row["password"];


}


}



}



class Access
{	

public $thispage;
public $iprestricted;
public $mainmenu;
public $contentmenu;
public $menulink;
public $contentlink;
public $mainlink;
public $maintype;
public $contenttype;
public $menuparent;
public $ordering;
public $input;

var $contentmanagement;
var $DB;


function __construct($db)
{
	$this->DB = $db;
}

public function check($m)
{
	$this->thispage = 0;
	$this->mainmenu = array();
	$this->contentmenu = array();
	$this->menulink = array();	
	$this->iprestricted = "no";
	
	if ($this->DB->type == "mysql")
	{
	$sql = "select distinct(permid),permissions.m,ip,permlevel, modules.name as modname from permissions, user, group_members, modules where permissions.m = \"$m\" and modules.m = permissions.m 
	and ((permissions.type = \"g\" and permissions.id = group_members.groupid and group_members.userid = $_SESSION[userid])
	or (permissions.type = \"i\" and permissions.id = $_SESSION[userid]))";
	}
	
	else
	{
	$sql = "select distinct(permid),permissions.m,ip,permlevel, modules.name as modname from permissions, user, group_members, modules where permissions.m = '$m' and modules.m = permissions.m 
	and ((permissions.type = 'g' and permissions.id = group_members.groupid and group_members.userid = $_SESSION[userid])
	or (permissions.type = 'i' and permissions.id = $_SESSION[userid]))";
	}
	
	
	$this->DB->query($sql,"classes.php");
	while ($row = $this->DB->get()) 
	
	{
	//check permissions for current page
	
			if ($row["permlevel"] > $this->thispage )
			{
			$this->thispage = $row["permlevel"];
			}
		
	
			
		if ($row["ip"] <> "")
			{
			$this->iprestricted = "yes";
			$addresses = explode(" ", $row["ip"]);
			foreach ( $addresses as $address )
				{
				$length = strlen(trim($address));
				$clientip = ip("pc");
				$client = substr("$clientip",0,$length);
				if ($client == $address) 
					{ $this->iprestricted = "no";}	
				}
				
			
			

			} 	
			
		
		
// end while loop
	}
}

	
public function mmenu()
{
	


	//create main menu array
	//$sql = "select distinct(modules.m), modules.name,modules.type,modules.parent, modules.menupath from permissions, modules, group_members where 
	//(((permissions.type = \"g\" and permissions.id = group_members.groupid and group_members.userid = $_SESSION[userid])
	//or (permissions.type = \"i\" and permissions.id = $_SESSION[userid]))
	//and modules.m = permissions.m  and menupath != \"\" and permlevel > \"0\") or modules.type = 'z' order by modules.position, modules.name";
	

	if ($this->DB->type == "mysql")
	{
	$sql = "(select distinct(modules.m), ordering,modules.name as n,modules.type as type ,modules.parent, modules.menupath, position as p from permissions, modules, group_members where 
	((permissions.type = \"g\" and permissions.id = group_members.groupid and group_members.userid = $_SESSION[userid])
	or (permissions.type = \"i\" and permissions.id = $_SESSION[userid]))
	and modules.m = permissions.m  and mainmenu = '1' and menupath != \"\" and permlevel > \"0\")
	union
	
	(select m, ordering,name AS n,modules.type , parent, menupath, position AS p from modules where modules.type = 'z')
		
	order by p, n";

	}
	
	//echo $sql;
	
	else
	{
	$sql = "select distinct(modules.m) as m, ordering,modules.name as n,modules.type as type,modules.parent as parent, modules.menupath as menupath, position as p from permissions, modules, group_members where 
	((permissions.type = 'g' and permissions.id = group_members.groupid and group_members.userid = $_SESSION[userid])
	or (permissions.type = 'i' and permissions.id = $_SESSION[userid]))
	and modules.m = permissions.m  and mainmenu = '1' and menupath <> '' and permlevel > '0'
	union
	
	select m,ordering, name AS n,modules.type as type, parent, menupath, position AS p from modules where modules.type = 'z'
		
	order by p, n";
	

	}
	
	
	
	$this->DB->query($sql,"classes.php");
	while ($row = $this->DB->get()) 
	{
		$this->mainmenu[$row["m"]] = htmlspecialchars($row["n"]);
		$this->mainlink[$row["m"]] = htmlspecialchars($row["menupath"]);
		$this->maintype[$row["m"]] = $row["type"];
		$this->mainparent[$row["m"]] = $row["parent"];
		$this->ordering[$row["m"]] = $row["ordering"];
		
	}
	


}

public function cmenu()
{
	

	if ($this->DB->type == "mysql")
	{
	//create content menu array
	$sql = "select distinct(modules.m), modules.name,modules.type as type,modules.adminpath from permissions, modules, group_members where 
	((permissions.type = \"g\" and permissions.id = group_members.groupid and group_members.userid = $_SESSION[userid])
	or (permissions.type = \"i\" and permissions.id = $_SESSION[userid]))
	and modules.m = permissions.m  and adminpath != \"\" and permlevel > \"2\" and modules.type != 'z' order by modules.position, modules.name";
	}
	
	else
	{
	//create content menu array
	$sql = "select distinct(modules.m) as m, modules.name as name,modules.type as type, modules.position as p,modules.adminpath as adminpath from permissions, modules, group_members where 
	((permissions.type = 'g' and permissions.id = group_members.groupid and group_members.userid = $_SESSION[userid])
	or (permissions.type = 'i' and permissions.id = $_SESSION[userid]))
	and modules.m = permissions.m  and adminpath <> '' and permlevel > '2' and modules.type <> 'z' order by name, p";

	}
	
	
	$this->contentmanagement = 0;
	$this->DB->query($sql,"classes.php");
	while ($row = $this->DB->get()) 
	{
		
		$this->contentmenu[$row["m"]] = htmlspecialchars($row["name"]);
		$this->contentlink[$row["m"]] = htmlspecialchars($row["adminpath"]);
		$this->contenttype[$row["m"]] = $row["type"];
		$this->contentmanagement++;

	}
    

}


public function listing()
{
  return $this->maintype;  
}
		
	//asort($this->mainmenu);
	//asort($this->contentmenu);
	
}
?>