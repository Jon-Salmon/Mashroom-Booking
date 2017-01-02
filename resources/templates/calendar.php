
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
        businessHours:
            {
                    start: '08:00',
                    end:   '22:30',
                    dow: [ 0, 1, 2, 3, 4, 5, 6]
            },
        eventRender: function (event, element) {
            element.attr('href', 'javascript:void(0);');
            element.click(function() {
                $("#startTime").html(moment(event.start).format('MMM Do h:mm A'));
                $("#endTime").html(moment(event.end).format('MMM Do h:mm A'));
                $("#eventInfo").html(event.description.replace(/\n/g, "<br />"));
                $("#eventContent").dialog({ 
                    modal: true,
                    title: event.title,
                    width:350,
                    open: function(){
                    jQuery('.ui-widget-overlay').bind('click',function(){
                        jQuery('#calEventDialog').dialog('close');
                        })
                    }
                });
            });
        },
        events: '<?php echo HTTP_ROOT ?>ajax/events.php',
        // put your options and callbacks here
    })

});

</script>
<div id="eventContent" title="Event Details" style="display:none;">
    <p id="eventInfo"></p>
    Start: <span id="startTime"></span><br>
    End: <span id="endTime"></span><br><br>
</div>
<div id='calendar'></div>