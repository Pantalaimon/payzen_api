<?php

class UsedMethod extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'charge_id' => 'required',
		'method' => 'required'
	);
}