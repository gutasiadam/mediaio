<?php
    if(isset($_SESSION['userId'])){
        #date_default_timezone_set("Europe/Budapest"); 
        echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark>
        <a class="navbar-brand" href="#">Arpad Media IO</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link disabled" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#">[X] Take Out</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#">[X] Retrieve</a>
          </li>
          <li class="nav-item">
          <a class="nav-link disabled" href="#"> [X] Adatok</a>
          </li>
          <li><span class="badge badge-dark">'. $_SERVER['REMOTE_ADDR'] .'</span></li>
          </ul>
          <form class="form-inline my-2 my-lg-0" action=./signup.php>
          <button class="btn btn-outline-dark my-2 my-sm-0" type="submit">Sign Up</button>
          </form>
        </div>
        </nav>
        <li  style="float:left"; font-size:10px;>'.date('h:m:s ').($_SESSION['UserUserName']).'</li>';?>
        <?php
    }else{
        echo '';
    } ?>

<body>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <div id="signupdata" align=center> 
        <form action="./Core.php" method="post">
            <h2>Regisztrációs felület</h2>
            <?php 
                if (isset($_GET['error'])){
                    if( $_GET['error'] == 'emptyField'){
                        echo '<p class="registererror">Please Fill out ALL fields!</p>';
                    }else if ($_GET['error'] == 'PasswordCheck'){
                        echo '<p class="registererror">Passwords does not match!</p>';
                    }else if ($_GET['error'] == 'PasswordLenght'){
                        echo '<p class="registererror">Password should be at least 8 characters long!</p>';
                    }else if ($_GET['error'] == 'UserTaken'){
                        echo '<p class="registererror">Username already exists. Sorry.</p>';}}
            ?>
            <table class="logintable">
                <tr><td><div class="form-group">
                <label for="lastName">Vezetéknév</label> <br> <input class="form-control mb-2 mr-sm-2" width=50% type="text" name="lastName" placeholder="Vezetéknév" required></div></td></tr>
                <tr><td><div class="form-group">
                <label for="firstName">Keresztnév</label> <br> <input class="form-control mb-2 mr-sm-2" width=50% type="text" name="firstName" placeholder="Keresztnév" required></div></td></tr>
                <tr><td><div class="form-group">
                <label for="userid">Felhasználónév</label> <br> <input class="form-control mb-2 mr-sm-2" type="text" name="userid" placeholder="Felhasználónév" required></div></td></tr>
                <tr><td><div class="form-group">
                <label for="email">E-mail cím</label> <br> <input class="form-control mb-2 mr-sm-2" type="email" name="email" placeholder="E-mail cím" required></div></td></tr>
                <tr><td><div class="form-group">
                <label for="tele">Telefonszám</label> <br> <input class="form-control mb-2 mr-sm-2" type="tel" name="tele" placeholder="pl.: 06201234567" pattern="[06][0-9]{2}[0-9]{3}[0-9]{2}[0-9]{3}" required></div></td></tr>
                <tr><td><div class="form-group">
                <label for="pwd">Jelszó</label> <br> <input class="form-control mb-2 mr-sm-2" type="password" name="pwd" placeholder="Jelszó" required></div></td></tr>
                <tr><td><div class="form-group"><input class="form-control mb-2 mr-sm-2" type="password" name="pwd-Re" placeholder="Jelszó újra" required></div></td></tr><br>
                <tr><td>
                <input class="form-control mb-2 mr-sm-2" width=50% type="hidden" name="register" value=1>  
                <button class="btn btn-dark mb-2 mr-sm-2" id="submitSgnUp" type="submit" name="signup-submit">Mehet</button></td></tr>
                <tr><td><a href="./index.php" class="btn btn-dark mb-2 mr-sm-2">Vissza a bejelentkezéshez</a></td></tr>
                <tr><td><div class="spinner-border" role="status">
                
                <span class="sr-only">Loading...</span>
                </div></tr></td>
              </table>
    </form>
    <h3></h3>
    </div>
</body>
<script>
$("#submitSgnUp").click(function(){
  $(".spinner-border").fadeIn();
});

$( document ).ready(function() {
  $(".spinner-border").hide();
});
</script>
<style>
    .registererror{
        font-size: 20px;
        color: red;
    }
    .success{
        font-size: 20px;
        color: green;
        background-color: #669999;
    }

    table{
      vertical-align: middle;
    text-align: center;
    }
</style>
