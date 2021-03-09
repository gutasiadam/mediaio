<?php if(isset($_GET['secureId'])){
    $secureId = $_GET['secureId'];
    $connect = new PDO("mysql:host=localhost;dbname=mediaio", "root", "umvHVAZ%");
    if($_GET['mode']=="add"){
    
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
            //echo $eventTitle.$eventStart.$eventEnd.$eventColor;
    }
    $query = "INSERT INTO events (title, start_event, end_event, borderColor, add_Date) VALUES ('$eventTitle','$eventStart','$eventEnd','$eventColor', now() );
    DELETE FROM eventprep WHERE secureId = '$secureId';";
    $statement2 = $connect->prepare($query);
    $statement2->execute();

    if($statement2){
        echo "<h1><strong>Sikeresen megerősítetted az eseményt! 🎉</strong></h1>";}

    } 
    else{
        echo "<h1>Az esemény kódja érvénytelen! Nem lehet, hogy már megerősítetted?</h1>";}
    }


    if($_GET['mode']=="del"){
        $query = "DELETE FROM eventprep WHERE secureId = '$secureId'";
        $statement = $connect->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() == 0){
            echo "<h1Az esemény kódja érvénytelen!</h1>";
        }else{
            echo "<h1>Törölve.</h1>";}
    }}

    $connect=null;
    ?>