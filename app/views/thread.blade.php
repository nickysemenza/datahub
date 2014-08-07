@extends('layout')

@section('content')


<table class="table table-striped table-bordered sortable">
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
Thread view - {{$data['thread_id']}}
@stop

@section('title')
@parent
thread view
@stop

@stop