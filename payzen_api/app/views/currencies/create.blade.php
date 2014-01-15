@extends('layouts.scaffold')

@section('main')

<h1>Create Currency</h1>

{{ Form::open(array('route' => 'currencies.store')) }}
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
			{{ Form::submit('Submit', array('class' => 'btn btn-info')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop


