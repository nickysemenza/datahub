@extends('layout')

@section('content')

    {{--<table class = 'table table-bordered'>--}}
        {{--@foreach($data['sticker_links'] as $k=>$v)--}}
            {{--<tr>--}}
                {{--<td><img src="{{$k}}"></td>--}}
                {{--<td>{{$v}}</td>--}}
            {{--</tr>--}}
        {{--@endforeach--}}
    {{--</table>--}}

    @foreach($data['sticker_links'] as $k=>$v)
        <div style="border: 1px solid black;  display: inline-block;">
        <img src="{{$k}}" width="200px">
        <br/><h4>{{$v}}</h4>
        </div>
    @endforeach
@section('append_heading')
    @parent
    Stickers
@stop

@section('title')
    @parent
    title

@stop

@stop