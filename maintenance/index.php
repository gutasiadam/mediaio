
<html>
    <?php 
    require("./header.php");
    require("../translation.php");
   ?>
    <script src="../utility/_initMenu.js" crossorigin="anonymous"></script>
<script> $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft("maintenance",menuItems,2);
              drawMenuItemsRight('maintenance',menuItems);
            });</script>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark nav-all" id="nav-head">
					<a class="navbar-brand" href="../index.php"><img src="../utility/logo2.png" height="50"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto navbarUl">
            <li>
            <a class="nav-link disabled" id="ServerMsg" href="#"></a>
            </li></ul>
            <?php
            if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<ul class="navbar-nav navbarPhP">';
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';
              echo '</ul>
              <form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                        <button class="btn btn-danger my-2 my-sm-0" type="submit">'.$nav_logOut.'</button>
                        </form>
                        <div class="menuRight"></div>
            </div></nav>';
            }
            else{
              echo '</ul><form class="form-inline my-2 my-lg-0" action=utility/logout.ut.php>
                        <button class="btn btn-danger my-2 my-sm-0" type="submit">'.$nav_logOut.'</button>
                        </form>
                        <div class="menuRight"></div>
            </div></nav>';} ?>
    </nav>
<h1 align=center class="rainbow">Takarítási rend, feladatok </h1>

<div class="tableParent">
<div class="form-check">
  <input class="form-check-input" type="checkbox" value="" id="showOnlyMyTasks_checkBox" data-toggle="toggle">
  <label class="form-check-label" for="defaultCheck1">
    Csak a saját feladataimat mutasd
  </label>
  </div>
<?php
            if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<table>
              <tr><td><button type="button" class="btn btn-warning table-Control edit_Table_Button noprint" data-toggle="modal" data-target="#add_Work_Modal">Módosítás</button></td>
              <td><button type="button" class="btn btn-danger table-Control delete_Table_Button noprint">Törlés</button></td></tr>
              </table>';
              
            }?>

<table class="takaritasirend" id="takaritasirend">
<tr>
    <th>Dátum</th>
    <th>Személyek</th>
    <th>Elvégzendő feladat(ok)</th>
  </tr>
  <?php  include("./render_work_Data.php");
  renderWorkTable("*");
  ?>

