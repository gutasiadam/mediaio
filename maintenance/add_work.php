<?php
    namespace Mediaio;
    use Mediaio\MailService;
    use Mediaio\Database;

    require_once "../Database.php";
    require_once "../Mailer.php";
//insert.php
session_start();

if(isset($_POST["date"]) && isset($_POST["user"]) && isset($_POST["task"]))
{
    $result=Database::runQuery("SELECT emailUsers, firstName FROM users WHERE userNameUsers='".$_POST['user']."'");
    //$result=Database::runQuery("SELECT emailUsers, firstName FROM users WHERE userNameUsers='gutasiadam'");
 if ($result->num_rows > 0) {
    while($row = mysqli_fetch_array($result)) {
        $to=$row['emailUsers'];
        $nev=$row['firstName'];
 $query = "INSERT INTO feladatok (Datum, Szemely, Feladat) VALUES ('".$_POST['date']."', '".$_POST['user']."', '".$_POST['task']."')";
 //$query = "INSERT INTO feladatok (Datum, Szemely, Feladat) VALUES ('NULL', 'gutasiadam', 'xd')";
 $result=Database::runQuery($query);


 $content = '
<html>
<head>
  <title>Arpad Media IO</title>
</head>
<body>
  <h3>Kedves '.$nev.'!</h3>
  <p>Új feladatot kaptál:
  <table max-width="600px" display: block; margin: 0 auto ; border="1px solid black" cellspacing="0" cellpadding="0">
  <th style="text-align: center;">Dátum 📅</th>
  <th style="text-align: center;">Feladat 📝</th>
  <tr><td style="text-align: center;">'.$_POST['date'].'</h6>'.'</td><td style="text-align: center;">'.$_POST['task'].'</td></tr>
</table>

Ha szerinted ez az e-mail nem releváns, vagy hibás, jelezd azt a vezetőségnek.
  <h5>Üdvözlettel: <br> Arpad Media Admin👋</h5>
</body>
</html>
';
try {
  echo "3";
  $result=MailService::sendContactMail('MediaIO-feladatok',$to,"Új feladatot kaptál!",$content);
  exit();
} catch (Exception $e) {
  echo "Mailer Error: ".$e;
}
}
}else{
  echo "1";// Nincs ilyen felhasználó
  exit();}
}
//}
?>