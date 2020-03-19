<?php 
    session_start();
    session_unset();
    session_destroy();
    if ($_GET["login"]="WrongCode"){header("Location: ../index.php?logout=WrongAuth");}
    else{header("Location: ../index.php?logout=success");}
?>