<?php

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

include ("../includes/initiliaze_page.php");



if (!isset($error))
{
	//check database connection
if (!$db = @mysql_pconnect('localhost', 'root','???????')) { $error[] = "Database connection failed" ;}
}


if (!isset($error))
{
//check database selection
if (! $select = @mysql_select_db('intranet', $db)) { $error[] = "Check database name.";}
}





$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");
if ($PREFERENCES["storage"] == "file")
                      {
echo   "delete image records that do not have matching files<br>";
//delete image records that do not have matching files
$sql = "select * from images";

	$result = mysql_query($sql);
				
        

        
		 while ($row = mysql_fetch_array($result))
		 	{
			$image_id = $row["image_id"];
			$image_name = $row["name"];
			$filename = $row["filename"];
			$modtype = $row["modtype"];
			$size = $row["size"];
			$page = $row["page"];
                        
                        $filepath = "";
                        
                        if ($modtype == "q")
                        {
                         $filepath = $PREFERENCES['docdir']."/tv/".$page."/".$filename;   
                        }
                        elseif ($modtype == "t")
                        {
                         $filepath = $PREFERENCES['docdir']."/pc/".$page."/".$filename;   
                        }
                        elseif ($modtype == "c")
                        {
			$filepath = $PREFERENCES['docdir']."/calendars/".$page."/".$filename;
                        }
                        elseif ($modtype == "p") {
                            $sql2 = "select m from page where page_id = '$page'";
                           $result2 = mysql_query($sql2);
                            $row2 = mysql_fetch_array($result2);
                            $m = $row2["m"];
                
			$filepath = $PREFERENCES['docdir']."/$m/".$page."/".$filename;
                        }   
                        
                        
                         if (!file_exists($filepath) && $filepath != "")
                         {
                             $sql2 = "delete from images where image_id = '$image_id'";
                             echo "file $filepath no exist $sql2 <br>";
                            mysql_query($sql2);
                         }

                }
                      }
                      
 
  echo "delete orphaned records<br>";                    
//delete orphaned records
                      
 $sql = " select * from images
where page not in (select event_id from cal_events) and modtype = 'c'
union
select * from images
where page not in (select event_id from cal_events) and modtype = 'c'
union
select * from images
where page not in (select pcno from pc_computers) and modtype = 't'
union
select * from images
where page not in (select id from tv_items) and modtype = 'q'

";                    
$result = mysql_query($sql);
				
        

        
		 while ($row = mysql_fetch_array($result))
		 	{                     
$image_id = $row["image_id"];
			$image_name = $row["name"];
			$filename = $row["filename"];
			$modtype = $row["modtype"];
			$size = $row["size"];
			$page = $row["page"];
                        
if ($PREFERENCES["storage"] == "file")
                      {
      $filepath = "";
                        
                        if ($modtype == "q")
                        {
                         $filepath = $PREFERENCES['docdir']."/tv/".$page."/".$filename;   
                        }
                        elseif ($modtype == "t")
                        {
                         $filepath = $PREFERENCES['docdir']."/pc/".$page."/".$filename;   
                        }
                        elseif ($modtype == "c")
                        {
			$filepath = $PREFERENCES['docdir']."/calendars/".$page."/".$filename;
                        }
                        elseif ($modtype == "p") {
                            $sql2 = "select m from page where page_id = '$page'";
                              $result2 = mysql_query($sql2);
                            $row2 = mysql_fetch_array($result2);
                            $m = $row2["m"];
                
			$filepath = $PREFERENCES['docdir']."/$m/".$page."/".$filename;
                        }   
              echo "deleting orphan file $filepath <br>";          
    @unlink($filepath);
}                        
      $sql2 = "delete from images where image_id = '$image_id'";
                             mysql_query($sql2);                 
              }                    
                        
                   
               
              
              
              
              
              
?>