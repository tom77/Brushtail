<?php

include ("../includes/initiliaze_page.php");

echo "<h1>Pd manager upgrade script from 1.2 to 1.3<h1>";


$sql = "select pref_value from preferences";




$sql = "CREATE TABLE pd_sessions2 ( pd_id INTEGER PRIMARY KEY , pd_title VARCHAR (250) , pd_description TEXT , pd_date DATE , pd_external INTEGER (1) , pd_hours INTEGER (6) , pd_cost INTEGER (6) , pd_approved INTEGER (1) , pd_attended INTEGER (1) , pd_user INTEGER (11) , pd_custom TEXT , pd_replacement INT (4) , pd_category INTEGER , pd_group INTEGER (11) DEFAULT '0' ) ";
$DB->query($sql,"calendar.php");

$sql = "INSERT INTO pd_sessions2 SELECT  pd_id,pd_title,pd_description,pd_date,pd_external,pd_hours,pd_cost,pd_approved,pd_attended,pd_user,pd_custom,pd_replacement
,pd_category,'0' as pd_group FROM pd_sessions";
$DB->query($sql,"calendar.php");

$sql = "drop table pd_sessions";
$DB->query($sql,"calendar.php");

$sql = "CREATE TABLE pd_sessions ( pd_id INTEGER PRIMARY KEY , pd_title VARCHAR (250) , pd_description TEXT , pd_date DATE , pd_external INTEGER (1) , pd_hours INTEGER (6) , pd_cost INTEGER (6) , pd_approved INTEGER (1) , pd_attended INTEGER (1) , pd_user INTEGER (11) , pd_custom TEXT , pd_replacement INT (4) , pd_category INTEGER , pd_group INTEGER (11) DEFAULT '0' )";
$DB->query($sql,"calendar.php");


$sql = "INSERT INTO pd_sessions SELECT  pd_id,pd_title,pd_description,pd_date,pd_external,pd_hours,pd_cost,pd_approved,pd_attended,pd_user,pd_custom,pd_replacement
,pd_category,pd_group FROM pd_sessions2";
$DB->query($sql,"calendar.php");

$sql = "drop table pd_sessions2";
$DB->query($sql,"calendar.php");



?>