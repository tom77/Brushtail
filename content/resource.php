<?php



include ("../includes/initiliaze_page.php");

$VAL = new Validate($DB,$PREFERENCES["key"],$PREFERENCES["guest"]);

include ("../includes/htmlheader.php");
include ("../includes/pageheader.php");







if (isset($_REQUEST["m"]))
	{	
	
	if (isinteger($_REQUEST["m"]))
		{	
		$m = $_REQUEST["m"];	
		$access->check($m);
	
	if ($access->thispage < 3)
			{
		
			$ERROR  =  "<div style=\"text-align:center;font-size:200%;margin-top:3em\">$phrase[1]</div>";
	
			}
	elseif ($access->iprestricted == "yes")
			{
			$ERROR  =  $phrase[0];
			}
	
		}
	else 
		{
		$ERROR  =  "$phrase[72]";
	
		}		
	}
else {
	$ERROR  =  "no module chosen";
}	

$integers[] = "resource";
$integers[] = "field";
$integers[] = "fee_applicable";
$integers[] = "book_multiple_days";
$integers[] = "print";
$integers[] = "recur";
$integers[] = "checkout";
$integers[] = "notify";
$integers[] = "startday";
$integers[] = "endday";
$integers[] = "startmonth";
$integers[] = "endmonth";
$integers[] = "startyear";
$integers[] = "endyear";
$integers[] = "closureid";
$integers[] = "openinghours";


foreach($integers as $key => $value)
{
if (isset($_REQUEST[$value])) 
	{
	if (!(isinteger($_REQUEST[$value]))) 
	{$ERROR  = "not an integer" . $_REQUEST[$value] . $value;}
	else {$$value = $_REQUEST[$value];}
	}
}

if (!isset($ERROR))
{	
$sql = "select name from modules where m = \"$m\"";
$DB->query($sql,"resource.php");
			$row = $DB->get();
$modname = formattext($row["name"]);
	
  //days of week array
$weekdays[0] = $phrase[425];
$weekdays[1] = $phrase[419];
$weekdays[2] = $phrase[420];
$weekdays[3] = $phrase[421];
$weekdays[4] = $phrase[422];
$weekdays[5] = $phrase[423];
$weekdays[6] = $phrase[424];

$monthsvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12');
$daysvalue = array('01','02','03','04','05','06', '07', '08','09','10','11', '12', '13', '14', '15', '16', '17',
		'18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31');


include("../includes/leftsidebar.php");
		
		
		

	  	echo "<div id=\"content\"><div> <h1 class=\"red\">$modname</h1>";
	

if (isset($_POST["update"]) && $_POST["update"] == "updatefield")
	{										
	
	$menu = $DB->escape($_POST["menu"]);
	$label = $DB->escape($_POST["label"]);
	$comment = $DB->escape($_POST["comment"]);
	//$compulsory = $DB->escape($_POST["compulsory"]);
	$output = $DB->escape($_POST["output"]);
	
	
	
	if (!isset($ERROR))
	{
	
	$sql = "update resource_custom_fields set label = '$label',menu = '$menu', comment = '$comment', output = '$output'  where fieldno=\"$field\"";


	$DB->query($sql,"resource.php");
	} else {$ERROR = $phrase[72];} 
	}
	
	
		if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "forder")
	{
	
	//reorders form fields
	$fieldreorder = explode(",",$_REQUEST["fieldreorder"]);
	

	
	foreach ($fieldreorder as $index => $value)
		{
		if (isinteger($value))
		{
		$sql = "update resource_fields set ranking = '$index' WHERE field = '$value' and m = '$m' and resource = '$resource'";	
              //  echo $sql;
		$DB->query($sql,"resource.php");
		}
		else {$ERROR = $phrase[72];}
		}
		
	
	
	
	}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deletefield")
	{
	
	$sql = "select field_no from resource_custom_values group by field_no";
        $fields = array();
 $DB->query($sql,"resource.php");
	
	while ($row = $DB->get())
        {
	$fields[] = $row["field_no"];
        }
        //print_r($fields);
        
         $sql = "delete from resource_custom_fields where fieldno = '$field'";
      // echo $sql;
        
        if (!in_array($field,$fields))
        {
       
        $DB->query($sql,"resource.php");
        }
        }        
        
        
        
        
        
if (isset($_POST["update"]) && $_POST["update"] == "assignfield")
	{
	
	
        
        $sql = "insert into resource_fields values ('$field','$m','$resource','0')";
      //  echo $sql;
        $DB->query($sql,"resource.php");
        }
   	
if (isset($_POST["update"]) && $_POST["update"] == "addfield")
	{
	
	$fieldtype = $DB->escape($_POST["fieldtype"]);
	$label = $DB->escape($_POST["label"]);
	//$compulsory = $_POST["compulsory"];
	$comment = $DB->escape($_POST["comment"]);
	$output = $DB->escape($_POST["output"]);

	//print_r($_POST);
	
	if (isset($_POST["values"]))
	{
		$values = $DB->escape($_POST["values"]);
	}
	else {$values = "";}
		
		//if (isinteger($compulsory))
		//{	
		$sql = "insert into resource_custom_fields values (NULL,'$m','$fieldtype','$label','0','$values','$comment','$output') ";
		echo $sql;
		$DB->query($sql,"resource.php");
		
		
		//}
	
	}
   
   
	
 if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "update")

	{

	$resource_name = $DB->escape($_REQUEST["resource_name"]);
	$location = $DB->escape($_REQUEST["location"]);
	$description = $DB->escape($_REQUEST["description"]);
	$email = $DB->escape($_REQUEST["email"]);
	//print_r($_REQUEST);
	
	 if ($resource_name == "") {$resource_name = "_name field empty_";}
	
	
	$sql = "update resource set resource_name = \"$resource_name\", location = \"$location\", description = \"$description\", fee_applicable = \"$fee_applicable\", book_multiple_days = \"$book_multiple_days\", notify = \"$notify\", email = \"$email\" , print = \"$print\", recur = \"$recur\", checkout = \"$checkout\", openinghours = \"$openinghours\"  where resource_no = \"$resource\"";	

	

	$DB->query($sql,"resource.php");

	
	
	}

	
	
	
 if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "add")
	{
	//print_r($_REQUEST);
	//$resource_name = $DB->escape($_REQUEST["resource_name"]);
	///$location = $DB->escape($_REQUEST["location"]);
	//$description = $DB->escape($_REQUEST["description"]);
	//$email = $DB->escape($_REQUEST["email"]);
	
// if ($resource_name == "") {$resource_name = "_name field empty_";}
	
	
	$sql = "insert into resource values (NULL,'$m','','','','0','0','0','0','0','0','0','0')";	
	
	$DB->query($sql,"resource.php");
	
	 $resource = $DB->last_insert();

	}

