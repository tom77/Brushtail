<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");


echo "<div class=\"mainbox\"><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1>";


if (isset($_REQUEST["branchno"]))
{
if ((isinteger($_REQUEST["branchno"])))
	{
	$branchno = $_REQUEST["branchno"];	
	}
	else 
	{$ERROR  =  "$phrase[72]";	}	
	
}
	
			
 if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addlocation")
   {
   	if ($_REQUEST["bname"] == "")
   	{
   		$WARNING = "$phrase[480]";
   	}
   	else {
	$bname = $DB->escape($_REQUEST["bname"]);
	$telephone = $DB->escape($_REQUEST["telephone"]);
	$earlyfinish = $DB->escape($_REQUEST["earlyfinish"]);
	$sql = "INSERT INTO branches VALUES(NULL,'$bname','$m','$bookinginterval','$telephone','$earlyfinish')";
	$DB->query($sql,"pcadmin.php");
	
   	}
		
	}
		
	
  if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delbranch")
  {
  	
 	
  	
  	$sql = "delete from branches where branchno = '$branchno'";
  	$DB->query($sql,"menu.php");
  
  	$DB->tidy("branches");
  	
  	$sql = "delete from branch_openinghours where branchid = '$branchno'";
  	$DB->query($sql,"menu.php");
  	
  
	$DB->tidy("branch_openinghours");
  	
  	$sql = "delete from pc_bookings where branchid = '$branchno'";
  	$DB->query($sql,"pcadmin.php");
  	
 $DB->tidy("pc_bookings");
  	
  	
  	
  }

		
		
if (isset($_REQUEST["rename"]) )
		{
		$bname = $DB->escape($_REQUEST["bname"]);
		
  		$sql = "update branches set name = \"$bname\" where branchno=\"$branchno\"";
		$DB->query($sql,"locations.php");
		

		}
		
elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delbranch")
			 {	
			 $sql = "select pcno from pc_computers where branch = '$branchno'";
			 $DB->query($sql,"pcadmin.php");
			 $num = $DB->countrows();
			 
			 if ($num > 0)
			 {
			 	warning("$phrase[500]");
			 }
			 else {	
			 echo "<h2>$phrase[410]</h2><b>$phrase[14]</b><br><br>
	<a href=\"pcadmin.php?m=$m&amp;update=delbranch&amp;branchno=$branchno\">$phrase[12]</a> | <a href=\"pcadmin.php?m=$m\">$phrase[13]</a>";

			 		
			 }
			 }
 else
 	{

echo "<h2>$phrase[179]</h2>";

?>
<form action="locations.php"><fieldset><legend><?php echo $phrase[177]?></legend><br>

<?php echo $phrase[180]?>  <input type="text" name="bname" size="50" maxlenghth="100"><br>

<input type="hidden" name="update" value="<?php echo $phrase[176]?>">
<input type="submit" name="addbranch" value="<?php echo $phrase[177]?>">

</fieldset>
</form>


<?php	
		

$sql = "select * from branches  where m = '$m' order by name";
 $DB->query($sql,"pc.php");


 echo "<h2>$phrase[410]</h2> <a href=\"locations.php?m=$m&amp;event=addlocation\"><img src=\"../images/add.png\" title=\"$phrase[522]\" alt=\"$phrase[522]\"></a><br><br>
 <table class=\"colourtable\" cellpadding=\"6\"  style=\"margin-left:auto;margin-right:auto\">";
while ($row = $DB->get())
      {
      $branchno = $row["branchno"];
      $bname = formattext($row["name"]);
     
      echo "<tr><td>$bname</td>

     <td><a href=\"locations.php?m=$m&amp;event=editbranch&amp;branchno=$branchno\"><img src=\"../images/clock.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
    
      <td><a href=\"locations.php?m=$m&amp;event=delbranch&amp;branchno=$branchno\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a></td></tr>";
      }


 echo "</table>
  <br>";
  







	}

echo "</div>";
include ("../includes/footer.php");

?>

