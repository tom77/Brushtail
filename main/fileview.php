<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$bodyjavascript = "onLoad=\"self.focus()\"";

include ("../includes/htmlheader.php");





if (isinteger($_REQUEST["m"]))
{
	$m = $_REQUEST["m"];
	$access->check($m);
	
	if ($access->thispage < 1)
		{
		
		$ERROR  =  "<div style=\"text-align:center;font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
		}
	elseif ($access->iprestricted == "yes")
		{
	$ERROR  =  $phrase[0];
		}
	
		
	
	
}
else 
{
$ERROR  =  $phrase[72];	
}

if (isset($_REQUEST["doc_id"])) 
	{
	if (!(isinteger($_REQUEST["doc_id"]))) 
	{$ERROR  = $phrase[72];}
	else {$doc_id = $_REQUEST["doc_id"];}
	}



if(!isset($ERROR))
	{
	
	 $sql = "select * from documents, page where  documents.page = page.page_id  and documents.m = \"$m\" and doc_id = \"$doc_id\"";
	
$DB->query($sql,"filesview.php");
	$row = $DB->get();
	$doc_id = $row["doc_id"];
     $cat_name = $row["page_title"];
      $doc_name = $row["doc_name"];
      $type = $row["type"];
      $description = nl2br($row["keywords"]);
      $uploaddate = strftime("%x",$row["uploaddate"]);
			
		echo "<div style=\"padding:2em\"><h1 style=\"margin-bottom:0\">$_REQUEST[modname]</h1>
		<span style=\"float:right\" class=\"hide\"><a href=\"javascript:window.print()\">$phrase[250]</a> &nbsp;
<a href=\"javascript:window.close()\">$phrase[266]</a></span><br><br>
		
		<table>
		<tr><td><b>$phrase[317]</b></td><td>$doc_name</td></tr>
		<tr><td><b>$phrase[279]</b></td><td>$cat_name</td></tr><br>
<tr><td><b>$phrase[186]</b></td><td>$uploaddate</td></tr>
<tr><td colspan=\"2\"><b>$phrase[280]</b></td></tr>
<tr><td colspan=\"2\"><br>
$description</td><td></td></tr>
		
		</table></div>";	
	
	
	}
	

include ("../includes/footer.php");
	
?>