if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "removefield")
	{
	
		
	 $sql = "delete from resource_fields where field = '$field' and resource = '$resource'"; 
        // echo "sql $sql";
		 	$DB->query($sql,"resource.php");		
		
	}	
	
 if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "delete")
	{
	
		//$sql = "delete from resource_custom_data,resourcebooking where resource_no = \"$resource\" and resourcebooking.bookingno = resource_custom_data."; 
		 	//$DB->query($sql,"resource.php");
		
	 $sql = "delete from resourcebooking where resource = \"$resource\" "; 
		 	$DB->query($sql,"resource.php");		
		
		
	 $sql = "delete from resource_custom_fields where resource = \"$resource\" "; 
		 	$DB->query($sql,"resource.php");
		 	
		
	$sql = "delete from resource where resource_no = \"$resource\"";	
	$DB->query($sql,"resource.php"); 
	
	$DB->tidy("resource");
	
	
	$DB->tidy("resource_custom_fields");

	$DB->tidy("resourcebooking");

	}
	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "addclosure")


	{
	$starttime =  date("Y-m-d", mktime(0, 0, 0, $startmonth, $startday, $startyear));
	$endtime =  date("Y-m-d", mktime(0, 0, 0, $endmonth, $endday, $endyear));
	
   if ($starttime > $endtime)
	  	{
		$WARNING = "$phrase[482]";
		}
	else {
	 $reason = $DB->escape($_REQUEST["reason"]);
	$sql = "INSERT INTO resource_closures VALUES(NULL,'$resource','$reason','$starttime','$endtime')";
       $DB->query($sql,"resource.php");
	}
	}
	
	
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "deleteclosure")
    {
	
    $sql = "delete from resource_closures where id = \"$closureid\"";
    $DB->query($sql,"resource.php");
	
    }

 if (isset($_REQUEST["update"]) && $_REQUEST["update"] == "editclosure")
	{
	$starttime =  date("Y-m-d", mktime(0, 0, 0, $startmonth, $startday, $startyear));
	$endtime =  date("Y-m-d", mktime(0, 0, 0, $endmonth, $endday, $endyear));
		
    if ($starttime > $endtime)
	  	{
		$WARNING= "$phrase[482]";
		}
	else {
	$reason = $DB->escape($_REQUEST["reason"]);
	$sql = "update resource_closures set resource_no = '$resource', reason = '$reason', date_blocked = '$starttime', date_finish ='$endtime' where id = '$closureid'";
       $DB->query($sql,"resource.php");
	}
	}
 
        
        
        
 if(isset($_REQUEST["update"]) && $_REQUEST["update"] == "hours")
	{
		
	$opening = $_REQUEST["opening"];
	$closing = $_REQUEST["closing"];
	$branchopen = $_REQUEST["branchopen"];
	//	print_r($opening);
	//print_r($branchopen);
	
	//include("../classes/PcBookings.php");
	//$pcbookings = new PcBookings($DB);
	
	
    $sql = "delete from resource_openinghours where resource_no = '$resource'";
    
    $DB->query($sql,"resource.php");
	
     $counter = 1;
    foreach ($opening  as $index => $openinghour)
            {
             //closingtime cannot be earlier than opening
            if ($openinghour >  $closing[$index])
               {
               $closing[$index] = $openinghour;
           		}
				
			
           	
		   $openinghour = $DB->escape($openinghour);
		   $closing[$index] = $DB->escape($closing[$index]);
		  $branchopen[$index] = $DB->escape($branchopen[$index]);
		
		
           	
             $sql = "INSERT INTO resource_openinghours VALUES('$resource','$openinghour','$closing[$index]','$index','$branchopen[$index]')";
            // echo "<br>$sql <br>";
             
             $DB->query($sql,"resource.php");
	
              $counter++;
        }

	}        
        
        
        
        
        
        
        
        
        
        
 if (isset($_REQUEST["event"]) && $_REQUEST["event"] == "delete")
       
 	
	{
	echo "<br><b>$phrase[14]</b><br><br>$_REQUEST[name]  <br><br>
	<a href=\"resource.php?m=$m&amp;update=delete&amp;resource=$resource\">$phrase[12]</a> | <a href=\"resource.php?m=$m\">$phrase[13]</a>";
	}	


 elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "removefield")
	{ 
	 	echo "<br><b>$phrase[1105]</b><br><br>$_REQUEST[name]  <br><br>
	 	
	 	
	 	<br><br>
	<a href=\"resource.php?m=$m&amp;update=removefield&amp;resource=$resource&amp;field=$field&amp;event=edit\">$phrase[12]</a> | <a href=\"resource.php?m=$m&amp;resource=$resource&amp;event=edit\">$phrase[13]</a>";
	
		
	}
	
	
