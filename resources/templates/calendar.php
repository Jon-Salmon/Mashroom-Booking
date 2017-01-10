
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
        eventClick: function(calEvent, jsEvent, view) {
            $("#eventContent").dialog("option", "title", calEvent.title);
            $("#startDate").html(moment(calEvent.start).format('Do MMM YYYY'));
            $("#startTime").html(moment(calEvent.start).format('h:mm A'));
            $("#endTime").html(moment(calEvent.end).format('h:mm A'));
            $("#eventInfo").html(calEvent.description.replace(/\n/g, "<br />"));
            $('#eventContent').dialog('open');
        },
        dayClick: function(date, jsEvent, view) {
            if (view.name == 'month'){
                setTimeout(function() {
                    $('#calendar') 
                        .fullCalendar('changeView', 'agendaWeek'/* or 'basicDay' */);
                    $('#calendar') 
                        .fullCalendar('gotoDate', date); 
                }, 1);
            }
        },
        height: function(){
            return $( window ).height() - 90;
        },
        events: 'events.php',
        scrollTime: '08:00:00',
        firstDay: 1
        // put your options and callbacks here
    })

    $("#eventContent").dialog({ 
        modal: true, 
        autoOpen: false,
        title: "Event details",
        width:350,
        open: function(){
            $(".validateTips").removeClass( "alert-danger" );
            jQuery('.ui-widget-overlay').bind('click',function(){
                jQuery('#eventContent   ').dialog('close');
            })
        }
        });

});

</script>
<div id="eventContent" class="display" title="Event Details" style="display:none;">
    <p id="eventInfo"></p>
    Date: <span id="startDate"></span><br>
    Start: <span id="startTime"></span><br>
    End: <span id="endTime"></span><br><br>
</div>
<div id='calendar'></div>