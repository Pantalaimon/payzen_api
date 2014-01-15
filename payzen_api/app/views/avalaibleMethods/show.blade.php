@extends('layouts.scaffold')

@section('main')

<h1>Show AvalaibleMethod</h1>

<p>{{ link_to_route('avalaibleMethods.index', 'Return to all avalaibleMethods') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Charge_id</th>
				<th>Method</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $avalaibleMethod->charge_id }}}</td>
					<td>{{{ $avalaibleMethod->method }}}</td>
                    <td>{{ link_to_route('avalaibleMethods.edit', 'Edit', array($avalaibleMethod->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('avalaibleMethods.destroy', $avalaibleMethod->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
