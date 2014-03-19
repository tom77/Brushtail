<?php


if(!include("../includes/initiliaze_page.php"))
 {
 	include("includes/initiliaze_page.php");
 }

#########################################################
#########################################################


  $phrase[130] = htmlspecialchars($phrase[130],ENT_QUOTES);
$phrase[131] = htmlspecialchars($phrase[131],ENT_QUOTES);
$phrase[132] = htmlspecialchars($phrase[132],ENT_QUOTES);
$phrase[133] = htmlspecialchars($phrase[133],ENT_QUOTES);
$phrase[134] = htmlspecialchars($phrase[134],ENT_QUOTES);
$phrase[135] = htmlspecialchars($phrase[135],ENT_QUOTES);
$phrase[110] = htmlspecialchars($phrase[110],ENT_QUOTES);
 
 
 if ($EVENTAUTHENTICATION == "autoaccept")
{
    exit();
}


//$event_id = $DB->escape($_REQUEST["event_id"]);


 if (($EVENTAUTHENTICATION == "registration" || $EVENTAUTHENTICATION == "local"  || $EVENTAUTHENTICATION == "web"  || $EVENTAUTHENTICATION == "ldap"  
 || $EVENTAUTHENTICATION == "email" || $EVENTAUTHENTICATION == "soap") && !isset($_SESSION['eventlogin']))
 
 {
 	
 	$url = "signin.php";
 	
 	if (isset($_REQUEST["event_id"])) 
 	{
  	
 	$url = $url . "?event_id=" . $_REQUEST["event_id"];
 	}
 	
 	header("Location: $url");
 	exit();
 	
 }

 //print_r($_COOKIE);
//  print_r($_REQUEST);
 
 if (isset($_REQUEST["event_id"])) {$event_id = $_REQUEST["event_id"];}
		//elseif (isset($_COOKIE["event_id"])) {$event_id = $_COOKIE["event_id"];}
		else {$event_id = 0;}
//	setcookie ("event_id", "", time() - 3600);
 
$heading = $phrase[961];	
	
