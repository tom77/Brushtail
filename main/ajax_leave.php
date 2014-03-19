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
		


        
 if 	(isset($_REQUEST["event"]) && $_REQUEST["event"] == "checkdate")
	{
   //  echo "hello";
     
     $type = $DB->escape($_REQUEST["type"]);
     
     
     $grouplist = array();
     $sql = "select location_id from leave_members where userid = '$_SESSION[userid]'";
     $DB->query($sql,"ajax_leave.php");
	while ($row = $DB->get())
	{
            $grouplist[] = $row["location_id"];
        }
     
        
     //   print_r($grouplist);
     
       $cstart = array();
        $cend = array();
        
        $sql  = "select * , unix_timestamp(enddate) as cend from leave_closed where m = '$m'";	
	$DB->query($sql,"ajax_leave.php");
	while ($row = $DB->get())
	{
            $temp  = explode(":",$row["leavegroup"]);
            $locations[] = $temp;
          // print_r($temp);
            $types[] = explode(":",$row["leavetypes"]);
          //  $locations = explode(":",$locations);
          //  if (in_array($location,$locations))
          //  {
	$cstart[] = str_replace("-","",$row["startdate"]);
	$cend[] = str_replace("-","",$row["enddate"]) ;
           // }
        
        }
        
        
       // $date = toMysqlDateFormat($_REQUEST["date"], $DATEFORMAT);
      //  $date = str_replace("-","",$date) ;
        $datevalues = explode(",",$_REQUEST["datevalues"]);
        $dateids = explode(",",$_REQUEST["dateids"]);
        
       // print_r($datevalues);
        
        $blocked = array();
        
        echo "var blocked = new Array();
";
           foreach ($cstart as $key => $value)
        {
               
             //
              
               
             foreach ($datevalues as $k => $datevalue)
             {
                  
               $datevalue =  str_replace("-","",toMysqlDateFormat($datevalue,$DATEFORMAT)) ;
               
              //  echo "$value <= $datevalue && $cend[$key] >= $datevalue <br>";
                
                   if ($value <= $datevalue && $cend[$key] >= $datevalue && in_array($type,$types[$key]))
                        {  
                      // echo "hello";
                     //  print_r($locations);
                       foreach ($locations[$key] as $i => $group)
                         {
                          // echo "group is $group";
                          if (in_array($group,$grouplist)) {  echo "blocked.push($dateids[$k]);
";                              
                              } 
                         }
                      

                        }
             }    
               
               
        
        }
        
        
       
        
        }
        }
	
?>