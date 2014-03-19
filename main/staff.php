<?php

$OPENING = '09';
$CLOSING = '20';


$hourdisplay = array('1 am','2 am','3 am','4 am','5 am','6 am', '7 am', '8 am','9 am','10 am','11 am', '12', '1 pm', '2 pm', '3 pm', '4 pm', '5pm',
		'6 pm', '7 pm', '8 pm', '9 pm', '10 pm', '11 pm','12 am');
$hoursvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12', '13', '14', '15', '16', '17','18', '19', '20', '21', '22', '23','24');

$minutesvalue = array('0','15','30','45');

$monthsvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12');

$daysvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12', '13', '14', '15', '16', '17',
		'18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31');

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$ip = ip("pc");
$proxy = ip("proxy");

$integers[] = "staffnum";
$integers[] = "t";
$integers[] = "status";

$integers[] = "year";
$integers[] = "day";
$integers[] = "month";
$integers[] = "shiftid";


$statuslabel[0] = $phrase[416];
$statuslabel[1] = $phrase[418];


foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
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

	

if (!isset($ERROR)) 
	{
		
	$datepicker = "yes";	
		
	
		
	page_view($DB,$PREFERENCES,$m,"");	
		
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	
		include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div>";
	  	
	  	
	  		 $sql = "select staffnum, first_name, last_name from staff, user where staffnum = userid order by last_name";

	 $DB->query($sql,"staff.php");
	 $staffnum = array();
	while ($row = $DB->get())
		{
		$staffnum[] = $row["staffnum"];
		$first_name[] = $row["first_name"];
		$last_name[] = $row["last_name"];
	
		}
	  	
	  	
		
		
		if (isset($_REQUEST["user"]))
		{
			
			$user = $DB->escape($_REQUEST["user"]);
			foreach ($staffnum as $key => $id)
				 {
	 			if ($id == $user) {break;}
	 			}
		}
		elseif (in_array($_SESSION["userid"],$staffnum)) 
		{
			$user = $_SESSION["userid"];
			foreach ($staffnum as $key => $id)
	 			{
	 			if ($id == $user) {break;}
	 			}		
		}

		
		
		
		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "cancel")
		{
			
			$sql = "delete from staffday where id = '$shiftid'";	
			
			//echo $sql;
			$DB->query($sql,"staff.php");	
		
		}
		
		
		
		
		
		
		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updateshift")
		{
			
		//	$sql = "select * from staffday where id = '$shiftid'";	
			
			//echo $sql;
			//$DB->query($sql,"staff.php");	
			//$row = $DB->get();
			//$oldstart = date("g.ia d/m/Y",$row["starttime"]);
			//$oldend = date("g.ia d/m/Y",$row["endtime"]);
			//$oldstatus = $row["status"];
			
			$now = time();
			
			
			if (isset($_REQUEST["deletehours"]))
			{
			
			
//$details = "
//<b>$first_name[$key] $last_name[$key]</b>
//
//$statuslabel[$oldstatus]
//$oldstart - $oldend
//";	
				
					
			$sql = "delete from staffday where id = '$shiftid' and staffid = '$user'";	
			
			$DB->query($sql,"staff.php");	
			
			
			
			
				
			}
			
			
			if (isset($_REQUEST["updatehours"]))
			{
			
				
				
			$comments = $DB->escape($_REQUEST["comments"]);	
			$startdate = $_REQUEST["startdate"];
			$enddate = $_REQUEST["enddate"];
			$sh = $_REQUEST["sh"];
			$eh = $_REQUEST["eh"];
			//$details = $DB->escape($_REQUEST["details"]);
			
			
			
			if ($startdate != "" && $enddate != "")
			{
				if ($DATEFORMAT == "%d-%m-%Y")
             {
             $sd = substr($startdate,0,2);
             $sm = substr($startdate,3,2);
             $sy = substr($startdate,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $sd = substr($startdate,3,2);
         $sm = substr($startdate,0,2);
         $sy = substr($startdate,6,4);
             }	


if ($DATEFORMAT == "%d-%m-%Y")
             {
             $ed = substr($enddate,0,2);
             $em = substr($enddate,3,2);
             $ey = substr($enddate,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $ed = substr($enddate,3,2);
         $em = substr($enddate,0,2);
         $ey = substr($enddate,6,4);
             }
             
             $starttime = mktime($sh,0,0,$sm,$sd,$sy);
             $endtime = mktime($eh,0,0,$em,$ed,$ey);
			}	
				
			
			
		//	$newstart = date("g.ia d/m/Y",$starttime);
		//	$newend = date("g.ia d/m/Y",$endtime);
				
			
//$details = "
//<b>$first_name[$key] $last_name[$key]</b>
//UPDATED
//FROM
//$statuslabel[$oldstatus]
//$oldstart - $oldend
//TO
//$statuslabel[$status]
//$newstart - $newend
//";	
			$now = time();	
					
			$sql = "update staffday set starttime = '$starttime',details = '$comments', endtime = '$endtime',status = '$status',added = '$now' where id = '$shiftid' and staffid = '$user'";
			//echo $sql;	
			$DB->query($sql,"staff.php");	
				
			
			
			}
			
		//	$details = $DB->escape($details);
		//	$sql = "insert into staffday_log values(NULL,'$now','$details');";
			//echo $sql;	
		//	$DB->query($sql,"staff.php");
			
			
		}
		
		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "add")
		{
			$now = time();
			
			$start = array();
			$end = array();
			
			$startdate = $_REQUEST["startdate"];
			$enddate = $_REQUEST["enddate"];
			$comments = $DB->escape($_REQUEST["comments"]);
			
			
			
			if ($startdate != "" && $enddate != "")
			{
				if ($DATEFORMAT == "%d-%m-%Y")
             {
             $sd = substr($startdate,0,2);
             $sm = substr($startdate,3,2);
             $sy = substr($startdate,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $sd = substr($startdate,3,2);
         $sm = substr($startdate,0,2);
         $sy = substr($startdate,6,4);
             }	


if ($DATEFORMAT == "%d-%m-%Y")
             {
             $ed = substr($enddate,0,2);
             $em = substr($enddate,3,2);
             $ey = substr($enddate,6,4);
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $ed = substr($enddate,3,2);
         $em = substr($enddate,0,2);
         $ey = substr($enddate,6,4);
             }
             
             $start[] = mktime($OPENING,0,0,$sm,$sd,$sy);
             $end[] = mktime($CLOSING,0,0,$em,$ed,$ey);
			}
			
			
		
		//	print_r($_REQUEST);
			
			if (isset($_REQUEST["date"]))
			{
			$date = $_REQUEST["date"];
			
			foreach ($date as $index => $value)
					{
					if (strlen($value) == 10)	
					{ 
				//	echo "hello dates";	
			$start = array();
			$end = array();	
						break;	
					}	
					}
				
			
			$sh = $_REQUEST["sh"];
			$eh = $_REQUEST["eh"];
			$sminute = $_REQUEST["sm"];
			$eminute = $_REQUEST["em"];
			
			foreach ($date as $index => $value)
					{
					if (strlen($value) == 10)	
					{	
					$starthour = $sh[$index];
					$endhour = $eh[$index];	
					
			if ($DATEFORMAT == "%d-%m-%Y")
             {
             $sd = substr($value,0,2);
             $sm = substr($value,3,2);
             $sy = substr($value,6,4);
             }

     		if ($DATEFORMAT == "%m-%d-%Y")
             {
         $sd = substr($value,3,2);
         $sm = substr($value,0,2);
         $sy = substr($value,6,4);
             }	


			if ($DATEFORMAT == "%d-%m-%Y")
             {
             $ed = substr($value,0,2);
             $em = substr($value,3,2);
             $ey = substr($value,6,4);
             }

    	 if ($DATEFORMAT == "%m-%d-%Y")
             {
         $ed = substr($value,3,2);
         $em = substr($value,0,2);
         $ey = substr($value,6,4);
             }
					
			$start[] = mktime($starthour,$sminute[$index],0,$sm,$sd,$sy);
             $end[] = mktime($endhour,$eminute[$index],0,$em,$ed,$ey);	
					}
					}
			}
			
			
			
			foreach ($start as $index => $_start)
					{
					$_start = $DB->escape($_start);
					$_end = $DB->escape($end[$index]);	
					
					$sql = "insert into staffday values(NULL,'$user','$_start','$comments','$status','$_end','0','$now');";	
					
					$DB->query($sql,"staff.php");
					
					/*
					$newstart = date("g.ia d/m/Y",$_start);
					$newend = date("g.ia d/m/Y",$_end);
				
			
$details = "
<b>$first_name[$key] $last_name[$key]</b>
ADDED
$statuslabel[$status]
$newstart - $newend
";	
$now = time();
				$details = $DB->escape($details);
			$sql = "insert into staffday_log values(NULL,'$now','$details');";
			//echo $sql;	
			$DB->query($sql,"staff.php");	
				*/	
					
					}
			
			
		}
		
		
		if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit")
		{
			
		
			$sql = "select * from staffday where id = '$shiftid' and staffid = '$user'";
			//echo $sql;
			$DB->query($sql,"staff.php");
	
			$row = $DB->get();
				
			$status = $row["status"];
			$comments = $row["details"];
			
				$sd = date("d",$row["starttime"]);
				$sm = date("m",$row["starttime"]);
				$sy = date("Y",$row["starttime"]);
				$sh = date("H",$row["starttime"]);
				$ed = date("d",$row["endtime"]);
				$em = date("m",$row["endtime"]);
				$ey = date("Y",$row["endtime"]);
				$eh = date("H",$row["endtime"]);
				
					if ($DATEFORMAT == "%d-%m-%Y")
             {
            $startdate = $sd . "-"  . $sm . "-" . $sy;
            $enddate = $ed . "-"  . $em . "-" . $ey;
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
        	$startdate = $sm . "-"  . $sd . "-" . $sy;
            $enddate = $em . "-"  . $ed . "-" . $ey;
             }	
			
					
				
			echo "<h1>$modname</h1><h2>$phrase[16]</h2><form action=\"staff.php\" method=\"post\">
			<table>
			<tr><td><b>$phrase[141]</b></td><td>$first_name[$key] $last_name[$key]</td></tr>
			<tr><td><b>$phrase[418]</b></td><td> <select name=\"status\">
			<option value=\"1\">$phrase[418]</option><option value=\"0\"";
			if ($status == 0) {echo " selected";}
			echo ">$phrase[416]</option>
			
			</select></td></tr>
				<tr><td><b>$phrase[135]</b></td><td><textarea name=\"comments\" cols=\"40\" rows=\"5\">$comments</textarea></td></tr>
			<tr><td><b>$phrase[242]</b></td><td><input type=\"text\"  name=\"startdate\" id=\"startdate\" value=\"$startdate\"  readonly class=\"datepicker\" size=\"10\">
			
			<select name=\"sh\">";
			foreach ($hourdisplay as $key => $value)
			{
				echo "<option value=\"$hoursvalue[$key]\""; if ($hoursvalue[$key]== $sh) {echo " selected";}echo ">$value</option>";
			}

			
			echo "</select> 
			
			
			</td></tr>
			<tr><td><b>$phrase[243]</b></td><td><input type=\"text\"  name=\"enddate\" id=\"enddate\" value=\"$enddate\"  readonly class=\"datepicker\" size=\"10\">
			<select name=\"eh\">";
			foreach ($hourdisplay as $key => $value)
			{
				echo "<option value=\"$hoursvalue[$key]\""; if ($hoursvalue[$key]== $eh) {echo " selected";}echo ">$value</option>";
			}

			
			echo "</select> </td></tr>
			</table>
		
			
			<script type=\"text/javascript\">
				
				datepicker('startdate');
				datepicker('enddate');
				
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
	
				
				
				</script>
				<input type=\"hidden\" name=\"shiftid\" value=\"$shiftid\">
				<input type=\"hidden\" name=\"m\" value=\"$m\">
		
				<input type=\"hidden\" name=\"user\" value=\"$user\">
				<input type=\"hidden\" name=\"update\" value=\"updateshift\"><br><br>
				<input type=\"submit\" name=\"updatehours\" value=\"Update\">
				<input type=\"submit\" name=\"deletehours\" value=\"Delete\" style=\"margin-left:4em\">
				
				</form>
			
			";
			
			
			
			
		}
		
		elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "add")
		{
			
			if (!isset($day)) {$day = date("d");}
			if (!isset($month)) {$month = date("m");}
			if (!isset($year)) {$year = date("Y");}
			
			
				if ($DATEFORMAT == "%d-%m-%Y")
             {
             $date = "$day-$month-$year";
             }

     if ($DATEFORMAT == "%m-%d-%Y")
             {
         $date = "$month-$day-$year";
             } else { $date = "$day-$month-$year";}
			
			
			echo "<h1>$modname</h1>
			
				<a href=\"staff.php?m=$m&amp;user=$user\">$phrase[642]</a> | <a href=\"staff.php?m=$m&amp;event=cal&amp;user=$user\">$phrase[427]</a><br><br>
			
			<b>Part time employee </b>- Scheduler already has your everyday roster recorded so whenever you complete any availability update information you do not include your everyday roster. Schedulers default position is that unless you tell us otherwise you are always available for additional hours work outside your everyday roster.
<br>
<br>
<b>Casual employee</b> - Scheduler�s default position is that unless you tell us otherwise you are always available for additional hours work.

<br>
<br>
<b>All</b> - If you are assigned a shift by Roster Support you do not need to fill in a Not Available form.
<br>
<br>
			
			
			
			<form action=\"staff.php\" method=\"post\">
			<table>
			<tr><td><b>$phrase[141]</b></td><td>$first_name[$key] $last_name[$key]</td></tr>
			<tr><td><b>$phrase[418]</b></td><td> <select name=\"status\">
			<option value=\"0\">$phrase[416]</option>
			<option value=\"1\">$phrase[418]</option>
			
			</select></td></tr>
			
			
			<tr><td><b>Dates</b></td><td>
			
		
			
			
			<span onclick=\"addday();return false;\"><img src=\"../images/add.png\" alt=\"$phrase[176]\" title=\"$phrase[176]\"><b>$phrase[1010]</b> </span>
		    <div id=\"dates\"></div>
				<br><b>$phrase[1042]</b><br><br>
		<div onclick=\"cleardates()\">
		<input type=\"text\"  name=\"startdate\" id=\"startdate\"   readonly class=\"datepicker\" size=\"10\" > <span style=\"width:100px;display:inline-block\">$phrase[267] </span> <br>
			<input type=\"text\"  name=\"enddate\" id=\"enddate\"   readonly  class=\"datepicker\" size=\"10\" > <span style=\"width:100px;display:inline-block\">$phrase[268] </span> </div>
		
			
			</td>
			
			</tr>
			
	<tr><td><b>$phrase[135]</b></td><td><textarea name=\"comments\" cols=\"40\" rows=\"5\"></textarea></td></tr>
			<tr><td></td><td>	<input type=\"hidden\" name=\"user\" value=\"$id\">
			<input type=\"hidden\" name=\"m\" value=\"$m\">
			<input type=\"hidden\" name=\"month\" value=\"$month\">
			<input type=\"hidden\" name=\"year\" value=\"$year\">
			<input type=\"hidden\" name=\"update\" value=\"add\">
			<input type=\"submit\" value=\"submit\"></td></tr>
			</table>
				<script type=\"text/javascript\">
				
				datepicker('startdate');
				datepicker('enddate');
				
				
				var rowcount = 0;
				
				function addday()
				{
				
				html = '<div id=\"div' + rowcount + '\" style=\"margin:0.5em:z-index:1000\"><span onclick=\"clear_range()\"> <input type=\"text\"  id=\"d' + rowcount + '\"  name=\"date[' + rowcount + ']\" size=\"10\" readonly value=\"$date\"></span> <select name=\"sh[' + rowcount + ']\">";
			foreach ($hourdisplay as $key => $value)
			{
				echo "<option value=\"$hoursvalue[$key]\""; if ($hoursvalue[$key]== "09") {echo " selected";}echo ">$value</option>";
			}

			
			echo "</select><select name=\"sm[' + rowcount + ']\"><option value=\"00\">00</option><option value=\"30\">30</option></select> to <select name=\"eh[' + rowcount + ']\">";
			foreach ($hourdisplay as $key => $value)
			{
				echo "<option value=\"$hoursvalue[$key]\""; if ($hoursvalue[$key]== 20) {echo " selected";}echo ">$value</option>";
			}

			
			echo "</select><select name=\"em[' + rowcount + ']\"><option value=\"00\">00</option><option value=\"30\">30</option></select><span onclick=\"HideDiv(\'div' + rowcount +'\');return false;\" style=\"position:relative;margin-left:5px\"> <img src=\"../images/cross.png\" alt=\"$phrase[24]\" title=\"$phrase[24]\" style=\"position:absolute;top:-4px;\"></span></div>';
				
				
				
				var date_values = new Array();
				var date_ids = new Array();
				
				if (document.getElementById('dates').getElementsByTagName('input'))
				{
				var inputs = document.getElementById('dates').getElementsByTagName('input');
				var count = 0;
			  for(i=0;i<inputs.length;i++){
    			
			  var temp = inputs[i].id;
			  
			  //alert('temp is ' + temp)
			  date_values[count] = inputs[i].value;
			  date_ids[count] = inputs[i].id;
			  count++;
  			}
			  }
				//alert(count)
				
				var dates = document.getElementById('dates');
				
				//dates.innerHTML = dates.innerHTML + html;
				
				var div = document.createElement('div')
				div.innerHTML = html;
				//alert(div.innerHTML)
				dates.appendChild(div);   
				
			
				
				
				datepicker('d' + rowcount);
				
				rowcount++;
				clear_range()
				}
					
		function DisableFormFields(myDiv,xHow)
			{
	
			var inputs = document.getElementById(myDiv).getElementsByTagName('input');
			  for(i=0;i<inputs.length;i++){
    			inputs[i].disabled = true;
  			}
			}
		function HideDiv(id)
				{
				DisableFormFields(id);
				var div =document.getElementById(id);
		
				div.style.display = 'none';
				}

				
		function cleardates()
		{
		
		var divs = document.getElementById('dates');
		divs.innerHTML = \"\";
		
		
		
		
		
		
		}	
				
					
				
	   function clear_range()
			{
//return;
			var start;
			var end;
			
				if (document.getElementById('dates').getElementsByTagName('input'))
				{
				var inputs = document.getElementById('dates').getElementsByTagName('input');
				var count = 0;
			  for(i=0;i<inputs.length;i++)
			  {
    			//alert(inputs[i].value);
			  if (count == 0) {
			 					 start = inputs[i].value;
			 					 end = inputs[i].value;
			  					}
			  else
			  				{
			 				 end = inputs[i].value;
			  
			  				}
			
			  count++;
  			}
			  }
			
			
			//alert(start)
			
			document.getElementById('startdate').value = start;
			document.getElementById('enddate').value = end;
			document.getElementById('startdate').value = '';
			document.getElementById('enddate').value = '';
			}	
				
			
			
			function clear_datesdiv()
			{
			var dates = document.getElementById('dates');
				
			dates.innerHTML = '';
			}
				
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
	
				
				addday();
				</script>
			</form>";
			
		}
		
		
		elseif (isset ($user))
		{
	
			
			if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "cal")
			{
			
				
			
				
				
				
				
				if (!isset($_REQUEST["t"]))
	{

	if (isset($_REQUEST["month"]))
	{
		$t = mktime(0, 0, 0, $_REQUEST["month"], 1, $_REQUEST["year"]);

	}
	else {
		$t = time();
	}
	} else {$t = $_REQUEST["t"];}
			
				
	$display = strftime("%B %Y", $t);
$day = date("d",$t);
$month = date("m",$t);
$monthname = date("F",$t);
$year = date("Y",$t);  
$daysinmonth = date("t",$t);  
 //$weekday = date("w");   
 
 $thisday = date("d");
$thismonth = date("m");
$thisyear = date("Y"); 

   
//$t = mktime(0, 0, 0, 01, 31,  $year);

$lastmonth = mktime(0, 0, 0, $month -1, 01,  $year);
$nextmonth = mktime(0, 0, 0, $month +1, 01,  $year);
  

   
   $fd  = mktime(0, 0, 0, $month  , 01, $year);
   $fd = date("w",$fd);
   $_fd = $year . $month . "01";
   $_fd = "$year-$month-01";
   //echo "fd is $fd<br>";
   
   $ld  = mktime(0, 0, 0, $month  , $daysinmonth, $year);
   $ld = date("w",$ld);
   $_ld = $year . $month . $daysinmonth;
    $_ld = "$year-$month-$daysinmonth";
   //echo "ld is $ld";		
			

   
   
   //get public holidays and put in array
		if ($DB->type == "mysql")
			{		
			$sql = "select name,date_format(holiday, '%Y%m%d') as holiday from holidays ";	
			}
		if ($DB->type == "sqlite")
			{
			$sql = "select name,strftime('%Y%m%d',holiday) as holiday from holidays ";		
			}
		//$sql = "select date_format(holiday, '%D %b %Y') as fholiday, date_format(holiday, '%Y%m%d') as holiday, name from holidays ";	
		
		$DB->query($sql,"calendar.php");
		$num = $DB->countrows();
		
		while ($row = $DB->get()) 
		{
			
		$a_holiday[] = $row["holiday"];
	
		$a_name[] = formattext($row["name"]);
		}
		
		

   
     $sql = "select details, shift, status from staffday
 	where ((staffday >= '$_fd' and staffday <= '$_ld') )
   and staffnum = '$user'
   ";
   
     
     
 

  $monthstart =  mktime(0, 0, 0, $month,1, $year);
	    $monthend =  mktime(0, 0, 0, $month + 1,1, $year);
		
   $sql = "select * from staffday 
   where staffid = '$user'
   and  ((starttime >= '$monthstart' and starttime < '$monthend')
   or   (endtime > '$monthstart' and endtime <= '$monthend') 
   or   (starttime <= '$monthstart' and endtime >= '$monthend'))  order by starttime ";
	$DB->query($sql,"staff.php");
	//echo $sql;
	
	$status = array();
	$start = array();
	$end = array();
	$endtime = array();
	$starttime = array();
	$enddate = array();
	$startdate = array();
	$ed = array();
	$sd = array();
	$shiftid = array();
	$details = array();
			
	
	$i = 1;
	while ($row = $DB->get()) 
		{
			$shiftid[$i] =$row["id"];
			$status[$i] =$row["status"];		
			$details[$i] =$row["details"];	
			
			$start[$i] = $row["starttime"];
			$end[$i] = $row["endtime"];
			
			$starttime[$i] = date("g:ia",$row["starttime"]);
			$endtime[$i] = date("g:ia",$row["endtime"]);
			
			$startdate[$i] = strftime("%x",$row["starttime"]);
			$enddate[$i] = strftime("%x",$row["endtime"]);
			
			$sd[$i] = date("Ymd",$row["starttime"]);
			$ed[$i] = date("Ymd",$row["endtime"]);
			
			$i++;
			}


			

			
		$_staff = array();
		$_comment = array();	
				
		$sql = "select * from stafftemplate where staff = '$user'";	
		$DB->query($sql,"staff.php");
		while ($row = $DB->get())
		{
		$day = $row["dayname"];
		
		$comment = $row["comment"];
		
	
		$_comment[$day] = $comment;	
		}
			
	
		
  echo "<h1>$modname</h1>
  
<a href=\"staff.php?m=$m&amp;event=add&amp;user=$user\">$phrase[176]</a> | <a href=\"staff.php?m=$m&amp;user=$user\">$phrase[642]</a><br><br>	
  
  <table  style=\"margin-left:2px\" class=\"colourtable\" id=\"calendar\" cellpadding=\"3\">
 <tr class=\"accent\"><td style=\"text-align:left\" valign=\"bottom\"> 
 <a href=\"staff.php?m=$m&amp;user=$user&amp;t=$lastmonth&amp;event=cal\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" >
   
   

   <form style=\"display:inline;\" action=\"staff.php\" method=\"get\">
   <select name=\"month\" style=\"font-size:1.5em\">";
$counter = 1;
while ($counter < 13)
{
	$monthname = strftime("%b",mktime(0,0,0,$counter,01,$year));
	echo "<option value=\"$counter\"";
	if ($counter == $month) { echo " selected";}
	echo ">$monthname</option>";
	$counter++;
	
}

$displayyear = $year -2;
$endyear = $year +2;

echo "</select><select name=\"year\" style=\"font-size:1.5em\">";
while ($displayyear <= $endyear)
{
	echo "<option value=\"$displayyear\"";
	if ($displayyear == $year) { echo " selected";}
	echo ">$displayyear</option>";
	$displayyear++;
	
}


echo "</select>
   
       <input type=\"hidden\" name=\"m\" value=\"$m\">
       <input type=\"hidden\" name=\"event\" value=\"cal\">
        <input type=\"hidden\" name=\"user\" value=\"$user\">
     <input type=\"submit\" value=\"Go\" style=\"font-size:1.5em\">
   
   </form>";

	 
		
		echo "<br><br><span style=\"font-size:1.6em\">$first_name[$key] $last_name[$key]</span>";

echo "</td>
   <td style=\"text-align:right\" valign=\"middle\">
   <a href=\"staff.php?m=$m&amp;user=$user&amp;t=$nextmonth&amp;event=cal\" class=\"hide\">$phrase[155]</a> </td></tr>
   
   "; 
   

if (isset($CALENDAR_START) && $CALENDAR_START =="MON") {$cal_offset = 1;} else {$cal_offset = 0;}

   //display blank cells at start of month
   $counter = 0 + $cal_offset;
    if ($cal_offset == 1 && $fd == 0) $fd=7; 
   if ($fd <> 0 + $cal_offset)
   	{
	echo "<tr >";
	}
   while ($counter < $fd)
   	{
	echo "<td>";
	
	
	
	echo "</td>";
	if ($counter == 6 + $cal_offset)
		{
		echo "</tr>\n";
		}
	$counter++;
	}
   
   if (isset($CALENDAR_START) && $CALENDAR_START =="MON") {$cal_offset = 1;} else {$cal_offset = 0;}
   
   
   //display month as table cells
   $daycount = 1;
   $displayholiday = 0;
   while ($daycount <= $daysinmonth)
   	{
	$endline = (($counter + $daycount - $cal_offset) % 7);
	$day = str_pad($daycount, 2, "0", STR_PAD_LEFT);
	$weekday  = date("w",mktime(0, 0, 0, $month, $daycount,  $year));
	$dayname  = strftime("%a",mktime(0, 0, 0, $month, $daycount,  $year));
	$t = mktime(0, 0, 0, $month, $day,  $year);
	if ($endline == 1) { echo "<tr>";}
	echo "<td valign=\"top\"";
	if ($thisyear.$thismonth.$thisday == $year.$month.$day)
				{
				echo " id=\"scrollpoint\" class=\"accent\"";
				$displayholiday = 1;
				}
				
				
	
	
	echo "><span style=\"font-size:large\"><b>$daycount</b></span>&nbsp;&nbsp; $dayname<br><br>";
	
	if(isset($a_holiday))
		{
		foreach ($a_holiday as $index => $holiday)
			{
			
			if ($holiday == $year.$month.$day)
				{
				echo "<span style=\"background:#FFCF4F;padding:0.2em\">$a_name[$index]</span><br><br>";
				}
			}
		}
	
		if (key_exists($weekday,$_comment)) {echo "$_comment[$weekday]<br>";}
		
		
		
		
	if (isset($pname))
	{	
	foreach ($pname as $id => $name)
		{
			
		$today = mktime(0, 0, 0, $month, $daycount,  $year);
		$gapindays = ($today - $pstart[$id]) /86400;
		//echo "gap is $gapindays";	
		if ($gapindays % $pdays[$id] == 0)
		{ echo "<span style=\"background:#$pcolour[$id];padding:0.2em\">$name</span><br><br>";}
		
		}
	}	
		
		
		
	$date =  ($year.$month.$day) * 1;
		
	//display events for today
	if (isset($status))
	{	

	
		
	foreach ($status as $index => $_status) 
		{
		
			$test = $start[$index];
			while ($test < $end[$index])
				{
				$testday  = date("j",$test);
				$testmonth  = date("n",$test);
				$testyear  = date("Y",$test);
				
			
				$testdate  = date("Ymd",$test);
				
				if (($testday == $daycount) && ($month == $testmonth) && ($year == $testyear))
					{
					
									echo "<a href=\"staff.php?m=$m&amp;event=edit&amp;shiftid=$shiftid[$index]&amp;t=$t&amp;user=$user\" title=\"$details[$index]\"";
									
									
									//	if ($startdate[$index] != $enddate[$index])
									//	{ echo " title=\"$startdate[$index] - $enddate[$index]\"";}
									
								
				
									
									$spanstyle="";
									
									if ($_status == 1)
									{
										
										echo " style=\"color:green\">Available</a>"; $spanstyle = " style=\"color:green\"";
										
							
									}
									else {echo " style=\"color:red\">Not available</a>"; $spanstyle = " style=\"color:red\"";}
									
									
									
		
					
					
					
					if ($startdate[$index] == $enddate[$index])
					{
					echo "<br><span $spanstyle> $starttime[$index] - $endtime[$index]</span>";
					}
				elseif  ($sd[$index] == $testdate)
				{
					echo "<br><span $spanstyle> $starttime[$index] &gt;</span>";
					
					}			 
					elseif  ($ed[$index] == $testdate)
				{
					echo "<br><span $spanstyle> &lt;  $endtime[$index]</span>";
					
					}
					
					elseif  ($sd[$index] != $testdate && $ed[$index] != $testdate)
				{
					echo "<br><span $spanstyle>$phrase[1016] </span>";
				}
						
					
					
					
					
	
						
						
						
				
						echo "<br><br>";
					}
				$test = mktime(0, 0, 0, $testmonth, $testday + 1,  $testyear);
				
				}
			
				
		}
	}

	
	
	
	
	echo "<a href=\"staff.php?m=$m&amp;event=add&amp;day=$day&amp;month=$month&amp;year=$year&amp;user=$user\" class=\"block\">&nbsp;</a></td>";
	
   				
				
				
			   if ( $endline == 0)
			   	{
				echo "</tr>\n";
				}
				$daycount++;
   }
   
   //displays blank cells at end of month
   
   if ($endline <> 0)
   	{
	while ($endline < (7 - $cal_offset))
		{
		echo "<td></td>";
		
		if ($endline == (7 - $cal_offset))
		{
		echo "</tr>";
		}
	$endline++;
		
		
		
		}
	
	
	}
   echo "
    <tr class=\"accent\"><td style=\"text-align:left\" valign=\"bottom\">   <a href=\"staff.php?m=$m&amp;user=$user&amp;t=$lastmonth&amp;event=cal\" class=\"hide\">$phrase[154]</a>
   </td><td colspan=\"5\" style=\"text-align:center\" ><span style=\"font-size:150%\"> $display</span></td>
   <td style=\"text-align:right\" valign=\"middle\">      <a href=\"staff.php?m=$m&amp;user=$user&amp;t=$nextmonth&amp;event=cal\" class=\"hide\">$phrase[155]</a> </td></tr>
   </table>";	
			
		}
		else 
		
		{
			
			
			
		$processlabel[0] = "$phrase[1006]";	
		$processlabel[1] = "$phrase[1009]";		
			
		echo "<h1>$modname</h1><h2>$first_name[$key] $last_name[$key]</h2>
		
		<a href=\"staff.php?m=$m&amp;event=add&amp;user=$user\">$phrase[176]</a> | <a href=\"staff.php?m=$m&amp;event=cal&amp;user=$user\">$phrase[427]</a><br><br>
		
		<table class=\"colourtable\">
		<tr style=\"font-weight:bold\"><td>Start date</td><td>Finish date</td><td>Time</td><td>Change</td><td></td><td></td><td></td><td></td></tr>";	
		
		
		$sql = "select * from staffday where staffid = '$user' order by starttime";
		$DB->query($sql,"staff.php");
		while ($row = $DB->get())
		{
		$shiftid = $row["id"];	
		$startdate = date("d/m/Y",$row["starttime"]);
		$enddate = date("d/m/Y",$row["endtime"]);
		$processed = $row["processed"];
		$comment = $row["details"];
		$status = $row["status"];
		
		$starttime = date("g.ia",$row["starttime"]);
		$endtime = date("g.ia",$row["endtime"]);
		
		echo "<tr><td>$startdate</td><td>$enddate</td><td>";
		if ($startdate == $enddate) {echo "$starttime - $endtime";} else {echo "date range";}
		echo "</td><td";
		if ($status == 0) {echo " style=\"color:red\">$phrase[416]"; } else {echo " style=\"color:green\">$phrase[418]";}
		echo "</td>
		
		<td>";
		
		if (trim($comment) != "") {
		echo "<span style=\"position:relative\" onmouseover=\"showelement('c$shiftid','30')\" onmouseout=\"hideelement('c$shiftid')\"><img src=\"../images/comments.png\">
		<p class=\"textballoon\" id=\"c$shiftid\">Comments: $comment</p></span>";
		}
		echo "</td>
		
		<td ";
		if ($processed == "0") {echo " class=\"nooption\"";} else {echo " class=\"yesoption\"";}
		echo ">$processlabel[$processed] </td><td><a href=\"staff.php?m=$m&amp;event=edit&amp;shiftid=$shiftid&amp;user=$user\">Edit</a></td><td>";
		if ($processed == "0") {echo "<a href=\"staff.php?m=$m&amp;update=cancel&amp;shiftid=$shiftid&amp;user=$user\">Cancel</a>";}
		echo "</td></tr>
";
		}
		echo "</table>";
		}
			
		}
		
		
		
	//end contentbox
		echo "</div></div>";
		
	 
	 echo "<div id=\"rightsidebar\"><div>";
	 
	 if ($access->thispage > 1)
	 {
	 echo "<h2>Staff</h2><ol class=\"listing\">";
	 
	 foreach ($staffnum as $key => $id)
	 {
	echo "<li><a href=\"staff.php?m=$m&amp;user=$id\">$last_name[$key], $first_name[$key]</a></li>
";
	 }
	 
	 echo "</ol>";
	 
	
	 
	 }
	 
	 echo "</div></div>";


include ("../includes/footer.php");




	}
?>

