<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$page_title = $phrase[466];

include ("../includes/htmlheader.php");


if (isset($_REQUEST["resource_no"])) 
	{
	if (!(isinteger($_REQUEST["resource_no"]))) 
	{$ERROR  = $phrase[72];}
	else {$resource_no = $_REQUEST["resource_no"];}
	}

if (isset($_REQUEST["bookingno"])) 
	{
	if (!(isinteger($_REQUEST["bookingno"]))) 
	{$ERROR  = $phrase[72];}
	else {$bookingno = $_REQUEST["bookingno"];}
	}

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
	


if(!isset($ERROR))
	{
	
	 $sql = "select * from resource where resource_no = \"$resource_no\"";
	
$DB->query($sql,"rbview.php");
	$row = $DB->get();
			
	$resource_name =$row["resource_name"];
			$fee_applicable = $row["fee_applicable"];
			$location = $row["location"];
			//$display_contact = $row["display_contact"];
			//$display_telephone = $row["display_telephone"];
			//$display_address = $row["display_address"];
			//$display_notes = $row["display_notes"];
			//$book_multiple_days = $row["book_multiple_days"];
			$print = $row["print"];
			
			
	
	
	$sql = "select * from resourcebooking where bookingno = \"$bookingno\" ";
	$DB->query($sql,"rbview.php");
	$row = $DB->get();
	$bookno =$row["bookingno"];		
			$bookname = formattext($row["name"]);
			$sname = formattext($row["staffname"]);
			$book =$row["bookeddate"];
			$bookeddate = date("g:ia",$book);
			$bookeddate .= strftime(" %x",$book);
			$username =$row["username"];
			$cancelled =$row["cancelled"];
			$cancelname =$row["cancelname"];
			$cancelip =$row["cancelip"];
			$cancel =$row["canceltime"];
			$canceltime = date("g:ia",$cancel);	
			$canceltime .= strftime(" %x",$cancel);	
			$ip =$row["ip"];
			$bookpaid =$row["paid"];
			//$bookcontact =$row["contact"];
			//$booktelephone =$row["telephone"];
			//$bookaddress = formattext($row["address"]);
			//$booknotes = formattext($row["notes"]);
			$start =$row["starttime"];
			$end =$row["endtime"];
			
			
			$starttime = date("g:ia",$row["starttime"]);
			$endtime = date("g:ia",$row["endtime"]);
			
			$startdate = strftime("%A %x",$row["starttime"]);
			$enddate = strftime("%A %x",$row["endtime"]);
			
	
	echo "<div style=\"padding:1em;\" >
		<span style=\"float:right\" class=\"hide\">";
	if ($print == 2 || $print == 3) {echo "<a href=\"javascript:window.print()\">$phrase[255]</a> &nbsp;";} 
	echo "<a href=\"javascript:window.close()\">$phrase[266]</a></span><br><br>";
	if ($cancelled == "1") {echo "<span style=\"color:#ff6666;font-size:120%\"><b>$phrase[152]</b></span>";}
	elseif ($print == 2 || $print == 3){
		
		echo "<div style=\"text-align:left\">$PREFERENCES[orgname]<br>

$phrase[265]</div>
		
		";
	
	}
	echo "<br><br>	<span style=\"font-size:150%\">$resource_name</span><br>";
	
	
	echo "<br><b>$phrase[296]</b><br>$bookname<br>";
	
				if ($startdate == $enddate)
					{
					echo "<b>$phrase[297]</b><br>$starttime<br>
					<b>$phrase[298]</b><br> $startdate<br>
					";
					}
				else { 
				$diff = (floor(($end - $start)/60/60/24) + 1);
					
					echo "$phrase[269]<br>
					<b>$phrase[267]</b><br>$starttime $startdate<br> <b>$phrase[268]</b><br>$endtime $enddate";
					}
					echo "
					
					<br><b>$phrase[299]</b><br>$location";
					if ($fee_applicable == 1) 
								{
								if ($bookpaid == 1) {echo "<br><b>$phrase[251]</b><br>$phrase[138]";}
								}
                                                                
                                      
      
         $sql = "select * from resource_custom_values where  booking = '$bookingno' ";
       //  echo $sql;
  $DB->query($sql,"resourcebooking.php");
       
        $field_values = array();
	while	($row = $DB->get())
        {
       	$field_id =$row["field_no"];	
	$field_values[$field_id]  =$row["field_value"];	
	}

    //    print_r($field_values);
     //   exit();
        
        	//$sql = "select * from resource_custom_fields where  output = '1' and resource = '$resource_no'  order by ranking";
                
                $sql = "select * from resource_custom_fields,resource_fields  where output = '1' and
resource_custom_fields.fieldno = resource_fields.field and                           
resource = '$resource_no' order by resource, ranking";
		    
		
	$counter = 0;	
						
	$DB->query($sql,"resource.php");
	while ($row = $DB->get())
	{
	$field = $row["fieldno"]; 
	$label = htmlspecialchars($row["label"]);
	$type = $row["type"];
	$menu = $row["menu"];
	$values = explode("\r\n", $menu);
        
        echo "<br><strong>$label</strong><br>";
        
        
        foreach ($field_values as $key => $value)
        {
         if ($field == $key)   
         {
           if ($type == "t" || $type == "m") {echo "$value";}
        if ($type == "a") {echo nl2br($value)   ;}   
         }
        }
  
        
        }
        
        
				echo "<br>
<br>
<br>
<br>
<br>
<br>
<br><br>
<br>&nbsp;

";	
				//}
	}
	

include ("../includes/footer.php");
	
?>