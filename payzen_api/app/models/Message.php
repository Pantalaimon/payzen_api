<?php

class Message extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'charge_id' => 'required',
		'title' => 'required',
		'description' => 'required'
	);
}
