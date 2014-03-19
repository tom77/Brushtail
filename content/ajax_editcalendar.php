<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


$integers[] = "m";


$ip = ip("pc");



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}




if ((isinteger($m)))
{
	
	$access->check($m);
	
	if ($access->thispage < 3)
		{
		
		$ERROR  =  "$phrase[1]";
	
		}
	elseif ($access->iprestricted == "yes")
		{
	$ERROR  =  $phrase[0];
		}
	
	
}	

if (isset($ERROR))
	{
	echo "$ERROR";
	}
else 
	{
		

if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "gettags")
	{
	
	$sql = "select tags from tags where  m = '$m'";	
	//echo "$sql";
	$DB->query($sql,"ajax_editcalendar.php");
	$row = $DB->get();
	$tags = $row["tags"];

        echo $tags;
	
	}
        
 if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "updatetags")
	{
        $tags = $_REQUEST["tags"];
      
         $tag = array();
                                $tag = explode(" ", $tags);
                                foreach ($tag as $value)
                                {
                                   echo " <span style=\"line-height:2.5em;margin:0.5em;padding: 0.3em 0.8em;\" class=\"accent\" onclick=\"addtag('$value','tags')\">$value</span> "; 
                                }
	$tags = $DB->escape($tags);
	$sql = "update tags set tags = '$tags' where  m = '$m'";	
	//echo "$sql";
	$DB->query($sql,"ajax_editcalendar.php");
	
	
	}
        }
	
?>