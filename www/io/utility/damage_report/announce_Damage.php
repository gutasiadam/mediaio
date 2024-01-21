<?php
namespace Mediaio;

require_once __DIR__ . '/../../Mailer.php';
require_once __DIR__ . '/../../Database.php';
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
  <link href='../../style/common.scss' rel='stylesheet' />
  <div class="UI_loading"><img class="loadingAnimation" src="../mediaIO_loading_logo.gif"></div>
  <meta charset='utf-8' />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
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
  </script>
</head>

<body>
  <?php if (isset($_SESSION["userId"])) { ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="../index.php">
        <img src="../logo2.png" height="50">
      </a>

      <!-- Load Menu and Index table Icons and links -->
      <script type="text/javascript">
        window.onload = function () {

          menuItems = importItem("../menuitems.json");
          drawMenuItemsLeft('profile', menuItems, 3);

          drawMenuItemsRight('profile', menuItems, 3);

          display = document.querySelector('#time');
          var timeUpLoc = "../userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
      </script>

      <!-- Mobile Navigation - Additional toggle button -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Main Navigation -->
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto navbarUl">
        </ul>
        <ul class="navbar-nav ms-auto navbarPhP">

          <!-- Timeout timer -->
          <li><a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span>
              <?php echo ' ' . $_SESSION['UserUserName']; ?>
            </a></li>
        </ul>

        <!-- User logout button -->
        <form method='post' class="form-inline my-2 my-lg-0" action=../userLogging.php>
          <button class="btn btn-danger my-2 my-sm-0" id="logoutBtn" name='logout-submit'
            type="submit">Kijelentkezés</button>
        </form>

      </div>
    </nav>
  <?php } else {
    echo "Ehhez a funkcióhoz be kell jelentkezned!";
    exit();
  } ?>


  <h3 class="rainbow">Probléma bejelentése</h3>

  <div class="contianer" id="damage_report">
    <div class="row">
      <div class="col">
        A bejelentés beküldése után a tárgy átkerül a szervízhez, nem fogod tudni visszahozni azt. Meg fog
        keresni majd egy vezetőségi tag.
      </div>
    </div>


    <form id='damageReportForm'>
      <div class="row">

        <?php
        if (in_array("admin", $_SESSION["groups"])) {
          $sql = "SELECT usernameUsers FROM `users`";
          $result = Database::runQuery($sql);
          //Create a HTML dropdown selector
        
          echo "<div class='col'>";

          echo "<div class='form-floating'>";
          echo "<select class='form-select' id='userNameSelector' name='user'>";
          echo "<option value='NULL'>Válassz felhasználót!</option>";
          while ($row = mysqli_fetch_array($result)) {
            echo "<option value='" . $row['usernameUsers'] . "'>" . $row['usernameUsers'] . "</option>";
          }

          echo "</select>";
          echo "<label for='userNameSelector'>Felhasználó:</label>";
          echo "</div>";
          echo "</div>";


        } else {
          //User can only select their own items
          echo "<div class='col'>";
          echo "<div class='form-floating'>";
          echo "<select class='form-select' id='userNameSelector' name='user'>";
          echo "<option value='" . $_SESSION['UserUserName'] . "'>" . $_SESSION['UserUserName'] . "</option>";

          echo "</select>";
          echo "<label for='userNameSelector'>Felhasználó:</label>";
          echo "</div>";
          echo "</div>";

        }
        ?>
      </div>
      <div class='row'>
        <div class='col'>
          <div class='form-floating'>
            <select class='form-select' id='userItemSelector' name='userItems'></select>
            <label for='userItemSelector'>Hibás tárgy:</label>
          </div>
        </div>
      </div>

      <!-- Create a HTML form long text input area -->
      <div class="row">
        <div class="col">
          <div class="form-floating">
            <textarea class="form-control" placeholder="Hiba leírása" id="floatingTextarea"
              style="height: 100px"></textarea>
            <label for="floatingTextarea">Hiba leírása...</label>
          </div>
        </div>
      </div>
      <!-- Create a FORM input that allows multiple images -->
      <div class="row">
        <div class="col">
          <input class="form-control" type="file" id="file" name='upFile' accept='image/*'>
        </div>
      </div>
      <!-- Submit button -->
      <div class="row">
        <div class="col" id="submit">
          <button class='btn btn-sm btn-success' type='submit' name='submit'>Bejelentés</button>
        </div>
      </div>

    </form>

    <div class="row">
      <div class="uploadedImages">
        <h6>Eddig feltöltött képek:</h6>
      </div>
      <p id='folder'></p>
    </div>
  </div>
</body>


<script>
  //When the dropdown selector changes, send a request to the server to get the user's data
  $(document).ready(function () {

    $('input[type=file]').on('change', function () {

      var uploadedImagesCount = 0;
      var $files = $(this).get(0).files;

      if ($files.length) {
        // Reject big files
        if ($files[0].size > $(this).data('max-size') * 1024) {
          console.log('Please select a smaller file');
          return false;
        }

        // Begin file upload
        console.log('Uploading file to Host.');
        var settings = {
          // async: false,
          // crossDomain: true,
          processData: false,
          contentType: false,
          cache: false,
          type: 'POST',
          url: './upload-handler.php',
          mimeType: 'multipart/form-data',
        };

        var formData = new FormData();
        formData.append('upFile', $files[0]);
        settings.data = formData;
      }

      console.log('Settings ok');

      // Response contains stringified JSON
      // Image URL available at response.data.link
      $.ajax(settings).done(function (response) {
        //convert response to json
        //Append an image to the uploadedImages div with the url being the response
        uploadedImagesCount++;
        var img = $('<img />', {
          id: 'img_' + uploadedImagesCount,
          src: "/../../uploads/images/" + response,
          alt: 'Uploaded Image' + uploadedImagesCount++,
          width: '100px',
        });
        img.appendTo($('.uploadedImages'));
        // $("#folder").text("Képek mappája: "+response);

      });
    });

    $("#userNameSelector").change(function () {
      getItemForSelectedUser();
    });

    function getItemForSelectedUser() {
      var userName = $("#userNameSelector").val();
      $.ajax({
        type: "POST",
        url: "damage-backend.php",
        data: {
          userName: userName,
          method: "get_user_items"
        },
        success: function (data) {
          console.log(data);
          //For each item in the returned JSON, create an option in the dropdown selector
          var userItems = JSON.parse(data);
          $("#userItemSelector").empty();
          for (var i = 0; i < userItems.length; i++) {
            $("#userItemSelector").append(
              "<option value='" + userItems[i].UID + "'itemName='" + userItems[i].Nev + "'>" + userItems[i].UID + " - " + userItems[i].Nev + "</option>"
            );
          }
        },
      });
    }
  });
  //When the submit button is clicked, send the form data and the uploaded images to the server
  $("#damageReportForm").submit(function (e) {

    var zipfile = '';
    e.preventDefault();
    var formData = new FormData(this);
    formData.append("method", "announceDamage");
    $.ajax({
      type: 'POST',
      url: './upload-handler.php',
      data: { uploadComplete: true },
      success: function (response) {
        console.log("zip-file:" + response);
        $('.uploadedImages h6').html('<h6>A képek feltöltése kész. Letöltheted a képeket: <a href="../../uploads/images/' + response + '.zip">Letöltés</a></h6>');
        formData.append("zip-file", response);

        //Add selected options itemName attribute to the form data
        formData.append("itemName", $("#userItemSelector option:selected").attr("itemName"));
        console.log("zip-file0:" + response);
        zipfile = response;
        //Perform mail send

        $.ajax({
          url: "damage-backend.php",
          type: "POST",
          data: formData,
          success: function (data) {
            console.log(data);
            if (data == 200) {
              $('#folder').text("Sikeres bejelentés!");
            } else {
              $('#folder').text("Hiba történt a bejelentés során!");
            }
          },
          cache: false,
          contentType: false,
          processData: false
        });
      }
    });



  });
</script>