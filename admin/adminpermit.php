<?php

//checks user is administrator
if ($_SESSION['userid'] <> 1)

{
	
warning($phrase[1]);
include ("../includes/footer.php");
exit();	
}






?>