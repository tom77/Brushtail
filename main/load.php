<?
set_time_limit(6000);

$file = "../../../sqldata/bdb2";

$db = sqlite_open("F:\portable\sqldata\bdb2");

echo "database opened";

$counter = 0;

$start = 1;
$end = 1801;

while ($counter < 10000)
{

$sql = "Begin transaction";
sqlite_query($db,$sql);



$sql = "INSERT INTO pc_bookings VALUES(NULL,'dave','', '$start', '28', '106', '0', '17', '', '$end','192.168.10.233','administrator','0',1231294007,'','','0','0','1','1','0','0','7766')";
sqlite_query($db,$sql);

$sql = "INSERT INTO pc_bookings VALUES(NULL,'dave','', '$start', '28', '107', '0', '17', '', '$end','192.168.10.233','administrator','0',1231294007,'','','0','0','1','1','0','0','7766')";
sqlite_query($db,$sql);

$sql = "INSERT INTO pc_bookings VALUES(NULL,'dave','', '$start', '28', '108', '0', '17', '', '$end','192.168.10.233','administrator','0',1231294007,'','','0','0','1','1','0','0','7766')";
sqlite_query($db,$sql);

$sql = "INSERT INTO pc_bookings VALUES(NULL,'dave','', '$start', '28', '109', '0', '17', '', '$end','192.168.10.233','administrator','0',1231294007,'','','0','0','1','1','0','0','7766')";
sqlite_query($db,$sql);

$sql = "INSERT INTO pc_bookings VALUES(NULL,'dave','', '$start', '28', '110', '0', '17', '', '$end','192.168.10.233','administrator','0',1231294007,'','','0','0','1','1','0','0','7766')";
sqlite_query($db,$sql);

$sql = "INSERT INTO pc_bookings VALUES(NULL,'dave','', '$start', '28', '111', '0', '17', '', '$end','192.168.10.233','administrator','0',1231294007,'','','0','0','1','1','0','0','7766')";
sqlite_query($db,$sql);

$sql = "INSERT INTO pc_bookings VALUES(NULL,'dave','', '$start', '28', '112', '0', '17', '', '$end','192.168.10.233','administrator','0',1231294007,'','','0','0','1','1','0','0','7766')";
sqlite_query($db,$sql);

$sql = "INSERT INTO pc_bookings VALUES(NULL,'dave','', '$start', '28', '113', '0', '17', '', '$end','192.168.10.233','administrator','0',1231294007,'','','0','0','1','1','0','0','7766')";
sqlite_query($db,$sql);

$sql = "INSERT INTO pc_bookings VALUES(NULL,'dave','', '$start', '28', '114', '0', '17', '', '$end','192.168.10.233','administrator','0',1231294007,'','','0','0','1','1','0','0','7766')";
sqlite_query($db,$sql);


$sql = "INSERT INTO pc_bookings VALUES(NULL,'dave','', '$start', '28', '115', '0', '17', '', '$end','192.168.10.233','administrator','0',1231294007,'','','0','0','1','1','0','0','7766')";
sqlite_query($db,$sql);

$sql = "end transaction";
sqlite_query($db,$sql);

$counter++;
$end = $end + 1800;
$start = $start + 1800;

}




?>