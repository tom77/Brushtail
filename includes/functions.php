<?php

function ip($pc)
{
if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) 
					   			{
					   			 $ipaddress["pc"] = $_SERVER["HTTP_X_FORWARDED_FOR"];
				         		 $ipaddress["proxy"]  = $_SERVER["REMOTE_ADDR"];
				      			}
					    elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) 
								{
				           		$ipaddress["pc"] = $_SERVER["HTTP_CLIENT_IP"];
								$ip["proxy"] = "";
				       			} 
						else 	{
				           		$ipaddress["pc"] = $_SERVER["REMOTE_ADDR"];
								$ipaddress["proxy"]  = "";
				       			}	
	
	return $ipaddress["$pc"];
}




function sqltext($DATABASE_FORMAT,$string)
	{ 
	
	
	
	$string = preg_replace('/[��]/','\'',$string); 
	$string = preg_replace('/[��]/','"',$string); 
	
	
	

	
	   
    //Quote if not integer
       if (!is_numeric($string)) {
       	if ($DATABASE_FORMAT == "mysql")
		{
       $string = mysql_real_escape_string($string);
		}
		
		
	else
	{
		
		$string =  sqlite_escape_string($string);
	}
   }
   return $string;
	}

	
	
function isinteger($var)
	{

		
	if (strlen($var) < 100)
		{
	
	if (preg_match("/^[0-9]*$/", $var) == 1)
		{
			return true;
		}
	}
	
	return false;
	}
	
	
	
	
function deldir($dirName) {
 //
  
   if(file_exists($dirName)) {
  
       $dir = dir($dirName);
       while($file = $dir->read()) {
           if($file != '.' && $file != '..') {
               if(is_dir($dirName.'/'.$file)) {
                   delDir($dirName.'/'.$file);
               } else {
                   $deletefile = @unlink($dirName.'/'.$file);
                  
               }
           }
       }
       $dir->close();
       $deletefolder = @rmdir($dirName);
   } else {
       return false;
       
   }
   return true;
} 	
	
	
	
	
	
	
function strip($array)
{
 
   foreach ($array as $key=>$value)
   {
   	if (!is_array($value))
 		 {
   		$array[$key]=stripslashes($value);
  		}
    else 
   	 {
     foreach ($value as $key2=>$value2)
     	{
     		if (!is_array($value2))
 		 {
     	$array[$key][$key2]=stripslashes($value2);
 		 }
 		 else
 		 {
 		 	foreach ($value2 as $key3=>$value3)
     	{
     	$array[$key][$key2][$key3]=stripslashes($value3);	
     	}
 		 	
 		 }
     	}
     
   	 }
    
   	
  }
  return $array;
  
}

	
	
	function random($length) {

       
		$string = "";
       $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
       
       for ($a = 0; $a < $length; $a++) {
               $rand = rand(0, strlen($chars) - 1);
               $b = substr($chars, $rand, 1);
               $string .= $b;
       }
       
       return $string;
       
}


function filename($length)
  {
  	$filename = "";
    $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
    for($i=0;$i<$length;$i++)
    {
      $filename .= $pattern{rand(0,35)};
    }
    //$filename = md5($filename . time());
    return $filename;
  }
  
  
	
function menu($name,$base,$max,$match)
{
$text = "<select name=\"$name\">";	

for ($i = $base; $i <= $max; $i++) {  

$text .= "<option value=\"$i\"";
if ($i == $match) {$text .= " selected";}
$text .= ">$i</option>"; 

}

$text .= "</select>";
return $text;
}


function errorlog($DB,$script,$error)
{
	$sql = "select * from preferences where pref_name = 'errorlogging'";	
$DB->query($sql,"errors.php");
$row = $DB->get();
$logging = $row["pref_value"];


if ($logging == "on")
{	
	$now = time();
	$sql = "insert into errors values (NULL,'$now',\"$script\",\"$error\")";
	//echo "sql is $sql";
	$DB->query($sql,$script);
}
}

function error($error)
{
echo "<div style=\"color:#ff3300;font-size:2em;\"><br><br>$error</div>";	
	
}
	
function warning($warning)
{
echo "<div style=\"color:#ff3300;font-weight:bold;font-size:150%;margin-top:4em\">$warning</div>";	
	
}	


function win2unixtime($timestamp)
{

   $epoch_diff = 11644473600; // difference 1601<>1970 in seconds. see reference URL
   $timestamp = $timestamp * 0.0000001;
   $unix_timestamp = $timestamp - $epoch_diff;
   
   return $unix_timestamp;   
}



function unix2wintime($timestamp)
{

   $epoch_diff = 11644473600; // difference 1601<>1970 in seconds. see reference URL
   $timestamp = $timestamp + $epoch_diff;
   $win_timestamp = $timestamp * 10000000;
  
   
   return $win_timestamp;   
}





