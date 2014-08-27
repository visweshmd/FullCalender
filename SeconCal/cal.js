$("#subb").click(function(theForm) {
    //value = /* get some value */
    var cals = ["#ebuisness", "#norton", "#smb", "#abs", "#symc", "#sympay", "#part", "#velocity", "#et", "#new"];
    if (document.getElementById('subb').value == 'Submit')
    {
        var ar = $('#calendar').fullCalendar('clientEvents');
        var aa = window.id;
        var tit = document.getElementById('name').value;
        var stat = document.getElementById('status');
        var end = document.getElementById('endDate').value;
        var des = document.getElementById('Summary').value;
        var col = stat.options[stat.selectedIndex].value;

        var evve;
        for (m = 0; m < ar.length; m++)
        {
            if (ar[m].id == aa)
            {
                evve = ar[m];
            }
        }


        evve.title = tit;
        evve.color = col;
        evve.desc = des;
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
        //alert(end);
        var tend;
        if (end != "")
        {
            //alert('reached');
            evve.end = end;
            tend = end;
        }
        else
        {
            var sss = moment(evve.start);
            //alert(sss.add('d',1));
            evve.end = sss.add('d', 1);
            tend = evve.end.format('YYYY-MM-DD');
        }

        $('#calendar').fullCalendar('updateEvent', evve);
        var tstart = evve.start.format('YYYY-MM-DD');
        var tempdep;
        //alert('here 1');
        $.ajax({
            type: "POST",
            url: 'hhhh.php',
            dataType: 'json',
            async: false,
            data: {title: evve.title,
                end: tend,
                desc: evve.desc,
                cal: 'calendar',
                start: tstart,
                status: status,
                id: evve.id
            },
            complete: function(response) {
                $('#output').html(response.responseText);
                //window.temp = response.responseText;
                //updateimpacts();
            },
            error: function() {
                $('#output').html('Bummer: there was an error!');
            },
        });




        var eventObject = {
            title: tit,
            start: evve.start,
            end: evve.end,
            color: '#ff0000',
            id: evve.id,
            desc: des
        };
        //alert(window.temp);
        var selected = [];
        $('#openModal input:checked').each(function() {
            selected.push($(this).attr('name'));
        });
        //alert(evve.end);
        for (j = 0; j < cals.length; j++)
        {
            $(cals[j]).fullCalendar('removeEvents', evve.id);
        }

        //alert(window.temp);
        for (i = 0; i < selected.length; i++)
        {
            var temp = '#'.concat(selected[i]);
            $(temp).fullCalendar('renderEvent', eventObject, true);


            $.ajax({
                type: "POST",
                url: 'hhhh.php',
                dataType: 'json',
                async: false,
                data: {title: evve.title,
                    end: tend,
                    desc: evve.desc,
                    cal: selected[i],
                    start: tstart,
                    status: status,
                    id: evve.id
                },
                complete: function(response) {
                    $('#output').html(response.responseText);
                    //window.temp = response.responseText;
                    //updateimpacts();
                },
                error: function() {
                    $('#output').html('Bummer: there was an error!');
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
        if (end == '')
        {
            end = a[0].end.format('YYYY-MM-DD');
        }
        //alert(end);
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
        a[0].start = start;
        a[0].end = end;
        a[0].color = col;
        a[0].title = tit;
        a[0].desc = des;
        $('#calendar').fullCalendar('updateEvent', a[0]);
        //alert('id');
        $.ajax({
            type: "POST",
            url: 'update.php',
            dataType: 'json',
            async: false,
            data: {title: tit,
                end: end,
                desc: des,
                cal: 'calendar',
                start: start,
                status: status,
                id: id
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
        //alert('id');
        var eventObject = {
            title: tit,
            start: start,
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
                //$('#output').html(response.responseText);
                //window.temp = response.responseText;
                //updateimpacts();
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
                    //$('#output').html(response.responseText);
                    //window.temp = response.responseText;
                    //updateimpacts();
                },
                error: function() {
                    $('#output').html('Bummer: there was an error!');
                },
            });
        }




        window.open("#close", "_self");
    }
});


$("#close").click(function() {
    //alert('complete');
    var cals = ["#calendar,#ebuisness", "#norton", "#smb", "#abs", "#symc", "#sympay", "#part", "#velocity", "#et", "#new"];
    var id = window.id;
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
            //$('#output').html(response.responseText);
            //window.temp = response.responseText;
            //updateimpacts();
        },
        error: function() {
            $('#output').html('Bummer: there was an error!');
        },
    });
    //$("#calender").fullCalendar( 'rerenderEvents' )
    //alert('sucess');
    window.open("#close", "_self");
});
//reason += validateName(theForm.name);
//reason += validatePhone(theForm.phone);
//reason += validateEmail(theForm.emaile);