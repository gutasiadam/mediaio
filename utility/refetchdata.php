<?php 
include("../translation.php");
if($_SESSION['role']=="Boss"){
    echo "ALLOW";
}

$mysqli = new mysqli("localhost", "root", $application_DATABASE_PASS, "mediaio");
$DB_Elements = fopen("DB_Elements.txt", "w");
/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}
//OLD TXT Method
$query = "SELECT Nev FROM leltar WHERE TakeRestrict='' ";

if ($result = $mysqli->query($query)) {

    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        fwrite($DB_Elements, $row["Nev"]."\n");
    }
    print json_encode($rows);
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

//NEW, JSON METHOD
$rows = array();
$query = "SELECT Nev, ID, UID, Category, Status FROM leltar WHERE TakeRestrict='' ";
if ($result = $mysqli->query($query)) {

    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        if($row['Status']==="0"){
            $row['state']=['disabled' => true];
        }else{
            $row['state']=['disabled' => false];
        }
        $rows[] = $row;

    }
    print json_encode($rows);

    $itemsJSONFile = fopen('takeOutItems.json', 'w');
    fwrite($itemsJSONFile, json_encode($rows));
    fclose($itemsJSONFile);
    $result->free();
}

/* close connection */
$mysqli->close();
?>