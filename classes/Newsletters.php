<?php

Class Newsletter

{
	public function __construct($m,$DB,$access)
    {
    //check permissions

    	$access->check($m);
		
    	$access->thispage;
    
	if ($access->thispage > 0)
	{
 //get module name and type
    
 	
    	
 
	$sql = "select name , type from modules where m = '$m'";


	$DB->query($sql,"Widgets.php");
	$row = $DB->get();
	
	$_name= $row["name"];
	$_type = $row["type"];	

	
	//instantiate widget	
	if ($_type == "c")
		{
		$widget = new calWidget($m,$DB,$_name);
		$widget->display();
		}
		
	if ($_type == "b")
		{
		$widget = new roomWidget($m,$DB,$_name);
		$widget->display();
		}
	}
    
    
   
    
    
	
	
    }
}



Class calWidget
{
	
	private $DB;
	private $m;
	private $modname;
	private $linkname;
	private $today;
	private $month;
	private $year;
	private $monthname;
	private $daysinmonth;
	private $displaydate;
	
	public function __construct($m,$DB,$modname)
    {
   $this->m = $m;
  $this->modname = $modname;
  $this->linkname = urlencode($modname);
   $this->DB = $DB;
   $t = time();
  
   if (isset($t)) {$this->today = date("d",$t);} else {$this->today = date("d");}
   if (isset($t)) {$this->month = date("m",$t);} else {$this->today = date("d");}
   if (isset($t)) {$this->year = date("Y",$t);} else {$this->today = date("Y");}
   if (isset($t)) {$this->monthname= strftime("%B",$t);} else {$this->monthname = strftime("%B");}
    if (isset($t)) {$this->displaydate= strftime("%A %x",$t);} else {$this->displaydate = strftime("%A %x");}
    if (isset($t)) {$this->daysinmonth= date("t",$t);  } else {$this->daysinmonth = date("t");  }
    }

    
   public function display()
    {
    	$linkname = urlencode($this->modname);
    	echo "
    	
    	
    
    	
    	
    	<div id=\"cal$this->m\" class=\"widget\">";
    $this->cal();
    

 
    $this->listing();
    
    
    
    
    echo "</div>
  
    
   
   ";
    }
 
      public function listing()
   {
   

   
   
   	$where = "and event_catid IN ("; 
		$typecount = 1;
		$sql = "SELECT cat FROM cal_bridge where m = \"$this->m\"";
		//echo "$sql <br>";
		$this->DB->query($sql,"newletters.php");
		
		$numrows = $this->DB->countrows();
		while ($row = $this->DB->get()) 
					{
					
					$cat =$row["cat"];
					if ($typecount == 1)
						{
						$where .= "'$cat'";
						}
					else
						{
						$where .= ",'$cat'";
						}
					$typecount++;
					
					}
					
		$where .= " )";
		if ($numrows == 0) {$where = "";}
		
		

		
  	if ($this->DB->type == "mysql")
		{
		
				$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and month(FROM_UNIXTIME(event_start)) = \"$this->month\" and year(FROM_UNIXTIME(event_start)) = \"$this->year\" and day(FROM_UNIXTIME(event_start)) = \"$this->today\" and template = \"0\" $where order by event_start";	
		}
		

			else
		{
		
				$sql = "SELECT event_id, event_name,maxbookings, event_location,cancelled, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and strftime('%m',datetime ( event_start , 'unixepoch','localtime' )) = \"$this->month\" and strftime('%Y',datetime ( event_start , 'unixepoch','localtime'  )) = \"$this->year\" and strftime('%d',datetime ( event_start , 'unixepoch','localtime' )) = \"$this->today\" and template = \"0\" $where order by event_start";	
		}
	
		
		$this->DB->query($sql,"calendar.php");
		
			
			while ($row = $this->DB->get()) 
					{
					$event_id =$row["event_id"];
					$event_name =$row["event_name"];
				$lines[] =  "<a href=\"calendar.php?m=$this->m&amp;event=book&amp;event_id=$event_id\">$event_name</a><br>";
					}
					
											echo "
<br><br><b>Events</b><BR>$this->displaydate<BR><br>";
											
					if (isset($lines))
					{

						
						foreach ($lines as $index => $line)	
						{
						echo "$line";	
						}
						
					}
 

    		
    }
    
    
    
    public function cal()
    {
    	   
    	$fd  = mktime(0, 0, 0, $this->month  , 01, $this->year);
    	$nm = mktime(0, 0, 0, $this->month + 1 , 01, $this->year);
    	$pm = mktime(0, 0, 0, $this->month - 1 , 01, $this->year);
  		$fd_date = date("w",$fd);
    	
    echo "
<table class=\"colourtable\" style=\"background:white;\">
<tr><td colspan=\"7\" >$this->modname</td></tr>
<tr><td colspan=\"7\" class=\"primary\"> <span onclick=\"updatecal('$pm','$this->m','$this->linkname')\" class=\"cursor\">&lt;&lt; </span> <a href=\"calendar.php?m=$this->m&t=$fd\">$this->monthname $this->year</a> <span onclick=\"updatecal($nm,'$this->m','$this->linkname')\" class=\"cursor\"> &gt;&gt;</span></td></tr>";
    	  //display blank cells at start of month
   $counter = 0;
   if ($fd_date <> 0)
   	{
	echo "<tr >";
	}
   while ($counter < $fd_date)
   	{
	echo "<td>";
	
	
	
	echo "</td>";
	if ($counter == 6)
		{
		echo "</tr>\n";
		}
	$counter++;
	}
   
   
   //display month as table cells
   $daycount = 1;
   while ($daycount <= $this->daysinmonth)
   	{
	$endline = (($counter + $daycount) % 7);
	$day = str_pad($daycount, 2, "0", STR_PAD_LEFT);
	$dayname  = strftime("%A",mktime(0, 0, 0, $this->month, $daycount,  $this->year));
	$t = mktime(0, 0, 0, $this->month, $day,  $this->year);
	if ($endline == 1) { echo "<tr>";}
	echo "<td valign=\"top\"";
	//if ($thisyear.$thismonth.$thisday == $year.$month.$day)
	if ($this->today == $daycount)
				{
				echo " id=\"scrollpoint\" class=\"accent\"";
				}
				
				
	
	
	echo "><span onclick=\"updatecal('$t','$this->m','$this->linkname')\">$daycount</span></td>";
	
   				
				
				
			   if ( $endline == 0)
			   	{
				echo "</tr>\n";
				}
				$daycount++;
   }
   
   //displays blank cells at end of month
   
   if ($endline <> 0)
   	{
	while (($endline) < 7)
		{
		echo "<td></td>";
		
		if ($endline == 7)
		{
		echo "</tr>";
		}
	$endline++;
		
		
		
		}
	
	
	}
   echo "

</table>";
  
    }
}

class roomWidget{
	
	
	private $DB;
	private $name;
	private $m;
	private $html;
	
	
	
	public function __construct($m,$DB,$name)
	{
	$this->DB = $DB;
	$this->name = $name;
	$this->m = $m;
	$this->listing();
	}
			
	private function listing()
	{
		
		$t = time();
		
		$this->html = "<div><p style=\"margin-bottom:1em;font-weight:bold\">$this->name</p>
		<ul  style:padding;0;margin:0;>";
		 $sql = "select * from resource where m = \"$this->m\"";
	$this->DB->query($sql,"widgets.php");
	
	
	while ($row = $this->DB->get())
	{
		$resource_name =$row["resource_name"];
		$resource_no =$row["resource_no"];
		$this->html .= "<li><a href=\"resourcebooking.php?m=$this->m&amp;event=cal&amp;resource_no=$resource_no&t=$t\">$resource_name</a></li>";
	}
	
	$this->html .= "</ul></div>";
	}
		
	 public function display()
    {
    	echo $this->html;
    	
	}	
	
	
}

?>