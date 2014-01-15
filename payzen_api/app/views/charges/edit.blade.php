@extends('layouts.scaffold')

@section('main')

<h1>Edit Charge</h1>
{{ Form::model($charge, array('method' => 'PATCH', 'route' => array('charges.update', $charge->id))) }}
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
            {{ Form::label('shop_key', 'Shop_key:') }}
            {{ Form::text('shop_key') }}
        </li>

        <li>
            {{ Form::label('status', 'Status:') }}
            {{ Form::text('status') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('charges.show', 'Cancel', $charge->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
