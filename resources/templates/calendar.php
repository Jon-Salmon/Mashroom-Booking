
<script src='<?php echo HTTP_ROOT ?>js/moment.min.js'></script>
<script src='<?php echo HTTP_ROOT ?>js/fullcalendar.js'></script>
<script>

$(document).ready(function() {

    // page is now ready, initialize the calendar...

    $('#calendar').fullCalendar({
        columnFormat: 'ddd D/M',
        defaultView: 'agendaWeek',
        header: {
            left: 'title',
            right: 'prev,next today agendaWeek,month'
        },
        allDaySlot: false,
        events: '<?php echo HTTP_ROOT ?>ajax/events.php',
        // put your options and callbacks here
    })

});

</script>
<div id='calendar'></div>