</table></br>
<i class="noprint">// A rendszer csak a mai, vagy újabb feladatokat mutatja.
  Ha a mai napnál régebbi feladatott állítottál be, akkor az automatikusan törölve lett :( //</i>
</div>
</html>


<div class="modal" tabindex="-1" role="dialog" id="add_Work_Modal" data-backdrop="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Új feladat hozzáadása</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form id="add_Work_Form">
  <div class="form-group">
    <label for="work_Date">Dátum</label>
    <input type="date" class="form-control" id="work_Date" aria-describedby="emailHelp" placeholder="Dátum. ÉV/HÓ/NAP formátumban">
  </div>
  <div class="form-group">
    <label for="work_User">Személy</label>
    <!--<input type="text" class="form-control" id="work_User" placeholder="Egyszerre csak egy felhasználónevet adj meg!">-->
    <div class="wrapper">
      <table><tr>
      <td> <p>Felhasználók</p>
      <div class="box 1" ondrop="drop(event)" ondragover="allowDrop(event)"">
   
    <?php 
    renderUsersDraggable();
    
    ?>
  </div></td>
  <td>
  <p>Kijelölt</p>
    <div class="box 2 selectedUsers" ondrop="drop(event)" ondragover="allowDrop(event)">
  </div>
  </td></tr></table>
</div>
  </div>
  <div class="form-group">
    <label for="work_Task">Feladat</label>
    <input type="text" class="form-control" id="work_Task" placeholder="Ide írd a feladatokat..">
  </div>
  
</form>
      </div>
      <div class="modal-footer">
        <!--<button type="button" class="btn btn-success">Mentés</button>-->
        <div id="processing">Feldolgozás..</div>
        <button type="button" class="btn btn-success send_Work_update">Mentés</button> 
        <button type="button" class="btn btn-danger clear_Update" data-dismiss="modal">Mégsem</button>
      </div>
    </div>
  </div>
</div>

<script>
//Ha nincs feladat, ne is jelenjen meg a táblázat:
function deleteTableIfNoTask(){
  if($('#takaritasirend tr').length==1){
    $('#takaritasirend').fadeOut(600);
    $('#takaritasirend').remove();
  }
}

deleteTableIfNoTask();

window.onload = function () {
  $('#processing').hide();
    var fiveMinutes = 10 * 60 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    (function(){
  setInterval(updateTime, 1000);
});
    updateTime();
};

$( ".send_Work_update" ).click(function( event ) {
  $('#processing').html("Feldolgozás..");
  $('#processing').fadeIn();
  //event.preventDefault();
  datum=$('#work_Date').val();
  //nDatum=datum.match('/\W/g')
  //szemely=$('#work_User').val();
  Szemelyek=$( $('.selectedUsers')).children()
  
  //nszemely=szemely.match('/" "/g')
  feladat=$('#work_Task').val();
  //Beírt adatok ellenőrzése regEXel
  for (let index = 0; index < Szemelyek.length; index++) {
    szemely=Szemelyek[index].innerHTML;
    
  
  if(/*nDatum.length==2 && nszemely.length==0 ha működne a REGEX*/ datum!="" & szemely!="" & feladat!=""){
  $.ajax({
       url:"add_work.php",
       type:"POST",
       async: true,
       data:{date:datum, user:szemely, task:feladat},
       success:function(result)
       {
        //alert(result);
        if(result==1){
          $('#processing').html("Nincs ilyen felhasználó!")
        }
        else if(result==2){
          $('#processing').html("Üres cella/formátumhiba!")
        }
        else if(result==3){
          $('#processing').html("Siker! A felhasználót e-mailben értesítettem. Újratöltés...")
          setTimeout(function(){ location.reload(); }, 1000);
        }
        /*else if(result==4){
          $('#processing').html("Adj meg mai, vagy későbbi dátumot!")
          $('#work_Date').val("");
          $('#work_Date').css("borderColor","red");
          setTimeout(function(){ $("#processing").fadeOut(200);}, 2000);
        }*/
       }
      })
    }else{
      $('#processing').html("Ismeretlen hiba ");
    }
  }
});

$( ".clear_Update" ).click(function( event ) {
  $('#work_Date').val("");
  $('#work_User').val("");
  $('#work_Task').val("");
});

$( ".delete_Table_Button" ).click(function( event ) {
  if(!$('.delete_Table_Button').hasClass('disabled')){
  $('.delete_Table_Button').addClass('disabled');
  $('#takaritasirend tr:first ').append("<td class='deleteRowTitle'></td>");
  $('#takaritasirend tr:not(:first)').each(function(i){
    console.log(i);
    $(this).append('<td class="deleteRow"><button type="button" class="btn btn-danger delRowBtn" onclick="deleteWork('+i+')" id="del_'+i+'"><span aria-hidden="true">&times;</span></button></td>');
 });   
 }
});

function deleteWork(index){
  //$(('#takaritasirend tr').eq(i))
  //Dátum, felhasználónév, és Elvégzendő feladat lekérése
  toBeDeleted_Date= $('#takaritasirend tr').find('td:nth-child(1)').eq(index).text();
  toBeDeleted_userName= $('#takaritasirend tr').find('td:nth-child(2)').eq(index).text();
  toBeDeleted_workDescription= $('#takaritasirend tr').find('td:nth-child(3)').eq(index).text();
  //console.log(toBeDeleted_Date+toBeDeleted_userName+toBeDeleted_workDescription)
  
  $.ajax({
       url:"delete_work.php",
       type:"POST",
       async: true,
       data:{date:toBeDeleted_Date, user:toBeDeleted_userName, task:toBeDeleted_workDescription},
       success:function(result)
       {
        if(result=200){
          //Sikeres a törlés
          $('#takaritasirend tr').eq(index+1).fadeOut(600);
          $('#takaritasirend tr').eq(index+1).remove();
          setInterval(function(){ deleteTableIfNoTask(); }, 600);
          
        }else{
          console.log(result);
          console.log("A törlés nem futott le sikeresen.")
        }
       }
      })
};
//Csak a saját feladatok mutatása

$('#showOnlyMyTasks_checkBox').change(function() {
        if(this.checked) {
            console.log("Check!");
            $('table#takaritasirend > tbody > tr').not(':first').remove();//Címsor után minden törlése
            $.ajax({
       url:"render_work_Data.php",
       type:"POST",
       async: true,
       // a PHP szkript utasítást kap, hogy csak egy felhasználóra vagyunk kíváncsiak.
       data:{mode:'UserFiltered'}, 
       success:function(result)
       {
        sentBack_result=JSON.parse(result);
        console.log(sentBack_result);

        if(sentBack_result.message=="success"){
          for (let index = 0; index < sentBack_result.data.length; index++) {
            $('#takaritasirend tr:last').after('<tr><td>'+sentBack_result.data[index].datum+'</td><td>'+sentBack_result.data[index].szemely+'</td><td>'+sentBack_result.data[index].feladat+'</td></tr>');
          }
        }
       }
      })
            
        }else{
          location.reload();
        }   
    });
//DRAG

function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
  ev.preventDefault();
  var data = ev.dataTransfer.getData("text");
  ev.target.appendChild(document.getElementById(data));
}

</script>