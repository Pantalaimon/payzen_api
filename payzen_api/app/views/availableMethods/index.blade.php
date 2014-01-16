@extends('layouts.scaffold')

@section('main')

<h1>All AvailableMethods</h1>

<p>{{ link_to_route('availableMethods.create', 'Add new availableMethod') }}</p>

@if ($availableMethods->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Charge_id</th>
				<th>Method</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($availableMethods as $availableMethod)
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
			@endforeach
		</tbody>
	</table>
@else
	There are no availableMethods
@endif

@stop
