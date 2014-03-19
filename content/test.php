<?




include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");


	

$sql = "ALTER TABLE `pc_computers` ADD `default_usage` INT( 11 ) NOT NULL ";
$DB->query($sql,"page.php");

  
$sql = "select  min(pc_bridge.useno) as useno,pc_bridge.pcnum as pcnum from   pc_usage, pc_bridge where  pc_bridge.useno = pc_usage.useno and pc_usage.power = '0' group by pcnum";
$DB->query($sql,"page.php");
	echo "$sql <br><br<br>";
while ($row = $DB->get())
		 {
		 $useno = $row["useno"];
	     $pcnum = $row["pcnum"];
	     
	     $uses[$pcnum] = $useno;
	    
	 
		 }

 foreach($uses as $pcnum => $useno)
{
	
   $sql = "update pc_computers set default_usage = '$useno' where pcno = '$pcnum'";
	   $DB->query($sql,"page.php");
	   
	   echo "$sql <br>";	
	
	
}
	
	
		

   
   include ("../includes/footer.php");
?>
