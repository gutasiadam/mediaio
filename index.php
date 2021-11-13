<?php
//header("Cache-Control: public, max-age=3600, no-cache");
//header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 10)); 

  include "translation.php";
  error_reporting(E_ALL ^ E_NOTICE);
  include "header.php";
  //require 'header.php'; NOT NECESSARY, SHOULD BE USED IN THE FUTURE
?>
<!DOCTYPE html>
<body>
                <?php



                if(isset($_SESSION['userId'])){
                  $host=$ftp_ip;
$output=shell_exec('ping -n 1 '.$host);

if (strpos($output, 'out') !== false) {
    $state = "red";
}
    elseif(strpos($output, 'expired') !== false)
{
  $state = "yellow";
}
    elseif(strpos($output, 'data') !== false)
{
  $state = "green";
}
else
{
  $state = "black";
}
                    date_default_timezone_set("Europe/Budapest"); 
                    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark nav-all" id="nav-head">
					<a class="navbar-brand" href="index.php"><img src="./utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
            '; ?>
            <script>
            $( document ).ready(function() {
              menuItems = importItem("./utility/menuitems.json");
              drawMenuItemsLeft('index',menuItems);
            });
            </script>
            
            <?php
            echo '</ul>';
            if ($_SESSION['role']>=3){
              echo '<ul class="navbar-nav navbarPhP"><li><a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span></a></li>';
              echo '<li><a class="nav-link disabled" href="#">Admin jogok</a></li>';
              echo '</ul>
              <form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                        <button class="btn btn-danger my-2 my-sm-0" type="submit">'.$nav_logOut.'</button>
                        </form>
                        <div class="menuRight"></div>
            </div>
      </nav>
      ';
            }
            else{
              echo '</ul>
              <form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                        <button class="btn btn-danger my-2 my-sm-0" type="submit">'.$nav_logOut.'</button>
                        </form>
                        <div class="menuRight"></div>
            </div>
      </nav>
      ';
            }
            
                    ?>
                    
                    <?php
                }
          // Handle specific GET requests
                ?>
                <!-- </ul -->
        </nav>
	<body>
		
    <?php if(!isset($_SESSION['userId'])){?>
                    <!--<div class="loginbox">
                    <form action="utility/login.ut.php" method="post" class="formmain" id="formmain" autocomplete="off" >
                    <h6 align=center width="100%" id="SystemMsg" class="successtable2" style="display:none;"></h6>
                    <h1 align=center class="rainbow"><?php echo $applicationTitleFull;?></h1>
                   <div class="row justify-content-center" style="text-align: center;"><div class="col-7 col-sm-4"><input type="text" name="useremail" placeholder="Felhasználónév/E-mail" class="form-control mb-2 mr-sm-2"></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-7 col-sm-4"><input type="password" name="pwd" placeholder="Jelszó" class="form-control mb-2 mr-sm-2"></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-5 col-sm-4"><button class="btn btn-dark" type="submit" name="login-submit" align=center>Bejelentkezés</button></div></div>
                    <div class="row justify-content-center" style="text-align: center;"><div class="col-5 col-sm-4"><a href="./pwReset.php">Elfelejtett jelszó</a></div></div>
                    </div>
                    </form>
                    -->
                    
                    
                    <form class="login" action="utility/login.ut.php" method="post" autocomplete="off">
                      <fieldset>
  	                    <legend id="zsoka" class="legend">
                        MediaIO
                        </legend>
                       
                          <div class="input">
    	                    <input type="text" name="useremail" placeholder="Felhasználónév/E-mail" required />
                          <span><i class="fa fa-envelope-o"></i></span>
                          </div>
                          <div class="input">
    	                      <input type="password" name="pwd" placeholder="Jelszó" required />
                            <span><i class="fa fa-lock"></i></span>
                            </div>
                           <button class="btn btn-dark" type="submit" name="login-submit"><i class="fa fa-long-arrow-right"></i></button>
                      </fieldset>
  
                      <div class="feedback">
  	                  átirányítás.. <br />
                      </div>
                      <div>
                      <h3 id="errorbox"></h3>
                    </div>
                      </form>
                      <h6 align=center id="SystemMsg" class="successtable2" style="display:none;"></h6>
                      
                    <footer class="page-footer font-small blue">
                    
                    <div class="fixed-bottom" align="center">
                    <a href="./profile/lostPwd.php"><h6>Elfelejtett jelszó?</h6></a>  <p></strong><br /> Code by <a href="https://github.com/gutasiadam">Adam Gutasi</a></p></div></footer>
                    </div>
                    <script>
                    $( ".input" ).focusin(function() {
  $( this ).find( "span" ).animate({"opacity":"0"}, 200);
});

$( ".input" ).focusout(function() {
  $( this ).find( "span" ).animate({"opacity":"1"}, 300);
});

$(".login").submit(function(){
  $(this).find(".submit i").removeAttr('class').addClass("fa fa-check").css({"color":"#fff"});
  $(".submit").css({"background":"#2ecc71", "border-color":"#2ecc71"});
  $(".feedback").show().animate({"opacity":"1", "bottom":"-80px"}, 400);
  $("input").css({"border-color":"#2ecc71"});
  $(".login").submit();
});
                    </script>
                    <style>

  #errorbox{
	  position: relative;
    text-align: center;
  }
                    .login{
  position: relative;
  top: 50%;
	width: 250px;
  display: table;
  margin: -150px auto 0 auto;
  background: #fff;
  border-radius: 4px;
  z-index: 2;
}

