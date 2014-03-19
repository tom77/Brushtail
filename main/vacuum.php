<?

$db = sqlite_open("F:\portable\sqldata\bdb", 0666, $sqliteerror);
  
    
    

	


	$query =  sqlite_exec($db, "VACUUM;");
	
	echo "vacuuming $this";


?>