<?php
namespace Mediaio;
use Mediaio\Database;
require_once '../Database.php';
if(isset($_POST["newScore"]))
{
 $query = ("UPDATE `users` SET `UserPoints` = '".$_POST["newScore"]."' WHERE `users`.`userNameUsers` = '".$_POST['userUpdate']."'");
 $result=Database::runQuery($query);
 echo "1";
}

?>