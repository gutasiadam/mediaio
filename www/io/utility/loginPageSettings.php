<?php
/**
 * Write a custom message to the index page.
 */
session_start();
if (!in_array("system", $_SESSION["groups"])) {
    echo "Ehhez a tartalomhoz nincs hozzáférésed!";
    exit();
}

function fetchSettings()
{
    //If a motd currently exists, print it here.
    if (file_exists("../data/loginPageSettings.json")) {
        $file = fopen("../data/loginPageSettings.json", "r");
        $message = fread($file, filesize("../data/loginPageSettings.json"));
        $message = json_decode($message, true);
        echo "Motd: <p style='color:" . $message["color"] . "'>" . $message["message"] . "</p>";
        if ($message["limit"] == "true") {
            echo "Belépések: korlátozva.";
        } else {
            echo "Belépések: nincs korlát.";
        }

        echo "<br>";
        if ($message["registrationLimit"] == "true") {
            echo "Regisztráció: korlátozva.";
        } else {
            echo "Regisztráció: nincs korlát.";
        }

        fclose($file);
    } else { ?>
        <p style='color:red'>Nincs MOTD beállítva.</p>
<?php  }} //Motd form ?>


<form action='loginPageSettings.php' method='post'>
    <input type='text' name='message' placeholder='MOTD üzenet'>
    <select name='color'>
        <option value='red' style='color:red'>Piros</option>
        <option value='green' style='color:green'>Zöld</option>
        <option value='orange' style='color:orange'>Sárga</option>
    </select></br>

    Belépések korlátozása:
    <input type='checkbox' name='limit' value='true'><br>

    Regisztráció korlátozása:
    <input type='checkbox' name='registrationLimit' value='true'><br>

    <button type='submit' name='submit'>Mentés</button>
</form>


<?php
//If the user has submitted the form, and the message is not empty, then write it to the file.
if (isset($_POST["submit"])) {
    $message = $_POST["message"];
    $color = $_POST["color"];
    $limit = isset($_POST["limit"]) ? "true" : "false";
    $registrationLimit = isset($_POST["registrationLimit"]) ? "true" : "false";
    //creat a combined JSON from the message and the color
    $message = json_encode(array(
        "message" => $message, "color" => $color, "limit" => $limit,
        "registrationLimit" => $registrationLimit
    ));
    //write the JSON to the file
    $file = fopen("../data/loginPageSettings.json", "w");
    fwrite($file, $message);
    fclose($file);
    // if(empty($_POST["message"])){
    //     //delete the file
    //     unlink("../data/loginPageSettings.json");

    // }
    //reload the page
    //header("Refresh:0");

    
}
fetchSettings();
exit();

?>