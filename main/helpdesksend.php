<?php

//tests file was called properly
if (isset($m))
{

	
	
	
echo "
	<div style=\"margin-left: auto; margin-right:auto; width:60%;text-align:left\">

	
	  
		<form action=\"helpdesk.php\" method=\"post\" > 
		<fieldset><legend>$phrase[63]</legend><br>



	
		<b>$phrase[101]</b><br> $phrase[567]<br>


		<input type=\"text\" name=\"to\" id=\"to\"  size=\"60\"><br>
<br>


	

		<b>$phrase[684]</b><br>
		<textarea name=\"comment\"  cols=\"60\"></textarea><br><br>


";
		
?>

		
		<input type="hidden" name="probnum" value="<?php echo $probnum?>">
		<input type="hidden" name="m" value="<?php echo $m?>">
		
		<input type="submit" name="send" value="<?php echo $phrase[63]?>" >
		
		
	
	
		
		
		</fieldset>
    	</form> 	
	</div>

<?php
}
?>


