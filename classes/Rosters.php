<?php

Class RosterShift
{
	
	private $types_id;
	private $types_name;
	private $types_displaytime;
	private $first_names;
	private $last_names;
	private $shifts_type;
	private $shifts_id;
	private $shifts_userid;
	private $managers;
	private $location_id;
	private $shifts_available;
	private $shifts_role;
	private $role_names;
	private $role_colours;
	private $keys;
	private $shifts_times;
	private $shifts_date;
	private $roles;
	private $phrase;
	

	
	
	public function __construct($m,$DB,$matches,$location_id,$phrase)
    {

    $this->location_id = $location_id;
    $this->phrase = $phrase;
  //  echo "location is $location_id"; print_r($matches);	
 // print_r($this->phrase);

	//get list of shifts
	$this->shifts_id = array();
	if (count($matches) > 0)
	{
	$start = $matches[0];
	$end = end($matches);
	$sql = "select * from roster_shifts, roster_types where roster_shifts.shift_type = roster_types.type_id 
	and roster_shifts.shift_location = '$location_id'
	and  shift_date >= '$start' and shift_date <= '$end'";
	$DB->query($sql,"rosterdmin.php");
	//echo $sql;
	while ($row = $DB->get())
	{

	$this->shifts_id[] = $row["shift_id"];
	$this->shifts_date[] = str_replace("-","",$row["shift_date"]);
	$this->shifts_available[] = $row["shift_available"];
	$shifts_published[] = $row["shift_published"];
	$this->shifts_userid[] = $row["userid"];
	$this->shifts_type[] = $row["shift_type"];
	$this->shifts_role[] = $row["shift_role"];
	$this->shifts_times[] = format_time($row["type_sh"],$row["type_sm"]) . " - " . format_time($row["type_eh"],$row["type_em"]);
	}
	reset($matches);
	}
	//print_r($this->shifts_date);
	
	//get list of staff 
	$sql = "select * from user ";
	 $DB->query($sql,"rosterdmin.php");
	while ($row = $DB->get())
	{

	$_userid = $row["userid"];
	$this->first_names[$_userid] = $row["first_name"];
	$this->last_names[$_userid] = $row["last_name"];
	}
	
	
	
	//get list of managers for this location
	$this->managers = array();
	$sql = "select * from roster_managers where manager_location = '$location_id' ";
	 $DB->query($sql,"rosterdmin.php");
	while ($row = $DB->get())
	{

	$this->managers[] = $row["manager_userid"];
	
	}
	//print_r($managers);
	
	//print_r($last_names);
	$sql = "select * from roster_staff left join roster_rolebridge on roster_rolebridge.userid = roster_staff.staff_userid 
	where roster_staff.staff_location = '$location_id' order by role ";
	 $DB->query($sql,"rosterdmin.php");
	//echo $sql;
	while ($row = $DB->get())
	{

	$_userid = $row["staff_userid"];
	$_roles[] = $row["role"];
	$this->roles[$_userid] = $row["role"];
	$this->keys[$_userid] = $row["staff_key"];
	}
	
	
	
	
	
	
	
	 
	//get list of roles
		$sql = "select * from roster_roles ";
	 $DB->query($sql,"rosterdmin.php");
	while ($row = $DB->get())
	{

	$_roleid = $row["role_id"];
	$this->role_names[$_roleid] = $row["role_name"];
	$this->role_colours[$_roleid] = $row["role_colour"];
	}
	
	
	//get list of shift types
	$types_name = array();
	 $sql = "select * from roster_types where m = '$m'";
	// echo $sql;
 $DB->query($sql,"rosterdmin.php");
while ($row = $DB->get())
{
	$this->types_id[] = $row["type_id"];
	$this->types_name[] = $row["type_name"];
	//$this->types_location[] = $row["type_location"];
	
	$types_sh[] = $row["type_sh"];
	$types_sm[] = $row["type_sm"];
	$types_eh[] = $row["type_eh"];
	$types_em[] = $row["type_em"];
	$this->types_displaytime[] = format_time($row["type_sh"],$row["type_sm"]) . " - " . format_time($row["type_eh"],$row["type_em"]);
	
	$this->days[][0] = $row["day_0"];
	$this->days[][1] = $row["day_1"];
	$this->days[][2] = $row["day_2"];
	$this->days[][3] = $row["day_3"];
	$this->days[][4] = $row["day_4"];
	$this->days[][5] = $row["day_5"];
	$this->days[][6] = $row["day_6"];
	
}	
	
	
	
	
} //end constructor


public function displayShift($date)
{
	
	//print_r($this->shifts_date);
	//echo "date is $date";
	
	$weekday = date("w",strtotime($date));
	
					
	
	
	
	
	//echo "weekday is $weekday ccc";
	
		foreach ($this->types_name as $t => $type_name)
		{
			
			foreach ($this->days[$t] as $d => $day)
		{
			if ($weekday == $d) 
			{
			$displaytime = $this->types_displaytime[$t];
			echo "<div style=\"margin:0;padding:0.4em\" class=\"accent\"><b>$displaytime</b>";
			foreach ($this->shifts_id as $s => $sid)
				{
				if ($this->shifts_type[$s] == $this->types_id[$t] && $this->shifts_date[$s] == $date)
					{
						$user = $this->shifts_userid[$s];
						
						if ($user == 0)
						{ $name = "Unassigned";}
						else {
							$last_name = $this->last_names[$user];
							$first_name = $this->first_names[$user];
							$name = "$last_name, $first_name";}
					
						$roleid = $this->shifts_role[$s];
						if ($roleid != "" && array_key_exists($roleid,$this->role_names))
						{
							$rname = $this->role_names[$roleid];
						$rolename = "<br>$rname";
						} else {$rolename = "";}
						
				
						
						
					echo "<div class=\"shift\"><br><span onclick=showMenu('$sid')>$name $rolename";
						if (array_key_exists($user,$this->keys) && $this->keys[$user] == 1) {echo "<br><img src=\"../images/key2.png\">";}	
					echo "</span>";
					if ($this->shifts_available[$s] == 1) {
						$text = $this->phrase[418];
						
						echo "<br><span class=\"red\">$text</span>";}
					echo "<div id=\"menu_$sid\">Action menu</div></div>";
					}
				}
			
			if (in_array($_SESSION["userid"],$this->managers))
			{ 
				$type = $this->types_id[$t];
				$location_id = $this->location_id ;
				echo "<br><br><div class=\"shift\"><img src=\"../images/add.png\" onclick=\"showStaff('$type','$date','$location_id')\" alt=\"Add\" title=\"Add\"><div id=\"addmenu_$type\">Action menu</div></div>";}	
				
				
			echo "</div><br>";
			}
		}	
			
		}
	
	
}

public function displayUser($user,$date,$rid)
{
	//print_r($this->shifts_date);
	//echo "user is $user date is $date";
	
	
	
	$menuid = "m_" . $date . "_" . $user;
	$location_id = $this->location_id;
	
	//print_r($this->roles);
	//if (array_key_exists($user,$this->roles))
	//{$rid = $this->roles[$user]; } else {$rid = "";}

	//print_r($this->shifts_id);	
	
	foreach ($this->shifts_id as $s => $sid)
				{
					
					//echo "user " . $this->shifts_userid[$s] . " date ";
					$test =  $this->shifts_role[$s];
					$testlen = strlen($test);
					$ridlen = strlen($rid);
					//echo "test $test $testlen rid $rid $ridlen <br><br><br>";
					$shiftuser = $this->shifts_userid[$s];
					$shiftdate = $this->shifts_date[$s];
					//echo "user $user $shiftuser date $date $shiftdate<br><br>";
					
					
				if ($this->shifts_userid[$s] == $user  && $date == $this->shifts_date[$s] && ($user != 0 || ($user == 0 && $rid == $this->shifts_role[$s])))
					{
					$displaytime = $this->shifts_times[$s];
					echo "<div class=\"shift\"><br><span onclick=showMenu('$sid')>$displaytime</span>";
					if ($this->shifts_available[$s] == 1) {
						$text = $this->phrase[418];
						echo "<br><span class=\"red\">$text</span>";}
					echo "<div id=\"menu_$sid\">Action menu</div></div>";	
						
					}
				}
	
			//print_r($this->managers);	
				
		if (in_array($_SESSION["userid"],$this->managers)) {echo "
	<div class=\"shift\" >
	<p style=\"background:white;white-space:pre\" onclick=\"showShift('$date','$user','$rid',$location_id)\"> + </p> 
	<div id=\"$menuid\">Action menu</div>
	</div>";}
	
}



} //end class

?>