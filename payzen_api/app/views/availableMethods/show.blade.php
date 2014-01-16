@extends('layouts.scaffold')

@section('main')

<h1>Show AvailableMethod</h1>

<p>{{ link_to_route('availableMethods.index', 'Return to all availableMethods') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Charge_id</th>
				<th>Method</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $availableMethod->charge_id }}}</td>
					<td>{{{ $availableMethod->method }}}</td>
                    <td>{{ link_to_route('availableMethods.edit', 'Edit', array($availableMethod->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('availableMethods.destroy', $availableMethod->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
