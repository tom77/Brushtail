<?php



include ("../includes/initiliaze_page.php");



include ("adminpermit.php");

	
	
	
if (isset($ERROR))
	{	
		echo $ERROR;
	}								

	
	else {

		$table = $DB->escape($_REQUEST["table"]);

		
		$result = mysql_query("SHOW COLUMNS FROM $table");

if (mysql_num_rows($result) > 0) {
   while ( $row = mysql_fetch_assoc($result)) {
        $fields[] = $row["Field"];
        
   }
}
	
		
		
		
		$data = "";
	
		$counter =0;
		
		foreach ($fields as $index => $fieldname)
		{
		if ($counter <> 0) { $data .= ",";}
		$data .= $fieldname;
		
		$counter++;
		}
$data .= "
";
		
	$sql = "select * from $table";
	
	$DB->query($sql,"export.php");
	while($row = $DB->get())
	{	
		
		$counter =0;
		
		foreach ($fields as $index => $fieldname)
		{
		if ($counter <> 0) { $data .= ",";}
		$field = $row[$fieldname];
		
		
	 $field = str_replace("\"","\"\"",$field);
	 
	 $field = "\"" . $field . "\"";
		
	
//$field = str_replace(',','"
//"',$field); 
		$data .= $field;
		
		$counter++;
		}
$data .= "
";
	}
			
			
			
	
				$size = strlen($data);
		
			header("content-type: text/csv \n");
			
			header("Content-Transfer-Encoding: binary\n"); 
			
			header("content-length: $size \n");
			
			header("content-disposition: attachment; filename=\"$table.csv\" \n");
		echo $data;

			
			
			}



?>
