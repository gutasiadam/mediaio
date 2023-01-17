<html>
    <?php 
    include "header.php";
            ?>
            <table class="logintable"><tr><td><h1>Elfelejtett jelszó pótlása</h1></td></tr>
            <form action="./Core.php" method="post">
            <tr><td><h3><strong>1. lépés</strong>: kérj egy tokent!</strong></h3></td></tr>
            <tr><td>Ennek segítségével tudsz majd új jelszót megadni.</td></tr>
            <tr><td><input class="form-control mb-2 mr-sm-2" type="email" name="emailAddr" placeholder="e-mail" required></td></tr> <br>
            <tr><td><input class="form-control mb-2 mr-sm-2" type="text" name="userName" placeholder="felhasználónév" required></td></tr> <br>
            <tr><td><br><button class="btn btn-dark" id="submitPwdCh"align=center type="submit" name="pwdLost-submit" required>Token küldése</button></td></tr>
            <tr><td><div class="spinner-border" role="status">
            <span class="sr-only">Folyamatban...</span>
            </div></tr></td>
            </form>
            <form action="./Core.php.php" method="post">
            <tr><td><h3><strong>2. lépés</strong>: új jelszó</strong></h3></td></tr>
            <tr><td>Az 1. lépésben kapott tokened és felhasználóneved megadásával már adhatsz is egy új jelszót!</td></tr>
            <tr><td><input class="form-control mb-2 mr-sm-2" type="text" name="token" placeholder="token" required></td></tr> <br>
            <tr><td><input class="form-control mb-2 mr-sm-2" type="text" name="userName" placeholder="felhasználónév" required></td></tr> <br>
            <tr><td><input class="form-control mb-2 mr-sm-2" type="password" name="chPwd-1" placeholder="új jelszó" required></td></tr> <br>
            <tr><td><input class="form-control mb-2 mr-sm-2" type="password" name="chPwd-2" placeholder="új jelszó még egyszer" required></td></tr> <br>
            <tr><td><br><button class="btn btn-dark" id="submitPwdCh"align=center type="submit" name="pwdLost-change-submit">Csere</button></td></tr>
            <tr><td><div class="spinner-border" role="status">
            <span class="sr-only">Folyamatban...</span>
            </div></tr></td>
            </form>
            <?php
                if (isset($_GET['error'])){
                    if( $_GET['error'] == 'emptyField'){
                        echo '<tr><td><h5 class="registererror text-danger">Kérlek MINDEN mezőt tölts ki!</h5></td></tr>';
                    }else if ($_GET['error'] == 'PasswordCheck'){
                        echo '<tr><td><h5 class="registererror text-danger">A megadott jelszavak nem egyeznek, vagy túl rövid jelszót adtál meg!</h5></td></tr>';
                    }else if ($_GET['error'] == 'PasswordLenght'){
                        echo '<tr><td><h5 class="registererror text-danger">Az új jelszónak legalább 8 karakter hosszúnak kell lennie!</h5></td></tr>';
                    }else if ($_GET['error'] == 'OldPwdError'){
                        echo '<tr><td><h5 class="registererror text-danger">Hibásan adtad meg a jelenlegi jelszavadat!</h5></td></tr>';
                    }else if ($_GET['error'] == 'none'){
                    echo '<tr><td><p class="success">A tokenedet elküldtük az e-mail címedre! Ezt tudod használni a második lépésben.</p></td></tr>';
                    }else if ($_GET['error'] == 'none'){
                    echo '<tr><td><p class="success"><strong>Sikeres jelszócsere.</strong> Mostmár beléphetsz az új jelszavaddal.</p></td></tr>';
                }
                }
                ?>
                <tr><td>Sikeres jelszóváltoztatásról e-mailben értesítünk. Eztuán lépj vissza a belépéshez.</td></tr>
                <tr><td><a href="../index.php"><button class="btn btn-dark">Vissza a belépéshez</button></a></td></tr>
                <?php
            echo "</table>";

    ?>
</html>
<script>
$("#submitPwdCh").click(function(){
  $(".spinner-border").fadeIn();
});

$( document ).ready(function() {
  $(".spinner-border").hide();
});
(function(){
  setInterval(updateTime, 1000);
});

function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            timer = duration;
            window.location.href = "../utility/logout.ut.php"
        }
    }, 1000);
}

window.onload = function () {
    var fiveMinutes = 3 * 60 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};
</script>
<style>
.logintable{
  width: 30%;
  text-align: center;
  margin: 0 auto; 
}

.rainbow {
  -webkit-animation: color 10s linear infinite;
  animation: color 10s linear infinite;  
}


@-webkit-keyframes color {
  0% { color: #000000; }
  20% { color: #c91d2b; } 
  40% { color: #ba833e; }
  60% { color: #0f6344; }
  80% { color: #09457a; }
  100% { color: #5f0976; }
}

@keyframes background {
  0% { color: #000000; }
  20% { color: #c91d2b; } 
  40% { color: #ba833e; }
  60% { color: #0f6344; }
  80% { color: #09457a; }
  100% { color: #5f0976; }
}

</style>