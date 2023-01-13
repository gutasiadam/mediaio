<?php
include "header.php";
session_start();
if(isset($_SESSION['userId'])){
    error_reporting(E_ALL ^ E_NOTICE);
//index.php
$serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }
?>

<html>  
    <head>
        <script src="../utility/jquery.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  
    </head>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      
            <a class="navbar-brand" href="../index.php"><img src="../utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
          
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
            </ul>
            <ul class="navbar-nav navbarPhP"><li><a class="nav-link disabled timelock" href="#">⌛ <span id="time"> 10:00 </span></a></li>
            <?php if ($_SESSION['role']>=3){
               ?>
              <li><a class="nav-link disabled" href="#">Admin jogok</a></li> <?php  }?>
            </ul>
						<form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
                      <button class="btn btn-danger my-2 my-sm-0" name="logout-submit" type="submit">Kijelentkezés</button>
                      </form>
                      <div class="menuRight"></div>
					</div>
          <script> $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft("profile",menuItems,2);
              drawMenuItemsRight('profile',menuItems,2);
            });</script>
    </nav>
    <body>  
        <div class="container">
   <br />

			
<div class="form-group">
   <div class="panel panel-default">
    <div class="panel-heading">
    <?php 
        $TKI = $_SESSION['UserUserName'];    
        $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
        $sql = ("SELECT * FROM `leltar` WHERE `RentBy` = '$TKI'");
        $result = mysqli_query($conn, $sql);
        $conn->close();
        echo '<h3 class="panel-title">'.$_SESSION['firstName'].', ezek a tárgyak vannak most nálad:</h3>
        </div>';
        $imodal=0;
        $resultArray = [];
        //$rows = mysqli_fetch_all($result);
          while($row = $result->fetch_assoc()) { 
              array_push($resultArray, $row);
              //$authGen = random_int(100000,999999);
              //if ($row["AuthState"] != NULL){ // Tehát már van kód generálva
                //Keressük meg az itemhez tartozó kód értékét és hogy melyik felhasználó használhatja ezt a kódot.
                $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
                $rowItem = $row["Nev"];
                //$query = ("SELECT * FROM `authcodedb` WHERE Item = '$rowItem'");
                //$result2 = mysqli_query($conn, $query);
                $conn->close();
                //while($codeRow = $result2->fetch_assoc()) {$dbCode = $codeRow["Code"]; $dbUser = $codeRow["AuthUser"];}
                
                /*echo '<div class="row">
              <div class="col-4">
               <h2>'. $row["Nev"].'</h2>
               <p>'. $row["Tipus"].'</p> 
              </div>
             </div>';*/
            //}else{
              echo '
              <div class="row">
              <div class="col-4">
               <h2>'. $row["Nev"].'</h2>
               <p>'. $row["UID"].'</p>
              </div>';
              //$query = "SELECT * FROM `leltar` WHERE RentBy = '$TKI'";
              echo '
             <div class="col-2"><button class="btn btn-success " id="bringback'.$imodal.'" data-toggle="modal" data-target="#b'.$imodal.'">Visszahoztam</button></div>
             </div>
             '
             
             ;
            
            echo '
            <div class="modal fade" id="b'.$imodal.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">'.$row["Nev"].' Visszahozása</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form action="./pfcurr.php" class="form-group" method=post>
              <input type="hidden" id="retrieveItem_'.$imodal.'" name="retrieveItem" value="'.$row["UID"].'"/> 
              <input type="hidden" name="User" value="'.$TKI.'"/>
              <div class="form-check">
              <input class="form-check-input intactItems" type="checkbox" value="" id="intactItems'.$imodal.'">
              </div>
              <h6></h6>
              <h6 id="emailHelp" class="form-text text-muted">A kipipálással igazolom, hogy amit visszahoztam sérülésmentes, és kifogástalanul működik. Sérülés esetén azonnal jelezd azt a vezetőségnek.</h6>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">❌</button>
                  <button type="submit" id="'.$imodal.'" onClick="reply_click(this.id)" class="btn go_btn btn-success disabled">☑</button>
                  <a href="../utility/damage_report/annouce_Damage.php" class="btn go_btn btn-warning">Problémát jelentek be</a>
                  </form>
                  
                  <p class="sysResponse"> </p>
            </div>
                  </div>      
              </div>
            </div>
            ';//}
            $imodal++;
            if($row["Event"]=="IN"){
              echo '';
            }
            
           }
           if($imodal==0){
             echo '// Jelenleg nincs nálad egy tárgy sem ';
           }
           echo '
           </div>
          </div>
         </div>
        </div>';
    $connect = null;
    ?>

    <!-- Modal -->


    </body>  
</html>
<style>
.timeline__item{
  background-color: #ededed;
}
</style>

<?php
}
else{
    echo("A tartalom megtekintéséhez először jelentkezz be.");
}
//AUTH Handling

?>
<script>
//Visszahozás ellenőrzése a handlernél
function retrieve(i){ // i=> item
  console.log("Begin retrieve by handler");
  retrieveItem_list=[i];
  retrieveJSON = JSON.stringify(retrieveItem_list);
  console.log(retrieveJSON);
      $.ajax({
    method: 'POST',
    url: '../utility/Retrieve_Handler.php',
    data: {data : retrieveJSON, mode: "handle"},
    success: function (response){
      //alert('Válasz:'+response);
      $('.sysResponse').append('Sikeres művelet! Az oldal hamarosan újratölt.');
      setTimeout(function(){location.reload();},5000);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
        console.log("Status: " + textStatus); console.log("Hiba: " + errorThrown); 
        setTimeout(function(){location.reload();},5000);
    }
    
});
  };
  function reply_click(clicked_id) //Begyűjti a tárgy nevét, amit vissza akar a felhasználó hozni.
  {
    retrieveItem=document.getElementById('retrieveItem_'+(clicked_id)).value
    //alert(retrieveItem);
    if($('.intactItems').is(":checked")){
      retrieve(retrieveItem);// AJAXos visszahozás megkezdése
    }else{
      alert('Ha probléma akad a tárggyal, jelezd azt a vezetőségnek!');
      //$( ".intactItems" ).effect( "shake" );
    }
    
  }
$(document).on('click', '.intactItems', function(){ // Submit gomb engedélyezése, ha az Intact form ki lett pipálva.
  if($('.intactItems').is(":checked")){
      $('.go_btn').removeClass('disabled');
    }
  });

function copyText(item) {
  /* Get the text field */
  var copyText = document.getElementById('$code'.item);
  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /*For mobile devices*/
  /* Copy the text inside the text field */
  document.execCommand("copy");
  /* Alert the copied text */
  alert("Copied the text: " + copyText.value);
}
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
            window.location.href = "./utility/logout.ut.php"
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
/*$(document).on('click', '.authToggle', function(){
  setTimeout(function(){ window.location.href = "./utility/logout.ut.php"; }, 3000);});*/
</script>