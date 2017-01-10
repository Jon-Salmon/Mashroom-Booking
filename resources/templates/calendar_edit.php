
<script src='<?php echo HTTP_ROOT ?>js/moment.min.js'></script>
<script src='<?php echo HTTP_ROOT ?>js/fullcalendar.js'></script>
<script src="<?php echo HTTP_ROOT ?>js/knockout-2.3.0.js" type="text/javascript"></script>
<script src="<?php echo HTTP_ROOT ?>js/moment-datepicker.min.js"></script>
<script src="js/jquery.timepicker.min.js"></script>
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
                    start: '08:00',
                    end:   '22:30',
                    dow: [ 0, 1, 2, 3, 4, 5, 6]
            },
        eventConstraint:
            {
                start: '00:00',    
                end: '24:00',
                dow: [ 0, 1, 2, 3, 4, 5, 6]
            },
        selectConstraint:
            {
                start: '00:00',    
                end: '24:00',
                dow: [ 0, 1, 2, 3, 4, 5, 6]
            },
        eventClick: function(calEvent, jsEvent, view) {
            if (calEvent.editable == 1 && !calEvent.end.isBefore()){
            $('#eventDate').datepicker("set", calEvent.start);
            $('#eventStart').timepicker("setTime", new Date(calEvent.start));
            $('#eventEnd').timepicker("setTime", new Date(calEvent.end));
            $('#eventEnd').timepicker('option', 'minTime', $('#eventStart').val());
            $('#calEventDialog #eventTitle').val(calEvent.title);
            $('#calEventDialog #eventDetails').val(calEvent.details);
            allFields.removeClass( "ui-state-error" );
            tips.text("");
            $("#calEventDialog").dialog("option", "buttons", [
                {
                text: "Save",
                click: function() {
                    var resultArray = updateEvent(calEvent.id);
                    if (resultArray[0]){
                        $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/eventChange.php',
                                dataType: "json",
                                data: {
                                    action: 'change',
                                    data: JSON.stringify(resultArray[1])
                                },
                                type: 'post',
                                    success: function(output) {
                                                if (output[0]){
                                                    $('#calendar').fullCalendar('refetchEvents');
                                                    $('#calEventDialog').dialog("close");
                                                } else {
                                                    updateTips(output[1]);
                                                }
                                            }
                        });
                    }
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
            $(".validateTips").removeClass( "alert-danger" );
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
        selectable: {
            month: false,
            agenda: true
        },
		selectHelper: true,
        select: function(start, end) {
            if (!end.isBefore()){
            $('#eventDate').datepicker("set", start);
            $('#eventStart').timepicker("setTime", new Date(start));
            $('#eventEnd').timepicker("setTime", new Date(end));
            $('#eventEnd').timepicker('option', 'minTime', $('#eventStart').val());
            $('#calEventDialog #eventTitle').val("");
            $('#calEventDialog #eventDetails').val("");
            allFields.removeClass( "ui-state-error" );
            tips.text("");
            
            $("#calEventDialog").dialog("option", "buttons", [
                {
                text: "Save",
                click: function() {
                    var resultArray = updateEvent(0);
                    if (resultArray[0]){
                            $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/eventChange.php',
                                    dataType: "json",
                                    data: {
                                        action: 'add',
                                        data: JSON.stringify(resultArray[1])
                                    },
                                    type: 'post',
                                    success: function(output) {
                                                if (output[0]){
                                                    $('#calendar').fullCalendar('refetchEvents');
                                                    $('#calEventDialog').dialog("close");
                                                } else {
                                                    updateTips(output[1]);
                                                }
				                                $('#calendar').fullCalendar('unselect');
                                            }
                            });
                        }
                }},
            {
                text: "Cancel",
                click: function() {
                    $(this).dialog("close");
                    $('#calendar').fullCalendar('unselect');
                }}
            ]);
            $(".validateTips").removeClass( "alert-danger" );
            $('#calEventDialog').dialog('open');

			} else {
				$('#calendar').fullCalendar('unselect');
            }
        },
        eventOverlap: false,
        selectOverlap: false,
        events: '<?php echo HTTP_ROOT ?>ajax/events.php',
        eventRender: function(event, element) {
            event.editable = event.editAllowed;
        },
        eventResize: function(event, delta, revertFunc) {
            var data = {
                'start': event.start.format() + ".000Z",
                'end': event.end.format() + ".000Z",
                'id': event.id,
                'title': event.band,
                'details': event.details
            };
            $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/eventChange.php',
                    dataType: "json",
                    data: {
                        action: 'change',
                        data: JSON.stringify(data)
                    },
                    type: 'post',
                    success: function(output) {
                                if (output[0]){
                                    $('#calendar').fullCalendar('refetchEvents');
                                } else {
                                    $("#message").html(output[1]);
                                    $('#errorDisplay').dialog('open');
                                    revertFunc();
                                }
                            }
            });
        },
        eventDrop: function(event, delta, revertFunc) {
            var data = {
                'start': event.start.format() + ".000Z",
                'end': event.end.format() + ".000Z",
                'id': event.id,
                'title': event.band,
                'details': event.details
            };
            $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/eventChange.php',
                    dataType: "json",
                    data: {
                        action: 'change',
                        data: JSON.stringify(data)
                    },
                    type: 'post',
                    success: function(output) {
                                if (output[0]){
                                    $('#calendar').fullCalendar('refetchEvents');
                                } else {
                                    $("#message").html(output[1]);
                                    $('#errorDisplay').dialog('open');
                                    revertFunc();
                                }
                            }
            });
        },
        dayClick: function(date, jsEvent, view) {
            if (view.name = 'month'){
                $('#calendar') 
                    .fullCalendar('changeView', 'agendaWeek'/* or 'basicDay' */);
                $('#calendar') 
                    .fullCalendar('gotoDate', date); 
                setTimeout(function() {
                    $('#calEventDialog').dialog('close');
                }, 1);
            }
        },
        height: function(){
            return $( window ).height() - 90;
        },
	scrollTime: '08:00:00',
	firstDay: 1
        // put your options and callbacks here
    });


    var title = $('#eventTitle');
    var start = $('#eventStart');
    var end = $('#eventEnd');
    var eventClass, color;

    $('#eventDate').datepicker({'weekStart':1});
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
        'step': 15,
        'maxTime': '24:00'
    });
    $('#eventStart').on('changeTime', function() {
        $('#eventEnd').timepicker('option', 'minTime', $(this).val());
        var start = $('#eventStart').timepicker('getTime');
        var end = $('#eventEnd').timepicker('getTime');
        if (start >= end){
            $('#eventEnd').timepicker('setTime', new Date(start.getTime() + 15*60000));
        }
    });

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
    $("#errorDisplay").dialog({ 
        modal: true, 
        autoOpen: false,
        title: "Error",
        open: function(){
        jQuery('.ui-widget-overlay').bind('click',function(){
            jQuery('#errorDisplay').dialog('close');
            })
        }
        });
        
    $('#calEventDialog').dialog({
        resizable: false,
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_POST['newBooking'] == "1") {
                echo "autoOpen: true,";
            }
        } else {
            echo "autoOpen: false,";
        }
        ?>
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
                    var resultArray = updateEvent(0);
                    if (resultArray[0]){
                            $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/eventChange.php',
                                    dataType: "json",
                                    data: {
                                        action: 'add',
                                        data: JSON.stringify(resultArray[1])
                                    },
                                    type: 'post',
                                    success: function(output) {
                                                if (output[0]){
                                                    $('#calendar').fullCalendar('refetchEvents');
                                                    $('#calEventDialog').dialog("close");
                                                } else {
                                                    updateTips(output[1]);
                                                }
                                            }
                            });
                        }
            },
            Cancel: function() {
                $(this).dialog('close');
            }
        }

    });

    var 
 
      // From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
      eventDate = $( "#eventDate" ),
      eventStart = $( "#eventStart" ),
      eventEnd = $( "#eventEnd" ),
      eventTitle = $("#eventTitle"),
      eventDetails = $("#eventDetails"),
      allFields = $( [] ).add( eventDate).add( eventStart).add(eventEnd ).add(eventTitle ).add(eventDetails ),
      tips = $( ".validateTips" );
 
    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "alert alert-danger" );
    }
 
    function checkLength( o, n, min, max ) {
      if ( o.length > max || o.length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }

    function checkSet( o, v) {
      if ( v == null || v == ""  ) {
        o.addClass( "ui-state-error" );
        updateTips( "Invalid value");
        return false;
      } else {
        return true;
      }
    }
 
    function updateEvent(id) {
        var valid = true;
        allFields.removeClass( "ui-state-error" );

        var data = {
            'date' : eventDate.datepicker('get').toDate(),
            'title': eventTitle.val(),
            'details': eventDetails.val(),
            'id': id
        };
    
        data.start = eventStart.timepicker('getTime', data.date);
        data.end = eventEnd.timepicker('getTime', data.date);

        if (data.start > data.end){
            if (data.end.getHours() == 0 && data.end.getMinutes() == 0){
                var tempDate = new Date(data.end);
                data.end.setDate(tempDate.getDate() + 1);
            }
            else {
            updateTips("End time cannot be after start time.");
            eventEnd.addClass("ui-state-error");
            valid = false;
            }
        }

        valid = valid && checkLength(data.details, "title", 0, 255);
 
      return [valid, data];
    }
    
    $("#newBooking").click(function(e) {
        e.preventDefault();
        
        if ($('#navbar').hasClass("in") == true){
            $('.navbar-toggle').click();
        }
        
        $('#eventStart').val("");
        $('#eventEnd').val("");
        $('#calEventDialog #eventTitle').val("");
        $('#calEventDialog #eventDetails').val("");
        allFields.removeClass( "ui-state-error" );
        tips.text("");
        
        $("#calEventDialog").dialog("option", "buttons", [
            {
            text: "Save",
            click: function() {
                var resultArray = updateEvent(0);
                if (resultArray[0]){
                        $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/eventChange.php',
                                dataType: "json",
                                data: {
                                    action: 'add',
                                    data: JSON.stringify(resultArray[1])
                                },
                                type: 'post',
                                success: function(output) {
                                            if (output[0]){
                                                $('#calendar').fullCalendar('refetchEvents');
                                                $('#calEventDialog').dialog("close");
                                            } else {
                                                updateTips(output[1]);
                                            }
                                            $('#calendar').fullCalendar('unselect');
                                        }
                        });
                    }
            }},
        {
            text: "Cancel",
            click: function() {
                $(this).dialog("close");
                $('#calendar').fullCalendar('unselect');
            }}
        ]);
        $(".validateTips").removeClass( "alert-danger" );
        $('#calEventDialog').dialog('open');
    });
  });


