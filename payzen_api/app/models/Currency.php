<?php

class Currency extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'alpha3' => 'required',
		'numeric' => 'required',
		'multiplicator' => 'required'
	);
}
