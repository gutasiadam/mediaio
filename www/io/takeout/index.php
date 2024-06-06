<?php

namespace Mediaio;

session_start();

include "header.php";
// Set timezone
date_default_timezone_set('Europe/Budapest');

if (!isset($_SESSION["userId"])) {
    echo "<script>window.location.href = '../index.php?error=AccessViolation';</script>";
    exit();
}


error_reporting(E_ALL ^ E_NOTICE);
?>

<body style="user-select: none;">
    <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
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
                        drawMenuItemsLeft('takeout', menuItems, 2);
                    });
                </script>
            </ul>
            <ul class="navbar-nav ms-auto navbarPhP">
                <li>
                    <a class="nav-link disabled timelock" href="#"><span id="time"> 30:00 </span>
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
                        startTimer(display, timeUpLoc, 30);
                    };
                </script>
            </form>
        </div>
    </nav>

    <?php include "modals.php"; ?>


    <ul class="nav nav-underline mt-1 mb-2" id="selectMenu" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" href="" id="takeout-tab" data-bs-toggle="tab" data-bs-target="#takeout-tab-pane"
                aria-current="page">
                <h2 class="rainbow" id="takeoutPage_Title" style="font-size: 40px; margin: 0;">Elvitel</h2>
            </a>
        </li>
        <h2 class="rainbow"> - </h2>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="" onclick="loadTakeOutPlanner();" id="prepared-tab" data-bs-toggle="tab"
                data-bs-target="#prepared-tab-pane">
                <h2 class="rainbow" id="reservationsPage_Title" style="margin: 0; font-size: 40px;">Előjegyzések</h2>
            </a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="takeout-tab-pane" role="tabpanel" aria-labelledby="takeout-tab"
            tabindex="0">
            <div class="container" id="takeOutContainer">
                <div class="row align-items-start" id="takeout-container">
                    <div class="col-4 selectedList" id="selected-desktop">
                        <h3>Kiválasztva:</h3>
                    </div>
                    <div class="col">
                        <div class="input-group mb-1">
                            <input type="text" class="form-control" id="search"
                                placeholder="Kezdd el ide írni, mit vinnél el.."
                                aria-label="Kezdd el ide írni, mit vinnél el.." aria-describedby="button-addon2">

                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                aria-expanded="false">Szűrés</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="dropdown-item">
                                        <input class="form-check-input filterCheckbox" type="checkbox"
                                            autocomplete="off" id="show_medias" data-filter="">
                                        <label class="form-check-label" for="show_medias">Médiás</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input class="form-check-input filterCheckbox" type="checkbox"
                                            autocomplete="off" id="show_studios" data-filter="s">
                                        <label class="form-check-label" for="show_studios">Stúdiós</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input class="form-check-input filterCheckbox" type="checkbox"
                                            autocomplete="off" id="show_eventes" data-filter="e">
                                        <label class="form-check-label" for="show_eventes">Eventes</label>
                                    </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input class="form-check-input filterCheckbox" type="checkbox"
                                            autocomplete="off" id="show_unavailable" data-filter="">
                                        <label class="form-check-label" for="show_unavailable">Csak elérhető</label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div id="takeout-option-buttons">
                            <button href="#sidebar" class="btn btn-sm btn-success mb-1" id="show_selected"
                                data-bs-toggle="offcanvas" role="button" aria-controls="sidebar">Folytatás
                                <span id="selectedCount" class="badge bg-danger">0</span>
                            </button>
                            <button class="btn btn-sm btn-success col-lg-auto mb-1" id="takeout2BTN"
                                style='margin-bottom: 6px' data-bs-target="#takeoutSettingsModal"
                                data-bs-toggle="modal">Mehet</button>

                            <button class="btn btn-sm btn-info col-lg-auto mb-1" onclick="showPresetsModal()"
                                style='margin-bottom:6px'>Presetek</button>
                            <button class="btn btn-sm btn-danger col-lg-auto mb-1 text-nowrap" id="clear"
                                style='margin-bottom: 6px' data-bs-target="#clear_Modal" data-bs-toggle="modal">Összes
                                törlése</button>
                            <button type="button" class="btn btn-warning btn-sm col-lg-auto mb-1 text-nowrap"
                                onclick="showScannerModal()">Szkenner <i class="fas fa-qrcode"></i></button>
                            <!-- Dropdown -->
                                <button class="btn btn-sm btn-warning col-lg-auto mb-1 text-nowrap" type="button"
                                    id="userReservations_selectorButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Előjegyzéseid
                                </button>
                                <ul class="dropdown-menu" id ="userReservations_selectorList" aria-labelledby="userReservations_selectorButton">
                                </ul>

                        </div>
                        <div id="itemsList">
                        </div>
                    </div>


                    <!-- Offcanvas -->
                    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebar-label">
                        <div class="offcanvas-header">
                            <h4 class="offcanvas-title" id="sidebar-label">Kiválasztva</h4>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body" id="sidebar-body">
                            <div class="row">
                                <div class="col-12 selectedList" id="offcanvasList">
                                </div>
                                <button class="btn btn-sm btn-success col-lg-auto mb-1" data-bs-dismiss="offcanvas"
                                    id="takeout2BTN-mobile" data-bs-target="#takeoutSettingsModal"
                                    data-bs-toggle="modal">Mehet</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navigation back to top -->
                <div id='toTop'><i class="fas fa-chevron-up"></i></div>
            </div>
        </div>
        <div class="tab-pane fade" id="prepared-tab-pane" role="tabpanel" aria-labelledby="prepared-tab" tabindex="0">
            <div class="container" id="preparedContainer">
                <div id='calendar'></div>
            </div>
        </div>
    </div>

