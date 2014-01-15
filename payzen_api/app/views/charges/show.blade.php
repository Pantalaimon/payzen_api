@extends('layouts.scaffold')

@section('main')

<h1>Show Charge</h1>

<p>{{ link_to_route('charges.index', 'Return to all charges') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Amount</th>
				<th>Currency</th>
				<th>Shop_key</th>
				<th>Status</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $charge->amount }}}</td>
					<td>{{{ $charge->currency }}}</td>
					<td>{{{ $charge->shop_key }}}</td>
					<td>{{{ $charge->status }}}</td>
                    <td>{{ link_to_route('charges.edit', 'Edit', array($charge->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('charges.destroy', $charge->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
