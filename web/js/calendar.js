document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay' // Add views here
        },
        views: {
            dayGridMonth: { // Monthly view
                buttonText: 'Month'
            },
            timeGridWeek: { // Weekly view
                buttonText: 'Week'
            },
            timeGridDay: { // Daily view
                buttonText: 'Day'
            }
        },
        events: function(fetchInfo, successCallback, failureCallback) {
           fetch('index.php?r=task/fetch-events', {
               method: 'POST',
               headers: {
                   'Content-Type': 'application/json',
                   'X-CSRF-Token': yii.getCsrfToken() // Yii2 CSRF token
               }
           })
           .then(response => response.json())
           .then(events => successCallback(events))
           .catch(error => failureCallback(error));
       },
       dateClick: function(info) {
          // Get the current view type
          var viewType = calendar.view.type;

          // Variables to hold formatted date and time
          var formattedDate = '';
          var formattedTime = '';

          if (viewType === 'dayGridMonth') {
              // Month view: Only date in Y-m-d format
              formattedDate = info.dateStr; // Already in Y-m-d format
          } else if (viewType === 'timeGridWeek' || viewType === 'timeGridDay') {
              // Week or Day view: Date in Y-m-d and time in h:i AM/PM
              var date = new Date(info.date);

              // Format date as Y-m-d
              formattedDate = date.toISOString().split('T')[0];

              // Format time as h:i AM/PM
              var hours = date.getHours();
              var minutes = date.getMinutes();
              var ampm = hours >= 12 ? 'PM' : 'AM';
              hours = hours % 12;
              hours = hours ? hours : 12; // the hour '0' should be '12'
              minutes = minutes < 10 ? '0'+minutes : minutes;
              formattedTime = hours + ':' + minutes + ' ' + ampm;
          }

          // Display the modal
          $('#calendarModal').modal('show');

          // Update modal content based on the view
          if (viewType === 'dayGridMonth') {
              // $('#calendarModalLabel').text('Add Event for ' + formattedDate);
              $('#tbltask-due_date').val(formattedDate);
          } else if (viewType === 'timeGridWeek' || viewType === 'timeGridDay') {
              // $('#calendarModalLabel').text('Add Event for ' + formattedDate + ' at ' + formattedTime);
              $('#tbltask-due_date').val(formattedDate);
              $('#tbltask-time').val(formattedTime);
          }
      },
      eventClick: function(info) {
          var eventId = info.event.id;
          // alert(eventId);
          // Fetch event details using the event ID
          fetch(`index.php?r=task/get-task-details&id=${eventId}`, {
              method: 'GET',
              headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-Token': yii.getCsrfToken()
              }
          })
          .then(response => response.json())
          .then(data => {
              // Populate the modal with event details
              // console.log(data);
              $("#task_id").val(eventId);
              $('#task_title').val(data.title);
              $('#task_description').val(data.description);
              $('#task_due_date').val(data.startDate);
              $('#task_time').val(data.time); // If applicable
              $('#task_assigned_to').val(data.assigned_to); // If applicable
              $('#task_status_id').val(data.status); // If applicable
              $('#calendarModalUpdate').modal('show');
          })
          .catch(error => {
              console.error('Error fetching event details:', error);
          });
      }
    });
    calendar.render();
});
