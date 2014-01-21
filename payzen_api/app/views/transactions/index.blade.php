@extends('layouts.scaffold')

@section('main')

<h1>All Transactions</h1>

<p>{{ link_to_route('transactions.create', 'Add new transaction') }}</p>

@if ($transactions->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Charge_id</th>
				<th>Trans_date</th>
				<th>Trans_id</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($transactions as $transaction)
				<tr>
					<td>{{{ $transaction->charge_id }}}</td>
					<td>{{{ $transaction->trans_date }}}</td>
					<td>{{{ $transaction->trans_id }}}</td>
                    <td>{{ link_to_route('transactions.edit', 'Edit', array($transaction->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('transactions.destroy', $transaction->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no transactions
@endif

@stop