function formattext($string)
	{
	
	 $string = htmlspecialchars($string);
	$string = nl2br($string);
	$string = str_replace('[bold]', '<b>', $string);
    $string = str_replace('[/bold]', '</b>', $string);
	
	$string = str_replace('[italic]', '<i>', $string);
    $string = str_replace('[/italic]', '</i>', $string);
	
	$string = str_replace('[indent]', '<blockquote>', $string);
    $string = str_replace('[/indent]', '</blockquote>', $string);
	
	$string = str_replace('[bulletlist]', '<ul>', $string);
    $string = str_replace('[/bulletlist]', '</ul>', $string);
	
	$string = str_replace('[numberlist]', '<ol>', $string);
    $string = str_replace('[/numberlist]', '</ol>', $string);
	
	$string = str_replace('[listitem]', '<li>', $string);
	$string = str_replace('[/listitem]', '</li>', $string);
  	
	$string = str_replace('[preformat]', '<pre>', $string);
    $string = str_replace('[/preformat]', '</pre>', $string);
	
	$string = preg_replace ('/\[link\=(.*?)\](.*?)\[\/link\]/is', '<a href="$1">$2</a>', $string);
	$string = str_replace('[/link]', '</a>', $string);
	
	$string = preg_replace ('/\[nwlink\=(.*?)\](.*?)\[\/nwlink\]/is', '<a href=\"$1\" target=\"_blank\">$2</a>', $string);
	$string = str_replace('[/nwlink]', '</a>', $string);
	

	$string = preg_replace_callback('/\[rss_brief ?m?a?x?=?([0-9]*)\](.*)\[\/rss_brief\]/', "rss_brief_javascript", $string);
	
	$string = preg_replace_callback('/\[rss_full ?m?a?x?=?([0-9]*)\](.*)\[\/rss_full\]/', "rss_full_javascript", $string);
	
	$string = preg_replace_callback('/\[rss_brief_inline ?m?a?x?=?([0-9]*)\](.*)\[\/rss_brief_inline\]/', "rss_brief", $string);
	
	$string = preg_replace_callback('/\[rss_full_inline ?m?a?x?=?([0-9]*)\](.*)\[\/rss_full_inline\]/', "rss_full", $string);
	
	$string = str_replace('</li><br />', '</li>', $string);
	$string = str_replace('</li><br>', '</li>', $string);
	
	return $string;
	}
	
	
	
function formattext_html($string)
	{
	
		
	
	//$string = nl2br($string);
	$string = str_replace('[bold]', '<b>', $string);
    $string = str_replace('[/bold]', '</b>', $string);
	
	$string = str_replace('[italic]', '<i>', $string);
    $string = str_replace('[/italic]', '</i>', $string);
	
	$string = str_replace('[indent]', '<blockquote>', $string);
    $string = str_replace('[/indent]', '</blockquote>', $string);
	
	$string = str_replace('[bulletlist]', '<ul>', $string);
    $string = str_replace('[/bulletlist]', '</ul>', $string);
	
	$string = str_replace('[numberlist]', '<ol>', $string);
    $string = str_replace('[/numberlist]', '</ol>', $string);
	
	$string = str_replace('[listitem]', '<li>', $string);
	$string = str_replace('[/listitem]', '</li>', $string);
  	
	$string = str_replace('[preformat]', '<pre>', $string);
    $string = str_replace('[/preformat]', '</pre>', $string);
	
	$string = preg_replace ('/\[link\=(.*?)\](.*?)\[\/link\]/is', '<a href="$1">$2</a>', $string);
	$string = str_replace('[/link]', '</a>', $string);
	
	$string = preg_replace ('/\[nwlink\=(.*?)\](.*?)\[\/nwlink\]/is', '<a href=\"$1\" target=\"_blank\">$2</a>', $string);
	$string = str_replace('[/nwlink]', '</a>', $string);
	
	$string = preg_replace_callback('/\[rss_brief ?m?a?x?=?([0-9]*)\](.*)\[\/rss_brief\]/', "rss_brief_javascript", $string);
	
	$string = preg_replace_callback('/\[rss_full ?m?a?x?=?([0-9]*)\](.*)\[\/rss_full\]/', "rss_full_javascript", $string);
	
	$string = preg_replace_callback('/\[rss_brief_inline ?m?a?x?=?([0-9]*)\](.*)\[\/rss_brief_inline\]/', "rss_brief", $string);
	
	$string = preg_replace_callback('/\[rss_full_inline ?m?a?x?=?([0-9]*)\](.*)\[\/rss_full_inline\]/', "rss_full", $string);
	
	return $string;
	}
	
function pagemetadata ($pageid,$DB)
{ 

$sql = "select * from page where page_id = '$pageid'";
$DB->query($sql,"functions.php");
$row = $DB->get();
	
	$insert = "";
	$insert .= "$row[page_title]

";
	$sql2 = "select * from content where page_id = '$pageid' and (event = 0 or event = 15)";
	//echo $sql2;

	$DB->query($sql2,"functions.php");
	while ($row2 = $DB->get()) 
	{
		//print_r($row);
		
	$insert .= "$row2[title]
	$row2[body]
	
	";
	}
        $insert =  $DB->escape($insert);
	$sql3 = "update page set metadata = '$insert' where page_id = '$pageid'";
	$DB->query($sql3,"functions.php");
	
	//echo "$sql3";
		
	}	

//this function is for authenticating computer self bookings




function deleteimage($now,$ip,$image_id,$DB)
{	  
	
	//query filename
	$sql = "select * from images where image_id = '$image_id' "; 
	$DB->query($sql,"functions.php");	  
	$row = $DB->get();
	$imagename = $row["name"];
	$page_id = $row["page"];
	$content_id = $row["content_id"];
	
	$sql = "update images set deleted = '1' where image_id = '$image_id' "; 
	$DB->query($sql,"functions.php");
	
	$sql = "insert into content values (NULL,'$imagename','$image_id','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','3','0','1','0')";	
	$DB->query($sql,"functions.php");
	
}



function deletecalfolder($eventid,$PREFERENCES)

