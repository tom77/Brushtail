<?php

Class Widget

{
	public function __construct($m,$DB,$access,$t,$height)
    {
    //check permissions

    	//$access->check($m);
		
  
    
	if ($access->thispage > 0)
	{
 //get module name and type
    
 	$_target = explode("-",$m);
 	$m = $_target[0];
    
 	//	$_target[0]; is module id
    // $_target[1] is page_id if content widget;
    
    
	$sql = "select name ,input, type from modules where m = '$m'";


	$DB->query($sql,"Widgets.php");
	$row = $DB->get();
	
	$_name= $row["name"];
	$_type = $row["type"];	
	$_input = $row["input"];	

	
	//instantiate widget	
	if ($_type == "c")
		{
		$widget = new calWidget($m,$DB,$_name,$t);
		
		$widget->display();
		}
		
	if ($_type == "b")
		{
		$widget = new roomWidget($m,$DB,$_name);
		$widget->display();
		}
		
		
	if ($_type == "n")
		{
		$widget = new noticeWidget($m,$DB,$_name);
		$widget->display();
		}
		
	if ($_type == "p")
		{
		$widget = new contentWidget($m,$DB,$_name,$_target[1],$height);
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
	
	public function __construct($m,$DB,$modname,$t)
    {
   $this->m = $m;
  $this->modname = $modname;
  $this->linkname = urlencode($modname);
   $this->DB = $DB;
   //$t = time();

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
    	
    	
    
    	
    	
    	<div id=\"cal$this->m\"  class=\"widget accent\" style=\"padding:1em;margin: 0 0 2em  0\">";
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
		$this->DB->query($sql,"Widgets.php");
		
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
		
				$sql = "SELECT event_id, event_name,maxbookings, event_location,event_description,cancelled, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and month(FROM_UNIXTIME(event_start)) = \"$this->month\" and year(FROM_UNIXTIME(event_start)) = \"$this->year\" and day(FROM_UNIXTIME(event_start)) = \"$this->today\" and template = \"0\" and cancelled = '0' $where order by event_start";	
		}
		

			else
		{
		
				$sql = "SELECT event_id, event_name,maxbookings, event_location,event_description,cancelled, cat_colour, cat_takesbookings, event_start FROM cal_events, cal_cat  where cal_cat.cat_id = cal_events.event_catid and strftime('%m',datetime ( event_start , 'unixepoch','localtime' )) = \"$this->month\" and strftime('%Y',datetime ( event_start , 'unixepoch','localtime'  )) = \"$this->year\" and strftime('%d',datetime ( event_start , 'unixepoch','localtime' )) = \"$this->today\" and template = \"0\" and cancelled = '0' $where order by event_start";	
		}
	
		
		$this->DB->query($sql,"Widgets.php");
		
			
			while ($row = $this->DB->get()) 
					{
					$event_id =$row["event_id"];
					$event_name =$row["event_name"];
					$event_description = nl2br($row["event_description"]);
				$string  =  "<a href=\"../main/calendar.php?m=$this->m&amp;event=book&amp;event_id=$event_id\"";
				
				
				$number = "";
		for ($i=0; $i<4; $i++) { 
		$number .= rand(0,9);
		}
		
		
		$id = "we" . $number . "e" .$event_id;
				
				if ($event_description != "") {$string .= "onmouseover=\"showelement('$id','25')\" onmouseout=\"hideelement('$id')\"";}
				
				$string .= ">$event_name</a>";
				
				if ($event_description != "")		{ $string .= "	<div style=\"position:relative;z-index:1;\" >
		<p class=\"textballoon\" id=\"$id\">$event_description</p></div>";} else {$string .= "<br>";}
				
				
				
				
				$lines[] = $string;
					}
					
											echo "
<br><span style=\"font-size:1.4em\">$this->displaydate</span><br>";
											
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
    	
  		$displayname = htmlspecialchars($this->modname);
  		
    echo "
<table class=\"colourtable\" style=\"background:white;\">
<tr><td colspan=\"7\" >$displayname</td></tr>
<tr><td colspan=\"7\" class=\"primary\"> <span onclick=\"updatecal('$pm','$this->m','$this->linkname')\" class=\"cursor\">&lt;&lt; </span> <a href=\"calendar.php?m=$this->m&amp;t=$fd\">$this->monthname $this->year</a> <span onclick=\"updatecal($nm,'$this->m','$this->linkname')\" class=\"cursor\"> &gt;&gt;</span></td></tr>";
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
		$displaydate = strftime("%A %x",$t);
		$this->html = "<div  class=\"accent\" style=\"padding:1em;margin: 0 0 2em  0\"><p style=\"margin-bottom:1em;font-size:1.3em\">$this->name
		<br>$displaydate</p>
		<ul  style=\"padding:0;margin:0;\" class=\"listing\">";
		
		
		$_d = date("j");
		$_m = date("m");
		$_y = date("Y");
		
		$daystart = mktime(0,0,0,$_m,$_d,$_y);
		$dayend = mktime(0,0,0,$_m,$_d,$_y);
		
		  $sql = "select bookingno, starttime, endtime, resourcebooking.name as name,resource_name,resource,paid,ip,staffname,username,bookeddate,cancelip,cancelname,canceltime,cancelled,bookinggroup,resourcebooking.checkout as checkout,checkin from resourcebooking, resource 
   where  resource.m = '$this->m' and resource.resource_no = resourcebooking.resource 
   and  ((starttime >= '$daystart' and starttime < '$dayend')
   or   (endtime > '$daystart' and endtime <= '$dayend') 
   or   (starttime <= '$daystart' and endtime >= '$dayend'))  and cancelled = '0' order by starttime ";
		
		// $sql = "select * from resource where m = \"$this->m\"";
	$this->DB->query($sql,"widgets.php");
	
	
	while ($row = $this->DB->get())
	{
		$bookingno =$row["bookingno"];		
		$bookname = formattext($row["name"]);
		
		$resource_name =$row["resource_name"];
		$resource_no =$row["resource"];
		$starttime = date("g:ia",$row["starttime"]);
		$endtime = date("g:ia",$row["endtime"]);
			
		$startdate = strftime("%x",$row["starttime"]);
		$enddate = strftime("%x",$row["endtime"]);
		
	if ($startdate == $enddate) {$times = "$starttime - $endtime";}
		else {$times =  "$startdate - $enddate";}
		
		$number = "";
		for ($i=0; $i<4; $i++) { 
		$number .= rand(0,9);
		}
		
		
		$id = "wr" . $number . "b" .$bookingno;
		
		$this->html .= "<li><a href=\"resourcebooking.php?m=$this->m&amp;event=edit&amp;bookingno=$bookingno&amp;resource_no=$resource_no&amp;t=$t\" 
		onmouseover=\"showelement('$id','25')\" onmouseout=\"hideelement('$id')\" 
		>$resource_name</a><div style=\"position:relative;z-index:1;\" ><p class=\"textballoon\" id=\"$id\"><b>$resource_name</b><br>
		$bookname<br>$times</p></div></li>";
	}
	
	$this->html .= "</ul></div>";
	}
		
	 public function display()
    {
    	echo $this->html;
    	
	}	
	
	
}



class noticeWidget{
	
	
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
		
		$this->html = "<div class=\"accent\" style=\"padding:1em;margin: 0 0 2em  0\"><p style=\"font-size:1.4em\">$this->name</p>
		<ul  style=\"padding:0;margin:0;\" class=\"listing\">";

	$sql = "select page_id from page where m = '$this->m'";
$this->DB->query($sql,"widgets.php");
$row = $this->DB->get();
$page_id = $row["page_id"];

		

	$sql = "select page_id from page where m = '$this->m'";
$this->DB->query($sql,"widgets.php");
$row = $this->DB->get();
$page_id = $row["page_id"];
		
	$sql = "SELECT * FROM content where page_id = '$page_id' and archive is null and deleted = '0' order by content_id desc limit 8";
	$this->DB->query($sql,"widgets.php");
	
	while ($row = $this->DB->get())
	{
		$content_id = $row["content_id"];
		$title = $row["title"];
		$body = nl2br($row["body"]);
		
				$number = "";
		for ($i=0; $i<4; $i++) { 
		$number .= rand(0,9);
		}
		
		
		$id = "wn" . $number . "b" .$content_id;
		
		$this->html .= "<li><a href=\"noticeboard.php?m=$this->m&amp;scroll=$content_id\" onmouseover=\"showelement('$id','18')\" 
		onmouseout=\"hideelement('$id','100')\" >$title</a>
		<div style=\"position:relative;z-index:1;\" >
		<p class=\"textballoon\" id=\"$id\">$body</p></div>
		
		
		</li>";
	}
	
	$this->html .= "</ul></div>";
	}
		
	 public function display()
    {
    	echo $this->html;
    	
	}	
	
	
}

class contentWidget{
	
	
	private $DB;
	private $name;
	private $m;
	private $html;
	private $height;
	
	
	
	public function __construct($m,$DB,$name,$page_id,$height)
	{
	$this->DB = $DB;
	$this->name = $name;
	$this->m = $m;
	$this->page_id = $page_id;
	$this->height = $height;

	}
			
	public function display()
	{
		
		
				$id = "ws";
		for ($i=0; $i<8; $i++) { 
		$id .= rand(0,9);
		}
		
		
		
		
		echo "<div id=\"$id\" style=\"margin:2em 0;\"></div>
		
	

		
		
		<script type=\"text/javascript\">
		
		var slider$id = new slider('$id','$this->height')
		
";
		
	$url = "../main/feed.php?m=$this->m&page_id=$this->page_id";	
		
	echo "

	slider$id.ajax('$url');
	//addEvent(window, 'load', slider$id.fade);
	</script>
	
	
<span onclick=\"slider$id.backslide()\" style=\"padding-right:2em\"><img src=\"../images/rewind.png\" title=\"Previous\" alt=\"Previous\"></span>
<span onclick=\"slider$id.status('label_$id')\" id=\"label_$id\" style=\"padding-right:2em\"><img src=\"../images/pause.png\" title=\"Pause\" alt=\"Pause\"></span>
<span onclick=\"slider$id.slide()\"><img src=\"../images/fast_forward.png\" title=\"Next\" alt=\"Next\"></span>
	
	
	
	";	
		
	}
	
}


?>