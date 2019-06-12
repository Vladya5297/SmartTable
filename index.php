<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Карта ресторана</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
<header>
    <p>Samsung IoT Cafeteria Project</p>
</header>

<div id="add_table_form" style="display: none" class="popup_form">
    <p>Запрос на добавление стола</p>
    <input type="text" id="new_table_coreid" disabled>
    <input type="number" id="new_table_number" placeholder="Введите номер стола">
    <div style="display: flex; justify-content: space-around">
        <button id="add_table">Отправить</button>
        <button onclick="hide(this)">Отмена</button>
    </div>
</div>

<div id="add_card_form" style="display: none" class="popup_form">
    <p>Запрос на добавление метки</p>
    <input type="text" id="new_card_id" disabled>
    <input type="text" id="new_card_name" placeholder="Введите имя карты">
    <div style="display: flex; justify-content: space-around">
        <button id="add_card">Отправить</button>
        <button onclick="hide(this)">Отмена</button>
    </div>
</div>

<div class="display_row">
    <div id="table_1" class="table">
        <h2>Table №1</h2>
        <img src="default_table.png" alt="Sample Text">
        <div id="table_1_timer" class="timer"></div>
        <div id="table_1_label" class="label"></div>
    </div>

    <div id="table_2" class="table">
        <h2>Table №2</h2>
        <img src="default_table.png" alt="Sample Text">
        <div id="table_2_timer" class="timer"></div>
        <div id="table_2_label" class="label"></div>
    </div>

    <div id="table_3" class="table">
        <h2>Table №3</h2>
        <img src="default_table.png" alt="Sample Text">
        <div id="table_3_timer" class="timer"></div>
        <div id="table_3_label" class="label"></div>
    </div>
</div>
</body>
</html>
<script>
    let tables_timers = new Map();
    let active_timers = new Map();
    get_tables_number();
    function get_tables_number() {
        $.post("get_tables.php", {data: "getData"}, function (data) {
            let json = JSON.parse(data);
            for (let i = 0; i < json.length; i++) {
                tables_timers.set("#table_"+json[i], 0);
                active_timers.set("#table_"+json[i], 0);
            }
        });
    }

    setInterval(getData, 1000);
    function getData() {
        $.post("updates.php", {data: "getData"}, onAjaxSuccess);
    }

    function onAjaxSuccess(data) {
        let json = JSON.parse(data);
        for (let i = 0; i < json.length; i++) {
            if (json[i]['add_table']) {addTable(json[i]['add_table']);}
            else if (json[i]['add_card']) {addCard(json[i]['add_card']);}
            else {
                let html_table_id = "#table_" + json[i]['table_number'];
                let card_to_img, label;
                if (json[i]['card_name'] == "menu") {
                    card_to_img = "warn_table.png";
                    label = "Примите заказ";
                    startTimer(html_table_id);
                } else if (json[i]['card_name'] == "dishware") {
                    card_to_img = "warn_table.png";
                    label = "Уберите посуду";
                    startTimer(html_table_id);
                } else {
                    card_to_img = "default_table.png";
                    label = "";
                    stopTimer(html_table_id, json[i]['card_name']);
                }
                $(html_table_id).children("img").attr('src', card_to_img);
                $(html_table_id).children(html_table_id+"_label").html(label);
            }
        }
    }

    function addTable(coreid) {
        $('#add_table_form').css("display", "flex");
        $('#new_table_coreid').val(coreid);
        $('#add_table').click(function () {
            $.post("add_table.php", {add_table: "new_table", coreid: coreid, table_number: $('#new_table_number').val()},
                function () {
                    $('#add_table_form').css("display", "none");
                });
        });
    }

    function addCard(data) {
        $('#add_card_form').css("display", "flex");
        $('#new_card_id').val(data);
        $('#add_card').click(function () {
            $.post("add_card.php", {add_card: "new_card", card_id: data, card_name: $('#new_card_name').val()},
                function () {
                    $('#add_card_form').css("display", "none");
                });
        });
    }

    function startTimer(html_table_id) {
        if (!tables_timers.get(html_table_id)) {
            tables_timers.set(html_table_id, new Date().getTime());
            active_timers.set(html_table_id, setInterval(function () {
                    let whole_time = parseInt((new Date().getTime() - tables_timers.get(html_table_id)) / 1000);
                    let h = parseInt(whole_time/3600);
                    let m = parseInt((whole_time-h*3600)/60);
                    let s = parseInt(whole_time-h*3600-m*60);
                    let hh, mm, ss;
                    if (h<10) hh="0"+h;
                    else hh=h;
                    if (m<10) mm="0"+m;
                    else mm=m;
                    if (s<10) ss="0"+s;
                    else ss=s;
                    $(html_table_id + "_timer").html(hh+":"+mm+":"+ss);
                    if (m>2) $(html_table_id).children("img").attr('src', "alarm_table.png");
                }, 100)
            );
        }
    }

    function stopTimer(html_table_id, employee) {
        if (tables_timers.get(html_table_id)) {
            tables_timers.set(html_table_id, 0);
            clearInterval(active_timers.get(html_table_id));
            let time = $(html_table_id + "_timer").html();
            $(html_table_id + "_timer").html("");
            $.post("serve_event.php", {serve_event: "new_event", employee: employee, time: time});
        }
    }

    function hide(Element) {
        $(Element).parent().parent().css('display', 'none');
    }
</script>