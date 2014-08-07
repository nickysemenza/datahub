@extends('layout')

@section('content')

<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="/js/d3.layout.cloud.js"></script>

<script>
    (function() {
        var fill = d3.scale.category20();
        //what range of font sizes do we want, we will scale the word counts
        var fontSize = d3.scale.log().range([10, 90]);

        //create my cloud object
        var mycloud = d3.layout.cloud().size([2400, 900])
            .words([])
            .padding(2)
            .rotate(function() { return ~~(Math.random() * 2) * 90; })
            // .rotate(function() { return 0; })
            .font("Impact")
            .fontSize(function(d) { return fontSize(d.size); })
            .on("end", draw)

        //render the cloud with animations
        function draw(words) {
            //fade existing tag cloud out
            d3.select("body").selectAll("svg").selectAll("g")
                .transition()
                .duration(1000)
                .style("opacity", 1e-6)
                .remove();

            //render new tag cloud
            d3.select("body").selectAll("svg")
                .append("g")
                .attr("transform", "translate(300,300)")
                .selectAll("text")
                .data(words)
                .enter().append("text")
                .style("font-size", function(d) { return ((d.size)* 1) + "px"; })
                .style("font-family", "Impact")
                .style("fill", function(d, i) { return fill(i); })
                .style("opacity", 1e-6)
                .attr("text-anchor", "middle")
                .attr("transform", function(d) { return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")"; })
                .transition()
                .duration(1000)
                .style("opacity", 1)
                .text(function(d) { return d.text; });
        }

        //ajax call
        function get_words() {
            //make ajax call
            d3.json("/fb/json/thread_wordcloud/{{$data['thread_id']}}/", function(error,json) {
                if (error) return console.warn(error);
                var words_array = [];
                for (key in json){
                    words_array.push({text: key, size: json[key]})
                }
                console.log(json);

                //render cloud
                mycloud.stop().words(words_array).start();
            });
        };

        //create SVG container
        d3.select("body").append("svg")
            .attr("width", 2400)
            .attr("height", 900);

        //render first cloud
        get_words();

        //start streaming
        //var interval = setInterval(function(){get_words()}, 4000);
    })();
</script>


<h3>{{$data['thread_id']}}</h3>
<table class="table table-striped table-bordered sortable hidden ">
    <thead>
    <tr>
        <th>name</th>
        <th>message</th>
        <th>time</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data['messages'] as $message)
    <tr>
        <td>{{$message->from_name}}</td>
        <td>{{$message->message}}</td>
        <td>{{$message->time}}</td>
    </tr>
    @endforeach
    </tbody>
</table>

@section('append_heading')
@parent
Thread view | {{$data['names']}}
@stop

@section('title')
@parent
thread view
@stop

@stop