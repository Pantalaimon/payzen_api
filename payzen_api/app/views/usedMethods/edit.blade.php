@extends('layouts.scaffold')

@section('main')

<h1>Edit UsedMethod</h1>
{{ Form::model($usedMethod, array('method' => 'PATCH', 'route' => array('usedMethods.update', $usedMethod->id))) }}
	<ul>
        <li>
            {{ Form::label('charge_id', 'Charge_id:') }}
            {{ Form::text('charge_id') }}
        </li>

        <li>
            {{ Form::label('method', 'Method:') }}
            {{ Form::text('method') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('usedMethods.show', 'Cancel', $usedMethod->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
