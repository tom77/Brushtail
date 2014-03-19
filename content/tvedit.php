<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);
$datepicker = "yes";
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
$integers[] = "itemid";
$integers[] = "channel";
$integers[] = "status";
$integers[] = "id";




foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (isset($_REQUEST["m"]))
	{	
	
	if (isinteger($_REQUEST["m"]))
		{	
		$m = $_REQUEST["m"];	
		$access->check($m);
	
	if ($access->thispage < 2)
			{
		
			$ERROR  =  "<div style=\"text-align:center;font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
			}
	elseif ($access->iprestricted == "yes")
			{
			$ERROR  =  $phrase[0];
			}
	
		}
	else 
		{
		$ERROR  =  $phrase[72];
	
		}		
	}
else {
	$ERROR  =  $phrase[72];
	
}	





	$now = time();
$ip = ip("pc");
	

		
		
		include("../includes/leftsidebar.php");
		
			echo "<div id=\"content\"><div>";
		
	if (!isset($ERROR))
	{	

		$sql = "select * from modules where m = '$m'";
$DB->query($sql,"travel.php");
$row = $DB->get();
$modname = formattext($row["name"]);

echo "<h1 class=\"red\">$modname</h1>";
		
$branches = array();	
	$sql = "select branchno, bname from cal_branches order by bname";
	
	$DB->query($sql,"tvedit.php");
	while ($row = $DB->get())
	     {
	     	$_bname = $row["bname"];
	     	$_bno = $row["branchno"];
	     	
            $branches[$_bno] =$_bname;
			
		}
                

	if ($access->thispage == 3)	
		//start block allowed only to edit users
	{	
		
		
if (isset($_REQUEST["reorder"]))
	{
	//reorders pages
	$reorder = explode(",",$_REQUEST["reorder"]);
	//print_r($reorder);
	foreach ($reorder as $index => $value)
		{
		$index = $DB->escape($index);	
		$value = $DB->escape($value);
		
		$sql = "update tv_items set displayorder = '$index' WHERE id = \"$value\"";	
		//echo $sql;
		$DB->query($sql,"tvedit.php");
		}
			
	}		


		
if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "add_channel")
{
 $channel_name = $DB->escape($_REQUEST["channel_name"]);
 //$defaultTimeout = $DB->escape($_REQUEST["defaultTimeout"]);
 	
$sql = "insert into tv_channels values(NULL,'$m','$channel_name')";	
//echo $sql;
$DB->query($sql,"tvedit.php");
	
}


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "rename")
{
    
$name = $DB->escape($_REQUEST["name"]);

  $sql = "update tv_channels set name = '$name' where id = '$id'";
 //echo $sql;
$DB->query($sql,"tvedit.php");

}

if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "delete_channel")
{
 

  $sql = "select * from tv_items where channel = '$id'";
 //echo $sql;
$DB->query($sql,"tvedit.php");
$count = $DB->countrows();

if ($count == 0)
{
  $sql = "delete from tv_channels where id = '$id'";
 //echo $sql;
$DB->query($sql,"tvedit.php");    
    
} else {}


}


if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "deleteitem")
{
    
  
 $sql = "delete from tv_items where id = '$itemid'";
// echo " $sql <br>";
$DB->query($sql,"tvedit.php");
 

 //$sql = "select * from images where page = '$itemid' and modtype = 'q'";

 // $DB->query($sql,"tvedit.php");
    
        //    $row = $DB->get();
	//$page =$row["page"];
       
         
         if ($PREFERENCES["storage"] == "file")
         {
         $filepath = $PREFERENCES['docdir']."/tv/".$itemid ;  
      //   echo " deleted $filepath <br>";
         delDir($filepath);
         } 

 $sql = "delete from images where page = '$itemid' and modtype = 'q'";	
//  echo " $sql <br>";
$DB->query($sql,"tvedit.php");

//delete file
}




