@extends('layouts.scaffold')

@section('main')

<h1>Show UsedMethod</h1>

<p>{{ link_to_route('usedMethods.index', 'Return to all usedMethods') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Charge_id</th>
				<th>Method</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $usedMethod->charge_id }}}</td>
					<td>{{{ $usedMethod->method }}}</td>
                    <td>{{ link_to_route('usedMethods.edit', 'Edit', array($usedMethod->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('usedMethods.destroy', $usedMethod->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