include('calheader.php');	
	
	 	echo "<a href=\"$HOME_LINK\" style=\"margin-bottom:1em;\">$phrase[984]</a> | ";
 echo "<a href=\"webcal.php\">$phrase[118]</a>"; 
 
 if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "cancel")
 {
 	$bookingno = $DB->escape(substr($_REQUEST["bookingno"],0,100));
 	$cardnumber = $DB->escape(substr($_SESSION['eventlogin'],0,100));
 	
 	$sql = "update cal_bookings set status = '3' where bookingno = '$bookingno' and cardnumber = '$cardnumber'";
 	$DB->query($sql,"myevent.php");
 }
 
 
 
 
 
 //get list of locations if any
	$branches = array();	
	$sql = "select branchno, bname from cal_branches ";
	
	$DB->query($sql,"calendars.php");
	while ($row = $DB->get())
	     {
	     	$_bname = $row["bname"];
	     	$_bno = $row["branchno"];
	     	
            $branches[$_bno] =$_bname;
			
		}		 

 
 
 
 
 
 if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addbooking")
 {
//print_r($_REQUEST);
 $ev_id = $DB->escape(substr($_REQUEST["ev_id"],0,100));
 	
 	$sql = "SELECT cat_web,cat_email, cat_name,cat_colour,cat_cost, cat_age, cat_staffname, cat_address, cat_multiple,cat_confirmation, cat_print, cat_comments, cal_cat.cat_notes as cat_notes,
            cat_takesbookings, cat_waitinglist,  maxbookings, event_name, event_cost, event_location,event_description, cat_trainer, trainer, event_staffnotes, cancelled , event_start, trainerEmail
            FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and cal_events.event_id = '$ev_id'";
	//echo $sql;	
 	$DB->query($sql,"myevent.php");

		$row = $DB->get();
	
		$cat_name = formattext($row["cat_name"]);
		$cat_colour = $row["cat_colour"];
		$event_name = formattext($row["event_name"]);
		$event_location = formattext($row["event_location"]);
		$event_cost = formattext($row["event_cost"]);
		$event_description = formattext($row["event_description"]);
		$cat_cost = $row["cat_cost"];
		$cat_web = $row["cat_web"];
		$cat_email = $row["cat_email"];
		
	

		$displaydate = strftime("%a %x", $row["event_start"]);
		$displaytime = date("g:i a", $row["event_start"]);
		$maxbookings = $row["maxbookings"];
		$cat_takesbookings = $row["cat_takesbookings"];
		$cat_age = $row["cat_age"];
		$cat_address = $row["cat_address"];
		$cat_multiple = $row["cat_multiple"];
		$cat_waitinglist = $row["cat_waitinglist"];
                $trainerEmail = $row["trainerEmail"];
	
		$cancelled = $row["cancelled"];
		
		//print_r($_REQUEST);
		
		//$barcode = $_REQUEST["barcode"];
	
 	
 	
 	
 			$firstname = $_POST["firstname"];
			$lastname = $_POST["lastname"];
			$telephone = $_POST["telephone"];
			
			if (isset($_POST["address"]))  { $address = $_POST["address"]; } else {$address = array();}
			if (isset($_POST["comments"]))  { $comments = $_POST["comments"]; } else {$comments = array();}
			if (isset($_POST["age"]))  { $age = $_POST["age"]; } else {$age = array();}
			
		
	
			
			//$paid = $DB->escape($_POST["paid"]);
		
			//$confirmation = $DB->escape($_POST["confirmation"]);
			//$staffname = $DB->escape($_POST["staffname"]);
			
			$status = $DB->escape($_POST["status"]);
			//$email = $DB->escape($_POST["email"]);
 			$cardnumber = $DB->escape($_SESSION['eventlogin']);
 	
 	
 	//print_r($_POST);
 	$sql = "select max(webid) as webid from cal_bookings";
 	$DB->query($sql,"myevent.php");
	$row = $DB->get();
	$webid = $row["webid"] + 1;
	
	
	 if ($DB->type == "mysql")
		{  	
		$sql = "LOCK TABLE cal_bookings WRITE";
		}
	else
		{  	
		$sql = "begin transaction";
		}
								
								
		$DB->query($sql,"myevent.php");
 	
 	
 	$group = count($firstname) ;
 		
 			$counter = 0;
 			while ($counter < $group)
 			{
 				
 			if (trim($telephone[$counter]) == "" || trim($lastname[$counter]) == "" || trim($firstname[$counter]) == "") 
				{
		$ERROR = "$phrase[60]<br><br> <a href=\"myevent.php?event_id=$ev_id\">$phrase[813]</a>" ;
				}	
 				
 			$counter++;	
 			}
 	
 	
 	
 			
			
			
	
	if ($status != 1 || $status != 2) {$status = 1;}
	
	if ($EVENTAUTHENTICATION == "registration")
	{
	$email = $DB->escape($_SESSION['eventlogin']);
	}
	else 
	{
	if (isset($_REQUEST["email"])) 
		{
		$email = $DB->escape($_REQUEST["email"]);	
		}
		else {$email = "";}
		
	
	}
	
	
	
	
 	 if ($EVENTAUTHENTICATION == "disabled")
               {
               	$ERROR = "Web bookings disabled";
               
               }
               
    elseif ($cat_web < 2)
	{ $ERROR = "This event type does not take web bookings";

	}
 	
 


						
		//get booking total	
	 $sql = "SELECT count(*) as count FROM cal_bookings  where eventno = '$ev_id' and status = '1' ";
     //echo $sql;
	$DB->query($sql,"myevent.php");
	$row = $DB->get();
	$total = $row["count"];
	$datebooked = time();
	
	
	//get waiting list total		
	 $sql = "SELECT count(*) as count FROM cal_bookings  where eventno = '$ev_id' and status = '2' ";
     //echo $sql;
	$DB->query($sql,"myevent.php");
	$row = $DB->get();
	$waiting = $row["count"];
		
		
		if ($maxbookings <> 0 && ($total >= $maxbookings || $waiting > 0) )
		{
			//echo "A";
		if ($cat_waitinglist == 0)
			{
			$ERROR = $phrase[147];
			}
		else 
			{
			$status = 2;
		//	echo "changeing status";
			}
		} 
		
		
			if ($total + $group > $maxbookings && $maxbookings <> 0  && $status == 1 )
										
									{		
										if ($group == 1)
										{	
										$ERROR = "
										$phrase[262]"; //could not complete booking
										}
										else 
										{
											$ERROR = "$phrase[263]"; //not enough places available
										}
									}	
		
		
		
		
	
			 
	
	 	
	 	
	 		$counter = 0;
 			while ($counter < $group && !isset($ERROR)  && $counter < $MAX_WEB_BOOKINGS)
 			{
 				
 			//	echo "counter is $counter group is $group<br>";
 				
 				//check if event full
     $sql = "SELECT count(*) as count FROM cal_bookings  where eventno = '$ev_id' and status = '1' ";
     //echo $sql;
	$DB->query($sql,"myevent.php");
	$row = $DB->get();
	$total = $row["count"];
	$datebooked = time();

	
			
           
 				
 					
					$_firstname = $DB->escape(substr($firstname[$counter],0,100)); 
					$_lastname = $DB->escape(substr($lastname[$counter],0,100)); 
					$_telephone = $DB->escape(substr($telephone[$counter],0,100)); 
					
					if (key_exists($counter,$address)) {$_address = $DB->escape(substr($address[$counter],0,500)); } else {$_address = "";}
 					if (key_exists($counter,$comments)) {	$_comments = $DB->escape(substr($comments[$counter],0,500)); } else {$_comments = "";}
 					if (key_exists($counter,$age)) {	$_age = $DB->escape(substr($age[$counter],0,30)); } else {$_age = "0";}
 				
 				
 				
														
													if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
													{
													$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
													}
													else
													{
													$ip = $_SERVER['REMOTE_ADDR'];	
													}
															
$sql = "INSERT INTO cal_bookings  (bookingno, firstname, lastname, paid, status, eventno, telephone,address,comments,confirmation, age,staffname,ip,username,cardnumber,time_booking,time_updated,email,webid,receipt)
VALUES(NULL,'$_firstname','$_lastname','0','$status', '$ev_id', '$_telephone','$_address','$_comments','0','$_age','www','$ip','www','$cardnumber','$datebooked','$datebooked','$email','$webid','0')";
													//echo $sql;			
														$DB->query($sql,"webcal.php");
                                                                                                                
                                                                                                                
       if (key_exists($event_location,$branches)) { $event_location = $branches[$event_location];}
					
                                                                                                                
                                                                                                                
                                                                                                                
                                                                                                                
                                                                                                                
														
	if ($cat_email == 1 && $status == 1)
	{													
														
	if ($EVENTAUTHENTICATION == "registration")
	{
		$email = $_SESSION['eventlogin'];
		
	}
	elseif (preg_match('/@/', $email) == 1)		
	{
		$email = $_REQUEST["email"];
	}

	else { $email = "";}

	if ($email != "")
	{
		
	$message = "$phrase[975]

$event_name
$displaytime
$displaydate
$event_location
$event_description
	
$EVENT_EMAIL_FOOTER
";
        
        $headers = "From:$EVENTBOOKINGS_FROM";
	
        
        
	send_email($DB,$email,$phrase[976], $message,$headers);
	//echo "send_email(DB,$email,$phrase[976], $message,$headers);";
	
	}
	}
        
        
          if (filter_var($trainerEmail, FILTER_VALIDATE_EMAIL)) {
                                                         
                                                        
                                                           
                                                           	$message = "
                                                                    
$firstname $lastname
$cardnumber
$telephone

$event_name
$displaytime
$displaydate
$event_location
$event_description

";
              if (isset($EVENTBOOKINGS_FROM)) {$EMAIL = $EVENTBOOKINGS_FROM;}
 
                                                                
                                                                
        $headers = "From:$EMAIL";
send_email($DB,$trainerEmail,$phrase[976], $message,$headers);
	//echo "send_email(DB,$trainerEmail,$phrase[976], $message,$headers);";
        
                                                       }
        
        
        
        
 	
	$counter++;
 }
 
 

	 if ($DB->type == "mysql")
		{  	
		 $sql = "unlock tables";
		}
								
	else
		{  	
		 $sql = "commit";
		}
 	$DB->query($sql,"myevent.php");
 	
 }
 
 

	
 
 
 
 
 
 
 
  if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "cancel") //cancel event
	{
		$bookingno = $_REQUEST["bookingno"];
		
echo "<h2>$phrase[670]</h2>
<h3>$phrase[239]</h3>

 <a href=\"myevent.php?update=cancel&bookingno=$bookingno\"><b>$phrase[12]</b></a> |  <a href=\"myevent.php\"><b>$phrase[13]</b></a>
";
	}
		
		
 //print_r($_COOKIE);
 
 elseif ($event_id != 0) //book event
{
	
		
	
		$event_id = $DB->escape($event_id);
	
	    $sql = "SELECT cat_web,cat_email, cat_name,cat_colour,cat_cost, cat_age, cat_staffname, cat_address, cat_multiple,cat_confirmation, cat_print, cat_comments, cal_cat.cat_notes as cat_notes, cat_takesbookings, cat_waitinglist,  maxbookings, event_name, event_cost, event_location,event_description, cat_trainer, trainer, event_staffnotes, cancelled , event_start,age_range  FROM cal_events, cal_cat  where cal_cat.cat_web > '0' and cal_cat.cat_id = cal_events.event_catid and cal_events.event_id = '$event_id'";
		
	 //   echo $sql;
	    $DB->query($sql,"calweb.php");
		
		if ($DB->countrows() == 0) 
		{
		//this event does not take web bookings 
		exit();
		}
		
		$row = $DB->get();
	
		$cat_name = formattext($row["cat_name"]);
		$cat_colour = $row["cat_colour"];
		$event_name = formattext($row["event_name"]);
		$event_location = formattext($row["event_location"]);
		$event_cost = formattext($row["event_cost"]);
		$event_description = formattext($row["event_description"]);
		$cat_cost = $row["cat_cost"];
		$cat_notes = $row["cat_notes"];
		$event_staffnotes = formattext($row["event_staffnotes"]);
		$agerange = $row["age_range"];
		$trainer = formattext($row["trainer"]);
		$displaydate = strftime("%A %x", $row["event_start"]);
		$displaytime = date("g:i a", $row["event_start"]);
		$event_start = $row["event_start"];
		$maxbookings = $row["maxbookings"];
		$cat_takesbookings = $row["cat_takesbookings"];
		$cat_age = $row["cat_age"];
		$cat_address = $row["cat_address"];
		$cat_multiple = $row["cat_multiple"];
		$cat_comments = $row["cat_comments"];
		$cat_staffname = $row["cat_staffname"];
		$cat_confirmation = $row["cat_confirmation"];
		$cat_waitinglist = $row["cat_waitinglist"];
		$cat_print = $row["cat_print"];
		$cat_web = $row["cat_web"];
		$cat_email = $row["cat_email"];
		$cancelled = $row["cancelled"];
	
	
	$now = time();
	
	//get maximum number of bookings
				$sql = "SELECT count(*) as total from cal_bookings
 					where cal_bookings.eventno = '$event_id' and cal_bookings.status = '1'";
			//echo "$sql";
			$DB->query($sql,"calweb.php");
			$row = $DB->get();
			$total = $row["total"];
	
	
	
	
		
      if ($EVENTAUTHENTICATION != "autoaccept") 
      {
		echo " | <a href=\"myevent.php\" style=\"margin-bottom:1em;\">$phrase[961]</a> | <a href=\"webcal.php?action=logout\">$phrase[275]</a>";
      }
	
    
  

         
    if ($cat_web > 1 && (($maxbookings == 0 || $total < $maxbookings) || $cat_waitinglist == 1)  && $EVENTAUTHENTICATION != "disabled" && $cancelled == 0 && $event_start > $now)
    {    

    	
    	
    /*	
	if ($maxbookings == 0)
	{ $places = $cat_multiple;}
	else
	{
	$vacancies = $maxbookings - $total;
	if ($vacancies > $cat_multiple) 
		{$places = $cat_multiple;} else {$places = $vacancies;}
	
	}
	
	*/
	echo "<h2>";
	
	
	if ($maxbookings == 0 || $maxbookings > $total) { echo "$phrase[960]<br><br>";}

	if ($maxbookings != 0 && $total >= $maxbookings &&  $cat_waitinglist == 1)
	{
		
		echo "$phrase[150]<br><br>$phrase[964]";
	}
	
	
	
	echo "</h2>
	 <form action=\"myevent.php\" method=\"post\" >
	<div style=\"width:50%;float:left;\">
	<div class=\"eventform\"  style=\"padding:0 0 4em 1em;text-align:left;\">
	";

		
	
                        
											
											
											
	echo "<div id=\"replace\" style=\"margin:0;padding:0;\">
	<p>
	<label>$phrase[130]</label>
         <input type=\"text\" name=\"firstname[0]\" id=\"fn_0\" onchange=\"_required()\" onkeyup=\"_required()\" class=\"required\"><span style=\"padding:0;margin:0;color:red;\" id=\"fn_0_w\">$phrase[110]</span>
   </p><p>
       <label>$phrase[131]</label>
         <input type=\"text\" name=\"lastname[0]\" id=\"ln_0\" onchange=\"_required()\" onkeyup=\"_required()\" class=\"required\"> <span style=\"color:red\" id=\"ln_0_w\">$phrase[110]</span>
            </p><p><label>$phrase[132]</label>
		<INPUT type=\"text\" name=\"telephone[0]\" id=\"t_0\" size=\"20\" maxlength=\"20\"  onchange=\"_required()\" onkeyup=\"_required()\" class=\"required\"> <span style=\"color:red\" id=\"t_0_w\">$phrase[110]</span></p>
		";
         
    
                    
										
         						
                        
          								
		if ($cat_address == 1)
			{
			echo "<p><label>$phrase[134]</label><textarea cols=\"40\" rows=\"5\" name=\"address[0]\" id=\"address_0\"></textarea></p>";
			}
         
		if ($cat_comments == 1)
											{
											echo "<p><label>$phrase[135]</label><textarea cols=\"40\" rows=\"5\" id=\"comments_0\" name=\"comments[0]\"></textarea></p>
											";
											}


		if ($cat_age == 1)
											{
											
											$pos = strpos($agerange,":");

											$minage = substr($agerange,0,$pos);
											$maxage = trim(substr($agerange,$pos + 1));	
											
											//check input values are sensible
											if (!(($maxage > $minage) && ($minage > 0 && $minage < 120) && ($maxage > 1 && $maxage < 120))) 
											{$minage = 0; $maxage = 120;}	
												
												
												
											echo "<p>
											<label>$phrase[133]</label><select class=\"age\" name=\"age[0]\" id=\"age_0\" onchange=\"_required()\"><option value=\"noselection\">$phrase[885]</option>";
											$c = $minage;
											while ($c <= $maxage)
											{
											echo "<option value=\"$c\">$c</option>
";	
											$c++;
											
											}
											
											
											echo "</select> <span style=\"color:red\" id=\"age_0_w\">$phrase[110]</span></p>";
											} 
											
		
		 
         
                        
                        
           if ($cat_age <> 1)
										{
											echo "
											<INPUT type=\"hidden\" name=\"age[0]\"  value=\"0\" size=\"2\" maxlength=\"2\">";
											$minage = 1;
											$maxage = 1;
										}
											
		if ($cat_address <> 1)
										{
											echo "
											<INPUT type=\"hidden\" name=\"address[0]\"  value=\"\" size=\"2\" maxlength=\"2\">";
										}
		

		if ($cat_comments <> 1)
											{
											echo "<input type=\"hidden\" name=\"comments[0]\" value=\"\">
											";
											}
											
		
        echo "</div>";
        
        	if ($cat_multiple  > 1)
											{
											echo "<div style=\"display:none\" id=\"groupmenu\">
											<label>$phrase[137]</label><select name=\"group\" id=\"group\" onchange=\"replace()\">";
											
											
											
											
											
											
											if (($cat_waitinglist == 1) && ($maxbookings <= $total)) 
											//booed out and waiting list available
											{
											$limit = $cat_multiple;
											}
											elseif ($maxbookings == 0) 
											//unlimited bookings
											{
											$limit = $cat_multiple;
											}
											else
											{   
												$vacancies = $maxbookings - $total;
											if ($vacancies > $cat_multiple) 
											{$limit = $cat_multiple;} else {$limit = $vacancies;}
											
											}
											
											if ($MAX_WEB_BOOKINGS < $limit) {$limit = $MAX_WEB_BOOKINGS;}
								
											
										
											$counter = 1;
											while ($counter <= $limit)
											{
											 echo "<option value=\"$counter\">$counter</option>
											 ";	
											$counter++;
											}
											
											
											echo "</select></div>
											";
											
											}
											
											
											
				
        
           if ($EVENTAUTHENTICATION != "registration" && $cat_email == 1)
       {
       	echo "<p> <label><b>$phrase[259]</b></label>
		<INPUT type=\"text\" name=\"email\" id=\"email\" size=\"40\" maxlength=\"250\"  > </p>
		";
       	
       	
       } else { echo  "<br>";}
										
         if ($cat_takesbookings == 1  && $cat_web > 1  && $EVENTAUTHENTICATION != "disabled" && $cancelled == 0 && $event_start > $now)
    {    

         
  
         if ($maxbookings == 0 || $maxbookings > $total) 
         { 
         echo "<input type=\"submit\" id=\"submit\" name=\"submit\" value=\"$phrase[960]\">
         <input type=\"hidden\" name=\"status\" value=\"1\">";
		}
	elseif ($cat_waitinglist == 1) {
		echo "<input type=\"submit\" id=\"submit\" name=\"submit\" value=\"$phrase[150]\">
	<input type=\"hidden\" name=\"status\" value=\"2\">
	";}
    }
         echo "      
   
         <input type=\"hidden\" name=\"ev_id\" value=\"$event_id\">
                <input type=\"hidden\" name=\"update\" value=\"addbooking\">
    </div></div>
         
         <div style=\"float:left;text-align:left;width:50%;\">
        <div style=\"border:solid 1px black;padding:1em\">
         
         
<b>$event_name</b><br><br>
$event_description<br><br>
<b>$phrase[962]</b><br>$displaytime $displaydate<br><br>


<b>$phrase[806]</b><br>";
         
      
					
					if (key_exists($event_location,$branches)) {echo "$branches[$event_location]<br>";}
					else {
					
						if ($event_location != "") {echo "$event_location<br> ";}
						}
         
         echo "<br>
<b>$phrase[126]</b><br>";

if ($event_cost == 0 || $event_cost == "") {echo $phrase[963];} else {echo $moneysymbol.$event_cost;}
         
         
         if ($cat_takesbookings == 1 && $maxbookings != 0 && $total >= $maxbookings)
	{
		echo "<div style=\"color:#ff6666;font-size:large;\">$phrase[147]</div>";
	}
         

	echo "</div></div>
     </form>
     
      <script type=\"text/javascript\">
                                                                                                                    
                            
            function showgroup()
            {
       		document.getElementById('submit').disabled = true;
			document.getElementById('groupmenu').style.display = 'block';
            //alert('showing')
            }           
                                 
            window.onload=showgroup
                   

            
                       
                
                                 
            function _required()
            {
			//alert(\"checking\")
            var formInputs = document.getElementsByTagName('input');
			var disabled = false;
    		for (var i = 0; i < formInputs.length; i++) 
    			{
				var theInput = formInputs[i];
		
				if (theInput.type == 'text' && theInput.className == 'required')
					{
					var warning =  theInput.id + '_w';
					
					if ( theInput.value == '') 
						{
						//alert(\"BD \" + warning)
						disabled = true;
						document.getElementById(warning).style.display = 'inline';
						}
						
					else {
					//alert(\"OK \" + warning)
						document.getElementById(warning).style.display = 'none';
						}
					}
				}
				
				";
				
			
			if ($cat_age == 1)
				{
						echo "
			var selectMenus = document.getElementsByTagName('select');	
			for (var i = 0; i < selectMenus.length; i++) 
    			{
				var menu = selectMenus[i];
				var warning =  menu.id + '_w';
				//alert(menu.id)
				
				if (menu.className == 'age')
				{
				
				//alert(menu.options[menu.selectedIndex].value)	
				
					if (menu.options[menu.selectedIndex].value == \"noselection\")
					{
					//alert(\"nothing selected\")
					//alert(menu.options[menu.selectedIndex].value)
					disabled = true;
					document.getElementById(warning).style.display = 'inline';
    				}
    				else
    				{
    				//alert(\"selected\")
					document.getElementById(warning).style.display = 'none';
    				}	
    			}					
    			}

				";
				}
	
			echo "
			
			if (disabled == true) {document.getElementById('submit').disabled = true;}
			else {document.getElementById('submit').disabled = false;}
				
				
            }         
								
	

			function copy(id)
			{
		
			document.getElementById('ln_' + id).value = document.getElementById('ln_0').value
			document.getElementById('t_' + id ).value = document.getElementById('t_0').value
			
			}		
			function replace() {
			
			
			
			if (document.getElementById('replace') && document.getElementById('group'))
			{
			
			var lname =  document.getElementById('ln_0').value
			var telephone =  document.getElementById('t_0').value
			var firstname =  document.getElementById('fn_0').value
			var comments;
			var address;
			
			if (document.getElementById('comments_0'))
			{comments = document.getElementById('comments_0').value;} else {comments = '';}
			
				
			if (document.getElementById('address_0'))
			{address = document.getElementById('address_0').value;} else {address = '';}
			
			if (document.getElementById('age_0'))
			{
			
			
			var agemenu = document.getElementById('age_0');
			var selectedAge = agemenu.options[agemenu.selectedIndex].value
			}
		
			
		
    		 var html = '';
    		 
    		
    		 var places = document.getElementById('group').value
    		 
    		for (var i=0;i<places;i++)
    		{
    		var displaycount = i + 1;
    		html = html + '<div style=\"border:1px solid grey;margin:0 1em 1em 0;padding:0.5em\"><b>' + displaycount + '</b>\\n';
    	
    		html = html + '<br>\\n';
    		html = html + '<p><b>$phrase[130]</b><br><INPUT type=\"text\" name=\"firstname[' + i + ']\"';
			if (i == 0) { html = html + ' value=\"' + firstname + '\" ';}
    		html = html + 'id=\"fn_' + i + '\" maxlength=\"50\" onchange=\"_required()\" onkeyup=\"_required()\" class=\"required\"> <span style=\"color:red\" id=\"fn_' + i + '_w\">$phrase[110]</span></p>\\n';
			html = html + '<p><b>$phrase[131]</b><br><INPUT type=\"text\" name=\"lastname[' + i + ']\" value=\"' + lname + '\" id=\"ln_' + i + '\"   maxlength=\"50\" onchange=\"_required()\" onkeyup=\"_required()\" class=\"required\"> <span style=\"color:red\" id=\"ln_' + i + '_w\">$phrase[110]</span><br></p>\\n';
			if (document.getElementById('age_0'))
			
				{
    			html = html + '<p><b>$phrase[133]</b><br><select class=\"age\" name=\"age[' + i + ']\" id=\"age_' + i + '\" onchange=\"_required()\"><option value=\"noselection\">$phrase[885]</option>';
    			
				
			var minage = $minage;
			var maxage = $maxage;
			
			while (minage <= maxage)
					{ 
					html = html + '<option value=\"' + minage + '\"';
					
					if (typeof(selectedAge) != 'undefined' && selectedAge == minage && i == 0) {html = html + ' selected';}
					html = html + '>' + minage + '</option>\\n';
					minage++;
					}
			
			
    			
    			html = html + '</select> <span style=\"color:red\" id=\"age_' + i + '_w\">$phrase[110]</span></p>\\n';
				
				
				}
			else
				{
				html = html + '<INPUT type=\"hidden\" name=\"age[' + i + ']\"  value=\"0\">\\n';
				}
				
			html = html + '<p><b>$phrase[132]</b><br><INPUT type=\"text\" name=\"telephone[' + i + ']\" value=\"' + telephone + '\" id=\"t_' + i + '\"  maxlength=\"20\" onchange=\"_required()\" onkeyup=\"_required()\" class=\"required\"> <span style=\"color:red\" id=\"t_' + i + '_w\">$phrase[110]</span></p>\\n';
			
			if (document.getElementById('address_0'))
			
				{
    			html = html + '<p><b>$phrase[134]</b><br><textarea cols=\"40\" rows=\"5\" id=\"address_' + i + '\" name=\"address[' + i + ']\" >' + address + '</textarea></p>\\n';
				}
			else
				{
				html = html + '<INPUT type=\"hidden\" name=\"address[' + i + ']\"></textarea>\\n';
				}	
				
			if (document.getElementById('comments_0'))
		
				{
    			html = html + '<p><b>$phrase[135]</b><br><textarea cols=\"40\" rows=\"5\" id=\"comments_' + i + '\" name=\"comments[' + i + ']\">' + comments + '</textarea></p>\\n';
				}
			else
				{
				html = html + '<INPUT type=\"hidden\" id=\"comments_' + i + '\" value=\"\">\\n';
				}
			
			html = html + '</div>\\n';
    		}
    		
     		
			document.getElementById('replace').innerHTML = html;
			_required();
			}
			
			}





</script>
     
     
     
     
     
     ";
}
       

}

elseif ($EVENTAUTHENTICATION != "autoaccept")
{

	echo " | <a href=\"webcal.php?action=logout\">$phrase[275]</a>
	<h2>$phrase[961]</h2>";
	
	
	
	if (isset($ERROR)) {echo "<p style=\"font-size:150%;color:red\">$ERROR</p>";}
	
	
	
	
	$now = time();
	
	$username = $DB->escape($_SESSION['eventlogin']);
	
	$sql = "select bookingno, firstname, lastname, event_name, event_start, event_location, status, cancelled from cal_events, cal_bookings
	where cal_bookings.eventno = cal_events.event_id and event_start > $now
	and cardnumber = '$username' order by bookingno";
	//echo $sql;
	$DB->query($sql,"myevent.php");
	$num = $DB->countrows();
	
	if ($num == 0)
	{
	echo "$phrase[974]";	
	}
	else 
	{
		
	echo "
	<span class=\"hide\"><a href=\"javascript:window.print()\">$phrase[250]</a></span>
	<p style=\"clear:both;\"></p>
	<table id=\"resulttable\" style=\"clear:both;\">";	
		
	}
	
	while ($row = $DB->get())
	{
	$bookingno = $row["bookingno"];
	$event_name = $row["event_name"];
	$firstname = $row["firstname"];
	$lastname = $row["lastname"];
	$event_start = $row["event_start"];
	$displaydate = strftime("%a %x", $row["event_start"]);
	$displaytime = date("g:i a", $row["event_start"]);
	$event_location = $row["event_location"];
	$cancelled = $row["cancelled"];
	$status = $row["status"];
	$bookingno = $row["bookingno"];
	$bookingno = $row["bookingno"];
	
	echo "<tr><td>$firstname $lastname</td><td>$event_name</td><td>$displaytime $displaydate</td><td>";
	
	if (key_exists($event_location,$branches)) {echo "$branches[$event_location]";}
					else {
					
						if ($event_location != "") {echo "$event_location ";}
						}
	echo "</td><td>";
	
	if ($cancelled == 1)
	{
		echo $phrase[120];
	}
	elseif ($status == 3)
	{
		echo $phrase[772];
	}
	elseif ($status == 2)
	{
		echo "$phrase[632] <a href=\"myevent.php?event=cancel&bookingno=$bookingno\">$phrase[670]</a>";
	}
	else
	{
	echo "<a href=\"myevent.php?event=cancel&bookingno=$bookingno\">$phrase[670]</a>";
	}
	
	echo "</td></tr>";
	}
	if ($num != 0) {echo "</table>";}
	
	
}
 	
 

?>
</div>

</body>
</html>
