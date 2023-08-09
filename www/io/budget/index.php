<?php 

    include ("../profile/header.php");
        session_start();
        if (in_array("teacher", $_SESSION["groups"]) or in_array("system", $_SESSION["groups"])){
            $connect = new PDO("mysql:host=localhost;dbname=mediaio","root", "umvHVAZ%");?>
<?php if (isset($_SESSION["userId"])) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="../utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
      <script>
        $(document).ready(function() {
          menuItems = importItem("../utility/menuitems.json");
          drawMenuItemsLeft('profile', menuItems,2);
        });
      </script>
    </ul>
    <ul class="navbar-nav navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
    </form>
    <a class="nav-link my-2 my-sm-0" href="./help.php">
      <i class="fas fa-question-circle fa-lg"></i>
    </a>
  </div>
</nav> <?php  } ?><?php
        }else{
            header("Location: ../index.php?error=AccessViolation");
            exit();
        }
        echo '<div class="printonly">'.date("Y/m/d").'</div>';
    ?>

<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Arpad Media IO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class="date" style="margin: 0 auto; padding-top: 50px;  text-align: center;"><div id="date"><?php echo date("Y/m/d"); ?></div></div>
<div style="margin: 0 auto; padding-top: 50px;  text-align: center;"><button class="btn btn-warning noprint" data-toggle="modal" data-target="#budgetModal">Tétel hozzáadása</button></div>
<div class="row">
<div class="col-sm-6">
<h1 align=center>Médiás költségvetés</br></h1>
  <table class="budget_table">
    <!--<tr><td class="tdTitle"><h3>Bevételek</h3></td>-->
    <tr>
      <td><table class="income_table money_table"><tr>
        <?php 
                $query = "SELECT * FROM `main_budget` WHERE (budget_type='m') ORDER BY `Year` DESC, `Month` DESC, `Day` DESC";
                $statement = $connect->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
                foreach($result as $row){
                  if ($row["Type"]=="EXP"){
                    echo '<tr><td style="text-align: center;"><h3 class="text text-danger entry" style="margin-bottom: 0px;">-'.$row["Amount"].' Ft</h3><h5 style="margin-bottom: 10px;"><strong>'.$row["Year"].'/'.$row["Month"].'/'.$row["Day"].'</strong> '.$row["Description"].' '.$row["addedBy"].'</h5></td></tr>'; //' '.$row["budget_type"].
                  }
                  else{
                    echo '<tr><td style="text-align: center;"><h3 class="text text-success entry" style="margin-bottom: 0px;">+'.$row["Amount"].' Ft</h3><h5 style="margin-bottom: 10px;"><strong>'.$row["Year"].'/'.$row["Month"].'/'.$row["Day"].'</strong> '.$row["Description"].' '.$row["addedBy"].'</h5></td></tr>'; //' '.$row["budget_type"].  
                  }
                }
                ?>
                </tr></table></td>
                
              </tr></table>
              <?php 
                $query = "SELECT * FROM `main_budget` WHERE budget_type='m' ORDER BY `Year` DESC, `Month` DESC, `Day` DESC";
                $statement = $connect->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
                $TotalMoney=0;
                foreach($result as $row){
                  if ($row["Type"]=="EXP"){
                    $row["Amount"]=$row["Amount"]*-1;
                  }
                  $TotalMoney += $row["Amount"];
                }
                echo '<h3 class="finalValue">Összesen: '.$TotalMoney.' Ft</h3>';
                ?>
                </div>
                
                
<div class="col-sm-6">
<h1 align=center>Egyesületi költségvetés</br></h1>
                <table class="budget_table">
                <tr>
                <td><table class="money_table"><tr>
                <?php 
                $query = "SELECT * FROM `main_budget` WHERE (budget_type='s') ORDER BY `Year` DESC, `Month` DESC, `Day` DESC";
                $statement = $connect->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
                foreach($result as $row){
                  if ($row["Type"]=="EXP"){
                    echo '<tr><td style="text-align: center;"><h3 class="text text-danger entry" style="margin-bottom: 0px;">-'.$row["Amount"].' Ft</h3><h5 style="margin-bottom: 10px;"><strong>'.$row["Year"].'/'.$row["Month"].'/'.$row["Day"].'</strong> '.$row["Description"].' '.$row["addedBy"].'</h5></td></tr>'; //' '.$row["budget_type"].
                  }
                  else{
                    echo '<tr><td style="text-align: center;"><h3 class="text text-success entry" style="margin-bottom: 0px;">+'.$row["Amount"].' Ft</h3><h5 style="margin-bottom: 10px;"><strong>'.$row["Year"].'/'.$row["Month"].'/'.$row["Day"].'</strong> '.$row["Description"].' '.$row["addedBy"].'</h5></td></tr>'; //' '.$row["budget_type"].  
                  }
                }
                ?>
                </tr></table></td>
                </tr></table>
                <?php 
                $query = "SELECT * FROM `main_budget` WHERE budget_type='s' ORDER BY `Year` DESC, `Month` DESC, `Day` DESC";
                $statement = $connect->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
                $TotalMoney=0;
                foreach($result as $row){
                    if ($row["Type"]=="EXP"){
                        $row["Amount"]=$row["Amount"]*-1;
                    }
                    $TotalMoney += $row["Amount"];
                }
                echo '<h3 class="finalValue">Összesen: '.$TotalMoney.' Ft</h3>';
                $connect=null;
                ?>
                </div>

