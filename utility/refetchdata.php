<?php 
session_start();

include("../translation.php");
if($_SESSION['role']=="Boss"){
    echo "Menő";
}

$mysqli = new mysqli("localhost", "root", $application_DATABASE_PASS, "leltar_master");
$DB_Elements = fopen("DB_Elements.txt", "w");
/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$query = "SELECT Nev FROM leltar WHERE TakeRestrict='' ";

if ($result = $mysqli->query($query)) {

    /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
        fwrite($DB_Elements, $row["Nev"]."\n");
    }
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

/* close connection */
$mysqli->close();
?>