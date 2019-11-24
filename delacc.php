<?php
    require "header.php"
?>

<main>
    <?php 
        $username = $_SESSION['userId'];
        if(isset($_SESSION['userId'])){
            echo '<p>User deletion Utility.
            <form action="utility/delacc.ut.php" method="post">
            <input type="password" name="pwd" placeholder="Please type in your password in order to continue."><br>
            <button type="submit" name="userDel">Delete!</button>
            </form></p>';
        }else{
            header("Location: ../index.php");
            exit();
        }
    ?>
</main>
    
<?php 
    require "footer.php"
?>

