<?php
session_start();
if(!isset($_SESSION['userId'])){
  header("Location: ../index.php?error=AccessViolation");}
#echo $_SESSION['color'];
?><html lang='en'>
  <head>
    <meta charset='utf-8' />
    <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>

    <link href='./core/main.css' rel='stylesheet' />
    <link href='./daygrid/main.css' rel='stylesheet' />
    <link href='./timegrid/main.css' rel='stylesheet' />
    <script src='./interaction/main.css'></script>

    <script src='./core/main.js'></script>
    <script src='./daygrid/main.js'></script>
    <script src='./timegrid/main.js'></script>
    <script src='./interaction/main.js'></script>

   
  <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' rel='stylesheet' />
  <script src="./moment/main.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
          plugins: [ 'dayGrid', 'timeGrid', 'interaction', 'moment', 'bootstrap' ],
          locale: 'hu',
    themeSystem: 'bootstrap',
    firstDay: 1,
    editable:true,
    droppable:true,
    height: "parent",
    width: "parent",
    nowIndicator: true,
    buttonText:{
    today:    'ma',
    month:    'hónap',
    week:     'hét'
    },
    header:{
    left:   'title',
    center: '',
    right:  'timeGridWeek dayGridMonth today prev,next,'
},
    events: 'load.php',
    selectable:true,
    selectHelper:true,
  windowResize: function(view) {
    calendar.updateSize();
  },
  select: function(info) {
    console.log("WHY ARE YOU RUNNING?");
    var startval = info.startStr;
    var endval = info.endStr;
    console.log(startval+' '+endval);
    document.getElementById('addEventInterval').innerHTML = startval+ ' - '+endval;
    document.getElementById('addEventStartVal').value = startval;
    document.getElementById('addEventEndVal').value = endval;;
    $('#exampleModal').modal('show');
    $( "#sendAddEvent" ).submit(function() {
    console.log( "Adder call!");
  $('#exampleModal').modal('hide');

  title = document.getElementById('addEventName').value;
  start = startval;
  end = endval;
  color= "#f7f7f7";
  console.log(title);
  console.log(startval);
  console.log(color);
  console.log(endval);
     if(title)
     {
      $.ajax({
       url:"insert.php",
       type:"POST",
       data:{title:title, start:start, end:end, color:color},
       success:function()
       {
        document.getElementById('SystemMsg').innerHTML = "Sikeres hozzáadás";
        calendar.refetchEvents()
        console.log("SUCC")
       }
      })
     }
    })

    },
    eventClick:function(info)
    {
      var id = info.event.id;
      var title = info.event.title;
    console.log(id+title)
     document.getElementById('delEventTitle').value = title;
     document.getElementById('deleteEventName').innerHTML = title;
     document.getElementById('deleteEventName2').innerHTML = title;
     document.getElementById('delEventId').value = id;
     $('#deleteModal').modal('show');
    $( "#sendDelEvent" ).submit(function() {
    console.log( "Deletion call!");
    $('#deleteModal').modal('hide');
    id = document.getElementById('delEventId').value;
      $.ajax({
       url:"delete.php",
       type:"POST",
       data:{id:id},
       success:function()
       {
        calendar.refetchEvents()
        console.log("Event Removed");
       }
      })
     })
    },
    eventResize:function(info)
    {
     var start = calendar.formatIso(info.event.start);
     var end = calendar.formatIso(info.event.end);
     var title = info.event.title;
     var id = info.event.id;
     console.log(start+end+title+id);
     $.ajax({
      url:"update.php",
      type:"POST",
      data:{title:title, start:start, end:end, id:id},
      success:function(){
       calendar.refetchEvents()
       console.log('Event Update');
      }
     })
    },
    eventDrop:function(info)
    {
     var start = calendar.formatIso(info.event.start);
     var end = calendar.formatIso(info.event.end);
     var title = info.event.title;
     var id = info.event.id;
     $.ajax({
      url:"update.php",
      type:"POST",
      data:{title:title, start:start, end:end, id:id},
      success:function()
      {
        calendar.refetchEvents()
       console.log("Event Updated");
      }
     });
    }

   });

        calendar.render();
      });

      
    </script>
  <!-- HOZZÁADÁS MODAL -->
  </head>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
					<a class="navbar-brand" href="index.php">Arpad Media IO</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					  <span class="navbar-toggler-icon"></span>
					</button>
				  
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
					  <ul class="navbar-nav mr-auto">
						<li class="nav-item  ">
						    <a class="nav-link" href="../index.php"><i class="fas fa-home fa-lg"></i><span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
						    <a class="nav-link" href="../takeout.php"><i class="fas fa-upload fa-lg"></i></a>
						</li>
						<li class="nav-item ">
						    <a class="nav-link" href="../retrieve.php"><i class="fas fa-download fa-lg"></i></a>
						</li>
            <li class="nav-item">
						    <a class="nav-link" href="../adatok.php"><i class="fas fa-database fa-lg"></i></a>
						</li>
            <li class="nav-item">
                        <a class="nav-link" href="../pathfinder.php"><i class="fas fa-project-diagram fa-lg"></i></a>
            </li>
            <li class="nav-item active">
                        <a class="nav-link" href="#"><i class="fas fa-calendar-alt fa-lg"></i></a>
            </li>
            <li class="nav-item">
                        <a class="nav-link" href="../profile/index.php"><i class="fas fa-user-alt fa-lg"></i></a>
            </li>
            <li>
              <a class="nav-link disabled" href="#">Időzár <span id="time">10:00</span></a>
            </li>
            <?php if (($_SESSION['role']=="Admin") || ($_SESSION['role']=="Boss")){
              echo '<li><a class="nav-link disabled" href="#">Admin jogokkal rendelkezel</a></li>';}?>
					  </ul>
						<form class="form-inline my-2 my-lg-0" action=../utility/logout.ut.php>
                      <button class="btn btn-danger my-2 my-sm-0" type="submit">Kijelentkezés</button>
                      </form>
					  <a class="nav-link my-2 my-sm-0" href="#"><span onclick="openNav()"><i class="fas fa-question-circle fa-lg"></i></span></a>
					</div>
