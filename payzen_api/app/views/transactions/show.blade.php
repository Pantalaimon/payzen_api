@extends('layouts.scaffold')

@section('main')

<h1>Show Transaction</h1>

<p>{{ link_to_route('transactions.index', 'Return to all transactions') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Charge_id</th>
				<th>Trans_date</th>
				<th>Trans_id</th>
		</tr>
	</thead>

	<tbody>
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
	</tbody>
</table>

@stop
