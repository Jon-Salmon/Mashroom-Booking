
<script src='<?php echo HTTP_ROOT ?>js/moment.min.js'></script>
<script src='<?php echo HTTP_ROOT ?>js/fullcalendar.js'></script>
<script src="<?php echo HTTP_ROOT ?>js/knockout-2.3.0.js" type="text/javascript"></script>
<script src="<?php echo HTTP_ROOT ?>js/moment-datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo HTTP_ROOT ?>js/jquery.timepicker.min.js"></script>

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
        eventClick: function(calEvent, jsEvent, view) {
            if (calEvent.editable == 1){
            $('#eventDate').datepicker("set", calEvent.start);
            $('#eventStart').timepicker("setTime", new Date(calEvent.start));
            $('#eventEnd').timepicker("setTime", new Date(calEvent.end));
            $('#calEventDialog #eventTitle').val(calEvent.title);
            $('#calEventDialog #eventDetails').val(calEvent.details);
            $("#calEventDialog").dialog("option", "buttons", [
                {
                text: "Save",
                click: function() {
                    $(this).dialog("close");
                }},
            {
                text: "Delete",
                click: function() {
                    $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/eventChange.php',
                            data: {
                                action: 'delete',
                                data: JSON.stringify(calEvent.id)
                            },
                            type: 'post',
                            success: function(output) {
                                        $('#calendar').fullCalendar('refetchEvents');
                                        if (output == '1'){
                                            $('#calEventDialog').dialog("close");
                                        }
                                        else {
                                            $('#calEventDialog').dialog("close");
                                            alert("Oops, something went wrong. Try again later or, if the problem persists, notify the webmaster.");
                                        }
                                    }
                    });
                }},
            {
                text: "Cancel",
                click: function() {
                    $(this).dialog("close");
                }}
            ]);
            $("#calEventDialog").dialog("option", "title", "Edit Event: " + calEvent.title);
            $('#calEventDialog').dialog('open');

            } else {
            $("#eventContent").dialog("option", "title", calEvent.title);
            $("#startDate").html(moment(calEvent.start).format('Do MMM YYYY'));
            $("#startTime").html(moment(calEvent.start).format('h:mm A'));
            $("#endTime").html(moment(calEvent.end).format('h:mm A'));
            $("#eventInfo").html(calEvent.description.replace(/\n/g, "<br />"));
            $('#eventContent').dialog('open');
            }
        },
        editable: false,
        selectable: true,
		selectHelper: true,
        select: function(start, end) {
            $('#eventDate').datepicker("set", start);
            $('#eventStart').timepicker("setTime", new Date(start));
            $('#eventEnd').timepicker("setTime", new Date(end));
            $('#calEventDialog #eventTitle').val("");
            $('#calEventDialog #eventDetails').val("");
            
            $('#calEventDialog').dialog('open');

				var title = prompt('Event Title:');
				var eventData;
				if (title) {
					eventData = {
						title: title,
						start: start,
						end: end
					};
                    $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/eventChange.php',
                            data: {
                                action: 'add',
                                data: JSON.stringify(eventData)
                            },
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
        eventRender: function(event, element) {
            event.editable = event.editAllowed;
        }
        // put your options and callbacks here
    });


    var title = $('#eventTitle');
    var start = $('#eventStart');
    var end = $('#eventEnd');
    var eventClass, color;
    $('#eventDate').datepicker();
    $('#eventStart').timepicker({
        'scrollDefault': '09:00',
        'timeFormat': 'H:i',
        'className' : 'time-dropdown',
        'step': 15
    });
    $('#eventEnd').timepicker({
        'scrollDefault': '09:00',
        'timeFormat': 'H:i',
        'minTime':'00:00',
        'showDuration':true,
        'className' : 'time-dropdown',
        'step': 15
    });
    $('#eventStart').on('changeTime', function() {
        $('#eventEnd').timepicker('option', 'minTime', $(this).val());
    });

    $("#eventContent").dialog({ 
        modal: true, 
        autoOpen: false,
        title: "Event details",
        width:350,
        open: function(){
        jQuery('.ui-widget-overlay').bind('click',function(){
            jQuery('#eventContent   ').dialog('close');
            })
        }
        });
    $('#calEventDialog').dialog({
        resizable: false,
        autoOpen: false,
        title: 'Add Event',
        width: 400,
        modal: true,
        open: function(){
        jQuery('.ui-widget-overlay').bind('click',function(){
            jQuery('#calEventDialog').dialog('close');
            })
        },
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
        <label for="eventTitle">Title: </label>
        <input type="text" name="eventTitle" class="form-control" id="eventTitle" /><br>
        <label for="eventDate">Date: </label>
        <div class="input-group date" id="eventDate" data-datepicker-format="DD-MM-YYYY">
            <input name="eventDate" class="form-control" type="text" size="16">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
        <label for="eventStart">Start: </label>
        <input type="text" name="eventStart" class="time form-control" id="eventStart" /><br>
        <label for="eventEnd">End: </label>
        <input type="text" name="eventEnd" class="time form-control" id="eventEnd" /><br>
        <label for="eventDetails">Details: </label>
        <textarea name="eventDetails" class="form-control" id="eventDetails"></textarea><br>
        </fieldset>
    </form>
</div>

<div id="eventContent" class="display" title="Event Details" style="display:none;">
    <p id="eventInfo"></p>
    Date: <span id="startDate"></span><br>
    Start: <span id="startTime"></span><br>
    End: <span id="endTime"></span><br><br>
</div>
<div id='calendar'></div>