<?php
namespace Mediao;
include "translation.php";
include "header.php";

namespace Mediaio;

use Mediaio\itemDataManager;
require "./ItemManager.php";

?>
<!DOCTYPE html>
<?php if (isset($_SESSION["userId"])) { ?> 
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php"><img src="./utility/logo2.png" height="50"></a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
    </ul>
    <ul class="navbar-nav ms-auto navbarPhP">
      <!-- Autologout -->
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?></a>
      </li>
    </ul>

    <form method='post' class="form-inline my-2 my-lg-0" action=utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
      <script type="text/javascript">
        window.onload = function () {
          menuItems = importItem("./utility/menuitems.json");
          drawMenuItemsLeft('adatok', menuItems);

          display = document.querySelector('#time');
          var timeUpLoc="utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
      </script>
    </form>
  </div>
</nav> <?php  } ?>
<br>
<form>
  <!-- Selection -->
<span>Mutasd a</span>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="rentable" id="inline_a" value="1" <?php if(isset($_GET['rentable']) && $_GET['rentable'] == '1') echo 'checked';?>>
  <label class="form-check-label" for="inline_a">Médiás,</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="studio" id="inline_b" value="2" <?php if(isset($_GET['studio']) && $_GET['studio'] == '2') echo 'checked';?>>
  <label class="form-check-label" for="inline_b">Stúdiós,</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="Event" id="inline_d" value="5" <?php if(isset($_GET['Event']) && $_GET['Event'] == '5') echo 'checked';?>>
  <label class="form-check-label" for="inline_d">Event</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="nonRentable" id="inline_c" value="3" <?php if(isset($_GET['nonRentable']) && $_GET['nonRentable'] == '3') echo 'checked';?>>
  <label class="form-check-label" for="inline_c">Nem Kölcsönözhető,</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="Out" id="inline_e" value="4" <?php if(isset($_GET['Out']) && $_GET['Out'] == '4') echo 'checked';?>>
  <label class="form-check-label" for="inline_e">Kinnlevő,</label>
</div>
<span>tárgyakat,</span>
<select id="orderByField" name="orderByField">
  <option value="UID" <?php if(isset($_GET['orderByField']) && $_GET['orderByField'] == 'UID') echo 'selected';?>>UID</option>
  <option value="Nev" <?php if(isset($_GET['orderByField']) && $_GET['orderByField'] == 'Nev') echo 'selected';?>>Név</option>
  <option value="Tipus" <?php if(isset($_GET['orderByField']) && $_GET['orderByField'] == 'Tipus') echo 'selected';?>>Típus</option>
  <option value="RentBy" <?php if(isset($_GET['orderByField']) && $_GET['orderByField'] == 'RentBy') echo 'selected';?>>Kivette</option>
</select>
<label for="orderByField">szerint rendezve,</label>
<select id="order" name="order">
  <option value="ASC" <?php if(isset($_GET['order']) && $_GET['order'] == 'ASC') echo 'selected';?>>növekvő</option>
  <option value="DESC" <?php if(isset($_GET['order']) && $_GET['order'] == 'DESC') echo 'selected';?>>csökkenő</option>
</select>

<label for="order">sorrendben.</label>
<button class="btn btn-success my-2 my-sm-0" type="submit">Mehet</button>
</form>

<?php 
	$countOfRec=0;
  $displayData= array("Event"=>$_GET['Event'],"rentable"=>$_GET['rentable'],"studio"=>$_GET['studio'],"nonRentable"=>$_GET['nonRentable'],"Out"=>$_GET['Out'],"orderByField"=>$_GET['orderByField'],"order"=>$_GET['order']);
  $result=itemDataManager::getItemData($displayData);
if ($result!=NULL && $result->num_rows > 0) {
	echo "<table id="."dataTable"." align=center class="."table"."><th onclick=sort(0)>UID</th><th onclick=sort(1)>Név</th><th onclick=sort(2)>Típus</th><th onclick=sort(3)>Kivette</th>";
     //output data of each row
    //Displays amount of records found in leltar_master DB
    while($row = $result->fetch_assoc()) {
		if($row["Status"]==0){
			echo "<tr style='background-color:#fffeab;' ><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td><strong>". $row["RentBy"]."</strong></td></tr>";
		}
    else if($row["TakeRestrict"]=="s"){
			echo "<tr style='background-color:#7db3e8;' ><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td><strong>". $row["RentBy"]."</strong></td></tr>";
		}
		else if($row["TakeRestrict"]=="*"){
			echo "<tr style='background-color:#F5B8B8;' ><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td><strong>". $row["RentBy"]."</strong></td></tr>";
		}else if($row["TakeRestrict"]=="e"){
			echo "<tr style='background-color:#4ca864;' ><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td><strong>". $row["RentBy"]."</strong></td></tr>";
		}
    else{
			echo "<tr><td><a id=#".$row["UID"]."></a>".$row["UID"]. "</td><td>" . $row["Nev"]. "</td><td>" . $row["Tipus"]. "</td><td>". "</td></tr>";
		}
		$countOfRec += 1;
	}
} else {
    echo "// Nem található a keresési feltételeknek megfelelő tárgy a rendszerben. //";
}
echo "</table>";
?>
<style>
  .btn-info2{color:white;background-color:#000658;border-color:#000658;border-width:2px}.btn-info2:hover{color:black;background-color:#ffffff;border-color:#000658;border-width:2px}
  .autocomplete {
    position: relative;
    display: inline-block;
  }

  input {
    border: 1px solid transparent;
    background-color: #f1f1f1;
    padding: 10px;
    font-size: 16px;
  }

  input[type=text] {
    background-color: #f1f1f1;
    width: 100%;
  }

  input[type=submit] {
    background-color: DodgerBlue;
   color: #fff;
   cursor: pointer;
  }

  .autocomplete-items {
    position: absolute;
    border: 1px solid #d4d4d4;
    border-bottom: none;
    border-top: none;
    z-index: 99;
    /*position the autocomplete items to be the same width as the container:*/
    top: 100%;
    left: 0;
    right: 0;
  }

  .autocomplete-items div {
    padding: 10px;
    cursor: pointer;
    background-color: #fff; 
    border-bottom: 1px solid #d4d4d4; 
  }

  /*when hovering an item:*/
  .autocomplete-items div:hover {
    background-color: #e9e9e9; 
  }

  /*when navigating through the items using the arrow keys:*/
  .autocomplete-active {
    background-color: Black !important; 
    color: #ffffff; 
  }

  .livearray{
    display:none;
  }
</style>
