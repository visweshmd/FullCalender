<html>
    <head>
        <meta charset='utf-8' />
        <link href='fullcalendar-2.0.2/fullcalendar.css' rel='stylesheet' />
        <link href='fullcalendar-2.0.2/fullcalendar.print.css' rel='stylesheet' media='print' />
        <link href='jquery-ui-1.11.1.custom/jquery-ui.css' rel='stylesheet' />
        <link href='jquery-ui-1.11.1.custom/jquery-ui.min.css' rel='stylesheet' />
        <link href='jquery-ui-1.11.1.custom/jquery-ui.structure.css' rel='stylesheet' />
        <link href='jquery-ui-1.11.1.custom/jquery-ui.theme.css' rel='stylesheet' />
        <link rel="stylesheet" type="text/css" href="mystyle.css">
        <link rel="stylesheet" type="text/css" href="cal.css">
        <script src='fullcalendar-2.0.2/lib/moment.min.js'></script>
        <script src='fullcalendar-2.0.2/lib/jquery.min.js'></script>
        <script src='fullcalendar-2.0.2/lib/jquery-ui.custom.min.js'></script>
        <script src='fullcalendar-2.0.2/fullcalendar.min.js'></script>
        <script src="http://jqueryvalidation.org/files/dist/jquery.validate.min.js"></script>
        <script src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>

        <?php
        include("db.php");
        ?>
        <?php
        $query = 'SELECT * FROM EVENTS';
        $newquery = 'SELECT Html_ID FROM Platforms';
        // Make a MySQL Connection
        $con = mysqli_connect(SERVER, USERNAME, PASSWORD);
        mysqli_select_db($con, DB);
        $result = mysqli_query($con, $query);
        $newresult = mysqli_query($con, $newquery);
        while ($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        while ($newrow = mysqli_fetch_array($newresult)) {
            $newrows[] = $newrow;
        }
        $output = json_encode($rows);
        $newoutput = json_encode($newrows);
        mysqli_free_result($result);
        mysqli_close($con);
        ?>
        <script>
            var inc = 0;
            $(document).ready(function() {
                $('#external-events div.external-event').each(function() {
                    var eventObject = {
                        title: $.trim($(this).text()) // use the element's text as the event title

                    };
                    // store the Event Object in the DOM element so we can get to it later
                    $(this).data('eventObject', eventObject);
                    // make the event draggable using jQuery UI
                    $(this).draggable({
                        zIndex: 999,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0  //  original position after the drag
                    });
                });
                $('#calendar').fullCalendar({
                    header: {
                        left: '',
                        center: '',
                        right: 'title'
                    },
                    editable: true,
                    defaultView: 'basicWeek',
                    //theme: true,
                    height: $("#external-events").height(),
                    droppable: true, // this allows things to be dropped onto the calendar !!!
                    drop: function(date) { // this function is called when something is dropped
                        var originalEventObject = $(this).data('eventObject');
                        var copiedEventObject = $.extend({}, originalEventObject);
                        var nam = copiedEventObject.title;
                        var sss = moment(date);
                        var end = sss.add('d', 1);
                        $('#openModal input:checkbox').each(function() {
                            this.checked = false;
                        });
                        document.getElementById("name").value = nam;
                        document.getElementById("status").value = '#FFA500';
                        document.getElementById("startDate").value = date.format('YYYY-MM-DD');
                        document.getElementById("endDate").value = end.format('YYYY-MM-DD');
                        document.getElementById("Summary").value = '';
                        document.getElementById("subb").value = 'Submit';
                        var newWindow = window.open("#openModal", "_self");
                        copiedEventObject.start = date;
                        inc = inc + 1;
                        newWindow.id = inc;
                        newWindow.date = date;
                        window.mode = 1;
                        copiedEventObject.id = inc;


                        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);


                    },
                    eventClick: function(calEvent, jsEvent, view) {
                        document.getElementById("name").value = calEvent.title;

                        document.getElementById("startDate").value = calEvent.start.format('YYYY-MM-DD');
                        document.getElementById("endDate").value = calEvent.end.format('YYYY-MM-DD');
                        document.getElementById("Summary").value = calEvent.desc;
                        if (typeof calEvent.color === 'undefined')
                        {
                            document.getElementById("subb").value = 'Submit';
                            document.getElementById("status").value = '#FFA500';
                        }
                        else
                        {
                            document.getElementById("subb").value = 'Update';
                            document.getElementById("status").value = calEvent.color;
                        }
                        var idtofind = calEvent.id;
                        var cals = [];
                        var calarr = <?php echo $newoutput; ?>;
                        for (h = 0; h < calarr.length; h++)
                        {
                            cals[h] = calarr[h]['Html_ID'];
                        }
                        for (m = 0; m < cals.length; m++)
                        {
                            document.getElementsByName(cals[m])[0].checked = false;
                            var a = $('#'.concat(cals[m])).fullCalendar('clientEvents', calEvent.id);
                            if (a[0] != undefined)
                            {
                                document.getElementsByName(cals[m])[0].checked = true;
                            }

                        }
                        var newWindow = window.open("#openModal", "_self");
                        newWindow.id = calEvent.id;
                    },
                    eventResize: function(event, delta, revertFunc) {
                        var cals = [];
                        var calarr = <?php echo $newoutput; ?>;
                        for (h = 0; h < calarr.length; h++)
                        {
                            cals[h] = calarr[h]['Html_ID'];
                        }

                        for (u = 0; u < cals.length; u++)
                        {
                            var a = $('#'.concat(cals[u])).fullCalendar('clientEvents', event.id);
                            if (a != '')
                            {
                                var tempend = a[0].end;
                                if (tempend.isAfter(event.end))
                                {
                                    a[0].end = event.end;
                                    $('#'.concat(cals[u])).fullCalendar('updateEvent', a[0]);
                                    $.ajax({
                                        type: "POST",
                                        url: 'update.php',
                                        dataType: 'json',
                                        async: false,
                                        data: {title: a[0].title,
                                            end: a[0].end.format('YYYY-MM-DD'),
                                            desc: a[0].desc,
                                            cal: cals[u],
                                            start: a[0].start.format('YYYY-MM-DD'),
                                            status: 'TO-Do',
                                            id: a[0].id,
                                            comment: ' '
                                        },
                                        complete: function(response) {
                                            //$('#output').html(response.responseText);
                                            

                                        },
                                        error: function() {
                                            $('#output').html('Bummer: there was an error!');
                                        },
                                    });
                                }
                            }
                        }
                        var status;
                        if (event.color == '#FFA500')
                        {
                            status = 'To-Do';
                        }
                        if (event.color == '#008000')
                        {
                            status = 'In-Progress';
                        }
                        if (event.color == '#808080')
                        {
                            status = 'Done';
                        }
                        var change = delta.asHours();
                        var endtochange;
                        if (change < 0)
                        {
                            endtochange = moment(event.end).add(Math.abs(change));
                        }
                        else
                        {
                            endtochange = moment(event.end).subtract(Math.abs(change));
                        }

                        $.ajax({
                            type: "POST",
                            url: 'update.php',
                            dataType: 'json',
                            async: false,
                            data: {title: event.title,
                                end: event.end.format('YYYY-MM-DD'),
                                desc: event.desc,
                                cal: 'calendar',
                                start: event.start.format('YYYY-MM-DD'),
                                status: status,
                                id: event.id,
                                comment: '\nEvent End Changed to '.concat(endtochange.format('YYYY-MM-DD'))
                            },
                            complete: function(response) {
                                //$('#output').html(response.responseText);
                                //window.temp = response.responseText;
                                //updateimpacts();
                            },
                            error: function() {
                                $('#output').html('Bummer: there was an error!');
                            },
                        });
                    },
                    eventDrop: function(event, delta, revertFunc) {
                        var cals1 = [];
                        var calarr = <?php echo $newoutput; ?>;
                        for (t = 0; t < calarr.length; t++)
                        {
                            cals1[t] = '#'.concat(calarr[t]['Html_ID']);
                        }

                        var status;
                        if (event.color == '#FFA500')
                        {
                            status = 'To-Do';
                        }
                        if (event.color == '#008000')
                        {
                            status = 'In-Progress';
                        }
                        if (event.color == '#808080')
                        {
                            status = 'Done';
                        }
                        var newdel = delta.asHours();

                        var addstart;
                        var addend;
                        if (newdel < 0)
                        {
                            addstart = moment(event.start).add(Math.abs(newdel), 'hours');
                            addend = moment(event.end).add(Math.abs(newdel), 'hours');
                        }
                        else
                        {
                            //alert('Pinnadi');
                            addstart = moment(event.start).subtract(newdel, 'hours');
                            addend = moment(event.end).subtract(newdel, 'hours');
                        }
                        $.ajax({
                            type: "POST",
                            url: 'update.php',
                            dataType: 'json',
                            async: false,
                            data: {title: event.title,
                                end: event.end.format('YYYY-MM-DD'),
                                desc: event.desc,
                                cal: 'calendar',
                                start: event.start.format('YYYY-MM-DD'),
                                status: status,
                                id: event.id,
                                comment: ('\n Event start Changed from '.concat(addstart.format('YYYY-MM-DD'))).concat('to ').concat(event.start.format('YYYY-MM-DD'))
                            },
                            complete: function(response) {
                                //$('#output').html(response.responseText);
                                //window.temp = response.responseText;
                                //updateimpacts();

                            },
                            error: function() {
                                $('#output').html('Bummer: there was an error!');
                                alert('Failure');

                            },
                        });

                        var diff = delta.as('days');
                        for (k = 0; k < cals1.length; k++)
                        {
                            var eve = $(cals1[k]).fullCalendar('clientEvents');
                            if (eve.length != 0)
                            {
                                for (l = 0; l < eve.length; l++)
                                {
                                    if (eve[l].id == event.id)
                                    {

                                        var newstart = (eve[l].start).add('d', diff);
                                        var newend = (eve[l].end).add('d', diff);
                                        eve[l].start = newstart;
                                        eve[l].end = newend;
                                        $(cals1[k]).fullCalendar('updateEvent', eve[l]);

                                        $.ajax({
                                            type: "POST",
                                            url: 'update.php',
                                            dataType: 'json',
                                            async: false,
                                            data: {title: eve[l].title,
                                                end: eve[l].end.format('YYYY-MM-DD'),
                                                desc: eve[l].desc,
                                                cal: cals1[k].substring(1),
                                                start: eve[l].start.format('YYYY-MM-DD'),
                                                status: 'TO-Do',
                                                id: eve[l].id,
                                                comment: ' '
                                            },
                                            complete: function(response) {
                                                //$('#output').html(response.responseText);
                                                //window.temp = response.responseText;
                                                //updateimpacts();

                                            },
                                            error: function() {
                                                $('#output').html('Bummer: there was an error!');
                                            },
                                        });
                                    }
                                }
                            }
                        }
                    }
                });
<?php
$query3 = 'SELECT Html_ID,Name FROM Platforms';
$con1 = mysqli_connect(SERVER, USERNAME, PASSWORD);
mysqli_select_db($con1, DB);
$result3 = mysqli_query($con1, $query3);
while ($row3 = mysqli_fetch_array($result3)) {
    echo "$('#" . $row3['Html_ID'] . "').fullCalendar({
                    header: false,
                    editable: true,
                    defaultView: 'basicWeek',
                    height: 40,
                    //theme: true,
                    columnFormat: '',
                    droppable: true,
                    eventDrop: function(event, delta, revertFunc) {
                        var ar = $('#calendar').fullCalendar('clientEvents', event.id);
                        parentstart = ar[0].start;
                        parentend = ar[0].end;
                        if(parentstart.isAfter(event.start,'day'))
                        {
                            alert('Sorry Impact Cannot be Before Deployment');
                            revertFunc();
                        }
                        if(parentend.isBefore(event.end,'day'))
                        {
                           alert('Sorry Impact Cannot be After Deployment');
                           revertFunc();
                        }
                        $.ajax({
                            type: 'POST',
                            url: 'update.php',
                            dataType: 'json',
                            async: false,
                            data: {title: event.title,
                                end: event.end.format('YYYY-MM-DD'),
                                desc: event.desc,
                                cal: '" . $row3['Html_ID'] . "',
                                start: event.start.format('YYYY-MM-DD'),
                                status: 'To-DO',
                                id: event.id,
                                comment: ''
                            },
                            complete: function(response) {
                                //$('#output').html(response.responseText);
                                //window.temp = response.responseText;
                                //updateimpacts();
                            },
                            error: function() {
                                $('#output').html('Bummer: there was an error!');
                            },
                        });

                    },
                    eventResize: function(event, delta, revertFunc) {
                        var ar = $('#calendar').fullCalendar('clientEvents', event.id);
                        parentend = ar[0].end;
                        if(parentend.isBefore(event.end,'day'))
                        {
                           alert('Sorry Impact Cannot be After Deployment');
                           revertFunc();
                        }
                        $.ajax({
                            type: 'POST',
                            url: 'update.php',
                            dataType: 'json',
                            async: false,
                            data: {title: event.title,
                                end: event.end.format('YYYY-MM-DD'),
                                desc: event.desc,
                                cal: '" . $row3['Html_ID'] . "',
                                start: event.start.format('YYYY-MM-DD'),
                                status: 'To-DO',
                                id: event.id,
                                 comment: ''
                            },
                            complete: function(response) {
                                //$('#output').html(response.responseText);
                                //window.temp = response.responseText;
                                //updateimpacts();
                                
                            },
                            error: function() {
                                //$('#output').html('Bummer: there was an error!');
                                
                            },
                        });
                        
                    }, /// this allows things to be dropped onto the calendar !!!
                    drop: function(date) { // this function is called when something is dropped

                        // retrieve the dropped element's stored Event Object
                        var originalEventObject = $(this).data('eventObject');
                        // we need to copy it, so that multiple events don't have a reference to the same object
                        var copiedEventObject = $.extend({}, originalEventObject);
                        // assign it the date that was reported
                        copiedEventObject.start = date;
                        // render the event on the calendar
                        // the last `true` argument determines if the event 'sticks' (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                        $('#new').fullCalendar('renderEvent', copiedEventObject, true);
                        // is the 'remove after drop' checkbox checked?


                    }
                });";
}
mysqli_free_result($result3);
mysqli_close($con1);
?>
                var data = <?php echo $output; ?>;
                for (i = 0; i < data.length; i++)
                {
                    var title = data[i]['Title'];
                    var id = data[i]['ID'];
                    var status = data[i]['Status'];
                    var start = data[i]['Start'];
                    var desc = data[i]['Descrip'];
                    var end = data[i]['End'];
                    var cal = data[i]['Calender'];
                    var active = data[i]['Active'];
                    var colour;
                    if (status == 'To-Do')
                    {
                        colour = '#FFA500';
                    }
                    if (status == 'In-Progress')
                    {
                        colour = '#008000';
                    }
                    if (status == 'Done')
                    {
                        colour = '#808080';
                    }
                    if (cal != 'calendar')
                    {
                        colour = '#ff0000';
                    }
                    var eventObject = {
                        title: title,
                        start: start,
                        color: colour,
                        id: id,
                        desc: desc,
                        end: end
                    }

                    var catoupdate = '#'.concat(cal);
                    if (active == 1)
                    {
                        $(catoupdate).fullCalendar('renderEvent', eventObject, true);
                    }

                }
                inc = data.length - 1;

            });</script>

    <h1>CAB Calender</h1>    
