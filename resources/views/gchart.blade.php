@extends('layout')

@section('content')

</body>

@section('append_heading')
@parent
Chartz yo
@stop

@section('bottom_js')
@parent
<div id="chart_div" style="width: 100%; height: 900px;"></div>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable({!!$data['chartdata']!!});

        var options = {
            //curveType: 'function',
            lineWidth: 4,
            title: '{!!$data['chartname']!!}'
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>
@stop

@stop