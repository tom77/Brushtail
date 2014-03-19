<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");

include ("adminpermit.php");


if (isset($_REQUEST["submit"]))
{

	$preferences = $_REQUEST["Preferences"];

	$emaillink = $DB->escape($preferences["emaillink"]);
	$displayemaillink = $DB->escape($preferences['displayemaillink']);
	$sitename = $DB->escape($preferences['sitename']);
	$orgname = $DB->escape($preferences['orgname']);
$stylesheet = $DB->escape($preferences['stylesheet']);
$logo = $DB->escape($preferences['logo']);
$maxfilesize = $DB->escape($preferences['maxfilesize']);
$maxpicsize = $DB->escape($preferences['maxpicsize']);
$maxpasswordage = $DB->escape($preferences['maxpasswordage']);
$docdir = $DB->escape($preferences['docdir']);
$allowguest= $DB->escape($preferences['allowguest']);
$complex= $DB->escape($preferences['complex']);
$passwordlength= $DB->escape($preferences['passwordlength']);
$logout = $DB->escape($preferences['logout']);
$imagesize = $DB->escape($preferences['imagesize']);
$flyoutmenu = $DB->escape($preferences['flyoutmenu']);
$searchbanner = $DB->escape($preferences['searchbanner']);
$custombanner = $DB->escape($preferences['custombanner']);
$navlocation = $DB->escape($preferences['navlocation']);
$cmbanner = $DB->escape($preferences['cmbanner']);
$stats_ignore = $DB->escape($preferences['stats_ignore']);
$storage= $DB->escape($preferences['storage']);
$loghits= $DB->escape($preferences['loghits']);
$hitslog= $DB->escape($preferences['hitslog']);




$sql = "update preferences set pref_value = '$sitename' where pref_name = 'sitename'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$orgname' where pref_name = 'orgname'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$stylesheet' where pref_name = 'stylesheet'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$emaillink' where pref_name = 'emaillink'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$displayemaillink' where pref_name = 'displayemaillink'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$maxpicsize' where pref_name = 'maxpicsize'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$maxfilesize' where pref_name = 'maxfilesize'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$maxpasswordage' where pref_name = 'maxpasswordage'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$logo' where pref_name = 'logo'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$docdir' where pref_name = 'docdir'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$allowguest' where pref_name = 'guest'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$complex' where pref_name = 'complex'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$passwordlength' where pref_name = 'passwordlength'";
$DB->query($sql,"prefs.php");


$sql = "update preferences set pref_value = '$logout' where pref_name = 'logout'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$imagesize' where pref_name = 'imagesize'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$flyoutmenu' where pref_name = 'flyoutmenu'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$searchbanner' where pref_name = 'searchbanner'";
$DB->query($sql,"prefs.php");



$sql = "update preferences set pref_value = '$custombanner' where pref_name = 'custombanner'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$navlocation' where pref_name = 'navlocation'";
$DB->query($sql,"prefs.php");

$sql = "update preferences set pref_value = '$cmbanner' where pref_name = 'cmbanner'";
$DB->query($sql,"prefs.php");


$sql = "update preferences set pref_value = '$stats_ignore' where pref_name = 'stats_ignore'";
$DB->query($sql,"prefs.php");


$sql = "update preferences set pref_value = '$storage' where pref_name = 'storage'";
$DB->query($sql,"prefs.php");


$sql = "update preferences set pref_value = '$loghits' where pref_name = 'loghits'";
$DB->query($sql,"prefs.php");


$sql = "update preferences set pref_value = '$hitslog' where pref_name = 'hitslog'";
$DB->query($sql,"prefs.php");


}



$sql = "select * from preferences";

