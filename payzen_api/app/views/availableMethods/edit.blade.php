@extends('layouts.scaffold')

@section('main')

<h1>Edit AvailableMethod</h1>
{{ Form::model($availableMethod, array('method' => 'PATCH', 'route' => array('availableMethods.update', $availableMethod->id))) }}
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
			{{ link_to_route('availableMethods.show', 'Cancel', $availableMethod->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
