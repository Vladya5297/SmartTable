<html>
<head>
    <title>Скорость обслуживания</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
<header>
    <p>Samsung IoT Кейс Лаборатория</p>
</header>
<div class="graphpage_container">
    <div class="search_form">
        <input type="text" id="employee">
        <button id="confirm_employee">Показать</button>
    </div>
    <div class="graph_container">
        <div id="curve_chart" style="width: 900px; height: 450px"></div>
    </div>
</div>
</body>
<script>
    $('#confirm_employee').click(function () {
        console.log("clicked");
        var json;
        $.post("serve_event.php", {serve_event: "get_events", employee: $('#employee').val()}, function (data) {
            json = JSON.parse(data);
            console.log(json);


            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(json);

                var options = {
                    title: 'Service speed',
                    curveType: 'function',
                    legend: {position: 'bottom'}
                };

                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

                chart.draw(data, options);
            }
        });
    });
</script>
</html>