</head>
<body onload="loaderf()">
    <div  class="wrapper">

        <div>
        </div>

        <div class="call"><button style="float: left" id='left'><-</button><button style="float: left" id='right'>-></button></div>
        <div style='clear:both'></div>
    </div>
    <div  class="wrapper">

        <div id='external-events'>
            <h4>Events</h4>
            <p style="font-style: italic; text-align: center">Drag And Drop Events to The Main Calender</p>
            <?php
            $que = 'SELECT Html_ID,Name FROM Platforms';
            $con2 = mysqli_connect(SERVER, USERNAME, PASSWORD);
            mysqli_select_db($con2, DB);
            $resultset = mysqli_query($con2, $que);
            while ($rowout = mysqli_fetch_array($resultset)) {

                echo"<div class='external-event' >" . $rowout['Name'] . "</div>";
            }
            mysqli_free_result($resultset);
            mysqli_close($con2);
            ?>
            <p>
                <!--<input type='checkbox' id='drop-remove' />
                <label for='drop-remove'>remove after drop</label>-->
            </p>
        </div>

        <div id='calendar' class="call"></div>
        <div style='clear:both'></div>
    </div>
    <br>
    <div><p><h3>NRD Impacts</h3></div>
    <?php
    $query2 = 'SELECT Html_ID,Name FROM Platforms';
    $con3 = mysqli_connect(SERVER, USERNAME, PASSWORD);
    mysqli_select_db($con3, DB);
    $result2 = mysqli_query($con3, $query2);
    echo "<div  class='wrapper2'>";
    while ($row2 = mysqli_fetch_array($result2)) {

        echo
        "<div  class='wrapper'>
            <div>
                <div class='heee'>" . $row2['Name'] . "</div>
            </div>
            <div id=" . $row2['Html_ID'] . " class='call'></div>
            <div style='clear:both'></div>
            </div>";
    }
    echo "</div>";
    mysqli_free_result($result2);
    mysqli_close($con3);
    ?>
    <br>
    <div  class="wrapper">

        <div>
        </div>

        <div class="call">
            <table title="Legend" align = "Center">

                <tr>
                    <td style="background-color: #FFA500;width: 5%"></td>
                    <td>To-Do</td>

                    <td style="background-color: #008000;width: 5%"></td>
                    <td>In-Progress</td>
                </tr>
                <tr>
                    <td style="background-color: #808080;width: 5%"></td>
                    <td>Done</td>

                    <td style="background-color: #FF0000;width: 5%"></td>
                    <td>Impact</td>
                </tr>

            </table></div>
        <div style='clear:both'></div>
    </div>

    <script>
    </script>
    <script>
        $('#right').click(function() {
            var cals = [];
            var calarr = <?php echo $newoutput; ?>;
            for (h = 0; h < calarr.length; h++)
            {
                cals[h] = '#'.concat(calarr[h]['Html_ID']);
            }
            $('#calendar').fullCalendar('next');
            for (f = 0; f < cals.length; f++)
            {
                $(cals[f]).fullCalendar('next');
            }

        });
        $('#left').click(function() {
            var cals = [];
            var calarr = <?php echo $newoutput; ?>;
            for (h = 0; h < calarr.length; h++)
            {
                cals[h] = '#'.concat(calarr[h]['Html_ID']);
            }
            $('#calendar').fullCalendar('prev');
            for (f = 0; f < cals.length; f++)
            {
                $(cals[f]).fullCalendar('prev');
            }
        });

    </script>
    <div id="openModal" class="modalDialog">
        <div>
            <a href="#close" id="link" title="Close" class="close">X</a>
            <h2>CAB Details</h2>
            <form id ="formip">
                <table width ="100%">
                    <tr><td width ="25%" align="left"><label for="name">Name *</label></td><td align="left" width ="75%"><input type="text" name="name" id="name" value="" class="text ui-widget-content ui-corner-all"></td></tr>
                    <tr><td align="left"><label for="status">Status *</label></td><td align="left">
                            <select id ="status" name = "status">
                                <option value ="#FFA500" >Tentative</option>
                                <option value = "#008000">Approved</option>
                                <option value="#808080">Complete</option>
                            </select></td></tr>
                    <tr><td align="left"><label for="startDate">Start Date *</label></td><td align="left"> <input type="date" name="startDate" id="startDate" value="" class="text ui-widget-content ui-corner-all"></td></tr>
                    <tr><td align="left"><label for="endDate">End Date *</label></td><td align="left"> <input type="date" name="endDate" id="endDate" value="" class="text ui-widget-content ui-corner-all"></td></tr>
                    <tr><td align="left"><label for="Summary">Summary</label></td><td align="left"> <textarea rows="4" cols="30" name="Summary" id="Summary" class="text ui-widget-content ui-corner-all"></textarea></td></tr>
                    <tr><td align="left"><label for="impact">Impact</label></td><td align="left">
                            <?php
                            $query4 = 'SELECT Html_ID,Name FROM Platforms';
                            $con4 = mysqli_connect(SERVER, USERNAME, PASSWORD);
                            mysqli_select_db($con4, DB);
                            $result4 = mysqli_query($con4, $query4);
                            while ($row4 = mysqli_fetch_array($result4)) {
                                echo "<input type='checkbox' name='" . $row4['Html_ID'] . "' value='" . $row4['Name'] . "' >" . $row4['Name'];
                            }
                            mysqli_free_result($result4);
                            mysqli_close($con4);
                            ?>
                        </td></tr>
                </table>
                <input type="button" value="Submit" id="subb">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Delete" id="close">
            </form>
        </div>
    </div>
    <script>
        jQuery.validator.addMethod("greaterThan",
                function(value, element, params) {

                    if (!/Invalid|NaN/.test(new Date(value))) {
                        return new Date(value) > new Date($(params).val());
                    }

                    return isNaN(value) && isNaN($(params).val())
                            || (Number(value) > Number($(params).val()));
                }, 'Must be greater than StartDate.');
        jQuery.validator.setDefaults({
            debug: true,
            success: "valid"
        });
        $("#formip").validate({
            rules: {
                endDate: {greaterThan: "#startDate"},
                name: {required: true}

            }
        });
        $("#subb").click(function() {
            if ($("#formip").valid())
            {

                var cals = [];
                var calarr = <?php echo $newoutput; ?>;
                for (h = 0; h < calarr.length; h++)
                {
                    cals[h] = '#'.concat(calarr[h]['Html_ID']);
                }

                if (document.getElementById('subb').value == 'Submit')
                {

                    var aa = window.id;
                    var ar = $('#calendar').fullCalendar('clientEvents', aa);
                    var tit = document.getElementById('name').value;
                    var stat = document.getElementById('status');
                    var startdate = document.getElementById('startDate').value;
                    var end = document.getElementById('endDate').value;
                    var des = document.getElementById('Summary').value;
                    var col = stat.options[stat.selectedIndex].value;


                    ar[0].title = tit;
                    ar[0].color = col;
                    ar[0].desc = des;
                    ar[0].start = startdate;
                    ar[0].end = moment(end);
                    var status;
                    if (col == '#FFA500')
                    {
                        status = 'To-Do';
                    }
                    if (col == '#008000')
                    {
                        status = 'In-Progress';
                    }
                    if (col == '#808080')
                    {
                        status = 'Done';
                    }



                    $('#calendar').fullCalendar('updateEvent', ar[0]);
                    var ar2 = $('#calendar').fullCalendar('clientEvents', aa);
                    alert(ar2[0].end);
                    var tstart = ar[0].start.format('YYYY-MM-DD');
                    var tempdep;
                    $.ajax({
                        type: "POST",
                        url: 'hhhh.php',
                        dataType: 'json',
                        async: false,
                        data: {title: ar[0].title,
                            end: end,
                            desc: ar[0].desc,
                            cal: 'calendar',
                            start: tstart,
                            status: status,
                            id: ar[0].id
                        },
                        complete: function(response) {
                            //$('#output').html(response.responseText);
                            //window.temp = response.responseText;
                            //updateimpacts();
                        },
                        error: function() {
                            $('#output').html('Bummer: there was an error!');
                        },
                    });
                    var eventObject = {
                        title: tit,
                        start: ar[0].start,
                        end: ar[0].end,
                        color: '#ff0000',
                        id: ar[0].id,
                        desc: des
                    };
                    var selected = [];
                    $('#openModal input:checked').each(function() {
                        selected.push($(this).attr('name'));
                    });

                    for (j = 0; j < cals.length; j++)
                    {
                        $(cals[j]).fullCalendar('removeEvents', ar[0].id);
                    }
                    for (i = 0; i < selected.length; i++)
                    {
                        var temp = '#'.concat(selected[i]);
                        $(temp).fullCalendar('renderEvent', eventObject, true);
                        $.ajax({
                            type: "POST",
                            url: 'hhhh.php',
                            dataType: 'json',
                            async: false,
                            data: {title: ar[0].title,
                                end: end,
                                desc: ar[0].desc,
                                cal: selected[i],
                                start: tstart,
                                status: status,
                                id: ar[0].id
                            },
                            complete: function(response) {
                                //$('#output').html(response.responseText);
                                //window.temp = response.responseText;
                                //updateimpacts();
                            },
                            error: function() {
                                //$('#output').html('Bummer: there was an error!');
                            },
                        });
                    }
                    window.open("#close", "_self");
                }
                if (document.getElementById('subb').value == 'Update')
                {

                    var a = $('#calendar').fullCalendar('clientEvents', window.id);

                    var id = window.id;
                    var start = a[0].start.format('YYYY-MM-DD');
                    var end = document.getElementById('endDate').value;
                    var startdate = document.getElementById('startDate').value;
                    if (end == '')
                    {
                        end = a[0].end.format('YYYY-MM-DD');
                    }
                    var tit = document.getElementById('name').value;
                    var stat = document.getElementById('status');
                    var des = document.getElementById('Summary').value;
                    var col = stat.options[stat.selectedIndex].value;
                    if (col == '#FFA500')
                    {
                        status = 'To-Do';
                    }
                    if (col == '#008000')
                    {
                        status = 'In-Progress';
                    }
                    if (col == '#808080')
                    {
                        status = 'Done';
                    }
                    a[0].start = startdate;
                    a[0].end = end;
                    a[0].color = col;
                    a[0].title = tit;
                    a[0].desc = des;
                    $('#calendar').fullCalendar('updateEvent', a[0]);
                    $.ajax({
                        type: "POST",
                        url: 'update.php',
                        dataType: 'json',
                        async: false,
                        data: {title: tit,
                            end: end,
                            desc: des,
                            cal: 'calendar',
                            start: startdate,
                            status: status,
                            id: id,
                            comment: ('\n Event Start Changed from '.concat(start)).concat(' to ').concat(a[0].start)
                        },
                        complete: function(response) {
                        },
                        error: function() {
                            $('#output').html('Bummer: there was an error!');
                        },
                    });
                    var eventObject = {
                        title: tit,
                        start: startdate,
                        end: end,
                        color: '#ff0000',
                        id: id,
                        desc: des
                    };

                    var selected = [];
                    $('#openModal input:checked').each(function() {
                        selected.push($(this).attr('name'));
                    });

                    for (j = 0; j < cals.length; j++)
                    {
                        $(cals[j]).fullCalendar('removeEvents', id);
                    }
                    $.ajax({
                        type: "POST",
                        url: 'delete.php',
                        dataType: 'json',
                        async: false,
                        data: {
                            id: id
                        },
                        complete: function(response) {
                        },
                        error: function() {
                            $('#output').html('Bummer: there was an error!');
                        },
                    });
                    for (i = 0; i < selected.length; i++)
                    {
                        var temp = '#'.concat(selected[i]);
                        $(temp).fullCalendar('renderEvent', eventObject, true);
                        $.ajax({
                            type: "POST",
                            url: 'hhhh.php',
                            dataType: 'json',
                            async: false,
                            data: {title: eventObject.title,
                                end: eventObject.end,
                                desc: eventObject.desc,
                                cal: selected[i],
                                start: eventObject.start,
                                status: status,
                                id: eventObject.id
                            },
                            complete: function(response) {
                            },
                            error: function() {
                                $('#output').html('Bummer: there was an error!');
                            },
                        });
                    }
                    window.open("#close", "_self");
                }
            }
        });
        $("#close").click(function() {
            //alert('complete');
            //var cals = ["#calendar,#ebuisness", "#norton", "#smb", "#abs", "#symc", "#sympay", "#part", "#velocity", "#et", "#new"];
            var cals = [];
            var calarr = <?php echo $newoutput; ?>;
            for (h = 0; h < calarr.length; h++)
            {
                cals[h] = '#'.concat(calarr[h]['Html_ID']);
            }
            var id = window.id;
            $("#calendar").fullCalendar('removeEvents', id);
            for (p = 0; p < cals.length; p++)
            {
                $(cals[p]).fullCalendar('removeEvents', id);
            }
            $.ajax({
                type: "POST",
                url: 'deleteall.php',
                dataType: 'json',
                async: false,
                data: {
                    id: id
                },
                complete: function(response) {
                },
                error: function() {
                    $('#output').html('Bummer: there was an error!');
                },
            });
            window.open("#close", "_self");
        });
        $("#link").click(function() {
            var validator = $("#formip").validate();
            validator.resetForm();
        });
    </script>
    <div><p id="output"></p></div>
</body>
</html>
