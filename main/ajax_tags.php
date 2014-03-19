<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);





$ip = ip("pc");






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
	$DB->query($sql,"ajax_tags.php");
	$row = $DB->get();
	$tags = $row["tags"];

        echo $tags;
	
	}
        
 if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "updatetags")
	{
     
        $sql = "delete from  tags where m = '$m'";	
	$DB->query($sql,"ajax_tags.php");
        
        $tags = $_REQUEST["tags"];
      
         $tag = array();
                                $tag = explode(" ", $tags);
                                foreach ($tag as $value)
                                {
                                    $value = trim($value);
                                    if ($value != "")
                                    {
                                   echo " <span style=\"line-height:2.5em;margin:0.5em;padding: 0.3em 0.8em;\" class=\"accent\" onclick=\"addtag('$value','tags')\">$value</span> "; 
                                    }
                                }
	$tags = $DB->escape($tags);
	$sql = "insert into tags values ('$m','$tags')";	
	//echo "$sql";
	$DB->query($sql,"ajax_tags.php");
	
	
	}
        }
	
?>