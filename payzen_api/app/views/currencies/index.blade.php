@extends('layouts.scaffold')

@section('main')

<h1>All Currencies</h1>

<p>{{ link_to_route('currencies.create', 'Add new currency') }}</p>

@if ($currencies->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Alpha3</th>
				<th>Numeric</th>
				<th>Multiplicator</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($currencies as $currency)
				<tr>
					<td>{{{ $currency->alpha3 }}}</td>
					<td>{{{ $currency->numeric }}}</td>
					<td>{{{ $currency->multiplicator }}}</td>
                    <td>{{ link_to_route('currencies.edit', 'Edit', array($currency->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('currencies.destroy', $currency->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no currencies
@endif

@stop
