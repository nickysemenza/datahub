<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="token" content="{{ Session::token() }}">
    <title>title</title>
    @section('css')
    {{ HTML::style('css/bootstrap.min.css'); }}
    {{ HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css'); }}
    @show

    @section('js')
    {{ HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'); }}
    {{ HTML::script('js/bootstrap.min.js'); }}
    @show
</head>
<body>
{{ HTML::script('js/tracking.js'); }}

@include('nav')
<div class="container" style="margin-top:20px;">
    <div class="row">
        <div class="col-md-12">
            <h1 id="heading"> @section('append_header')@show</h1>
            @yield('content')
        </div>
    </div>
</div>
@section('bottom_js')
@show
</body>
</html>