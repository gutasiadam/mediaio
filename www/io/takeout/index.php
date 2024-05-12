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
            <a class="nav-link active" id="takeout-tab" data-bs-toggle="tab" data-bs-target="#takeout-tab-pane"
                aria-current="page" href="#">
                <h2 class="rainbow" style="font-size: 40px; margin: 0;">Elvitel</h2>
            </a>
        </li>
        <h2 class="rainbow"> - </h2>
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="#" onclick="loadTakeOutPlanner();" id="prepared-tab" data-bs-toggle="tab"
                data-bs-target="#prepared-tab-pane">
                <h2 class="rainbow" style="margin: 0; font-size: 40px;">Előjegyzések</h2>
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
                                        <input class="form-check-input" type="checkbox" autocomplete="off"
                                            id="show_unavailable">
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
    //Selected items badge counter
    var badge = document.getElementById("selectedCount");

    $(document).ready(function () {
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

    async function loadPage() {
        //Load items
        await loadItems();
        //Load selected items
        await loadTooltips();
    }


    // Load tooltips
    async function loadTooltips() {
        //Load tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    }


    async function submitTakout() {
        const selectedItems = document.getElementsByClassName("selected");

        if (selectedItems.length == 0) {
            errorToast("Nincs kiválasztva semmi!");
            $('#takeoutSettingsModal').modal('hide');
            setTimeout(() => {
                document.getElementById("search").focus();
            }, 500);
            return;
        }

        const takeoutItems = Array.from(selectedItems).map(item => ({
            //id: item.getAttribute("data-main-id"),
            uid: item.id,
            name: item.getAttribute("data-name"),
        }));

        console.log(takeoutItems);

        // Planned data
        const Name = document.getElementById("plannedName").value;
        const StartingDate = document.getElementById("startingDate").value;

        if (StartingDate == "") {
            errorToast("Nem adtál meg kezdési időpontot!");
            return;
        }

        const EndDate = document.getElementById("endDate").value;

        if (EndDate == "") {
            document.getElementById("endDate").focus();
            errorToast("Nem adtál meg visszahozás időpontot!");
            return;
        }
        if (EndDate < StartingDate) {
            errorToast("A visszahozás időpontja nem lehet korábbi a kezdetinél!");
            return;
        }
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

        if (response == 200) {
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