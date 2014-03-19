<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);







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
	



if (isset($_REQUEST["bookingno"])) 
	{
	if (!(isinteger($_REQUEST["bookingno"]))) 
	{$ERROR  = $phrase[72];}
	else {$bookingno = $_REQUEST["bookingno"];}
	}
$imagecounter = 0;
$doccounter = 0;

if(!isset($ERROR))
        
{
 if (isset($_REQUEST["migrate"]))   
{
    echo "<h1>Migrating files to database storage</h1>";
    
    $sql = "select image_id,page, filename from images where content_id = '0'";
    $DB->query($sql,"migrate.php");
	$row = $DB->get(); 
	
	$image_id = $row["image_id"];
	$page = $row["page"];
	$filename = $row["filename"];
    
        $path = $PREFERENCES["docdir"] ."/calendars/" . $page . "/$filename";
        
        $data = file_get_contents($path);
        
        if ($data)
        {
            $data = base64_encode($data);
            $data = $DB->escape($data);
        $sql2 = "update images set data = '$data' where image_id = '$image_id'";
        $DB->query($sql2,"migrate.php");
        $imagecounter++;
        }
        
        
          $sql = "select m,image_id,content.page_id as page, filename from images, content,page where images.content_id = content.content_id and content.page_id = page.page_id";
    $DB->query($sql,"migrate.php");
	$row = $DB->get(); 
	
	$image_id = $row["image_id"];
	$page = $row["page"];
	$filename = $row["filename"];
        $m = $row["m"];
    
        $path = $PREFERENCES["docdir"] ."/$m/$page/$filename";
        
        $data = file_get_contents($path);
        
        if ($data)
        {
            $data = base64_encode($data);
            $data = $DB->escape($data);
        $sql2 = "update images set data = '$data' where image_id = '$image_id'";
        $DB->query($sql2,"migrate.php");
        $imagecounter++;
        }
        
        
          $sql = "select m,doc_id,page documents ";
    $DB->query($sql,"migrate.php");
	$row = $DB->get(); 
	
	$doc_id = $row["doc_id"];
	$page = $row["page"];
	$filename = $row["filename"];
        $m = $row["m"];
    
        $path = $PREFERENCES["docdir"] ."/$m/$page/$filename";
        
        $data = file_get_contents($path);
        
        if ($data)
        {
            $data = base64_encode($data);
            $data = $DB->escape($data);
        $sql2 = "update documents set data = '$data' where doc_id = '$doc_id'";
        $DB->query($sql2,"migrate.php");
        $doccounter++;
        }
        
        echo "
Images migrated: $imagecounter $files<br>            
Documents migrated: $doccounter files.
";
        
}
else
{
    echo "<h1>Migrate files to database</h1>
        
<a href=\"migratefiles.php?migrate=yes\">Start</a>

";
    
}
        
}
	
?>