{
	$path = $PREFERENCES["docdir"] ."/calendars/" . $eventid;
	if (file_exists($path))
	{
	deldir($path);
	}
}


function deletecalimage($image_id,$m,$PREFERENCES,$DB)
{	  
	
	//query filename
	$sql = "select * from images where image_id = '$image_id' "; 
	$DB->query($sql,"functions.php");	  
	$row = $DB->get();
	$filename = $row["filename"];
	$page_id = $row["page"];
	
	
	$sql = "delete from images where image_id = '$image_id' "; 
	$DB->query($sql,"editcontent.php");
	
        if ($PREFERENCES["storage"] == "file")
        {
	$path = $PREFERENCES["docdir"] ."/calendars/" . $page_id . "/" . $filename;
	@unlink($path);
        }
	
}

function deletedoc($ip,$doc_id,$DB)
{
	$now = time();
	$sql = "select * from documents where doc_id = '$doc_id'";
	$DB->query($sql,"editcontent.php"); 
	$row = $DB->get();
	$doc_name = $row["doc_name"];
	$doc_id = $row["doc_id"];
	$content_id = $row["content_id"];
	$page_id = $row["page"];
	

		
	$sql = "update documents set deleted = '1' where doc_id = '$doc_id' "; 
	$DB->query($sql,"functions.php");
		$sql = "insert into content values (NULL,'$doc_name','$doc_id','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','5','0','1','0')";	
	$DB->query($sql,"functions.php");
}



function deleteparagraph($content_id,$page_id,$ip,$now,$DB)
{
	$sql = "update content set deleted = '1' where content_id = '$content_id' "; 
	$DB->query($sql,"functions.php");
		$sql = "insert into content values (NULL,'','','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','9','0','1','0')";	
	$DB->query($sql,"functions.php");
	
	
	pagemetadata ($page_id,$DB);
		
			
}

function restoreparagraph($content_id,$oldtitle,$page_id,$ip,$now,$DB)
{
	$sql = "update content set deleted = '0' where content_id = '$content_id' "; 
	$DB->query($sql,"functions.php");
		$sql = "insert into content values (NULL,'','','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','11','0','1','0')";	
	$DB->query($sql,"functions.php");
	//echo "$sql now is $now";
	
	pagemetadata ($page_id,$DB);
		
			
}


function deletecomment($content_id,$page_id,$ip,$DB)
{
	$now = time();	
	$sql = "update content set deleted = '1' where content_id = '$content_id' "; 

	$DB->query($sql,"functions.php");
		$sql = "insert into content values (NULL,'','','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','17','0','1','0')";	
	$DB->query($sql,"functions.php");
	
	
	pagemetadata ($page_id,$DB);
		
			
}

