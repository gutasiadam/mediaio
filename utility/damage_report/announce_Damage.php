<?php
namespace Mediaio;
require_once __DIR__.'/../../Mailer.php';
require_once __DIR__.'/../../Database.php';
use Mediaio\MailService;
use Mediaio\Database;
error_reporting(E_ALL ^ E_NOTICE);
session_start();
$TKI = $_SESSION['UserUserName'];  
?>

<!--Hibabejelentő űrlap, tartalmazza:
- A tárgy nevét, amivel gond van
- A bejelentő nevét
- Képfeltöltés lehetőségét
- Hiba leírását
-->
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
      console.log("Finishing UI");
      setInterval(() => {
        $(".UI_loading").fadeOut("slow");
      }, 200);
 });
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
</nav> <?php  } ?> 
<div class="contianer">
  <div class="row" style="width: 80%; margin: 0 auto;">
  <div class="col-sm">
  <?php
  echo "<form>";
    if (in_array("admin", $_SESSION["groups"])){
      $sql = "SELECT usernameUsers FROM `users`";
      $result = Database::runQuery($sql);
      //Create a HTML dropdown selector

      echo "<select id='userNameSelector' name='user'>";
      echo "<option value='NULL'>Válassz felhasználót.</option>";
      while ($row = mysqli_fetch_array($result)) {
        echo "<option value='" . $row['usernameUsers'] . "'>" . $row['usernameUsers'] . "</option>";
      }
      echo "</select>";

      echo "<select id='userItemSelector' name='userItems'></select>";
      

    }else{
      echo "<select id='userNameSelector' name='user'>";
       echo "<option value='".$_SESSION['UserUserName']."'>".$_SESSION['UserUserName']."</option>";
      echo "</select>";

      echo "<select id='userItemSelector' name='userItems'></select>";

    }
    echo "</div>";
    echo "<div class='col-sm'>";
    //Create a HTML form long text input area
    echo "<textarea name='description' placeholder='Hiba leírása' rows='4' cols='50'></textarea>";
    //Create a FORM input that allows multiple images
    echo "<input type='file' name='file[]' multiple='multiple' accept='image/*'>";
    echo "<input type='submit' name='submit' value='Bejelentés'>";
echo "</form>";
  ?>
  </div>
  </div>
</div>

<script>
  //When the dropdown selector changes, send a request to the server to get the user's data
  $(document).ready(function() {

    $("#userNameSelector").change(function() {
      getItemForSelectedUser();
    });

    function getItemForSelectedUser(){
      var userName = $("#userNameSelector").val();
      $.ajax({
        type: "POST",
        url: "damage-backend.php",
        data: {
          userName: userName,
          method: "get_user_items"
        },
        success: function(data) {
          console.log(data);
          //For each item in the returned JSON, create an option in the dropdown selector
          var userItems = JSON.parse(data);
          $("#userItemSelector").empty();
          for (var i = 0; i < userItems.length; i++) {
            $("#userItemSelector").append(
              "<option value='" +userItems[i].UID +"'>" + userItems[i].UID+" - "+userItems[i].Nev +"</option>"
            );
          }
        },
      });
    }
  });
  //When the submit button is clicked, send the form data and the uploaded images to the server
  $("form").submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append("method", "announceDamage");
    console.log(formData);
    $.ajax({
      url: "damage-backend.php",
      type: "POST",
      data: formData,
      success: function(data) {
        console.log(data);
        if (data == 200) {
          alert("Sikeres bejelentés!");
        } else {
          alert("Hiba történt a bejelentés során!");
        }
      },
      cache: false,
      contentType: false,
      processData: false
    });
  });
</script>
