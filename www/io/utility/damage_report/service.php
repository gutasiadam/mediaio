<?php
session_start();
//Access control
if (!isset($_SESSION["userId"])) {
    header("Location: ../index.php?error=AccessViolation");
    exit();
}
if(!(in_array("system", $_SESSION["groups"]) or in_array("admin", $_SESSION["groups"]))){
    header("Location: ../index.php?error=AccessViolation");
    exit();
}
error_reporting(E_ALL | E_WARNING | E_NOTICE);
    require_once("../../header.php");
   ?>
   <html>
<head>
  <link href='../../main.css' rel='stylesheet' />
  <div class="UI_loading"><img class="loadingAnimation" src="../mediaIO_loading_logo.gif"></div>
    <meta charset='utf-8' />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
  <script src="../../utility/_initMenu.js" crossorigin="anonymous"></script>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Arpad Media IO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
    $(window).on('load', function () {
      //console.log("Finishing UI");
      setInterval(() => {
        $(".UI_loading").fadeOut("slow");
      }, 200);
 });

         window.onload = function () {
          display = document.querySelector('#time');
          var timeUpLoc="../utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
  </script>
</head>
<?php if (isset($_SESSION["userId"])) { ?> <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="../../index.php">
    <img src="../../utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
      <script>
        $(document).ready(function() {
          menuItems = importItem("../../utility/menuitems.json");
          drawMenuItemsLeft('profile', menuItems,3);
        });
      </script>
    </ul>
    <ul class="navbar-nav navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=../../utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
    </form>
    <a class="nav-link my-2 my-sm-0" href="../../help.php">
      <i class="fas fa-question-circle fa-lg"></i>
    </a>
  </div>
</nav>

<div class="container">
  <div class="row">
    <div class="col-lg">
        <table class="table table-striped" id="serviceItemsTable">
          <thead class="thead-dark">
            <tr>
              <th scope="col">UID</th>
              <th scope="col">Név</th>
              <th scope="col">Művelet</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
    </div>
  </div>
  <div class="row">
    <div class="col-lg">
        <div class="spinner-border text-primary" id="loading" role="status">
        <span class="sr-only">Loading...</span>
        </div>
    </div>
  </div>
</div>



<?php  }
?>


<script>

    //On document load
    $(document).ready(function() {
        //Hide loading spinner
        $("#loading").hide();
        //Get service items
        $.ajax({
            url: "./damage-backend.php",
            type: "POST",
            data: {
                "method": "getServiceItems"
            },
            success: function(data) {
                //Parse JSON
                var items = JSON.parse(data);
                //Iterate through items
                for (var i = 0; i < items.length; i++) {
                    //Append row to table
                    $("#serviceItemsTable").append("<tr><th scope='row'>" + items[i].UID + "</th><td>" + items[i].name + "</td><td><button class='btn btn-success' onclick='repairItem("+'"'+items[i].UID+'"'+","+'"'+items[i].name+'"'+")'><i class='fas fa-check'></i></button></td></tr>");
                }
            }
        });
    });

    function repairItem(uid,itemName){
        //Show loading spinner
        $("#loading").show();
        //Get service items
        $.ajax({
            url: "./damage-backend.php",
            type: "POST",
            data: {
                "method": "returnServiceItem","UID":uid, "itemName":itemName
            },
            success: function(data) {
              console.log(data);
              if(data=='200'){
                //Remove row from tabe, where uid is the same as the returned item
                $("#serviceItemsTable tr").filter(function() {
                    return $(this).find("th").text() == uid;
                }).remove();
                $("#loading").hide();
              }else{
                alert("Hiba történt a tárgy visszaadása közben!");
              }
            }
        }); 
    }

</script>