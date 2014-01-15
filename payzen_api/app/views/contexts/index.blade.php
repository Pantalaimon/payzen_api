@extends('layouts.scaffold')

@section('main')

<h1>All Contexts</h1>

<p>{{ link_to_route('contexts.create', 'Add new context') }}</p>

@if ($contexts->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Charge_id</th>
				<th>Status</th>
				<th>Trans_date</th>
				<th>Trans_id</th>
				<th>Trans_time</th>
				<th>Cache_id</th>
				<th>Locale</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($contexts as $context)
				<tr>
					<td>{{{ $context->charge_id }}}</td>
					<td>{{{ $context->status }}}</td>
					<td>{{{ $context->trans_date }}}</td>
					<td>{{{ $context->trans_id }}}</td>
					<td>{{{ $context->trans_time }}}</td>
					<td>{{{ $context->cache_id }}}</td>
					<td>{{{ $context->locale }}}</td>
                    <td>{{ link_to_route('contexts.edit', 'Edit', array($context->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('contexts.destroy', $context->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no contexts
@endif

@stop
