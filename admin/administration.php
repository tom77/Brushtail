<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");




include("../includes/leftsidebar.php");
							

	
	echo "<div id=\"content\"><div><h1 class=\"red\">$phrase[5]</h1>
	<h2>Brushtail $PREFERENCES[version]</h2>
	
	<ul class=\"listing\">
				
			<li><a href=\"../admin/calendars.php\">$phrase[607]</a></li>
			<li><a href=\"../admin/cleanup.php\">$phrase[608]</a></li>
			<li><a href=\"../admin/errors.php\">$phrase[609]</a></li>
			<li><a href=\"../admin/failedlogins.php\">$phrase[187]</a></li>
			<li><a href=\"../admin/prefs.php\">$phrase[533]</a></li>
			<li><a href=\"../admin/logins.php\">$phrase[188]</a></li>
		
			<li><a href=\"../admin/menu.php\">$phrase[610]</a></li>";
	
			if ($DATABASE_FORMAT == "mysql")
			{	
			echo "<li><a href=\"../admin/mysql.php\">$phrase[611]</a></li>";
			}
			
			if ($DATABASE_FORMAT == "sqlite" || $DATABASE_FORMAT == "sqlite3")
			{	
			echo "<li><a href=\"../admin/vacuum.php\">$phrase[921]</a></li>
                            <li><a href=\"../admin/sqlite.php\">$phrase[1079]</a></li>
                            ";
			}
			echo "<li><a href=\"../admin/phpinfo.php\">$phrase[612]</a></li>
			<li><a href=\"../admin/holidays.php\">$phrase[613]</a></li>
			<li><a href=\"../admin/users.php\">$phrase[614]</a></li>
			<li><a href=\"../admin/groups.php\">$phrase[615]</a></li>
			<li><a href=\"../admin/userpermissions.php\">$phrase[616]</a></li>
			<li><a href=\"../admin/grouppermissions.php\">$phrase[617]</a></li>
			<li><a href=\"../admin/statistics.php\">$phrase[685]</a></li>
			<li><a href=\"../admin/webauth.php\">$phrase[915]</a></li>
				
				
				</ul></div></div>
				";

include ("../includes/rightsidebar.php");
include ("../includes/footer.php");

?>

