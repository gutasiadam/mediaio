<?php

namespace Mediaio;

session_start();

include "header.php";


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

    <!-- Info toast -->
    <div class="toast-container bottom-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
        <div class="toast" id="infoToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="../logo.ico" class="rounded me-2" alt="..." style="height: 20px; filter: invert(1);">
                <strong class="me-auto" id="infoToastTitle">Projektek</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
            </div>
        </div>
    </div>

    <!-- takeoutSettingsModal Modal -->
    <div class="modal fade" id="takeoutSettingsModal" tabindex="-1" role="dialog"
        aria-labelledby="takeoutSettingsModal_Modal_Label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="takeoutSettingsModal_Label">Kivétel beállítások</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <i>Fejlesztés alatt...</i><br>
                    <b>Nyomj a "Mehet" gombra a kivétel megerősítéséhez!</b>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="submitTakout()">
                        Mehet</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Presets Modal -->
    <div class="modal fade" id="presets_Modal" tabindex="-1" role="dialog" aria-labelledby="presets_ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Elérhető presetek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="presetsLoading" class="spinner-grow text-info" role="status"></div>
                    <div id="presetsContainer"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Presets Modal -->

    <!-- Clear Modal -->
    <div class="modal fade" id="clear_Modal" tabindex="-1" role="dialog" aria-labelledby="clear_ModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Összes törlése</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <a>Biztosan ki akarsz törölni mindent?</a>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger col-lg-auto mb-1" id="clear" data-bs-dismiss="modal"
                        onclick="deselect_all()">Összes törlése</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mégse</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Clear Modal -->

    <!-- Scanner Modal -->
    <div class="modal fade" id="scanner_Modal" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="scanner_ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Szkenner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="pauseCamera()"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="scanner_body">
                    <div id="reader" width="600px"></div>
                    <!-- Toasts -->
                    <div class="toast align-items-center" id="scan_toast" role="alert" aria-live="assertive"
                        aria-atomic="true" style="z-index: 99; display:none;">
                        <div class="d-flex">
                            <div class="toast-body" id="scan_result">
                            </div>
                            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                                aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="scanner_footer">
                    <button type="button" class="btn btn-outline-dark" id="ext_scanner" onclick="ExternalScan()">Külső
                        olvasó</button>
                    <button type="button" class="btn btn-info" id="zoom_btn" onclick="zoomCamera()"
                        style="display: none;">Zoom: 2x</button>
                    <button type="button" class="btn btn-info" id="torch_btn" onclick="startTorch()"
                        style="display: none;">Vaku</button>
                    <div class="dropdown dropup">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="true">
                            Kamerák
                        </button>
                        <ul class="dropdown-menu" id="av_cams"></ul>
                    </div>
                    <button type="button" class="btn btn-success" onclick="pauseCamera()"
                        data-bs-dismiss="modal">Kész</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Scanner Modal -->




    <h2 class="rainbow" id="doTitle">Tárgy kivétel</h2>
    <div class="container">
        <div class="row align-items-start" id="takeout-container">
            <div class="col-4 selectedList" id="selected-desktop">
                <h3>Kiválasztva:</h3>
            </div>
            <div class="col">
                <div class="input-group mb-1">
                    <input type="text" class="form-control" id="search" placeholder="Kezdd el ide írni, mit vinnél el.."
                        aria-label="Kezdd el ide írni, mit vinnél el.." aria-describedby="button-addon2">

                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-expanded="false">Szűrés</button>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="dropdown-item">
                                <input class="form-check-input filterCheckbox" type="checkbox" autocomplete="off"
                                    id="show_studios" data-filter="s">
                                <label class="form-check-label" for="show_studios">Stúdiós</label>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-item">
                                <input class="form-check-input filterCheckbox" type="checkbox" autocomplete="off"
                                    id="show_eventes" data-filter="e">
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
                        data-bs-toggle="offcanvas" role="button" aria-controls="sidebar">Kiválasztva
                        <span id="selectedCount" class="badge bg-danger">0</span>
                    </button>
                    <button class="btn btn-sm btn-success col-lg-auto mb-1" id="takeout2BTN" style='margin-bottom: 6px'
                        data-bs-target="#takeoutSettingsModal" data-bs-toggle="modal">Mehet</button>

                    <button class="btn btn-sm btn-info col-lg-auto mb-1" onclick="showPresetsModal()"
                        style='margin-bottom:6px'>Presetek</button>
                    <button class="btn btn-sm btn-danger col-lg-auto mb-1 text-nowrap" id="clear"
                        style='margin-bottom: 6px' data-bs-target="#clear_Modal" data-bs-toggle="modal">Összes
                        törlése</button>
                    <button type="button" class="btn btn-warning btn-sm col-lg-auto mb-1 text-nowrap"
                        onclick="showScannerModal()">Szkenner <i class="fas fa-qrcode"></i></button>


                    <!-- GivetoAnotherperson button -->
                    <!-- <button class="btn btn-sm btn-dark col-lg-auto mb-1 text-nowrap" id="givetoAnotherPerson_Button"
                        type="button" data-bs-toggle="modal" data-bs-target="#givetoAnotherPerson_Modal"
                        style="margin-bottom: 6px">Másnak veszek
                        ki</button> -->
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
    </div>

    <!-- Navigation back to top -->
    <div id='toTop'><i class="fas fa-chevron-up"></i></div>
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
            return;
        }

        const takeoutItems = Array.from(selectedItems).map(item => ({
            id: item.getAttribute("data-main-id"),
            uid: item.id,
            name: item.getAttribute("data-name"),
        }));

        console.log(takeoutItems);

        const response = await $.ajax({
            url: "../ItemManager.php",
            method: "POST",
            data: {
                mode: "stageTakeout",
                items: JSON.stringify(takeoutItems),
                user: null, // TODO: Implement user selection
            }
        });

        console.log(response);

        if (response == 200) {
            deselect_all();
            successToast("Sikeres kivétel!");
            badge.innerHTML = 0;
            loadPage();
        } else {
            errorToast("Hiba történt a kivétel során!");
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