<?php
//insert.php
session_start();
$n=0;
function renderUsersDraggable(){
    $conn = new mysqli("localhost", "root", "umvHVAZ%", "loginsystem");
    $result = $conn->query("SELECT usernameUsers FROM users");
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $n++;
            echo '<h5><span id="user'.$n.'" class="badge badge-secondary" draggable="true" ondragstart="drag(event)">'.$row['usernameUsers'].'</span></h5>';
    }}
    $conn->close();
}
$today=date("Y/m/d");
    //Először töröljük a mainál régebbi dátumú feladokat, ha van ilyen.
    $conn = new mysqli("localhost", "root", "umvHVAZ%", "rendrakas");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
    $user=$_POST["user"];

    $conn->query("DELETE FROM feladatok WHERE Datum<'$today'");
    $result = $conn->query("SELECT * FROM feladatok ORDER BY Datum");

 if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo'<tr>
        <td>'.$row['Datum'].'</td>
        <td>'.$row['Szemely'].'</td>
        <td>'.$row['Feladat'].'</td>
      </tr>';
    }
    $conn->close();
    //Összes felhasználó hozzáadása a DRAGnDROPhoz
    
  /*  $connect = new PDO("mysql:host=localhost;dbname=rendrakas", "root", "umvHVAZ%");
 
 $query = "
 INSERT INTO feladatok 
 (Datum, Szemely, Feladat) 
 VALUES (:date, :user, :task)
 ";
 $statement = $connect->prepare($query);
 $statement->execute(
  array(
   ':date'  => $_POST['date'],
   ':user' => $_POST['user'],
   ':task' => $_POST['task']
  )
 );
*/
}else{
    echo "Nincs megjeleníthető feladat.\n";
}
$connect=null;


?>