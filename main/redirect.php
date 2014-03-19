<?php
include ("../includes/initiliaze_page.php");
extract($_GET);


if (!isset($_SESSION['userid'])) {exit();}

header("Location: $url");


$sql = "update content set archive = archive + 1 where content_id = '$content_id'";

$DB->query($sql,"page.php");




?>