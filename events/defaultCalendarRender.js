      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
          plugins: [ 'dayGrid', 'timeGrid', 'interaction', 'moment', 'bootstrap' ],
          locale: 'hu',
    themeSystem: 'bootstrap',
    firstDay: 1,
    editable:false,
    droppable:true,
    height: "parent",
    width: "auto",
    nowIndicator: true,
    buttonText:{
    today:    'ma',
    month:    'hónap',
    week:     'hét'
    },
    header:{
    left:   'title',
    center: '',
    right:  'timeGridWeek dayGridMonth today prev,next'
},
    events: 'load.php',
    selectable:true,
    selectHelper:true,
  windowResize: function(view) {
    calendar.updateSize();
  },
  select: function(info) {
    var startval = info.startStr;
    var endval = info.endStr;
    console.log(startval+' '+endval);
    document.getElementById('addEventInterval').innerHTML = startval+ ' - '+endval;
    document.getElementById('addEventStartVal').value = startval;
    document.getElementById('addEventEndVal').value = endval;;
    $('#exampleModal').modal('show');
    $(".mailSend").fadeIn(2500);
    $( "#sendAddEvent" ).submit(function() {
      setTimeout(function(){ 
        console.log( "Adder call!"); 
      }, 2000);
  title = document.getElementById('addEventName').value;
  type = document.getElementById('eventTypeSelect').value;
  start = startval;
  end = endval;
  color= "#f7f7f7";
  console.log(title);
  console.log(startval);
  console.log(color);
  console.log(endval);
     if(title!="")
     {
      $.ajax({
       url:"insertprep.php",
       type:"POST",
       data:{title:title, start:start, end:end, color:color, type:type},
       success:function(sVal)
       {
        if(sVal==1){
          $('#exampleModal').modal('hide');      
        }
        if(sVal==0){
          alert("Valami nem éppen sikerült :(");
        }
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
     document.getElementById('delEventId').value = id;
     var workSheetURL = "./worksheet.php/?eventId="+id;
     $('#deleteModal').modal('show');
     
    $("#worksheetShow").submit(function() {
      window.open(workSheetURL);
    })
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