<?php
    /*Login handler*/

    namespace Mediaio;

    require '../Core.php';
    use Mediaio\Core;

    if (isset($_POST['login-submit']) ){
        $userName = $_POST['useremail'];
        $password = $_POST['pwd'];
        $c=new Core();
        $c->loginUser($_POST);
    }
    else if (isset($_POST['logout-submit']) or isset($_GET['logout-submit'])){
        $c=new Core();
        $c->logoutUser($_POST);
    }
?>
