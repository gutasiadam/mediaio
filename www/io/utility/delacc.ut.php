<?php 
    session_start();
    if (isset($_POST['userDel'])){
        require 'dbHandler.ut.php';

        $username = $_SESSION['userId'];
        $ususname  = $_SESSION['UserUserName'];
        $password = $_POST['pwd'];

        if(empty($password)){
            header("Location: ../delacc.php?error=emptyField");
            exit();
        }else{
            //First, check is the current passw is the correct one.
            $sql = "SELECT * FROM users WHERE idUsers='$username'";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: ../delacc.php?error=SQLError1");
                exit();
            }else{
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                //Fetch Assoc
                if($row = mysqli_fetch_assoc($result)){
                    $pwdcheck = password_verify($password, $row['pwdUsers']);
                    if ($pwdcheck == false){
                        header("Location: ../delacc.php?error=OldPwdError");
                    }else if ($pwdcheck == true){
                        //Deletion
                        $sql = "DELETE FROM users WHERE idUsers='$username';" ;
                        session_unset();
                        session_destroy();
                        if (!mysqli_query($conn,$sql)){
                            echo("Error description: " . mysqli_error($conn));
                        }else{
                            header("Location: ../index.php?note=success");
                            exit();
                        }
                    }
                }
            }    
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        exit();
    }else{
        header("Location: ../index.php?submit=AccessViolation");
        exit();
    }
?>