function upload($m,$page_id,$content_id,$PREFERENCES,$DB,$type,$ip,$phrase)
{
                      //  $insert_id = 0;
	

			$now = time();
			//print_r($_FILES);
                        
			foreach($_FILES['upload']['name'] as $index => $value)
                             	{
			
			//$name = $_FILES['upload']['name'];
			$name = $value;
			$ext = strrchr($name, '.');
			$randomname = filename(10) . "_" . $name;
		
			//$extension = substr($filename, -4);
			//$extension= strtolower($extension);
			$filetype = $_FILES['upload']['type'][$index];
	
			$filesize = $_FILES['upload']['size'][$index];
			$temp = $_FILES['upload']['tmp_name'][$index];
			
				
			if ($type == "document")
			{
			$maxsize = $PREFERENCES['maxfilesize'];
			$maxsize = $maxsize * 1048576;
			//echo "sizes $filesize $maxsize";
			
			}
			else {
			$maxsize = $PREFERENCES['maxpicsize'];
			$maxsize = $maxsize * 1024;
			}
			
    		//echo "$filesize $maxsize";

			
			
			
			if ($type == "calimage") {
				$moduledir = $PREFERENCES["docdir"] ."/calendars";
			$pagedir = $PREFERENCES["docdir"] ."/calendars/" . $page_id;	
			}
                        elseif ($type == "pcimage") {
				$moduledir = $PREFERENCES["docdir"] ."/pc";
			$pagedir = $PREFERENCES["docdir"] ."/pc/" . $page_id;	
			}
                        
                        elseif ($type == "tvimage") {
				$moduledir = $PREFERENCES["docdir"] ."/tv";
			$pagedir = $PREFERENCES["docdir"] ."/tv/" . $page_id;
                        $maxsize = 1048576;
			}
                        
                        
                        
			else 
			{
			$moduledir = $PREFERENCES["docdir"] ."/".$m;
			$pagedir = $PREFERENCES["docdir"] ."/".$m . "/" . $page_id;	
			}
			
			$fullpath = $pagedir."/".$randomname;

		
			
			if ($name == "")
			
				{ 
				$WARNING = "$phrase[207]"; 
				echo "<p style=\"color:red;margin:1em 0;font-size:2em\">$WARNING</p><br>";
				}
			elseif ($filesize == 0)
				{
					$WARNING = $phrase[83];
					echo "<p style=\"color:red;margin:1em 0;font-size:2em\">$WARNING</p><br>";
					
				}
			elseif ($filesize > $maxsize)
				{
					$WARNING = $phrase[83];
					echo "<p style=\"color:red;margin:1em 0;font-size:2em\">$WARNING</p><br>";
					
				}
			
							
			elseif (!file_exists($PREFERENCES["docdir"]) && $PREFERENCES["storage"] == "file")
					{	
					$WARNING = "$phrase[646]" ;
					echo "<p style=\"color:red;margin:1em 0;font-size:2em\">$WARNING</p><br>";
							
					}
				
				
			if ( file_exists($PREFERENCES["docdir"]) && !file_exists($moduledir)  && $PREFERENCES["storage"] == "file")
					{
					
					if (!(@mkdir($moduledir))) 
					{$WARNING = "<p style=\"color:red;margin:1em 0;font-size:2em\">$phrase[647] module/calendars folder</p><br>" ;}
					
					
					}
				
				
				
			if (file_exists($PREFERENCES["docdir"]) && file_exists($moduledir) && !file_exists($pagedir) && $PREFERENCES["storage"] == "file")
					{
					
					if (!(@mkdir($pagedir))) 
					{$WARNING = "<p style=\"color:red;margin:1em 0;font-size:2em\">$phrase[647] page/event folder</p><br>" ;}
					
					
					}
					
				
					
			if (file_exists($fullpath) && $PREFERENCES["storage"] == "file")
					{	
					$WARNING = "$phrase[294]";
					echo "<p style=\"color:red;margin:1em 0;font-size:2em\">$WARNING</p><br>";
					//exit();
					}
					
					//$newfilename = uniqid(time()) ."_".$docname;
				
					//echo "fullpath is $fullpath moduledir is $moduledir";
					
				if (!isset($WARNING))
				{	
			
				if (is_uploaded_file($_FILES['upload']['tmp_name'][$index]))
				{
                                $content = file_get_contents($_FILES['upload']['tmp_name'][$index]);
                           //   echo "content is $content";
                                if ($PREFERENCES["storage"] == "file")
                                {               
				$fh = fopen($fullpath, "w+");
				$fwrite = fwrite($fh, $content);
				fclose($fh);
				if ($fh == false) {$WARNING = $phrase[85];}
                                $content = "";
                                }
                                
                                 if ($PREFERENCES["storage"] == "database")
                                { 
                                 // if ($DB->type)
                                 //  
                                     $content = base64_encode($content);
                                     $content = $DB->escape($content); 
                                }
                                }
                                else {$WARNING = "No file uploaded";}
				
				
				
			
				//if($fh==false)
	//die("unable to create file");

				//if (!(@move_uploaded_file($temp, $fullpath))) 
				
				//	{
				//	
				//	$WARNING = $phrase[84] ;
				//	$error = $WARNING . " " . $fullpath;	
				//	errorlog($DB, "fucntions.php", $error);
					
				//	echo "<p style=\"color:red;margin:2em 0;\">$error<br></p>";
				//	}	
				
					
				if (!isset($WARNING))
					{
                                    
					$name = $DB->escape($name);
					$randomname = $DB->escape($randomname);
				//$newfilename = sqltext($DATABASE_FORMAT,$newfilename);
		 		
				$filetype = $DB->escape($filetype);
				
				
					if ($type == "calimage")
				{
                                
				$sql = "insert into images values (NULL, '$name','$filetype', '$content_id','$filesize','$randomname','$page_id','0' ,'$content','c')";
				$DB->query($sql,"functions.php");	
				//echo "$sql";
				// $insert_id = $DB->last_insert();	
				}
                                
                                		if ($type == "pcimage")
				{
                                
				$sql = "insert into images values (NULL, '$name','$filetype', '$content_id','$filesize','$randomname','$page_id','0' ,'$content','t')";
				$DB->query($sql,"functions.php");	
				//echo "$sql";
				// $insert_id = $DB->last_insert();	
				}
                                
					if ($type == "tvimage")
				{
				$sql = "insert into images values (NULL, '$name','$filetype', '$content_id','$filesize','$randomname','$page_id','0' ,'$content','q')";
				$DB->query($sql,"functions.php");	
				//echo "$sql";
				
                               // $insert_id = $DB->last_insert();
				}
                                
                                
				
				if ($type == "image")
				{
				$sql = "insert into images values (NULL, '$name','$filetype', '$content_id','$filesize','$randomname','$page_id','0' ,'$content','p')";
				$DB->query($sql,"functions.php");
                                
                              //  $insert_id = $DB->last_insert();
				//echo "$sql";
						$sql = "insert into content values (NULL,'$name','','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','2','0','1','0')";	
	$DB->query($sql,"functions.php");
				}
	
				if ($type == "document")
				{
				$metadata = $DB->escape($_REQUEST["metadata"]);
				
				$now = time();
				
					$sql = "insert into documents  values (NULL, '$name','$filesize', '$content_id', '$metadata', '$filetype','$randomname','$m','$page_id','$now','0' ,'$content')";
					$DB->query($sql,"editcontent.php");
                                       // $insert_id = $DB->last_insert();
					//echo $sql;
					
						$sql = "insert into content values (NULL,'$name','','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','4','0','1','0')";	
	$DB->query($sql,"functions.php");	
				}
					
				}
			
				
				}
				}
				
				
				
				
				if (isset($WARNING))
				{
			
					
					
			
					$error = $WARNING . " " . $fullpath;	
					errorlog($DB, "functions.php", $error);
					
					echo "<p style=\"color:red;margin:2em 0;\">$error<br></p>";	
					
					
					
				}

				
			//return $insert_id;			
			
}










function addcomment($body,$page_id,$ip,$DB)
{

	$now = time();
								
$sql = "insert into content values (NULL,'','$body','$page_id','0','$_SESSION[username]','$ip','$now',NULL,'15','0','1','0')";	
	   
 
$DB->query($sql,"functions.php");

$content_id = $DB->last_insert();

$sql = "insert into content values (NULL,'','','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','16','0','1','0')";	
	$DB->query($sql,"functions.php");

pagemetadata ($page_id,$DB);

}


