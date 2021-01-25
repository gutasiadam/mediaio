
<?php

//update.php

$connect = new PDO("mysql:host=localhost;dbname=mediaio", "root", "umvHVAZ%");

if(isset($_POST["newScore"]))
{
 $query = "
 UPDATE users 
 SET UserPoints=:newScore
 WHERE userNameUsers=:userUpdate
 ";
 $SQL = ("UPDATE `users` SET `UserPoints` = :newScore WHERE `users`.`userNameUsers` = :userUpdate");
 $statement = $connect->prepare($query);
 $statement->execute(
  array(
   ':newScore'  => $_POST['newScore'],
   ':userUpdate' => $_POST['userUpdate']
  )
 );
 echo "1";
}

?>