$DB->query($sql,"cleanup.php");
while ($row = $DB->get())
{
   // print_r($row);
	if ($row["pref_name"] == "sitename" ) { $sitename = $row["pref_value"]; }
	if ($row["pref_name"] == "orgname" ) { $orgname = $row["pref_value"]; }
	if ($row["pref_name"] == "stylesheet" ) { $stylesheet = $row["pref_value"]; }
	if ($row["pref_name"] == "emaillink" ) { $emaillink = $row["pref_value"]; }
	if ($row["pref_name"] == "displayemaillink" ) { $displayemaillink = $row["pref_value"]; }	
	if ($row["pref_name"] == "maxpicsize" ) { $maxpicsize = $row["pref_value"]; }
	if ($row["pref_name"] == "maxfilesize" ) { $maxfilesize = $row["pref_value"]; }
	if ($row["pref_name"] == "maxpasswordage" ) { $maxpasswordage = $row["pref_value"]; }
	if ($row["pref_name"] == "logo" ) { $logo = $row["pref_value"]; }
	if ($row["pref_name"] == "docdir" ) { $docdir = $row["pref_value"]; }
	if ($row["pref_name"] == "guest" ) { $allowguest = $row["pref_value"]; }
	if ($row["pref_name"] == "complex" ) { $complex = $row["pref_value"]; }
	if ($row["pref_name"] == "passwordlength" ) { $passwordlength = $row["pref_value"]; }
	if ($row["pref_name"] == "logout" ) { $logout = $row["pref_value"]; }
	if ($row["pref_name"] == "imagesize" ) { $imagesize = $row["pref_value"]; }
	if ($row["pref_name"] == "flyoutmenu" ) { $flyoutmenu = $row["pref_value"]; }
	if ($row["pref_name"] == "searchbanner" ) { $searchbanner = $row["pref_value"]; }
	if ($row["pref_name"] == "custombanner" ) { $custombanner = $row["pref_value"]; }
	if ($row["pref_name"] == "navlocation" ) { $navlocation = $row["pref_value"]; }
	if ($row["pref_name"] == "cmbanner" ) { $cmbanner = $row["pref_value"]; }
	if ($row["pref_name"] == "stats_ignore" ) { $stats_ignore = $row["pref_value"]; }
        if ($row["pref_name"] == "storage" ) { $storage = $row["pref_value"]; }
        if ($row["pref_name"] == "loghits" ) { $loghits = $row["pref_value"]; }
        if ($row["pref_name"] == "hitslog" ) { $hitslog = $row["pref_value"]; }
	
	
}


echo "


<div id=\"mainbox\"><h1><a href=\"administration.php\" class=\"link\">$phrase[5]</a></h1> 
</div>



<div style=\"text-align:center\">
			<h2>$phrase[533]</h2>
			
			<form action=\"prefs.php\" method=\"post\" style=\"width:80%;margin-left:auto;margin-right:auto;text-align:center\">
	";
			
			
			?>
			

<fieldset style="margin-top:1em"><legend>Layout</legend>
<table style="margin:1em auto;width:100%">
<tr><td class="label" style="width:40%"><strong><?php echo $phrase[536]?></strong></td><td><INPUT type="text" name="Preferences[sitename]" value="<?php echo $sitename?>" size="50" maxlength="50">


</td></tr>


<tr><td class="label"><strong><?php echo $phrase[538]?></strong></td><td><select name="Preferences[stylesheet]">

<?php

$css["blue"]= "blue.css";
$css["blueblue"]= "blueblue.css";
$css["greybeige"]= "greybeige.css";
$css["greyblue"]= "greyblue.css";
$css["custom"]= "custom.css";
/*

$css["bluepurple"]= "bluepurple.css";
$css["bonetan"]= "bonetan.css";
$css["browncream"]= "browncream.css";
$css["brownolive"]= "brownolive.css";
$css["greenblue"]= "greenblue.css";
$css["green"]= "green.css";
$css["greenbrown"]= "greenbrown.css";

$css["mauve"]= "mauve.css";
$css["purple"]= "purple.css";
$css["sandstone"]= "sandstone.css";
$css["tan"]= "tan.css";
$css["warmbeige"]= "warmbeige.css";*/


		
	foreach ($css as $index => $value)
			{
			
			echo "<option value=\"$value\"";
			if ($stylesheet == $value)
				{
				echo " selected";
				}
			echo ">$value </option>";
			
			
			}





echo "</select><br>
$phrase[550]
</td></tr>



<tr><td class=\"label\"><strong>$phrase[935]</strong>



</td><td valign=\"top\">
<div style=\"float:left;padding-right:2em\"><input type=\"radio\" id=\"navlocl\" name=\"Preferences[navlocation]\" value=\"l\"";
if ($navlocation == "l") {echo " checked";}
echo "><label for=\"navlocl\"> $phrase[936]<br><img src=\"../images/navl.png\" alt=\"$phrase[936]\"></label></div>
<div style=\"float:left;padding-right:2em;\"><input type=\"radio\" id=\"navloct\" name=\"Preferences[navlocation]\" value=\"t\"";
if ($navlocation == "t") {echo " checked";}
echo "><label for=\"navloct\"> $phrase[937]<br><img src=\"../images/navt.png\" alt=\"$phrase[937]\"></label></div>

<div style=\"float:left;clear:left;padding-right:2em\"><input type=\"radio\" id=\"navlocr\" name=\"Preferences[navlocation]\" value=\"r\"";
if ($navlocation == "r") {echo " checked";}
echo "><label for=\"navlocr\"> $phrase[938]<br><img src=\"../images/navr.png\" alt=\"$phrase[938]\"></label></div>

<div style=\"float:left;padding-right:2em\"><input type=\"radio\" id=\"navlocn\" name=\"Preferences[navlocation]\" value=\"n\"";
if ($navlocation == "n") {echo " checked";}
echo "><label for=\"navlocn\"> $phrase[495]<br><img src=\"../images/navn.png\" alt=\"$phrase[495]\"></label></div>

