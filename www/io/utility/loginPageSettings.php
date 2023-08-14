<?php 
/**
 * Write a custom message to the index page.
 */
session_start();
if(!in_array("system", $_SESSION["groups"])){
    echo "Ehhez a tartalomhoz nem tudsz hozzáférni!";
    exit();
}
//If a motd currently exists, print it here.
if(file_exists("../data/loginPageSettings.json")){
    $file = fopen("../data/loginPageSettings.json", "r");
    $message = fread($file, filesize("../data/loginPageSettings.json"));
    $message = json_decode($message, true);
    echo "Motd: <p style='color:".$message["color"]."'>".$message["message"]."</p>";
    if($message["limit"] == "true"){
        echo "Belépések korlátozva.";
    }else{
        echo "Belépések nincsenek korlátozva.";
    }
    fclose($file);
}else{
    echo "<p style='color:red'>Nincs MOTD beállítva.</p>";
}


    echo "System admin";
    //Motd form
    echo "<form action='loginPageSettings.php' method='post'>";
    echo "<input type='text' name='message' placeholder='MOTD üzenet'>";
    echo "<select name='color'>";
    echo "<option value='red' style='color:red'>Piros</option>";
    echo "<option value='green' style='color:green'>Zöld</option>";
    echo "<option value='orange' style='color:orange'>Sárga</option>";
    echo "</select></br>";


    //Limit logins
    echo "Belépések korlátozása: ";
    echo "<input type='checkbox' name='limit' value='true' >";

    echo "<button type='submit' name='submit'>Mentés</button>";
    echo "</form>";

//If the user has submitted the form, and the message is not empty, then write it to the file.
if(isset($_POST["submit"])){
    $message = $_POST["message"];
    $color = $_POST["color"];
    //creat a combined JSON from the message and the color
    $message = json_encode(array("message" => $message, "color" => $color, "limit" => $_POST["limit"]));
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
    exit();
}
?>
