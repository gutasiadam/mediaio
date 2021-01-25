<?php
//insert.php
session_start();
if(isset($_POST["date"]) && isset($_POST["user"]) && isset($_POST["task"]))
{
    //Először nézzük meg, létezik-e a felhasználó:
    $conn = new mysqli("localhost", "root", "umvHVAZ%", "mediaio");
    $user=$_POST["user"];
    $result = $conn->query("SELECT emailUsers, firstName FROM users WHERE userNameUsers='$user'");
    $conn->close();
 if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $to=$row['emailUsers'];
        $nev=$row['firstName'];
    }
     
    $connect = new PDO("mysql:host=localhost;dbname=mediaio", "root", "umvHVAZ%");
 
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
 //E-mail küldése a felhasználónak
 $subject = 'MediaIO - Feladatot kaptál!';
 $message = '
<html>
<head>
  <title>Arpad Media IO</title>
</head>
<body>
  <h3>Kedves '.$nev.'!</h3>
  <p>Új feladatot kaptál:
 <table style="border: 1px solid black; width: 50%">
 <tr>
 <th>Dátum</th>
 <th>Feladat<td></th>
 </tr>
 <tr>
 <td>'.$_POST['date'].'</h6>'.'</td><td>'.$_POST['task'].'</td></tr>
 </table>
Ha szerinted ez az e-mail nem releváns, vagy hibás, jelezd a vezetőségnek.
  <h5>Üdvözlettel: <br> Arpad Media Admin</h5>
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'From: arpadmedia.io@gmail.com';
$headers[] = 'Content-type: text/html; charset=utf-8';

/* Additional headers
$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
$headers[] = 'From: Birthday Reminder <birthday@example.com>';
$headers[] = 'Cc: birthdayarchive@example.com';*/
mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, implode("\r\n", $headers));
echo "3";//Sikeres
}else{
    echo "1";// Nincs ilyen felhasználó
}
$connect=null;
}else{
    echo "2";//Üres cella, vagy formátumhiba.
}

?>