@extends('layout')

@section('content')

<div id="chart_div" style="width: 2000px; height: 800px;"></div>
</body>

@section('append_heading')
@parent
Chartz yo
@stop

@section('append_header')
@parent
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable({{$data['chartdata']}});

        var options = {
            //curveType: 'function',
            title: '{{$data['chartname']}}'
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>
@stop

@stop