function addparagraph($title,$body,$page_id,$ip,$DB,$imagealign,$expiry)
{

	
	$sql = "select max(page_order) as max from content where page_id = '$page_id'";
	//echo $sql;
	$DB->query($sql,"functions.php");
	$row = $DB->get();
	$max = $row["max"] + 1;
 	
	$now = time();
								
$sql = "insert into content values (NULL,'$title','$body','$page_id','$max','$_SESSION[username]','$ip','$now',NULL,'0','0','$imagealign','$expiry')";	
	//echo $sql;	   
 
$DB->query($sql,"functions.php");

$content_id = $DB->last_insert();

$sql = "insert into content values (NULL,'$title','','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','10','0','0','0')";	
	$DB->query($sql,"functions.php");

pagemetadata ($page_id,$DB);

}


function updateparagraph($content_id,$title,$body,$ip,$DB,$imagealign,$expiry)
{
	$now = time();
	
	//get old record 
	$sql = "select * from content where content_id = '$content_id'";
	
	$DB->query($sql,"functions.php");

	$row = $DB->get();
	$oldtitle = $DB->escape($row["title"]);	
	$oldbody = $DB->escape($row["body"]);	
	$oldpage_id = $row["page_id"];
	$page_id = $oldpage_id;
	$oldpage_order = $row["page_order"];
	$oldupdated_by = $DB->escape($row["updated_by"]);
	$oldupdated_ip = $row["updated_ip"];
	$oldupdated_when = $row["updated_when"];
	$oldimagealign = $row["imagealign"];
	

		
	

	$sql = "update content set title = '$title', updated_when = '$now', updated_by = '$_SESSION[username]', body = '$body', updated_ip = '$ip',imagealign = '$imagealign', expiry = '$expiry' WHERE content_id = '$content_id'";	

	$DB->query($sql,"editcontent.php");
	
	
									
$sql = "insert into content values (NULL,'$oldtitle','$oldbody','$oldpage_id','$oldpage_order','$oldupdated_by','$oldupdated_ip','$oldupdated_when','$content_id','1','0','$oldimagealign','0')";	
	$DB->query($sql,"functions.php");
	//echo $sql;
	
	pagemetadata ($page_id,$DB);
		
		
}



function restoredocument($doc_id,$content_id,$ip,$page_id,$DB)
{
	$now = time();
	$sql = "select * from documents where doc_id = \"$doc_id\"";
	$DB->query($sql,"functions.php"); 
	$row = $DB->get();

		
	$sql = "update documents set deleted = '0' where doc_id = \"$doc_id\" "; 
	$DB->query($sql,"editcontent.php");
		$sql = "insert into content values (NULL,'$row[doc_name]','$doc_id','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','8','0','1','0')";	
	$DB->query($sql,"functions.php");
}






function restorecomment($content_id,$page_id,$ip,$DB)
{
	$now = time();
		$sql = "update content set deleted = '0' WHERE content_id = \"$content_id\"";	
	//echo $sql;	
	$DB->query($sql,"functions.php");
	
	
									
$sql = "insert into content values (NULL,'','','$page_id','0','$_SESSION[username]','$ip','$now','$content_id','18','0','1','0')";	
	$DB->query($sql,"functions.php");
	
}

function deletepage($delpage,$ip,$DB)
{
	
	$now = time();
		  $sql = "update page set deleted = '1' where page_id = \"$delpage\" "; 
		 $DB->query($sql,"functions.php");
		 
		 $sql = "select * from page where page_id = \"$delpage\"";

	$DB->query($sql,"functions.php");

	$row = $DB->get();
	$title = $DB->escape($row["page_title"]);	
		 
		 
		$sql = "insert into content values (NULL,'$title','','$delpage','0','$_SESSION[username]','$ip','$now','$delpage','13','0','1','0')";	
		
	$DB->query($sql,"functions.php");
}

function restorepage($restorepage,$ip,$DB)
{
$now = time();
	$sql = "update page set deleted = '0' where page_id = \"$restorepage\" "; 
		 $DB->query($sql,"functions.php");
		 
		 $sql = "select * from page where page_id = \"$restorepage\"";
	
	$DB->query($sql,"functions.php");

	$row = $DB->get();
	$title = $DB->escape($row["page_title"]);	
		 
		 
		$sql = "insert into content values (NULL,'$title','','$restorepage','0','$_SESSION[username]','$ip','$now','$restorepage','14','0','1','0')";	
	$DB->query($sql,"functions.php");
}



function dirURL() {
 $pageURL = 'http' ;
 if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://" . $_SERVER["SERVER_NAME"];
 if (isset($_SERVER["SERVER_PORT"]) && ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") ) {
  $pageURL .= ":".$_SERVER["SERVER_PORT"];
 }
 
 
 $directory = dirname($_SERVER["PHP_SELF"]);
 $pageURL = $pageURL .$directory;
 return $pageURL;
}