if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "additem")
{
    
   // print_r($_REQUEST);
 $expiry = $DB->escape($_REQUEST["expiry"]);
 
 
 if ($DATEFORMAT == "%d-%m-%Y")
             {
             $_d = substr($expiry,0,2);
             $_m = substr($expiry,3,2);
             $_y = substr($expiry,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $_d = substr($expiry,3,2);
         $_m = substr($expiry,0,2);
         $_y = substr($expiry,6,4);
             }	
 
 
 $expiry = $_y . "-" . $_m . "-" . $_d;
  
 
 
 $timeout = $DB->escape($_REQUEST["timeout"]);
 if (isset($_REQUEST["type"]))
 {
 $type = $DB->escape($_REQUEST["type"]);
 $daysahead = $DB->escape($_REQUEST["daysahead"]);
 
 $locations = array();
 if (isset($_REQUEST["location"])) { $locations = $_REQUEST["location"]; }
 
 $location = "";
 $counter = 0;
 foreach ($locations as $value)
 {
 if ($counter != 0) {$location .= ":";}
 $location .= $value;   
 $counter++;    
 }
 
 $html = $DB->escape($_REQUEST["html"]);
 $url = $DB->escape($_REQUEST["url"]);
 //$feed = $DB->escape($_REQUEST["feed"]);
 $keywords = $DB->escape($_REQUEST["keywords"]);
 $youtube = $DB->escape($_REQUEST["youtube"]);
 $html5video = $DB->escape($_REQUEST["html5video"]);
 $showDetails = $DB->escape($_REQUEST["showDetails"]);
 //$reload = $DB->escape($_REQUEST["reload"]);
 
 $reload = '0';
 
 if ($type == "html") {$content = $html;}	
 if ($type == "url" ) {$content = $url;}
 if ($type == "youtube" ) {$content = $youtube;}
 if ($type == "feed") {$content = $feed;}
 if ($type == "html5video") {$content = $html5video;}
 if ($type == "cal") {
 
     /*
 			$string = "daysahead=$daysahead
 			location=" ;
 			$length = count($locations);
 			$count = 0;
 			foreach ($locations as $index => $location){
 			//$location = $DB->escape($location);
 			if ($count == 0) {$string .= "$location";} else {$string .= ":" .$location ;}
 			$count++;
 			}
 			$string .= "
 			keywords=$keywords";
      
      */
 
 			$content = "";
 }	
 
 if ($type == "image") { $content = "";}
 
 $displayorder = "0";
 
$sql = "insert into tv_items values(NULL,'$type','$content','$expiry', '$timeout','$id','$location','$daysahead','$keywords','$showDetails','$reload','$displayorder')";	
//echo $sql;
$DB->query($sql,"tvedit.php");

if ($type == "image") {
$insert_id = $DB->last_insert();
//print_r($_FILES);
upload($m,$insert_id,'0',$PREFERENCES,$DB,'tvimage',$ip,$phrase);
}

 } else {
     
     //type not selected
     echo "<span style=\"color:red\">Error: no type selected</span>";
 }
}





if (isset($_REQUEST["update"])  && $_REQUEST["update"] == "updateitem")
{
    
    //print_r($_REQUEST);
 $expiry = $DB->escape($_REQUEST["expiry"]);
 
 
 if ($DATEFORMAT == "%d-%m-%Y")
             {
             $_d = substr($expiry,0,2);
             $_m = substr($expiry,3,2);
             $_y = substr($expiry,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $_d = substr($expiry,3,2);
         $_m = substr($expiry,0,2);
         $_y = substr($expiry,6,4);
             }	
 
 
 $expiry = $_y . "-" . $_m . "-" . $_d;
  
 
 
 $timeout = $DB->escape($_REQUEST["timeout"]);
 $content = "";
 $location = "";
 $daysahead = 0;
 $keywords = "";
 $showDetails = 0;
 $reload = 0;
 
 // if (isset($_REQUEST["reload"])) {$reload = $DB->escape($_REQUEST["reload"]);}
 
 if (isset($_REQUEST["type"]))
 {
 $type = $DB->escape($_REQUEST["type"]);
 
 if ($type == "html") {$content = $DB->escape($_REQUEST["html"]);}
  if ($type == "youtube" ) {$content = $DB->escape($_REQUEST["youtube"]);}
   if ($type == "html5video") {$content = $DB->escape($_REQUEST["html5video"]);}
 if ($type == "url") {$content = $DB->escape($_REQUEST["url"]);}	
  if ($type == "feed") {$content = $DB->escape($_REQUEST["feed"]);}	
 if ($type == "cal") {
     
     $daysahead = $DB->escape($_REQUEST["daysahead"]);
 $keywords = $DB->escape($_REQUEST["keywords"]);
  $showDetails = $DB->escape($_REQUEST["showDetails"]);
 
 $locations = array();
 if (isset($_REQUEST["location"])) { $locations = $_REQUEST["location"]; }
 
 $location = "";
 $counter = 0;
 foreach ($locations as $value)
 {
 if ($counter != 0) {$location .= ":";}
 $location .= $value;   
 $counter++;    
 }
 
     /*
 			$string = "daysahead=$daysahead
 			location=" ;
 			$length = count($locations);
 			$count = 0;
 			foreach ($locations as $index => $location){
 			//$location = $DB->escape($location);
 			if ($count == 0) {$string .= "$location";} else {$string .= ":" .$location ;}
 			$count++;
 			}
 			$string .= "
 			keywords=$keywords";
      
      */
 
 			$content = "";
 }	
 
 
 
$sql = "update tv_items set content = '$content', expiry = '$expiry', timeout = '$timeout',location = '$location',daysahead = '$daysahead', keywords = '$keywords',showDetails = '$showDetails', reload = '$reload' where id = '$itemid'";	
//echo $sql;
$DB->query($sql,"tvedit.php");

if ($type == "image") {
//$insert_id = $DB->last_insert();

//upload($m,$itemid,'0',$PREFERENCES,$_FILES,$DB,'tvimage',$ip,$phrase);
}

 } else {
     
     //type not selected
 }
}

	




	} //end block allowed only to edit users




if (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edititem" && $access->thispage == 3)
{
	

	 $sql  = "select * from tv_items where id = '$itemid'";	
      //   echo $sql;
	$DB->query($sql,"tvedit.php");
	


$row = $DB->get();
	

	$type = $row["type"];
	$content = $row["content"];
        $expiry = $row["expiry"];
	$timeout = $row["timeout"];
        $keywords = $row["keywords"];
        $daysahead = $row["daysahead"];
        $location = $row["location"];
        $showDetails = $row["showDetails"];
        $reload = $row["reload"];
        	
			$ey = substr($expiry,0,4);
			$em = substr($expiry,5,2);
			$ed = substr($expiry,8,4);
			//$enddate = $ed . "-"  . $em . "-" . $ey;
			
			
	if ($DATEFORMAT == "%d-%m-%Y")
             {
            $expiry = $ed . "-"  . $em . "-" . $ey;
         
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
        	$expiry = $em . "-"  . $ed . "-" . $ey;
         
             }
        
        
        
        
     
        $locations = explode(":",$location);
        
        $url = "";
        $html = "";
        
        if ($type == "url" || $type == "feed" || $type == "html5video" || $type == "youtube") {$url = $content;}
        if ($type == "html") {$html = $content;}
         if ($type == "image") {$html = $content;}
        if ($type == "cal") {
            
            
            
        }
        
        
	echo "<h2>$phrase[28]</h2>
	<form action=\"tvedit.php\" method=\"post\"  enctype=\"multipart/form-data\" >

	

	<b>$phrase[1080]</b><br>
	<input type=\"text\" name=\"expiry\" id=\"expiry\" value=\"$expiry\"><br><br>
	<b>$phrase[1093]</b><br>
	<input type=\"text\" name=\"timeout\" value=\"$timeout\"><br><br>
	
	";
        if ($type == "image") {echo "<b> Image</b><br>
        <div style=\"border-style:solid black 1px\">";
        
             $sql = "select * from images where modtype = 'q' and page = '$itemid'";
                  // echo $sql;
                    $DB->query($sql,"tvedit.php");
	while ($row = $DB->get())
		{
		$image_id = $row["image_id"];
		$name = $row["name"];  
                  
                echo "<img src=\"../main/image.php?m=$m&module=tv&image_id=$image_id\" style=\"width:500px;height:300px\">";
                  } 
        
        
        
        echo "</div>";
        }
        if ($type == "youtube") {echo "<b>Youtube clip ID</b><br>
      <input type=\"text\" size=\"60\" name=\"youtube\" value=\"$url\">";
        
        }
        
        if ($type == "url") {echo "<b>URL</b><br>
      <input type=\"text\" size=\"60\" name=\"url\" value=\"$url\">";
        
        }
        
         if ($type == "html5video") {echo "<b>video file url</b><br>
      <input type=\"text\" size=\"60\" name=\"html5video\" value=\"$url\">";
        
        }
        
        
        
         if ($type == "feed") {echo "<b> RSS feed</b><br>
      <input type=\"text\" size=\"60\" name=\"feed\" value=\"$url\">
        

        
        ";
        
        
        }
        if ($type == "cal") {echo "<b> Calendar</b><br><br><b>$phrase[179]</b><br>";
       
			
		
			
				foreach ($branches as $bno => $bname)
						{
							
		
			echo "<input type=\"checkbox\" name=\"location[$bno]\" value=\"$bno\"";
                        if(in_array($bno,$locations)) {echo " checked";}
                        echo "> $bname<br>";
							
						}
				
						
			echo "<br><b>$phrase[983]</b><br>
				<select name=\"daysahead\">";
				$counter = 1;
				
				while ($counter < 366)
				{
					echo "<option value=\"$counter\"";
                                        if ($counter == $daysahead) { echo " selected";}
                                        echo ">$counter</option>";
					$counter++;
				}
								
						
						
						
						
						
				echo "</select>
				<br><br><b>$phrase[99]</b></br>
				<input type=\"text\" name=\"keywords\" value=\"$keywords\">
                                <br><br>
                           <b>  Display</b><br> <select name=\"showDetails\"><option value=\"0\">Image only</option><option value=\"1\"";
                                if ($showDetails == 1) {echo " selected";}
                                echo ">Image and title</option></select>
                                
                                
                                
                                ";
        }
	
	

        if ($type == "html") {echo " <b>HTML</b><br>";
        echo " <textarea name=\"html\" rows=\"6\" cols=\"60\">$html</textarea>";
}

        echo "<br><br>
	<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\">
        <input type=\"hidden\" name=\"itemid\" value=\"$itemid\">
         <input type=\"hidden\" name=\"type\" value=\"$type\">
	<input type=\"hidden\" name=\"update\" value=\"updateitem\">
	<input type=\"hidden\" name=\"event\" value=\"edit_channel\">
	<input type=\"hidden\" name=\"id\" value=\"$id\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	</form>
	
	
			<script type=\"text/javascript\">		
			
function datepicker(id){
		
		new JsDatePick({
			useMode:2,
			target:id,
			dateFormat:\"$DATEFORMAT\",
			yearsRange:[2010,2020],
			limitToToday:false,
			isStripped:false,

			imgPath:\"img/\",
			weekStartDay:1
		});
	};
	
datepicker('expiry');
	</script>
	
	
	
	";
	
}













elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "additem" && $access->thispage == 3)
{
	
    
    
 if ($DATEFORMAT == "%d-%m-%Y")
             {
             $expiry  = date("d-m-Y",time() + 5184000);
            }
     
   if ($DATEFORMAT == "%m-%d-%Y")
             {
             $expiry  = date("m-d-Y",time() + 5184000);
            }   
     
 

	
	echo "<h2>Add item</h2>
	<form action=\"tvedit.php\" method=\"post\"  enctype=\"multipart/form-data\" >

	

	<b>$phrase[1080]</b><br>
	<input type=\"text\" name=\"expiry\" id=\"expiry\" value=\"$expiry\"><br><br>
	<b>$phrase[1093]</b><br>
	<input type=\"text\" name=\"timeout\" value=\"30\"><br><br>
	<b>$phrase[1000]</b><br>
	<table>
	<tr><td> <input type=\"radio\" name=\"type\" value=\"image\"> Image</td><td><div><div style=\"border-style:solid black 1px\"> <input type=\"file\" name=\"upload[]\"  /></div></td></tr>

             
            <tr><td> <input type=\"radio\" name=\"type\" value=\"url\"> URL</td><td><input type=\"text\" size=\"60\" name=\"url\">
         
            
            <tr><td> <input type=\"radio\" name=\"type\" value=\"youtube\"> Youtube clip ID</td><td><input type=\"text\" size=\"60\" name=\"youtube\">
            
               
            <tr><td> <input type=\"radio\" name=\"type\" value=\"html5video\"> video file url</td><td><input type=\"text\" size=\"60\" name=\"html5video\">
        
     
    </td></tr>
	<tr><td> <input type=\"radio\" name=\"type\" value=\"cal\"> Calendar</td><td>
	
				<b>$phrase[179]</b><br>
				";
			
		
			
				foreach ($branches as $bno => $bname)
						{
							
		
			echo "<input type=\"checkbox\" name=\"location[$bno]\" value=\"$bno\"> $bname<br>";
							
						}
				
						
			echo "<br><b>$phrase[983]</b><br>
				<select name=\"daysahead\">";
				$counter = 1;
				
				while ($counter < 366)
				{
					echo "<option value=\"$counter\" ";
                                        if ($counter == 30) {echo " selected";}
                                        echo ">$counter</option>";
					$counter++;
				}
								
						
						
						
						
						
				echo "</select>
				<br><br><b>$phrase[99]</b></br>
				<input type=\"text\" name=\"keywords\">
	<br><br><b>Display</b><br><select name=\"showDetails\"><option value=\"0\">Image only</option><option value=\"1\">Image and title</option></select>
	
</td></tr>
	<tr><td> <input type=\"radio\" name=\"type\" value=\"html\"> HTML</td><td><textarea name=\"html\" rows=\"6\" cols=\"60\"></textarea></td></tr>
	</table>
	<input type=\"submit\" name=\"submit\" value=\"$phrase[176]\">

	<input type=\"hidden\" name=\"update\" value=\"additem\">
	<input type=\"hidden\" name=\"event\" value=\"edit_channel\">
	<input type=\"hidden\" name=\"id\" value=\"$channel\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	</form>
	
	
			<script type=\"text/javascript\">		
			
function datepicker(id){
		
		new JsDatePick({
			useMode:2,
			target:id,
			dateFormat:\"$DATEFORMAT\",
			yearsRange:[2010,2020],
			limitToToday:false,
			isStripped:false,

			imgPath:\"img/\",
			weekStartDay:1
		});
	};
	
datepicker('expiry');
	</script>
	
	
	
	";
	
	
}


elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "rename" && $access->thispage == 3)
{
	
    //print_r($_REQUEST);
	$sql = "select * from tv_channels where id = '$id'";

	$DB->query($sql,"tvedit.php");
	$row = $DB->get();
	$channel_name =$row["name"];

	echo "
<h2>$phrase[178]</h2>            

<form action=\"tvedit.php\" method=\"post\">
        <input type=\"text\" name=\"name\" value=\"$channel_name\">
        <br><br><input type=\"hidden\" name=\"m\" value=\"$m\">
         <input type=\"hidden\" name=\"id\" value=\"$id\">
         <input type=\"hidden\" name=\"update\" value=\"rename\">
        
        <input type=\"submit\" name=\"submit\" value=\"$phrase[178]\"></from>";
        
        
        
      }


