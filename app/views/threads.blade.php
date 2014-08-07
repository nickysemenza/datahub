@extends('layout')

@section('content')
@section('append_header')
@parent
<style>

    .bar {
        fill: steelblue;
    }

    .bar:hover {
        fill: brown;
    }

    .axis {
        font: 10px sans-serif;
    }

    .axis path,
    .axis line {
        fill: none;
        stroke: #000;
        shape-rendering: crispEdges;
    }

    .x.axis path {
        display: none;
    }

</style>
@stop


<div class="chart">
</div>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script>

    var margin = {top: 20, right: 20, bottom: 150, left: 40},
        width = 1560 - margin.left - margin.right,
        height = 500 - margin.top - margin.bottom;

    var x = d3.scale.ordinal()
        .rangeRoundBands([0, width], .1,.5);

    var y = d3.scale.linear()
        .range([height, 0]);

    var xAxis = d3.svg.axis()
        .scale(x)
        .orient("bottom");

    var yAxis = d3.svg.axis()
        .scale(y)
        .orient("left")

    var svg = d3.select(".chart").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        d3.json("/fb/json/threads", function(error, data) {

        data.forEach(function(d) {
            d.message_count = +d.message_count;
        });

        x.domain(data.map(function(d) { return d.people; }));
        y.domain([0, d3.max(data, function(d) { return d.message_count; })]);

        svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis)
            .selectAll("text")
            .style("text-anchor", "end")
            .attr("dx", "-.8em")
            .attr("dy", ".15em")
            .attr("transform", function(d) {
                return "rotate(-60)"
            });


        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis)
            .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", 6)
            .attr("dy", ".71em")
            .style("text-anchor", "end")
            .text("message_count");

        svg.selectAll(".bar")
            .data(data)
            .enter().append("rect")
            .attr("class", "bar")
            .attr("x", function(d) { return x(d.people); })
            .attr("width", x.rangeBand())
            .attr("y", function(d) { return y(d.message_count); })
            .attr("height", function(d) { return height - y(d.message_count); });

    });

</script>

<table class="table table-striped table-bordered sortable">
    <thead>
    <tr>
        <th>message_count</th>
        <th>thread_id</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data['threads'] as $thread)
    <tr>
        <td>{{$thread['message_count']}}</td>
        <td><a href="{{action('FBController@getThread', array('thread_id' => $thread['thread_id']))}}">{{$thread['people']}}</a></td>
    </tr>
    @endforeach
    </tbody>
</table>

@section('append_heading')
@parent
Threads
@stop

@section('title')
@parent
Threads
@stop

@stop