<style>
.two_col {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  grid-auto-rows: 50%;
  height: 100vh;
}
</style>

<!--Temporary development modal-->
<div class="modal fade" id="WIPModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Fejlesztés alatt</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h5><p>Kedves felhasználó!</p></h5>
        <p>Az oldal feljesztése folyamatban van, kérlek ne használd ezt a funkciót.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Bezárás</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="budgetModal" tabindex="-1" role="dialog" aria-labelledby="budgetModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Költségvetés hozzádása</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form id="sendBudgetForm">
      <div class="form-group">
      <input autocomplete="off" id="datepicker" class="form-control" type="text" placeholder="Dátum" required></input></div>
      <div class="form-group">
      <label class="form-check-label" for="budgetTypeSelect1">Típus</label>
      <select class="form-control" id="budgetTypeSelect" required>
      <option value="" selected disabled hidden>Válassz a legördülő menüből...</option>
      <option value="INC">Bevétel</option>
      <option value="EXP">Kiadás</option>
    </select></div>
    <div class="form-group"><input autocomplete="off" class="form-control" id="budgetName" type="text" placeholder="Bevétel/Kiadás címe" required></input></div>
    <div class="form-group"><input autocomplete="off" value='<?php echo $_SESSION['UserUserName'];?>' class="form-control" id="userName" type="text" placeholder='none' hidden required></input></div>
    <div class="form-group"><input autocomplete="off" class="form-control" id="budgetValue" type="number" placeholder="Érték" required></input></div>
    <label class="form-check-label" for="budgetTypeSelect2">Kassza</label>
    <select class="form-control" id="kassza" required>
      <option value="" selected disabled hidden>Válassz a legördülő menüből..</option>
      <option value="m">Médiás</option>
      <option value="s">Egyesületi</option>
    </select></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
        <input type="submit" id="sendBudget" class="btn btn-primary" value="Küldés"></input>
        </form>
      </div>
    </div>
  </div>
</div>
</html>
<script>
$('#sendBudgetForm').on('submit', function (e) {
e.preventDefault();
$('#budgetModal').modal('hide');
var bType = $( "#budgetTypeSelect").val();
var bName = document.getElementById('budgetName').value;
var bVal = document.getElementById('budgetValue').value;
var bUser = document.getElementById('userName').value;
var bDate = document.getElementById('datepicker').value;
var bKassza = $( "#kassza").val();
$.ajax({
       url:"budgetHandler.php",
       type:"POST",
       data:{bType:bType, bName:bName, bVal:bVal, bUser:bUser, bDate:bDate, bKassza:bKassza},
       success:function(successNum){
        if(successNum == 1){ // if true (1)
      setTimeout(function(){// wait for 5 secs(2)
           location.reload(); // then reload the page.(3)
      }, 100); 
   }
       },
       error: function(jqXHR, textStatus, errorThrown){
          alert('error');
      } 
      })

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
   $('#WIPModal').modal();
    var fiveMinutes = 10 * 60 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
};
$( document ).ready(function() {
  $('#datepicker').datepicker({
    format: "yyyy/mm/dd",
    uiLibrary: 'bootstrap',
            weekStart: 1,
            clearBtn: true,
            language: "hu",
            autoclose: true
});
});

</script>

<style>

.printonly {
  display: none;
}

@media print{
  .printonly * {
    display: inline;
    background: red;
  }
  .noprint {
    display: none;
  }
}

.budget_table{
  max-width: 1000px;
  border-style: solid;
  text-align: center;
  margin: 0 auto; 
}

.finalValue{
  /*padding-top: 25px;
  padding-left: 20px;
  width: 500px;*/
  text-align: center;
  margin: 0 auto; 
}

.money_table{
  padding-left: 5px;
  margin: 0 auto;
}

.entry{
  /* A bemeneti kiadások/bevételek classja*/
}

#unavailable{
  font-size:18px;
  color: red;
}
</style>