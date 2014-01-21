@extends('layouts.scaffold')

@section('main')

<h1>Create Transaction</h1>

{{ Form::open(array('route' => 'transactions.store')) }}
	<ul>
        <li>
            {{ Form::label('charge_id', 'Charge_id:') }}
            {{ Form::text('charge_id') }}
        </li>

        <li>
            {{ Form::label('trans_date', 'Trans_date:') }}
            {{ Form::text('trans_date') }}
        </li>

        <li>
            {{ Form::label('trans_id', 'Trans_id:') }}
            {{ Form::text('trans_id') }}
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


