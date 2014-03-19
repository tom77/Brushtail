<?php
Class PdRead
{
	
	private $DB;
	private $m;
	private $year;
	private $userid;
	private $email;
	private $email_action;
	private $first_name;
	private $last_name;
	private $showprint;
	
	
	public function __construct($m,$DB)
    {
    	
   $this->m = $m;

   $this->DB = $DB;
   $this->userid = $this->DB->escape($_SESSION["userid"]);
   
   
   $sql = "select email, email_action, showprint, instructions from pd_options where m = '$m'";
	$this->DB->query($sql,"pdedit.php");
	$row = $this->DB->get()	;
	$this->email = $row["email"];
	$this->email_action = $row["email_action"];
	$this->showprint = $row["showprint"];
	$this->instructions = $row["instructions"];
   
   	if (isset($_REQUEST["year"]))
		{$this->year =  $this->DB->escape($_REQUEST["year"]);}
		else
		{$this->year = date("Y");}
   
   $year = $this->year;
   
   	$sql = "SELECT userid, first_name, last_name from user where userid = '$this->userid'";
		$this->DB->query($sql,"pdedit.php");
		while ($row = $this->DB->get()) 
					{
				
					$this->last_name = $row["last_name"];
					$this->first_name = $row["first_name"];	
				
					}
   
					
					
					
		if (isset($_REQUEST["event"])) {$event = $_REQUEST["event"];} else {$event = "";}
	if (isset($_REQUEST["filter"])) {$filter = $_REQUEST["filter"];} else {$filter = "";}				
					
					
	$random = random(5);				
					
   echo "
   
   <script type=\"text/javascript\">

	function changeyear()
	{
	var event = '$event';
	var filter = '$filter';
	var menu = document.getElementById(\"year\");
	year = menu.options[menu.selectedIndex].value ; 
	var url = 'pd.php?m=$this->m&year=' + year + '&amp;filter=' + filter + '&amp;event=' + event;
	//alert(url)
	location.href= url ;
	}
	
	</script>
   

   
   <a href=\"pd.php?m=$this->m&amp;year=$year&amp;event=addpd&amp;z=$random\">Add PD</a>  |    <a href=\"pd.php?m=$this->m&amp;year=$year&amp;z=$random\">List PD</a>";
 if ($this->showprint == 1)
 {
  echo " | <a href=\"pdprint.php?m=$this->m&amp;year=$year\">Print</a>";
 }
	echo " <form style=\"display:inline\" method=\"get\" action=\"pd.php\">
	 <select name=\"year\" id=\"year\" onchange=\"changeyear()\">";
	 
	 $counter = $year - 10;
	 while ($counter < $year + 11)
	 {
	 	echo "<option value=\"$counter\""; 
		if ($counter == $year) {echo " selected";}
		echo ">$counter</option>";
    	$counter++;
	 }
	 echo "</select>
	
	 </form>
	 <h2>$this->first_name $this->last_name</h2>
	 
	 ";

   if(isset($_REQUEST["update"]) && $_REQUEST["update"] == "updatepd") 
  {
  	$this->update_pd();
  }

  
  
   if(isset($_REQUEST["update"]) && $_REQUEST["update"] == "insertpd") 
  {
  	$this->insert_pd();
  }
 

   
    if(isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit") 
  {

  	$this->edit_pd();
  }
   
 
  elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "addpd" ) 
  {
  	
	$this->add_pd();
	}
 

  else
 
  {

  	$this->list_pd();
  }
   
   }

   private function update_pd()
   {
   	$id = $this->DB->escape($_REQUEST["pd_id"]);
	$title = $this->DB->escape($_REQUEST["title"]);
	$description = $this->DB->escape($_REQUEST["description"]);
	$external = $this->DB->escape($_REQUEST["external"]);
	$hours = $this->DB->escape($_REQUEST["hours"]);
	$cost = $this->DB->escape($_REQUEST["cost"]);
	
	$day = $this->DB->escape($_REQUEST["day"]);
	$month = $this->DB->escape($_REQUEST["month"]);
	$year = $this->DB->escape($_REQUEST["year"]);
	$replacement = $this->DB->escape($_REQUEST["replacement"]);
	$pd_category = $this->DB->escape($_REQUEST["pd_category"]);
	
	$date = $year . "-" . $month . "-" . $day;
	
	
 		$custom= $_REQUEST["custom"];
 	
 	
 		
 		$sql = "select pd_custom from pd_sessions where pd_id = '$id'";
 		
 	//echo $sql;
       	$this->DB->query($sql,"pdread.php");
		$row = $this->DB->get();
		//print_r($row);
		$custom_data =$row["pd_custom"];	
       	//echo "BBBB $custom_data BBBBBB";
       	
 		if (isset($custom))
		{
      
	
		 //create xml string fro database
		 	 	$xml_output  = "<?xml version=\"1.0\"?>\n";
			$xml_output .= "<fields>\n"; 
			
			
			
			$sql = "select * from pd_custom_fields where m = '$this->m' order by ranking";

	$counter = 0;	
						
	$this->DB->query($sql,"pdadmin.php");
	while ($row = $this->DB->get())
	{
	$field_id = $row["fieldno"]; 
	$field_label = htmlspecialchars($row["label"]);
	$field_type = $row["type"];
	$field_menu = $row["menu"];
	//$values = split("\n", $menu);
	//$comment =  htmlspecialchars($row["comment"]);
	//$custom_ranking[$counter] = $field;
			
			
			
	
			
				
			$xml_output .= "<field>\n"; 
			$xml_output .= "<field_id><![CDATA[$field_id]]></field_id>\n"; 
			$xml_output .= "<field_label><![CDATA[$field_label]]></field_label>\n"; 
			$xml_output .= "<field_value><![CDATA[$custom[$field_id]]]></field_value>\n"; 
			$xml_output .= "<menu><![CDATA[$field_menu]]></menu>\n"; 
			$xml_output .= "<type><![CDATA[$field_type]]></type>\n";
			$xml_output .= "</field>\n"; 
			
			}	
		
		
		$xml_output .= "</fields>\n"; 
		
		echo $xml_output;
		
		$custom_data = $this->DB->escape($xml_output);
		
	
		
		 
		}
			
		$sql = "update pd_sessions set pd_title = '$title', pd_description = '$description', pd_date = '$date',
		pd_external = '$external',pd_hours = '$hours', pd_cost = '$cost', pd_custom = '$custom_data', pd_replacement = '$replacement', pd_category = '$pd_category'
		where pd_id = '$id'";
		//echo $sql;
		$this->DB->query($sql,"pdedit.php");
		
	
   }
 
  
   private function insert_pd()
   {
   	$user = $this->userid;
	$title = $this->DB->escape($_REQUEST["title"]);
	$description = $this->DB->escape($_REQUEST["description"]);
	$external = $this->DB->escape($_REQUEST["external"]);
	$hours = $this->DB->escape($_REQUEST["hours"]);
	$cost = $this->DB->escape($_REQUEST["cost"]);
	$approved = '0';
	$attended = '0';
	$day = $this->DB->escape($_REQUEST["day"]);
	$month = $this->DB->escape($_REQUEST["month"]);
	$year = $this->DB->escape($_REQUEST["year"]);
	$replacement = $this->DB->escape($_REQUEST["replacement"]);
		$pd_category = $this->DB->escape($_REQUEST["pd_category"]);
	
	$date = $year . "-" . $month . "-" . $day;
	
		$sql = "select * from pd_custom_fields where m = '$this->m'";
     
		$this->DB->query($sql,"pd_edit.php");
	while ($row = $this->DB->get())
		{
		$field = htmlspecialchars($row["fieldno"]);
		$custom_label[$field] = htmlspecialchars($row["label"]);
		$custom_type[$field] = htmlspecialchars($row["type"]);
		$custom_menu[$field] = htmlspecialchars($row["menu"]);
		
	}
       	
	
		$xml_output  = "<?xml version=\"1.0\"?>\n";
			$xml_output .= "<fields>\n"; 
		
			$custom = $_REQUEST["custom"];
				
		if (isset($custom))
		{
			foreach($custom as $key => $value)
			{
			
				
			$xml_output .= "<field>\n"; 
			$xml_output .= "<field_id><![CDATA[$key]]></field_id>\n"; 
			$xml_output .= "<field_label><![CDATA[$custom_label[$key]]]></field_label>\n"; 
			$xml_output .= "<field_value><![CDATA[$value]]></field_value>\n"; 
			$xml_output .= "<menu><![CDATA[$custom_menu[$key]]]></menu>\n"; 
			$xml_output .= "<type><![CDATA[$custom_type[$key]]]></type>\n";
			$xml_output .= "</field>\n"; 
			
			}	
		}
		
		$xml_output .= "</fields>\n"; 
		
		$xml_output = $this->DB->escape($xml_output);
	
	
			
		$sql = "insert into pd_sessions values 	(NULL,'$title','$description','$date','$external','$hours','$cost','$approved','$attended','$user','$xml_output','$replacement','$pd_category','0')";
			$this->DB->query($sql,"pdRead.php");

	
		
		$headers = "From: $this->email";
								$emailaddress = $this->email;
								$subject = "PD request";
								$message = "
Name: $this->first_name $this->last_name
Title: $title
Description: $description	
Date $date							

								
								
								";
		
								
								
		send_email($this->DB,$emailaddress, $subject, $message,$headers);
		
		
		
   } 
   
   
 private function list_pd()
   {
   	
   	
   	 	$sql = "select * from pd_categories";

							
	$this->DB->query($sql,"pdedit.php");
	while ($row = $this->DB->get())
	{
	$cat_name = $row["cat_name"]; 
	
	$id = $row["id"]; 

	$categories[$id] = $cat_name;
	}
   	
   	
	
   	
   	
   	
   	
   	
		if (isset($_REQUEST["orderby"])) {$orderby = $_REQUEST["orderby"];} else {$orderby = "pd_date";}
	
		 $year = $this->year;

	
			$sql = "select * from pd_custom_fields where m = '$this->m' order by ranking";
	
	
						
	$this->DB->query($sql,"pdedit.php");
	while ($row = $this->DB->get())
	{
	$field = $row["fieldno"]; 
	$label = htmlspecialchars($row["label"]);
	
	$custom_fields[$field] = $label;
	}
		
	
	

	

		
		$string = " pd_user = '$this->userid' and ";
			echo "<h2>$this->year</h2>";
			
		$linkstring = "&amp;pd_user=$this->userid";	
	
		

		
				
   		if ($this->DB->type == "mysql")
			{
		$sql = "SELECT pd_id, UNIX_TIMESTAMP(pd_date) as pd_date, pd_title,pd_custom,pd_description, pd_external, pd_hours, pd_cost, pd_approved, pd_attended, pd_user,last_name, pd_replacement, pd_category FROM pd_sessions,user where  $string year(pd_date) = '$year' and user.userid = pd_sessions.pd_user and pd_approved != '2' order by $orderby";
			}
		else
			{
		$sql = "SELECT pd_id, pd_title,strftime('%s',pd_date) as pd_date,pd_custom,pd_description, pd_external, pd_hours, pd_cost, pd_approved, pd_attended, pd_user,last_name, pd_replacement, pd_category FROM pd_sessions,user where $string strftime('%Y',pd_date) = '$year' and user.userid = pd_sessions.pd_user and pd_approved != '2' order by $orderby ";
			}
		
//	echo $sql;
	
$hourstotal = 0;
$hoursapproved = 0;
$hoursattended = 0;
$catsummary = array();

	
		echo "
		
		<script type=\"text/javascript\">
		
		
	
		
		</script>
		
		
		
		
		
		<table class=\"colourtable\" style=\"margin:1em auto;text-align:left\">
		<tr style=\"font-weight:bold\" class=\"accent\"><td><a href=\"pd.php?&amp;m=$this->m&amp;year=$year&amp;orderby=pd_id$linkstring\">PD ID</a></td><td><a href=\"pd.php?&amp;m=$this->m&amp;year=$year&amp;orderby=pd_date$linkstring\">Date</a></td>
<td><a href=\"pd.php?&amp;year=$year&amp;m=$this->m&amp;orderby=pd_title$linkstring\">Title</a>
<td><a href=\"pd.php?&amp;year=$year&amp;m=$this->m&amp;orderby=pd_category$linkstring\">Category</a>

</td>

<td>Description</td>";
		
		if (isset($custom_fields))
		{
		//foreach ($custom_fields as $key => $value)

		//{
			echo "<td>Details</td>";
		//}
		}
		
		echo "<td><a href=\"pd.php?&amp;year=$year&amp;m=$this->m&amp;orderby=pd_external$linkstring\">External</a></td><td><a href=\"pd.php?year=$year&amp;m=$this->m&amp;orderby=pd_hours$linkstring\">Hours</a></td>
		<td><a href=\"pd.php?year=$year&amp;m=$this->m&amp;orderby=pd_cost$linkstring\">Cost</a></td>
		<td><a href=\"pd.php?year=$year&amp;m=$this->m&amp;orderby=pd_replacement$linkstring\">Replace</a></td>
		<td><a href=\"pd.php?year=$year&amp;m=$this->m&amp;orderby=pd_approved$linkstring\">Approved</a></td><td><a href=\"pd.php?year=$year&amp;m=$this->m&amp;orderby=pd_attended$linkstring\">Attended</a></td></tr>";	
		
		$external[0] = "Internal";
		$external[1] = "External";
		
		$yesno[0] = "No";
		$yesno[1] = "Yes";
			
		$this->DB->query($sql,"pdedit.php");
		while ($row = $this->DB->get()) 
					{
					$pd_title = htmlspecialchars($row["pd_title"]);	
					$pd_id = htmlspecialchars($row["pd_id"]);	
					$pd_external = htmlspecialchars($row["pd_external"]);
					$pd_hours = htmlspecialchars($row["pd_hours"]);
					$pd_cost = htmlspecialchars($row["pd_cost"]);
					$pd_approved = htmlspecialchars($row["pd_approved"]);
					$pd_attended = htmlspecialchars($row["pd_attended"]);
					$pd_description = nl2br(htmlspecialchars($row["pd_description"]));
					$pd_custom = $row["pd_custom"];
					$pd_replacement= htmlspecialchars($row["pd_replacement"]);
					$pd_category= htmlspecialchars($row["pd_category"]);
					$pd_date = strftime("%x",$row["pd_date"]);


$hourstotal = $hourstotal + $pd_hours;
if ($pd_approved == 1) {$hoursapproved = $hoursapproved + $pd_hours;}
if ($pd_attended == 1) {$hoursattended = $hoursattended + $pd_hours;}

					
					$approved_id = "approved_" . $pd_id;
					$attended_id = "attended_" . $pd_id;

$catname = $categories[$pd_category];
//echo "catname is $catname";
if (array_key_exists($catname,$catsummary))
{
$catsummary[$catname]  = $catsummary[$catname] + $pd_hours;
}
else
{
$catsummary[$catname]  = $pd_hours;
}						
					echo "<tr><td>$pd_id</td><td>$pd_date</td><td>$pd_title</td><td>$categories[$pd_category]</td>
					<td class=\"hoverdes\" id=\"td1_$pd_id\"><div style=\"position:relative\" ><span class=\"nodisplay\" id=\"p1_$pd_id\"><span style=\"font-size:1.4em;\">Description</span> <br>$pd_description</span></div></td>
					<td class=\"hoverdet\" id=\"td2_$pd_id\"><div style=\"position:relative\" ><span  class=\"nodisplay\" id=\"p2_$pd_id\">
					<span style=\"font-size:1.4em;\">Details</span><br><br>
					";
					
					
					
						
					if ($pd_custom != "")	{    $xml = simplexml_load_string($pd_custom);		}
		
					
					 if (isset($custom_fields))
					{
					foreach ($custom_fields as $key => $value)
		
						{
						
						
						if (isset($xml))
						{
						 foreach ($xml as $field) 
		 				{ 
		 	
		
						if ($key == $field->field_id)
							{
							echo "<b>$field->field_label</b><br> $field->field_value<br><br>";
				
							}
		 				}
						}
						
						
			
						}
					}
					
					
					
					
					echo "</span></div></td>
					
					
					";
					
					
					
					
						
				
					echo "<td>$external[$pd_external]</td>
					<td style=\"text-align:right\">$pd_hours</td><td style=\"text-align:right\">$pd_cost</td><td style=\"text-align:right\">$pd_replacement</td><td ";
					if ($pd_approved == 1) {echo " style=\"background:#B1D8A9\"";} else {echo " style=\"background:#E05C5C\"";}
					echo ">";
				
					if ($pd_approved == 1) {echo " Yes";} else {echo "No";}
				
					
				echo "</td><td";
					if ($pd_attended == 1) {echo " style=\"background:#B1D8A9\"";} else {echo " style=\"background:#E05C5C\"";}
					echo ">";
				
					if ($pd_attended == 1) {echo " Yes";} else {echo "No";}
				
			echo "</td><td>";
			
			if ($pd_attended == 0)
			{
				echo "<a href=\"pd.php?m=$this->m&amp;year=$year&amp;event=edit&pd_id=$pd_id\">Edit</a>";
			}
				echo "</td></tr>";	
					}
	echo "</table>
	<h2>Totals</h2><div style=\"text-align:left\"><table style=\"margin:0 auto\" class=\"colourtable\">";

foreach ($catsummary as $key => $value)
    {
echo "<tr><td>$key</td><td> $value </td></tr>";
}

echo "<tr><td>Total hours</td><td>$hourstotal </td></tr>
<tr><td>Hours approved</td><td>$hoursapproved</td></tr>
<tr><td>Hours attended</td><td> $hoursattended</td></tr>
</table>
</div>	
	<script type=\"text/javascript\" src=\"pd.js\"></script>
		<script type=\"text/javascript\">
	
	
	window.onload=loadhover
	
	</script>

	
	
	
	
	
	";
   }
   
   
   
   
   
   private function add_pd()
   {
   	
	$sql = "select * from pd_options";		
	$this->DB->query($sql,"pdedit.php");
	$row = $this->DB->get();
	{
	$email_user = $row["email_user"]; 
	$replacement_text = nl2br($row["replacement_text"]); 
	}
	
   	echo "
	<h2  style=\"text-align:center\">Add PD</h2>
   	
   	
	<p >";
			if (trim($this->instructions) != "")
   	{
   		$instructions = $this->instructions;
   		echo "$instructions";
   	}
	echo "</p>
   	
<form method=\"post\" action=\"pd.php\" class=\"swouter\" style=\"clear:both;margin-top:2em;\">
	<table class=\"swinner\">
   	
   	<tr><td class=\"label\">Title</td><td><input name=\"title\" type=\"text\" size=\"50\"></td></tr>
	<tr><td class=\"label\">Description</td><td><textarea name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr>
	<tr><td class=\"label\">Commence date</td><td>
	<select name=\"day\">";
		
			$counter = 1;
			while ($counter < 32)
				{
				$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
				echo "<option value=\"$counter\">$counter</option>";
				$counter++;
				}
			
			$year = date("Y"); 
			echo "</select>
			/ <select name=\"month\">";
			$counter = 1;
			while ($counter < 13)
				{
				$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
				echo "<option value=\"$counter\">$counter</option>";
				$counter++;
				}
			
			echo "</select>
		
	
	
	 / <select name=\"year\">";
		$y = $year - 10;
		
		while ($y < $year + 10)
		{
		echo "<option value=\"$y\"";
		if ($y == $year) {echo " selected";}
		echo ">$y</option>";	
		$y++;
			
		}
			echo "</select>
	
	
	</td></tr>
	<tr><td class=\"label\">Internal/External</td><td>
	<select name=\"external\">
	<option value=\"1\">External</option>
	<option value=\"0\">Internal</option>
	</select>
	</td></tr>
	<tr><td class=\"label\">Hours</td><td>
	<input name=\"hours\" type=\"text\" value=\"0\" id=\"hours\"> <span id=\"hourswarning\"></span>
	</td></tr>
	<tr><td class=\"label\">Cost $</td><td>
		<input name=\"cost\" type=\"text\" value=\"0.00\" id=\"cost\"> <span id=\"costwarning\"></span></td></tr>
			<tr><td class=\"label\">Replacement $</td><td>
		<input name=\"replacement\" type=\"text\" value=\"0.00\" id=\"replacement\"><span id=\"replacewarning\"></span><br>$replacement_text</td></tr>
";
			
				$sql = "select * from pd_custom_fields where m = '$this->m' order by ranking";
		
	$counter = 0;	
						
	$this->DB->query($sql,"pdread.php");
	while ($row = $this->DB->get())
	{
	$field = $row["fieldno"]; 
	$label = htmlspecialchars($row["label"]);
	$type = $row["type"];
	$menu = $row["menu"];
	$values = split("\n", $menu);
	$comment =  htmlspecialchars($row["comment"]);
	$custom_ranking[$counter] = $field;
	
	
	echo "<tr><td class=\"label\">$label</td><td>";
		if ($type == "t") {echo "<input type=\"text\" name=\"custom[$field]\" size=\"50\">";}
	if ($type == "a") {echo "<textarea name=\"custom[$field]\" cols=\"45\" rows=\"6\"></textarea>";}
	if ($type == "m") {echo "<select name=\"custom[$field]\">";
	 foreach ($values as $indexa => $value)
							{
							echo "<option value=\"$value\"> $value</option>";
							}
	
	echo "</select>";}
	
	if ($comment != "") {echo "<br>$comment";}
	echo "</td></tr>";
		
	}		
	
	
		
	echo "<tr><td class=\"label\">Category</td><td><select name=\"pd_category\">";

	$sql = "select * from pd_categories order by  cat_name";

	$counter = 0;	
						
	$this->DB->query($sql,"pdadmin.php");
	while ($row = $this->DB->get())
	{
	$cat_name = $row["cat_name"]; 
	$id = $row["id"]; 
	
	echo "<option value=\"$id\">$cat_name</option>";
	}


echo "</select></td></tr>";

	echo "<tr><td></td><td>
	<input name=\"update\" type=\"hidden\" value=\"insertpd\">
	<input name=\"m\" type=\"hidden\" value=\"$this->m\">
	<input name=\"Add PD\" value=\"Add PD\" type=\"submit\"></td></tr>
	</table>
	</form>
	<script language=\"javascript\" src=\"pd.js\"></script>
	";
		
   }
   
   private function edit_pd()
   {
   	
   	
   $sql = "select * from pd_options";		
	$this->DB->query($sql,"pdedit.php");
	$row = $this->DB->get();
	{
	$email_user = $row["email_user"]; 
	$replacement_text = nl2br($row["replacement_text"]); 
	}
   	
   	
   	$pd_id =  $this->DB->escape($_REQUEST["pd_id"]);
   	
   	if ($this->DB->type == "mysql")
			{
		$sql = "SELECT pd_id, UNIX_TIMESTAMP(pd_date) as pd_date, pd_title,pd_custom,pd_description, pd_external, pd_hours, pd_cost, pd_approved, pd_attended, pd_user,last_name, pd_replacement, pd_category FROM pd_sessions,user where  pd_id = '$pd_id' and user.userid = pd_sessions.pd_user  ";
			}
		else
			{
		$sql = "SELECT pd_id, pd_title,strftime('%s',pd_date) as pd_date,pd_custom,pd_description, pd_external, pd_hours, pd_cost, pd_approved, pd_attended, pd_user,last_name, pd_replacement, pd_category FROM pd_sessions,user where   pd_id = '$pd_id' and user.userid = pd_sessions.pd_user  ";
			}
   	
   	//echo $sql;
	$this->DB->query($sql,"pdread.php");
	$row = $this->DB->get();
					
					$pd_title = htmlspecialchars($row["pd_title"]);	
					$pd_id = htmlspecialchars($row["pd_id"]);	
					$pd_external = htmlspecialchars($row["pd_external"]);
					$pd_hours = htmlspecialchars($row["pd_hours"]);
					$pd_cost = htmlspecialchars($row["pd_cost"]);
					$pd_approved = htmlspecialchars($row["pd_approved"]);
					$pd_attended = htmlspecialchars($row["pd_attended"]);
					$pd_description = htmlspecialchars($row["pd_description"]);
					$pd_replacement = htmlspecialchars($row["pd_replacement"]);
					$pd_category = htmlspecialchars($row["pd_category"]);
					$custom_data = $row["pd_custom"];
				
						$pd_date = $row["pd_date"];	
					
					$approved_id = "approved_" . $pd_id;
					$attended_id = "attended_" . $pd_id;
					
					$pd_day = date("d",$pd_date);
					$pd_month = date("m",$pd_date);
					$pd_year = date("Y",$pd_date);
	
					
   	echo "
	<h2  style=\"text-align:center\">Edit PD</h2>
		<p >";
			if (trim($this->instructions) != "")
   	{
   		$instructions = $this->instructions;
   		echo "$instructions";
   	}
	echo "</p>
	<form method=\"post\" action=\"pd.php\" class=\"swouter\" style=\"clear:both;margin-top:2em;\">
	<table class=\"swinner\">
	<tr><td class=\"label\">Title</td><td><input name=\"title\" type=\"text\" size=\"50\" value=\"$pd_title\"></td></tr>
	<tr><td class=\"label\">Description</td><td><textarea name=\"description\" cols=\"60\" rows=\"10\">$pd_description</textarea></td></tr>
	<tr><td class=\"label\">Commence date</td><td>
	<select name=\"day\">";
		
			$counter = 1;
			while ($counter < 32)
				{
				$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
				echo "<option value=\"$counter\"";
				if ($pd_day == $counter) {echo " selected";}
				echo ">$counter</option>";
				$counter++;
				}
			
			$year = date("Y"); 
			echo "</select>
			/ <select name=\"month\">";
			$counter = 1;
			while ($counter < 13)
				{
				$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
				echo "<option value=\"$counter\"";
				if ($pd_month == $counter) {echo " selected";}
				echo ">$counter</option>";
				$counter++;
				}
			
			echo "</select>
		
	
	
	 / <select name=\"year\">";
			$displayyear = "no";
			$finish = $year + 3;
			while ($year < $finish)
			{
				echo "<option value=\"$year\"";
				if ($year == $pd_year ) 
				{echo " selected";
				$displayyear = "yes";
				}
				echo ">$year</option>";
				$year++;
			}
		if ($displayyear == "no") {echo "<option value=\"$pd_year\" selected>$pd_year</option>";}
	
			echo "</select>
	
	
	</td></tr>
	<tr><td class=\"label\">Internal/External</td><td>
	<select name=\"external\">
	<option value=\"1\">External</option>
	<option value=\"0\"";
	if ($pd_external == 0) {echo " selected";}
	echo ">Internal</option>
	</select>
	</td></tr>
	<tr><td class=\"label\">Hours</td><td>
	<input name=\"hours\" type=\"text\" value=\"$pd_hours\" id=\"hours\"> <span id=\"hourswarning\"></span>
	</td></tr>
	<tr><td class=\"label\">Cost $</td><td>
		<input name=\"cost\" type=\"text\" value=\"$pd_cost\" id=\"cost\"> <span id=\"costwarning\"></span></td></tr>
		<tr><td class=\"label\">Replacement $</td><td>
		<input name=\"replacement\" type=\"text\" value=\"$pd_replacement\" id=\"replacement\"><span id=\"replacewarning\"></span><br>$replacement_text</td></tr>";
	
					
	
			/*
				$sql = "select * from pd_custom_fields where m = '$this->m' order by ranking";
		
	$counter = 0;	
						
	$this->DB->query($sql,"pdread.php");
	while ($row = $this->DB->get())
	{
	$field = $row["fieldno"]; 
	$label = htmlspecialchars($row["label"]);
	$type = $row["type"];
	$menu = $row["menu"];
	$values = split("\n", $menu);
	$comment =  htmlspecialchars($row["comment"]);
	$custom_ranking[$counter] = $field;
	
	
	echo "<tr><td class=\"label\">$label</td><td>";
		if ($type == "t") {echo "<input type=\"text\" name=\"custom[$field]\" size=\"50\">";}
	if ($type == "a") {echo "<textarea name=\"custom[$field]\" cols=\"45\" rows=\"6\"></textarea>";}
	if ($type == "m") {echo "<select name=\"custom[$field]\">";
	 foreach ($values as $indexa => $value)
							{
							echo "<option value=\"$value\"> $value</option>";
							}
	
	echo "</select>";}
	
	if ($comment != "") {echo "<br>$comment";}
	echo "</td></tr>";
		
	}		
	/*/
		////////////////////////	
				

		/*
		if ( $custom_xml = simplexml_load_string($custom_data))
		 {
		 foreach ($custom_xml as $field) 
		 {
		 	
			echo "<tr><td class=\"label\">$field->field_label</td><td>";
		if ($field->type == "t") {echo "<input type=\"text\" name=\"custom[$field->field_id]\" size=\"50\" value=\"$field->field_value\">";}
	if ($field->type == "a") {echo "<textarea name=\"custom[$field->field_id]\" cols=\"45\" rows=\"6\">$field->field_value</textarea>";}
	if ($field->type == "m") {echo "<select name=\"custom[$field->field_id]\">";
	$values = split("\n", $field->menu);
	
	 foreach ($values as $indexa => $value)
							{
							echo "<option value=\"$value\"";
							if ($value == $field->field_value) {echo " selected";}
							
							echo "> $value</option>";
							}
	
	echo "</select>";}
	

	echo "</td></tr>";
		 	
		 	
		 }
	}
	*/
		
		
	$custom_xml = simplexml_load_string($custom_data);
	
	
			$sql = "select * from pd_custom_fields where m = '$this->m' order by ranking";

	$counter = 0;	
						
	$this->DB->query($sql,"pdadmin.php");
	while ($row = $this->DB->get())
	{
	$fieldno = $row["fieldno"]; 
	$label = htmlspecialchars($row["label"]);
	$type = $row["type"];
	$menu = $row["menu"];
	$values = split("\r\n", $menu);
	$comment =  htmlspecialchars($row["comment"]);
	$custom_ranking[$counter] = $field;
	
	$val = "";
	
	if ($custom_xml)
	{
	 foreach ($custom_xml as $field) 
		 {
		 	$temp = $field->field_id;

		 if ($fieldno == $field->field_id)	
			 {
			 	
			 $val = 	$field->field_value;
			
			 }
		 	
		 }
		 	
	 }
	
	
	echo "<tr><td class=\"label\">$label</td><td>
	
	
	";
	

	
		if ($type == "t") {echo "<input type=\"text\" name=\"custom[$fieldno]\" value=\"$val\" size=\"50\">";}
	if ($type == "a") {echo "<textarea name=\"custom[$fieldno]\" cols=\"45\" rows=\"6\">$val</textarea>";}
	if ($type == "m") {echo "<select name=\"custom[$fieldno]\">";
	 foreach ($values as $indexa => $value)
							{
							echo "<option value=\"$value\""; if ($val == $value) {echo " selected";}  echo "> $value</option>
";
							}
	
	echo "</select>";}
	
	if ($comment != "") {echo "<br>$comment";}
	echo "</td></tr>";
		
	}		
		
	
	
			
			//////////////////////////////
			/*
			$custom_xml = simplexml_load_string($custom_data);
			
				$sql = "select * from pd_custom_fields where m = '$this->m' order by ranking";
		
	$counter = 0;	
						
	$this->DB->query($sql,"pdread.php");
	while ($row = $this->DB->get())
	{
	$field = $row["fieldno"]; 
	$label = htmlspecialchars($row["label"]);
	$type = $row["type"];
	$menu = $row["menu"];
	$values = split("\n", $menu);
	$comment =  htmlspecialchars($row["comment"]);
	$custom_ranking[$counter] = $field;
	
	 if ( $custom_xml)
		 {
		 foreach ($custom_xml as $field) 
		 {
		 	echo "hello $field->field_value <br>";
		 if ($field->field_id == $fieldno)	
		 {$val = $field->field_value;}
		 }
		 }
	
	
	echo "<tr><td class=\"label\">$label</td><td>";
		if ($type == "t") {echo "<input type=\"text\" name=\"custom[$field]\" size=\"50\" value=\"$val\">";}
	if ($type == "a") {echo "<textarea name=\"custom[$field]\" cols=\"45\" rows=\"6\">$val</textarea>";}
	if ($type == "m") {echo "<select name=\"custom[$field]\">";
	 foreach ($values as $indexa => $value)
							{
							echo "<option value=\"$value\"";
							if ($val == $value) {echo " selected";}
							echo "> $value</option>";
							}
	
	echo "</select>";}
	
	if ($comment != "") {echo "<br>$comment";}
	echo "</td></tr>";
		
	}		

	
	*/
			
				
echo "<tr><td class=\"label\">Category</td><td><select name=\"pd_category\">";


	$sql = "select * from pd_categories order by  cat_name";

	$counter = 0;	
						
	$this->DB->query($sql,"pdadmin.php");
	while ($row = $this->DB->get())
	{
	$cat_name = $row["cat_name"]; 
	$id = $row["id"]; 
	
	echo "<option value=\"$id\" ";
	if ($id == $pd_category) {echo " selected";} 
	echo ">$cat_name</option>";
	}


echo "</select></td></tr>";	
			
			
			
	echo "<tr><td></td><td>
	<input name=\"update\" type=\"hidden\" value=\"updatepd\">
	<input name=\"m\" type=\"hidden\" value=\"$this->m\">
	<input name=\"pd_id\" type=\"hidden\" value=\"$pd_id\">
	<input name=\"Update PD\" value=\"Update PD\" type=\"submit\"></td></tr>
	</table>
	</form>
	<script language=\"javascript\">	
	var cost = document.getElementById(\"cost\");
	var hours = document.getElementById(\"hours\");
	
	
	function testcost(){
	//alert(\"testing\")
	var td = cost.parentNode
	
	testRegExp = /^[0-9]+\.[0-9][0-9]$/i;
		if (testRegExp.test(cost.value)) {
			td.style.background = 'white';
			document.getElementById(\"costwarning\").innerHTML = '';
		}
		else {
			
		td.style.background = '#E05C5C';
		document.getElementById(\"costwarning\").innerHTML = '<br>Not a decimal value.';
		}
	}
	
	
		function testhours(){
	//alert(\"testing\")
	var td = hours.parentNode
	var status;
	
	if (hours.value.length == 1)
	{
	testRegExp = /[0-9]/i;
	}
	if (hours.value.length == 2)
	{
	testRegExp = /[0-9][0-9]/i;
	}
		if (hours.value.length == 3)
	{
	testRegExp = /^[0-9]+[\.0-9][0-9]$/i;
	}
	
	
		if (testRegExp.test(hours.value)) {
			td.style.background = 'white';
			document.getElementById(\"hourswarning\").innerHTML = '';
		}
		else {
			
		td.style.background = '#E05C5C';
		document.getElementById(\"hourswarning\").innerHTML = '<br> Not a numerical value. <br>eg 90 minutes would be 1.5 hours';
		}
	}
	
	
	//cost.onchange=test
	cost.onkeyup=testcost
	hours.onkeyup=testhours
	</script>
	";
		
   }
}



?>