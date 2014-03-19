<?php

/*
a = roster module
p = content module
c = calendar module
b = resource booking module
h = helpdesk module
i = inventpry module
l = links module
f = files/docbox module
e = email form module
s = casual staff listing module
w = wiki module
t = pc bookings module
z = menu parent module
n = noticeboard module	
x = custom module
y = direct link module
d = pd manager
g = email newsletter module
d = pd manager
j = clicker
k = leave module
v = travel claims module
q = TV display module


*/

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");

echo "<div id=\"mainbox\"><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>


";	

$ip = ip("pc");	

if (isset($_REQUEST["m"]))
{
if ((isinteger($_REQUEST["m"])))
	{
	$m = $_REQUEST["m"];	
	}
	else 
	{$ERROR  =  "$phrase[72]";	}	
	
}
if (isset($_REQUEST["reorderwidget"]))
	{
	$id = $DB->escape($_REQUEST["id"]);
	$sd = $DB->escape($_REQUEST["sd"]);
	$m = $DB->escape($_REQUEST["m"]);	
	$reorderwidget = $DB->escape($_REQUEST["reorderwidget"]);

	$counter = 1;
	
	$sql = "select id, position from widgets where sidebar = '$sd' and m = '$m' order by position";	
	
	$DB->query($sql,"menu.php");	
		while ($row = $DB->get())
		{
		$_id = $row["id"];
		$_stack[$_id] = $counter;
		$counter++;
		}
		
		//ksort($_stack);
		//print_r($_stack);
		
		
		if ($reorderwidget == "up")
		{
		//move widget up
		$keyofprevious = array_search($_stack[$id] -1, $_stack); 
		
		$_stack[$keyofprevious]= $_stack[$keyofprevious] +1;
		$_stack[$id] = $_stack[$id]-1;
			
		}
		
		
		if ($reorderwidget == "down")
		{
		//move widget down
		$keyofnext = array_search($_stack[$id] + 1, $_stack); 
		
		$_stack[$keyofnext] = $_stack[$keyofnext]  - 1;
		$_stack[$id] = $_stack[$id] + 1;
			
		}
		
		
		
		
		
		if (isset($_stack))
		{
		foreach ($_stack as $index => $value)
		{
		$index = $DB->escape($index);	
		$value = $DB->escape($value);
		
		$sql = "update widgets set position = \"$value\" WHERE id = \"$index\"";	
		$DB->query($sql,"menu.php");
		}
		}
		
		
		
		
	}