</nav>
    <body>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Esemény hozzáadása</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h6>Esemény hozzáadása <span id="addEventInterval"></span> időben</h6>
        <form id="sendAddEvent">
        <input class="form-control" id="addEventName" type="text" placeholder="esemény címe"></input>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
        <input type="submit" class="btn btn-primary"></button>
        <input type="hidden" id="addEventStartVal"></input>
        <input type="hidden" id="addEventEndVal"></input>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- TÖRLÉS MODAL -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Esemény törlése</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h6 class="bg-warning text-black" align="center">Biztosan törölni szeretnéd a(z) <span id="deleteEventName2"></span> eseményt?</h6>
        <form id="sendDelEvent">
      </div>
      <div class="modal-footer">
      <span id="deleteEventName"></span>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégsem</button>
        <input type="submit" class="btn btn-danger" value="Törlés"></button>
        <input type="hidden" id="delEventId"></input>
        <input type="hidden" id="delEventTitle"></input>
        </form>
      </div>
    </div>
  </div>
</div>
<table class="table table-bordered" height=90%><tr>
    <td width=100%><div id='calendar'></td></div>
</table>

    <div id="sideHelp1" class="sideHelp">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()"><i class="fas fa-times fa-2x"></i></a>
  <h3 class="text-white">Eseménynaptár - segítség</h3>
  <span class="badge badge-success">Hozzáadás</span><h6 class="text-white">Jelöld ki a naptárban az időszakot, majd töltsd ki a felugró ablakot</h6>
  <span class="badge badge-danger">Törlés</span><h6 class="text-white">Kattints rá az adott eseményre, majd válaszd ki a törlés opciót</h6>
  <span class="badge badge-info">Áttevés</span><h6 class="text-white">Húzd át az eseményt egy másik napra/időpontra</h6>
  <span class="badge badge-dark">Rövidítés/hosszabítás</span><h6 class="text-white">Heti nézetben kezdd el az eseményt le/felfele húzni, akkár több napon át.</h6>
</div>
  </body>
</html>
<style>
.sideHelp {
  height: 100%; /* 100% Full-height */
  width: 0; /* 0 width - change this with JavaScript */
  position: fixed; /* Stay in place */
  z-index: 1; /* Stay on top */
  top: 0; /* Stay at the top */
  left: 0;
  background-color: #222; /* Black*/
  overflow-x: hidden; /* Disable horizontal scroll */
  padding-top: 60px; /* Place content 60px from the top */
  transition: 0.5s; /* 0.5 second transition effect to slide in the sidenav */
  padding-left: 10px;
}

.closebtn{
  color:white;
  transition: .8s ease-in-out;
  display: block;
}
.closebtn:hover{
  color:red;
  transform: rotateX(45deg);
  transition: 0.5s;
  -webkit-transform:rotateX(45deg);
   -moz-transform:rotateX(45deg);
   -o-transform:rotateX(45deg); 
}
#calendar{
  margin-left: 2%;
  width: 90%;
}

#deleteEventName{
  position: relative;
  color: #dbdbdb;
  text-align: left;
  font-size: 10;
  align:right;
}

#exampleModalLabel{
  position: absolute;
  font-size: 30;
}
</style>

 <script>
function openNav() {
  document.getElementById("sideHelp1").style.width = "250px";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
  document.getElementById("sideHelp1").style.width = "0";
}

(function(){
  setInterval(updateTime, 1000);
});

function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
      minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (timer > 60){
          $('#time').animate({'opacity': 0.9}, 0, function(){
          $(this).html(display.textContent).animate({'opacity': 1}, 500);
          setTimeout(function() { $("#time").text(display.textContent).animate({'opacity': 1}, 250); }, 700);;});
        }

        if (timer < 60){
          $('#time').animate({'opacity': 0.9}, 0, function(){
          $(this).html("<font color='red'>"+display.textContent+"</font").animate({'opacity': 1}, 500);
          setTimeout(function() { $("#time").html("<font color='red'>"+display.textContent+"</font").animate({'opacity': 1}, 250); }, 700);;});
        }

        if (--timer < 0) {
            timer = duration;
            window.location.href = "../utility/logout.ut.php"
        }
    }, 1000);
}

window.onload = function () {
    var fiveMinutes = 60 * 10 - 1,
        display = document.querySelector('#time');
    startTimer(fiveMinutes, display);
    setInterval(updateTime, 1000);
    updateTime();
};
 </script>