</td></tr>







<tr><td class=\"label\"><strong>Enable main menu flyouts</strong>



</td><td valign=\"top\">
<input type=\"radio\" name=\"Preferences[flyoutmenu]\" id=\"fmy\" size=\"2\" maxlength=\"2\" value=\"yes\"
";

   if ( $flyoutmenu == "yes")
         {
      echo " checked";
     }


 echo "> <label for=\"fmy\">$phrase[12]</label> 

<input type=\"radio\" name=\"Preferences[flyoutmenu]\" id=\"fmn\" size=\"2\" maxlength=\"2\" value=\"no\"";

   if ( $flyoutmenu == "no")
         {
      echo " checked";
     }
echo "> <label for=\"fmn\">$phrase[13]</label> 

</td></tr>



<tr><td class=\"label\"><strong>Top banner</strong>



</td><td valign=\"top\">
<input type=\"radio\" name=\"Preferences[custombanner]\" id=\"cby\"  value=\"1\"";

   if ( $custombanner == 1)
         {
      echo " checked";
     }


 echo ">  <label for=\"cby\">Custom banner (custombanner.php)</label><br><br>
 


<input type=\"radio\" name=\"Preferences[custombanner]\" id=\"cbn\"  value=\"0\"";

   if ( $custombanner == "0")
         {
      echo " checked";
     }
echo "> <label for=\"cbn\">Default banner</label> 

 <div style=\"margin-top:1em;border:solid 1px black;margin:1em;padding:1em\" id=\"bannerdiv\">

<span style=\"padding-right:1em\"><strong>$phrase[539]</strong></span>

<input type=\"radio\" name=\"Preferences[displayemaillink]\" id=\"dey\" size=\"2\" maxlength=\"2\" value=\"yes\"
";


   if ( $displayemaillink == "yes")
         {
      echo "  checked";
     }


 echo "> <label for=\"dey\">$phrase[12] </label>

<input type=\"radio\" name=\"Preferences[displayemaillink]\" id=\"den\" size=\"2\" maxlength=\"2\" value=\"no\"";

   if ( $displayemaillink == "no")
         {
      echo " checked";
     }
echo "> <label for=\"den\">$phrase[13]</label>


<br><br>
<span class=\"label\"><b>$phrase[540]</b></span><br>
<INPUT type=\"text\" name=\"Preferences[emaillink]\" value=\"$emaillink\" size=\"70\" maxlength=\"100\">
<br><br>




<span  style=\"padding-right:1em\" ><strong>$phrase[551]</strong> images/logo.png



</span>
<input type=\"radio\" name=\"Preferences[logo]\" id=\"logoy\" value=\"yes\"";

   if ( $logo == "yes")
         {
      echo " checked";
     }


 echo "> <label for=\"logoy\">$phrase[12] </label>

<input type=\"radio\" name=\"Preferences[logo]\" id=\"logon\" value=\"no\"";

   if ( $logo == "no")
         {
      echo " checked";
     }
echo "> <label for=\"logon\">$phrase[13] </label>

</div>

</td></tr>


<tr><td class=\"label\"><strong>Search banner</strong>



</td><td valign=\"top\">
<input type=\"radio\" name=\"Preferences[searchbanner]\" id=\"sy\" value=\"1\"";

   if ( $searchbanner == 1)
         {
      echo " checked";
     }


 echo "> <label for=\"sy\">$phrase[12]</label>

<input type=\"radio\" name=\"Preferences[searchbanner]\" id=\"sn\" value=\"0\"";

   if ( $searchbanner == "0")
         {
      echo " checked";
     }
echo ">  <label for=\"sn\">$phrase[13]</label>

</td></tr>


<tr><td class=\"label\"><strong>Content management banner visible to guest user.</strong>



</td><td valign=\"top\">
<input type=\"radio\" name=\"Preferences[cmbanner]\" id=\"cmy\" value=\"1\"";

   if ( $cmbanner == 1)
         {
      echo " checked";
     }


 echo ">  <label for=\"cmy\">$phrase[12]</label>

<input type=\"radio\" name=\"Preferences[cmbanner]\" id=\"cmn\" value=\"0\"";

   if ( $cmbanner == "0")
         {
      echo " checked";
     }
echo "> <label for=\"cmn\">$phrase[13]</label>

</td></tr>

<tr><td class=\"label\"><strong>$phrase[695]</strong><br>

</td><td><INPUT type=\"text\" name=\"Preferences[logout]\" value=\"$logout\" size=\"60\" maxlength=\"200\"><br>
Leave blank to return to login page.

<?php echo $phrase[694]?>


</td></tr>


</table></fieldset><fieldset style=\"margin-top:1em\"><legend>Uploads</legend>

