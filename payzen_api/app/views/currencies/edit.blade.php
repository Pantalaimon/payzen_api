@extends('layouts.scaffold')

@section('main')

<h1>Edit Currency</h1>
{{ Form::model($currency, array('method' => 'PATCH', 'route' => array('currencies.update', $currency->id))) }}
	<ul>
        <li>
            {{ Form::label('alpha3', 'Alpha3:') }}
            {{ Form::text('alpha3') }}
        </li>

        <li>
            {{ Form::label('numeric', 'Numeric:') }}
            {{ Form::text('numeric') }}
        </li>

        <li>
            {{ Form::label('multiplicator', 'Multiplicator:') }}
            {{ Form::input('number', 'multiplicator') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('currencies.show', 'Cancel', $currency->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
