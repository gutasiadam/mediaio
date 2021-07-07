<!-- A Felhasználónál levő tárgyak mutatása -->
<?php 
session_start();
if(isset($_SESSION['userId'])){
    error_reporting(E_ALL ^ E_NOTICE);
//index.php
$serverName="localhost";
	$userName="root";
	$password="umvHVAZ%";
	$dbName="mediaio";
?>

<html>  
    <head>
        <script src="utility/timeline.min.js"></script>
        <link rel="stylesheet" href="utility/pathfinder.css" />
        <link rel="stylesheet" href="utility/timeline.min.css" />
        <script src="utility/jquery.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  <title>PathFinder/AuthCodeGen</title>
  
    </head>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
					<a class="navbar-brand" href="../index.php">Arpad Media IO</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto">
						<li class="nav-item ">
						  <a class="nav-link" href="../index.php"><i class="fas fa-home fa-lg"></i><span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
						  <a class="nav-link" href="../takeout.php"><i class="fas fa-upload fa-lg"></i></a>
						</li>
						<li class="nav-item">
						  <a class="nav-link" href="../retrieve.php"><i class="fas fa-download fa-lg"></i></a>
						</li>
            <li class="nav-item">
						  <a class="nav-link" href="../adatok.php"><i class="fas fa-database fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        	<a class="nav-link active" href="./pfcurr.php"><i class="fas fa-project-diagram fa-lg"></i></a>
            			</li>
                  <li class="nav-item">
                        <a class="nav-link" href="../events/"><i class="fas fa-calendar-alt fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        <a class="nav-link" href="../profile/index.php"><i class="fas fa-user-alt fa-lg"></i></a>
            </li>
						<li>
              <a class="nav-link disabled" href="#">Időzár <span id="time">10:00</span></a>
            </li>
            <?php if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';}?>
					  </ul>
						<form class="form-inline my-2 my-lg-0" action=../utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form></form>
              <a class="nav-link my-2 my-sm-0" href="../help.php"><i class="fas fa-question-circle fa-lg"></i></a>
					</div>
</nav>
    <body>  
        <div class="container">
   <br />
   <!--<h3 align="center">PathFinder <?php echo $_SESSION['UserUserName'];?> felhasználónak<i class="fas fa-project-diagram fa-lg"></i></h3>-->
			
<div class="form-group">
   <div class="panel panel-default">
    <div class="panel-heading">
    <?php 
        $TKI = $_SESSION['UserUserName'];    
        $conn = new mysqli($serverName, $userName, $password, $dbName);
        $sql = ("SELECT * FROM `leltar` WHERE `RentBy` = '$TKI'");
        $result = $result = mysqli_query($conn, $sql);
        $conn->close();
        echo '<h3 class="panel-title">👇'.$_SESSION['firstName'].', ezek a tárgyak vannak most nálad 👇</h3>
        </div>
        <div class="panel-body">
         <div class="timeline">
          <div class="timeline__wrap">
           <div class="timeline__items">';
        $imodal=0;
        $resultArray = [];
        //$rows = mysqli_fetch_all($result);
          while($row = $result->fetch_assoc()) { 
              array_push($resultArray, $row);
              $authGen = random_int(100000,999999);
              if ($row["AuthState"] != NULL){ // Tehát már van kód generálva
                //Keressük meg az itemhez tartozó kód értékét és hogy melyik felhasználó használhatja ezt a kódot.
                $conn = new mysqli($serverName, $userName, $password, $dbName);
                $rowItem = $row["Nev"];
                $query = ("SELECT * FROM `authcodedb` WHERE Item = '$rowItem'");
                $result2 = mysqli_query($conn, $query);
                $conn->close();
                while($codeRow = $result2->fetch_assoc()) {$dbCode = $codeRow["Code"]; $dbUser = $codeRow["AuthUser"];}
                
                echo '<div class="row">
              <div class="col-4">
               <h2>'. $row["Nev"].'</h2>
               <p>'. $row["Tipus"].'</p> 
              </div>
              <div class="col-2"><button class="btn btn-danger disabled" id="auth'.$imodal.'"">AuthCode generated!</button></div>
              <div class="col-2">AuthCode: <br><h6 id="code'.$imodal.'"><strong>'.$dbCode.'</strong></h6></div>
              <div class="col-2"><button id="copy'.$imodal.'" onclick="copyText(xd) class="x">Copy text</button></div>
             </div>';
            }else{
              echo '
              <div class="row">
              <div class="col-4">
               <h2>'. $row["Nev"].'</h2>
               <p>'. $row["Tipus"].'</p>
              </div>';
              //$query = "SELECT * FROM `leltar` WHERE RentBy = '$TKI'";
              echo '<div class="col-2"><button class="btn btn-dark disabled" id="auth'.$imodal.'" data-toggle="modal" data-target="#a'.$imodal.'">AuthCode Készítése</button></div>
             <div class="col-2"><button class="btn btn-success " id="bringback'.$imodal.'" data-toggle="modal" data-target="#b'.$imodal.'">Visszahoztam</button></div>
             </div>
             '
             
             ;
            echo              
            '<div class="modal fade" id="a'.$imodal.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Generate AuthCode for '.$row["Nev"].'</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form action="./pfcurr.php" class="form-group" method=post>
              <input type="hidden" name="authItem" value="'.$row["Nev"].'"/> 
              <input type="hidden" name="authGen" value="'.$authGen.'"/> 
              <h6 id="emailHelp" class="form-text text-muted">Kérlek vedd figyelembe, hogy a generált kódot <strong>bárki</strong> felhasználhatja!
              A gomb lenyomása után töltsd újra az odlalt!</h6>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-success authToggle">Generálás</button>
                  </form>
            </div>
                  </div>      
              </div>
            </div>
            
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
              <input type="hidden" id="retrieveItem_'.$imodal.'" name="retrieveItem" value="'.$row["Nev"].'"/> 
              <input type="hidden" name="User" value="'.$TKI.'"/>
              <div class="form-check">
              <input class="form-check-input intactItems" type="checkbox" value="" id="intactItems'.$imodal.'">
              </div>
              <h6></h6>
              <h6 id="emailHelp" class="form-text text-muted">A kipipálással igazzolom, hogy amit visszahoztam sérülésmentes és kifogástalanul működik. Sérülés esetén azonnal jelezd azt a vezetőségnek.</h6>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">❌</button>
                  <button type="submit" id="'.$imodal.'" onClick="reply_click(this.id)" class="btn go_btn btn-success disabled">☑</button>
                  </form>
                  <p class="sysResponse"> </p>
            </div>
                  </div>      
              </div>
            </div>
            ';}
            $imodal++;
            if($row["Event"]=="IN"){
              echo '';
            }
            
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
    echo("Acces Denied.");
}
//AUTH Handling
session_start();

      /*if (isset($_POST["retrieveItem"])){//Tárgy visszahozása
        $serverName = "localhost";
        $dbUserName = "root";
        $dbPassword = "umvHVAZ%";
        $dbDatabase = "mediaio";

        $d = $_POST["retrieveItem"];
        $User = $_SESSION['UserUserName'];

        $conn = new mysqli($serverName, $dbUserName, $dbPassword, $dbDatabase); // FIRST, This database stores the currently used authCodes,
              if ($conn->connect_error){
                die("Connection failed: ".mysqli_connect_error());}
        echo $Item;

    $sql = ("UPDATE `leltar` SET `Status` = '1', `RentBy` = NULL, `AuthState` = NULL WHERE `leltar`.`Nev` = '$d';");
    $sql.= ("DELETE FROM authcodedb WHERE Item = '$d';");
    $sql.= ("INSERT INTO takelog (`ID`, `takeID`, `Date`, `User`, `Item`, `Event`) VALUES (NULL, '1', '$currDate', '$SESSuserName', '$d', 'IN')");
    if (!$conn->multi_query($sql)) {
      echo "Multi query fail!: (" . $conn->errno . ") " . $conn->error;
    }
    //else{}

      }*/
      if (isset($_POST["authItem"])){
        //echo 'Data recieved, process!'.$_POST["authItem"].'</br>';
        //echo 'Your AuthCode is'.$_POST["authGen"];
        $serverName = "localhost";
        $dbUserName = "root";
        $dbPassword = "umvHVAZ%";
        $dbDatabase = "mediaio";
        
        $authCode = $_POST["authGen"];
        $authItem = $_POST["authItem"];
        $SESSuserName = $_SESSION['UserUserName'];
            $conn = new mysqli($serverName, $dbUserName, $dbPassword, $dbDatabase); // FIRST, This database stores the currently used authCodes,
              if ($conn->connect_error){
                die("Connection failed: ".mysqli_connect_error());}
        //Check whether the specific code exists.
        $checkCodeRowNum = 1;
        while($checkCodeRowNum != 0){
          $codeCheckSQl = ("SELECT * FROM `authcodedb` WHERE `Code` = '$authCode'");
          $checkcoderes = $conn->query($codeCheckSQl);
          $checkCodeRowNum = $checkcoderes->num_rows;
          //echo $authCode.'The AuthCode did exist, generating a new keypair...<br>';
          $authCode = random_int(0,999999);
        }
        //Prevent Double Generation
        $itemCheckSQl = ("SELECT * FROM `authcodedb` WHERE `Item` = '$authItem'");
        $checkitemRes = $conn->query($itemCheckSQl);
        $checkItemRowNum = $checkitemRes->num_rows;
        if ($checkItemRowNum!=0){
          //echo "Desired Item Already In AuthCodeDB, can't generate a code for you. :( ";
          exit();
        }else{
        $sql=("INSERT IGNORE INTO authcodedb (`ID`,`Code`, `AuthBy`, `TakeID`, `Item`) VALUES (NULL, '$authCode', '$SESSuserName', '1', '$authItem')");
        //$sql=("INSERT INTO authcodedb (`ID`,`Code`, `AuthBy`, `AuthUser`, `TakeID`, `Item`)
        //SELECT * FROM (SELECT '$authItem') AS tmp
        //WHERE NOT EXISTS (
        //    SELECT name FROM authcodedb WHERE name = 'name1'
        //) LIMIT 1;");
        $result = $conn->query($sql);
        
        if ($result===TRUE){
          //echo"Success!! AuthCode given, and done.";
          $conn->close();
          $conn = new mysqli($serverName, $dbUserName, $dbPassword, 'mediaio'); // SECOND CONN, Sets status in master!
          $sql=("UPDATE leltar SET `AuthState` = 1 WHERE `leltar`.`Nev` = '$authItem'");
          $result = $conn->query($sql); if($result===TRUE){}
          $conn->close();
          
        }
        }
      }
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
        alert("Status: " + textStatus); alert("Hiba: " + errorThrown); 
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