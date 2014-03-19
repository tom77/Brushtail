<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$datepicker = "yes";


         if (isset($SPAMTOOLS) && ($SPAMTOOLS == "all" || $SPAMTOOLS == "internal"))
 {
   $salt = uniqid(); 


salt($salt);
 }




	page_view($DB,$PREFERENCES,$m,"");	

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

 	include("../includes/leftsidebar.php");    

 	
 echo "<div id=\"content\"><div>";	
 	
 	
//echo "<div style=\"text-align:center;float:left\">";



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

	$access->check($m);

if ($access->thispage < 1)
		{
		
		
		header("Location: $url" . "error.php?error=permissions");
		exit();
		}
	elseif ($access->iprestricted == "yes")
		{
		header("Location: $url" . "error.php?error=ipaccess");
		exit();
		}
	
	

$formpage = "emailform.php";
include("form.php");




   echo "</div></div>";
   
   	include("../includes/rightsidebar.php");     
include ("../includes/footer.php");

?>

