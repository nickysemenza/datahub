@extends('layout')

@section('content')


<table class="table table-striped table-bordered">
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

@section('append_header')
@parent
Threads
@stop

@section('title')
@parent
Threads
@stop

@stop