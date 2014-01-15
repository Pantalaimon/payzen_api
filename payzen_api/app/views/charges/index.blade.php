@extends('layouts.scaffold')

@section('main')

<h1>All Charges</h1>

<p>{{ link_to_route('charges.create', 'Add new charge') }}</p>

@if ($charges->count())
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
			@foreach ($charges as $charge)
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
			@endforeach
		</tbody>
	</table>
@else
	There are no charges
@endif

@stop
