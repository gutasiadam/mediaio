<?php 
namespace Mediaio;
require_once __DIR__.'/../Core.php';
require_once __DIR__.'/../ItemManager.php';
require_once __DIR__.'/../Database.php';
use Mediaio\MailService;
use Mediaio\ItemManager;
use Mediaio\Database;

$mysqli = Database::runQuery_mysqli();
$DB_Elements = fopen("./DB_Elements.txt", "w");
$mysqli->set_charset("utf8");
/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}
//OLD TXT Method
$query = "SELECT Nev, UID FROM leltar"; // WHERE TakeRestrict=''

if ($result = $mysqli->query($query)) {

    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        fwrite($DB_Elements, $row["Nev"]."\t".$row["UID"]."\n");
    }
    //print json_encode($rows);
    fclose($DB_Elements);
    /* free result set */
    $result->free();
}
$DB_Elements = fopen("DB_UID.txt", "w");
$query = "SELECT * FROM leltar WHERE TakeRestrict='' ";

if ($result = $mysqli->query($query)) {

    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        fwrite($DB_Elements, $row["UID"]."\n");
    }
    fclose($DB_Elements);
    /* free result set */
    $result->free();
}
function refetchData(){
    //NEW, JSON METHOD

}


?>