
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
                    start: '8:00',
                    end:   '22:00',
                    dow: [ 0, 1, 2, 3, 4, 5, 6]
            },
        eventConstraint: "businessHours",
        selectConstraint: "businessHours",
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
                    jQuery('#eventContent   ').dialog('close');
                    })
                }
                });
        });
        },
        eventClick: function(calEvent, jsEvent, view) {
            $('#eventStart').datepicker("setDate", new Date(calEvent.start));
            $('#eventEnd').datepicker("setDate", new Date(calEvent.end));
            $('#calEventDialog #eventTitle').val(calEvent.title);
            $('#calEventDialog #allday').val([calEvent.className == "gbcs-halfday-event" ? "1" : "2"]).prop('checked', true);
            $("#calEventDialog").dialog("option", "buttons", [
                {
                text: "Save",
                click: function() {
                    $(this).dialog("close");
                }},
            {
                text: "Delete",
                click: function() {
                    $(this).dialog("close");
                }},
            {
                text: "Cancel",
                click: function() {
                    $(this).dialog("close");
                }}
            ]);
            $("#calEventDialog").dialog("option", "title", "Edit Event");
            $('#calEventDialog').dialog('open');
        },
        editable: true,
        selectable: true,
		selectHelper: true,
        select: function(start, end) {
            $('#calEventDialog #eventStart').val(start);
            $('#calEventDialog #eventEnd').val(end);
            $('#calEventDialog').dialog('open');

				var title = prompt('Event Title:');
				var eventData;
				if (title) {
					eventData = {
						title: title,
						start: start,
						end: end
					};
                    $.ajax({ url: '/dev/public_html/ajax/eventAdd.php',
                            data: {action: JSON.stringify(eventData)},
                            type: 'post',
                            success: function(output) {
                                        alert(output);
                                    }
                    });
					$('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
				}
				$('#calendar').fullCalendar('unselect');
			},
        eventOverlap: false,
        selectOverlap: false,
        events: '<?php echo HTTP_ROOT ?>ajax/events.php',
        // put your options and callbacks here
    });


    var title = $('#eventTitle');
    var start = $('#eventStart');
    var end = $('#eventEnd');
    var eventClass, color;
    $('#calEventDialog').dialog({
        resizable: false,
        autoOpen: false,
        title: 'Add Event',
        width: 400,
        buttons: {
            Save: function() {
                if (title.val() !== '') {
                    $myCalendar.fullCalendar('renderEvent', {
                        title: title.val(),
                        start: start.val(),
                        end: end.val(),
                    }, true // make the event "stick"
                    );
                }
                $myCalendar.fullCalendar('unselect');
                $(this).dialog('close');
            },
            Cancel: function() {
                $(this).dialog('close');
            }
        }

    });
});



</script>

<div id="calEventDialog" class="dialog">
    <form>
        <fieldset>
        <label for="eventTitle">Title</label>
        <input type="text" name="eventTitle" id="eventTitle" /><br>
        <label for="eventStart">Start Date</label>
        <input type="text" name="eventStart" id="eventStart" /><br>
        <label for="eventEnd">End Date</label>
        <input type="text" name="eventEnd" id="eventEnd" /><br>
        </fieldset>
    </form>
</div>

<div id="eventContent" class="display" title="Event Details" style="display:none;">
    <p id="eventInfo"></p>
    Start: <span id="startTime"></span><br>
    End: <span id="endTime"></span><br><br>
</div>
<div id='calendar'></div>