.legend{
  position: relative;
  width: 100%;
  display: block;
  background: #1d2660;
  padding: 15px;
  color: #fff;
  font-size: 20px;
  
  &:after{
    content: "";
    background-image: url(http://simpleicon.com/wp-content/uploads/multy-user.png);
    background-size: 100px 100px;
    background-repeat: no-repeat;
    background-position: 152px -16px;
    opacity: 0.06;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    position: absolute;
  }
}

.input{
  position: relative;
  width: 90%;
  margin: 15px auto;
  
  span{
    position: absolute;
    display: block;
    color: darken(#EDEDED, 10%);
    left: 10px;
    top: 8px;
    font-size: 20px;
  }
  
  input{
    width: 100%;
    padding: 10px 5px 10px 40px;
    display: block;
    border: 1px solid #EDEDED;
    border-radius: 4px;
    transition: 0.2s ease-out;
    color: darken(#EDEDED, 30%);
    
    &:focus{
      padding: 10px 5px 10px 10px;
      outline: 0;
      border-color: #FF7052;
    }
  }
}

.submit{
  width: 45px;
  height: 45px;
  display: block;
  margin: 0 auto -15px auto;
  background: #fff;
  border-radius: 100%;
  border: 1px solid #FF7052;
  color: #FF7052;
  font-size: 24px;
  cursor: pointer;
  box-shadow: 0px 0px 0px 7px #fff;
  transition: 0.2s ease-out;
  
  &:hover, &:focus{
    background: #FF7052;
    color: #fff;
    outline: 0;
  }
}

.feedback{
  position: absolute;
  bottom: -70px;
  width: 100%;
  text-align: center;
  color: #fff;
  background: #2ecc71;
  padding: 10px 0;
  font-size: 12px;
  display: none;
  opacity: 0;
  
  &:before{
    bottom: 100%;
    left: 50%;
    border: solid transparent;
    content: "";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-color: rgba(46, 204, 113, 0);
    border-bottom-color: #2ecc71;
    border-width: 10px;
    margin-left: -10px;
    
  }
}
                    </style>
                    
                    
                     <?php ;}
               else{ ?>
              <div class="alert alert-warning alert-dismissible fade show" id="note" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <strong>Kedves <?php echo $_SESSION['firstName'] ?>!</strong> Az oldal <u>folyamatos fejlesztés</u> alatt áll. Ha hibát szeretnél bejelenteni/észrevételed van, írj az arpadmedia.io@gmail.com címre, vagy <a href="mailto:arpadmedia.io@gmail.com?Subject=MediaIO%20Hibabejelent%C3%A9s" target="_top">írj most egy e-mailt!</a>
</div>
              <h1 align=center class="rainbow">Árpád Média IO</h1>
                    <div class="row justify-content-center mainRow1" style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div><br>
                    <div class="row justify-content-center mainRow2" style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div><br>
                    <div class="row justify-content-center mainRow3" style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div><br>
                    <div class="row justify-content-center mainRow4" style="text-align: center; width:100%; max-width: 1000px; margin: 0 auto;"></div><br>
              <footer class="page-footer font-small blue"> <div class="fixed-bottom" align="center"><p></strong><br /> dev: <a href="https://github.com/d3rang3">Gutási Ádám</a>
            </p></div></footer>';
            <script type = "text/javascript">
            $( document ).ready(function() {             
              //WebSocketTest();
              drawMenuItemsRight('index',menuItems);
              drawIndexTable(menuItems,0);
            });
            </script>
            <?php }
            //GET változók kezelése
            
            if($_GET['signup'] == "success"){
              echo '<script>document.getElementById("errorbox").innerHTML="Sikeres regisztráció!";
              document.getElementById("errorbox").className = "alert alert-success successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
            }
            if($_GET['logout'] == "success"){
              echo '<script>document.getElementById("errorbox").innerHTML="Sikeres kijelentkezés!";
              document.getElementById("errorbox").className = "alert alert-success successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';} // ÁTMÁSOLNI
            if($_GET['logout'] == "pwChange"){
              echo '<script>document.getElementById("errorbox").innerHTML="Sikeres jelszócsere!";
              document.getElementById("errorbox").className = "alert alert-success successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
            }
            if($_GET['error'] == "WrongPass"){
              echo '<script>document.getElementById("errorbox").innerHTML="Helytelen jelszó!";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
            }
            if($_GET['error'] == "NoUser"){
              echo '<script>document.getElementById("errorbox").innerHTML="Hibás felhasználónév / jelszó!";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
            }
            if($_GET['error'] == "AccessViolation"){
              echo '<script>document.getElementById("errorbox").innerHTML="Ehhez a funkcióhoz be kell jelentkezned!";
              document.getElementById("errorbox").className = "alert alert-danger successtable";
              $("#zsoka").fadeIn();
              setTimeout(function(){ $("#errorbox").fadeOut(); }, 6000);
              </script>';
            }?>

	</body>
<script type="text/javascript">

window.onload = function () {
    var fiveMinutes = 60 * 10 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};
/*window.onload = function () {
    var fiveMinutes = 60 * 10 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};*/
</script>