<table style=\"margin:1em auto;width:100%\">


<tr><td class=\"label\" style=\"width:40%\"><strong>$phrase[860]</strong>



</td><td valign=\"top\">
<select name=\"Preferences[imagesize]\">";
$size = 100;

while ($size < 1100)
{
	
	echo "<option value=\"$size\" ";
	if ($size == $imagesize) {echo " selected";}
	echo ">$size</option>";
	$size = $size + 100;
}

echo "</select> px

</td></tr>



<tr><td class=\"label\"><strong>$phrase[541]</strong></td><td><input type=\"text\" name=\"Preferences[maxfilesize]\" size=\"2\" maxlength=\"2\" value=\"$maxfilesize\"> MG<br>
</td></tr>
<tr><td class=\"label\">
<strong>$phrase[542]</strong></td><td><input type=\"text\" name=\"Preferences[maxpicsize]\" size=\"3\" maxlength=\"4\" value=\"$maxpicsize\"> K<br>

<br>
$phrase[534]<br> post_max_size, upload_max_filesize, file_uploads  <a href=\"phpinfo.php\">$phrase[535]</a> 
</td></tr>
<tr><td class=\"label\">
<strong>$phrase[1088] $storage</strong></td><td><select name=\"Preferences[storage]\">
<option value=\"file\">$phrase[1090]</option>
<option value=\"database\" ";
if ($storage == "database") {echo "selected";}
echo ">$phrase[1089]</option>

size=\"30\" maxlength=\"100\" value=\"$storage\"></td></tr>
<tr><td class=\"label\">
<strong>$phrase[1088] $phrase[652]</strong></td><td><input type=\"text\" name=\"Preferences[docdir]\" size=\"30\" maxlength=\"100\" value=\"$docdir\"></td></tr>
</table></fieldset>







<fieldset style=\"margin-top:1em\"><legend>Security</legend>
<table style=\"margin:1em auto;width:100%\">

<tr><td class=\"label\" style=\"width:40%\">
<strong>$phrase[653]</strong></td><td><input type=\"text\" name=\"Preferences[maxpasswordage]\" size=\"3\" maxlength=\"3\" value=\"$maxpasswordage\"> days</td></tr>

<tr><td class=\"label\">
<strong>$phrase[673]</strong></td><td><input type=\"text\" name=\"Preferences[passwordlength]\" size=\"3\" maxlength=\"2\" value=\"$passwordlength\"></td></tr>


<tr><td class=\"label\"><strong>$phrase[671]</strong>



</td><td valign=\"top\">
<input type=\"radio\" name=\"Preferences[complex]\"  value=\"1\"";

   if ( $complex == "1")
         {
      echo " checked";
     }


 echo "> $phrase[12] 

<input type=\"radio\" name=\"Preferences[complex]\"  value=\"0\"";

   if ( $complex == "0")
         {
      echo " checked";
     }
echo "> $phrase[13] 

</td></tr>


<tr><td class=\"label\"><strong>$phrase[654]</strong>



</td><td valign=\"top\">
<input type=\"radio\" name=\"Preferences[allowguest]\"  value=\"1\"";

   if ( $allowguest == "1")
         {
      echo " checked";
     }


 echo "> $phrase[12] 

<input type=\"radio\" name=\"Preferences[allowguest]\"  value=\"0\"";

   if ( $allowguest == "0")
         {
      echo " checked";
     }
echo "> $phrase[13] 

</td></tr>






</table></fieldset>


<fieldset style=\"margin-top:1em\"><legend>Other</legend>
<table style=\"margin:1em auto\">
<tr><td class=\"label\"><strong>$phrase[537]</strong><br>

</td><td><INPUT type=\"text\" name=\"Preferences[orgname]\" value=\"$orgname\" size=\"50\" maxlength=\"50\">


</td></tr>
     
<tr><td class=\"label\"><strong>Enable visit statistics</strong><br>

</td><td><select name=\"Preferences[loghits]\">
 <option value=\"0\">$phrase[13]</option>
 <option value=\"1\"";
if ($loghits == 1) {echo " selected";}

echo ">$phrase[12]</option>
</td></tr>
<tr><td class=\"label\"><strong>Log file path</strong><br>

</td><td><INPUT type=\"text\" name=\"Preferences[hitslog]\" value=\"$hitslog\" size=\"50\" maxlength=\"50\">


</td></tr>  
     

     
     
     
     
     
<tr><td class=\"label\"><strong>$phrase[991]</strong><br>

</td><td><INPUT type=\"text\" name=\"Preferences[stats_ignore]\" value=\"$stats_ignore\" size=\"50\" maxlength=\"50\">


</td></tr>



</table>


</fieldset>
<p>
<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\"></p>

			
			
		
			</form>
			
			
			
</div>

";


include ("../includes/footer.php");

?>

