<?php
    /*Login handler*/

    namespace Mediaio;

    require '../Core.php';
    use Mediaio\Core;

 
    
    $serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }

    //*ISTENÍTETT KÓD*
    if (isset($_POST['login-submit'])){
        $userName = $_POST['useremail'];
        $password = $_POST['pwd'];
        Core::loginUser($_POST);
    }
    else if (isset($_POST['logout-submit'])){
        Core::logoutUser();
    }
?>
