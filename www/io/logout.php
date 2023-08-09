<?php
    if ($_GET['return'] == 'success'){
        sleep(1);
        header("Location: ./index.php?warn=logout");
        exit();
    }else{
        header("Location: ./index.php?error=unknown");
        exit();
    }
?>