function rss_brief($matches)
{

$link = $matches[2];	
$max = $matches[1];



$html = "";

$output = @file_get_contents($link);
$counter = 1;

if ($output)
{
	try {
@$xml = new SimpleXMLElement($output);



foreach ($xml->xpath('//channel') as $channel) {
    //echo $channel->title;
    $html .= "<h4>$channel->title <a href=\"$link\"> <img src=\"../images/rss.png\" title=\"RSS\" alt=\"RSS\"> </a></h4> ";
}






	$html .= "<ul>";
foreach ($xml->xpath('//item') as $item) {
    //echo $channel->title;
     if ($max == "" || $counter <= $max)
    {
    $html .= "<li><a href=\"$item->link\">$item->title</a></li>";
    }
    $counter++;
}


$html .= "</ul>";
	}
	catch (Exception $e){$html .= "<span style=\"color:red\">Invalid xml $link";}


} else { $html .= "<span style=\"color:red\">Failed to retrieve RSS feed $link";}







echo $html;	
}




function rss_full($matches)
{

$link = $matches[2];	
$max = $matches[1];



$counter = 1;

$html = "";

$output = @file_get_contents($link);

//echo $output;

if ($output)
{
		try {
	$xml = new SimpleXMLElement($output);



foreach ($xml->xpath('//channel') as $channel) {
    //echo $channel->title;
    $html .= "<h4>$channel->title <a href=\"$link\"> <img src=\"../images/rss.png\" title=\"RSS\" alt=\"RSS\"> </a></h4> ";
}





foreach ($xml->xpath('//item') as $item) {
    //echo $channel->title;
  //echo $item->title[0]; 
  
    
    if ($max == "" || $counter <= $max)
    {
   
  //  $title = $item->title[0];
    $html .= "<p><strong><a href=\"$item->link\">$item->title</a></strong> <br> $item->description</p>";
    }
    $counter++;
}
	}
	
	catch (Exception $e){$html .= "<span style=\"color:red\">Invalid xml $link";}



} else { $html .= "<span style=\"color:red\">Failed to retrieve RSS feed $link";}









echo  $html;	
}

function rss_brief_javascript($matches)
{
	
	//print_r($matches);
	
	$link = $matches[2];	
	$max = $matches[1];

	$links = explode(",",$matches[2]);

	

	if (count($links) == 1)
	{
	$links[0] = urlencode($links[0]);
		
	$url= "../main/rss.php?link=" . $links[0] . "&mode=brief";
	
	
if ($max != "") {$url .= "&max=$max";}
	$id = "i" . rand(5000,30000);
	
	$string = "<div class=\"feed\" id=\"$id\"><img src=\"../images/loading.gif\" alt=\"Loading rss feed\"></div>
	<script type=\"text/javascript\">
	if (typeof feeds == 'undefined') {var feeds = new Object;}
	feeds.$id = '$url';
	</script>	";
	
	}
	
	else 
	{
		
	$id = "i" . rand(5000,30000);
	$string = "<div class=\"feed\"  >
	<ul class=\"feed_list\" >
";
	
	$jlabels = "{";
	$counter = 0;
		foreach ($links as $key => $value)
    {
    	
    	$labelid = "l" . rand(5000,30000);
    	
    	$items = explode("::",$value);
    	$url= "../main/rss.php?link=" . $items[1] . "&mode=brief";
    	
    	
    	if ($max != 0) {$url .= "&max=$max";}
    	
    	$a_links[] = $url;
    	$a_label_id[] = $labelid;
    	//$a_label_name[] = $items[0];
    	
    		$items[0] = str_replace(' ', '&nbsp;', $items[0]);
    	
    	
    	$string .= "<li onclick='update_rss(\"$url\",\"$labelid\",\"$id\")' class=\"accent\" id=\"$labelid\"><a href=\"\" onclick=\"return false;\">$items[0]</a></li>
";
    	if ($counter != 0) {$jlabels .= ",";}
    	$jlabels .= "'$counter':'$labelid'";
    	$counter++;
    	
    }
	$jlabels .= "}";
	
	$string .= "</ul>
<div id=\"$id\"></div>
	<script type=\"text/javascript\">";
	
		
	$string .= "
	

	if (typeof panelfeeds == 'undefined') {var panelfeeds = new Object;}
	panelfeeds.$id = {'labelid':'$a_label_id[0]','link':'$a_links[0]'};
	panelfeeds.$id.labels = $jlabels;
	
	</script></div>";	
		
		
	}
	
	return $string;
	
}
function rss_full_javascript($matches)
{

	
	$link = $matches[2];	
	$max = $matches[1];
	

	$url= "../main/rss.php?link=" . $link . "&mode=full";
	if ($max != 0) {$url .= "&max=$max";}
	$id = "i" . rand(5000,30000);
	$string =  "<div class=\"feed\" id=\"$id\"><img src=\"../images/loading.gif\" alt=\"Loading rss feed\"></div>
	<script type=\"text/javascript\">
	if (typeof feeds == 'undefined') {var feeds = new Object;}
	feeds.$id = '$url';
	</script>";
	
	return $string;
}


function secondsToMinutes($time)

{
if ($time > 0)
{

$hours = (floor($time /3600));	
$difference = $time % 3600;	


	
$minutes = floor($difference /60);
$seconds = $difference % 60;

if ($seconds < 10) { $seconds = "0" . $seconds ;}
if (($minutes * 1) < 10) { $minutes = "0" . $minutes ;}
if ($hours < 1) { $hours = "0"  ;}
}
else
{
$hours = "0";
$minutes = "00";
$seconds = "00";
}


$string = $hours. "." .$minutes. ":" . $seconds;


return $string;

}

function xml_escape($string)
{
	    $string = str_replace("&", "&amp;", $string);
        $string = str_replace("<", "&lt;", $string);
        $string = str_replace(">", "&gt;", $string);
        $string = str_replace("\"", "&quot;", $string); 
	return $string;
}