elseif (isset($_REQUEST["event"])  && $_REQUEST["event"] == "edit_channel" && $access->thispage == 3)
{
	
    //print_r($_REQUEST);
	$sql = "select * from tv_channels where id = '$id'";

	$DB->query($sql,"tvedit.php");
	$row = $DB->get();
	$channel_name = formattext($row["name"]);

	
     //   <script type=\"text/javascript\" src=\"swfobject/swfobject.js\"></script> 
        //     <script>
                  //      var params = { allowScriptAccess: 'always' };
                    
        //           function embedVideo(elementId,youtubeId)
//{
// var url  = 'http://www.youtube.com/v/' + youtubeId + '?enablejsapi=1&playerapiid=' + elementId; 
//swfobject.embedSWF(url, elementId, '320', '240', '8', null, null, params); 
// alert('hello');

//  }</script>
                  
      
	
	$members = array();
        $arrayitemid = array();
	$sql = "select * ,  unix_timestamp(expiry) as tstamp from tv_items where channel = '$id' order by displayorder, expiry";
      
    //    echo $sql;
        $counter = 0;
	$DB->query($sql,"tvedit.php");
          $num = $DB->countrows();
	while ($row = $DB->get())
		{
                $counter++;
		$itemid = $row["id"];
		$type = $row["type"];
		$expiry = $row["expiry"];
                $tstamp = $row["tstamp"];
		$content = $row["content"];
                $daysahead = $row["daysahead"];
                $location = $row["location"];
                $keywords = $row["keywords"];
                $timeout = $row["timeout"];
                $displayorder = $row["displayorder"];
                $showDetails = $row["showDetails"];
                $position[$counter] = $itemid;	
                
                if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {  $displayTime[] = strftime("%#d %b %Y",$tstamp);}
                else {$displayTime[] = strftime("%e %b %Y",$tstamp);}
                
                
                if ($type == "cal") {
                  
                    $content = "<b>$phrase[179]</b><br>";
                    
                   $array =  explode(":",$location);
                   foreach ($array as $value)
                   {
                   if (array_key_exists($value,$branches)) { $content .=  $branches[$value];}
                   }
                    $content .= "<br><br><b>$phrase[983]</b><br>$daysahead<br><br><b>$phrase[99]</b><br>$keywords<br><br><b>Display</b><br>";
                    if ($showDetails == "0") {$content .=  "Image only";} else {$content .=  "Image and title";} 
                    }
                    
                  
                  
                  
                  
                $arrayitemid[] = $itemid;  
                $arraycontent[] = $content;
                $arraytype[] = $type;  
                $arraystamp[] = $tstamp; 
                $arraytimeout[] = $timeout; 
                
                }
                
                
          
                
                 echo "         
        <a href=\"tvedit.php?m=$m\">Channel list</a>
        
        <h2>$channel_name</h2>
	<a href=\"tvedit.php?m=$m&event=additem&channel=$id\"><img src=\"../images/add.png\" title=\"$phrase[176]\" alt=\"$phrase[176]\"></a><br><br>
	
	<table class=\"colourtable\" >";
                
                
                
                $counter = 0;
                foreach ($arrayitemid as $index => $itemid)
                {
                    
                    $counter++;
                    
		echo "<tr";
                if ($arraystamp[$index] + 86400 < $now) {echo " class=\"grey\"";}
                echo "><td>$displayTime[$index]</td><td>$arraytimeout[$index]</td><td>$arraytype[$index]</td><td>";
                
                if ($arraytype[$index] == "image")
                {
                   
                      
                   $sql = "select * from images where modtype = 'q' and page = '$itemid'";
                  // echo $sql;
                    $DB->query($sql,"tvedit.php");
	while ($row = $DB->get())
		{
		$image_id = $row["image_id"];
		$name = $row["name"];  
                  
                echo  "<img src=\"../main/image.php?m=$m&module=tv&image_id=$image_id\" style=\"width:300px;height:200px\">";
                  }   
                  
                }
                
                elseif ($arraytype[$index] == "youtube") {
                
                echo "youTube ID $arraycontent[$index]
          ";
                
                }
                elseif ($arraytype[$index] == "html5video") {
                
                echo "video file url $arraycontent[$index]";
                }
                else {
               echo  $arraycontent[$index];}
               echo "</td><td>";
                   
                   if ($counter > 1) {
					
				
			//int_r($position);
							foreach ($position as $i => $value)
									{
									//echo "<br>index is $i $value count is $counter <br>";
									if ($i == ($counter - 1))
										{
										
										$up = $position;
										$up[$i] = $position[$counter];
										$up[$counter] = $value;
										
										}
									}
							
							
							
						//int_r($up);
							$up = implode(",",$up);
							
				echo "<a href=\"tvedit.php?reorder=$up&m=$m&id=$id&event=edit_channel\"><img src=\"../images/up.png\" title=\"$phrase[77]\"></a>";
				
			}
			echo "</td><td>";
			//print_r($position);
			if ($counter < $num) {
				
					foreach ($position as $i => $value)
									{
									//echo "$array_order[$index] <br>";
									if ($i == ($counter))
										{
										$down = $position;
										$temp = $down[$i];
										$down[$i] = $down[$i + 1];
										$down[$i + 1] = $temp;
										}
									}
								
								
							
							$down = implode(",",$down);
							echo "<a href=\"tvedit.php?reorder=$down&m=$m&id=$id&event=edit_channel\"><img src=\"../images/down.png\" title=\"$phrase[78]\"></a>";
			}
                        echo "</td>
                <td><a href=\"tvedit.php?m=$m&itemid=$itemid&id=$id&event=edititem\">Edit</a></td>
                <td><a href=\"tvedit.php?m=$m&itemid=$itemid&update=deleteitem&event=edit_channel&id=$id\">Delete</a></td></tr>";
		}
		
		
echo "</table>";
	
}










	
	else

