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


$integers[] = "image_id";
$integers[] = "width";



foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (isset($width) && $width > 2000) {echo "Too big"; }



	
	
	
if (isset($ERROR))
	{	
		echo $ERROR;
	}								

	
	else {

            if (isset($_REQUEST["module"]) && ($_REQUEST["module"] == "tv" || $_REQUEST["module"] == "pc" ))
                        {
	$sql = "select images.name as name, images.type as type,data, images.size as size,images.filename as filename, images.page as page 
	from images where image_id = \"$image_id\" 
" ;	   
	
                        }
             else
             {
              
                 $sql = "select images.name as name, images.type as type,data, images.size as size,images.filename as filename, images.page as page 
	from images,cal_cat,cal_events where image_id = \"$image_id\" 
	and images.page = cal_events.event_id and cal_cat.cat_id = cal_events.event_catid and cal_cat.cat_web != '0'" ;	
             }

	//$sql = "select images.name as name, images.type as type, images.size as size,images.filename as filename, images.page as page from images,cal_bridge,cal_events,modules where image_id = \"$image_id\" 
//	and images.page = cal_events.event_id and cal_bridge.cat = cal_events.event_catid and cal_bridge.m = modules.m and modules.m = \"$m\"" ;	
	
	//$sql = "select images.name as name, images.type as type, images.size as size,images.filename as filename, images.page as page from images where image_id = \"$image_id\" " ;
	
	
	//echo $sql;		
	$DB->query($sql,"calimage.php");
        
        $rowcount = $DB->countrows();
        
        if ($rowcount == 1)
        {
			$row = $DB->get();
			
			
			$image_name = $row["name"];
			$filename = $row["filename"];
			$type = $row["type"];
			$size = $row["size"];
			$page = $row["page"];
                        if (isset($_REQUEST["module"]) && $_REQUEST["module"] == "tv")
                        {
                         $filepath = $PREFERENCES['docdir']."/tv/".$page."/".$filename;   
                        }
                         elseif (isset($_REQUEST["module"]) && $_REQUEST["module"] == "pc")
                        {
                         $filepath = $PREFERENCES['docdir']."/pc/".$page."/".$filename;   
                        }
                        else
                        {
			$filepath = $PREFERENCES['docdir']."/calendars/".$page."/".$filename;
                        }
                         header("content-disposition: inline; filename=\"$image_name\" \n");
			
		//	echo $filepath;
			//$size = filesize($filepath);
		$ext = strtolower(substr(strrchr($filepath, "."), 1)) ;
			
			

                        
                        
                      if ($PREFERENCES["storage"] == "file")
                      {
                      //    echo "path is $filepath";
                          
                          
                          if (file_exists($filepath)) {$data = file_get_contents($filepath);} else {$ERROR = "File not found!";}
                      }
                        if ($PREFERENCES["storage"] == "database")
                      {
                          $data = base64_decode($row["data"]);
                      }
        } else { $ERROR = "Image deleted.";}      
                       
                      if (isset($ERROR))
			{
				echo  $ERROR;
                                exit();
			}
                      
                      
                      
                     if (function_exists('imagecreatefromstring') && isset($width))
                         
                     {    
                     $image = imagecreatefromstring($data);
                    $current_width = imagesx($image);
                    $current_height = imagesy($image);
 
                     if ($current_width > $width) 
                        {
                         $resize = "yes";
                         $new_height = $current_height / $current_width * $width;
                        
                            $new = imagecreatetruecolor($width,$new_height);

                            imagecopyresampled($new,$image,0,0,0,0,$width,$new_height,imagesx($image),imagesy($image));

                     
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