@extends('layouts.scaffold')

@section('main')

<h1>All Messages</h1>

<p>{{ link_to_route('messages.create', 'Add new message') }}</p>

@if ($messages->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Charge_id</th>
				<th>Title</th>
				<th>Description</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($messages as $message)
				<tr>
					<td>{{{ $message->charge_id }}}</td>
					<td>{{{ $message->title }}}</td>
					<td>{{{ $message->description }}}</td>
                    <td>{{ link_to_route('messages.edit', 'Edit', array($message->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('messages.destroy', $message->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no messages
@endif

@stop
