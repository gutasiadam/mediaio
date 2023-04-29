
console.log("Default display")
document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: ['dayGrid', 'timeGrid', 'interaction', 'moment', 'bootstrap'],
    locale: 'hu',
    themeSystem: 'bootstrap',
    firstDay: 1,
    editable: true,
    droppable: true,
    height: "parent",
    width: "auto",
    nowIndicator: true,
    buttonText: {
      today: 'ma',
      month: 'hónap',
      week: 'hét'
    },
    header: {
      left: 'title',
      center: '',
      right: 'timeGridWeek dayGridMonth today prev,next,'
    },
    events: 'EventManager.php?o=load',
    selectable: true,
    selectHelper: true,
    windowResize: function (view) {
      calendar.updateSize();
    },
    eventClick: function (info) {
      var id = info.event.id;
      var title = info.event.title;
      console.log(id + title)
      document.getElementById('delEventTitle').value = title;
      document.getElementById('deleteEventName').innerHTML = title;
      document.getElementById('delEventId').value = id;
      var workSheetURL = "./worksheet.php?eventId=" + id;
      $('#deleteModal').modal('show');
      $('#optionsLabel').text(title);

      $("#worksheetShow").submit(function () {
        window.open(workSheetURL);
      })
    }
  });
  calendar.render();
});