function HTTP_POST($proxy,$port, $path, $data_to_send,$cookie) {

//echo "<h1>hello world $data_to_send cookiei is $cookie</h1>";	
//exit();	
//$vars = explode("&",$data_to_send);



$output = "";

$opts['http']['method'] = 'POST';

if (isset($cookie) && trim($cookie) != "") {
$opts['http']['header'] = "Content-type: application/x-www-form-urlencoded\r\nCoookie: $cookie";   
 // echo "hello cookie";  
}
else
{
 $opts['http']['header'] = 'Content-type: application/x-www-form-urlencoded';   
    
}

//echo "111 $data_to_send 2222";

$opts['http']['content'] = $data_to_send;
$opts['http']['timeout'] = 8;


$context  = stream_context_create($opts);

$url = "http://" . $proxy;
if ($port != "") { $url .= ":" . $port ; }
$url .= $path ;

$output = @file_get_contents($url, false, $context);


//echo "$url";

return $output;

}





function GetViaProxy ($proxy,$port,$url)
{
$output = "";
$fp = @fsockopen($proxy, $port, $errno, $errstr, 15);
@fputs($fp, "GET $url HTTP/1.0\r\nHost: $proxy\r\n\r\n");
 if (gettype($fp) == "resource") {
while(!feof($fp)){
  $output .= @fgets($fp, 4000);
//  print($line);
}
 }
@fclose($fp);

return $output;
}


function Get ($host,$port,$url)
{
$output = "";
$fp = @fsockopen($host, $port);
@fputs($fp, "GET $url HTTP/1.0\r\nHost: $host\r\n\r\n");
 if (gettype($fp) == "resource") {
while(!feof($fp)){
  $output .= @fgets($fp, 4000);
//  print($line);
}
 }
@fclose($fp);

return $output;
}



function send_email($DB,$emailaddress, $subject, $message,$headers)
{
	
	$message = str_replace("\r", "", $message); 
	
 	  $badwords[] = "to:";
	  $badwords[] = "cc:";
	  $badwords[] = "from:";
	  $badwords[] = "bcc:";
	  $badwords[] = "Content-Transfer-Encoding:";
	  $badwords[] = "Content-Type:";
	  $badwords[] = "Mime-Version:";
          $badwords[] = "CHAR(";
   
		//echo "message is $message";
	 
      $text = strtolower($message);
      $error = "no"; 
      	if (isset($badwords))
			{
			foreach ($badwords as $index => $word)
				{
					if (strstr($text, $word)) { $error = "banned word = $word";}			
				}
			}
       

if ($error == "no")
{
$send = mail($emailaddress, $subject, $message,$headers);
	//echo "posting email $emailaddress <br> subject $subject<br> message $message <br> headers $headers";
	
		if (!$send)
		{
			$error = "email error to $emailaddress from $headers";
			   return false;
	
		} else {
                    
                  //  echo "message sent";
                    return true;
                }
	
}

if ($error != "no")
{
	errorlog($DB,"functions.php send_email",$error);
         return false;
}


	
}



function send_message($DB,$emailaddress, $subject, $message,$headers)
{
	
	$message = str_replace("\r", "", $message); 
	
       

$send = @mail($emailaddress, $subject, $message,$headers);
	//echo "posting email $emailaddress <br> subject $subject<br> message $message <br> headers $headers";
	
		if (!$send)
		{
			$mailerror = "email error to $emailaddress from $headers";
			
	
		}
	


if (isset($mailerror))
{
	errorlog($DB,"functions.php send_message",$error);
}


	
}

function homepath($DB)
{
	
	$sql = "select  m, menupath,type from modules where frontpage = '1'";
	
		$DB->query($sql,"index.php");	
                $count = 0;
		while ($row = $DB->get())
                {
		$m = $row["m"];
		$menupath = $row["menupath"];
		$type = $row["type"];
		$count++;
                }
		
		if ($count == 0)
		{
		$homepath =  "main/page.php";
		}
		else 
		{
			
		if ($type == "x")
		{
	
		$homepath =  "main/custom.php?m=" . $m . "&front=yes";
		$homepath =  "main/custom.php?m=" . $m ;
		}
		
	else 
		{
		$homepath =  "main/" . $menupath . "?m=" . $m . "&front=yes";
		$homepath =  "main/" . $menupath . "?m=" . $m ;
		}	
			
			
		
		}
		
		return $homepath;	
	
}


function texttolink($string)
{
	//echo "<h1>blah</h1>";
$string = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\"  target=\"_blank\">\\2</a>", $string);
return $string;
}


function pc_debug($DB,$bookingno,$debugsql)
{
	$debugsql = $DB->escape($debugsql);
	$bookingno = $DB->escape($bookingno);
	$sql = "insert into pc_debug values(NULL,'$bookingno','$debugsql')";
	
	$DB->query($sql,"functions.php");
	//echo $sql;
}


function email_filter($badwords,$text)
{
	$text = strtolower($text);
      
      	if (isset($badwords))
			{
			foreach ($badwords as $index => $word)
				{
					if (strstr($text, $word)) {
						
						$errors[] = $word;
						}			
				}
				
			if (isset($errors)) {return $errors;}	
			
			}
	
}


