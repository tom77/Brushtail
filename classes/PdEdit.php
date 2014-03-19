<?php

Class PdEdit
{
	
	private $DB;
	private $m;
	private $year;
	
	
	public function __construct($m,$DB,$PREFERENCES)
    {
   $this->m = $m;
   $this->PREFERENCES = $PREFERENCES;

   $this->DB = $DB;
   
   	if (isset($_REQUEST["year"]))
		{$this->year =  $this->DB->escape($_REQUEST["year"]);}
		else
		{$this->year = date("Y");}
   
   $year = $this->year;
   
   
     $sql = "select email, email_action, showprint, instructions from pd_options where m = '$m'";
	$this->DB->query($sql,"pdedit.php");
	$row = $this->DB->get()	;
	$this->email = $row["email"];
	$this->email_action = $row["email_action"];
	$this->showprint = $row["showprint"];
	$this->instructions = $row["instructions"];
   
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
   
   
   
   <a href=\"pd.php?m=$this->m&amp;year=$year&amp;event=addpd&amp;z=$random\">Add PD</a> | <a href=\"pd.php?m=$this->m&amp;event=listpd&amp;year=$year&amp;filter=pending&amp;z=$random\">Pending approval</a> 
   | <a href=\"pd.php?m=$this->m&amp;event=listpd&amp;year=$year&amp;filter=all&amp;z=$random\">List all</a>
     | <a href=\"pd.php?m=$this->m&amp;year=$year&amp;event=stafflist&amp;z=$random\">Staff summary</a>  | <a href=\"pd.php?m=$this->m&amp;year=$year&amp;event=catlist&amp;z=$random\">Categories</a>   | <a href=\"pdprint.php?m=$this->m&amp;user=all&year=$year&amp;z=$random\">Print all</a>   | <a href=\"pd.php?m=$this->m&amp;event=listpd&amp;filter=deleted&amp;year=$year&amp;z=$random\">Deleted</a>
	 <form style=\"display:inline\" method=\"get\" action=\"pd.php\">
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
	 <input type=\"text\" name=\"keywords\" ";
	 if (isset($_REQUEST["keywords"])) { echo " value=\"$_REQUEST[keywords]\"";}
	 
	 echo "><input type=\"submit\" value=\"Search\">
	 <input type=\"hidden\" name=\"filter\" value=\"search\">
	  <input type=\"hidden\" name=\"year\" value=\"$year\">
	 <input type=\"hidden\" name=\"m\" value=\"$this->m\">
	 <input type=\"hidden\" name=\"event\" value=\"listpd\">
	 </form>";

   
   if(isset($_REQUEST["update"]) && $_REQUEST["update"] == "insertpd") 
  {
  	$this->insert_pd();
  }
 
    if(isset($_REQUEST["update"]) && $_REQUEST["update"] == "updatepd") 
  {
  	$this->update_pd();
  }
  
     if(isset($_REQUEST["update"]) && $_REQUEST["update"] == "updatebudget") 
  {
  	$this->update_budget();
  }
   
 if(isset($_REQUEST["event"]) && $_REQUEST["event"] == "editbudget" ) 
  {
  	
	$this->editbudget();
	}
  elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "addpd" ) 
  {
  	
	$this->add_pd();
	}
 elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "editpd")
  {
  	
	$this->edit_pd();
	}
	
  elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "stafflist") 
  {
  	
	$this->staff_list();
	}
	
	
 elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "catlist") 
  {
  	
	$this->cat_list();
	}	
 elseif(isset($_REQUEST["event"]) && $_REQUEST["event"] == "listpd") 
  {
  	
	$this->list_pd();
	}
  else
  {
  		$sql = "select pref_value from preferences where pref_name = 'pdmanager'";
  	
  		$this->DB->query($sql,"pd.php");
		$row = $this->DB->get();
		$version = $row["pref_value"];
  	echo "<div style=\"text-align:center\"><img src=\"../images/pd.gif\"><br>
  	Version $version
  	</div>";
  }
    
   
   }

  
   
   
     private function editbudget()
   {
   	
	$cat = $_REQUEST["cat"];
	
	$sql = "select budget from pd_budgets where year = '$this->year' and category = '$cat'";
	$this->DB->query($sql,"pd.php");
	$row = $this->DB->get();
	if ($row) {$budget = $row["budget"];}
	else {$budget = "0.00";}
	
	$sql = "select cat_name from pd_categories where  id = '$cat'";

	$this->DB->query($sql,"pdedit.php");
	$row = $this->DB->get();
	$category = $row["cat_name"];
		
	
	echo "<h2>Edit Budget</h2>
	
	<form action =\"pd.php\" method=\"post\">
	<table style=\"margin:0 auto\">
	<tr><td><b>Year</b></td><td>$this->year</td></tr>
	<tr><td><b>Category</b></td><td>$category</td></tr>
	<tr><td><b>Budget</b></td><td><input type=\"text\" value=\"$budget\" name=\"budget\" id=\"cost\"> <span id=\"costwarning\"></span></td></td></tr>
	
	<tr><td></td><td><input type=\"submit\" value=\"Update\">
	<input type=\"hidden\" name=\"update\" value=\"updatebudget\">
	<input type=\"hidden\" name=\"cat\" value=\"$cat\">
	<input type=\"hidden\" name=\"year\" value=\"$this->year\">
	<input type=\"hidden\" name=\"m\" value=\"$this->m\">
	<input type=\"hidden\" name=\"event\" value=\"catlist\">
	</td></tr>
	
	
	</table>
	
	</form>
	<script type=\"text/javascript\" src=\"pd.js\"></script>
	
	
	";
	
	
	
   }
   
     private function cat_list()
   {
   	
	//get list of budgets
	
	$budgets = array();
	
	$sql = "select budget , category from pd_budgets where year = '$this->year'";
	$this->DB->query($sql,"pdedit.php");
	while ($row = $this->DB->get()) 
			{
			$budget = $row["budget"];	
			$category = $row["category"];	
			$budgets[$category] = $budget;
			}
	
	
	
	
   	echo "
   	<h2>Category summary $this->year</h2>
   	<table class=\"colourtable\" style=\"text-align:right;margin:0 auto\">
   	<tr style=\"font-weight:bold\"><td>Category</td><td>Sessions</td><td>Total hours</td><td>Course cost</td>
	<td>Replacement cost</td><td>Total cost</td><td>Total budget</td><td>Total funds available</td><td></td><td></td></tr>
   	";
   	
   //	$sql = "select pd_user, sum(pd_hours) as total_hours, sum(pd_cost) as total_cost from pd_sessions group by pd_user";
   	 
	 	if ($this->DB->type == "mysql")
			{
	 	 $sql = "select cat_name, id,count(*) as count, sum(pd_hours) as total_hours, sum(pd_cost) as course_cost, sum(pd_replacement) as total_replacement from pd_sessions, pd_categories where year(pd_date) = '$this->year' and pd_categories.id = pd_sessions.pd_category group by id order by cat_name";
		
			}
	
	else
			{
	 	// $sql = "select last_name, first_name, pd_user,count(*) as count, sum(pd_hours) as total_hours, sum(pd_cost) as course_cost from user, pd_sessions  
	 	 //where strftime('%Y',pd_date) = '$this->year' and user.userid = pd_sessions.pd_user   group by pd_user";
	 	 
	 	  $sql = "select cat_name, id,count(*) as count, sum(pd_hours) as total_hours, sum(pd_cost) as course_cost, sum(pd_replacement) as total_replacement from pd_categories left OUTER JOIN pd_sessions on pd_categories.id = pd_sessions.pd_category
	 	 where strftime('%Y',pd_date) = '$this->year' and pd_approved = '1' group by id order by cat_name";
			}
	
			$summary_count = 0;
			$summary_hours = 0;
			$summary_cost = 0;
			$summary_replacment = 0;
			$summary_budgets = 0;
			$summary_available = 0;
			$summary_course = 0;
			$summary_replacement = 0;
	
	$this->DB->query($sql,"pdedit.php");
	while ($row = $this->DB->get()) 
			{
			$cat_name = htmlspecialchars($row["cat_name"]);	
			$id = htmlspecialchars($row["id"]);	
		//	$pd_user = htmlspecialchars($row["pd_user"]);	
			$total_hours = number_format ($row["total_hours"],2);	
			$course_cost = htmlspecialchars($row["course_cost"]);
			$count = htmlspecialchars($row["count"]);
			$total_replacement = htmlspecialchars($row["total_replacement"]);
			
			$summary_count += $count;
			$summary_hours += $total_hours;
			$summary_course += $course_cost;
			$summary_replacement += $total_replacement;
			
			$total_cost = $course_cost + $total_replacement;
			$course_cost = number_format ($course_cost, 2);
			$total_cost = number_format ($total_cost, 2);
			$total_replacement = number_format ($total_replacement, 2);
			
			
			if (array_key_exists($id,$budgets)) { $budget = $budgets[$id];} else {$budget = "0.00";}
			$summary_budgets += $budget;
			$available = $budget - $total_cost;
				$available = number_format ($available, 2);
			
			$budget = number_format ($budget, 2);
			
			
			echo "<tr><td><a href=\"pd.php?m=$this->m&amp;event=listpd&amp;filter=cat&amp;pd_category=$id\">$cat_name</a></td>
			<td>$count</td><td>$total_hours</td><td>$$course_cost</td><td>$$total_replacement</td><td>$$total_cost</td>
			<td>$$budget</td><td>$$available</td>
			
			<td><a href=\"pdprint.php?m=$this->m&amp;cat=$id&amp;year=$this->year\">Print</a></td>
			<td><a href=\"pd.php?m=$this->m&amp;event=editbudget&amp;year=$this->year&amp;cat=$id\">Edit budget</a></td></tr>";
			
			
			}
			$summary_total = $summary_course + $summary_replacement;
			
			
			$summary_available = $summary_budgets - $summary_total;
			$summary_available = number_format ($summary_available, 2);
			$summary_replacement = number_format ($summary_replacement, 2);
			$summary_budgets = number_format ($summary_budgets, 2);
			$summary_course = number_format ($summary_course, 2);
			$summary_total = number_format ($summary_total, 2);
			
			
   	echo "
   	<tr style=\"font-weight:bold\"><td>Summary totals</td><td>$summary_count</td><td>$summary_hours</td>
	<td>$$summary_course</td><td>$$summary_replacement</td><td>$$summary_total</td>
	<td>$$summary_budgets</td><td>$$summary_available</td><td></td><td></td></tr>
   	</table>";
   	
   }
   
   
    
    private function staff_list()
   {
   	
   	echo "
   	<h2>Staff approval summary $this->year</h2>
   	<table class=\"colourtable\" style=\"text-align:right;margin:0 auto\">
   	<tr style=\"font-weight:bold\"><td>Staff name</td><td>Sessions</td><td>Total hours</td><td>Course cost</td><td>Replacement cost</td><td>Total cost</td></tr>
   	";
   	
   //	$sql = "select pd_user, sum(pd_hours) as total_hours, sum(pd_cost) as total_cost from pd_sessions group by pd_user";
   	 
	 	if ($this->DB->type == "mysql")
			{
	 	 $sql = "select last_name, first_name, pd_user,count(*) as count, sum(pd_hours) as total_hours, sum(pd_cost) as course_cost, sum(pd_replacement) as total_replacement from pd_sessions, user where year(pd_date) = '$this->year' and user.userid = pd_sessions.pd_user group by pd_user order by last_name";
			}
	
	else
			{
	 	// $sql = "select last_name, first_name, pd_user,count(*) as count, sum(pd_hours) as total_hours, sum(pd_cost) as total_cost from user, pd_sessions  
	 	 //where strftime('%Y',pd_date) = '$this->year' and user.userid = pd_sessions.pd_user   group by pd_user";
	 	 
	 	  $sql = "select last_name, first_name, pd_user,count(*) as count, sum(pd_hours) as total_hours, sum(pd_cost) as course_cost, sum(pd_replacement) as total_replacement from user left OUTER JOIN pd_sessions on user.userid = pd_sessions.pd_user 
	 	 where strftime('%Y',pd_date) = '$this->year' and pd_approved = '1' group by pd_user order by last_name";
			}
	
			$summary_count = 0;
			$summary_hours = 0;
			$summary_cost = 0;
			$summary_replacement = 0;
			$summary_course = 0;
	
	$this->DB->query($sql,"pdedit.php");
	while ($row = $this->DB->get()) 
			{
			$last_name = htmlspecialchars($row["last_name"]);	
			$first_name = htmlspecialchars($row["first_name"]);	
			$pd_user = htmlspecialchars($row["pd_user"]);	
			$total_hours = htmlspecialchars($row["total_hours"]);	
			$course_cost = htmlspecialchars($row["course_cost"]);
			$count = htmlspecialchars($row["count"]);
			$total_replacement = htmlspecialchars($row["total_replacement"]);
			
			$summary_count += $count;
			$summary_hours += $total_hours;
			$summary_course += $course_cost;
			$summary_replacement += $total_replacement;
			$total_cost = $course_cost + $total_replacement;
			
			$total_replacement = number_format ($total_replacement, 2);
			$course_cost = number_format ($course_cost, 2);
			$total_cost = number_format ($total_cost, 2);
			
			echo "<tr><td><a href=\"pd.php?m=$this->m&amp;event=listpd&amp;filter=user&amp;pd_user=$pd_user\">$last_name, $first_name</a></td>
			<td>$count</td><td>$total_hours</td><td>$$course_cost</td><td>$$total_replacement</td><td>$$total_cost</td><td><a href=\"pdprint.php?m=$this->m&amp;user=$pd_user&amp;year=$this->year\">Print</a></td></tr>";
			
			
			}
			
			$summary_cost = $summary_course + $summary_replacement;
			$summary_course = number_format ($summary_course, 2);
			$summary_replacement = number_format ($summary_replacement, 2);
			$summary_cost = number_format ($summary_cost, 2);
			
   	echo "
   	<tr style=\"font-weight:bold\"><td>Summary totals</td><td>$summary_count</td><td>$summary_hours</td><td>$$summary_course</td><td>$$summary_replacement</td><td>$$summary_cost</td><td></td></tr>
   	</table>";
   	
   }
     private function update_budget()
   {
   	$year = $this->DB->escape($_REQUEST["year"]);
	$cat = $this->DB->escape($_REQUEST["cat"]);
	$budget = $this->DB->escape($_REQUEST["budget"]);
	
	$sql = "delete from pd_budgets where year = '$year' and category = '$cat'";
	$this->DB->query($sql,"pdedit.php");
	
	
	$sql = "insert into  pd_budgets values(NULL,'$budget','$year','$cat')";
	$this->DB->query($sql,"pdedit.php");
	//echo $sql;
	
   }
    private function update_pd()
   {
   	$id = $this->DB->escape($_REQUEST["pd_id"]);
	$title = $this->DB->escape($_REQUEST["title"]);
	$description = $this->DB->escape($_REQUEST["description"]);
	$external = $this->DB->escape($_REQUEST["external"]);
	$hours = $this->DB->escape($_REQUEST["hours"]);
	$cost = $this->DB->escape($_REQUEST["cost"]);
	$approved = $this->DB->escape($_REQUEST["approved"]);
	$attended = $this->DB->escape($_REQUEST["attended"]);
	$day = $this->DB->escape($_REQUEST["day"]);
	$month = $this->DB->escape($_REQUEST["month"]);
	$year = $this->DB->escape($_REQUEST["year"]);
	$pd_replacement = $this->DB->escape($_REQUEST["replacement"]);
	$pd_category = $this->DB->escape($_REQUEST["pd_category"]);
	
	$pd_group = $this->DB->escape($_REQUEST["pd_group"]);
	$date = $year . "-" . $month . "-" . $day;
	
	if (isset($_REQUEST["updategroup"]))
	{
	$updategroup = $this->DB->escape($_REQUEST["updategroup"]);
	} else {$updategroup = "no";}
	
 		$custom= $_REQUEST["custom"];
 	
 	
 		
 		$sql = "select pd_custom from pd_sessions where pd_id = '$id'";
 		
 	//echo $sql;
       	$this->DB->query($sql,"pdedit.php");
		$row = $this->DB->get();
		//print_r($row);
		$custom_data =$row["pd_custom"];	
       	//echo "BBBB $custom_data BBBBBB";
       	
 		if (isset($custom))
		{
      
		
		// if ( $custom_xml = simplexml_load_string($custom_data))
	//	 {
		// var_dump($custom_xml);
		 //update xmlobject	
		// foreach ($custom_xml as $field) 
		// {
		 	
	//	 foreach($custom as $key => $value)
	///		{
		//if ($key == $field->field_id)
			//	{
				//$field->field_value = $value;
				
			//	}
				
			//}
		 	
		// }
		//
		 
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
		
		if ($pd_group > 0 && $updategroup == "yes")
		{
		$sql = "update pd_sessions set pd_title = '$title', pd_description = '$description', pd_date = '$date',
		pd_external = '$external',pd_hours = '$hours', pd_cost = '$cost',pd_approved = '$approved',pd_attended = '$attended', pd_custom = '$custom_data',
		 pd_replacement = '$pd_replacement', pd_category = '$pd_category'
		where pd_group = '$pd_group'";	
			
		}
		else 
		{
			
		$sql = "update pd_sessions set pd_title = '$title', pd_description = '$description', pd_date = '$date',
		pd_external = '$external',pd_hours = '$hours', pd_cost = '$cost',pd_approved = '$approved',pd_attended = '$attended', pd_custom = '$custom_data',
		 pd_replacement = '$pd_replacement', pd_category = '$pd_category'
		where pd_id = '$id'";
		
		}
	
		$this->DB->query($sql,"pdedit.php");
		
	
   }
   
   
   private function insert_pd()
   {
   	$user = $_REQUEST["user"];
	$title = $this->DB->escape($_REQUEST["title"]);
	$description = $this->DB->escape($_REQUEST["description"]);
	$external = $this->DB->escape($_REQUEST["external"]);
	$hours = $this->DB->escape($_REQUEST["hours"]);
	$cost = $this->DB->escape($_REQUEST["cost"]);
	$approved = $this->DB->escape($_REQUEST["approved"]);
	$attended = $this->DB->escape($_REQUEST["attended"]);
	$day = $this->DB->escape($_REQUEST["day"]);
	$month = $this->DB->escape($_REQUEST["month"]);
	$year = $this->DB->escape($_REQUEST["year"]);
	$pd_replacement = $this->DB->escape($_REQUEST["replacement"]);
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
	
	
	
	
	
	
	
	
	
	
	if (isset($user) && is_array($user))
	{
	
	$count = count($user);

	if ($count > 1)
	{

	$sql = "select max(pd_group) as max from pd_sessions";	
	$this->DB->query($sql,"pdedit.php");
	$row = $this->DB->get();
	$max = $row["max"]; 

	$group = $max + 1;
	
	}
	else 
	{
		$group = "0";
	}
		
	foreach ($user as $uid => $status)
		{
			
		if ($status == "on")
		{	
		$uid = $this->DB->escape($uid);
			
		$sql = "insert into pd_sessions values
		(NULL,'$title','$description','$date','$external','$hours','$cost','$approved','$attended','$uid','$xml_output','$pd_replacement','$pd_category','$group')";
		
		$this->DB->query($sql,"pdedit.php");
		}
		
		}
	}
	else 
	{
		echo "<h2 style=\"color:red\">Error adding PD. No staff were selected.</h2>";
	}
   }
   
    private function list_pd()
   {
   	
   	
 	$sql = "select * from pd_options";		
	$this->DB->query($sql,"pdedit.php");
	$row = $this->DB->get();
	{
	$email_user = $row["email_user"]; 
	$replacement_text = $row["replacement_text"]; 
	}
	
   	
   	$sql = "select * from pd_categories";

							
	$this->DB->query($sql,"pdedit.php");
	while ($row = $this->DB->get())
	{
	$cat_name = $row["cat_name"]; 
	
	$id = $row["id"]; 

	$categories[$id] = $cat_name;
	}
		
   	
   
   	
   	
		//custom fields
			
		$sql = "select * from pd_custom_fields where m = '$this->m' order by ranking";
	
	
						
	$this->DB->query($sql,"pdedit.php");
	while ($row = $this->DB->get())
	{
	$field = $row["fieldno"]; 
	$label = htmlspecialchars($row["label"]);
	
	$custom_fields[$field] = $label;
	}
		
	
			
		$filter = $_REQUEST["filter"];
	
		 $year = $this->year;

		
		if (isset($_REQUEST["orderby"])) {$orderby = $_REQUEST["orderby"];} else {$orderby = "pd_id";}
		
		$sql = "SELECT userid, first_name, last_name from user";
		$this->DB->query($sql,"pdedit.php");
		while ($row = $this->DB->get()) 
					{
					$userid = $row["userid"];	
					$last_name = $row["last_name"];
					$first_name = $row["first_name"];	
					$users[$userid]	= $last_name . ", " . $first_name;
					}
	

	

		if ($filter == "pending")
		{ $string = " pd_approved = '0' and ";
		$linkstring = "";	
	echo "<h2>Pending approval $this->year</h2>";
		} 
		
		elseif ($filter == "user")
		{
		$pd_user =  $this->DB->escape($_REQUEST["pd_user"]);
		$string = " pd_user = '$pd_user' and ";
			echo "<h2>$users[$pd_user] $this->year</h2>";
			
		$linkstring = "&amp;pd_user=$pd_user";	
		} 
		
		elseif ($filter == "search")
		{
			$linkstring = "";
			$keywords =  $this->DB->escape($_REQUEST["keywords"]);
			$words = explode(" ",$keywords);
       			
       			$string = "";
       			
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
				$string .= " (pd_title like '%$value%' or pd_description like '%$value') and";	
				$counter++;
				}	
				if ($keywords == "") {$string = " and 1 == 2";}	
		
			echo "<h2>Search results '$_REQUEST[keywords]' $this->year</h2>";
		
		
		}
		elseif ($filter == "deleted")
		{
			
			$string = " pd_approved == '2' and ";
			$linkstring = "";
			echo "<h2>Deleted $this->year</h2>";
		
		}
		
	
		elseif ($filter == "cat")
		{
			$pd_category =  $this->DB->escape($_REQUEST["pd_category"]);
			
			$string = " pd_category == '$pd_category' and ";
			$linkstring = "";
			echo "<h2>Category $categories[$pd_category]</h2>";
		
		}
		
		else {
			$string = " pd_approved != '2' and ";
			$linkstring = "";
			echo "<h2>List all $this->year</h2>";
		}
		
				
   		if ($this->DB->type == "mysql")
			{
		$sql = "SELECT pd_id, UNIX_TIMESTAMP(pd_date) as pd_date, pd_title,pd_description,pd_custom, pd_external, pd_hours, pd_cost, pd_approved, pd_attended, pd_user,last_name, pd_replacement, pd_category FROM pd_sessions,user where  $string year(pd_date) = '$year' and user.userid = pd_sessions.pd_user order by $orderby";
			}
		else
			{
		$sql = "SELECT pd_id, pd_title,pd_description ,pd_custom,strftime('%s',pd_date) as pd_date, pd_external, pd_hours, pd_cost, pd_approved, pd_attended, pd_user,last_name, pd_replacement, pd_category FROM pd_sessions,user where $string strftime('%Y',pd_date) = '$year' and user.userid = pd_sessions.pd_user order by $orderby ";
			}
		
	//echo $sql;
	
	
		echo "
		
		<script type=\"text/javascript\">
		
		var queue = [];
		var xhr = null;
		
		var email_user = $email_user;
		
		
		function update_approved(id)
		{
		
		var values = id.split('_')
		var action = values[0];
		var userid = values[1];
		//alert(action);
		//alert(userid)
		//var key = action + '_' + id
		var element = document.getElementById(id);
		//alert(element)
		var td = element.parentNode
		td.style.background = 'url(\"../images/ajax_progress2.gif.\")';
		//td.style.background-color: transparent
		var value = element.options[element.selectedIndex].value ;
		var email = email_user;
		
		
		if (email_user == 2)
		{
		
		var reply = confirm('Send email notification');
		if (reply) {email = 3} else {email = 1}

		
		}
		
		
		var url = \"ajax.php?m=$this->m&event=updatepd&action=\" + action + \"&id=\" + userid + \"&value=\" + value + \"&email=\" + email
		//alert(url)
		var values = [id,url]
		
		 
		queue.push(values)
		queuecheck()
		
		}
		
		
		
		
		
			function update_attended(id)
		{
		
		var values = id.split('_')
		var action = values[0];
		var userid = values[1];
		//alert(action);
		//alert(userid)
		//var key = action + '_' + id
		var element = document.getElementById(id);
		//alert(element)
		var td = element.parentNode
		td.style.background = 'url(\"../images/ajax_progress2.gif.\")';
		//td.style.background-color: transparent
		var value = element.options[element.selectedIndex].value ;
		
	
		
		
		var url = \"ajax.php?m=$this->m&event=updatepd&action=\" + action + \"&id=\" + userid + \"&value=\" + value + \"&email=\" + 1
		var values = [id,url]
		
		 
		queue.push(values)
		queuecheck()
		
		}
		
		
		
		
			function update_cat(id)
		{
		
		var values = id.split('_')
		var action = values[0];
		var pd_id = values[1];
		//alert(action);
		//alert(userid)
		//var key = action + '_' + id
		var element = document.getElementById(id);
		//alert(element)
		var td = element.parentNode
		td.style.background = 'url(\"../images/ajax_progress2.gif.\")';
		//td.style.background-color: transparent
		var value = element.options[element.selectedIndex].value ;
		
	
		
		
		var url = \"ajax.php?m=$this->m&event=updatepd&action=\" + action + \"&id=\" + pd_id + \"&value=\" + value 
	//	alert(url)
		var values = [id,url]
		
		 
		queue.push(values)
		queuecheck()
		
		}
		
		
		
		
		function queuecheck()
		{
		
		if (xhr == null)
		{
		
		if (queue.length > 0)
		{
		//alert (\"queue exists\")
		var values = queue.shift()
		ajax(values)
		}
		else 
		{
		//alert (\"no queue exists\")
		}
		
		}
		
		
		}
		
		function ajax(values){
	

	
		
		
		
		if (window.XMLHttpRequest) {
			xhr = new XMLHttpRequest();
			
		}
		else 
			if (window.ActiveXObject) {
				try {
					xhr = new ActiveXObject(\"Msxml2.XMLHTTP\");
				} 
				catch (e) {
					try {
						xhr = new ActiveXObject(\"Microsoft.XMLHTTP\");
					} 
					catch (e) {
					}
				}
			}
		
		
		
		xhr.onreadystatechange = function(){
		
		
			if (xhr.readyState == 4) {
			
			if (xhr.status == 200)
			{
			changecolour(values[0],xhr.responseText)
			//alert(xhr.responseText)
			}
			
			xhr = null;
			queuecheck();
			}
			
		}
		
		
		var timestamp = new Date();
		
		var fullurl = values[1] + \"&rt=\" + timestamp.getTime()
		//alert(fullurl)
		xhr.open(\"GET\", fullurl, true);
		xhr.send(null);
		
	
			
	}
	
		
		
		
		
		function changecolour(id,colour)
		{
		
		var element = document.getElementById(id);
		element.parentNode.style.background = colour;
		
		}
		
		
		</script>
		
		
		
		
		
		<table class=\"colourtable\" style=\"margin:1em auto;text-align:left\">
		<tr style=\"font-weight:bold\" class=\"accent\"><td><a href=\"pd.php?event=listpd&amp;m=$this->m&amp;filter=$filter&amp;year=$year&amp;orderby=pd_id$linkstring\">PD ID</a></td>
		<td><a href=\"pd.php?event=listpd&amp;m=$this->m&amp;filter=$filter&amp;year=$year&amp;orderby=pd_date$linkstring\">Date</a></td>
		<td><a href=\"pd.php?event=listpd&amp;year=$year&amp;m=$this->m&amp;filter=$filter&amp;orderby=last_name$linkstring\">Staff</a></td>
		<td><a href=\"pd.php?event=listpd&amp;year=$year&amp;m=$this->m&amp;filter=$filter&amp;orderby=pd_title$linkstring\">Title</a></td>
		<td><a href=\"pd.php?event=listpd&amp;year=$year&amp;m=$this->m&amp;filter=$filter&amp;orderby=pd_category$linkstring\">Category</a></td>
		<td>Description</td>";
		
	if (isset($custom_fields))
		{
		//foreach ($custom_fields as $key => $value)

		//{
			echo "<td>Details</td>";
		//}
		}
		
		echo "<td><a href=\"pd.php?event=listpd&amp;year=$year&amp;m=$this->m&amp;filter=$filter&amp;orderby=pd_external$linkstring\">External</a></td><td><a href=\"pd.php?event=listpd&amp;year=$year&amp;m=$this->m&amp;filter=$filter&amp;orderby=pd_hours$linkstring\">Hours</a></td>
		<td><a href=\"pd.php?event=listpd&amp;year=$year&amp;m=$this->m&amp;filter=$filter&amp;orderby=pd_cost$linkstring\">Cost</a></td>
			<td><a href=\"pd.php?event=listpd&amp;year=$year&amp;m=$this->m&amp;filter=$filter&amp;orderby=pd_replacement$linkstring\">Replace</a></td>
		<td><a href=\"pd.php?event=listpd&amp;year=$year&amp;m=$this->m&amp;filter=$filter&amp;orderby=pd_approved$linkstring\">Approved</a></td><td><a href=\"pd.php?event=listpd&amp;year=$year&amp;m=$this->m&amp;filter=$filter&amp;orderby=pd_attended$linkstring\">Attended</a></td><td></td></tr>";	
		
		$external[0] = "Internal";
		$external[1] = "External";
		
		$yesno[0] = "No";
		$yesno[1] = "Yes";
		$yesno[2] = "Deleted";
		
		
			
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
					$pd_user = htmlspecialchars($row["pd_user"]);
					$pd_date = strftime("%x",$row["pd_date"]);
					$pd_custom = $row["pd_custom"];
					$pd_replacement = htmlspecialchars($row["pd_replacement"]);
				    $pd_category = htmlspecialchars($row["pd_category"]);
					
					$approved_id = "approved_" . $pd_id;
					$attended_id = "attended_" . $pd_id;
						
					$cat_id = "pdcat_" . $pd_id;
					
					echo "<tr><td>$pd_id</td><td>$pd_date</td><td>$users[$pd_user]</td><td>$pd_title</td><td><select id=\"$cat_id\" onchange=update_cat('$cat_id')>";
					
					foreach ($categories as $key => $cat_name)
					{
						echo "<option value=\"$key\"";
						if ($pd_category == $key) { echo " selected";}
						echo ">$cat_name</option>";
						
					}
					
					
					echo "</select>
			</td>
					
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
					
		 
				$pd_cost = number_format ($pd_cost, 2);
				$pd_replacement = number_format ($pd_replacement, 2);
					
					
					echo "<td>$external[$pd_external]</td>
					<td style=\"text-align:right\">$pd_hours</td><td style=\"text-align:right\">$$pd_cost</td><td style=\"text-align:right\">$$pd_replacement</td><td ";
					if ($pd_approved == 1) {echo " style=\"background:#B1D8A9\"";} elseif ($pd_approved == 2) {echo " style=\"background:#bfc7ce\"";} else {echo " style=\"background:#E05C5C\"";}
					echo ">
					<select id=\"$approved_id\" onchange=update_approved('$approved_id')>
					<option value=\"0\">No</option>
					<option";
					if ($pd_approved == 1) {echo " selected";}
					echo " value=\"1\">Yes</option>
					<option";
					if ($pd_approved == 2) {echo " selected";}
					echo " value=\"2\">Deleted</option>
					</select>
					
				</td><td";
					if ($pd_attended == 1) {echo " style=\"background:#B1D8A9\"";} else {echo " style=\"background:#E05C5C\"";}
					echo ">
					<select id=\"$attended_id\" onchange=update_attended('$attended_id')>
					<option value=\"0\">No</option>
					<option";
					if ($pd_attended == 1) {echo " selected";}
					echo " value=\"1\">Yes</option>
					</select>
			</td>
					<td><a href=\"pd.php?m=$this->m&amp;event=editpd&amp;pd_id=$pd_id\">Edit</a></td></tr>";	
					}
	echo "</table>
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

	<form method=\"post\" action=\"pd.php\"  style=\"clear:both;margin-top:2em;\">
	<table style=\"margin:0 auto\"><tr><td>
	<table>";
   
   	
	echo "<tr><td class=\"label\">Title</td><td><input name=\"title\" type=\"text\" size=\"50\"></td></tr>
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
	<input name=\"hours\" type=\"text\" id=\"hours\" value=\"0\"><span id=\"hourswarning\"></span>
	</td></tr>
	<tr><td class=\"label\">Cost $</td><td>
		<input name=\"cost\" type=\"text\" value=\"0.00\" id=\"cost\"><span id=\"costwarning\"></span></td></tr>
	<tr><td class=\"label\">Replacement $</td><td>
		<input name=\"replacement\" type=\"text\" value=\"0.00\" id=\"replacement\"><span id=\"replacewarning\"></span><br>$replacement_text</td></tr>
	<tr><td class=\"label\">Approved</td><td>
	<select name=\"approved\">
	<option value=\"1\">Yes</option>
	<option value=\"0\">No</option>
	</select>
	</td></tr>
	<tr><td class=\"label\">Attended</td><td>
	<select name=\"attended\">
	<option value=\"0\">No</option>
	<option value=\"1\">Yes</option>
	
	</select>
	</td></tr>";
			
			
			
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
			
			
			
		$sql = "select * from pd_custom_fields where m = '$this->m' order by ranking";
		
	$counter = 0;	
						
	$this->DB->query($sql,"pdadmin.php");
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
			
		

	

	
	
	
	echo "<tr><td></td><td>
	<input name=\"update\" type=\"hidden\" value=\"insertpd\">
	<input name=\"m\" type=\"hidden\" value=\"$this->m\">
	<input name=\"event\" type=\"hidden\" value=\"listpd\">
	<input name=\"filter\" type=\"hidden\" value=\"all\">
	<input name=\"year\" type=\"hidden\" value=\"$year\">
	<input name=\"Add PD\" value=\"Add PD\" type=\"submit\" onclick=\"return staff()\"></td></tr>
	</table></td>
	<td>
	
	<div style=\"float:left;\"><p style=\"font-size:1.5em;margin:0;\">Staff</p>
	<input type=\"button\" onclick=\"checkboxes(true);\" value=\"Select all\">

<input type=\"button\" onclick=\"checkboxes(false);\" value=\"Unselect all\"><br><br>
	<ul class=\"listing\" style=\"\">";
	
   	
 
   	
	$sql = "select userid, username, first_name, last_name from user where username != 'administrator' and username != 'guest' and disabled = '0' order by last_name";
	
	$this->DB->query($sql,"pdedit.php");
	while ($row = $this->DB->get())
	{
	$userid = $row["userid"];
	$first_name = $row["first_name"];
	$last_name = $row["last_name"];	
	echo "<li><input type=\"checkbox\" name=\"user[$userid]\" class=\"staff\"> $last_name, $first_name</li>";
	}
	
	echo "</ul>
	
	<br>
	
	
	
	</div>
	</td></tr></table>
	</form>
	
	<script language=\"javascript\" src=\"pd.js\"></script>
	";
		
   }
   
    private function edit_pd()
   {
   	
	$pd_id = $this->DB->escape($_REQUEST["pd_id"]);
	
	$sql = "select * from pd_options";		
	$this->DB->query($sql,"pdedit.php");
	$row = $this->DB->get();
	{
	$email_user = $row["email_user"]; 
	$replacement_text = nl2br($row["replacement_text"]); 
	}
					
   		if ($this->DB->type == "mysql")
			{
		$sql = "SELECT last_name, first_name,pd_description, pd_id,pd_custom, UNIX_TIMESTAMP(pd_date) as pd_date, pd_title, pd_external, pd_hours, pd_cost, pd_approved, pd_attended, pd_user,last_name, pd_replacement, pd_category,pd_group FROM pd_sessions,user where pd_id = '$pd_id' and user.userid = pd_sessions.pd_user ";
			}
		else
			{
		$sql = "SELECT last_name, first_name,pd_description, pd_id, pd_custom, pd_title,strftime('%s',pd_date) as pd_date, pd_external, pd_hours, pd_cost, pd_approved, pd_attended, pd_user,last_name, pd_replacement, pd_category,pd_group  FROM pd_sessions,user where  pd_id = '$pd_id' and user.userid = pd_sessions.pd_user ";
			}
	//echo $sql;
	$this->DB->query($sql,"pdedit.php");
					$row = $this->DB->get();
				
					$pd_title = htmlspecialchars($row["pd_title"]);	
					$pd_id = htmlspecialchars($row["pd_id"]);	
					$pd_external = htmlspecialchars($row["pd_external"]);
					$pd_hours = htmlspecialchars($row["pd_hours"]);
					$pd_cost = htmlspecialchars($row["pd_cost"]);
					$pd_approved = htmlspecialchars($row["pd_approved"]);
					$pd_attended = htmlspecialchars($row["pd_attended"]);
					$pd_user = htmlspecialchars($row["pd_user"]);
					$pd_date = $row["pd_date"];	
					$custom_data = $row["pd_custom"];	
					$pd_group = $row["pd_group"];		
					$last_name = htmlspecialchars($row["last_name"]);	
					$first_name = htmlspecialchars($row["first_name"]);	
					$pd_description = htmlspecialchars($row["pd_description"]);	
					$pd_replacement = htmlspecialchars($row["pd_replacement"]);	
					$day = date("d",$pd_date);
					$month = date("m",$pd_date);
					$year = date("Y",$pd_date);
					$pd_category = htmlspecialchars($row["pd_category"]);
			
			
   	echo "
	<h2  style=\"text-align:center\">Edit PD</h2>
	<form method=\"post\" action=\"pd.php\" class=\"swouter\">
	<table class=\"swinner\">
	<tr><td class=\"label\">Title</td><td><input name=\"title\" type=\"text\" size=\"50\" value=\"$pd_title\"></td></tr>
	<tr><td class=\"label\">Staff name</td><td>$last_name, $first_name</td></tr>
	<tr><td class=\"label\">Description</td><td><textarea name=\"description\" cols=\"60\" rows=\"10\">$pd_description</textarea></td></tr>
	<tr><td class=\"label\">Commence date</td><td>
	<select name=\"day\">";
		
			$counter = 1;
			while ($counter < 32)
				{
				$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
				echo "<option value=\"$counter\"";
				if ($counter == $day) {echo " selected";}
				echo ">$counter</option>";
				$counter++;
				}
			
			
			
			echo "</select>
			/ <select name=\"month\">";
			$counter = 1;
			while ($counter < 13)
				{
				$counter = str_pad($counter, 2, "0", STR_PAD_LEFT);
				echo "<option value=\"$counter\"";
				if ($counter == $month) {echo " selected";}
				echo ">$counter</option>";
				$counter++;
				}
			
			echo "</select>
		
	
	
	 / <select name=\"year\">";
		$year--; 
			echo "<option value=\"$year\">$year</option>";
		$year++; 
			echo "<option value=\"$year\" selected>$year</option>";
		$year++; 
			echo "<option value=\"$year\">$year</option>";
	
		
			
			
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
		<input name=\"cost\" type=\"text\"  value=\"$pd_cost\" id=\"cost\"> <span id=\"costwarning\"></span></td></tr>
	<tr><td class=\"label\">Replacement $</td><td>
		<input name=\"replacement\" type=\"text\" value=\"$pd_replacement\" id=\"replacement\"><span id=\"replacewarning\"></span><br>$replacement_text</td></tr>
	<tr><td class=\"label\">Approved</td><td>
	<select name=\"approved\">
	<option value=\"1\">Yes</option>
	<option value=\"0\"";
	if ($pd_approved == 0) {echo " selected";}
	echo ">No</option>
	</select>
	</td></tr>
	<tr><td class=\"label\">Attended</td><td>
	<select name=\"attended\">
	<option value=\"0\">No</option>
	<option value=\"1\"";
	if ($pd_attended == 1) {echo " selected";}
	echo ">Yes</option>
	
	</select>
	</td></tr>";
	
	
	
		
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
	
	$custom_xml = simplexml_load_string($custom_data);
	
	
	
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
	$custom_ranking[$counter] = $fieldno;
	
	$val = "";
	
	if ($custom_xml)
	{
	 foreach ($custom_xml as $field) 
		 {
	
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
							echo "<option value=\"$value\"";
								if ($val == $value) {echo " selected";}
							echo "> $value</option>";
							}
	
	echo "</select>";}
	
	if ($comment != "") {echo "<br>$comment";}
	echo "</td></tr>";
		
	}		
		
	

	
	
	
	
	
	
	echo "
	
	";
	if ($pd_group > 0)
	{
	echo "<tr><td class=\"label\">Update scope</td><td><select name=\"updategroup\">
	<option value=\"no\">Update this session only</option>
	<option value=\"yes\">Update all sessions in this session group</option>
	</select></td></tr>";	
		
	}
	
	echo "
	<tr>	<td></td><td><input name=\"update\" type=\"hidden\" value=\"updatepd\">
	<input name=\"pd_group\" type=\"hidden\" value=\"$pd_group\">
	<input name=\"m\" type=\"hidden\" value=\"$this->m\">
	<input name=\"pd_id\" type=\"hidden\" value=\"$pd_id\">
	<input name=\"Edit PD\" value=\"Update PD\" type=\"submit\"></td></tr>
	</table>
	</form>
			<script language=\"javascript\" src=\"pd.js\"></script>
	";
		
   }
   
   
   
}

?>