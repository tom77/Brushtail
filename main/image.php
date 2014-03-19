<?php
//error_reporting(E_ALL);
include ("../includes/initiliaze_page.php");

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
   header('HTTP/1.1 304 Not Modified');
   die();
}
else
{
header('Cache-control: max-age='.(60*60*24*365));
header("Pragma: public");
//echo "The cache limiter is now set to $cache_limiter<br />";
}

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


if (!(isset($_GET['image_id'])))
{
$ERROR  = "Image document id.";	
}
else {
		if (!(isinteger($_GET["image_id"])))
		{
		$ERROR  = "Image id is not an integer";
		}
		else 
		{
		$image_id = $_GET["image_id"];
		}
}


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
		echo $ERROR;
	}								

	
	else {

        if (isset($_REQUEST["module"]) && $_REQUEST["module"] == "tv")
	{
	$sql = "select images.name as name, images.type as type, images.size as size,images.filename as filename, images.page as page,data from images where image_id = \"$image_id\" 
" ;	
	}    
            

	elseif (isset($_REQUEST["module"]) && $_REQUEST["module"] == "cal")
	{
	$sql = "select images.name as name, images.type as type, images.size as size,images.filename as filename, images.page as page,data from images,cal_bridge,cal_events,modules where image_id = \"$image_id\" 
	and images.page = cal_events.event_id and cal_bridge.cat = cal_events.event_catid and cal_bridge.m = modules.m and modules.m = \"$m\"" ;	
	}
	else 
	{
	$sql = "select images.name as name, images.type as type, images.size as size,images.filename as filename, images.page as page,data from images,page,modules where image_id = \"$image_id\" 
	and images.page = page.page_id and page.m = modules.m and modules.m = \"$m\"" ;
	}
	//$sql = "select images.name as name, images.type as type, images.size as size,images.filename as filename, images.page as page from images where image_id = \"$image_id\" " ;
	
	
	//echo $sql;	


	$DB->query($sql,"image.php");
			$row = $DB->get();
			
			
			$image_name = $row["name"];
			$filename = $row["filename"];
			$type = $row["type"];
			$size = $row["size"];
			$page = $row["page"];
                       
                        
                        header("content-disposition: inline; filename=\"$image_name\" \n");
			
                        if (isset($_REQUEST["module"]) && $_REQUEST["module"] == "tv")
			{
			$filepath = $PREFERENCES['docdir']."/tv/".$page."/".$filename;	
			}
			elseif (isset($_REQUEST["module"]) && $_REQUEST["module"] == "cal")
			{
			$filepath = $PREFERENCES['docdir']."/calendars/".$page."/".$filename;	
			}
			else 
			{
			$filepath = $PREFERENCES['docdir']."/".$m."/".$page."/".$filename;	
			}
			
			
			//echo $filepath;
			
			//$size = filesize($filepath);
	
			
			if (isset($ERROR))
			{
				echo  $ERROR;
                                exit();
			}
			
			
                      if ($PREFERENCES["storage"] == "file")
                      {
                        $data = file_get_contents($filepath);
                      }
                        if ($PREFERENCES["storage"] == "database")
                      {
                         $data = base64_decode($row["data"]);
                      }
                        
                    
                      
                     if (function_exists('imagecreatefromstring'))
                         
                     {    
                     $image = imagecreatefromstring($data);
                    $current_width = imagesx($image);
                    $current_height = imagesy($image);
                    
 //echo "$current_width $current_height";
 
// exit();
                     if ($current_width > $PREFERENCES["imagesize"]) 
                        {
                         $resize = "yes";
                         $new_height = $current_height / $current_width * $PREFERENCES["imagesize"];
                        
                            $new = imagecreatetruecolor($PREFERENCES["imagesize"],$new_height);

                            imagecopyresampled($new,$image,0,0,0,0,$PREFERENCES["imagesize"],$new_height,imagesx($image),imagesy($image));

                     
                        header("Content-Type: $type");


                        if ($type == "image/gif")
                        {
                         imagegif($new);   
                        }
                         if ($type == "image/jpeg" || $type == "image/pjpeg")
                        {
                         imagejpeg($new);   
                        }
                        
                         if ($type == "image/png")
                        {
                         imagepng($new);   
                        }
                        
                        
                        imagedestroy($new);
                        }
                        imagedestroy($image);
                   

                     }
			
			if (!isset($resize))
                        {
                         //   echo "hello";
                            header("content-type: $type");		
                           echo $data;
                            
                        }

			
			
			
			
			}



?>