function newsletter($input,$issue_id,$DB)
{

	$issue_id = $DB->escape($issue_id);
	$sql = "select temp_file from newsletter_templates,newsletter_issues where issue_id = '$issue_id' and newsletter_templates.temp_id = newsletter_issues.issue_template";
	$DB->query($sql,"functions.php");	
	$row = $DB->get();
	$temp_file = 
	//echo "temp file is $temp_file";
	
	$current = getcwd();
	$path = $current . "/newsletter_templates/" . $row["temp_file"];
	$html = @file_get_contents($path);
	
	$sql = "select issue_name from newsletter_issues where issue_id = '$issue_id'";
	$DB->query($sql,"functions.php");
	$row = $DB->get();
	$issue_name = $row["issue_name"];	
	$insert =  "<h2>$issue_name</h2>";
	
	$sql = "SELECT content_id, content_title, content_text FROM newsletter_content where content_issue = '$issue_id' order by content_order, content_id";
		
	$DB->query($sql,"functions.php");


					
					while (	$row = $DB->get()) 
						{
						
						$title= formattext($row["content_title"]);
						
							if ($input == 0)
						{
						$text = formattext($row["content_text"]);
						}
						else 
						{
						$text = formattext_html($row["content_text"]);	
						}
						
						
						$insert .= "<h3>$title</h3><div>$text</div>";
						}
	
						
					
	$html = str_replace("<<<CONTENT>>>",$insert,$html);
	
	return $html;
	
	
}


function page_view($DB,$PREFERENCES,$m,$page_id)
{
	$ip = ip("pc");
	
	
        if ($PREFERENCES['loghits'] == 1)
        {
	$string = trim($PREFERENCES['stats_ignore']);
	
	if (stripos($ip,$string) === FALSE)
	{
            
                 $date = date("Y-m-d");
               // $sql = "insert into page_views values('$m','$page_id','$date')";
         
              //  $DB->query($sql,"functions.php");
            $string  =  "$m,$page_id,$date
";   
           
            $fh = @fopen($PREFERENCES['hitslog'], 'a');
            if ($fh)
            {
            fwrite($fh, $string);
            fclose($fh);
            }
           
      
	}
        }
}

function format_time($hour,$minute)
{
$ampm = "";	
$output = "";
if ($hour > 12)	
{
$hour = $hour - 12;	
$ampm = "pm";
}
else {$ampm = "am";}
$output .= "$hour";
if ($minute != 0)
{$output .= ":$minute";}
$output .= $ampm;	
	
return $output;
}



function toMysqlDateFormat($date,$DATEFORMAT)
{
    $pattern = '/^\d\d-\d\d-\d\d\d\d$/';
   if ( preg_match ($pattern ,$date))
   {
   	if ($DATEFORMAT == "%d-%m-%Y")
             {
             $d = substr($date,0,2);
             $m = substr($date,3,2);
             $y = substr($date,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $d = substr($date,3,2);
         $m = substr($date,0,2);
         $y = substr($date,6,4);
             } 
    
    $date = "$y-$m-$d";
   } else {$date = "0000-00-00";}
    
    return $date;    
}


function fromMysqlDateFormat($date,$DATEFORMAT)
{
    
    
   
$y = substr($date,0,4);
$m = substr($date,5,2);
$d = substr($date,8,4);

			if ($DATEFORMAT == "%d-%m-%Y")
             {
         
            $date = $d . "-"  . $m . "-" . $y;
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
        
            $date = $m . "-"  . $d . "-" . $y;
             }	
             return $date;
}


function salt($id)
{
    $time = time();
    
    if (isset($_SESSION['salt'])) {$temp = $_SESSION['salt'];}
else {$temp = array();}

//delete salts older than 1 hour
foreach ($temp as $_id => $timestamp)
    {
    if (($time - $timestamp) > 3600) {
        unset($temp[$_id]); 
        //echo "deleteing old salt";
        
        }
    }


//no more than 100 salts
if (count($temp) > 100) {
    asort($temp);
    array_shift($temp);
   // echo "deleteing too many  salt";
    }

$temp[$id]  = $time;

$_SESSION['salt'] = $temp;
    
    
    
    
}


function spamTools($phrase)
{
     $time = time(); 
      if (isset($_SESSION['salt'])) {$temp = $_SESSION['salt'];}
else {$temp = array();}


 if (isset($_REQUEST['salt'])) {$salt = $_REQUEST['salt'];}
else {$salt = "";}


     
  if (isset($_REQUEST["spam1"]) && $_REQUEST["spam1"] != "")
  { 
    
   //   echo "<h1 style=\"color:red\">honeypot</h1>";
      $result["status"] = "0";
      $result["error"] = "$phrase[1120] Honeypot.";
   }
   
   
 elseif ($salt == "")
  { 
    
   //   echo "<h1 style=\"color:red\">No salt</h1>";
      $result["status"] = "0";
      $result["error"] = "$phrase[1120] No salt.";
   }
 elseif(!array_key_exists($_REQUEST["salt"],$temp))
     {
    //  echo "<h1 style=\"color:red\">No salt match</h1>";
      $result["status"] = "0";
      $result["error"] = "$phrase[1120] No salt match."; 
     
     }
     
  elseif (($time - $temp[$salt]) < 4)
  { 
      
     // echo "<h1 style=\"color:red\">too fast</h1>";
      $result["status"] = "0";
      $result["error"] = $phrase[1119]; //"Form submitted too quickly. Try reloading page to resubmit form.";
   } 
   else {
       $result["status"] = "1";
      $result["error"] = "Pass";
      unset($temp[$_REQUEST["salt"]]);
      $_SESSION["salt"] = $temp;
      
           
   } 

   return $result; 
     
   }




?>