</script>

<div id="calEventDialog" class="dialog">
    <form>
        <fieldset>
        <div class="table-style">
        <div class="dialog-element">
        <label for="eventTitle" class="one-line-input">Title: </label>
        <span class="input-box"><input type="text" name="eventTitle" class="form-control" id="eventTitle" /></span>
        </div>
        <div class="dialog-element">
        <label for="eventDate" class="one-line-input">Date: </label>
        <div class="input-group date" id="eventDate" data-datepicker-format="DD-MM-YYYY">
            <input name="eventDate" class="form-control" type="text" size="16" readonly>
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
        </div>
        <div class="dialog-element">
        <label for="eventStart" class="one-line-input">Start: </label>
        <span class="input-box"><input type="text" name="eventStart" class="time form-control" id="eventStart" /></span>
        </div>
        <div class="dialog-element">
        <label for="eventEnd" class="one-line-input">End: </label>
        <span class="input-box" id="end"><input type="text" name="eventEnd" class="time form-control" id="eventEnd" /></span>
        </div>
        </div>
        <div id="detail-div">
        <label for="eventDetails">Details: </label>
        <textarea name="eventDetails" class="form-control" id="eventDetails"></textarea>
        <div>
        </fieldset>
    </form>
  <p class="alert validateTips"></p>
</div>


<div id="eventContent" class="display" title="Event Details" style="display:none;">
    <p id="eventInfo"></p>
    Date: <span id="startDate"></span><br>
    Start: <span id="startTime"></span><br>
    End: <span id="endTime"></span><br><br>
</div>

<div id="errorDisplay" class="display" title="Error" style="display:none;">
    <span id="message"></span><br><br>
</div>

<div id='calendar'></div>
