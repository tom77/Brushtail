<?php
$limit = 10;

include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);
//if (isset($_REQUEST["search"]) && $_REQUEST["search"] == "advanced") { $bodyjavascript=" onload=\"check_all()\"";}
//if (isset($_REQUEST["search"]) && $_REQUEST["search"] == "advanced") { $bodyjavascript=" onload=\"document.getElementsByTagName('input').checked=true\"";}
include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");





include("../includes/leftsidebar.php");

echo "<div id=\"content\"><div><h1 >Search</h1>";

if (isset($_REQUEST["offset"]))
{
if (!(isinteger($_REQUEST["offset"])))
	{$ERROR  =  "$phrase[72]";	}
	else 	
	{
	$offset = $_REQUEST["offset"];	
	}
}
else {$offset = 0;}





function advancedform($menu,$path,$phrase)
{
	

	echo "<script type=\"text/javascript\">
function checkboxes(value)
{
	
   var inputs = document.getElementsByTagName(\"input\");
   for(var t = 0;t < inputs.length;t++){
     if(inputs[t].type == \"checkbox\")
       inputs[t].checked = value;
   }
}
</script>

	<form  method=\"get\" action=\"search.php\"  ><fieldset><legend>$phrase[326]</legend><br>
 <table>
 <tr><td><b>$phrase[319]</b></td><td><input type=\"text\" name=\"keyword\" size=\"60\"";
 if (isset($_REQUEST["keyword"])) {echo " value=\"$_REQUEST[keyword]\"";}
 echo "><br><br></td></tr>
 <tr><td><b>$phrase[320]</b></td><td>
 ";
 //\"
	if (isset($menu)	)
	{
		foreach ($menu as $mod => $name)
	
	{
		if ($path[$mod] == "page.php" || $path[$mod] == "files.php")
		{
	echo "<input type=\"checkbox\" name=\"scope[$mod]\"  checked onchange=\"uncheck()\"> $name<br>";}
	}					
	//onchange=\"document.getElementById('chk_all').checked=false\"				
	}			
 echo "
<input type=\"button\" onclick=\"checkboxes(true);\" value=\"$phrase[878]\">
&nbsp;&nbsp;
<input type=\"button\" onclick=\"checkboxes(false);\" value=\"$phrase[879]\">
 
 
 </td></tr>
 <tr><td><b>$phrase[322]</b> </td><td>
 <select name=\"type\">
 <option value=\"all\">$phrase[323]</option>
 <option value=\"content\">$phrase[324]</option>
  <option value=\"docs\">$phrase[325]</option>
 </select>
 </td></tr>
 <tr><td></td><td>
 <input type=\"hidden\" name=\"search\" value=\"advanced\">
 <input type=\"submit\" name=\"submit\" value=\"$phrase[282]\"></td></tr>
 </table></fieldset>
 
				</form>";
}



function simpleform($phrase)
{
	
	echo "<form  method=\"get \" action=\"search.php\" ><input type=\"text\" name=\"keyword\" size=\"60\" ";
						 if (isset($_REQUEST["keyword"])) {echo "value=\"$_REQUEST[keyword]\"";}
						 echo ">
						 <input type=\"hidden\" name=\"global\" value=\"on\">
						 <input type=\"hidden\" name=\"type\" value=\"all\">
						 <br><input type=\"submit\" name=\"search\" value=\"$phrase[282]\">
						</form>";
	
}







if (isset($_REQUEST["search"]) && $_REQUEST["search"] == "advanced" && !isset($_REQUEST["submit"]))
			{
			
			advancedform($access->mainmenu,$access->mainlink,$phrase);	
				

				
			}

 elseif (isset($_REQUEST["keyword"]) )
			{
		
			$keyword = $_REQUEST["keyword"];
			
			
			if($keyword == "")
			
			{
				echo "<h2>$phrase[328]</h2></div>";
			}
	else 
	{
			
		
		
		if ($offset == 0)
			{
			$ender = " limit $limit";
			}
		else	
			{
	
			$ender = " limit $offset, $limit";
			}

	
	$sqlkeyword = $DB->escape($keyword);
	if (isset($_REQUEST["search"]) && $_REQUEST["search"] == "advanced") 
	{$search = $_REQUEST["search"];}	else {$search = "simple";}
	if (isset($_GET["linkscope"])) {$scope = unserialize($_GET["linkscope"]);}
	if (isset($_REQUEST["scope"])) {$scope = $_REQUEST["scope"];}

						
	//print_r($scope);		

	$insert = "page.m in (";
	$counter = 0;
	foreach ($access->mainmenu as $index => $mod)
		{
			
			if (!isset($scope) || (array_key_exists($index,$scope) && $scope[$index] == "on"))
			{
			if ($counter <> 0) {$insert .= ",";}
		$insert .= "$index";	
		$counter++;	
			}							
										
		}
		
		$insert .= ") and ";
	
	//print_r("$_REQUEST");
	
	/*
	if (isset($scope)) 
	{
	$insert .= "modules.m in (";
	$counter = 0;
	foreach ($scope as $mod => $status)
		{
		if ($counter <> 0) {$insert .= ",";}
		$insert .= "$mod";	
		$counter++;
		}
	$insert .= ") and ";
	}
	
	*/
	
//,MATCH (page.metadata) AGAINST (\"$sqlkeyword\" IN BOOLEAN MODE) as score
//$startscript = time();
	if ($DB->type == "mysql")
		{
	$pagesql = "select distinct page.page_id as page_id,\"none\" as doc_id,page.page_title as page_title, \"none\" as doc_name, \"page\" as resulttype,page.m,modules.name,
	MATCH (page.metadata) AGAINST (\"$sqlkeyword\" IN BOOLEAN MODE) as score  from page,modules
	where page.deleted = '0' and page.published = '1' and modules.m = page.m and $insert 
	MATCH (page.metadata) AGAINST (\"$sqlkeyword\" IN BOOLEAN MODE)";

	
	$docsql = "select distinct page_id,doc_id,  page_title,  doc_name, \"document\" as resulttype ,page.m,modules.name,
	 MATCH (keywords) AGAINST (\"$sqlkeyword\" IN BOOLEAN MODE) as score from documents, page,modules 
	 where documents.deleted = '0' and page.m = documents.m and page.page_id = documents.page and modules.m = page.m and $insert 
 	MATCH (keywords) AGAINST (\"$sqlkeyword\" IN BOOLEAN MODE)";
	
		}
	
		
			else
		{
			
		$sqlkeyword = trim($sqlkeyword);
       			
       			$words = explode(" ",$sqlkeyword);
       			
       			$string = "";
       			
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
				$string .= " and (page.metadata like '%$value%') ";	
				$counter++;
				}	
				if ($sqlkeyword == "") {$string = " and 1 == 2";}	
				
			
			
	$pagesql = "select distinct page.page_id as page_id,'none' as doc_id,page.page_title as page_title, 'none' as doc_name, 'page' as resulttype,page.m as m,modules.name as name
	  from page,modules
	where page.deleted = '0' and page.published = '1' and modules.m = page.m $string ";
	
	//echo $pagesql;
	

	$sqlkeyword = trim($sqlkeyword);
       			
       			$words = explode(" ",$sqlkeyword);
       			
       			$string = "";
       			
       			$counter = 0;
       			foreach ($words as $index => $value)
				{
				$string .= " and (keywords like '%$value%') ";	
				$counter++;
				}	
				if ($sqlkeyword == "") {$string = " and 1 == 2";}	
				
	$docsql = "select distinct page_id,doc_id,  page_title,  doc_name, 'document' as resulttype ,page.m as m,modules.name as name from documents, page,modules 
	 where documents.deleted = '0' and page.m = documents.m and page.page_id = documents.page and modules.m = page.m  $string ";
	
		}
		
		
	
	//echo $pagesql;	
	if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "content")
	{ $sql = $pagesql;}
	elseif (isset($_REQUEST["type"]) && $_REQUEST["type"] == "docs")
	{ $sql = $docsql;}
	else { $sql = $pagesql . " union " . $docsql;}
	//$sql = $pagesql;
	
	
	 $DB->query($sql,"search.php");
	$numrows= $DB->countrows();
		//$ender = "";
		
	if ($DB->type == "mysql")
		{
		 $sql = $sql . " order by score desc " . $ender ;
		}
		
	else
		{
		 $sql = $sql . $ender ;
		}	
		
		$DB->query($sql,"search.php");
		//echo $sql ;
		
		
		if (!isset($offset)) {$offset = 0;}
		$displayoffset = 1 + $offset;
		$displayend = $limit + $offset;
		if ($displayend > $numrows) {$displayend = $numrows;}
		 
		
		if ($displayend == 0) {$displayoffset = 0;}

$_keyword = htmlspecialchars($keyword);
	echo "<div ><span style=\"font-size:150%\">$phrase[329] <i>$_keyword</i></span></div>
	<table  cellspacing=\"0\" cellpadding=\"5\"    style=\"clear:both\">
	";
	
	
	
	if ($numrows == 0) {echo "</table>";}
	else {
echo "<tr class=\"accent\"><td style=\"width:20%\">";

if ($numrows > $limit && $numrows <> 0)
	{
		
		
	$prevoffset = $offset - $limit;
	$nextoffset = $offset + $limit;	
	
	$keyword = urlencode($keyword);
	
	
	if (isset($_REQUEST["global"]))
	{
	$insert = "&amp;keyword=$keyword&global=$_REQUEST[global]&amp;type=$_REQUEST[type]";
	}
	else 
	{
		$scope = urlencode(serialize($scope));
	$insert = "&amp;keyword=$keyword&amp;linkscope=$scope&amp;type=$_REQUEST[type]";	
	}
	

	
if ($offset <> 0)
		{
		 echo  "<a href=\"search.php?offset=$prevoffset$insert\" >$phrase[215]</a>";
		}
	} else {echo "&nbsp;";}
		
	echo "</td><td style=\"width:20%\" align=\"center\">$displayoffset - $displayend  $phrase[330] $numrows</td><td style=\"width:20%\" align=\"right\">";	

	if ($numrows > $limit)
	{		
if ($nextoffset < $numrows)
			{
			echo  "<a href=\"search.php?offset=$nextoffset$insert\">$phrase[216]</a>";	
			}
	 
	}	
	echo "</td></tr></table><br>";
	}
	
	echo "<div >";
	
if (!isset($printprev)) { $printprev = "";}
if (!isset($printnext)) { $printnext = "";}
	
		

		while ($row = $DB->get())
						{
						$doc_id = $row["doc_id"];
						$doc_name = $row["doc_name"];
						$page_id = $row["page_id"];
						$page_title = $row["page_title"];
						$name = $row["name"];
						//$menupath = $row["menupath"];
						$resulttype = $row["resulttype"];
						$m = $row["m"];
						
					//	echo "$row[score]";
						
						
						if ($resulttype == "page") 
						{
						echo "<a href=\"page.php?m=$m&amp;page_id=$page_id\">$page_title</a>	<br>
						
						<span class=\"primaryfont\"> $name > $page_title</span> <br><br>";	
						 //$phrase[332]
						}
					
						else 
						{
						echo "<a href=\"doc.php?m=$m&doc_id=$doc_id\">$doc_name</a> <br>
					
						<span class=\"primaryfont\">$name > $doc_name</span> <br><br>";
							//$phrase[331]		
						}
						}
						
echo "</div>";
						
						if ($numrows <> 0) {
						echo "<table  cellspacing=\"0\" cellpadding=\"5\" ><tr class=\"accent\"><td style=\"width:20%\">";
						if ($numrows > $limit)
	{
		

	
if ($offset <> 0)
		{
		 echo  "<a href=\"search.php?offset=$prevoffset$insert\" >$phrase[215]</a>";
		} else {echo "&nbsp;";}
	}
		
	echo "</td><td align=\"center\" style=\"width:20%\">$displayoffset - $displayend  $phrase[330] $numrows</td><td align=\"right\" style=\"width:20%\">";	

	if ($numrows > $limit)
	{		
if ($nextoffset < $numrows)
			{
			echo  "<a href=\"search.php?offset=$nextoffset$insert\">$phrase[216]</a>";	
			}
	 
	}	
	echo "</td></tr></table><br><br><br>";
						}

			
		
       if ($numrows == 0)	
				{ echo "$phrase[333]<br><br>
				
				<b>$phrase[334]</b><br><br>";
				if ($search == "advanced")
				{ advancedform($menu,$path,$phrase);}
				else {	simpleform($phrase);	}
				
				}
	
	
	}	
}



echo "</div></div>";


include("../includes/rightsidebar.php");
	//$endscript = time();
	//$diff = $endscript - $startscript;
	//echo "<h1>duration is $diff</h1>";

include ("../includes/footer.php");

?>

