@extends('layouts.scaffold')

@section('main')

<h1>Edit Context</h1>
{{ Form::model($context, array('method' => 'PATCH', 'route' => array('contexts.update', $context->id))) }}
	<ul>
        <li>
            {{ Form::label('charge_id', 'Charge_id:') }}
            {{ Form::text('charge_id') }}
        </li>

        <li>
            {{ Form::label('status', 'Status:') }}
            {{ Form::text('status') }}
        </li>

        <li>
            {{ Form::label('trans_date', 'Trans_date:') }}
            {{ Form::text('trans_date') }}
        </li>

        <li>
            {{ Form::label('trans_id', 'Trans_id:') }}
            {{ Form::text('trans_id') }}
        </li>

        <li>
            {{ Form::label('trans_time', 'Trans_time:') }}
            {{ Form::text('trans_time') }}
        </li>

        <li>
            {{ Form::label('cache_id', 'Cache_id:') }}
            {{ Form::text('cache_id') }}
        </li>

        <li>
            {{ Form::label('locale', 'Locale:') }}
            {{ Form::text('locale') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('contexts.show', 'Cancel', $context->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
