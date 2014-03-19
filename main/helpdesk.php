<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$integers[] = "id";
$integers[] = "probnum";
$integers[] = "status";
$integers[] = "cat";
$integers[] = "year";
$integers[] = "month";
$integers[] = "category";

$datepicker = "yes";








foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (isset($_REQUEST["keywords"]))
{ 
	if ($_REQUEST["keywords"] == "")
	{ $WARNING = $phrase[219];} else {
	$keywords = $_REQUEST["keywords"];}
}



	
$ip = ip("pc");
$proxy = ip("proxy");

	
	 if (isset($ERROR))
	{
   	header("Location: $url" . "error.php?error=input");
	exit();
	}
        
  elseif (!isset($m))
    {
	header("Location: $url" . "error.php?error=module");
	exit();
    }



	
	$access->check($m);
	
	if ($access->thispage < 1)
		{
		
		
		header("Location: $url" . "error.php?error=permissions");
		exit();
		}
	elseif ($access->iprestricted == "yes")
		{
		header("Location: $url" . "error.php?error=ipaccess");
		exit();
		}

	

if (!isset($ERROR)) 
	{
	$sql = "select name from modules where m = '$m'";
	$DB->query($sql,"helpdesk.php");
	$row = $DB->get();
	$modname = formattext($row["name"]);	
		
	page_view($DB,$PREFERENCES,$m,"");	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");



		
		
		
			$callcounter = 0;
$search = 0;
 $email = "";
	$sql  = "select * from helpdesk_options where m = '$m'";	
	$DB->query($sql,"helpdesk.php");
	
	$row = $DB->get();
	$callcounter = $row["callcounter"];
	$email= $row["email"];	
	$email_action= $row["email_action"];
	$search = $row["search"];
	$delbutton = $row["delbutton"];
	$showclock = $row["showclock"];
	$texttolink = $row["texttolink"];	
	$assignment = $row["assignment"];	
	
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                {
    $userid = $DB->escape($_SESSION["userid"]);
        
        $sql  = "select email from user where userid = '$userid'";
        
	$DB->query($sql,"helpdesk.php");
	
	$row = $DB->get();

	$email= $row["email"];
     
                }
        
        
          if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                {
              
            $email = $EMAIL_FROM;  
          }
		
		
		
		$sql = "select name, ordering, type from modules where m = '$m'";
		$DB->query($sql,"helpdesk.php");
		$row = $DB->get();
		$modname = formattext($row["name"]);
		$moduletype = $row["type"];		
		
		
		
			if ($access->thispage == 1)
		{
		
			if ($search ==1 || $search ==3)
			{
			$formpage = "helpdesk.php";
		
			
			
				
			 
	  		include("../includes/leftsidebar.php");   
		 	echo "<div id=\"content\"><div>";
			include ("form.php");
			echo "</div></div>";
			include("../includes/rightsidebar.php"); 
			
			include ("../includes/footer.php");  
			}
			else 
			{
				$include = "yes";
				
			include("../includes/leftsidebar.php");   
		 	echo "<div id=\"content\"><div>";	
			include ("helpdesksearchinclude.php");
			echo "</div></div>";
			include("../includes/rightsidebar.php"); 
			
			include ("../includes/footer.php");	
			}
				
			
		}
		
		elseif  ($access->thispage > 1){
		
	
echo "<div style=\"text-align:center\" id=\"helpdesk\">";
	
	
function helpdesk_message($DB,$phrase,$to,$probnum,$event,$from,$modname,$preamble)
{
	
	
	
	 if ($DB->type == "mysql")
			{		
	$sql = "select *, UNIX_TIMESTAMP(datereported) AS datereported, UNIX_TIMESTAMP(datefixed) AS datefixed from helpdesk where probnum=\"$probnum\" ";
			}
	else
			{		
	$sql = "select *, strftime('%s',datereported) AS datereported, strftime('%s',datefixed) AS datefixed from helpdesk where probnum=\"$probnum\" ";
			}		

			//echo $sql;

	$DB->query($sql,"helpdesk.php");
	
	$row = $DB->get();
	$probnum = $row["probnum"];
	
	//$branch = formattext($row["branch"]);
	$datereported = strftime("%x",$row["datereported"]) . " " . date("g:i a",$row["datereported"]);
	$datefixed = strftime("%x",$row["datefixed"]) ;
	//$name = formattext($row["name"]);

	
	
	$query = $row["query"];

	$solution = $row["solution"];
	
	
	
$message = "$preamble
	
$query
	
$solution";


	
	

if ($from == "") {
 $WARNING = $phrase[697];}


	$headers = "From: $from";
	$subject = $modname;
	if ($event == "assign") {$subject .=  " - $phrase[1059]";}
	if ($event == "new") {$subject .=  " - $phrase[872]";}
	if ($event == "send") {$subject .=  " - $phrase[206]";}

	
	
	
	//ini_set('sendmail_from', $from);

	send_email($DB,$to, $subject, $message,$headers);
	
	//echo "from is $from";
	
}
			 
	
			
		 if (isset($_REQUEST["send"]) && $email_action > 1 )
	 	{
			$comment  = $_REQUEST["comment"];
	 		helpdesk_message($DB,$phrase,$_REQUEST["to"],$probnum,"send",$email,$modname,$comment);
		
	 	}	
		
	
                
 if (((isset($_REQUEST["add"])) || (isset($_REQUEST["complete"]))) )
		{
			
			
			
			
				if ($_REQUEST["query"] == "")
				
				{ 
				$WARNING=  "$phrase[200]";
				
				}
				else
				{
			
					$query = $DB->escape($_REQUEST["query"]);
					$solution = $DB->escape($_REQUEST["solution"]);
                                        if (isset($_REQUEST["assignedtouser"]))
					{$assignedtouser = $DB->escape($_REQUEST["assignedtouser"]);} else {$assignedtouser = 0;}
				
					$requestedby = $_SESSION["userid"];
				
					
						
					if (isset($_REQUEST["add"]))
						{
					$datereported = date("Y-m-d H:i:s");
					$datefixed = date("Y-m-d H:i:s");
					
					if ($status == 3)
						{
			    		$sql = "INSERT INTO helpdesk (probnum,m,  query, solution, datefixed, datereported,ip,proxy,username,status,cat,jobtime,requestedby,assigned)  
		    			VALUES(NULL,'$m','$query', '$solution','$datefixed','$datereported','$ip','$proxy','$_SESSION[username]','$status','$category','0','$requestedby','$assignedtouser')"; 
						$DB->query($sql,"helpdeskadd.php");
						}
						else {
							$sql = "INSERT INTO helpdesk (probnum,m,query, solution, datefixed, datereported,ip,proxy,username,status,cat,jobtime,requestedby,assigned)  
		    			VALUES(NULL,'$m','$query', '$solution',NULL,'$datereported','$ip','$proxy','$_SESSION[username]','$status','$category','0','$requestedby','$assignedtouser')"; 
						$DB->query($sql,"helpdeskadd.php");
					
						
						}
						
						//echo $sql ;
						//$probnum = mysql_insert_id();
                                                $probnum = $DB->last_insert();
					
                                                echo "";
							
							if ($assignment == 1)
	
								{
                                                            
                                                             $sql = "select email from user where userid = '$assignedtouser'";	
                                                                    $DB->query($sql,"helpdeskadd.php");
                                                                                $row = $DB->get();
                                                                     $useremail =  $row["email"];	
    
                                                            $pos = strpos($useremail, "@");
    	
                                                                        if ($pos === false) {} else {
							$preamble = "$phrase[872]";
                                                    //    echo "$email_action helpdesk_message($useremail,$probnum,new,$EMAIL_FROM,$modname,$preamble";
										helpdesk_message($DB,$phrase,$useremail,$probnum,"new",$email,$modname,$preamble);
                                                                        }
									
								}
						
						
						
						
						
						}
					
						
		    			
				
				//end else
				}
		}
		
		
		 
	  if(isset($_REQUEST["updatejob"]) || isset($_REQUEST["sendjob"])) 
 	{
	$sql = "select assigned from helpdesk where probnum = '$probnum'";
	$DB->query($sql,"helpdeskadd.php");
	$row = $DB->get();
    $oldassigned =  $row["assigned"];
 		
 	$query = trim($_REQUEST["query"]);
	$solution = trim($_REQUEST["solution"]);
        
        if ($assignment == 1)
        {
	$newassigned = $DB->escape($_REQUEST["assignedtouser"]);
    
    if ($newassigned != $oldassigned)
    {
    $sql = "select email from user where userid = '$newassigned'";	
    $DB->query($sql,"helpdeskadd.php");
	$row = $DB->get();
    $useremail =  $row["email"];	
    
    $pos = strpos($useremail, "@");
    	
    if ($pos === false) {} else {
    
    		$preamble = $phrase[1059];
    		helpdesk_message($DB,$phrase,$useremail,$probnum,"assign",$email,$modname,$preamble);
    		
    }    	
    }
        }
	
	$query = $DB->escape($query);
	$solution = $DB->escape($solution);
	     if (isset($_REQUEST["assignedtouser"]))
					{$newassigned = $DB->escape($_REQUEST["assignedtouser"]);} else {$newassigned = 0;}
	

	$datefixed = date("Y-m-d");
	
	if ($status == 3)
	{
	$sql = "update helpdesk set query = \"$query\",status = \"$status\", datefixed = \"$datefixed\", solution = \"$solution\",  ip = \"$ip\", proxy = \"$proxy\", username = \"$_SESSION[username]\", cat = \"$category\", assigned = \"$newassigned\" where probnum = \"$probnum\" ";
$DB->query($sql,"helpdesk.php");

	}
	else {
		$sql = "update helpdesk set query = \"$query\",status = \"$status\", solution = \"$solution\",  ip = \"$ip\", proxy = \"$proxy\", username = \"$_SESSION[username]\", cat = \"$category\",assigned = \"$newassigned\" where probnum = \"$probnum\" ";
$DB->query($sql,"helpdesk.php");
		
}
		

	}
	
	if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete" ) 
	{
	$sql = "delete from helpdesk where probnum = \"$probnum\" ";
	$DB->query($sql,"helpdesk.php");
	
	}

	if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deleteall" ) 
	{
	$sql = "delete from helpdesk where m = \"$m\" and status = '$status'";
	$DB->query($sql,"helpdesk.php");
	
	}


		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addtemplate" ) 
	{
	$template = $DB->escape(trim($_REQUEST["template"]));
	$template_name = $DB->escape(trim($_REQUEST["template_name"]));	
		
	$sql = "insert into helpdesk_templates values(NULL,'$template_name','$m','$template')";
	//echo $sql;
	$DB->query($sql,"helpdesk.php");
	
	}

	
		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updatetemplate" ) 
	{
	$template = $DB->escape(trim($_REQUEST["template"]));
	$template_name = $DB->escape(trim($_REQUEST["template_name"]));	
		
	$sql = "update helpdesk_templates set template_name = '$template_name', template = '$template' where id = '$id' and m = '$m'";
	//echo $sql;
	$DB->query($sql,"helpdesk.php");
	
	}

			if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletetemplate" ) 
	{


		
	$sql = "delete from  helpdesk_templates where id = '$id' and m = '$m'";
	//echo $sql;
	$DB->query($sql,"helpdesk.php");
	
	}
		
		//check module details
		
		


		


print <<<EOF
 
<br>

 	
	  
	  <ul><li><a href="helpdesk.php?m=$m"><img src="../images/mail_next.png" alt="$phrase[220]" title="$phrase[220]"></a></li><li><a href="helpdesk.php?event=open&amp;m=$m"><img src="../images/mail_edit.png" alt="$phrase[400]" title="$phrase[400]"></a></li><li><a href="helpdesk.php?m=$m&amp;event=comp"><img src="../images/mail_accept.png" alt="$phrase[221]" title="$phrase[221]"></a>
	</li>

<li><a href="helpdesk.php?m=$m&amp;event=add"><img src="../images/mail_add.png" alt="$phrase[176]" title="$phrase[176]"></a> </li> 

EOF;



if ($callcounter == 1)
{
echo " <li> <a href=\"\" onclick=\"updatePage('ajax.php?event=callcounter&amp;m=$m','calls');return false\" style=\"outline:none\"><img src=\"../images/calculator_add.png\" alt=\"$phrase[403]\" title=\"$phrase[403]\"></a> <span id=\"calls\">&#40;";


$_month = date("m");
$_year = date("Y");
			if ($DB->type == "mysql")
		{
			
$sql = "SELECT count( * ) as total  FROM itcalls where month( day ) = \"$_month\" AND year( day ) = \"$_year\" and  m = \"$m\"";
}

			else
		{
			
$sql = "SELECT count( * ) as total  FROM itcalls where strftime('%m', day ) = \"$_month\" AND strftime('%Y', day ) = \"$_year\" and  m = \"$m\"";
}

$DB->query($sql,"helpdeskstats.php");
$row = $DB->get();
$counter = $row["total"];	
echo "$counter";

echo "&#41;</span></li>";
}



echo "<li><a href=\"helpdesk.php?m=$m&amp;event=stats\"><img src=\"../images/calculator.png\" alt=\"$phrase[202]\" title=\"$phrase[202]\"></a> </li>";
if ($showclock == 1)
{
echo "<li><a href=\"helpdesk.php?m=$m&amp;event=timestats\"><img src=\"../images/clock.png\" alt=\"$phrase[903]\" title=\"$phrase[903]\"></a> </li>";	

}

echo "<li><a href=\"helpdesk.php?m=$m&amp;event=templates\"><img src=\"../images/notes_edit.png\" alt=\"$phrase[316]\" title=\"$phrase[316]\"></a> </li>";


if (!isset($_REQUEST["event"]) )	{$status = 1;}	
if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "new")	{$status = 1;}
if (isset($_REQUEST["event"]) &&$_REQUEST["event"] == "open")	{$status = 2;}
if (isset($_REQUEST["event"]) &&$_REQUEST["event"] == "comp")	{$status = 3;}

if ( (!isset($_REQUEST["event"]) || (($_REQUEST["event"] != "add" && $_REQUEST["event"] != "templates" && $_REQUEST["event"] != "addtemplate" && $_REQUEST["event"] != "edittemplate" && $_REQUEST["event"] != "stats" && $_REQUEST["event"] != "edit" && $_REQUEST["event"] != "delete")  )))
{
	
echo "<a href=\"javascript:pop_window('helpviewcurrent.php?m=$m&amp&amp;status=$status')\"><img src=\"../images/printers.png\" alt=\"$phrase[666]\" title=\"$phrase[666]\"></a>

<a href=\"helpdesk.php?m=$m&amp;event=deleteall&amp;status=$status\"><img src=\"../images/crosses.png\" alt=\"$phrase[665]\" title=\"$phrase[665]\"></a>  <br><br>
";
}




echo "</ul>";


print <<<EOF
<form action="helpdesk.php" method="get"><p><input type="hidden" name="m" value="$m"> 

<input type="text" size="25" name="keywords"><input type="submit" name="refsearch" value="$phrase[201]"></p> </form><br><br>
<h1>$modname</h1>
EOF;

if (isset($WARNING))
{
echo $WARNING;	
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delete")
	{
	
	//if(isset($event)) {$insert = "&amp;event=$event";} else { $insert = "";}
	echo "<br>
<br>
<b>$phrase[14]</b><br><br>

	<a href=\"helpdesk.php?m=$m&amp;update=delete&amp;probnum=$probnum\">$phrase[12]</a> | <a href=\"helpdesk.php?m=$m\">$phrase[13]</a>";
	
	} 
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteall")
	{
	
	//if(isset($event)) {$insert = "&amp;event=$event";} else { $insert = "";}
	echo "<br>
<br>
<b>$phrase[14]</b><br><br>";
if  ($status == 1)
{ echo "$phrase[667]";}
if  ($status == 2)
{ echo "$phrase[668]";}
if  ($status == 3)
{ echo "$phrase[669]";}
echo "<br><br>
	<a href=\"helpdesk.php?m=$m&amp;update=deleteall&amp;status=$status\">$phrase[12]</a> | <a href=\"helpdesk.php?m=$m\">$phrase[13]</a>";
	
	}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "filter")
		{	
	
			
			$sql = "select * from helpdesk_cat where m = '$m'";
	$DB->query($sql,"edithelpdesk.php");

while ($row = $DB->get())
      {	
     
      	$id =  $row["id"];
      
		$category[$id] = $row["cat_name"];
      }


      
      // get list of user with edit permissions
      $sql = "select distinct(user.userid) as userid, first_name, last_name from permissions, user, group_members where permlevel > '1' and m = '$m'  
		and (
		(type = 'i' and permissions.id = user.userid)
		or 
		(type= 'g' and permissions.id = group_members.groupid and group_members.userid = user.userid)
		) group by userid";
		
		$DB->query($sql,"helpdeskadd.php");
		
		$last_names = array();
		while ($row = $DB->get())
      {	
      	$userid =  $row["userid"];
      	$first_names[$userid] =  $row["first_name"];
      	$last_names[$userid] =  $row["last_name"];
      	
      }	
			
      
      echo "";
	
	
		}
 
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit")
		{
	include  'helpdeskedit.php';
	}
	
elseif ((isset($_REQUEST["event"]) && $_REQUEST["event"] == "all") || isset($_REQUEST["keywords"]) )
	 	{
		include  'helpdeskall.php';
		
		}
elseif (isset($_REQUEST["sendjob"]))
	{
	include  'helpdesksend.php';
	}
	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "stats")
	{
	include  'helpdeskstats.php';
	}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "timestats")
	{
	include  'helpdesktimes.php';
	}

elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "add")
	{
	include  'helpdeskadd.php';
	}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edittemplate")
	{
		
		$sql = "select template, template_name from helpdesk_templates where id = '$id' and m = $m";
	//echo $sql;
	$DB->query($sql,"helpdesk.php");
	;$row = $DB->get();
		
		
		$template_name = $row["template_name"];
		$template = $row["template"];	
		
		
	echo "<h2>$phrase[316]</h2>
	<form action=\"helpdesk.php\" style=\"text-align:left;margin-left:35%\">
	
	<b>$phrase[141]</b><br>
	<input type=\"text\" name=\"template_name\" size=\"50\" value=\"$template_name\"><br><br>
	<b>$phrase[791]</b><br>
	<textarea name=\"template\" cols=\"50\" rows=\"15\">$template</textarea><br><br>
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"event\" value=\"templates\">
	<input type=\"hidden\" name=\"id\" value=\"$id\">
	<input type=\"hidden\" name=\"update\" value=\"updatetemplate\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[304]\">
	</form>
	
	
	
	";
	}		
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addtemplate")
	{
	echo "<h2>$phrase[316]</h2>
	<form action=\"helpdesk.php\" style=\"text-align:left;margin-left:35%\">
	
	<b>$phrase[141]</b><br>
	<input type=\"text\" name=\"template_name\" size=\"50\"><br><br>
	<b>$phrase[791]</b><br>
	<textarea name=\"template\" cols=\"50\" rows=\"15\"></textarea><br><br>
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"event\" value=\"templates\">

	<input type=\"hidden\" name=\"update\" value=\"addtemplate\">
	<input type=\"submit\" name=\"submit\" value=\"$phrase[303]\">
	</form>
	
	
	
	";
	}	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "templates")
	{
	//echo "hello templates";
	
	echo "<h2>$phrase[316]</h2>
	<a href=\"helpdesk.php?m=$m&amp;event=addtemplate\"><img src=\"../images/add.png\" alt=\"$phrase[303]\" title=\"$phrase[303]\"></a>
<table style=\"margin-left:45%;margin-top:2em;\" class=\"colourtable\">";
	$sql = "select id,template, template_name from helpdesk_templates where m = '$m'";
	//echo $sql;
	$DB->query($sql,"helpdesk.php");
	while ($row = $DB->get())
		{
		$id = $row["id"];
		$template_name = $row["template_name"];
		$template = $row["template"];
		echo "<tr><td>$template_name</td>
		<td><a href=\"helpdesk.php?m=$m&amp;event=edittemplate&amp;id=$id\"><img src=\"../images/pencil.png\" alt=\"$phrase[26]\" title=\"$phrase[26]\"></a></td>
		<td><a href=\"helpdesk.php?m=$m&amp;event=templates&amp;update=deletetemplate&amp;id=$id\"><img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\"></a></td></tr>";
		}
	echo "</table>";
	}
	

else
		{
		include  'helpdeskall.php';
		}
	 
	 
		
			echo "<script type=\"text/javascript\" src=\"helpdesk.js\"></script></div>";

include ("../includes/footer.php");
		
	}
	
		}
		
		
		//print_r($_REQUEST);
	
		
		


?>

