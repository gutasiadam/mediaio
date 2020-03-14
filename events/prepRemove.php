<?php if(isset($_GET['secureId'])){
    $secureId = $_GET['secureId'];

    $connect = new PDO("mysql:host=localhost;dbname=calendar", "root", "umvHVAZ%");
    $query = "SELECT title, start_event, end_event, borderColor FROM `eventprep` WHERE secureId = '$secureId'";

    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    
    if ($statement->rowCount() == 1){
        foreach($result as $row){
            $eventTitle=$row["title"];
            $eventStart=$row["start_event"];
            $eventEnd=$row["end_event"];
            $eventColor=$row["borderColor"];
            echo $eventTitle.$eventStart.$eventEnd.$eventColor;
    }
    $query = "INSERT INTO events (title, start_event, end_event, borderColor) VALUES ('$eventTitle','$eventStart','$eventEnd','$eventColor');
    DELETE FROM eventprep WHERE secureId = '$secureId';";
    $statement2 = $connect->prepare($query);
    $statement2->execute();

    if($statement2){
        echo "Insert Successful, invalidating secureId.";}

    } 
    else{
        echo "Code invalid!";}
    }

    ?>