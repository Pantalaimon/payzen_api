@extends('layouts.scaffold')

@section('main')

<h1>Create Charge</h1>

{{ Form::open(array('route' => 'charges.store')) }}
	<ul>
        <li>
            {{ Form::label('amount', 'Amount:') }}
            {{ Form::text('amount') }}
        </li>

        <li>
            {{ Form::label('currency', 'Currency:') }}
            {{ Form::text('currency') }}
        </li>

        <li>
            {{ Form::label('shop_id', 'Shop_id:') }}
            {{ Form::text('shop_id') }}
        </li>

        <li>
            {{ Form::label('shop_key', 'Shop_key:') }}
            {{ Form::text('shop_key') }}
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


