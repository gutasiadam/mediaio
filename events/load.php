<?php

//load.php

$connect = new PDO("mysql:host=localhost;dbname=calendar", "root", "umvHVAZ%");

$data = array();

$query = "SELECT * FROM events ORDER BY id";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

foreach($result as $row)
{
 $data[] = array(
  'id'   => $row["id"],
  'title'   => $row["title"],
  'start'   => $row["start_event"],
  'end'   => $row["end_event"],
  'backgroundColor' => $row["borderColor"],
  'borderColor' => $row["borderColor"]
 );
}

echo json_encode($data);

?>