</body>

<script>
    // Add event listener to each tab
    document.querySelectorAll('.nav-link').forEach(function (tab) {
        tab.addEventListener('click', function () {
            // Store the id of the clicked tab in local storage
            localStorage.setItem('activeTab', this.id);
        });
    });

    // On page load
    function restoreActiveTab() {
        // Check if there is a tab id stored in local storage
        var activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            if (activeTab == "prepared-tab") {
                loadTakeOutPlanner();
            }
            // If there is, activate the tab with that id
            var tab = document.getElementById(activeTab);
            var bsTab = new bootstrap.Tab(tab);
            bsTab.show();
        }
    };


    //Selected items badge counter
    var badge = document.getElementById("selectedCount");

    $(document).ready(function () {

        restoreActiveTab();
        loadPage();



        $('#itemsList').scroll(function () {
            if ($(this).scrollTop()) {
                $('#toTop').fadeIn();
            } else {
                $('#toTop').fadeOut();
            }
        });

        $("#toTop").click(function () {
            $("#itemsList").animate({
                scrollTop: 0
            }, 700);
        });

        //Preventing double click zoom
        document.addEventListener('dblclick', function (event) {
            event.preventDefault();
        }, { passive: false });


    });

    //Loads reservation items from the database, and selects them
    async function loadReservation(reservationProject) {
        const response = await $.ajax({
            url: "../ItemManager.php",
            method: "POST",
            data: {
                mode: "listTakeoutItems",
                eventID: reservationProject,
            }
        });


        if (response == 404) {
            errorToast("Nem található ilyen előjegyzés!");
        }
        if (response== 403) {
            errorToast("Nincs jogosultságod ennek a tartalomnak a megtekintéséhez!");
        }
        else {
    const items = JSON.parse(response);
    Promise.all(items.map(item => {
        return new Promise((resolve, reject) => {
            selectorItem = {
                UID: item.uid,
                Nev: item.name,
            };
            toggleSelectItem(selectorItem);
            resolve();
        });
    }));

    //Change the title of the menu to the reservations's name.
    loadReservationData(reservationProject,"get").then((reservations)=>{
        for (project of reservations){
            if(project["ID"]==reservationProject){
                document.getElementById("takeoutPage_Title").innerText = project["Name"];
            }
        }

        document.getElementById("takeout2BTN").innerText = "Előjegyzés módosítása";
    //Pre-fill takeoutsettingsmodal
    document.getElementById("plannedName").value = project["Name"];
    picker.setDate(project["StartTime"]);
    picker.setEndDate(project["ReturnTime"]);
    document.getElementById("plannedDesc").value = project["Description"];

    //Change the button's behaviour to update the reservation
    document.getElementById("submitTakeoutButton").setAttribute("onclick","updateTakeout()");

    });


}
    }

    async function loadPage() {
        const urlParams = new URLSearchParams(window.location.search);
        const reservationProject = urlParams.get('reservationProject');
        if (reservationProject) {
            cookiesEnabled = false;
        }
        //Load items
        await loadItems();
        //Load selected items
        await loadTooltips();

        await loadReservationData();

        if (reservationProject) {
            loadReservation(reservationProject);
            //disable cookie updates 
        }
        //check for reservation variable in URL


    }


    //Obtains a list of ID's from the server to which ID's the user has access to
    async function loadReservationData(id=-1,method="fillTable") {
        const response = await $.ajax({
            url: "../ItemManager.php",
            method: "POST",
            data: {
                mode: "listReservationData",
                id: id
            }
        });

        //for each returned ID, add button with the speifig id as a href to userReservations_selectorButton

        reservations= JSON.parse(response);

        if(method=="get"){
            return reservations;
        }

        for (project of JSON.parse(response)){
            var link = document.createElement("a");
            link.classList.add("dropdown-item");

            link.innerText=project["Name"];
            link.href = "./?reservationProject="+project["ID"];
            var listItem = document.createElement("li");
            listItem.appendChild(link);
            document.getElementById("userReservations_selectorList").appendChild(listItem);

        }


    }

    // Load tooltips
    async function loadTooltips() {
        //Load tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    //Updates an existing reservation
    async function updateTakeout(){
        takeoutItems = getTakeoutItemsArray();

        const urlParams = new URLSearchParams(window.location.search);
        const reservationProject = urlParams.get('reservationProject');

        // Create an ajax request to update the reservation
        const response = await $.ajax({
            url: "../ItemManager.php",
            method: "POST",
            data: {
                mode: "change_Takeout",
                items: JSON.stringify(takeoutItems),
                id: reservationProject,
                newProjectName: document.getElementById("plannedName").value,
                newStartTime: formatDateTime(picker.getStartDate()),
                newEndTime: formatDateTime(picker.getEndDate()),
                newDescription: document.getElementById("plannedDesc").value,
            }
        });

        if (response == 200) {
            //Get the returned output
            successToast("Sikeres módosítás!");
            $('#takeoutSettingsModal').modal('hide');

            //Wait for the modal to close
            setTimeout(() => {
                //Redirect to the main page
                window.location.href = ".";
            }, 1000);
        }else if (response == 410) {
            warningToast("Nem történt változás")
        }else {
            console.log(response);
            console.log(response==200);
            errorToast("Valami hiba történt a módosítás során!");
        }


    }

    function getTakeoutItemsArray() {
        const selectedItems = document.getElementsByClassName("selected");

    if (selectedItems.length == 0) {
        errorToast("Nincs kiválasztva semmi!");
        $('#takeoutSettingsModal').modal('hide');
        setTimeout(() => {
            document.getElementById("search").focus();
        }, 500);
        return;
    }

    return Array.from(document.getElementsByClassName("selected")).map(item => ({
            //id: item.getAttribute("data-main-id"),
            uid: item.id,
            name: item.getAttribute("data-name"),
        }));
    }

    //Formats the date to a format that can be stored in the database
    function formatDateTime(date) {
            let d = new Date(date);
            let timezoneOffset = d.getTimezoneOffset() * 60000; // Get timezone offset in milliseconds
            let localDate = new Date(d.getTime() - timezoneOffset); // Adjust the date to local timezone
            return localDate.toISOString().slice(0, 19).replace('T', ' ');
        }

    async function submitTakeout() {

        takeoutItems = getTakeoutItemsArray();
        console.log(takeoutItems);

        // Planned data
        const Name = document.getElementById("plannedName")?.value || "";

        let StartingDate = picker.getStartDate();

        if (StartingDate == "") {
            errorToast("Nem adtál meg kezdési időpontot!");
            return;
        }

        let EndDate = picker.getEndDate();

        if (EndDate == "") {
            errorToast("Nem adtál meg visszahozás időpontot!");
            return;
        }
        if (EndDate < StartingDate) {
            errorToast("A visszahozás időpontja nem lehet korábbi a kezdetinél!");
            return;
        }
        // Format the dates in 'YYYY-MM-DD HH:MM:SS' format
        StartingDate = formatDateTime(StartingDate);
        EndDate = formatDateTime(EndDate);




        const Desc = document.getElementById("plannedDesc").value;

        const PlannedData = {
            Name: Name,
            StartingDate: StartingDate,
            EndDate: EndDate,
            Desc: Desc,
        };

        const response = await $.ajax({
            url: "../ItemManager.php",
            method: "POST",
            data: {
                mode: "stageTakeout",
                items: JSON.stringify(takeoutItems),
                plannedData: JSON.stringify(PlannedData),
            }
        });

        if (response == 409) {
            errorToast("Az általad megadott időre nem elérhető valamelyik eszközt!");
        }
        else if (response == 200) {
            deselect_all();
            if (<?php echo in_array("admin", $_SESSION["groups"]) ? 1 : 0; ?>) {
                successToast("Sikeres elvitel!");
            } else {
                warningToast("Sikeres elvitel! Jóváhagyásra vár!");
            }
            badge.innerHTML = 0;
            $('#takeoutSettingsModal').modal('hide');
            loadPage();
        } else {
            console.log(response);
            errorToast("Hiba történt az elvitel során!");
        }
    }

    const roleLevel = <?php
    if (in_array("system", $_SESSION["groups"])) {
        echo 5;
    } else if (in_array("admin", $_SESSION["groups"])) {
        echo 4;
    } else if (in_array("event", $_SESSION["groups"])) {
        echo 3;
    } else if (in_array("studio", $_SESSION["groups"])) {
        echo 2;
    } else {
        echo 0;
    }
    ?>;
</script>

</html>