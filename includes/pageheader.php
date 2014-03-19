<div id="container" >

					
					<?php
					
					if ($PREFERENCES["custombanner"] == 1)
					{
						
						include("custombanner.php");
					}
					else 
					{
					
					echo "<div id=\"banner\">";
					
					
if ($PREFERENCES["logo"] == "yes") { echo "<img src=\"../images/logo.png\" alt=\"$PREFERENCES[sitename]\">";}
				
					echo "<h1>$PREFERENCES[sitename]</h1>";
					
				
			
						if ($PREFERENCES["displayemaillink"] == "yes")
						{ echo "<a href=\"$PREFERENCES[emaillink]\" onclick=\"window.open('$PREFERENCES[emaillink]');return false;\" id=\"emaillink\" >$phrase[276]</a>";}
						
						
						echo "</div>";
					}
						
				
				
						
						
						
						

	if ($_SESSION['username'] != "guest" || $PREFERENCES["cmbanner"] == 1)
	{					
						echo "<div id=\"utility\">
						<ul><li>
						<a href=\"../";
						
						echo homepath($DB);
						
						echo "\" class=\"link\">$phrase[277]</a></li>";
                                                
                                                if (!isset($modhidemenu) || (isset($modhidemenu) && $modhidemenu == 0)) echo "<li>|<a href=\"../main/sitemap.php\" class=\"link\">$phrase[990]</a></li>";
						
						
						
						if ($_SESSION['userid'] == 1)
						{ echo "<li>|<a href=\"../admin/administration.php\" class=\"link\">$phrase[5]</a></li>";}
						
						
						if ($access->contentmanagement > 0)
						
						echo "<li>|<a href=\"../content/content.php\" class=\"link\">$phrase[278]</a></li>";							
					    
						echo "</ul>";
					 

				
						if ($_SESSION['username'] != "guest")
						{ echo "$phrase[274] <strong> $_SESSION[username]</strong>";}
						

						
						echo "";
						
						if ($_SESSION['username'] == "guest")
						{ echo "<a href=\"../index.php\"  class=\"link\">$phrase[29]</a>";}
						else {
							echo "<a href=\"../logout.php\"  class=\"link\">$phrase[275]</a>";;
						}
						

							echo "</div>";
}
							
							if ($PREFERENCES["navlocation"] == "t" && $modhidemenu == 0)
							{
							$nav = "topnav";
								
							include("../includes/nav.php")	;
								
								
							}
							
						
							if ($PREFERENCES["searchbanner"] == 1)
							{
						?>
						
						
						
					
					
						<div id="search"> 
						<a href="../main/searchtips.php"><?php echo $phrase[327]?></a>|<a href="../main/search.php?search=advanced"><?php echo $phrase[326]?></a>
						<form  method="GET" action="../main/search.php"><p>
						 <input type="text" name="keyword"><input type="hidden" name="global" value="on">
						  <input type="hidden" name="type" value="all"> 
						  <input type="submit" name="search" value="Search"></p></form>
					</div>
					
					<?php
							} else {echo "<br>";}
							
							echo "&nbsp;<div id=\"floatwrap\">";
					?>

