<?php 
  include "translation.php";
  error_reporting(E_ALL ^ E_NOTICE);
  //require 'header.php'; NOT NECESSARY, SHOULD BE USED IN THE FUTURE
?>
<!DOCTYPE html>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
<html data-theme='dark'>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="./main.css">
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Arpad Media IO Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    
    <?php if(!isset($_SESSION['userId'])){header("Location: ../index.php?login=AccessViolation");}
            else{
              echo '
              <div class="alert alert-warning alert-dismissible fade hide" id="note" role="alert">
</div>
                    <div >
                    <form action="utility/GAuth_login.php" method="post" class="formmain" id="formmain" autocomplete="off" >
                    <h6 align=center width="50%" id="SystemMsg" class="successtable2" style="display:none;"></h6>
                    <h1 align=center class="text text-light">'.$applicationTitleFull.' </h1>
		                <h4 align=center>Kérlek írd be Google Authenticator kódodat!</h4>
                   <div class="row justify-content-center" style="text-align: center;"><div class="col-7 col-sm-4"><input type="text" name="GcodeatLogin" placeholder="Kód" class="form-control mb-2 mr-sm-2"></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-5 col-sm-4"><button class="btn btn-dark" type="submit" name="login-submit" align=center>Ellenőrzés</button></div></div>
                    </div>
                    </form><footer class="page-footer font-small blue"> <div class="fixed-bottom" align="center"><p>'.$applicationTitleFull.' <strong>ver. '.$application_Version.'</strong><br /> Code by <a href="https://github.com/d3rang3">Adam Gutasi</a></p></div></footer>
                    </div>
              <footer class="page-footer font-small blue"> <div class="fixed-bottom" align="center"><p>'.$applicationTitleFull.' <strong>ver. '.$application_Version.'</strong><br /> Code by <a href="https://github.com/d3rang3">Adam Gutasi</a></p></div></footer>';
            }?>

	</body>
<style>
.logintable{
  width: 15%;
  text-align: center;
  margin: 0 auto; 
}



.col-6{
  border-style: solid;
  border-width: 1px;
  transition: 0.5s;
  text-align:center;
}

.col-6:hover{
  background-color:#3b3b3b;
  border-radius: 25px;
}

.ab{
  color: #3b3b3b;
  transition: 0.5s;
}

.ab:hover{
  color: #ffffff;
}


.formmain{
  position: absolute;
        top: 50%;
        left: 50%;
        margin-right: -50%;
        width: 30%;
        transform: translate(-50%, -50%);;}

body{
background: linear-gradient(43deg, #25725e, #212672, #034954);
background-size: 6000% 600%;

-webkit-animation: AnimationName 205s ease infinite;
-moz-animation: AnimationName 205s ease infinite;
-o-animation: AnimationName 205s ease infinite;
animation: AnimationName 205s ease infinite;}

@-webkit-keyframes AnimationName {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
@-moz-keyframes AnimationName {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
@-o-keyframes AnimationName {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
@keyframes AnimationName {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
</style>

<script type="text/javascript">



function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
     
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (timer > 60){
          $('#time').animate({'opacity': 0.9}, 0, function(){
          $(this).html(display.textContent).animate({'opacity': 1}, 500);
          setTimeout(function() { $("#time").text(display.textContent).animate({'opacity': 1}, 250); }, 700);;});
        }

        if (timer < 60){
          $('#time').animate({'opacity': 0.9}, 0, function(){
          $(this).html("<font color='red'>"+display.textContent+"</font").animate({'opacity': 1}, 500);
          setTimeout(function() { $("#time").html("<font color='red'>"+display.textContent+"</font").animate({'opacity': 1}, 250); }, 700);;});
        }

        if (--timer < 0) {
            timer = duration;
            window.location.href = "utility/logout.ut.php"
        }
    }, 1000);
}

window.onload = function () {
    var fiveMinutes = 60 * 10 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};


</script>