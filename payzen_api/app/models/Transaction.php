<?php

class Transaction extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'charge_id' => 'required',
		'trans_date' => 'required',
		'trans_id' => 'required'
	);
}
