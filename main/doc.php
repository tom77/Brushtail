<?php

include ("../includes/initiliaze_page.php");


$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);


if (!(isset($_GET['doc_id'])))
{
$ERROR  = "No document id.";	
}
else {
		if (!(isinteger($_GET["doc_id"])))
		{
		$ERROR  = "Document id is not an integer";
		}
		else 
		{
		$doc_id = $_GET["doc_id"];
		}
}


 if (isset($ERROR))
	{
   	header("Location: $url" . "error.php?error=input");
	exit();
	}
        
  elseif (!isset($m))
    {
	header("Location: $url" . "error.php?error=module");
	exit();
    }
		$access = new Access($DB) ;
		$access->check($m);
	
		if ($access->thispage < 1)
			{
		
			$ERROR  =  "<div style=\"text-align:center;font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
			}
		elseif ($access->iprestricted == "yes")
			{
			$ERROR  =  $phrase[0];
			}

	
if (!isset($ERROR))
{
	$sql = "select doc_name,data, type, size,filename, page from documents where doc_id = '$doc_id' and m = '$m'" ;
			
	$DB->query($sql,"doc.php");
			$row = $DB->get();
			
			
			$doc_name = $row["doc_name"];
			$filename = $row["filename"];
			$type = $row["type"];
			$size = $row["size"];
			$category = $row["page"];
			$path = $PREFERENCES['docdir'];
			$filepath = $path."/".$m."/".$category."/".$filename;
			$size = @filesize($filepath);
			
			//echo "path is $filepath cat $category filename $filename $sql";
			
			
			//$size = strlen($contents);
                        $data = "";
                        
                        if ($PREFERENCES["storage"] == "file")
                      {
                       // readfile($filepath);
                       // if (!@$file_handle = fopen($filepath,"rb")) { $ERROR = "$phrase[85] $filepath " ; } 
			if (!@$data = file_get_contents($filepath)) { $ERROR = "$path $phrase[86] $filepath"; } ;
			@fclose($file_handle); 
                      }
                        if ($PREFERENCES["storage"] == "database")
                      {
                          $data = base64_decode($row["data"]);
                      }
                        
                        
                        
                       // exit();
			
			if (isset($ERROR))
			{
				echo  $ERROR;
			}
			else 
			{
				$size = strlen($data);
			
			header("content-type: $type");
			
			header("Content-Transfer-Encoding: binary\n"); 
			
			header("content-length: $size");
			
			header("content-disposition: inline; filename=\"$doc_name\"");
			echo $data;

			
			}
			
			
			
			
}


?>