if (isset($_REQUEST["order"]))
{
$order = $_REQUEST["order"];
foreach ($order as $index => $value)
		{
		$value = $DB->escape($value);	
		$index = $DB->escape($index);
		
		
		$sql = "update modules set position = \"$value\" WHERE m = \"$index\"";	
		//echo "$sql <br> ";
		$DB->query($sql,"menu.php");	
		}	

}
if (isset($_REQUEST["reorder"]))
	{
	//reorders pages
	$reorder = explode(",",$_REQUEST["reorder"]);
	
	foreach ($reorder as $index => $value)
		{
		$index = $DB->escape($index);	
		$value = $DB->escape($value);
		
		$sql = "update modules set position = \"$index\" WHERE m = \"$value\"";	
		$DB->query($sql,"menu.php");
		}
			
	}

	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "updatewidget")
	{
	


	$target = $DB->escape($_REQUEST["target"]);
	$id = $DB->escape($_REQUEST["id"]);
	$widget_name = $DB->escape($_REQUEST["widget_name"]);
	
	$sql = "update widgets set target = '$target', widget_name = '$widget_name' where id = '$id'";

	$DB->query($sql,"menu.php");
	
	}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addwidget")
	{
	
	
	$m = $DB->escape($_REQUEST["m"]);
	$type = $DB->escape($_REQUEST["type"]);
	
	if ($type == "html") {$target = $DB->escape($_REQUEST["htmltarget"]);}
	if ($type == "php") {$target = $DB->escape($_REQUEST["phptarget"]);}
	if ($type == "module") {$target = $DB->escape($_REQUEST["moduletarget"]);}
	
	
	$sd = $DB->escape($_REQUEST["sd"]);
	$widget_name = $DB->escape($_REQUEST["widget_name"]);
	
	$sql = "insert into widgets values(NULL,'$m','$target','$sd','$type','0','$widget_name')";
	
	$DB->query($sql,"menu.php");
	
	
	}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletewidget")
	{
	
	
	$id = $DB->escape($_REQUEST["id"]);

	
	$sql = "delete from widgets where id = '$id'";

	$DB->query($sql,"menu.php");
	
	
	}
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "add")
	{
	
	
	$modname = $DB->escape($_REQUEST["modname"]);
	$type = $DB->escape($_REQUEST["type"]);
	$input = $DB->escape($_REQUEST["input"]);
	$menutype = $DB->escape($_REQUEST["menutype"]);
	$frontpage = $DB->escape($_REQUEST["frontpage"]);
	$mainmenu = $DB->escape($_REQUEST["mainmenu"]);
	$menupath = $DB->escape($_REQUEST["menupath"]);
	$adminpath = $DB->escape($_REQUEST["adminpath"]);
        $hidemenu = $DB->escape($_REQUEST["hidemenu"]);
        $stylesheet = $DB->escape($_REQUEST["stylesheet"]);
	
	if (isset($_REQUEST["parent"])) {	$parent = $DB->escape($_REQUEST["parent"]); } else {$parent = 0;}
	
	if ($type == "p")
	{
	$menupath = "page.php";
	$adminpath = "editpage.php";	
	}
	elseif ($type == "c")
	{
	$menupath = "calendar.php";
	$adminpath = "editcalendar.php";	
	}
	elseif ($type == "b")
	{
	$menupath = "resourcebooking.php";
	$adminpath = "resource.php";	
	
	}

        elseif ($type == "a")
	{
	$menupath = "roster.php";
	$adminpath = "rosteradmin.php";

	}

	elseif ($type == "h")
	{
	$menupath = "helpdesk.php";
	$adminpath = "editform.php";
	
	}
	elseif ($type == "i")
	{
	$menupath = "inventory.php";
	$adminpath = "";
	
	}
	
	elseif ($type == "l")
	{
	$menupath = "links.php";
	$adminpath = "editlinks.php";
	
	}
	elseif ($type == "f")
	{
	$menupath = "files.php";
	$adminpath = "";	
	}
	elseif ($type == "e")
	{
	$menupath = "emailform.php";
	$adminpath = "editform.php";	
	}
	elseif ($type == "s")
	{
	$menupath = "staff.php";
	$adminpath = "editstaff.php";	
	}
	
		elseif ($type == "w")
	{
	$menupath = "wiki.php";
	$adminpath = "";	
	}
	
		elseif ($type == "n")
	{
	$menupath = "noticeboard.php";
	$adminpath = "";	
	}
	
		elseif ($type == "k")
	{
	$menupath = "leave.php";
	$adminpath = "leaveadmin.php";	
	}
		elseif ($type == "v")
	{
	$menupath = "travel.php";
	$adminpath = "traveladmin.php";	
	}
	
	
	elseif ($type == "t")
	{
	$menupath = "pc.php";
	$adminpath = "pcadmin.php";	
	}
	elseif ($type == "j")
	{
	$menupath = "clicker.php";
	$adminpath = "clickeradmin.php";	
	}
	elseif ($type == "z")
	{
	$menupath = "menu.php";
	$adminpath = "menu.php";	
	}

	elseif ($type == "g")
	{
	$menupath = "newsletter.php";
	$adminpath = "newsletteradmin.php";	
	}
	
	elseif ($type == "q")
	{
	$menupath = "tv.php";
	$adminpath = "tvedit.php";	
	}
	
	$sql = "insert into modules values (NULL,'$modname','$type','t','$menupath','$adminpath','0','$input','$menutype','$parent','$frontpage','$mainmenu','$stylesheet','$hidemenu')";
	$DB->query($sql,"menu.php");
	
	$comment =  "Module added. $modname";
	
	$mod = $DB->last_insert();
	
	
	if ($type == "c" )
	{
	$sql = "insert into cal_branch_bridge values ($mod,'0')";
	$DB->query($sql,"menu.php");	
	}
	
	if ($type == "e" )
	{
	$sql = "insert into forms values ($mod,'','','0','0','0')";
	$DB->query($sql,"menu.php");	
	}
	
	if ($type == "h" )
	{
	$sql = "insert into forms values ($mod,'','','0','0','0')";
	$DB->query($sql,"menu.php");	
	}
	
	if ($type == "w" || $type == "n")
	{
	$sql = "insert into page values (NULL,'Home','$mod','1','0','1','c',NULL,'0','0','0')";

	$DB->query($sql,"menu.php");	
	}

	}

	
	
	if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "edit")
	{
	
	//print_r($_REQUEST);	
		
	$input = $DB->escape($_REQUEST["input"]);
	$name = $DB->escape($_REQUEST["name"]);	
	$m = $DB->escape($_REQUEST["m"]);
	$menutype = $DB->escape($_REQUEST["menutype"]);	
	$parent = $DB->escape($_REQUEST["parent"]);	
	$frontpage = $DB->escape($_REQUEST["frontpage"]);	
	$mainmenu = $DB->escape($_REQUEST["mainmenu"]);
	$_menupath = $DB->escape($_REQUEST["menupath"]);
	$adminpath = $DB->escape($_REQUEST["adminpath"]);
           $hidemenu = $DB->escape($_REQUEST["hidemenu"]);
        $stylesheet = $DB->escape($_REQUEST["stylesheet"]);

	if ($frontpage == 1)
	{
	$sql = "update modules set frontpage = '0'";
	$DB->query($sql,"menu.php");	
	}

	$sql = "update modules set name = '$name', input = '$input', menu = '$menutype', parent= '$parent', mainmenu= '$mainmenu', menupath= '$_menupath', adminpath= '$adminpath', 
        frontpage= '$frontpage', hidemenu = '$hidemenu', stylesheet = '$stylesheet' where m = '$m'";
//echo $sql;
	$DB->query($sql,"menu.php");
	
	//check if inpout type has changed 
	//html input requires line breaks to be changed to <br>
	//no html input requires <br> to be changed to line breaks
	$sql = "select * from modules where m = '$m'";
	$DB->query($sql,"menu.php");
	$row = $DB->get();
	$oldinput = $row["input"];	
	$_type = $row["type"];
	
	if (($_type == "p" || $_type == "w")  && $input == 1 && $oldinput == 0)
	{
		//change line breaks to <br>
		$sql = "select content.content_id, content.body from content, page where content.page_id = page.page_id and page.m = '$m'";
	$DB->query($sql,"menu.php");
	while ($row = $DB->get())
		{
		$body = nl2br($row["body"]);
		$content_id = $row["content_id"];
		$sql2 = "update content set body = '$body' where content_id = '$content_id'";
		$DB->query($sql2,"menu.php");
		}
		
	}
	
	if (($_type == "p" || $_type == "w")  && $input == 0 && $oldinput == 1)
	{
		//change <br> to line breaks to
		//change line breaks to <br>
		$sql = "select content.content_id, content.body from content, page where content.page_id = page.page_id and page.m = '$m'";
	$DB->query($sql,"menu.php");
	while ($row = $DB->get())
		{
		$body = str_replace('<br>', '
		', $row["body"]);
		$body = str_replace('<br />', '
		', $row["body"]);
		$content_id = $row["content_id"];
		$sql2 = "update content set body = '$body' where content_id = '$content_id'";
		$DB->query($sql2,"menu.php");
		}
	}
	
	

	if (isset($_REQUEST["toptext"]))
	{
	$sql = "delete from widgets where m = '$m' and (sidebar = 't' or sidebar = 'b')";
	$DB->query($sql,"menu.php");
	
	
	$toptext = $DB->escape($_REQUEST["toptext"]);
	$bottomtext = $DB->escape($_REQUEST["bottomtext"]);
	
	
	$sql = "insert into widgets values(NULL,'$m','$toptext','t','html','0','')";
	//echo $sql;
	$DB->query($sql,"menu.php");
	
	$sql = "insert into widgets values(NULL,'$m','$bottomtext','b','html','0','')";
	
	$DB->query($sql,"menu.php");
	}
	
	
	
	}
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete2")
	{
	
		$type = $_REQUEST["type"];
	
	if (!isset($ERROR)	)
			{
			
			
				
				
					
			if (isset($_REQUEST["type"]) && ($_REQUEST["type"] == "j"))
			
			{
			
				
			$sql = "delete from clicker_options where m = '$m'";
			$DB->query($sql,"menu.php");
			$sql = "delete from clicker_location where m = '$m'";
			$DB->query($sql,"menu.php");	
				
			
			
			
				if ($DB->type == "mysql")
		{
			
			
		$sql = "delete clicker_clicks, clicker_category from clicker_clicks inner join clicker_category where  clicker_category.cat_id = clicker_clicks.category and clicker_category.m = '$m'";
		}
		else
		{
		$sql = "delete from clicker_clicks where category in  (select cat_id from clicker_category where m = '$m')";
		}
			$DB->query($sql,"menu.php");
			
			
		$sql = "delete from clicker_category where m = '$m'";
			$DB->query($sql,"menu.php");	
			
					
				$sql = "delete from modules where m = '$m'";
			$DB->query($sql,"menu.php");
						
				
				$DB->tidy("modules");
				$DB->tidy("clicker_clicks");
				$DB->tidy("clicker_category");
				$DB->tidy("clicker_location");
				$DB->tidy("clicker_options");
			}	
				
				
				
				
				
				
				
			if (isset($_REQUEST["type"]) && ($_REQUEST["type"] == "y" || $_REQUEST["type"] == "x"))
			
			{
				
				$sql = "delete from modules where m = '$m'";
			$DB->query($sql,"menu.php");
						
				
				$DB->tidy("modules");
			}
				
				
			if (isset($_REQUEST["type"]) && ($_REQUEST["type"] == "p" ||  $_REQUEST["type"] == "l"))	
			{
			
			$sql = "select count(*) as num from page where m = '$m' and deleted = '0'";	
			$DB->query($sql,"menu.php");
		   $row = $DB->get();
			$num = $row["num"];			 
		
			if ($num <> 0)
			
			
			{ $comment = "$phrase[593]";}
			else {
				
				
				$sql = "delete from modules where m = '$m'";
			$DB->query($sql,"menu.php");
			
			//delete module directory
			$uploaddir = $PREFERENCES["docdir"] ."/".$m; 
			delDir($uploaddir);	
				
				
				$DB->tidy("modules");
			}
			
			}
		
			
			
			
				if (isset($_REQUEST["type"]) && ($_REQUEST["type"] == "z")	)
			{
			
		
				$sql = "update modules set parent = '0' where parent = '$m'";
			$DB->query($sql,"menu.php");
				
				$sql = "delete from modules where m = '$m'";
			$DB->query($sql,"menu.php");
			
		
				
				$DB->tidy("modules");
			
			
			}
			
			
			
			
			
					if (isset($_REQUEST["type"]) && ($_REQUEST["type"] == "g")	)
			{
			
		
			
				
				
			$sql = "delete newsletter_content from newsletter_content, newsletter_issues  where issue_m = \"$m\" and content_issue = issue_id"; 
		 	$DB->query($sql,"menu.php");
			
		
				$sql = "delete from newsletter_issues where issue_m = '$m'";
			$DB->query($sql,"menu.php");
			
		
			
				$sql = "delete from modules where m = '$m'";
			$DB->query($sql,"menu.php");
			
		
				
				$DB->tidy("modules");
			
			
			}
			
			
					if (isset($_REQUEST["type"]) && ($_REQUEST["type"] == "k")	)
			{
			//leave module
                                            
        $sql = "select count(*) as leavetotal from leave_requests where m = '$m'";
        $DB->query($sql,"users.php");
        $row = $DB->get();
        $leavetotal = $row["leavetotal"];
	//echo $sql;
        
          $sql = "select count(*) as traveltotal from travel_requests where m = '$m'";
        $DB->query($sql,"users.php");
        $row = $DB->get();
        $traveltotal = $row["traveltotal"];
        
       
        
        if ($traveltotal == 0 && $leavetotal == 0)
        {

				$sql = "delete from modules where m = '$m'";
			$DB->query($sql,"menu.php");
		
				$DB->tidy("modules");
        }
			
			}
			
			
			
			
			
			
			
				
		if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "i")	
			{	
			
				
				$sql = "select count(*) as num from hardware where m = '$m'";	
			$DB->query($sql,"menu.php");
		   $row = $DB->get();
			$num = $row["num"];			 
		
			if ($num <> 0)
			{ $comment = "$phrase[594] ";}
			else {
				
				
				  $sql = "delete from hardware where m = \"$m\" "; 
		 		$DB->query($sql,"menu.php");
		 		
			$sql = "delete from modules where m = \"$m\" "; 
		 	$DB->query($sql,"menu.php");
		 	
		 	
		 	
		 		if ($DB->type == "mysql")
					{
					$DB->tidy("hardware");	
					$DB->tidy("modules");	
					}
		
				else
					{
					$DB->tidy("database");	
					}	
		 	
		 	
			}
			
			
			}
		
			
		if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "s")	
			{	
			
				
				$sql = "select count(*) as num from staff where m = '$m'";	
			$DB->query($sql,"menu.php");
		   $row = $DB->get();
			$num = $row["num"];			 
		
			if ($num <> 0)
			{ $comment = "$phrase[594] ";}
		else {
				
			$sql = "delete stafftemplate from stafftemplate, staff  where m = \"$m\" and stafftemplate.staff = staff.staffnum"; 
		 	$DB->query($sql,"menu.php");
		 		
		 	
			
			$sql = "delete staffday from staffday, staff where staff.m = \"$m\" and staff.staffnum = staffday.staffid"; 
		 	$DB->query($sql,"menu.php");
		 		
		 
			
			
			$sql = "delete from staff where m = \"$m\" "; 
		 	$DB->query($sql,"menu.php");
		 		
		 	
			
			
			$sql = "delete from modules where m = \"$m\" "; 
		 	$DB->query($sql,"menu.php");
		 	
		 	
		 	
		 		if ($DB->type == "mysql")
					{
					$DB->tidy("stafftemplate");
					$DB->tidy("staffday");
					$DB->tidy("staff");
					}
		
				else
					{
					$DB->tidy("database");	
					}
			}
			
			
			}	
			
			
			
		
		if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "b")	
			{	
				$sql = "select count(*) as num from resource where m = '$m'";	
			$DB->query($sql,"menu.php");
		   $row = $DB->get();
			$num = $row["num"];			 
		
			if ($num <> 0)
			{ $comment = "$phrase[594] ";}
		else {
			
				
		 	
		 	$sql = "delete from modules where m = '$m'";
			$DB->query($sql,"menu.php");	
			
			$DB->tidy("modules");
			
			
		}
			}
				
			
		if (isset($_REQUEST["type"]) && ($_REQUEST["type"] == "n" || $_REQUEST["type"] == "w")	)
			{
			//delete categories (stored in pages table)
			
			
			$sql = "select page_id from page where m = '$m'";	
			$DB->query($sql,"menu.php");
		   while ($row = $DB->get())
		   
		   {
			$page = $row["page_id"];
			
			
			deletepage($page,$ip,$DB);
		   }
		 		
			$sql = "delete from modules where m = '$m'";
			$DB->query($sql,"menu.php");
			}
		
		
			
			
		if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "f")	
			{
			//delete categories (stored in pages table)
			  $sql = "delete from page where m = \"$m\" "; 
		 	$DB->query($sql,"menu.php");	
		 	
		 	//delete categories (stored in pages table)
			  $sql = "delete from documents where m = \"$m\" "; 
		 	$DB->query($sql,"menu.php");
		 	
		 		
			$sql = "delete from modules where m = '$m'";
			$DB->query($sql,"menu.php");
			
			
			//delete module directory
			$uploaddir = $PREFERENCES["docdir"] ."/".$m; 
			delDir($uploaddir);	
				
			
			
		 		if ($DB->type == "mysql")
					{
					$DB->tidy("modules");
					$DB->tidy("page");
					$DB->tidy("documents");
					}
		
				else
					{
					$DB->tidy("database");	
					}
		 	
			}
			
			
		if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "c")	
			{
					
			
			//delete cal_bridge
			$sql = "delete from cal_bridge where m = \"$m\"";
			$DB->query($sql,"menu.php");
		
			
			
			
			$sql = "delete from modules where m = \"$m\"";
			$DB->query($sql,"menu.php");
			
			
				if ($DB->type == "mysql")
					{
					$DB->tidy("cal_bridge");
					$DB->tidy("modules");
					}
		
				else
					{
					$DB->tidy("database");	
					}
			}
		
			if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "t")	
			{
			
					$sql = "select count(*) as num from pc_branches where m = '$m'";	
			$DB->query($sql,"menu.php");
		   $row = $DB->get();
			$num = $row["num"];			 
		
			
			if ($num <> 0)
			{ $comment = "$phrase[645] ";}
			else {		
			
			//delete cal_bridge<br>

			$sql = "delete cal_bridge from pc_bridge, pc_usage  where pc_bridge.useno = pc_usage.useno and  pc_usage.m = \"$m\"";
			$DB->query($sql,"menu.php");
			
		
			
			$sql = "delete from pc_usage  where m = \"$m\"";
			$DB->query($sql,"menu.php");
			
			
		
			$sql = "delete from pc_blacklist  where m = \"$m\"";
			$DB->query($sql,"menu.php");
			
			
			
			$sql = "delete from modules where m = \"$m\"";
			$DB->query($sql,"menu.php");
			
	
			if ($DB->type == "mysql")
					{
					$DB->tidy("pc_usage");
					$DB->tidy("pc_bridge");
					$DB->tidy("modules");
					$DB->tidy("pc_blacklist");
					}
		
			else
					{
					$DB->tidy("database");	
					}
			}
			}
	
	
	if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "e")	
			{		
			
			
			
			
				 //delete formfields
		    $sql = "select fieldset from fieldset where form = '$m'"; 
		  $DB->query($sql,"menu.php");
		  while (  $row = $DB->get())
		  		{
		  		$fieldset = $row["fieldset"];
		  	 	$sql2 = "delete  from formfields where fieldset = '$fieldset'"; 
		  		$DB->query($sql2,"menu.php");
		  	
		  		}
		  
		  
		  //delete fieldsets
		  $sql = "delete from fieldset where form = '$m'"; 
		  $DB->query($sql,"menu.php");
		  
		  //delete form
		  $sql = "delete from forms where module = '$m'"; 
		  $DB->query($sql,"menu.php");
		  
		    //delete form
		  $sql = "delete from page where m = '$m'"; 
		  $DB->query($sql,"menu.php");

		  //delete module
		  $sql = "delete from modules where m = '$m'"; 
		  $DB->query($sql,"menu.php");
		  
		  
		
				if ($DB->type == "mysql")
					{
					 $DB->tidy("modules");
					 $DB->tidy("page");
					$DB->tidy("forms");
					$DB->tidy("fieldset");
					$DB->tidy("helpdesk");
					$DB->tidy("formfields");
					}
		
				else
					{
					$DB->tidy("database");	
					}
			}
	
			
			
	if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "h")	
			{		
				
				 //delete formfields
		  $sql = "delete formfields from formfields, fieldset
					where formfields.fieldset = fieldset.fieldset and fieldset.form = '$m'"; 
		  $DB->query($sql,"menu.php");
		  
		  //delete fieldsets
		  $sql = "delete from fieldset where form = '$m'"; 
		  $DB->query($sql,"menu.php");
		  
		  //delete form
		  $sql = "delete from forms where module = '$m'"; 
		  $DB->query($sql,"menu.php");
		  
		  //delete helpdesk
		  $sql = "delete from helpdesk where m = '$m'"; 
		  $DB->query($sql,"menu.php");
		  
		    //delete helpdesk
		  $sql = "delete from helpdesk_options where m = '$m'"; 
		  $DB->query($sql,"menu.php");


		  //delete module
		  $sql = "delete from modules where m = '$m'"; 
		  $DB->query($sql,"menu.php");
		  
		  
	;
		
		
		
		if ($DB->type == "mysql")
					{
				$DB->tidy("modules");
				$DB->tidy("forms");
				$DB->tidy("fieldset");
				$DB->tidy("helpdesk");
				$DB->tidy("formfields");
					}
		
				else
					{
					$DB->tidy("database");	
					}
			
			}
	}

	}

	
	

			
			
			

	
	
		
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete" )
	{
		$type = $_REQUEST["type"];
		$name = $_REQUEST["name"];
	echo "<br><b>$name</b><br><br>$phrase[14]<br><br>
	<a href=\"menu.php?type=$type&amp;m=$m&amp;update=delete2\">$phrase[12]</a> | <a href=\"menu.php\">$phrase[13]</a>";
	
	}
	
	elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "add")
{
	

	echo "<br><a href=\"menu.php\">$phrase[610]</a><br><br>
	
	
	<FORM method=\"POST\" action=\"menu.php\" style=\"width:80%;margin:0 auto\"><fieldset><legend>$phrase[596]</legend>
	<br><table style=\"margin:0 auto;text-align:left\"><tr><td class=\"label\">
	$phrase[141]</td><td><input type=\"text\" name=\"modname\" size=\"50\" maxlength=\"50\"></td></tr>
	

<tr><td class=\"label\">
	<b>$phrase[838]</b></td><td><input type=\"hidden\" name=\"update\"  value=\"add\">
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"c\">$phrase[598]<br>

	<input type=\"radio\" name=\"type\" class=\"type\" value=\"p\" checked >$phrase[599] <br>
	
	
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"x\"> $phrase[928] <br>
	
	
	
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"y\">$phrase[933] <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"e\">$phrase[600]  <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"j\">$phrase[954] <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"f\">$phrase[601] <br>
	
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"i\">$phrase[602]  <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"k\">$phrase[993] <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"l\">$phrase[840]  <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"n\">$phrase[97]   <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"g\">$phrase[944] <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"t\">$phrase[603] <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"h\">$phrase[604] <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"b\">$phrase[605] <br>
        <input type=\"radio\" name=\"type\" class=\"type\" value=\"a\">$phrase[1045] <br>
	
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"z\">$phrase[875] <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"w\">$phrase[826] <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"v\">$phrase[1073] <br>
	<input type=\"radio\" name=\"type\" class=\"type\" value=\"q\">$phrase[1091] <br>
	
	

	</td></tr>
	
	
	
	<tr id=\"inputtyperow\"><td class=\"label\">$phrase[942]</td><td>
<span id=\"inputtype\">
	<select name=\"input\"><option value=\"0\">$phrase[337]</option><option value=\"1\">$phrase[839]</option><option value=\"2\">html</option></select>
</span>
	</td></tr>
	<tr id=\"menupathrow\" style=\"display:none\"><td class=\"label\" id=\"pathname\">$phrase[931]</td><td>
	<input type=\"text\" name=\"menupath\" size=\"50\">
	</td></tr>
	<tr id=\"adminpathrow\" style=\"display:none\"><td class=\"label\">$phrase[932]</td><td>
<input  type=\"text\" name=\"adminpath\" size=\"50\">
	</td></tr>
	
	<tr id=\"menutyperow\"><td class=\"label\">$phrase[852]</td><td>
<span id=\"menutype\">
	<select name=\"menutype\"><option value=\"vertical\">$phrase[853]</option><option value=\"horizontal\">$phrase[854]</option></select>
</span>
	</td></tr>
	
	
	<tr id=\"frontpagerow\"><td class=\"label\">$phrase[924]</td><td>

	<select name=\"frontpage\"><option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option></select>

	</td></tr>
	
	<tr id=\"mainmenurow\"><td class=\"label\">$phrase[930]</td><td>

	<select name=\"mainmenu\"><option value=\"1\">$phrase[12]</option><option value=\"0\">$phrase[13]</option></select>

	</td></tr>
	
	<tr id=\"stylesheet\"><td class=\"label\">$phrase[538]</td><td>

	<input type=\"text\" name=\"stylesheet\">

	</td></tr>
            
         <tr id=\"hidemenu\"><td class=\"label\">$phrase[1085]</td><td>

	<select name=\"hidemenu\"><option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option></select>

	</td></tr>
            
	<tr id=\"parentrow\"><td class=\"label\"><b>$phrase[875]</b></td><td style=\"text-align:left\"><select name=\"parent\">";
	 echo "<option value=\"0\">$phrase[495]</option>";
	
	 $sql = "select * from modules where type = 'z'";
	$DB->query($sql,"menu.php");

while ($row = $DB->get())
	     {
	     
	     	$mod = $row["m"];
            $name = $row["name"];
            echo "<option value=\"$mod\">$name</option>";
            
	     }
	
	echo "
	
	</select></td><tr>
	
	
	<tr><td></td><td>

	<input type=\"submit\" name=\"addmod\" value =\"$phrase[596]\"></td></tr></table>
	</fieldset></form>
	<script type=\"text/javascript\">
	
	function test(e)
	{
		var targ;
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	if (targ.nodeType == 3) // defeat Safari bug
		targ = targ.parentNode;
		//alert(targ.value);
		
		
		if (targ.value == 'p')
		{
		show_inputtype();
		show_menutype();
		show_mainmenu();
		show_frontpage();
		hide_menupath();
		hide_adminpath();
		show_parent();
	
		}
				else if (targ.value == 'g')
		{
		show_inputtype();
		hide_menutype();
		show_mainmenu();
		show_frontpage();
		hide_menupath();
		hide_adminpath();
		show_parent();
		}
		else if (targ.value == 'z')
		{
		hide_frontpage();
		hide_mainmenu();	
		hide_inputtype();
		hide_menutype();
		hide_menupath();
		hide_adminpath();	
		hide_parent();
		}
		else if (targ.value == 'y')
		{
		hide_frontpage();
		hide_mainmenu();	
		hide_inputtype();
		hide_menutype();	
		show_menupath();
		hide_adminpath();
		show_parent();
		document.getElementById('pathname').innerHTML = '$phrase[933]';
		}
		else if (targ.value == 'w' || targ.value == 'n' )
		{
		show_inputtype();	
		show_mainmenu();
		show_frontpage();
		hide_menutype();
		hide_menupath();
		hide_adminpath();
		show_parent();
		}
		else if (targ.value == 'x' )
		{
		hide_inputtype();
		hide_menutype();
		show_mainmenu();
		show_frontpage();
		show_menupath();
		show_adminpath();
		show_parent();	
		document.getElementById('pathname').innerHTML = '$phrase[931]';
		}
		else
		{
		hide_inputtype();
		hide_menutype();
		show_mainmenu();
		show_frontpage();
		hide_menupath();
		hide_adminpath();
		show_parent();
		}
	}
	
	var inputs = document.getElementsByTagName('input');
	
	
	for (var i = 0; i < inputs.length; i++) 
	{
	if (inputs[i].className=='type')
	{
		
	inputs[i].onclick=test;
	
	
	}
	
	}
	
	
		function hide_menupath()
	{
	if (document.getElementById)	
	{
	document.getElementById('menupathrow').style.display = 'none';
	}
	}
	
		
	
		function show_menupath()
	{
	if (document.getElementById)	
	{
	document.getElementById('menupathrow').style.display = '';
	}
	}
	
	
	
		function hide_adminpath()
	{
	if (document.getElementById)	
	{
	document.getElementById('adminpathrow').style.display = 'none';
	}
	}
	

	
		
		function show_parent()
	{
	if (document.getElementById)	
	{
	document.getElementById('parentrow').style.display = '';
	}
	}
	
	
	
		function hide_parent()
	{
	if (document.getElementById)	
	{
	document.getElementById('parentrow').style.display = 'none';
	}
	}
	
	
	
	
	
		function show_adminpath()
	{
	if (document.getElementById)	
	{
	document.getElementById('adminpathrow').style.display = ''
	}
	}
	
	function hide_inputtype()
	{
	if (document.getElementById)	
	{
	document.getElementById('inputtyperow').style.display = 'none';
	document.getElementById('inputtype').innerHTML = '<input type=\"hidden\" name=\"input\" value=\"0\">';	
	}
  	}
	
  	
  	function hide_menutype()
	{
	if (document.getElementById)	
	{
	document.getElementById('menutyperow').style.display = 'none';
	document.getElementById('menutype').innerHTML = '<input type=\"hidden\" name=\"menutype\" value=\"vertical\">';	
	}
  	}
  	
  		function hide_frontpage()
	{
	if (document.getElementById)	
	{
	document.getElementById('frontpagerow').style.display = 'none';
	}
	}
	  	
	  	
	  	
	 	function show_frontpage()
	{
	if (document.getElementById)	
	{
	document.getElementById('frontpagerow').style.display = '';
	}
	}
	  	
	 	function hide_mainmenu()
	{
	if (document.getElementById)	
	{
	document.getElementById('mainmenurow').style.display = 'none';
	}
	  	}
	  	
	  	
	  	
	 	function show_mainmenu()
	{
	if (document.getElementById)	
	{
	document.getElementById('mainmenurow').style.display = '';
	}
	}
	  	 	
  	
	function show_inputtype()
	{
		if (document.getElementById)	
	{
	document.getElementById('inputtyperow').style.display = '';
	document.getElementById('inputtype').innerHTML = '<select name=\"input\"><option value=\"0\">$phrase[337]</option><option value=\"1\">$phrase[839]</option><option value=\"2\">html</option></select>';	
	}
	}
	
	
	
	function show_menutype()
	{
		if (document.getElementById)	
	{
	
	document.getElementById('menutyperow').style.display = ''
	document.getElementById('menutype').innerHTML = '<select name=\"menutype\"><option value=\vertical\">$phrase[853]</option><option value=\"horizontal\">$phrase[854]</option></select>';	
	}
	}
	
	
	
	
	
	</script>";
	
	
	
	
}


elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "addwidget")
{
	
	$m = $DB->escape($_REQUEST["m"]);
	$sd = $DB->escape($_REQUEST["sd"]);
	
	$sql = "select name from modules where m = '$m' ";
	$DB->query($sql,"menu.php");
	$row = $DB->get();
	
	$mod_name = htmlspecialchars($row["name"]);
	echo "<h2>$mod_name</h2>";
	
	echo "<h4>Add widget to left sidebar</h4>
	<form method=\"post\" action=\"menu.php\">
	<table style=\"width:60%;margin:0 auto\">
	<tr><td style=\"width:50%\" class=\"label\">Widget name</td><td> <input type=\"text\" name=\"widget_name\"> </td></tr>
	<tr><td class=\"label\">Type </td><td> 
	
	
	
	
	<input type=\"radio\" name=\"type\" value=\"module\" id=\"module\"> <label for=\"module\">module</label> 
	<br><select name=\"moduletarget\">";
	
	$sql = "select name, m from modules where type = 'b' or type = 'c' or type = 'n'  ";
	$DB->query($sql,"menu.php");
	while ($row = $DB->get())
	{
	$mod_name = htmlspecialchars($row["name"]);	
	$mod = $row["m"];	
	
	echo "<option value=\"$mod\">$mod_name</option>";
	}
	
	echo "</select><br><br>
	 <input type=\"radio\" name=\"type\" value=\"php\" id=\"php\"> <label for=\"php\">php</label><br>
<input type=\"text\" name=\"phptarget\"><br><br>
	 <input type=\"radio\" name=\"type\" value=\"html\" id=\"html\" checked> <label for=\"html\">html</label><br>
<textarea name=\"htmltarget\" cols=\"60\" rows=\"10\"></textarea>
<input type=\"hidden\" name=\"update\" value=\"addwidget\">
<input type=\"hidden\" name=\"sd\" value=\"$sd\">
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"event\" value=\"editsidebar\"><br><br>
<input type=\"submit\" value=\"$phrase[176]\">
	
	
	
	
	</tr></table>
	</form>";
	
	
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editwidget")
{
	
	echo "<a href=\"menu.php\">$phrase[189]</a>";
	
	$m = $DB->escape($_REQUEST["m"]);
	$sd = $DB->escape($_REQUEST["sd"]);
	$id = $DB->escape($_REQUEST["id"]);
	
	
	$sql = "select name from modules where m = '$m' ";
	$DB->query($sql,"menu.php");
	$row = $DB->get();
	$mod_name = htmlspecialchars($row["name"]);
	
	$sql = "select * from widgets where id = '$id'";
	$DB->query($sql,"menu.php");
	$row = $DB->get();


	$widget_name = $row["widget_name"];
	$type = $row["type"];
	$target = $row["target"];
	
	$target=htmlspecialchars($target,ENT_NOQUOTES); 
	
	$type = $row["type"];
	
	
	
	echo "<h2>$mod_name</h2>";
	
	if ($sd == "l"	) {echo "<h4>Update left sidebar widget</h4>";}
	if ($sd == "r"	) {echo "<h4>Update right sidebar widget</h4>";}

	echo "<form method=\"post\" action=\"menu.php\">
	<table style=\"width:60%;margin:0 auto\">
	<tr><td style=\"width:50%\" class=\"label\">Widget name</td><td> <input type=\"text\" name=\"widget_name\" value=\"$widget_name\"> </td></tr>
	<tr><td class=\"label\">Type: $type </td><td> ";
	
	if ($type == "module")
		{ 
			echo "<select name=\"target\">";
	
	$sql = "select name, m from modules where type = 'b' or type = 'c' or type = 'n' ";
	$DB->query($sql,"menu.php");
	while ($row = $DB->get())
	{
	$mod_name = htmlspecialchars($row["name"]);	
	$mod = $row["m"];	
	
	echo "<option value=\"$mod\"";
	if ($mod  == $target) {echo " selected";}
	echo ">$mod_name</option>";
	}
	
	echo "</select>";
		}
	
	if ($type == "php")
		{
		echo "<input type=\"text\" name=\"target\" value=\"$target\">";	
			
		}	
		
	
	if ($type == "html")
		{
		echo "<textarea name=\"target\" cols=\"80\" rows=\"30\">$target</textarea>";	
			
		}
		

echo "
<input type=\"hidden\" name=\"update\" value=\"updatewidget\">
<input type=\"hidden\" name=\"sd\" value=\"$sd\">
<input type=\"hidden\" name=\"id\" value=\"$id\">
<input type=\"hidden\" name=\"m\" value=\"$m\">
<input type=\"hidden\" name=\"event\" value=\"editsidebar\"><br><br>
<input type=\"submit\" value=\"$phrase[28]\">
	
	
	
	
	</tr></table>
	</form>";
	
	
}

	
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editsidebar")
{
	
	$m = $DB->escape($_REQUEST["m"]);
	$sd = $DB->escape($_REQUEST["sd"]);
	
	$sql = "select name from modules where m = '$m' ";
	$DB->query($sql,"menu.php");
	$row = $DB->get();
	
	$mod_name = htmlspecialchars($row["name"]);
echo "<a href=\"menu.php\">$phrase[189]</a><h2>$mod_name</h2>";
	
	
	if ($sd == "l") {echo "<h4>$phrase[939]</h4>";}
	if ($sd == "r") {echo "<h4>$phrase[940]</h4>";}

	echo "
	<a href=\"menu.php?event=addwidget&amp;m=$m&amp;sd=$sd\"><img src=\"../images/add.png\" title=\"$phrase[176]\"></a>
	
	<table style=\"margin:2em auto;padding:1em;text-align:left\" class=\"colourtable\">";
	
	$sql = "select * from widgets where m = '$m' and sidebar = '$sd' order by position";

	$DB->query($sql,"menu.php");
	$num = $DB->countrows();
	$counter = 1;
	while ($row = $DB->get())
	{
	$id = $row["id"];
	$widget_name = $row["widget_name"];
	$type = $row["type"];
	$target = $row["target"];
	$target = $row["target"];
	$position = $row["position"];
	$type = $row["type"];
	
	echo "<tr><td>$widget_name</td><td>$type</td><td>";
	if ($counter > 1) {	echo "<a href=\"menu.php?reorderwidget=up&amp;event=editsidebar&amp;sd=$sd&amp;id=$id&amp;m=$m\"><img src=\"../images/up.png\" title=\"$phrase[77]\"></a>";}
	echo "</td><td>";
	if ($counter < $num)
	{echo "<a href=\"menu.php?reorderwidget=down&amp;event=editsidebar&amp;sd=$sd&amp;id=$id&amp;m=$m\"><img src=\"../images/down.png\" title=\"$phrase[78]\"></a>";}
	
	echo "</td>
	<td><a href=\"menu.php?event=editwidget&amp;m=$m&amp;sd=$sd&amp;id=$id\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"></a></td>
	<td>	 <a href=\"menu.php?update=deletewidget&amp;m=$m&amp;sd=$sd&amp;event=editsidebar&amp;id=$id\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"></a></td></tr>";
	
	
	$counter++;
	
	}
	echo "</table>";
	
	
}
elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit")
{
	
	$m = $DB->escape($_REQUEST["m"]);
	
	$sql = "select * from modules where m = '$m'";
	
$DB->query($sql,"menu.php");
$row = $DB->get();
$name = $row["name"];
$type = $row["type"];
$input = $row["input"];
$menupath = $row["menupath"];
$adminpath = $row["adminpath"];
$frontpage = $row["frontpage"];
$menu = $row["menu"];
$mainmenu = $row["mainmenu"];
$parent = $row["parent"];
$stylesheet = $row["stylesheet"];
$hidemenu = $row["hidemenu"];
	
	echo "<a href=\"menu.php\">$phrase[610]</a><h2>$name</h2>
	
	
	<form method=\"post\" action=\"menu.php\" style=\"width:50%;margin:0 auto\">

	<table style=\"margin:0 auto\" >
	<tr><td class=\"label\">$phrase[141]</td><td class=\"input\"><input type=\"text\" name=\"name\" value=\"$name\" size=\"40\"></td></tr>
	
	<tr ";
		if ($type != "p" && $type != "w" && $type != "n" && $type != "g") { echo " style=\"display:none\"";}
		//wysiwyg
		
	
	echo "><td class=\"label\">$phrase[942]</td><td class=\"input\">
		<select name=\"input\">

		<option value=\"0\"";
		if ($input == 0) {echo " selected";}
		echo ">$phrase[337]</option>		
		<option value=\"1\" ";
		if ($input == 1) {echo " selected";}
		
		echo ">$phrase[839]</option>
			<option value=\"2\" ";
		if ($input == 2) {echo " selected";}
		
		echo ">html</option>
		</select>
		
		</td></tr>";
	
	
		echo "<tr";
			if ($type != "p") { echo " style=\"display:none\"";}
			//vertical vs hrozontal menu
		echo "><td class=\"label\">$phrase[852]</td><td class=\"input\">
		<select name=\"menutype\">
		<option value=\"vertical\" ";
		if ($menu == "vertical") {echo " selected";}
		
		
		echo ">$phrase[853]</option>
		<option value=\"horizontal\"";
		if ($input == "horizontal") {echo " selected";}
		echo ">$phrase[854]</option>
		</select>
		
		</td></tr>";
		
		echo "<tr";
			if ($type != "x" && $type != "y") { echo " style=\"display:none\"";}
			//menupath
		echo "><td class=\"label\">";
		if ($type == "x") {echo "$phrase[931]";}
		if ($type == "y") {echo "$phrase[933]";}
		
		echo "</td><td class=\"input\">
		<input type=\"text\" name=\"menupath\" value=\"$menupath\" size=\"40\">
				</td></tr><tr";
	
	if ($type != "x") { echo " style=\"display:none\"";}
			//menupath
		echo "><td class=\"label\">$phrase[932]</td><td class=\"input\">
		<input type=\"text\" name=\"adminpath\" value=\"$adminpath\" size=\"40\">
				</td></tr>";
	echo "
	<tr";
	if ($type == "z" || $type == "y") { echo " style=\"display:none\"";}
	//frontpage
	echo "><td class=\"label\">$phrase[924]</td><td>

	<select name=\"frontpage\"><option value=\"0\">$phrase[13]</option><option value=\"1\"";
	if ($frontpage == 1) {echo " selected";}
	echo ">$phrase[12]</option></select>

	</td></tr>
	<tr";
	if ($type == "z" || $type == "y") { echo " style=\"display:none\"";}
	//main menu
	echo "><td class=\"label\"><span id=\"menulabel\">$phrase[930]</span></td><td>

	<select name=\"mainmenu\"><option value=\"0\">$phrase[13]</option><option value=\"1\"";
	if ($mainmenu == 1) {echo " selected";}
	echo ">$phrase[12]</option></select>

	</td></tr>
        
        
        
        
        <tr><td class=\"label\"><span id=\"stylesheet\">$phrase[538]</span></td><td>

	<input name=\"stylesheet\" type=\"text\" value=\"$stylesheet\">

	</td></tr>
        
        <tr><td class=\"label\"><span id=\"hidemenu\">$phrase[1085]</span></td><td>

	<select name=\"hidemenu\"><option value=\"0\">$phrase[13]</option><option value=\"1\"";
	if ($hidemenu == 1) {echo " selected";}
	echo ">$phrase[12]</option></select>

	</td></tr>
        <tr";
	if ($type == "z")	{ echo " style=\"display:none\"";}
	echo "><td class=\"label\"><b>$phrase[875]</b></td><td style=\"text-align:left\"><select name=\"parent\">";
	 echo "<option value=\"0\"";
	if ($parent == 0) {echo " selected";}
	echo ">$phrase[495]</option>";
	
	 $sql = "select * from modules where type = 'z'";
	$DB->query($sql,"menu.php");

while ($row = $DB->get())
	     {
	     
	     	$mod = $row["m"];
            $name = $row["name"];
            echo "<option value=\"$mod\"";
            if ($parent == $mod) {echo " selected";}
            echo ">$name</option>";
            
	     }
	
	
	
	
	
	echo "</select></td><tr>";	
		
	
	if  ($type == "z") {
		 $sql = "select target,sidebar from widgets where m = '$m'";
	$DB->query($sql,"menu.php");

	$t = "";
	$b = "";
	
	
while ($row = $DB->get())
	     {
		$sidebar= $row["sidebar"];
		if ($sidebar == "t") {$t = $row["target"];}
	    if ($sidebar == "b") {$b = $row["target"];} 	
	     	
	     }
		echo "<tr><td class=\"label\">Text above navigation menu</td><td style=\"text-align:left\"><textarea cols=\"60\" rows=\"10\" name=\"toptext\">$t</textarea></td></tr>
		<tr><td class=\"label\">Text below navigation menu</td><td style=\"text-align:left\"><textarea cols=\"60\" rows=\"10\" name=\"bottomtext\">$b</textarea></td></tr>
		";
		
		
		
	}

		
		
	
	
	echo "<tr><td></td><td style=\"text-align:left\"><input type=\"submit\" value=\"$phrase[16]\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"edit\">";
	if ($type != "p" && $type != "w" && $type != "n" && $type != "g")
	{
		echo "<input type=\"hidden\" name=\"input\" value=\"0\">";
	}
		if ($type != "p")
	{
		echo "<input type=\"hidden\" name=\"menutype\" value=\"vertical\">";
	}
	echo "
	</td></tr>
	</table>

	
	
	
	</form>
	
	
	
	
	";
}
	else
		{
	
	
	
		echo "<h2>$phrase[189]</h2><a href=\"menu.php?event=add\"><img src=\"../images/add.png\" title=\"$phrase[176]\"></a>
	<p>$phrase[190]</p><br>";
		
		if (isset($comment)) {echo "<br><span style=\"font-size:200%;color:#cc3300\">$comment</span><br><br><br>
	";}
	

	 $sql = "select * from modules order by position,name ";

$DB->query($sql,"menu.php");
$num = $DB->countrows();

if ($num > 0)
{
	

	
	echo "<form method=\"post\" action=\"menu.php\"><table cellpadding=\"4\" style=\"margin:0 auto;text-align:left\" class=\"colourtable\" >
	<tr class=\"accent\"><td><b>$phrase[141]</b></td><td><b>$phrase[340]</b></td><td><b>$phrase[838]</b></td><td><b>$phrase[942]</b></td><td><strong>$phrase[852]</strong></td>
	<td></td><td></td><td><input type=\"submit\" value=\"reorder\" name=\"submit\"></td>
	
	<td></td><td></td><td><b>$phrase[26]</b></td><td><b>$phrase[24]</b></td></tr>
	";
	
	
	
	
$count = 0;

while ($row = $DB->get())
	     {
	     	$count++;
	     	$m = $row["m"];
            $modid[$count] = $m;
            $name = $row["name"];
        // echo $name;
			$linkname[$count] = urlencode($name);
			$mname[$count] = htmlspecialchars($name);
			$type[$count] =$row["type"];
			$input[$count] =$row["input"];
			$menu[$count] =$row["menu"];
			$position[$count] =$m;
			$parent[$count] =$row["parent"];
			$frontpage[$count] =$row["frontpage"];
			$menupath[$count] =$row["menupath"];
		
			
	     }
	     
	     $count = 0;
	
	     foreach ($modid as $index => $value)
									{
										$count++;
										
			if ($type[$index] == "y") {$link = $menupath[$index];}
			else if ($type[$index] == "x") { $link = "../main/custom.php?m=$modid[$index]";}
			else { $link = "../main/$menupath[$index]?m=$modid[$index]";}
			
			echo "<tr><td><strong><a href=\"$link\" title=\"$link\">$mname[$index]</a></strong>";
			if ($frontpage[$index] == 1) {echo "<br><span style=\"color:red\">$phrase[924]</span>";}
			
			
			
			echo "</td><td>$value</td><td>";
			if ($type[$index] == "c") {echo "$phrase[598]";}
			if ($type[$index] == "p") {echo "$phrase[599]";}
			if ($type[$index] == "e") {echo "$phrase[600]";}
			if ($type[$index] == "f") {echo "$phrase[601]";}
			if ($type[$index] == "i") {echo "$phrase[602]";}
			if ($type[$index] == "t") {echo "$phrase[603]";}
			if ($type[$index] == "h") {echo "$phrase[604]";}
			if ($type[$index] == "b") {echo "$phrase[605]";}
			if ($type[$index] == "s") {echo "$phrase[606]";}
			if ($type[$index] == "w") {echo "$phrase[826]";}
			if ($type[$index] == "l") {echo "$phrase[840]";}
			if ($type[$index] == "n") {echo "$phrase[97]";}
			if ($type[$index] == "1") {echo "$phrase[840]";}
			if ($type[$index] == "z") {echo "$phrase[875]";}
			if ($type[$index] == "x") {echo "$phrase[928]";}
			if ($type[$index] == "y") {echo "$phrase[933]";}
			if ($type[$index] == "g") {echo "$phrase[944]";}
			if ($type[$index] == "j") {echo "$phrase[954]";}
                        if ($type[$index] == "a") {echo "$phrase[1045]";}




			echo "</td><td>";
		if ($type[$index] == "p" || $type[$index] == "w" || $type[$index] == "n" || $type[$index] == "g") 
		{
			if ($input[$index] == 1) {echo "$phrase[839]";}
			if ($input[$index] == 0) {echo "$phrase[337]";} 
			if ($input[$index] == 2) {echo "html";}  
			
		}
									
		echo "</td><td>";
		
		if ($type[$index] == "p" || $type[$index] == "w") 
		{
			if ($menu[$index] == "vertical") {echo "$phrase[853]";} elseif ($menu[$index] == "horizontal") {{echo "$phrase[854]";}}
			
		}
		
		echo "</td>
		<td>";
			if ($type[$index] != "y" && $type[$index] != "c" && $type[$index] != "t")
			{ echo "<a href=\"menu.php?event=editsidebar&amp;sd=l&amp;m=$modid[$index]\"><img src=\"../images/sidebar_l.png\" title=\"$phrase[939]\"></a>";}
			
			echo "</td><td>";
			if ($type[$index] != "y" && $type[$index] != "c" && $type[$index] != "t")
			{ echo "<a href=\"menu.php?event=editsidebar&amp;sd=r&amp;m=$modid[$index]\"><img src=\"../images/sidebar_r.png\" title=\"$phrase[940]\"></a>";}
			
			echo "</td><td><input type=\"text\" name=\"order[$modid[$index]]\" value=\"$count\" size=\"2\"> </td><td>";
			//up arrow
			
			
			if (($num > 0) && ($count > 1) )
							{
						
							
							foreach ($position as $i => $value)
									{
									//echo "index is $index $value pagecount is $pagecount";
									if ($i == ($count - 1))
										{
										
										$up = $position;
										$up[$i] = $position[$count];
										$up[$count] = $value;
										
										}
									}
							
							
							

							$pageup = implode(",",$up);
							echo "<a href=\"menu.php?reorder=$pageup\"><img src=\"../images/up.png\" title=\"$phrase[77]\"></a>";
							
							
							
							
							}
						
			
			
			
			echo "</td><td>";
			
			
		//down arrow
		if (($num > 0) && ($count < $num))
							{
							
							//echo "<br>";
						
							
							
							
								foreach ($position as $i => $value)
									{
									//echo "$array_order[$index] <br>";
									if ($i == ($count))
										{
										$down = $position;
										$temp = $down[$i];
										$down[$i] = $down[$i + 1];
										$down[$i + 1] = $temp;
										}
									}
								
								
							
							$pagedown = implode(",",$down);
							echo "<a href=\"menu.php?reorder=$pagedown\"><img src=\"../images/down.png\" title=\"$phrase[78]\"></a>";
							}
		
		echo "</td><td><a href=\"menu.php?event=edit&amp;m=$modid[$index]\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"></a></td><td>
				
			 <a href=\"menu.php?update=delete&amp;type=$type[$index]&amp;m=$modid[$index]&amp;name=$linkname[$index]\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"></a></td></tr>";
			}
			echo "</table></form>";
}
			
			
	
	


}
echo "</div>";

include ("../includes/footer.php");

?>