{	
		
	
	
	
		
	
	
	echo "<h2>$phrase[1092]</h2>
	<table class=\"colourtable\">";
		
	

	
	
	
		
		$sql = "select * from tv_channels where m = '$m' order by name";
				
	//echo $sql;			

		$DB->query($sql,"tvedit.php");
		$num = $DB->countrows();
		$counter = 0;
		
		while ($row = $DB->get())
		{
			$counter++;
		$tv_name[$counter] = $row["name"];
		
		$tv_id[$counter] = $row["id"];
		//$position[$counter] = $row["location_id"];	
		//$count[$counter] = $row["count"];		
		
		
	
		}
	
		if (isset($tv_id))
		{
		foreach ($tv_id as $counter => $id)
		{
		
			echo "<tr><td>$tv_name[$counter]</td><td><a href=\"tvedit.php?m=$m&id=$id&event=rename\">Rename</a></td>
			<td><a href=\"tvedit.php?m=$m&amp;id=$id&amp;event=edit_channel\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
			<td style=\"width:1.6em\">";
		//	if ($count[$counter] == 0) {
			echo "<a href=\"tvedit.php?m=$m&amp;id=$id&amp;update=delete_channel\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>";
			//}
			
			echo "</td></tr>";
		}
		}
		

		
		echo "</table>
		<br><br>
		<form action=\"tvedit.php\" method=\"post\">

Name<br>
<input type=\"text\" name=\"channel_name\"> 
<input type=\"hidden\" name=\"m\" value=\"$m\"><br><br>

<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"update\" value=\"add_channel\">

<input type=\"submit\" value=\"$phrase[176]\">
</form>";
		
}
	
	
	
}
	

	


	
	
	
	
	
		

		
	



echo "</div></div>";
		
		
	include("../includes/rightsidebar.php");

include ("../includes/footer.php");

?>

