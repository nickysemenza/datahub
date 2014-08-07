<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="token" content="{{ Session::token() }}">
    <title>@section('title')@show</title>
    @section('css')
    {{ HTML::style('css/bootstrap.min.css'); }}
    {{ HTML::style('css/bootstrap-sortable.css'); }}
    {{ HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css'); }}
    @show

    @section('js')
    {{ HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'); }}
    {{ HTML::script('js/bootstrap.min.js'); }}
    {{ HTML::script('js/bootstrap-sortable.js'); }}
    {{ HTML::script('js/moment.min.js'); }}
    @show

    @section('append_header')@show
    <script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>

</head>
<body>

@include('nav')
<div class="container" style="margin-top:20px;">
    <h1 id="heading">@section('append_heading')@show</h1>
    @yield('content')
</div>
@section('bottom_js')
@show
</body>
</html>