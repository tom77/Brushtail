<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$integers[] = "m";
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


	$sql = "select * from helpdesk_cat where m = '$m'";
	$DB->query($sql,"edithelpdesk.php");

	$category = array();
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
		) group by user.userid";
		
      
    //  echo $sql;
    
		$DB->query($sql,"helpdeskadd.php");
		
		$last_names = array();
		while ($row = $DB->get())
      {	
      	$userid =  $row["userid"];
      	$first_names[$userid] =  $row["first_name"];
      	$last_names[$userid] =  $row["last_name"];
      	
      }
      

//if (isset($keywords)) { $keywords = remove($keywords);}







	
if (isset($keywords))
	{
	
	$sqlkeywords = $DB->escape($keywords);
	
	


	if ($DB->type == "mysql")
			{
				$sqlinsert = "MATCH (query,solution) AGAINST (\"$sqlkeywords\" IN BOOLEAN MODE)";
	$sql = "select *, UNIX_TIMESTAMP(datereported) AS datereported from helpdesk where m = \"$m\" and $sqlinsert order by probnum";
			}
			
	else
			{
				$keywords = trim($keywords);
       			
       			$words = explode(" ",$keywords);
       			
       			$string = "";
       			
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
				$string .= " and (query like '%$value%' or solution like '%$value%') ";	
				$counter++;
				}	
				if ($keywords == "") {$string = " and 1 == 2";}
				
	$sql = "select *, strftime('%s',datereported) AS datereported from helpdesk where m = \"$m\" $string order by probnum";
			}
				
			
	}
else
	{
		//print_r($_REQUEST);
		
		
	if(isset($_REQUEST["event"]) && $_REQUEST["event"] == "comp")
	{
	
		$banner = $phrase[221];
		$status = 3;
		$event="comp";
		$statusinsert = " and status = '3'";
		//$javascriptinsert = "";
	}	
		
		

	elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "open")
	{
	
		$event = "open";
	$banner = $phrase[400];
	$status = 2;
	$statusinsert = " and status = '2' ";
	//$javascriptinsert = "+ '&event=open'";
	}
	else 
	{

	$event = "new";
	$banner = $phrase[220];
	$statusinsert = " and status = '1' ";
	$status = 1;
	//$javascriptinsert = "+ '&event=new'";
	}
	
		
		
	if(isset($_REQUEST["assigned"]))
	{
	$assigned = $_REQUEST["assigned"];
	if ($_REQUEST["assigned"] == "") {$assigninsert = "";}
	else{
		
		$assigninsert = " and assigned = '" . $DB->escape($_REQUEST["assigned"]) . "'";
		}	
	}
	else {$assigninsert = "";}
	
	
	if(isset($_REQUEST["cat"]))
	{
	$cat = $_REQUEST["cat"];
	if ($_REQUEST["cat"] == "") {$catinsert = "";}
	else{
		
		$catinsert = " and cat = '" . $DB->escape($_REQUEST["cat"]) . "'";
		}	
	}
	else {$catinsert = "";}
	
	
	
	
	//print_r($_REQUEST);
		
		if (isset($cat) && $cat != 0) {$insert = " and cat = '$cat' ";} else {$insert = "";}	
	if ($DB->type == "mysql")
			{	
	$sql = "select *, UNIX_TIMESTAMP(datereported) AS datereported from helpdesk where m = \"$m\" $catinsert $assigninsert $statusinsert order by probnum";
			}
			
		else
			{	
	$sql = "select *, strftime('%s',datereported) AS datereported from helpdesk where m = \"$m\" $catinsert $assigninsert $statusinsert order by probnum";
			}		
			
			
	
	}


	
	//echo $sql;
	











	
		
	

$DB->query($sql,"helpdeskall.php");


$out = fopen('php://output', 'w');


header("content-type: text/csv \n");
			
header("Content-Transfer-Encoding: binary\n"); 
			
			//header("content-length: $size \n");
			
header("content-disposition: attachment; filename=\"helpdesk.csv\" \n");



echo "$phrase[197],$phrase[217],$phrase[205],$phrase[206],$phrase[1059], $phrase[401],$phrase[890],$phrase[279]
";


while ($row = $DB->get()) 
	{
	$line[0] = $row["probnum"];
	//$pcnum = $row["pcnum"];
	//$branch = formattext($row["branch"]);
	$line[1] = strftime("%x",$row["datereported"]);
	$query = $row["query"];
	$line[2] = str_replace ( chr(10), chr(13) . chr(10), $query ); 
	$line[3] = $row["solution"] ;
	
	$assignedto = $row["assigned"];
	
		if ($assignedto != ""  && array_key_exists($assignedto,$last_names) && $assignment == 1)
		{
		
		$line[4] =	$last_names[$assignedto] . ", " .$first_names[$assignedto];	
		
		} else {$line[4] = "";}
		
	
	
	$status = $row["status"];
	$statusArray[1] = $phrase[220]; 
	$statusArray[2] = $phrase[400]; 
	$statusArray[3] = $phrase[221];
	
	$line[5] = $statusArray[$status];
	
	
	$jobtime = $row["jobtime"];
	$display = secondsToMinutes($jobtime);
	
	if ( $showclock == 1)
		{
		$line[6] = $display;
		} else { $line[6] = "";}
		
		
	$cat = $row["cat"];
	
	if (array_key_exists($cat,$category))
	{$line[7] = $category[$cat];} else {$line[7] = "";}
	
	
	fputcsv($out, $line,',','"');
	
	
	}
	
	fclose($out);
	
	}
	
?>


	