elseif (isset($_GET["event"]) && $_GET["event"] == "assignfield")
	{
    
    
    //get list of assigned fields
     $sql = "select field from resource_fields where resource = '$resource'";
 
$fields = array();
 $DB->query($sql,"resource.php");
	
	while ($row = $DB->get())
        {
	$fields[] = $row["field"];
        }
   // print_r($fields);
    
    
    $sql = "select * from resource where resource_no = \"$resource\"";							
	$DB->query($sql,"resource.php");
	$row = $DB->get();
	
	$resource_name = formattext($row["resource_name"]);
    
    
    	echo "<a href=\"resource.php?m=$m\">$phrase[642]</a><br><br>
            <h4>$resource_name</h4><form action=\"resource.php\" method=\"post\" >
	<fieldset><legend>$phrase[1104]</legend>
<select name=\"field\">";
        
 $sql = "select distinct fieldno, label from resource_custom_fields where m = '$m' ";
 

 $DB->query($sql,"resource.php");
	
	while ($row = $DB->get())
        {
	$field = $row["fieldno"];
        $label = $row["label"];
       if (!in_array($field,$fields)) {echo "<option value=\"$field\">$label</option>";}
        }
        
echo "</select>
<input type=\"hidden\" name=\"m\"  value=\"$m\">
<input type=\"hidden\" name=\"update\"  value=\"assignfield\">   
<input type=\"hidden\" name=\"event\"  value=\"edit\"> 
<input type=\"hidden\" name=\"resource\"  value=\"$resource\"> 
<input type=\"submit\" name=\"submit\"  value=\"$phrase[1104]\"> 
	</form>";
    
    
    
}	
	
	
elseif (isset($_GET["event"]) && $_GET["event"] == "addfield")
	{
print <<<EOF

	
	<script type="text/javascript">
	
function menu(){
document.getElementById('labelspan').innerHTML = '<b>Menu values</b>';
document.getElementById('menuvalues').innerHTML = '<textarea name="values" rows="5" cols="60"></textarea><br>Separate each value with a line break.';


}

function radio(){
document.getElementById('labelspan').innerHTML = '<b>Menu values</b>';
document.getElementById('menuvalues').innerHTML = '<textarea name="values" rows="5" cols="60"></textarea><br>Separate each value with a line break.';

}

function text(){
document.getElementById('labelspan').innerHTML = '';
document.getElementById('menuvalues').innerHTML = '<input type="hidden" name="values" value=""';

}

function area(){
document.getElementById('labelspan').innerHTML = '';
document.getElementById('menuvalues').innerHTML = '<input type="hidden" name="values" value=""';

}
</script>
<div >
    <a href="resource.php?m=$m">$phrase[642]</a> |
<a href="resource.php?m=$m&amp;event=customfields">$phrase[908]</a> <br><br>
    
	<form action="resource.php" method="post" >
	<fieldset><legend>$phrase[100]</legend><br>
<br>

	<table cellspacing="7" style="text-align:left">
	
	<tr><td align="right"><strong>$phrase[109]</strong></td><td align="left">
<input type="text" name="label" size="40" maxlength="200"></td></tr>

 
 
 

<tr><td align="right"><strong>$phrase[111]</strong></td><td align="left">

<input type="radio" name="fieldtype" value="t" checked onClick="text()"> $phrase[105] <br>
<input type="radio" name="fieldtype" value="a" onClick="area()"> $phrase[106] <br>
<input type="radio" name="fieldtype" value="m" onClick="menu()"> $phrase[107]</td></tr>


 <tr><td align="right"><strong>$phrase[250]</strong></td><td>
<select name="output">
<option value="1">$phrase[12]</option>
<option value="0">$phrase[13]</option>    
</select>     

</td></tr>

<tr><td><span id="labelspan"></span></td><td><span id="menuvalues"><input type="hidden" name="values" value=""></span></td></tr>
 <tr><td align="right"><strong>Field comment</strong></td><td><textarea name="comment" cols="60"></textarea></td></tr>
<tr><td></td><td align="left">

		
	<input type="hidden" name="m" value="$m">
	<input type="hidden" name="update" value="addfield">
	<input type="hidden" name="event" value="customfields">

<input type="submit" name="submit" value="$phrase[100]"></td></tr></table></fieldset>
	</form></div>
	
EOF;
	
	}
        
        
         elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "customfields")
	{ 
            
             
                $sql = "select field_no from resource_custom_values group by field_no";
 
$fields = array();
 $DB->query($sql,"resource.php");
	
	while ($row = $DB->get())
        {
	$fields[] = $row["field_no"];
        }
             
             
             
             
             echo "<h4>$phrase[908]</h4><a href=\"resource.php?m=$m\">$phrase[642]</a> |
<a href=\"resource.php?m=$m&amp;event=addfield\">$phrase[557]</a>               

<br> <br><table class=\"colourtable\">";
         $sql = "select * from resource_custom_fields where m = '$m' order by label";
	$DB->query($sql,"resource.php");
	
	while ($row = $DB->get())
        {
	$label = $row["label"];
	$field = $row["fieldno"];  
        echo "<tr><td>$label</td><td>$field</td>
            <td><a href=\"resource.php?m=$m&amp;event=editfield&amp;field=$field\"><img src=\"../images/pencil.png\"  title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td>
            <td>";
        if (!in_array($field,$fields)){
        echo "<a href=\"resource.php?m=$m&amp;event=customfields&amp;update=deletefield&amp;field=$field\"><img src=\"../images/cross.png\"  title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a>";
        } echo "</td>
                ";
        }
        echo "</table>";
             
         }
 elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "add")
	{ 
print <<<EOF
<div >
	<FORM method="POST" action="resource.php" ><fieldset ><legend>$phrase[176]</legend>
	
	<table  cellpadding="3" >
	
	<tr><td align="right"><b>$phrase[233]</b></td><td align="left">
	<input type="text" name="resource_name" maxlength="100" size="60"></td></tr>
	<tr><td align="right"><b>$phrase[204]</b></td><td align="left"><input type="text" name="location" maxlength="100" size="60"></td></tr>
	<tr><td valign="top" align="right"><b>$phrase[234]</b></td><td align="left"><textarea name="description" rows="10" cols="60"></textarea></td></tr>

<tr><td align="right"><b>$phrase[235]</b></td><td align="left"><select name="display_contact">
<option value="0">$phrase[13]</option><option value="1">$phrase[12]</option></select></td></tr>	
<tr><td align="right"><b>$phrase[134]</b></td><td align="left"><select name="display_address">
<option value="0">$phrase[13]</option><option value="1">$phrase[12]</option></select></td></tr>		
<tr><td align="right"><b>$phrase[132]</b></td><td align="left"><select name="display_telephone">
<option value="0">$phrase[13]</option><option value="1">$phrase[12]</option></select></td></tr>			
<tr><td align="right"><b>$phrase[236]</b></td><td align="left"><select name="display_notes">
<option value="0">$phrase[13]</option><option value="1">$phrase[12]</option></select></td></tr>		
<tr><td align="right"><b>$phrase[251]</b></td><td align="left"><select name="fee_applicable">
<option value="0">$phrase[13]</option><option value="1">$phrase[12]</option></select></td></tr>		
<tr><td align="right"><b>$phrase[252]</b></td><td align="left"><select name="book_multiple_days">
EOF;
    
   $counter = 0;
                
                while ($counter < 3)
                {
                echo "<option value=\"$counter\">";
                if ($counter == 0)
			{
			echo $phrase[13];
			}
		elseif	($counter == 1)
			{
			echo $phrase[12];
			}
                        else
                        {
                         echo $phrase[1099];   
                        }
                echo "</option>";    
                $counter++;    
                }
                
   
   
   
print <<<EOF

   </select></td></tr>		
	
	
		<tr><td align="right"><b>$phrase[250]</b></td><td align="left"><select name="print">
		<option value="0">$phrase[254]</option>
			<option value="1">$phrase[256]</option> 
			<option value="2">$phrase[255]</option>
			<option value="3">$phrase[257]</option>
		
		</select></td></tr>
		
<tr><td align="right"><b>$phrase[258]</b></td><td align="left"><select name="notify">
<option value="0">$phrase[13]</option><option value="1">$phrase[12]</option></select></td></tr>			
		
	
	<tr><td align="right"><b>$phrase[259]</b></td><td align="left"><input type="text" name="email" size="50" maxlength="100"></td></tr>
	
<tr><td align="right"><b>$phrase[775]</b></td><td align="left"><select name="checkout">
<option value="0">$phrase[13]</option><option value="1">$phrase[12]</option></select></td></tr>		
	

	
	
	<tr><td  align="right" valign="top"><b>$phrase[260]</b></td><td align="left"><select name="recur">
EOF;
$counter = 1;
while ($counter < 53)
{
echo "<option value=\"$counter\">$counter</option>";
$counter++;
}

echo "</select> bookings.<br>
$phrase[261]

</td></tr>
	
	
	<tr><td ></td><td align=\"left\">
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"add\">
	<input type=\"submit\" name=\"add\" value=\"$phrase[176]\"></td></tr>
	
	</table></fieldset></form></div>";


		
	}
	
	
	elseif (isset($_GET["event"]) && $_GET["event"] == "editfield")
	{
		
$sql = "select * from resource_custom_fields where fieldno = '$field'";
	$DB->query($sql,"resource.php");
	
	$row = $DB->get();
	$label = $row["label"];
	$compulsory = $row["compulsory"];
	
	$menu = $row["menu"];
	$comment = $row["comment"];
	$output = $row["output"];
	$type = $row["type"];
	
	
echo "

<div >
	<form action=\"$_SERVER[PHP_SELF]\" method=\"post\" >
	<fieldset><legend>$phrase[575]</legend>
	
<table cellspacing=\"7\" >
	
	<tr><td align=\"right\"><strong>$phrase[109]</strong></td><td>
<input type=\"text\" name=\"label\" size=\"80\" maxlength=\"200\" value=\"$label\"></td></tr>

 <tr><td align=\"right\"><strong>$phrase[576]</strong></td><td><textarea name=\"comment\" cols=\"60\" rows=\"4\">$comment</textarea></td></tr>
     
 <tr><td align=\"right\"><strong>$phrase[250]</strong></td><td>
<select name=\"output\">
<option value=\"1\""; if ( $output == 1) {echo " selected";} echo ">$phrase[12]</option>
<option value=\"0\""; if ( $output == 0) {echo " selected";} echo ">$phrase[13]</option>    
</select>     

</td></tr>


";
if ($type == "m" || $type == "r")
{ echo "<tr><td align=\"right\"><strong>$phrase[577]</strong></td><td><textarea name=\"menu\" cols=\"60\" rows=\"8\">$menu</textarea></td></tr>";}

echo "


<tr><td></td><td>
	<input type=\"hidden\" name=\"field\" value=\"$field\">
	<input type=\"hidden\" name=\"event\" value=\"customfields\">
	
	<input type=\"hidden\" name=\"m\" value=\"$m\">
	<input type=\"hidden\" name=\"update\" value=\"updatefield\">";
if ($type == "t" || $type == "a")  {echo "<input type=\"hidden\" name=\"menu\" value=\"\">";}

echo "<input type=\"submit\" name=\"submit\" value=\"$phrase[28]\"></td></tr></table>
	</fieldset></form></div><br>";
	}


 elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "edit")
	{
	
	$sql = "select * from resource where resource_no = \"$resource\"";							
	$DB->query($sql,"resource.php");
	$row = $DB->get();
	
	$resource_name = formattext($row["resource_name"]);
	$description = $row["description"];
	$location = formattext($row["location"]);
	$fee_applicable = $row["fee_applicable"];
	$book_multiple_days = $row["book_multiple_days"];
	$notify = $row["notify"];
	$email = $row["email"];
	$print = $row["print"];
	$recur = $row["recur"];
	$checkout = $row["checkout"];
        $openinghours = $row["openinghours"];
	
	$sql = "select distinct * from resource_custom_fields, resource_fields 
            where resource_fields.m = '$m' and resource_fields.resource = '$resource' and resource_custom_fields.fieldno = resource_fields.field order by resource_fields.ranking";
	//ech	
	$counter = 0;	
						
	$DB->query($sql,"resource.php");
	while ($row = $DB->get())
	{
	$field = $row["fieldno"]; 
	$custom_labels[$field] = $row["label"];
	$custom_types[$field] = $row["type"];
	$custom_menu[$field] = $row["menu"];
	//$custom_ranking[$field] = $row["ranking"];
	$custom_comments[$field] = $row["comment"];
	$custom_ranking[$counter] = $field;
	$counter++;
		
	}
$total = $counter;
		
	 $optionno =  "<option value=\"0\">$phrase[13]</option><option value=\"1\">$phrase[12]</option>";
			 $optionyes = "<option value=\"1\">$phrase[12]</option><option value=\"0\">$phrase[13]</option>";
			 
			 	



echo "
<a href=\"resource.php?m=$m\">$phrase[642]</a> | <a href=\"resource.php?m=$m&event=closures&resource=$resource\">$phrase[478]</a> 
         | <a href=\"resource.php?m=$m&event=customfields\">$phrase[908]</a> "; 
if ($openinghours == 1)
{
echo "| <a href=\"resource.php?m=$m&event=hours&resource=$resource\">$phrase[489]</a> ";
}
echo "<h2>$resource_name</h2>

<FORM method=\"POST\" action=\"resource.php\" ><fieldset ><legend>$phrase[1107]</legend>

<a href=\"resource.php?m=$m&amp;event=assignfield&amp;resource=$resource\"><img src=\"../images/add.png\" title=\"$phrase[1104]\" alt=\"$phrase[1104]\"></a>
<table>
";







$counter = 0;


if (isset($custom_labels))
{
foreach ($custom_labels as $key => $value)
{
	$linkname = urlencode($value);
	echo "
	<tr>
<td><label>$value</label></td><td>$key</td><td><span>";
	if ($custom_types[$key] == "t") {echo "<input type=\"text\" disabled size=\"10\">";}
	if ($custom_types[$key] == "a") {echo "<textarea disabled cols=\"10\" rows=\"2\"></textarea>";}
	if ($custom_types[$key] == "m") {echo "<select disabled><option></option></select>";}
	echo "<br>$custom_comments[$key]</span></td><td> <a href=\"resource.php?m=$m&amp;event=editfield&amp;field=$key&amp;resource=$resource\">
	<img src=\"../images/pencil.png\"  title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td><td> <a href=\"resource.php?m=$m&amp;event=removefield&amp;field=$key&amp;resource=$resource&amp;name=$linkname\"><img src=\"../images/cross.png\"  title=\"$phrase[1105]\"  alt=\"$phrase[1105]\"></a> 
	</td><td>";
	
	
	
			if ( $total > 0 && $counter > 0 ) {
			
				foreach ($custom_ranking as $index2 => $value)
									{
									//echo "index is $index $value pagecount is $pagecount";
									if ($index2 == $counter)
										{
										
										$up = $custom_ranking;
										$up[$counter] = $custom_ranking[$counter - 1];
										$up[$counter - 1] = $custom_ranking[$counter];
										
										}
									}
			
			
			
			
			$fieldup = implode(",",$up);
			echo "<a href=\"resource.php?m=$m&amp;resource=$resource&amp;update=forder&amp;event=edit&amp;fieldreorder=$fieldup\"><img src=\"../images/up.png\" alt=\"$phrase[562]\"  title=\"$phrase[562]\"></a>";
							
			}
			
			
			echo "</td><td>";
			
				if ($total > 0 && $counter + 1 < $total) { 
			
			
								foreach ($custom_ranking as $index2 => $value)
									{
									//echo 
									if ($index2 == $counter)
										{
										$down = $custom_ranking;
										
										$down[$counter] = $custom_ranking[$counter + 1];
										$down[$counter + 1] = $custom_ranking[$counter];
									
										}
									}
								
								
							
							$fielddown = implode(",",$down);
							echo "<a href=\"resource.php?m=$m&amp;resource=$resource&amp;event=edit&amp;update=forder&amp;fieldreorder=$fielddown\"><img src=\"../images/down.png\" alt=\"$phrase[563]\"  title=\"$phrase[563]\"></a>";
						
			$counter++;	
			
			}
	
	echo "</td>
</tr>";
	
	

	
}
}


echo "
</table>
</fieldset>
<fieldset style=\"margin-top:1em;\"><legend>$phrase[565]</legend><table >



	<tr>
<td><label>$phrase[233]</label></td><td><span>	<input type=\"text\" name=\"resource_name\" value=\"$resource_name\" maxlength=\"100\" size=\"60\"></span></td>
</tr>
<tr>

<td><label>$phrase[204]</label></td><td><span>	<input type=\"text\" name=\"location\" value=\"$location\" maxlength=\"100\" size=\"60\"></span></td>
</tr>
<tr>

<td><label>$phrase[1083]</label></td><td><span>	<select name=\"openinghours\">";
		if ($openinghours == 0)
			{

			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></span></td>
</tr>

<tr>
<td><label>$phrase[234]</label></td><td><span>	<textarea name=\"description\" rows=\"10\" cols=\"60\">$description</textarea></span></td>
</tr>	

		<tr><td><label>$phrase[251]</label></td><td>
		<span>
		<select name=\"fee_applicable\">";
		if ($fee_applicable == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select>	</span></td></tr>
		
	<tr><td><label>$phrase[252]</label></td><td>
	<span>
	<select name=\"book_multiple_days\">";
                $counter = 0;
                
                while ($counter < 3)
                {
                echo "<option value=\"$counter\""; 
                if ($book_multiple_days == $counter) {echo " selected";}
                echo ">";
                if ($counter == 0)
			{
			echo $phrase[13];
			}
		elseif	($counter == 1)
			{
			echo $phrase[12];
			}
                        else
                        {
                         echo $phrase[1099];   
                        }
                echo "</option>";    
                $counter++;    
                }
                
		
		echo "</select></span></td></tr>
		
		<tr><td><label>$phrase[253]</label></td><td>
		<span>
		<select name=\"print\">
			<option value=\"0\"";
			if ($print == 0)	{echo " selected";}
			echo ">$phrase[254]</option>
			<option value=\"1\"";
			if ($print == 1)	{echo " selected";}
			echo ">$phrase[256]</option>
			<option value=\"2\"";
			if ($print == 2)	{echo " selected";}
			echo ">$phrase[255]</option>
			<option value=\"3\"";
			if ($print == 3)	{echo " selected";}
			echo ">$phrase[257]</option>";
		
		
		echo "</select></span></td></tr>
		
		<tr><td><label>$phrase[258]</label></td><td>
		<span>
		<select name=\"notify\">";
		if ($notify == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></span></td></tr>
		
		
		<tr><td><label>$phrase[259]</label></td><td> <span><input type=\"text\" name=\"email\" size=\"50\" maxlength=\"100\" value=\"$email\"></span></td></tr>
<tr><td><label>$phrase[775]</label></td><td>
<span>
<select name=\"checkout\">";
		if ($checkout == 0)
			{
			echo $optionno;
			}
		else	
			{
			echo $optionyes;
			}
		echo "</select></span></td></tr>
	
		<tr><td><label>$phrase[260]</label></td><td><span><select name=\"recur\">";
$counter = 1;
while ($counter < 53)
{
echo "<option value=\"$counter\"";
if ($counter == $recur) { echo " selected";}
echo ">$counter</option>";
$counter++;
}

echo "</select> bookings.</span>

</td></tr>";
print <<<EOF
<tr ><td>&nbsp;</td><td><span>
<input type="hidden" name="m" value="$m">
<input type="hidden" name="update" value="update">
	<input type="hidden" name="resource" value="$resource">
	<input type="submit" name="submit" value="$phrase[16]"></span></td>

	</tr></table></fieldset></form>	<br>
EOF;


	}
        
        
        
        elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "closures")
								 {
						
						
						 $year = date("Y");	 
								 $sql = "select * from resource where resource_no = '$resource'";
								$DB->query($sql,"resource.php");
								$row = $DB->get();
								 $name = $row["resource_name"];
                                                                 
								echo "
                                                                   <a href=\"resource.php?m=$m&event=edit&resource=$resource\">$name</a>
                                                                 <h2>$name $phrase[478]</h2>";
                                                                
                                                                
								
								echo "<div >
								<form action=\"resource.php\" method=\"post\" ><fieldset><legend>$phrase[176]</legend><br>
								<table  >
								
								
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[502]</b></td><td align=\"left\"><textarea name=\"reason\" cols=\"40\" rows=\"5\"></textarea></td></tr>
								<tr><td class=\"formlabels\"><b>$phrase[267]</b> </td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"startday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"startmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"startyear\">
								<option value=\"$year\">$year</option>";
									$nextyear = $year + 1;
									echo "<option value=\"$nextyear\">$nextyear</option></select>";
								
								echo "</td></tr>
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[268]</b> </td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"endday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"endmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"endyear\">
								<option value=\"$year\">$year</option>";
									$nextyear = $year + 1;
									echo "<option value=\"$nextyear\">$nextyear</option></select>";
								
								echo "<br><br>
								<input type=\"hidden\" name=\"m\" value=\"$m\">
                                                                        <input type=\"hidden\" name=\"resource\" value=\"$resource\">
							<input type=\"hidden\" name=\"update\" value=\"addclosure\">
								<input type=\"hidden\" name=\"event\" value=\"closures\">
							
								<input type=\"submit\" name=\"submit\" value=\"$phrase[176]\"></td>
								</table></fieldset></form>
								
								  </div>
	  <div >";
						   
						
								 
						if ($DB->type == "mysql")
		{
						  $sql = "select resource_closures.id as closureid, reason, UNIX_TIMESTAMP(date_blocked) as date_blocked ,UNIX_TIMESTAMP(date_finish) as date_finish from resource_closures where resource_no = '$resource'";
		}
			else
		{
						  $sql = "select resource_closures.id as closureid,  reason,strftime('%s',date_blocked) as date_blocked ,strftime('%s',date_finish) as date_finish from resource_closures where  resource_no = '$resource'";
		}
		
	
						 $DB->query($sql,"resource.php");
						 		
						$numrows = $DB->countrows();
						if ($numrows > 0)
						{
						
						echo "<table class=\"colourtable\" cellpadding=\"10\"   style=\"margin-top:2em\">
						<tr><td><b>$phrase[267]</b></td><td><b>$phrase[268]</b></td><td><b>$phrase[502] </b></td><td></td><td></td>";
						}
						while ($row = $DB->get())
						      {
						      $closureid = $row["closureid"];
							 
						    //  $branchname = formattext($row["branchname"]);
						      
						      $reason = $row["reason"];
							  $date_blocked = strftime("%x", $row["date_blocked"]);
							  $date_finish = strftime("%x", $row["date_finish"]);
							  echo "<tr><td>$date_blocked</td><td>$date_finish</td><td>$reason</td>
                                                          <td><a href=\"resource.php?m=$m&amp;event=editclosure&amp;closureid=$closureid&resource=$resource\">Edit</a></td><td><a href=\"resource.php?m=$m&amp;event=deleteclosure&amp;closureid=$closureid&resource=$resource\">Delete</a></td></tr>";
								}
								
								if ($numrows > 0)
								{
						
								echo "</table><br><br>";
								}
								
								echo "</div>";
								//add patron goes here 
							}


elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "editclosure")
			 {
		
			 $year = date("Y");	    
			 				if ($DB->type == "mysql")
		{
			 $sql = "select reason, DATE_FORMAT(date_blocked, '%d') as startday , DATE_FORMAT(date_blocked, '%m') as startmonth , DATE_FORMAT(date_blocked, '%Y') as startyear , DATE_FORMAT(date_finish, '%d') as endday  , DATE_FORMAT(date_finish, '%m') as endmonth , DATE_FORMAT(date_finish, '%Y') as endyear from resource_closures where  id = \"$closureid\"";
			 
		}
		
				 				else
                                                                {
			 $sql = "select reason, strftime('%d',date_blocked) as startday , strftime('%m',date_blocked) as startmonth , strftime('%Y',date_blocked) as startyear , strftime('%d',date_finish) as endday  , strftime('%m',date_finish) as endmonth , strftime('%Y',date_finish) as endyear from resource_closures where  id = '$closureid'";
			 
		}
		
	
		//echo $sql;
			 $DB->query($sql,"resource.php");
			$row = $DB->get();
						
		  
		
		      $reason = $row["reason"];
		      

			$startday = $row["startday"];
			$startmonth = $row["startmonth"];
			$startyear = $row["startyear"];
			
			$endday = $row["endday"];
			$endmonth = $row["endmonth"];
			$endyear = $row["endyear"];
			
			 $sql = "select * from resource where resource_no = '$resource'";
								$DB->query($sql,"resource.php");
								$row = $DB->get();
								 $name = $row["resource_name"];	
				
				
								echo "
                                                                 <a href=\"resource.php?m=$m&event=edit&resource=$resource\">$name</a>
                                                                 
                                                                 <h2>$name $phrase[478]</h2><br>
								<div ><form action=\"resource.php\" method=\"post\" >
								<fieldset><legend>$phrase[26]</legend><br>
								<table  >
								
							
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[502]</b></td><td align=\"left\"><textarea name=\"reason\" cols=\"40\" rows=\"5\">$reason</textarea></td></tr>
								<tr><td class=\"formlabels\"><b>$phrase[267]</b> </td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"startday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $startday)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"startmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $startmonth)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"startyear\"><option value=\"$startyear\">$startyear</option>";
								if ($year <> $startyear) { echo "<option value=\"$year\">$year</option>";}
								$nextyear = $year + 1;
								if ($nextyear <> $startyear) {echo "<option value=\"$nextyear\">$nextyear</option>"; }
								
							
								
								echo "</select></td></tr>
								<tr><td valign=\"top\" class=\"formlabels\"><b>$phrase[268] </b></td><td align=\"left\">";
								
								echo " &nbsp;&nbsp;$phrase[182] <select name=\"endday\">";
								foreach ($daysvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $endday)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								 $phrase[181] <select name=\"endmonth\">";
								foreach ($monthsvalue as $index => $value)
									{
									echo "<option value=\"$value\"";
									if ($value == $endmonth)
										{ echo " selected";}
									echo ">$value</option>";
									}
								
								echo "</select>
								$phrase[183] <select name=\"endyear\"><option value=\"$endyear\">$endyear</option>";
								if ($year <> $endyear) { echo "<option value=\"$year\">$year</option>";}
									$nextyear = $year + 1;
								if ($nextyear <> $endyear) {echo "<option value=\"$nextyear\">$nextyear</option>";}
									
								
								echo "</select><br><br>
								<input type=\"hidden\" name=\"m\" value=\"$m\">
								<input type=\"hidden\" name=\"closureid\" value=\"$closureid\">
                                                                <input type=\"hidden\" name=\"resource\" value=\"$resource\">
								<input type=\"hidden\" name=\"event\" value=\"closures\">
								<input type=\"hidden\" name=\"update\" value=\"editclosure\">
								<input type=\"submit\" name=\"updateclosure\" value=\"$phrase[28]\"></td>
								</table></fieldset></form></div>";
								//add patron goes here 
							
			 
			 }
        
        
    elseif  (isset($_REQUEST["event"]) && $_REQUEST["event"] == "deleteclosure")	

	{
	echo "<h2>$phrase[478]</h2><b>$phrase[14]</b><br><br>
	<a href=\"resource.php?m=$m&amp;update=deleteclosure&amp;closureid=$closureid&amp;event=closures&resource=$resource\">$phrase[12]</a> | <a href=\"resource.php?m=$m&amp;event=closures&resource=$resource\">$phrase[13]</a>";
		
	}    
        
        
        elseif (isset($_REQUEST["event"]) && $_REQUEST["event"] == "hours")
       {
     
       
       //array of time values to create drop down  time menu
       $optionlabel[]= " 6.00 am";  $optionvalue[] = "060000";
       $optionlabel[]= " 6.30 am";  $optionvalue[] = "063000";
       $optionlabel[]= " 7.00 am";  $optionvalue[] = "070000";
       $optionlabel[]= " 7.30 am";   $optionvalue[] = "073000";
       $optionlabel[]= " 8.00 am";  $optionvalue[] = "080000";
       $optionlabel[]= " 8.30 am"; $optionvalue[] = "083000";
       $optionlabel[]= " 9.00 am"; $optionvalue[] = "090000";
       $optionlabel[]= " 9.30 am";  $optionvalue[] = "093000";
       $optionlabel[]= "10.00 am";   $optionvalue[] = "100000";
       $optionlabel[]= "10.30 am"; $optionvalue[] = "103000";
       $optionlabel[]= "11.00 am";  $optionvalue[] = "110000";
       $optionlabel[]= "11.30 am";  $optionvalue[] = "113000";
       $optionlabel[]= "12.00 pm";  $optionvalue[] = "120000";
       $optionlabel[]= "12.30 pm";   $optionvalue[] = "123000";
       $optionlabel[]= " 1.00 pm";   $optionvalue[] = "130000";
       $optionlabel[]= " 1.30 pm";  $optionvalue[] = "133000";
       $optionlabel[]= " 2.00 pm"; $optionvalue[] = "140000";
       $optionlabel[]= " 2.30 pm";   $optionvalue[] = "143000";
       $optionlabel[]= " 3.00 pm"; $optionvalue[] = "150000";
       $optionlabel[]= " 3.30 pm"; $optionvalue[] = "153000";
       $optionlabel[]= " 4.00 pm";   $optionvalue[] = "160000";
       $optionlabel[]= " 4.30 pm";  $optionvalue[] = "163000";
       $optionlabel[]= " 5.00 pm";   $optionvalue[] = "170000";
       $optionlabel[]= " 5.30 pm"; $optionvalue[] = "173000";
       $optionlabel[]= " 6.00 pm";  $optionvalue[] = "180000";
       $optionlabel[]= " 6.30 pm";  $optionvalue[] = "183000";
       $optionlabel[]= " 7.00 pm"; $optionvalue[] = "190000";
       $optionlabel[]= " 7.30 pm"; $optionvalue[] = "193000";
       $optionlabel[]= " 8.00 pm";  $optionvalue[] = "200000";
       $optionlabel[]= " 8.30 pm";  $optionvalue[] = "203000";
       $optionlabel[]= " 9.00 pm";   $optionvalue[] = "210000";
       $optionlabel[]= " 9.30 pm";   $optionvalue[] = "213000";
       $optionlabel[]= " 10.00 pm";  $optionvalue[] = "220000";
       $optionlabel[]= " 10.30 pm";  $optionvalue[] = "223000";
       $optionlabel[]= " 11.00 pm";   $optionvalue[] = "230000";
       
       $option = "";
       foreach ($optionlabel as $index => $value)
               {
            $option .= "<option value=\"$optionvalue[$index]\">$value</option>";
            

           }


       
     /*
       $weekday[] = "Sunday";
       $weekday[] = "Monday";
       $weekday[] = "Tuesday";
       $weekday[] = "Wednesday";
       $weekday[] = "Thursday";
       $weekday[] = "Friday";
       $weekday[] = "Saturday";
       */

  //days of week array
$weekdays[0] = $phrase[425];
$weekdays[1] = $phrase[419];
$weekdays[2] = $phrase[420];
$weekdays[3] = $phrase[421];
$weekdays[4] = $phrase[422];
$weekdays[5] = $phrase[423];
$weekdays[6] = $phrase[424];
       
       
        
		     $sql = "select * from resource where resource_no = '$resource'";
								$DB->query($sql,"resource.php");
								$row = $DB->get();
								 $name = $row["resource_name"];	
				
    		 
    		 
      echo "  <a href=\"resource.php?m=$m&event=edit&resource=$resource\">$name</a>
            <h2>$name $phrase[489]</h2>";
     
$sql = "select * from resource_openinghours where resource_no = \"$resource\" order by day";
$DB->query($sql,"pcadmin.php");
$num = $DB->countrows();	


while ($row = $DB->get())
      {

     $opening[] = $row["openinghour"];
     $closing[] = $row["closinghour"];
     $day[]  = $row["day"];
     $open[]  = $row["open"];
	
	
  }

      echo "<form action=\"resource.php\" method=\"post\" > <table class=\"colourtable\"  cellpadding=\"5\" >
      <tr class=\"accent\"><td><b>$phrase[209]</b></td><td><b>$phrase[489]</b></td><td><b>$phrase[490]</b></td><td><b>$phrase[491]</b></td></tr>";

      //cycle through days of week to display opening hours
      foreach ($weekdays as $daynum => $dayname)
      
              {



                
                  $addrow = "yes";
				  
				  if (isset($day))
				{
				
                  foreach($day as $i => $dayofweek)
                     {
                       if ($daynum == $dayofweek)
                         {
                         $addrow = "no";
                          echo "<tr";
                          if ($open[$daynum] == 0)
                             { echo " style=\"background:#cccccc\"";}

                          echo "><td align=\"left\"> <b>$dayname</b></td><td>
                          <select name=\"opening[$daynum]\">";
                                foreach ($optionvalue as $index => $time)
                                        {
                                     if ($time == $opening[$daynum])
                                               {
                                               echo "<option value=\"$optionvalue[$index]\"> $optionlabel[$index]";
                                                }
                                        }


                          echo "</option>$option</select>
                          </td><td><select name=\"closing[$daynum]\">";


                          foreach ($optionvalue as $index => $time)
                                        {
                                     if ($time == $closing[$daynum])
                                        {
                                        echo "<option value=\"$optionvalue[$index]\"> $optionlabel[$index]";
                                        }
                                    }

                          echo "</option>$option</select></td><td>";
                            if ($open[$i] == 1)
                                      {
                           //library open
                                     echo " <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"1\" checked> $phrase[493] <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"0\"> $phrase[494]";
                                      }
                           else
                                       {
                                       echo " <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"1\" > $phrase[493] <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"0\" checked> $phrase[494]";
                                       }

                            echo "</td>
						 </tr>";

                                 }
                 //ends foreach  $day line 249
                      }
					  //ends isset $day
					  }
                      
                      
                      if ($addrow == "yes")
                                 {
                                  echo "<tr><td> $dayname</td><td>
              <select name=\"opening[$daynum]\">

<option value=\"060000\"> 6.00 am</option>
<option value=\"063000\"> 6.30 am</option>
<option value=\"070000\"> 7.00 am</option>
<option value=\"073000\"> 7.30 am</option>
<option value=\"080000\"> 8.00 am</option>
<option value=\"083000\"> 8.30 am</option>
<option value=\"090000\"> 9.00 am</option>
<option value=\"093000\"> 9.30 am</option>
<option value=\"100000\" selected>10.00 am</option>
<option value=\"103000\">10.30 am</option>
<option value=\"110000\">11.00 am</option>
<option value=\"113000\">11.30 am</option>
<option value=\"120000\">12.00 pm</option>
<option value=\"123000\">12.30 pm</option>
<option value=\"130000\"> 1.30 pm</option>
<option value=\"140000\"> 2.00 pm</option>
<option value=\"143000\"> 2.30 pm</option>
<option value=\"150000\"> 3.00 pm</option>
<option value=\"153000\"> 3.30 pm</option>
<option value=\"160000\"> 4.00 pm</option>
<option value=\"163000\"> 4.30 pm</option>
<option value=\"170000\"> 5.00 pm</option>
<option value=\"173000\"> 5.30 pm</option>
<option value=\"180000\"> 6.00 pm</option>
<option value=\"183000\"> 6.30 pm</option>
<option value=\"190000\"> 7.00 pm</option>
<option value=\"193000\"> 7.30 pm</option>
<option value=\"200000\"> 8.00 pm</option>
<option value=\"203000\"> 8.30 pm</option>
<option value=\"210000\"> 9.00 pm</option>
<option value=\"213000\"> 9.30 pm</option>
<option value=\"220000\"> 10.00 pm</option>
<option value=\"223000\"> 10.30 pm</option>
<option value=\"230000\"> 11.00 pm</option>
</select>
              </td><td><select name=\"closing[$daynum]\" > 
              
              
              
              
              
<option value=\"060000\"> 6.00 am</option>
<option value=\"063000\"> 6.30 am</option>
<option value=\"070000\"> 7.00 am</option>
<option value=\"073000\"> 7.30 am</option>
<option value=\"080000\"> 8.00 am</option>
<option value=\"083000\"> 8.30 am</option>
<option value=\"090000\"> 9.00 am</option>
<option value=\"093000\"> 9.30 am</option>
<option value=\"100000\">10.00 am</option>
<option value=\"103000\">10.30 am</option>
<option value=\"110000\">11.00 am</option>
<option value=\"113000\">11.30 am</option>
<option value=\"120000\">12.00 pm</option>
<option value=\"123000\">12.30 pm</option>
<option value=\"130000\"> 1.30 pm</option>
<option value=\"140000\"> 2.00 pm</option>
<option value=\"143000\"> 2.30 pm</option>
<option value=\"150000\"> 3.00 pm</option>
<option value=\"153000\"> 3.30 pm</option>
<option value=\"160000\"> 4.00 pm</option>
<option value=\"163000\"> 4.30 pm</option>
<option value=\"170000\"> 5.00 pm</option>
<option value=\"173000\"> 5.30 pm</option>
<option value=\"180000\"> 6.00 pm</option>
<option value=\"183000\"> 6.30 pm</option>
<option value=\"190000\"> 7.00 pm</option>
<option value=\"193000\"> 7.30 pm</option>
<option value=\"200000\"> 8.00 pm</option>
<option value=\"203000\"> 8.30 pm</option>
<option value=\"210000\" selected> 9.00 pm</option>
<option value=\"213000\"> 9.30 pm</option>
<option value=\"220000\"> 10.00 pm</option>
<option value=\"223000\"> 10.30 pm</option>
<option value=\"230000\"> 11.00 pm</option>
</select>
              </select></td><td>$phrase[493] <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"1\" checked> $phrase[494] <input type=\"radio\" name=\"branchopen[$daynum]\" value=\"0\"></td>
			  </tr>";


                                 }
               //ends foreach
              }


      echo "</table><p><input type=\"hidden\" name=\"m\" value=\"$m\">
        <input type=\"hidden\" name=\"resource\" value=\"$resource\">
         <input type=\"hidden\" name=\"event\" value=\"edit\">
      <input type=\"hidden\" name=\"update\" value=\"hours\">
      <input type=\"submit\" name=\"submit\" value=\"$phrase[28]\"></p></form>";
        
        
   }
        
        
        
	else
	{

	
	

	echo "
	<a href=\"resource.php?m=$m&amp;event=customfields\" >$phrase[908]</a>
<br><br>
	<a href=\"resource.php?m=$m&amp;update=add&amp;event=edit\" style=\"font-size:150%\"><img src=\"../images/add.png\" title=\"$phrase[176]\"  alt=\"$phrase[176]\"></a>
            
<br>

";
	
	
	$sql = "select * from resource  where m = '$m' order by resource_name";
	

	$DB->query($sql,"resource.php");
			
	$num_rows = $DB->countrows();
        
    
	if ($num_rows > 0)
		{
		echo "<table   cellpadding=\"10\" >";
		}
	while ($row = $DB->get()) 
		{
			$resource =$row["resource_no"];		
			$resource_name =$row["resource_name"];
			$description =$row["description"];
			$location =$row["location"];
			$linkname = urlencode($resource_name);
			
			echo "<tr><td style=\"text-align:left\"><b>$resource_name</b></td><td><a href=\"resource.php?m=$m&amp;event=edit&amp;resource=$resource\"><img src=\"../images/pencil.png\" title=\"$phrase[26]\"  alt=\"$phrase[26]\"></a></td><td><a href=\"resource.php?m=$m&amp;event=delete&amp;resource=$resource&amp;name=$linkname\"><img src=\"../images/cross.png\" title=\"$phrase[24]\"  alt=\"$phrase[24]\"></a></td></tr>";
		}
	
	if ($num_rows > 0)
		{
		echo "</table>";
		}	

   echo "<br><br>";
  } 
}
  
 
echo "</div></div>";
		
	 
	  	

include ("../includes/footer.php");