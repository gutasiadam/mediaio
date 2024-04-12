<!-- THIS TABLE HOLDS THE TWO CHILDS - selectable and selected-->
<!-- Selectable items -->
<?php
echo '<div class="row" id="retrieve-row">
      
      <div class="col-6" id="items-to-retrieve">
        <table class="table table-bordered table-dark dynamic-table" id="retrieve_items">';

//Get the items that are currently by the user
//Todo: Moves this function to the itemManager.php
$TKI = $_SESSION['UserUserName'];
$conn = Database::runQuery_mysqli();
$sql = ("SELECT * FROM `leltar` WHERE `RentBy` = '$TKI' AND Status=0");
$result = mysqli_query($conn, $sql);
$conn->close();
$n = 0;
while ($row = $result->fetch_assoc()) {
    //var_dump($row);
    $n++;
    echo '<tr id="' . $row['UID'] . '"><td class="result dynamic-field"><button disabled="true" id="' . $row['UID'] . '" class="btn btn-dark" onclick="' . "prepare(this.id,'" . $row['UID'] . "'" . ",'" . $row['Nev'] . "');" . '"' . '>' . $row['Nev'] . ' [' . $row['UID'] . ']' . ' <i class="fas fa-angle-double-right"></i></button></td></tr>';
    //echo '<div class="result dynamic-field"><button id="' . $row['UID'] . '" class="btn btn-dark" onclick="' . "prepare(this.id,'" . $row['Nev'] . "'" . ');' . '"' . '>' . $row['Nev'] . ' [' . $row['UID'] . ']' . ' <i class="fas fa-angle-double-right"></i></button></div>';
}
echo '</table>';
echo '</div>';
if ($n == 0) {
    echo '<h3 class="nothing_here">Jelenleg nincs nálad egy tárgy sem!</h3>';
}
?>
<div class="col-6">
    <table class="table table-success table-bordered" style="line-height: 10px; background-color:green;"
        id="dynamic_field">
    </table>
</div>



</div>


<script>

    $("#toTop").click(function () {
        $("#retrieve-container").animate({
            scrollTop: $("#retrieve-container")[0].scrollHeight
        }, 700);
        $('#toTop').fadeOut();
    });



    function prepare(id, uid, name, fromCookie = false) {
        $('#dynamic_field').append('<tr class="bg-success" id="prep-' + id + '"><td class="dynamic-field"><button id="prep-' + id + '" class="btn btn-succes" onclick="unstage(this.id);"><i class="fas fa-angle-double-left"></i> ' + name + ' [' + uid + ']' + '</button></td></tr>');
        $('#' + id).hide();
        $('.intactForm').css('display', 'block');
        $('#toTop').fadeIn();
        if (fromCookie == false) {
            updateSelectionCookie(id, uid, name);
        }
    }

    function unstage(id) {
        $('#' + id).remove();
        id = id.replace("prep-", "");
        $('#' + id).show();
        if ($('#dynamic_field tr').length == 1) {
            $('.intactForm').css('display', 'none');
            $('.send').hide();
            $("#intactItems").prop("checked", false);
            $('#toTop').fadeOut();
        }
        updateSelectionCookie(id, "", "", false);
    }



    var itemsToRetrieve = [];


    function clearSelectionCookie() {
        console.log("Cleared cookie");
        var d = new Date();
        d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = "itemsToRetrieve=;" + expires + ";path=/";
    }


    // END OF cooke management
    $(document).ready(function () {
        $('.intactForm').css('display', 'none');
        // Csak akkor jelenjen meg a checkbox, ha már van Go gomb is.
        $('.send').hide();

        function startTimer(duration, display) {
            var timer = duration,
                minutes, seconds;
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

        reloadSavedSelections();



        /*     $(document).on('click', '.result', function () {
              $('.intactForm').css('display', 'flex');
            }); */

        function allowGO() {
            if ($('#intactItems').is(":checked")) {
                $('.send').show();

            }
        }
        $(document).on('click', '#intactItems', function () {
            allowGO();
        });
        //Initiate Takeout process
        $(document).on('click', '.send', function () {
            $("#retrieve-container").animate({
                scrollTop: 0
            }, 500);
            if ($("#intactItems").prop("checked")) { // ha a felhasználó elfogadta, hogy a tárgyak rendben vannak.
                var items = []; //Items that will be retreievd.
                $('#dynamic_field > tbody  > tr > td > button ').each(function (index, tr) {
                    console.log(this.innerText);

                    newItem = {
                        'uid': this.innerText.split('[')[1].slice(0, -1),
                        'name': this.innerText.split('[')[0].trim()
                    }

                    //push only if items are not already in the list

                    items.indexOf(newItem) === -1 ? items.push(newItem) : console.log("This item already exists");


                });
                //console.log(items);
                retrieveJSON = JSON.stringify(items);
                //console.log(retrieveJSON);
                $.ajax({
                    method: 'POST',
                    url: './ItemManager.php',
                    data: {
                        data: retrieveJSON,
                        mode: "retrieveStaging"
                    },
                    success: function (response) {
                        if (response == '200') {
                            clearSelectionCookie();
                            $('#doTitle').animate({
                                'opacity': 0
                            }, 400, function () {
                                $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak ! Az oldal újratölt.</h2>').animate({
                                    'opacity': 1
                                }, 400);
                                $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak ! Az oldal újratölt.</h2>').animate({
                                    'opacity': 1
                                }, 3000);
                                $(this).html('<h2 class="text text-info" role="success">Sikeresen visszakerültek a tárgyak ! Az oldal újratölt.</h2>').animate({
                                    'opacity': 0
                                }, 400);
                                setTimeout(function () {
                                    $("#doTitle").text(applicationTitleShort).animate({
                                        'opacity': 1
                                    }, 400);
                                }, 3800);;
                            });
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else {
                            $('#doTitle').animate({
                                'opacity': 0
                            }, 400, function () {
                                $(this).html('<h2 class="text text-danger" role="danger">A vissszahozás során szerveroldali hiba történt.</h2>').animate({
                                    'opacity': 1
                                }, 400);
                                $(this).html('<h2 class="text text-danger" role="danger">A vissszahozás során szerveroldali hiba történt.</h2>').animate({
                                    'opacity': 1
                                }, 3000);
                                $(this).html('<h2 class="text text-danger" role="danger">A vissszahozás során szerveroldali hiba történt.</h2>').animate({
                                    'opacity': 0
                                }, 400);
                                setTimeout(function () {
                                    $("#doTitle").text(applicationTitleShort).animate({
                                        'opacity': 1
                                    }, 400);
                                    location.reload();
                                }, 3800);;
                            });
                        }

                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        alert("Status: " + textStatus);
                        alert("Error: " + errorThrown);
                    }
                });
            } else {
                alert("Ha a tárggyal gond van, jelezd a vezetőségnek!");
                return;
            }
        });

    });



</script>