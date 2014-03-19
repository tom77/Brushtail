<?php


include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

$bodyjavascript = "onLoad=\"self.focus()\"";

include ("../includes/htmlheader.php");



$ip = ip("pc"); 



if (isset($_REQUEST["bookingno"])) 
	{
	if (!(isinteger($_REQUEST["bookingno"]))) 
	{$ERROR  = $phrase[72];}
	else {$probnum = $_REQUEST["bookingno"];}
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
	
if(!isset($ERROR))
	{
	
		
		$sql  = "select pin from pc_bookings where bookingno = '$bookingno'";	
	$DB->query($sql,"helpdesk.php");
	
	$row = $DB->get();
	$callcounter = $row["callcounter"];
	$email= $row["email"];	
	$email_action= $row["email_action"];
	$search = $row["search"];
	$delbutton = $row["delbutton"];
	$showclock = $row["showclock"];	
		
		
	
	
	}
	

include ("../includes/footer.php");
	
?>