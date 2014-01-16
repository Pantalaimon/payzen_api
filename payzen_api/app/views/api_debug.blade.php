@extends('layouts.scaffold')

@section('main')

<h1>API call details</h1>

<h2>Request</h2>
<p>Url : {{ $url }}</p>
<pre>{{{ $json }}}</pre>

<h2>Response</h2>
<pre>{{{ $response }}}</pre>

@stop
