@extends('layouts.scaffold')

@section('main')

<h1>Edit Transaction</h1>
{{ Form::model($transaction, array('method' => 'PATCH', 'route' => array('transactions.update', $transaction->id))) }}
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
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('transactions.show', 'Cancel', $transaction->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
