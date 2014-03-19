<?php


function tidycontent($storage,$docpath,$num,$month,$year,$DB)
{
	
	$cutoff = mktime (0,0,0,$month - $num,01,$year);
	
	

	$sql = "select content_id, title, body, content.page_id as page_id, event, archive, updated_when, m from content, page where page.page_id = content.page_id and event != '0' and updated_when < $cutoff";
	
	$DB->query($sql,"cron.php");
	$row = $DB->get();
	$content_id = $row["content_id"];
	$event = $row["event"];
	$title = $row["title"];
	$archive= $row["archive"];
	$updated_when = $row["updated_when"];
	$page_id = $row["page_id"];
	$image_id = $row["body"];
	$doc_id = $row["body"];
	$widget_id = $row["body"];
	$m = $row["m"];

	if ($event == 0 || $event == 15)
	{}
	elseif ($event == 3)
	{
		//image deleted
		$restore = 0;
		$sql = "select max(updated_when) as restore from content where event = '7' and body = '$image_id'";
		//
		$DB->query($sql,"functions.php");
		$row = $DB->get();
		$restore = $row["restore"];


		if ($updated_when > $restore)
		{
                        
                        if ($storage == "file")
                        {
			$sql = "select filename from images where image_id = \"$image_id\"";
			$DB->query($sql,"cron.php");
			$row = $DB->get();
			$filename = $row["filename"];

			$fullpath = $docpath ."/".$m."/".$page_id."/".$filename;
			if (file_exists($fullpath))
			{
				@unlink($fullpath);
			}
                        }
			$sql = "delete from images where image_id = \"$image_id\"";
			
			$DB->query($sql,"functions.php");

		}
	}
		elseif ($event == 5)
		{
			//document deleted
			$restore = 0;
			$sql = "select max(updated_when) as restore from content where event = '8' and body = '$doc_id'";
			//
			$DB->query($sql,"functions.php");
			$row = $DB->get();
			$restore = $row["restore"];


			if ($updated_when > $restore)
			{
                                 if ($storage == "file")
                                 {
				$sql = "select filename from documents where doc_id = \"$doc_id\"";
				$DB->query($sql,"cron.php");
				$row = $DB->get();
				$filename = $row["filename"];

				$fullpath = $docpath ."/".$m."/".$page_id."/".$filename;
				if (file_exists($fullpath))
				{
					@unlink($fullpath);
				}
                                 }
				$sql = "delete from documents where doc_id = \"$doc_id\"";
				
				$DB->query($sql,"functions.php");

			}
		}

		elseif ($event == 9 || $event == 17)
		{
			//paragraph deleted
			$restore = 0;
			$sql = "select max(updated_when) as restore from content where (event = '11' or event= '18') and archive = '$archive'";
			//
			$DB->query($sql,"functions.php");
			$row = $DB->get();
			$restore = $row["restore"];


			if ($updated_when > $restore)
			{
                                 if ($storage == "file")
                                 {
				$sql = "select filename, doc_id from documents where content_id = \"$archive\"";
				
				$DB->query($sql,"cron.php");
				while ($row = $DB->get())
				{
					$docfilenames[] = $row["filename"];
					$doc_ids[] = $row["doc_id"];
				}
		
				
				if (isset($doc_ids))
				{
					foreach ($doc_ids as $index => $doc_id)
					{

						$fullpath = $docpath ."/".$m."/".$page_id."/".$docfilenames[$index];
						if (file_exists($fullpath))
						{
							@unlink($fullpath);
						}
						$sql = "delete from documents where doc_id = \"$doc_id\"";
						
						$DB->query($sql,"cron.php");
					}


				}
                                 } else {
                                     
                                     $sql = "delete from documents where content_id = \"$archive\"";
                                    $DB->query($sql,"cron.php");
                                 }

                                   if ($storage == "file")
                                 {
				$sql = "select filename, image_id from images where content_id = \"$archive\"";
				$DB->query($sql,"cron.php");
				while ($row = $DB->get())
				{
					$imagefilenames[] = $row["filename"];
					$image_ids[] = $row["image_id"];
				}

				if (isset($image_ids))
				{
					foreach ($image_ids as $index => $image_id)
					{

						$fullpath = $docpath ."/".$m."/".$page_id."/".$imagefilenames[$index];
						if (file_exists($fullpath))
						{
							@unlink($fullpath);
						}
						$sql = "delete from images where image_id = \"$image_id\"";
						
						$DB->query($sql,"functions.php");
					}


				}
                                 } else {
                                     $sql = "delete from images where content_id = \"$archive\"";
				$DB->query($sql,"cron.php");
                                 }

				$sql = "delete from content where content_id = '$archive'";
				
				$DB->query($sql,"cron.php");
				
				if (substr($title,0,10) == "--WIDGET--")
				{
				$sql = "delete from widgets where id = '$widget_id'";
				$DB->query($sql,"cron.php");
				}
				

			}
		}

		
		elseif ($event == 13)
	{
		//page deleted
		$restore = 0;
		$sql = "select max(updated_when) as restore from content where event = '14' and page_id = '$page_id'";
		//
		$DB->query($sql,"cron.php");
		$row = $DB->get();
		$restore = $row["restore"];


		if ($updated_when > $restore)
		{
			//set paragraphs as deleted to be deleted upon subsequent logins
		$sql = "update content set deleted = '1',event = '9', archive = content_id where page_id = '$page_id' and event = '0'";
		
		$DB->query($sql,"cron.php");
		
	
		}

		
		
		
		

	}
	
		//deleted old deleted empty pages
		$sql = "SELECT page.page_id as page_id FROM page left join content on page.page_id = content.page_id where page.deleted = '1' and content.page_id is null";
		$DB->query($sql,"cron.php");
		$row = $DB->get();
		$deletepage = $row["page_id"];
		if (isset($deletepage))
		{
		$sql = "delete from page where page_id = '$deletepage'";
		
		$DB->query($sql,"cron.php");	
		}
	
	
		if (isset($content_id))
		{
		$sql = "delete from content where content_id = '$content_id'";
		
		$DB->query($sql,"cron.php");
		}
		
		
			if ($DB->type == "mysql")
		{
		$DB->tidy("page");	
		$DB->tidy("content");	
		$DB->tidy("images");
		$DB->tidy("documents");
		
		}
		
			else
		{
		$DB->tidy("database");	
		}	
		
		
		return;
	}


	function tidyevents($PREFERENCES,$month,$year,$DB)
	{
//	echo "deleting events";
		
		//$m =  date("m",mktime (0,0,0,$month - $num,01,$year));
		//$y =  date("Y",mktime (0,0,0,$month - $num,01,$year));
		//$cutoff = $y.$m."00000000";
		$cutoff = mktime (0,0,0,$month - $PREFERENCES["deleteevents"],01,$year);

		
		if ($DB->type == "mysql")
		{
			
			
		$sql = "delete cal_bookings, cal_events from cal_events inner join cal_bookings where cal_events.event_start < '$cutoff' and cal_bookings.eventno = cal_events.event_id";
		}
		else
		{
		$sql = "delete from cal_bookings where bookingno in  (select bookingno from cal_events,
cal_bookings where cal_events.event_start < '$cutoff' and cal_bookings.eventno = cal_events.event_id)";
		}
		
		//echo $sql;
		
		$DB->query($sql,"cron.php");
		
		
		//$sql = "select image_id, page, m FROM images, cal_events, cal_bridge WHERE cal_bridge.cat = cal_events.event_catid
		//AND images.page = cal_events.event_id AND event_start < '$cutoff'";
		
		$sql = "select event_id from cal_events where event_start < '$cutoff'";
		$DB->query($sql,"cron.php");
		$event_id = array();
		while ($row = $DB->get())
		{
		$event_id[] = $row["event_id"];
		}
		
		foreach ($event_id as $id)
		{
		deletecalfolder($id,$PREFERENCES);
		$sql = "delete from images where page = '$id' and modtype = 'c'"; 
		$DB->query($sql,"cron.php");
		}
	
		$sql = "delete from cal_events where event_start < '$cutoff'";
		$DB->query($sql,"cron.php");
		
			if ($DB->type == "mysql")
		{
		$DB->tidy("cal_events");
		$DB->tidy("cal_bookings");
		}
		
			else
		{
		$DB->tidy("database");	
		}
	}


	function tidypcbookings($num,$month,$year,$DB)
	{


		$cutoff =  mktime (0,0,0,$month - $num,01,$year);
		$sql = "delete from pc_bookings where bookingtime < '$cutoff'";
		
		$DB->query($sql,"cron.php");

		$DB->tidy("pcbookings");
	}

	function tidyresourcebookings($num,$month,$year,$DB)
	{

	
		$cutoff =  mktime (0,0,0,$month - $num,01,$year);
                
                $sql = "delete from resource_custom_values, resourcebooking where resource_custom_values.booking = resourcebooking.bookingno and  starttime < '$cutoff'";
		
		$DB->query($sql,"cron.php");
                
                
                
                
		$sql = "delete from resourcebooking where starttime < '$cutoff'";
		
		$DB->query($sql,"cron.php");
                
                
                
                
                

		$DB->tidy("resourcebooking");
	}

	function tidypageviews($num,$month,$year,$DB)
	{
			

	
		//$cutoff =  mktime (0,0,0,$month - $num,01,$year);
                $cutoff = $year . "-" . $month . "-01";
		$sql = "delete from page_views where view_date < '$cutoff'";
		
		$DB->query($sql,"cron.php");

		$DB->tidy("page_views");
	
		
	}
	function tidylogins($num,$month,$year,$DB)
	{

	
		$cutoff =  mktime (0,0,0,$month - $num,01,$year);
		$cutoffdate = date("Y-m-d H:i:s",$cutoff);
		$sql = "delete from logins where logtime < '$cutoffdate'";
		
		$DB->query($sql,"cron.php");

		

		$sql = "delete from loginsfailed where logtime < '$cutoffdate'";
		$DB->query($sql,"cron.php");

	
		
		
		$sql = "delete from web_auth_log where authtime < '$cutoff'";
		$DB->query($sql,"cron.php");
		
	
		
		
			if ($DB->type == "mysql")
		{
		$DB->tidy("logins");	
		$DB->tidy("loginsfailed");	
		$DB->tidy("web_auth_log");
		}
		
			else
		{
		$DB->tidy("database");	
		}
	}


	function tidyrequests($num,$month,$year,$DB)
	{

		$cutoff =  mktime (0,0,0,$month - $num,01,$year);
		
		$cutoff = date("YmdHis",$cutoff);

		$sql = "delete from helpdesk where datereported < '$cutoff'";
		
		$DB->query($sql,"cron.php");

		$DB->tidy("helpdesk");

		$month=  date("m",mktime (0,0,0,$month - $num,01,$year));
		$cutoff = $year . $month . "01";

		$sql = "delete from itcalls where day < '$cutoff'";
		$DB->query($sql,"cron.php");

			$DB->tidy("itcalls");
			
			
			if ($DB->type == "mysql")
		{
		$DB->tidy("itcalls");	
		$DB->tidy("helpdesk");	
		
		}
		
			else
		{
		$DB->tidy("database");	
		}	
			
	}

        
        
        

	//update the job counter to determine which cleanup job will run on next login
	if ($PREFERENCES["cron"] > 11)
	{
	$counter = 0;
	}
	else 
	{
	$counter = $PREFERENCES["cron"] + 1;	
	}
	$sql = "update preferences set pref_value = '$counter' where pref_name = 'cron'";	
	$DB->query($sql,"cron.php");
	


	
	//check which cleanup job to run
	
	$month = date("m",time());
	$year = date("Y",time());

	
	
	if ($PREFERENCES["cron"] > 0 && $PREFERENCES["cron"] < 6 && $PREFERENCES["deletecontent"] != 0)
	{
	
		
	
		tidycontent($PREFERENCES["storage"],$PREFERENCES["docdir"],$PREFERENCES["deletecontent"],$month,$year,$DB);

	
	}
	elseif ($PREFERENCES["cron"] == 6 && $PREFERENCES["deleteevents"] != 0)
	{
		
		tidyevents($PREFERENCES,$month,$year,$DB);
		
	}
	elseif ($PREFERENCES["cron"] == 7 && $PREFERENCES["deletelogins"] != 0)
	{
		tidylogins($PREFERENCES["deletelogins"],$month,$year,$DB);
	}
	elseif ($PREFERENCES["cron"] == 8 && $PREFERENCES["deleteresource"] != 0)
	{
		tidyresourcebookings($PREFERENCES["deleteresource"],$month,$year,$DB);
	}
	elseif ($PREFERENCES["cron"] == 9 && $PREFERENCES["deleterequests"] != 0)
	{
		tidyrequests($PREFERENCES["deleterequests"],$month,$year,$DB);
	}
	elseif ($PREFERENCES["cron"] == 10 && $PREFERENCES["deletebookings"] != 0)
	{
		tidypcbookings($PREFERENCES["deletebookings"],$month,$year,$DB);
	}
	elseif ($PREFERENCES["cron"] == 11 && $PREFERENCES["deletestats"] != 0)
	{
	
		tidypageviews($PREFERENCES["deletestats"],$month,$year,$DB);
	}
        
		//tidyevents($PREFERENCES,$month,$year,$DB);


?>