<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header("Location: ../index.php?error=AccessViolation");
    exit();
}
include("header.php");
include("../translation.php");

?>
<html>

<?php if (in_array("admin", $_SESSION["groups"])) { ?>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="../index.php">
            <img src="../utility/logo2.png" height="50">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto navbarUl">
                <script>
                    $(document).ready(function () {
                        menuItems = importItem("../utility/menuitems.json");
                        drawMenuItemsLeft('forms', menuItems, 2);
                    });
                </script>
            </ul>
            <ul class="navbar-nav ms-auto navbarPhP">
                <li>
                    <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span>
                        <?php echo ' ' . $_SESSION['UserUserName']; ?>
                    </a>
                </li>
            </ul>
            <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
                <button id="logoutBtn" class="btn btn-danger my-2 my-sm-0 logout-button" name='logout-submit'
                    type="submit">Kijelentkezés</button>
                <script type="text/javascript">
                    window.onload = function () {
                        display = document.querySelector('#time');
                        var timeUpLoc = "../utility/userLogging.php?logout-submit=y"
                        startTimer(display, timeUpLoc);
                    };
                </script>
            </form>
        </div>
    </nav>

    <body>
        <h2 class="rainbow" id="form_name"></h2>
        <div class="container" id="form-body">

        </div>

    </body>


    <script>
        $(document).ready(function () {
            //Load form from server
            console.log(<?php echo $_GET['formId'] ?>);
            $.ajax({
                type: "POST",
                url: "../formManager.php",
                data: { mode: "viewForm", id: <?php echo $_GET['formId'] ?> },
                success: function (data) {
                    console.log(data);
                    //if data is 404, redirect to index.php
                    if (data == 404) {
                        window.location.href = "index.php?invalidID";
                    }
                    var form = JSON.parse(data);
                    var formElements = JSON.parse(form.Data);
                    console.log(formElements);
                    var formName = form.Name;
                    //Set form Name
                    document.getElementById("form_name").innerHTML = formName;

                    formContainer = document.getElementById("form-body");
                    //Load form elements
                    for (var j = 0; j < formElements.length; j++) {
                        var element = formElements[j];
                        console.log(element);
                        var elementType = element.type;
                        var elementId = element.id;
                        var elementSettings = element.settings;
                    
                        

<?php } ?>

</html>