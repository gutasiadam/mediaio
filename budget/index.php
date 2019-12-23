<?php 
    require("../header.php");
        session_start();
        if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
            $connect = new PDO("mysql:host=localhost;dbname=budget", "root", "umvHVAZ%");
            echo '
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
					<a class="navbar-brand" href="index.php">Arpad Media IO</a>
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
						<li class="nav-item ">
						    <a class="nav-link" href="../retrieve.php"><i class="fas fa-download fa-lg"></i></a>
						</li>
            <li class="nav-item">
						    <a class="nav-link" href="../adatok.php"><i class="fas fa-database fa-lg"></i></a>
						</li>
            <li class="nav-item">
                        <a class="nav-link" href="../pathfinder.php"><i class="fas fa-project-diagram fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        <a class="nav-link" href="../events/"><i class="fas fa-calendar-alt fa-lg"></i></a>
            </li>
            <li class="nav-item active">
                        <a class="nav-link" href="../profile"><i class="fas fa-user-alt fa-lg"></i></a>
            </li>
            <li>
              <a class="nav-link disabled" href="#">Időzár <span id="time">10:00</span></a>
            </li>';
            if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';}
            echo '</ul>
						<form class="form-inline my-2 my-lg-0" action=../utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form>
                      <a class="nav-link my-2 my-sm-0" href="../help.php"><i class="fas fa-question-circle fa-lg"></i></a>
					</div>
		</nav>';
        }else{
            header("Location: ../index.php?error=AccessViolation");
            exit();
        }
    ?>
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Arpad Media IO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
                <h1 align=center>Költségvetés</br><button class="btn btn-warning" data-toggle="modal" data-target="#budgetModal">Bevétel/Kiadás hozzáadása</button></h1>
                <table class="budget_table">
                <tr><td><h3>Bevételek</h3></td>
                <td><h3>Kiadások</h3></td></tr>
                <tr>
                <td><table class="income_table money_table"><tr>
                <?php 
                $query = "SELECT * FROM `main_budget` WHERE `Type` = 'INC' ORDER BY `Date` DESC";
                $statement = $connect->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
                foreach($result as $row){
                    echo '<tr><td><h3 class="text text-success">+'.$row["Amount"].' Ft</h3><h5>'.$row["Description"].'</h5></td></tr>';
                }
                ?>
                </tr></table></td>
                <td><table class="expense_table money_table "><tr>
                <?php 
                $query = "SELECT * FROM `main_budget` WHERE `Type` = 'EXP' ORDER BY `Date` DESC";
                $statement = $connect->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
                foreach($result as $row){
                    echo '<tr><td><h3 class="text text-danger">-'.$row["Amount"].' Ft</h3><h5>'.$row["Description"].'</h5></td></tr>';
                }
                ?>
                </tr></table></td>
                </tr></table>
                <?php 
                $query = "SELECT * FROM `main_budget` ORDER BY `Date` DESC";
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
                echo '<h4 class="finalValue">Megmaradt összeg: '.$TotalMoney.' Ft</h4>';
                $connect=null;
                ?>
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
    <select class="form-control" id="budgetTypeSelect">
      <option value="INC">Bevétel</option>
      <option value="EXP">Kiadás</option>
    </select>
        <input class="form-control" id="budgetName" type="text" placeholder="Bevétel/Kiadás címe"></input>
        <input class="form-control" id="budgetValue" type="number" placeholder="Érték"></input>
        <input type="hidden" id="userName" value=<?php echo $_SESSION["UserUserName"];?>></input>
      </div>
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
$.ajax({
       url:"budgetHandler.php",
       type:"POST",
       data:{bType:bType, bName:bName, bVal:bVal, bUser:bUser},
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
    var fiveMinutes = 10 * 60 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
};
</script>

<style>
.budget_table{
  width: 95%;
  border-style: solid;
  text-align: center;
  margin: 0 auto; 
}

.finalValue{
  width: 95%;
  text-align: left;
  margin: 0 auto; 
}

.money_table{
  padding-left: 5px;
  margin-left: 20px;
}
#unavailable{
  font-size:18px;
  color: red;
}
</style>