/* ── Events Calendar (#events - standalone page) ── */
if ($("#events").length > 0) {
  document.addEventListener("DOMContentLoaded", function () {
    var calendarEl = document.getElementById("events");
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: "dayGridMonth",
      headerToolbar: {
        start: "title",
        center: "dayGridMonth,dayGridWeek,dayGridDay",
        end: "custombtn",
      },
      customButtons: {
        custombtn: {
          text: "Add New Event",
          click: function () {
            var myModal = new bootstrap.Modal(document.getElementById("add_event"));
            myModal.show();
          },
        },
      },
      eventClick: function (info) {
        var modalTitle = document.getElementById("eventTitle");
        modalTitle.textContent = info.event.title;
        var eventModal = new bootstrap.Modal(document.getElementById("eventModal"));
        eventModal.show();
      },
    });
    calendar.render();
  });
}

/* ── Dashboard Calendar (#calendar) ── */
if ($("#calendar").length > 0) {
  document.addEventListener("DOMContentLoaded", function () {
    var todayDate = moment().startOf("day");
    var TODAY = todayDate.format("YYYY-MM-DD");

    var calendarEl = document.getElementById("calendar");
    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
      },

      height: 500,
      contentHeight: 580,
      aspectRatio: 3,

      views: {
        dayGridMonth: { buttonText: "month" },
        timeGridWeek: { buttonText: "week" },
        timeGridDay: { buttonText: "day" },
      },

      initialView: "dayGridMonth",
      initialDate: TODAY,

      editable: true,
      dayMaxEvents: true,
      navLinks: true,

      /* ── Load events from database via AJAX ── */
      events: "calendar_api.php?action=fetch",

      /* ── Click an event to view details ── */
      eventClick: function (info) {
        info.jsEvent.preventDefault();

        var evt = info.event;
        var props = evt.extendedProps || {};

        $("#viewEventTitle").html('<i class="fa-solid fa-calendar-day me-2"></i>' + evt.title);

        var dateStr = evt.start
          ? moment(evt.start).format("DD MMM YYYY")
          : "-";
        if (evt.end) {
          dateStr += " to " + moment(evt.end).format("DD MMM YYYY");
        }
        if (evt.allDay) {
          dateStr += " (All Day)";
        }
        $("#viewEventDate").text(dateStr);

        var typeLabel = props.type
          ? props.type.charAt(0).toUpperCase() + props.type.slice(1)
          : "Event";
        var badge =
          props.type === "holiday"
            ? '<span class="badge bg-danger">Holiday</span>'
            : props.type === "reminder"
            ? '<span class="badge bg-warning text-dark">Reminder</span>'
            : '<span class="badge bg-primary">Event</span>';
        $("#viewEventType").html(badge);

        $("#viewEventDesc").text(props.description || "No description");

        $("#deleteEventBtn").data("id", evt.id);

        var viewModal = new bootstrap.Modal(
          document.getElementById("viewEventModal")
        );
        viewModal.show();
      },

      /* ── Drag to resize/move (optional, saves nothing) ── */
      eventDrop: function (info) {
        /* no-op for now, could add an update endpoint */
      },
    });

    calendar.render();

    /* ── Add Event Button ── */
    $("#addEventBtn").on("click", function () {
      $("#eventTitle").val("");
      $("#eventStartDate").val(TODAY);
      $("#eventEndDate").val("");
      $("#eventType").val("event");
      $("#eventColor").val("#007bff");
      $("#eventAllDay").prop("checked", true);
      $("#eventDescription").val("");
      var modal = new bootstrap.Modal(document.getElementById("addEventModal"));
      modal.show();
    });

    /* ── Save Event ── */
    $("#saveEventBtn").on("click", function () {
      var title = $.trim($("#eventTitle").val());
      var startDate = $("#eventStartDate").val();

      if (!title || !startDate) {
        Swal.fire({
          toast: true,
          position: "bottom-end",
          icon: "warning",
          title: "Title and start date are required",
          showConfirmButton: false,
          timer: 3000,
        });
        return;
      }

      var $btn = $(this).prop("disabled", true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Saving...');

      $.post(
        "calendar_api.php?action=add",
        {
          title: title,
          start_date: startDate,
          end_date: $("#eventEndDate").val() || null,
          all_day: $("#eventAllDay").is(":checked") ? 1 : 0,
          color: $("#eventColor").val(),
          event_type: $("#eventType").val(),
          description: $("#eventDescription").val(),
        },
        function (res) {
          if (res.success) {
            calendar.refetchEvents();
            bootstrap.Modal.getInstance(
              document.getElementById("addEventModal")
            ).hide();
            Swal.fire({
              toast: true,
              position: "bottom-end",
              icon: "success",
              title: "Event added successfully",
              showConfirmButton: false,
              timer: 2000,
            });
          } else {
            Swal.fire({
              toast: true,
              position: "bottom-end",
              icon: "error",
              title: res.error || "Failed to add event",
              showConfirmButton: false,
              timer: 3000,
            });
          }
        },
        "json"
      ).fail(function () {
        Swal.fire({
          toast: true,
          position: "bottom-end",
          icon: "error",
          title: "Server error. Please try again.",
          showConfirmButton: false,
          timer: 3000,
        });
      }).always(function () {
        $btn.prop("disabled", false).html('<i class="fa-solid fa-check me-1"></i>Save Event');
      });
    });

    /* ── Delete Event ── */
    $("#deleteEventBtn").on("click", function () {
      var eventId = $(this).data("id");
      if (!eventId) return;

      Swal.fire({
        title: "Delete Event?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it",
      }).then(function (result) {
        if (result.isConfirmed) {
          $.post(
            "calendar_api.php?action=delete",
            { id: eventId },
            function (res) {
              if (res.success) {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(
                  document.getElementById("viewEventModal")
                ).hide();
                Swal.fire({
                  toast: true,
                  position: "bottom-end",
                  icon: "success",
                  title: "Event deleted",
                  showConfirmButton: false,
                  timer: 2000,
                });
              }
            },
            "json"
          );
        }
      });
    });
  });
}
