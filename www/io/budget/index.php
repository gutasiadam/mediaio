<?php
use Mediaio\Database;
require_once "../Database.php";
session_start();
include("../profile/header.php");


//At least one condition is true
 if (!(in_array("admin", $_SESSION["groups"]) || in_array("teacher", $_SESSION["groups"]))){
  
  echo "Nincs jogosultságod a lap megtekintéséhez!"; 
  exit();
  }?>


<?php if (isset($_SESSION["userId"])) { ?> <nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="../utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
      <script>
        $(document).ready(function() {
          menuItems = importItem("../utility/menuitems.json");
          drawMenuItemsLeft('profile', menuItems,2);
        });
      </script>
    </ul>
    <ul class="navbar-nav ms-auto navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
      <script type="text/javascript">
        window.onload = function () {
          display = document.querySelector('#time');
          var timeUpLoc="../utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
      </script>
    </form>
  </div>
</nav> 


<body>
  <form>
  <div class="mb-3">
  <div class="container range noprint">
    <div class="row">
      <div class="col">
    <label for="customRange3" class="form-label" id="yearRangeLabel">Adatok betöltése eddig:</label>
    <input type="range" class="form-range" min="2022" max="<?php echo date("Y");?>" value="<?php echo date("Y");?>" step="1" id="yearRange">
    <span id="yearRangeValue"><?php echo date("Y");?></span>
  </div>
  </div>
  </div>
  </div>
  </form>

    <div class="container">
      <div class="container text-center">
      </br>
        <div class="row">
          <div class="col">
            <button data-bs-toggle="modal" data-bs-target="#addDataModal" type="button" class="btn btn-success noprint" style='height: 2rem'><i class="fas fa-plus fa-xl" style="color: #ffffff;"></i></button>
          </div>
      </div>
      </div>

      
  <div class="row">
    <div class="col">
      <h1>Médiás</h1>
      <div id="mediaTable_full">
        <script>

          //Updates both tables
          function updateTables(){
            $("#mediaTable_full").empty();
            $("#egyesuletTable_full").empty();
            const startDate=parseInt($("#yearRange").val());
            const currentYear = new Date().getFullYear();
            for (let index = 0; index <= currentYear-startDate; index++) {
              const element = startDate+index;
              //clear both table divs


              $("#mediaTable_full").append("<div id='loadmediaTable_"+element+"'></div>");
              $("#loadmediaTable_"+element).append("<h3>"+(element)+"</h3>");
              $("#loadmediaTable_"+element+" h3").append("<button type='button' class='btn btn-light' onclick="+'loadResource("year","media",'+element+')'+"><i class='fas fa-level-down-alt' style='color: #1f2551;'></i></button>");
              $("#mediaTable_full").append("<hr class='solid'>");

              $("#egyesuletTable_full").append("<div id='loadegyesuletTable_"+element+"'></div>");
              $("#loadegyesuletTable_"+element).append("<h3>"+(element)+"</h3>");
              $("#loadegyesuletTable_"+element+" h3").append("<button type='button' class='btn btn-light' onclick="+'loadResource("year","egyesulet",'+element+')'+"><i class='fas fa-level-down-alt' style='color: #1f2551;'></i></button>");
              $("#egyesuletTable_full").append("<hr class='solid'>");
            }
          }
        </script>

      </div>
    </div>
    <div class="col">
      <h1>Egyesületi</h1>
      <div id="egyesuletTable_full">
        <script>
          $(document).ready(function() {
            updateTables();
          });
        </script>
      </div>
    </div>
  </div>
</div>

</body>
<!--Add Data Modal -->
<div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tétel hozzáadása</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
                <form>
        <div class="mb-3">
          <label for="dateInput" class="form-label">Dátum</label>
          <input type="date" class="form-control" id="dateInput" placeholder="" required>
        </div>
        <div class="mb-3">
          <label for="typeSelect" class="form-label">Kassza</label>
          <select id='typeSelect'>
            <option value="media">Médiás</option>
            <option value="egyesulet">Egyesületi</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="valueInput" class="form-label">Összeg</label>
          <input type="number" autocomplete="off" class="form-control" id="valueInput" name="value" min="-999999999" max="999999999" required>
          </div>
        <div class="mb-3">
          <label for="nameInput" class="form-label">Tétel</label>
          <input type="text" autocomplete="off" class="form-control" id="nameInput" placeholder="" required>
        </div>
        <div class="mb-3">
          <label for="commentInput" class="form-label">Megjegyzés</label>
          <input type="text" autocomplete="off" class="form-control" id="commentInput" placeholder="">
        </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégsem</button>
        <button type="button" class="btn btn-primary">Hozzáadás</button>
        <script>
          function openTodaysResources(){
            //Automatically opens down todays year and month
            var today=new Date();
            var year=today.getFullYear();
            var month=today.getMonth()+1;

            
              loadResource("year","media",year);
              loadResource("year","egyesulet",year);

              //Delay, to make sure tables are loaded
            setTimeout(function(){ 
              loadResource("month","media",year,month);
              loadResource("month","egyesulet",year,month);
            }, 500);
          }
          $(document).ready(function() {
            openTodaysResources();
            $(".btn-primary").click(function() {
              console.log("Clicked");
              var date = $("#dateInput").val();
              var type = $("#typeSelect").val();
              var value = $("#valueInput").val();
              var name = $("#nameInput").val();
              var comment = $("#commentInput").val();
              $.ajax({
                url: 'budgetManager.php',
                type: 'post',
                data: {
                  type: 'add',
                  date: date,
                  table: type,
                  value: value,
                  name: name,
                  comment: comment
                },
                success: function(response) {
                  console.log(response);
                  if (response == '200') {
                    console.log('ok');
                    location.reload();
                    //Reload the table
                    // $("#loadmediaTable_"+year+"_"+month+" table").remove();
                    // loadResource("month","media",year,month);
                  } else {
                    console.log("Error");
                  }
                }
              });
            });
          });
        </script>
      </div>
    </div>
  </div>
</div>

<!--Edit Data Modal -->
<div class="modal fade" id="editDataModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tétel módosítása</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
                <form>
        <div class="mb-3">
          <label for="dateInput" class="form-label">Dátum</label>
          <input type="date" class="form-control" id="dateInput" placeholder="" required>
        </div>
        <div class="mb-3">
          <label for="typeSelect" class="form-label">Kassza</label>
          <select id='typeSelect'>
            <option value="media">Médiás</option>
            <option value="egyesulet">Egyesületi</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="valueInput" class="form-label">Összeg</label>
          <input type="number" autocomplete="off" class="form-control" id="valueInput" name="value" min="-999999999" max="999999999" required>
          </div>
        <div class="mb-3">
          <label for="nameInput" class="form-label">Tétel</label>
          <input type="text" autocomplete="off" class="form-control" id="nameInput" placeholder="" required>
        </div>
        <div class="mb-3">
          <label for="commentInput" class="form-label">Megjegyzés</label>
          <input type="text" autocomplete="off" class="form-control" id="commentInput" placeholder="">
        </div>
        <input type="hidden" id="idInput">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégsem</button>
        <button type="button" class="btn btn-warning">Módosítás</button>
        <script>
          $(document).ready(function() {
            $(".btn-warning").click(function() {
              var date = $("#editDataModal #dateInput").val();
              var type = $("#editDataModal #typeSelect").val();
              var value = $("#editDataModal #valueInput").val();
              var name = $("#editDataModal #nameInput").val();
              var comment = $("#editDataModal #commentInput").val();
              var id = $("#editDataModal #idInput").val();
              $.ajax({
                url: 'budgetManager.php',
                type: 'post',
                data: {
                  type: 'modify',
                  id: id,
                  date: date,
                  table: type,
                  value: value,
                  name: name,
                  comment: comment
                },
                success: function(response) {
                  if (response == '200') {
                    console.log('ok');
                    location.reload();
                  } else {
                    console.log("Error");
                    console.log(response);
                  }
                }
              });
            });
          });
        </script>
      </div>
    </div>
  </div>
</div>

<?php  } 

?>

        <hr class="solid">
        <div class="row">
          <div class="col" id='sum_media'></div>
          <div class="col" id='sum_egyesulet'></div>
        <script>
          $(document).ready(function() {
            getSum();
          });
        </script>
        </div>  


<script>

  $(document).on('input change', '#yearRange', function() {
    $('#yearRangeValue').html( $(this).val() );
    updateTables();
  });

  //If the year range is changed, reload the tables

  function getMonthName(monthNumber) {
    const date = new Date();
    date.setMonth(monthNumber - 1);

    return date.toLocaleString('hu-HU', { month: 'long' });
  }

  function loadResource(type,table,value,value2=null){
    console.log(type,table,value,value2);
    if(type=='year'){

      //Hide table's first button
      $("#load"+table+"Table_"+value+" button").hide();
      $.ajax({
      url: 'budgetManager.php',
      type: 'post',
      data: {type:type,table:table,value:value},
      success: function(response){

        //If no data is present
        if(response=='[]'){
          $("#load"+table+"Table_"+value).append("<h4>Nincs adat az adott időszakra.</h4>");
        }
        response=JSON.parse(response);
        for (let index = 0; index < response.length; index++) {
          const element = response[index];

          //Add "Ft" to total value
          element.total_value=element.total_value+" Ft";

          $("#load"+table+"Table_"+value).append("<div id='load"+table+"Table_"+value+"_"+element.month+"'></div>");
          $("#load"+table+"Table_"+value+"_"+element.month).append("<h4>"+getMonthName(element.month)+' - <span style="color: grey;">'+element.total_value+"</span></h4>");
          $("#load"+table+"Table_"+value+"_"+element.month+" h4").append("<button type='button' class='btn btn-light' onclick="+'loadResource("month","'+table+'",'+value+','+element.month+')'+"><i class='fas noprint fa-level-down-alt' style='color: #1f2551;'></i></button>");
          
        }
      }
      });
    }
    if(type=='month'){
      //Hide table's first button
      $("#load"+table+"Table_"+value+"_"+value2+" button").hide();
      $.ajax({
      url: 'budgetManager.php',
      type: 'post',
      data: {type:type,table:table,value:value,value2:value2},
      success: function(response){
        
        //Response will be a JSON object, containing the days, comments, name, and the values for the days
        response=JSON.parse(response);
        //If no data is present
        if(response=='[]'){
          $("#load"+table+"Table_"+value).append("<h4>Nincs adat</h4>");
        }
        $("#load"+table+"Table_"+value+"_"+value2).append("<table class='table' id='load"+table+"Table_"+value+"_"+value2+"_table'><tr><th>Dátum</th><th>Tétel</th><th>Összeg</th><th>Megjegyzés</th><th class='noprint'>Műveletek</th></tr></table>");
        for (let index = 0; index < response.length; index++) {
          const element = response[index];
          
          

          //If Value is negative, color it red
          if(element.Value<0){
            //Add "Ft" to values
            element.Value=element.Value+" Ft";
            $("#load"+table+"Table_"+value+"_"+value2+" table").append("<tr><td>"+element.Date+"</td><td>"+element.Name+"</td><td><font color='red'>"+element.Value+"</font></td><td>"+JSON.parse(element.Data).comment+"</td><td><a id='editData' onclick='showEditModal("+element.ID+","+'"'+table+'"'+","+value+","+value2+","+JSON.stringify(element)+")' href='#'</a><i class='far noprint fa-lg fa-edit'></i></a><span class='noprint'> | </span><font color='red'><i class='fas fa-lg fa-times noprint' onclick='deleteResource("+element.ID+","+'"'+table+'"'+","+value+","+value2+")'></i></td></tr></font>");
          }else{
            //Add "Ft" to values
            element.Value=element.Value+" Ft";
          $("#load"+table+"Table_"+value+"_"+value2+" table").append("<tr><td>"+element.Date+"</td><td>"+element.Name+"</td><td>"+element.Value+"</td><td>"+JSON.parse(element.Data).comment+"</td><td><a id='editData' onclick='showEditModal("+element.ID+","+'"'+table+'"'+","+value+","+value2+","+JSON.stringify(element)+")' href='#'</a><i class='far noprint fa-lg fa-edit'></i></a><span class='noprint'> | </span><font color='red'><i class='fas fa-lg fa-times noprint' onclick='deleteResource("+element.ID+","+'"'+table+'"'+","+value+","+value2+")'></i></td></tr></font>");
          }
      }
    }
      });
    }
  }


  function deleteResource(id,table,year,month){
    // console.log(id,table,year,month);
    $.ajax({
      url: 'budgetManager.php',
      type: 'post',
      data: {type:'delete',id:id},
      success: function(response){
        if(response=='200'){
          console.log('ok');
          //Reload the table
          $("#load"+table+"Table_"+year+"_"+month).remove();
          loadResource("year",table,year,month);
          loadResource("month",table,year,month);
          getSum();
        }else{
          console.log("Error");
        }
      }
      });
  }


  function getSum(){
        //Get full sum of both tables.
    $.ajax({
      url: 'budgetManager.php',
      type: 'post',
      data: {
        type: 'sum'
      },
      success: function(response) {
        response = JSON.parse(response);
        $("#sum_media").empty();
        $("#sum_egyesulet").empty();
        if(response.length<2){
          $("#sum_media").append("<h6 style='color: grey;'>Mindkét sorban legalább egy tételnek szerepelnie kell az összegzéshez.</h6>");
        }else{
          //Add "Ft" to values
          response[0].sum=response[0].sum+" Ft";
          response[1].sum=response[1].sum+" Ft";
          $("#sum_media").append("<h6>"+response[0].sum+"</h6>");
          $("#sum_egyesulet").append("<h6>"+response[1].sum+"</h6>");
        }


      }
    });
  }

   function showEditModal(id,table,year,month,element){
    //change editDataModal datinput value to the date of the element
    $("#editDataModal #dateInput").val(element.Date);
    $("#editDataModal #typeSelect").val(table);
    $("#editDataModal #valueInput").val(element.Value);
    $("#editDataModal #nameInput").val(element.Name);
    $("#editDataModal #commentInput").val(JSON.parse(element.Data).comment);
    $("#editDataModal #idInput").val(id);
    $('#editDataModal').modal('show');
   }



</script>


<style>
  hr.solid {
  border-top: 3px solid #bbb;
}

@media print
{    
    .noprint, .noprint *
    {
        display: none !important;
    }
}
</style>
